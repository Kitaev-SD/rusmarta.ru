<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Application;
	
$request = Application::getInstance()->getContext()->getRequest();

if($request->isPost() && $request->getPost("action") == "geolocationDelivery" && check_bitrix_sessid()) {
	$arParams = $request->getPost("arParams");
	if(SITE_CHARSET != "utf-8")
		$arParams = $APPLICATION->ConvertCharsetArray($arParams, "utf-8", SITE_CHARSET);

	$APPLICATION->IncludeComponent("altop:geolocation.delivery", "popup",
		array(		
			"ELEMENT_ID" => $arParams["ELEMENT_ID"],
			"ELEMENT_COUNT" => $arParams["ELEMENT_COUNT"],
			"CART_PRODUCTS" => $arParams["CART_PRODUCTS"],
			"AJAX_CALL" => "Y",
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"]
		),
		false,
		array("HIDE_ICONS" => "Y")
	);
}?>