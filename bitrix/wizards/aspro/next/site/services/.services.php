<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"public.php",
			"template.php",
			"theme.php",
			"menu.php",
			"settings.php",
		),
	),
	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php",
			"banner_types.php",
			"regions.php",
			"banners.php",
			"adv_content.php",
			"bg_images.php",
			"banners_inner.php",
			"tizers.php",
			"references.php",
			"references_color.php",
			"references_tizer.php",
			"references_contact.php",
			"staff.php",
			"faq.php",
			"shops.php",
			"vacancy.php",
			"brands.php",
			"licenses.php",
			"services.php",
			"news.php",
			"projects.php",
			"catalog.php",
			"articles.php",
			"stock.php",
			"sku.php",
			"landing_in_catalog.php",
			"landing_in_page.php",
			"landing_in_search.php",
			"errors_updates.php",
		),
	),
	"form" => array(
		"NAME" => GetMessage("SERVICE_FORM_DEMO_DATA"),
		"STAGES" => array(
			"toorder.php",
			"ask.php",
			"feedback.php",
			"resume.php",
			"services.php",
			"callback.php",
			"projects.php",
			"cheaper.php",
			"one_click_buy.php",
			"send_gift.php",
			"sms.php",
			"errors_updates.php",
		)
	),
	"sale" => Array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => Array(
			"locations.php",
			"step1.php",
			"step2.php",
			"step3.php"
		),
	),
	"forum" => Array(
		"NAME" => GetMessage("SERVICE_FORUM")
	),
	/*"search" => array(
		"NAME" => GetMessage("SERVICE_SEARCH"),
		"STAGES" => array(
			"search.php",
		),
	),*/
);
?>