<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(!empty($arResult["NAV_RESULT"])) {
	$navParams =  array(
		"NavPageCount" => $arResult["NAV_RESULT"]->NavPageCount,
		"NavPageNomer" => $arResult["NAV_RESULT"]->NavPageNomer,
		"NavNum" => $arResult["NAV_RESULT"]->NavNum
	);
} else {
	$navParams = array(
		"NavPageCount" => 1,
		"NavPageNomer" => 1,
		"NavNum" => $this->randString()
	);
}

$showBottomPager = false;
$showLazyLoad = false;

if($arParams["PAGE_ELEMENT_COUNT"] > 0 && $navParams["NavPageCount"] > 1) {	
	$showBottomPager = $arParams["DISPLAY_BOTTOM_PAGER"];
	$showLazyLoad = $arParams["LAZY_LOAD"] === "Y" && $navParams["NavPageNomer"] != $navParams["NavPageCount"];
}

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => GetMessage("CT_BCS_TPL_ELEMENT_DELETE_CONFIRM"));

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($navParams["NavNum"]));
$containerName = "container-".$navParams["NavNum"];

//CATALOG//?>
<div id="catalog">	
	<div class="catalog-item-<?=$arParams['TYPE']?>-view" data-entity="<?=$containerName?>">		
		<?if(!empty($arResult["ITEMS"])) {
			$areaIds = array();
			foreach($arResult["ITEMS"] as $item) {
				$uniqueId = $item["ID"]."_".md5($this->randString().$component->getAction());
				$areaIds[$item["ID"]] = $this->GetEditAreaId($uniqueId);
				$this->AddEditAction($uniqueId, $item["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($uniqueId, $item["DELETE_LINK"], $elementDelete, $elementDeleteParams);
			}?>
			<!-- items-container -->
			<?foreach($arResult["ITEMS"] as $item) {
				$APPLICATION->IncludeComponent("bitrix:catalog.item", "",
					array(
						"RESULT" => array(
							"ITEM" => $item,
							"AREA_ID" => $areaIds[$item["ID"]],
							"TYPE" => $arParams["TYPE"]								
						),
						"PARAMS" => $arResult["ORIGINAL_PARAMETERS"] + array("SETTING" => $arResult["SETTING"])
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);
			}?>
			<!-- items-container -->
		<?} else {
			//load css for bigData/deferred load
			$APPLICATION->IncludeComponent("bitrix:catalog.item", "",
				array(),
				$component,
				array("HIDE_ICONS" => "Y")
			);
		}?>
	</div>
	<?if($showLazyLoad) {?>
		<div class="catalog_more" data-entity="show-more-container">
			<button type="button" class="btn_buy apuo" data-use="show-more-<?=$navParams['NavNum']?>"><?=(!empty($arParams["MESS_BTN_LAZY_LOAD"]) ? $arParams["MESS_BTN_LAZY_LOAD"] : GetMessage("CP_BCS_BTN_MESSAGE_LAZY_LOAD_DEFAULT"))?></button>
		</div>
	<?}
	if($showBottomPager) {?>
		<div class="catalog_pagination" data-pagination-num="<?=$navParams['NavNum']?>">
			<!-- pagination-container -->
			<?=$arResult["NAV_STRING"]?>
			<!-- pagination-container -->
		</div>
	<?}?>
</div>
<div class="clr"></div>

<?$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, "catalog.section");
$signedParams = $signer->sign(base64_encode(serialize($arResult["ORIGINAL_PARAMETERS"])), "catalog.section");?>

<script type="text/javascript">
	BX.ready(function() {
		BX.message({			
			ELEMENT_FROM: "<?=GetMessageJS('CT_BCS_ELEMENT_FROM')?>",
			ADDITEMINCART_ADDED: "<?=GetMessageJS('CT_BCS_ELEMENT_ADDED')?>",
			POPUP_WINDOW_TITLE: "<?=GetMessageJS('CT_BCS_ELEMENT_ADDITEMINCART_TITLE')?>",			
			POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CT_BCS_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
			POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CT_BCS_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
			SITE_DIR: "<?=SITE_DIR?>",
			POPUP_WINDOW_MORE_OPTIONS: "<?=GetMessageJS('CT_BCS_ELEMENT_MORE_OPTIONS')?>",			
			COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
			OFFERS_VIEW: "<?=$arResult['SETTING']['OFFERS_VIEW']?>",
			COMPONENT_PARAMS: "<?=CUtil::JSEscape($signedParams)?>",
			BTN_MESSAGE_LAZY_LOAD: "<?=$arParams['MESS_BTN_LAZY_LOAD']?>",
			BTN_MESSAGE_LAZY_LOAD_WAITER: "<?=GetMessageJS('CT_BCS_BTN_MESSAGE_LAZY_LOAD_WAITER')?>",
		});
		var <?=$obName?> = new JCCatalogSectionComponent({
			siteId: "<?=CUtil::JSEscape(SITE_ID)?>",
			componentPath: "<?=CUtil::JSEscape($componentPath)?>",
			navParams: <?=CUtil::PhpToJSObject($navParams)?>,
			lazyLoad: !!"<?=$showLazyLoad?>",
			loadOnScroll: !!"<?=($arParams['LOAD_ON_SCROLL'] === 'Y')?>",
			template: "<?=CUtil::JSEscape($signedTemplate)?>",
			ajaxId: "<?=CUtil::JSEscape($arParams['AJAX_ID'])?>",			
			container: "<?=$containerName?>"
		});
	});
</script>