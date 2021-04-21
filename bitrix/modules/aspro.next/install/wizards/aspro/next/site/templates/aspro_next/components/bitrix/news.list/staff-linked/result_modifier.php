<?
if($arResult['ITEMS']){
	foreach($arResult['ITEMS'] as $key => $arItem){
		CNext::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));

		if($arItem['PROPERTIES'])
		{
			foreach($arItem['PROPERTIES'] as $key2 => $arProp)
			{
				if(($key2 == 'EMAIL' || $key2 == 'PHONE') && $arProp['VALUE'])
					$arItem['MIDDLE_PROPS'][] = $arProp;
				if(strpos($key2, 'SOCIAL') !== false && $arProp['VALUE'])
				{
					if($arItem['DISPLAY_PROPERTIES'][$key2])
						unset($arItem['DISPLAY_PROPERTIES'][$key2]);
					$arResult['ITEMS'][$key]['SOCIAL_PROPS'][] = $arProp;
				}
			}
		}
	}
}
?>