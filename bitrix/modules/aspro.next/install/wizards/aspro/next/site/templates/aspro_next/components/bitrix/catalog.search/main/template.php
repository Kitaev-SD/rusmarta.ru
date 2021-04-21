<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$isAjax="N";?>
<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest"  && isset($_GET["ajax_get"]) && $_GET["ajax_get"] == "Y" || (isset($_GET["ajax_basket"]) && $_GET["ajax_basket"]=="Y")){
	$isAjax="Y";
}?>
<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest" && isset($_GET["ajax_get_filter"]) && $_GET["ajax_get_filter"] == "Y" ){
	$isAjaxFilter="Y";
}?>
<?
global $arTheme, $arRegion, $searchQuery;
$catalogIBlockID = $arParams["IBLOCK_ID"];
$arParams["AJAX_FILTER_CATALOG"] = "N";

if($arParams["FILTER_NAME"] == '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])){
	$arParams["FILTER_NAME"] = "searchFilter";
}

$bShowFilter = ($arTheme["SEARCH_VIEW_TYPE"]["VALUE"] == "with_filter");
if($bShowFilter){
	$APPLICATION->SetPageProperty("HIDE_LEFT_BLOCK", "Y");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.history.js');
}

$arIBlocks = array($arParams["IBLOCK_ID"]);
$arSKU = array();
if($arParams['IBLOCK_ID'])
{
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
	if($arSKU['IBLOCK_ID'])
		$arIBlocks[] = $arSKU['IBLOCK_ID'];
}?>
<?if($bShowFilter):?>
	<div class="right_block wide_N">
		<div class="middle">
<?endif;?>
<?
// show bitrix.search_page content
$APPLICATION->ShowViewContent('comp_search_page');

// include bitrix.search_page
ob_start();
include 'include_search_page.php';
$searchPageContent = ob_get_clean();

if(!strlen($searchQuery)){
	$searchQuery = $_GET['q'];
}

// find landings in search
$oSearchQuery = new \Aspro\Next\SearchQuery($searchQuery);
$arLandingsFilter = array('ACTIVE' => 'Y');
if($arRegion){
	// filter landings by property LINK_REGION (empty or ID of current region)
	$arLandingsFilter[] = array(
		'LOGIC' => 'OR',
		array('PROPERTY_LINK_REGION' => false),
		array('PROPERTY_LINK_REGION' => $arRegion['ID']),
	);
}

if(isset($_REQUEST['ls'])){
	if(($landingID = intval($_REQUEST['ls'])) > 0){
		$arLandingsFilter['ID'] = $landingID;
		if(!strlen($searchQuery)){
			// query is empty
			$dbRes = \CIBlockElement::GetByID($landingID);
			if($arElement = $dbRes->Fetch()){
				$arElement = \CNextCache::CIBlockElement_GetList(
					array(
						'CACHE' => array(
							'TAG' => \CNextCache::GetIBlockCacheTag($arElement['IBLOCK_ID']),
							'MULTI' => 'N'
						)
					),
					array('ID' => $landingID),
					false,
					false,
					array(
						'ID',
						'IBLOCK_ID',
						'PROPERTY_QUERY',
					)
				);

				$arQuery = (array)$arElement['PROPERTY_QUERY_VALUE'];
				if(strlen($query = $arQuery ? trim(htmlspecialchars_decode($arQuery[0])) : '')){
					if(strlen($query = \Aspro\Next\SearchQuery::getSentenceExampleQuery($query))){
						$searchQuery = $_GET['q'] = $_POST['q'] = $_REQUEST['q'] = $query;
						$_GET['spell'] = $_POST['spell'] = $_REQUEST['spell'] = 1;
						$oSearchQuery->setQuery($searchQuery);

						// include bitrix.search_page and replace $arElements by default query example
						ob_start();
						include 'include_search_page.php';
						$searchPageContent = ob_get_clean();
					}
				}
			}
		}
		else{
			$_SESSION['q_'.$landingID] = $searchQuery;
		}
	}
}

