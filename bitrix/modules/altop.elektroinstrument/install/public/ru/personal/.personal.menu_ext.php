<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinks = Array(
	Array(
		"Текущие заказы",
		"/personal/orders/",
		Array(),
		Array(),
		""
	),
	Array(
		"Моя корзина",
		"/personal/cart/",
		Array(),
		Array(),
		""
	),
	Array(
		"Отложенные товары",
		"/personal/cart/?delay=Y",
		Array(),
		Array(),
		""
	),
	Array(
		"Ожидаемые товары",
		"/personal/subscribe/",
		Array(),
		Array(),
		""
	),
	Array(
		"Архив заказов",
		"/personal/orders/?filter_history=Y",
		Array(),
		Array(),
		""
	),
	Array(
		"Личные данные",
		"/personal/private/",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Личный счет",
		"/personal/account/",
		Array(), 
		Array(),
		"CBXFeatures::IsFeatureEnabled('SaleAccounts')"
	),
	Array(
		"Профили заказов",
		"/personal/profiles/",
		Array(),
		Array(),
		""
	),
	Array(
		"Email рассылки",
		"/personal/mailings/",
		Array(),
		Array(),
		""
	)
);?>