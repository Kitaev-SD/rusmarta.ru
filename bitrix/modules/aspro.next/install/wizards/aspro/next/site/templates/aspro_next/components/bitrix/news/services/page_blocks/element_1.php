<?$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"news",
	Array(
		"S_ASK_QUESTION" => $arParams["S_ASK_QUESTION"],
		"S_ORDER_SERVISE" => $arParams["S_ORDER_SERVISE"],
		"T_GALLERY" => $arParams["T_GALLERY"],
		"T_DOCS" => $arParams["T_DOCS"],
		"T_GOODS" => $arParams["T_GOODS"],
		"T_SERVICES" => $arParams["T_SERVICES"],
		"T_PROJECTS" => $arParams["T_PROJECTS"],
		"T_REVIEWS" => $arParams["T_REVIEWS"],
		"T_STAFF" => $arParams["T_STAFF"],
		"T_VIDEO" => $arParams["T_VIDEO"],
		"FORM_ID_ORDER_SERVISE" => $arParams["FORM_ID_ORDER_SERVISE"],
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"META_KEYWORDS" => $arParams["META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
		"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
		"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"USE_SHARE" 			=> $arParams["USE_SHARE"],
		"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
		"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
		"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
		"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
		"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		"GALLERY_TYPE" => $arParams["GALLERY_TYPE"],
	),
	$component
);?>

