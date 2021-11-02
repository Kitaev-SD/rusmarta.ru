<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/mainpage.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/catalog/general/catalog_sku.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
$module_id = 'cackle.reviews';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/classes/general/cackle_reviews_sync.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/classes/general/request_handler.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/classes/general/cackle_orders_realtime.php");
IncludeModuleLangFile(__FILE__);
$cackle_reviews_api = new CackleReviewsAPI();
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main;

//cackle_reviews_request_handler();


//$APPLICATION->AddHeadScript('/bitrix/modules/' . $module_id .'/classes/general/cackle.min.js');

//$APPLICATION->ShowHead();
?>
<!-- Angular Material Dependencies -->
<style>
    body {
        font-size: 1rem;
        font-font: arial;

    }

    span.md-subheader-content span {
        line-height: 16px !important;
        font-weight: 400;
    }

    div.md-subheader-inner {
        padding-top: 0 !important;;
    }

    h1 {
        font-size: 1.5rem;
    }

    #wpbody-content > div.error {
        display: none;
    }

    #wpwrap {
        background-color: #FFFFFF;

    }

    md-checkbox {
        margin: 0 !important;
    }

    md-list-item.disable-padding .md-no-style {
        padding: 0px !important;
        padding-left: 2px !important;
    }

    md-toolbar.cackle-errors, md-toolbar.cackle-errors .md-toolbar-tools {
        min-height: 36px !important;
        max-height: 36px !important;
    }

    md-toolbar.cackle-errors, md-toolbar.cackle-errors .md-toolbar-tools h1 span {
        font-size: 16px;
    }

    md-content.cackle-errors {
        font-size: 14px;
    }

    spinner svg {
        height: 32px;
        width: 32px;
    }

    .success {
        color: #008000;
    }

    .warn {
        color: #ff0000;
    }

    .mc-spin {
        display: inline-block;
        width: 16px !important;
        height: 16px !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        text-indent: 999em !important;
        vertical-align: middle !important;
        background: url(data:image/gif;base64,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) 0 center no-repeat !important;
    }

</style>

<!--    Asset::getInstance()->addJs("https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js")-->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.0/angular-material.min.css">

<?php
Asset::getInstance()->addJs("https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js");
Asset::getInstance()->addJs("https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-messages.js");
Asset::getInstance()->addJs("https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-sanitize.js");

Asset::getInstance()->addJs("https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-animate.min.js");
Asset::getInstance()->addJs("https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-aria.min.js");

Asset::getInstance()->addJs("https://ajax.googleapis.com/ajax/libs/angular_material/0.11.0/angular-material.min.js");


Asset::getInstance()->addJs("/bitrix/js/cacklereviews/cackle.min.js");
//Asset::getInstance()->addJs("/bitrix/js/" . "cacklereviews" . '/settings-module/index.js');
//Asset::getInstance()->addJs("/bitrix/js/" . "cacklereviews" . '/settings-module/controllers/index.js');
//Asset::getInstance()->addJs("/bitrix/js/" . "cacklereviews" . '/settings-module/controllers/settings.ctrl.js');
//Asset::getInstance()->addJs("/bitrix/js/" . "cacklereviews" . '/settings-module/services/index.js');
//Asset::getInstance()->addJs("/bitrix/js/" . "cacklereviews" . '/settings-module/services/cackle_api.js');
//Asset::getInstance()->addJs("/bitrix/js/" . "cacklereviews" . '/app.js');

function getUtfMessage($mes){
    if (strtoupper(SITE_CHARSET) != "UTF-8") {
        //$str = preg_replace_callback('/\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $mes);
        //return iconv('cp1251', 'utf-8', $str);
        global $APPLICATION;
        $conv = $APPLICATION->ConvertCharset($mes,  "Windows-1251","UTF-8");
        CUtil::InitJSCore(array('ajax', 'ls'));
        return CUtil::JSEscape($conv);
    }
    else {
        CUtil::InitJSCore(array('ajax', 'ls'));
        return CUtil::JSEscape($mes);
    }
}

$sites = Array();
$rsSites = CSite::GetList($by="sort", $order="desc", Array());
while ($arrSite = $rsSites->Fetch()){
    $lid = $arrSite['LID'];
    $name = getUtfMessage($arrSite['NAME']);
    $sites[] = array(
        'lid'=>$lid,
        'name'=>$name
    );
}
//var_dump($sites);die();
$sites = json_encode($sites);



function GetMessageF($mes){
    $mes = GetMessage($mes);
    return getUtfMessage($mes);
}

