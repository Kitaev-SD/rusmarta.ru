<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(false);

use Bitrix\Main\Loader,
	Bitrix\Iblock,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager;

if(!Loader::includeModule("iblock"))
	return;

Loc::loadMessages(__FILE__); 

global $arSetting;  

//CURRENT_SECTION//
$arFilter = array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"]
);
if(0 < intval($arResult["VARIABLES"]["SECTION_ID"])) {
	$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
} elseif("" != $arResult["VARIABLES"]["SECTION_CODE"]) {
	$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
    }

$arSelect = array("ID", "CODE", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "ACTIVE", "GLOBAL_ACTIVE", "PICTURE", "DESCRIPTION", "DEPTH_LEVEL", "SECTION_PAGE_URL", "UF_BANNER", "UF_BANNER_URL", "UF_BACKGROUND_IMAGE", "UF_PREVIEW", "UF_VIEW", "UF_VIEW_COLLECTION", "UF_SECTION_TITLE_H1", "UF_YOUTUBE_BG");

$arproductType = array("newproduct", "saleleader", "discount");

$cache_id = md5(serialize($arFilter));
$cache_dir = "/catalog/section";
$obCache = new CPHPCache();
if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_dir)) {
	$arCurSection = $obCache->GetVars();
} elseif($obCache->StartDataCache()) {
	$arCurSection = array();
	$rsSections = CIBlockSection::GetList(array(), $arFilter, false, $arSelect);
	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($cache_dir);
	$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);	
	if($arSection = $rsSections->Fetch()) {		
		if(in_array($arSection["CODE"], $arproductType) || $arSection["ACTIVE"] == "Y" || $arSection["GLOBAL_ACTIVE"] == "Y") {
			if(in_array($arSection["CODE"], $arproductType))
				$arCurSection["PRODUCT_TYPE"] = true;
			$arCurSection["ID"] = $arSection["ID"];
			$arCurSection["CODE"] = $arSection["CODE"];
			$arCurSection["NAME"] = $arSection["NAME"];
			if($arSection["PICTURE"] > 0)
				$arCurSection["PICTURE"] = CFile::GetFileArray($arSection["PICTURE"]);
			$arCurSection["DESCRIPTION"] = $arSection["DESCRIPTION"];
			$arCurSection["SECTION_PAGE_URL"] = $arSection["~SECTION_PAGE_URL"];
			$arCurSection["BANNER"] = array(
				"PICTURE" => $arSection["UF_BANNER"] > 0 ? CFile::GetFileArray($arSection["UF_BANNER"]) : "",
				"URL" => $arSection["UF_BANNER_URL"]
			);		
			$arCurSection["PREVIEW"] = $arSection["UF_PREVIEW"];
			if($arSection["UF_VIEW_COLLECTION"] > 0) {
				$arCurSection["VIEW_COLLECTION"] = true;
			};
			$arCurSection["SECTION_TITLE_H1"] = $arSection["UF_SECTION_TITLE_H1"];
			if(isset($arSection["UF_YOUTUBE_BG"]) && !empty($arSection["UF_YOUTUBE_BG"])) {
				$arCurSection["BACKGROUND_YOUTUBE"] = $arSection["UF_YOUTUBE_BG"];
			}
			if($arSection["UF_VIEW"] > 0) {
				$UserField = CUserFieldEnum::GetList(array(), array("ID" => $arSection["UF_VIEW"]));
				if($UserFieldAr = $UserField->Fetch()) {
					$arCurSection["VIEW"] = $UserFieldAr["XML_ID"];
				}
			};
			if(($arSection["UF_BACKGROUND_IMAGE"] <= 0 || $arSection["UF_VIEW"] <= 0 || empty($arSection["UF_YOUTUBE_BG"])) && $arSection["DEPTH_LEVEL"] > 1) {
				if($arSection["DEPTH_LEVEL"] > 2) {
					$rsParentSectionPath = CIBlockSection::GetNavChain($arSection["IBLOCK_ID"], $arSection["IBLOCK_SECTION_ID"]);
					while($arParentSectionPath = $rsParentSectionPath->GetNext()) {
						$parentSectionPathIds[] = $arParentSectionPath["ID"];
					}
				} else {
					$parentSectionPathIds = $arSection["IBLOCK_SECTION_ID"];
				}
				if(!empty($parentSectionPathIds)) {
					$rsSections = CIBlockSection::GetList(
						array("DEPTH_LEVEL" => "DESC"),	
						array("IBLOCK_ID" => $arSection["IBLOCK_ID"], "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "ID" => $parentSectionPathIds),
						false,
						array("ID", "IBLOCK_ID", "DEPTH_LEVEL", "UF_BACKGROUND_IMAGE", "UF_VIEW", "UF_YOUTUBE_BG")
					);
					while($arSection = $rsSections->GetNext()) {						
						if(!isset($arCurSection["BACKGROUND_IMAGE"]) && $arSection["UF_BACKGROUND_IMAGE"] > 0) {
							$arCurSection["BACKGROUND_IMAGE"] = CFile::GetFileArray($arSection["UF_BACKGROUND_IMAGE"]);
						}
						if(!isset($arCurSection["VIEW"]) && $arSection["UF_VIEW"] > 0) {
							$UserField = CUserFieldEnum::GetList(array(), array("ID" => $arSection["UF_VIEW"]));
							if($UserFieldAr = $UserField->Fetch()) {
								$arCurSection["VIEW"] = $UserFieldAr["XML_ID"];						
							}
						}
						if(!isset($arCurSection["BACKGROUND_YOUTUBE"]) && !empty($arSection["UF_YOUTUBE_BG"])) {
							$arCurSection["BACKGROUND_YOUTUBE"] = $arSection["UF_YOUTUBE_BG"];
						}
					}
				}
			}
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arSection["IBLOCK_ID"], $arSection["ID"]);
			$arCurSection["IPROPERTY_VALUES"] = $ipropValues->getValues();
		}
	}
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache($arCurSection);
} else {
	$arCurSection = array();
}

