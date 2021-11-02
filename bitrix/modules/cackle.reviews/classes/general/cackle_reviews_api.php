<?php
/*Enter database settings*/
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");

global $MAIN_OPTIONS;
define('PREFIX', 'b');
global $APPLICATION;
global $cackle_reviews_site_id, $cackle_reviews_site_name, $cackle_reviews_site_api, $cackle_reviews_account_api, $cackle_reviews_sso, $cackle_reviews_encoding, $cackle_reviews_activated;
$cackle_reviews_site_id = COption::GetOptionString("cackle.reviews", "site_id_".CMainPage::GetSiteByHost(), $_POST['siteId']);

$cackle_reviews_account_api = COption::GetOptionString("cackle.reviews", "account_api_".CMainPage::GetSiteByHost(), $_POST['accountApiKey']);
$cackle_reviews_sso = COption::GetOptionString("cackle.reviews", "cackle_reviews_sso_".CMainPage::GetSiteByHost(), $_POST['enable_sso']);
$cackle_reviews_encoding = COption::GetOptionString("cackle.reviews", "cackle_reviews_encoding_".CMainPage::GetSiteByHost(), $_POST['enable_encoding']);
$cackle_reviews_activated = COption::GetOptionString("cackle.reviews", "cackle_reviews_activated_".CMainPage::GetSiteByHost(), $_POST['cackle_reviews_activated']);


class CackleReviewsAPI{

    function __construct(){
        $this->site_id = $GLOBALS['cackle_reviews_site_id'];
        $this->site_api = $GLOBALS['cackle_reviews_site_api'];
        $this->account_api = $GLOBALS['cackle_reviews_account_api'];
        $this->cackle_reviews_sso =$GLOBALS['cackle_reviews_sso'];
        $this->cackle_reviews_encoding = $GLOBALS['cackle_reviews_encoding'];
        $this->cackle_reviews_activated = $GLOBALS['cackle_reviews_activated'];
    }
    function db_connect($query,$return=true,$list=false){

        global $DB;
        if ($this->cackle_reviews_get_param("cackle_reviews_encoding_".$GLOBALS['cackle_reviews_site_name']) == 1){

            $db_d=('SET NAMES cp1251;');
        }
        else{
            $db_d=('SET NAMES utf8;');
        }
        $res = $DB->Query($query,true);
        //var_dump($query);

        if($return){
            $i = 0;
            $result_arr = array();
            //var_dump($query);
            //var_dump($res);die();
            if($res != false){
                if($res->SelectedRowsCount()>1){
                    while ($result = $res->Fetch()) {
                        $result_arr[$i] = $result;
                        $i++;
                    }
                    return $result_arr;
                }
                else{
                    $res = $res->Fetch();
                    $result = array();
                    $result[0] = $res;
                    return $result;
                }
            }
            else{
                return false;
            }


        }



    }
    function conn(){
        try {
            global $DB;
            return $DB;
        }
        catch (Exception $e) {
            echo "invalid sql -  - " . $e;
        }
    }
    function db_table_exist($table){

        return true;
    }

    function cackle_reviews_set_param($param, $value){
        COption::SetOptionString("cackle.reviews", $param, $value);
    }

        function  cackle_reviews_get_param($param,$def=""){
        return COption::GetOptionString("cackle.reviews", $param,$def);

    }


    function cackle_reviews_db_prepare(){

        if ($this->db_table_exist("".PREFIX."_reviews")){
            //    $this->db_connect("ALTER TABLE ".PREFIX."_reviews ADD user_agent VARCHAR(64) NOT NULL default ''");
            // $this->db_connect("ALTER TABLE ".PREFIX."_reviews MODIFY 'user_agent' varchar(64) NOT NULL default ''");
        }

    }
    function import_wordpress_reviews(&$wxr, $timestamp, $eof) {
        $postdata = http_build_query(
                array(
                    'siteId' =>$this->cackle_reviews_get_param("site_id_".$GLOBALS['cackle_reviews_site_name']),
                    'accountApiKey' => $this->cackle_reviews_get_param("account_api_".$GLOBALS['cackle_reviews_site_name']),
                    'siteApiKey' => $this->cackle_reviews_get_param("site_api_".$GLOBALS['cackle_reviews_site_name']),

                    'wxr' => $wxr,

                    'eof' => (int)$eof
                )
            );
            $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded;Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'content' => $postdata
            )
            );

            $context  = stream_context_create($opts);

            $response = file_get_contents('http://import.cackle.me/api/import-wordpress-reviews', false, $context);

        if ($response['body']=='fail') {
            $this->api->last_error = $response['body'];
            return -1;
        }
        $data = $response;
        if (!$data || $data== 'fail') {
            return -1;
        }

        return $data;
    }
    function get_last_error() {
        if (empty($this->last_error)) return;
        if (!is_string($this->last_error)) {
            return var_export($this->last_error);
        }
        return $this->last_error;
    }
    function curl($url) {

        return file_get_contents($url);
    }
    function key_validate($site_id, $site_api, $account_api)
    {
        global $cackle_reviews_api;
        $key_url = "http://cackle.me/api/2.0/site/info.json?id=$site_id&accountApiKey=$account_api&siteApiKey=$site_api";
        $key_response = file_get_contents($key_url);
        return $key_response;
    }

}