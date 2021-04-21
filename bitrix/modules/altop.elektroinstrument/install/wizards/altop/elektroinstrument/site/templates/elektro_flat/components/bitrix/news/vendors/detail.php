<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

use Bitrix\Main\Loader,
	Bitrix\Iblock,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager;
	
if(!Loader::includeModule("iblock"))
	return;

Loc::loadMessages(__FILE__);

global $arSetting;

//CURRENT_VENDOR//
$arFilter = array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ACTIVE" => "Y"
);
if(0 < intval($arResult["VARIABLES"]["ELEMENT_ID"])) {
	$arFilter["ID"] = $arResult["VARIABLES"]["ELEMENT_ID"];
} elseif("" != $arResult["VARIABLES"]["ELEMENT_CODE"]) {
	$arFilter["CODE"] = $arResult["VARIABLES"]["ELEMENT_CODE"];
}

$arSelect = array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT");

$cache_id = md5(serialize($arFilter));
$cache_dir = "/catalog/vendor";
$obCache = new CPHPCache();
if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_dir)) {
	$arCurVendor = $obCache->GetVars();	
} elseif($obCache->StartDataCache()) {
	$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($cache_dir);
	$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
	if($arElement = $rsElement->GetNext()) {	
		$arCurVendor["ID"] = $arElement["ID"];
		$arCurVendor["NAME"] = $arElement["NAME"];
		if($arElement["PREVIEW_PICTURE"] > 0)
			$arCurVendor["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
		$arCurVendor["PREVIEW_TEXT"] = $arElement["PREVIEW_TEXT"];
		$ipropValues = new Iblock\InheritedProperty\ElementValues($arElement["IBLOCK_ID"], $arElement["ID"]);
		$arCurVendor["IPROPERTY_VALUES"] = $ipropValues->getValues();
		$CACHE_MANAGER->EndTagCache();
		$obCache->EndDataCache($arCurVendor);
	} else {
		$CACHE_MANAGER->abortTagCache();
		Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ? : GetMessage("T_NEWS_DETAIL_NF")
			,true
			,$arParams["SET_STATUS_404"] === "Y"
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);
	}
}

if($arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS") {
	//SECTIONS//?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "vendors",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE_CATALOG"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],		
			"TOP_DEPTH" => "2",
			"SECTION_FIELDS" => array(),
			"SECTION_USER_FIELDS" => array(
				0 => "UF_ICON"
			),
			"SECTION_URL" => "",
			"ADD_SECTIONS_CHAIN" => "N",
			"DISPLAY_IMG_WIDTH" => "50",
			"DISPLAY_IMG_HEIGHT" => "50",
			"VENDOR_ID" => $arCurVendor["ID"],
			"VENDOR_NAME" => $arCurVendor["NAME"],
			"SEF_MODE" => $arParams["SEF_MODE"],
			"HIDE_SECTION" => $arParams["HIDE_SECTION"]
		),
		false,
		array("HIDE_ICONS" => "Y")
	);?>
