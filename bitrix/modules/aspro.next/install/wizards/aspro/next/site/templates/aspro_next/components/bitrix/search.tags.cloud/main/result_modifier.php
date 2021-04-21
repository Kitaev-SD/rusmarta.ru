<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
if (is_array($arResult['SEARCH']))
{
	if($arParams['TAGS_ELEMENT'])
	{
		$arAllTags = array();
		foreach($arParams['TAGS_ELEMENT'] as $key => $arTags)
		{
			foreach($arTags as $key => $tag)
			{
				$tag = ltrim($tag);
				$arAllTags[$tag] = $tag;
			}
		}
		foreach ($arResult['SEARCH'] as $key => $arItem)
		{
			if(!$arAllTags[$arItem['NAME']])
				unset($arResult['SEARCH'][$key]);
		}
	}
	else
	{
		unset($arResult['SEARCH']);
	}
}?>