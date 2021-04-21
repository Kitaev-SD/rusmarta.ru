<?
if($arItems){
	$setionIDRequest = (isset($_GET["section_id"]) && $_GET["section_id"] ? $_GET["section_id"] : 0);

	foreach($arItems as $arItem){
		$arItemsID[$arItem["ID"]] = $arItem["ID"];
		if($arItem["IBLOCK_SECTION_ID"] && $arItem["IBLOCK_ID"] == $catalogIBlockID){
			if(is_array($arItem["IBLOCK_SECTION_ID"])){
				foreach($arItem["IBLOCK_SECTION_ID"] as $id){
					$arAllSections[$id]["COUNT"]++;
					$arAllSections[$id]["ITEMS"][$arItem["ID"]] = $arItem["ID"];
				}
			}
			else
			{
				$arAllSections[$arItem["IBLOCK_SECTION_ID"]]["COUNT"]++;
				$arAllSections[$arItem["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]] = $arItem["ID"];
			}
		}
	}

	$arSectionsID = array_keys($arAllSections);
	?>
	<?if(count($arAllSections) > 1):?>
		<?
		$arSections = CNextCache::CIBlockSection_GetList(array('NAME' => 'ASC', 'CACHE' => array("MULTI" => "N", "GROUP" => array("ID"), "TAG" => CNextCache::GetIBlockCacheTag($catalogIBlockID))), array("ID" => $arSectionsID, "IBLOCK_ID" => $catalogIBlockID), false, array("ID", "IBLOCK_ID", "NAME"));

		$arDeleteParams = array('section_id');
		if(preg_match_all('/PAGEN_\d+/i'.BX_UTF_PCRE_MODIFIER, $_SERVER['QUERY_STRING'], $arMatches)){
			$arPagenParams = $arMatches[0];
			$arDeleteParams = array_merge($arDeleteParams, $arPagenParams);
		}
		?>
		<div class="top_block_filter_section toggle_menu">
			<div class="title"><a class="dark_link" title="<?=GetMessage("FILTER_ALL_SECTON");?>" href="<?=$APPLICATION->GetCurPageParam('', $arDeleteParams)?>"><?=GetMessage($arLanding && $arLanding['PROPERTY_HIDE_QUERY_INPUT_VALUE'] ? 'FILTER_SECTON' : 'FILTER_SECTON_SEARCH')?></a></div>
			<div class="items">
				<?
				$cntToShow = ($cntToShow = intval($arParams['SECTIONS_SEARCH_COUNT'])) > 0 ? $cntToShow : count($arSections);
				$cntShow = 0;
				$bCurrentShowed = false;
				$bNeedShowCurrent = in_array($setionIDRequest, $arSectionsID);
				?>
				<?foreach($arSections as $sId => $arSection):?>
					<?
					$bCurrent = $setionIDRequest && $sId == $setionIDRequest;
					$bCurrentShowed |= $bCurrent;
					$bLastToShow = $cntShow == ($cntToShow - 1);
					$bCollapsed = ($bLastToShow && $bNeedShowCurrent && !$bCurrentShowed) ? true : !$bCurrent && $cntShow >= $cntToShow;
					if(!$bCollapsed){
						++$cntShow;
					}
					?>
					<div class="item<?=($bCurrent ? ' current' : '')?><?=($bCollapsed ? ' collapsed' : '')?>"><a href="<?=$APPLICATION->GetCurPageParam('section_id='.$sId, $arDeleteParams)?>" class="dark_link"><span class="item_title"><?=$arSection['NAME']?></span><noindex><span class="item_count"><?=$arAllSections[$sId]['COUNT']?></span></noindex></a></div>
				<?endforeach;?>
				<?$cntMore = count($arSections) - $cntShow;?>
				<?if($cntMore > 0):?>
					<div class="item"><span class="item_title colored more_items with_dropdown"><?=GetMessage('MORE_SECTIONS')?> <?=Aspro\Functions\CAsproNext::declOfNum($cntMore, array(GetMessage('MORE_SECTIONS0'), GetMessage('MORE_SECTIONS1'), GetMessage('MORE_SECTIONS2')))?></span></div>
				<?endif;?>
			</div>
		</div>
	<?endif;?>
	<?
}
?>