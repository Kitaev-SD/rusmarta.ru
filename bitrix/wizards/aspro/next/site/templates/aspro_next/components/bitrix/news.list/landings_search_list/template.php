<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if($arResult['ITEMS']):?>
	<div class="items landings_list">
		<?if($arParams["TITLE_BLOCK"]):?>
			<h4><?=$arParams["TITLE_BLOCK"];?></h4>
		<?endif;?>
		<div class="wrap">
			<ul class="clearfix">
				<?$i = 0;?>
				<?$catalogDir = preg_replace('/[\?].*/', '', $arResult['ITEMS'][0]['DETAIL_PAGE_URL']);?>
				<?foreach($arResult['ITEMS'] as $arItem):?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

					$url = \Aspro\Next\SearchQuery::getLandingUrl(
						$catalogDir,
						$arItem['PROPERTIES']['URL_CONDITION']['VALUE'],
						$arItem['PROPERTIES']['REDIRECT_URL']['VALUE'],
						$arItem['PROPERTIES']['QUERY']['VALUE']
					);

					++$i;
					$bHidden = ($i > $arParams["SHOW_COUNT"] && $arParams["SHOW_COUNT"] > 1 ? true : false);
					?>
					<li class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
						<div>
							<?if(strlen($url)):?>
								<a class="<?=(strpos($APPLICATION->GetCurDir(), $url) !== false ? 'active' : '')?>" href="<?=$url?>"<?=(!array_key_exists('IS_INDEX', $arItem['PROPERTIES']) || $arItem['PROPERTIES']['IS_INDEX']['VALUE'] ? '' : ' rel="noindex,nofollow"')?>><?=$arItem['NAME']?></a>
							<?else:?>
								<span><?=$arItem['NAME']?></span>
							<?endif?>
						</div>
					</li>
					<?if($bHidden && !$bHiddenOK):?>
						<?
						$bHiddenOK = true;
						?>
						</ul>
						<ul class="hidden_items clearfix">
					<?endif?>
				<?endforeach?>
			</ul>
			<?if($bHidden):?>
				<div class="more"><span data-opened="N" data-text="<?=GetMessage("HIDE");?>"><?=GetMessage("SHOW_ALL");?></span></div>
			<?endif?>
		</div>
	</div>
<?endif?>