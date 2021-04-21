<?php

namespace CarrotQuest\Marketing;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\EventResult;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Sale;
use Bitrix\Sale\Compatible\BasketCompatibility;
use Bitrix\Sale\Compatible\OrderCompatibility;
use Bitrix\Catalog;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/tools.php");

/**
 * Class CarrotEvents
 * @package CarrotQuest\Marketing
 */
class CarrotEvents
{
    public static $MODULE_ID = "carrotquest.marketing";
    static $UF_CARROTQUEST_UID = "UF_CARROTQUEST_UID";
    static $TIMEOUT = 3;

    static function onPageStart()
    {
        global $APPLICATION, $USER;
        if (Option::get(self::$MODULE_ID, "api_key")) {
            define("CARROTQUEST_API_KEY", true);
        }

        if (Option::get(self::$MODULE_ID, "api_secret")) {
            define("CARROTQUEST_API_SECRET", true);
        }

        if (Option::get(self::$MODULE_ID, "api_auth_key")) {
            define("CARROTQUEST_API_AUTH_KEY", true);
        }

        if (Option::get(self::$MODULE_ID, "sites_to_show")) {
            define("CARROTQUEST_RESTRICT_SITES", true);
            $allowedSites = explode(',', Option::get(self::$MODULE_ID, "sites_to_show"));
        }

        if ((defined("CARROTQUEST_API_KEY") || defined("CARROTQUEST_API_SECRET") || defined("CARROTQUEST_API_AUTH_KEY"))
            && !CarrotEvents::SkipPage()
            && (!defined("CARROTQUEST_RESTRICT_SITES") || defined("CARROTQUEST_RESTRICT_SITES") && in_array(SITE_ID, $allowedSites))) {

            if (defined("CARROTQUEST_API_KEY")) {
                $carrotquest_script = "
                    <!-- Bitrix CMS + Carrot quest BEGIN -->
                    <script defer type=\"text/javascript\">
                    
                    function carrot_timeout() {
                        function t(t,e){return function(){window.carrotquestasync.push(t,arguments)}}if(\"undefined\"==typeof carrotquest){var e=document.createElement(\"script\");e.type=\"text/javascript\",e.async=!0,e.src=\"//cdn.carrotquest.io/api.min.js\",document.getElementsByTagName(\"head\")[0].appendChild(e),window.carrotquest={},window.carrotquestasync=[],carrotquest.settings={};for(var n=[\"connect\",\"track\",\"identify\",\"auth\",\"oth\",\"onReady\",\"addCallback\",\"removeCallback\",\"trackMessageInteraction\"],a=0;a<n.length;a++)carrotquest[n[a]]=t(n[a])} 
                        carrotquest.connect('" . Option::get(self::$MODULE_ID, "api_key") . "');
                    } setTimeout(carrot_timeout, 5000);
                    
                    </script>
                    <!-- Carrot quest END -->
					";

                $APPLICATION->AddHeadString($carrotquest_script);
                $curPage = strtok($_SERVER["REQUEST_URI"], '?');

                switch ($curPage) {
                    case Option::get(self::$MODULE_ID, "basket_page"):
                        CarrotEventsBasket::VisitedBasket();
                        break;
                }

            }

            @session_start();
            $_SESSION["VIEWED_PRODUCT"] = 0;
            unset($_SESSION["VIEWED_ENABLE"]);
        }
    }

    static function onBeforeProlog(){
        global $APPLICATION, $USER;

        if (Option::get(self::$MODULE_ID, "sites_to_show")) {
            $allowedSites = explode(',', Option::get(self::$MODULE_ID, "sites_to_show"));
        }

        if (strtoupper(\COption::GetOptionString(self::$MODULE_ID, "auth_users")) == "Y"
            && defined("CARROTQUEST_API_AUTH_KEY")
            && $USER->isAuthorized()
            && !CarrotEvents::SkipPage()
            && (!defined("CARROTQUEST_RESTRICT_SITES") || defined("CARROTQUEST_RESTRICT_SITES") && in_array(SITE_ID, $allowedSites))) //если в системе есть AUTH_KEY добавляем склеивание пользователей
        {
            $userId = $USER->GetID();
            if ($userId > 0) {
                $hash = hash_hmac('sha256', $userId, \COption::GetOptionString(self::$MODULE_ID, "api_auth_key"));
                $carrotquest_script = "<script>carrotquest.auth('" . $userId . "','" . $hash . "');</script>";
            }
            $APPLICATION->AddHeadString($carrotquest_script);
        }
    }

    /**
     *
     * Проверка на необходимость отображения когда
     *
     * @return bool - если true - код не выведется на страницу
     */
    private static function SkipPage()
    {
        $url = $_SERVER["REQUEST_URI"];
        $exception_pages = explode("\n", str_replace("\r", "", Option::get(self::$MODULE_ID, "exception_pages")));
        if (strpos($url, "ajax.php?UPDATE_STATE") !== false
            or (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest")
            or (isset($_SERVER["HTTP_BX_AJAX"]) && strtolower($_SERVER["HTTP_BX_AJAX"]) === 'y')
            or (isset($_REQUEST["ajax"]) && strtolower($_REQUEST["ajax"]) === 'y')
            or strpos($url, "/bitrix/admin/") !== false
            or strpos($url, "/bitrix/tools/") !== false
            or strpos($url, "/bitrix/components/") !== false
            or strpos($url, "/ajax/") !== false
            or in_array($url, $exception_pages)
        )
            return true;
        else return false;
    }

    /**
     *
     * Метод для отправки событий в кэррот
     *
     * @param $userId - carrotquest_uid
     * @param $event - название события
     * @param array $params - свойства события
     * @param bool $by_user_id - отправка по user_id
     */
    public static function SendEvent($userId, $event, $params = array(), $by_user_id = false)
    {
        if ($send_uid = self::GetSendUserID($userId, $by_user_id)) {
            $url = "https://api.carrotquest.io/v1/users/" . $send_uid . "/events";
            $data = array(
                'auth_token' => "app." . Option::get(self::$MODULE_ID, "api_key") . "." . Option::get(self::$MODULE_ID, "api_secret"),
                'event' => $event
            );

            if ($by_user_id && $send_uid == $userId) {
                $data['by_user_id'] = true;
            }

            if (count($params) > 0) {
                $data['params'] = json_encode($params);
            }

            $httpClient = new HttpClient();
            $httpClient->setHeader('Content-Type', 'application/json', true);
            $httpClient->waitResponse(false);
            $httpClient->post($url, json_encode($data));
        }
    }

    /**
     *
     * Метод для отправки свойств в кэррот
     *
     * @param $userId - id по которому будет происходить отправка
     * @param $operations - массив с мета-данными для записи свойств - array(array("op"=>"append","key"=>'key',"value"=>$value)
     * @param bool $by_user_id - отправка по user_id
     */
    public static function SendOperations($userId, $operations, $by_user_id = false)
    {
        if ($send_uid = self::GetSendUserID($userId, $by_user_id)) {
            $url = "https://api.carrotquest.io/v1/users/" . $send_uid . "/props";
            $data = array(
                'auth_token' => "app." . Option::get(self::$MODULE_ID, "api_key") . "." . Option::get(self::$MODULE_ID, "api_secret"),
                'operations' => json_encode($operations),
            );

            if ($by_user_id && $send_uid == $userId) {
                $data['by_user_id'] = true;
            }

            $httpClient = new HttpClient();
            $httpClient->setHeader('Content-Type', 'application/json', true);
            $httpClient->waitResponse(false);
            $httpClient->post($url, json_encode($data));
        }
    }

    /**
     * @param $Title
     * @param null $Text
     */
    public static function WriteLog($Title, $Text = null)
    {
        $message = $Title;
        if ($Text != null && strlen($Text) > 0) {
            $message .= ": \r\n" . (string)$Text;
        }
        $fileResult = "[" . date("Y-m-d H:i:s") . "] " . $message . "\r\n";
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/carrot_integr_log.txt';
        $ret = file_put_contents($filePath, $fileResult, FILE_APPEND | LOCK_EX);
    }

    /**
     * Получение carrotquest_uid из свойств пользователя по id пользователя
     *
     * @param $user_id
     * @return bool
     */
    public static function GetCarrotquestUID($user_id)
    {
        global $USER_FIELD_MANAGER, $USER;

        $entity_id = "USER";
        $cookie_carrot_uid = $_COOKIE["carrotquest_uid"];
        $arr_UF = $USER_FIELD_MANAGER->GetUserFields($entity_id, $user_id);
        $current_user_id = $USER->GetID();

        if ($arr_UF[self::$UF_CARROTQUEST_UID]) {
            $user_carrot_uid = $arr_UF[self::$UF_CARROTQUEST_UID]["VALUE"];
            if ($user_carrot_uid == $cookie_carrot_uid || ($user_id != $current_user_id)) {
                return $user_carrot_uid;
            } else if ($user_id == $current_user_id) {
                return self::SetCarrotquestUID($user_id);
            }
        }

        return false;
    }

    /**
     * @param $userId
     * @return bool
     */
    public static function SetCarrotquestUID($userId)
    {
        global $USER_FIELD_MANAGER;
        $entity_id = "USER";
        $uf_value = $_COOKIE["carrotquest_uid"];
        if ($uf_value) {
            $USER_FIELD_MANAGER->Update($entity_id, $userId,
                Array(self::$UF_CARROTQUEST_UID => $uf_value));
            return $uf_value;
        }

        return false;
    }

    /**
     * @param $userId
     * @param $by_user_id
     * @return bool
     */
    public static function GetSendUserID($userId, $by_user_id)
    {
        if ($by_user_id) {
            if (strtoupper(\COption::GetOptionString(self::$MODULE_ID, "auth_users")) == "Y" && defined("CARROTQUEST_API_AUTH_KEY")) {
                $sendUID = $userId;
            } else {
                $sendUID = self::GetCarrotquestUID($userId);
            }
        } else if ($userId) {
            $sendUID = $userId;
        } else {
            $sendUID = false;
        }

        return $sendUID;
    }

    /**
     * @return bool
     */
    public static function WatchLegacyEvents()
    {
        $sale_watch_legacy_events = Option::get("sale", "expiration_processing_events");

        if ($sale_watch_legacy_events === "Y") {
            $watch_old_events = Option::get(static::$MODULE_ID, "old_events", "Y");
            if ($watch_old_events === "N") {
                return false;
            } else {
                return true;
            }
        } else {
          return false;
        }
    }
}