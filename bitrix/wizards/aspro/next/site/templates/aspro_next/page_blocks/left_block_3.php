<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<? global $arTheme, $APPLICATION;?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/left_block/menu.left_menu.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"HIDE_CATALOG" => "Y",
		"EDIT_TEMPLATE" => "include_area.php"
	),
	false
);?>

<?$APPLICATION->ShowViewContent('left_menu');?>
<?$APPLICATION->ShowViewContent('under_sidebar_content');?>

<?CNext::get_banners_position('SIDE', 'Y');?>