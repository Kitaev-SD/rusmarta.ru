
<?if($arSeoItem):?>
	<div class="seo_block">
		<?if($arSeoItem["DETAIL_PICTURE"]):?>
			<img src="<?=CFile::GetPath($arSeoItem["DETAIL_PICTURE"]);?>" alt="" title="" class="img-responsive"/>
		<?endif;?>

		<?ob_start();?>
		<?if($arSeoItem["PREVIEW_TEXT"]):?>
			<?=$arSeoItem["PREVIEW_TEXT"]?>
		<?endif;?>
		<?
		$html = ob_get_clean();
		$APPLICATION->AddViewContent('top_desc', $html);
		$APPLICATION->ShowViewContent('top_desc');
		$APPLICATION->ShowViewContent('sotbit_seometa_top_desc');
		?>

		<?if($arSeoItem["PROPERTY_FORM_QUESTION_VALUE"]):?>
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
		<?if($arSeoItem["PROPERTY_TIZERS_VALUE"]):?>
			<?$GLOBALS["arLandingTizers"] = array("ID" => $arSeoItem["PROPERTY_TIZERS_VALUE"]);?>
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

<?if($iSectionsCount):?>
	<div class="section_block">
		<?$APPLICATION->IncludeComponent(
			"bitrix:catalog.section.list",
			"front_sections_only",
			Array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"COUNT_ELEMENTS" => "N",
				"ADD_SECTIONS_CHAIN" => ((!$iSectionsCount || $arParams['INCLUDE_SUBSECTIONS'] !== "N") ? 'N' : 'Y'),
				"SHOW_SECTION_LIST_PICTURES" => $arParams["SHOW_SECTION_PICTURES"],
				"TOP_DEPTH" => "1",
				"FILTER_NAME" => "arSubSectionFilter",
				"CACHE_FILTER" => "Y",
			),
			$component, array('HIDE_ICONS' => 'Y')
		);?>
	</div>
<?endif;?>
<?global $arTheme;?>
<?$isAjax="N";?>
<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest"  && isset($_GET["ajax_get"]) && $_GET["ajax_get"] == "Y" || (isset($_GET["ajax_basket"]) && $_GET["ajax_basket"]=="Y")){
	$isAjax="Y";
}?>
<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest" && isset($_GET["ajax_get_filter"]) && $_GET["ajax_get_filter"] == "Y" ){
	$isAjaxFilter="Y";
}
if($isAjaxFilter == "Y")
	$isAjax="N";?>
