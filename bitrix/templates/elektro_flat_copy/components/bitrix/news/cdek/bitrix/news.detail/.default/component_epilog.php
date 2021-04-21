<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult["PROPERTIES"]["LINKED"]["VALUE"])):
	global $arLinkPrFilter;
	$arLinkPrFilter = array(
		"ID" => $arResult["PROPERTIES"]["LINKED"]["VALUE"]
	);?>
	<div class="news-detail__products">
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/linked.php"
			),
			false,
			array("HIDE_ICONS" => "Y")
		);?>
	</div>
<?endif;?>