<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => "Y"
);
if(0 < intval($arResult["VARIABLES"]["SECTION_ID"])) {
	$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
} elseif("" != $arResult["VARIABLES"]["SECTION_CODE"]) {
	$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
}

$arSelect = array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PICTURE", "DESCRIPTION", "DEPTH_LEVEL", "UF_BANNER", "UF_BANNER_URL", "UF_BACKGROUND_IMAGE", "UF_PREVIEW", "UF_VIEW");

$cache_id = md5(serialize($arFilter));
$cache_dir = "/catalog/section";
$obCache = new CPHPCache();
if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_dir)) {
	$arCurSection = $obCache->GetVars();
} elseif($obCache->StartDataCache()) {
	$rsSections = CIBlockSection::GetList(array(), $arFilter, false, $arSelect);
	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($cache_dir);
	$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);	
	if($arSection = $rsSections->Fetch()) {
		$arCurSection["ID"] = $arSection["ID"];		
		$arCurSection["NAME"] = $arSection["NAME"];
		if($arSection["PICTURE"] > 0)
			$arCurSection["PICTURE"] = CFile::GetFileArray($arSection["PICTURE"]);
		$arCurSection["DESCRIPTION"] = $arSection["DESCRIPTION"];
		$arCurSection["BANNER"] = array(
			"PICTURE" => $arSection["UF_BANNER"] > 0 ? CFile::GetFileArray($arSection["UF_BANNER"]) : "",
			"URL" => $arSection["UF_BANNER_URL"]
		);		
		$arCurSection["PREVIEW"] = $arSection["UF_PREVIEW"];
		if($arSection["UF_VIEW"] > 0) {
			$UserField = CUserFieldEnum::GetList(array(), array("ID" => $arSection["UF_VIEW"]));
			if($UserFieldAr = $UserField->Fetch()) {
				$arCurSection["VIEW"] = $UserFieldAr["XML_ID"];
			}
		}
		if(($arSection["UF_BACKGROUND_IMAGE"] <= 0 || $arSection["UF_VIEW"] <= 0) && $arSection["DEPTH_LEVEL"] > 1) {
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
					array("ID", "IBLOCK_ID", "DEPTH_LEVEL", "UF_BACKGROUND_IMAGE", "UF_VIEW")
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
				}
			}
		}
		$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arSection["IBLOCK_ID"], $arSection["ID"]);
		$arCurSection["IPROPERTY_VALUES"] = $ipropValues->getValues();
	}
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache($arCurSection);
}