function filter_cp1251($string1){
    $str = preg_replace_callback('/\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $string1);
    return iconv('cp1251', 'utf-8', $str);
}

$cackle_locale[] = array(
    'Cackle plugin installation' => GetMessageF('Cackle plugin installation'),
    'Check status' => GetMessageF('Check status'),
    'Export comments' => GetMessageF('Export comments'),
    'Sync comments' => GetMessageF('Sync comments'),
    'Cackle plugin status and account availiable options' => GetMessageF('Cackle plugin status and account availiable options'),
    'Warnings and errors' => GetMessageF('Warnings and errors'),
    'Enable SSO' => GetMessageF('Enable SSO'),
    'Enable sync(SEO)' => GetMessageF('Enable sync(SEO)'),
    'Activate' => GetMessageF('Activate'),
    'Plugin activated' => GetMessageF('Plugin activated'),
    'Single sign on' => GetMessageF('Single sign on'),
    'This will export your existing WordPress comments to Cackle' => GetMessageF('This will export your existing WordPress comments to Cackle'),
    'This will download your Cackle comments and store them locally in WordPress' => GetMessageF('This will download your Cackle comments and store them locally in WordPress'),
    'Start' => GetMessageF('Start'),
    'Continue' => GetMessageF('Continue'),
    'Stop' => GetMessageF('Stop'),
    'Cackle widget language' => GetMessageF('Cackle widget language'),
    'Export process' => GetMessageF('Export process'),
    'Sync process' => GetMessageF('Sync process'),
    'Stop' => GetMessageF('Stop'),
    'export_Processed comments for post_id = ' => GetMessageF('Processed export comments for post_id = '),
    'export_Processed comments was stopped for post_id = ' => GetMessageF('Processed export comments was stopped for post_id = '),
    'export_All comments were transfer successfully to Cackle!' => GetMessageF('All comments were exported successfully to Cackle!'),
    'sync_Processed comments for post_id = ' => GetMessageF('Processed sync comments for post_id = '),
    'sync_Processed comments was stopped for post_id = ' => GetMessageF('Processed sync comments was stopped for post_id = '),
    'sync_All comments were transfer successfully to Cackle!' => GetMessageF('All comments were synchronized successfully to Cackle!'),
    'curl_exist_error' => GetMessageF('You need to enable curl extension in your hosting server, and then click to Activate button again.'),
    'curl_openbase_error' => GetMessageF('Open_basedir have some value and sync cannot work with it. Go to the php.ini and set ; before it to disable, and then click to Activate button again.'),
    'curl_safemode_error' => GetMessageF('Safe mode is enabled and sync cannot work with it. Go to the php.ini and set safe_mode = off to disable, and then click to Activate button again.'),

    'Warning' => GetMessageF('Warning'),
    'Success' => GetMessageF('Success'),
    'Plugin was successfully activated!' => GetMessageF('Plugin was successfully activated!'),
    'The entered keys are wrong. Please check it again. Plugin was not activated' => GetMessageF('The entered keys are wrong. Please check it again. Plugin was not activated'),
    'Plugin was successfully activated' => GetMessageF('Plugin was successfully activated'),
    'Plugin was not activated, check keys' => GetMessageF('Plugin was not activated, check keys'),
    'Cackle widget language' => GetMessageF('Cackle widget language'),
    'Paid Single Sign On option' => GetMessageF('Paid Single Sign On option'),
    'Paid white label option' => GetMessageF('Paid white label option'),

    'with specified error: ' => GetMessageF('with specified error: '),
    'Error 500. Unable to connect server. Check server or internet' => GetMessageF('Error 500. Unable to connect server. Check server or internet'),
    'Unable to connect with Cackle' => GetMessageF('Unable to connect with Cackle'),
    'Last successfull exported comments was for post_id = ' => GetMessageF('Last successfull exported comments was for post_id = '),
    'Last successfull synced comments was for post_id = ' => GetMessageF('Last successfull synced comments was for post_id = '),
    'CACKLE_REVIEWS_SYNC_ORDERS_SETTINGS' => GetMessageF('CACKLE_REVIEWS_SYNC_ORDERS_SETTINGS'),
    'CACKLE_REVIEWS_SYNC_ORDERS' => GetMessageF('CACKLE_REVIEWS_SYNC_ORDERS'),
    'CACKLE_REVIEWS_SYNC_ORDERS_DESC' => GetMessageF('CACKLE_REVIEWS_SYNC_ORDERS_DESC'),
    'CACKLE_REVIEWS_SYNC_ORDERS_QUANTITY' => GetMessageF('CACKLE_REVIEWS_SYNC_ORDERS_QUANTITY'),
    'CACKLE_REVIEWS_SYNC_PAYED' => GetMessageF('CACKLE_REVIEWS_SYNC_PAYED'),
    'CACKLE_REVIEWS_SYNC_PAYED_DESC' => GetMessageF('CACKLE_REVIEWS_SYNC_PAYED_DESC'),
    'CACKLE_REVIEWS_SYNC_DELED' => GetMessageF('CACKLE_REVIEWS_SYNC_DELED'),
    'CACKLE_REVIEWS_SYNC_DELED_DESC' => GetMessageF('CACKLE_REVIEWS_SYNC_DELED_DESC'),
    'Load orders to Cackle' => GetMessageF('Load orders to Cackle'),
    'Load orders' => GetMessageF('Load orders'),
    'CACKLE_REVIEWS_SAVE' => GetMessageF('CACKLE_REVIEWS_SAVE'),
    'CACKLE_REVIEWS_CLEAN_LOG' => GetMessageF('CACKLE_REVIEWS_CLEAN_LOG'),
    'CACKLE_REVIEWS_LOG_NAME' => GetMessageF('CACKLE_REVIEWS_LOG_NAME'),
    'CACKLE_REVIEWS_LOG_PATH' => GetMessageF('CACKLE_REVIEWS_LOG_PATH'),
    'CACKLE_REVIEWS_LOG_MES' => GetMessageF('CACKLE_REVIEWS_LOG_MES'),
    'CACKLE_REVIEWS_LOG' => GetMessageF('CACKLE_REVIEWS_LOG'),
    'Order was sent with ID = ' => GetMessageF('Order was sent with ID = '),
    'All orders were sent!' => GetMessageF('All orders were sent!'),
    'CACKLE_REVIEWS_ORDERS_SYNC_MANUAL' => GetMessageF('CACKLE_REVIEWS_ORDERS_SYNC_MANUAL'),
    'allow_url_fopen_error' => GetMessageF('allow_url_fopen_error'),
    'simple_install' =>GetMessageF('simple_install'),
    'hard_install' =>GetMessageF('hard_install'),
    'simple_desc' =>GetMessageF('simple_desc'),
    'hard_desc' =>GetMessageF('hard_desc'),
    'Sent orders'=>GetMessageF('Sent orders'),
    'Orders_prepared'=>GetMessageF('Orders_prepared'),
    'how_bind'=>GetMessageF('how_bind'),
    'Choise site' =>GetMessageF('Choise site')
);
$cackle_locale = json_encode($cackle_locale);

$installType = isset($_SESSION['last_channel']) && ($_SESSION['last_channel']) == true ? 'hard' : 'simple';
$selected_lid = $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_lid_', '');
$settings[] = array(
    'siteId' => $cackle_reviews_api->cackle_reviews_get_param('site_id_' . $selected_lid),
    'siteApiKey' => $cackle_reviews_api->cackle_reviews_get_param('site_api_' . $selected_lid),
    'accountApiKey' => $cackle_reviews_api->cackle_reviews_get_param('account_api_' . $selected_lid),
    'sso' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_sso_' . $selected_lid) ? true : false,

    'sync' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_sync_' . $selected_lid) ? true : false,
    'aggregateRating' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_aggregateRating_' . $selected_lid, false)) ? true : false,
    'productRating' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_productRating_' . $selected_lid, false)) ? true : false,
    'manual_sync' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_manual_sync' . $selected_lid),
    'manual_export' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_manual_export' . $selected_lid),

    'allow_url_fopen_error' => (ini_get('allow_url_fopen') != '1') ? true : false,
    'empty_url_error' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_' . $selected_lid)&&$_SERVER['SERVER_NAME']=='') ? true : false,
    'sync_orders' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_' . $selected_lid)) ? true : false,
    'sync_orders_payed' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_payed_' . $selected_lid)) ? true : false,
    'sync_orders_deled' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_deled_' . $selected_lid)) ? true : false,
    'logs' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_logs_' . $selected_lid, false)) ? true : false,
    'logName' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_logName_' . $selected_lid, "log")),
    'baseUrl' => cackle_reviews_orders_realtime::http() . $_SERVER['SERVER_NAME'],
    'installType' => ($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_installType_' . $selected_lid, $installType)),
    'lid' => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_lid_', '')

);
$settings = json_encode($settings);

$status[] = Array(
    GetMessageF('Paid white label option') => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_whitelabel_' . $selected_lid),
    GetMessageF('Cackle widget language') => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_lang_' . $selected_lid),
    GetMessageF('Paid Single Sign On option') => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_paidsso_' . $selected_lid),
    GetMessageF('Plugin activated') => $cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_correctKey_' . $selected_lid)
);

$status = json_encode($status);
?>

<script type="text/javascript">
    cackle_admin = {};
    cackle_admin.settings = JSON.parse('<?php echo($settings)?>')[0];
    cackle_admin.url = '';
    cackle_admin.status = JSON.parse('<?php echo($status)?>')[0];
    cackle_admin.sites = JSON.parse('<?php echo($sites)?>');
    cackle_locale={
        'Cackle plugin installation': '<?php print_r(GetMessage('Cackle plugin installation')) ?>',
        'Check status': '<?php print_r(GetMessage('Check status')) ?>',
        'Export comments' : '<?php print_r(GetMessage('Export comments')) ?>',
        'Sync comments' : '<?php print_r(GetMessage('Sync comments')) ?>',
        'Cackle plugin status and account availiable options' : '<?php print_r(GetMessage('Cackle plugin status and account availiable options')) ?>',
        'Warnings and errors' : '<?php print_r(GetMessage('Warnings and errors')) ?>',
        'Enable SSO' : '<?php print_r(GetMessage('Enable SSO')) ?>',
        'Enable sync(SEO)' : '<?php print_r(GetMessage('Enable sync(SEO)')) ?>',
        'Activate' : '<?php print_r(GetMessage('Activate')) ?>',
        'Plugin activated' : '<?php print_r(GetMessage('Plugin activated')) ?>',
        'Single sign on' : '<?php print_r(GetMessage('Single sign on')) ?>',
        'This will export your existing WordPress comments to Cackle' : '<?php print_r(GetMessage('This will export your existing WordPress comments to Cackle')) ?>',
        'This will download your Cackle comments and store them locally in WordPress' : '<?php print_r(GetMessage('This will download your Cackle comments and store them locally in WordPress')) ?>',
        'Start' : '<?php print_r(GetMessage('Start')) ?>',
        'Continue' : '<?php print_r(GetMessage('Continue')) ?>',
        'Stop' : '<?php print_r(GetMessage('Stop')) ?>',
        'Cackle widget language' : '<?php print_r(GetMessage('Cackle widget language')) ?>',
        'Export process' : '<?php print_r(GetMessage('Export process')) ?>',
        'Sync process' : '<?php print_r(GetMessage('Sync process')) ?>',
        'Stop' : '<?php print_r(GetMessage('Stop')) ?>',
        'export_Processed comments for post_id = ' : '<?php print_r(GetMessage('Processed export comments for post_id = ')) ?>',
        'export_Processed comments was stopped for post_id = ' : '<?php print_r(GetMessage('Processed export comments was stopped for post_id = ')) ?>',
        'export_All comments were transfer successfully to Cackle!' : '<?php print_r(GetMessage('All comments were exported successfully to Cackle!')) ?>',
        'sync_Processed comments for post_id = ' : '<?php print_r(GetMessage('Processed sync comments for post_id = ')) ?>',
        'sync_Processed comments was stopped for post_id = ' : '<?php print_r(GetMessage('Processed sync comments was stopped for post_id = ')) ?>',
        'sync_All comments were transfer successfully to Cackle!' : '<?php print_r(GetMessage('All comments were synchronized successfully to Cackle!')) ?>',
        'curl_exist_error' : '<?php print_r(GetMessage('You need to enable curl extension in your hosting server, and then click to Activate button again.')) ?>',
        'curl_openbase_error' : '<?php print_r(GetMessage('Open_basedir have some value and sync cannot work with it. Go to the php.ini and set ; before it to disable, and then click to Activate button again.')) ?>',
        'curl_safemode_error' : '<?php print_r(GetMessage('Safe mode is enabled and sync cannot work with it. Go to the php.ini and set safe_mode = off to disable, and then click to Activate button again.')) ?>',

        'Warning' : '<?php print_r(GetMessage('Warning')) ?>',
        'Success' : '<?php print_r(GetMessage('Success')) ?>',
        'Plugin was successfully activated!' : '<?php print_r(GetMessage('Plugin was successfully activated!')) ?>',
        'The entered keys are wrong. Please check it again. Plugin was not activated' : '<?php print_r(GetMessage('The entered keys are wrong. Please check it again. Plugin was not activated')) ?>',
        'Plugin was successfully activated' : '<?php print_r(GetMessage('Plugin was successfully activated')) ?>',
        'Plugin was not activated, check keys' : '<?php print_r(GetMessage('Plugin was not activated, check keys')) ?>',
        'Cackle widget language' : '<?php print_r(GetMessage('Cackle widget language')) ?>',
        'Paid Single Sign On option' : '<?php print_r(GetMessage('Paid Single Sign On option')) ?>',
        'Paid white label option' : '<?php print_r(GetMessage('Paid white label option')) ?>',

        'with specified error: ' : '<?php print_r(GetMessage('with specified error: ')) ?>',
        'Error 500. Unable to connect server. Check server or internet' : '<?php print_r(GetMessage('Error 500. Unable to connect server. Check server or internet')) ?>',
        'Unable to connect with Cackle' : '<?php print_r(GetMessage('Unable to connect with Cackle')) ?>',
        'Last successfull exported comments was for post_id = ' : '<?php print_r(GetMessage('Last successfull exported comments was for post_id = ')) ?>',
        'Last successfull synced comments was for post_id = ' : '<?php print_r(GetMessage('Last successfull synced comments was for post_id = ')) ?>',
        'CACKLE_REVIEWS_SYNC_ORDERS_SETTINGS' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SYNC_ORDERS_SETTINGS')) ?>',
        'CACKLE_REVIEWS_SYNC_ORDERS' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SYNC_ORDERS')) ?>',
        'CACKLE_REVIEWS_SYNC_ORDERS_DESC' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SYNC_ORDERS_DESC')) ?>',
        'CACKLE_REVIEWS_SYNC_ORDERS_QUANTITY' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SYNC_ORDERS_QUANTITY')) ?>',
        'CACKLE_REVIEWS_SYNC_PAYED' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SYNC_PAYED')) ?>',
        'CACKLE_REVIEWS_SYNC_PAYED_DESC' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SYNC_PAYED_DESC')) ?>',
        'CACKLE_REVIEWS_SYNC_DELED' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SYNC_DELED')) ?>',
        'CACKLE_REVIEWS_SYNC_DELED_DESC' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SYNC_DELED_DESC')) ?>',
        'Load orders to Cackle' : '<?php print_r(GetMessage('Load orders to Cackle')) ?>',
        'Load orders' : '<?php print_r(GetMessage('Load orders')) ?>',
        'CACKLE_REVIEWS_SAVE' : '<?php print_r(GetMessage('CACKLE_REVIEWS_SAVE')) ?>',
        'CACKLE_REVIEWS_CLEAN_LOG' : '<?php print_r(GetMessage('CACKLE_REVIEWS_CLEAN_LOG')) ?>',
        'CACKLE_REVIEWS_LOG_NAME' : '<?php print_r(GetMessage('CACKLE_REVIEWS_LOG_NAME')) ?>',
        'CACKLE_REVIEWS_LOG_PATH' : '<?php print_r(GetMessage('CACKLE_REVIEWS_LOG_PATH')) ?>',
        'CACKLE_REVIEWS_LOG_MES' : '<?php print_r(GetMessage('CACKLE_REVIEWS_LOG_MES')) ?>',
        'CACKLE_REVIEWS_LOG' : '<?php print_r(GetMessage('CACKLE_REVIEWS_LOG')) ?>',
        'Order was sent with ID = ' : '<?php print_r(GetMessage('Order was sent with ID = ')) ?>',
        'All orders were sent!' : '<?php print_r(GetMessage('All orders were sent!')) ?>',
        'CACKLE_REVIEWS_ORDERS_SYNC_MANUAL' : '<?php print_r(GetMessage('CACKLE_REVIEWS_ORDERS_SYNC_MANUAL')) ?>',
        'allow_url_fopen_error' : '<?php print_r(GetMessage('allow_url_fopen_error')) ?>',
        'simple_install' :'<?php print_r(GetMessage('simple_install')) ?>',
        'hard_install' :'<?php print_r(GetMessage('hard_install')) ?>',
        'simple_desc' :'<?php print_r(GetMessage('simple_desc')) ?>',
        'hard_desc' :'<?php print_r(GetMessage('hard_desc')) ?>',
        'Sent orders':'<?php print_r(GetMessage('Sent orders')) ?>',
        'Orders_prepared':'<?php print_r(GetMessage('Orders_prepared')) ?>',
        'how_bind':'<?php print_r(GetMessage('how_bind')) ?>',
        'Choise site' :'<?php print_r(GetMessage('Choise site'))?>',
        'Empty url' :'<?php print_r(GetMessage('Empty url'))?>',
        'Main' :'<?php print_r(GetMessage('Main'))?>',
        'Advanced' :'<?php print_r(GetMessage('Advanced'))?>',
        'productRating' :'<?php print_r(GetMessage('productRating'))?>',
        'aggregateRating' :'<?php print_r(GetMessage('aggregateRating'))?>',
        'productRatingDetails' :'<?php print_r(GetMessage('productRatingDetails'))?>',
        'aggregateRatingDetails' :'<?php print_r(GetMessage('aggregateRatingDetails'))?>'
    };

</script>
<div ng-app="cackle-admin.Angular">
    <div ng-controller="settings.ctrl">
        <div ng-include src="'main.html'"></div>
    </div>

    <script type="text/ng-template" id="main.html">
        <div ng-cloak>
            <md-content>
                <md-tabs md-dynamic-height md-border-bottom>
                    <md-tab label="{{locale['Main']}}">
                        <md-content class="md-padding">



        <div layout-margin="10" layout="row" layout-sm="column" layout-md="column" layout-wrap>
            <div class="md-whiteframe-z1" flex-sm flex-md flex>
                <md-toolbar class="md-primary md-default-theme">
                    <div class="md-toolbar-tools">
                        <h1>
                            <span>{{ locale['Cackle plugin installation']}}</span>
                        </h1>
                    </div>
                </md-toolbar>


                <md-content layout-padding md-default-theme>
                    <form name="userForm">
                        <div layout-sm="column">
                            <div ng-init="initData.sites.length == 1 ? initData.lid = initData.sites[0].lid : initData.lid"></div>
                            <md-input-container ng-show="initData.sites.length>1">
                                <label>{{locale['Choise site']}}</label>
                                <md-select ng-change="changeSite()" ng-model="initData.lid" placeholder="{{locale['Choise site']}}">
                                    <md-option ng-repeat="site in initData.sites" value="{{site.lid}}">{{site.lid}} - {{site.name}}</md-option>

                                </md-select>
                            </md-input-container>
                        </div>
                        <div ng-if="changeSiteSpinner" layout="row" layout-align="center center">
                            <spinner2></spinner2>
                        </div>
                        <div  ng-if="!changeSiteSpinner" layout-sm="column">
                            <md-input-container>
                                <label for="input-1">Widget ID</label>
                                <input type="text" id="input-1" ng-model="initData.siteId">
                            </md-input-container>

                        </div>
                        <div  ng-if="!changeSiteSpinner"  layout-sm="column">
                            <md-input-container>
                                <label for="inputId">accountApiKey</label>
                                <input name="accountApiKey" type="text" ng-model="initData.accountApiKey" required
                                       md-maxlength="64" minlength="4">

                                <div ng-messages="userForm.accountApiKey.$error"
                                     ng-show="userForm.accountApiKey.$dirty">
                                    <div ng-message="required">This is required!</div>
                                    <div ng-message="md-maxlength">That's too long!</div>
                                    <div ng-message="minlength">That's too short!</div>
                                </div>
                            </md-input-container>

                        </div>
                        <div  ng-if="!changeSiteSpinner"  layout-sm="column">
                            <md-input-container>
                                <label for="inputId">siteApiKey</label>
                                <input name="siteApiKey" type="text" ng-model="initData.siteApiKey" required
                                       md-maxlength="64" minlength="4">

                                <div ng-messages="userForm.siteApiKey.$error" ng-show="userForm.siteApiKey.$dirty">
                                    <div ng-message="required">This is required!</div>
                                    <div ng-message="md-maxlength">That's too long!</div>
                                    <div ng-message="minlength">That's too short!</div>
                                </div>
                            </md-input-container>

                            <md-list>
                                <md-list-item class="">

                                    <p>{{locale['Enable SSO']}}</p>
                                    <md-checkbox ng-model="initData.sso" class="md-primary"></md-checkbox>

                                </md-list-item>
                                <md-list-item class="">
                                    <p>{{locale['Enable sync(SEO)']}}</p>
                                    <md-checkbox ng-model="initData.sync" class="md-primary"></md-checkbox>
                                </md-list-item>


                            </md-list>
                            <md-toolbar class="md-primary md-default-theme">
                                <div class="md-toolbar-tools">
                                    <h3>{{locale['how_bind']}}</h3>
                                </div>
                            </md-toolbar>
                            <md-radio-group ng-model="initData.installType">
                                <div layout="row" layout-wrap>

                                    <div flex>
                                        <md-radio-button value="simple" class="md-primary">{{locale['simple_install']}}</md-radio-button>
                                    </div>


                                    <div flex>
                                        <md-radio-button value="hard" class="md-primary">{{locale['hard_install']}}</md-radio-button>
                                    </div>
                                    <md-content  ng-if="initData.installType=='simple'" layout-padding>
                                        <p style="font-size:12px;" ng-bind-html="locale['simple_desc']"></p>
                                    </md-content>
                                    <md-content   ng-if="initData.installType=='hard'" layout-padding>
                                            <p style="font-size:12px;" ng-bind-html="locale['hard_desc']"></p>
                                    </md-content>


                                </div>


                            </md-radio-group>

                            <md-button ng-click="activate()" class="md-raised md-primary">{{locale['Activate']}}
                            </md-button>
                            <md-subheader ng-if="object_keys(messages).length>0" class="md-no-sticky">{{locale['Warnings
                                and errors']}}
                            </md-subheader>
                            <div ng-repeat="message in messages">
                                <div error-message error="message.text" header="message.header"
                                     errorclass="message.class"></div>
                            </div>
                        </div>





                    </form>
                </md-content>


            </div>
            <div class="md-whiteframe-z1" flex-sm flex>

                <md-toolbar class="md-primary md-default-theme">
                    <div class="md-toolbar-tools">
                        <h1>
                            <span ng-bind="locale['Check status']"></span>
                        </h1>
                    </div>
                </md-toolbar>


                <md-content layout-padding md-default-theme>
                    <md-list>
                        <md-list-item>
                            <h2 ng-if="status[locale['Plugin activated']]||status['correctKey']==true">
                                {{locale['Plugin was successfully activated']}}</h2>

                            <h2 ng-if="status[locale['Plugin activated']]==false || status['correctKey']==false">
                                {{locale['Plugin was not activated, check keys']}}</h2>
                        </md-list-item>
                    </md-list>
                    <md-list>
                        <md-subheader class="md-no-sticky">
                            {{locale['Cackle plugin status and account availiable options']}}
                        </md-subheader>

                        <md-list-item
                            ng-repeat="(k,v) in (filtered = (status|objToArray:locale['Cackle widget language']))">

                            <p ng-hide="k=='correctKey'||k=='whitelabel'||k=='sso'||k=='lang'"> {{ k }} </p>


                            <span ng-show="k==locale['Cackle widget language']">{{v}}</span>

                            <p ng-show="k=='correctKey'">{{locale['Plugin activated']}}</p>

                            <p ng-show="k=='whitelabel'">{{locale['Paid white label option']}}</p>

                            <p ng-show="k=='sso'">{{locale['Paid Single Sign On option']}}</p>

                            <p ng-show="k=='lang'">{{locale['Cackle widget language']}}</p>
                            <span ng-show="k=='lang'">{{v}}</span>
                            <md-checkbox ng-disabled="true" ng-hide="k==locale['Cackle widget language']||k=='lang'"
                                         class="md-primary" ng-model="v"></md-checkbox>
                        </md-list-item>
                        <md-divider></md-divider>


                    </md-list>

                </md-content>

            </div>

        </div>
        <div layout-margin="10" layout="row" layout-wrap layout-sm="column" layout-md="column">

            <div class="md-whiteframe-z1" flex-sm flex>
                <md-toolbar class="md-primary md-default-theme">
                    <div class="md-toolbar-tools">
                        <h1>
                            <span>{{locale['Sync comments']}}</span>
                        </h1>
                    </div>
                </md-toolbar>
                <md-content layout-padding>
                    <p>{{locale['This will download your Cackle comments and store them locally in WordPress']}}</p>
                    <md-button ng-disabled="transfer['sync'].status" ng-click="import_reviews()"
                               class="md-raised md-primary">{{locale['Start']}}
                    </md-button>


                    <md-list>
                        <md-subheader ng-show="transfer['sync']" class="md-no-sticky">{{locale['Sync process']}}
                        </md-subheader>
                        <md-list-item ng-show="transfer['sync'].spinner">
                            <spinner></spinner>
                        </md-list-item>
                        <md-list-item ng-repeat="mess in ((transfer['sync'].messages|limitTo:-5)) track by $index">
                            <span ng-bind-html="mess"></span>
                        </md-list-item>
                    </md-list>
                </md-content>
            </div>
        </div>

        <div layout-margin="10" layout="row" layout-wrap layout-sm="column" layout-md="column">
            <div class="md-whiteframe-z1" flex-sm flex flex-md>
                <md-toolbar class="md-primary md-default-theme">
                    <div class="md-toolbar-tools">
                        <h1>
                            <span>{{locale['CACKLE_REVIEWS_SYNC_ORDERS_SETTINGS']}}</span>
                        </h1>
                    </div>
                </md-toolbar>
                <md-content layout-padding>
                    <md-list>

                        <md-list-item class="">
                            <p>{{locale['CACKLE_REVIEWS_SYNC_ORDERS']}}</p>
                            <md-checkbox ng-model="initData.sync_orders" class="md-primary"></md-checkbox>
                        </md-list-item>
                        <md-subheader class="md-no-sticky">{{locale['CACKLE_REVIEWS_SYNC_ORDERS_DESC']}}</md-subheader>
                        <md-list-item class="">
                            <p>{{locale['CACKLE_REVIEWS_SYNC_PAYED']}}</p>
                            <md-checkbox ng-model="initData.sync_orders_payed" class="md-primary"></md-checkbox>
                        </md-list-item>
                        <md-subheader class="md-no-sticky">{{locale['CACKLE_REVIEWS_SYNC_PAYED_DESC']}}</md-subheader>
                        <md-list-item class="">
                            <p>{{locale['CACKLE_REVIEWS_SYNC_DELED']}}</p>
                            <md-checkbox ng-model="initData.sync_orders_deled" class="md-primary"></md-checkbox>
                        </md-list-item>
                        <md-subheader class="md-no-sticky">{{locale['CACKLE_REVIEWS_SYNC_DELED_DESC']}}</md-subheader>
                        <md-button ng-click="activate()" class="md-raised md-primary">{{locale['CACKLE_REVIEWS_SAVE']}}
                        </md-button>


                    </md-list>

                    <md-list>
                        <md-list-item class="">
                            <h2>{{locale['Load orders to Cackle']}}</h2>
                        </md-list-item>
                        <md-subheader class="md-no-sticky">{{locale['CACKLE_REVIEWS_ORDERS_SYNC_MANUAL']}}
                        </md-subheader>
                        <md-list-item class="">

                            <md-button ng-click="initOrdersPrepare()" class="md-raised md-primary">{{locale['Load orders']}}
                            </md-button>
                            <span ng-if="orderLoading" class="mc-spin"></span>

                        </md-list-item>

                        <md-list-item class="" ng-repeat="orderPrepared in (orders_prepared|limitTo:-1) track by $index">
                            {{locale['Orders_prepared']}} {{orderPrepared}}
                        </md-list-item>
                        <md-list-item class="" ng-repeat="orderPack in (orders|limitTo:-1) track by $index">
                            {{locale['Sent orders']}} {{orderPack}}
                        </md-list-item>
                        <md-list-item ng-if="orders_complete">{{locale['All orders were sent!']}}</md-list-item>

                    </md-list>


                </md-content>
            </div>
        </div>
                        </md-content>
                    </md-tab>
                    <md-tab label="{{locale['Advanced']}}">
                        <md-content class="md-padding">
                            <md-list>
                                <md-list-item class="">
                                    <p>{{locale['CACKLE_REVIEWS_LOG']}}</p>

                                    <md-checkbox ng-change="log('log_enable',initData.logs)" ng-model="initData.logs"
                                                 class="md-primary"></md-checkbox>
                                </md-list-item>
                            </md-list>
                            <md-subheader class="md-no-sticky">{{locale['CACKLE_REVIEWS_LOG_MES']}}</md-subheader>
                            <div ng-if="initData.logs">
                                <md-input-container style="padding:16px;>
                                <label for=" logName
                                ">{{locale['CACKLE_REVIEWS_LOG_NAME']}}</label>
                                <input name="logName" ng-change="log('log_changeName',initData.logName)" type="text"
                                       ng-model="initData.logName">
                                </md-input-container>
                                <p style="padding:16px;">{{locale['CACKLE_REVIEWS_LOG_PATH']}} - <a target="_blank"
                                                                                                    href="{{initData.baseUrl+'/'+initData.logName}}">{{initData.baseUrl+'/'+initData.logName}}</a>
                                </p>
                                <md-button ng-click="log('log_clean')" class="md-raised md-primary">
                                    {{locale['CACKLE_REVIEWS_CLEAN_LOG']}}
                                </md-button>
                            </div>
                            <md-list>
                                <md-list-item class="">
                                    <p>{{locale['productRating']}}</p>
                                    <md-checkbox ng-change="rating('productRating',initData.productRating)" ng-model="initData.productRating"
                                                 class="md-primary"></md-checkbox>
                                </md-list-item>
                                <md-subheader class="md-no-sticky">{{locale['productRatingDetails']}}</md-subheader>
                                <md-list-item class="">
                                    <p>{{locale['aggregateRating']}}</p>

                                    <md-checkbox ng-change="rating('aggregateRating',initData.aggregateRating)" ng-model="initData.aggregateRating"
                                                 class="md-primary"></md-checkbox>
                                </md-list-item>
                                <md-subheader class="md-no-sticky">{{locale['aggregateRatingDetails']}}</md-subheader>
                            </md-list>
                        </md-content>
                    </md-tab>
                </md-tabs>
            </md-content>
        </div>

    </script>

    <script type="text/ng-template" id="scopeTemplate">
        <div layout="row">
            <div flex-sm flex class="md-whiteframe-z5" layout-margin>

                <md-toolbar class="cackle-errors {{errorClass}}">
                    <div class="md-toolbar-tools">
                        <h1>
                            <span>{{header}}</span>
                        </h1>
                    </div>
                </md-toolbar>
                <md-content class="cackle-errors" layout-padding ng-bind-html="error">

                </md-content>


            </div>
        </div>
    </script>
</div>




