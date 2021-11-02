<?php

namespace Acrit\Core\Orders\Plugins\RozetkaRuHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request {

//	public function __construct($strClientLogin, $strClientPwd, $strClientLang, $intProfileId, $strModuleId) {
//		parent::__construct($strClientLogin, $strClientPwd, $strClientLang, $intProfileId, $strModuleId);
//	}
	public function __construct($obPlugin) {
		parent::__construct($obPlugin);
	}

	/**
	 * Check connection
	 */
	public function checkConnection($token, &$message) {
		$result = false;
		$message = '';
		$res = $this->execute('markets/business-types', null, [], $token);
		if ($res['error']) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_ROZETKA_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		elseif (isset($res['success'])) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_ROZETKA_CHECK_SUCCESS');
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
				'sort'         => 'id',
				'types'        => 1,
				'created_from' => date(self::DATE_FORMAT, strtotime('2020-01-01')),
			];
			$req_filter = array_merge($req_filter, $filter);
			$req_filter['page'] = 1;
			$res = $this->execute('orders/search', false, $req_filter);
			if ($res['success']) {
				if ($res['content']['orders']) {
					$list = array_merge($list, $res['content']['orders']);
				}
				$pages = $res['content']['_meta']['pageCount'];
				if ($pages > 1) {
					for ($page = 2; $page <= $pages && count($list) < $limit; $page ++) {
						$req_filter['page'] = $page;
						$res = $this->execute('orders/search', false, $req_filter);
						if ($res['content']['orders']) {
							$list = array_merge($list, $res['content']['orders']);
						}
					}
				}
			}
			$list = array_slice($list, 0, $limit);
		}
		return $list;
	}

	/**
	 * Get orders count
	 */
	public function getOrdersCount(array $filter, int $limit=1) {
		$count = false;
		$req_filter = [
			'types' => 1,
		];
		$req_filter = array_merge($req_filter, $filter);
		$res = $this->execute('orders/search', false, $req_filter);
		if ($res['success']) {
			$count = $res['content']['_meta']['totalCount'];
		}
		return $count;
	}

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$object = false;
		$res = $this->execute('orders/' . $order_id, false, [
			'expand' => 'delivery,user,is_promo,status_data,purchases,total_quantity,payment_type,credit_info,payment_type_name,item_details,payment_status,status_payment,feedback_count'
			//expand => delivery_service,status_available
		]);
		if ($res['content']) {
			$object = $res['content'];
		}
		return $object;
	}
}
