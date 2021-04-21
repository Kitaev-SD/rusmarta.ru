<?$arResult = CNext::getChilds2($arResult);
global $arRegion, $arTheme;
if($arResult)
{
	foreach($arResult as $key=>$arItem)
	{
		if(isset($arItem['PARAMS']) && $arRegion && $arTheme['USE_REGIONALITY']['VALUE'] === 'Y' && $arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['REGIONALITY_FILTER_ITEM']['VALUE'] === 'Y')
		{
			// filter items by region
			if(isset($arItem['PARAMS']['LINK_REGION']) )
			{
				if($arItem['PARAMS']['LINK_REGION'])
				{
					if(!in_array($arRegion['ID'], $arItem['PARAMS']['LINK_REGION']))
						unset($arResult[$key]);
				}
				else
					unset($arResult[$key]);
			}
		}
		if($arItem["CHILD"])
			$arResult[$key]["CHILD"]=CNext::unique_multidim_array($arItem["CHILD"], "TEXT");
	}
}?>