<?} else {
	if($arSetting["VENDORS_VIEW"]["VALUE"] != "SECTIONS_PRODUCTS") {
		//COUNT//
		$arFilter = array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"],		
			"ACTIVE" => "Y",
			"PROPERTY_MANUFACTURER" => $arCurVendor["ID"]
		);
		$cache_id = md5(serialize($arFilter));
		$cache_dir = "/catalog/vendor/amount";
		$obCache = new CPHPCache();
		if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_dir)) {
			$count = $obCache->GetVars();
		} elseif($obCache->StartDataCache()) {		
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache($cache_dir);
			$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID_CATALOG"]);			
			$count = CIBlockElement::GetList(array(), $arFilter, array(), false);
			$CACHE_MANAGER->EndTagCache();
			$obCache->EndDataCache($count);
		}?>

		<div class="count_items">
			<label><?=Loc::getMessage("COUNT_ITEMS")?></label>
			<span><?=$count?></span>
		</div>

		<?//SORT//
		$arAvailableSort = array(
			"default" => array($arParams["ELEMENT_SORT_FIELD"], $arParams["ELEMENT_SORT_ORDER"]),
			"price" => array("PROPERTY_MINIMUM_PRICE", "asc"),
			"rating" => array("PROPERTY_rating", "desc"),
		);

		$sort = $APPLICATION->get_cookie("sort") ? $APPLICATION->get_cookie("sort") : $arParams["ELEMENT_SORT_FIELD"];

		if($_REQUEST["sort"]) {
			$sort = $arParams["ELEMENT_SORT_FIELD"];
			$APPLICATION->set_cookie("sort", $sort, false, "/", SITE_SERVER_NAME); 
		} 
		if($_REQUEST["sort"] == "price") {
			$sort = "PROPERTY_MINIMUM_PRICE";
			$APPLICATION->set_cookie("sort", $sort, false, "/", SITE_SERVER_NAME);
		}
		if($_REQUEST["sort"] == "rating") {
			$sort = "PROPERTY_rating";
			$APPLICATION->set_cookie("sort", $sort, false, "/", SITE_SERVER_NAME);
		}

		$sort_order = $APPLICATION->get_cookie("order") ? $APPLICATION->get_cookie("order") : $arParams["ELEMENT_SORT_ORDER"];

		if($_REQUEST["order"]) {
			$sort_order = "asc";	
			$APPLICATION->set_cookie("order", $sort_order, false, "/", SITE_SERVER_NAME);
		}
		if($_REQUEST["order"] == "desc") {
			$sort_order = "desc";
			$APPLICATION->set_cookie("order", $sort_order, false, "/", SITE_SERVER_NAME);
		}?>

		<div class="catalog-item-sorting">
			<label><span class="full"><?=Loc::getMessage("SECT_SORT_LABEL_FULL")?></span><span class="short"><?=Loc::getMessage("SECT_SORT_LABEL_SHORT")?></span>:</label>
			<?foreach($arAvailableSort as $key => $val) {
				$className = $sort == $val[0] ? "selected" : "";
				if($className) 
					$className .= $sort_order == "asc" ? " asc" : " desc";
				$newSort = $sort == $val[0] ? $sort_order == "desc" ? "asc" : "desc" : $arAvailableSort[$key][1];?>

				<a href="<?=$APPLICATION->GetCurPageParam("sort=".$key."&amp;order=".$newSort, array("sort", "order"))?>" class="<?=$className?>" rel="nofollow"><?=Loc::getMessage("SECT_SORT_".$key)?></a>
			<?}?>
		</div>
		
		<?//VIEW//
             $arAvailableView = array("table", "list", "price");

		$view = $APPLICATION->get_cookie("view") ? $APPLICATION->get_cookie("view") : (isset($arCurSection["VIEW"]) && !empty($arCurSection["VIEW"]) ? $arCurSection["VIEW"] : "table");

		if($_REQUEST["view"]) {
			$view = "table";	
			$APPLICATION->set_cookie("view", $view, false, "/", SITE_SERVER_NAME); 
		}
		if($_REQUEST["view"] == "list") {
			$view = "list";
			$APPLICATION->set_cookie("view", $view, false, "/", SITE_SERVER_NAME); 
		}
		if($_REQUEST["view"] == "price") {
			$view = "price";
			$APPLICATION->set_cookie("view", $view, false, "/", SITE_SERVER_NAME);
		}?>

		<div class="catalog-item-view">
			<?foreach($arAvailableView as $val) {?>
				<a href="<?=$APPLICATION->GetCurPageParam("view=".$val, array("view"))?>" class="<?=$val?><?if($view==$val) echo ' selected';?>" title="<?=Loc::getMessage('SECT_VIEW_'.$val)?>" rel="nofollow">
					<?if($val == "table") {?>
						<i class="fa fa-th-large"></i>
					<?} elseif($val == "list") {?>
						<i class="fa fa-list"></i>
					<?} elseif($val == "price") {?>
						<i class="fa fa-align-justify"></i>
					<?}?>
				</a>
			<?}?>
		</div>
		<div class="clr"></div>
	<?}
	
	//ELEMENTS//
	$arParams["PAGE_ELEMENT_COUNT"] = (int)$arParams["PAGE_ELEMENT_COUNT"] ?: 12;
	$arParams["LINE_ELEMENT_COUNT"] = 4;
	
	CBitrixComponent::includeComponentClass("bitrix:catalog.section");
	if(!isset($arParams["PRODUCT_ROW_VARIANTS"]) || empty($arParams["PRODUCT_ROW_VARIANTS"])) {		
		$arParams["PRODUCT_ROW_VARIANTS"] = Bitrix\Main\Web\Json::encode(CatalogSectionComponent::predictRowVariants($arParams["LINE_ELEMENT_COUNT"], $arParams["PAGE_ELEMENT_COUNT"]));
	}
	global $arVendorFilter;
	$arVendorFilter = array(	
		"PROPERTY_MANUFACTURER" => $arCurVendor["ID"],
		"PROPERTY_THIS_COLLECTION" => false
	);?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.section", $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? "sections" : "",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE_CATALOG"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"],
			"ELEMENT_SORT_FIELD" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? $arParams["ELEMENT_SORT_FIELD"] : $sort,
			"ELEMENT_SORT_ORDER" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? $arParams["ELEMENT_SORT_ORDER"] : $sort_order,
			"ELEMENT_SORT_FIELD2" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? $arParams["ELEMENT_SORT_FIELD2"] : "",
			"ELEMENT_SORT_ORDER2" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? $arParams["ELEMENT_SORT_ORDER2"] : "",
			"PROPERTY_CODE" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? "" : $arParams["PROPERTY_CODE"],
			"SET_META_KEYWORDS" => "N",		
			"SET_META_DESCRIPTION" => "N",		
			"SET_BROWSER_TITLE" => "N",		
			"SET_LAST_MODIFIED" => "N",
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"BASKET_URL" => "/personal/cart/",
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",		
			"SECTION_ID_VARIABLE" => "SECTION_ID",		
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"FILTER_NAME" => "arVendorFilter",
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SET_TITLE" => "N",
			"MESSAGE_404" => "",
			"SET_STATUS_404" => "N",
			"SHOW_404" => "N",
			"FILE_404" => "",
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"PAGE_ELEMENT_COUNT" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? "900" : $arParams["PAGE_ELEMENT_COUNT"],
			"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => "Y",
			"SHOW_PRICE_COUNT" => "1",
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"USE_PRODUCT_QUANTITY" => "Y",
			"ADD_PROPERTIES_TO_BASKET" => "",
			"PARTIAL_PRODUCT_PROPERTIES" => "",
			"PRODUCT_PROPERTIES" => "",
			"DISPLAY_TOP_PAGER" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? "N" : $arParams["DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? "N" : $arParams["DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? "" : $arCurVendor["NAME"],
			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
			"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
			"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
			"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
			"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
			"LAZY_LOAD" => (isset($arParams["LAZY_LOAD"]) ? $arParams["LAZY_LOAD"] : "Y"),
			"MESS_BTN_LAZY_LOAD" => (isset($arParams["~MESS_BTN_LAZY_LOAD"]) ? $arParams["~MESS_BTN_LAZY_LOAD"] : ""),
			"LOAD_ON_SCROLL" => (isset($arParams["LOAD_ON_SCROLL"]) ? $arParams["LOAD_ON_SCROLL"] : "Y"),
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
			"PRODUCT_ROW_VARIANTS" => $arParams["PRODUCT_ROW_VARIANTS"],
			"TYPE" => $view,
			"ADD_SECTIONS_CHAIN" => "N",		
			"COMPARE_PATH" => "",
			"BACKGROUND_IMAGE" => "",
			"DISABLE_INIT_JS_IN_COMPONENT" => "",
			"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
			"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
			"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
			"HIDE_SECTION" => $arSetting["VENDORS_VIEW"]["VALUE"] == "SECTIONS_PRODUCTS" ? $arParams["HIDE_SECTION"] : "",
            "SHOW_MAX_QUANTITY" => "M",
		    "MESS_SHOW_MAX_QUANTITY" => "В наличии",
		    "RELATIVE_QUANTITY_FACTOR" => "5",
		    "MESS_RELATIVE_QUANTITY_MANY" => "много",
		    "MESS_RELATIVE_QUANTITY_FEW" => "мало",
		),
		false,
		array("HIDE_ICONS" => "Y")
	);?>
