<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = array(
	"main" => array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => array(
			"files.php", //Copy bitrix files
			"search.php", //Indexing files
			"template.php", //Install template
			"menu.php", //Install menu
			"settings.php"
		)
	),
	"catalog" => Array(
		"NAME" => GetMessage("SERVICE_CATALOG_SETTINGS"),
		"STAGES" => Array(
			"index.php"
		)
	),
	"iblock" => array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => array(
			"types.php", //Iblock types			
			"callback.php",
			"under_order.php",
			"ask_price.php",
			"cheaper.php",
			"banners_main.php",
			"banners_left.php",
			"contacts.php",
			"advantages.php",
			"join_us.php",
			"slider.php",
			"payments.php",
			"payments_icons.php",
			"gifts.php",
			"vendors.php",
			"colors.php",
			"catalog.php", //Catalog iblock import
			"catalog2.php", //Offers iblock import
			"catalog3.php",
			"comments.php",
			"promotions.php",
			"news.php",
			"reviews.php"
		)
	),
	"sale" => array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => array(
			"locations.php",
			"step1.php",
			"step2.php",
			"step3.php"
		)
	)
);?>