<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/mainpage.php");
$module_name = 'cackle.reviews';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_name . "/classes/general/cackle_reviews_api.php");
IncludeModuleLangFile(__FILE__);

class cackle_reviews_orders_realtime {
    function addCackleLog($var_str, $var){
        $lid=CMainPage::GetSiteByHost();
        if (COption::GetOptionString("cackle.reviews", "cackle_reviews_logs_" . $lid, false)) {
            $logName = COption::GetOptionString("cackle.reviews", "cackle_reviews_logName_" . $lid);
            $filesize = filesize($_SERVER["DOCUMENT_ROOT"]."/".$logName);
            if($filesize>286668470){
                COption::SetOptionString("cackle.reviews", "cackle_reviews_logs_" . $lid, false);
            }
            if (!defined("LOG_FILENAME")) define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/" . $logName);
            $var_log = var_export($var, true);
            $var_log = $var_str . ($var_log);
            AddMessage2Log($var_log, "cackle.reviews");
        }
    }

    function isSecure() {
        return
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443;
    }

    function http() {
        return cackle_reviews_orders_realtime::isSecure() ? "https://" : "http://";
    }

    function sendOrdersRequest($fields,$lid){
        $postfields = json_encode($fields);

        $curl_fields = array(
            'id' => COption::GetOptionString("cackle.reviews", "site_id_" . $lid, $_POST['siteId']),
            'accountApiKey' => COption::GetOptionString("cackle.reviews", "account_api_" . $lid, $_POST['accountApiKey']),
            'siteApiKey' => COption::GetOptionString("cackle.reviews", "site_api_" . $lid, $_POST['siteApiKey']),
            'orders' => $postfields
        );

        $context = stream_context_create(array(
            'http' => array(
                // http://www.php.net/manual/en/context.http.php
                'method' => 'POST',
                'header' =>
                    "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n" .
                    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
                    "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3\r\n" .
                    "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:35.0) Gecko/20100101 Firefox/35.0\r\n" .
                    "Accept-Encoding:gzip, deflate\r\n",
                'content' => http_build_query($curl_fields)
            )
        ));
        $response = file_get_contents('http://cackle.me/api/3.0/review/order.json', FALSE, $context);
        return $response;
    }

    function sendOrders($arFields,$lid){

        $resp = cackle_reviews_orders_realtime::sendOrdersRequest($arFields,$lid);
        cackle_reviews_orders_realtime::addCackleLog("cackle_response_", $resp);
        $resp = json_decode($resp, true);

        $response = !isset($resp['responseApi']['error']) ? $resp['responseApi']['status'] : $resp['responseApi']['error'];

        if ($response == "ok") {
            $status = "sent";
            foreach ($arFields as $order) {
                cackle_reviews_orders_realtime::local_storage($order['orderId'], $lid, "sent");
            }

        } else {
            $status = "pending"; //is already pending
        }
        return $response;


    }

    function local_storage($id, $lid, $status = 'pending') {
        global $DB;
        $DB->PrepareFields("b_reviews_orders");
        $order = cackle_reviews_orders_realtime::compileOrder($id,$lid);
        if (!is_array($order)) return;
        $arFields = array(
            "order_id" => "'" . trim($id) . "'",
            "status" => "'" . trim($status) . "'",
            "paid" => "'" . trim($order['paid']) . "'",
            "deliver" => "'" . trim($order['deliver']) . "'",
            "lid" => "'" . trim($lid) . "'",

        );
        $sql = "select order_id from " . PREFIX . "_reviews_orders where order_id = $id and lid = '$lid'";
        $check = new CackleReviewsAPI();
        $check = $check->db_connect($sql);
        if (!isset($check[0]['order_id'])) {

            $ID = $DB->Insert("b_reviews_orders", $arFields);

        } else {
            $err_mess = '';
            $arFields['modified'] = time() * 1000;
            $ID = $DB->Update("b_reviews_orders", $arFields, "WHERE order_id='" . $id . "'", $err_mess . __LINE__);
        }


    }

    function getChannelFromDb($arOrderItem) {
        global $DB;
        $product_xml_id = $arOrderItem["PRODUCT_XML_ID"];
        $product_xml_id = explode("#", $product_xml_id);
        $product_xml_id = '"' . $product_xml_id[0] . '"';
        $select = "select id from b_iblock_element where XML_ID =" . $product_xml_id;

        $chan = $DB->Query($select, false);
        $chan = $chan->Fetch();
        //null or not exist
        if (isset($chan['id'])) {
            return $chan['id'];
        } else {
            return false;
        }
    }

