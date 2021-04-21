<?
class CEdostModifySaleOrderAjax {
	public static $error_key = array('zip', 'delivery', 'office');

	// подключение класса edost + языкового файла
	public static function EdostDelivery() {
		if (class_exists('edost_class')) return;
		$s = 'modules/edost.delivery/classes/general/delivery_edost.php';
		$s = $_SERVER['DOCUMENT_ROOT'].getLocalPath($s);
		IncludeModuleLangFile($s);
		require_once($s);
	}

	// загрузка настроек модуля edost
	public static function GetEdostConfig($site_id) {
		self::EdostDelivery();
		return CDeliveryEDOST::GetEdostConfig($site_id);
	}

	// загрузка выбранного пункта выдачи
	public static function GetOffice($param = false) {
		$office_set = edost_class::GetRequest('edost_office');
		if (empty($param)) return $office_set;

		$office_id = 0;
		$office_key = (!empty($param['tariff']['office_key']) ? $param['tariff']['office_key'] : '');
		$format = (!empty($param['tariff']['format']) ? $param['tariff']['format'] : '');
		if (!empty($param['office'])) $param['office_data'] = (isset($param['office'][$office_key]) ? $param['office'][$office_key] : array());
		if (!empty($param['office_data']))
			if (isset($param['office_data'][$office_set])) $office_id = $_SESSION['EDOST']['address'][$office_key] = $office_set;
			else {
				$i = (isset($_SESSION['EDOST']['address'][$office_key]) ? $_SESSION['EDOST']['address'][$office_key] : '');
				if (isset($param['office_data'][$i])) $office_id = $i;
				else {
					$i = (isset($_SESSION['EDOST']['office_default'][$format]['id']) ? $_SESSION['EDOST']['office_default'][$format]['id'] : '');
					if (isset($param['office_data'][$i])) $office_id = $i;
					else if ($param['map'] != 'Y' || count($param['office_data']) == 1) foreach ($param['office_data'] as $o) { $office_id = $o['id']; break; }
				}
			}

		if (!empty($param['full'])) return (isset($param['office_data'][$office_id]) ? $param['office_data'][$office_id] : false);
		else return $office_id;
	}

	// добавление ошибок
	public static function SetError(&$arResult, $error, $template) {
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$warning = GetMessage('EDOST_DELIVERY_WARNING');
		$i = 0;
		foreach ($error as $k => $v) if (!empty($v)) {
			if ($k == 'zip') $s = $warning[$v];
			else if ($k == 'delivery') $s = $sign['delivery_unchecked'];
			else if ($k == 'office') $s = $sign['office_unchecked'];
			else continue;

			if ($template == 'N3' && $i == 0) {
				$s .= '<input type="hidden" value="'.edost_class::PackDataArray(array($error), self::$error_key).'" name="edost_error">';
				if (isset($arResult['WARNING']['DELIVERY'])) unset($arResult['WARNING']['DELIVERY']);
			}
			$i++;

			$arResult['ERROR'][] = $arResult['ERROR_SORTED']['DELIVERY'][] = $s;
		}
	}

	// добавление предупреждений
	public static function SetWarning(&$v, $warning) {
		$a = (isset($v['DESCRIPTION']) ? true : false);
		$s = ($a ? $v['DESCRIPTION'] : '');
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		if (!empty($s) && strpos($s, $sign[$warning]) !== false) return;
		$s = str_replace('%warning%', $sign[$warning], $sign['bitrix_warning']).(!empty($s) ? '<br>' : '').$s;
		if ($a) $v['DESCRIPTION'] = $s; else $v[] = $s;
	}

	// проверка наличия в заказе доставки и наложенного платежа edost
	public static function CheckOrderDevileryEdostAndEdostPayCod($arOrder) {
		self::EdostDelivery();
		if (isset($arOrder['PAY_SYSTEM_ID']) && isset($arOrder['PERSON_TYPE_ID']) && !empty($arOrder['DELIVERY_ID']) && CDeliveryEDOST::GetEdostProfile($arOrder['DELIVERY_ID']) !== false) {
			$dbPaySystem = CSalePaySystem::GetList(array('SORT' => 'ASC', 'PSA_NAME' => 'ASC'), array('ACTIVE' => 'Y', 'PERSON_TYPE_ID' => $arOrder['PERSON_TYPE_ID'], 'PSA_HAVE_PAYMENT' => 'Y'));
			while ($arPaySystem = $dbPaySystem->Fetch()) if ($arPaySystem['ID'] == $arOrder['PAY_SYSTEM_ID']) {
				if (substr($arPaySystem['PSA_ACTION_FILE'], -11) == 'edostpaycod') return true;
				break;
			}
		}
		return false;
	}


	// отмена отправки письма с напоминанием об оплате заказа, если выбран наложенный платеж edost
	function OnSCOrderRemindSendEmail($OrderID, &$eventName, &$arFields) {
		if ($eventName == 'SALE_ORDER_REMIND_PAYMENT') {
			$arOrder = CSaleOrder::GetByID($OrderID);
			if (self::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) return false;
		}
		return true;
	}


	// установка статуса нового заказа, если выбран наложенный платеж edost
	function OnSCBeforeOrderAdd(&$arOrder) {
		if (self::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) {
			$config = self::GetEdostConfig(isset($arOrder['SITE_ID']) ? $arOrder['SITE_ID'] : '');
			if ($config['cod_status'] != '') $arOrder['STATUS_ID'] = $config['cod_status'];
		}
	}


	// вызывается в начале каждой страницы
	function OnProlog() {

//		$ajax_file = '/bitrix/components/edost/delivery/edost_delivery.php';
		$ajax_file = '/bitrix/components/edost/delivery/ajax.php';

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if (strpos($s, '/shop/orders/shipment/details/') !== false) $mode = 'crm_shipment'; // CRM редактирование отгрузки
		if (strpos($s, '/shop/orders/details/') !== false) $mode = 'crm_order'; // CRM просмотр заказа

		if (!in_array($mode, array('crm_shipment', 'crm_order'))) return;

		$shipment_id = 0;
		$site_id = '';
		if ($mode == 'crm_shipment') {
			$w = explode('/', $s);
			if (isset($w[5])) {
				$shipment_id = intval($w[5]);
				if (!empty($shipment_id)) {
					self::EdostDelivery();
					$shipment = edost_class::GetShipmentData($shipment_id, array('control' => false, 'edost' => false));
					if (!empty($shipment[$shipment_id]['site_id'])) $site_id = $shipment[$shipment_id]['site_id'];
				}
			}
		}
		if ($mode == 'crm_order') {
			$w = explode('/', $s);
			if (isset($w[4])) {
				$order_id = intval($w[4]);
				if (!empty($order_id)) {
					self::EdostDelivery();
					$shipment = edost_class::GetShipmentData($order_id, array('control' => false, 'edost' => false, 'order' => true));
					foreach ($shipment as $k => $v) { $site_id = $k; break; }
				}
			}
		}

		if ($site_id == '') return;

		$config = self::GetEdostConfig($site_id);
		if ($config['admin'] != 'Y') return;

		CDeliveryEDOST::GetAutomatic();
		$ar = array();
		foreach (CDeliveryEDOST::$automatic as $k => $v) $ar[$k] = $v['name'];

		$s = '
			function edost_SetTariffNameCRM() {

				var s = '.\Bitrix\Main\Web\Json::encode($ar).';
				var id = BX.Crm.EntityEditor.defaultInstance._id;
				var c = BX.Crm.EntityEditor.items[id];
				id = c._settings.model._data.DELIVERY_ID;

				if (!s[id]) return;

				var E = document.querySelector(\'div[data-cid="DELIVERY_ID"] div.crm-entity-widget-content-block-inner\');
				if (E && !E.getAttribute("data-edost_tariff_name") && !document.querySelector(\'input[name="DELIVERY_ID"]\')) {
					E.setAttribute("data-edost_tariff_name", "Y");
					E.innerHTML = s[id];
				}

			}

			function edost_SetAdmin() {

				if (!BX.Crm.EntityEditor.defaultInstance) {
					window.setTimeout("edost_SetAdmin()", 100);
					return;
				}

				edost_SetTariffNameCRM();

				var post = [];
				post.push("site_id=" + BX.Crm.EntityEditor.defaultInstance._settings.model._settings.data.SITE_ID);
				post.push("order_id=" + BX.Crm.EntityEditor.defaultInstance._settings.model._settings.data.ORDER_ID);
				post.push("id=" + BX.Crm.EntityEditor.defaultInstance._settings.model._settings.data.ID);

				BX.ajax.post("'.$ajax_file.'", "mode=crm&" + post.join("&"), function(r) {
					var E = document.getElementById("edost_crm_data");
					if (!E) document.body.appendChild( BX.create("div", {"props": {"id": "edost_crm_data", "innerHTML": r}}) );
				});

			}

			BX.ready(function() { edost_SetAdmin(); });
		';
		$s = '<script>'.$s.'</script>';
		$GLOBALS['APPLICATION']->AddHeadString($s);

	}


	// вызывается после сохранения заказа
	function OnSaleOrderSaved(Bitrix\Sale\Order $order) {

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if ($s == '/bitrix/admin/sale_order_create.php') $mode = 'order_create'; // новый заказ
		if ($mode == '') return;

		$config = self::GetEdostConfig(SITE_ID);
		if ($config['control_auto'] != 'Y') return;

		foreach ($order->getShipmentCollection() as $v) if (!$v->isSystem()) {
			// автоматическая постановка на контроль
			$delivery_id = $v->getDeliveryId();

			$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id, true);
			if ($tariff === false) return;

			$allow_delivery = $v->isAllowDelivery();
			$tracking_code = trim($v->getField('TRACKING_NUMBER'));
			if ($allow_delivery && !empty($tracking_code)) $data = edost_class::Control($v);

			return;
		}

	}


	// вызывается при сохранении заказа
	function OnSaleOrderBeforeSaved(Bitrix\Sale\Order $order, $old_values = false) {
//		echo '<br><b>old_values:</b> <pre style="font-size: 12px">'.print_r($old_values, true).'</pre>';
//		$props = edost_class::GetProps($order, array('order', 'no_location'));
//		echo '<br><b>props:</b> <pre style="font-size: 12px">'.print_r($props, true).'</pre>';

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if ($s == '/bitrix/admin/sale_order_edit.php') $mode = 'order_edit'; // редактирование заказа
		if ($s == '/bitrix/admin/sale_order_shipment_edit.php') $mode = 'shipment_edit'; // редактирование отгрузки
		if ($s == '/bitrix/admin/sale_order_ajax.php') $mode = 'order_ajax'; // изменение разрешения доставки, отгрузки и идентификатора отправления
		if ($s == '/bitrix/admin/sale_order_create.php') $mode = 'order_create'; // новый заказ
		if (strpos($s, '/bitrix/components/bitrix/crm.order.') !== false) $mode = 'crm_ajax'; // CRM ajax    /bitrix/components/bitrix/crm.order.shipment.details/ajax.php
		$order_paid = (!empty($old_values['PAYED']) ? true : false); // изменение флага "заказ оплачен"


		// оформление нового заказа покупателем
		if ($order->isNew() && strpos($s, '/admin/') === false && $mode == '') {
			$config = self::GetEdostConfig(SITE_ID);
			if (!empty($_SESSION['EDOST']['template_auto'])) $config['template'] = $_SESSION['EDOST']['template_auto'];

			$edost_locations = (edost_class::GetRequest('edost_zip_full', 1) !== false && CModule::IncludeModule('edost.locations') ? true : false);
			if ($edost_locations) edost_class::SaveOrderLocation($order, 'new', false, false, $edost_locations);

			// вывод свойств заказа для проверки
			if (1 == 2) {
				echo '<br><b>old_values:</b> <pre style="font-size: 12px">'.print_r($_SESSION['EDOST']['develop'], true).'</pre>';

				$ar = $order->getPropertyCollection();
				foreach ($ar->getGroups() as $g) foreach ($ar->getGroupProperties($g['ID']) as $v) {
					$k = $v->getField('CODE');
					if ($k == 'PHONE') echo '<br>phone: ['.$v->getValue().']';
					if ($k == 'ZIP') echo '<br>zip: ['.$v->getValue().']';
					if ($k == 'ZIP_AUTO') echo '<br>zip_auto: ['.$v->getValue().']';
				}

				die();
			}

			if (empty($config['template']) || $config['template'] != 'N3') return;
			if (!class_exists('SaleOrderAjax') || !method_exists('SaleOrderAjax', 'getCurrentShipment')) return;

			$shipment = CDeliveryEDOST::GetShipment($order);
			if ($shipment === false || $shipment->getField('CUSTOM_PRICE_DELIVERY') == 'Y') return;

			$delivery_id = $shipment->getDeliveryId();
			$delivery_price = $shipment->getPrice();

			$profile = CDeliveryEDOST::GetEdostProfile($delivery_id, false);
			if ($profile === false || $profile['tariff'] == 0) return;

			$props = edost_class::GetProps($order, array('order', 'no_location'));
			$tariff = CDeliveryEDOST::GetEdostTariff($profile['profile'], isset($props['office']) ? $props['office'] : false);

			$tariff_original = CDeliveryEDOST::GetEdostTariff($profile['profile']);
			if (isset($tariff['error'])) return;

			$price = (!empty($tariff['priceoffice_active']) ? edost_class::SetDiscount($tariff['price'], $tariff_original['price'], $delivery_price) : -1);
			if (!empty($props['cod'])) $price = edost_class::SetDiscount($tariff['pricecash'], $tariff_original['price'], $delivery_price, $config['sale_discount_cod']);
			if ($price >= 0) {
				$ar = $order->getPaymentCollection();
				if (count($ar) != 1) return;

				$base_currency = CDeliveryEDOST::GetRUB();
				$r = $shipment->setFields(array('CUSTOM_PRICE_DELIVERY' => 'Y', 'PRICE_DELIVERY' => edost_class::GetPrice('value', $price, $base_currency, $order->getCurrency())));
				if (!$r->isSuccess()) return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, null, null, 'sale');

				$payment = $ar->rewind();
				$payment->setField('SUM', $order->getPrice());
			}

			return;
		}


		if ($mode == '' && !$order_paid) return;


		$site_id = $order->getSiteId();
		$config = self::GetEdostConfig($site_id);

		$props = false;
		$props_new = array();
		$shipment_id = array();


		// редактирование заказа + изменение флага "заказ оплачен"
		if ($config['control'] == 'Y' && ($mode == 'order_edit' || $order_paid)) {
			foreach ($order->getShipmentCollection() as $v) if (!$v->isSystem()) $shipment_id[] = $v->getId();
			$props = edost_class::GetProps($order->getId(), array('no_payment'));

			if ($mode == 'order_edit') {
				$p = edost_class::GetProps($order, array('order', 'no_payment'));
				$props_new['location_code'] = (!empty($p['location_code']) ? $p['location_code'] : '');
			}
			if ($order_paid) {
				$props_new['order_paid'] = $order->isPaid();
			}
		}


		// изменение разрешения доставки и идентификатора отправления через ajax
		if ($mode == 'order_ajax' && !empty($_REQUEST['shipmentId']) && isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('updateShipmentStatus', 'saveTrackingNumber'))) {
			$shipment_id = array(intval($_REQUEST['shipmentId']));
			$props = edost_class::GetProps($shipment_id[0], array('shipment'));

			if (isset($_REQUEST['field']) && $_REQUEST['field'] == 'ALLOW_DELIVERY' && isset($_REQUEST['status']) && $_REQUEST['status'] == 'Y') $props_new['allow_delivery'] = true;
			if (isset($_REQUEST['trackingNumber'])) $props_new['tracking_code'] = trim($_REQUEST['trackingNumber']);
		}


