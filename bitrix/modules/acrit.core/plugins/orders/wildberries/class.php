<?
/**
 * Acrit Core: Orders integration plugin for Wildberries
 * Documentation: https://suppliers-api.wildberries.ru/swagger/index.html#/Marketplace/get_api_v2_orders
 */

namespace Acrit\Core\Crm\Plugins;

require_once __DIR__.'/lib/api/request.php';
require_once __DIR__.'/lib/api/orders.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\Orders\Plugins\WildberriesHelpers\Orders,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class Wildberries extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
	protected static $arOrders = [];

	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return 'WILDBERRIES';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return 'Wildberries';
	}

	/**
	 *	Include classes
	 */
	public function includeClasses() {
		#require_once(__DIR__.'/lib/json.php');
	}

	/**
	 * Get comment for the tab
	 */
	public function getTabComment($tab){
	    $comment = '';
	    switch ($tab) {
//            case 'products': $comment = Loc::getMessage(self::getLangCode('PRODUCTS_MESSAGE'));
//                break;
        }
		return $comment;
	}

	/**
	 * Get id of products in marketplace
	 */
	public static function getIdField() {
		return [
			'id' => 'BARCODE',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_WILDBERRIES_PRODUCTS_ID_FIELD_NAME'),
		];
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
		$list = [];
		$list[] = [
			'id' => '0',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_0')),
		];
        $list[] = [
			'id' => '1',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_1')),
		];
		$list[] = [
			'id' => '2',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_2')),
		];
		$list[] = [
			'id' => '3',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_3')),
		];
		$list[] = [
			'id' => '5',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_5')),
		];
		$list[] = [
			'id' => '6',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_6')),
		];
		$list[] = [
			'id' => '7',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_7')),
		];
		return $list;
	}

	/**
	 * Store fields for deal contact
	 * @return array
	 */
	public function getContactFields() {
		$list = [];
		$list['user'] = [
			'title' => Loc::getMessage(self::getLangCode('CONTACT_TITLE')),
		];
		$list['user']['items'][] = [
			'id' => 'fio',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_FIO')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'phone',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_PHONE')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'user_id',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_USER_ID')),
			'direction' => self::SYNC_STOC,
		];
		return $list;
	}

	/**
	 * Store fields for deal fields
	 * @return array
	 */
	public function getFields() {
		$list = parent::getFields();
		$list[] = [
			'id' => 'orderId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDERID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'dateCreated',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DATECREATED')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'wbWhId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_WBWHID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'pid',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'officeAddress',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_OFFICEADDRESS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryAddress',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYADDRESS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryDescription',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYDESCRIPTION')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'province',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PROVINCE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'area',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_AREA')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'city',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CITY')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'street',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STREET')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'home',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_HOME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'flat',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FLAT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'entrance',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ENTRANCE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'longitude',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_LONGITUDE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'latitude',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_LATITUDE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'userId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_USERID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'fio',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FIO')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'phone',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PHONE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'chrtId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CHRTID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'barcode',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BARCODE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'statusText',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUSTEXT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'userStatus',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_USERSTATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'userStatusText',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_USERSTATUSTEXT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'rid',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_RID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'totalPrice',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_TOTALPRICE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'orderUID',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDERUID')),
			'direction' => self::SYNC_STOC,
		];
		return $list;
	}

	/**
	 *	Show plugin default settings
	 */
	public function showSettings($arProfile){
		ob_start();
		?>
        <table class="acrit-exp-plugin-settings" style="width:100%;">
            <tbody>
            <tr class="heading" id="tr_HEADING_CONNECT"><td colspan="2"><?=Loc::getMessage(self::getLangCode('SETTINGS_HEADING'));?></td></tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_TOKEN_HINT')));?>
					<?=Loc::getMessage(self::getLangCode('SETTINGS_TOKEN'));?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][token]" size="50" maxlength="255" data-role="connect-cred-token"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['token']);?>" />
                    <p><a class="adm-btn" data-role="connection-check"><?=Loc::getMessage(self::getLangCode('SETTINGS_CHECK_CONN'));?></a></p>
                    <p id="check_msg"></p>
                </td>
            </tr>
            </tbody>
        </table>
		<?
		return ob_get_clean();
	}

	/**
	 *	Show plugin periodical synchronization settings
	 */
	public function getSettingsPeriodSyncBlock() {
		$arValues = [];
//	    PeriodSync::setModuleId($this->strModuleId);
//	    PeriodSync::set($this->intProfileId);
//		Controller::syncStoreToCRM(14400 * 60);
		$arValues[1]['period'] = $this->arProfile['SYNC']['add']['1']['period'] ? $this->arProfile['SYNC']['add']['1']['period'] : 10;
		$arValues[1]['range'] = $this->arProfile['SYNC']['add']['1']['range'] ? $this->arProfile['SYNC']['add']['1']['range'] : 60;
		$arValues[2]['period'] = $this->arProfile['SYNC']['add']['2']['period'] ? $this->arProfile['SYNC']['add']['2']['period'] : 24;
		$arValues[2]['range'] = $this->arProfile['SYNC']['add']['2']['range'] ? $this->arProfile['SYNC']['add']['2']['range'] : 336;
		ob_start();
		?>
        <tr id="tr_sync_add_period">
            <td>
                <label for="field_sync_add_period"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SYNC_ADD_PERIOD_1');?><label>
            </td>
            <td>
                <input type="text" name="PROFILE[SYNC][add][1][period]" id="field_sync_add_1_period" value="<?=$arValues[1]['period'];?>" placeholder="10" /> <?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SYNC_ADD_MINUTES');?>
                <input type="hidden" name="PROFILE[SYNC][add][1][measure]" value="m" />
            </td>
        </tr>
        <tr id="tr_sync_add_range">
            <td>
                <label for="field_sync_add_range"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SYNC_ADD_RANGE_1');?><label>
            </td>
            <td>
                <input type="text" name="PROFILE[SYNC][add][1][range]" id="field_sync_add_1_range" value="<?=$arValues[1]['range'];?>" placeholder="60" /> <?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SYNC_ADD_MINUTES');?>
            </td>
        </tr>
        <tr id="tr_sync_add_period">
            <td>
                <label for="field_sync_add_period"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SYNC_ADD_PERIOD_2');?><label>
            </td>
            <td>
                <input type="text" name="PROFILE[SYNC][add][2][period]" id="field_sync_add_2_period" value="<?=$arValues[2]['period'];?>" placeholder="24" /> <?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SYNC_ADD_HOURS');?>
                <input type="hidden" name="PROFILE[SYNC][add][2][measure]" value="h" />
            </td>
        </tr>
        <tr id="tr_sync_add_range">
            <td>
                <label for="field_sync_add_range"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SYNC_ADD_RANGE_1');?><label>
            </td>
            <td>
                <input type="text" name="PROFILE[SYNC][add][2][range]" id="field_sync_add_2_range" value="<?=$arValues[2]['range'];?>" placeholder="336" /> <?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SYNC_ADD_HOURS');?>
            </td>
        </tr>
		<?
		return ob_get_clean();
	}

	/**
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch ($strAction) {
			case 'connection_check':
				$token = $arParams['POST']['token'];
				$message = '';
				$api = $this->getApi();
				$res = $api->checkConnection($token, $message);
				$arJsonResult['check'] = $res ? 'success' : 'fail';
				$arJsonResult['message'] = $message;
				$arJsonResult['result'] = 'ok';
				break;
		}
	}

	/**
	 * Get object for api requests
	 */

	public function getApi() {
		$api = new Orders($this);
		return $api;
	}

	/**
	 * Get orders count
	 */

	public function getOrdersCount($create_from_ts) {
		$count = false;
	    if ($create_from_ts) {
//		    $api = $this->getApi();
//		    $filter = [
//			    'date_start' => date(Orders::DATE_FORMAT, $create_from_ts),
//		    ];
//		    $count = $api->getOrdersCount($filter);
            $list = $this->getOrdersIDsList($create_from_ts);
		    $count = count($list);
	    }
		return $count;
	}

	/**
	 * Get orders count
	 */

	public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
		$list = [];
		// Get the list
		$req_filter = [];
		if ($create_from_ts || $change_from_ts) {
			$filter_date = $change_from_ts ? : $create_from_ts;
			$req_filter = [
				'date_start' => date(Orders::DATE_FORMAT, $filter_date),
			];
		}
		$api = $this->getApi();
		$orders_list = $api->getOrdersList($req_filter, 1000);
		self::$arOrders = [];
		foreach ($orders_list as $item) {
			$list[] = $item['id'];
			// Remember orders for function getOrder (because The API does not have a function of receiving a separate order)
            // RAM consumption - less than 15 Mb on 1000 orders
			self::$arOrders[$item['id']] = $item;
		}
		return $list;
	}

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$order = false;
		// Order data
		$mp_order = self::$arOrders[$order_id];
		if ($mp_order) {
			// Main fields
			$order = [
				'ID'          => $mp_order['orderId'],
				'DATE_INSERT' => strtotime($mp_order['dateCreated']),
				'STATUS_ID'   => $mp_order['status'],
				'IS_CANCELED' => false,
			];
			// User data
			$order['USER'] = [
				'fio' => $mp_order['userInfo']['fio'],
				'phone'  => $mp_order['userInfo']['phone'],
				'user_id'   => $mp_order['userInfo']['userId'],
			];
			// Fields
			$order['FIELDS'] = [
				'orderId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['orderId']],
				],
				'dateCreated' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['dateCreated']],
				],
				'wbWhId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['wbWhId']],
				],
				'pid' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['pid']],
				],
				'officeAddress' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['officeAddress']],
				],
				'deliveryAddress' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddress']],
				],
				'deliveryDescription' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['description']],
				],
				'province' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['id']],
				],
				'area' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['area']],
				],
				'city' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['city']],
				],
				'street' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['street']],
				],
				'home' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['home']],
				],
				'flat' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['flat']],
				],
				'entrance' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['entrance']],
				],
				'longitude' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['longitude']],
				],
				'latitude' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryAddressDetails']['latitude']],
				],
				'userId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['userInfo']['userId']],
				],
				'fio' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['userInfo']['fio']],
				],
				'phone' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['userInfo']['phone']],
				],
				'chrtId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['chrtId']],
				],
				'barcode' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['barcode']],
				],
				'status' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status']],
				],
				'statusText' => [
					'TYPE'  => 'STRING',
					'VALUE' => [Loc::getMessage(self::getLangCode('FIELDS_STATUS_' . $mp_order['status']))],
				],
				'userStatus' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['userStatus']],
				],
				'userStatusText' => [
					'TYPE'  => 'STRING',
					'VALUE' => [Loc::getMessage(self::getLangCode('FIELDS_USERSTATUS_' . $mp_order['userStatus']))],
				],
				'rid' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['rid']],
				],
				'totalPrice' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['totalPrice']],
				],
				'orderUID' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['orderUID']],
				],
			];
			// Products
			$order['PRODUCTS'] = [];
			$products_list = $mp_order['products'];
			foreach ($products_list as $item) {
				$order['PRODUCTS'][] = [
					'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $item['barcode'],
					'PRICE'            => $item['price'],
					'CURRENCY'         => 'RUB',
					'QUANTITY'         => $item['quantity'],
					'DISCOUNT_TYPE_ID' => 1,
					'DISCOUNT_SUM'     => 0,
					'MEASURE_CODE'     => 0,
					'TAX_RATE'         => 0,
					'TAX_INCLUDED'     => 'Y',
				];
			}
		}
		return $order;
	}
}
