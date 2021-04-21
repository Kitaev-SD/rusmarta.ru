<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
?>
<?if($arResult["ID"]):?>
	<?$bUseBigData = (ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N'));?>
	<?$disply_elements=($arParams["DISPLAY_ELEMENT_SLIDER"] ? $arParams["DISPLAY_ELEMENT_SLIDER"] : 10);?>
	<?if($templateData['BRAND_ITEM'] || $bUseBigData):?>
		<div class="col-md-3">
			<div class="right_info_block">
				<?if($templateData['BRAND_ITEM']):?>
					<div class="brand">
						<?if($templateData['BRAND_ITEM']["IMAGE"]):?>
							<div class="image"><a href="<?=$templateData['BRAND_ITEM']["DETAIL_PAGE_URL"];?>"><img src="<?=$templateData['BRAND_ITEM']["IMAGE"]["src"];?>" alt="<?=$templateData['BRAND_ITEM']["NAME"];?>" title="<?=$templateData['BRAND_ITEM']["NAME"];?>" itemprop="image"></a></div>
						<?endif;?>
						<div class="preview">
							<?if($templateData['BRAND_ITEM']["PREVIEW_TEXT"]):?>
								<div class="text"><?=$templateData['BRAND_ITEM']["PREVIEW_TEXT"];?></div>
							<?endif;?>
							<?if($arResult['SECTION']):?>
								<div class="link icons_fa"><a href="<?=$arResult['SECTION']['SECTION_PAGE_URL']?>filter/brand-is-<?=$templateData['BRAND_ITEM']['CODE'];?>/apply/" target="_blank"><?=GetMessage("ITEMS_BY_SECTION")?></a></div>
							<?endif;?>
							<div class="link icons_fa"><a href="<?=$templateData['BRAND_ITEM']["DETAIL_PAGE_URL"];?>" target="_blank"><?=GetMessage("ITEMS_BY_BRAND", array("#BRAND#" => $templateData['BRAND_ITEM']["NAME"]))?></a></div>
						</div>
					</div>
				<?endif;?>
				<?if($bUseBigData):?>
					<?include_once($_SERVER["DOCUMENT_ROOT"].$arParams["BIGDATA_PATH_TEMPLATE"]);?>
				<?endif;?>
			</div>
		</div>
	</div>
	<?endif;?>
	<?if($arParams["USE_REVIEW"] == "Y"):?>
		<?if($templateData["YM_ELEMENT_ID"]):?>
			<div id="reviews_content">
				<?$APPLICATION->IncludeComponent(
					"aspro:api.yamarket.reviews_model.next",
					"main",
					Array(
						"YANDEX_MODEL_ID" => $templateData["YM_ELEMENT_ID"]
					)
				);?>
			</div>
		<?elseif(IsModuleInstalled("forum")):?>
			<div id="reviews_content">
				<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("area");?>
					<?$APPLICATION->IncludeComponent(
						"bitrix:forum.topic.reviews",
						"main",
						Array(
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
							"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
							"FORUM_ID" => $arParams["FORUM_ID"],
							"ELEMENT_ID" => $arResult["ID"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
							"SHOW_RATING" => "N",
							"SHOW_MINIMIZED" => "Y",
							"SECTION_REVIEW" => "Y",
							"POST_FIRST_MESSAGE" => "Y",
							"MINIMIZED_MINIMIZE_TEXT" => GetMessage("HIDE_FORM"),
							"MINIMIZED_EXPAND_TEXT" => GetMessage("ADD_REVIEW"),
							"SHOW_AVATAR" => "N",
							"SHOW_LINK_TO_FORUM" => "N",
							"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
						),	false
					);?>
				<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("area", "");?>
			</div>
		<?endif;?>
	<?endif;?>
	<?if(($arParams["SHOW_ASK_BLOCK"] == "Y") && (intVal($arParams["ASK_FORM_ID"]))):?>
		<div id="ask_block_content">
			<?$APPLICATION->IncludeComponent(
				"bitrix:form.result.new",
				"inline",
				Array(
					"WEB_FORM_ID" => $arParams["ASK_FORM_ID"],
					"IGNORE_CUSTOM_TEMPLATE" => "N",
					"USE_EXTENDED_ERRORS" => "N",
					"SEF_MODE" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "3600000",
					"LIST_URL" => "",
					"EDIT_URL" => "",
					"SUCCESS_URL" => "?send=ok",
					"CHAIN_ITEM_TEXT" => "",
					"CHAIN_ITEM_LINK" => "",
					"VARIABLE_ALIASES" => Array("WEB_FORM_ID" => "WEB_FORM_ID", "RESULT_ID" => "RESULT_ID"),
					"AJAX_MODE" => "Y",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"SHOW_LICENCE" => CNext::GetFrontParametrValue('SHOW_LICENCE'),
				)
			);?>
		</div>
	<?endif;?>
	<script type="text/javascript">
		if($(".wraps.product_reviews_tab").length && $("#reviews_content").length){
			$("#reviews_content").insertAfter($(".wraps.product_reviews_tab h4"));
		}
		if($("#ask_block_content").length && $("#ask_block").length){
			$("#ask_block_content").appendTo($("#ask_block"));
		}
		if($(".gifts").length && $("#reviews_content").length){
			$(".gifts").insertAfter($("#reviews_content"));
		}
		if($("#reviews_content").length && !$(".tabs .tab-content .active").length){
			$(".shadow.common").hide();
			$("#reviews_content").show();
		}
		if(!$(".stores_tab").length){
			$('.item-stock .store_view').removeClass('store_view');
		}
		viewItemCounter('<?=$arResult["ID"];?>','<?=current($arParams["PRICE_CODE"]);?>');
	</script>
	<?if($templateData['ASSOCIATED'] || $templateData['ASSOCIATED_FILTER']):?>
		<div class="wraps hidden_print addon_type last_bottom">
			<hr>
			<h4><?=($arParams["DETAIL_ASSOCIATED_TITLE"] ? $arParams["DETAIL_ASSOCIATED_TITLE"] : GetMessage("DETAIL_ASSOCIATED_TITLE"))?></h4>
			<div class="bottom_slider specials tab_slider_wrapp custom_type">
			<ul class="slider_navigation top custom_flex border">
				<li class="tabs_slider_navigation accos_nav cur" data-code="accos"></li>
			</ul>

			<ul class="tabs_content">
				<li class="tab accos_wrapp cur" data-code="accos">
					<div class="flexslider loading_state shadow border custom_flex top_right" data-plugin-options='{"animation": "slide", "animationSpeed": 600, "directionNav": true, "controlNav" :false, "animationLoop": true, "slideshow": false, "controlsContainer": ".tabs_slider_navigation.accos_nav", "counts": [4,3,3,2,1]}'>
						<ul class="tabs_slider accos_slides slides">
							<?
							if($templateData['ASSOCIATED'])
								$GLOBALS['arrFilterAssoc'] = array("ID" => $templateData['ASSOCIATED']);

							$GLOBALS['arrFilterAssoc']['IBLOCK_ID'] = $arParams["IBLOCK_ID"];
							CNext::makeElementFilterInRegion($GLOBALS['arrFilterAssoc']);
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
									"FILTER_NAME" => 'arrFilterAssoc',
									"CUSTOM_FILTER" => $templateData['ASSOCIATED_FILTER'],
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
									"DISPLAY_COMPARE" => ($arParams["DISPLAY_COMPARE"] ? "Y" : "N"),
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
				</li>
			</ul>
		</div>
		</div>
	<?endif;?>
<?endif;?>
<?if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY'])){
	$loadCurrency = false;
	if (!empty($templateData['CURRENCIES']))
		$loadCurrency = Loader::includeModule('currency');
	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency){?>
		<script type="text/javascript">
			BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
		</script>
	<?}
}?>
<script type="text/javascript">
var viewedCounter = {
	path: '/bitrix/components/bitrix/catalog.element/ajax.php',
	params: {
		AJAX: 'Y',
		SITE_ID: "<?= SITE_ID ?>",
		PRODUCT_ID: "<?= $arResult['ID'] ?>",
		PARENT_ID: "<?= $arResult['ID'] ?>"
	}
};
BX.ready(
	BX.defer(function(){
		$('body').addClass('detail_page');
		BX.ajax.post(
			viewedCounter.path,
			viewedCounter.params
		);
	})
);
</script>
<?$des = new \Bitrix\Main\Page\FrameStatic('des');$des->startDynamicArea();?>
<script>
BX.ready(
	BX.defer(function(){
		if( $('.stores_tab').length ){
			var objUrl = parseUrlQuery(),
			add_url = '';
			if('clear_cache' in objUrl)
			{
				if(objUrl.clear_cache == 'Y')
					add_url = '?clear_cache=Y';
			}
			$.ajax({
				type:"POST",
				url:arNextOptions['SITE_DIR']+"ajax/productStoreAmount.php"+add_url,
				data:<?=CUtil::PhpToJSObject($templateData["STORES"], false, true, true)?>,
				success: function(data){
					var arSearch=parseUrlQuery();
					$('.tabs_section .stores_tab').html(data);
					if("oid" in arSearch)
					{
						$('.stores_tab .sku_stores_'+arSearch.oid).show();
					}
					else
					{
						var obSKU = window['<?=$templateData['STR_ID']?>'];
						if(typeof obSKU == "object")
						{
							obSKU.setStoreBlock(obSKU.offers[obSKU.offerNum].ID)
						}
						else
							$('.stores_tab > div:first').show();
					}

				}
			});
		}
	})
);
</script>
<?$des->finishDynamicArea();?>
<?if(isset($_GET["RID"])){?>
	<?if($_GET["RID"]){?>
		<script>
			$(document).ready(function() {
				$("<div class='rid_item' data-rid='<?=htmlspecialcharsbx($_GET["RID"]);?>'></div>").appendTo($('body'));
			});
		</script>
	<?}?>
<?}?>