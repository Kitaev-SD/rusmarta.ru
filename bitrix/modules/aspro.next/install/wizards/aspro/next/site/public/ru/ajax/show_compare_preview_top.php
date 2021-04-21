<?$bFromModule = (isset($arParams['FROM_MODULE']) && $arParams['FROM_MODULE'] == 'Y');
if(!$bFromModule)
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

	if(\Bitrix\Main\Loader::includeModule('aspro.next'))
		CNext::clearBasketCounters();
}


$APPLICATION->IncludeComponent(
	"bitrix:catalog.compare.list",
	"compare_top",
	Array(
		"IBLOCK_TYPE" => "#IBLOCK_NEXT_CATALOG_TYPE#",
		"IBLOCK_ID" => "#IBLOCK_CATALOG_ID#",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"DETAIL_URL" => "#SITE_DIR#catalog/#SECTION_CODE_PATH#/#ELEMENT_ID#/",
		"COMPARE_URL" => CNext::GetFrontParametrValue("COMPARE_PAGE_URL"),
		"CLASS_LINK" => (isset($arParams["CLASS_LINK"]) ? $arParams["CLASS_LINK"] : ""),
		"CLASS_ICON" => (isset($arParams["CLASS_ICON"]) ? $arParams["CLASS_ICON"] : ""),
		"NAME" => "CATALOG_COMPARE_LIST",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);

if(!$bFromModule)
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
}?>