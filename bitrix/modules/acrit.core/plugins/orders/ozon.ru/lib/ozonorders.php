<?php

namespace Acrit\Core\Orders\Plugins\OzonRuHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/ozonrequest.php';

class OzonOrders extends OzonRequest {

	public function __construct($strClientId, $strApiKey, $intProfileId, $strModuleId) {
		parent::__construct($strClientId, $strApiKey, $intProfileId, $strModuleId);
	}

	/**
	 * Check connection
	 */
	public function checkConnection(&$message) {
		$result = false;
		$res = $this->execute('/v2/posting/fbs/list', [
			'dir' => 'desc',
			'filter' => [
				"since" => "2020-01-01T00:00:00.944Z",
			],
			'limit' => 1,
		], [
			'METHOD' => 'POST'
		]);
		if ($res['error']) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_OZON_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		elseif (isset($res['result'])) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_OZON_CHECK_SUCCESS');
			$result = true;
		}
		return $result;
	}

	/**
	 * Get postings list
	 */
	public function getPostingsList(array $filter, int $limit=1) {
		$list = [];
		$req_filter = [
			'since' => '2020-01-01T00:00:00.944Z',
//			'status' => 'delivering'
		];
		$req_filter = array_merge($req_filter, $filter);
		$res = $this->execute('/v2/posting/fbs/list', [
			'dir' => 'desc',
			'filter' => $req_filter,
			'limit' => $limit,
		], [
			'METHOD' => 'POST'
		]);
		if ($res['result']) {
			$list = $res['result'];
		}
		return $list;
	}

	/**
	 * Get posting
	 */
	public function getPosting($posting_number) {
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
