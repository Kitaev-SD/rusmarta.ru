<?
/**
 * Acrit Core: AliExpress crm integration plugin
 */

namespace Acrit\Core\Crm\Plugins;

require_once __DIR__.'/lib/sdk/TopSdk.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class AliExpress extends Plugin {
	const APP_KEY = '30433054';
	const SECRET_KEY = '17ec1f9a551165193fffcc8c3ffd614c';

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];

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
		return 'ALIEXPRESS';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return 'AliExpress';
	}

	/**
	 *	Include classes
	 */
	public function includeClasses() {
		#require_once(__DIR__.'/lib/json.php');
	}

	/**
	 * Get id of products in marketplace
	 */
	public static function getIdField() {
		return [
			'id' => 'NAME',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_PRODUCTS_ID_FIELD_NAME'),
		];
	}

	/**
	 * Store fields for deal contact
	 * @return array
	 */
	public function getContactFields() {
		$list = [];
		$list['user'] = [
			'title' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_CONTACT_TITLE'),
		];
		$list['user']['items'][] = [
			'id' => 'buyer_login_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_CONTACT_LOGIN_ID'),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'buyer_last_name',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_CONTACT_LAST_NAME'),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'buyer_first_name',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_CONTACT_FIRST_NAME'),
			'direction' => self::SYNC_STOC,
		];
		return $list;
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
		$list = [];
		$list[] = [
			'id' => 'PLACE_ORDER_SUCCESS',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_PLACE_ORDER_SUCCESS'),
		];
		$list[] = [
			'id' => 'PAYMENT_PROCESSING',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_PAYMENT_PROCESSING'),
		];
		$list[] = [
			'id' => 'RISK_CONTROL',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_RISK_CONTROL'),
		];
		$list[] = [
			'id' => 'RISK_CONTROL_HOLD',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_RISK_CONTROL_HOLD'),
		];
		$list[] = [
			'id' => 'WAIT_SELLER_EXAMINE_MONEY',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_WAIT_SELLER_EXAMINE_MONEY'),
		];
		$list[] = [
			'id' => 'SELLER_PART_SEND_GOODS',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_SELLER_PART_SEND_GOODS'),
		];
		$list[] = [
			'id' => 'WAIT_SELLER_SEND_GOODS',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_WAIT_SELLER_SEND_GOODS'),
		];
		$list[] = [
			'id' => 'WAIT_BUYER_ACCEPT_GOODS',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_WAIT_BUYER_ACCEPT_GOODS'),
		];
		$list[] = [
			'id' => 'FINISH',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_FINISH'),
		];
		$list[] = [
			'id' => 'IN_CANCEL',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_IN_CANCEL'),
		];
		$list[] = [
			'id' => 'ARCHIVE',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_STATUSES_ARCHIVE'),
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
			'id' => 'buyer_first_name',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_FIRST_NAME'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_last_name',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_LAST_NAME'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_login_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_LOGIN_ID'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_country',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_COUNTRY'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'gmt_create',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_GMT_CREATE'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'over_time_left',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_OVER_TIME_LEFT'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'logistics_amount',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_LOGISTICS_AMOUNT'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'logistics_status',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_LOGISTICS_STATUS'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'receipt_address_country',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_RECEIPT_ADDRESS_COUNTRY'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'receipt_address_province',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_RECEIPT_ADDRESS_PROVINCE'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'receipt_address_city',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_RECEIPT_ADDRESS_CITY'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'receipt_address_detail_address',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_RECEIPT_ADDRESS_DETAIL_ADDRESS'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'receipt_address_zip',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_RECEIPT_ADDRESS_ZIP'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'receipt_address_phone',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_RECEIPT_ADDRESS_PHONE'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'receipt_address_contact_person',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_FIELDS_RECEIPT_ADDRESS_CONTACT_PERSON'),
			'direction' => self::SYNC_STOC,
		];
		return $list;
	}

	public function getTokenLink() {
		$link = "https://oauth.aliexpress.com/authorize?response_type=code&client_id=".self::APP_KEY."&state=&view=web&sp=ae";
		return $link;
	}

	/**
	 *	Show plugin default settings
	 */
	public function showSettings($arProfile){
		ob_start();
//		$order = $this->getOrder(8018196774972936);
//		echo '<pre>'; print_r($order); echo '</pre>';
//		Settings::setModuleId($this->strModuleId);
//		Controller::setModuleId($this->strModuleId);
//		Controller::setProfile($arProfile['ID']);
//		Controller::syncOrderToDeal($order);
//		Controller::syncStoreToCRM(60);
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
            <tr class="heading" id="tr_HEADING_CONNECT"><td colspan="2"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_SETTINGS_HEADING');?></td></tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_SETTINGS_TOKEN_HINT'));?>
					<?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_SETTINGS_TOKEN');?>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
                    <div id="acrit-module-update-notifier">
                        <div class="acrit-exp-note-compact">
                            <div class="adm-info-message-wrap">
                                <div class="adm-info-message"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_SETTINGS_TOKEN_HELP');?></div>
                            </div>
                        </div>
                    </div>
                    <p><a href="<?=$this->getTokenLink();?>" target="_blank"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_SETTINGS_GET_TOKEN');?></a></p>
                    <input type="text" name="PROFILE[CONNECT_CRED][token]" size="50" maxlength="255" data-role="connect-cred-token"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['token']);?>" />
                    <a class="adm-btn" data-role="connection-check"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_SETTINGS_CHECK_TOKEN');?></a>
                    <p id="check_msg"></p>
				</td>
			</tr>
			</tbody>
		</table>
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
				$res = $this->checkConnection($token, $message);
				$arJsonResult['check'] = $res ? 'success' : 'fail';
				$arJsonResult['message'] = $message;
				$arJsonResult['result'] = 'ok';
				break;
		}
	}

	/**
	 *	Regular synchronization interval modifications
	 */
	public function modifSyncInterval($sync_interval) {
	    // Min interval for search orders
		return $sync_interval + 12*3600;
	}

	/**
     * Check connection
     */

	public function checkConnection($token, &$message) {
		$result = false;
		$c = new \TopClient;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionOrderGetRequest;
		$param0 = new \OrderQuery;
		$param0->create_date_start = "2010-01-01 00:00:00";
		$param0->page_size = "1";
		$param0->current_page = "20";
		$req->setParam0(json_encode($param0));
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		if ($resp['code']) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_CHECK_ERROR') . $resp['msg'] . ' [' . $resp['code'] . ']';
		}
		else {
			$result = true;
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALI_CHECK_SUCCESS');
		}
	    return $result;
	}

	/**
     * Get orders list
     */

	public function getAliOrdersList($filter=[]) {
	    $list = [];
		$page_size = 20;
		$page = 1;
		$token = $this->arProfile['CONNECT_CRED']['token'];
		// Filter
		$create_date = "2010-01-01 00:00:00";
		if ($filter['create_date_from']) {
			$create_date = date('Y-m-d H:i:s', $filter['create_date_from']);
		}
		if ($filter['change_date_from']) {
			$change_date = date('Y-m-d H:i:s', $filter['change_date_from']);
		}
		// Get the list
        $i = 0;
        do {
	        $c = new \TopClient;
	        $c->appkey = self::APP_KEY;
	        $c->secretKey = self::SECRET_KEY;
	        $req = new \AliexpressSolutionOrderGetRequest;
	        $param0 = new \OrderQuery;
	        if ($change_date) {
		        $param0->modified_date_start = $change_date;
	        }
	        elseif ($create_date) {
		        $param0->create_date_start = $create_date;
	        }
	        $param0->page_size = $page_size;
	        $param0->current_page = $page;
	        $param0->order_status_list = ['*'];
	        $req->setParam0(json_encode($param0));
	        $resp = $c->execute($req, $token);
	        $resp = json_decode(json_encode($resp), true);
	        if ($resp['code']) {
		        throw new \Exception($resp['msg'], $resp['code']);
		    }
	        if (is_array($resp['result']['target_list']) && !empty($resp['result']['target_list'])) {
	            if ($resp['result']['target_list']['order_dto']['order_id']) {
		            $list[] = $resp['result']['target_list']['order_dto'];
	            }
	            else {
		            foreach ($resp['result']['target_list']['order_dto'] as $item) {
			            $list[] = $item;
		            }
	            }
	        }
	        $page++;
	        $i++;
        } while($resp['result']['total_page'] && $resp['result']['current_page'] < $resp['result']['total_page'] && $i < 1000);
	    return $list;
	}

	/**
     * Get order
     */

	public function getAliOrder($order_id) {
        $order = false;
		$token = $this->arProfile['CONNECT_CRED']['token'];
		$c = new \TopClient;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionOrderInfoGetRequest;
		$param1 = new \OrderDetailQuery;
		$param1->ext_info_bit_flag = "11111";
		$param1->order_id = $order_id;
		$req->setParam1(json_encode($param1));
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		if ($resp['result']['data']) {
			$order = $resp['result']['data'];
		}
        return $order;
	}


	/**
	 * Get orders count
	 */

	public function getOrdersCount($create_from_ts) {
	    $count = 0;
		$token = $this->arProfile['CONNECT_CRED']['token'];
		// Get the list
        $start_date = '2010-01-01 00:00:00';
        if ($create_from_ts) {
	        $start_date = date('Y-m-d H:i:s', $create_from_ts);
        }
        $c = new \TopClient;
        $c->appkey = self::APP_KEY;
        $c->secretKey = self::SECRET_KEY;
        $req = new \AliexpressSolutionOrderGetRequest;
        $param0 = new \OrderQuery;
        $param0->create_date_start = $start_date;
        $param0->page_size = 1;
        $param0->current_page = 1;
        $req->setParam0(json_encode($param0));
        $resp = $c->execute($req, $token);
        $resp = json_decode(json_encode($resp), true);
        if ($resp['code']) {
            throw new \Exception($resp['msg'], $resp['code']);
        }
        $count = (int) $resp['result']['total_count'];
	    return $count;
	}


	/**
	 * Get orders count
	 */

	public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
	    $list = [];
		// Get the list
		$filter = [];
		if ($create_from_ts) {
			$filter['create_date_from'] = $create_from_ts;
		}
		if ($change_from_ts) {
			$filter['change_date_from'] = $change_from_ts;
		}
		$orders_list = self::getAliOrdersList($filter);
		foreach ($orders_list as $item) {
			$list[] = $item['order_id'];
		}
	    return $list;
	}


	/**
	 * Get order
	 */

	public function getOrder($order_id) {
	    $order = false;
	    $ali_order = self::getAliOrder($order_id);
	    if ($ali_order['id']) {
		    // Check encoding
		    if (!Helper::isUtf()) {
			    $ali_order['buyer_info'] = Helper::convertEncoding($ali_order['buyer_info']);
			    $ali_order['receipt_address'] = Helper::convertEncoding($ali_order['receipt_address']);
		    }
	        // Main fields
		    $order = [
			    'ID'          => $ali_order['id'],
			    'DATE_INSERT' => strtotime($ali_order['gmt_create']),
			    'STATUS_ID'   => $ali_order['order_status'],
			    'IS_CANCELED' => false,
		    ];
		    // User data
		    $order['USER'] = [
			    'first_name' => $ali_order['buyer_info']['first_name'],
			    'last_name'  => $ali_order['buyer_info']['last_name'],
			    'login_id'   => $ali_order['buyer_info']['login_id'],
			    'country'    => $ali_order['buyer_info']['country'],
            ];
            // Fields
		    $order['FIELDS'] = [
                'buyer_first_name' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['buyer_info']['first_name']],
                ],
                'buyer_last_name' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['buyer_info']['last_name']],
                ],
                'buyer_login_id' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['buyer_info']['login_id']],
                ],
                'buyer_country' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['buyer_info']['country']],
                ],
                'logistics_amount' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['logistics_amount']['amount']],
                ],
                'logistics_status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['logistics_status']],
                ],
                'gmt_create' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['gmt_create']],
                ],
                'over_time_left' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['over_time_left']],
                ],
                'receipt_address_country' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['receipt_address']['country']],
                ],
                'receipt_address_province' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['receipt_address']['province']],
                ],
                'receipt_address_city' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['receipt_address']['city']],
                ],
                'receipt_address_detail_address' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['receipt_address']['detail_address']],
                ],
                'receipt_address_zip' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['receipt_address']['zip']],
                ],
                'receipt_address_phone' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['receipt_address']['phone_country'] . $ali_order['receipt_address']['mobile_no']],
                ],
                'receipt_address_contact_person' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ali_order['receipt_address']['contact_person']],
                ],
		    ];
            // Products
		    $order['PRODUCTS'] = [];
		    $products_list = [];
		    if (is_array($ali_order['child_order_list']['global_aeop_tp_child_order_dto']) && !empty($ali_order['child_order_list']['global_aeop_tp_child_order_dto'])) {
			    if (isset($ali_order['child_order_list']['global_aeop_tp_child_order_dto'][0])) {
				    $products_list = $ali_order['child_order_list']['global_aeop_tp_child_order_dto'];
			    }
			    else {
				    $products_list[] = $ali_order['child_order_list']['global_aeop_tp_child_order_dto'];
			    }
		    }
		    foreach ($products_list as $item) {
			    if (!Helper::isUtf()) {
				    $item['product_name'] = Helper::convertEncoding($item['product_name']);
			    }
                $order['PRODUCTS'][] = [
	                'PRODUCT_NAME'     => $item['product_name'],
					'PRODUCT_CODE'     => $item['sku_code'],
					'PRICE'            => $item['product_price']['amount'],
	                'CURRENCY'         => 'RUB',
	                'QUANTITY'         => $item['product_count'],
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