if(isset($arCurSection) && !empty($arCurSection)) {
	//BANNER//
	if(is_array($arCurSection["BANNER"]["PICTURE"])):?>		<div class="catalog-item-banner">
			<a href="<?=!empty($arCurSection["BANNER"]["URL"]) ? $arCurSection["BANNER"]["URL"] : 'javascript:void(0)'?>">
				<img class="data-lazy-src" data-lazy-src="<?=$arCurSection['BANNER']['PICTURE']['SRC']?>" width="<?=$arCurSection['BANNER']['PICTURE']['WIDTH']?>" height="<?=$arCurSection['BANNER']['PICTURE']['HEIGHT']?>" alt="<?=$arCurSection['NAME']?>" title="<?=$arCurSection['NAME']?>" />
			</a>
		</div>
	<?endif;

	//SUBSECTION//?>
	<?$APPLICATION->IncludeComponent("argit:catalog.section.list", "subsection",
		Array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"COUNT_ELEMENTS" => "N",
			"TOP_DEPTH" => "1",
			"SECTION_FIELDS" => array(),
			"SECTION_USER_FIELDS" => array(),
			"VIEW_MODE" => "",
			"SHOW_PARENT_NAME" => "",
			"SECTION_URL" => "",
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),
			"DISPLAY_IMG_WIDTH"	 =>	"50",
			"DISPLAY_IMG_HEIGHT" =>	"50"
		),
		$component
	);?>

	<?//PREVIEW//
	if(!empty($arCurSection["PREVIEW"])):
		if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1):?>
			<div class="catalog_preview">
				<?=$arCurSection["PREVIEW"];?>
			</div>
		<?endif;
	endif;

	//FILTER//
	if($arParams["USE_FILTER"] == "Y" && $arSetting["SMART_FILTER_VISIBILITY"]["VALUE"] != "DISABLE"):?>
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
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>

		<div class="filter_indent<?=($arSetting['SMART_FILTER_LOCATION']['VALUE'] == 'VERTICAL') ? ' vertical' : '';?> clr"></div>

		<?global $arSmartFilter;
	else:
		$arSmartFilter = array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ACTIVE" => "Y",
			"INCLUDE_SUBSECTIONS" => "Y",
			"SECTION_ID" => $arCurSection["ID"]
		);
	endif;
} else {
	//PRODUCT_TYPE//
	$productTypeFound = false;
	$arproductType = array("newproduct", "saleleader", "discount");
	if(in_array($arResult["VARIABLES"]["SECTION_CODE"], $arproductType)) {
		$productTypeFound = true;
		$arCurSection["NAME"] = Loc::getMessage($arResult["VARIABLES"]["SECTION_CODE"]."_TITLE");
		$arSmartFilter = array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],		
			"ACTIVE" => "Y",
			"!PROPERTY_".strtoupper($arResult["VARIABLES"]["SECTION_CODE"]) => false
		);
		global $arrFilter;
		$arrFilter = $arSmartFilter;
	}
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
	<label><?=Loc::getMessage("COUNT_ITEMS")?></label>
	<span><?=$count?></span>
</div>

<?//SORT//
$arAvailableSort = array(
	"default" => Array("sort", "asc"),
	"price" => Array("PROPERTY_MINIMUM_PRICE", "asc"),
	"rating" => Array("PROPERTY_rating", "desc"),
);

$sort = $APPLICATION->get_cookie("sort") ? $APPLICATION->get_cookie("sort") : "sort";

