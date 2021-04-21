<?
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('PUBLIC_AJAX_MODE', true);
//define('DisableEventsCheck', true);
//define('BX_SECURITY_SHOW_MESSAGE', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$mode = (isset($_POST['mode']) ? preg_replace("/[^a-z|_]/i", "", substr($_POST['mode'], 0, 20)) : '');
$id = (isset($_POST['id']) ? preg_replace("/[^0-9A,]/i", "", substr($_POST['id'], 0, 2000)) : 0);
$order_id = (isset($_POST['order_id']) ? preg_replace("/[^0-9]/i", "", substr($_POST['order_id'], 0, 20)) : 0);
$site_id = (isset($_POST['site_id']) ? preg_replace("/[^0-9]/i", "", substr($_POST['site_id'], 0, 20)) : 0);
$zip = (isset($_POST['id']) ? preg_replace("/[^0-9a-z.]/i", "", substr($_POST['zip'], 0, 10)) : 0);
$admin = (isset($_POST['admin']) && $_POST['admin'] == 'Y' ? true : false);
$flag = (isset($_POST['flag']) ? preg_replace("/[^a-z|_]/i", "", substr($_POST['flag'], 0, 20)) : '');
$office_id = (isset($_POST['office_id']) ? preg_replace("/[^0-9A]/i", "", substr($_POST['office_id'], 0, 20)) : false);
$profile = (isset($_POST['profile']) ? intval($_POST['profile']) : false);
$option = (isset($_POST['option']) ? preg_replace("/[^0-9Y,]/i", "", substr($_POST['option'], 0, 100)) : false);

$group_right = $GLOBALS['APPLICATION']->GetGroupRight('sale');


// запись параметров заказа в сессию
if ($mode == 'order_param') {
	$name = (isset($_POST['name']) ? preg_replace("/[^0-9a-z_]/i", "", substr($_POST['name'], 0, 20)) : '');
	$value = (isset($_POST['value']) ? substr($_POST['value'], 0, 1000) : '');
	$value = $GLOBALS['APPLICATION']->ConvertCharset($value, 'utf-8', LANG_CHARSET);
	$person_type = (isset($_POST['person_type']) ? intval($_POST['person_type']) : 0);
	if (!empty($person_type)) {
		if ($name == 'ORDER_DESCRIPTION') $_SESSION['EDOST']['order_param']['ORDER_DESCRIPTION'] = $value;
		else {
			$s = explode('ORDER_PROP_', $name);
			$id = (!empty($s[1]) ? intval($s[1]) : 0);
			if ($id != 0) $_SESSION['EDOST']['order_param']['ORDER_PROP'][$person_type][$id] = $value;
			else {
				$s = explode('edost_', $name);
				$id = (!empty($s[1]) ? $s[1] : '');
				if ($id != '') $_SESSION['EDOST']['order_param']['location'][$person_type][$id] = $value;
			}
		}
	}
}


// детальная информация по контролируемому заказу
if ($mode == 'detail') {
	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array('MODE' => 'detail', 'SHIPMENT_ID' => $id), null, array('HIDE_ICONS' => 'Y'));
}

// CRM
if ($mode == 'crm') {
	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array('MODE' => 'order_edit', 'CRM' => 'Y', 'ADMIN' => 'Y', 'SHIPMENT_ID' => $id, 'SITE_ID' => $site_id, 'ORDER_ID' => $order_id), null, array('HIDE_ICONS' => 'Y'));
}

// сохранение выбранного офиса и получение данных для страницы редактирования отгрузки
if ($mode == 'order_edit' && $group_right != 'D') {
	if ($id == '') $id = 0;
	if ($office_id !== false) {
		if (!empty($profile)) $_SESSION['EDOST']['admin_order_edit_office'][$id] = array('id' => 'edost:'.$profile, 'profile' => $profile, 'office_id' => $office_id);
		echo 'OK';
	}
	else {
		if (!isset($_SESSION['EDOST']['admin_order_edit'][$id])) $id = 0;
		if (isset($_SESSION['EDOST']['admin_order_edit'][$id])) echo $_SESSION['EDOST']['admin_order_edit'][$id];
		else echo '{}';
	}
}


