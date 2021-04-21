<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

if($request->isPost() && check_bitrix_sessid()) {
	$action = $request->getPost("action");	
	$arParams = $request->getPost("arParams");
	
	switch($action) {
		case "ask_price":
			//ASK_PRICE//
			global $arAskPriceFilter;
			$arAskPriceFilter = array(
				"ELEMENT_ID" => $arParams["ELEMENT_ID"],
				"ELEMENT_AREA_ID" => $action."_".$arParams["ELEMENT_AREA_ID"],
				"ELEMENT_NAME" => $arParams["ELEMENT_NAME"]
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));?>
			<?break;		
		case "under_order":
			//UNDER_ORDER//
			global $arUnderOrderFilter;
			$arUnderOrderFilter = array(
				"ELEMENT_ID" => $arParams["ELEMENT_ID"],
				"ELEMENT_AREA_ID" => $action."_".$arParams["ELEMENT_AREA_ID"],
				"ELEMENT_NAME" => $arParams["ELEMENT_NAME"]
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));?>
			<?break;
		case "boc":
			//BUY_ONE_CLICK//
			global $arBuyOneClickFilter;
			$arBuyOneClickFilter = array(
				"ELEMENT_ID" => $arParams["ELEMENT_ID"],
				"ELEMENT_AREA_ID" => $arParams["ELEMENT_AREA_ID"],				
				"BUY_MODE" => "ONE"
			);?>
<?/*vp*/?>
<?$APPLICATION->IncludeComponent("altop:buy.one.click", "",  
                array(                                                                 
                    "ELEMENT_ID" => $arBuyOneClickFilter["ELEMENT_ID"],
                    "ELEMENT_AREA_ID" => $arBuyOneClickFilter["ELEMENT_AREA_ID"],
                    "BUY_MODE" => $arBuyOneClickFilter["BUY_MODE"],
                ), 
                false, 
                array("HIDE_ICONS" => "Y") 
);?>

			<?//$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_buy_one_click.php"), false, array("HIDE_ICONS" => "Y"));?>
<?/*vp*/?>			
			<?break;
		/*case "cheaper":
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
            break; */
        case "cheaper":
			//CHEAPER//
			global $arCheaperFilter;
			$arCheaperFilter = array(
				"ELEMENT_ID" => $arParams["ELEMENT_ID"],
				"ELEMENT_AREA_ID" => $action."_".$arParams["ELEMENT_AREA_ID"],
				"ELEMENT_NAME" => $arParams["ELEMENT_NAME"],
				"ELEMENT_PRICE" => $arParams["ELEMENT_PRICE"]
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_cheaper.php"), false, array("HIDE_ICONS" => "Y"));?>
			<?break;
		case "subscribe":
			//SUBSCRIBE//
			$arParams = CUtil::JsObjectToPhp($arParams);
			$arParams = str_replace("true", "Y", $arParams);
			$arParams = str_replace("false", "N", $arParams);
			$elementId = $request->getPost("ELEMENT_ID");
			$useCaptcha = $request->getPost("USE_CAPTCHA");
			$strMainId = $request->getPost("STR_MAIN_ID");?>
			<?$APPLICATION->includeComponent("bitrix:catalog.product.subscribe", "",
				array(
					"PRODUCT_ID" => $elementId,
					"USE_CAPTCHA" => $useCaptcha,
					"BUTTON_ID" => "subscribe_product_".$strMainId,
					"BUTTON_CLASS" => "btn_buy subscribe_anch"
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
			<?break;
		case "delivery":
			//GEOLOCATION_DELIVERY//
			$arParams = CUtil::JsObjectToPhp($arParams);
			$arParams = str_replace("true", "Y", $arParams);
			$arParams = str_replace("false", "N", $arParams);
			$elementId = $request->getPost("ELEMENT_ID");
			$elementCount = $request->getPost("ELEMENT_COUNT");?>
			<?$APPLICATION->IncludeComponent("altop:geolocation.delivery", "",
				array(			
					"ELEMENT_ID" => $elementId,
					"ELEMENT_COUNT" => $elementCount,
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"]
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
			<?break;
		case "constructor":
			//SET_CONSTRUCTOR//
			$arParams = CUtil::JsObjectToPhp($arParams);			
			$arParams = str_replace("true", "Y", $arParams);
			$arParams = str_replace("false", "N", $arParams);
			$iblockId = $request->getPost("IBLOCK_ID");
			$elementId = $request->getPost("ELEMENT_ID");
			$strMainId = $request->getPost("STR_MAIN_ID");
			$settingProduct = CUtil::JsObjectToPhp($request->getPost("SETTING_PRODUCT"));?>
			<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
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
			);?>
			<?break;
		case "store":
			//STORES//
			$arParams = CUtil::JsObjectToPhp($arParams);
			$arParams = str_replace("true", "Y", $arParams);
			$arParams = str_replace("false", "N", $arParams);
			$elementId = $request->getPost("ELEMENT_ID");?>
			<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount",	".default",
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
			);?>
			<?break;
	}
	die();
}?>