<?php

namespace Acrit\Core\Orders\Plugins\SbermegamarketHelpers;

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
	public function checkConnection($token, &$message) {
		$result = false;
		$res = $this->request('/order/search', [
//			'dateFrom' => date(self::DATE_FORMAT, strtotime('today')),
			'count' => 1,
		], $token);
		if (!$res['success']) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . implode('. ', $res['error']);
		}
		else {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_SUCCESS');
			$result = true;
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
				'dateFrom' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
				'count' => $limit,
			];
			$req_filter = array_merge($req_filter, $filter);
			$res = $this->request('/order/search', $req_filter);
			if ($res['data']['shipments']) {
				$list = $res['data']['shipments'];
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
			'dateFrom' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
		];
		$req_filter = array_merge($req_filter, $filter);
		$res = $this->request('/order/search', $req_filter);
		if ($res['success']) {
			$count = count($res['data']['shipments']);
		}
		return $count;
	}

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$result = false;
		$resp = $this->request('/order/get', [
			'shipments' => [
				$order_id,
			],
		]);
		if ($resp['data']['shipments']) {
			$result = $resp['data']['shipments'][0];
		}
		return $result;
	}
}