if($_REQUEST["sort"]) {
	$sort = "sort";	
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

$sort_order = $APPLICATION->get_cookie("order") ? $APPLICATION->get_cookie("order") : "asc";

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
	<?foreach($arAvailableSort as $key => $val):
		$className = $sort == $val[0] ? "selected" : "";
		if($className) 
			$className .= $sort_order == "asc" ? " asc" : " desc";
		$newSort = $sort == $val[0] ? $sort_order == "desc" ? "asc" : "desc" : $arAvailableSort[$key][1];?>

		<a href="<?=$APPLICATION->GetCurPageParam("sort=".$key."&amp;order=".$newSort, array("sort", "order"))?>" class="<?=$className?>" rel="nofollow"><?=Loc::getMessage("SECT_SORT_".$key)?></a>
	<?endforeach;?>
</div>

<?//LIMIT//
$arAvailableLimit = array("12", "48", "900");

$limit = $APPLICATION->get_cookie("limit") ? $APPLICATION->get_cookie("limit") : "12";

if($_REQUEST["limit"]) {
	$limit = "12";	
	$APPLICATION->set_cookie("limit", $limit, false, "/", SITE_SERVER_NAME); 
}
if($_REQUEST["limit"] == "48") {
	$limit = "48";
	$APPLICATION->set_cookie("limit", $limit, false, "/", SITE_SERVER_NAME); 
}
if($_REQUEST["limit"] == "900") {
	$limit = "900";
	$APPLICATION->set_cookie("limit", $limit, false, "/", SITE_SERVER_NAME); 
}?>

<div class="catalog-item-limit">
	<label><span class="full"><?=Loc::getMessage("SECT_COUNT_LABEL_FULL")?></span><span class="short"><?=Loc::getMessage("SECT_COUNT_LABEL_SHORT")?></span>:</label>
	<?foreach($arAvailableLimit as $val):?>
		<a href="<?=$APPLICATION->GetCurPageParam("limit=".$val, array("limit"))?>" <?if($limit==$val) echo " class='selected'";?> rel="nofollow"><?if($val=="900"): echo Loc::getMessage("SECT_COUNT_ALL"); else: echo $val; endif;?></a>
	<?endforeach;?>
</div>

<?//VIEW//
$arAvailableView = array("table", "list_n", "price");

$view = $APPLICATION->get_cookie("view") ? $APPLICATION->get_cookie("view") : (isset($arCurSection["VIEW"]) && !empty($arCurSection["VIEW"]) ? $arCurSection["VIEW"] : "table");

if($_REQUEST["view"]) {
	$view = "table";	
	$APPLICATION->set_cookie("view", $view, false, "/", SITE_SERVER_NAME); 
}
if($_REQUEST["view"] == "list_n") {
	$view = "list_n";
	$APPLICATION->set_cookie("view", $view, false, "/", SITE_SERVER_NAME); 
}
if($_REQUEST["view"] == "price") {
	$view = "price";
	$APPLICATION->set_cookie("view", $view, false, "/", SITE_SERVER_NAME);
}?>

<div class="catalog-item-view">
	<?foreach($arAvailableView as $val):?>
		<a href="<?=$APPLICATION->GetCurPageParam("view=".$val, array("view"))?>" class="<?=$val?><?if($view==$val) echo ' selected';?>" title="<?=Loc::getMessage('SECT_VIEW_'.$val)?>" rel="nofollow">
			<?if($val == "table"):?>
				<i class="fa fa-th-large"></i>
			<?elseif($val == "list_n"):?>
				<i class="fa fa-list"></i>
			<?elseif($val == "price"):?>
				<i class="fa fa-align-justify"></i>
			<?endif?>
		</a>
	<?endforeach;?>
</div>
<div class="clr"></div>

<?//SECTION//?>
<?$intSectionID = $APPLICATION->IncludeComponent("argit:catalog.section", $view,
	array(
        "SECTION_USER_FIELDS" => ['UF_SECTION_TAG',	'UF_SECTION_LINK'],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		/***************vp***************/
		/*"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_FIELD2" => $sort,
		"ELEMENT_SORT_ORDER2" => $sort_order,*/
		"ELEMENT_SORT_FIELD" => $sort, 
		"ELEMENT_SORT_ORDER" => $sort_order,
		"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
		/******************************/
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"SHOW_ALL_WO_SECTION" => "Y",
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
		"PAGE_ELEMENT_COUNT" => $limit,
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
		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
		"SECTION_ID" => !$productTypeFound ? $arResult["VARIABLES"]["SECTION_ID"] : "",
		"SECTION_CODE" => !$productTypeFound ? $arResult["VARIABLES"]["SECTION_CODE"] : "",
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
		"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],				
		"ADD_SECTIONS_CHAIN" => "N",		
		"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
		"BACKGROUND_IMAGE" => (isset($arParams["SECTION_BACKGROUND_IMAGE"]) ? $arParams["SECTION_BACKGROUND_IMAGE"] : ""),
		"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
		"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
		"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
		"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"]
	),
	false,
	array("HIDE_ICONS" => "Y")
);


$GLOBALS['productSlider1'] = [];
$GLOBALS['productSlider2'] = [];

$list = CIBlockSection::GetNavChain(false,$arResult['VARIABLES']['SECTION_ID'], array("ID"), true);

