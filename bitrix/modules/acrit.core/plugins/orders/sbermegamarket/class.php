<?
/**
 * Acrit Core: Orders integration plugin for SberMegaMarket
 */

namespace Acrit\Core\Crm\Plugins;

require_once __DIR__.'/lib/api/request.php';
require_once __DIR__.'/lib/api/orders.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\Orders\Plugins\SbermegamarketHelpers\Orders,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class Sbermegamarket extends Plugin {

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
		return 'SBERMEGAMARKET';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return Loc::getMessage(self::getLangCode('PLUGIN_NAME'));
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
			'id' => 'CODE',
			'name' => Loc::getMessage(self::getLangCode('PRODUCTS_ID_FIELD_NAME')),
		];
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
		$list = [];
		$list[] = [
			'id' => 'NEW',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_NEW')),
		];
        $list[] = [
			'id' => 'CONFIRMED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_CONFIRMED')),
		];
		$list[] = [
			'id' => 'PACKED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_PACKED')),
		];
		$list[] = [
			'id' => 'SHIPPED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_SHIPPED')),
		];
		$list[] = [
			'id' => 'DELIVERED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_DELIVERED')),
		];
		$list[] = [
			'id' => 'MERCHANT_CANCELED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_MERCHANT_CANCELED')),
		];
		$list[] = [
			'id' => 'CUSTOMER_CANCELED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_CUSTOMER_CANCELED')),
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
			'name' => Loc::getMessage(self::getLangCode('CONTACT_CUSTOMER_FIO')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'address',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_ADDRESS')),
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
			'id' => 'shipmentId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPMENTID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'orderCode',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDERCODE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shippingPoint',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPINGPOINT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'customerFullName',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CUSTOMERFULLNAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'customerAddress',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CUSTOMERADDRESS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'confirmedTimeLimit',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CONFIRMEDTIMELIMIT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'packingTimeLimit',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PACKINGTIMELIMIT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shippingTimeLimit',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPINGTIMELIMIT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'creationDate',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREATIONDATE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryDateFrom',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYDATEFROM')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryDateTo',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYDATETO')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shippmentDateFrom',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPMENTDATEFROM')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shipmentDateTo',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPMENTDATETO')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS')),
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
//	    PeriodSync::setModuleId($this->getModuleId());
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
		    $api = $this->getApi();
		    $filter = [
			    'dateFrom' => date(Orders::DATE_FORMAT, $create_from_ts),
		    ];
		    $count = $api->getOrdersCount($filter);
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
				'dateFrom' => date(Orders::DATE_FORMAT, $filter_date),
			];
		}
		$api = $this->getApi();
		$orders_list = $api->getOrdersList($req_filter, 1000);
		foreach ($orders_list as $id) {
			$list[] = $id;
		}
		return $list;
	}

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$order = false;
		// Order data
		$api = $this->getApi();
		$mp_order = $api->getOrder($order_id);
		if ($mp_order) {
			// Main fields
			$order = [
				'ID'          => $mp_order['shipmentId'],
				'DATE_INSERT' => strtotime($mp_order['creationDate']),
				'STATUS_ID'   => $mp_order['items'][0]['status'],
				'IS_CANCELED' => false,
			];
			// User data
			$order['USER'] = [
				'fio' => $mp_order['customerFullName'],
				'address'  => $mp_order['customerAddress'],
			];
			//    [] => 2021-09-22T12:00:00+03:00
			//    [] => 2021-09-23T10:00:00+03:00
			//    [] => 2021-09-23T20:00:00+03:00
			//    [] => 2021-09-23T10:00:00+03:00
			//    [] => 2021-09-23T20:00:00+03:00
			//    [] => 910669351
			//    [] => 1
			//    [] => 1
			// Fields
			$order['FIELDS'] = [
				'orderCode' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['orderCode']],
				],
				'shipmentId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentId']],
				],
				'confirmedTimeLimit' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['confirmedTimeLimit']],
				],
				'packingTimeLimit' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['packingTimeLimit']],
				],
				'shippingTimeLimit' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shippingTimeLimit']],
				],
				'shipmentDateFrom' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentDateFrom']],
				],
				'shipmentDateTo' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentDateTo']],
				],
				'deliveryId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryId']],
				],
				'shipmentDateShift' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentDateShift']],
				],
				'shipmentIsChangeable' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentIsChangeable']],
				],
				'customerFullName' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['customerFullName']],
				],
				'customerAddress' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['customerAddress']],
				],
				'shippingPoint' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shippingPoint']],
				],
				'creationDate' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['creationDate']],
				],
				'deliveryDate' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryDate']],
				],
				'deliveryDateFrom' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryDateFrom']],
				],
				'deliveryDateTo' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryDateTo']],
				],
				'deliveryMethodId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryMethodId']],
				],
				'serviceScheme' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['serviceScheme']],
				],
				'customer' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['customer']],
				],
				'depositedAmount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['depositedAmount']],
				],
			];
			// Products
			$order['PRODUCTS'] = [];
			$products_list = [];
			foreach ($mp_order['items'] as $item) {
                // Unite same products
                if (isset($products_list[$item['offerId']])) {
	                $products_list[$item['offerId']]['QUANTITY'] += $item['quantity'];
                }
                else {
	                $products_list[$item['offerId']] = [
		                'PRODUCT_NAME'     => $item['goodsData']['name'],
		                'PRODUCT_CODE'     => $item['offerId'],
		                'PRICE'            => $item['finalPrice'],
		                'CURRENCY'         => 'RUB',
		                'QUANTITY'         => $item['quantity'],
		                'DISCOUNT_TYPE_ID' => 1,
		                'DISCOUNT_SUM'     => ($item['price'] - $item['finalPrice']),
		                'MEASURE_CODE'     => 0,
		                'TAX_RATE'         => 0,
		                'TAX_INCLUDED'     => 'Y',
	                ];
                }
			}
			foreach ($products_list as $item) {
				$order['PRODUCTS'][] = $item;
			}
            $order = self::formatOrder($order);
		}
		return $order;
	}
}
