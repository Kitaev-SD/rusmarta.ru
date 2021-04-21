<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
		"PAGE_RESULT_COUNT" => "900",
		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
	),
	$component->__parent
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
		<label><span class="full"><?=GetMessage("SECT_SORT_LABEL_FULL")?></span><span class="short"><?=GetMessage("SECT_SORT_LABEL_SHORT")?></span>:</label>
		<?foreach($arAvailableSort as $key => $val):
			$className = $sort == $val[0] ? "selected" : "";
			if($className) 
				$className .= $sort_order == "asc" ? " asc" : " desc";
			$newSort = $sort == $val[0] ? $sort_order == "desc" ? "asc" : "desc" : $arAvailableSort[$key][1];?>

			<a href="<?=$APPLICATION->GetCurPageParam("sort=".$key."&order=".$newSort, array("sort", "order"))?>" class="<?=$className?>" rel="nofollow"><?=GetMessage("SECT_SORT_".$key)?></a>
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
		<label><span class="full"><?=GetMessage("SECT_COUNT_LABEL_FULL")?></span><span class="short"><?=GetMessage("SECT_COUNT_LABEL_SHORT")?></span>:</label>
		<?foreach($arAvailableLimit as $val):?>
			<a href="<?=$APPLICATION->GetCurPageParam("limit=".$val, array("limit"))?>" <?if($limit==$val) echo " class='selected'";?>><?if($val=="900"): echo GetMessage("SECT_COUNT_ALL"); else: echo $val; endif;?></a>
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
	<?$APPLICATION->IncludeComponent("bitrix:catalog.section", $view, 
		Array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
			"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
			"ELEMENT_SORT_FIELD2" => $sort,
			"ELEMENT_SORT_ORDER2" => $sort_order,
			"FILTER_NAME" => "searchFilter",
			"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
			"SHOW_ALL_WO_SECTION" => $arParams["SHOW_ALL_WO_SECTION"],
			"SECTION_URL" => $arParams["SECTION_URL"],
			"DETAIL_URL" => $arParams["DETAIL_URL"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"SET_META_KEYWORDS" => "N",		
			"SET_META_DESCRIPTION" => "N",		
			"SET_BROWSER_TITLE" => "N",
			"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"PAGE_ELEMENT_COUNT" => $limit,
			"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
			"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
			"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],		
			"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
			"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_NOTES" => $arParams["CACHE_NOTES"],
			"CACHE_FILTER" => $arParams["CACHE_FILTER"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
			"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"AJAX_OPTION_JUMP" => $arParams["AJAX_OPTION_JUMP"],
			"AJAX_OPTION_STYLE" => $arParams["AJAX_OPTION_STYLE"],
			"AJAX_OPTION_HISTORY" => $arParams["AJAX_OPTION_HISTORY"],
			"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
			"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
			"SHARPEN" => $arParams["SHARPEN"],
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
	ShowNote(GetMessage("CT_BCSE_NOT_FOUND"));
}?>