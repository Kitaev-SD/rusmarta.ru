<?php
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");



if (LANG_CHARSET=='windows-1251'){
    header('Content-type:text/html; charset=cp1251');
}

$module_name='cackle.reviews';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $module_name . "/classes/general/cackle_reviews_api.php");
class CackleReviewsSync {
    function CackleReviewsSync($lid) {
        $cackle_reviews_api = new CackleReviewsAPI();

        $this->siteId = $cackle_reviews_api->cackle_reviews_get_param("site_id_".$lid);
        $this->accountApiKey = $cackle_reviews_api->cackle_reviews_get_param("account_api_".$lid);
        $this->siteApiKey = $cackle_reviews_api->cackle_reviews_get_param("site_api_".$lid);
        $this->lid = $lid;
    }

    function has_next ($size_reviews, $size_pagination = 100) {
        return $size_reviews == $size_pagination;
    }
    function push_next_reviews($mode,$review_last_modified, $size_reviews){
        $i = 1;
        while($this->has_next($size_reviews)){
            if ($mode=="all_reviews"){
                $response = $this->get_reviews(0,$i) ;
            }
            else{
                $response = $this->get_reviews($review_last_modified,$i) ;
            }
            $size_reviews = $this->push_reviews($response); // get review from array and insert it to wp db
			$i++;
        }
    }
    function init($lid, $mode = "") {

        $apix = new CackleReviewsAPI();
        $review_last_modified = $apix->cackle_reviews_get_param("last_modified_".$lid);

        if ($mode == "all_reviews") {
            $response = $this->get_reviews(0);

        }
        else {
            $response = $this->get_reviews($review_last_modified);
        }
		
        //get reviews from Cackle Api for sync
        if ($response==NULL){
            return false;
        }
		$size_reviews = $this->push_reviews($response); // get review from array and insert it to wp db, and return size
		if ($this->has_next($size_reviews)) {
            $this->push_next_reviews($mode,$review_last_modified, $size_reviews);
		}
        return "success";
    }

    function get_reviews($review_last_modified, $cackle_reviews_page = 0){
        $this->get_url = "http://cackle.me/api/3.0/review/list.json?id=$this->siteId&accountApiKey=$this->accountApiKey&siteApiKey=$this->siteApiKey";
        $host = $this->get_url . "&modified=" . $review_last_modified . "&page=" . $cackle_reviews_page . "&size=100";
		$result = file_get_contents($host);
		return $result;
	}

    function to_i($number_to_format){
        return number_format($number_to_format, 0, '', '');
    }

    function cackle_reviews_json_decodes($response){
        $obj = json_decode($response,true);
        return $obj;
    }