<?$list_view = ($arParams['LIST_VIEW'] ? $arParams['LIST_VIEW'] : 'slider');?>
<?// goods links?>
<?if(in_array('LINK_GOODS', $arParams['DETAIL_PROPERTY_CODE'])):?>
	<?$catalogID = \Bitrix\Main\Config\Option::get('aspro.next', 'CATALOG_IBLOCK_ID', CNextCache::$arIBlocks[SITE_ID]["aspro_next_catalog"]["aspro_next_catalog"][0]);?>
	<?
	$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $catalogID, "CODE" => "SERVICES"));
	if($dbProperty->SelectedRowsCount() && $arElement['ID'])
	{
		$arTmpElement = CNextCache::CIblockElement_GetList(array('CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($catalogID), 'MULTI' => 'Y')), array('IBLOCK_ID' => $catalogID, 'PROPERTY_SERVICES' => $arElement['ID']), false, false, array('ID'));
		if($arTmpElement)
		{
			foreach($arTmpElement as $key => $arItem)
				$arFilterElements[] = $arItem['ID'];

			if($arElement['PROPERTY_LINK_GOODS_VALUE'])
				$arElement['PROPERTY_LINK_GOODS_VALUE'] = array_merge((array)$arElement['PROPERTY_LINK_GOODS_VALUE'], $arFilterElements);
			else
				$arElement['PROPERTY_LINK_GOODS_VALUE'] = $arFilterElements;
		}
	}
	$arTmpGoods = json_decode($arElement["~PROPERTY_LINK_GOODS_FILTER_VALUE"], true);
	?>
	<?global $isHideLeftBlock;?>
	<?if(in_array('FORM_QUESTION', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_FORM_QUESTION_VALUE'] && $isHideLeftBlock):?>
		<div class="row">
			<div class="col-md-9">
	<?endif;?>

	<?if($arElement['PROPERTY_LINK_GOODS_VALUE'] || ($arElement['PROPERTY_LINK_GOODS_FILTER_VALUE'] && $arTmpGoods['CHILDREN'])):?>
		<div class="wraps goods-block with-padding block ajax_load catalog">
			<?$bAjax = ((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest")  && (isset($_GET["ajax_get"]) && $_GET["ajax_get"] == "Y"));?>
			<?if($bAjax):?>
				<?$APPLICATION->RestartBuffer();?>
			<?endif;?>
			<?if(!isset($arParams["PRICE_CODE"]))
				$arParams["PRICE_CODE"] = array(0 => "BASE", 1 => "OPT");
			if(!isset($arParams["STORES"]))
				$arParams["STORES"] = array(0 => "1", 1 => "2");
			?>
			<?
			if(!($arElement['PROPERTY_LINK_GOODS_FILTER_VALUE'] && $arTmpGoods['CHILDREN']))
				$GLOBALS['arrProductsFilter'] = array('ID' => $arElement['PROPERTY_LINK_GOODS_VALUE']);?>
			<?
			global $arRegion;
			if($arRegion)
			{
				if($arRegion['LIST_PRICES'])
				{
					if(reset($arRegion['LIST_PRICES']) != 'component')
						$arParams['PRICE_CODE'] = array_keys($arRegion['LIST_PRICES']);
				}
				if($arRegion['LIST_STORES'])
				{
					if(reset($arRegion['LIST_STORES']) != 'component')
						$arParams['STORES'] = $arRegion['LIST_STORES'];
				}
			}

			if($arParams['LIST_PRICES'])
			{
				foreach($arParams['LIST_PRICES'] as $key => $price)
				{
					if(!$price)
						unset($arParams['LIST_PRICES'][$key]);
				}
			}

			if($arParams['STORES'])
			{
				foreach($arParams['STORES'] as $key => $store)
				{
					if(!$store)
						unset($arParams['STORES'][$key]);
				}
			}

			if($arRegion)
			{
				if($arRegion["LIST_STORES"] && $arParams["HIDE_NOT_AVAILABLE"] == "Y")
				{
					if($arParams['STORES']){
						if(count($arParams['STORES']) > 1){
							$arStoresFilter = array('LOGIC' => 'OR');
							foreach($arParams['STORES'] as $storeID)
							{
								$arStoresFilter[] = array(">CATALOG_STORE_AMOUNT_".$storeID => 0);
							}
						}
						else{
							foreach($arParams['STORES'] as $storeID)
							{
								$arStoresFilter = array(">CATALOG_STORE_AMOUNT_".$storeID => 0);
							}
						}

						$arTmpFilter = array('!TYPE' => '2');
						if($arStoresFilter){
							if(count($arStoresFilter) > 1){
								$arTmpFilter[] = $arStoresFilter;
							}
							else{
								$arTmpFilter = array_merge($arTmpFilter, $arStoresFilter);
							}

							$GLOBALS['arrProductsFilter'][] = array(
								'LOGIC' => 'OR',
								array('TYPE' => '2'),
								$arTmpFilter,
							);
						}
					}
				}
			}
			?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:main.include",
				"main",
				array(
					"COMPONENT_TEMPLATE" => "main",
					"PATH" => SITE_DIR."include/news.detail.products_".$list_view.".php",
					"AREA_FILE_SHOW" => "file",
					"AREA_FILE_SUFFIX" => "",
					"AREA_FILE_RECURSIVE" => "Y",
					"EDIT_TEMPLATE" => "standard.php",
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"STORES" => $arParams["STORES"],
					"BIG_DATA_RCM_TYPE" => "bestsell",
					"CUSTOM_FILTER" => $arElement['~PROPERTY_LINK_GOODS_FILTER_VALUE'],
					"STIKERS_PROP" => "HIT",
					"SALE_STIKER" => "SALE_TEXT",
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"LINKED_ELEMENST_PAGE_COUNT" => $arParams["LINKED_ELEMENST_PAGE_COUNT"],
					"LINKED_ELEMENST_PAGINATION" => $arParams["LINKED_ELEMENST_PAGINATION"],
					"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
					"FROM_AJAX" => ($bAjax?"Y":"N"),
					"TITLE" => (strlen($arParams['T_GOODS']) ? $arParams['T_GOODS'] : GetMessage('T_GOODS'))
				),
				false
			);?>
			<?if($bAjax):?>
				<?die();?>
			<?endif;?>
		</div>
	<?endif;?>

	<?if(in_array('FORM_QUESTION', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_FORM_QUESTION_VALUE'] && $isHideLeftBlock):?>
		</div></div>
	<?endif;?>
<?endif;?>