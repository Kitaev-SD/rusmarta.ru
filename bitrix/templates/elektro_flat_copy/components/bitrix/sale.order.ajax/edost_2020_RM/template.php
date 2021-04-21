<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$edost_locations = (!empty($arResult['edost']['locations_installed']) ? true : false);

$APPLICATION->SetAdditionalCSS($templateFolder.'/style.css');

if (!$edost_locations) $APPLICATION->AddHeadScript($templateFolder.'/location.js'); // ����������� ����� �������������� ��������

CJSCore::Init(array('fx', 'popup', 'window', 'ajax'));

// ������������ ������� ��� �������� �������� ����� � �������� ������ (�� ��� ���� ���� �������� ���������� ������ ����� ����������� ������!!!)
//$APPLICATION->AddHeadString('<script type="text/javascript" src="https://api-maps.yandex.ru/2.0-stable/?load=package.standard,package.clusters&lang=ru-RU"></script>');

if (empty($arParams['STYLE']) || !in_array($arParams['STYLE'], array('light', 'bright'))) $arParams['STYLE'] = 'bright';
if (empty($arParams['CART'])) $arParams['CART'] = 'compact';
if (empty($arParams['COMPACT_PREPAY_JOIN'])) $arParams['COMPACT_PREPAY_JOIN'] = 'Y';
if (empty($arParams['COMPACT_CART_SHOW_IMG'])) $arParams['COMPACT_CART_SHOW_IMG'] = 'Y';
if (empty($arParams['CART_SHOW_PROPS'])) $arParams['CART_SHOW_PROPS'] = 'N';
if (empty($arParams['ORDER_FORMAT'])) $arParams['ORDER_FORMAT'] = 'progress';
if (empty($arParams['BORDER_COLOR'])) $arParams['BORDER_COLOR'] = 'Y';
if (empty($arParams['FONT_BIG'])) $arParams['FONT_BIG'] = 'Y';
if (empty($arParams['COD_LIGHT'])) $arParams['COD_LIGHT'] = 'N';
if (empty($arParams['ACTIVE_LIGHT'])) $arParams['ACTIVE_LIGHT'] = 'Y';
if (empty($arParams['ACTIVE_LIGHT2'])) $arParams['ACTIVE_LIGHT2'] = 'Y';
if (empty($arParams['FAST'])) $arParams['FAST'] = 'full';
if (empty($arParams['FAST_INFO'])) $arParams['FAST_INFO'] = 'Y';
if (empty($arParams['POLICY'])) $arParams['POLICY'] = 'bitrix';
if (empty($arParams['DISCOUNT_SAVING'])) $arParams['DISCOUNT_SAVING'] = 'N';

$edost_catalogdelivery = false;

$data = (isset($arResult['edost']) ? $arResult['edost'] : false);
$compact = (!empty($data['format']['compact']) ? $data['format']['compact'] : '');
$arResult['edost_compact'] = $compact;
$format = (isset($data['format']) ? $data['format'] : false);
$format_active = (!empty($arResult['edost']['format']['active']) ? $arResult['edost']['format']['active'] : false);
$company_ico = (isset($format['company_ico']) ? $format['company_ico'] : 'T');
$priority = (isset($format['priority']) ? $format['priority'] : 'P');
$cod_tariff = (!empty($data['format']['cod_tariff']) ? true : false);

$sign = GetMessage('EDOST_DELIVERY_SIGN');
$active_tariff = false;

$no_office = (!empty($format['active']['profile']) && in_array($format['active']['profile'], array('shop', 'office', 'terminal')) ? true : false);
$no_delivery = (empty($format['active']['id']) ? true : false);
//$no_delivery_tariff = (empty($arResult['DELIVERY']) ? true : false);
$no_delivery_tariff = (empty($format['count']) ? true : false);
$save_button = ($no_office || $no_delivery || $no_delivery_tariff ? false : true);

$delivery_location = (!empty($arResult['USER_VALS']['DELIVERY_LOCATION']) ? $arResult['USER_VALS']['DELIVERY_LOCATION'] : false);

$delivery_id = $arResult['USER_VALS']['DELIVERY_ID'];
$warning = (!empty($arResult['edost']['format']['warning']) || isset($arResult['edost']['warning']) ? true : false);
$address_hide = (!empty($arResult['edost']['address_hide']) || empty($delivery_id) ? true : false); // ������������ ��� �������� ������ � ��������� �� ������ ������ ������ edost.delivery (�� ������!!!)
$address_id = (empty($arResult['edost']['locations_installed']) && isset($arResult['edost']['address_id']) ? $arResult['edost']['address_id'] : 0);


// ������� ������������ (��������� ������/���������� � ����������� �� ������� �����)
$template_param_site = array(
	'phone_width' => '', // ������ ���� �������� ��� ������� ���� ��������� � ����� ��������
	'header_query' => (!empty($arParams['MENU']) && $arParams['MENU'] == 'Y' ? $arParams['MENU_QUERY'] : ''), // 'query' ��� ������ ���� ��������, ������������� � ��������� (��� �������� ����������� ����� �����)
	'header_height' => (!empty($arParams['MENU']) && $arParams['MENU'] == 'Y' ? intval($arParams['MENU_HEIGHT']) : ''), // ������������� ������ ���� ��������, ������������� � ��������� (��� �������� ����������� ����� �����)
	'header_width' => (!empty($arParams['MENU']) && $arParams['MENU'] == 'Y' ? intval($arParams['MENU_WIDTH']) : ''), // ������ ������ �� �������� ��������� ������ (��� �������� ����������� ����� �����)
);

