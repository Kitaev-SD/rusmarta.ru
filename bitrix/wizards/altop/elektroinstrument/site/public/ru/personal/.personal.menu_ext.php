<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinks = Array(
	Array(
		"������� ������",
		"#SITE_DIR#personal/orders/",
		Array(),
		Array(),
		""
	),
	Array(
		"��� �������",
		"#SITE_DIR#personal/cart/",
		Array(),
		Array(),
		""
	),
	Array(
		"���������� ������",
		"#SITE_DIR#personal/cart/?delay=Y",
		Array(),
		Array(),
		""
	),
	Array(
		"��������� ������",
		"#SITE_DIR#personal/subscribe/",
		Array(),
		Array(),
		""
	),
	Array(
		"����� �������",
		"#SITE_DIR#personal/orders/?filter_history=Y",
		Array(),
		Array(),
		""
	),
	Array(
		"������ ������",
		"#SITE_DIR#personal/private/",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"������ ����",
		"#SITE_DIR#personal/account/",
		Array(), 
		Array(),
		"CBXFeatures::IsFeatureEnabled('SaleAccounts')"
	),
	Array(
		"������� �������",
		"#SITE_DIR#personal/profiles/",
		Array(),
		Array(),
		""
	),
	Array(
		"Email ��������",
		"#SITE_DIR#personal/mailings/",
		Array(),
		Array(),
		""
	)
);?>