//FILTER_SEO
if($arParams["USE_FILTER_SEO"] == "Y" && !empty($arParams["USE_FILTER_SEO_IBLOCK"])) {
    $pageUrl = $APPLICATION->GetCurPage();   
    $pageSeo = array();    
    $arSelectSeo = Array("ID", "NAME", "PREVIEW_TEXT", "PROPERTY_FILTER_TITLE", "PROPERTY_FILTER_KEYWORDS", "PROPERTY_FILTER_DESCRIPTION", "PROPERTY_FILTER_HEADER", "PROPERTY_FILTER_SEO_TEXT");
    $arFilterSeo = Array("IBLOCK_ID" => $arParams["USE_FILTER_SEO_IBLOCK"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "PROPERTY_FILTER_URL_PAGE" => $pageUrl);
    $res = CIBlockElement::GetList(Array(), $arFilterSeo, false, false, $arSelectSeo);
    while($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();      
        $pageSeo = array(
            "NAME"=>$arFields["NAME"],
            "TITLE" => $arFields["PROPERTY_FILTER_TITLE_VALUE"],
            "KEYWORDS" => $arFields["PROPERTY_FILTER_KEYWORDS_VALUE"],
            "DESCRIPTION" => $arFields["PROPERTY_FILTER_DESCRIPTION_VALUE"]["TEXT"],
            "HEADER" => $arFields["PROPERTY_FILTER_HEADER_VALUE"],
            "SEO_TEXT" => $arFields["PREVIEW_TEXT"],
            "PREVIEW_TEXT" => $arFields["PROPERTY_FILTER_SEO_TEXT_VALUE"]["TEXT"], 
        );
    }    
}

if(!empty($arCurSection)) {
	//BANNER//
	if(is_array($arCurSection["BANNER"]["PICTURE"])) {?>
		<div class="catalog-item-banner">
			<a href="<?=!empty($arCurSection["BANNER"]["URL"]) ? $arCurSection["BANNER"]["URL"] : 'javascript:void(0)'?>">
				<img src="<?=$arCurSection['BANNER']['PICTURE']['SRC']?>" width="<?=$arCurSection['BANNER']['PICTURE']['WIDTH']?>" height="<?=$arCurSection['BANNER']['PICTURE']['HEIGHT']?>" alt="<?=$arCurSection['NAME']?>" title="<?=$arCurSection['NAME']?>" />
			</a>
		</div>
	<?}

	//SUBSECTION//
	if(!$arCurSection["PRODUCT_TYPE"]) {?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],			
				"TOP_DEPTH" => "1",
				"SECTION_FIELDS" => array(),
				"SECTION_USER_FIELDS" => array(
					0 => "UF_ICON"
				),
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),
				"DISPLAY_IMG_WIDTH"	 =>	"50",
				"DISPLAY_IMG_HEIGHT" =>	"50"
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>
	<?}
	
	//PREVIEW//
	if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
		<div class="catalog_preview"><?=(!empty($arCurSection["PREVIEW"]) && empty($pageSeo["PREVIEW_TEXT"]) ? $arCurSection["PREVIEW"] : $pageSeo["PREVIEW_TEXT"])?></div>
	<?}

	//FILTER//
	if($arParams["USE_FILTER"] == "Y" && $arSetting["SMART_FILTER_VISIBILITY"]["VALUE"] != "DISABLE" && !$arCurSection["PRODUCT_TYPE"] && !$arCurSection["VIEW_COLLECTION"]) {?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.smart.filter", "elektro",
			Array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"SECTION_ID" => $arCurSection["ID"],
				"FILTER_NAME" => $arParams["FILTER_NAME"],
				"PRICE_CODE" => $arParams["FILTER_PRICE_CODE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SAVE_IN_SESSION" => "N",
				"FILTER_VIEW_MODE" => "",			
				"XML_EXPORT" => "N",
				"SECTION_TITLE" => "NAME",
				"SECTION_DESCRIPTION" => "DESCRIPTION",
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"TEMPLATE_THEME" => "",
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"SEF_MODE" => $arParams["SEF_MODE"],
				"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
				"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
				"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
				"INSTANT_RELOAD" => ($arParams["INSTANT_RELOAD"] === 'Y' && $arParams["AJAX_MODE"] === 'Y'? 'Y': 'N')
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>	

		<div class="filter_indent<?=($arSetting['SMART_FILTER_LOCATION']['VALUE'] == 'VERTICAL') ? ' vertical' : '';?> clr"></div>
		
		<?global $arSmartFilter;
	} else {
		$arSmartFilter = array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],		
			"ACTIVE" => "Y",
			"SECTION_GLOBAL_ACTIVE" => "Y"
		);
		if(!$arCurSection["PRODUCT_TYPE"]) {
			$arSmartFilter["SECTION_ID"] = $arCurSection["ID"];
			$arSmartFilter["INCLUDE_SUBSECTIONS"] = "Y";
		}
		if($arCurSection["PRODUCT_TYPE"]) {			
			$arSmartFilter["!PROPERTY_".strtoupper($arCurSection["CODE"])] = false;
			$arSmartFilter["PROPERTY_THIS_COLLECTION"] = false;
		} elseif($arCurSection["VIEW_COLLECTION"]) {
			$arSmartFilter["!PROPERTY_THIS_COLLECTION"] = false;
		}
	}
} elseif(in_array($arResult["VARIABLES"]["SECTION_CODE"], $arproductType)) {
	$arCurSection["PRODUCT_TYPE"] = true;
	$arCurSection["CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
	$arCurSection["NAME"] = Loc::getMessage($arCurSection["CODE"]."_TITLE");
	$arCurSection["SECTION_PAGE_URL"] = $arResult["FOLDER"].$arResult["VARIABLES"]["SECTION_CODE"]."/";
	$arSmartFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],		
		"ACTIVE" => "Y",
		"SECTION_GLOBAL_ACTIVE" => "Y",
		"!PROPERTY_".strtoupper($arCurSection["CODE"]) => false,
		"PROPERTY_THIS_COLLECTION" => false
	);
}

