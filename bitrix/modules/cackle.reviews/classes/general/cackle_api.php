<?php
/*Enter database settings*/
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
$site_name=CMainPage::GetSiteByHost();
global $MAIN_OPTIONS;
define('PREFIX', 'b');
global $APPLICATION;
global $cackle_site_id, $cackle_site_name, $cackle_site_api, $cackle_account_api, $cackle_sso, $cackle_encoding, $cackle_activated;
$cackle_site_id = COption::GetOptionString("cackle.reviews", "site_id_".$site_name, $_POST['siteId']);
$cackle_site_name = CMainPage::GetSiteByHost();
$cackle_account_api = COption::GetOptionString("cackle.reviews", "account_api_".$site_name, $_POST['accountApiKey']);
$cackle_sso = COption::GetOptionString("cackle.reviews", "cackle_sso_".$site_name, $_POST['enable_sso']);
$cackle_encoding = COption::GetOptionString("cackle.reviews", "cackle_encoding_".$site_name, $_POST['enable_encoding']);
$cackle_activated = COption::GetOptionString("cackle.reviews", "cackle_activated_".$site_name, $_POST['cackle_activated']);


class CackleReviewAPI{

    function __construct(){
        $this->site_id = $GLOBALS['cackle_site_id'];
        $this->site_api = $GLOBALS['$cackle_site_api'];
        $this->account_api = $GLOBALS['$cackle_account_api'];
        $this->cackle_sso =$GLOBALS['$cackle_sso'];
        $this->cackle_encoding = $GLOBALS['$cackle_encoding'];
        $this->cackle_activated = $GLOBALS['$cackle_activated'];

       // var_dump($GLOBALS['cackle_site_id']);
    }
    function db_connect($query,$return=true,$list=false){

        global $DB;
        global $site_name;
        if ($this->cackle_get_param("cackle_encoding_".$GLOBALS['cackle_site_name']) == 1){

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

    function cackle_set_param($param, $value){
        COption::SetOptionString("cackle.reviews", $param, $value);
    }

    public function  cackle_get_param($param){
        return COption::GetOptionString("cackle.reviews", $param);

    }


    function cackle_db_prepare(){

        if ($this->db_table_exist("".PREFIX."_reviews")){
            //    $this->db_connect("ALTER TABLE ".PREFIX."_reviews ADD user_agent VARCHAR(64) NOT NULL default ''");
            // $this->db_connect("ALTER TABLE ".PREFIX."_reviews MODIFY 'user_agent' varchar(64) NOT NULL default ''");
        }

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

}