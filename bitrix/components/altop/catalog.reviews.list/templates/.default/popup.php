<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

if($request->isPost() && check_bitrix_sessid()) {	
	$arParams = $request->getPost("arParams");
	
	//CATALOG_REVIEWS//?>
	<?$APPLICATION->IncludeComponent("altop:catalog.reviews", "",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_ID" => $arParams["ELEMENT_ID"],
			"ELEMENT_AREA_ID" => $arParams["ELEMENT_AREA_ID"],
			"COMMENT_URL" => $arParams["COMMENT_URL"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"]
		),
		false
	);?>
<?}?>