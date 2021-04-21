<?

foreach($arResult['ITEMS'] as $arItem){
	if($SID = ($arItem['IBLOCK_SECTION_ID'] ? $arItem['IBLOCK_SECTION_ID'] : 0)){
		$arSectionsIDs[] = $SID;
	}
}

if($arSectionsIDs){
	$arResult['SECTIONS'] = CNextCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arSectionsIDs));
}

foreach($arResult['ITEMS'] as $key => $arItem){
	$SID = ($arItem['IBLOCK_SECTION_ID'] ? $arItem['IBLOCK_SECTION_ID'] : 0);
	if(strlen($arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE']))
	{
		$arItem['DETAIL_PAGE_URL'] = $arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE'];
		$arResult['ITEMS'][$key]['DETAIL_PAGE_URL'] = $arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE'];
	}
	if($arItem['DISPLAY_PROPERTIES'])
		$arResult['ITEMS'][$key]['SHOW_PROPS'] = CNext::PrepareItemProps($arItem['DISPLAY_PROPERTIES']);
	
	$arResult['SECTIONS'][$SID]['ITEMS'][$arItem['ID']] = $arItem;
	CNext::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
}

if(is_array($arResult['SECTIONS'])){
	foreach($arResult['SECTIONS'] as $i => $arSection){
		if(!$arSection['ITEMS']){
			unset($arResult['SECTIONS'][$i]);
		}
	}
}
?>