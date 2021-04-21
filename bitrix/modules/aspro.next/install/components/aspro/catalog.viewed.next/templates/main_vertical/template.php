<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$arParams['TITLE_BLOCK'] = strlen($arParams['TITLE_BLOCK']) ? $arParams['TITLE_BLOCK'] : GetMessage('CATALOG_VIEWED_TITLE');
?>
<!-- noindex -->
<?if(strlen($arResult['ERROR'])):?>
	<?ShowError($arResult['ERROR']);?>
<?else:?>
	<?if($arResult['ITEMS']):?>
		<div class="viewed_block">
			<h3 class="title_block sm"><?=$arParams["TITLE_BLOCK"]?></h3>
			<div class="outer_wrap flexslider shadow items border custom_flex top_right" data-plugin-options='{"animation": "slide", "directionNav": true, "itemMargin":10, "controlNav" :false, "animationLoop": true, "slideshow": false, "counts": [8,4,3,2,1]}'>
				<ul class="rows_block slides">
					<?foreach($arResult['ITEMS'] as $key=>$arItem):?>
						<?
						if($key > 7)
							continue;
						$isItem = (isset($arItem['PRODUCT_ID']) ? true : false);
						?>
						<li class="item_block">
							<?if($isItem):?>
								<div data-id=<?=$arItem['PRODUCT_ID']?> data-picture='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arItem['PICTURE']))?>' class="item_wrap item <?=($isItem ? 'has-item' : '' );?>" id=<?=$this->GetEditAreaId($arItem['PRODUCT_ID'])?>>
									<?
									$this->AddEditAction($arItem['PRODUCT_ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
									$this->AddDeleteAction($arItem['PRODUCT_ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
									?>
								</div>
							<?else:?>
								<div class="item_wrap item"></div>
							<?endif;?>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
		<script type="text/javascript">
			BX.message({
				LAST_ACTIVE_FROM_VIEWED: '<?=$arResult['LAST_ACTIVE_FROM']?>',
				SHOW_MEASURE_VIEWED: '<?=($arParams['SHOW_MEASURE'] !== 'N' ? 'true' : '')?>',
				SITE_TEMPLATE_PATH: '<?=SITE_TEMPLATE_PATH?>',
				CATALOG_FROM_VIEWED: '<?=GetMessage('CATALOG_FROM')?>',
				SITE_ID: '<? echo SITE_ID; ?>'
			})
			var lastViewedTime = BX.message('LAST_ACTIVE_FROM_VIEWED');
			var bShowMeasure = BX.message('SHOW_MEASURE_VIEWED');
			var $viewedSlider = $('.viewed_block .item_block');

			showViewedItems(lastViewedTime, bShowMeasure, $viewedSlider);
		</script>
	<?endif;?>
<?endif;?>
<!-- /noindex -->