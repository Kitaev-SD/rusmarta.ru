<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

$bxajaxid = $_REQUEST["bxajaxid"];
if(!empty($bxajaxid)) {
	//JS//?>	
	<script type="text/javascript">
		//<![CDATA[
		BX.ready(function() {
			//ITEMS_HEIGHT//
			var itemsTable = $(".filtered-items:visible .catalog-item-card");
			if(!!itemsTable && itemsTable.length > 0) {
				$(window).resize(function() {
					adjustItemHeight(itemsTable);
				});
				adjustItemHeight(itemsTable);
			}
		});
		//]]>
	</script>
<?}

if($arParams["OFFERS_SORT_FIELD"] == "PRICE") {
	$dbPriceType = CCatalogGroup::GetList(array("SORT" => "ASC"),array('ID', 'NAME', 'CAN_ACCESS'));
	$flag = false;

	while ($arPriceType = $dbPriceType->Fetch()) {
	    if($arPriceType['CAN_ACCESS'] == "Y" && in_array($arPriceType['NAME'], $arParams["PRICE_CODE"])) {
	    	$arParams["OFFERS_SORT_FIELD_EL"] = "catalog_PRICE_".$arPriceType['ID'];
	    	$flag = true;
	    	break;
	    }
	}
	
	if(!$flag) {
		$arPriceBase = CCatalogGroup::GetBaseGroup();
    	$arParams["OFFERS_SORT_FIELD_EL"] = "catalog_PRICE_".$arPriceBase['ID'];
	}

} elseif($arParams["OFFERS_SORT_FIELD"] == "PROPERTIES") {
	$arParams["OFFERS_SORT_FIELD_EL"] = "SORT";
	$arParams["OFFERS_SORT_FIELD3"] = "PROPERTIES";
} else {
	$arParams["OFFERS_SORT_FIELD_EL"] = $arParams["OFFERS_SORT_FIELD"];
}

//ELEMENT//?>
<?$ElementID = $APPLICATION->IncludeComponent("bitrix:catalog.element", "",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
		"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"CHECK_SECTION_ID_VARIABLE" => (isset($arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"]) ? $arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"] : ""),
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
		"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
		"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
		"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
		"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
		"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],
		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD_EL"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_SORT_FIELD3" => $arParams["OFFERS_SORT_FIELD3"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
		"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
		"USE_ELEMENT_COUNTER" => $arParams["USE_ELEMENT_COUNTER"],
		"SHOW_DEACTIVATED" => $arParams["SHOW_DEACTIVATED"],
		"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
		"MAIN_BLOCK_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
		"MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),
		"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ""),		
		"DISPLAY_COMPARE" => (isset($arParams["USE_COMPARE"]) ? $arParams["USE_COMPARE"] : ""),
		"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
		"BACKGROUND_IMAGE" => (isset($arParams["DETAIL_BACKGROUND_IMAGE"]) ? $arParams["DETAIL_BACKGROUND_IMAGE"] : ""),
		"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
		"SET_VIEWED_IN_COMPONENT" => (isset($arParams["DETAIL_SET_VIEWED_IN_COMPONENT"]) ? $arParams["DETAIL_SET_VIEWED_IN_COMPONENT"] : ""),	
		"SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
		"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
		"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
		"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
		"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
        "USE_STORE" => $arParams["USE_STORE"],
		"STORE_PATH" => $arParams['STORE_PATH'],
		"MAIN_TITLE" => $arParams['MAIN_TITLE'],
		"USE_MIN_AMOUNT" => $arParams['USE_MIN_AMOUNT'],
		"MIN_AMOUNT" => $arParams['MIN_AMOUNT'],
		"STORES" => $arParams['STORES'],
		"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
		"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
		"USER_FIELDS" => $arParams['USER_FIELDS'],
		"FIELDS" => $arParams['FIELDS'],		
		"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
		"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
		"DISPLAY_DETAIL_IMG_WIDTH"	 =>	$arParams["DISPLAY_DETAIL_IMG_WIDTH"],
		"DISPLAY_DETAIL_IMG_HEIGHT" =>	$arParams["DISPLAY_DETAIL_IMG_HEIGHT"],
		"DISPLAY_MORE_PHOTO_WIDTH"	 =>	$arParams["DISPLAY_MORE_PHOTO_WIDTH"],
		"DISPLAY_MORE_PHOTO_HEIGHT" =>	$arParams["DISPLAY_MORE_PHOTO_HEIGHT"],		
		"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
		"IBLOCK_TYPE_REVIEWS" => $arParams["IBLOCK_TYPE_REVIEWS"],
		"IBLOCK_ID_REVIEWS" => $arParams["IBLOCK_ID_REVIEWS"],
		"BUTTON_PAYMENTS_HREF" => $arParams["BUTTON_PAYMENTS_HREF"],
		"BUTTON_CREDIT_HREF" => $arParams["BUTTON_CREDIT_HREF"],
		"BUTTON_DELIVERY_HREF" => $arParams["BUTTON_DELIVERY_HREF"],
		"AJAX_OPTION_HISTORY" => $arParams["AJAX_OPTION_HISTORY"],
		"AJAX_MODE" => $arParams["AJAX_MODE"],
		"STRICT_SECTION_CHECK" => $arParams["DETAIL_STRICT_SECTION_CHECK"],
		"USE_GIFTS_DETAIL" => $arParams["USE_GIFTS_DETAIL"],
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
		"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
		"1CB_USE_FILE_FIELD" => $arParams["1CB_USE_FILE_FIELD"],
		"1CB_FILE_FIELD_MULTIPLE" => $arParams["1CB_FILE_FIELD_MULTIPLE"],
		"1CB_FILE_FIELD_MAX_COUNT" => $arParams["1CB_FILE_FIELD_MAX_COUNT"],
		"1CB_FILE_FIELD_NAME" => $arParams["1CB_FILE_FIELD_NAME"],
		"1CB_FILE_FIELD_TYPE" => $arParams["1CB_FILE_FIELD_TYPE"],
		"1CB_REQUIRED_FIELDS" => $arParams["1CB_REQUIRED_FIELDS"],
		"NUMBER_ACCESSORIES" => $arParams["NUMBER_ACCESSORIES"],
		"COUNT_REVIEW" => $arParams["COUNT_REVIEW"]
	),
	$component
);?>