    function compileOrder($id,$lid){
        if ($id == null) return;
        if (!CModule::IncludeModule("sale")) return;
        $installType = isset($_SESSION['last_channel']) && ($_SESSION['last_channel']) == true ? 'hard' : 'simple';
        global $DB;

        $arOrder = CSaleOrder::GetByID($id);
        $rsUser = CUser::GetByID($arOrder["USER_ID"]);

        if ($arUser = $rsUser->Fetch()) {
            $arOrderPropsValue = array();
            $dbOrderProps = CSaleOrderPropsValue::GetOrderProps($arOrder["ID"]);
            while ($arOrderProps = $dbOrderProps->Fetch()) {
                $arOrderPropsValue[$arOrderProps["CODE"]] = $arOrderProps["VALUE"];
            }

            $userFullNameParts = array($arUser["LAST_NAME"], $arUser["NAME"], $arUser["SECOND_NAME"]);
            foreach ($userFullNameParts as $idx => $val)
                if (trim($val) == "") unset($userFullNameParts[$idx]);

            $arStatuses = array();
            $rsStatus = CSaleStatus::GetList(
                array("SORT" => "ASC"),
                array("LID" => LANGUAGE_ID)
            );
            while ($arStatus = $rsStatus->Fetch()) $arStatuses[$arStatus["ID"]] = $arStatus["NAME"];
            $created_at = new DateTime($arOrder['DATE_INSERT']);
            $modified = new DateTime($arOrder['DATE_UPDATE']);
            $modified_at = $modified->getTimestamp() * 1000;
            $arFields = array(
                "orderId" => $arOrder["ID"],
                "created" => $created_at->getTimestamp() * 1000,
                "modified" => $modified_at,
                "paid" => $arOrder["PAYED"] == "Y" ? true : false,
                "deliver" => $arOrder["STATUS_ID"] == "F" ? true : false,
                "user" => array(
                    "id" => $arUser["ID"],
                    "email" => $arUser["EMAIL"],
                    "phone" => $arOrderPropsValue["PHONE"],

                    "name" => (strtoupper(SITE_CHARSET) != "UTF-8") ? iconv(SITE_CHARSET, "utf-8", implode(" ", $userFullNameParts)) : implode(" ", $userFullNameParts),
                ),
                "products" => array()
            );


            if(isset($lid)){
                $rsBasket = CSaleBasket::GetList(
                    array("SORT" => "ASC"),
                    array("ORDER_ID" => $arOrder["ID"],"LID" => $lid)
                );
            }
            else{
                $rsBasket = CSaleBasket::GetList(
                    array("SORT" => "ASC"),
                    array("ORDER_ID" => $arOrder["ID"])
                );
                cackle_reviews_orders_realtime::addCackleLog("LID NOT", NULL);
            }

            if (!CModule::IncludeModule("catalog"))
                CModule::IncludeModule("catalog");


            while ($arOrderItem = $rsBasket->Fetch()) {
                if ($arOrderItem["DETAIL_PAGE_URL"] == NULL || $arOrderItem["DETAIL_PAGE_URL"] == "") {
                    cackle_reviews_orders_realtime::addCackleLog("skip_", $arOrder["ID"]);
                    return false;
                }

                cackle_reviews_orders_realtime::addCackleLog("arOrderItem", $arOrderItem);
                cackle_reviews_orders_realtime::addCackleLog("siteId", $lid );

                //if(COption::GetOptionString("cackle.reviews", "cackle_reviews_installType_" . CMainPage::GetSiteByHost(), $installType) == 'hard'){
                $arProductRes = CCatalogSku::GetProductInfo($arOrderItem["PRODUCT_ID"]);
                if (is_array($arProductRes)) {
                    $chan = $arProductRes['ID'];
                    cackle_reviews_orders_realtime::addCackleLog("chan ID", $chan);
                } else {
                    $chan = cackle_reviews_orders_realtime::getChannelFromDb($arOrderItem);
                    if (!$chan) {
                        cackle_reviews_orders_realtime::addCackleLog("chan NOT FOUND IN DB", $chan);
                        $chan == NULL;
                    }
                    cackle_reviews_orders_realtime::addCackleLog("chan PRODUCT_ID", $chan);
                }
                //}
                if($chan!=NULL){
                    $dbIBlockElement = CIBlockElement::GetList(
                        array(),
                        array(
                            "ID" => $chan,
                            "ACTIVE" => "Y",
                            "ACTIVE_DATE" => "Y",
                            "CHECK_PERMISSIONS" => "N",
                        ),
                        false,
                        false,
                        array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'TIMESTAMP_X', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
                    );
                    $arProduct = $dbIBlockElement->GetNext();
                }

                cackle_reviews_orders_realtime::addCackleLog("arProduct", $arProduct);
                cackle_reviews_orders_realtime::addCackleLog("CFile::GetPath", CFile::GetPath($arProduct['DETAIL_PICTURE']));

                $arFields["products"][] = array(
                    "prodId" => $arOrderItem["PRODUCT_ID"],
                    "name" => (strtoupper(SITE_CHARSET) != "UTF-8") ? iconv(SITE_CHARSET, "utf-8", $arOrderItem["NAME"]) : $arOrderItem["NAME"],
                    "price" => $arOrderItem["PRICE"],
                    "count" => $arOrderItem["QUANTITY"],
                    "url" => cackle_reviews_orders_realtime::http() . $_SERVER['SERVER_NAME'] . $arOrderItem["DETAIL_PAGE_URL"],
                    "chan" => COption::GetOptionString("cackle.reviews", "cackle_reviews_installType_" . $lid, $installType) == 'hard' ? $chan : $arOrderItem["DETAIL_PAGE_URL"],
                    "photo" => CFile::GetPath($arProduct['DETAIL_PICTURE']) == NULL && $chan != NULL ? NULL : cackle_reviews_orders_realtime::http() . $_SERVER['SERVER_NAME'] . CFile::GetPath($arProduct['DETAIL_PICTURE'])

                );

                cackle_reviews_orders_realtime::addCackleLog("SERVER_NAME", $_SERVER['SERVER_NAME']);
                cackle_reviews_orders_realtime::addCackleLog("Compiled Product", $arFields);


                return $arFields;
            }
        }
    }

