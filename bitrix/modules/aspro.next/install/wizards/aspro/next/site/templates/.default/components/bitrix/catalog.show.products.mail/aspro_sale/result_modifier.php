<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

if (!empty($arResult['ITEMS']))
{
	$arFilter = array(
		"ACTIVE" => "Y",
		"ID" => $arParams['LIST_ITEM_ID']
	);
	$arSelect = array('ID', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE');
	$rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while($arItem = $rsItems->Fetch())
	{
		if($arItem['PREVIEW_TEXT'])
			$arResult['ITEMS'][$arItem['ID']]['PREVIEW_TEXT'] = $arItem['PREVIEW_TEXT'];
	}
	
}
?>