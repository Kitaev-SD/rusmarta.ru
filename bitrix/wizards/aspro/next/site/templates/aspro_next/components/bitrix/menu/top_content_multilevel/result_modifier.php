<?
if($arResult){
	$arResult = CNext::getChilds($arResult);
	$isCatalog=false;
	$arCatalogItem=array();
	foreach($arResult as $key=>$arItem){
		if(isset($arItem["PARAMS"]["CLASS"]) && $arItem["PARAMS"]["CLASS"]=="catalog"){
			$isCatalog=true;
			$arCatalogItem=$arItem;
			unset($arResult[$key]);
		}
		if(isset($arItem["PARAMS"]["NOT_SHOW"]) && $arItem["PARAMS"]["NOT_SHOW"]=="Y"){
			unset($arResult[$key]);
		}
		if($arItem["CHILD"]){
			foreach($arItem["CHILD"] as $key2=>$arChild){
				if(isset($arChild["PARAMS"]["NOT_SHOW"]) && $arChild["PARAMS"]["NOT_SHOW"]=="Y"){
					unset($arResult[$key]["CHILD"][$key2]);
				}
				if($arChild["PARAMS"]["PICTURE"]){
					$img=CFile::ResizeImageGet($arChild["PARAMS"]["PICTURE"], Array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
					$arResult[$key]["CHILD"][$key2]["PARAMS"]["IMAGES"]=$img;
				}
			}
		}
	}
	if($isCatalog){
		global $arTheme, $arRegion;
		$catalog_id = \Bitrix\Main\Config\Option::get("aspro.next", "CATALOG_IBLOCK_ID", CNextCache::$arIBlocks[SITE_ID]['aspro_next_catalog']['aspro_next_catalog'][0]);
		$arSectionsFilter = array('IBLOCK_ID' => $catalog_id, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', '<DEPTH_LEVEL' =>\Bitrix\Main\Config\Option::get("aspro.next", "MAX_DEPTH_MENU", 2));
		$arSections = CNextCache::CIBlockSection_GetList(array('SORT' => 'ASC', 'ID' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($catalog_id), 'GROUP' => array('ID'))), CNext::makeSectionFilterInRegion($arSectionsFilter), false, array("ID", "NAME", "PICTURE", "LEFT_MARGIN", "RIGHT_MARGIN", "DEPTH_LEVEL", "SECTION_PAGE_URL", "IBLOCK_SECTION_ID"));
		$arTmpResult = array();
		if($arSections){

			$cur_page = $GLOBALS['APPLICATION']->GetCurPage(true);
			$cur_page_no_index = $GLOBALS['APPLICATION']->GetCurPage(false);

			foreach($arSections as $ID => $arSection){
				$arSections[$ID]['SELECTED'] = CMenu::IsItemSelected($arSection['SECTION_PAGE_URL'], $cur_page, $cur_page_no_index);
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
		}
		array_unshift($arResult, $arCatalogItem);
		$arResult[0]["CHILD"]=$arTmpResult;
	}
}?>