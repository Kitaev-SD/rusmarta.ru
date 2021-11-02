<?php
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");



if (LANG_CHARSET=='windows-1251'){
    header('Content-type:text/html; charset=cp1251');
}

$module_name='cackle.reviews';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $module_name . "/classes/general/cackle_api.php");
class CackleYandexSync {
    function CackleYandexSync() {
        $cackle_reviews_api = new CackleReviewAPI();
       // var_dump("account_api_".CMainPage::GetSiteByHost());die();
        $this->siteId = $cackle_reviews_api->cackle_reviews_get_param("site_id_".CMainPage::GetSiteByHost());
        $this->accountApiKey = $cackle_reviews_api->cackle_reviews_get_param("account_api_".CMainPage::GetSiteByHost());
        $this->siteApiKey = $cackle_reviews_api->cackle_reviews_get_param("site_api_".CMainPage::GetSiteByHost());
        //var_dump("account_api_".CMainPage::GetSiteByHost());
    }


    function init($offer_id) {
        $apix = new CackleReviewAPI();
        $response = $this->get_model_id($offer_id);
        $obj = $this->cackle_reviews_json_decodes($response,true);
        if(isset($obj['offer'])){
            $obj = $obj['offer'];
            $model_id = $obj['modelid'];
        }
        $this->push_model($offer_id,$model_id);
        return "success";
    }

    function get_model_id(){
        return
        '{
  "offer":
  {
    "id": "3",
    "modelId": "1",
    "name": "{наименование_предложения}",
    "description": "{описание_предложения}",
    "shopInfo":
    {
      "id": {идентификатор_магазина},
      "name": "{название_магазина}",
      "shopName": "{URL_главной_страницы_магазина}",
      "url": "{URL_предложения}",
      "gradeTotal": {количество_оценок},
      "rating": {значение_рейтинга},
      "factAddress": "{юридический_адрес}",
      "juridicalAddress": "{фактический_адрес}",
      "ogrn": "{регистрационный_номер}",
      "yamoney": {признак_возможности_оплаты_Яндекс.Деньгами}
    }
  }}
    ';
    }

    function get_reviews($review_last_modified, $cackle_reviews_page = 0){
        $this->get_url = "http://cackle.me/api/3.0/review/list.json?id=$this->siteId&accountApiKey=$this->accountApiKey&siteApiKey=$this->siteApiKey";
        $host = $this->get_url . "&modified=" . $review_last_modified . "&page=" . $cackle_reviews_page . "&size=100";
		
        $result = file_get_contents($host);
		//print_r($result);
        return $result;
		//return false;

    }



    function to_i($number_to_format){
        return number_format($number_to_format, 0, '', '');
    }


    function cackle_reviews_json_decodes($response){

        $obj = json_decode($response,true);

        return $obj;
    }

    function filter_cp1251($string1){
        $cackle_reviews_api = new CackleReviewAPI();
        if ($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".CMainPage::GetSiteByHost()) == "1"){
            $string2 = iconv("utf-8", "CP1251",$string1);
            //print "###33";
        }
        return $string2;
    }
	function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    function push_model($offer_id,$model_id){
        $arFields=
            Array(

                'offer_id'=>$offer_id,
                'model_id'=>$model_id,
            );
        global $DB;
        //$err_mess = (CForm::err_mess())."<br>Function: AddResultAnswer<br>Line: ";
        $arInsert = $DB->PrepareInsert("cackle_reviews_model", $arFields);
        //var_dump($arFields);
        $strSql = "INSERT INTO cackle_reviews_model (".$arInsert[0].") VALUES (".$arInsert[1].") ON DUPLICATE KEY UPDATE
offer_id=VALUES(offer_id), model_id=VALUES(model_id)";
        $DB->Query($strSql, false);


    }

    function review_status_decoder($review) {
        $status;
        if (strtolower($review['status']) == "approved") {
            $status = 1;
        }
        elseif (strtolower($review['status'] == "pending") || strtolower($review['status']) == "rejected") {
            $status = 0;
        }
        elseif (strtolower($review['status']) == "spam") {
            $status = 0;
        }
        elseif (strtolower($review['status']) == "deleted") {
            $status = 0;
        }
        return $status;
    }

    function update_review_status($review_id, $status, $modified, $review_content) {
        $cackle_reviews_api = new CackleReviewAPI();
        //print_r('review_content='.$review_content);
		if ($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".CMainPage::GetSiteByHost()) == 1){
            $review_content = iconv("utf-8", "cp1251",$review_content);
        }
        global $DB;

        $arFields = array(
            'approve' => $status,
            'text' => $review_content
        );
		
        $sql = $DB->PrepareUpdate(PREFIX ."_reviews",$arFields);
        $resFields = "'Cackle:$review_id'";
        $strSql = "UPDATE b_reviews SET ".$sql." WHERE `user_agent`=". $resFields;
        $DB->Query($strSql, false);
        $cackle_reviews_api->cackle_reviews_set_param("last_modified_".CMainPage::GetSiteByHost(),$modified);

    }

    function push_reviews1 ($response){
        $obj = $response['reviews'];
        if ($obj) {
            foreach ($obj as $review) {
                $cackle_reviews_api = new CackleReviewAPI();
                $get_last_modified = $cackle_reviews_api->cackle_reviews_get_param("last_modified_".CMainPage::GetSiteByHost());
                $get_last_review = $cackle_reviews_api->cackle_reviews_get_param("last_review_".CMainPage::GetSiteByHost());
                //$get_last_review = $this->db_connect("select common_value from common where `common_name` = 'last_review'","common_value");
                //$get_last_modified = $this->db_connect("select common_value from common where `common_name` = 'last_modified'","common_value");
                if ($review['id'] > $get_last_review) {
                    $this->insert_comm($review, $this->review_status_decoder($review));
                } else {
                    if ($get_last_modified==""){
                        $get_last_modified == 0;
                    }
                    if ($review['modified'] > $get_last_modified) {
                        $this->update_review_status($review['id'], $this->review_status_decoder($review), $review['modified'], $review['message'] );
                    }
                }

            }
        }
    }


    function push_reviews ($response){
	//print_r($response);
        $apix = new CackleReviewAPI();
        $get_last_modified = $apix->cackle_reviews_get_param("last_modified_".CMainPage::GetSiteByHost());
        $get_last_review = $apix->cackle_reviews_get_param("last_review_".CMainPage::GetSiteByHost());
		
        $obj = $this->cackle_reviews_json_decodes($response,true);
        $obj = $obj['reviews'];
		
		
        if ($obj) {
            $reviews_size = count($obj);
            if ($reviews_size != 0){
                foreach ($obj as $review) {
                    if ($review['id'] > $get_last_review) {
						
                        $this->insert_review($review, $this->review_status_decoder($review));
						
                    } else {
                        // if ($review['modified'] > $apix->cackle_reviews_get_param('cackle_reviews_last_modified', 0)) {
                        $this->update_review_status($review['id'], $this->review_status_decoder($review), $review['modified'], $review['message'] );
                        // }
                    }
                }
            }
        }
        return $reviews_size;

    }
}
?>