//COUNT//
$cache_id = md5(serialize($arSmartFilter));
$cache_dir = "/catalog/amount";
$obCache = new CPHPCache();
if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_dir)) {
	$count = $obCache->GetVars();
} elseif($obCache->StartDataCache()) {		
	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($cache_dir);
	$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
	$count = CIBlockElement::GetList(array(), $arSmartFilter, array(), false);
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache($count);
}?>

<div class="count_items">
	<?if(!$arCurSection["VIEW_COLLECTION"]) {?>
		<label><?=Loc::getMessage("COUNT_ITEMS")?></label>
	<?} else {?>
		<label><?=Loc::getMessage("COUNT_COLLECTION")?></label>
	<?}?>	
	<span><?=$count?></span>
</div>

<?//SORT//
if(!$arCurSection["VIEW_COLLECTION"]) {
	$arAvailableSort = array(
		"default" => array($arParams["ELEMENT_SORT_FIELD"], $arParams["ELEMENT_SORT_ORDER"]),
		"price" => array("PROPERTY_MINIMUM_PRICE", "asc"),
		"rating" => array("PROPERTY_rating", "desc"),
	);
} else {
	$arAvailableSort = array(
		"default" => array($arParams["ELEMENT_SORT_FIELD"], $arParams["ELEMENT_SORT_ORDER"]),
		"rating" => array("PROPERTY_rating", "desc"),
	);
}