// get one landing
$arLanding = $oSearchQuery->getLandings(
	array(),
	$arLandingsFilter,
	false,
	false,
	array(
		'ID',
		'IBLOCK_ID',
		'NAME',
		'PREVIEW_TEXT',
		'DETAIL_TEXT',
		'DETAIL_PICTURE',
		'PROPERTY_IS_INDEX',
		'PROPERTY_FORM_QUESTION',
		'PROPERTY_HIDE_QUERY_INPUT',
		'PROPERTY_TIZERS',
		'PROPERTY_H3_GOODS',
		'PROPERTY_SIMILAR',
		'PROPERTY_REDIRECT_URL',
		'PROPERTY_URL_CONDITION',
		'PROPERTY_QUERY_REPLACEMENT',
		'PROPERTY_CUSTOM_FILTER',
		'PROPERTY_CUSTOM_FILTER_TYPE',
		'PROPERTY_I_ELEMENT_PAGE_TITLE',
		'PROPERTY_I_ELEMENT_PREVIEW_PICTURE_FILE_ALT',
		'PROPERTY_I_ELEMENT_PREVIEW_PICTURE_FILE_TITLE',
		'PROPERTY_I_SKU_PAGE_TITLE',
		'PROPERTY_I_SKU_PREVIEW_PICTURE_FILE_ALT',
		'PROPERTY_I_SKU_PREVIEW_PICTURE_FILE_TITLE',
	),
	true
);
if($arLanding){
	if(!$arLanding['PROPERTY_IS_INDEX_VALUE']){
		$APPLICATION->AddHeadString('<meta name="robots" content="noindex,nofollow" />');
	}

	if(strlen($arLanding['PROPERTY_URL_CONDITION_VALUE'])){
		$urlCondition = ltrim(trim($arLanding['PROPERTY_URL_CONDITION_VALUE']), '/');
		$canonicalUrl = '/'.$urlCondition;

		if(!isset($_REQUEST['ls'])){
			$_SESSION['q_'.$arLanding['ID']] = $searchQuery;
			LocalRedirect($canonicalUrl, true, '301 Moved permanently');
			die();

			// not use APPLICATION->AddHeadString because it`s cached template
			?><link rel="canonical" href="<?=$canonicalUrl?>" /><?
		}
	}

	if(strlen($arLanding['PROPERTY_REDIRECT_URL_VALUE']) && !strlen($urlCondition)){
		if(!isset($_REQUEST['ls'])){
			LocalRedirect($arLanding['PROPERTY_REDIRECT_URL_VALUE'], false, '301 Moved Permanently');
			die();
		}
	}

	if($arLanding['PROPERTY_HIDE_QUERY_INPUT_VALUE']){
		$searchPageContent = '';
	}

	if($arLanding['PROPERTY_CUSTOM_FILTER_VALUE'] && $arLanding['PROPERTY_CUSTOM_FILTER_TYPE_VALUE']){
		// decode CUSTOM_FILTER
		if(\Bitrix\Main\Loader::includeModule('catalog') && class_exists('CNextCondition')){
			$arCustomFilter = array();
			$cond = new CNextCondition();
			$arLanding['PROPERTY_CUSTOM_FILTER_VALUE'] = (array)$arLanding['PROPERTY_CUSTOM_FILTER_VALUE'];

			foreach($arLanding['PROPERTY_CUSTOM_FILTER_VALUE'] as $customFilter){
				if(isset($customFilter) && is_string($customFilter)){
					try{
						$customFilter = $cond->parseCondition(\Bitrix\Main\Web\Json::decode($customFilter), $arParams);
					}
					catch(\Exception $e){
						$customFilter = array();
					}
				}

				if($customFilter){
					$arCustomFilter = array_merge($arCustomFilter, $customFilter);
				}
			}
		}
	}

	if($bReplaceElementsByCustomFilter = $arCustomFilter && $arLanding['PROPERTY_CUSTOM_FILTER_TYPE_VALUE'] && strpos($arLanding['PROPERTY_CUSTOM_FILTER_TYPE_VALUE'], GetMessage('REPLACE_SUBSTR')) !== false){
		// replace $arElements by CUSTOM_FILTER
		$arItemsFilter = array_merge(
			array(
				"IBLOCK_ID" => $catalogIBlockID,
				"ACTIVE" => "Y",
			),
			array($arCustomFilter)
		);

		$arElements = CNextCache::CIBLockElement_GetList(
			array(
				'CACHE' => array(
					'MULTI' => 'Y',
					'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
					'RESULT' => array('ID'),
				)
			),
			$arItemsFilter,
			false,
			false,
			array(
				'ID',
			)
		);
	}

	if(!$bReplaceElementsByCustomFilter && $arLanding['PROPERTY_QUERY_REPLACEMENT_VALUE'] && $arLanding['PROPERTY_QUERY_REPLACEMENT_VALUE'] !== $searchQuery){
		// save oroginal query
		$originalSearchQuery = $searchQuery;

		// replace query
		$searchQuery = $_GET['q'] = $_POST['q'] = $_REQUEST['q'] = $arLanding['PROPERTY_QUERY_REPLACEMENT_VALUE'];
		$_GET['spell'] = $_POST['spell'] = $_REQUEST['spell'] = 1;

		// include bitrix.search_page and replace $arElements by other search results
		ob_start();
		include 'include_search_page.php';
		ob_end_clean();

		// restore original query
		$searchQuery = $_GET['q'] = $_POST['q'] = $_REQUEST['q'] = $originalSearchQuery;
	}

	$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arLanding['IBLOCK_ID'], $arLanding['ID']);
	$arLanding['IPROPERTY_VALUES'] = $ipropValues->getValues();

	if($arLanding['PROPERTY_SIMILAR_VALUE']){
		$arLanding['PROPERTY_SIMILAR_VALUE'] = (array)$arLanding['PROPERTY_SIMILAR_VALUE'];
		if(in_array($arLanding['ID'], $arLanding['PROPERTY_SIMILAR_VALUE'])){
			unset($arLanding['PROPERTY_SIMILAR_VALUE'][array_search($arLanding['ID'], $arLanding['PROPERTY_SIMILAR_VALUE'])]);
		}
	}

	$arIBInheritTemplates = array(
		"ELEMENT_PAGE_TITLE" => $arLanding["PROPERTY_I_ELEMENT_PAGE_TITLE_VALUE"],
		"ELEMENT_PREVIEW_PICTURE_FILE_ALT" => $arLanding["PROPERTY_I_ELEMENT_PREVIEW_PICTURE_FILE_ALT_VALUE"],
		"ELEMENT_PREVIEW_PICTURE_FILE_TITLE" => $arLanding["PROPERTY_I_ELEMENT_PREVIEW_PICTURE_FILE_TITLE_VALUE"],
		"SKU_PAGE_TITLE" => $arLanding["PROPERTY_I_SKU_PAGE_TITLE_VALUE"],
		"SKU_PREVIEW_PICTURE_FILE_ALT" => $arLanding["PROPERTY_I_SKU_PREVIEW_PICTURE_FILE_ALT_VALUE"],
		"SKU_PREVIEW_PICTURE_FILE_TITLE" => $arLanding["PROPERTY_I_SKU_PREVIEW_PICTURE_FILE_TITLE_VALUE"],
	);
}
?>
<?=$searchPageContent?>
<?if($arLanding && ($arLanding["DETAIL_PICTURE"] || strlen($arLanding["PREVIEW_TEXT"]) || $arLanding["PROPERTY_FORM_QUESTION_VALUE"]) || $arLanding["PROPERTY_TIZERS_VALUE"]):?>
	<div class="seo_block">
		<?if($arLanding["DETAIL_PICTURE"]):?>
			<img src="<?=CFile::GetPath($arLanding["DETAIL_PICTURE"]);?>" alt="" title="" class="img-responsive"/>
		<?endif;?>

		<?if(strlen($arLanding["PREVIEW_TEXT"])):?>
			<?=$arLanding["PREVIEW_TEXT"]?>
		<?endif;?>

		<?$APPLICATION->ShowViewContent('sotbit_seometa_top_desc');?>

		<?if($arLanding["PROPERTY_FORM_QUESTION_VALUE"]):?>
			<table class="order-block noicons">
				<tbody>
					<tr>
						<td class="col-md-9 col-sm-8 col-xs-7 valign">
							<div class="text">
								<?$APPLICATION->IncludeComponent(
									 'bitrix:main.include',
									 '',
									 Array(
										  'AREA_FILE_SHOW' => 'page',
										  'AREA_FILE_SUFFIX' => 'ask',
										  'EDIT_TEMPLATE' => ''
									 )
								);?>
							</div>
						</td>
						<td class="col-md-3 col-sm-4 col-xs-5 valign">
							<div class="btns">
								<span><span class="btn btn-default btn-lg white transparent animate-load" data-event="jqm" data-param-form_id="ASK" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : GetMessage('S_ASK_QUESTION'))?></span></span></span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		<?endif;?>
		<?if($arLanding["PROPERTY_TIZERS_VALUE"]):?>
			<?$GLOBALS["arLandingTizers"] = array("ID" => $arLanding["PROPERTY_TIZERS_VALUE"]);?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"next",
				array(
					"IBLOCK_TYPE" => "aspro_next_content",
					"IBLOCK_ID" => CNextCache::$arIBlocks[SITE_ID]["aspro_next_content"]["aspro_next_tizers"][0],
					"NEWS_COUNT" => "4",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arLandingTizers",
					"FIELD_CODE" => array(
						0 => "",
						1 => "",
					),
					"PROPERTY_CODE" => array(
						0 => "LINK",
						1 => "",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"CACHE_TYPE" =>$arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => "N",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "j F Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"INCLUDE_SUBSECTIONS" => "Y",
					"PAGER_TEMPLATE" => "",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"PAGER_TITLE" => "",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"COMPONENT_TEMPLATE" => "next",
					"SET_BROWSER_TITLE" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_LAST_MODIFIED" => "N",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"SHOW_404" => "N",
					"MESSAGE_404" => ""
				),
				false, array("HIDE_ICONS" => "Y")
			);?>
		<?endif;?>
		<?$APPLICATION->ShowViewContent('sotbit_seometa_add_desc');?>
	</div>
