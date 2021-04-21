<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinks = Array(
	Array(
		"Текущие заказы",
		"#SITE_DIR#personal/orders/",
		Array(),
		Array(),
		""
	),
	Array(
		"Моя корзина",
		"#SITE_DIR#personal/cart/",
		Array(),
		Array(),
		""
	),
	Array(
		"Отложенные товары",
		"#SITE_DIR#personal/cart/?delay=Y",
		Array(),
		Array(),
		""
	),
	Array(
		"Ожидаемые товары",
		"#SITE_DIR#personal/subscribe/",
		Array(),
		Array(),
		""
	),
	Array(
		"Архив заказов",
		"#SITE_DIR#personal/orders/?filter_history=Y",
		Array(),
		Array(),
		""
	),
	Array(
		"Личные данные",
		"#SITE_DIR#personal/private/",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Личный счет",
		"#SITE_DIR#personal/account/",
		Array(), 
		Array(),
		"CBXFeatures::IsFeatureEnabled('SaleAccounts')"
	),
	Array(
		"Профили заказов",
		"#SITE_DIR#personal/profiles/",
		Array(),
		Array(),
		""
	),
	Array(
		"Email рассылки",
		"#SITE_DIR#personal/mailings/",
		Array(),
		Array(),
		""
	)
);?>