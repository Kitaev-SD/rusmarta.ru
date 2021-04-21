<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
$address_inside = false; // выводить блок ввода адреса сразу под тарифом доставки (только при использовании модуля edost.locations)
$no_free = array(1, 100); // коды тарифов, для которых не нужно выводить "Бесплатно!" (или вместо этого массива, можно добавить в описание тарифа текст "[no_free]")

// собственные тарифы "Самовывоз" магазина, для которых требуется отключение ввода адреса и замена адреса на заданное значение (только при использовании модуля edost.locations)
// или вместо этого массива, можно добавить в описание тарифа текст "[address=магазин: ул. Кутузовская, д. 20, телефон: +7-123-456-78-90]"
$address_tariff = array(
//	'edost:61' => 'магазин: ул. Кутузовская, д. 20, телефон: +7-123-456-78-90', // по коду тарифа eDost
//	140 => 'магазин: ул. Кутузовская, д. 20, телефон: +7-123-456-78-90', // по ID доставки битрикса
	10 => 'original', // оставить оригинальный адрес (для модулей с доставкой до пункта выдачи)
);
?>

<style>
	/* перебивание глобальных стилей магазина !!! */
	div.label { color: #000; font-size: 100%; }
	div.edost label { font-weight: normal; }
	div.bx_element label { display: inline; }
	div.bx_ordercart h2 { font-size: 16px; }
</style>

<?
// предзагрузка скрипта для быстрого открытия карты с пунктами выдачи (но при этом сама страница оформления заказа будет загружаться дольше!!!)
//$APPLICATION->AddHeadString('<script type="text/javascript" src="https://api-maps.yandex.ru/2.0-stable/?load=package.standard,package.clusters&lang=ru-RU"></script>');
?>

<?
// правила адаптивности (установка стилей/параметров в зависимости от размера блока)
if (isset($arResult['edost']['format'])) $resize = array(
	'ico' => array('ico', 'edost_resize_ico', 'edost_ico_normal,0', 380, 'edost_ico_small,35', 300, 'edost_ico_hide,65'),
	'ico_row' => array('ico_row', 'edost_resize_ico', '3', 550, 'auto', 450, '1'),
	'cod' => array('id', 'edost_delivery_div', '', 550, 'edost_cod_hide'),
	'delivery' => array('id', 'edost_delivery_div', 'edost_delivery_normal', 450, 'edost_delivery_small', 380, 'edost_delivery_small2'),
	'bookmark_cod' => array('id', 'edost_delivery_div', 'edost_bookmark_cod_normal', 350, 'edost_bookmark_cod_small'),
	'delimiter' => array('class', 'edost_main', 'edost_delimiter_normal', 600, 'edost_delimiter_small'),
	'bookmark' => array('id', 'edost_delivery_div', 'edost_bookmark_normal', 700, 'edost_bookmark_small'),
	'map' => array('id', 'edost_delivery_div', 'edost_map_normal', 500, 'edost_map_hide'),
);
else $resize = array(
	'section' => array('class', 'bx_section', 'visual_normal', 600, 'visual_small'),
	'ico_row' => array('ico_row', 'visual_ico', '2', 600, '1'),
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
?>
<div id="edost_template_width_div"></div>
<input id="edost_template_width" name="edost_template_width" value="<?=$template_width?>" type="hidden">
<input id="edost_template_data" value="<?=$template_data?>" type="hidden">




<? /* ==================== edost.locations (выбор местоположений) */ ?>
<? if (!empty($arResult['edost']['locations_installed'])) { ?>
<style>
	/* перебивание глобальных стилей шаблона Visual (больших полей) !!! */
	#edost_location_city_div input.edost_city { width: 100% !important; max-width: 600px !important; }
	#edost_location_city_div input.edost_city, #edost_location_city_zip_div input.edost_input, #edost_location_city_div input.edost_input, #edost_location_address_div input.edost_input { height: 25px !important; padding: 2px 4px !important; margin-bottom: 0 !important; vertical-align: baseline; }
</style>

<div class="edost_main edost_template_div">
	<h4 class="edost_location_head">Местоположение доставки</h4>
<?
	$delivery_id = $arResult['USER_VALS']['DELIVERY_ID'];
	$warning = (!empty($arResult['edost']['format']['warning']) || isset($arResult['edost']['warning']) ? true : false);
	$address_shop = 'N'; // если указано любое значение отличное от 'N', тогда поле для ввода адреса отключается и в заказе сохраняется указанный адрес
	$address_hide = (!empty($arResult['edost']['address_hide']) || empty($delivery_id) ? true : false); // генерируется при активном тарифе с доставкой до пункта выдачи модуля edost.delivery (НЕ менять!!!)

	// отключение вывода адресного блока
	$s = false;
	if (isset($arResult['DELIVERY'][$delivery_id])) {
		$v = $arResult['DELIVERY'][$delivery_id];
		if (isset($address_tariff[$delivery_id])) $s = $address_tariff[$delivery_id];
		else if (isset($v['profile'])) {
			$p = $v['profile'];
			if (isset($address_tariff['edost:'.$p])) $s = $address_tariff['edost:'.$p];
		}
		if ($s === false && !empty($v['DESCRIPTION']) && strpos($v['DESCRIPTION'], '[address=') !== false) {
			$s = explode('[address=', $v['DESCRIPTION']);
			$s = explode(']', $s[1]);
			$s = $s[0];
		}
	}
	if ($s !== false) {
		$address_hide = true;
		if ($s == 'original') echo '<input id="edost_address_original" name="edost_address_original" value="Y" type="hidden">';
		else $address_shop = $s;
	}

	$edost_locations_param = array(
		'ID' => $arResult['USER_VALS']['DELIVERY_LOCATION'],
		'DELIVERY_ID' => $delivery_id,
		'PROP' => $arResult['edost']['order_prop'],
		'PROP2' => $arResult['edost']['order_prop2'],

		'PARAM' => array(
//			'edost_delivery' => false, // false - отключить использование модуля edost.delivery (перейти на выбор местоположений через выпадающие списки), только для быстрой проверки - для постоянного использования требуется включение блокировки в константах !!!
			'input' => (!empty($arResult['edost']['location_input']) ? $arResult['edost']['location_input'] : 0), // ID местоположения с которого включится режим выбора (если не задано или равно "0", тогда модуль работает в автоматическом режиме)
			'zip_in_city' => (isset($arResult['edost']['order_prop']['ZIP']) && ($arResult['edost']['order_prop']['ZIP']['value'] === '' || $warning && $address_hide) ? true : false), // true - выводить индекс в блоке с местоположением
			'address' => $address_shop, // присвоить собственный адрес самовывоза ('N' - стандартная работа)
//			'loading' => 'loading_small_f2.gif', // иконка загрузки при расчете доставки и проверке индекса - лежит в папке bitrix/components/edost/locations/images/
/*
			// предупреждения (если указаны, тогда заменяют значения по умолчанию)
			'zip_warning' => array(
				1 => 'Такого индекса НЕ существует!2222',
				2 => 'Индекс НЕ соответствует региону!',
				'digit' => 'Должны быть только цифры!',
			),
*/
			// модификация полей адреса (если указаны, тогда заменяют значения по умолчанию)
			'address_field' => array(
/*
				'city2' => array(
					'name' => 'Населенный пункт', // название поля
					'width' => 200, // длина поля в пикселях
					'max' => 50, // допустимое количество символов
					'enter' => true, // true - добавить перед полем 'ввод'
				),
				'street' => array(
					'delimiter' => true, // установить после поля разделитель
					'delimiter_style' => 'width: 20px', // собственный стиль для разделителя
				),
//				'house_1', 'house_2', 'house_3', 'house_4', 'door_1', 'door_2' // остальные поля
*/

				// ограничения по выводу полей
				'city2_required' => array(
//					'value' => 'Y', // '' - не обязательно, 'Y' - обязательно (по умолчанию)
				),
				'street_required' => array(
//					'value' => '', // '' - не обязательно, 'Y' - обязательно, 'A' - работа с модулем edost.delivery с обязательным выбором улицы из списка подсказок для городов с отдаленными районами (по умолчанию)
//					'value' => 'Y', // '' - не обязательно, 'Y' - обязательно, 'A' - работа с модулем edost.delivery с обязательным выбором улицы из списка подсказок для городов с отдаленными районами (по умолчанию)
				),
				'zip_required' => array(
//					'value' => (in_array($delivery_id, array(10, 20)) ? 'Y' : ''), // '' - поле не выводится, 'S' - поле выводится, 'Y' - должно быть обязательно заполнено, 'A' - работа с модулем edost.delivery (по умолчанию)
				),
				'metro_required' => array(
//					'value' => (in_array($delivery_id, array(10, 20)) ? 'S' : ''), // '' - поле не выводится, 'S' - поле выводится, 'A' - работа с модулем edost.delivery (по умолчанию)
				),
			),
		),
	);
?>
	<? $GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'city') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y')); ?>
</div>
<? }
if (!empty($address_inside) && (empty($arResult['edost']['locations_installed']) || !isset($arResult['edost']['order_prop']['ADDRESS']))) $address_inside = false;
?>
<? /* ==================== edost.locations */ ?>




<? /* шаблон edost - НАЧАЛО */ ?>
<? if (isset($arResult['edost']['format'])) { ?>
<?
	$edost_catalogdelivery = false;
	$data = (isset($arResult['edost']) ? $arResult['edost'] : false);
	$calculate_function = (!empty($data['calculate_function']) ? $data['calculate_function'] : 'submitForm()');
	$sign = GetMessage('EDOST_DELIVERY_SIGN');
	$cod_head_bookmark2 = str_replace('<br>', ' ', $sign['cod_head_bookmark']);
	$table_width = 645;
	$ico_path = '/bitrix/images/delivery_edost_img';
	$ico_loading = '<img style="vertical-align: middle;" src="'.$ico_path.'/loading_small.gif" width="20" height="20" border="0">'; // иконка загрузки
//	$ico_loading_map_inside = '<div class="edost_map_loading"><img src="'.$ico_path.'/loading_small.gif" border="0" width="64" height="64"></div>'; // иконка загрузки для интегрированной карты

	if ($edost_catalogdelivery) $ico_default = (!empty($param['ico_default']) ? $param['ico_default'] : $arResult['component_path'].'/images/logo-default-d.gif');
	else $ico_default = $templateFolder.'/images/logo-default-d.gif';

	if (!empty($data['javascript'])) echo $data['javascript'];
	if (!empty($data['format']['warning']) && empty($arResult['edost']['locations_installed'])) echo '<div class="edost_warning edost_warning_big" style="padding-top: 20px;">'.$data['format']['warning'].'</div>';
	$map_inside = (!$edost_catalogdelivery && empty($data['map_inside']) || empty($data['format']['map_inside']) || $data['format']['map_inside'] == 'N' ? '' : $data['format']['map_inside']);

	$tariff_price_hide = array(); // трифы без вывода стоимости доставки (сравнивается параметр 'html_id')

	// собственное описание для групп пунктов выдачи
//	$sign['office_description']['shop'] = ''; // адреса магазинов
//	$sign['office_description']['office'] = ''; // пункты выдачи
//	$sign['office_description']['terminal'] = ''; // терминалы
?>

<style>
	div.edost_main { font-family: arial; line-height: normal; margin: 45px 0 20px 0; }
/*
	div.edost_office_window_fon { z-index: 1500 !important; }
	div.edost_office_window { z-index: 1501 !important; }
	div.edost_catalogdelivery_window_fon { z-index: 1490 !important; }
	div.edost_catalogdelivery_window { z-index: 1491 !important; }
	div.edost_office_balloon { z-index: 1492 !important; }
	div.edost_office_info { z-index: 1493 !important; }
*/
</style>

<script type="text/javascript">
	function edost_SetOffice(profile, id, cod, mode) {

		if (id == undefined) {
<?			if (!$edost_catalogdelivery) { ?>
			var E = document.getElementById('edost_delivery_id');
			if (E) if (E.value != 'edost:' + profile) <?=$calculate_function?>;
<?			} ?>
			return;
		}

		if (window.edost_office && edost_office.map && edost_office.map.balloon) {
			edost_office.balloon('close');
			edost_office.map = false;
			edost_office.window('close');
		}
		if (window.edost_office2 && edost_office2.map && edost_office2.map.balloon) {
			edost_office2.balloon('close');
			edost_office2.map = false;
		}

<?		if (!$edost_catalogdelivery) { ?>
		var E = document.getElementById('edost_address_' + mode);
		if (E) {
			if (E.style.display == 'none') {
				var ar = ['office', 'terminal', 'shop'];
				for (var i = 0; i < ar.length; i++) if (ar[i] != mode) {
					var E = document.getElementById('edost_address_' + ar[i]);
					if (E && E.style.display != 'none') { mode = ar[i]; break; }
				}
			}
			E.style.display = 'none';
		}

		var E = document.getElementById('edost_office_inside');
		if (E) E.style.display = 'none';
		var E = document.getElementById('edost_office_detailed');
		if (E) E.innerHTML = '<br>';

		var E = document.getElementById('edost_address_' + mode + '_loading');
		if (E) E.innerHTML = '<?=$ico_loading?> <span class="edost_description"><?=$sign['loading']?></span>';

		var E = document.getElementById('edost_office_map_div');
		if (E && E.style.display != 'none') E.innerHTML = '<br> <?=$ico_loading?> <span class="edost_description"><?=$sign['loading']?></span>';

		var ar = document.getElementsByName('DELIVERY_ID');
		for (var i = 0; i < ar.length; i++) if (ar[i].id == 'ID_DELIVERY_edost_' + mode) {
			ar[i].value = 'edost' + ':' + profile + ':' + id + (cod != '' ? ':' + cod : '');
			ar[i].checked = true;
			break;
		}

		<?=$calculate_function?>;
<?		} else { ?>
		edost_catalogdelivery.calculate('loading');
		BX.ajax.post('<?=$arResult['component_path']?>/edost_catalogdelivery.php', 'set_office=Y&id=' + id + '&profile=' + profile + '&cod=' + cod + '&mode=' + mode, function(r) {
			edost_catalogdelivery.calculate();
		});
<?		} ?>

	}

	function edost_MapInside() {

<?		if ($edost_catalogdelivery) { ?>
		edost_RunScript('map_inside');
<?		} else { ?>
		if (!window.edost_office2) return;
		var E = document.getElementById('edost_office_inside');
		if (!E) return;
		var E = document.getElementById('edost_office_inside_head');
		if (E) return;
		var E = document.getElementById('edost_office_div');
		if (E && E.style.display != 'none') {
			if (window.edost_office) edost_office.map = false;
			edost_office2.map = false;
			edost_office2.window('inside');
		}
<?		} ?>

	}

	function edost_SetBookmark(id, bookmark) {

		var start = false;
		if (bookmark == undefined) bookmark = '';
		if (id == 'start') {
			start = true;
			E2 = document.getElementById('edost_bookmark');
			if (E2) id = E2.value;
			if (id == '') return;
		}

		var ar = ['office', 'door', 'house', 'post', 'general', 'show'];
		for (var i = 0; i < ar.length; i++) {
			var E = document.getElementById('edost_' + ar[i] + '_div');
			var E_map = (ar[i] == 'office' ? document.getElementById('edost_' + ar[i] + '_map_div') : false);
			var E2 = document.getElementById('edost_' + ar[i] + '_td');
			if (!E && !E2) continue;

			var E3 = document.getElementById('edost_' + ar[i] + '_td_bottom');
			var show = (ar[i] == id ? true : false);
			if (E2) E2.className = 'edost_bookmark edost_active_' + (show ? 'on' : 'off');
			if (E3) E3.className = 'edost_active_fon_' + (show ? 'on' : 'off');
<?			if (!$edost_catalogdelivery) { ?>
			if (E)
				if (!start) E.style.display = 'none';
				else if (bookmark == 1) E.style.display = (show ? '' : 'none');
<?			} else { ?>
			if (E) E.style.display = (show ? '' : 'none');
<?			} ?>
			if (E_map) E_map.style.display = E.style.display;
		}

		var E = document.getElementById('edost_bookmark_delimiter');
		if (E) E.className = 'edost_active_fon_on';

		if (!start) {
			var E = document.getElementById('edost_bookmark_loading');
			if (E) {
				E.innerHTML = '<?=$ico_loading?> <span class="edost_description"><?=$sign['loading2']?></span>';
				E.style.display = 'block';
			}

			var E = document.getElementById('edost_bookmark_info');
			if (E) E.style.display = 'none';

			E = document.getElementById('edost_bookmark');
			if (E) E.value = id + '_s';

<?			if (!$edost_catalogdelivery) { ?>
			<?=$calculate_function?>;
<?			} ?>
		}

<?		if ($edost_catalogdelivery && $map_inside == 'Y') { ?>
		if (id == 'office') edost_MapInside();
<?		} ?>

<?		if ($edost_catalogdelivery && $mode != 'manual') { ?>
		edost_catalogdelivery.position('update');
<?		} ?>
	}

<? if (!$edost_catalogdelivery && !empty($data['map_inside'])) { ?>
	if (window.edost_office2 && edost_office2.timer_inside == false) {
		edost_office2.timer_inside = window.setInterval('edost_MapInside()', 500);
	}
<? } ?>
</script>

<? if (!empty($data['format']['data'])) { ?>
<div id="edost_delivery_div" class="edost edost_main <?=(!$edost_catalogdelivery ? ' edost_template_div' : '')?><?=$resize['cod']?><?=$resize['delivery']?><?=$resize['bookmark_cod']?><?=$resize['delimiter']?><?=$resize['bookmark']?><?=$resize['map']?>">

<?
	$border = (!empty($data['format']['border']) ? true : false);
	$cod = (!empty($data['format']['cod']) ? true : false);
	$cod_bookmark = (!empty($data['format']['cod_bookmark']) ? true : false);
	$top = ($border ? 15 : 40);
	$hide_radio = ($data['format']['count'] == 1 ? true : false);
	$cod_tariff = (!empty($data['format']['cod_tariff']) ? true : false);

	$ico_width = ($hide_radio || $edost_catalogdelivery ? '70' : '95');

	if ($data['format']['priceinfo']) $table_width = 645;
	else $table_width = ($data['format']['day'] ? 620 : 570);

	$day_width = ($data['format']['day'] ? 80 : 10);
	$price_width = 85;
	$cod_width = 90;

	$bookmark = (!empty($data['format']['bookmark']) ? $data['format']['bookmark'] : '');
	$bookmark_id = (!empty($data['format']['active']['bookmark']) ? $data['format']['active']['bookmark'] : '');

	if ($cod_tariff) {
		$sign['price_head'] = '<span class="edost_payment_normal">'.str_replace('<br>', ' ', $sign['price_head']).'</span>';
		$sign['cod_head'] = '<span class="edost_payment_cod">'.str_replace('<br>', ' ', $sign['cod_head']).'</span>';
	}
?>


<? if (!$edost_catalogdelivery) { ?>
	<h4><?=($bookmark != '' ? 'Способ доставки' : 'Служба доставки')?></h4>
<? } ?>


<?	if ($bookmark != '') { ?>
	<div id="edost_bookmark_div">
	<input id="edost_bookmark" name="edost_bookmark" value="<?=$bookmark_id?>" type="hidden">

	<div id="edost_bookmark_tariff2" class="edost_format edost_resize_bookmark2" style="max-width: <?=($bookmark == 1 ? 400 : 500)?>px;">
<?
		$i2 = 0;
		foreach ($data['format']['data'] as $f_key => $f) if ($bookmark !== 2 || $f_key !== 'general') {
			$id = 'edost_bookmark_input_'.$f_key;
			$cod_head = ($cod_bookmark && ($bookmark == 1 && $f['cod'] || $bookmark == 2 && (!$cod_tariff && isset($f['min']['pricecash']) || $f['min']['cod_tariff'])) ? true : false);

			if ($i2 != 0) echo '<div name="edost_delimiter_s" class="edost_delimiter edost_delimiter_ms'.($edost_catalogdelivery ? '2' : '').'"></div>';
			$i2++;

			$row = $resize['ico_row'];
			if ($row == 'auto') {
				$row = 1;
				if ($cod_head) $row++;
			}
?>
		<table class="edost_format_tariff" width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="edost_resize_ico<?=$resize['ico'][0]?>" width="<?=($ico_width - $resize['ico'][1])?>" data-width="<?=$ico_width?>" rowspan="<?=$row?>">
					<input class="edost_format_radio" <?=($hide_radio ? 'style="display: none;"' : '')?> type="radio" id="<?=$id?>" name="edost_bookmark_input" value="<?=$id?>" <?=($f_key == $bookmark_id ? 'checked="checked"' : '')?> onclick="edost_SetBookmark('<?=$f_key?>')">

<?					if ($ico !== false) { ?>
					<label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico" src="<?=$ico_path.'/'.$f_key.'.gif'?>" border="0"></label>
<?					} else { ?>
					<div class="edost_ico"></div>
<?					} ?>
				</td>

				<td class="edost_format_tariff">
					<label for="<?=$id?>">
					<span class="edost_format_tariff"><?=$f['head']?></span>
					</label>

<?					if ($cod_head) { ?>
					<div class="<?=($bookmark == 2 ? 'edost_resize_description2' : 'edost_resize_bookmark_cod')?>"><span class="edost_price_head edost_payment"><?=$cod_head_bookmark2?></span></div>
<?					} ?>
				</td>

<?				if ($f_key != 'show') { ?>

<?				if ($bookmark == 2) { ?>
				<td class="edost_format_price edost_resize_day" width="<?=$day_width?>" align="center">
<?					if (!empty($f['min']['day'])) { ?>
					<span class="edost_format_price edost_day"><?=(!empty($f['min']['day']) ? $f['min']['day'] : '')?></span>
<?					} ?>
				</td>
<?				} ?>

				<td class="edost_format_price" width="<?=($price_width + 30)?>" align="right">
					<label for="<?=$id?>">

<?					if (isset($f['free']) || isset($f['min']['free'])) { ?>
					<span class="edost_format_price edost_price_free" style=""><?=(isset($f['free']) ? $f['free'] : $f['min']['free'])?></span>
<?					} else if (isset($f['price_formatted']) || isset($f['min']['price_formatted'])) { ?>
					<span class="edost_format_price edost_price"<?=(isset($f['price_formatted']) ? ' style="color: #888;"' : '')?>><?=(isset($f['price_formatted']) ? $f['price_formatted'] : $f['min']['price_formatted'])?></span>
<?					} ?>

<?					if (!empty($f['min']['day'])) { ?>
					<div class="edost_resize_day2"><span class="edost_format_price edost_day"><?=(!empty($f['min']['day']) ? $f['min']['day'] : '')?></span></div>
<?					} ?>

					</label>
				</td>
<?				} ?>
			</tr>
<?			if ($cod_head) { ?>
			<tr>
				<td class="<?=($bookmark == 2 ? 'edost_resize_description' : 'edost_resize_bookmark_cod2')?>" colspan="5">
					<span class="edost_price_head edost_payment"><?=$cod_head_bookmark2?></span>
				</td>
			</tr>
<?			} ?>

<?
		if ($bookmark == 2) if (!empty($f['min'])) {
			$v = $f['min'];

			$description = array();
			if (!empty($f['description'])) $description[] = $f['description'];
			if (!empty($v['description'])) $description[] = $v['description'];

			$warning = array();
			if (!empty($f['warning'])) $warning[] = $f['warning'];
			if (!empty($v['error'])) $warning[] = $v['error'];
			if (!empty($v['warning'])) $warning[] = $v['warning'];
?>
<?			if (!empty($description) || !empty($warning) || !empty($v['office_address'])) { ?>
			<tr name="edost_description">
				<td colspan="5">
<?					if (!empty($v['office_address'])) { ?>
					<div>
						<span class="edost_format_address_head"><?=$sign['address2']?>: </span>
						<span class="edost_format_address"><?=$v['office_address']?></span>
						<a class="edost_link" href="http://www.edost.ru/office.php?c=<?=$v['office_id']?>" target="_blank"><?=$sign['map']?></a>
					</div>
<?					} ?>

<?					if (!empty($warning)) { ?>
					<div class="edost_format_description edost_warning"><?=implode('<br>', $warning)?></div>
<?					} ?>

<?					if (!empty($description)) { ?>
					<div class="edost_format_description edost_description"><?=implode('<br>', $description)?></div>
<?					} ?>
				</td>
			</tr>
<?			} ?>
<?
		}
?>
		</table>
<?		} ?>
	</div>


	<div id="edost_bookmark_tariff" class="edost_resize_bookmark">
	<table class="edost_bookmark" cellpadding="0" cellspacing="0" border="0">
		<tr>
<?		foreach ($data['format']['data'] as $f_key => $f) if ($bookmark !== 2 || $f_key !== 'general') { $id = $f_key; ?>
			<td id="edost_<?=$id?>_td" class="edost_active" width="110" style="padding-bottom: 5px;" onclick="edost_SetBookmark('<?=$id?>')">
				<img src="<?=$ico_path.'/'.$f_key.'.gif'?>" border="0">
				<br>
				<span class="edost_bookmark"><?=$f['head']?></span>
				<br>
<?			if ($f_key != 'show') { ?>
				<div>
<?					if (isset($f['free']) || isset($f['min']['free'])) { ?>
					<span class="edost_format_price edost_price_free" style=""><?=(isset($f['free']) ? $f['free'] : $f['min']['free'])?></span>
<?					} else if (isset($f['price_formatted']) || isset($f['min']['price_formatted'])) { ?>
					<span class="edost_format_price edost_price"<?=(isset($f['price_formatted']) ? ' style="color: #888;"' : '')?>><?=(isset($f['price_formatted']) ? $f['price_formatted'] : $f['min']['price_formatted'])?></span>
<?					} ?>

<?					if (!empty($f['min']['day'])) { ?>
					<br><span class="edost_format_price edost_day"><?=(!empty($f['min']['day']) ? $f['min']['day'] : '')?></span>
<?					} ?>

<?					if ($cod_bookmark && ($bookmark == 1 && $f['cod'] || $bookmark == 2 && (!$cod_tariff && isset($f['min']['pricecash']) || $f['min']['cod_tariff']))) { ?>
						<br><span class="edost_price_head edost_payment"><?=$sign['cod_head_bookmark']?></span>
<?					} ?>
				</div>
<?			} ?>
			</td>
			<td width="25"></td>
<?		} ?>
		</tr>
<?		if ($bookmark == 1) { ?>
		<tr>
<?			foreach ($data['format']['data'] as $f_key => $f) { $id = $f_key; ?>
			<td id="edost_<?=$id?>_td_bottom" style="height: 10px;"></td>
			<td></td>
<?			} ?>
		</tr>
		<tr>
			<td id="edost_bookmark_delimiter" colspan="10" style="height: 5px;"></td>
		</tr>
<?		} ?>
	</table>
	</div>


<?	if (!$edost_catalogdelivery) { ?>
	<div id="edost_bookmark_loading" style="padding-top: 20px; display: none;"></div>
<?	} ?>
<?	if ($bookmark_id == 'show') echo '<div style="height: 20px;"></div>'; ?>
	</div>
<?	} ?>

<?
	if ($bookmark == 2 && $bookmark_id != '' && $bookmark_id != 'show') foreach ($data['format']['data'] as $f_key => $f) if (!empty($f['tariff'])) foreach ($f['tariff'] as $v) if (!empty($v['checked'])) {
		$description = array();
		if (!empty($f['description'])) $description[] = $f['description'];
		if (!empty($v['description'])) $description[] = $v['description'];

		$warning = array();
		if (!empty($f['warning'])) $warning[] = $f['warning'];
		if (!empty($v['error'])) $warning[] = $v['error'];
		if (!empty($v['warning'])) $warning[] = $v['warning'];

		if (!empty($description) || !empty($warning) || !empty($v['office_address'])) {
			echo '<div id="edost_bookmark_info" class="edost_resize_bookmark" style="margin-top: 15px; padding: 12px 12px 0 12px; border-color: #DD8; border-style: solid; border-width: 1px 0; background: #FFD;">';
?>
<?			if (!empty($v['office_address'])) { ?>
				<div style="padding-bottom: 12px;">
					<span class="edost_format_address_head"><?=$sign['address2']?>: </span>
					<span class="edost_format_address"><?=$v['office_address']?></span>
					<a class="edost_link" href="http://www.edost.ru/office.php?c=<?=$v['office_id']?>" target="_blank"><?=$sign['map']?></a>
				</div>
<?			} ?>
<?
			if (!empty($warning)) echo '<div class="edost_warning edost_format_info">'.implode('<br>', $warning).'</div>';
			if (!empty($description)) echo '<div class="edost_format_info">'.implode('<br>', $description).'</div>';
			echo '</div>';
		}
	}
?>


<?	if ($bookmark == 1) { ?>
	<div class="edost_format edost_resize_bookmark2">
		<div class="edost_template_div"></div>
		<h4>Тариф доставки</h4>
	</div>
<?	} ?>


	<div id="edost_tariff_div">
<?
	$i = 0;
	foreach ($data['format']['data'] as $f_key => $f) if (!empty($f['tariff'])) {
		$display = ($bookmark == 1 && $bookmark_id != $f_key || $bookmark == 2 && $bookmark_id != 'show' ? ' display: none;' : '');
		$map = ($map_inside == 'Y' && $f_key == 'office' ? true : false);
		$cod_td = ($cod && ($f['cod'] || $border) ? true : false);

//		if ($map) $w = 'width: 100%';
//		else
		$w = 'max-width: '.($table_width - ($cod_td ? 0 : $cod_width)).'px';

		if ($bookmark == 1) $head = '';
		else $head = ($f['head'] != '' ? '<div class="edost_format_head">'.$f['head'].':'.'</div>' : '');

		if ($map) {
?>
	<div id="edost_<?=$f_key?>_map_div" class="edost_resize_map<?=(!$border || $f['head'] == '' ? ' edost_format' : ' edost_format_border')?>" style="100%; margin: <?=($i != 0 && $bookmark != 1 ? $top.'px' : '0')?> 0 0 0;<?=$display?>">
<?
		if ($head != '') {
			echo '<div id="edost_format_head_div">';
			echo $head.'<div style="padding: 8px 0 0 0;"></div>';
			echo '<div style="padding: 3px 0 0 0;"></div>';
			echo '</div>';
		}

		echo '<div id="edost_office_inside" class="edost_office_inside edost_resize_map" style="height: 450px;"></div>';
		echo '<div id="edost_office_detailed" class="edost_office_detailed edost_resize_map"><span class="edost_format_link_big" onclick="edost_office.window(\'all\');">'.$sign['detailed_office'].'</span></div>';
?>
	</div>
<?		} ?>

	<div id="edost_<?=$f_key?>_div" class="<?=($map ? 'edost_resize_map2 ' : '')?><?=(!$border || $f['head'] == '' ? ' edost_format' : ' edost_format_border')?>" style="<?=$w?>; margin: <?=($i != 0 && $bookmark != 1 ? $top.'px' : '0')?> 0 0 0;<?=$display?>">
<?
		$i++;

//		if ($bookmark == 1 && !$map) echo '<div class="edost_resize_bookmark" style="height: 8px;"></div>';
		if ($bookmark == 1) echo '<div class="edost_resize_bookmark" style="height: 8px;"></div>';

//		if ($cod && $f['cod'] && !$map) {
		if ($cod && $f['cod']) {
			echo '<div'.($head == '' ? ' class="edost_resize_cod"' : '').'>';
			echo '<table class="edost_format_head" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
			echo '<td>'.($head != '' ? $head : '&nbsp;').'</td>';
			echo '<td class="edost_format_head edost_resize_cod" width="'.$price_width.'"><span class="edost_price_head edost_price_head_color">'.$sign['price_head'].'</span></td>';
			echo '<td class="edost_format_head edost_resize_cod" width="'.$cod_width.'"><span class="edost_price_head edost_payment">'.$sign['cod_head'].'</span></td>';
			echo '</tr></table>';
			echo '<div style="padding: 8px 0 0 0;"></div>';
			echo '</div>';
		}
		else if ($head != '') {
			echo '<div id="edost_format_head_div">';
			echo $head.'<div style="padding: 8px 0 0 0;"></div>';
			echo '<div style="padding: 3px 0 0 0;"></div>';
			echo '</div>';
		}

		if ($f['warning'] != '') echo '<div class="edost_warning edost_format_info">'.$f['warning'].'</div>';
		if ($f['description'] != '') echo '<div class="edost_format_info">'.$f['description'].'</div>';
		if ($f['insurance'] != '') echo '<div class="edost_format_info"><span class="edost_insurance">'.$f['insurance'].'</span></div>';

		$i2 = 0;
		foreach ($f['tariff'] as $v) {
			if (isset($v['delimiter'])) {
				echo '<div class="'.(!$edost_catalogdelivery ? 'edost_resize_delimiter ' : '').'edost_delimiter edost_delimiter_mb'.($edost_catalogdelivery ? '2' : '').'"></div>';
				if (!$edost_catalogdelivery) echo '<div class="edost_resize_delimiter2 edost_delimiter edost_delimiter_mb2"></div>';
				$i2 = 0;
				continue;
			}

			$display = (!$map && isset($v['office_mode']) && ($map_inside == 'Y' || $map_inside == 'tariff' && empty($v['checked_inside'])) ? ' style="display: none;"' : '');

			if ($i2 != 0) {
				echo '<div'.($map || $map_inside == '' || !isset($v['office_mode']) ? '' : ' id="edost_delimiter_'.$f_key.'" style="display: none;"').'>';
				echo '<div class="'.(!$edost_catalogdelivery ? 'edost_resize_delimiter ' : '').'edost_delimiter edost_delimiter_ms'.($edost_catalogdelivery ? '2' : '').'"></div>';
				if (!$edost_catalogdelivery) echo '<div class="edost_resize_delimiter2 edost_delimiter edost_delimiter_mb2"></div>';
				echo '</div>';
			}
			$i2++;

			$id = 'ID_DELIVERY_'.$v['html_id'];
			$value = $v['html_value'];
			$office_map = (isset($v['office_map']) ? $v['office_map'] : '');
			$onclick = ($office_map == 'get' ? "edost_office.window('".$v['office_mode']."', true);" : $calculate_function);
			$price_long = (isset($v['price_long']) ? $v['price_long'] : '');

			if (isset($v['ico']) && $v['ico'] !== '') $ico = (strlen($v['ico']) <= 3 ? $ico_path.'/'.$v['ico'].'.gif' : $v['ico']);
			else $ico = (!empty($ico_default) ? $ico_default : false);

			if (isset($v['office_mode']) && $office_map == 'get' && !empty($sign['office_description'][$v['office_mode']])) $v['description'] = $sign['office_description'][$v['office_mode']];
			if (isset($v['office_mode'])) echo '<div id="edost_address_'.$v['office_mode'].'_loading"></div>';

			$row = $resize['ico_row'];
			if ($row == 'auto') {
				$row = 1;
				if (isset($v['company_head'])) $row++;
				if (!empty($v['office_address'])) $row++;
			}

			if (isset($v['free'])) {
			    $w = explode('_', $v['html_value']);
				if (isset($w[1]) && in_array($w[1], $no_free) || !empty($v['description']) && strpos($v['description'], '[no_free]') !== false) {
					if (!empty($v['description'])) $v['description'] = str_replace('[no_free]', '', $v['description']);
					$v['free'] = '';
				}
			}
			if (!empty($v['description']) && strpos($v['description'], '[address=') !== false) {
				$s = explode('[address=', $v['description']);
				$v['description'] = $s[0];
			}

?>
		<table class="edost_format_tariff" <?=($office_map != '' && isset($v['office_mode']) ? 'id="edost_address_'.$v['office_mode'].'"' : '')?> width="100%" cellpadding="0" cellspacing="0" border="0"<?=$display?>>
			<tr>
				<td class="edost_resize_ico<?=$resize['ico'][0]?>" width="<?=($ico_width - $resize['ico'][1])?>" data-width="<?=$ico_width?>" rowspan="<?=$row?>">
<?					if (!$edost_catalogdelivery) { ?>
					<input class="edost_format_radio" <?=($hide_radio ? 'style="display: none;"' : '')?> type="radio" id="<?=$id?>" name="DELIVERY_ID" value="<?=$value?>" <?=(!empty($v['checked']) ? 'checked="checked"' : '')?> onclick="<?=$onclick?>">
<?					} ?>

<?					if ($ico !== false) { ?>
					<label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico" src="<?=$ico?>" border="0"></label>
<?					} else { ?>
					<div class="edost_ico"></div>
<?					} ?>
				</td>

				<td class="edost_format_tariff">
					<label for="<?=$id?>">
					<span class="edost_format_tariff"><?=(isset($v['head']) ? $v['head'] : $v['company'])?></span>
<?					if ($v['name'] != '' && !isset($v['company_head'])) { ?>
					<span class="edost_format_name"> (<?=$v['name']?>)</span>
<?					} ?>

<?					if ($v['insurance'] != '' && (!$cod_tariff || empty($v['cod_tariff']))) { ?>
					<br><span class="edost_insurance"><?=$v['insurance']?></span>
<?					} ?>

<?					if ($cod_tariff && $office_map == 'get' && isset($v['pricecod']) && $v['pricecod'] >= 0) { ?>
					<br><span class="edost_price_head edost_payment"><?=$cod_head_bookmark2?></span>
<?					} ?>

<?					if ($cod_tariff && $v['automatic'] == 'edost' && $v['profile'] != 0 && ($office_map == '' || !empty($v['office_address']))) { ?>
						<br><?=(empty($v['cod_tariff']) ? $sign['price_head'] : $sign['cod_head'])?>
<?					} ?>
					</label>


<?					if (isset($v['company_head'])) { ?>
					<div class="edost_resize_description2" style="<?=($cod_tariff ? ' padding-top: 2px;"' : '')?>">
						<span class="edost_format_company_head"><?=$v['company_head']?>: </span>
						<span class="edost_format_company"><?=$v['company']?></span>
						<?=($v['name'] != '' ? '<span class="edost_format_company_name"> ('.$v['name'].')</span>' : '')?>
					</div>
<?					} ?>
<?					if (!empty($v['office_address'])) { ?>
					<div class="edost_resize_description2" style="<?=($cod_tariff && $office_map != 'get' ? ' padding-top: 2px;' : '')?>">
						<span class="edost_format_address_head"><?=$sign['address']?>: </span>
						<span class="edost_format_address"><?=$v['office_address']?></span>

<?						if ($office_map == 'change') { ?>
						<br><span class="edost_format_link" onclick="edost_office.window('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
<?						} else { ?>
						<a class="edost_link" href="http://www.edost.ru/office.php?c=<?=$v['office_id']?>" target="_blank"><?=$v['office_link']?></a>
<?						} ?>
					</div>
<?					} ?>


<?					if ($office_map == 'get') { ?>
					<br><span class="edost_format_link_big<?=(!empty($v['office_link2']) ? ' edost_resize_day' : '')?>" onclick="edost_office.window('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
<?					if (!empty($v['office_link2'])) { ?>
					<span class="edost_format_link_big edost_resize_day2" onclick="edost_office.window('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link2']?></span>
<?					} ?>
<?					} ?>
				</td>

<?				if (!isset($v['error'])) { ?>

<?				if ($price_long === '') { ?>
				<td class="edost_format_price edost_resize_day" width="<?=$day_width?>" align="center">
					<label for="<?=$id?>"><span class="edost_format_price edost_day"><?=(!empty($v['day']) ? $v['day'] : '')?></span></label>
				</td>
<?				} ?>

				<td class="edost_format_price" width="<?=($price_width + ($price_long !== '' ? 30 : 0))?>" align="right">
<?					if (!in_array($v['html_id'], $tariff_price_hide)) { ?>
					<label for="<?=$id?>">
<?					if (isset($v['free'])) { ?>
					<span class="edost_format_price edost_price_free" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=$v['free']?></span>
<?					} else { ?>
					<span class="edost_format_price edost_price" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=(isset($v['priceinfo_formatted']) ? $v['priceinfo_formatted'] : $v['price_formatted'])?></span>
<?					} ?>

<?					if ($price_long === '') { ?>
					<div class="edost_resize_day2"><span class="edost_format_price edost_day"><?=(!empty($v['day']) ? $v['day'] : '')?></span></div>
<?					} ?>

					</label>
<?					} ?>
				</td>

<?				if ($cod_td) { ?>
				<td class="edost_format_price edost_resize_cod" width="<?=$cod_width?>" align="right">
<?					if (isset($v['pricecod']) && $v['pricecod'] >= 0) { ?>
					<label for="<?=$id?>"><span class="edost_price_head edost_payment"><?=(isset($v['cod_free']) ? $v['cod_free'] : $v['pricecod_formatted'])?></span></label>
<?					} ?>
				</td>
<?				} ?>

<?				} ?>
			</tr>

<?			if (isset($v['company_head'])) { ?>
			<tr class="edost_resize_description">
				<td colspan="5"<?=($cod_tariff ? ' style="padding-top: 2px;"' : '')?>>
					<span class="edost_format_company_head"><?=$v['company_head']?>: </span>
					<span class="edost_format_company"><?=$v['company']?></span>
					<?=($v['name'] != '' ? '<span class="edost_format_company_name"> ('.$v['name'].')</span>' : '')?>
				</td>
			</tr>
<?			} ?>

<?			if (!empty($v['office_address'])) { ?>
			<tr class="edost_resize_description">
				<td colspan="5"<?=($cod_tariff && $office_map != 'get' ? ' style="padding-top: 2px;"' : '')?>>
					<span class="edost_format_address_head"><?=$sign['address']?>: </span>
					<span class="edost_format_address"><?=$v['office_address']?></span>

<?					if ($office_map == 'change') { ?>
					<br><span class="edost_format_link" onclick="edost_office.window('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
<?					} else { ?>
					<a class="edost_link" href="http://www.edost.ru/office.php?c=<?=$v['office_id']?>" target="_blank"><?=$v['office_link']?></a>
<?					} ?>
				</td>
			</tr>
<?			} ?>

<?			if (!empty($v['description']) || !empty($v['warning']) || !empty($v['error'])) { ?>
			<tr name="edost_description">
				<td colspan="5">
<?					if (!empty($v['error'])) { ?>
					<div class="edost_format_description edost_warning"><b><?=$v['error']?></b></div>
<?					} ?>

<?					if (!empty($v['warning'])) { ?>
					<div class="edost_format_description edost_warning"><?=$v['warning']?></div>
<?					} ?>

<?					if (!empty($v['description'])) { ?>
					<div class="edost_format_description edost_description"><?=nl2br($v['description'])?></div>
<?					} ?>
				</td>
			</tr>
<?			} ?>
		</table>

<?
/* ==================== edost.locations (ввод адреса) */
if (!empty($v['checked']) && !empty($address_inside) && empty($address_hide)) {
	$address_disable = true;
?>
<style>
	div.edost_address_delimiter { display: block; padding-top: 8px; }
</style>

<div id="edost_location_address_head" style="padding: 15px 0;">
	<span style="font-weight: bold; color: #884;">Адрес доставки:</span>
	<div style="margin-top: 3px; padding: 12px; border-color: #DD8; border-style: solid; border-width: 1px 0; background: #FFD;">
<?
	$edost_locations_param['PARAM']['address_field']['street']['width'] = 280;
	$GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'address') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y'));
?>
	</div>
</div>
<?
}
/* ==================== edost.locations */
?>

<?		} ?>
	</div>
<?	} ?>
	</div>


<?	if (!$edost_catalogdelivery && isset($arResult['BUYER_STORE'])) { ?>
	<input name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult['BUYER_STORE']?>" type="hidden">
<?	} ?>

<?	if (!empty($data['format']['active']['id'])) { ?>
	<input id="edost_delivery_id" value="<?=$data['format']['active']['id']?>" type="hidden">
<?	} ?>

<?	if (!empty($data['format']['map_json'])) { ?>
	<input id="edost_office_data" autocomplete="off" value='{"ico_path": "<?=$ico_path?>", <?=$data['format']['map_json']?>}' type="hidden">
<?	} ?>

<?	if ($edost_catalogdelivery && $map_inside != '') { ?>
	<script type="text/javascript">
		if (window.edost_office) edost_office.map = false;
		if (window.edost_office2) edost_office2.map = false;
<?		if ($map_inside == 'Y' && $bookmark == '') { ?>
		edost_MapInside();
<?		} ?>
	</script>
<?	} ?>

<? if (isset($ico_loading_map_inside)) { ?>
	<script type="text/javascript">
		if (window.edost_office2) edost_office2.loading_inside = '<?=$ico_loading_map_inside?>';
	</script>
<? } ?>

<? if ($bookmark != '') { ?>
	<script type="text/javascript">
		edost_SetBookmark('start', '<?=$bookmark?>');
	</script>
<? } ?>

</div>
<? } ?>

<? } ?>
<? /* шаблон edost - КОНЕЦ */ ?>




