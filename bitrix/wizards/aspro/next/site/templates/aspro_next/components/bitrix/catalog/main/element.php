<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager;

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");

global $arTheme, $NextSectionID, $arRegion;
$arSection = $arElement = array();
$bFastViewMode = (isset($_REQUEST['FAST_VIEW']) && $_REQUEST['FAST_VIEW'] == 'Y');

// get current section & element
if($arResult["VARIABLES"]["SECTION_ID"] > 0){
	$arSection = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arResult["VARIABLES"]["SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN", "UF_OFFERS_TYPE", "UF_ELEMENT_DETAIL"));
}
elseif(strlen(trim($arResult["VARIABLES"]["SECTION_CODE"])) > 0){
	$arSection = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN", "UF_OFFERS_TYPE", "UF_ELEMENT_DETAIL"));
}

if($arResult["VARIABLES"]["ELEMENT_ID"] > 0){
	$arElementFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => $arResult["VARIABLES"]["ELEMENT_ID"]);
}
elseif(strlen(trim($arResult["VARIABLES"]["ELEMENT_CODE"])) > 0){
	$arElementFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "=CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"]);
}

if($arParams['SHOW_DEACTIVATED'] !== 'Y'){
	$arElementFilter['ACTIVE'] = 'Y';
}

if($GLOBALS[$arParams['FILTER_NAME']]){
	$arElementFilter = array_merge($arElementFilter, $GLOBALS[$arParams['FILTER_NAME']]);
}

$arElement = CNextCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), CNext::makeElementFilterInRegion($arElementFilter), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_ASSOCIATED_FILTER", "PROPERTY_EXPANDABLES_FILTER"));

if(!$arElement){
	\Bitrix\Iblock\Component\Tools::process404(
		""
		,($arParams["SET_STATUS_404"] === "Y")
		,($arParams["SET_STATUS_404"] === "Y")
		,($arParams["SHOW_404"] === "Y")
		,$arParams["FILE_404"]
	);
}

if($arParams['STORES'])
{
	foreach($arParams['STORES'] as $key => $store)
	{
		if(!$store)
			unset($arParams['STORES'][$key]);
	}
}
if(!$arSection){
	if($arElement["IBLOCK_SECTION_ID"]){
		$sid = ((isset($arElement["IBLOCK_SECTION_ID_SELECTED"]) && $arElement["IBLOCK_SECTION_ID_SELECTED"]) ? $arElement["IBLOCK_SECTION_ID_SELECTED"] : $arElement["IBLOCK_SECTION_ID"]);
		$arSection = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $sid, "IBLOCK_ID" => $arElement["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN", "UF_OFFERS_TYPE"));
	}
}

$typeSKU = '';
//set offer view type
$typeTmpSKU = 0;
if($arSection['UF_OFFERS_TYPE'])
	$typeTmpSKU = $arSection['UF_OFFERS_TYPE'];
else
{
	if($arSection["DEPTH_LEVEL"] > 2)
	{
		$arSectionParent = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arSection["IBLOCK_SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_OFFERS_TYPE"));
		if($arSectionParent['UF_OFFERS_TYPE'] && !$typeTmpSKU)
			$typeTmpSKU = $arSectionParent['UF_OFFERS_TYPE'];

		if(!$typeTmpSKU)
		{
			$arSectionRoot = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_OFFERS_TYPE"));
			if($arSectionRoot['UF_OFFERS_TYPE'] && !$typeTmpSKU)
				$typeTmpSKU = $arSectionRoot['UF_OFFERS_TYPE'];
		}
	}
	else
	{
		$arSectionRoot = CNextCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_OFFERS_TYPE"));
		if($arSectionRoot['UF_OFFERS_TYPE'] && !$typeTmpSKU)
			$typeTmpSKU = $arSectionRoot['UF_OFFERS_TYPE'];
	}
}
if($typeTmpSKU)
{
	$rsTypes = CUserFieldEnum::GetList(array(), array("ID" => $typeTmpSKU));
	if($arType = $rsTypes->GetNext())
		$typeSKU = $arType['XML_ID'];

}

if($arRegion)
{
	if($arRegion['LIST_PRICES'])
	{
		if(reset($arRegion['LIST_PRICES']) != 'component')
			$arParams['PRICE_CODE'] = array_keys($arRegion['LIST_PRICES']);
	}
	if($arRegion['LIST_STORES'])
	{
		if(reset($arRegion['LIST_STORES']) != 'component')
			$arParams['STORES'] = $arRegion['LIST_STORES'];
	}
}

$NextSectionID = $arSection["ID"];
$arParams["GRUPPER_PROPS"] = $arTheme["GRUPPER_PROPS"]["VALUE"];
if($arTheme["GRUPPER_PROPS"]["VALUE"] != "NOT")
{
	$arParams["PROPERTIES_DISPLAY_TYPE"] = "TABLE";

	if($arParams["GRUPPER_PROPS"] == "GRUPPER" && !\Bitrix\Main\Loader::includeModule("redsign.grupper"))
		$arParams["GRUPPER_PROPS"] = "NOT";
	if($arParams["GRUPPER_PROPS"] == "WEBDEBUG" && !\Bitrix\Main\Loader::includeModule("webdebug.utilities"))
		$arParams["GRUPPER_PROPS"] = "NOT";
	if($arParams["GRUPPER_PROPS"] == "YENISITE_GRUPPER" && !\Bitrix\Main\Loader::includeModule("yenisite.infoblockpropsplus"))
		$arParams["GRUPPER_PROPS"] = "NOT";
}

/* hide compare link from module options */
if(CNext::GetFrontParametrValue('CATALOG_COMPARE') == 'N')
	$arParams["USE_COMPARE"] = 'N';
/**/

if($bFastViewMode)
	include_once('element_fast_view.php');
else
	include_once('element_normal.php');
?>