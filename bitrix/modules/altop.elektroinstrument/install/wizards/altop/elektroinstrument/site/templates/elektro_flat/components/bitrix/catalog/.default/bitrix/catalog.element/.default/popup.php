<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

if($request->isPost() && check_bitrix_sessid()) {
	$action = $request->getPost("action");
	
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	
	$arParams = $request->getPost("arParams");
	if(!empty($arParams))
		$arParams = unserialize(base64_decode($signer->unsign($arParams, "catalog.element")));
	
	$settingProduct = $request->getPost("SETTING_PRODUCT");
	if(!empty($settingProduct))
		$settingProduct = unserialize(base64_decode($signer->unsign($settingProduct, "settings")));
	
	$iblockId = $request->getPost("IBLOCK_ID");
	$strMainId = $request->getPost("STR_MAIN_ID");
	$useCaptcha = $request->getPost("USE_CAPTCHA");
	
	$elementId = $request->getPost("ELEMENT_ID");	
	$elementAreaId = $request->getPost("ELEMENT_AREA_ID");
	$elementName = $request->getPost("ELEMENT_NAME");
	$elementPrice = $request->getPost("ELEMENT_PRICE");
	$elementCount = $request->getPost("ELEMENT_COUNT");
	
	switch($action) {		
		case "ask_price":
			//ASK_PRICE//			
			global $arAskPriceFilter;
			$arAskPriceFilter = array(
				"ELEMENT_ID" => $elementId,
				"ELEMENT_AREA_ID" => $action."_".$elementAreaId,
				"ELEMENT_NAME" => $elementName
			);
			$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));
			break;		
		case "under_order":
			//UNDER_ORDER//
			global $arUnderOrderFilter;
			$arUnderOrderFilter = array(
				"ELEMENT_ID" => $elementId,
				"ELEMENT_AREA_ID" => $action."_".$elementAreaId,
				"ELEMENT_NAME" => $elementName
			);
			$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));
			break;
		case "boc":
			//BUY_ONE_CLICK//
			$APPLICATION->IncludeComponent("altop:buy.one.click", "", 
				array(																
					"ELEMENT_ID" => $elementId,
					"ELEMENT_AREA_ID" => $elementAreaId,
					"USE_FILE_FIELD" => $arParams["1CB_USE_FILE_FIELD"],
					"FILE_FIELD_MULTIPLE" => $arParams["1CB_FILE_FIELD_MULTIPLE"],
					"FILE_FIELD_MAX_COUNT" => $arParams["1CB_FILE_FIELD_MAX_COUNT"],
					"FILE_FIELD_NAME" => $arParams["1CB_FILE_FIELD_NAME"],
					"FILE_FIELD_TYPE" => $arParams["1CB_FILE_FIELD_TYPE"],
					"REQUIRED" => $arParams["1CB_REQUIRED_FIELDS"],
					"BUY_MODE" => "ONE",		
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"]
				),
				false,
				array("HIDE_ICONS" => "Y")
			);
			break;
		case "cheaper":
			//CHEAPER//
			global $arCheaperFilter;
			$arCheaperFilter = array(
				"ELEMENT_ID" => $elementId,
				"ELEMENT_AREA_ID" => $action."_".$elementAreaId,
				"ELEMENT_NAME" => $elementName,
				"ELEMENT_PRICE" => $elementPrice
			);
			$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_cheaper.php"), false, array("HIDE_ICONS" => "Y"));
			break;
		case "subscribe":
			//SUBSCRIBE//
			$APPLICATION->includeComponent("bitrix:catalog.product.subscribe", "",
				array(
					"PRODUCT_ID" => $elementId,
					"USE_CAPTCHA" => $useCaptcha,
					"BUTTON_ID" => "subscribe_product_".$strMainId,
					"BUTTON_CLASS" => "btn_buy subscribe_anch"
				),
				false,
				array("HIDE_ICONS" => "Y")
			);
			break;
		case "delivery":
			//GEOLOCATION_DELIVERY//
			$APPLICATION->IncludeComponent("altop:geolocation.delivery", "",
				array(			
					"ELEMENT_ID" => $elementId,
					"ELEMENT_COUNT" => $elementCount,
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"]
				),
				false,
				array("HIDE_ICONS" => "Y")
			);
			break;
		case "constructor":
			//SET_CONSTRUCTOR//
			$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
				array(
					"IBLOCK_TYPE_ID" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $iblockId,						
					"ELEMENT_ID" => $elementId,		
					"BASKET_URL" => $arParams["BASKET_URL"],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
					"STR_MAIN_ID" => $strMainId,
					"SETTING_PRODUCT" => $settingProduct
				),
				false,
				array("HIDE_ICONS" => "Y")
			);
			break;
		case "store":
			//STORES//
			$APPLICATION->IncludeComponent("bitrix:catalog.store.amount",	".default",
				array(
					"ELEMENT_ID" => $elementId,
					"STORE_PATH" => $arParams["STORE_PATH"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"MAIN_TITLE" => $arParams["MAIN_TITLE"],
					"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
					"SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
					"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
					"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],									
					"STORES" => $arParams["STORES"],
					"SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
					"SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
					"USER_FIELDS" => $arParams["USER_FIELDS"],
					"FIELDS" => $arParams["FIELDS"]
				),
				false,
				array("HIDE_ICONS" => "Y")
			);
			break;
	}
	die();
}?>