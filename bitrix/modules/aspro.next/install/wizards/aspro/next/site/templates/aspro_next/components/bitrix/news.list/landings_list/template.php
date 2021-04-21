<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>


<?if($arResult['ITEMS']):?>
	<?$i = 0;?>
	<div class="items landings_list">
		<?if($arParams["TITLE_BLOCK"]):?>
			<h4><?=$arParams["TITLE_BLOCK"];?></h4>
		<?endif;?>
		<div class="row margin0 flexbox">
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
			    ?>
			    <div class="col-md-4 col-sm-6 col-xs-12 <?=($bHidden)?"hidden_items":""?>"><a class="<?=($APPLICATION->GetCurDir() === $url) ? 'active' : ''?>" href="<?=$url?>" ><?=$arItem['NAME']?></a></div>			    
		    <?endforeach?>
		</div>
		<?if($bHidden ):?>
			<span class="more icons_fa" data-opened="N" data-text="<?=GetMessage("HIDE");?>"><?=GetMessage("SHOW_ALL");?></span>
		<?endif?>
	</div>
	<div class="clearfix"></div>
<?endif?>