		// новый заказ в админке + редактирование отгрузки: сохранение пункта выдачи в поле 'ADDRESS' + местоположения (edost.locations)
		if (in_array($mode, array('order_create', 'shipment_edit', 'crm_ajax'))) {
			$edost_locations = (isset($_REQUEST['edost_location_admin']) && CModule::IncludeModule('edost.locations') ? true : false);

			$address = (!empty($_REQUEST['edost_address']) ? $_REQUEST['edost_address'] : '');
			$location = (!empty($_REQUEST['edost_shop_LOCATION']) ? CSaleLocation::getLocationCODEbyID(intval($_REQUEST['edost_shop_LOCATION'])) : 0);

			if (!empty($address) || !empty($edost_locations)) {
				$delivery_id = 0;
				if (isset($_REQUEST['SHIPMENT'][1]['PROFILE'])) $delivery_id = $_REQUEST['SHIPMENT'][1]['PROFILE'];
				if (isset($_REQUEST['DELIVERY_ID'])) $delivery_id = $_REQUEST['DELIVERY_ID'];

				$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id);
				if ($tariff !== false || $mode == 'order_create') edost_class::SaveOrderLocation($order, 'admin', $tariff, array('ADDRESS' => $address, 'LOCATION' => $location), $edost_locations);
			}
		}


		// редактирование отгрузки
		$shipment_id = '';
		if (!empty($_REQUEST['shipment_id'])) $shipment_id = $_REQUEST['shipment_id'];
		else if (!empty($_REQUEST['PRODUCT_COMPONENT_DATA']['params']['SHIPMENT_ID'])) $shipment_id = $_REQUEST['PRODUCT_COMPONENT_DATA']['params']['SHIPMENT_ID'];

		if (in_array($mode, array('shipment_edit', 'crm_ajax')) && !empty($shipment_id)) {
			$shipment_id = array(intval($shipment_id));
			$props = edost_class::GetProps($shipment_id[0], array('shipment'));

			// сохранение платежной системы
			if (!empty($_REQUEST['edost_payment'])) {
				$payment_id = intval($_REQUEST['edost_payment']);
				$ar = $order->getPaymentCollection();
				$payment = $ar->rewind();

				$ar = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment_id);
				if (!empty($ar)) {
					$s = $ar->getField('NAME');
					if (!empty($s)) {
						$payment->setField('PAY_SYSTEM_NAME', $s);
						$payment->setField('PAY_SYSTEM_ID', $payment_id);
					}
				}
			}

			$ar = array('delivery_id' => 'PROFILE', 'tracking_code' => 'TRACKING_NUMBER', 'allow_delivery' => 'ALLOW_DELIVERY');
			foreach ($ar as $k => $v) if (isset($_REQUEST['SHIPMENT']['1'][$v])) $props_new[$k] = trim($_REQUEST['SHIPMENT']['1'][$v]);
			if (isset($props_new['allow_delivery'])) $props_new['allow_delivery'] = ($props_new['allow_delivery'] == 'Y' ? true : false);

			$props['shipment_flag'] = (isset($_REQUEST['edost_shipment_flag_start']) ? $_REQUEST['edost_shipment_flag_start'] : '0');
			$props_new['shipment_flag'] = (isset($_REQUEST['edost_shipment_flag']) ? $_REQUEST['edost_shipment_flag'] : '0');
		}

//		echo '<br><b>props:</b> <pre style="font-size: 12px">'.print_r($props, true).'</pre>';
//		echo '<br><b>props_new:</b> <pre style="font-size: 12px">'.print_r($props_new, true).'</pre>';
//		echo '<br><b>shipment_id:</b> <pre style="font-size: 12px">'.print_r($shipment_id, true).'</pre>';

		if ($config['control'] != 'Y') return;


		// автоматическая постановка на контроль и обновление данных при изменении параметров + снятие с контроля (если изменился тариф или удалили идентификатор отправления)
		if (!empty($props_new) && !empty($shipment_id)) {
			$a = false;
			foreach ($props_new as $k => $v) if (!isset($props[$k]) || $v != $props[$k]) { $a = true; break; }
			if ($a) {
				$data = edost_class::Control();
				$c = false;
				foreach ($shipment_id as $k2 => $v2) {
					if (isset($data['data'][$v2])) $c = true;
					else if ($mode == 'order_edit' || $order_paid) unset($shipment_id[$k2]);
				}
				if (empty($shipment_id)) return;

				$flag = (isset($props['shipment_flag']) && $props_new['shipment_flag'] != $props['shipment_flag'] ? $props_new['shipment_flag'] : false);

				if (!isset($props_new['allow_delivery'])) $props_new['allow_delivery'] = $props['allow_delivery'];
				if (!isset($props_new['tracking_code'])) $props_new['tracking_code'] = $props['tracking_code'];

				$a = false;
				if ($c) {
					if (empty($props_new['tracking_code'])) $flag = 0;
				}
				else if (!empty($props_new['allow_delivery']) && !empty($props_new['tracking_code'])) {
					if ($flag !== false) $a = true;
					else if ($config['control_auto'] == 'Y' && ($props_new['allow_delivery'] != $props['allow_delivery'] || $props_new['tracking_code'] != $props['tracking_code'])) $a = true;
				}

				if (!$c && !$a) return;

				if ($mode == 'order_edit' || $order_paid) {
					$ar = array();
					foreach ($shipment_id as $v2) $ar[] = $order->getShipmentCollection()->getItemById($v2);
					$data = edost_class::Control($ar);
				}
				else {
					$shipment = $order->getShipmentCollection()->getItemById($shipment_id[0]);
					if ($shipment) {
						$delivery_id = $shipment->getDeliveryId();
						$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id, true);

						$a = true;
						if ($tariff === false)
							if ($c) $flag = 0;
							else $a = false;

						if ($a) $data = edost_class::Control($shipment, array('flag' => $flag));
					}
				}
			}
		}

	}


	// вызывается в админке перед выводом формы редактирования
	function OnAdminTabControlBegin(&$form) {
//		echo '<br><b>_REQUEST:</b> <pre style="font-size: 12px">'.print_r($_REQUEST, true).'</pre>';

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if ($s == '/bitrix/admin/sale_order_edit.php') $mode = 'order_edit'; // редактирование заказа
		if ($s == '/bitrix/admin/sale_order_view.php') $mode = 'order_view'; // просмотр заказа
		if ($s == '/bitrix/admin/sale_order_shipment_edit.php') $mode = 'shipment_edit'; // редактирование отгрузки
		if ($s == '/bitrix/admin/sale_order_create.php') $mode = 'order_create'; // новый заказ
		if ($mode == '') return;

		$site_id = '';
		if ($mode == 'order_create') $site_id = (!empty($_REQUEST['SITE_ID']) ? $_REQUEST['SITE_ID'] : '');
		else {
			$id = (!empty($_REQUEST['ID']) ? $_REQUEST['ID'] : '');
			if (empty($id)) $id = (!empty($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '');
			if (!empty($id)) {
				$order = \Bitrix\Sale\Order::load($id);
				if ($order) $site_id = $order->getSiteId();
			}
		}

		$config = self::GetEdostConfig($site_id);
		if (empty($config)) return;

		// новый заказ + просмотр заказа + редактирование отгрузки
		if (in_array($mode, array('order_edit', 'order_create', 'shipment_edit')) && $config['admin'] == 'Y' || $mode == 'order_view' && $config['control'] == 'Y') {
			$ar = array();
			if ($mode == 'shipment_edit') $ar = array(
				'ORDER_ID' => (!empty($_REQUEST['order_id']) ? $_REQUEST['order_id'] : ''),
				'SHIPMENT_ID' => (!empty($_REQUEST['shipment_id']) ? $_REQUEST['shipment_id'] : ''),
			);

			$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array('MODE' => 'order_edit'.($mode == 'order_edit' ? '2' : ''), 'ADMIN' => 'Y', 'ADMIN_MODE' => $mode) + $ar, null, array('HIDE_ICONS' => 'Y'));
		}

	}