<?endif;?>
<?
if (is_array($arElements) && !empty($arElements))
{
	if($arSKU)
	{
		foreach($arElements as $key => $value)
		{
			$arTmp = CIBlockElement::GetProperty($arSKU['IBLOCK_ID'], $value, array("sort" => "asc"), Array("ID"=>$arSKU['SKU_PROPERTY_ID']))->Fetch();
			if($arTmp['VALUE'])
				$arElements[$arTmp['VALUE']] = $arTmp['VALUE'];
		}
	}

	$GLOBALS[$arParams["FILTER_NAME"]] = array(
		"=ID" => $arElements,
		'SECTION_GLOBAL_ACTIVE' => 'Y',
	);

	if($arLanding && $arCustomFilter){
		if($bReplaceElementsByCustomFilter){
			$GLOBALS[$arParams["FILTER_NAME"]] = array($arCustomFilter);
		}
		else{
			$GLOBALS[$arParams["FILTER_NAME"]] = array_merge($GLOBALS[$arParams["FILTER_NAME"]], array($arCustomFilter));
		}
	}

	if($arParams['HIDE_NOT_AVAILABLE'] === 'Y'){
		$GLOBALS[$arParams["FILTER_NAME"]]['CATALOG_AVAILABLE'] = 'Y';
	}

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
		if($arRegion['LIST_STORES'] && $arParams["HIDE_NOT_AVAILABLE"] == "Y")
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

					$GLOBALS[$arParams["FILTER_NAME"]][] = array(
						'LOGIC' => 'OR',
						array('TYPE' => '2'),
						$arTmpFilter,
					);
				}
			}
		}
	}

	$arItems = CNextCache::CIBLockElement_GetList(
		array(
			'CACHE' => array(
				'MULTI' => 'Y',
				'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
			)
		),
		CNext::makeElementFilterInRegion($GLOBALS[$arParams["FILTER_NAME"]]),
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID',
			'IBLOCK_SECTION_ID',
		)
	);

	$arAllSections = $arSectionsID = $arItemsID = array();

	if($arItems){
		// sections
		ob_start();
		include_once 'sections.php';
		$htmlSections = ob_get_clean();
		$APPLICATION->AddViewContent('filter_section', $htmlSections);

		// sort
		ob_start();
		include_once 'sort.php';
		$htmlSort = ob_get_clean();
		$listElementsTemplate = $template;

		// filter
		ob_start();
		include_once 'filter.php';
		$htmlFilter = ob_get_clean();
		$APPLICATION->AddViewContent('filter_content', $htmlFilter);
	}
	?>
	<?if($isAjax === "Y"):?>
		<?$APPLICATION->RestartBuffer();?>
	<?endif;?>
	<?$APPLICATION->ShowViewContent('search_content');?>
	<div class="catalog vertical">
		<?if($arLanding && strlen($arLanding['PROPERTY_H3_GOODS_VALUE'])):?>
			<h3 class="title_block langing_title_block"><?=$arLanding['PROPERTY_H3_GOODS_VALUE']?></h3>
		<?endif;?>

		<?=$htmlSections;?>
		<br/>

		<?// sort?>
		<?=$htmlSort?>

		<?unset($_GET['q']);?>

		<div class="ajax_load <?=$display?>">
			<?$arTransferParams = array(
				"SHOW_ABSENT" => $arParams["SHOW_ABSENT"],
				"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
				"LIST_OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
				"LIST_OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
				"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
				"SHOW_COUNTER_LIST" => $arParams["SHOW_COUNTER_LIST"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
				"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
				"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
				"USE_REGION" => ($arRegion ? "Y" : "N"),
				"STORES" => $arParams["STORES"],
				"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
				"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
				"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
				"SHOW_DISCOUNT_TIME_EACH_SKU" => $arParams["SHOW_DISCOUNT_TIME_EACH_SKU"],
				"SHOW_ARTICLE_SKU" => $arParams["SHOW_ARTICLE_SKU"],
				"OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				'CURRENT_BASE_PAGE' => $arLanding && strlen($arLanding['PROPERTY_URL_CONDITION_VALUE']) ? $canonicalUrl : null,
				"IBINHERIT_TEMPLATES" => $arLanding ? $arIBInheritTemplates : array(),
				'OFFER_SHOW_PREVIEW_PICTURE_PROPS' => $arParams['OFFER_SHOW_PREVIEW_PICTURE_PROPS'],
			);?>
			<div class="catalog <?=$display;?> search js_wrapper_items" data-params='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arTransferParams, false))?>'>
				<?if($isAjax === "Y" && $isAjaxFilter !== "Y"):?>
					<?$APPLICATION->RestartBuffer();?>
				<?endif;?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:catalog.section",
					$listElementsTemplate,
					array(
						"USE_REGION" => ($arRegion ? "Y" : "N"),
						"STORES" => $arParams['STORES'],
						"TYPE_SKU" => $arTheme["TYPE_SKU"]["VALUE"],
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ELEMENT_SORT_FIELD" => $sort,
						"ELEMENT_SORT_ORDER" => $sort_order,
						"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
						"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
						"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
						"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
						"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
						"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
						"SHOW_ARTICLE_SKU" => $arParams["SHOW_ARTICLE_SKU"],
						"SHOW_MEASURE_WITH_RATIO" => $arParams["SHOW_MEASURE_WITH_RATIO"],
						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
						"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
						"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
						"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
						"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
						"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
						"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
						"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
						'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
						"SHOW_COUNTER_LIST" => $arParams["SHOW_COUNTER_LIST"],
						"DISPLAY_TYPE" => $display,
						"SECTION_URL" => $arParams["SECTION_URL"],
						"DETAIL_URL" => $arParams["DETAIL_URL"],
						"BASKET_URL" => $arParams["BASKET_URL"],
						"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
						"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
						"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
						"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
						"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
						"PRICE_CODE" => $arParams["PRICE_CODE"],
						"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
						"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
						"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
						"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
						"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
						"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
						"PAGER_TITLE" => $arParams["PAGER_TITLE"],
						"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
						"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
						"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
						"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
						"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
						"FILTER_NAME" => $arParams["FILTER_NAME"],
						"SECTION_ID" => ($setionIDRequest ? $setionIDRequest : ""),
						"SECTION_CODE" => "",
						"SECTION_USER_FIELDS" => array(),
						"INCLUDE_SUBSECTIONS" => "Y",
						"SHOW_ALL_WO_SECTION" => "Y",
						"META_KEYWORDS" => "",
						"META_DESCRIPTION" => "",
						"BROWSER_TITLE" => "",
						"ADD_SECTIONS_CHAIN" => "N",
						"SET_TITLE" => "N",
						"SET_STATUS_404" => "N",
						"CACHE_FILTER" => "Y",
						"AJAX_REQUEST" => (($isAjax == "Y" && $isAjaxFilter != "Y") ? "Y" : "N"),
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"DISPLAY_SHOW_NUMBER" => "N",
						"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
						"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
						"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
						"SALE_STIKER" => $arParams["SALE_STIKER"],
						"STIKERS_PROP" => $arParams["STIKERS_PROP"],
						"SHOW_RATING" => $arParams["SHOW_RATING"],
						"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
						"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
						"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
						"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
						"OFFER_HIDE_NAME_PROPS" => $arParams["OFFER_HIDE_NAME_PROPS"],
						"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
						"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
						'CURRENT_BASE_PAGE' => $arLanding && strlen($arLanding['PROPERTY_URL_CONDITION_VALUE']) ? $canonicalUrl : null,
						"SET_SKU_TITLE" => (($arTheme["TYPE_SKU"]["VALUE"] == "TYPE_1" && $arTheme["CHANGE_TITLE_ITEM"]["VALUE"] == "Y") ? "Y" : ""),
						"IBINHERIT_TEMPLATES" => $arLanding ? $arIBInheritTemplates : array(),
						'OFFER_SHOW_PREVIEW_PICTURE_PROPS' => $arParams['OFFER_SHOW_PREVIEW_PICTURE_PROPS'],
					),
					$arResult["THEME_COMPONENT"]
				);?>
				<?if($isAjax === "Y" && $isAjaxFilter !== "Y"):?>
					<?die();?>
				<?endif;?>
			</div>
		</div>
	</div>
	<?if($isAjax === "Y"):?>
		<?die();?>
	<?endif;?>