$sort = $APPLICATION->get_cookie("sort") ? $APPLICATION->get_cookie("sort") : $arParams["ELEMENT_SORT_FIELD"];

//AJAX_MODE//
if($arParams["AJAX_MODE"] == "Y") {
	if($_REQUEST["sort"])
		$_SESSION["sort"] = $_REQUEST["sort"];
	
	if($_SESSION["sort"])
		$_REQUEST["sort"] = $_SESSION["sort"];
}

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

//AJAX_MODE//
if($arParams["AJAX_MODE"] == "Y") {
	if($_REQUEST["order"])
		$_SESSION["order"] = $_REQUEST["order"];
	
	if($_SESSION["order"])
		$_REQUEST["order"] = $_SESSION["order"];
}

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
if(!$arCurSection["VIEW_COLLECTION"]) {
	$arAvailableView = array("table", "list", "price");

	$view = $APPLICATION->get_cookie("view") ? $APPLICATION->get_cookie("view") : (isset($arCurSection["VIEW"]) && !empty($arCurSection["VIEW"]) ? $arCurSection["VIEW"] : "table");

	//AJAX_MODE//
	if($arParams["AJAX_MODE"] == "Y") {
		if($_REQUEST["view"])
			$_SESSION["view"] = $_REQUEST["view"];
		
		if($_SESSION["view"])
			$_REQUEST["view"] = $_SESSION["view"];
	}

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
<?} else {
	$view = "collections";
	$arParams["DISPLAY_IMG_WIDTH"] = "480";
	$arParams["DISPLAY_IMG_HEIGHT"] = "255";
}?>
<div class="clr"></div>

<?//SECTION//
if($arCurSection["PRODUCT_TYPE"]) {
	global ${$arParams["FILTER_NAME"]};
	${$arParams["FILTER_NAME"]} = array(
		"SECTION_GLOBAL_ACTIVE" => "Y",
		"!PROPERTY_".strtoupper($arCurSection["CODE"]) => false,
		"PROPERTY_THIS_COLLECTION" => false
	);
} elseif($arCurSection["VIEW_COLLECTION"]) {
	global ${$arParams["FILTER_NAME"]};
	${$arParams["FILTER_NAME"]} = array(
		"SECTION_GLOBAL_ACTIVE" => "Y",
		"!PROPERTY_THIS_COLLECTION" => false
	);
}