if (isset($arResult['edost']['format'])) {
	$resize = array(
		'auth' => array('id', 'order_auth', 'edost_auth_normal', 700, 'edost_auth_small'),
		'ico_row' => array('ico_row', 'edost_resize_ico', '1', 550, '1', 450, '1'),
		'cart_inside' => array('id', 'ORDER_FORM', 'edost_cart_normal', 480, 'edost_cart_small'),
		'props' => array('id', 'ORDER_FORM', 'edost_props_normal', 520, 'edost_props_small', 420, 'edost_props_small2'),
		'ico2' => array('class', 'edost_resize_ico', 'edost_compact_normal', 380, 'edost_compact_small2'),
		'bookmark_cod' => array('id', 'edost_delivery_div', 'edost_bookmark_cod_normal', 350, 'edost_bookmark_cod_small'),
		'delimiter' => array('class', 'edost_main', 'edost_delimiter_normal', 680, 'edost_delimiter_small'),
		'bookmark' => array('id', 'edost_delivery_div', 'edost_bookmark_normal', 700, 'edost_bookmark_small'),
		'map' => array('id', 'edost_delivery_div', 'edost_map_normal', 500, 'edost_map_hide'),
		'payment_window' => array('id-window', 'edost_window', 'edost_window_payment_normal', 500, 'edost_window_payment_small'),
		'delivery_window' => array('id-window', 'edost_window', 'edost_window_delivery_normal', 500, 'edost_window_delivery_small', 440, 'edost_window_delivery_small2'),
		'head' => array('id', 'ORDER_FORM', 'edost_compact_head_normal', 400, 'edost_compact_head_small', 320, 'edost_compact_head_small2'),
	);
	if ($arParams['FONT_BIG'] == 'Y') {
		if ($compact != '') $resize += array(
			'delivery' => array('id', 'ORDER_FORM', 'edost_compact_delivery_normal', 600, 'edost_compact_delivery_normal2', 540, 'edost_compact_delivery_small', 420, 'edost_compact_delivery_small2'),
		);
		else $resize += array(
			'delivery' => array('id', 'ORDER_FORM', 'edost_full_delivery_normal', 580, 'edost_full_delivery_normal2', 500, 'edost_full_delivery_small', 380, 'edost_full_delivery_small2'),
		);
	}
	else {
		if ($compact != '') $resize += array(
			'delivery' => array('id', 'ORDER_FORM', 'edost_compact_delivery_normal', 520, 'edost_compact_delivery_normal2', 470, 'edost_compact_delivery_small', 320, 'edost_compact_delivery_small2'),
		);
		else $resize += array(
			'delivery' => array('id', 'ORDER_FORM', 'edost_full_delivery_normal', 500, 'edost_full_delivery_normal2', 420, 'edost_full_delivery_small', 320, 'edost_full_delivery_small2'),
		);
	}
}
else $resize = array(
	'section' => array('class', 'bx_section', 'visual_normal', 600, 'visual_small'),
	'ico_row' => array('ico_row', 'visual_ico', '2', 600, '1'),
);

$s = (isset($_POST['edost_template_2019']) ? substr($_POST['edost_template_2019'], 0, 100) : '');
$s = explode('|', $s);
$template_param = array(
	'mode' => (empty($s[0]) && in_array($s[0], array('full', 'small', 'off')) ? $s[0] : 'off'),
	'width' => (isset($s[1]) ? intval($s[1]) : 0),
	'width2' => (isset($s[2]) ? intval($s[2]) : 0),
	'fixed' => (isset($s[3]) ? intval($s[3]) : 0),
	'top' => (isset($s[4]) ? intval($s[4]) : 0),
);

$template_width = 0;
$key = ($edost_catalogdelivery ? 'catalogdelivery' : 'order');
if (isset($_POST['edost_template_width'])) {
	$template_width = intval($_POST['edost_template_width']);
	$_SESSION['EDOST']['template_width'][$key] = $template_width;
}
else if (isset($_SESSION['EDOST']['template_width'][$key])) $template_width = $_SESSION['EDOST']['template_width'][$key];

$s = array();
foreach ($resize as $k => $v) {
	$s[] = implode(':', $v);
	$c = '';
	foreach ($v as $k2 => $v2) if ($k2 > 1)
		if (!($k2%2)) $c = $v2;
		else if (empty($template_width) || $v2 < $template_width) break;
	$c = explode(',', $c);
	if ($c[0] != '' && $k != 'ico_row') $c[0] = ' '.$c[0];
	$resize[$k] = (count($c) > 1 ? $c : $c[0]);
}
$template_data = implode('|', $s);


if (!function_exists('getColumnName')) {
	function getColumnName($arHeader) {
		return (strlen($arHeader['name']) > 0) ? $arHeader['name'] : GetMessage('SALE_'.$arHeader['id']);
	}
}

// ��������� �������
if (!function_exists('RGBlight')) {
	function RGBlight($c, $n, $mix = 0, $mix_color = 255) {
		$s = '';
		foreach ($c as $v) {
			$v = $v*(1-$mix) + $mix_color*$mix;
			$v = $v + $n;
			if ($v < 0) $v = 0; else if ($v > 255) $v = 255;
			$v = round($v);
			$s .= ($v < 16 ? '0' : '').dechex($v);
		}
		return $s;
	}
}


$s = (!empty($arParams['BORDER_RADIUS']) ? preg_replace("/[^0-9]/i", "", $arParams['BORDER_RADIUS']) : '');
$border_radius = (!empty($s) ? $s : 0);

$v = (!empty($arParams['COLOR']) ? $arParams['COLOR'] : 'blue');
$ar = array('blue' => '27b', 'blue_light' => '1186D3', 'turquoise' => '009DB5', 'turquoise_dark' => '007475', 'green' => '00A036', 'lime' => 'D1F809', 'oak' => 'BF6F17', 'yellow' => 'ffe94c', 'orange' => 'F56800', 'carrot' => 'F13300', 'raspberries' => 'F2255D', 'violet' => 'E905A5', 'purple' => '9B029B');

