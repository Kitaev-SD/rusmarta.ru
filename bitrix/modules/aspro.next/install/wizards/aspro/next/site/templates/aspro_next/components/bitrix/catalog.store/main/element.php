<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);

global $arTheme;
if(\Bitrix\Main\Loader::includeModule('catalog'))
{
	$arElement = CCatalogStore::GetList(array('ID' => 'ASC'), array('ID' => $arResult['STORE']), false, false, array("ID"))->Fetch();
}
?>
<?if($arElement):?>		
	<?$bStoreID = $APPLICATION->IncludeComponent(
		"bitrix:catalog.store.detail",
		"main",
		Array(
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"STORE" => $arResult["STORE"],
			"TITLE" => $arParams["TITLE"],
			"PATH_TO_ELEMENT" => $arResult["PATH_TO_ELEMENT"],
			"PATH_TO_LISTSTORES" => $arResult["PATH_TO_LISTSTORES"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"MAP_TYPE" => $arParams["MAP_TYPE"],
			"GOOGLE_API_KEY" => $arParams["GOOGLE_API_KEY"],
		),
		$component
	);?>
<?else:?>
	<?$APPLICATION->ShowViewContent('map_content');?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.detail",
		"shops",
		Array(
			"MAP_TYPE" => 0,
			"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
			"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
			"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
			"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
			"IBLOCK_TYPE" => "aspro_next_content",
			"IBLOCK_ID" => CNextCache::$arIBlocks[SITE_ID]["aspro_next_content"]["aspro_next_shops"][0],
			"FIELD_CODE" => array(
				0 => "NAME",
				1 => "PREVIEW_PICTURE",
				2 => "DETAIL_TEXT",
				3 => "DETAIL_PICTURE",
				4 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "EMAIL",
				1 => "ADDRESS",
				2 => "MAP",
				3 => "METRO",
				4 => "SCHEDULE",
				5 => "PHONE",
				6 => "MORE_PHOTOS",
				7 => "",
			),
			"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
			"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"META_KEYWORDS" => $arParams["META_KEYWORDS"],
			"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
			"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
			"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
			"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"SET_STATUS_404" => "Y",
			"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
			"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
			"ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
			"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
			"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
			"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
			"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
			"CHECK_DATES" => $arParams["CHECK_DATES"],
			"ELEMENT_ID" => $arResult["STORE"],
			"ELEMENT_CODE" => "",
			"IBLOCK_URL" => "",
			"USE_SHARE" 			=> $arParams["USE_SHARE"],
			"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
			"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
			"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
			"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
			"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		),
		$component
	);?>	
<?endif;?>
<?
if ($arParams['SET_TITLE'] == 'Y') {
	$APPLICATION->SetTitle($_SESSION['SHOP_TITLE']);
	$APPLICATION->SetPageProperty("title", $_SESSION['SHOP_TITLE']);
}
$APPLICATION->AddChainItem($_SESSION['SHOP_TITLE'], "");
?>