$arParams["PAGE_ELEMENT_COUNT"] = (int)$arParams["PAGE_ELEMENT_COUNT"] ?: 12;
$arParams["LINE_ELEMENT_COUNT"] = !$arCurSection["VIEW_COLLECTION"] ? 4 : 3;

CBitrixComponent::includeComponentClass("bitrix:catalog.section");

if(!isset($arParams["LIST_PRODUCT_ROW_VARIANTS"]) || empty($arParams["LIST_PRODUCT_ROW_VARIANTS"])) {		
	$arParams["LIST_PRODUCT_ROW_VARIANTS"] = Bitrix\Main\Web\Json::encode(CatalogSectionComponent::predictRowVariants($arParams["LINE_ELEMENT_COUNT"], $arParams["PAGE_ELEMENT_COUNT"]));
}
if($arCurSection["VIEW_COLLECTION"]) {
	$arParams["LIST_PRODUCT_ROW_VARIANTS"] = Bitrix\Main\Web\Json::encode(CatalogSectionComponent::predictRowVariants($arParams["LINE_ELEMENT_COUNT"], $arParams["PAGE_ELEMENT_COUNT"]));
}?>
<?$intSectionID = $APPLICATION->IncludeComponent("bitrix:catalog.section", "",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => $sort,
		"ELEMENT_SORT_ORDER" => $sort_order,
		"ELEMENT_SORT_FIELD2" => "",
		"ELEMENT_SORT_ORDER2" => "",
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"SHOW_ALL_WO_SECTION" => $arCurSection["PRODUCT_TYPE"] ? "Y" : "N",
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
		"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
		"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
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
		"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
		"SECTION_ID" => !$arCurSection["PRODUCT_TYPE"] ? $arResult["VARIABLES"]["SECTION_ID"] : "",
		"SECTION_CODE" => !$arCurSection["PRODUCT_TYPE"] ? $arResult["VARIABLES"]["SECTION_CODE"] : "",
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
		"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
		"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],		
		"PRODUCT_ROW_VARIANTS" => $arParams["LIST_PRODUCT_ROW_VARIANTS"],
		"TYPE" => $view,
		"ADD_SECTIONS_CHAIN" => "N",		
		"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
		"BACKGROUND_IMAGE" => (isset($arParams["SECTION_BACKGROUND_IMAGE"]) ? $arParams["SECTION_BACKGROUND_IMAGE"] : ""),
		"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
		"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
		"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
		"PROPERTY_CODE_MOD" => !$arCurSection["VIEW_COLLECTION"] ? $arParams["PROPERTY_CODE_MOD"] : "",
        "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
		"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
		"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
		"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
		"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
	),
	$component
);?>

<?//DESCRIPTION//
if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
	<div class="catalog_preview"><?=(!empty($arCurSection["DESCRIPTION"]) && empty($pageSeo["SEO_TEXT"]) ? $arCurSection["DESCRIPTION"] : $pageSeo["SEO_TEXT"])?></div>
<?}

//FILTER_SEO_ADD_CHAIN//
if($arParams["USE_FILTER_SEO"] == "Y" && !empty($arParams["USE_FILTER_SEO_IBLOCK"]) && !empty($pageSeo["NAME"]))     
     $APPLICATION->AddChainItem($pageSeo["NAME"]); 

