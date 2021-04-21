<?$this->setFrameMode(true);?>
<?if($arResult["NavPageCount"] > 1):?>
	<?
	$count_item_between_cur_page = 2; // count numbers left and right from cur page
	$count_item_dotted = 2; // count numbers to end or start pages

	$arResult["nStartPage"] = $arResult["NavPageNomer"] - $count_item_between_cur_page;
	$arResult["nStartPage"] = $arResult["nStartPage"] <= 0 ? 1 : $arResult["nStartPage"];
	$arResult["nEndPage"] = $arResult["NavPageNomer"] + $count_item_between_cur_page;
	$arResult["nEndPage"] = $arResult["nEndPage"] > $arResult["NavPageCount"] ? $arResult["NavPageCount"] : $arResult["nEndPage"];
	$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
	$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
	if($arResult["NavPageNomer"] == 1){
		$bPrevDisabled = true;
	}
	elseif($arResult["NavPageNomer"] < $arResult["NavPageCount"]){
		$bPrevDisabled = false;
	}
	if($arResult["NavPageNomer"] == $arResult["NavPageCount"]){
		$bNextDisabled = true;
	}
	else{
		$bNextDisabled = false;
	}
	?>
	<?global $APPLICATION;?>
	<?
	$bHasPage = (isset($_GET['PAGEN_'.$arResult["NavNum"]]) && $_GET['PAGEN_'.$arResult["NavNum"]]);
	if($bHasPage)
	{
		if($_GET['PAGEN_'.$arResult["NavNum"]] == 1 && !isset($_GET['q']))
		{
			LocalRedirect($arResult["sUrlPath"], false, "301 Moved permanently");
		}
		elseif($_GET['PAGEN_'.$arResult["NavNum"]] > $arResult["nEndPage"])
		{
			if (!defined("ERROR_404"))
			{
				define("ERROR_404", "Y");
				\CHTTP::setStatus("404 Not Found");
			}
		}

	}?>
	<div class="module-pagination">
		<div class="nums">
			<ul class="flex-direction-nav">
				<?if(!$bPrevDisabled):?>
					<?$page = ( $bHasPage ? ($arResult["NavPageNomer"]-1 == 1 ? '' : $arResult["NavPageNomer"]-1) : '' );
					$url = ($page ? '?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.$page : $strNavQueryStringFull);?>
					<li class="flex-nav-prev "><a href="<?=$arResult["sUrlPath"]?><?=$url?>" class="flex-prev"></a></li>
					<link rel="prev" href="<?=$arResult["sUrlPath"].$url?>" />
					<link rel="canonical" href="<?=$arResult["sUrlPath"]?>" />
				<?endif;?>
				<?if(!$bNextDisabled):?>
					<li class="flex-nav-next "><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" class="flex-next"></a></li>
					<link rel="next" href="<?=$arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1)?>" />
				<?endif;?>
			</ul>
			<?if($arResult["nStartPage"] > 1):?>
				<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1" class="dark_link">1</a>
				<?if(($arResult["nStartPage"] - $count_item_dotted) > 1):?>
					<span class='point_sep'></span>
				<?elseif(($firstPage = $arResult["nStartPage"]-1) > 1 && $arResult["nStartPage"] !=2):?>
					<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$firstPage?>"><?=$firstPage?></a>
				<?endif;?>
			<?endif;?>
			<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>
				<?if($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
					<span class="cur"><?=$arResult["nStartPage"]?></span>
				<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
					<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="dark_link"><?=$arResult["nStartPage"]?></a>
				<?else:?>
					<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" class="dark_link"><?=$arResult["nStartPage"]?></a>
				<?endif;?>
				<?$arResult["nStartPage"]++;?>
			<?endwhile;?>
			<?if($arResult["nEndPage"] < $arResult["NavPageCount"]):?>
				<?if(($arResult["nEndPage"] + $count_item_dotted) < $arResult["NavPageCount"]):?>
					<span class='point_sep'></span>
				<?elseif(($lastPage = $arResult["nEndPage"]+1) < $arResult["NavPageCount"]):?>
					<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$lastPage?>"><?=$lastPage?></a>
				<?endif;?>
				<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>" class="dark_link"><?=$arResult["NavPageCount"]?></a>
			<?endif;?>
			<?if ($arResult["bShowAll"]):?>
			<noindex>
				<div class="all_block_nav">
					<?if ($arResult["NavShowAll"]):?>
						<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0" class="link" rel="nofollow"><?=GetMessage("nav_paged")?></a>
					<?else:?>
						<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1" class="link" rel="nofollow"><?=GetMessage("nav_all")?></a>
					<?endif?>
				</div>
			</noindex>
			<?endif?>
		</div>
	</div>
<?endif;?>