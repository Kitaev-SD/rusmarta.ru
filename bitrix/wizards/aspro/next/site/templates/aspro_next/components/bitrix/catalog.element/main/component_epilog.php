<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
?>
<?if($arResult["ID"]):?>
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
		<div id="ask_block_content" class="hidden">
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
		if($("#ask_block_content").length && $("#ask_block").length){
			$("#ask_block_content").appendTo($("#ask_block"));
			$("#ask_block_content").removeClass("hidden");
		}
		if($(".gifts").length && $("#reviews_content").length){
			$(".gifts").insertAfter($("#reviews_content"));
		}
		if($("#reviews_content").length && (!$(".tabs .tab-content .active").length) || $('.product_reviews_tab.active').length){
			$(".shadow.common").hide();
			$("#reviews_content").show();
		}
		if(!$(".stores_tab").length){
			$('.item-stock .store_view').removeClass('store_view');
		}
		viewItemCounter('<?=$arResult["ID"];?>','<?=current($arParams["PRICE_CODE"]);?>');
	</script>
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
					$('.tab-content .tab-pane .stores_wrapp').html(data);
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
							$('.stores_tab .stores_wrapp > div:first').show();
					}
				}
			});
		}
	})
);
</script>
<?$des->finishDynamicArea();?>
<?if($_REQUEST && isset($_REQUEST['formresult'])):?>
	<script>
	$(document).ready(function() {
		if($('#ask_block .form_result').length){
			$('.product_ask_tab').trigger('click');
		}
	});
	</script>
<?endif;?>
<?if(isset($_GET["RID"])){?>
	<?if($_GET["RID"]){?>
		<script>
			$(document).ready(function() {
				$("<div class='rid_item' data-rid='<?=htmlspecialcharsbx($_GET["RID"]);?>'></div>").appendTo($('body'));
			});
		</script>
	<?}?>
<?}?>