if (isset($ar[$v])) $v = $ar[$v];
else {
	$v = (!empty($arParams['COLOR_MANUAL']) ? preg_replace("/[^0-9a-fA-F]/i", "", $arParams['COLOR_MANUAL']) : '');
	if (!(strlen($v) == 3 || strlen($v) == 6)) $v = $ar['blue'];
}
$color = $v;

$n = strlen($v);
if ($n == 3) $c = array(hexdec($v{0}.$v{0}), hexdec($v{1}.$v{1}), hexdec($v{2}.$v{2}));
else if ($n == 6) $c = array(hexdec($v{0}.$v{1}), hexdec($v{2}.$v{3}), hexdec($v{4}.$v{5}));

$light = ceil(0.3*$c[0] + 0.59*$c[1] + 0.11*$c[2]);

$color_person = RGBlight($c, 0, 0.95, 250);
$color_fon = 'FFF';
$color_ico = 'DDD';
$color_head = 'F5F5F5';

if ($light < 170) {
	if ($arParams['STYLE'] == 'bright') {
		$color_light = RGBlight($c, 0, 0.5);
		$color_active_light2 = RGBlight($c, 0, 0.95, 252);
	}
	else {
		$color_light = RGBlight($c, 0, 0.5);
		$color_active_light2 = RGBlight($c, 0, 0.95, 250);
	}

	$color_active = RGBlight($c, 0, 0.1);
	$color_active_hover = RGBlight($c, 0, 0.2);
	$color_active2 = RGBlight($c, 0, 0.9);

	$color_total = $color_active_tariff = $color;

	$color_border_big = RGBlight($c, 0, 0.9, 240);

	$color_button_span = 'FFF';
	$color_button_hover = $color_active;
	$color_button2_hover = RGBlight($c, 0, 0.95);

	$color_input = RGBlight($c, 0, 0.96);
	$color_input_border = $color_active_tariff;
	$color_active_light2_border = $color_light;
}
else {
	if ($arParams['STYLE'] == 'bright') {
		$color_light = RGBlight($c, 50, 0.8, 150);
	}
	else {
		$color_light = RGBlight($c, 0, 0.5, 150);
	}

	$color_active = RGBlight($c, 0, 0.3, 0);
	$color_active_hover = RGBlight($c, 0, 0.2);
	$color_active2 = RGBlight($c, 0, 0.9);

	$color_active_tariff = RGBlight($c, 0, 0.4, 50);

	$color_border_big = RGBlight($c, 0, 0.9, 240);

	$color_active_tariff = RGBlight($c, 0, 0.4, 50);

	$color_button_span = RGBlight($c, 0, 0.8, 0);
	$color_button_hover = RGBlight($c, 0, 0.05, 0);
	$color_button2_hover = RGBlight($c, 0, 0.95);

	$color_total = '555';

	$color_input = $color_active_light2 = RGBlight($c, 0, 0.9, 250);
	$color_input_border = $color_active_tariff;
	$color_active_light2_border = $color_light;
}

/*
��������� ������:
---------------------
order_form_div - ����� �������
	ORDER_FORM - ������� ����� 'compact' � ����� ������������
		order_form_content - ����� ������ (�������� ��� ������)
			order_form_main
			order_form_total
			order_save_button
---------------------
*/
?>

<style>

/* ���� ��� ������ ����� � �������� */
<? if ($company_ico == 'C') { ?>
	#edost_window td.edost_resize_ico { width: 45px; }
	#edost_window img.edost_ico { width: 35px; height: auto; max-height: 55px; padding: 2px; background: #FFF; margin: 0; box-sizing: content-box; border: 0; }
<? } else { ?>
	#edost_window td.edost_resize_ico { width: 70px; }
	#edost_window img.edost_ico { width: 60px; height: auto; padding: 0; box-sizing: content-box; }
<? } ?>

#order_form_div svg.edost_loading path { fill: #<?=$color?>; }