if (in_array($mode, array('package', 'option_setting', 'profile_setting', 'profile', 'batch', 'option', 'control')) && $group_right != 'D') {
	$s = 'modules/edost.delivery/classes/general/delivery_edost.php';
	require_once($_SERVER['DOCUMENT_ROOT'].(version_compare(SM_VERSION, '15.0.0') >= 0 ? getLocalPath($s) : '/bitrix/'.$s));

	if ($mode == 'batch' && !empty($_POST['save'])) {
		// изменение профилей и вызов курьера
		$batch = array();
		$ar = array('profile_shop', 'profile_delivery', 'call');
		foreach ($ar as $v) if (isset($_POST[$v])) $batch[$v] = intval($_POST[$v]);

        if (!empty($batch)) {
			$ar = edost_class::Control($id, array('batch' => $batch));
			edost_class::Control('clear_cache_flag');
			if (isset($ar['add_error'])) echo 'error:'.$ar['add_error'];
		}
	}
	if ($mode == 'profile') {
		// вывод выпадающих списков с профилями
		$control = edost_class::Control();
		if (!empty($control['data'][$id]['batch'])) {
			$v = $control['data'][$id];
			edost_class::GetRegisterProfile(array('company' => $v['company_id'], 'profile_shop' => $v['batch']['profile_shop'], 'profile_delivery' => $v['batch']['profile_delivery']));
		}
	}

	// настройки профилей
	if ($mode == 'profile_setting') {
		edost_class::GetRegisterProfile(array('id' => $id, 'new' => (!empty($_POST['data']) ? $GLOBALS['APPLICATION']->ConvertCharset($_POST['data'], 'utf-8', LANG_CHARSET) : false), 'setting' => true));
	}
	// настройки опций
	if ($mode == 'option_setting') {
		edost_class::GetRegisterOption($id, !empty($_POST['data']) ? $GLOBALS['APPLICATION']->ConvertCharset($_POST['data'], 'utf-8', LANG_CHARSET) : false, true);
	}

	// сохранние параметров упаковки и опций доставки
	if ($mode == 'package') {
		if ($option === 'Y') {
			$param = 'option';
			$data = explode(',', $_POST['data']);
		}
		else {
			$param = 'full';
			$data = edost_class::UnPackDataArray($_POST['data'], 'package2');
			if (!empty($option)) {
				$service = explode(',', $option);
				foreach ($data as $k => $v) $data[$k]['service'] = $service;
			}
		}
		edost_class::SavePackage($order_id, $id, $data, $param);

		$data = edost_class::GetShipmentData($id);
		edost_class::AddRegisterData($data);
//		echo '<br><b>data:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';
		if ($param == 'full') $param = 'package';
		echo edost_class::GetPackageString(array_shift($data), $param);
	}

	// получение данных по контролируемым заказам для страницы просмотра заказа и редактирования отгрузки
	if ($mode == 'control') {
		$id = explode(',', $id);
		foreach ($id as $k => $v) if (intval($v) != 0) $id[$k] = intval($v); else unset($id[$k]);

		if (!empty($flag)) {
			if ($flag === 'changed_delete') {
				edost_class::ControlChanged('delete');
			}
			else if ($flag === 'paid') {
				// зачисление платежа и перевод заказа в статус 'control_status_completed'
				$ar = edost_class::GetControlShipment($id);

				if (!empty($ar['data'])) foreach ($ar['data'] as $k => $v) {
					$config = CDeliveryEDOST::GetEdostConfig($v['site_id']);
					$props = edost_class::GetProps($v['order_id'], array('order_payment'));

					if (empty($props['cod'])) continue;

					edost_class::OrderUpdate($v, array(
						'function' => 'paid',
						'status' => ($config['control_status_completed'] != '' && $v['order_status'] != $config['control_status_completed'] ? $config['control_status_completed'] : ''),
						'payment_id' => $props['payment_id'],
						'paid' => (empty($props['paid']) ? true : false),
					));
				}
			}
			else {
				// изменение флага контроля
				$ar = array('flag' => $flag);
				if ($flag == 'order_date') $ar['date'] = (isset($_POST['date']) ? preg_replace("/[^0-9.]/i", "", substr($_POST['date'], 0, 20)) : '');

				$ar = edost_class::Control($id, $ar);
				edost_class::Control('clear_cache_flag');
			}
//			echo '<br><b>control:</b><pre style="font-size: 12px">'.print_r($ar, true).'</pre>';

			if (!empty($ar['error'])) echo CDeliveryEDOST::GetEdostError($ar['error']);
			else echo 'OK';
		}
		else if (!empty($id)) {
			$r = array();

			// получение данных упаковки (вес, габариты и опции)
			$ar = array();
			$data = edost_class::GetShipmentData($id);
			edost_class::AddRegisterData($data);
			foreach ($data as $k => $v) if (!empty($v['props']['prop']['PACKAGE']['value'])) $ar[] = array(
				'id' => $v['id'],
				'package_formatted' => (!empty($v['package_formatted']) ? $v['package_formatted'] : ''),
				'option_formatted' => edost_class::GetPackageString($v, 'option', false),
				'prop' => $v['props']['prop']['PACKAGE']['value'],
			);
//			echo '<br><b>package:</b> <pre style="font-size: 12px">'.print_r($ar, true).'</pre>';
			if (!empty($ar)) $r[] = '"package": '. edost_class::GetJson($ar, array('id', 'package_formatted', 'option_formatted', 'prop'), true, false);

			// получение данных контроля
			$ar = edost_class::GetControlShipment($id);
//			echo '<br><b>GetControlShipment:</b><pre style="font-size: 12px">'.print_r($ar, true).'</pre>';
			if (!empty($ar['data'])) {
				$tracking = false;
				foreach ($ar['data'] as $v) { $tracking = edost_class::GetTracking($v['site_id']); break; }

				foreach ($ar['data'] as $k => $v) {
					$v['status_full'] = edost_class::GetControlString($v);
					if (!empty($tracking['data'])) foreach ($tracking['data'] as $v2) if (in_array($v['tariff'], $v2['tariff'])) {
						$v['tracking_example'] = $v2['example'];
						$v['tracking_format'] = $v2['format'];
					}
					$ar['data'][$k] = $v;
				}

				$r[] = '"data": '.edost_class::GetJson($ar['data'], array('id', 'flag', 'tariff', 'tracking_code', 'status', 'status_full', 'control', 'tracking_example', 'tracking_format', 'shop_id', 'control_count', 'register'), true, false);
			}

			echo '{'.implode(', ', $r).'}';
		}
		else echo 'ERROR';
	}
}


require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
?>