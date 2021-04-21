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
	
	$elementId = $request->getPost("ELEMENT_ID");	
	$elementAreaId = $request->getPost("ELEMENT_AREA_ID");
	$elementName = $request->getPost("ELEMENT_NAME");
	
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
	}
	die();
}?>