// =========================== sale.order.ajax ===========================


	// вызывается перед расчетом доставки
	function OnSaleComponentOrderProperties(&$arUserResult, Bitrix\Main\HttpRequest $http_request, &$arParams, &$arResult) {
//		echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult, true).'</pre>';

		$config = self::GetEdostConfig(SITE_ID);
		if (empty($config['template']) || $config['template'] == 'off') return;

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$locations_installed = (!empty($arResult['edost']['locations_installed']) ? true : false);

		$arParams['COMPATIBLE_MODE'] = 'Y';

		if ($arParams['USE_ENHANCED_ECOMMERCE'] == 'Y' && !empty($_REQUEST['edost_ecommerce_products'])) $_SESSION['EDOST']['ecommerce_products'] = $_REQUEST['edost_ecommerce_products'];

		// переключение на стандартный шаблон битрикса (если имя шаблона '' или '.default')
		if (defined('DELIVERY_EDOST_TEMPLATE_AUTO') && DELIVERY_EDOST_TEMPLATE_AUTO == 'Y') {
			if (empty($arParams['COMPONENT_TEMPLATE']) || $arParams['COMPONENT_TEMPLATE'] == '.default') $config['template'] = 'N3';
			$_SESSION['EDOST']['template_auto'] = $config['template'];
		}

		if ($arParams['COMPONENT_TEMPLATE'] == 'edost_2019') {
			$config['template'] = 'Y';
			if (empty($arParams['COMPACT'])) $arParams['COMPACT'] = 'S';
			if (empty($arParams['PRIORITY'])) $arParams['PRIORITY'] = 'B';
		}

		// поддержка модуля "Система бонусов. Программы лояльности (vbcherepanov.bonus)"
		if ($config['template'] == 'Y' && isset($arParams['MODULE_VBCHEREPANOV_BONUS'])) $arParams['SHOW_BONUS_SYSTEM'] = ($arParams['MODULE_VBCHEREPANOV_BONUS'] == 'Y' ? 'Y' : 'N');

		if ($config['template'] != 'Y') {
			if (isset($arParams['COMPACT'])) unset($arParams['COMPACT']);
			if (isset($arParams['PRIORITY'])) unset($arParams['PRIORITY']);
		}
		else if (!empty($arParams['COMPACT'])) {
			$config['map'] = 'Y';
			$config['COMPACT'] = $arParams['COMPACT'];
			$config['PRIORITY'] = $arParams['PRIORITY'] = ($config['COMPACT'] == 'off' || empty($arParams['PRIORITY']) ? 'P' : $arParams['PRIORITY']);

			$s = array('SHOP_MAIN', 'NO_POST_MAIN', 'POST_SMALL', 'COD_FILTER_ZERO_TARIFF', 'NO_INSURANCE');
			foreach ($s as $v) if (!empty($arParams[$v])) $config[$v] = $arParams[$v];
		}


		// сохранение и восстановление доставки, оплаты и полей заказа
		$clear_order_param = false;
		if (!empty($arParams['COMPACT'])) {
			$preload_delivery = (!empty($arParams['USE_PRELOAD_DELIVERY']) ? $arParams['USE_PRELOAD_DELIVERY'] : 'Y');
			$preload_prop = (!empty($arParams['USE_PRELOAD_PROP']) ? $arParams['USE_PRELOAD_PROP'] : 'Y');
			if (!empty($_GET['preload_disable'])) $preload_delivery = $preload_prop = 'N'; // ручная блокировка автозаполнения
			$person_type = (!empty($arUserResult['PERSON_TYPE_ID']) ? $arUserResult['PERSON_TYPE_ID'] : 0);
			$write = ($_SERVER['REQUEST_METHOD'] == 'POST' && !($arUserResult['PERSON_TYPE_OLD'] != '' && $arUserResult['PERSON_TYPE_ID'] != $arUserResult['PERSON_TYPE_OLD']) ? true : false);

			$key = array();
			if ($preload_delivery == 'Y') $key = array('DELIVERY_ID', 'PAY_SYSTEM_ID');
			if ($preload_prop == 'Y') { $key[] = 'ORDER_PROP'; $key[] = 'ORDER_DESCRIPTION'; }

			if ($preload_delivery == 'clear' && $_SERVER['REQUEST_METHOD'] != 'POST') {
				$clear_order_param = true;
				if (isset($_SESSION['EDOST']['compact_tariff'])) unset($_SESSION['EDOST']['compact_tariff']); // сбросить выбранные доставки в компактном формате
				if (isset($_SESSION['EDOST']['delivery_default'])) unset($_SESSION['EDOST']['delivery_default']); // сбросить выбранные доставки в закладках
				if (isset($_SESSION['EDOST']['office_default'])) unset($_SESSION['EDOST']['office_default']); // сбросить выбранные на карте пункты выдачи
			}

			if (!empty($key)) {
				if ($write) {
					foreach ($key as $k) if (!empty($arUserResult[$k]))
						if (!is_array($arUserResult[$k])) $_SESSION['EDOST']['order_param'][$k] = $arUserResult[$k];
						else $_SESSION['EDOST']['order_param'][$k][$person_type] = $arUserResult[$k];
				}
				else {
					foreach ($key as $k)
						if (!is_array($arUserResult[$k])) {
							if (!empty($_SESSION['EDOST']['order_param'][$k])) $arUserResult[$k] = $_SESSION['EDOST']['order_param'][$k];
						}
						else {
							if (!empty($_SESSION['EDOST']['order_param'][$k][$person_type]))
								foreach ($_SESSION['EDOST']['order_param'][$k][$person_type] as $k2 => $v2) $arUserResult[$k][$k2] = $v2;
						}
				}
			}

			if ($preload_prop == 'Y' && $locations_installed) {
				$ar = GetMessage('EDOST_LOCATIONS_ADDRESS');
				if ($write) {
					foreach ($ar as $k => $v) {
						$s = edost_class::GetRequest('edost_'.$k, 200);
						if ($s !== false) $_SESSION['EDOST']['order_param']['location'][$person_type][$k] = $s;
					}
				}
				else {
					foreach ($ar as $k => $v)
						if (isset($_SESSION['EDOST']['order_param']['location'][$person_type][$k])) $_REQUEST['edost_'.$k] = $_SESSION['EDOST']['order_param']['location'][$person_type][$k];
						else if (isset($_REQUEST['edost_'.$k])) unset($_REQUEST['edost_'.$k]);
					$arUserResult['edost']['set_prop2'] = $config['set_prop2'] = false; // запрет на загрузку prop2 из профиля покупателя
				}
			}

			// удаление в телефоне разделителей + замена "+7" на "8"
			if ($arUserResult['CONFIRM_ORDER'] == 'Y') {
				$tel_clear = (!empty($arParams['TEL_CLEAR']) && $arParams['TEL_CLEAR'] == 'Y' ? true : false);
				$tel_ru8 = (!empty($arParams['TEL_RU8']) && $arParams['TEL_RU8'] == 'Y' ? true : false);
				if ($tel_clear || $tel_ru8) {
					$props = edost_class::SetPropsCode($arUserResult['ORDER_PROP']);
					foreach ($props as $k => $v) if ($v['type'] == 'tel') {
						$id = $v['id'];
						$s = trim($arUserResult['ORDER_PROP'][$id]);
						$n = preg_replace("/[^0-9+]/i", "", $s);
						if ($tel_clear) $s = $n;
						if ($tel_ru8 && strlen(preg_replace("/[^0-9]/i", "", $s)) == 11 && substr($s, 0, 2) == '+7' && substr($n, 0, 3) != '+77') $s = '8'.substr($s, 2);
						$arUserResult['ORDER_PROP'][$id] = $s;
						break;
					}
				}
			}
		}


		if (edost_class::GetRequest('edost_post_manual') === 'Y') $config['POST_MANUAL'] = true;

		$arResult['edost']['config'] = $config;
		$arResult['edost']['cod_tariff'] = (!empty($arParams['COMPACT']) && $arParams['COMPACT'] == 'off' && $config['template_cod'] == 'tr' ? true : false);

		$compact = (!empty($arParams['COMPACT']) && $arParams['COMPACT'] != 'off' ? true : false);
//		echo '<br><b>config (sale.order.ajax):</b> <pre style="font-size: 12px">'.print_r($config, true).'</pre>';

		$s = '';
		if (!empty($arParams['YANDEX_API_KEY'])) $s = $arParams['YANDEX_API_KEY'];
		if (empty($s)) $s = \Bitrix\Main\Config\Option::Get('fileman', 'yandex_map_api_key', '');
		if (!empty($s)) $arResult['edost']['yandex_api_key'] = $s;

		if (!empty($_REQUEST['edost_fast']) && $_REQUEST['edost_fast'] == 'Y') $arResult['edost']['fast'] = true; // быстрое оформление заказа

		if (!empty($arParams['COMPACT'])) $arParams['DELIVERY_TO_PAYSYSTEM'] = ($arParams['PRIORITY'] == 'C' ? 'p2d' : 'd2p');

		if (!empty($arResult['edost']['order_recreated_delivery_id'])) $arUserResult['DELIVERY_ID'] = $arResult['edost']['order_recreated_delivery_id'];

		if (!$compact) {
			if ($config['template_map_inside'] == 'Y' && $config['template'] == 'Y' && $config['template_format'] !== 'off' && $config['template_block'] !== 'off') $arResult['edost']['map_inside'] = true;
			if ($config['map'] == 'Y' && !in_array($config['template'], array('Y', 'N3')) && (!defined('DELIVERY_EDOST_PICKPOINT_WIDGET') || DELIVERY_EDOST_PICKPOINT_WIDGET == 'Y')) $arResult['edost']['pickpoint_widget'] = true;
		}

		// загрузка дополнительных параметров из id доставки: edost:profile_id:office_id:cod_tariff
		$id = (isset($arUserResult['DELIVERY_ID']) ? $arUserResult['DELIVERY_ID'] : '');
		$v = explode(':', $id);
		$ar = array();
		if ($v[0] === 'edost') {
			$profile = $v[1];
			$s = explode('_', $profile);
			if (isset($s[1])) {
				$id = $s[1];
				$profile = $s[0];
			}
			else $id = 'edost:'.$profile;

			$ar['profile'] = $profile;
			if (isset($v[2])) {
				if (!empty($v[2])) $ar['office_id'] = $v[2];
				if (!empty($v[3])) $ar['cod_tariff'] = ($v[3] === 'Y' ? true : false);
			}

			$arUserResult['DELIVERY_ID'] = $id;
		}
		$ar['id'] = $id;
		if (!empty($_REQUEST['edost_bookmark'])) $ar['bookmark'] = substr($_REQUEST['edost_bookmark'], 0, 10);
		$arResult['edost']['active'] = $ar;

		// переключение платежной системы, после смены тарифа доставки с наложенным платежом на тариф без наложенного, если включен вывод наложенного платежа отдельным тарифом (для шаблона eDost)
		if (empty($arResult['edost']['fast']) && !empty($_REQUEST['edost_cod_tariff_paysystem'])) {
			$s = explode('|', $_REQUEST['edost_cod_tariff_paysystem']);
			$s[0] = intval($s[0]);
			if (!empty($ar['cod_tariff'])) {
				if (!empty($s[0])) $arUserResult['PAY_SYSTEM_ID'] = $s[0];
			}
			else {
				if ($arUserResult['PAY_SYSTEM_ID'] == $s[0]) $arUserResult['PAY_SYSTEM_ID'] = (!empty($s[1]) ? intval($s[1]) : '');
			}
		}

		// поле ADDRESS (для сохранения данных по выбранному пункту выдачи)
		$address_id = (isset($arResult['edost']['address_id']) ? $arResult['edost']['address_id'] : -1);
		$address = (isset($arResult['edost']['address_value']) ? $arResult['edost']['address_value'] : '');
		if (!$locations_installed) {
			$props = edost_class::SetPropsCode($arUserResult['ORDER_PROP']);
			if (isset($props['ADDRESS'])) {
				$arResult['edost']['address_id'] = $address_id = $props['ADDRESS']['id'];
				$arResult['edost']['address_value'] = $address = $props['ADDRESS']['value'];
			}
			if (isset($props['ZIP'])) $arResult['edost']['zip_id'] = $props['ZIP']['id'];
			if (isset($props['ZIP_AUTO'])) $arResult['edost']['zip_auto_id'] = $props['ZIP_AUTO']['id'];
		}

		// подключение скрипта выбора пунктов выдачи и стилей
		if (empty($arResult['edost']['order_recreated']) && empty($arResult['edost']['order_recreated2']) && (!defined('DELIVERY_EDOST_JS_SALE_ORDER_AJAX') || DELIVERY_EDOST_JS_SALE_ORDER_AJAX != 'N')) {
			$file = array();
			if ($config['template'] == 'Y' || $config['map'] == 'Y') $file[] = 'main.css';
			if ($config['template'] != 'N3' || $config['map'] == 'Y') $file[] = 'office.js';
			if ($config['map'] == 'Y' && !empty($arResult['edost']['pickpoint_widget'])) $file[] = 'pickpoint';
			if (!empty($file)) {
				$script = edost_class::GetScriptData($config, $file);
				$GLOBALS['APPLICATION']->AddHeadString($script['main']);
			}

			if ($config['template'] != 'Y') {
				if ($config['map'] == 'Y') $s = '
					function edost_SetOffice(profile, id, cod, mode) {
						if (profile !== "post_manual") {
							var E = document.getElementById("edost_office");
							if (E) E.value = id;

							if (edost.office.map) {
								edost.office.balloon("close");
								edost.office.window("hide");
							}

		                    '.($config['template'] != 'N3' ? '
	    	                var s = profile.split("_");
	        	            if (s[1] != undefined) {
								var E = document.getElementById("ID_DELIVERY_ID_" + s[1]);
								if (E) {
									E.click();
									return;
								}
		                    }
	    	                ' : '').'
	                    }

						var p = edost.office.point(id);
						if (p && p.city && edost.E("edost_country")) {
							var a = (p.mode == "post" ? true : false);
							edost.location.set("", true, p.city, edost.V("edost_region"), edost.V("edost_country").split("_")[0], a ? false : true, p.city, a ? p.code : false);
							return;
						}

	                    '.($config['template'] == 'N3' ? 'BX.Sale.OrderAjaxComponent.sendRequest();' : 'submitForm();').'
					}
					if (window.edost && window.edost.resize) edost.resize.template_ico = "'.(!empty($config['template_ico']) ? $config['template_ico'] : 'C').'";';
				else $s = '
					function edost_OpenMap(n) {
						var E = document.getElementById("edost_office_" + n);
						if (!E) return false;
						var s = E.value.split("|")[1];
						if (window.edost && window.edost.office && window.edost.office.info) edost.office.info(0, s); else window.open(s, "_blank");
					}

					function edost_SetOffice(n, id) {
						var E = document.getElementById("edost_office_" + n);
						if (E) {
							var E2 = document.getElementById("edost_office");
							if (E2) E2.value = E.value.split("|")[0];

							'.($config['template'] == 'N3' ? 'BX.Sale.OrderAjaxComponent.sendRequest();' : '
							id = "ID_DELIVERY_" + (id != undefined ? "ID_" + id : "edost_" + n);
							if (document.getElementById(id).checked) submitForm();
							').'
						}
					}';

				if ($config['template'] == 'N3') $s .= '
					function edost_GetOffice() {
						var E = document.querySelector(\'input[name="DELIVERY_ID"]:checked\');
						if (!E) return;
						var E = BX(\'edost_get_office_span_\' + E.value);
						if (E) E.click();
					}

					function edost_ShowOfficeAddress() {
						var E = document.getElementById("edost_address_input");
						if (!E || E.value == "") return;
						var E = document.getElementById("soa-property-" + E.value);
						if (E && E.style.display != "none") {
							if (E.value.indexOf("'.$sign['code'].'" + ": ") == -1) E.value = "";
							E.style.display = "none";

							var s = E.value;
							if (s == "") {
								s = "<span style=\"color: #F00;\">'.$sign['office_unchecked'].'!<span/>";
								s += " <span class=\"edost_link\" onclick=\"edost_GetOffice()\">'.$sign['change2'].'</span>";
							}
							else {
								s = s.split(", '.$sign['code'].'");
								s = s[0];
							}

							var E2 = BX.findParent(E);
							E2.appendChild( BX.create("div", {"props": {"className": "edost_office_address_n3", "style": "font-weight: bold;", "innerHTML": s}}) );
						}
					}

					window.setInterval("edost_ShowOfficeAddress()", 500);';

				$s = '<script type="text/javascript">'.$s.'</script>';
				$GLOBALS['APPLICATION']->AddHeadString($s);
			}
		}

		// обработка ошибок для N3
		if ($config['template'] == 'N3' && (isset($_REQUEST['save']) && $_REQUEST['save'] == 'Y' || isset($_REQUEST['action']) && $_REQUEST['action'] == 'saveOrderAjax')) {
			$s = edost_class::GetRequest('edost_error');
			if (!empty($s)) {
				$s = edost_class::UnPackDataArray($s, self::$error_key);
				self::SetError($arResult, $s[0], $config['template']);
			}
		}

		// сброс старого (из профиля покупателя) адреса пункта выдачи при первой загрузке + перенос в дефолтные для нового выбора
		if ($address_id != -1 && ($_SERVER['REQUEST_METHOD'] != 'POST' || !$locations_installed && !isset($_SESSION['EDOST']['readonly']))) {
			if (!$locations_installed) $_SESSION['EDOST']['readonly'] = false;
			$office = edost_class::ParseOfficeAddress($address);
			if ($office !== false) {
				if (is_array($office) && !$clear_order_param) $_SESSION['EDOST']['office_default']['profile'] = array('id' => $office['id'], 'profile' => $office['profile'], 'cod_tariff' => $office['cod_tariff']);
				if (!$locations_installed) $arUserResult['ORDER_PROP'][$address_id] = '';
			}
		}

	}


	// вызывается после расчета заказа в sale.order.ajax
	function OnSaleComponentOrderDeliveriesCalculated(Bitrix\Sale\Order $order, &$arUserResult, Bitrix\Main\HttpRequest $http_request, &$arParams, &$arResult, &$arDeliveryServiceAll, &$arPaySystemServiceAll) {

		if (empty($arResult['edost']['config'])) return;
		$config = $arResult['edost']['config'];

		// быстрое оформление заказа + нулевой тариф
		$fast = (!empty($arResult['edost']['fast']) ? true : false);
		$zero_tariff = (empty($arResult['DELIVERY']) && ($config['hide_error'] != 'Y' || $config['show_zero_tariff'] == 'Y') ? true : false);
		if ($fast || $zero_tariff) {
			$shipment = CDeliveryEDOST::GetShipment($order);
			$tariff = CDeliveryEDOST::GetZeroTariff($config);
			if (!empty($shipment) && !empty($tariff)) {
				$sign = GetMessage('EDOST_DELIVERY_SIGN');

				$arResult['DELIVERY'] = array($tariff['id'] => array('CHECKED' => 'Y', 'PRICE' => 0, 'PRICE_FORMATED' => '0', 'PERIOD_TEXT' => ''));
				$arDeliveryServiceAll = array($tariff['id'] => \Bitrix\Sale\Delivery\Services\Manager::getObjectById($tariff['id']));

				$shipment->setField('CUSTOM_PRICE_DELIVERY', 'N');
				$shipment->setField('DELIVERY_ID', $tariff['id']);
				$shipment->setField('DELIVERY_NAME', $tariff['name']);
				$order->getShipmentCollection()->calculateDelivery();

				if ($fast) {
					$order->setField('COMMENTS', $sign['fast']);
//					$order->setField('USER_DESCRIPTION', $sign['fast']);
				}

				$order->doFinalAction(true);
			}
			if ($fast) {
				$payment = CDeliveryEDOST::GetPayment($order);
				if ($payment) $payment->delete();

				if (!empty($arParams['FAST_STATUS'])) $order->setField('STATUS_ID', $arParams['FAST_STATUS']);
			}
		}

		if (!empty(CDeliveryEDOST::$result['order']['location'])) $arResult['edost']['location'] = CDeliveryEDOST::$result['order']['location'];

		$shipment = CDeliveryEDOST::GetShipment($order);
		if ($shipment === false) return;

		$bitrix_data = $arResult['DELIVERY'];
		$bitrix_delivery_id = $delivery_id = $shipment->getDeliveryId();
		$bitrix_delivery_price = $shipment->getPrice();

		$active_payment = $arUserResult['PAY_SYSTEM_ID'];

		$edost_payment = false;
		if (!empty($arPaySystemServiceAll)) foreach ($arPaySystemServiceAll as $k => $v) if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') { $edost_payment = $k; break; }

		$payment_inner = CDeliveryEDOST::GetPayment($order, true);
		if (!empty($payment_inner) && $payment_inner->getSum() == $order->getPrice()) $active_payment = $payment_inner->getPaymentSystemId();

		$compact = (!empty($arParams['COMPACT']) && $arParams['COMPACT'] != 'off' ? true : false);
		$priority = (!empty($arParams['PRIORITY']) ? $config['PRIORITY'] : '');
		$payment_bonus = ($priority != '' && (empty($arParams['PAYMENT_BONUS']) || $arParams['PAYMENT_BONUS'] == 'Y') ? true : false);
		$delivery_bonus = ($priority == 'C' && $edost_payment !== false && (empty($arParams['DELIVERY_BONUS']) || $arParams['DELIVERY_BONUS'] == 'Y') ? true : false);

		// подготовка данных по доставке для форматирования + расчет стоимости для тарифов битрикса (для шаблона eDost и расширенного Visual)
		if (!empty($bitrix_data) && !empty($arDeliveryServiceAll) && empty($arResult['edost']['order_recreated'])) {
			foreach ($bitrix_data as $k => $v) if (isset($arDeliveryServiceAll[$k])) {
				if (isset($v['CHECKED'])) unset($arResult['DELIVERY'][$k]['CHECKED']);

				$o = $arDeliveryServiceAll[$k];
				$code = $o->getCode();
				$s = explode(':', $code);
				$automatic = (isset($s[1]) ? $s[0] : '');
				$profile = ($o->isProfile() ? true : false);
				$id = $o->getId();

				$v = array(
					'ID' => $id,
					'OWN_NAME' => ($profile && $automatic !== 'edost' ? $o->getNameWithParent() : $o->getName()),
					'DESCRIPTION' => $o->getDescription(),
					'SORT' => $o->getSort(),
					'CODE' => $code, // в стандартном массиве битрикса кода нет
					'CURRENCY' => $order->getCurrency(),
//					'FIELD_NAME' => 'DELIVERY_ID',
//					'EXTRA_SERVICES' => $o->getExtraServices()->getItems(),
//					'STORE' => \Bitrix\Sale\Delivery\ExtraServices\Manager::getStoresList($id),
				) + $v;

				if (!$profile) {
					$s = $o->getConfig();
					if (!empty($s['MAIN']['ITEMS']['PERIOD']['ITEMS'])) {
						$s = $s['MAIN']['ITEMS']['PERIOD']['ITEMS'];
						$v['PERIOD'] = array('PERIOD_FROM' => $s['FROM']['VALUE'], 'PERIOD_TO' => $s['TO']['VALUE'], 'PERIOD_TYPE' => $s['TYPE']['VALUE']);
					}
				}

				$s = $o->getLogotip();
				if (!empty($s)) $v['LOGOTIP'] = array('ID' => $s, 'SRC' => CFile::GetPath($s));

				$bitrix_data[$k] = $v;
			}
//			echo '<br><b>bitrix_data:</b> <pre style="font-size: 12px">'.print_r($bitrix_data, true).'</pre>';

			$c = $config;

			$a = $cod_disable = false;
			if ($edost_payment !== false) {
				$a = true;
				if ($priority == 'C' && $edost_payment == $active_payment) $c['COD_FILTER'] = true;
			}
			if (!$a) {
				// отключение вывода стоимости с наложенным платежом, если нет модуля с обработчиком 'edostpaycod'
				$c['template_cod'] = 'off';
				if ($config['template'] == 'Y') $c['COD_DISABLE'] = $cod_disable = true;
			}

			if ($delivery_bonus) $c['DELIVERY_BONUS'] = true;
			if ($config['template'] == 'Y') $c['MAP_DATA'] = true;
			if (!empty($arUserResult['PAY_SYSTEM_ID'])) $c['PAY_SYSTEM_ID'] = $arUserResult['PAY_SYSTEM_ID'];

			// отключение вывода нулевого тарифа для наложки за рубежом
			if ($priority == 'C') {
				$location = false;
				if (isset(CDeliveryEDOST::$result['order']['location'])) $location = CDeliveryEDOST::$result['order']['location'];
				else if (!empty($arUserResult['DELIVERY_LOCATION'])) $location = CDeliveryEDOST::GetEdostLocation($arUserResult['DELIVERY_LOCATION']);
				if (!empty($location['country'])) $c['COD_FILTER_ZERO_TARIFF'] = 'N';
			}

			$format = edost_class::FormatTariff($bitrix_data, $arResult['BASE_LANG_CURRENCY'], in_array($config['template'], array('Y', 'N2', 'N3')) ? array('bitrix' => $order) : false, isset($arResult['edost']['active']) ? $arResult['edost']['active'] : false, $c);
			if ($format === false) return;
//			echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';

			$arResult['edost']['format'] = $format;

			$delivery_id = $arUserResult['DELIVERY_ID'] = $format['active']['id'];
			if (isset($arResult['DELIVERY'][$delivery_id])) $arResult['DELIVERY'][$delivery_id]['CHECKED'] = 'Y';
			$backup = CDeliveryEDOST::$result;
			$bonus = array('payment' => array(), 'delivery' => array());

			// определение стоимости заказа в зависимости от способов оплаты + поиск скидок способов оплаты
			if ($payment_bonus || $delivery_bonus) {
				$base_price = false;
				$payment_price = $bonus_data = array();
				$active_price = (!empty(CDeliveryEDOST::$result) ? CDeliveryEDOST::$result['order']['original']['PRICE'] : false);
				$order_clone = $order->createClone();
				$payment_clone = CDeliveryEDOST::GetPayment($order_clone, 'new');
				foreach ($arPaySystemServiceAll as $v) {
					$payment_clone->setField('PAY_SYSTEM_ID', $v['ID']);
					if ($base_price === false) $base_price = $order_clone->getBasket()->getBasePrice();
					$payment_price[$v['ID']] = $order_clone->getBasket()->getPrice();
					if ($active_price == false && $v['ID'] == $active_payment) $active_price = $payment_price[$v['ID']];
				}
				$a = false;
				$p = false;
				foreach ($payment_price as $v) if ($p === false) $p = $v; else if ($p != $v) { $a = true; break; }
				if ($a) {
					$p = array();
					foreach ($payment_price as $k => $v) if ($v !== $base_price) $p[$k] = $v - $base_price;
					$bonus['payment'] = $p;
				}
			}

			$cod_update = (($priority == 'P' || $priority == 'B') && $edost_payment !== false && $active_payment !== false && $active_payment != $edost_payment && isset($bonus['payment'][$active_payment]) ? true : false);

			// данные для расчета бонусов служб доставки
			if ($delivery_bonus || $cod_update) {
				$bonus_data = array_fill_keys(array_keys($payment_price), false);
				foreach ($payment_price as $k => $v) if ($v == $active_price) $bonus_data[$k] = ($edost_payment !== false && $k == $edost_payment ? $format['bonus']['cod'] : $format['bonus']['normal']);

				// дополнительный расчет доставки для способов оплат со скидками
				if (($cod_update || $priority == 'C') && !empty($bonus['payment'])) {
					$format_cod = false;
					foreach ($bonus_data as $k => $v) if ($bonus_data[$k] === false) {
						$delivery = array();
						CDeliveryEDOST::$result = null;

						$payment_clone->setField('PAY_SYSTEM_ID', $k);
						$order_clone->getShipmentCollection()->calculateDelivery();
						$shipment_clone = CDeliveryEDOST::GetShipment($order_clone);
						$services = \Bitrix\Sale\Delivery\Services\Manager::getRestrictedObjectsList($shipment_clone);
						foreach ($services as $v2) $delivery[] = array(
							'ID' => $v2->getId(),
							'NAME' => $v2->getName(),
							'DESCRIPTION' => $v2->getDescription(),
							'SORT' => $v2->getSort(),
							'CODE' => $v2->getCode(),
						);

						if (empty(CDeliveryEDOST::$result)) foreach (CDeliveryEDOST::$result_full as $v2) if (isset($v2['order']['original']['PRICE']) && $v2['order']['original']['PRICE'] == $payment_price[$k]) { CDeliveryEDOST::$result = $v2; break; }

						$format2 = edost_class::FormatTariff($delivery, $arResult['BASE_LANG_CURRENCY'], array('bitrix' => $order_clone), isset($arResult['edost']['active']) ? $arResult['edost']['active'] : false, $config + array('DELIVERY_BONUS' => true, 'CLONE' => true, 'MAP_DATA' => true, 'COD_DISABLE' => $cod_disable));
						if ($k == $edost_payment || !isset($bonus['payment'][$k])) $format_cod = $format2;

						foreach ($bonus_data as $k2 => $v2) if ($k2 == $k || $bonus_data[$k2] === false && $payment_price[$k2] == $payment_price[$k]) $bonus_data[$k2] = ($edost_payment !== false && $k2 == $edost_payment ? $format2['bonus']['cod'] : $format2['bonus']['normal']);
					}

					// перенос наложки из дополнительного расчета в основной
					if ($cod_update && !empty($format) && !empty($format_cod)) {
						edost_class::AddCod($format, $format_cod, $arResult['edost']['cod_tariff']);
						$arResult['edost']['format'] = $format;
					}
				}

				$bonus['delivery'] = edost_class::GetDeliveryBonus($bonus_data, $edost_payment, $config['template_format']);
			}
			if (!empty($bonus['payment']) || !empty($bonus['delivery'])) $arResult['edost']['bonus'] = $bonus;
			CDeliveryEDOST::$result = $backup;

			// удаление дублирующего тарифа для кнопки "выбрать доставку с возможностью оплаты при получении"
			if ($priority == 'B' && !empty($format['data'])) {
				foreach ($format['data'] as $f_key => $f) if (in_array($f_key, array('office', 'postmap'))) {
					$key_delete = $key = -1;
					foreach ($f['tariff'] as $k => $v) if (!empty($v['compact_cod'])) if (!empty($v['compact_cod_copy'])) $key_delete = $k; else $key = $k;
					if ($key_delete != -1 && $key != -1) unset($format['data'][$f_key]['tariff'][$key_delete]);
				}
				$arResult['edost']['format'] = $format;
			}

			if (empty($delivery_id)) {
				$shipment->setFields(array('CUSTOM_PRICE_DELIVERY' => 'Y', 'PRICE_DELIVERY' => 0));
				$arUserResult['CALCULATE_PAYMENT'] = true;
				return;
			}

			// пересоздание заказа при изменении выбранной доставки
			if ($delivery_id != $bitrix_delivery_id) {
				$arUserResult['RECREATE_ORDER'] = true;
				$arResult['edost']['order_recreated'] = true;
				$arResult['edost']['order_recreated_delivery_id'] = $delivery_id;
				return;
			}
		}

		if (isset($arResult['edost']['format']['map_data'])) unset($arResult['edost']['format']['map_data']);

		$format = (!empty($arResult['edost']['format']) ? $arResult['edost']['format'] : false);
		$format_active = (!empty($format['active']) ? $format['active'] : false);
		$cod_tariff = (!empty($format['cod_tariff']) ? true : false);
		$cod_tariff_active = (!empty($format_active['cod_tariff']) ? true : false);
		if ($cod_tariff && $cod_tariff_active) $arUserResult['PAY_CURRENT_ACCOUNT'] = false;

		$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id);
		if ($tariff !== false) {
			$tariff = CDeliveryEDOST::GetEdostTariff($tariff['profile'], $format_active);
			if ($config['template'] != 'Y' && !empty($format_active) && !empty($tariff['company_id']) && !empty($format['office'])) {
				$o = self::GetOffice(array('tariff' => $tariff, 'office' => $format['office'], 'map' => $config['map'], 'full' => true));
				if (!empty($o)) {
					$format_active['office_id'] = $o['id'];
					$format_active['office_type'] = $o['type'];
					$format_active['office_options'] = $o['options'];
					$format_active['office_city'] = $o['city'];
					$tariff = CDeliveryEDOST::GetEdostTariff($tariff['profile'], $format_active);
				}
			}

			if (isset($tariff['error'])) $tariff = false;
			else {
				if (empty($format_active['cod'])) $tariff['pricecash'] = -1;
				$tariff_original = CDeliveryEDOST::GetEdostTariff($tariff['profile']);

				// присвоение индекса, если выбрана доставка в почтовое отделение
				if (!empty($tariff['company_id']) && !empty($format_active['office_type']) && $tariff['company_id'] == 23 && $format_active['office_type'] == 1) {
					$zip = $format_active['office_id'];

					$zip_id = (isset($arResult['edost']['zip_id']) ? $arResult['edost']['zip_id'] : '');
					$zip_auto_id = (isset($arResult['edost']['zip_auto_id']) ? $arResult['edost']['zip_auto_id'] : '');
					if (empty($zip_id)) {
						$props = edost_class::SetPropsCode($arUserResult['ORDER_PROP']);
						if (isset($props['ZIP'])) $zip_id = $props['ZIP']['id'];
						if (isset($props['ZIP_AUTO'])) $zip_auto_id = $props['ZIP_AUTO']['id'];
					}

					$arUserResult['DELIVERY_LOCATION_ZIP'] = $zip;

					if (!empty($zip_id)) $arUserResult['ORDER_PROP'][$zip_id] = $zip;
					if (!empty($zip_auto_id)) $arUserResult['ORDER_PROP'][$zip_auto_id] = '';

					if (isset($_REQUEST['edost_zip'])) $_REQUEST['edost_zip'] = $zip;
					if (isset($_REQUEST['edost_zip_full'])) $_REQUEST['edost_zip_full'] = 'Y';
					if (isset($_REQUEST['order']['edost_zip'])) $_REQUEST['order']['edost_zip'] = $zip;
					if (isset($_REQUEST['order']['edost_zip_full'])) $_REQUEST['order']['edost_zip_full'] = 'Y';

					edost_class::OrderProperty($order, array('ZIP' => $zip, 'ZIP_AUTO' => ''));
				}
			}
		}

		// поиск наложенного платежа eDost и оплаты битрикса
		if (!empty($arResult['edost']['cod_tariff'])) {
			$edost_id = $bitrix_id = false;
			if (!empty($arPaySystemServiceAll)) foreach ($arPaySystemServiceAll as $k => $v)
				if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') $edost_id = $v['ID'];
				else if ($v['ACTION_FILE'] != 'inner' && $bitrix_id === false) $bitrix_id = $v['ID'];
			$arResult['edost']['cod_tariff'] = $edost_id.'|'.$bitrix_id;
		}

		if (!empty($arResult['edost']['fast'])) return;

		// удаление наложенного платежа для тарифов без наложки + выбор наложки для тарифов с 'cod_tariff' + выделение первого способа оплаты, если нет активных
		$cod = $i = $update = false;
		if (!empty($arPaySystemServiceAll)) foreach ($arPaySystemServiceAll as $k => $v) {
			$a = ($v['ID'] == $active_payment ? true : false);
			if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') {
				if ($cod_tariff && $cod_tariff_active && !$a) {
					$update = $cod = true;
					$i = $v['ID'];
					break;
				}
				else if ($tariff === false || $tariff['pricecash'] < 0 || $cod_tariff && !$cod_tariff_active) {
					if ($a) $update = true;
					if ($priority == '' || $priority  == 'P' || !$compact && $priority == 'B') unset($arPaySystemServiceAll[$k]);
				}
				else if ($a) $cod = true;
			}
			else if ($v['ACTION_FILE'] != 'inner' && $i === false) $i = $v['ID'];
		}
		if ($update) {
			$arUserResult['PAY_SYSTEM_ID'] = ($i !== false ? $i : '');

			$arUserResult['RECREATE_ORDER'] = true;
			$arResult['edost']['order_recreated2'] = true;
			$arResult['edost']['order_recreated_delivery_id'] = $arUserResult['DELIVERY_ID'];
			return;
		}

		if ($tariff === false) return;

		// установка стоимости доставки для эксклюзивных офисов и наложенного платежа
		$price = (!empty($tariff['priceoffice_active']) ? edost_class::SetDiscount($tariff['price'], $tariff_original['price'], $bitrix_delivery_price) : -1);
		if ($cod) $price = edost_class::SetDiscount($tariff['pricecash'], $tariff_original['price'], $bitrix_delivery_price, $config['sale_discount_cod']);
		if ($price >= 0) {
			$base_currency = CDeliveryEDOST::GetRUB();
			$r = $shipment->setFields(array('CUSTOM_PRICE_DELIVERY' => 'Y', 'PRICE_DELIVERY' => edost_class::GetPrice('value', $price, $base_currency, $order->getCurrency())));
			if (!$r->isSuccess()) return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, null, null, 'sale');
			$arUserResult['CALCULATE_PAYMENT'] = true;
		}

	}


	// вызывается после расчета доставки
	function OnSCOrderOneStepDeliveryHandler(&$arResult, &$arUserResult, &$arParams, $isNew = false) {
//		echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arUserResult, true).'</pre>';

		if (empty($arResult['DELIVERY'])) return;

		if (empty($arResult['edost']['config'])) return;
		$config = $arResult['edost']['config'];

		$bitrix_delivery_id = $arUserResult['DELIVERY_ID'];
		$address_id = (isset($arResult['edost']['address_id']) && !$locations_installed ? $arResult['edost']['address_id'] : -1);

		if (!isset($arResult['edost']['format'])) return;
		$format = $arResult['edost']['format'];
//		echo '<br><b>FORMAT name:</b> <pre style="font-size: 12px">'.print_r($format['data'], true).'</pre>';

		$ar = $format;
		if (!empty($ar['data'])) foreach ($ar['data'] as $f_key => $f) foreach ($f['tariff'] as $k => $v) if (!empty($v['automatic'])) $ar['data'][$f_key]['tariff'][$k]['id'] = $v['automatic']; // поддержка старого шаблона eDost (для вывода иконок)
		$arResult['edost']['format'] = $ar;

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$format_data = GetMessage('EDOST_DELIVERY_FORMAT');
		$base_currency = CDeliveryEDOST::GetRUB();

		// перевод форматированных тарифов обратно в формат битрикса (для стандартного шаблона)
		$ar = array();
		if (!empty($format['data'])) foreach ($format['data'] as $f_key => $f) for ($i = 0; $i <= 1; $i++) foreach ($f['tariff'] as $k => $v) if (!empty($v['id']) && ($i == 0 && empty($v['to_office']) || $i == 1 && !empty($v['to_office']))) {
			$id = $v['id'];
			$bitrix_tariff = (isset($arResult['DELIVERY'][$id]) ? $arResult['DELIVERY'][$id] : false);

			if (!empty($bitrix_tariff['OWN_NAME']) && $v['automatic'] == 'edost' && $config['template'] == 'N3') $name = $bitrix_tariff['OWN_NAME'];
			else {
				$name = (isset($v['name']) ? $v['name'] : '');
				if (!empty($v['insurance'])) $name .= (!empty($name) ? ' ' : '').$v['insurance'];
				if (!empty($v['company'])) $name = $v['company'].(!empty($name) ? ' ('.$name.')' : '');
			}

			if ($v['automatic'] == 'edost') {
				if ($config['template'] == 'Y') $v['description'] = $sign['template_warning'].(!empty($v['description']) ? '<br>'.$v['description'] : '');
				else if ($v['tariff_id'] == 29 && isset($format['pickpointmap'])) $v['pickpointmap'] = $format['pickpointmap'];
				else if (in_array($v['format'], CDeliveryEDOST::$office_key) && !empty($format['office'][$v['office_key']])) $v['office_data'] = $format['office'][$v['office_key']];

				// эксклюзивный тариф
				if (isset($ar[$id])) {
					$a = true;
					if (!empty($v['office_data'])) {
						$tariff = CDeliveryEDOST::GetEdostTariff($v['profile'], isset($v['office_key']) ? array('office_key' => $v['office_key']) : false);
						if (!isset($tariff['error'])) {
							$office_id = self::GetOffice(array('tariff' => $tariff, 'office_data' => $v['office_data'], 'map' => $config['map']));
							if (!empty($office_id) && !empty($v['to_office'])) {
								if ($v['to_office'] == $v['office_data'][$office_id]['type']) $a = false;

								if (!isset($ar[$id]['priceoffice'])) $ar[$id]['priceoffice'] = array();
								if (isset($v['pricetotal_formatted'])) $ar[$id]['priceoffice'][$v['to_office']] = $v['pricetotal_formatted'];
							}
						}
					}
					if ($a) continue;
				}

				if (!empty($v['error']))
					if ($config['template'] == 'N3') $name .= ' ('.$v['error'].')';
					else $name .= '<br><font color="#FF0000">'.$v['error'].'</font>';
			}

			if ($bitrix_tariff !== false) $s = $bitrix_tariff;
			else $s = array('ID' => $id, 'FIELD_NAME' => 'DELIVERY_ID', 'EXTRA_SERVICES' => array(), 'STORE' => array(), 'SORT' => 100, 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']);

			if ($v['automatic'] == 'edost' || $config['template'] == 'N2' || $bitrix_tariff === false) {
				$s = array_merge($s, array(
					'NAME' => $name,
					'DESCRIPTION' => (isset($v['description']) ? $v['description'] : ''),
					'PRICE' => (isset($v['price']) ? $v['price'] : ''),
					'PRICE_FORMATED' => (isset($v['price_formatted']) ? $v['price_formatted'] : ''),
					'PERIOD_TEXT' => (isset($v['day']) ? $v['day'] : ''),
				));

				if (!empty($v['price_original'])) {
					$s['DELIVERY_DISCOUNT_PRICE'] = $s['PRICE'];
					$s['DELIVERY_DISCOUNT_PRICE_FORMATED'] = $s['PRICE_FORMATED'];
					$s['PRICE'] = $v['price_original'];
					$s['PRICE_FORMATED'] = $v['price_original_formatted'];
				}
				if (isset($ar[$id]['priceoffice'])) $s['priceoffice'] = $ar[$id]['priceoffice'];
			}

			if ($v['automatic'] != 'edost' && $config['template'] == 'N2' && empty($v['error'])) {
				if ($s['PRICE'] == 0) $s['PRICE_FORMATED'] = $sign['free_bitrix'];
				if (isset($s['DELIVERY_DISCOUNT_PRICE']) && $s['DELIVERY_DISCOUNT_PRICE'] == 0) $s['DELIVERY_DISCOUNT_PRICE_FORMATED'] = $sign['free_bitrix'];
			}

			if ($config['template'] == 'N3') {
				if (isset($v['sort'])) $s['SORT'] = $v['sort'];
				if (isset($v['priceinfo'])) $s['pricetotal_formatted'] = $v['pricetotal_formatted'];
			}

			if (!empty($v['error']) && $v['automatic'] != 'edost') $s['CALCULATE_ERRORS'] = $v['error'];
			if (!empty($v['pickpointmap'])) $s['pickpointmap'] = $v['pickpointmap'];
			if (!empty($v['office_data'])) $s['office_data'] = $v['office_data'];
			if ($v['automatic'] == 'edost') $s['profile'] = $v['profile'];
			if (isset($v['office_key'])) $s['office_key'] = $v['office_key'];
			if (isset($v['format'])) $s['format'] = $v['format'];

			if ($id == $format['active']['id']) $s['CHECKED'] = 'Y';
			else if (isset($s['CHECKED'])) unset($s['CHECKED']);

			$ar[$id] = $s;
		}
		$arResult['DELIVERY'] = $ar;

		// данные для стандартного шаблона (стоимость доставки, дни и офисы)
		if ($config['template'] != 'Y') {
			$office_data = false;
			$office_set = self::GetOffice();
			$div = ($config['template'] == 'N3' ? true : false);

			foreach ($ar as $k => $v) {
				if (empty($v['profile'])) continue;
				$profile = $v['profile'];
				$tariff = CDeliveryEDOST::GetEdostTariff($profile, isset($v['office_key']) ? array('office_key' => $v['office_key']) : false);
				if (isset($tariff['error'])) continue;

				// офисы
				if (isset($v['office_data'])) {
					$company_id = $tariff['company_id'];
					$office_number = count($v['office_data']);

					$office_id = self::GetOffice(array('tariff' => $tariff, 'office_data' => $v['office_data'], 'map' => $config['map']));
					if ($office_id == 0 && $company_id == 23 && !empty($arResult['edost']['order_prop2']['zip_full'])) $office_id = $arResult['edost']['order_prop2']['zip'];

					if ($office_id != 0) {
						$o = $v['office_data'][$office_id];
						$tariff = CDeliveryEDOST::GetEdostTariff($profile, $o);

						if ($v['CHECKED'] == 'Y') {
							$arResult['edost']['format']['active']['address'] = edost_class::GetOfficeAddress($o, $tariff);
							$arResult['edost']['format']['active']['office_id'] = $office_id;
							if (isset($o['codmax']) && $tariff['pricecash'] > $o['codmax'] || !empty($o['cod_disable'])) $arResult['edost']['format']['active']['cod'] = false;
							$arResult['edost']['format']['active']['office_type'] = $o['type'];
							$arResult['edost']['format']['active']['office_options'] = $o['options'];
							$arResult['edost']['format']['active']['office_city'] = $o['city'];
						}

						$s = $sign['delivery'];
						if (in_array($o['type'], CDeliveryEDOST::$postamat)) $s = $s['postamat'];
						else if ($company_id == 23 && $o['type'] == 1) $s = $s['post'];
//						else $s = $s[$tariff['format']];
						else $s = $s[$v['format']];
						$head = $s;
					}
					else if ($v['CHECKED'] == 'Y') {
						$arResult['edost']['format']['active']['address'] = '';
						$arResult['edost']['format']['active']['office_id'] = 0;
					}

					if ($config['map'] == 'Y') {
						$s = '';
						$office_link = '';
						if ($office_id != 0) {
							if ($office_number != 1) $office_link = '<br>'.$sign['change'.($v['format'] == 'postmap' ? '_postmap' : '')];
							$s .= '<span class="edost_address_head_n" style="font-size: 14px; color: #888;'.($div ? ' display: block;' : '').'">'.$head.':</span> <b style="font-size: 14px;">'.edost_class::GetOfficeAddress($o, $tariff, false).'</b>';
							if ($office_number == 1 && (!isset($o['detailed']) || $o['detailed'] !== 'N')) $s .= ' <a class="edost_link edost_address_map_n" style="'.($div ? ' display: block;' : '').'" onclick="window.edost.office.info(0, \''.edost_class::GetOfficeLink($o).'\'); return false;">'.$sign['map'].'</a>';
						}
						else {
//							echo '<br><b>FORMAT name:</b> <pre style="font-size: 12px">'.print_r($ar, true).'</pre>';
//							die();

							if ($company_id == 26) $office_link = $sign['postamat']['format_get'];
//							else $office_link = $format_data[$tariff['format']]['get'];
							else $office_link = $format_data[$v['format']]['get'];
						}
						if ($office_link != '') $s .= '<span'.($config['template'] == 'N3' ? ' id="edost_get_office_span_'.$v['ID'].'"' : '').' class="edost_format_link'.($office_id == 0 ? '_big' : '').'" onclick="window.edost.office.set(\'profile_'.$profile.'_'.$v['ID'].'\');">'.$office_link.'</span>';
						$s = '<div class="edost">'.$s.'</div>';
					}
					else {
						$s = $head;
						$set_office = 'edost_SetOffice('.$profile.', '.$v['ID'].')';

						if ($div) $s = '<div class="edost"><span style="color: #888;">'.$s.':</span><br>';
						else $s = '<td>'.$s.':</td><td style="padding-left: 5px;">';

						$link = false;
						if ($office_number != 1) $s .= '<select id="edost_office_'.$profile.'" style="width: 100%; max-width: 250px;" onchange="'.$set_office.'">';
						foreach ($v['office_data'] as $o) {
							$value = $o['id'];
							$link = edost_class::GetOfficeLink($o);
							if ($link != '') $value .= '|'.$link;
							if ($office_number == 1) $s .= '<b>'.$o['address'].'</b>'.'<input type="hidden" id="edost_office_'.$profile.'" value="'.$value.'">';
							else $s .= '<option '.($o['id'] == $office_id ? 'selected="selected"' : '').' value="'.$value.'">'.($v['format'] == 'postmap' ? $o['code'].', ' : '').$o['address'].(in_array($o['type'], CDeliveryEDOST::$postamat) ? ' ('.$sign['postamat']['name_address'].')' : '').(!empty($v['priceoffice'][$o['type']]) ? ' ('.$v['priceoffice'][$o['type']].')' : '').'</option>';
						}
						if ($office_number != 1) $s .= '</select>';

						if ($div) $s .= '<br>';
						else $s .= '</td><td style="padding-left: 10px;">';
						if ($link) $s .= '<a href="#" style="cursor: pointer; text-decoration: none; font-size: 11px;" onclick="edost_OpenMap('.$profile.'); return false;" >'.$sign[($div ? 'map' : 'map2')].'</a>';
						if ($div) $s .= '</div>';
						else {
							$s .= '</td>';
							$s = '<table class="edost_office_table" style="display: inline; margin: 0px;" border="0" cellspacing="0" cellpadding="0"><tr style="padding: 0px; margin: 0px;">'.$s.'</tr></table>';
						}

						$v['onclick'] = $set_office;
					}

					if ($config['map'] == 'Y' && !empty($arResult['edost']['format']['map_json']) && ($config['template'] == 'N3' && $v['CHECKED'] == 'Y' || $config['template'] != 'N3' && !$office_data)) {
						$office_data = true;
						$s .= '<input id="edost_office_data" autocomplete="off" value=\'{"ico_path": "/bitrix/images/delivery_edost_img", "yandex_api_key": "'.(!empty($arResult['edost']['yandex_api_key']) ? $arResult['edost']['yandex_api_key'] : '').'", '.$arResult['edost']['format']['map_json'].'}\' type="hidden">';
					}
					if ($v['CHECKED'] == 'Y' && $config['template'] == 'N3' && $address_id != -1) {
						$s .= '<input type="hidden" value="" id="edost_office" name="edost_office">';
						$s .= '<input type="hidden" value="'.$address_id.'" id="edost_address_input">';
					}

					$v['office'] = $s;

					if ($config['template'] == 'N3' && (!defined('DELIVERY_EDOST_OFFICE_DATA_SALE_ORDER_AJAX') || DELIVERY_EDOST_OFFICE_DATA_SALE_ORDER_AJAX != 'Y')) unset($v['office_data']);
				}

				$tariff['price_formatted'] = edost_class::GetPrice('formatted', $tariff['price'], $base_currency, $arResult['BASE_LANG_CURRENCY']);

				if ($profile == 0 || !empty($tariff['priceinfo'])) $p = '';
				else if ($tariff['price'] == 0) $p = $sign['free_bitrix'];
				else $p = $tariff['price_formatted'];
				$v['price'] = $p;

				if (!empty($tariff['day'])) $v['day'] = $tariff['day'];

				if (!empty($tariff['priceinfo'])) {
					$v['price_backup'] = $tariff['price_formatted'];
					$v['priceinfo'] = edost_class::GetPrice('formatted', $tariff['priceinfo'], $base_currency, $arResult['BASE_LANG_CURRENCY']);

					$s0 = $v['DESCRIPTION'];
					$s1 = str_replace('%price_info%', $v['priceinfo'], $sign['priceinfo_warning_bitrix']);

					if (isset($v['DELIVERY_DISCOUNT_PRICE'])) $s2 = ($v['DELIVERY_DISCOUNT_PRICE'] > 0 ? str_replace('%price%', $v['DELIVERY_DISCOUNT_PRICE_FORMATED'], $sign['priceinfo_description']) : '');
					else $s2 = ($v['PRICE'] > 0 ? str_replace('%price%', $v['PRICE_FORMATED'], $sign['priceinfo_description']) : '');

					$v['DESCRIPTION'] = $s1 . ($s1 != '' && $s2 != '' ? '<br>' : '') . $s2 . (($s1 != '' || $s2 != '') && $s0 != '' ? '<br>' : '') . $s0;
				}

				if ($tariff['id'] == 3 && empty(CDeliveryEDOST::$result['order']['location']['bitrix']['city'])) self::SetWarning($v, 'ems_warning');

//				if (!empty($tariff['format'])) {
//					if ($tariff['format'] == 'house') self::SetWarning($v, 'house_warning');
//					if ($tariff['format'] == 'terminal' && $office_number > 1) self::SetWarning($v, 'terminal_warning');
//				}
				if (!empty($v['format'])) {
					if ($v['format'] == 'house') self::SetWarning($v, 'house_warning');
					if ($v['format'] == 'terminal' && $office_number > 1) self::SetWarning($v, 'terminal_warning');
				}

				// PickPoint
				if ($profile == 57 && !empty($v['pickpointmap'])) {
					$arResult['edost']['pickpoint_id'] = $v['ID'];

					if (isset($_SESSION['EDOST']['location_pickpoint']) && $_SESSION['EDOST']['location_pickpoint'] != $arUserResult['DELIVERY_LOCATION']) $_SESSION['EDOST']['address'][$tariff['company_id']] = '';
					else if ($office_set === 'pickpoint') {
						if (isset($arResult['edost']['order_prop']['ADDRESS'])) $address = $arResult['edost']['order_prop']['ADDRESS']['value'];
						else $address = (isset($arResult['edost']['address_value']) ? $arResult['edost']['address_value'] : '');
						$_SESSION['EDOST']['address'][$tariff['company_id']] = $address;
					}

					$s = (isset($_SESSION['EDOST']['address'][$tariff['company_id']]) ? $_SESSION['EDOST']['address'][$tariff['company_id']] : '');
					if ($v['CHECKED'] == 'Y') {
						$arResult['edost']['format']['active']['address'] = $s;
//						if (strpos($s, $sign['postamat']['name'].' PickPoint') === 0) $arResult['edost']['cod_description2'] = true;
					}
					if ($s != '') {
						$s1 = explode(': ', $s);
						$s2 = explode(', '.$sign['code'].': ', $s);
						$s = ($s1[0] == $sign['postamat']['name'].' PickPoint' ? $sign['delivery']['postamat'] : $sign['delivery']['office']).': <b>'.str_replace($s1[0].': ', '', $s2[0]).'</b><br>';
					}
					else {
						$s = $sign['postamat']['get'];
						$v['onclick'] = "PickPoint.open(EdostPickPoint,{city:'".$v['pickpointmap']."', ids:null}); edost_SubmitActive('set'); submitForm();";
					}
					$v['office'] = '<a style="color: #A00; text-decoration: none;" href="#" id="EdostPickPointRef" onclick="PickPoint.open(EdostPickPoint,{city:\''.$v['pickpointmap'].'\', ids:null}); return false;">'.$s.'</a>';
				}

				if ($config['template'] == 'N3') {
					if ($config['map'] == 'Y' && $config['postmap'] == 'Y' && $v['CHECKED'] == 'Y' && $address_id != -1) $v['DESCRIPTION'] .= '<input id="edost_post_manual" name="edost_post_manual" value="'.(!empty($config['POST_MANUAL']) ? 'Y' : '').'" type="hidden">';

					if (!empty($v['office'])) {
						if ($office_id != 0 && ($o['options'] & 256)) $v['DESCRIPTION'] = str_replace('%warning%', $sign['full_warning'], $sign['bitrix_warning']).(!empty($v['DESCRIPTION']) ? '<br>' : '').$v['DESCRIPTION'];
						$v['DESCRIPTION'] = $v['office'].(!empty($v['DESCRIPTION']) ? '<br>' : '').$v['DESCRIPTION'];
						unset($v['office']);
					}

					if (isset($v['DELIVERY_DISCOUNT_PRICE'])) {
						if (isset($v['pricetotal_formatted'])) {
							$v['DELIVERY_DISCOUNT_PRICE_FORMATED'] = $v['pricetotal_formatted'];
							$v['PRICE_FORMATED'] = '';
						}
						else if ($v['DELIVERY_DISCOUNT_PRICE'] == 0) $v['DELIVERY_DISCOUNT_PRICE_FORMATED'] = $sign['free'];
					}
					else if (isset($v['pricetotal_formatted'])) $v['PRICE_FORMATED'] = $v['pricetotal_formatted'];
					else if ($v['PRICE_FORMATED'] == '0') $v['PRICE_FORMATED'] = $sign['free'];
				}

				$arResult['DELIVERY'][$k] = $v;
			}
		}

	}


	// вызывается после обработки платежных систем
	function OnSCOrderOneStepPaySystemHandler(&$arResult, &$arUserResult, &$arParams, $isNew = false) {
//		echo '<br><b>arResult[DELIVERY]:</b> <pre style="font-size: 12px">'.print_r($arResult['DELIVERY'], true).'</pre>';

		if (!empty($arResult['edost']['fast'])) $arResult['ERROR'] = array();

		if (empty($arResult['edost']['config'])) return;
		$config = $arResult['edost']['config'];

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$error = array();
		$arResult['edost']['javascript'] = '';
		$locations_installed = (!empty($arResult['edost']['locations_installed']) ? true : false);
		$format_active = (!empty($arResult['edost']['format']['active']) ? $arResult['edost']['format']['active'] : false);
		$address_id = (isset($arResult['edost']['address_id']) && !$locations_installed ? $arResult['edost']['address_id'] : -1);
		$address = (isset($arResult['edost']['address_value']) ? $arResult['edost']['address_value'] : '');
		if ($address_id != -1 && !isset($arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id])) $address_id = -1;
		if ($address_id != -1) $arResult['edost']['javascript'] .= '<input type="hidden" value="ORDER_PROP_'.$address_id.'" id="edost_address_input">';
		$bonus = (!empty($arResult['edost']['bonus']) ? $arResult['edost']['bonus'] : false);
		$prepay_join = (empty($arParams['COMPACT_PREPAY_JOIN']) || $arParams['COMPACT_PREPAY_JOIN'] == 'Y' ? true : false);
		$compact = (!empty($arParams['COMPACT']) && $arParams['COMPACT'] != 'off' ? true : false);
		$priority = (!empty($arParams['PRIORITY']) ? $arParams['PRIORITY'] : '');
		$cod_tariff = (!empty($arResult['edost']['format']['cod_tariff']) ? true : false);
		$cod_tariff_active = (!empty($format_active['cod_tariff']) ? true : false);
		if ($cod_tariff && $cod_tariff_active) $arUserResult['PAY_CURRENT_ACCOUNT'] = false;

		// предупреждения модуля edost (warning)
		$warning = CDeliveryEDOST::GetEdostWarning();
		if ($warning != '') {
			// вывод ошибки при подтверждении заказа, если перед оформлением была выбрана почта (наземная, посылка онлайн, курьер онлайн) и есть предупреждение по индексу (в N3 не обрабатывается!!!)
			if ($config['template'] != 'N3' && isset($arResult['edost']['active']['profile']) && in_array(ceil(intval($arResult['edost']['active']['profile']) / 2), CDeliveryEDOST::$zip_required))
				foreach (CDeliveryEDOST::$result['warning'] as $v) if (in_array($v, array(1, 2))) $error['zip'] = $v;

			// для стандартного шаблона
			if ($config['template'] != 'Y') $arResult['edost']['warning'] = '<span id="edost_warning" style="color: #F00; font-weight: bold;">'.$warning.'</span>';
			if ($config['template'] == 'N3') $arResult['WARNING']['DELIVERY'][] = $warning . $sign['post_zip'];
		}

		// сохранение нового адреса в поле ADDRESS
		if ($address_id != -1) {
			$office_set = self::GetOffice();
			$address_readonly = (isset($format_active['address']) ? true : false);
			$address_new = ($address_readonly ? $format_active['address'] : false);

			if (empty($_SESSION['EDOST']['readonly']) && $office_set !== 'pickpoint') {
				$office = edost_class::ParseOfficeAddress($address);
				if (empty($office)) $_SESSION['EDOST']['address'][0] = $address;
			}
			else if (!$address_readonly) $address_new = (isset($_SESSION['EDOST']['address'][0]) ? $_SESSION['EDOST']['address'][0] : '');

			if ($address_new !== false) {
				$address = $address_new;
				$arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] = $address;
				$arUserResult['ORDER_PROP'][$address_id] = $address;
			}

			$_SESSION['EDOST']['readonly'] = $address_readonly;
			if ($config['template'] != 'Y') $_SESSION['EDOST']['location_pickpoint'] = $arUserResult['DELIVERY_LOCATION'];
		}

		// удаление способов оплаты, если нет способов доставки или доставка не выбрана
		if ($config['hide_payment'] == 'Y' && ($priority == '' || $priority != 'C') && (empty($arResult['edost']['format']['count']) || empty($arUserResult['DELIVERY_ID']))) $arResult['PAY_SYSTEM'] = array();

		// ошибка "не выбран способ доставки"
		if ($config['autoselect'] != 'Y' && !empty($arResult['edost']['format']['count']) && empty($arUserResult['DELIVERY_ID'])) $error['delivery'] = true;

		// ошибка "не выбрана точка самовывоза"
		if (!empty($address_readonly) && $arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] == '') $error['office'] = true;

		// вывод ошибок
		if (!empty($error) && ($arUserResult['CONFIRM_ORDER'] == 'Y' || $config['template'] == 'N3')) self::SetError($arResult, $error, $config['template']);

		// удаление наложенного платежа для тарифов без наложки
		$tariff = false;
		$count = count($arResult['PAY_SYSTEM']);
		if (!empty($format_active['automatic']) && $format_active['automatic'] == 'edost' && $format_active['profile'] !== '') {
			$tariff = CDeliveryEDOST::GetEdostTariff($format_active['profile'], $format_active);
			if (!isset($tariff['pricecash']) || $tariff['pricecash'] < 0) $tariff = false;
			if (empty($format_active['cod'])) $tariff = false;
			if ($cod_tariff && !$cod_tariff_active) $tariff = false;
		}
		$active = $set = $edost = false;
		foreach ($arResult['PAY_SYSTEM'] as $k => $v) {
			if (substr($v['PSA_ACTION_FILE'], -11) == 'edostpaycod') {
				if ($tariff !== false) {
					$edost = $k;
					if ($v['CHECKED'] == 'Y' && (!$compact || $priority == 'B')) $arResult['PAY_SYSTEM'][$k]['edost_cod'] = true;
				}
				else if ($compact && $priority == 'B') {
					if (empty($arResult['edost']['format']['cod'])) unset($arResult['PAY_SYSTEM'][$k]);
					else {
						$arResult['PAY_SYSTEM'][$k]['CHECKED'] = 'N';
						$arResult['PAY_SYSTEM'][$k]['compact'] = true;
						$arResult['PAY_SYSTEM'][$k]['edost_cod'] = true;
						$arResult['PAY_SYSTEM'][$k]['disable'] = true;
					}
					continue;
				}
				else if (!empty($arResult['edost']['format']['cod_delete'])) {
					unset($arResult['PAY_SYSTEM'][$k]);
					continue;
				}
			}
			if ($set === false) $set = $k;
			if ($v['CHECKED'] == 'Y') $active = $k;
		}
		if ($cod_tariff && $cod_tariff_active && $active !== false && $edost !== false && $active != $edost) {
			unset($arResult['PAY_SYSTEM'][$active]);
			$active = false;
			$set = $edost;
		}

		// выделение первого способа оплаты, если нет активных
		if ($active === false && $set !== false && (empty($arUserResult['PAY_CURRENT_ACCOUNT']) || $arUserResult['PAY_CURRENT_ACCOUNT'] !== 'Y' || $cod_tariff && $cod_tariff_active)) {
			$arResult['PAY_SYSTEM'][$set]['CHECKED'] = 'Y';
			$active = $set;
		}

		// служебное описание для активной оплаты (в описании оплаты текст экранирован тэгами [active] ... [/active])
		foreach ($arResult['PAY_SYSTEM'] as $k => $v) if (!empty($v['DESCRIPTION'])) {
			$s = edost_class::service_string('active', $v['DESCRIPTION']);
			if (!empty($s) && $v['CHECKED'] == 'Y') $v['note_active'] = $s;
			$arResult['PAY_SYSTEM'][$k] = $v;
		}

		// учет наценок наложенного платежа
		if ($edost !== false && $tariff !== false) {
			$v = $arResult['PAY_SYSTEM'][$edost];
			$base_currency = CDeliveryEDOST::GetRUB();
			$note = $note_active = $warning = $description = array(); // $note_active - выводится только у выбранной оплаты

			if (!empty($v['note_active'])) $note_active[] = $v['note_active'];

			// нестандартное название и описание
			if ($priority != 'C' && $config['template'] != 'N3') {
				$ar = GetMessage('EDOST_DELIVERY_COD');
				if (is_array($ar)) foreach ($ar as $k => $s) if (in_array($tariff['id'], $s['tariff'])) {
					if ($k !== 0 || empty($arParams['COD_POST_NAME']) || $arParams['COD_POST_NAME'] == 'Y') {
						if (isset($s['name'])) $v['PSA_NAME'] = $s['name'];
						if (!$compact || !$prepay_join) if (isset($s['description'])) $v['DESCRIPTION'] = $s['description'];
					}
//					if (isset($s['description2']) && (!empty($arResult['edost']['cod_description2']) || !empty($format_active['office_type']) && in_array($format_active['office_type'], CDeliveryEDOST::$postamat))) $v['DESCRIPTION'] = $s['description2'];
//					if (isset($s['description2']) && !empty($arResult['edost']['cod_description2'])) $v['DESCRIPTION'] = $s['description2'];
				}
			}

			if (!empty($format_active['office_options'])) {
				$p = $format_active['office_options'] & 6;
				if ($priority == '') {
					if ($p == 4) $description[] = $sign['paysystem_card'];
					if ($p == 6) self::SetWarning($description, 'paysystem_card2');
				}
				else {
					if ($p == 4) $note[] = $sign['paysystem_card'];
					if ($p == 6) $warning[] = $sign['paysystem_card2'];
				}
			}

			if (!empty($format_active['pricecashplus']) || !empty($format_active['transfer'])) {
				if (!empty($format_active['pricecashplus'])) $v['codplus'] = str_replace('%codplus%', $format_active['pricecashplus_formatted'], $sign['codplus']);
				if (!empty($format_active['transfer'])) $v['transfer'] = str_replace('%transfer%', $format_active['transfer_formatted'], $sign['transfer']);
				if (!empty($format_active['codtotal'])) $v['codtotal'] = str_replace('%codtotal%', $format_active['codtotal_formatted'], $sign['codtotal']);

				// надбавки для шаблона eDost_2019
				if ($priority == 'P' || $priority == 'B') {
					$s = array();
					if (!empty($format_active['pricecashplus'])) $s[] = array('red', $format_active['pricecashplus_formatted']);
					if (!empty($format_active['transfer'])) $s[] = array('red', $format_active['transfer_formatted']);
					if (!empty($s)) $v['discount'] = $s;

					if ($v['CHECKED'] == 'Y' && !empty($format_active['cod_note'])) $note_active[] = $format_active['cod_note'];
				}
			}

			// в стандартном шаблоне информация по наценкам добавляется в описание
			if ($config['template'] != 'Y') {
				$s = array();
				$ar = array('codplus', 'transfer', 'codtotal');
				foreach ($ar as $v2) if (!empty($v[$v2])) $s[] = ($v2 == 'transfer' ? '<font color="#FF0000">'.$v[$v2].'</font>' : $v[$v2]);
				if (!empty($s)) $description[] = implode('<br>', $s);
			}

			if (!empty($note)) $v['note'] = implode('<br>', $note);
			if (!empty($note_active)) $v['note_active'] = implode('<br>', $note_active);
			if (!empty($warning)) $v['warning'] = implode('<br>', $warning);
			if (!empty($description)) {
				if (isset($v['DESCRIPTION'])) {
					$s = trim($v['DESCRIPTION']);
					if ($s != '') $description = array_merge(array($s), $description);
				}
				$v['DESCRIPTION'] = implode('<br><br>', $description);
			}

			$arResult['PAY_SYSTEM'][$edost] = $v;
		}
		if ($config['template'] == 'N3' && $count != count($arResult['PAY_SYSTEM'])) $arResult['PAY_SYSTEM'] = array_values($arResult['PAY_SYSTEM']);

		// обработка бонусов и компктного режима для шаблона eDost_2019
		if (!empty($bonus) || $compact) {
			$prepay = false;
			$prepay_count = 0;
			foreach ($arResult['PAY_SYSTEM'] as $k => $v) if (empty($v['disable'])) {
				if (isset($bonus['payment'][$v['ID']])) {
					if (!isset($v['discount'])) $v['discount'] = array();
					if (empty($arParams['PAYMENT_BONUS']) || $arParams['PAYMENT_BONUS'] == 'Y') $v['discount'][] = array($bonus['payment'][$v['ID']] < 0 ? 'green' : 'red', edost_class::GetPrice('formatted', abs($bonus['payment'][$v['ID']]), '', $arResult['BASE_LANG_CURRENCY']));
				}
				if (isset($bonus['delivery'][$v['ID']]) && !empty($config['DELIVERY_BONUS']) && $config['PRIORITY'] == 'C') {
					$v['delivery_bonus'] = $bonus['delivery'][$v['ID']];
				}

				if ($compact) {
					if ($v['CHECKED'] == 'Y') $v['supercompact'] = true;
					if (substr($v['PSA_ACTION_FILE'], -11) == 'edostpaycod') {
						$v['compact'] = true;
						$v['edost_cod'] = true;
					}
					else {
						if ($prepay_join) $prepay_count++; else $v['compact'] = true;
						if ($prepay === false || $v['CHECKED'] == 'Y' || $_SESSION['EDOST']['prepay'] == $v['ID']) $prepay = $k;
						if ($v['CHECKED'] == 'Y') $_SESSION['EDOST']['prepay'] = $v['ID'];
					}
				}

				$arResult['PAY_SYSTEM'][$k] = $v;
			}
			if ($compact) {
				if ($prepay !== false) $arResult['PAY_SYSTEM'][$prepay]['compact'] = true;
				if ($prepay_count > 1) $arResult['edost']['format']['prepay_change'] = true;
			}
		}

		if ($config['template'] == 'Y') {
			if (!isset($arResult['edost']['format'])) $arResult['edost']['format'] = false;
		}
		else if (isset($arResult['edost']['format'])) unset($arResult['edost']['format']);

		// javascript - офисы (стандартный шаблон)
		if (!in_array($config['template'], array('Y', 'N3')) && ($address_id != -1 || $locations_installed)) $arResult['edost']['javascript'] .= '
		<input type="hidden" value="" id="edost_office" name="edost_office">';

		// javascript - PickPoint (стандартный шаблон)
		if (!in_array($config['template'], array('Y', 'N3')) && ($address_id != -1 || $locations_installed) && $config['map'] == 'Y' && !empty($arResult['edost']['pickpoint_widget'])) $arResult['edost']['javascript'] .= '
		<input type="hidden" value="" id="edost_submit_active">
		<script type="text/javascript">
			function edost_SubmitActive(n) {
				var E = document.getElementById("edost_submit_active");
				if (E) {
					if (n == "set") E.value = "Y";
					else return (E.value == "Y" ? true : false);
				}
			}

			function EdostPickPoint(rz) {
				if (edost_SubmitActive("get")) return false;

				var s = (rz[\'name\'].substr(0, 3) == "'.$sign['postamat']['pvz'].'" ? "'.$sign['office'].'" : "'.$sign['postamat']['name'].'") + " PickPoint: ";

				if (rz[\'shortaddress\'] != undefined) rz[\'address\'] = rz[\'shortaddress\'];
				var i = rz[\'address\'].indexOf("'.$sign['postamat']['rf'].'");
				if (i > 0) rz[\'address\'] = rz[\'address\'].substr(i + 22);
				var s2 = rz[\'name\'];
				var i = s2.indexOf(":");
				if (i > 0) s2 = s2.substr(i + 1).replace(/^\s+/g, "");
				s2 = s2.trim();
				if (s2 != "") rz[\'address\'] += " (" + s2 + ")";

				rz[\'id\'] = ", '.$sign['code'].': " + rz[\'id\'];

				s += rz[\'address\'] + rz[\'id\'];

				var E = document.getElementById("edost_shop_ADDRESS");
				if (E) E.value = s;
				else {
					E = document.getElementById("edost_address_input");
					if (E) E = document.getElementById(E.value);
					if (E) E.value = s;
				}
				if (!E) return;

				var E = document.getElementById("EdostPickPointRef");
				if (E) E.innerHTML = "'.$sign['loading'].'";

				var E = document.getElementById("edost_office");
				if (E) E.value = "pickpoint";

				var E = document.getElementById("ID_DELIVERY_'.(!empty($arResult['edost']['pickpoint_id']) ? 'ID_'.$arResult['edost']['pickpoint_id'] : 'edost_57').'");
				if (E && !E.checked) E.checked = true;

		        submitForm();
			}
		</script>';

		// javascript - блокировка поля ADDRESS, если выбран тариф с офисом
		if ($config['template'] != 'N3' && $address_id != -1) $arResult['edost']['javascript'] .= '
		<script type="text/javascript">
			var E = document.getElementById(document.getElementById("edost_address_input").value);
			if (E) {'.
				(!empty($address_readonly) ? '
				E.readOnly = true; E.style.color = "#707070"; E.style.backgroundColor = "#E0E0E0";' : '
				E.readOnly = false; E.style.color = "#000000"; E.style.backgroundColor = "#FFFFFF";').'
			}
		</script>';

	}

}
?>