#edost_location_city_template_div .edost_delivery_loading { margin-top: 10px; text-align: center; font-size: 16px; color: #<?=$color?>; }
.edost_template_color { color: #<?=$color?>; }

.edost_pay_from_account { border: 5px solid #<?=$color_border_big?>; padding: 8px; }
#ORDER_FORM .edost_main_active.edost_pay_from_account { border-color: #<?=$color_light?>; }

<? if ($arParams['POLICY'] != 'text') { ?>
div.edost_window_landscape .edost_button_div { margin-bottom: 30px; }
<? } ?>

<? if ($arParams['COD_LIGHT'] == 'Y') { ?>
span.edost_payment, div.edost_payment { color: #F00 !important; font-weight: bold; }
<?	 if ($arParams['FONT_BIG'] != 'Y') { ?>
span.edost_payment, td.edost_format_price div.edost_payment, div.edost_resize_cod2.edost_payment { font-size: 12px !important; }
<?	 } ?>
<? } ?>

<? if ($arParams['FONT_BIG'] == 'Y') { ?>
	span.edost_city_name { font-size: 20px !important; }
<? } ?>

/* ������ "�������" */
.edost_template_light div.edost_main h4 { background: #<?=$color_head?>; }
.edost_template_light div.edost_main {
	border: 1px solid #<?=($arParams['BORDER_COLOR'] == 'Y' ? $color_light : 'CCC')?>;
	border-radius: <?=$border_radius?>px;
}

<? if ($arParams['ACTIVE_LIGHT'] == 'Y') { ?>
.edost_template_light #ORDER_FORM div.edost_main_active span.edost_format_tariff { color: #<?=$color_active_tariff?>; }
.edost_template_light #ORDER_FORM div.edost_main_active span.edost_format_tariff2 { color: #<?=$color_active?>; }

.edost_template_bright div.edost_main_active span.edost_format_tariff { color: #<?=$color_active_tariff?>; }
.edost_template_bright .edost_compact_main div.edost_main_active span.edost_format_tariff2 { color: #<?=$color_active?>; }
.edost_template_bright .edost_supercompact_main #edost_delivery_div span.edost_format_tariff2 { color: #<?=$color?>; }
.edost_template_bright .edost_supercompact_main #edost_paysystem_div span.edost_format_tariff2 { color: #<?=$color_active_tariff?>; font-weight: bold; }

#order_form_div span.edost_city_name { color: #<?=$color_active_tariff?>; }
#order_form_div span.edost_city_name span { color: #<?=$color_active_tariff?>; opacity: 0.6; }

<?	 if ($compact == '') { ?>
#ORDER_FORM div.edost_main_active span.edost_format_name { color: #<?=$color_active_tariff?> !important; }
<?	 } ?>

<? } else { ?>
#order_form_div span.edost_city_name { color: #555; }
<? } ?>

<? if ($arParams['ACTIVE_LIGHT2'] == 'Y') { ?>
.edost_template_light .edost_compact_main div.edost_main_active, .edost_template_light .edost_full_main div.edost_main_active { background: #<?=$color_active_light2?>; padding: 8px 0; border-radius: <?=$border_radius?>px; }

.edost_template_bright .edost_compact_main div.edost_main_active, .edost_template_bright .edost_full_main div.edost_main_active { background: #<?=$color_active_light2?>; padding: 8px 4px 8px 0; border: 1px solid #<?=$color_active_light2_border?>; border-radius: <?=$border_radius?>px; }
.edost_template_bright .edost_compact_main div.edost_main_active img.edost_ico, .edost_template_bright .edost_full_main div.edost_main_active img.edost_ico { border-color: #<?=$color_light?> !important; }
<? } ?>

/* ������ "�����" */
.edost_template_bright div.edost_main h4 { color: #<?=($arParams['ACTIVE_LIGHT'] != 'Y' ? 'AAA' : '737989')?>; }
.edost_template_bright div.edost_main {
	background: #<?=$color_fon?>;
	border: 1px solid #<?=($arParams['BORDER_COLOR'] == 'Y' ? $color_light : 'CCC')?>;
	border-radius: <?=$border_radius?>px;
}

/* ������� */
span.edost_cart_ico { border: 1px solid #<?=$color_ico?>; border-radius: <?=$border_radius?>px; }
div.edost_cart_ico { border: 1px solid #<?=$color_ico?>; border-radius: <?=$border_radius?>px; }

span.edost_props_link { cursor: pointer; color: #<?=$color_light?>; }

div.edost_order_total_main span:nth-child(2) { display: block; font-size: 26px; color: #<?=$color_total?>; }

<? if ($arParams['FONT_BIG'] == 'Y') { ?>
form.edost_compact_main span.edost_format_tariff, div.edost_compact_main span.edost_format_tariff { font-size: 18px; }
form.edost_compact_main span.edost_format_tariff2, div.edost_compact_main span.edost_format_tariff2 { font-size: 18px; }
form.edost_compact_main span.edost_format_price, div.edost_compact_main span.edost_format_price { font-size: 16px; }

form.edost_supercompact_main span.edost_format_tariff, div.edost_supercompact_main span.edost_format_tariff { font-size: 18px; }
form.edost_supercompact_main span.edost_format_tariff2, div.edost_supercompact_main span.edost_format_tariff2 { font-size: 18px; }
form.edost_supercompact_main span.edost_format_price, div.edost_supercompact_main span.edost_format_price { font-size: 16px; }

#edost_window span.edost_format_tariff { font-size: 18px; }
#edost_window span.edost_format_tariff2 { font-size: 18px; }
#edost_window span.edost_format_price { font-size: 16px; }

#edost_window span.edost_format_tariff2 { font-size: 18px; }
#edost_window span.edost_format_name { font-size: 16px; color: #888; }

#edost_order_main div.edost_format_description, #edost_window div.edost_format_description { font-size: 14px; }

<?	 if ($compact == '') { ?>
#order_form_div span.edost_format_tariff { font-size: 18px; }
#order_form_div span.edost_format_name { font-size: 16px; color: #888; }
#order_form_div span.edost_format_price { font-size: 16px; }
#order_form_div span.edost_payment { font-size: 14px; font-weight: bold; }
#order_form_div div.edost_resize_cod2 { font-size: 14px; }
#order_form_div span.edost_format_address_head { font-size: 16px; }
#order_form_div span.edost_format_address { font-size: 16px; }
<?	 } ?>
<? } ?>

div.edost_button_big { background: #<?=$color?>; }
div.edost_button_big span { color: #<?=$color_button_span?>; }
div.edost_button_big:hover { background: #<?=$color_button_hover?>; }

div.edost_button_big2 { border: 1px solid #<?=$color_light?>; }
div.edost_button_big2 span { color: #<?=$color_active_tariff?>; }
div.edost_button_big2:hover { background: #<?=$color_button2_hover?>; }

div.edost_person_type_active { background: #<?=$color_person?>; color: #000; opacity: 1; }
div.edost_person_type:hover { color: #<?=$color_active_tariff?>; opacity: 0.8; }

#order_form_div input[type="text"], #order_form_div input[type="tel"], #order_form_div input[type="email"], #order_form_div input[type="password"], #order_form_div textarea, #order_form_div select, #edost_window input[type="text"], #edost_window input[type="tel"], #edost_window input[type="email"], .bitrix_location .bx-ui-sls-input-block {
	border: 1px solid #<?=$color_input_border?>;
	background: #<?=$color_input?>;
	border-radius: <?=$border_radius?>px;
	font-family: arial;
}

</style>

<?
	// ���� "����������"
	$agreement_label = '';
	$agreement_checked = false;

	if ($arParams['POLICY'] == 'checkbox') {
		$agreement_label = $arParams['~POLICY_CHECKBOX_LABEL'];
		if ($arParams['POLICY_CHECKBOX_CHECKED'] == 'Y') $agreement_checked = true;
	}
	if ($arParams['POLICY'] == 'bitrix' && isset($arParams['USER_CONSENT']) && $arParams['USER_CONSENT'] === 'Y' && !empty($arResult['USER_CONSENT_PROPERTY_DATA'])) {
		$ar = $arResult['USER_CONSENT_PROPERTY_DATA'];
		foreach ($ar as $k => $v) if (strpos($v, ':') !== false) { $s = explode(':', $v); $ar[$k] = trim($s[0]); }
		$ar[] = GetMessage('SOA_TEMPL_AGREEMENT_IP');
		$replace = array('button_caption' => '%button%', 'fields' => $ar);
		$agreement = new Bitrix\Main\UserConsent\Agreement($arParams['USER_CONSENT_ID'], $replace);
		if ($agreement->isExist() && $agreement->isActive()) {
			$agreement_label = $agreement->getLabelText();
			$agreement_text = nl2br(htmlspecialcharsbx($agreement->getText()));
			if (!empty($arParams['USER_CONSENT_IS_CHECKED']) && $arParams['USER_CONSENT_IS_CHECKED'] == 'Y') $agreement_checked = true;
		}
	}

	if ($agreement_label != '') {
		$s = '<div class="edost_agreement_checkbox">';
		$s .= '<input type="checkbox" id="%id%" '.($agreement_checked ? 'checked=""' : '').' data-name="edost_agreement" data-type="agreement" onclick="window.edost_window.input(\'update\', this)">';
		$s .= ' <label for="%id%">'.$agreement_label.'</label>';
		if ($arParams['POLICY'] == 'bitrix') $s .= ' (<span class="edost_link" onclick="edost_window.set(\'agreement\', \'head='.GetMessage('SOA_TEMPL_AGREEMENT_HEAD').';class=edost_window_form\')">'.GetMessage('SOA_TEMPL_AGREEMENT_OPEN').'</span>)';
		$s .= '<div class="edost_prop_error" style="text-align: center;"></div>';
		$s .= '</div>';
		$agreement_label = $s;
	}
	if ($arParams['POLICY'] == 'bitrix') { ?>
		<div id="edost_agreement_div" style="display: none;">
			<div class="edost_window_form_head"><?=GetMessage('SOA_TEMPL_AGREEMEN_WARNING')?></div>
			<div id="edost_agreement_text" class="edost_agreement_text"><?=$agreement_text?></div>
			<div class="edost_button_div">
				<div class="edost_button_left">
					<div class="edost_button_form edost_button_big" onclick="submitForm('Y')"><span><?=GetMessage('SOA_TEMPL_AGREEMENT_YES')?></span></div>
				</div>
				<div class="edost_button_right">
					<div class="edost_button_form edost_button_big2" onclick="window.edost_window.set('close_full');"><span><?=GetMessage('SOA_TEMPL_AGREEMENT_NO')?></span></div>
				</div>
			</div>
		</div>
<?	} ?>


<a name="order_form"></a>
<div id="order_form_div" class="edost_template_<?=$arParams['STYLE']?>">
<NOSCRIPT>
	<div class="edost_noscript"><?=GetMessage('SOA_NO_JS')?></div>
</NOSCRIPT>

<input id="edost_template_2019" name="edost_template_2019" data-ico="<?=$company_ico?>" data-compact="<?=$compact?>" data-priority="<?=(($compact == '' || $compact == 'off') && (!empty($format['cod']) || !empty($format['cod_tariff'])) ? 'B' : $priority)?>" value="<?=implode('|', $template_param).':'.implode('|', $template_param_site)?>" type="hidden">
<input id="edost_template_data" value="<?=$template_data?>" type="hidden">

<?
	if (!$USER->IsAuthorized() && $arParams['ALLOW_AUTO_REGISTER'] == 'N') {
		if (!empty($arResult['ERROR_SORTED'])) {
			$e = array();
			foreach ($arResult['ERROR_SORTED'] as $k => $v) if (!in_array($k, array('PAY_SYSTEM', 'DELIVERY'))) foreach ($v as $v2) $e[] = '<span>'.$v2.'</span>';
			if (!empty($e)) echo '<div class="edost edost_main edost_order_error">'.implode('', $e).'</div>';
		}
		else if(!empty($arResult['OK_MESSAGE'])) {
			foreach ($arResult['OK_MESSAGE'] as $v) echo ShowNote($v);
		}

		include($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/auth.php');
	}
	else {
		if ($arResult['USER_VALS']['CONFIRM_ORDER'] == 'Y' || $arResult['NEED_REDIRECT'] == 'Y') {
			if(strlen($arResult['REDIRECT_URL']) > 0) { ?>
				<script type="text/javascript">
					window.top.location.href = '<?=CUtil::JSEscape($arResult['REDIRECT_URL'])?>';
				</script>
<?				die();
			}
			else include($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/confirm.php');
		}
		else { ?>
			<script type="text/javascript">
				var BXFormPosting = false;

<?			if (!$edost_locations) {
				$city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch(); ?>

				BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
					'source' => $this->__component->getPath().'/get.php',
					'cityTypeId' => intval($city['ID']),
					'messages' => array(
						'otherLocation' => '--- '.GetMessage('SOA_OTHER_LOCATION'),
						'moreInfoLocation' => '--- '.GetMessage('SOA_NOT_SELECTED_ALT'),
						'notFoundPrompt' => '<div class="-bx-popup-special-prompt">'.GetMessage('SOA_LOCATION_NOT_FOUND').'.<br />'.GetMessage('SOA_LOCATION_NOT_FOUND_PROMPT', array(
							'#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
							'#ANCHOR_END#' => '</a>'
						)).'</div>'
					)
				))?>);

				function edost_SetOfficeAddress() {
					var E = BX('edost_address_input');
					if (!E) return;
					var s = E.value.split('|');
					var E = BX(s[0]);
					if (E && s[1]) { E.readOnly = true; E.style.backgroundColor = '#f7f7f7'; }
				}
<?			} ?>

				function loadingForm() {

					var E = document.getElementById('edost_order_loading_fon');
					if (!E) {
						var E = document.getElementById('order_form_content');

						var browser_h = (document.documentElement.clientHeight == 0 ? document.body.clientHeight : document.documentElement.clientHeight);

						var E2 = document.createElement('DIV');
						E2.className = 'edost_order_loading_fon';
						E2.id = 'edost_order_loading_fon';
						E2.style.display = 'none';
						E2.innerHTML = '<div class="edost_loading_128" style="padding: ' + Math.round(browser_h/2 - 64) + 'px 0 0 0; text-align: center;">' + edost_office.loading + '</div>';
//						'<div class="" style="padding: ' + Math.round(browser_h/2) + 'px 0 0 0; text-align: center;"><img class="edost_ico_loading" src="<?=$templateFolder?>/images/loading.gif" border="0" width="64" height="64"></div>';

						E.appendChild(E2);
					}

					var E = document.getElementById('edost_order_loading_fon');
					if (E) E.style.display = 'block';

				}

				function edost_Agreement(param, fast) {
//					alert('edost_Agreement: ' + param + ' | ' + fast);

					if (param == 'fast') edost_window.set('fast', 'head=<?=GetMessage('SOA_TEMPL_FAST_HEAD')?>;class=edost_window_form');
					else if (param == 'request') edost_window.set('agreement' + (fast ? '_fast' : ''), 'head=<?=GetMessage('SOA_TEMPL_AGREEMENT_HEAD')?>;class=edost_window_form');
					else if (param == 'set') {
						var E = document.getElementById('edost_agreement');
						if (E) {
							if (!fast) E.click();
							E.checked = true;
						}

						if (fast) edost_Agreement('fast');
					}
					else if (param == 'unset') {
						var E = document.getElementById('edost_agreement');
						if (E) E.checked = false;
					}
					else if (param == 'submit') {
						if (fast) {
							var E = document.getElementById('edost_fast');
							if (E) E.value = 'Y';
						}

						submitForm('Y');
					}
				}

				function submitForm(val, param) {

					var orderForm = BX('ORDER_FORM');

					if (val == 'update' && orderForm.classList.contains('edost_supercompact_main')) return true;
					if (val == 'office') {
						window.edost_office.window(param, true);
						return;
					}

					if (BXFormPosting === true) return true;

					BXFormPosting = true;
					if (val != 'Y') BX('confirmorder').value = 'N';

					loadingForm();

<?					if (!$edost_locations) echo 'BX.saleOrderAjax.cleanUp();'; ?>

<?					if (!empty($arResult['edost']['map_inside'])) { ?>
					if (window.edost_office2 && edost_office2.map) {
						edost_office2.map.destroy();
						edost_office2.map = false;
					}
<?					} ?>

					BX.ajax.submit(orderForm, ajaxResult);

					return true;
				}

				function updateForm() {
					if (window.edost_resize) edost_resize.start();
					if (window.edost_office) edost_office.window('parse');

<?					if (!empty($arResult['edost']['map_inside'])) { ?>
					if (window.edost_office2) {
						edost_office2.data_parsed = false;
						edost_office2.window('inside');
					}
<?					}

					if (!empty($address_id)) echo 'edost_SetOfficeAddress();'; ?>
				}

				function ajaxResult(res) {
					var orderForm = BX('ORDER_FORM');
					try {
						var json = JSON.parse(res);
						if (json.error) {
							BXFormPosting = false;
							return;
						}
						else if (json.redirect) window.top.location.href = json.redirect;
					}
					catch (e) {
						BXFormPosting = false;
						BX('order_form_content').innerHTML = res;
<?						if (!$edost_locations) echo 'BX.saleOrderAjax.initDeferredControl();'; ?>
						updateForm();
					}

//					BX.onCustomEvent(orderForm, 'onAjaxSuccess');
				}

				function SetContact(profileId) {
					BX('profile_change').value = 'Y';
					submitForm();
				}
			</script>

<?			if ($_POST['is_ajax_post'] != 'Y') {
				$c = 'full';
				if ($compact == 'Y') $c = 'compact';
				else if ($compact == 'S' || $compact == 'S2') $c = 'supercompact';
?>
				<form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" class="<?=($c != '' ? 'edost_'.$c.'_main edost_'.$c.'_main2 edost_order_main' : '')?> edost_compact_head_normal edost_cart_normal edost_template_total_off" id="ORDER_FORM" enctype="multipart/form-data">
				<?=bitrix_sessid_post()?>

				<div id="order_form_content" class="<?=($company_ico == C ? 'edost_company_ico' : 'edost_tariff_ico')?>">
<?			}
			else {
				$APPLICATION->RestartBuffer();
			}
?>
			<div id="order_form_main" class="" style="width: <?=(!empty($template_param['width']) ? $template_param['width'].'px' : '100%')?>;">
<?
			$error = false;
			if (!empty($arResult['edost']['fast']) && !empty($arResult['ERROR_SORTED']['AUTH'])) $error = $arResult['ERROR_SORTED']['AUTH'];
			else if (!empty($arResult['ERROR'])) $error = $arResult['ERROR'];
			if (!empty($error) && $arResult['USER_VALS']['FINAL_STEP'] == 'Y') {
				$e = array();
				$error = array_unique($error); // �������� ������
				foreach ($error as $v) $e[] = '<span>'.$v.'</span>';
				if (!empty($e)) {
					echo '<div class="edost edost_main edost_order_error">'.implode('', $e).'</div>'; ?>
				<script type="text/javascript">
					var rect = top.BX('ORDER_FORM').getBoundingClientRect();
					if (rect.top < -20) top.window.scrollBy(0, rect.top - 20);
				</script>
<?				}
			} ?>

			<div id="edost_template_width_div"></div>
			<input id="edost_template_width" name="edost_template_width" value="<?=$template_width?>" type="hidden">
<?			if (!empty($address_id)) echo '<input type="hidden" value="ORDER_PROP_'.$address_id.'|'.(isset($format_active['address']) ? 'Y' : '').'" id="edost_address_input">';
			if (!empty($arResult['edost']['cod_tariff'])) echo '<input type="hidden" value="'.$arResult['edost']['cod_tariff'].'" name="edost_cod_tariff_paysystem">';

			$param = array('compact' => $compact, 'edost_locations' => $edost_locations, 'delivery_location' => $delivery_location);
			include($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/props.php');

			if ($arParams['ORDER_FORMAT'] == 'classic') PrintPropsForm(GetMessage('SOA_TEMPL_BUYER_INFO'), array('person_type', 'profile', 'props_Y', 'props_N'), $arResult, $arParams, $param + array('disable' => array('LOCATION', 'ZIP', 'CITY'), 'class' => array('main' => 'edost_main_start2')));
			else {
				$profile = (!empty($arResult['ORDER_PROP']['USER_PROFILES']) && $arParams['ALLOW_USER_PROFILES'] == 'Y' ? true : false);
				$person_type = (count($arResult['PERSON_TYPE']) > 1 ? true : false);
				PrintPropsForm('', array('person_type', 'profile'), $arResult, $arParams, $param + array('hide' => array('main' => !$profile && !$person_type), 'id' => array('main' => 'order_person_main'), 'class' => array('main' => 'edost_main_fon edost_main_start2')));
			}

			if ($edost_locations) PrintPropsForm(GetMessage('SOA_TEMPL_LOCATION'), array('location'), $arResult, $arParams, $param + array('id' => array('main' => 'edost_location_div'), 'class' => array('main' => 'edost_template_location_div edost_main_fon '.($delivery_location === false ? 'edost_template_location_hide' : ''))));
			else PrintPropsForm(GetMessage('SOA_TEMPL_LOCATION'), array('props_Y'), $arResult, $arParams, $param + array('enable' => array('LOCATION', 'ZIP', 'CITY')));

//			echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult, true).'</pre>'; // !!!!!
//			echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult['edost']['format']['active'], true).'</pre>'; // !!!!!
//			echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult['edost']['format'], true).'</pre>'; // !!!!!
//			echo '<br><b>_SESSION:</b> <pre style="font-size: 12px">'.print_r($_SESSION['EDOST']['develop'], true).'</pre>'; // !!!!!
//			echo '<br><b>_SESSION:</b> <pre style="font-size: 12px">'.print_r($_SESSION['EDOST']['request'], true).'</pre>'; // !!!!!
//			$_SESSION['EDOST']['develop'] = array(); // !!!!!

			if ($delivery_location === false) { ?>
			<div class="edost_no_location_warning"<?=($edost_locations ? ' style="cursor: pointer;" onclick="edost_SetTemplateLocation(\'new\')"' : '')?>><?=GetMessage('SOA_TEMPL_NO_LOCATION_WARNING')?></div>
			<div style="display: none;">
<?			}

			$ar = array('delivery.php', 'paysystem.php');
			if ($priority == 'C') $ar = array_reverse($ar);
			foreach ($ar as $v) include($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/'.$v);

			if (!$save_button) {
				if ($no_delivery_tariff) { ?>
				<div class="edost_no_location_warning" style="color: #F00;"><?=GetMessage('SOA_TEMPL_NO_DELIVRY_TARIFF_WARNING')?></div>
<?				} else if ($no_office && (!$no_delivery || $format['count'] == 1)) { ?>
				<div class="edost_no_location_warning" style="cursor: pointer;" onclick="window.edost_office.window('<?=$format['active']['profile']?>', false);"><?=GetMessage('SOA_TEMPL_NO_OFFICE_WARNING')?></div>
<?				} else if ($no_delivery) { ?>
				<div class="edost_no_location_warning" style="cursor: pointer;" onclick="var E = document.getElementById('edost_get_delivery_button'); if (E) E.click();"><?=GetMessage('SOA_TEMPL_NO_DELIVRY_WARNING')?></div>
<?				} ?>
			<div style="display: none;">
<?			} ?>

			<input id="edost_address_hide" value="<?=($address_hide || $delivery_location === false ? 'Y' : 'N')?>" type="hidden">

<?			if ($arParams['ORDER_FORMAT'] == 'progress_compact') {
				PrintPropsForm(GetMessage('SOA_TEMPL_BUYER_INFO'), array('props_Y', 'props_N', 'address', 'passport', 'props_related', 'comment'), $arResult, $arParams, $param + array('disable' => array('LOCATION', 'ZIP', 'CITY'), 'hide' => array('address' => $address_hide || $delivery_location === false ? true : false), 'class' => array('main' => 'edost_main_fon')));
			}
			else {
				if ($edost_locations) PrintPropsForm(GetMessage('SOA_TEMPL_ADDRESS_HEAD'), array('address'), $arResult, $arParams, $param + array('hide' => array('main' => $address_hide || $delivery_location === false ? true : false)));
				else PrintPropsForm(GetMessage('SOA_TEMPL_ADDRESS_HEAD'), array('props_Y'), $arResult, $arParams, $param + array('enable' => array('ADDRESS')));

				PrintPropsForm(GetMessage('SOA_TEMPL_PASSPORT_HEAD'), array('passport'), $arResult, $arParams, $param);

				PrintPropsForm(GetMessage($arParams['ORDER_FORMAT'] == 'progress' ? 'SOA_TEMPL_BUYER_INFO' : 'SOA_TEMPL_RELATED_PROPS'), $arParams['ORDER_FORMAT'] == 'progress' ? array('props_Y', 'props_N', 'props_related') : array('props_related'), $arResult, $arParams, $param + array('disable' => array('LOCATION', 'ZIP', 'CITY')));

				PrintPropsForm(GetMessage('SOA_TEMPL_SUM_COMMENTS'), array('comment'), $arResult, $arParams, $param);
			}

			if (!$save_button) echo '</div>';
			if ($delivery_location === false) echo '</div>';


/* ���� � ������� "������� ����������" */
if ($arParams['FAST'] == 'full' || $arParams['FAST'] == 'inside' || $arParams['FAST'] == 'compact') { ?>
<div class="edost_main edost_template_div edost_fast <?=($arParams['FAST'] == 'compact' ? 'edost_order_inside' : '')?>">
	<table class="edost_fast" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
		<td><?=GetMessage('SOA_TEMPL_FAST_INFO')?></td>
		<td width="155" align="right"><div class="edost_button_big2" style="font-size: 16px;" onclick="edost_Agreement('fast')"><span style="display: inline-block; font-size: 16px;"><?=GetMessage('SOA_TEMPL_FAST_BUTTON')?></span></div></td>
	</tr></table>
</div>
<? }


/* ���� "������� ����������" */ ?>
<input id="edost_fast" name="edost_fast" value="" type="hidden">
<div id="edost_fast_div" style="display: none;">

	<div class="edost_window_form_head" style="font-size: 14px;"><?=GetMessage('SOA_TEMPL_FAST_INFO2')?></div>

<?	$i = 0;
	foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as $k => $v) {
		if (!($v['IS_PROFILE_NAME'] == 'Y' || $v['CODE'] == 'FIO' || $v['IS_EMAIL'] == 'Y' || $v['CODE'] == 'EMAIL' || $v['IS_PHONE'] == 'Y' || $v['CODE'] == 'PHONE')) continue;
		$i++; ?>
		<div class="edost_prop_div">
			<div class="edost_prop_head"><?=$v['NAME']?></div>
			<div class="edost_prop"><?=DrawInput($v, true)?></div>
		</div>
<? } ?>

<?	if ($agreement_label !== false) { ?>
	<?=str_replace(array('%id%', '%button%'), array('edost_agreement_2', GetMessage('SOA_TEMPL_FAST_BUTTON2')), $agreement_label)?>
<?	} ?>

	<div class="edost_button_div" style="margin-top: 10px;">
		<div class="edost_button_form edost_button_big" onclick="submitForm('Y')"><span><?=GetMessage('SOA_TEMPL_FAST_BUTTON2')?></span></div>

<?		if ($arParams['POLICY'] == 'text') { ?>
		<div class="edost_policy_text" style="padding-top: 12px;"><?=str_replace('%button%', GetMessage('SOA_TEMPL_FAST_BUTTON2'), $arParams['~POLICY_TEXT'])?></div>
<?		} ?>
	</div>
</div>

<?			$total_compact = false; include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
			if (strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0) echo $arResult["PREPAY_ADIT_FIELDS"]; ?>

					</div> <? /* �������� order_form_main */ ?>

					<div id="order_form_total" style="<?=($template_param['mode'] == 'off' ? 'display2222: none;' : '')?> <?=(!empty($template_param['width2']) ? 'width: '.$template_param['width2'].'px;' : '')?>">
						<div class="edost_order_compact" id="order_form_total_div" style="<?=(!empty($template_param['width2']) ? 'width: '.$template_param['width2'].'px;' : '')?> <?=(!empty($template_param['fixed']) ? 'position: fixed;' : '')?> <?=(!empty($template_param['top']) ? 'top: '.$template_param['top'].'px;' : '')?>">
<?						$total_compact = true; include($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/summary.php'); ?>
						</div>
					</div>

					<div style="clear: both;"></div>

<?					if ($delivery_location !== false && $save_button) {
						if ($agreement_label != '') { ?>
							<?=str_replace(array('%id%', '%button%'), array('edost_agreement', GetMessage('SOA_TEMPL_BUTTON')), $agreement_label)?>
<?						} ?>

						<div id="order_save_button" class="edost_button_big" onclick="if (window.edost_window.props()) submitForm('Y'); ym(22745254,'reachGoal','order'); ga('send', 'event', 'order', 'click');"><span><?=GetMessage('SOA_TEMPL_BUTTON')?></span></div>

<?						if ($arParams['POLICY'] == 'text') { ?>
							<div class="edost_policy_text"><?=str_replace('%button%', GetMessage('SOA_TEMPL_BUTTON'), $arParams['~POLICY_TEXT'])?></div>
<?						}
					}

			if ($_POST['is_ajax_post'] != 'Y') { ?>
				</div> <? /* �������� order_form_content */ ?>

					<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
					<input type="hidden" name="profile_change" id="profile_change" value="N">
					<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">

					<script type="text/javascript">
						updateForm();
					</script>

				</form>
<?				if ($arParams['DELIVERY_NO_AJAX'] == 'N') {
					$APPLICATION->AddHeadScript("/bitrix/js/main/cphttprequest.js");
					$APPLICATION->AddHeadScript("/bitrix/components/bitrix/sale.ajax.delivery.calculator/templates/.default/proceed.js");
				}
			}
			else { ?>
					<script type="text/javascript">
						top.BX('confirmorder').value = 'Y';
						top.BX('profile_change').value = 'N';
					</script>
<?					die();
			}
		}
	} ?>

</div>

<? if (!$edost_locations) { ?>
	<div style="display: none">
<?		$APPLICATION->IncludeComponent('bitrix:sale.location.selector.steps', '.default', array(), false);
		$APPLICATION->IncludeComponent('bitrix:sale.location.selector.search', '.default', array(), false); ?>
	</div>
<? } ?>