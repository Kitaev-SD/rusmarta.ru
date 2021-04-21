<?
foreach($arResult['ITEMS'] as $key => $arItem){
	if($arItem['IBLOCK_SECTION_ID'] && $arItem['PREVIEW_PICTURE']){
		$arSectionsIDs[] = $arItem['IBLOCK_SECTION_ID'];
	}
}
if($arResult['ITEMS'])
{
	if($arSectionsIDs){
		$arResult['SECTIONS'] = CNextCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arSectionsIDs));
	}

	// group elements by sections
	foreach($arResult['ITEMS'] as $arItem){
		$SID = ($arItem['IBLOCK_SECTION_ID'] ? $arItem['IBLOCK_SECTION_ID'] : 0);

		$arResult['SECTIONS'][$SID]['ITEMS'][$arItem['ID']] = $arItem;
	}

	// unset empty sections
	if(is_array($arResult['SECTIONS'])){
		foreach($arResult['SECTIONS'] as $i => $arSection){
			if(!$arSection['ITEMS']){
				unset($arResult['SECTIONS'][$i]);
			}
		}
	}
}
?>