    function filter_cp1251($string1){
        $cackle_reviews_api = new CackleReviewsAPI();
        if ($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$this->lid) == "1"){
            $string2 = iconv("utf-8", "CP1251",$string1);
            //print "###33";
        }
        return $string2;
    }
	function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    function insert_review($review,$status){
        if ($this->startsWith($review['chan']['channel'], 'http')) {
            $channel = 0;
        } else {
            $channel = $review['chan']['channel'];
        }

        if ($review['author']!=null){
            $author_name = isset($review['author']['name']) ? $review['author']['name'] : "";
            $author_www = isset($review['author']['www']) ? $review['author']['www']: "" ;
            $author_avatar = isset($review['author']['avatar']) ? $review['author']['avatar']: "" ;
            $author_email= isset($review['author']['email']) ?  $review['author']['email'] : "";
            $author_provider = isset($review['author']['provider']) ? $review['author']['provider']: "" ;
            $author_anonym_name = "";
            $anonym_email = "";
        }
        else{
            $author_name = isset($review['anonym']['name']) ? $review['anonym']['name']: "" ;
            $author_email= isset($review['anonym']['email']) ?  $review['anonym']['email'] : "";
            $author_www = "";
            $author_avatar = "";

        }
        $get_parent_local_id = null;
        $review_id = $review['id'];
        $review_modified = $review['modified'];
        $cackle_reviews_api = new CackleReviewsAPI();
        if ($cackle_reviews_api->cackle_reviews_get_param("last_review_".$GLOBALS['cackle_reviews_site_name'])==0){
            $cackle_reviews_api->cackle_reviews_db_prepare();
        }

        $date =strftime("%d.%m.%Y %H:%M:%S", $review['created']/1000);
        $ip = ($review['ip']) ? $review['ip'] : "";
        $comment = $review['comment'];
        $pros = $review['pros'];
        $cons = $review['cons'];
        $star = $review['star'];
        $up = $review['up'];
        $down = $review['down'];
        $user_agent = 'CackleReview:' . $review['id'];


        $conn = $cackle_reviews_api->conn();
        if ($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$GLOBALS['cackle_reviews_site_name']) == 1){

            $conn->Query('SET NAMES cp1251');
        }
        else{
            $conn->Query('SET NAMES utf8');
        }

        $arFields=
            Array(
                'channel'=>($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$GLOBALS['cackle_reviews_site_name']) == 1) ? iconv("utf-8", "CP1251",$channel) : $channel,
                'autor'=>($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$GLOBALS['cackle_reviews_site_name']) == 1) ? iconv("utf-8", "CP1251",$author_name) : $author_name,
                'email'=>($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$GLOBALS['cackle_reviews_site_name']) == 1) ? iconv("utf-8", "CP1251",$author_email) : $author_email ,
                'date'=>$date,
                'ip'=>$ip,
                'comment'=>($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$GLOBALS['cackle_reviews_site_name']) == 1) ? iconv("utf-8", "CP1251",$comment) : $comment,
                'pros'=>($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$GLOBALS['cackle_reviews_site_name']) == 1) ? iconv("utf-8", "CP1251",$pros) : $pros,
                'cons'=>($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$GLOBALS['cackle_reviews_site_name']) == 1) ? iconv("utf-8", "CP1251",$cons) : $cons,
                'status'=>$status,
                'star'=>$star,
                'up'=>$up,
                'down'=>$down,
                'user_agent'=>$user_agent,
                'lid' => $this->lid


            );
        global $DB;
        //$err_mess = (CForm::err_mess())."<br>Function: AddResultAnswer<br>Line: ";
        $arInsert = $DB->PrepareInsert("b_reviews", $arFields);
        //var_dump($arFields);
        $strSql = "INSERT INTO b_reviews (".$arInsert[0].") VALUES (".$arInsert[1].")";
        $DB->Query($strSql, false);


        $cackle_reviews_api->cackle_reviews_set_param("last_review_".$this->lid,$review_id);
        $get_last_modified = $cackle_reviews_api->cackle_reviews_get_param("last_modified_".$this->lid);
        $get_last_modified = (int)$get_last_modified;
        if ($review['modified'] > $get_last_modified) {
            $cackle_reviews_api->cackle_reviews_set_param("last_modified_".$this->lid,(string)$review['modified']);
        }

    }

    function review_status_decoder($review) {
        $status=0;
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
        $cackle_reviews_api = new CackleReviewsAPI();
        //print_r('review_content='.$review_content);
		if ($cackle_reviews_api->cackle_reviews_get_param("cackle_reviews_encoding_".$this->lid) == 1){
            $review_content = iconv("utf-8", "cp1251",$review_content);
        }
        global $DB;

        $arFields = array(
            'approve' => $status,
            'text' => $review_content
        );
		
        $sql = $DB->PrepareUpdate(PREFIX ."_reviews",$arFields);
        $resFields = "'Cackle:$review_id'";
        $strSql = "UPDATE b_reviews SET ".$sql." WHERE `user_agent`=". $resFields . "and lid='$this->lid'";
        $DB->Query($strSql, false);
        $cackle_reviews_api->cackle_reviews_set_param("last_modified_".$this->lid,$modified);

    }

    function push_reviews1 ($response){
        $obj = $response['reviews'];
        if ($obj) {
            foreach ($obj as $review) {
                $cackle_reviews_api = new CackleReviewsAPI();
                $get_last_modified = $cackle_reviews_api->cackle_reviews_get_param("last_modified_".$this->lid);
                $get_last_review = $cackle_reviews_api->cackle_reviews_get_param("last_review_".$this->lid);
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
	    $apix = new CackleReviewsAPI();
        $get_last_modified = $apix->cackle_reviews_get_param("last_modified_".$this->lid);
        $get_last_review = $apix->cackle_reviews_get_param("last_review_".$this->lid);
		
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