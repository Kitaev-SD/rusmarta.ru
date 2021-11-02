<?php
global $APPLICATION;
define('CACKLE_SCHEDULE_REVIEWS', 300 );
define('CACKLE_SCHEDULE_ORDERS', 200);
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/cackle.reviews/classes/general/cackle_reviews_api.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/cackle.reviews/classes/general/cackle_orders_realtime.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/cackle.reviews/classes/general/cackle_reviews_sync.php");
function time_is_over($cron_time, $schedule) {
    $cackle_reviews_api = new CackleReviewsAPI();
    $lid=CMainPage::GetSiteByHost();
    $get_last_time = $cackle_reviews_api->cackle_reviews_get_param("last_time_" . $schedule . "_" . $lid);
    $now = time();
    if ($get_last_time == "") {
        $q = "last_time_" . $schedule . "_" . $lid;
        $set_time = $cackle_reviews_api->cackle_reviews_set_param($q, $now);
        return time();
    } else {
        if ($get_last_time + $cron_time > $now) {
            return false;
        }
        if ($get_last_time + $cron_time < $now) {
            $q = "last_time_" . $schedule . "_" . $lid;
            $set_time = $cackle_reviews_api->cackle_reviews_set_param($q, $now);
            return $cron_time;
        }
    }
}



/*if (isset($_GET['schedule']) && $_GET['schedule'] == 'orders') {
    if (time_is_over(CACKLE_SCHEDULE_ORDERS,"orders")) {
        cackle_reviews_orders_realtime::order_sync();
    }
}
*/
$lid=CMainPage::GetSiteByHost();
if (time_is_over(CACKLE_SCHEDULE_REVIEWS,"reviews")) {
    $sync = new CackleReviewsSync($lid);
    $sync->init($lid);
}


if($lid != "" || $lid != false ){
    //timer is already contain $lid
    if (time_is_over(CACKLE_SCHEDULE_ORDERS,"orders")) {
        $cackle_reviews_api = new CackleReviewsAPI();
        if($cackle_reviews_api->cackle_reviews_get_param('cackle_reviews_orders_sync_'.$lid)) {
            cackle_reviews_orders_realtime::addCackleLog("_starting_orders_auto_load_in_time=", time());
            cackle_reviews_orders_realtime::order_sync($lid);
        }
    }
}

?>
