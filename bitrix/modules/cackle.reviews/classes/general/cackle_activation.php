<?php
class CackleActivation{
    public static function check($activation_fields){
        $k_validate = new CackleReviewsApi();
        $k_req = $k_validate->key_validate($activation_fields->siteId, $activation_fields->siteApiKey, $activation_fields->accountApiKey);
        if($k_req==NULL) return Array('connected' => false);
        $k_req = json_decode( $k_req, true);
        $k_req = $k_req["siteInfo"];
        if ($k_req['correctKey'] == "true") {
            COption::SetOptionString("cackle.reviews", "site_id_".$activation_fields->lid, $activation_fields->siteId);
            COption::SetOptionString("cackle.reviews", "site_api_".$activation_fields->lid, $activation_fields->siteApiKey);
            COption::SetOptionString("cackle.reviews", "account_api_".$activation_fields->lid, $activation_fields->accountApiKey);
            //COption::SetOptionString("cackle.reviews", "yandex_api_".$activation_fields->lid, $_POST['yandexApiKey']);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_sso_".$activation_fields->lid, $activation_fields->sso);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_orders_sync_".$activation_fields->lid, $activation_fields->sync_orders ? 1 :0);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_sync_".$activation_fields->lid, $activation_fields->sync ? 1 :0);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_orders_sync_payed_".$activation_fields->lid, $activation_fields->sync_orders_payed ? 1 :0);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_orders_sync_deled_".$activation_fields->lid, $activation_fields->sync_orders_deled ? 1 :0);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_encoding_".$activation_fields->lid, $activation_fields->enable_encoding ? 1 :0);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_activated_".$activation_fields->lid, true);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_correctKey_".$activation_fields->lid, true);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_whitelable_".$activation_fields->lid, ($k_req["whitelabel"])?true:false);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_paidsso_".$activation_fields->lid, ($k_req["sso"])?true:false);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_lang_".$activation_fields->lid, $k_req["lang"]);
            COption::SetOptionString("cackle.reviews", "cackle_reviews_installType_".$activation_fields->lid, ($activation_fields->installType) );
            COption::SetOptionString("cackle.reviews", "cackle_reviews_lid_", ($activation_fields->lid) );

            $arr[]=Array(
                'whitelabel' => $k_req["whitelabel"],
                'lang' => $k_req["lang"],
                'sso' => $k_req["sso"],
                'correctKey' => $k_req['correctKey']

            );
            return $arr;
        }
        else{
            COption::SetOptionString("cackle.reviews", "cackle_reviews_activated_".CMainPage::GetSiteByHost(), false);
            $arr[]=Array('correctKey' => false);
            return $arr;
        }


    }
}

?>