<? /* шаблон bitrix (на базе 16) - НАЧАЛО */ ?>
<? if (!isset($arResult['edost']['format'])) { ?>

<?
  if (isset($arResult['edost']['javascript'])) echo $arResult['edost']['javascript'];
  if (isset($arResult['edost']['warning']) && empty($arResult['edost']['locations_installed'])) echo '<br>'.$arResult['edost']['warning'].'<br>';
?>

<script type="text/javascript">
	function fShowStore(id, showImages, formWidth, siteId)
	{
		var strUrl = '<?=$templateFolder?>' + '/map.php';
		var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;

		var storeForm = new BX.CDialog({
					'title': '<?=GetMessage('SOA_ORDER_GIVE')?>',
					head: '',
					'content_url': strUrl,
					'content_post': strUrlPost,
					'width': formWidth,
					'height':450,
					'resizable':false,
					'draggable':false
				});

		var button = [
				{
					title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
					id: 'crmOk',
					'action': function ()
					{
						GetBuyerStore();
						BX.WindowManager.Get().Close();
					}
				},
				BX.CDialog.btnCancel
			];
		storeForm.ClearButtons();
		storeForm.SetButtons(button);
		storeForm.Show();
	}

	function GetBuyerStore()
	{
		BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
		//BX('ORDER_DESCRIPTION').value = '<?=GetMessage("SOA_ORDER_GIVE_TITLE")?>: '+BX('POPUP_STORE_NAME').value;
		BX('store_desc').innerHTML = BX('POPUP_STORE_NAME').value;
		BX.show(BX('select_store'));
	}

	function showExtraParamsDialog(deliveryId)
	{
		var strUrl = '<?=$templateFolder?>' + '/delivery_extra_params.php';
		var formName = 'extra_params_form';
		var strUrlPost = 'deliveryId=' + deliveryId + '&formName=' + formName;

		if(window.BX.SaleDeliveryExtraParams)
		{
			for(var i in window.BX.SaleDeliveryExtraParams)
			{
				strUrlPost += '&'+encodeURI(i)+'='+encodeURI(window.BX.SaleDeliveryExtraParams[i]);
			}
		}

		var paramsDialog = new BX.CDialog({
			'title': '<?=GetMessage('SOA_ORDER_DELIVERY_EXTRA_PARAMS')?>',
			head: '',
			'content_url': strUrl,
			'content_post': strUrlPost,
			'width': 500,
			'height':200,
			'resizable':true,
			'draggable':false
		});

		var button = [
			{
				title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
				id: 'saleDeliveryExtraParamsOk',
				'action': function ()
				{
					insertParamsToForm(deliveryId, formName);
					BX.WindowManager.Get().Close();
				}
			},
			BX.CDialog.btnCancel
		];

		paramsDialog.ClearButtons();
		paramsDialog.SetButtons(button);
		//paramsDialog.adjustSizeEx();
		paramsDialog.Show();
	}

	function insertParamsToForm(deliveryId, paramsFormName)
	{
		var orderForm = BX("ORDER_FORM"),
			paramsForm = BX(paramsFormName);
			wrapDivId = deliveryId + "_extra_params";

		var wrapDiv = BX(wrapDivId);
		window.BX.SaleDeliveryExtraParams = {};

		if(wrapDiv)
			wrapDiv.parentNode.removeChild(wrapDiv);

		wrapDiv = BX.create('div', {props: { id: wrapDivId}});

		for(var i = paramsForm.elements.length-1; i >= 0; i--)
		{
			var input = BX.create('input', {
				props: {
					type: 'hidden',
					name: 'DELIVERY_EXTRA['+deliveryId+']['+paramsForm.elements[i].name+']',
					value: paramsForm.elements[i].value
					}
				}
			);

			window.BX.SaleDeliveryExtraParams[paramsForm.elements[i].name] = paramsForm.elements[i].value;

			wrapDiv.appendChild(input);
		}

		orderForm.appendChild(wrapDiv);

		BX.onCustomEvent('onSaleDeliveryGetExtraParams',[window.BX.SaleDeliveryExtraParams]);
	}

	if(typeof submitForm === 'function')
		BX.addCustomEvent('onDeliveryExtraServiceValueChange', function(){ submitForm(); });

</script>

<style>
	div.edost_main { font-family: arial; line-height: normal; margin: 45px 0 20px 0; }
	div.edost_main h4 { border-bottom: 1px solid #DCDFE2; display: block; margin-bottom: 10px; padding-bottom: 5px; }
	div.edost_warning { color: #F00; }
	div.edost_warning_big { font-size: 14px; font-weight: bold; }

	.bx_element label { margin: 0; }

	div.visual_normal td.visual_ico { width: 110px; }
	div.visual_normal span.visual_title { font-size: 17px; }
	div.visual_normal .visual_price2 { display: none; }

	div.visual_small div.visual_delimiter { border-width: 1px 0 0 0; border-color: #CCC; border-style: solid; margin: 10px 0 0 0; }
	div.visual_small .bx_logotype { margin-right: 10px; }
	div.visual_small .bx_logotype span { width: 47px; height: 27px; }
	div.visual_small td.visual_ico { width: 50px; }
	div.visual_small span.visual_title { font-size: 15px; }
	div.visual_small .visual_price { display: none; }

	td.visual_ico { vertical-align: top; }
	td.visual_price2 { text-align: right; }
	table.edost_office_table { line-height: normal; }

	#EdostPickPointRef { font-size: 13px; }
	table.edost_office_table { color: #000; font-size: 13px; }
	table.edost_office_table select { height: 25px; padding: 2px !important; }
	strong.bitrix_title { font-size: 14px; }
	div.bitrix_description { overflow: hidden; margin-top: 5px; }
	div.bitrix_description p { line-height: normal !important; }
	.bx_order_make .bx_logotype span { width: 95px; height: 55px; background-size: contain;	}
	.bx_element input[type="radio"]:checked + label .bx_logotype, .bx_element label.selected .bx_logotype { border: 4px solid #00AAFF !important; padding: 1px; }
	.bx_order_make .bx_logotype { border: 4px solid rgba(255, 255, 255, 0.1); padding: 1px; }
	.bx_order_make .bx_logotype.active, .bx_order_make .bx_logotype:hover { border: 4px solid #AAE2FF; padding: 1px; }
	div.bx_section label { font-weight: bold; }
</style>

<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult['BUYER_STORE']?>" />
<div class="bx_section<?=$resize['section']?>" style="padding-top: 10px;">
<?	if (!empty($arResult["DELIVERY"])) {
		$width = ($arParams['SHOW_STORES_IMAGES'] == 'Y' ? 850 : 700);
?>
		<h4><?=GetMessage('SOA_TEMPL_DELIVERY')?></h4>
<?
		$i2 = 0;
		foreach ($arResult['DELIVERY'] as $delivery_id => $arDelivery) {
			if ($i2 != 0) echo '<div class="visual_delimiter">&nbsp;</div>';
			$i2++;

			$profile = (isset($arDelivery['profile']) ? $arDelivery['profile'] : false);
			$draw_price = ($profile !== false || $profile === false && isset($arResult['edost']['config']['template']) && $arResult['edost']['config']['template'] == 'N2' ? true : false);

			$price = false;
			if (isset($arDelivery['price'])) {
				if (!empty($arDelivery['price'])) $price = $arDelivery['price'];
			}
			else if ($draw_price && !empty($arDelivery['PRICE_FORMATED'])) $price = $arDelivery['PRICE_FORMATED'];

			$draw_day = $draw_price;

			if (in_array($delivery_id, $no_free) || !empty($arDelivery['DESCRIPTION']) && strpos($arDelivery['DESCRIPTION'], '[no_free]') !== false) {
				if (!empty($arDelivery['DESCRIPTION'])) $arDelivery['DESCRIPTION'] = str_replace('[no_free]', '', $arDelivery['DESCRIPTION']);
				if ($price !== false && !isset($arDelivery['CALCULATE_ERRORS']) && $arDelivery['PRICE'] == 0) $draw_price = false;
			};

			if (!empty($arDelivery['DESCRIPTION']) && strpos($arDelivery['DESCRIPTION'], '[address=') !== false) {
				$s = explode('[address=', $arDelivery['DESCRIPTION']);
				$arDelivery['DESCRIPTION'] = $s[0];
			}

			if (isset($arDelivery['office']) && !empty($arDelivery['DESCRIPTION']) || strpos($arDelivery['DESCRIPTION'], '<br>') > 0) $top = 0;
			else if (isset($arDelivery['office']) || !empty($arDelivery['DESCRIPTION'])) $top = 12;
			else $top = 24;
			$top = 'padding-top: '.$top.'px;';

			if($arDelivery['ISNEEDEXTRAINFO'] == 'Y') $extraParams = 'showExtraParamsDialog("'.$delivery_id.'");';
			else $extraParams = '';

			if (!empty($arDelivery["STORE"])) $clickHandler = "onClick = \"fShowStore('".$arDelivery["ID"]."','".$arParams['SHOW_STORES_IMAGES']."','".$width."','".SITE_ID."')\";";
			else $clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;".$extraParams."submitForm();\"";

			if ($profile !== false) $deliveryImgURL = '/bitrix/images/delivery_edost_img/big/'.ceil($profile / 2).'.gif';
			else if (count($arDelivery["LOGOTIP"]) > 0) {
				$arFileTmp = CFile::ResizeImageGet($arDelivery["LOGOTIP"]["ID"], array("width" => "95", "height" =>"55"), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				$deliveryImgURL = $arFileTmp["src"];
			}
			else $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";

			$id = 'ID_DELIVERY_ID_'.$arDelivery['ID'];
?>
		<table class="edost_format_tariff" width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="bx_element visual_ico" rowspan="<?=$resize['ico_row']?>">
					<input type="radio" id="<?=$id?>" name="<?=htmlspecialcharsbx($arDelivery['FIELD_NAME'])?>" style="display: none;" value="<?=$arDelivery['ID']?>"<?if ($arDelivery['CHECKED']=='Y') echo ' checked';?> onclick="submitForm();">

					<label for="<?=$id?>">
						<div class="bx_logotype"><span style='background-image:url(<?=$deliveryImgURL?>);' <?=$clickHandler?>></span></div>
					</label>
				</td>

				<td class="edost_format_tariff">
					<label for="<?=$id?>">

					<span class="visual_title"><?=$arDelivery['NAME']?><span class="visual_price"><?=(!empty($arDelivery['PERIOD_TEXT']) && $draw_day ? '<span style="font-weight: normal;">, '.$arDelivery['PERIOD_TEXT'].'</span>' : '')?><?=($draw_price && $price !== false ? ' - '.$price : '')?></span></span>

<?					if (isset($arDelivery['CALCULATE_ERRORS'])) ShowError($arDelivery['CALCULATE_ERRORS']); ?>

					<? if ($profile === false && $price === false && !isset($arDelivery['CALCULATE_ERRORS'])) { ?>
					<div class="visual_price">
<?						if (isset($arDelivery['PRICE'])) {
							echo GetMessage('SALE_DELIV_PRICE').': <b>';
							if (!empty($arDelivery['DELIVERY_DISCOUNT_PRICE']) && round($arDelivery['DELIVERY_DISCOUNT_PRICE'], 4) != round($arDelivery['PRICE'], 4)) {
								echo (strlen($arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED']) > 0 ? $arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED'] : number_format($arDelivery['DELIVERY_DISCOUNT_PRICE'], 2, ',', ' '));
								echo '</b><br><span style="text-decoration:line-through;color:#828282;">'.(strlen($arDelivery['PRICE_FORMATED']) > 0 ? $arDelivery['PRICE_FORMATED'] : number_format($arDelivery['PRICE'], 2, ',', ' ')).'</span>';
							}
							else {
								echo (strlen($arDelivery['PRICE_FORMATED']) > 0 ? $arDelivery['PRICE_FORMATED'] : number_format($arDelivery['PRICE'], 2, ',', ' ')).'</b>';
							}
							echo '<br>';

							if (strlen($arDelivery['PERIOD_TEXT']) > 0) {
								echo 'Срок доставки: <b>'.$arDelivery['PERIOD_TEXT'].'</b>';
								echo '<br>';
							}
							if ($arDelivery['PACKS_COUNT'] > 1) {
								echo '<br>';
								echo GetMessage('SALE_SADC_PACKS').': <b>'.$arDelivery['PACKS_COUNT'].'</b>';
							}
						}
						else {
							$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
								'NO_AJAX' => $arParams['DELIVERY_NO_AJAX'],
								'DELIVERY_ID' => $delivery_id,
								'ORDER_WEIGHT' => $arResult['ORDER_WEIGHT'],
								'ORDER_PRICE' => $arResult['ORDER_PRICE'],
								'LOCATION_TO' => $arResult['USER_VALS']['DELIVERY_LOCATION'],
								'LOCATION_ZIP' => $arResult['USER_VALS']['DELIVERY_LOCATION_ZIP'],
								'CURRENCY' => $arResult['BASE_LANG_CURRENCY'],
								'ITEMS' => $arResult['BASKET_ITEMS'],
								'EXTRA_PARAMS_CALLBACK' => $extraParams,
								'ORDER_DATA' => $arResult['ORDER_DATA']
							), null, array('HIDE_ICONS' => 'Y'));
						} ?>
					</div>
					<? } ?>

					</label>
				</td>

				<td class="visual_price2" width="100">
					<label for="<?=$id?>">
						<span class="visual_title">
						<?=($price !== false ? $price.'<br>' : '')?>
						<?=(!empty($arDelivery["PERIOD_TEXT"]) && $draw_price ? '<span style="font-weight: normal;">'.$arDelivery["PERIOD_TEXT"].'</span>' : '')?>
						</span>
					</label>
				</td>
			</tr>

			<tr name="edost_description">
				<td colspan="5">
	            <?=(isset($arDelivery['office']) ? $arDelivery['office'] : '')?>

<?				if (strlen($arDelivery['DESCRIPTION']) > 0) echo '<div '.(isset($arDelivery['office']) ? 'style="padding-top: 5px;"' : '').'>'.$arDelivery['DESCRIPTION'].'</div>'; ?>

<?				if (!empty($arDelivery['STORE']) && $arDelivery['CHECKED'] == 'Y') { ?>
					<span id="select_store"<?=(strlen($arResult['STORE_LIST'][$arResult['BUYER_STORE']]['TITLE']) <= 0 ? ' style="display:none;"' : '')?>>
						<span class="select_store"><?=GetMessage('SOA_ORDER_GIVE_TITLE');?>: </span>
						<span class='ora-store' id='store_desc'><?=htmlspecialcharsbx($arResult['STORE_LIST'][$arResult['BUYER_STORE']]['TITLE'])?></span>
					</span>
<?				} ?>

<?				if ($arDelivery['CHECKED'] == 'Y') { ?>
					<table class="delivery_extra_services">
<?						foreach ($arDelivery['EXTRA_SERVICES'] as $extraServiceId => $extraService) { ?>
							<?if(!$extraService->canUserEditValue()) continue;?>
							<tr>
								<td class="name">
									<?=$extraService->getName()?>
								</td>
								<td class="control">
									<?=$extraService->getEditControl('DELIVERY_EXTRA_SERVICES['.$arDelivery['ID'].']['.$extraServiceId.']')	?>
								</td>
								<td rowspan="2" class="price">
<?									if ($price = $extraService->getPrice()) {
										echo GetMessage('SOA_TEMPL_SUM_PRICE').': ';
										echo '<strong>'.SaleFormatCurrency($price, $arResult['BASE_LANG_CURRENCY']).'</strong>';
									} ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="description">
									<?=$extraService->getDescription()?>
								</td>
							</tr>
<?						} ?>
					</table>
<?					} ?>
				</td>
			</tr>
		</table>

<?
/* ==================== edost.locations (ввод адреса) */
if (!empty($arDelivery['CHECKED']) && $arDelivery['CHECKED'] == 'Y' && !empty($address_inside) && empty($address_hide)) {
	$address_disable = true;
?>
<style>
	#edost_location_address_head * { box-sizing: content-box; }
</style>

<div id="edost_location_address_head" style="padding: 15px 0; max-width: 610px;">
	<span style="font-weight: bold; color: #884;">Адрес доставки:</span>
	<div style="margin-top: 3px; padding: 12px; border-color: #DD8; border-style: solid; border-width: 1px 0; background: #FFD;">
<?	$GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'address') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y')); ?>
	</div>
</div>
<?
}
/* ==================== edost.locations */
?>

<?
		}
	}
?>

</div>
<? } ?>
<? /* шаблон bitrix - КОНЕЦ */ ?>




<?
/* ==================== edost.locations (ввод адреса) */
if (!empty($arResult['edost']['locations_installed']) && isset($arResult['edost']['order_prop']['ADDRESS']) && empty($address_disable)) {
?>
<div class="edost_main edost_template_div"<?=($address_hide ? ' style="display: none;"' : '')?>>
	<h4 id="edost_location_address_head" class="edost_location_head"<?=($address_hide ? ' style="display: none;"' : '')?>>Адрес доставки</h4>
	<? $GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'address') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y')); ?>
</div>
<?
}
/* ==================== edost.locations */
?>




<script type="text/javascript">
		var Ed = document.getElementById('edost_office_map_div');
		if (!Ed){
			var d = document.getElementById('cdek');
			d.className += " no-marg";
		}
	if (window.edost_resize) edost_resize.start();
</script>
