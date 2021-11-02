<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/classes/general/cackle_orders_realtime.php");
function cackle_i($text, $params = null)
{
    if (!is_array($params)) {
        $params = func_get_args();
        $params = array_slice($params, 1);
    }
    return vsprintf(__($text, 'cackle'), $params);
}

function get_comment_status($status)
{
    if ($status == "1") {
        $status = "approved";
    } elseif ($status == "0") {
        $status = "pending";
    } elseif ($status == "spam") {
        $status = "spam";
    } elseif ($status == "trash") {
        $status = "deleted";
    }
    return $status;
}
function render_json($array){
    ob_start();
    header('Content-type: text/javascript');
    $debug = ob_get_clean();
    echo json_encode($array);
    die();
}

switch ($_GET['cackleApi']) {

    case 'orders_prepare':
        $cackle_reviews_api = new CackleReviewsAPI();
        $lid = $_GET['lid'];
        $cackle_reviews_api->cackle_reviews_set_param("cackle_reviews_orders_sync_".$lid,0);
        //First clean all orders quee
        $limit=10;
        $offset = $_GET['value'];
        if($offset==0){
            //delete all local storage if init
            $cackle_reviews_api->db_connect("delete from " . PREFIX . "_reviews_orders",false);
        }

        //Send all orders to que
        $sql =  "select ".PREFIX."_sale_order.ID, count(*)  from ".PREFIX."_sale_order, ".PREFIX."_sale_basket where ".PREFIX."_sale_order.ID=".PREFIX."_sale_basket.ORDER_ID and ".PREFIX."_sale_basket.DETAIL_PAGE_URL!='' and ".PREFIX."_sale_order.LID='$lid' group by ".PREFIX."_sale_order.ID having count(*) > 0 limit $limit offset " . $offset;
        //$sql = "select ID from ".PREFIX."_sale_order";
        $all_orders = $cackle_reviews_api->db_connect($sql);
        $arr = array();
        if(isset($all_orders[0]['ID'])){
            foreach ($all_orders as $order) {
                cackle_reviews_orders_realtime::local_storage($order['ID'],$lid);
            }
            $arr['status'] = 'partial';
            $arr['orders_prepared'] = sizeof($all_orders);
        }
        else{
            $arr['status'] = 'complete';
        }
        $log_counter_all_orders = "select count(*) from (select ".PREFIX."_sale_order.ID, count(*)  from ".PREFIX."_sale_order, ".PREFIX."_sale_basket where ".PREFIX."_sale_order.ID=".PREFIX."_sale_basket.ORDER_ID and ".PREFIX."_sale_basket.DETAIL_PAGE_URL!=''   and ".PREFIX."_sale_order.LID='$lid' group by ".PREFIX."_sale_order.ID having count(*) > 0) fall";
        $log_counter_payed_orders = "select count(*) from (select ".PREFIX."_sale_order.ID, count(*)  from ".PREFIX."_sale_order, ".PREFIX."_sale_basket where ".PREFIX."_sale_order.ID=".PREFIX."_sale_basket.ORDER_ID and ".PREFIX."_sale_basket.DETAIL_PAGE_URL!='' and ".PREFIX."_sale_order.PAYED='Y'  and ".PREFIX."_sale_order.LID='$lid'  group by ".PREFIX."_sale_order.ID having count(*) > 0) payed";
        $log_counter_delivered_orders = "select count(*) from (select ".PREFIX."_sale_order.ID, count(*)  from ".PREFIX."_sale_order, ".PREFIX."_sale_basket where ".PREFIX."_sale_order.ID=".PREFIX."_sale_basket.ORDER_ID and ".PREFIX."_sale_basket.DETAIL_PAGE_URL!=''  and ".PREFIX."_sale_order.STATUS_ID='F'  and ".PREFIX."_sale_order.LID='$lid'  group by ".PREFIX."_sale_order.ID having count(*) > 0) delivered";

        //sql execution
        $log_counter_all_orders = $cackle_reviews_api->db_connect($log_counter_all_orders);
        $log_counter_payed_orders = $cackle_reviews_api->db_connect($log_counter_payed_orders);
        $log_counter_delivered_orders = $cackle_reviews_api->db_connect($log_counter_delivered_orders);

        //write to log
        cackle_reviews_orders_realtime::addCackleLog("_all_orders=", $log_counter_all_orders);
        cackle_reviews_orders_realtime::addCackleLog("_payed_orders=", $log_counter_payed_orders);
        cackle_reviews_orders_realtime::addCackleLog("_delivered_orders=", $log_counter_delivered_orders);


        render_json($arr);
        break;
    case 'orders_send':
        $cackle_reviews_api = new CackleReviewsAPI();
        $lid = $_GET['lid'];
        $resp = cackle_reviews_orders_realtime::order_sync($lid);
        $arr = array();
        $arr['status']= $resp->status;
        $arr['orders']= isset($resp->orders)?$resp->orders:NULL;
        $arr['response']= isset($resp->response)?$resp->response:NULL;
        if($arr['status']=='complete') $cackle_reviews_api->cackle_reviews_set_param("cackle_reviews_orders_sync_".$lid,1);
        render_json($arr);
        break;
    case 'log_enable':
        $cackle_reviews_api = new CackleReviewsAPI();
        $arr = array();
        $log = $_GET['value'] == 'true' ? 1 : 0;
        $lid = $_GET['lid'];
        $cackle_reviews_api->cackle_reviews_set_param('cackle_reviews_logs_'.$lid, $log);
        
        if (COption::GetOptionString("cackle.reviews", "cackle_reviews_logs_" . $lid, false)) {
            $logName = COption::GetOptionString("cackle.reviews", "cackle_reviews_logName_" . $lid);
            if ($logName == '') {
                COption::SetOptionString("cackle.reviews", "cackle_reviews_logName_" . $lid, "cackle_log_" . rand(3000000000, 1000000000) . ".txt");
                $logName = COption::GetOptionString("cackle.reviews", "cackle_reviews_logName_" . $lid);
            }
        }
        $arr['status']= "ok";
        render_json($arr);
        break;
    case 'rating':
        $cackle_reviews_api = new CackleReviewsAPI();
        $arr = array();
        $enable = $_GET['value'] == 'true' ? 1 : 0;
        $param =$_GET['param'];
        $lid = $_GET['lid'];
        if($param=="aggregateRating"){
            $cackle_reviews_api->cackle_reviews_set_param('cackle_reviews_aggregateRating_'.$lid, $enable);
        }
        else{
            $cackle_reviews_api->cackle_reviews_set_param('cackle_reviews_productRating_'.$lid, $enable);
        }
        $arr['status']= "ok";
        render_json($arr);
        break;
    case 'log_clean':
        $cackle_reviews_api = new CackleReviewsAPI();
        $arr = array();
        $lid = $_GET['lid'];
        DeleteDirFilesEx(COption::GetOptionString("cackle.reviews", "cackle_reviews_logName_" . $lid, "log"));
        $arr['status']= "ok";
        render_json($arr);
        break;
    case 'log_changeName':
        $cackle_reviews_api = new CackleReviewsAPI();
        $arr = array();
        $name = $_GET['value'];
        $lid = $_GET['lid'];
        ($cackle_reviews_api->cackle_reviews_set_param('cackle_reviews_logName_'.$lid,$name));
        $arr['status']= "ok";
        render_json($arr);
        break;
    case 'change_site':
        $cackle_reviews_api = new CackleReviewsAPI();
        $selected_lid=$_GET['value'];
        $settings[] = array(
            'siteId' => $cackle_reviews_api->cackle_reviews_get_param('site_id_' . $selected_lid),
            'siteApiKey' => $cackle_reviews_api->cackle_reviews_get_param('site_api_' . $selected_lid),
            'accountApiKey' => $cackle_reviews_api->cackle_reviews_get_param('account_api_' . $selected_lid),
            'sso' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_sso_' . $selected_lid) ? true : false,

            'sync' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_sync_' . $selected_lid) ? true : false,
            'manual_sync' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_manual_sync' . $selected_lid),
            'manual_export' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_manual_export' . $selected_lid),

            'allow_url_fopen_error' => (ini_get('allow_url_fopen') != '1') ? true : false,

            'sync_orders' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_' . $selected_lid)) ? true : false,
            'sync_orders_payed' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_payed_' . $selected_lid)) ? true : false,
            'sync_orders_deled' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_deled_' . $selected_lid)) ? true : false,
            'logs' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_logs_' . $selected_lid, false)) ? true : false,
            'logName' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_logName_' . $selected_lid, "log")),
            'baseUrl' => cackle_reviews_orders_realtime::http() . $_SERVER['SERVER_NAME'],
            'installType' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_installType_' . $selected_lid, $installType)),
            'lid' => $selected_lid

        );
        render_json($settings);
        break;
    case 'import_reviews':
        $cackle_reviews_api = new CackleReviewsAPI();
        $lid = $_GET['lid'];
        if ($cackle_reviews_api->cackle_reviews_get_param("last_review_".$lid)) {
            $cackle_reviews_api->db_connect("delete from " . PREFIX . "_reviews where user_agent like 'CackleReview:%%' and lid = '$lid'",false);
        }
        $cackle_reviews_api->cackle_reviews_set_param("last_review_".$lid, 0);
        $cackle_reviews_api->cackle_reviews_set_param("last_modified_".$lid, 0);

        ob_start();
        $sync = new CackleReviewsSync($lid);

        $response = $sync->init($lid,"all_reviews");
        $debug = ob_get_clean();

        if (!$response) {
            $status = 'error';
            $result = 'fail';
            $error = $cackle_reviews_api->get_last_error();
            $msg = '<p class="status cackle-export-fail">' . 'There was an error downloading your reviews from Cackle.' . '<br/>' . htmlspecialchars($error) . '</p>';
        } else {
            if ($response) {
                $status = 'complete';
                $msg = 'Your reviews have been downloaded from Cackle and saved in your local database.';
            }
            $result = 'success';
        }
        $debug = explode("\n", $debug);
        $response = compact('result', 'status', 'reviews', 'msg', 'last_review_id', 'debug');
        render_json($response);

        break;

}
switch ($_GET['cackleApi']) {
    case 'checkKeys':
        require_once(dirname(__FILE__) . '/cackle_activation.php');
        $activation_fields=$APPLICATION->UnJSEscape($_GET['value']);
        $activation_fields = json_decode($activation_fields);
        $resp = CackleActivation::check($activation_fields);
        echo json_encode($resp);
        die();


}

?>