<?}else{
	if(!strlen($searchQuery))
		echo GetMessage("CT_BCSE_EMPTY_QUERY")."<br /><br />";
	else
		echo GetMessage("CT_BCSE_NOT_FOUND")."<br /><br />";
}
?>
<?if($arLanding):?>
	<?if(strlen($arLanding["DETAIL_TEXT"])):?>
		<?=$arLanding["DETAIL_TEXT"];?>
	<?endif;?>

	<?$APPLICATION->ShowViewContent('sotbit_seometa_bottom_desc');?>

	<?if($arParams['SHOW_LANDINGS'] !== 'N' && $arLanding['PROPERTY_SIMILAR_VALUE']):?>
		<?$arLandingsFilter['ID'] = $arLanding['PROPERTY_SIMILAR_VALUE'];?>
		<?$GLOBALS["arLandingsFilter"] = $arLandingsFilter;?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list",
			"landings_search_list",
			array(
				"IBLOCK_TYPE" => "aspro_next_catalog",
				"IBLOCK_ID" => CNextCache::$arIBlocks[SITE_ID]["aspro_next_catalog"]["aspro_next_search"][0],
				"NEWS_COUNT" => "999",
				"SHOW_COUNT" => $arParams["LANDING_SECTION_COUNT"],
				"SORT_BY1" => "SORT",
				"SORT_ORDER1" => "ASC",
				"SORT_BY2" => "ID",
				"SORT_ORDER2" => "DESC",
				"FILTER_NAME" => "arLandingsFilter",
				"FIELD_CODE" => array(
					0 => "",
					1 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "URL_CONDITION",
					1 => "REDIRECT_URL",
					2 => "QUERY",
					3 => "",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" =>$arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "N",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "j F Y",
				"SET_TITLE" => "N",
				"SET_STATUS_404" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"PAGER_TEMPLATE" => "",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "N",
				"PAGER_TITLE" => "",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"AJAX_OPTION_ADDITIONAL" => "",
				"COMPONENT_TEMPLATE" => "next",
				"SET_BROWSER_TITLE" => "N",
				"SET_META_KEYWORDS" => "N",
				"SET_META_DESCRIPTION" => "N",
				"SET_LAST_MODIFIED" => "N",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"TITLE_BLOCK" => $arParams["LANDING_TITLE"],
				"SHOW_404" => "N",
				"MESSAGE_404" => ""
			),
			false, array("HIDE_ICONS" => "Y")
		);?>
	<?endif;?>
	<?
	$langing_seo_h1 = strip_tags(htmlspecialchars_decode($arLanding["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != "" ? $arLanding["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arLanding["NAME"]));

	$APPLICATION->SetTitle($langing_seo_h1);

	if($arLanding["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"])
		$APPLICATION->SetPageProperty("title", strip_tags(htmlspecialchars_decode($arLanding["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"])));
	else
		$APPLICATION->SetPageProperty("title", strip_tags(htmlspecialchars_decode($arLanding["NAME"].$postfix)));

	if($arLanding["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])
		$APPLICATION->SetPageProperty("description", strip_tags(htmlspecialchars_decode($arLanding["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])));

	if($arLanding["IPROPERTY_VALUES"]['ELEMENT_META_KEYWORDS'])
		$APPLICATION->SetPageProperty("keywords", $arLanding["IPROPERTY_VALUES"]['ELEMENT_META_KEYWORDS']);
	?>
<?endif;?>
<?if($bShowFilter):?>
		</div><!-- middle -->
	</div><!-- right_block wide_N -->
	<div class="left_block filter_visible">
		<?$APPLICATION->ShowViewContent('filter_section');?>

		<?$APPLICATION->ShowViewContent('filter_content');?>

		<?$APPLICATION->ShowViewContent('under_sidebar_content');?>

		<?CNext::get_banners_position('SIDE', 'Y');?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
			array(
				"COMPONENT_TEMPLATE" => ".default",
				"PATH" => SITE_DIR."include/left_block/comp_subscribe.php",
				"AREA_FILE_SHOW" => "file",
				"AREA_FILE_SUFFIX" => "",
				"AREA_FILE_RECURSIVE" => "Y",
				"EDIT_TEMPLATE" => "include_area.php"
			),
			false
		);?>
	</div>
<?endif;?>