//GIFTS//
if($arParams["USE_GIFTS_SECTION"] == "Y" && ModuleManager::isModuleInstalled("sale")) {?>
	<?$APPLICATION->IncludeComponent("bitrix:sale.products.gift.section", "",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SHOW_FROM_SECTION" => "N",
			"SECTION_ID" => $intSectionID,
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"SECTION_ELEMENT_ID" => "",
			"SECTION_ELEMENT_CODE" => "",
			"DEPTH" => "",
			"ELEMENT_SORT_FIELD" => "RAND",
			"ELEMENT_SORT_ORDER" => "ASC",
			"ELEMENT_SORT_FIELD2" => "",
			"ELEMENT_SORT_ORDER2" => "",
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false}]",
			"PAGE_ELEMENT_COUNT" => 4,			
			"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
			"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],			
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],			
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"ADD_PROPERTIES_TO_BASKET" => isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : "",
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"PARTIAL_PRODUCT_PROPERTIES" => isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : "",
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],			
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
			"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
			"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
			"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
			"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
			"HIDE_BLOCK_TITLE" => $arParams["GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE"],
			"BLOCK_TITLE" => $arParams["GIFTS_SECTION_LIST_BLOCK_TITLE"],
			"1CB_USE_FILE_FIELD" => $arParams["1CB_USE_FILE_FIELD"],
			"1CB_FILE_FIELD_MULTIPLE" => $arParams["1CB_FILE_FIELD_MULTIPLE"],
			"1CB_FILE_FIELD_MAX_COUNT" => $arParams["1CB_FILE_FIELD_MAX_COUNT"],
			"1CB_FILE_FIELD_NAME" => $arParams["1CB_FILE_FIELD_NAME"],
			"1CB_FILE_FIELD_TYPE" => $arParams["1CB_FILE_FIELD_TYPE"],
			"1CB_REQUIRED_FIELDS" => $arParams["1CB_REQUIRED_FIELDS"],
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

//BIGDATA_ITEMS//
if(ModuleManager::isModuleInstalled("sale") && (!isset($arParams["USE_BIG_DATA"]) || $arParams["USE_BIG_DATA"] != "N")) {
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
			"SHOW_ALL_WO_SECTION" => $arCurSection["PRODUCT_TYPE"] ? "Y" : "N",
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
			"SECTION_ID" => !$arCurSection["PRODUCT_TYPE"] ? $intSectionID : "",
			"SECTION_CODE" => !$arCurSection["PRODUCT_TYPE"] ? $arResult["VARIABLES"]["SECTION_CODE"] : "",
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
			"ADD_SECTIONS_CHAIN" => "N",
			"RCM_TYPE" => isset($arParams["BIG_DATA_RCM_TYPE"]) ? $arParams["BIG_DATA_RCM_TYPE"] : "",
			"SHOW_FROM_SECTION" => isset($arParams["SHOW_FROM_SECTION"]) ? $arParams["SHOW_FROM_SECTION"] : "N",
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

//FILTER_SEO
if(isset($pageSeo) && !empty($pageSeo)) {   
    $APPLICATION->SetTitle(!empty($pageSeo["HEADER"]) ? $pageSeo["HEADER"] : (!empty($pageSeo["NAME"]) ? $pageSeo["NAME"] : ""));
    $APPLICATION->SetPageProperty("title", !empty($pageSeo["TITLE"]) ? $pageSeo["TITLE"] : "");
    $APPLICATION->SetPageProperty("keywords", !empty($pageSeo["KEYWORDS"]) ? $pageSeo["KEYWORDS"] : "");
    $APPLICATION->SetPageProperty("description", !empty($pageSeo["DESCRIPTION"]) ? $pageSeo["DESCRIPTION"] : "");
} else {
	//META_PROPERTY//
    if($arCurSection["PRODUCT_TYPE"]) {
        if ($arParams["SET_TITLE"] == "Y")
            $APPLICATION->SetTitle(!empty($arCurSection["SECTION_TITLE_H1"]) ? $arCurSection["SECTION_TITLE_H1"] : (!empty($arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]) ? $arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] : $arCurSection["NAME"]));
        if ($arParams["SET_BROWSER_TITLE"] != "N")
            $APPLICATION->SetPageProperty("title", $arCurSection["IPROPERTY_VALUES"]["SECTION_META_TITLE"]);
        if ($arParams["SET_META_KEYWORDS"] != "N")
            $APPLICATION->SetPageProperty("keywords", $arCurSection["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"]);
        if ($arParams["SET_META_DESCRIPTION"] != "N")
            $APPLICATION->SetPageProperty("description", !empty($arCurSection["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"]) ? $arCurSection["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"] : (!empty($arCurSection["PREVIEW"]) ? strip_tags($arCurSection["PREVIEW"]) : ""));
        if ($arParams["ADD_SECTIONS_CHAIN"] == "Y")
            $APPLICATION->AddChainItem(!empty($arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]) ? $arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] : $arCurSection["NAME"], $arCurSection["SECTION_PAGE_URL"]);
    } else {
        if (!empty($arCurSection["SECTION_TITLE_H1"]))
            $APPLICATION->SetTitle($arCurSection["SECTION_TITLE_H1"]);
        if (empty($arCurSection["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"]) && !empty($arCurSection["PREVIEW"]))
            $APPLICATION->SetPageProperty("description", strip_tags($arCurSection["PREVIEW"]));
    }
}


