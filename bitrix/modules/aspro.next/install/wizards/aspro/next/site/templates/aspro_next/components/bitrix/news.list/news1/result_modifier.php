<?
foreach($arResult['ITEMS'] as $key => $arItem){
	if($SID = $arItem['IBLOCK_SECTION_ID']){
		$arSectionsIDs[] = $SID;
	}
	$arResult['ITEMS'][$key]['DETAIL_PAGE_URL'] = CNext::FormatNewsUrl($arItem);
	
	if(strlen($arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE']))
		unset($arResult['ITEMS'][$key]['DISPLAY_PROPERTIES']['REDIRECT']);
	
	CNext::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
}

if($arSectionsIDs){
	$arResult['SECTIONS'] = CNextCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arSectionsIDs));
}
?>