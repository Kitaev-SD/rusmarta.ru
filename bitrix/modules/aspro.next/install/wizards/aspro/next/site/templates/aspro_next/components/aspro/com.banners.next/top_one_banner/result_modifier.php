<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?
if($arResult['ITEMS'])
{
	$arTmpItems = array();
	foreach($arResult['ITEMS'] as $key => $arItem)
	{
		$arTmpItems[$arItem['TYPE_BANNER']]['ITEMS'][] = $arItem;
	}
	if($arParams['BANNER_TYPE_THEME'] && $arTmpItems[$arParams['BANNER_TYPE_THEME']])
		$arResult['HAS_SLIDE_BANNERS'] = true;
	if($arParams['BANNER_TYPE_THEME_CHILD'] && $arTmpItems[$arParams['BANNER_TYPE_THEME_CHILD']])
		$arResult['HAS_CHILD_BANNERS'] = true;
	$arResult['ITEMS'] = $arTmpItems;

}?>