<?$section_pos_top = \Bitrix\Main\Config\Option::get("aspro.next", "TOP_SECTION_DESCRIPTION_POSITION", "UF_SECTION_DESCR", SITE_ID );?>
<?$section_pos_bottom = \Bitrix\Main\Config\Option::get("aspro.next", "BOTTOM_SECTION_DESCRIPTION_POSITION", "DESCRIPTION", SITE_ID );?>
<?if($itemsCnt):?>
	<?
	//sort
	ob_start();
	include_once(__DIR__."/../sort.php");
	$sortHtml = ob_get_clean();
	$listElementsTemplate = $template;
	?>

	<?// sliceheight for ajax mode?>
	<?if($arParams['AJAX_MODE'] == 'Y' && strpos($_SERVER['REQUEST_URI'], 'bxajaxid') !== false):?>
		<script type="text/javascript">
			setTimeout(function(){
				$('.ajax_load .catalog_block .catalog_item_wrapp .catalog_item .item-title').sliceHeight({resize: false});
				$('.ajax_load .catalog_block .catalog_item_wrapp .catalog_item .cost').sliceHeight({resize: false});
				$('.ajax_load .catalog_block .catalog_item_wrapp .item_info').sliceHeight({resize: false});
				$('.ajax_load .catalog_block .catalog_item_wrapp').sliceHeight({classNull: '.footer_button', resize: false});
			}, 100);
			setStatusButton();
		</script>
	<?endif;?>

	<?// filer?>
	<?if($arTheme["FILTER_VIEW"]["VALUE"] === "VERTICAL"):?>
		<?//add filter with ajax?>
		<?if($arParams['AJAX_MODE'] == 'Y' && strpos($_SERVER['REQUEST_URI'], 'bxajaxid') !== false):?>
			<div class="filter_tmp swipeignore">
				<?include_once(__DIR__."/../filter.php")?>
			</div>
			<script type="text/javascript">
				if(typeof window['trackBarOptions'] !== 'undefined'){
					window['trackBarValues'] = {}
					for(key in window['trackBarOptions']){
						window['trackBarValues'][key] = {
							'leftPercent': window['trackBar' + key].leftPercent,
							'leftValue': window['trackBar' + key].minInput.value,
							'rightPercent': window['trackBar' + key].rightPercent,
							'rightValue': window['trackBar' + key].maxInput.value,
						}
					}
				}

				if($('.filter_wrapper_ajax').length)
					$('.filter_wrapper_ajax').remove();
				var filter_node = $('.left_block .bx_filter.bx_filter_vertical'),
					new_filter_node = $('<div class="filter_wrapper_ajax"></div>'),
					left_block_node = $('#content .left_block');
				if(!filter_node.length)
				{
					if(left_block_node.find('.menu_top_block').length)
						new_filter_node.insertAfter(left_block_node.find('.menu_top_block'));
				}
				else
				{
					new_filter_node.insertBefore(filter_node);
					filter_node.remove();
				}
				$('.filter_tmp').appendTo($('.filter_wrapper_ajax'));

				if(typeof window['trackBarOptions'] !== 'undefined'){
					for(key in window['trackBarOptions']){
						window['trackBarOptions'][key].leftPercent = window['trackBarValues'][key].leftPercent;
						window['trackBarOptions'][key].rightPercent = window['trackBarValues'][key].rightPercent;
						window['trackBarOptions'][key].curMinPrice = window['trackBarValues'][key].leftValue;
						window['trackBarOptions'][key].curMaxPrice = window['trackBarValues'][key].rightValue;
						window['trackBar' + key] = new BX.Iblock.SmartFilter(window['trackBarOptions'][key]);
						window['trackBar' + key].minInput.value = window['trackBarValues'][key].leftValue;
						window['trackBar' + key].maxInput.value = window['trackBarValues'][key].rightValue;
					}
				}

			</script>
		<?endif;?>
		<?ob_start();?>
			<?include_once(__DIR__."/../filter.php")?>
			<script>
				$('#content > .wrapper_inner > .left_block').addClass('filter_ajax filter_visible');
			</script>
		<?$html = ob_get_clean();?>
		<?$APPLICATION->AddViewContent('left_menu', $html);?>
	<?endif;?>
	<?if(isset($arParams['LANDING_POSITION']) && $arParams['LANDING_POSITION'] === 'BEFORE_PRODUCTS'):?>
	    <div class="<?=($arParams["LANDING_TYPE_VIEW"] ? $arParams["LANDING_TYPE_VIEW"] : "landing_1" );?>" >
		    <?@include_once(($arParams["LANDING_TYPE_VIEW"] ? $arParams["LANDING_TYPE_VIEW"] : "landing_1").'.php');?>
	    </div>
	<?endif;?>
	<div class="right_block1 clearfix catalog <?=strtolower($arTheme["FILTER_VIEW"]["VALUE"]);?>" id="right_block_ajax">
		<?if($arTheme["FILTER_VIEW"]["VALUE"] === "HORIZONTAL" || $arTheme["FILTER_VIEW"]["VALUE"] === "COMPACT"){?>
			<div class="<?=($arTheme["FILTER_VIEW"]["VALUE"]=="HORIZONTAL" ? 'filter_horizontal' : '');?><?=($arTheme["FILTER_VIEW"]["VALUE"]=="COMPACT" ? ' filter_compact' : '');?> swipeignore">
				<?include_once(__DIR__."/../filter.php")?>
			</div>
		<?}/*else{?>
			<div class="js_filter filter_horizontal">
				<?if($isAjaxFilter == "Y"):?>
					<?include(__DIR__."/../filter.php")?>
				<?else:?>
					<div class="bx_filter bx_filter_vertical"></div>
				<?endif;?>
			</div>
		<?}*/?>
		<div class="inner_wrapper">
