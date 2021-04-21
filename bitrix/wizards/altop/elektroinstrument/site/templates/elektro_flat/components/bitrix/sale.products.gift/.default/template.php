<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => GetMessage("CT_SPG_TPL_ELEMENT_DELETE_CONFIRM"));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'sale-products-gift-container';?>

<div class="filtered-items" data-entity="parent-container">
	<?if(!isset($arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"]) || $arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"] != 'Y') {?>
		<div class="h3" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
			<?=($arParams["GIFTS_DETAIL_BLOCK_TITLE"] ?: Loc::getMessage('CT_SPG_ELEMENT_TITLE_DEFAULT'))?>
		</div>
	<?}?>
	<div class="catalog-item-cards" data-entity="<?=$containerName?>">
		<?if(!empty($arResult['ITEMS'])) {
			$areaIds = array();
			foreach($arResult['ITEMS'] as &$item) {
				$uniqueId = $item['ID'].'_'.md5($this->randString().$component->getAction());
				$areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);
				$this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
				$this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);
			}
			unset($item);?>
			<!-- items-container -->
			<?foreach($arResult['ITEMS'] as $item) {
				$APPLICATION->IncludeComponent("bitrix:catalog.item", "gift",
					array(
						"RESULT" => array(
							"ITEM" => $item,
							"AREA_ID" => $areaIds[$item["ID"]],
							"TYPE" => "card"
						),
						"PARAMS" => $arResult["ORIGINAL_PARAMETERS"] + array("SETTING" => $arResult["SETTING"])
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);
			}?>
			<!-- items-container -->
		<?} else {
			// load css for bigData/deferred load
			$APPLICATION->IncludeComponent(
				'bitrix:catalog.item', 'gift',
				array(),
				$component,
				array('HIDE_ICONS' => 'Y')
			);
		}?>
	</div>
	<div class="clr"></div>
</div>

<?$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, "sale.products.gift");
$signedParams = $signer->sign(base64_encode(serialize($arResult["ORIGINAL_PARAMETERS"])), "sale.products.gift");?>

<script type="text/javascript">
	BX.message({		
		GIFT_ADDITEMINCART_ADDED: "<?=GetMessageJS('CT_SPG_ELEMENT_ADDED')?>",
		GIFT_POPUP_WINDOW_TITLE: "<?=GetMessageJS('CT_SPG_ELEMENT_ADDITEMINCART_TITLE')?>",			
		GIFT_POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CT_SPG_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
		GIFT_POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CT_SPG_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
		GIFT_SITE_DIR: "<?=SITE_DIR?>",
		GIFT_POPUP_WINDOW_MORE_OPTIONS: "<?=GetMessageJS('CT_SPG_ELEMENT_MORE_OPTIONS')?>",			
		GIFT_COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
		GIFT_OFFERS_VIEW: "<?=$arResult['SETTING']['OFFERS_VIEW']?>",
		GIFT_COMPONENT_PARAMS: "<?=CUtil::JSEscape($signedParams)?>"
	});	
	var <?=$obName?> = new JCSaleProductsGiftComponent({
		siteId: "<?=CUtil::JSEscape(SITE_ID)?>",
		componentPath: "<?=CUtil::JSEscape($componentPath)?>",
		deferredLoad: true,
		initiallyShowHeader: "<?=!empty($arResult['ITEMS'])?>",
		currentProductId: <?=CUtil::JSEscape((int)$arResult['POTENTIAL_PRODUCT_TO_BUY']['ID'])?>,
		template: "<?=CUtil::JSEscape($signedTemplate)?>",		
		container: "<?=$containerName?>"
	});	
</script>