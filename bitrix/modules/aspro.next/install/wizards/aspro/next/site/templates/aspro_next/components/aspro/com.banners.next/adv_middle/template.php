<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult['ITEMS']):?>
	<div class="adv_list small_banners">
		<div class="row">
			<?foreach( $arResult['ITEMS'] as $arItem ){
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$bUrl = (isset($arItem['DISPLAY_PROPERTIES']['URL']) && $arItem['DISPLAY_PROPERTIES']['URL']['VALUE']);
				$sUrl = ($bUrl ? $arItem['DISPLAY_PROPERTIES']['URL']['VALUE'] : '');
				?>
				<div class="col-md-4 col-sm-6">
					<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item">					
						<?if(is_array($arItem['PREVIEW_PICTURE']) ):?>
							<div class="img shine">
								<div class="img_inner">
									<?if($sUrl):?>
										<a href="<?=$sUrl;?>">
									<?endif;?>
									<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=($arItem['PREVIEW_PICTURE']['ALT']?$arItem['PREVIEW_PICTURE']['ALT']:$arItem['NAME']);?>" title="<?=($arItem['PREVIEW_PICTURE']['TITLE']?$arItem['PREVIEW_PICTURE']['TITLE']:$arItem['NAME']);?>" />
									<?if($sUrl):?>
										</a>
									<?endif;?>
								</div>
							</div>
						<?endif;?>
					</div>
				</div>
			<?}?>
		</div>
	</div>
<?endif;?>