<?endif;?>
			<?if(!$arSeoItem):?>
				<?if($arParams["SHOW_SECTION_DESC"] != 'N' && strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false):?>
					<?ob_start();?>
					<?if($posSectionDescr === "BOTH"):?>
						<?if ($arSection[$section_pos_top]):?>
							<div class="group_description_block top">
								<div><?=$arSection[$section_pos_top]?></div>
							</div>
						<?endif;?>
					<?elseif($posSectionDescr === "TOP"):?>
						<?if($arSection[$arParams["SECTION_PREVIEW_PROPERTY"]]):?>
							<div class="group_description_block top">
								<div><?=$arSection[$arParams["SECTION_PREVIEW_PROPERTY"]]?></div>
							</div>
						<?elseif($arSection["DESCRIPTION"]):?>
							<div class="group_description_block top">
								<div><?=$arSection["DESCRIPTION"]?></div>
							</div>
						<?elseif($arSection["UF_SECTION_DESCR"]):?>
							<div class="group_description_block top">
								<div><?=$arSection["UF_SECTION_DESCR"]?></div>
							</div>
						<?endif;?>
					<?endif;?>
					<?
					$html = ob_get_clean();
					$APPLICATION->AddViewContent('top_desc', $html);
					$APPLICATION->ShowViewContent('sotbit_seometa_top_desc');
					$APPLICATION->ShowViewContent('top_desc');
					?>
				<?endif;?>
			<?endif;?>

