<div class="row">
	<div class="">
		<div class="col-md-3 col-sm-3 hidden-xs hidden-sm left-menu-md">
			<?CNext::ShowPageType('left_block');?>
		</div>
		<div class="col-md-9 col-sm-12 col-xs-12 content-md">
			<?
			$bHasSection = (isset($arSection['ID']) && $arSection['ID']);
			if($bHasSection):?>

			<?// edit/add/delete buttons for edit mode
			$arSectionButtons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], array('SESSID' => false, 'CATALOG' => true));
			$this->AddEditAction($arSection['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_EDIT'));
			$this->AddDeleteAction($arSection['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div class="main-section-wrapper" id="<?=$this->GetEditAreaId($arSection['ID'])?>">
				<?$arSubSectionFilter = CNext::GetCurrentSectionSubSectionFilter($arResult["VARIABLES"], $arParams, $arSection['ID']);
				$arSubSections = CNextCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), $arSubSectionFilter, false, array("ID", "DEPTH_LEVEL"));?>
				<?if($arSubSections):?>
					<?// sections list?>
					<?@include_once('page_blocks/'.$arParams["SECTION_TYPE_VIEW"].'.php');?>
				<?endif;?>
			<?endif;?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					($bHasSection ? 'news-project' : 'news-section2'),
					Array(
						"IMAGE_POSITION" => $arParams["IMAGE_POSITION"],
						"SHOW_CHILD_SECTIONS" => $arParams["SHOW_CHILD_SECTIONS"],
						"DEPTH_LEVEL" => 1,
						"LINE_ELEMENT_COUNT_LIST" => $arParams["LINE_ELEMENT_COUNT_LIST"],
						"IMAGE_WIDE" => $arParams["IMAGE_WIDE"],
						"SHOW_SECTION_PREVIEW_DESCRIPTION" => $arParams["SHOW_SECTION_PREVIEW_DESCRIPTION"],
						"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
						"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
						"NEWS_COUNT"	=>	$arParams["NEWS_COUNT"],
						"SORT_BY1"	=>	$arParams["SORT_BY1"],
						"SORT_ORDER1"	=>	$arParams["SORT_ORDER1"],
						"SORT_BY2"	=>	$arParams["SORT_BY2"],
						"SORT_ORDER2"	=>	$arParams["SORT_ORDER2"],
						"FIELD_CODE"	=>	$arParams["LIST_FIELD_CODE"],
						"PROPERTY_CODE"	=>	$arParams["LIST_PROPERTY_CODE"],
						"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
						"SET_TITLE"	=>	$arParams["SET_TITLE"],
						"SET_STATUS_404" => $arParams["SET_STATUS_404"],
						"INCLUDE_IBLOCK_INTO_CHAIN"	=>	$arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
						"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
						"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
						"CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"DISPLAY_TOP_PAGER"	=>	$arParams["DISPLAY_TOP_PAGER"],
						"DISPLAY_BOTTOM_PAGER"	=>	$arParams["DISPLAY_BOTTOM_PAGER"],
						"PAGER_TITLE"	=>	$arParams["PAGER_TITLE"],
						"PAGER_TEMPLATE"	=>	$arParams["PAGER_TEMPLATE"],
						"PAGER_SHOW_ALWAYS"	=>	$arParams["PAGER_SHOW_ALWAYS"],
						"PAGER_DESC_NUMBERING"	=>	$arParams["PAGER_DESC_NUMBERING"],
						"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
						"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
						"DISPLAY_DATE"	=>	$arParams["DISPLAY_DATE"],
						"DISPLAY_NAME"	=>	$arParams["DISPLAY_NAME"],
						"DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
						"DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
						"PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
						"ACTIVE_DATE_FORMAT"	=>	$arParams["LIST_ACTIVE_DATE_FORMAT"],
						"USE_PERMISSIONS"	=>	$arParams["USE_PERMISSIONS"],
						"GROUP_PERMISSIONS"	=>	$arParams["GROUP_PERMISSIONS"],
						"SHOW_DETAIL_LINK"	=>	$arParams["SHOW_DETAIL_LINK"],
						"FILTER_NAME"	=>	$arParams["FILTER_NAME"],
						"HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
						"CHECK_DATES"	=>	$arParams["CHECK_DATES"],
						"PARENT_SECTION"	=>	$arResult["VARIABLES"]["SECTION_ID"],
						"PARENT_SECTION_CODE"	=>	$arResult["VARIABLES"]["SECTION_CODE"],
						"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
						"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
						"IBLOCK_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
						"INCLUDE_SUBSECTIONS" => "N",
					),
					$component
				);?>
			<?if($bHasSection):?>
				</div>
			<?endif;?>
		</div>
	</div>
</div>