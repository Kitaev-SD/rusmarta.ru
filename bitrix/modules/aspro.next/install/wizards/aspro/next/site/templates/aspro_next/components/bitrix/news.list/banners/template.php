<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>


<?if($arResult['ITEMS']):?>
	<?$count = count($arResult['ITEMS']);?>
	<?foreach($arResult['ITEMS'] as $i => $arItem):?>
		<?
			// edit/add/delete buttons for edit mode
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

			// show preview picture?
			$bImage = isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']);
			$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : false);
		?>
		<div class="banner <?=$arItem['PROPERTIES']['SIZING']['VALUE_XML_ID']?> <?=$arParams['POSITION']?> <?=($arItem['PROPERTIES']['HIDDEN_SM']['VALUE_XML_ID']=='Y'?'hidden-sm':'')?> <?=($arItem['PROPERTIES']['HIDDEN_XS']['VALUE_XML_ID']=='Y'?'hidden-xs':'')?>" <?=($arItem['PROPERTIES']['BGCOLOR']['VALUE']?' style=" background:'.$arItem['PROPERTIES']['BGCOLOR']['VALUE'].';"':'')?> id="<?=$this->GetEditAreaId($arItem['ID'])?>">
			<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
				<a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" <?=($arItem["PROPERTIES"]["TARGET"]["VALUE_XML_ID"] ? "target='".$arItem["PROPERTIES"]["TARGET"]["VALUE_XML_ID"]."'" : "");?>>
			<?endif;?>
				<img src="<?=$imageSrc?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" class="<?=$arItem['PROPERTIES']['SIZING']['VALUE_XML_ID']=='CROP'?'':'img-responsive'?>" />
			<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
				</a>
			<?endif;?>
		</div>
	<?endforeach;?>
<?endif;?>