<?if($itemsCnt):?>
			<?if('Y' === $arParams['USE_FILTER']):?>
				<?
			    $matchesFilter = array();
			    if(isset($arParams["SEF_URL_TEMPLATES"]['smart_filter']) && strripos($arParams["SEF_URL_TEMPLATES"]['smart_filter'], "#SMART_FILTER_PATH#")) {
				$isSmartFilter = str_replace("#SMART_FILTER_PATH#", "(.*?)", $arParams["SEF_URL_TEMPLATES"]['smart_filter']);
				$isSmartFilter = preg_replace('/^#[a-zA-Z_]+#/i', "", $isSmartFilter);
				$isSmartFilter = str_replace("/", "\/", $isSmartFilter);
				preg_match("/".$isSmartFilter."/i", $APPLICATION->GetCurPage(), $matchesFilter);
			    }
				?>
				<div class="adaptive_filter">
					<a class="filter_opener<?=($_REQUEST['set_filter'] === 'y' ? ' active num' : '')?><?=(($_REQUEST['set_filter'] === 'y' || (count($matchesFilter)>1 && $matchesFilter[1] != 'clear')) ? ' active num' : '')?>"><i></i><span><?=GetMessage("CATALOG_SMART_FILTER_TITLE")?></span></a>
				</div>
			<?endif;?>

			<?if($isAjax=="N"){
				$frame = new \Bitrix\Main\Page\FrameHelper("viewtype-block");
				$frame->begin();?>
			<?}?>

			<?// sort?>
			<?=$sortHtml;?>

			<?if($isAjax === 'Y'){
				$APPLICATION->RestartBuffer();
			}?>

			<?$show = $arParams["PAGE_ELEMENT_COUNT"];?>

			<?if($isAjax === 'N'){?>
				<div class="ajax_load <?=$display;?>">
			<?}?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:catalog.section",
					$listElementsTemplate,
					Array(
						"USE_REGION" => ($arRegion ? "Y" : "N"),
						"STORES" => $arParams['STORES'],
						"SHOW_UNABLE_SKU_PROPS"=>$arParams["SHOW_UNABLE_SKU_PROPS"],
						"ALT_TITLE_GET" => $arParams["ALT_TITLE_GET"],
						"SEF_URL_TEMPLATES" => $arParams["SEF_URL_TEMPLATES"],
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"SHOW_COUNTER_LIST" => $arParams["SHOW_COUNTER_LIST"],
						"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
						"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
						"AJAX_REQUEST" => $isAjax,
						"ELEMENT_SORT_FIELD" => $sort,
						"ELEMENT_SORT_ORDER" => $sort_order,
						"SHOW_DISCOUNT_TIME_EACH_SKU" => $arParams["SHOW_DISCOUNT_TIME_EACH_SKU"],
						"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
						"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
						"FILTER_NAME" => $arParams["FILTER_NAME"],
						"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
						"PAGE_ELEMENT_COUNT" => $show,
						"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
						"DISPLAY_TYPE" => $display,
						"TYPE_SKU" => ($typeSKU ? $typeSKU : $arTheme["TYPE_SKU"]["VALUE"]),
						"SET_SKU_TITLE" => ((($typeSKU == "TYPE_1" || $arTheme["TYPE_SKU"]["VALUE"] == "TYPE_1") && $arTheme["CHANGE_TITLE_ITEM"]["VALUE"] == "Y") ? "Y" : ""),
						"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
						"SHOW_ARTICLE_SKU" => $arParams["SHOW_ARTICLE_SKU"],
						"SHOW_MEASURE_WITH_RATIO" => $arParams["SHOW_MEASURE_WITH_RATIO"],
						"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
						"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
						"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
						"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
						"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
						"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
						'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
						'OFFER_SHOW_PREVIEW_PICTURE_PROPS' => $arParams['OFFER_SHOW_PREVIEW_PICTURE_PROPS'],
						"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
						"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
						"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
						"BASKET_URL" => $arParams["BASKET_URL"],
						"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
						"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
						"PRODUCT_QUANTITY_VARIABLE" => "quantity",
						"PRODUCT_PROPS_VARIABLE" => "prop",
						"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
						"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
						"AJAX_MODE" => $arParams["AJAX_MODE"],
						"AJAX_OPTION_JUMP" => $arParams["AJAX_OPTION_JUMP"],
						"AJAX_OPTION_STYLE" => $arParams["AJAX_OPTION_STYLE"],
						"AJAX_OPTION_HISTORY" => $arParams["AJAX_OPTION_HISTORY"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"CACHE_FILTER" => "Y",
						"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
						"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
						"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
						"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
						"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
						'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
						"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
						"SET_TITLE" => $arParams["SET_TITLE"],
						"SET_STATUS_404" => $arParams["SET_STATUS_404"],
						"SHOW_404" => $arParams["SHOW_404"],
						"MESSAGE_404" => $arParams["MESSAGE_404"],
						"FILE_404" => $arParams["FILE_404"],
						"PRICE_CODE" => $arParams['PRICE_CODE'],
						"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
						"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
						"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
						"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
						"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
						"PAGER_TITLE" => $arParams["PAGER_TITLE"],
						"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
						"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
						"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
						"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
						"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
						"AJAX_OPTION_ADDITIONAL" => "",
						"ADD_CHAIN_ITEM" => "N",
						"SHOW_QUANTITY" => $arParams["SHOW_QUANTITY"],
						"SHOW_QUANTITY_COUNT" => $arParams["SHOW_QUANTITY_COUNT"],
						"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
						"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
						"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
						"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"USE_STORE" => $arParams["USE_STORE"],
						"MAX_AMOUNT" => $arParams["MAX_AMOUNT"],
						"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
						"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
						"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
						"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
						"LIST_DISPLAY_POPUP_IMAGE" => $arParams["LIST_DISPLAY_POPUP_IMAGE"],
						"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
						"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
						"SHOW_HINTS" => $arParams["SHOW_HINTS"],
						"OFFER_HIDE_NAME_PROPS" => $arParams["OFFER_HIDE_NAME_PROPS"],
						"SHOW_SECTIONS_LIST_PREVIEW" => $arParams["SHOW_SECTIONS_LIST_PREVIEW"],
						"SECTIONS_LIST_PREVIEW_PROPERTY" => $arParams["SECTIONS_LIST_PREVIEW_PROPERTY"],
						"SHOW_SECTION_LIST_PICTURES" => $arParams["SHOW_SECTION_LIST_PICTURES"],
						"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
						"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
						"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
						"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
						"SALE_STIKER" => $arParams["SALE_STIKER"],
						"STIKERS_PROP" => $arParams["STIKERS_PROP"],
						"SHOW_RATING" => $arParams["SHOW_RATING"],
						"ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
						"IBINHERIT_TEMPLATES" => $arSeoItem ? $arIBInheritTemplates : array(),
					), $component, array("HIDE_ICONS" => $isAjax)
				);?>
			<?if($isAjax !== 'Y'){?>
				</div>
				<?$frame->end();?>
			<?}?>