if ($list[0]['ID'] == 263) {

    $sections_list = CIblockSection::GetList(Array(), Array("ID"=>$arResult['VARIABLES']['SECTION_ID'], "IBLOCK_ID"=>16), false, Array("ID", "UF_PRODUCT_SLIDER_1", "UF_PRODUCT_SLIDER_2",  "IBLOCK_SECTION_ID", "DEPTH_LEVEL"));
    if($section = $sections_list->GetNext()){
        if (is_array($section["UF_PRODUCT_SLIDER_1"]) && count($section["UF_PRODUCT_SLIDER_1"]) > 0)
			$element_list = CIblockElement::GetList(Array("RAND" => "ASC"), Array("ID"=>$section["UF_PRODUCT_SLIDER_1"], "IBLOCK_ID"=>16, "INCLUDE_SUBSECTIONS"=>"Y", 'ACTIVE' => 'Y', 'SECTION_ACTIVE' => 'Y', '!SECTION_ID' => array(482, '')), false, false, Array("ID"));
			while($element = $element_list->GetNext()){
				$GLOBALS['productSlider1']['ID'][] = $element['ID'];
			}
        if (is_array($section["UF_PRODUCT_SLIDER_2"]) && count($section["UF_PRODUCT_SLIDER_2"]) > 0)
			$element_list = CIblockElement::GetList(Array("RAND" => "ASC"), Array("ID"=>$section["UF_PRODUCT_SLIDER_2"], "IBLOCK_ID"=>16, "INCLUDE_SUBSECTIONS"=>"Y", 'ACTIVE' => 'Y', 'SECTION_ACTIVE' => 'Y', '!SECTION_ID' => array(482, '')), false, false, Array("ID"));
			while($element = $element_list->GetNext()){
				$GLOBALS['productSlider2']['ID'][] = $element['ID'];
			}
                
        if (count($GLOBALS['productSlider1']) == 0){
            if ($section["DEPTH_LEVEL"] >= 2){
                $element_list = CIblockElement::GetList(Array("RAND" => "ASC"), Array("SECTION_ID"=>$section['IBLOCK_SECTION_ID'], "IBLOCK_ID"=>16, "INCLUDE_SUBSECTIONS"=>"Y", 'ACTIVE' => 'Y', '!SECTION_ID' => array($arResult['VARIABLES']['SECTION_ID'], 482, '', 245, 261, 271, 276)), false, Array("nPageSize"=>4), Array("ID"));
                while($element = $element_list->GetNext()){
                    $GLOBALS['productSlider1']['ID'][] = $element['ID'];
                }
                if ($section["DEPTH_LEVEL"] > 2 && count($GLOBALS['productSlider1']) == 0){
                    $sections_list = CIblockSection::GetList(Array(), Array("ID"=>$section['IBLOCK_SECTION_ID'], "IBLOCK_ID"=>16), false, Array("ID", "IBLOCK_SECTION_ID"));
                    while($section = $sections_list->GetNext()){
                        $element_list = CIblockElement::GetList(Array("RAND" => "ASC"), Array("SECTION_ID"=>$section['IBLOCK_SECTION_ID'], "IBLOCK_ID"=>16, "INCLUDE_SUBSECTIONS"=>"Y", 'ACTIVE' => 'Y', '!SECTION_ID' => array($arResult['VARIABLES']['SECTION_ID'], 482, '', 245, 261, 271, 276)), false, Array("nPageSize"=>4), Array("ID"));
                        while($element = $element_list->GetNext()){
                            $GLOBALS['productSlider1']['ID'][] = $element['ID'];
                        }
                    }
                }        
            }
        }
    }
    
}


$APPLICATION->IncludeComponent("bitrix:main.include", "template3", Array(
	"AREA_FILE_SHOW" => "file",	
		"EDIT_TEMPLATE" => "",	
		"PATH" => SITE_DIR."/include/why_rusmarta.php",	
	),
	false
);

