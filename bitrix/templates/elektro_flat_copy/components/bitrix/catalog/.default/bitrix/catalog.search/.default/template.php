<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(isset($arResult["OFFERS"]["SKU_IBLOCK_ID"]))
  $arIBlockList = array($arParams["IBLOCK_ID"], $arResult["OFFERS"]["SKU_IBLOCK_ID"]);
else
  $arIBlockList = array($arParams["IBLOCK_ID"]);?>

<?$arElements = $APPLICATION->IncludeComponent("bitrix:search.page", ".default",
	Array(
		"RESTART" => $arParams["RESTART"],
		"NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"],
		"USE_LANGUAGE_GUESS" => $arParams["USE_LANGUAGE_GUESS"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"arrFILTER" => array("iblock_".$arParams["IBLOCK_TYPE"]),
		"arrFILTER_iblock_".$arParams["IBLOCK_TYPE"] => $arIBlockList,
		"USE_TITLE_RANK" => "N",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"SHOW_WHERE" => "N",
		"arrWHERE" => array(),
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => $arParams["PAGE_RESULT_COUNT"],
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "N",
	),
	$component->__parent,
	array("HIDE_ICONS" => "Y")
);?>

<?if(is_array($arElements) && !empty($arElements) && CModule::IncludeModule("catalog")) {
	$arElementsNew = array();
	foreach($arElements as $arElement) {
		$mxResult = CCatalogSku::GetProductInfo($arElement);
		if(is_array($mxResult)) {
			$arElementsNew[] = $mxResult["ID"];
		} else {
			$arElementsNew[] = $arElement;
		}
	}
	$arElementsNew = array_unique($arElementsNew);
}

if(is_array($arElementsNew) && !empty($arElementsNew)) {
	global $searchFilter;
	$searchFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],		
		"ACTIVE" => "Y",
		"ID" => $arElementsNew
	);

	//COUNT//
	$cache_id = md5(serialize($searchFilter));
	$cache_dir = "/catalog/search/amount";
	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_dir)) {
		$count = $obCache->GetVars();
	} elseif($obCache->StartDataCache()) {
		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($cache_dir);
		$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
		$count = CIBlockElement::GetList(array(), $searchFilter, array(), false);
		$CACHE_MANAGER->EndTagCache();
		$obCache->EndDataCache($count);
	}?>
	
	<div class="count_items">
		<label><?=GetMessage("COUNT_ITEMS")?></label>
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
		<label><span class="full"><?=GetMessage("SECT_SORT_LABEL_FULL")?></span><span class="short"><?=GetMessage("SECT_SORT_LABEL_SHORT")?></span>:</label>
		<?foreach($arAvailableSort as $key => $val):
			$className = $sort == $val[0] ? "selected" : "";
			if($className) 
				$className .= $sort_order == "asc" ? " asc" : " desc";
			$newSort = $sort == $val[0] ? $sort_order == "desc" ? "asc" : "desc" : $arAvailableSort[$key][1];?>

			<a href="<?=$APPLICATION->GetCurPageParam("sort=".$key."&order=".$newSort, array("sort", "order"))?>" class="<?=$className?>" rel="nofollow"><?=GetMessage("SECT_SORT_".$key)?></a>
		<?endforeach;?>
	</div>
	
	<?//VIEW//
	$arAvailableView = array("table", "list", "price");

	$view = $APPLICATION->get_cookie("view") ? $APPLICATION->get_cookie("view") : "table";

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
		<?foreach($arAvailableView as $val):?>
			<a href="<?=$APPLICATION->GetCurPageParam("view=".$val, array("view"))?>" class="<?=$val?><?if($view==$val) echo ' selected';?>" title="<?=GetMessage('SECT_VIEW_'.$val)?>">
				<?if($val == "table"):?>
					<i class="fa fa-th-large"></i>
				<?elseif($val == "list"):?>
					<i class="fa fa-list"></i>
				<?elseif($val == "price"):?>
					<i class="fa fa-align-justify"></i>
				<?endif?>
			</a>
		<?endforeach;?>
	</div>
	<div class="clr"></div>
	
	<?//SECTION//?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => $sort,
			"ELEMENT_SORT_ORDER" => $sort_order,
			"ELEMENT_SORT_FIELD2" => "",
			"ELEMENT_SORT_ORDER2" => "",
			"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
			"META_KEYWORDS" => $arParams["META_KEYWORDS"],
			"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
			"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
			"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
			"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
			"SHOW_ALL_WO_SECTION" => $arParams["SHOW_ALL_WO_SECTION"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"FILTER_NAME" => "searchFilter",
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_FILTER" => $arParams["CACHE_FILTER"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"MESSAGE_404" => $arParams["MESSAGE_404"],
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"SHOW_404" => $arParams["SHOW_404"],
			"FILE_404" => $arParams["FILE_404"],
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
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
			"LAZY_LOAD" => $arParams["LAZY_LOAD"],
			"MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
			"LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],
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
			"SECTION_URL" => $arParams["SECTION_URL"],
			"DETAIL_URL" => $arParams["DETAIL_URL"],
			"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],		
			"PRODUCT_ROW_VARIANTS" => $arParams["PRODUCT_ROW_VARIANTS"],
			"TYPE" => $view,
			"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],		
			"COMPARE_PATH" => "",
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
		false,
		array("HIDE_ICONS" => "Y")
	);?>
	
	<?//PAGE_TITLE//
	if(!empty($_REQUEST['PAGEN_2']) && $_REQUEST['PAGEN_2'] > 1):
		$APPLICATION->SetPageProperty("title", GetMessage("CMP_TITLE").": ".$_REQUEST['q']." | ".GetMessage('SECT_TITLE')." ".$_REQUEST['PAGEN_2']);
		$APPLICATION->SetPageProperty("keywords", "");
		$APPLICATION->SetPageProperty("description", "");
	endif;

	//CANONICAL//
	if(!empty($_REQUEST['sort']) || !empty($_REQUEST['order']) || !empty($_REQUEST['limit']) || !empty($_REQUEST['view']) || !empty($_REQUEST["PAGEN_2"])):
		$APPLICATION->AddHeadString("<link rel='canonical' href='".$APPLICATION->GetCurPageParam("", array('sort', 'order', 'limit', 'view', 'submit', 'PAGEN_2'))."'>");
	endif;
} else {
	ShowNote(GetMessage("CT_BCSE_NOT_FOUND"), "infotext");
}?>