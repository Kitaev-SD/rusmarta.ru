<?
if($arResult['ITEMS'])
{
	$arSectionsIDs = array();
	foreach($arResult['ITEMS'] as $key => $arItem)
	{
		$arResult['ITEMS'][$key]['DETAIL_PAGE_URL'] = CNext::FormatNewsUrl($arItem);
		CNext::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
		if($SID = $arItem['IBLOCK_SECTION_ID'])
		{
			$arSectionsIDs[] = $SID;
		}
		if($arItem['PROPERTIES'])
		{
			foreach($arItem['PROPERTIES'] as $key2 => $arProp)
			{
				if(($key2 == 'EMAIL' || $key2 == 'PHONE') && $arProp['VALUE'])
					$arResult['ITEMS'][$key]['MIDDLE_PROPS'][] = $arProp;
				if(strpos($key2, 'SOCIAL') !== false && $arProp['VALUE'])
					$arResult['ITEMS'][$key]['SOCIAL_PROPS'][] = $arProp;
			}
		}
	}
	if($arSectionsIDs){
		$arResult['SECTIONS'] = CNextCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arSectionsIDs));
	}
}
?>