    function OnOrderAdd($id, $data){
        $lid=CMainPage::GetSiteByHost();
        $cackle_reviews_api = new CackleReviewsAPI();
        cackle_reviews_orders_realtime::addCackleLog("_OnOrderAddId", $id);
        if ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_' . $lid)) {
            cackle_reviews_orders_realtime::local_storage($id,$lid);
        }
    }

    function OnOrderUpdate($id, $data) {
        $cackle_reviews_api = new CackleReviewsAPI();
        $lid=CMainPage::GetSiteByHost();
        cackle_reviews_orders_realtime::addCackleLog("_OnOrderUpdateId", $id);
        if ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_' . $lid)) {
            cackle_reviews_orders_realtime::local_storage($id,$lid);
        }
    }
    

    function order_sync($lid){
        $payed = COption::GetOptionString("cackle.reviews", 'cackle_reviews_orders_sync_payed_' . $lid, 1);
        $deled = COption::GetOptionString("cackle.reviews", 'cackle_reviews_orders_sync_deled_' . $lid, 1);
        $where = '';
        if ($payed == 1 && $deled == '0') {
            $where = " and paid=1";
        }
        if ($payed == '0' && $deled == 1) {
            $where = " and deliver=1";
        }
        if ($payed == 1 && $deled == 1) {
            $where = " and (paid=1 or deliver=1)";
        }
        $sql = "select order_id from " . PREFIX . "_reviews_orders where (status = 'pending'" . $where . ") and lid='$lid' order by id desc limit 10";
        $cackle_reviews_api = new CackleReviewsAPI();
        $orders = array();
        $pending_orders = $cackle_reviews_api->db_connect($sql);
        if (isset($pending_orders[0]['order_id'])) {
            foreach ($pending_orders as $order) {
                $arFields = cackle_reviews_orders_realtime::compileOrder($order['order_id'],$lid);
                //check if pending order have products.. if not, not send
                if (isset($arFields["products"][0]["prodId"])) {
                    $orders[] = ($arFields);
                } else {

                }

            }
        }
        if (sizeof($orders) > 0) {
            $resp = cackle_reviews_orders_realtime::sendOrders($orders,$lid);
            $ret_object = new stdClass();
            $ret_object->status = 'partial';
            $ret_object->orders = $orders;
            $ret_object->response = $resp;
            return $ret_object;
        } else {
            $ret_object = new stdClass();
            $ret_object->status = 'complete';
            return $ret_object;
        }
    }

} ?>
