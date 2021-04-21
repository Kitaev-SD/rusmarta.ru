<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use \Bitrix\Main\Localization\Loc;?>
<?//show reviews pagination?>
<?if($arResult["ITEMS"]):?>
	<?if($arResult["TOTAL_PAGES"] > 1):?>
		<div class="bottom_nav block">
			<div class="module-pagination">
				<div class="nums">
					<ul class="flex-direction-nav">
						<?if(!$arResult["PAGINATION"]["PREV_DISABLED"]):?>
							<li class="flex-nav-prev "><a href="javascript:void(0)" data-page="<?=($arResult["CURRENT_PAGE"]-1);?>" title="<?=Loc::getMessage("YM_PREV_PAGE")?>" class="flex-prev"></a></li>
						<?endif;?>
						<?if(!$arResult["PAGINATION"]["NEXT_DISABLED"]):?>
							<li class="flex-nav-next "><a href="javascript:void(0)" data-page="<?=($arResult["CURRENT_PAGE"]+1);?>" title="<?=Loc::getMessage("YM_NEXT_PAGE")?>" class="flex-next"></a></li>
						<?endif;?>
					</ul>

					<?if($arResult["PAGINATION"]["START_PAGE"] > 1):?>
						<a href="javascript:void(0)" data-page="1" class="dark_link">1</a>
						<?if(($arResult["PAGINATION"]["START_PAGE"] - $arResult["PAGINATION"]["CI_DOTTED"]) > 1):?>
							<span class='point_sep'></span>
						<?elseif(($firstPage = $arResult["PAGINATION"]["START_PAGE"]-1) > 1 && $arResult["PAGINATION"]["START_PAGE"] !=2):?>
							<a href="javascript:void(0)" data-page="<?=$firstPage;?>"><?=$firstPage?></a>
						<?endif;?>
					<?endif;?>

					<?while($arResult["PAGINATION"]["START_PAGE"] <= $arResult["PAGINATION"]["END_PAGE"]):?>
						<?if($arResult["PAGINATION"]["START_PAGE"] == $arResult["CURRENT_PAGE"]):?>
							<span class="cur"><?=$arResult["PAGINATION"]["START_PAGE"]?></span>
						<?elseif($arResult["PAGINATION"]["START_PAGE"] == 1):?>
							<a href="javascript:void(0)" class="dark_link" data-page="<?=$arResult["PAGINATION"]["START_PAGE"];?>"><?=$arResult["PAGINATION"]["START_PAGE"]?></a>
						<?else:?>
							<a href="javascript:void(0)" class="dark_link" data-page="<?=$arResult["PAGINATION"]["START_PAGE"];?>"><?=$arResult["PAGINATION"]["START_PAGE"]?></a>
						<?endif;?>
						<?$arResult["PAGINATION"]["START_PAGE"]++;?>
					<?endwhile;?>

					<?if($arResult["PAGINATION"]["END_PAGE"] < $arResult["TOTAL_PAGES"]):?>
						<?if(($arResult["PAGINATION"]["END_PAGE"] + $arResult["PAGINATION"]["CI_DOTTED"]) < $arResult["TOTAL_PAGES"]):?>
							<span class='point_sep'></span>
						<?elseif(($lastPage = $arResult["PAGINATION"]["END_PAGE"]+1) < $arResult["TOTAL_PAGES"]):?>
							<a href="javascript:void(0)" data-page="<?=$lastPage;?>"><?=$lastPage?></a>
						<?endif;?>
						<a href="javascript:void(0)" data-page="<?=$arResult["TOTAL_PAGES"];?>" class="dark_link"><?=$arResult["TOTAL_PAGES"]?></a>
					<?endif;?>
				</div>
			</div>
		</div>
	<?endif;?>
<?endif;?>