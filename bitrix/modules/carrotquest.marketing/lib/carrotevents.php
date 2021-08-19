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

require_once( $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/tools.php" );

/**
 * Class CarrotEvents
 *
 * @package CarrotQuest\Marketing
 */
class CarrotEvents
{
	public static $MODULE_ID          = "carrotquest.marketing";
	static        $UF_CARROTQUEST_UID = "UF_CARROTQUEST_UID";
	static        $TIMEOUT            = 3;

	static function onPageStart()
	{
		global $APPLICATION;

		if ( Option::get( self::$MODULE_ID, "api_key" ) ) {
			define( "CARROTQUEST_API_KEY", true );
		}

		if ( Option::get( self::$MODULE_ID, "api_secret" ) ) {
			define( "CARROTQUEST_API_SECRET", true );
		}

		if ( Option::get( self::$MODULE_ID, "api_auth_key" ) ) {
			define( "CARROTQUEST_API_AUTH_KEY", true );
		}

		if ( Option::get( self::$MODULE_ID, "sites_to_show" ) ) {
			define( "CARROTQUEST_RESTRICT_SITES", true );
			$allowedSites = explode( ',', Option::get( self::$MODULE_ID, "sites_to_show" ) );
		}

		if ( ( defined( "CARROTQUEST_API_KEY" ) || defined( "CARROTQUEST_API_SECRET" ) || defined( "CARROTQUEST_API_AUTH_KEY" ) )
			&& !CarrotEvents::SkipPage()
			&& defined( "CARROTQUEST_RESTRICT_SITES" ) && in_array( SITE_ID, $allowedSites ) ) {

			if ( defined( "CARROTQUEST_API_KEY" ) ) {
				$carrotquest_script = "
				<!-- Carrot quest BEGIN -->
				<script>
					!function(){function t(t,e){return function(){window.carrotquestasync.push(t,arguments)}}if('undefined'==typeof carrotquest){var e=document.createElement('script');e.async=!0,e.src='//cdn.carrotquest.app/api.min.js',document.getElementsByTagName('head')[0].appendChild(e),window.carrotquest={},window.carrotquestasync=[],carrotquest.settings={};for(var n=['connect','track','identify','auth','oth','onReady','addCallback','removeCallback','trackMessageInteraction'],a=0;a<n.length;a++)carrotquest[n[a]]=t(n[a])}}(),carrotquest.connect('" . Option::get( self::$MODULE_ID, "api_key" ) . "');
				</script >
				<!--Carrot quest END-->
				";

				$APPLICATION->AddHeadString( $carrotquest_script );
				$curPage = strtok( $_SERVER["REQUEST_URI"], '?' );
				switch ( $curPage ) {
					case Option::get( self::$MODULE_ID, "basket_page" ):
						CarrotEventsBasket::VisitedBasket();
						break;
				}

			}
		}
	}

	/**
	 * If current user authenticated and the stars align (needed settings match) - run auth script to save User ID to cq
	 * Unset previous product view state (needed to exclude random view event reiterations)
	 */
	static function onProlog()
	{
		$_SESSION["VIEWED_PRODUCT"] = 0;
		unset( $_SESSION["VIEWED_ENABLE"] );
	}

	static function OnEpilog()
	{
		$allowedSites = array();
		if ( Option::get( self::$MODULE_ID, "sites_to_show" ) ) {
			$allowedSites = explode( ',', Option::get( self::$MODULE_ID, "sites_to_show" ) );
		}
		if ( !defined( "ADMIN_SECTION" ) || ADMIN_SECTION !== true && !CarrotEvents::SkipPage() &&
			"Y" === strtoupper( \COption::GetOptionString( self::$MODULE_ID, "auth_users" ) )
			&& defined( "CARROTQUEST_API_AUTH_KEY" )
			&& defined( "CARROTQUEST_RESTRICT_SITES" ) && in_array( SITE_ID, $allowedSites ) ) {
			if ( class_exists( '\Bitrix\Main\Composite\StaticArea' ) ) {
				self::createDynamicArea();
			} else {
				self::legacyDynamicArea();
			}
		}
	}

	private static function legacyDynamicArea()
	{
		global $USER;
		$randomElementsFrame = new \Bitrix\Main\Page\FrameStatic( 'carrotquest_marketing' );
		$randomElementsFrame->setContainerID( 'carrotquest-marketing-container' );
		$randomElementsFrame->startDynamicArea();
		if ( $USER->isAuthorized() ) {
			$user_id = $USER->GetID();
			if ( isset( $user_id ) && $user_id > 0 ) {
				$hash               = hash_hmac( 'sha256', $user_id, \COption::GetOptionString( self::$MODULE_ID, "api_auth_key" ) );
				$carrotquest_script = "<script>carrotquest.auth('" . $user_id . "','" . $hash . "');</script>";
				\Bitrix\Main\Page\Asset::getInstance()->addString( $carrotquest_script );
			}
		}
		$randomElementsFrame->finishDynamicArea();

	}

	private static function createDynamicArea()
	{
		global $USER;

		$dynamicArea = new \Bitrix\Main\Composite\StaticArea( "carrotquest_marketing" );
		$dynamicArea->setStub( "" );
		$dynamicArea->startDynamicArea();
		if ( $USER->isAuthorized() ) {
			$user_id = $USER->GetID();
			if ( isset( $user_id ) && $user_id > 0 ) {
				$hash               = hash_hmac( 'sha256', $user_id, \COption::GetOptionString( self::$MODULE_ID, "api_auth_key" ) );
				$carrotquest_script = "<script>carrotquest.auth('" . $user_id . "','" . $hash . "');</script>";
				\Bitrix\Main\Page\Asset::getInstance()->addString( $carrotquest_script );
			}
		}
		$dynamicArea->finishDynamicArea();
	}


	/**
	 * Check if current page should contain cq widget (if page is skipped then no action of cq service can work on it)
	 *
	 * @return bool Skip current page if "true" returned
	 */
	private static function SkipPage()
	{
		$url                  = $_SERVER["REQUEST_URI"];
		$exception_pages      = explode( "\n", str_replace( "\r", "", Option::get( self::$MODULE_ID, "exception_pages" ) ) );
		$exclude_current_page = false;

		foreach ( $exception_pages as &$page ) {
			if ( fnmatch( $page, $url ) ) {
				$exclude_current_page = true;
			}
		}

		if ( strpos( $url, "ajax.php?UPDATE_STATE" ) !== false
			or ( isset( $_SERVER["HTTP_X_REQUESTED_WITH"] ) && strtolower( $_SERVER["HTTP_X_REQUESTED_WITH"] ) == "xmlhttprequest" )
			or ( isset( $_SERVER["HTTP_BX_AJAX"] ) && strtolower( $_SERVER["HTTP_BX_AJAX"] ) === 'y' )
			or ( isset( $_REQUEST["ajax"] ) && strtolower( $_REQUEST["ajax"] ) === 'y' )
			or strpos( $url, "/bitrix/admin/" ) !== false
			or strpos( $url, "/bitrix/tools/" ) !== false
			or strpos( $url, "/bitrix/components/" ) !== false
			or strpos( $url, "/ajax/" ) !== false
			or $exclude_current_page
		)
			return true;
		else return false;
	}

	/**
	 * Send lead event to Carrot quest
	 *
	 * @param $user_id - carrotquest_uid or User ID to identify user
	 * @param $event - event name
	 * @param array $params - event params (optional)
	 * @param bool $by_user_id - if $user_id contains cq uid or User ID
	 */
	public static function SendEvent( $user_id, $event, $params = array(), $by_user_id = false )
	{
		if ( $send_uid = self::GetSendUserID( $user_id, $by_user_id ) ) {
			$url  = "https://api.carrotquest.io/v1/users/" . $send_uid . "/events";
			$data = array(
				'auth_token' => "app." . Option::get( self::$MODULE_ID, "api_key" ) . "." . Option::get( self::$MODULE_ID, "api_secret" ),
				'event'      => $event,
			);

			if ( $by_user_id && $send_uid == $user_id ) {
				$data['by_user_id'] = true;
			}

			if ( count( $params ) > 0 ) {
				$data['params'] = json_encode( $params );
			}

			$httpClient = new HttpClient();
			$httpClient->setHeader( 'Content-Type', 'application/json', true );
			$httpClient->waitResponse( false );
			$httpClient->post( $url, json_encode( $data ) );
		}
	}

	/**
	 * Send lead properties to Carrot quest
	 *
	 * @param $user_id - carrotquest_uid or User ID to identify user
	 * @param $operations - array of props meta-data to correctly save props to cq  - array(array("op"=>"append","key"=>'prop key',"value"=>$value)
	 * @param bool $by_user_id - if $user_id contains cq uid or User ID
	 */
	public static function SendOperations( $user_id, $operations, $by_user_id = false )
	{
		if ( $send_uid = self::GetSendUserID( $user_id, $by_user_id ) ) {
			$url  = "https://api.carrotquest.io/v1/users/" . $send_uid . "/props";
			$data = array(
				'auth_token' => "app." . Option::get( self::$MODULE_ID, "api_key" ) . "." . Option::get( self::$MODULE_ID, "api_secret" ),
				'operations' => json_encode( $operations ),
			);

			if ( $by_user_id && $send_uid == $user_id ) {
				$data['by_user_id'] = true;
			}

			$httpClient = new HttpClient();
			$httpClient->setHeader( 'Content-Type', 'application/json', true );
			$httpClient->waitResponse( false );
			$httpClient->post( $url, json_encode( $data ) );
		}
	}

	/**
	 * Writes passed data to output file (mostly used for debug)
	 *
	 * @param string $title log line title
	 * @param string|null $text main log info
	 */
	public static function WriteLog( $title, $text = null )
	{
		$message = $title;
		if ( isset( $text ) && $text ) {
			$message .= ": \r\n" . (string)$text;
		}
		$fileResult = "[" . date( "Y-m-d H:i:s" ) . "] " . $message . "\r\n";
		$filePath   = $_SERVER['DOCUMENT_ROOT'] . '/carrot_integr_log.txt';
		$ret        = file_put_contents( $filePath, $fileResult, FILE_APPEND | LOCK_EX );
	}

	/**
	 * Gets carrotquest_uid from custom user field or cookies, if field is empty
	 *
	 * @param string|integer $user_id Bitrix User ID
	 * @return bool
	 */
	public static function GetCarrotquestUID( $user_id )
	{
		global $USER_FIELD_MANAGER, $USER;

		$entity_id         = "USER";
		$cookie_carrot_uid = $_COOKIE["carrotquest_uid"];
		$arr_UF            = $USER_FIELD_MANAGER->GetUserFields( $entity_id, $user_id );
		$current_user_id   = $USER->GetID();

		if ( $arr_UF[ self::$UF_CARROTQUEST_UID ] ) {
			$user_carrot_uid = $arr_UF[ self::$UF_CARROTQUEST_UID ]["VALUE"];
			if ( $user_carrot_uid == $cookie_carrot_uid || ( $user_id != $current_user_id ) ) {
				return $user_carrot_uid;
			} else if ( $user_id == $current_user_id ) {
				return self::SetCarrotquestUID( $user_id );
			}
		}

		return false;
	}

	/**
	 *  Save current carrotquest_uid from cookies to custom user field
	 *
	 * @param string|integer $user_id Bitrix User ID
	 * @return bool
	 */
	public static function SetCarrotquestUID( $user_id )
	{
		global $USER_FIELD_MANAGER;
		$entity_id = "USER";
		$uf_value  = $_COOKIE["carrotquest_uid"];
		if ( $uf_value ) {
			$USER_FIELD_MANAGER->Update( $entity_id, $user_id,
				array( self::$UF_CARROTQUEST_UID => $uf_value ) );
			return $uf_value;
		}

		return false;
	}

	/**
	 * Gets user's ID that will be used to identify user in cq (either cq uid, or bitrix User ID)
	 * If User Auth turned off in module settings it'll try to get cq id from user field
	 *
	 * @param string|integer $user_id carrotquest_uid or User ID to identify user
	 * @param bool $by_user_id if $user_id contains User ID or cq uid
	 * @return bool
	 */
	public static function GetSendUserID( $user_id, $by_user_id )
	{
		if ( $by_user_id ) {
			if ( strtoupper( \COption::GetOptionString( self::$MODULE_ID, "auth_users" ) ) == "Y" && defined( "CARROTQUEST_API_AUTH_KEY" ) ) {
				$sendUID = $user_id;
			} else {
				$sendUID = self::GetCarrotquestUID( $user_id );
			}
		} else if ( $user_id ) {
			$sendUID = $user_id;
		} else {
			$sendUID = false;
		}

		return $sendUID;
	}

	/**
	 * Checks if shop legacy events are turned on
	 * Used to differentiate between triggered product viewed events
	 *
	 * @param bool $consider_core_version
	 *
	 * @return bool
	 */
	public static function WatchLegacyEvents( $consider_core_version = false )
	{
		// TODO: in perspective it'd be better to check an actual version in which event (and libraries used in it's handler) first appeared
		if ( $consider_core_version ) {
			$legacy_version = true;
			if ( defined( "SM_VERSION" ) ) { //проверяем, задана ли версия CMS константой
				$vr             = constant( "SM_VERSION" );
				$versions       = explode( ".", $vr );
				$legacy_version = intval( $versions[0] ) < 16 || intval( $versions[0] ) == 16 && intval( $versions[1] ) < 5;
			}

			if ( $legacy_version ) {
				return true;
			}
		}

		$sale_watch_legacy_events = Option::get( "sale", "expiration_processing_events" );

		if ( $sale_watch_legacy_events === "Y" ) {
			$watch_old_events = Option::get( static::$MODULE_ID, "old_events", "Y" );
			if ( $watch_old_events === "N" ) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * Check if current site must work with this module
	 *
	 * @param string $siteId
	 * @return bool
	 */
	public static function WatchSite( $siteId = "" )
	{
		$siteId = $siteId ? $siteId : SITE_ID;
		if ( defined( "CARROTQUEST_RESTRICT_SITES" ) ) {
			$allowedSites = explode( ',', Option::get( self::$MODULE_ID, "sites_to_show" ) );
			return in_array( $siteId, $allowedSites );
		}

		return false;
	}
}
