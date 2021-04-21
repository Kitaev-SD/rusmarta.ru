<?$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"projects",
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
		"T_CLIENTS" => $arParams["T_CLIENTS"],
		"BRAND_PROP_CODE" => $arParams["DETAIL_BRAND_PROP_CODE"],
		"BRAND_USE" => $arParams["DETAIL_BRAND_USE"],
		"FORM_ID_ORDER_SERVISE" => ($arParams["FORM_ID_ORDER_SERVISE"] ? $arParams["FORM_ID_ORDER_SERVISE"] : 'PROJECTS'),
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
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
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

<?if(in_array('FORM_QUESTION', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_FORM_QUESTION_VALUE']):?>
	<div class="row">
		<div class="col-md-9">
<?endif;?>

<?// order?>
<?if(in_array('FORM_ORDER', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_FORM_ORDER_VALUE']):?>
	<table class="order-block">
		<tr>
			<td class="col-md-9 col-sm-8 col-xs-7">
				<div class="text">
					<?$APPLICATION->IncludeComponent(
						'bitrix:main.include',
						'',
						Array(
							'AREA_FILE_SHOW' => 'page',
							'AREA_FILE_SUFFIX' => 'project',
							'EDIT_TEMPLATE' => ''
						)
					);?>
				</div>
			</td>
			<td class="col-md-3 col-sm-4 col-xs-5">
				<div class="btns">
					<span class="btn btn-default btn-lg animate-load" data-event="jqm" data-param-form_id="<?=($arParams["FORM_ID_ORDER_SERVISE"] ? $arParams["FORM_ID_ORDER_SERVISE"] : 'PROJECTS');?>" data-name="order_services" data-autoload-service="<?=$arElement['NAME']?>" data-autoload-project="<?=$arElement['NAME']?>"><span><?=(strlen($arParams['S_ORDER_SERVISE']) ? $arParams['S_ORDER_SERVISE'] : \Bitrix\Main\Localization\Loc::getMessage('S_ORDER_SERVISE'))?></span></span>
				</div>
			</td>
		</tr>
	</table>
<?endif;?>

<?$list_view = ($arParams['LIST_VIEW'] ? $arParams['LIST_VIEW'] : 'slider');?>
<?// goods links?>
<?if(in_array('LINK_GOODS', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_LINK_GOODS_VALUE']):?>
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
		<?$GLOBALS['arrProductsFilter'] = array('ID' => $arElement['PROPERTY_LINK_GOODS_VALUE']);?>
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

<?// services links?>
<?if(in_array('LINK_SERVICES', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_LINK_SERVICES_VALUE']):?>
	<div class="wraps">
		<?global $arrrFilter; $arrrFilter = array("ID" => $arElement["PROPERTY_LINK_SERVICES_VALUE"]);?>
		<?$APPLICATION->IncludeComponent("bitrix:news.list", "items-services", array(
			"IBLOCK_TYPE" => "aspro_next_content",
			"IBLOCK_ID" => CNextCache::$arIBlocks[SITE_ID]["aspro_next_content"]["aspro_next_services"][0],
			"NEWS_COUNT" => "20",
			"SORT_BY1" => "ACTIVE_FROM",
			"SORT_ORDER1" => "DESC",
			"SORT_BY2" => "SORT",
			"SORT_ORDER2" => "ASC",
			"FILTER_NAME" => "arrrFilter",
			"TITLE" => (strlen($arParams['T_SERVICES']) ? $arParams['T_SERVICES'] : GetMessage('T_SERVICES')),
			"FIELD_CODE" => array(
				0 => "NAME",
				1 => "PREVIEW_TEXT",
				2 => "PREVIEW_PICTURE",
				3 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "",
				1 => "",
			),
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "N",
			"PREVIEW_TRUNCATE_LEN" => "",
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"INCLUDE_SUBSECTIONS" => "Y",
			"PAGER_TEMPLATE" => ".default",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"PAGER_TITLE" => "Новости",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"VIEW_TYPE" => "list",
			"SHOW_TABS" => "N",
			"SHOW_IMAGE" => "Y",
			"SHOW_NAME" => "Y",
			"SHOW_DETAIL" => "Y",
			"IMAGE_POSITION" => "top",
			"COUNT_IN_LINE" => "3",
			"GALLERY_TYPE" => $arParams["GALLERY_TYPE"],
			"AJAX_OPTION_ADDITIONAL" => ""
			),
		false, array("HIDE_ICONS" => "Y")
		);?>
	</div>
<?endif;?>
<?if(in_array('FORM_QUESTION', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_FORM_QUESTION_VALUE']):?>
	</div>
<?endif;?>