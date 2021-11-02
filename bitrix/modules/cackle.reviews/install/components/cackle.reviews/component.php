<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");

//$site_name=
//global $USER;
if (CModule::IncludeModule("cackle.reviews")) {
    $cackle_reviews_api = new CackleReviewsAPI();

    function cackle_reviews_auth()
    {
        global $USER;
        $cackle_reviews_api = new CackleReviewsAPI();
        $siteApiKey = $cackle_reviews_api->cackle_reviews_get_param("site_api_" . CMainPage::GetSiteByHost());
        $timestamp = time();
        $arUser = CUser::GetByID($USER->GetID());

        if ($res = $arUser->Fetch()) {
            $userFullNameParts = array($res["LAST_NAME"], $res["NAME"], $res["SECOND_NAME"]);
            $userFullNameParts = implode(" ", $userFullNameParts);
            if(strlen($userFullNameParts)>3){
                $username = $userFullNameParts;
            }
            else{
                $username = $res["LOGIN"];
            }
            $user = array(
                'id' => $res["ID"],
                'name' => $username,
                'email' => $res["EMAIL"],
                'avatar' => $_SERVER['HOST'] . CFile::GetPath($res["PERSONAL_PHOTO"]),
            );
            $user_data = base64_encode(json_encode($user));
        } else {
            $user = '{}';
            $user_data = base64_encode($user);
        }
        $sign = md5($user_data . $siteApiKey . $timestamp);
        return "$user_data $sign $timestamp";
    }


    function cackle_reviews_time_is_over($cron_time, $schedule)
    {
        $cackle_reviews_api = new CackleReviewsAPI();
        $get_last_time = $cackle_reviews_api->cackle_reviews_get_param("last_time_" . $schedule . "_" . CMainPage::GetSiteByHost());
        $now = time();
        if ($get_last_time == "") {
            $q = "last_time_" . $schedule . "_" . CMainPage::GetSiteByHost();
            $set_time = $cackle_reviews_api->cackle_reviews_set_param($q, $now);
            return time();
        } else {
            if ($get_last_time + $cron_time > $now) {
                return false;
            }
            if ($get_last_time + $cron_time < $now) {
                $q = "last_time_" . $schedule . "_" . CMainPage::GetSiteByHost();
                $set_time = $cackle_reviews_api->cackle_reviews_set_param($q, $now);
                return $cron_time;
            }
        }
    }


//Controllers to start cron

    if (!isset($_GET['schedule'])) {
        ?>



        <?php
        function get_offset()
        {
            $id = isset($_GET['cacklepage']) ? (int)$_GET['cacklepage'] : 0;
            $size = 100;
            return $size * $id;
        }

        function get_pages($post_id)
        {
            $size = 100;
            $cackle_reviews_api = new CackleReviewsAPI();
            $sql = "select count(*) as c from " . PREFIX . "_reviews where post_id = '$post_id' and approve = 1";
            $get_all_reviews_count = $cackle_reviews_api->db_connect($sql);
            return $pages = $get_all_reviews_count[0]['c'] / $size;
        }

        function get_local_reviews($channel)
        {
            //getting all reviews for special post_id from database.
            $size = 100;
            $cackle_reviews_api = new CackleReviewsAPI();
            $offset = get_offset();
            $sql = "select * from " . PREFIX . "_reviews where channel = '$channel' and approve = 1 limit $size offset " . $offset;
            $get_all_reviews = $cackle_reviews_api->db_connect($sql);

            return $get_all_reviews;
        }


        $arResult = array();

        //
        $arResult['SITE_ID'] = $cackle_reviews_api->cackle_reviews_get_param("site_id_" . CMainPage::GetSiteByHost());

        /*
            $IBLOCK_ID = 2;
            $ID = 29;

            $arInfo = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
            if (is_array($arInfo))
            {
                $rsOffers = CIBlockElement::GetList(array(),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $ID));
                while ($arOffer = $rsOffers->GetNext())
                {
                    print_r($arOffer);
                }
            }

        */
        /*
    $ID = 209;
    $ar_res = CCatalogProductProvider::ViewProduct(array(PRODUCT_ID=>$ID));
    echo "<br>����� � ����� ".$ID." ����� ��������� ���������:<pre>";
    print_r($ar_res);
    echo "</pre>";
    */
        /*$res = CIBlockElement::GetProperty( 2, 23, "sort", "asc");

        //while ( $obCustomField = $res->GetNext() ) {

            //$fieldData = CIBlockElement::GetByID( $obCustomField[ 'VALUE' ] );
            $fieldData = CIBlockElement::GetByID(211);
            if ( $ar_res = $fieldData->GetNext() ) {

                $arGood[] = array(

                    'id' => $obCustomField[ 'VALUE' ],
                    'name' => $ar_res[ 'NAME' ],
                    'detail_pic' => $ar_res[ 'DETAIL_PICTURE' ],
                    'pic_url'=>CFile::GetPath($ar_res[ 'DETAIL_PICTURE' ]),
                    'pre_pic' => $ar_res[ 'PREVIEW_PICTURE' ],
                );
            }

        //}

        var_dump($arGood);

    */

        if ($arParams['CHANNEL_ID'] == 'URL') {
            $arResult['MC_CHANNEL'] = $APPLICATION->GetCurPage();
            session_start();
            $_SESSION['last_channel'] = false;
        } else if (isset($arParams['CHANNEL_ID'])) {
            $arResult['MC_CHANNEL'] = $arParams['CHANNEL_ID'];
            session_start();
            $_SESSION['last_channel'] = true;
        } else {
            $arResult['MC_CHANNEL'] = $APPLICATION->GetCurPage();
            session_start();
            $_SESSION['last_channel'] = false;
        }
        if (isset($arParams['PRODUCT_NAME'])){
            $arResult['PRODUCT'] = $arParams['PRODUCT_NAME'];
            //pass params only if product_name defined
            $arResult['aggregateRating'] = $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_aggregateRating_' . CMainPage::GetSiteByHost());
            $arResult['productRating'] = $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_productRating_' . CMainPage::GetSiteByHost());
        }
        else{
            $arResult['PRODUCT'] = NULL;
        }

        $arResult['CACKLE_REVIEWS_OBJ'] = get_local_reviews($arResult['MC_CHANNEL']);
        if ((strtolower($DB->type) == "mysql")) {


            if (isset($arParams['CHANNEL_ID'])) {
                //    $yandex_sync->init($arResult['MC_CHANNEL']);
            }
        }
//var_dump($APPLICATION);
        $arResult['SSO'] = cackle_reviews_auth();
        $arResult['SSO_PARAM'] = $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_sso_' . CMainPage::GetSiteByHost());
        $arResult['Activated'] = $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_correctKey_' . CMainPage::GetSiteByHost());
        
        $this->IncludeComponentTemplate();
    }
}
?>