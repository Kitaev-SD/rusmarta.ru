<?
use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager;
?>
<?CNext::AddMeta(
	array(
		'og:description' => $arElement['PREVIEW_TEXT'],
		'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
	)
);?>
<?$sViewElementTemplate = ($arParams["ELEMENT_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["CATALOG_PAGE_DETAIL"]["VALUE"] : $arParams["ELEMENT_TYPE_VIEW"]);?>
<?$hide_left_block = ($arTheme["LEFT_BLOCK_CATALOG_DETAIL"]["VALUE"] == "Y" ? "N" : "Y");
$arWidePage = array("element_3", "element_4", "element_5");

//set offer view type
$typeTmpDetail = 0;
if($arSection['UF_ELEMENT_DETAIL'])
	$typeTmpDetail = $arSection['UF_ELEMENT_DETAIL'];
else
{
	if($arSection["DEPTH_LEVEL"] > 2)
	{
		$sectionParent = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arSection["IBLOCK_SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
		if($sectionParent['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
			$typeTmpDetail = $sectionParent['UF_ELEMENT_DETAIL'];

		if(!$typeTmpDetail)
		{
			$sectionRoot = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
			if($sectionRoot['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
				$typeTmpDetail = $sectionRoot['UF_ELEMENT_DETAIL'];
		}
	}
	else
	{
		$sectionRoot = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
		if($sectionRoot['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
			$typeTmpDetail = $sectionRoot['UF_ELEMENT_DETAIL'];
	}
}
if($typeTmpDetail)
{
	$rsTypes = CUserFieldEnum::GetList(array(), array("ID" => $typeTmpDetail));
	if($arType = $rsTypes->GetNext())
		$typeDetail = $arType['XML_ID'];
	if($typeDetail)
		$sViewElementTemplate = $typeDetail;
}

if(in_array($sViewElementTemplate, $arWidePage))
	$hide_left_block = "Y";
?>
<?$APPLICATION->SetPageProperty("HIDE_LEFT_BLOCK", $hide_left_block)?>
<?if($arParams["USE_SHARE"] == "Y" && $arElement && !in_array($sViewElementTemplate, $arWidePage)):?>
	<?$this->SetViewTarget('product_share');?>
	<div class="line_block share top <?=($arParams['USE_RSS'] == 'Y' ? 'rss-block' : '');?>">
		<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
	</div>
	<?$this->EndViewTarget();?>
<?endif;?>
<?$isWideBlock = (isset($arParams["DIR_PARAMS"]["HIDE_LEFT_BLOCK"]) ? $arParams["DIR_PARAMS"]["HIDE_LEFT_BLOCK"] : "");?>
<?if($arParams['AJAX_MODE'] == 'Y' && strpos($_SERVER['REQUEST_URI'], 'bxajaxid') !== false):?>
	<script type="text/javascript">
		setStatusButton();
	</script>
<?endif;?>
<?$sViewBigDataTemplate = ($arParams["BIGDATA_NORMAL"] ? $arParams["BIGDATA_NORMAL"] : "bigdata_1" );?>
<?$sViewBigDataExtTemplate = ($arParams["BIGDATA_EXT"] ? $arParams["BIGDATA_EXT"] : "bigdata_2" );?>
<div class="catalog_detail detail<?=($isWideBlock == "Y" ? " fixed_wrapper" : "");?> <?=$sViewElementTemplate;?>" itemscope itemtype="http://schema.org/Product">
	<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
</div>

<?CNext::checkBreadcrumbsChain($arParams, $arSection, $arElement);?>
<div class="clearfix"></div>


<?$arAllValues=$arSimilar=$arAccessories=array();
$arShowTabs = array("element_1", "element_2");
if(!in_array($sViewElementTemplate, $arWidePage)):
/*similar goods*/
$arExpValues=CNextCache::CIBlockElement_GetProperty($arParams["IBLOCK_ID"], $ElementID, array("CACHE" => array("TAG" =>CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("CODE" => "EXPANDABLES"));
if($arExpValues){
	$arAllValues["EXPANDABLES"]=$arExpValues;
}
/*accessories goods*/
$arAccessories=CNextCache::CIBlockElement_GetProperty($arParams["IBLOCK_ID"], $ElementID, array("CACHE" => array("TAG" =>CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("CODE" => "ASSOCIATED"));
if($arAccessories){
	$arAllValues["ASSOCIATED"]=$arAccessories;
}

?>

<?if(($arAccessories || $arElement["PROPERTY_ASSOCIATED_FILTER_VALUE"]) || ($arExpValues || $arElement["PROPERTY_EXPANDABLES_FILTER_VALUE"]) || (ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N'))){?>
	<?$bViewBlock = ($arParams["VIEW_BLOCK_TYPE"] == "Y");?>
	<?
	$arTab=array();
	$arTmpExp = json_decode($arElement["PROPERTY_EXPANDABLES_FILTER_VALUE"], true);
	$arTmpAssoc = json_decode($arElement["PROPERTY_ASSOCIATED_FILTER_VALUE"], true);

	if($arExpValues || ($arElement["PROPERTY_EXPANDABLES_FILTER_VALUE"] && $arTmpExp["CHILDREN"])){
		$arTab["EXPANDABLES"]["TITLE"]=($arParams["DETAIL_EXPANDABLES_TITLE"] ? $arParams["DETAIL_EXPANDABLES_TITLE"] : GetMessage("EXPANDABLES_TITLE"));
		if($arElement["PROPERTY_EXPANDABLES_FILTER_VALUE"])
			$arTab["EXPANDABLES"]["FILTER"] = $arElement["PROPERTY_EXPANDABLES_FILTER_VALUE"];
	}
	if($arAccessories || ($arElement["PROPERTY_ASSOCIATED_FILTER_VALUE"] && $arTmpAssoc["CHILDREN"])){
		$arTab["ASSOCIATED"]["TITLE"]=( $arParams["DETAIL_ASSOCIATED_TITLE"] ? $arParams["DETAIL_ASSOCIATED_TITLE"] : GetMessage("ASSOCIATED_TITLE"));
		if($arElement["PROPERTY_ASSOCIATED_FILTER_VALUE"])
			$arTab["ASSOCIATED"]["FILTER"] = $arElement["PROPERTY_ASSOCIATED_FILTER_VALUE"];
	}
	/* Start Big Data */
	if(ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N'))
		$arTab["RECOMENDATION"]["TITLE"]= ( $arParams["TITLE_SLIDER"] ? $arParams["TITLE_SLIDER"] : GetMessage("RECOMENDATION_TITLE"));
	?>
	<?if($isWideBlock == "Y"):?>
		<div class="row">
			<div class="col-md-9">
	<?endif;?>

	<?if($bViewBlock):?>
		<div class="bottom_slider specials tab_slider_wrapp block_v">
			<?if($arTab):?>
				<?foreach($arTab as $code=>$arValue):?>
					<div class="wraps">
						<hr>
						<h4><?=$arValue["TITLE"];?></h4>
						<ul class="slider_navigation top custom_flex border">
							<li class="tabs_slider_navigation <?=$code?>_nav cur" data-code="<?=$code?>"></li>
						</ul>
						<ul class="tabs_content">
							<li class="tab <?=$code?>_wrapp cur" data-code="<?=$code?>">
								<?if($code=="RECOMENDATION"){?>
									<?
									$GLOBALS["CATALOG_CURRENT_ELEMENT_ID"] = $ElementID;

									$GLOBALS['arrFilter'.$code]['IBLOCK_ID'] = $arParams["IBLOCK_ID"];
									CNext::makeElementFilterInRegion($GLOBALS['arrFilter'.$code]);
									?>
									<?include_once('page_blocks/'.$sViewBigDataTemplate.'.php');?>

								<?}else{?>
									<div class="flexslider loading_state shadow border custom_flex top_right" data-plugin-options='{"animation": "slide", "animationSpeed": 600, "directionNav": true, "controlNav" :false, "animationLoop": true, "slideshow": false, "controlsContainer": ".tabs_slider_navigation.<?=$code?>_nav", "counts": [4,3,3,2,1]}'>
										<ul class="tabs_slider <?=$code?>_slides slides">
											<?
											if(array_key_exists($code, $arAllValues) && $arAllValues[$code])
												$GLOBALS['arrFilter'.$code] = array( "ID" => $arAllValues[$code] );

											$GLOBALS['arrFilter'.$code]['IBLOCK_ID'] = $arParams["IBLOCK_ID"];
											CNext::makeElementFilterInRegion($GLOBALS['arrFilter'.$code]);
											?>
											<?$APPLICATION->IncludeComponent(
												"bitrix:catalog.top",
												"main",
												array(
													"USE_REGION" => ($arRegion ? "Y" : "N"),
													"STORES" => $arParams['STORES'],
													"TITLE_BLOCK" => $arParams["SECTION_TOP_BLOCK_TITLE"],
													"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
													"IBLOCK_ID" => $arParams["IBLOCK_ID"],
													"SALE_STIKER" => $arParams["SALE_STIKER"],
													"STIKERS_PROP" => $arParams["STIKERS_PROP"],
													"SHOW_RATING" => $arParams["SHOW_RATING"],
													"CUSTOM_FILTER" => ($arValue["FILTER"] ? $arValue["FILTER"] : ""),
													"FILTER_NAME" => 'arrFilter'.$code,
													"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
													"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
													"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
													"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
													"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
													"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
													"BASKET_URL" => $arParams["BASKET_URL"],
													"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
													"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
													"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
													"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
													"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
													"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
													"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
													"ELEMENT_COUNT" => $disply_elements,
													"SHOW_MEASURE_WITH_RATIO" => $arParams["SHOW_MEASURE_WITH_RATIO"],
													"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
													"LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
													"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
													"PRICE_CODE" => $arParams['PRICE_CODE'],
													"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
													"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
													"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
													"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
													"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
													"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
													"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
													"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
													"CACHE_TYPE" => $arParams["CACHE_TYPE"],
													"CACHE_TIME" => $arParams["CACHE_TIME"],
													"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
													"CACHE_FILTER" => $arParams["CACHE_FILTER"],
													"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
													"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
													"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
													"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
													"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
													"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
													"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
													"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
													'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
													'CURRENCY_ID' => $arParams['CURRENCY_ID'],
													'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
													'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
													'VIEW_MODE' => (isset($arParams['TOP_VIEW_MODE']) ? $arParams['TOP_VIEW_MODE'] : ''),
													'ROTATE_TIMER' => (isset($arParams['TOP_ROTATE_TIMER']) ? $arParams['TOP_ROTATE_TIMER'] : ''),
													'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
													'LABEL_PROP' => $arParams['LABEL_PROP'],
													'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
													'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

													'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
													'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
													'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
													'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
													'SHOW_DISCOUNT_PERCENT_NUMBER' => $arParams['SHOW_DISCOUNT_PERCENT_NUMBER'],
													'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
													'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
													'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
													'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
													'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
													'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
													'ADD_TO_BASKET_ACTION' => $basketAction,
													'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
													'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
												),
												false, array("HIDE_ICONS"=>"Y")
											);?>
										</ul>
									</div>
								<?}?>
							</li>
						</ul>
					</div>
				<?endforeach;?>
			<?endif;?>
		</div>
	<?else:?>
	<div class="bottom_slider specials tab_slider_wrapp">
		<div class="top_blocks">
			<ul class="tabs">
				<?$i=1;
				foreach($arTab as $code=>$arValue):?>
					<li data-code="<?=$code?>" <?=($code=="RECOMENDATION" ? "style='display:none;'" : "" );?> <?=($i==1 ? "class='cur'" : "")?>><span><?=$arValue["TITLE"];?></span></li>
					<?$i++;?>
				<?endforeach;?>
				<li class="stretch"></li>
			</ul>
			<ul class="slider_navigation top custom_flex border">
				<?$i=1;
				foreach($arTab as $code=>$arValue):?>
					<li class="tabs_slider_navigation <?=$code?>_nav <?=($i==1 ? "cur" : "")?>" data-code="<?=$code?>"></li>
					<?$i++;?>
				<?endforeach;?>
			</ul>
		</div>

		<?
		$disply_elements=($arParams["DISPLAY_ELEMENT_SLIDER"] ? $arParams["DISPLAY_ELEMENT_SLIDER"] : 10);
		?>
		<ul class="tabs_content">
			<?foreach($arTab as $code=>$arValue){
				?>
				<li class="tab <?=$code?>_wrapp" data-code="<?=$code?>">
					<?if($code=="RECOMENDATION"){?>
						<?
						$GLOBALS["CATALOG_CURRENT_ELEMENT_ID"] = $ElementID;

						$GLOBALS['arrFilter'.$code]['IBLOCK_ID'] = $arParams["IBLOCK_ID"];
						CNext::makeElementFilterInRegion($GLOBALS['arrFilter'.$code]);
						?>
						<?include_once('page_blocks/'.$sViewBigDataTemplate.'.php');?>
					<?}else{
						?>
						<div class="flexslider loading_state shadow border custom_flex top_right" data-plugin-options='{"animation": "slide", "animationSpeed": 600, "directionNav": true, "controlNav" :false, "animationLoop": true, "slideshow": false, "controlsContainer": ".tabs_slider_navigation.<?=$code?>_nav", "counts": [4,3,3,2,1]}'>
						<ul class="tabs_slider <?=$code?>_slides slides">
							<?
							if(array_key_exists($code, $arAllValues) && $arAllValues[$code])
								$GLOBALS['arrFilter'.$code] = array( "ID" => $arAllValues[$code] );

							$GLOBALS['arrFilter'.$code]['IBLOCK_ID'] = $arParams["IBLOCK_ID"];
							CNext::makeElementFilterInRegion($GLOBALS['arrFilter'.$code]);
							?>
							<?$APPLICATION->IncludeComponent(
								"bitrix:catalog.top",
								"main",
								array(
									"USE_REGION" => ($arRegion ? "Y" : "N"),
									"STORES" => $arParams['STORES'],
									"TITLE_BLOCK" => $arParams["SECTION_TOP_BLOCK_TITLE"],
									"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
									"IBLOCK_ID" => $arParams["IBLOCK_ID"],
									"SALE_STIKER" => $arParams["SALE_STIKER"],
									"STIKERS_PROP" => $arParams["STIKERS_PROP"],
									"SHOW_RATING" => $arParams["SHOW_RATING"],
									"FILTER_NAME" => 'arrFilter'.$code,
									"CUSTOM_FILTER" => ($arValue["FILTER"] ? $arValue["FILTER"] : ""),
									"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
									"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
									"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
									"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
									"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
									"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
									"BASKET_URL" => $arParams["BASKET_URL"],
									"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
									"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
									"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
									"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
									"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
									"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
									"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
									"ELEMENT_COUNT" => $disply_elements,
									"SHOW_MEASURE_WITH_RATIO" => $arParams["SHOW_MEASURE_WITH_RATIO"],
									"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
									"LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
									"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
									"PRICE_CODE" => $arParams['PRICE_CODE'],
									"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
									"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
									"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
									"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
									"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
									"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
									"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
									"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
									"CACHE_TYPE" => $arParams["CACHE_TYPE"],
									"CACHE_TIME" => $arParams["CACHE_TIME"],
									"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
									"CACHE_FILTER" => $arParams["CACHE_FILTER"],
									"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
									"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
									"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
									"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
									"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
									"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
									"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
									"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
									'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
									'CURRENCY_ID' => $arParams['CURRENCY_ID'],
									'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
									'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
									'VIEW_MODE' => (isset($arParams['TOP_VIEW_MODE']) ? $arParams['TOP_VIEW_MODE'] : ''),
									'ROTATE_TIMER' => (isset($arParams['TOP_ROTATE_TIMER']) ? $arParams['TOP_ROTATE_TIMER'] : ''),
									'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
									'LABEL_PROP' => $arParams['LABEL_PROP'],
									'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
									'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

									'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
									'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
									'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
									'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
									'SHOW_DISCOUNT_PERCENT_NUMBER' => $arParams['SHOW_DISCOUNT_PERCENT_NUMBER'],
									'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
									'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
									'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
									'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
									'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
									'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
									'ADD_TO_BASKET_ACTION' => $basketAction,
									'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
									'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
								),
								false, array("HIDE_ICONS"=>"Y")
							);?>
						</ul>
						</div>
					<?}?>
				</li>
			<?}?>
		</ul>
	</div>
	<?endif;?>
	<?if($isWideBlock == "Y"):?>
			</div>
		</div>
	<?endif;?>
<?}?>
<?/*fix title after ajax form start*/
endif;
$arAdditionalData = $arNavParams = array();

$postfix = '';
global $arSite;
if(\Bitrix\Main\Config\Option::get("aspro.next", "HIDE_SITE_NAME_TITLE", "N")=="N")
	$postfix = ' - '.$arSite['SITE_NAME'];

$arAdditionalData['TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle());
$arAdditionalData['WINDOW_TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle('title').$postfix);

// dirty hack: try to get breadcrumb call params
for ($i = 0, $cnt = count($APPLICATION->buffer_content_type); $i < $cnt; $i++){
	if ($APPLICATION->buffer_content_type[$i]['F'][1] == 'GetNavChain'){
		$arNavParams = $APPLICATION->buffer_content_type[$i]['P'];
	}
}
if ($arNavParams){
	$arAdditionalData['NAV_CHAIN'] = $APPLICATION->GetNavChain($arNavParams[0], $arNavParams[1], $arNavParams[2], $arNavParams[3], $arNavParams[4]);
}
?>
<script type="text/javascript">
	if(!$('.js_seo_title').length)
		$('<span class="js_seo_title" style="display:none;"></span>').appendTo($('body'));
	BX.addCustomEvent(window, "onAjaxSuccess", function(e){
		var arAjaxPageData = <?=CUtil::PhpToJSObject($arAdditionalData, true, true, true);?>;

		//set title from offers
		if(typeof ItemObj == 'object' && Object.keys(ItemObj).length)
		{
			if('TITLE' in ItemObj && ItemObj.TITLE)
			{
				arAjaxPageData.TITLE = ItemObj.TITLE;
				arAjaxPageData.WINDOW_TITLE = ItemObj.WINDOW_TITLE;
			}
		}

		if (arAjaxPageData.TITLE)
			$('h1').html(arAjaxPageData.TITLE);
		if (arAjaxPageData.WINDOW_TITLE || arAjaxPageData.TITLE)
		{
			$('.js_seo_title').html(arAjaxPageData.WINDOW_TITLE || arAjaxPageData.TITLE); //seo fix for spec symbol
			BX.ajax.UpdateWindowTitle($('.js_seo_title').html());
		}

		if (arAjaxPageData.NAV_CHAIN)
			BX.ajax.UpdatePageNavChain(arAjaxPageData.NAV_CHAIN);
		$('.catalog_detail input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').html());
	});
</script>
<?/*fix title after ajax form end*/?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.history.js');?>