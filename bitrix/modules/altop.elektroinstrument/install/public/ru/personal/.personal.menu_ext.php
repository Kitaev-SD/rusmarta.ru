<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinks = Array(
	Array(
		"������� ������",
		"/personal/orders/",
		Array(),
		Array(),
		""
	),
	Array(
		"��� �������",
		"/personal/cart/",
		Array(),
		Array(),
		""
	),
	Array(
		"���������� ������",
		"/personal/cart/?delay=Y",
		Array(),
		Array(),
		""
	),
	Array(
		"��������� ������",
		"/personal/subscribe/",
		Array(),
		Array(),
		""
	),
	Array(
		"����� �������",
		"/personal/orders/?filter_history=Y",
		Array(),
		Array(),
		""
	),
	Array(
		"������ ������",
		"/personal/private/",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"������ ����",
		"/personal/account/",
		Array(), 
		Array(),
		"CBXFeatures::IsFeatureEnabled('SaleAccounts')"
	),
	Array(
		"������� �������",
		"/personal/profiles/",
		Array(),
		Array(),
		""
	),
	Array(
		"Email ��������",
		"/personal/mailings/",
		Array(),
		Array(),
		""
	)
);?>