<?//CURRENT_ELEMENT//
$arCurElement = array();
$arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => $ElementID);
$obCache = new CPHPCache();
if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/catalog/element")) {
	$arCurElement = $obCache->GetVars();	
} elseif($obCache->StartDataCache()) {
	$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "PROPERTY_THIS_COLLECTION"));
	if($arElement = $rsElement->GetNext()) {
		$arCurElement = array(
			"SECTION_ID" => $arElement["IBLOCK_SECTION_ID"],
			"IS_COLLECTION" => $arElement["PROPERTY_THIS_COLLECTION_VALUE"] != false ? true : false
		);
	}
	$obCache->EndDataCache($arCurElement);
}
unset($arFilter);

if(!empty($arCurElement)) {
	//RELATED_ITEMS//
	if(!$arCurElement["IS_COLLECTION"] && $arParams["RELATED_PRODUCTS_SHOW"] !== "N") {	
		global $arRelPrFilter;
		$arRelPrFilter = Array("!ID" => $ElementID, "PROPERTY_THIS_COLLECTION" => false);?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "filtered",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_SORT_FIELD" => "RAND",
				"ELEMENT_SORT_ORDER" => "ASC",
				"ELEMENT_SORT_FIELD2" => "",
				"ELEMENT_SORT_ORDER2" => "",
				"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
				"SET_META_KEYWORDS" => "N",		
				"SET_META_DESCRIPTION" => "N",		
				"SET_BROWSER_TITLE" => "N",
				"SET_LAST_MODIFIED" => "N",
				"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"FILTER_NAME" => "arRelPrFilter",
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SET_TITLE" => "N",
				"MESSAGE_404" => "",
				"SET_STATUS_404" => "N",
				"SHOW_404" => "N",
				"FILE_404" => "",
				"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
				"PAGE_ELEMENT_COUNT" => "4",
				"LINE_ELEMENT_COUNT" => "",
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
				"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
				"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "N",
				"PAGER_TITLE" => Loc::getMessage("RELATED_ITEMS"),
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => "",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
				"PAGER_SHOW_ALL" => "N",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"PAGER_BASE_LINK" => "",
				"PAGER_PARAMS_NAME" => "",
				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
				"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
				"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
				"SECTION_ID" => !empty($arResult["VARIABLES"]["SECTION_ID"]) ? $arResult["VARIABLES"]["SECTION_ID"] : $arCurElement["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
				"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
				"ADD_SECTIONS_CHAIN" => "N",		
				"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
				"BACKGROUND_IMAGE" => "",
				"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
				"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
				"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
				"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
                "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
		        "MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
		        "RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
		        "MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
		        "MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
	}
	
	//BIGDATA_ITEMS//
	if(!isset($arParams["USE_BIG_DATA"]) || $arParams["USE_BIG_DATA"] != "N") {
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
				"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
				"SET_META_KEYWORDS" => "N",		
				"SET_META_DESCRIPTION" => "N",		
				"SET_BROWSER_TITLE" => "N",
				"SET_LAST_MODIFIED" => "N",
				"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],				
				"CUSTOM_FILTER" => !empty($arProperty) ? "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[{\"CLASS_ID\":\"CondIBProp:".$arParams["IBLOCK_ID"].":".$arProperty["PROPERTY_ID"]."\",\"DATA\":{\"logic\":\"Not\",\"value\":".$arProperty["ID"]."}}]}" : "",
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"FILTER_NAME" => "arRecomPrFilter",
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_FILTER" => $arParams["CACHE_FILTER"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SET_TITLE" => "N",
				"MESSAGE_404" => "",
				"SET_STATUS_404" => "N",
				"SHOW_404" => "N",
				"FILE_404" => "",
				"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
				"PAGE_ELEMENT_COUNT" => "0",
				"LINE_ELEMENT_COUNT" => "4",
				"FILTER_IDS" => array($ElementID),
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
				"ADD_PROPERTIES_TO_BASKET" => isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : "",
				"PARTIAL_PRODUCT_PROPERTIES" => isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : "",
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
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
				"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
				"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
				"SECTION_ID" => !empty($arResult["VARIABLES"]["SECTION_ID"]) ? $arResult["VARIABLES"]["SECTION_ID"] : $arCurElement["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
				"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
				"ADD_SECTIONS_CHAIN" => "N",
				"RCM_TYPE" => isset($arParams["BIG_DATA_RCM_TYPE"]) ? $arParams["BIG_DATA_RCM_TYPE"] : "",
				"RCM_PROD_ID" => $ElementID,
				"SHOW_FROM_SECTION" => !$arCurElement["IS_COLLECTION"] ? (isset($arParams["SHOW_FROM_SECTION"]) ? $arParams["SHOW_FROM_SECTION"] : "N") : "N",
				"BIG_DATA_TITLE" => "Y",
				"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
				"BACKGROUND_IMAGE" => "",
				"DISABLE_INIT_JS_IN_COMPONENT" => isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : "",
				"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':true}]",
				"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
				"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
				"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
                "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
		        "MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
		        "RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
		        "MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
		        "MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>
	<?}
}?>