if (count($GLOBALS['productSlider1']) > 0){

    ?>
    <div class="filtered-items productSlider1">
    <div class="h3">Похожие товары</div>
    <?
    
    $APPLICATION->IncludeComponent("argit:catalog.section", $view,
        array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            /***************vp***************/
            /*"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
            "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
            "ELEMENT_SORT_FIELD2" => $sort,
            "ELEMENT_SORT_ORDER2" => $sort_order,*/
            "ELEMENT_SORT_FIELD" => $sort, 
            "ELEMENT_SORT_ORDER" => $sort_order,
            "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
            "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
            /******************************/
            "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
            "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
            "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
            "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
            "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
            "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
            "SHOW_ALL_WO_SECTION" => "Y",
            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
            "FILTER_NAME" => 'productSlider1',
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
            "PAGE_ELEMENT_COUNT" => 8,
            "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
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
            "PAGER_TITLE" => $arParams["PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
            "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
            "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
            "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
            "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
            //"SECTION_ID" => !$productTypeFound ? $arResult["VARIABLES"]["SECTION_ID"] : "",
            //"SECTION_CODE" => !$productTypeFound ? $arResult["VARIABLES"]["SECTION_CODE"] : "",
            "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
            "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
            "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
            "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
            "CURRENCY_ID" => $arParams["CURRENCY_ID"],
            "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],				
            "ADD_SECTIONS_CHAIN" => "N",		
            "COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
            "BACKGROUND_IMAGE" => (isset($arParams["SECTION_BACKGROUND_IMAGE"]) ? $arParams["SECTION_BACKGROUND_IMAGE"] : ""),
            "DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
            "DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
            "DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
            "PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"]
        ),
        false,
        array("HIDE_ICONS" => "Y")
    );
?>
</div>
<?
}
if (count($GLOBALS['productSlider2']) > 0){

    ?>
    <div class="filtered-items productSlider2">
    <div class="h3">Вам может понравиться</div>
    <?
    
    $APPLICATION->IncludeComponent("argit:catalog.section", $view,
        array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            /***************vp***************/
            /*"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
            "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
            "ELEMENT_SORT_FIELD2" => $sort,
            "ELEMENT_SORT_ORDER2" => $sort_order,*/
            "ELEMENT_SORT_FIELD" => $sort, 
            "ELEMENT_SORT_ORDER" => $sort_order,
            "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
            "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
            /******************************/
            "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
            "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
            "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
            "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
            "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
            "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
            "SHOW_ALL_WO_SECTION" => "Y",
            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
            "FILTER_NAME" => 'productSlider2',
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
            "PAGE_ELEMENT_COUNT" => 8,
            "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
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
            "PAGER_TITLE" => $arParams["PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
            "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
            "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
            "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
            "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
            //"SECTION_ID" => !$productTypeFound ? $arResult["VARIABLES"]["SECTION_ID"] : "",
            //"SECTION_CODE" => !$productTypeFound ? $arResult["VARIABLES"]["SECTION_CODE"] : "",
            "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
            "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
            "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
            "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
            "CURRENCY_ID" => $arParams["CURRENCY_ID"],
            "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],				
            "ADD_SECTIONS_CHAIN" => "N",		
            "COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
            "BACKGROUND_IMAGE" => (isset($arParams["SECTION_BACKGROUND_IMAGE"]) ? $arParams["SECTION_BACKGROUND_IMAGE"] : ""),
            "DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
            "DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
            "DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
            "PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"]
        ),
        false,
        array("HIDE_ICONS" => "Y")
    );
?>
</div>
<?
}

//DESCRIPTION//
if(!empty($arCurSection["DESCRIPTION"])):
	if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1):?>
		<div class="catalog_description">
			<?=$arCurSection["DESCRIPTION"];?>
		</div>
	<?endif;
endif;

