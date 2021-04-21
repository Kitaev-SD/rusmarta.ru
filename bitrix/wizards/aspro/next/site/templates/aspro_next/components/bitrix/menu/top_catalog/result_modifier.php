<?if($arResult){
	global $arTheme, $arRegion;
	$catalog_id = \Bitrix\Main\Config\Option::get("aspro.next", "CATALOG_IBLOCK_ID", CNextCache::$arIBlocks[SITE_ID]['aspro_next_catalog']['aspro_next_catalog'][0]);
	$arSectionsFilter = array('IBLOCK_ID' => $catalog_id, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', '<DEPTH_LEVEL' => $arParams['MAX_LEVEL']);
	$arSections = CNextCache::CIBlockSection_GetList(array('SORT' => 'ASC', 'ID' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($catalog_id), 'GROUP' => array('ID'))), CNext::makeSectionFilterInRegion($arSectionsFilter), false, array("ID", "NAME", "PICTURE", "LEFT_MARGIN", "RIGHT_MARGIN", "DEPTH_LEVEL", "SECTION_PAGE_URL", "IBLOCK_SECTION_ID"));
	if($arSections){
		$arTmpResult = array();
		$cur_page = $GLOBALS['APPLICATION']->GetCurPage(true);
		$cur_page_no_index = $GLOBALS['APPLICATION']->GetCurPage(false);

		foreach($arSections as $ID => $arSection){
			$arSections[$ID]['SELECTED'] = CMenu::IsItemSelected($arSection['SECTION_PAGE_URL'], $cur_page, $cur_page_no_index);
			if($arSection['PICTURE']){
				$img=CFile::ResizeImageGet($arSection['PICTURE'], Array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				$arSections[$ID]['IMAGES']=$img;
			}
			if($arSection['IBLOCK_SECTION_ID']){
				if(!isset($arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'])){
					$arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'] = array();
				}
				$arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'][] = &$arSections[$arSection['ID']];
			}

			if($arSection['DEPTH_LEVEL'] == 1){
				$arTmpResult[] = &$arSections[$arSection['ID']];
			}
		}
		$arResult[0]["CHILD"]=$arTmpResult;
	}
}?>