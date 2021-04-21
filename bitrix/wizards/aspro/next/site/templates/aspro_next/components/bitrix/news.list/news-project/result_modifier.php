<?
foreach($arResult['ITEMS'] as $key => $arItem)
{
	/*if($SID = $arItem['IBLOCK_SECTION_ID']){
		$arSectionsIDs[] = $SID;
	}*/
	if($arItem['IBLOCK_SECTION_ID']){
		$resGroups = CIBlockElement::GetElementGroups($arItem['ID'], true, array('ID'));
		while($arGroup = $resGroups->Fetch())
		{
			$arResult['ITEMS'][$key]['SECTIONS'][$arGroup['ID']] = $arGroup['ID'];
			$arGoodsSectionsIDs[$arGroup['ID']] = $arGroup['ID'];
		}
	}
	CNext::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
}
/*if($arSectionsIDs){
	$arResult['SECTIONS'] = CNextCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arSectionsIDs));
}*/
if($arGoodsSectionsIDs){
	$arResult['SECTIONS'] = CNextCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arGoodsSectionsIDs));
	foreach($arResult['ITEMS'] as $key => $arItem)
	{
		if($arItem['IBLOCK_SECTION_ID'])
		{
			foreach($arItem['SECTIONS'] as $id => $name)
				$arResult['ITEMS'][$key]['SECTIONS'][$id] = $arGoodsSections[$id];
		}
	}
}
?>