//BIGDATA_ITEMS//
$arRecomData = array();
$recomCacheID = array("IBLOCK_ID" => $arParams["IBLOCK_ID"]);
$obCache = new CPHPCache();
if($obCache->InitCache($arParams["CACHE_TIME"], serialize($recomCacheID), "/catalog/recommended")) {
	$arRecomData = $obCache->GetVars();	
} elseif($obCache->StartDataCache()) {
	if(Loader::includeModule("catalog")) {
		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]);
		$arRecomData["OFFER_IBLOCK_ID"] = (!empty($arSKU) ? $arSKU["IBLOCK_ID"] : 0);
	}
	$obCache->EndDataCache($arRecomData);
}
if(!empty($arRecomData)):
	if(ModuleManager::isModuleInstalled("sale") && (!isset($arParams["USE_BIG_DATA"]) || $arParams["USE_BIG_DATA"] != "N")):?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.bigdata.products", ".default",
			array(
				"DISPLAY_IMG_WIDTH" => $arParams["DISPLAY_IMG_WIDTH"],
				"DISPLAY_IMG_HEIGHT" => $arParams["DISPLAY_IMG_HEIGHT"],
				"SHARPEN" => $arParams["SHARPEN"],
				"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
				"SHOW_POPUP" => "Y",
				"LINE_ELEMENT_COUNT" => "4",
				"TEMPLATE_THEME" => "",
				"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
				"SHOW_OLD_PRICE" => "",
				"SHOW_DISCOUNT_PERCENT" => "",
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRODUCT_SUBSCRIPTION" => "",
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
				"SHOW_NAME" => "Y",
				"SHOW_IMAGE" => "Y",
				"MESS_BTN_BUY" => "",
				"MESS_BTN_DETAIL" => "",
				"MESS_BTN_SUBSCRIBE" => "",
				"MESS_NOT_AVAILABLE" => "",
				"PAGE_ELEMENT_COUNT" => "4",
				"SHOW_FROM_SECTION" => "Y",
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"DEPTH" => "2",
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
				"ADDITIONAL_PICT_PROP_".$arParams["IBLOCK_ID"] => "",
				"LABEL_PROP_".$arParams["IBLOCK_ID"] => "",
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"SECTION_ID" => $intSectionID,
				"SECTION_CODE" => "",
				"SECTION_ELEMENT_ID" => "",
				"SECTION_ELEMENT_CODE" => "",
				"ID" => "",
				"PROPERTY_CODE_".$arParams["IBLOCK_ID"] => "",
				"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
				"CART_PROPERTIES_".$arParams["IBLOCK_ID"] => "",
				"RCM_TYPE" => $arParams["BIG_DATA_RCM_TYPE"],
				"OFFER_TREE_PROPS_".$arRecomData["OFFER_IBLOCK_ID"] => $arParams["LIST_OFFERS_PROPERTY_CODE"],
				"ADDITIONAL_PICT_PROP_".$arRecomData["OFFER_IBLOCK_ID"] => ""
			),
			false,
			array("HIDE_ICONS" => "Y")
		);?>
	<?endif;
endif;

//PAGE_TITLE//

    $rsSections = CIBlockSection::GetList(array(), $arFilter, false, Array('ID','UF_H1'));
	if($arSection = $rsSections->Fetch()) {
        if ($arSection['UF_H1']!=''){
            $APPLICATION->SetTitle($arSection['UF_H1']);
        }        
	}  
	
if($productTypeFound)
	$APPLICATION->SetTitle($arCurSection["NAME"]);
if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1):
	global $page;
	$page = [];

	$requestArr = str_split($_REQUEST["PAGEN_1"]);
	foreach ($requestArr as $value) {
		if (is_numeric($value)) {
			array_push($page, $value);
		} else {
			break;
		}
	}

	$page = implode($page);

	$APPLICATION->SetPageProperty("title", (!empty($arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]) ? $arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] : $arCurSection["NAME"]) . " — страница " . $page);
	$APPLICATION->SetPageProperty("keywords", "");
	$APPLICATION->SetPageProperty("description", $APPLICATION->GetProperty('description') . " Страница " . $page);
endif;

