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
		$arParams = unserialize(base64_decode($signer->unsign($arParams, "sale.basket.basket")));

	$elementAreaId = $request->getPost("ELEMENT_AREA_ID");
	
	switch($action) {
		case "callback":
			//CALLBACK//
			$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_callback.php"), false, array("HIDE_ICONS" => "Y"));
			break;
		case "boc":
			//BUY_ONE_CLICK_CART//			
			$APPLICATION->IncludeComponent("altop:buy.one.click", "", 
				array(																
					"ELEMENT_ID" => "",
					"ELEMENT_AREA_ID" => $elementAreaId,
					"USE_FILE_FIELD" => $arParams["1CB_USE_FILE_FIELD"],
					"FILE_FIELD_MULTIPLE" => $arParams["1CB_FILE_FIELD_MULTIPLE"],
					"FILE_FIELD_MAX_COUNT" => $arParams["1CB_FILE_FIELD_MAX_COUNT"],
					"FILE_FIELD_NAME" => $arParams["1CB_FILE_FIELD_NAME"],
					"FILE_FIELD_TYPE" => $arParams["1CB_FILE_FIELD_TYPE"],
					"REQUIRED" => $arParams["1CB_REQUIRED_FIELDS"],
					"BUY_MODE" => "ALL",		
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000"
				),
				false,
				array("HIDE_ICONS" => "Y")
			);
			break;
	}
	die();
}?>