<?}

//DESCRIPTION//
if(!empty($arCurVendor["PREVIEW_TEXT"])) {
	if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
		<div class="catalog_description">
			<?=$arCurVendor["PREVIEW_TEXT"];?>
		</div>
	<?}
}

//BIGDATA_ITEMS//
if(!isset($arParams["USE_BIG_DATA"]) || $arParams["USE_BIG_DATA"] != "N") {
	$arProperty = array();
	$propCacheID = array("IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"]);
	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($propCacheID), "/catalog/property")) {
		$arProperty = $obCache->GetVars();	
	} elseif($obCache->StartDataCache()) {
		$dbProperty = CIBlockProperty::GetPropertyEnum("THIS_COLLECTION", array(), array("IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"]));
		if($arProp = $dbProperty->GetNext()) {
			$arProperty = array(
				"PROPERTY_ID" => $arProp["PROPERTY_ID"],
				"ID" => $arProp["ID"]
			);
		}
		$obCache->EndDataCache($arProperty);
	}?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "bigdata",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE_CATALOG"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"],
			"ELEMENT_SORT_FIELD" => "RAND",
			"ELEMENT_SORT_ORDER" => "ASC",
			"ELEMENT_SORT_FIELD2" => "",
			"ELEMENT_SORT_ORDER2" => "",
			"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
			"SET_META_KEYWORDS" => "N",		
			"SET_META_DESCRIPTION" => "N",		
			"SET_BROWSER_TITLE" => "N",
			"SET_LAST_MODIFIED" => "N",
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"CUSTOM_FILTER" => !empty($arProperty) ? "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[{\"CLASS_ID\":\"CondIBProp:".$arParams["IBLOCK_ID_CATALOG"].":".$arProperty["PROPERTY_ID"]."\",\"DATA\":{\"logic\":\"Not\",\"value\":".$arProperty["ID"]."}}]}" : "",
			"BASKET_URL" => "/personal/cart/",
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",		
			"SECTION_ID_VARIABLE" => "SECTION_ID",		
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"FILTER_NAME" => "",
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
			"PAGE_ELEMENT_COUNT" => "0",
			"LINE_ELEMENT_COUNT" => "4",
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
			"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
			"SHOW_MAX_QUANTITY" => "M",
		    "MESS_SHOW_MAX_QUANTITY" => "В наличии",
		    "RELATIVE_QUANTITY_FACTOR" => "5",
		    "MESS_RELATIVE_QUANTITY_MANY" => "много",
		    "MESS_RELATIVE_QUANTITY_FEW" => "мало",
		),
		false
	);?>