//BACKGROUND_IMAGE//
if(isset($arCurSection["BACKGROUND_IMAGE"]) && is_array($arCurSection["BACKGROUND_IMAGE"])):
	$APPLICATION->SetPageProperty(
		"backgroundImage",
		'style="background-image:url(\''.CHTTP::urnEncode($arCurSection['BACKGROUND_IMAGE']['SRC'], 'UTF-8').'\')"'
	);
endif;

//META_PROPERTY//
$APPLICATION->AddHeadString("<meta property='og:title' content='".(!empty($arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]) ? $arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] : $arCurSection["NAME"])."' />", true);
if(!empty($arCurSection["PREVIEW"])):
	$APPLICATION->AddHeadString("<meta property='og:description' content='".strip_tags($arCurSection["PREVIEW"])."' />", true);
endif;
$APPLICATION->AddHeadString("<meta property='og:url' content='https://".SITE_SERVER_NAME.$APPLICATION->GetCurPage()."' />", true);
if(is_array($arCurSection["PICTURE"])):
	$APPLICATION->AddHeadString("<meta property='og:image' content='https://".SITE_SERVER_NAME.$arCurSection["PICTURE"]["SRC"]."' />", true);
	$APPLICATION->AddHeadString("<meta property='og:image:width' content='".$arCurSection["PICTURE"]["WIDTH"]."' />", true);
	$APPLICATION->AddHeadString("<meta property='og:image:height' content='".$arCurSection["PICTURE"]["HEIGHT"]."' />", true);
	$APPLICATION->AddHeadString("<link rel='image_src' href='https://".SITE_SERVER_NAME.$arCurSection["PICTURE"]["SRC"]."' />", true);
elseif(is_array($arCurSection["BANNER"]["PICTURE"])):
	$APPLICATION->AddHeadString("<meta property='og:image' content='https://".SITE_SERVER_NAME.$arCurSection["BANNER"]["PICTURE"]["SRC"]."' />", true);
	$APPLICATION->AddHeadString("<meta property='og:image:width' content='".$arCurSection["BANNER"]["PICTURE"]["WIDTH"]."' />", true);
	$APPLICATION->AddHeadString("<meta property='og:image:height' content='".$arCurSection["BANNER"]["PICTURE"]["HEIGHT"]."' />", true);
	$APPLICATION->AddHeadString("<link rel='image_src' href='https://".SITE_SERVER_NAME.$arCurSection["BANNER"]["PICTURE"]["SRC"]."' />", true);
endif; 
 
//CANONICAL//
//if(!empty($_REQUEST["sort"]) || !empty($_REQUEST["order"]) || !empty($_REQUEST["limit"]) || !empty($_REQUEST["view"]) || !empty($_REQUEST["action"]) || !empty($_REQUEST["set_filter"]) || !empty($_REQUEST["PAGEN_1"])):
	$APPLICATION->AddHeadString('<link rel="canonical" href="https://'.SITE_SERVER_NAME.str_replace(" ", "",$APPLICATION->GetCurPage()).'">');	
	
	echo '<!--###11-->';
	
	$GLOBALS['arrFilter'] = ['UF_BOTTOM_TAGS' => 1];
    $APPLICATION->IncludeComponent("argit:catalog.section.list", "simple-subsection",
		Array(
            "FILTER_NAME" => "arrFilter",
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"COUNT_ELEMENTS" => "N",
			"TOP_DEPTH" => "1",
			"SECTION_FIELDS" => array(),
			"SECTION_USER_FIELDS" => array(),
			"VIEW_MODE" => "",
			"SHOW_PARENT_NAME" => "",
			"SECTION_URL" => "",
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"ADD_SECTIONS_CHAIN" => "N",
			"DISPLAY_IMG_WIDTH"	 =>	"50",
			"DISPLAY_IMG_HEIGHT" =>	"50"
		),
		$component
	);
	
	echo '<!--###2-->';
//endif;?>
