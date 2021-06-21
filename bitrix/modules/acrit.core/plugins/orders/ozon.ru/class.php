<?
/**
 * Acrit Core: AliExpress crm integration plugin
 */

namespace Acrit\Core\Orders\Plugins;

require_once __DIR__ . '/lib/ozonorders.php';

use Acrit\Core\Orders\PeriodSync;
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log,
	\Acrit\Core\Orders\Plugins\OzonRuHelpers\OzonOrders;

Loc::loadMessages(__FILE__);

class OzonRu extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
	protected $isCountable = false;

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
		return 'OZON_RU';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return 'OZON';
	}

	/**
	 *	Include classes
	 */
	public function includeClasses() {
		#require_once(__DIR__.'/lib/json.php');
	}

	/**
	 * Store fields for deal contact
	 * @return array
	 */
	public function getContactFields() {
		$list = [];
		$list[0]['title'] = Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_CONTACT_FIELDS_GROUP_1');
		$list[0]['items'] = self::getFields();
		return $list;
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
		$list = [];
		$list[] = [
			'id' => 'awaiting_approve',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_AWAITING_APPROVE'),
		];
		$list[] = [
			'id' => 'awaiting_packaging',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_AWAITING_PACKAGING'),
		];
		$list[] = [
			'id' => 'awaiting_deliver',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_AWAITING_DELIVER'),
		];
		$list[] = [
			'id' => 'delivering',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_DELIVERING'),
		];
		$list[] = [
			'id' => 'driver_pickup',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_DRIVER_PICKUP'),
		];
		$list[] = [
			'id' => 'delivered',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_DELIVERED'),
		];
		$list[] = [
			'id' => 'cancelled',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_CANCELLED'),
		];
		return $list;
	}

	/**
	 * Store fields for deal fields
	 * @return array
	 */
	public function getFields() {
		$list = [];
		$list[] = [
			'id' => 'order_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ORDER_ID'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'order_number',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ORDER_NUMBER'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'posting_number',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_POSTING_NUMBER'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_STATUS'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_city',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_CITY'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_delivery_type',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_DELIVERY_TYPE'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_is_premium',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_IS_PREMIUM'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_payment_type_group_name',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_PAYMENT_TYPE_GROUP_NAME'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_region',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_REGION'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_warehouse_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_WAREHOUSE_ID'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_warehouse_name',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_WAREHOUSE_NAME'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'cancel_reason_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CANCEL_REASON_ID'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'created_at',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CREATED_AT'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'in_process_at',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_IN_PROCESS_AT'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shipment_date',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_SHIPMENT_DATE'),
			'direction' => self::SYNC_STOC,
		];
		return $list;
	}


	/**
	 *	Show plugin default settings
	 */
	public function showSettings(){
		ob_start();
//		$client_id = $this->arProfile['CONNECT_CRED']['client_id'];
//		$api_key = $this->arProfile['CONNECT_CRED']['api_key'];
//		$ozon = new OzonOrders($client_id, $api_key, $this->arProfile['ID'], $this->strModuleId);
//		$res = $ozon->checkConnection($msg);
//		$res = $ozon->getPostingsList([]);
//		echo '<pre>'; print_r($res); echo '</pre>';
//		$order_data = $this->getOrder('61206578-0004-3');
//      echo '<pre>'; print_r($order_data); echo '</pre>';
//		Settings::setModuleId($this->strModuleId);
//		Controller::setModuleId($this->strModuleId);
//		Controller::setProfile($this->arProfile['ID']);
//		Controller::syncExtToStore($order_data);
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
            <tr class="heading" id="tr_HEADING_CONNECT"><td colspan="2"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_HEADING');?></td></tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_CLIENT_ID_HINT'));?>
					<?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_CLIENT_ID');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][client_id]" size="50" maxlength="255" data-role="connect-cred-client_id"
                           value="<?=htmlspecialcharsbx($this->arProfile['CONNECT_CRED']['client_id']);?>" />
				</td>
			</tr>
            <tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_API_KEY_HINT'));?>
					<?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_API_KEY');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][api_key]" size="50" maxlength="255" data-role="connect-cred-api_key"
                           value="<?=htmlspecialcharsbx($this->arProfile['CONNECT_CRED']['api_key']);?>" />
                    <p><a class="adm-btn" data-role="connection-check"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_CHECK_CONN');?></a></p>
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
			    $client_id = $arParams['POST']['client_id'];
			    $api_key = $arParams['POST']['api_key'];
				$ozon = new OzonOrders($client_id, $api_key, $this->arProfile['ID'], $this->strModuleId);
				$res = $ozon->checkConnection($message);
				$arJsonResult['check'] = $res ? 'success' : 'fail';
				$arJsonResult['message'] = $message;
				$arJsonResult['result'] = 'ok';
				break;
		}
	}

	/**
	 * Get orders count
	 */

	public function getOrdersCount($filter) {
	    $count = 0;
	    return $count;
	}

	/**
	 * Get orders count
	 */

	public function getOrdersIDsList($filter) {
	    $list = [];
		// Get the list
        $req_filter = [];
        if ($filter['create_date_from'] || $filter['change_date_from']) {
	        $filter_date = $filter['change_date_from'] ? $filter['change_date_from'] : $filter['create_date_from'];
	        $req_filter = [
		        'since' => date("Y-m-d\TH:i:s.000\Z", $filter_date),
	        ];
        }
		$client_id = $this->arProfile['CONNECT_CRED']['client_id'];
		$api_key = $this->arProfile['CONNECT_CRED']['api_key'];
		$ozon = new OzonOrders($client_id, $api_key, $this->arProfile['ID'], $this->strModuleId);
		$orders_list = $ozon->getPostingsList($req_filter, 1000);
		foreach ($orders_list as $item) {
			$list[] = $item['posting_number'];
		}
	    return $list;
	}

	/**
	 * Get order
	 */
	public function getOrder($posting_id) {
		$order = false;
		// Ozon posting data
		$client_id = $this->arProfile['CONNECT_CRED']['client_id'];
		$api_key = $this->arProfile['CONNECT_CRED']['api_key'];
		$ozon = new OzonOrders($client_id, $api_key, $this->arProfile['ID'], $this->strModuleId);
		$mp_order = $ozon->getPosting($posting_id);
		if ($mp_order['posting_number']) {
			// Main fields
			$order = [
				'ID'          => $mp_order['posting_number'],
				'DATE_INSERT' => strtotime($mp_order['created_at']),
				'STATUS_ID'   => $mp_order['status'],
				'IS_CANCELED' => false,
			];
			// User data
			$order['USER'] = [
			];
			// Fields
			$order['FIELDS'] = [
				'order_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['order_id']],
				],
				'order_number' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['order_number']],
				],
				'posting_number' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['posting_number']],
				],
				'status' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status']],
				],
				'cancel_reason_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['cancel_reason_id']],
				],
				'created_at' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['created_at']],
				],
				'in_process_at' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['in_process_at']],
				],
				'shipment_date' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipment_date']],
				],
				'analytics_data_city' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['city']],
				],
				'analytics_data_delivery_type' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['delivery_type']],
				],
				'analytics_data_is_premium' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['is_premium']],
				],
				'analytics_data_payment_type_group_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['payment_type_group_name']],
				],
				'analytics_data_region' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['region']],
				],
				'analytics_data_warehouse_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['warehouse_id']],
				],
				'analytics_data_warehouse_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['warehouse_name']],
				],
			];
			// Products
			$order['PRODUCTS'] = [];
			$products_list = [];
			if (is_array($mp_order['products']) && !empty($mp_order['products'])) {
				$products_list = $mp_order['products'];
			}
			foreach ($products_list as $item) {
				$order['PRODUCTS'][] = [
					'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $item['offer_id'],
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
