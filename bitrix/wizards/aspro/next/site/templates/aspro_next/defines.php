<?
global $is404, $isIndex, $isForm, $isWidePage, $isBlog, $isHideLeftBlock, $isShowTopAdvBottomBanner, $isShowFloatBanner, $isShowTizers, $isShowSale,
$isShowBottomBanner, $isShowCompany, $isShowBrands, $isShowCatalogSections, $isShowCatalogElements, $isShowIndexLeftBlock, $isShowMiddleAdvBottomBanner, $isShowBlog,
$bInstagrammIndex;
$is404 = (defined("ERROR_404") && ERROR_404 === "Y");
$isIndex = CNext::IsMainPage();
$isForm = CNext::IsFormPage();
$isBlog = (CSite::inDir(SITE_DIR.'blog/') || $APPLICATION->GetProperty("BLOG_PAGE") == "Y");
$isWidePage = ($APPLICATION->GetProperty("WIDE_PAGE") == "Y");
$isHideLeftBlock = ($APPLICATION->GetProperty("HIDE_LEFT_BLOCK") == "Y");

$indexType = $arTheme["INDEX_TYPE"]["VALUE"];
$isShowIndexLeftBlock = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["WITH_LEFT_BLOCK"]["VALUE"] == "Y");
$isShowTopAdvBottomBanner = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["TOP_ADV_BOTTOM_BANNER"]["VALUE"] != "N");
$isShowMiddleAdvBottomBanner = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["MIDDLE_ADV"]["VALUE"] != "N");
$isShowFloatBanner = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["FLOAT_BANNER"]["VALUE"] != "N");
$isShowTizers = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["TIZERS"]["VALUE"] != "N");
$isShowSale = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["SALE"]["VALUE"] != "N");
$isShowBottomBanner = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["BOTTOM_BANNERS"]["VALUE"] != "N");
$isShowCompany = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["COMPANY_TEXT"]["VALUE"] != "N");
$isShowBrands = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["BRANDS"]["VALUE"] != "N");
$isShowCatalogSections = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["CATALOG_SECTIONS"]["VALUE"] != "N");
$isShowCatalogElements = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["CATALOG_TAB"]["VALUE"] != "N");
$isShowBlog = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["BLOG"]["VALUE"] != "N");
$isShowBlog = ($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["BLOG"]["VALUE"] != "N");
$bInstagrammIndex = (isset($arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["INSTAGRAMM"]["VALUE"]) ? $arTheme["INDEX_TYPE"]["SUB_PARAMS"][$indexType]["INSTAGRAMM"]["VALUE"] != "N" : true);

global $arRegion;
if($isIndex)
{
	$GLOBALS['arRegionLinkFront'] = array('PROPERTY_SHOW_ON_INDEX_PAGE_VALUE' => 'Y');
}

if($arRegion && $arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['REGIONALITY_FILTER_ITEM']['VALUE'] == 'Y')
{
	$GLOBALS['arRegionLink'] = array('PROPERTY_LINK_REGION' => $arRegion['ID']);
	if($isIndex)
	{
		$GLOBALS['arRegionLinkFront']['PROPERTY_LINK_REGION'] = $arRegion['ID'];
	}
}
?>