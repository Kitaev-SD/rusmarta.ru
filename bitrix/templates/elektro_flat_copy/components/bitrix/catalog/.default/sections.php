<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>

<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],		
		"TOP_DEPTH" => 2,
		"SECTION_FIELDS" => array(),
		"SECTION_USER_FIELDS" => array(
			0 => "UF_ICON"
		),
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DISPLAY_IMG_WIDTH" => 50,
		"DISPLAY_IMG_HEIGHT" => 50
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>

<div class="clr"></div>
<div class="catalog-section-descr">	
	<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/catalog_descr.php"), false);?>
</div>