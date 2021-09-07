<?php

namespace Acrit\Core\Orders\Plugins\WildberriesHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request {

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
				$list = $res['orders'];
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

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$object = false;
		$res = $this->execute('/v2/posting/fbs/get', [
			'posting_number' => $posting_number,
			'with' => [
				'analytics_data' => true,
			],
		], [
			'METHOD' => 'POST'
		]);
		if ($res['result']) {
			$object = $res['result'];
		}
		return $object;
	}
}
