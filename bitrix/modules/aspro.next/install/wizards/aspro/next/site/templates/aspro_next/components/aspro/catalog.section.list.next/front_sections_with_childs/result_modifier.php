<?
$arRootItems = $arChildItems = array();
foreach($arResult['SECTIONS'] as $key => $arSection)
{
	if($arSection['DEPTH_LEVEL'] == 1)
		$arRootItems[$arSection['ID']] = $arSection;
	else
		$arChildItems[$arSection['ID']] = $arSection;
	unset($arResult['SECTIONS'][$key]);
}
if($arChildItems)
{
	foreach($arChildItems as $key => $arSection)
	{
		$arRootSection = CNextCache::CIBlockSection_GetList(array('CACHE' => array('MULTI' =>'N', 'TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), array('GLOBAL_ACTIVE' => 'Y', '<=LEFT_BORDER' => $arSection['LEFT_MARGIN'], '>=RIGHT_BORDER' => $arSection['RIGHT_MARGIN'], 'DEPTH_LEVEL' => 1, 'IBLOCK_ID' => $arParams['IBLOCK_ID']), false, array('ID', 'NAME', 'SORT', 'SECTION_PAGE_URL', 'PICTURE'));
		if(!isset($arRootItems[$arRootSection['ID']]))
			$arRootItems[$arRootSection['ID']] = $arRootSection;
	}
}
\Bitrix\Main\Type\Collection::sortByColumn($arRootItems, array('SORT' => array(SORT_NUMERIC, SORT_ASC), 'ID' => array(SORT_NUMERIC, SORT_ASC)));
foreach($arRootItems as $key => $arSection)
{
	$arSections = CNextCache::CIBlockSection_GetList(array('SORT' => 'ASC', 'ID' => 'ASC', 'CACHE' => array('MULTI' =>'Y', 'TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), array('GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => $arSection['ID'], 'DEPTH_LEVEL' => 2, 'IBLOCK_ID' => $arParams['IBLOCK_ID']), $arParams['COUNT_ELEMENTS'], array('ID', 'NAME', 'SORT', 'SECTION_PAGE_URL'));
	$arRootItems[$key]['ITEMS'] = $arSections;
}
$arResult['SECTIONS'] = $arRootItems;
?>