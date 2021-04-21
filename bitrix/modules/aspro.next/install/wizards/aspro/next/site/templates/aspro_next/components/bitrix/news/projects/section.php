<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
// geting section items count and section [ID, NAME]
$arItemFilter = CNext::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
$arSectionFilter = CNext::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);

if($arParams['CACHE_GROUPS'] == 'Y')
{
	$arSectionFilter['CHECK_PERMISSIONS'] = 'Y';
	$arSectionFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$arSection = CNextCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE'), true);
CNext::AddMeta(
	array(
		'og:description' => $arSection['DESCRIPTION'],
		'og:image' => (($arSection['PICTURE'] || $arSection['DETAIL_PICTURE']) ? CFile::GetPath(($arSection['PICTURE'] ? $arSection['PICTURE'] : $arSection['DETAIL_PICTURE'])) : false),
	)
);

$bFoundSection = false;
$arYears = array();

if($arSection)
{
	$bFoundSection = true;
	$itemsCnt = CNextCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
}

global $arTheme;
if($arTheme['PROJECTS_PAGE']['VALUE'] == 'list_elements_3' || $arParams["SECTION_ELEMENTS_TYPE_VIEW"] == 'list_elements_3')
{
	$arYears = CNext::GetItemsYear($arParams);
	if($arYears)
	{
		$current_year = current($arResult['VARIABLES']);
		if($current_year && $arYears[$current_year])
		{
			$bFoundSection = true;
			$GLOBALS[$arParams["FILTER_NAME"]] = array(
				">DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".$current_year, "DD.MM.YYYY"),
				"<=DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".(intval($current_year)+1), "DD.MM.YYYY"),
			);
			$title_news = GetMessage('CURRENT_PROJECTS', array('#YEAR#' => $current_year));
		}
		$itemsCnt = 1;
	}
}?>

<?if(!$bFoundSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif(!$bFoundSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CNext::goto404Page();?>
<?else:?>

	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CNext::ShowRSSIcon(CComponentEngine::makePathFromTemplate($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss_section'], array_map('urlencode', $arResult['VARIABLES'])));
	}?>
	<?if(!$itemsCnt):?>
		<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
	<?endif;?>
	
	<?$sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["PROJECTS_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
	<?@include_once('page_blocks/'.$sViewElementsTemplate.'.php');?>
<?endif;?>
<?if($arYears && $bFoundSection)
{			
	$APPLICATION->SetTitle($title_news);
	$APPLICATION->AddChainItem($title_news);
}?>