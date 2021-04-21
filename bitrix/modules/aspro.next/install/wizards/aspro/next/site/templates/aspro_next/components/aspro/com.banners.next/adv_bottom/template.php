<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult['ITEMS']):?>
	<div class="adv_bottom_block hover_blink">
		<?foreach($arResult['ITEMS'] as $arItem)
		{
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$bUrl = (isset($arItem['DISPLAY_PROPERTIES']['URL']) && $arItem['DISPLAY_PROPERTIES']['URL']['VALUE']);
			$sUrl = ($bUrl ? $arItem['DISPLAY_PROPERTIES']['URL']['VALUE'] : '');
			?>
			<?if(is_array($arItem['PREVIEW_PICTURE']) ):?>
				<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item">
					<div class="img shine">
						<div class="img_inner">
							<?if($sUrl):?>
								<a href="<?=$sUrl;?>" title="<?=($arItem['PREVIEW_PICTURE']['TITLE']?$arItem['PREVIEW_PICTURE']['TITLE']:$arItem['NAME']);?>">
							<?endif;?>
							<span style="background-image:url(<?=$arItem['PREVIEW_PICTURE']['SRC']?>)"></span>
							<?if($sUrl):?>
								</a>
							<?endif;?>
						</div>
					</div>
				</div>
			<?endif;?>
		<?}?>
	</div>
<?endif;?>