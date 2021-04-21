<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$arUrlTempl = Array(
	"delete" => $APPLICATION->GetCurPage()."?action=delete&id=#ID#",
	"delay" => $APPLICATION->GetCurPage()."?action=delay&id=#ID#",
	"add" => $APPLICATION->GetCurPage()."?action=add&id=#ID#",
	"BasketClear" => $APPLICATION->GetCurPage()."?BasketClear=Y",
	"DelayClear" => $APPLICATION->GetCurPage()."?DelayClear=Y",		
);

if(strlen($arResult["ERROR_MESSAGE"]) <= 0) {?>	
	<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form">
		<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php");?>
		<input type="hidden" name="BasketOrder" value="BasketOrder" />
	</form>
<?} else {
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
}

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arParams)), "sale.basket.basket");?>

<script type="text/javascript">	
	BX.message({
		SBB_COMPONENT_PARAMS: "<?=CUtil::JSEscape($signedParams)?>"
	});
</script>

<?//GIFTS//
if($arParams["USE_GIFTS"] == "Y") {?>
	<?$APPLICATION->IncludeComponent("altop:sale.gift.basket", ".default",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SHOW_FROM_SECTION" => "N",
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_ELEMENT_ID" => "",
			"SECTION_ELEMENT_CODE" => "",
			"DEPTH" => "",
			"ELEMENT_SORT_FIELD" => "RAND",
			"ELEMENT_SORT_ORDER" => "ASC",
			"ELEMENT_SORT_FIELD2" => "",
			"ELEMENT_SORT_ORDER2" => "",
			"DETAIL_URL" => "",				
			"PAGE_ELEMENT_COUNT" => "4",
			"LINE_ELEMENT_COUNT" => "",
			"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
			"PROPERTY_CODE" => "",
			"PROPERTY_CODE_".$arParams["IBLOCK_ID"] => "",
			"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
			"PROPERTY_CODE_".$arResult["OFFERS_IBLOCK"] => $arParams["OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => "N",
			"SHOW_PRICE_COUNT" => "1",
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"USE_PRODUCT_QUANTITY" => "Y",		
			"BASKET_URL" => $APPLICATION->GetCurPage(),
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",		
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",			
			"PRODUCT_PROPS_VARIABLE" => "prop",			
			"PRODUCT_PROPERTIES" => "",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",			
			"CACHE_GROUPS" => "Y",
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"CART_PROPERTIES_".$arResult["OFFERS_IBLOCK"] => $arParams["OFFERS_CART_PROPERTIES"],
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"COMPARE_PATH" => "",
			"APPLIED_DISCOUNT_LIST" => $arResult["APPLIED_DISCOUNT_LIST"],
			"FULL_DISCOUNT_LIST" => $arResult["FULL_DISCOUNT_LIST"],
			"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
			"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
			"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
			"HIDE_BLOCK_TITLE" => $arParams["GIFTS_HIDE_BLOCK_TITLE"],
			"BLOCK_TITLE" => $arParams["GIFTS_BLOCK_TITLE"],
			"1CB_USE_FILE_FIELD" => $arParams["1CB_USE_FILE_FIELD"],
			"1CB_FILE_FIELD_MULTIPLE" => $arParams["1CB_FILE_FIELD_MULTIPLE"],
			"1CB_FILE_FIELD_MAX_COUNT" => $arParams["1CB_FILE_FIELD_MAX_COUNT"],
			"1CB_FILE_FIELD_NAME" => $arParams["1CB_FILE_FIELD_NAME"],
			"1CB_FILE_FIELD_TYPE" => $arParams["1CB_FILE_FIELD_TYPE"],
			"1CB_REQUIRED_FIELDS" => $arParams["1CB_REQUIRED_FIELDS"]
		),
		false,
		array("HIDE_ICONS" => "Y")
	);?>
<?}