<?endif;?>
			<?if($isAjax === 'N'){?>
				<?if(!$arSeoItem):?>
					<?if($arParams["SHOW_SECTION_DESC"] != 'N' && strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false):?>
						<?ob_start();?>
						<?if($posSectionDescr === "BOTH"):?>
							<?if($arSection[$section_pos_bottom]):?>
								<div class="group_description_block bottom">
									<div><?=$arSection[$section_pos_bottom]?></div>
								</div>
							<?endif;?>
						<?elseif($posSectionDescr === "BOTTOM"):?>
							<?if($arSection[$arParams["SECTION_PREVIEW_PROPERTY"]]):?>
								<div class="group_description_block bottom">
									<div><?=$arSection[$arParams["SECTION_PREVIEW_PROPERTY"]]?></div>
								</div>
							<?elseif ($arSection["DESCRIPTION"]):?>
								<div class="group_description_block bottom">
									<div><?=$arSection["DESCRIPTION"]?></div>
								</div>
							<?elseif($arSection["UF_SECTION_DESCR"]):?>
								<div class="group_description_block bottom">
									<div><?=$arSection["UF_SECTION_DESCR"]?></div>
								</div>
							<?endif;?>
						<?endif;?>
						<?
						$html = ob_get_clean();
						$APPLICATION->AddViewContent('bottom_desc', $html);
						$APPLICATION->ShowViewContent('bottom_desc');
						$APPLICATION->ShowViewContent('sotbit_seometa_bottom_desc');
						$APPLICATION->ShowViewContent('sotbit_seometa_add_desc');
						?>
					<?endif;?>
				<?else:?>
					<?ob_start();?>
					<?if($arSeoItem["DETAIL_TEXT"]):?>
						<?=$arSeoItem["DETAIL_TEXT"];?>
					<?endif;?>
					<?
					$html = ob_get_clean();
					$APPLICATION->AddViewContent('bottom_desc', $html);
					$APPLICATION->ShowViewContent('bottom_desc');
					$APPLICATION->ShowViewContent('sotbit_seometa_bottom_desc');
					?>
				<?endif;?>
				<?if(!isset($arParams['LANDING_POSITION']) || $arParams['LANDING_POSITION'] === 'AFTER_PRODUCTS'):?>
					<div class="<?=($arParams["LANDING_TYPE_VIEW"] ? $arParams["LANDING_TYPE_VIEW"] : "landing_1" );?>" >
						<?@include_once(($arParams["LANDING_TYPE_VIEW"] ? $arParams["LANDING_TYPE_VIEW"] : "landing_1" ).'.php');?>
					</div>
				<?endif;?>

				<?if($itemsCnt):?>
					<div class="clear"></div>
					<?//</div> //.ajax_load?>
				<?endif;?>
			<?}?>

