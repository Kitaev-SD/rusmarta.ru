<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/include.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/prolog.php');
IncludeModuleLangFile(__FILE__);

$right = $APPLICATION->GetGroupRight('sale');
if ($right == 'D') $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

if (!class_exists('edost_class')) require_once($_SERVER['DOCUMENT_ROOT'].getLocalPath('modules/edost.delivery/classes/general/delivery_edost.php'));

$admin_sign = GetMessage('EDOST_ADMIN');
$protocol = CDeliveryEDOST::GetProtocol();
$setting_cookie = edost_class::GetCookie();
//echo '<br><b>setting_cookie:</b><pre style="font-size: 12px">'.print_r($setting_cookie, true).'</pre>';

// данные из POST и GET
$type = (!empty($_REQUEST['type']) ? preg_replace("/[^a-z|_]/i", "", substr($_REQUEST['type'], 0, 30)) : '');
if (!in_array($type, array('control', 'register', 'setting', 'paysystem', 'print'))) $type = 'setting';
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == 'Y' ? true : false);

// изменять настройки можно только при '[W] полный доступ'
if (in_array($type, array('setting', 'paysystem')) && $right != 'W') {
	echo $admin_sign['warning']['setting_denied'];
	die();
}

$history = edost_class::History();


// сохранение настроек модуля и привязок к оплате
if (in_array($type, array('setting', 'paysystem')) && !empty($_REQUEST['save'])) {
	$save = false;
	if (isset($_POST['module'])) $save = $_POST['module'];
	else if (isset($_POST['paysystem'])) { $save = $_POST['paysystem']; $type = 'paysystem'; };
	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array(
		'MODE' => 'setting',
		'ADMIN' => 'Y',
		'PARAM' => array(
			'module_id' => false,
			'save' => $save,
			'paysystem' => ($type == 'paysystem' ? true : false),
		),
	), null, array('HIDE_ICONS' => 'Y'));

	die();
}


// страница с документами на печать
if ($type == 'print') {
	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array(
		'MODE' => 'print',
		'ADMIN' => 'Y',
		'PARAM' => array(
			'mode' => (isset($_REQUEST['mode']) ? $_REQUEST['mode'] : ''),
			'doc' => (isset($_REQUEST['doc']) ? $_REQUEST['doc'] : ''),
		),
	), null, array('HIDE_ICONS' => 'Y'));

	die();
}


// обработка кнопок на странице оформления доставки
if ($type == 'register' && isset($_REQUEST['set']) && isset($_REQUEST['time']) && !isset($_REQUEST['control'])) {
	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array(
		'MODE' => 'register',
		'ADMIN' => 'Y',
		'PARAM' => array(
			'set' => $_REQUEST['set'],
			'time' => $_REQUEST['time'],
			'button' => (isset($_REQUEST['button']) ? $_REQUEST['button'] : false),
			'count' => (isset($_REQUEST['count']) ? $_REQUEST['count'] : 0),
			'batch' => (isset($_REQUEST['batch']) ? $_REQUEST['batch'] : false),
			'batch_date' => (isset($_REQUEST['batch_date']) ? $_REQUEST['batch_date'] : false),

			'call' => (isset($_REQUEST['call']) ? $_REQUEST['call'] : false),
			'profile_shop' => (isset($_REQUEST['profile_shop']) ? $_REQUEST['profile_shop'] : false),
			'profile_delivery' => (isset($_REQUEST['profile_delivery']) ? $_REQUEST['profile_delivery'] : false),

			'history' => $history,
		),
	), null, array('HIDE_ICONS' => 'Y'));

	die();
}


// ---------------------------------------------------------------------------


if (!$ajax) {
	$APPLICATION->SetTitle($admin_sign['title']['head'].': '.$admin_sign['title'][$type]);
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
}