//CART_ACCESSORIES//
if(!empty($arResult["ITEMS"]["ACCESSORIES"])) {
	global $arAcsCartFilter;
	$arAcsCartFilter = array(
		"ID" => array_unique($arResult["ITEMS"]["ACCESSORIES"]),
		"!ID" => $arResult["ITEMS"]["PARENT_PRODUCT_IDS"]
	);?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "filtered",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
			"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
			"ELEMENT_SORT_FIELD2" => "",
			"ELEMENT_SORT_ORDER2" => "",
			"PROPERTY_CODE" => "",
			"SET_META_KEYWORDS" => "N",		
			"SET_META_DESCRIPTION" => "N",		
			"SET_BROWSER_TITLE" => "N",
			"SET_LAST_MODIFIED" => "N",
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"BASKET_URL" => $APPLICATION->GetCurPage(),
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",
			"SECTION_ID_VARIABLE" => "SECTION_ID",
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"FILTER_NAME" => "arAcsCartFilter",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "Y",
			"SET_TITLE" => "N",
			"MESSAGE_404" => "",
			"SET_STATUS_404" => "N",
			"SHOW_404" => "N",
			"FILE_404" => "",
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"PAGE_ELEMENT_COUNT" => "8",
			"LINE_ELEMENT_COUNT" => "",
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => "N",
			"SHOW_PRICE_COUNT" => "1",
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"USE_PRODUCT_QUANTITY" => "Y",
			"ADD_PROPERTIES_TO_BASKET" => "",
			"PARTIAL_PRODUCT_PROPERTIES" => "",
			"PRODUCT_PROPERTIES" => "",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => GetMessage("SALE_ACCESSORIES_ITEMS"),
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => "",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
			"PAGER_SHOW_ALL" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"PAGER_BASE_LINK" => "",
			"PAGER_PARAMS_NAME" => "",
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_URL" => "",
			"DETAIL_URL" => "",
			"USE_MAIN_ELEMENT_SECTION" => "Y",
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"ADD_SECTIONS_CHAIN" => "N",		
			"COMPARE_PATH" => "",
			"BACKGROUND_IMAGE" => "",
			"DISABLE_INIT_JS_IN_COMPONENT" => "",
			"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
			"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
			"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"]
		),
		false,
		array("HIDE_ICONS" => "Y")
	);?>
<?}

//BIGDATA_ITEMS//
if(!empty($arResult["ITEMS"]["AnDelCanBuy"]) && (!isset($arParams["USE_BIG_DATA"]) || $arParams["USE_BIG_DATA"] != "N")) {
	$arProperty = array();
	$propCacheID = array("IBLOCK_ID" => $arParams["IBLOCK_ID"]);
	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($propCacheID), "/catalog/property")) {
		$arProperty = $obCache->GetVars();	
	} elseif($obCache->StartDataCache()) {
		$dbProperty = CIBlockProperty::GetPropertyEnum("THIS_COLLECTION", array(), array("IBLOCK_ID" => $arParams["IBLOCK_ID"]));
		if($arProp = $dbProperty->GetNext()) {
			$arProperty = array(
				"PROPERTY_ID" => $arProp["PROPERTY_ID"],
				"ID" => $arProp["ID"]
			);
		}
		$obCache->EndDataCache($arProperty);
	}
	global $arRecomPrFilter;
	$arRecomPrFilter = array(
		"SECTION_GLOBAL_ACTIVE" => "Y"
	);?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "bigdata",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => "RAND",
			"ELEMENT_SORT_ORDER" => "ASC",
			"ELEMENT_SORT_FIELD2" => "",
			"ELEMENT_SORT_ORDER2" => "",
			"PROPERTY_CODE" => "",
			"SET_META_KEYWORDS" => "N",		
			"SET_META_DESCRIPTION" => "N",		
			"SET_BROWSER_TITLE" => "N",
			"SET_LAST_MODIFIED" => "N",
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"CUSTOM_FILTER" => !empty($arProperty) ? "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[{\"CLASS_ID\":\"CondIBProp:".$arParams["IBLOCK_ID"].":".$arProperty["PROPERTY_ID"]."\",\"DATA\":{\"logic\":\"Not\",\"value\":".$arProperty["ID"]."}}]}" : "",
			"BASKET_URL" => $APPLICATION->GetCurPage(),
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",		
			"SECTION_ID_VARIABLE" => "SECTION_ID",		
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"FILTER_NAME" => "arRecomPrFilter",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "Y",
			"SET_TITLE" => "N",
			"MESSAGE_404" => "",
			"SET_STATUS_404" => "N",
			"SHOW_404" => "N",
			"FILE_404" => "",
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"PAGE_ELEMENT_COUNT" => "0",
			"LINE_ELEMENT_COUNT" => "4",
			"FILTER_IDS" => $arResult["ITEMS"]["PARENT_PRODUCT_IDS"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => "Y",
			"SHOW_PRICE_COUNT" => "1",
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"USE_PRODUCT_QUANTITY" => "Y",
			"ADD_PROPERTIES_TO_BASKET" => "",
			"PARTIAL_PRODUCT_PROPERTIES" => "",
			"PRODUCT_PROPERTIES" => "",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => "",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
			"PAGER_SHOW_ALL" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"PAGER_BASE_LINK" => "",
			"PAGER_PARAMS_NAME" => "",
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],			
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_URL" => "",
			"DETAIL_URL" => "",
			"USE_MAIN_ELEMENT_SECTION" => "Y",
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],			
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
			"ADD_SECTIONS_CHAIN" => "N",
			"RCM_TYPE" => isset($arParams["BIG_DATA_RCM_TYPE"]) ? $arParams["BIG_DATA_RCM_TYPE"] : "",
			"SHOW_FROM_SECTION" => "N",
			"BIG_DATA_TITLE" => "Y",
			"COMPARE_PATH" => "",
			"BACKGROUND_IMAGE" => "",
			"DISABLE_INIT_JS_IN_COMPONENT" => isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : "",
			"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':true}]",
			"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
			"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
			"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"]			
		),
		false
	);?>
<?}?>