<?}

//META_PROPERTY//
$APPLICATION->SetTitle(!empty($arCurVendor["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) ? $arCurVendor["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arCurVendor["NAME"]);
if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {
	$APPLICATION->SetPageProperty("title", !empty($arCurVendor["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]) ? $arCurVendor["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"] : $arCurVendor["NAME"]);
	$APPLICATION->SetPageProperty("keywords", !empty($arCurVendor["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]) ? $arCurVendor["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"] : "");
	$APPLICATION->SetPageProperty("description", !empty($arCurVendor["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]) ? $arCurVendor["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"] : (!empty($arCurVendor["PREVIEW_TEXT"]) ? strip_tags($arCurVendor["PREVIEW_TEXT"]) : ""));
} else {
	$APPLICATION->SetPageProperty("title", (!empty($arCurVendor["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]) ? $arCurVendor["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"] : $arCurVendor["NAME"])." | ".Loc::getMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
	$APPLICATION->SetPageProperty("keywords", "");
	$APPLICATION->SetPageProperty("description", "");
}

//OG_PROPERTY//
$APPLICATION->SetPageProperty("ogtype", "website");
if(is_array($arCurVendor["PREVIEW_PICTURE"])) {
	$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$arCurVendor["PREVIEW_PICTURE"]["SRC"]);
	$APPLICATION->SetPageProperty("ogimagewidth", $arCurVendor["PREVIEW_PICTURE"]["WIDTH"]);
	$APPLICATION->SetPageProperty("ogimageheight", $arCurVendor["PREVIEW_PICTURE"]["HEIGHT"]);
}

//CANONICAL//
if(!empty($_REQUEST["sort"]) || !empty($_REQUEST["order"]) || !empty($_REQUEST["limit"]) || !empty($_REQUEST["view"]) || !empty($_REQUEST["action"]) || !empty($_REQUEST["PAGEN_1"])) {
	$APPLICATION->AddHeadString("<link rel='canonical' href='".$APPLICATION->GetCurPage()."'>");	
}

//BREADCRUMBS//
if($arParams["ADD_ELEMENT_CHAIN"] != "N") {
	$APPLICATION->AddChainItem(!empty($arCurVendor["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) ? $arCurVendor["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arCurVendor["NAME"]);
}?>