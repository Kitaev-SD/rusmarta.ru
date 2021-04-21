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
		case "callback":
			//CALLBACK//?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_callback.php"), false, array("HIDE_ICONS" => "Y"));?>
			<?break;
		case "boc":
			//BUY_ONE_CLICK_CART//			
			global $arBuyOneClickFilter;
			$arBuyOneClickFilter = array(
				"ELEMENT_ID" => "",
				"ELEMENT_AREA_ID" => $arParams["ELEMENT_AREA_ID"],
				"BUY_MODE" => "ALL"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_buy_one_click.php"), false, array("HIDE_ICONS" => "Y"));?>
			<?break;
	}
	die();
}?>