<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if($arResult['ITEMS']):?>
	<?$i = 0;?>
	<div class="items landings_list3">
		<?if($arParams["TITLE_BLOCK"]):?>
			<h4><?=$arParams["TITLE_BLOCK"];?></h4>
		<?endif;?>
		<?$compare_field = (isset($arParams["COMPARE_FIELD"]) && $arParams["COMPARE_FIELD"] ? $arParams["COMPARE_FIELD"] : "DETAIL_PAGE_URL");
		    $bProp = (isset($arParams["COMPARE_PROP"]) && $arParams["COMPARE_PROP"] == "Y");?>
		<?foreach($arResult['ITEMS'] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

			++$i;
			$bHidden = ($i > $arParams["SHOW_COUNT"] && $arParams["SHOW_COUNT"] >= 1 ? true : false);
			$url = $arItem[$compare_field];
			if($bProp)
				$url = $arItem["PROPERTIES"][$compare_field]["VALUE"];

			if(strlen($url)):?>
				<a title="<?=$arItem['NAME']?>" class="<?=($APPLICATION->GetCurDir() === $url) ? 'active' : ''?>" href="<?=$url?>" ><?=$arItem['NAME']?></a>
			<?endif?>
				
			<?if($bHidden && !$bHiddenOK):?>
				<?$bHiddenOK = true;?>
				<div class="hidden_items">
			<?endif?>
		<?endforeach?>
				    
		<?if($bHidden ):?>
			</div><span class="more icons_fa" data-opened="N" data-text="<?=GetMessage("HIDE");?>"><?=GetMessage("SHOW_ALL");?></span>
		<?endif?>
	</div>
	<div class="clearfix"></div>
<?endif?>