//PAGEN_META_PROPERTY//
if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1) {
    if($arParams["USE_FILTER_SEO"] == "Y" && !empty($arParams["USE_FILTER_SEO_IBLOCK"])) {
       $APPLICATION->SetPageProperty("title", $pageSeo["NAME"]." | ".Loc::getMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
    }else{   
	   $APPLICATION->SetPageProperty("title", (!empty($arCurSection["SECTION_TITLE_H1"]) ? $arCurSection["SECTION_TITLE_H1"] : (!empty($arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]) ? $arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] : $arCurSection["NAME"]))." | ".Loc::getMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
    }
	$APPLICATION->SetPageProperty("keywords", "");
	$APPLICATION->SetPageProperty("description", "");
}

//BACKGROUND_IMAGE//
if(isset($arCurSection["BACKGROUND_IMAGE"]) && is_array($arCurSection["BACKGROUND_IMAGE"])) {
	$APPLICATION->SetPageProperty(
		"backgroundImage",
		'style="background-image:url(\''.CHTTP::urnEncode($arCurSection['BACKGROUND_IMAGE']['SRC'], 'UTF-8').'\')"'
	);
}

//BACKGROUND_YOUTUBE//
if(isset($arCurSection["BACKGROUND_YOUTUBE"]) && $arSetting['SITE_BACKGROUND']['VALUE'] === 'Y') {
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.mb.YTPlayer.min.js');
	$APPLICATION->AddHeadString("<script>
		$(function(){
			$('body').prepend(\"<div id='bgVideoYT'></div>\");
			$('#bgVideoYT').YTPlayer({
				videoURL: '{$arCurSection["BACKGROUND_YOUTUBE"]}',
				mute: true,
				showControls: false,
				quality: 'defaul',
				containment: 'body',
				optimizeDisplay: true,
				startAt: 0,
				autoPlay: true,
				realfullscreen: true,
				stopMovieOnBlur: true,
				showYTLogo: false,
				gaTrack: false

			});
		});
		</script>", true);
}

//OG_PROPERTY//
$APPLICATION->SetPageProperty("ogtype", "website");
if(is_array($arCurSection["PICTURE"])) {
	$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$arCurSection["PICTURE"]["SRC"]);
	$APPLICATION->SetPageProperty("ogimagewidth", $arCurSection["PICTURE"]["WIDTH"]);
	$APPLICATION->SetPageProperty("ogimageheight", $arCurSection["PICTURE"]["HEIGHT"]);
} elseif(is_array($arCurSection["BANNER"]["PICTURE"])) {
	$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$arCurSection["BANNER"]["PICTURE"]["SRC"]);
	$APPLICATION->SetPageProperty("ogimagewidth", $arCurSection["BANNER"]["PICTURE"]["WIDTH"]);
	$APPLICATION->SetPageProperty("ogimageheight", $arCurSection["BANNER"]["PICTURE"]["HEIGHT"]);
}

//CANONICAL//
if(!empty($_REQUEST["sort"]) || !empty($_REQUEST["order"]) || !empty($_REQUEST["limit"]) || !empty($_REQUEST["view"]) || !empty($_REQUEST["action"]) || !empty($_REQUEST["set_filter"])) {
	$APPLICATION->AddHeadString("<link rel='canonical' href='".$APPLICATION->GetCurPageParam("", array("sort", "order", "limit", "view", "action", "set_filter", "clear_cache_session"), false)."'>");	
}?>