<?
global $arSite, $arTheme;
$postfix = "";
$bBitrixAjax = (strpos($_SERVER["QUERY_STRING"], "bxajaxid") !== false);
if($arTheme["HIDE_SITE_NAME_TITLE"]["VALUE"] == "N" && ($bBitrixAjax || $isAjaxFilter))
{
	$postfix = " - ".$arSite["NAME"];
}
?>
<?if($itemsCnt):?>
			<?if($isAjax == 'Y'){
				die();
			}?>
		</div>
	</div>
	<?if($bBitrixAjax)
	{
		$page_title = $arValues['SECTION_META_TITLE'] ? $arValues['SECTION_META_TITLE'] : $arSection["NAME"];
		if($page_title){
			$APPLICATION->SetPageProperty("title", $page_title.$postfix);
		}
	}?>
<?else:?>
	<?if(!$section):?>
		<?\Bitrix\Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
			,true
			,$arParams["SET_STATUS_404"] === "Y"
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);?>
	<?else:?>
		<?if(!$iSectionsCount):?>
			<div class="no_goods">
				<div class="no_products">
					<div class="wrap_text_empty">
						<?if($_REQUEST["set_filter"]){?>
							<?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products_filter.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?>
						<?}else{?>
							<?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?>
						<?}?>
					</div>
				</div>
			</div>
		<?endif;?>
		<?
		$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], IntVal($arSection["ID"]));
		$arValues = $ipropValues->getValues();
		if($arParams["SET_TITLE"] !== 'N'){
			$page_h1 = $arValues['SECTION_PAGE_TITLE'] ? $arValues['SECTION_PAGE_TITLE'] : $arSection["NAME"];
			if($page_h1){
				$APPLICATION->SetTitle($page_h1);
			}
			else{
				$APPLICATION->SetTitle($arSection["NAME"]);
			}
		}
		$page_title = $arValues['SECTION_META_TITLE'] ? $arValues['SECTION_META_TITLE'] : $arSection["NAME"];
		if($page_title){
			$APPLICATION->SetPageProperty("title", $page_title.$postfix);
		}
		if($arValues['SECTION_META_DESCRIPTION']){
			$APPLICATION->SetPageProperty("description", $arValues['SECTION_META_DESCRIPTION']);
		}
		if($arValues['SECTION_META_KEYWORDS']){
			$APPLICATION->SetPageProperty("keywords", $arValues['SECTION_META_KEYWORDS']);
		}
		?>
	<?endif;?>
<?endif;?>
<?
if($arSeoItem)
{
	$langing_seo_h1 = ($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != "" ? $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arSeoItem["NAME"]);

	$APPLICATION->SetTitle($langing_seo_h1);

	if($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"])
		$APPLICATION->SetPageProperty("title", $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
	else
		$APPLICATION->SetPageProperty("title", $arSeoItem["NAME"].$postfix);

	if($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])
		$APPLICATION->SetPageProperty("description", $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);

	if($arSeoItem["IPROPERTY_VALUES"]['ELEMENT_META_KEYWORDS'])
		$APPLICATION->SetPageProperty("keywords", $arSeoItem["IPROPERTY_VALUES"]['ELEMENT_META_KEYWORDS']);
	?>
<?}?>
<?if($isAjaxFilter):?>
	<?global $APPLICATION;?>
	<?$arAdditionalData['TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle());
	if($arSeoItem)
	{
		$postfix = '';
	}
	$arAdditionalData['WINDOW_TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle('title').$postfix);?>
	<script type="text/javascript">
		BX.removeCustomEvent("onAjaxSuccessFilter", function tt(e){});
		BX.addCustomEvent("onAjaxSuccessFilter", function tt(e){
			var arAjaxPageData = <?=CUtil::PhpToJSObject($arAdditionalData);?>;
			if (arAjaxPageData.TITLE)
				BX.ajax.UpdatePageTitle(arAjaxPageData.TITLE);
			if (arAjaxPageData.WINDOW_TITLE || arAjaxPageData.TITLE)
				BX.ajax.UpdateWindowTitle(arAjaxPageData.WINDOW_TITLE || arAjaxPageData.TITLE);
		});
	</script>
<?endif;?>
