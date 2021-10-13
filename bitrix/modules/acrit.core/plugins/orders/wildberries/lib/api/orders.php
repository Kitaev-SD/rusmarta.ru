<?php

namespace Acrit\Core\Orders\Plugins\WildberriesHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request {
	protected static $stocks = [];

	public function __construct($obPlugin) {
		parent::__construct($obPlugin);
	}

	/**
	 * Check connection
	 */
	public function checkConnection(&$message) {
		$result = false;
		$res = $this->execute('/api/v2/orders', [
			'date_start' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
			'take' => 1,
			'skip' => 0,
		], [
			'METHOD' => 'GET'
		]);
		if ($res['error']) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . $res['errorText'] . ' [' . $res['error'] . ']';
		}
		elseif (isset($res['orders'])) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_SUCCESS');
			$result = true;
		}
		return $result;
	}

	/**
	 * Product info
	 */
	public function getProduct($barcode) {
		$result = [];
		// Fill static array $stocks
		if (empty(self::$stocks)) {
			$res = $this->execute('/api/v2/stocks', [
				'take' => 1000,
				'skip' => 0,
			], [
				'METHOD' => 'GET'
			]);
			foreach ($res['stocks'] as $stock) {
				self::$stocks[$stock['barcode']] = $stock;
			}
		}
		if (isset(self::$stocks[$barcode])) {
			$result = self::$stocks[$barcode];
		}
		else {
			$res = $this->execute('/api/v2/stocks', [
				'search' => $barcode,
				'take'   => 1,
				'skip'   => 0,
			], [
				'METHOD' => 'GET'
			]);
			if ($res['stocks'][0]) {
				$result = $res['stocks'][0];
			}
		}
		return $result;
	}

	/**
	 * Get orders list
	 */
	public function getOrdersList(array $filter, int $limit=1) {
		$list = [];
		if ($limit) {
			$req_filter = [
				'date_start' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
				'take' => $limit,
				'skip' => 0,
			];
			$req_filter = array_merge($req_filter, $filter);
			$res = $this->execute('/api/v2/orders', $req_filter, [
				'METHOD' => 'GET'
			]);
			if ($res['orders']) {
				// Unite orders by orderUID
				foreach ($res['orders'] as $wb_order) {
					// Existing order
					if (isset($list[$wb_order['orderUID']])) {
						// Product row already in the order
						if (isset($list[$wb_order['orderUID']]['products'][$wb_order['barcode']])) {
							$list[$wb_order['orderUID']]['products'][$wb_order['barcode']]['quantity']++;
						}
						// New product row
						else {
							$list[$wb_order['orderUID']]['products'][$wb_order['barcode']] = self::getProduct($wb_order['barcode']);
							$list[$wb_order['orderUID']]['products'][$wb_order['barcode']]['quantity'] = 1;
							$list[$wb_order['orderUID']]['products'][$wb_order['barcode']]['price'] = $wb_order['totalPrice'] / 100;
						}
					}
					// New order
					else {
						$list[$wb_order['orderUID']] = $wb_order;
						$list[$wb_order['orderUID']]['id'] = $wb_order['orderUID'];
						$list[$wb_order['orderUID']]['products'][$wb_order['barcode']] = self::getProduct($wb_order['barcode']);
						$list[$wb_order['orderUID']]['products'][$wb_order['barcode']]['quantity'] = 1;
						$list[$wb_order['orderUID']]['products'][$wb_order['barcode']]['price'] = $wb_order['totalPrice'] / 100;
					}
				}
			}
			if (!empty($list)) {
				unset($list[$wb_order['orderUID']]);
			}
		}
		return $list;
	}

	/**
	 * Get orders count
	 */
	public function getOrdersCount(array $filter, int $limit=1) {
		$count = false;
		$req_filter = [
			'date_start' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
			'take' => $limit,
			'skip' => 0,
		];
		$req_filter = array_merge($req_filter, $filter);
		$res = $this->execute('/api/v2/orders', $req_filter);
		if ($res['total']) {
			$count = $res['total'];
		}
		return $count;
	}
}