if (!$ajax) { ?>
<style>
	div.checkbox label, div.checkbox input { vertical-align: middle; }

	span.low { color: #888; }
	span.low2 { color: #555; }
	table.standard a { text-decoration: none; }
	table.standard { border-collapse: collapse; border-color: #888; border-style: solid; border-width: 1px; }
	table.standard td { vertical-align: top; }
	tr.slim td { border-width: 1px 0px; }
	span.button { color: #00F; }
	b.orange { color: #E60; }

	div.menu { float: right; width: 200px; background: #F5F5F5; padding: 3px; text-align: center; font-size: 18px; font-weight: bold; border: 1px solid #A4B9CC; cursor: pointer; color: #5F6674; }
	div.menu:hover { background: #FFF; }

	div.head { margin-top: 20px; color2: #555; color: #006fd2; font-size: 20px; }
	div.delimiter { width: 900px; margin: 5px 0 5px 0; border-width: 1px 0 0 0; border-color: #CCC; border-style: solid; }

	span.error { padding-top: 5px; color: #F00; font-weight: bold; font-size: 14px; }
	span.note { color: #888; vertical-align: middle; }

	div.checkbox input[type="checkbox"]:checked + label { color: #000; }
	div.checkbox input[type="checkbox"] + label { color: #888; }
	div.checkbox input[type="checkbox"]:checked + label.blue { color: #00F; }
	div.checkbox input[type="checkbox"] + label.blue { color: #88F; }
	div.checkbox input[type="checkbox"]:checked + label.green { color: #080; }
	div.checkbox input[type="checkbox"] + label.green { color: #8B8; }
	div.checkbox input[type="checkbox"]:checked + label.orange { color: #E60; }
	div.checkbox input[type="checkbox"] + label.orange { color: #B98; }

	div.radio input[type="radio"]:checked + span.red { color: #A00; }
	div.radio input[type="radio"] + span.red { color: #888; }
	div.radio input[type="radio"]:checked + span.green { color: #080; }
	div.radio input[type="radio"] + span.green { color: #888; }
	div.radio input[type="radio"]:checked + span.normal { color: #000; }
	div.radio input[type="radio"] + span.normal { color: #888; }

	tr.active { background: #F0FBF0; }
	tr.normal { background: none; }

	div.link { cursor: pointer; font-size: 13px; font-weight: bold; }
	span.link { cursor: pointer; font-size: 13px; font-weight: bold; }

	div.on { color: #F00; background: #FEE; border: 1px solid #F00; }
	div.off { color: #AAA; }

	div.checkbox input.normal { background: #FFF; border: 1px solid #A4B9CC; border-radius: 0; box-shadow: none; vertical-align: baseline; padding: 0px 4px; height: 21px; }

	span.edost_module_config_head { font-size: 12px; color: #888; }
	span.edost_module_config_value { font-size: 14px; color: #555; display: block; }

	div.edost_delimiter { border-width: 1px 0 0 0; border-color: #CCC; border-style: solid; }

	div.edost_setting_button { position: sticky; position: -webkit-sticky; top: 200px; }
	b.edost_setting_param_head { width: 270px; display: inline-block; text-align: right; }
	.edost_paysystem_tariff label { font-size: 14px; }
</style>

<script type="text/javascript">

	function edost_UpdateActive(id, active, checked) {

		if (active == undefined) active = false;
		if (checked == undefined) checked = '';

		if (id == 'all') {
			var ar = BX.findChildren(BX('edost_control_data_div'), {'tag': 'input', 'type': 'checkbox'}, true);
			if (ar) for (var i = 0; i < ar.length; i++) edost_UpdateActive(ar[i].id, false, checked);
		}
		else {
			var E = BX(id);
			if (active) E.checked = (E.checked ? false : true);
			if (checked != '') E.checked = (checked == 'Y' ? true : false);
			BX(id + '_tr').className = (E.checked ? 'slim active' : 'slim normal');
		}

	}

	function edost_SetData(mode, param) {

		var post = [];
		var reload = false;

		if (mode == 'module_active') {
			var E = document.getElementById('module_active_' + param);
			if (E) {
				var E2 = document.getElementById('module_active_' + param + '_main');
				if (E2) edost.class(E2, ['edost_module_off', ''], E.checked ? 1 : 0);
			}
		}
		else if (mode == 'site') {
			var a = param.getAttribute('data-site');
			var E = param.parentNode.parentNode;
		       var s = E.querySelectorAll('input');
			if (s) for (var i = 0; i < s.length; i++) {
				var a2 = s[i].getAttribute('data-site');
				if (a === a2) continue;
				if (a === '' && param.checked) s[i].checked = false;
				if (a !== '' && a2 === '') s[i].checked = false;
			}
		}
		else if (mode == 'module_update') {
			// настройки контроля заказов
			var E = BX('module_' + param + '_control');
			if (!E) return;
			var display = (E.checked ? 'block' : 'none');
			var ar = ['control_auto', 'control_status_arrived', 'control_status_completed', 'control_status_completed_cod', 'register_status', 'browser']; // 'register_deducted'
			for (var i2 = 0; i2 < ar.length; i2++) {
				var E = BX('module_' + param + '_' + ar[i2] + '_div');
				if (E) E.style.display = display;
			}

			// настройки шаблона eDost
			var E = BX('module_' + param + '_template');

			var E2 = BX('module_' + param + '_template_ico_div');
			E2.style.display = (E.value != 'off' ? 'block' : 'none');

			var a1 = (E.value == 'Y' ? true : false);
			var a2 = (BX('module_' + param + '_template_format').value != 'off' ? true : false);
			var a3 = (BX('module_' + param + '_template_block').value != 'off' ? true : false);
			var a4 = (BX('module_' + param + '_map').checked ? true : false);
			var a5 = (BX('module_' + param + '_template_autoselect_office').checked ? true : false);

			var ar = ['format', 'block', 'block_type', 'cod', 'autoselect_office', 'map_inside'];
			for (var i = 0; i < ar.length; i++) {
				var display = (a1 && (ar[i] != 'block' && ar[i] != 'block_type' || a2) && (ar[i] != 'block_type' || a3) && (ar[i] != 'autoselect_office' || a4) && (ar[i] != 'map_inside' || a2 && a3 && a4 && !a5) ? 'block' : 'none');
				var E = BX('module_' + param + '_template_' + ar[i] + '_div');
				if (E) E.style.display = display;
			}
		}
		else if (mode == 'paysystem_active') {
			var s = param.id.split('_');
			var id = s[1];
			var type = s[2];

			var a = (BX('paysystem_' + id + '_active').checked ? true : false);
			var list = (BX('paysystem_' + id + '_list').checked ? true : false);

			BX('paysystem_' + id + '_list_span').style.display = (a ? 'inline' : 'none');
			BX('paysystem_tariff_' + id).style.display = (a && list ? 'block' : 'none');

			edost.class(BX('paysystem_' + id), ['edost_module_off', ''], a ? 1 : 0);

			if (type == 'list' && list) {
				var s = document.querySelectorAll('#paysystem_' + id + ' .paysystem_list_show');
				if (s) for (var i = 0; i < s.length; i++) s[i].click();
			}

			edost_resize.bar('timer');
		}
		else if (mode == 'paysystem_tariff_active') {
			var a = param.getAttribute('data-active');
			var s = document.querySelectorAll('#' + param.getAttribute('data-id') + '_list input');
			if (s) for (var i = 0; i < s.length; i++) s[i].checked = (a == 'Y' ? true : false);

			edost_resize.bar('timer');
		}
		else if (mode == 'paysystem_tariff_active_company') {
			var s = document.querySelectorAll('#edost_data_div .' + param.id + ' input');
			if (s) for (var i = 0; i < s.length; i++) s[i].checked = param.checked;
		}
		else if (mode == 'paysystem_tariff_show') {
			param.style.display = 'none';
			var s = document.querySelectorAll('#edost_data_div .' + param.getAttribute('data-id'));
			if (s) for (var i = 0; i < s.length; i++) s[i].style.display = 'block';

			edost_resize.bar('timer');
		}
		else if (mode == 'paysystem_list_show') {
			param.style.display = 'none';
			id = param.getAttribute('data-id');
			BX(id + '_string').style.display = 'none';
			BX(id + '_list').style.display = 'block';

			edost_resize.bar('timer');
		}
		else if (mode == 'check_Y' || mode == 'check_N') {
			edost_UpdateActive('all', false, mode == 'check_Y' ? 'Y' : 'N');
		}
		else if (mode == 'get') {
			edost_resize.bar('loading');
			post.push((param === 'paysystem' ? 'type' : 'module') + '=' + param);

			var s = '?lang=' + window.location.search.split('lang=')[1].split('&')[0];
			if (param === 'paysystem') s += '&type=' + param; else s += '&type=setting' + (param ? '&module=' + param : '');
			if (window.history && history.pushState) window.history.pushState(null, null, window.location.pathname + s);
		}
		else if (mode == 'history') {
			if (edost.get('type') === 'setting') edost_SetData('get', edost.get('module'));
//			window.location = s;
			return;
		}
		else if (mode == 'new') {
			edost_resize.bar('loading');
			post.push('module=new');
		}
		else if (mode == 'save') {
			if (BX('paysystem_error') || BX('module_error') || BX('edost_config_old')) reload = true;

			edost_resize.bar('save');
			var v = '';
			var s = document.querySelectorAll('#edost_data_div input[type="radio"]:checked, #edost_data_div input[type="text"], #edost_data_div input[type="checkbox"], #edost_data_div input[type="hidden"], #edost_data_div select');
			if (s) for (var i = 0; i < s.length; i++) {
				if (s[i].type == 'checkbox') v = (s[i].checked ? 'Y' : 'N'); else v = s[i].value;
				post.push(s[i].name + '=' + encodeURIComponent(v));

				if (s[i].name == 'zero_tariff_paysystem' && v != s[i].getAttribute('data-start') ||
					s[i].name.indexOf('[reload_ico]') > 0 && v == 'Y' ||
					s[i].name.indexOf('[reload_vat]') > 0 && v == 'Y' ||
					s[i].name == 'module[0][config][id]' ||
					s[i].name.indexOf('[site]') > 0 && v != s[i].getAttribute('data-start') ||
					(s[i].name.indexOf('[config][id]') > 0 || s[i].name.indexOf('[config][ps]') > 0) && v != s[i].getAttribute('data-start')) reload = true;
			}
			post.push('save=Y');
		}

		if (post.length == 0) return;

		BX.ajax.post('edost.php', 'ajax=Y&' + post.join('&'), function(r) {
			if (mode == 'save') edost_resize.bar('start');

			var json = false;
			if (r.indexOf('{') == 0) json = (window.JSON && window.JSON.parse ? JSON.parse(r) : eval('(' + r + ')'));

			if (reload || json.reload == 'Y') {
				if (document.location.href.indexOf('type=paysystem') > 0) edost_SetData('get', 'paysystem');
				else edost_SetData('get', json.id != undefined ? json.id : '');

				return;
			}

			if (mode == 'get' || mode == 'new') BX('edost_data_div').innerHTML = r;
			if (mode == 'save' && json.error) BX('setting_save_error').innerHTML = json.error;
		});

	}

	window.addEventListener('popstate', function(E) { edost_SetData('history') });
</script>
<? }


if (in_array($type, array('setting', 'paysystem'))) {
	if (!$ajax) { ?>
<div id="edost_data_div" style="display: none; max-width: 910px; border: 1px solid #bec7c8; padding: 20px; background: #FFF;">
<?	}

	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array(
		'MODE' => 'setting',
		'ADMIN' => 'Y',
		'PARAM' => array(
			'module_id' => (isset($_REQUEST['module']) && $_REQUEST['module'] !== '' ? $_REQUEST['module'] : false),
			'paysystem' => ($type == 'paysystem' ? true : false),
		),
	), null, array('HIDE_ICONS' => 'Y'));

	if (!$ajax) { ?>
</div>
<?	}
}


if ($type == 'control') {
	if (!$ajax) {
		$control = GetMessage('EDOST_DELIVERY_CONTROL');
?>
<script type="text/javascript">
	var edost_search_value = '';
	var edost_search_timer;
</script>
<div class="adm-filter-content" style="max-width: 910px; padding: 20px; margin-bottom: 20px;">
	<?=$control['tracking_head']?>: <input id="control_search" class="edost_city" style="height: 20px; width: 200px;" type="text" onkeydown="edost.admin.search(this.id, 'keydown', event);" onblur="edost.admin.search(this.id, 'hide')" onfocus="edost.admin.search(this.id, 'start');" maxlength="200" autocomplete="off" value="">
</div>
<div id="edost_data_div" style="display: none; max-width: 910px; border: 1px solid #bec7c8; padding: 20px; background: #FFF;">
<?
	}

	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array(
		'MODE' => 'list',
		'ADMIN' => 'Y',
		'PARAM' => array(
			'control' => (isset($_REQUEST['control']) ? $_REQUEST['control'] : ''),
			'search' => (!empty($_POST['search']) ? trim($GLOBALS['APPLICATION']->ConvertCharset(substr($_POST['search'], 0, 200), 'utf-8', LANG_CHARSET)) : ''),
		),
	), null, array('HIDE_ICONS' => 'Y'));

	if (!$ajax) { ?>
</div>
<?
	}
}


if ($type == 'register') {
	$company = (isset($_REQUEST['company']) ? $_REQUEST['company'] : '');
	$c = (isset($_REQUEST['control']) ? $_REQUEST['control'] : '');
	$s = (!empty($_REQUEST['search']) ? trim($GLOBALS['APPLICATION']->ConvertCharset(substr($_REQUEST['search'], 0, 200), 'utf-8', LANG_CHARSET)) : '');

	if (!$ajax) {
		$control = GetMessage('EDOST_DELIVERY_CONTROL');
?>
<script type="text/javascript">
	var edost_search_value = '';
	var edost_search_timer;
</script>
<div id="edost_transfer_fon" style="display: none; position: absolute; background: #888; opacity: 0.4; z-index: 4;"></div>
<div id="edost_transfer" style="display: none; position: fixed; padding: 20px 20px 16px 20px; margin: 40px; background: #FFF; border: 1px solid #AAA; z-index: 5;">
	<div id="edost_transfer_bar"></div>
	<div id="edost_transfer_status" style="padding-top: 10px; text-align: center;"></div>
</div>
<div id="edost_main_div" class="edost_main_div_size<?=($setting_cookie['register_item'] == 'Y' ? '2' : '')?>">
<div class="adm-filter-content" style="padding: 20px; margin-bottom: 20px;">
	<div style="display: inline-block; padding-right: 50px;">
		<?=$control['order_head']?>: <input id="register_search_order" class="edost_city" style="height: 20px; width: 200px;" type="text" onkeydown="edost.admin.search(this.id, 'keydown', event);" onblur="edost.admin.search(this.id, 'hide')" onfocus="edost.admin.search(this.id, 'start');" maxlength="200" autocomplete="off" value="<?=($c == 'search_order' ? $s : '')?>">
	</div>
	<div style="display: inline-block;">
		<?=$control['shipment_head']?>: <input id="register_search_shipment" class="edost_city" style="height: 20px; width: 200px;" type="text" onkeydown="edost.admin.search(this.id, 'keydown', event);" onblur="edost.admin.search(this.id, 'hide')" onfocus="edost.admin.search(this.id, 'start');" maxlength="200" autocomplete="off" value="<?=($c == 'search_shipment' ? $s : '')?>">
	</div>
	<div id="edost_history_div" style="font-size: 13px; padding-top: 15px;<?=(empty($history['select']) ? ' display: none;' : '')?>">
		<?=$admin_sign['history_head']?> <span id="edost_history_select"><?=(!empty($history['select']) ? $history['select'] : '')?></span>
		<input style="height: 18px; margin-left: 4px;" value="<?=$admin_sign['button']['history']['name']?>" type="button" onclick="edost.admin.set_param('register', 'history')">
	</div>
</div>
<div id="edost_data_div" style="display: none; border: 1px solid #bec7c8; padding: 20px; background: #FFF;">
<?
	}

	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array(
		'MODE' => 'register',
		'ADMIN' => 'Y',
		'PARAM' => array(
			'control' => $c,
			'company' => $company,
			'search' => $s,
			'history' => $history,
			'history_id' => ($c == 'history' && isset($_REQUEST['id']) ? $_REQUEST['id'] : false),
//			'option' => (!empty($_REQUEST['option']) ? $_REQUEST['option'] : false),
		),
	), null, array('HIDE_ICONS' => 'Y'));

	if (!$ajax) { ?>
</div>
</div>
<?
	}
}


if (!$ajax) require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');


// вывод подсказки
function draw_hint($name, $data, $warning = false, $x = 6, $y = 3) {
	global $protocol;
?>
	<img id="<?=$name?>_hint" style="position: absolute; margin: <?=$y?>px 0 0 <?=$x?>px;" src="<?=$protocol?>edostimg.ru/img/hint/<?=($warning ? 'attention.gif' : 'hint.gif')?>">
	<script type="text/javascript"> new top.BX.CHint({parent: top.BX('<?=$name?>_hint'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 400, hint: '<?=$data?>'}); </script>
<?
}

?>