<?
/**
 * Acrit Core: Orders integration plugin for Rozetka.com.ua
 * Documentation: https://api-seller.rozetka.com.ua/apidoc_ru/
 */

namespace Acrit\Core\Crm\Plugins;

require_once __DIR__.'/lib/api/orders.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\Orders\Plugins\RozetkaRuHelpers\Orders,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class Rozetka extends Plugin {

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
		return 'ROZETKA';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return 'Rozetka.com.ua';
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
			'id' => 'article',
			'name' => Loc::getMessage(self::getLangCode('PRODUCTS_ID_FIELD_NAME')),
		];
	}

	/**
	 * Get credentials
	 */
	public function getCredPwd() {
	    $password = $this->arProfile['CONNECT_CRED']['password'];
		return $password;
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
		$list = [];
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
			'id' => '4',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_4')),
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
		$list[] = [
			'id' => '10',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_10')),
		];
		$list[] = [
			'id' => '11',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_11')),
		];
		$list[] = [
			'id' => '12',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_12')),
		];
		$list[] = [
			'id' => '13',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_13')),
		];
		$list[] = [
			'id' => '15',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_15')),
		];
		$list[] = [
			'id' => '16',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_16')),
		];
		$list[] = [
			'id' => '17',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_17')),
		];
		$list[] = [
			'id' => '18',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_18')),
		];
		$list[] = [
			'id' => '19',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_19')),
		];
		$list[] = [
			'id' => '20',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_20')),
		];
		$list[] = [
			'id' => '24',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_24')),
		];
		$list[] = [
			'id' => '25',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_25')),
		];
		$list[] = [
			'id' => '26',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_26')),
		];
		$list[] = [
			'id' => '27',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_27')),
		];
		$list[] = [
			'id' => '28',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_28')),
		];
		$list[] = [
			'id' => '29',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_29')),
		];
		$list[] = [
			'id' => '30',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_30')),
		];
		$list[] = [
			'id' => '31',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_31')),
		];
		$list[] = [
			'id' => '32',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_32')),
		];
		$list[] = [
			'id' => '33',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_33')),
		];
		$list[] = [
			'id' => '34',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_34')),
		];
		$list[] = [
			'id' => '35',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_35')),
		];
		$list[] = [
			'id' => '36',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_36')),
		];
		$list[] = [
			'id' => '37',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_37')),
		];
		$list[] = [
			'id' => '38',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_38')),
		];
		$list[] = [
			'id' => '39',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_39')),
		];
		$list[] = [
			'id' => '40',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_40')),
		];
		$list[] = [
			'id' => '41',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_41')),
		];
		$list[] = [
			'id' => '42',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_42')),
		];
		$list[] = [
			'id' => '43',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_43')),
		];
		$list[] = [
			'id' => '44',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_44')),
		];
		$list[] = [
			'id' => '45',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_45')),
		];
		$list[] = [
			'id' => '46',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_46')),
		];
		$list[] = [
			'id' => '47',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_47')),
		];
		$list[] = [
			'id' => '48',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_48')),
		];
		$list[] = [
			'id' => '49',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_49')),
		];
		$list[] = [
			'id' => '50',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_50')),
		];
		$list[] = [
			'id' => '51',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_51')),
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
			'id' => 'buyer_id',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'buyer_last_name',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_LAST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'buyer_first_name',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_FIRST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'buyer_second_name',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_SECOND_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'buyer_full_name',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_FULL_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'buyer_phone',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_PHONE')),
			'direction' => self::SYNC_STOC,
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
			'id' => 'id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'market_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_MARKET_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'created',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREATED')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'changed',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CHANGED')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'amount',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_AMOUNT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'amount_with_discount',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_AMOUNT_WITH_DISCOUNT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'cost',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_COST')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'cost_with_discount',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_COST_WITH_DISCOUNT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_last_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_LAST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_first_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_FIRST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_second_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_SECOND_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_full_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_FULL_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_phone',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_PHONE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'seller_comment_created',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SELLER_COMMENT_CREATED')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'current_seller_comment',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CURRENT_SELLER_COMMENT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'comment',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_COMMENT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'recipient_phone',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_RECIPIENT_PHONE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'recipient_first_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_RECIPIENT_FIRST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'recipient_last_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_RECIPIENT_LAST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'recipient_second_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_RECIPIENT_SECOND_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'recipient_full_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_RECIPIENT_FULL_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'from_warehouse',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FROM_WAREHOUSE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'ttn',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_TTN')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'total_quantity',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_TOTAL_QUANTITY')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'created_type',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREATED_TYPE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'callback_off',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CALLBACK_OFF')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'is_fulfillment',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_IS_FULFILLMENT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'credit_info_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREDIT_INFO_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'credit_info_field',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREDIT_INFO_FIELD')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'credit_info_value',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREDIT_INFO_VALUE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_data_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_DATA_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_data_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_DATA_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_data_name_uk',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_DATA_NAME_UK')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_data_name_en',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_DATA_NAME_EN')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_data_group',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_DATA_GROUP')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_service_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_SERVICE_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_service_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_SERVICE_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_method_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_METHOD_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_place_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PLACE_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_pickup_rz_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PICKUP_RZ_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_place_street',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PLACE_STREET')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_place_number',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PLACE_NUMBER')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_place_house',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PLACE_HOUSE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_place_flat',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PLACE_FLAT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_cost',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_COST')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_city',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_CITY')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_ref_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_REF_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_street_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_STREET_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'payment_type',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAYMENT_TYPE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'payment_type_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAYMENT_TYPE_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_payment_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_PAYMENT_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_payment_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_PAYMENT_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_payment_value',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_PAYMENT_VALUE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_payment_created_at',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_PAYMENT_CREATED_AT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'payment_invoice_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAYMENT_INVOICE_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'credit_status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREDIT_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'credit_broker',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREDIT_BROKER')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'feedback_count',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FEEDBACK_COUNT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'is_promo',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_IS_PROMO')),
			'direction' => self::SYNC_STOC,
		];
		return $list;
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
            <tr class="heading" id="tr_HEADING_CONNECT"><td colspan="2"><?=Loc::getMessage(self::getLangCode('SETTINGS_HEADING'));?></td></tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_LOGIN_HINT')));?>
					<?=Loc::getMessage(self::getLangCode('SETTINGS_LOGIN'));?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][login]" size="50" maxlength="255" data-role="connect-cred-login"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['login']);?>" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_PASSWORD_HINT')));?>
					<?=Loc::getMessage(self::getLangCode('SETTINGS_PASSWORD'));?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="password" name="PROFILE[CONNECT_CRED][password]" size="50" maxlength="255" data-role="connect-cred-password"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['password']);?>" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_LANG_HINT')));?>
					<?=Loc::getMessage(self::getLangCode('SETTINGS_LANG'));?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <select name="PROFILE[CONNECT_CRED][lang]">
                        <option value="uk"<?=$arProfile['CONNECT_CRED']['lang']=='uk'?' selected':''?>><?=Loc::getMessage(self::getLangCode('SETTINGS_LANG_VARIANTS_UK'));?></option>
                        <option value="ru"<?=$arProfile['CONNECT_CRED']['lang']=='ru'?' selected':''?>><?=Loc::getMessage(self::getLangCode('SETTINGS_LANG_VARIANTS_RU'));?></option>
                        <option value="en"<?=$arProfile['CONNECT_CRED']['lang']=='en'?' selected':''?>><?=Loc::getMessage(self::getLangCode('SETTINGS_LANG_VARIANTS_EN'));?></option>
                    </select>
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
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch ($strAction) {
			case 'connection_check':
				$message = '';
				$api = $this->getApi();
				$res = $api->checkConnection($message);
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
//		$api = new Orders($this->arProfile['CONNECT_CRED']['login'], $this->getCredPwd(), $this->arProfile['CONNECT_CRED']['lang'], $this->arProfile['ID'], $this->strModuleId);
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
			    'created_from' => date(Orders::DATE_FORMAT, $create_from_ts),
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
		if ($create_from_ts) {
			$req_filter = [
				'created_from' => date(Orders::DATE_FORMAT, $create_from_ts),
			];
		}
		if ($change_from_ts) {
			$req_filter = [
				'changed_from' => date(Orders::DATE_FORMAT, $change_from_ts),
			];
		}
		$api = $this->getApi();
		$orders_list = $api->getOrdersList($req_filter, 1000);
		foreach ($orders_list as $item) {
			$list[] = $item['id'];
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
				'ID'          => $mp_order['id'],
				'DATE_INSERT' => strtotime($mp_order['created']),
				'STATUS_ID'   => $mp_order['status'],
				'IS_CANCELED' => false,
			];
			// User data
			$order['USER'] = [
			];
			// Fields
			$order['FIELDS'] = [
				'id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['id']],
				],
				'market_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['market_id']],
				],
				'created' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['created']],
				],
				'changed' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['changed']],
				],
				'amount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['amount']],
				],
				'amount_with_discount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['amount_with_discount']],
				],
				'cost' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['cost']],
				],
				'cost_with_discount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['cost_with_discount']],
				],
				'buyer_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['user']['id']],
				],
				'buyer_last_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['user_title']['last_name']],
				],
				'buyer_first_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['user_title']['first_name']],
				],
				'buyer_second_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['user_title']['second_name']],
				],
				'buyer_full_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['user_title']['full_name']],
				],
				'buyer_phone' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['user_phone']],
				],
				'seller_comment_created' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['seller_comment_created']],
				],
				'current_seller_comment' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['current_seller_comment']],
				],
				'comment' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['comment']],
				],
				'recipient_phone' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['recipient_phone']],
				],
				'recipient_first_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['recipient_title']['first_name']],
				],
				'recipient_last_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['recipient_title']['last_name']],
				],
				'recipient_second_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['recipient_title']['second_name']],
				],
				'recipient_full_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['recipient_title']['full_name']],
				],
				'from_warehouse' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['from_warehouse']],
				],
				'ttn' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['ttn']],
				],
				'total_quantity' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['total_quantity']],
				],
				'created_type' => [
					'TYPE'  => 'STRING',
					'VALUE' => [Loc::getMessage(self::getLangCode('FIELDS_CREATED_TYPE_' . $mp_order['created_type']))],
				],
				'callback_off' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['callback_off']],
				],
				'is_fulfillment' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['is_fulfillment']],
				],
				'credit_info_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['credit_info']['id']],
				],
				'credit_info_field' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['credit_info']['field']],
				],
				'credit_info_value' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['credit_info']['value']],
				],
				'status_data_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_data']['id']],
				],
				'status_data_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_data']['name']],
				],
				'status_data_name_uk' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_data']['name_uk']],
				],
				'status_data_name_en' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_data']['name_en']],
				],
				'status_data_group' => [
					'TYPE'  => 'STRING',
					'VALUE' => [Loc::getMessage(self::getLangCode('FIELDS_STATUS_DATA_GROUP_' . $mp_order['status_data']['status_group']))],
				],
				'delivery_service_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['delivery_service_id']],
				],
				'delivery_service_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['delivery_service_name']],
				],
				'delivery_method_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['delivery_method_id']],
				],
				'delivery_place_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['place_id']],
				],
				'delivery_pickup_rz_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['pickup_rz_id']],
				],
				'delivery_place_street' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['place_street']],
				],
				'delivery_place_number' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['place_number']],
				],
				'delivery_place_house' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['place_house']],
				],
				'delivery_place_flat' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['place_flat']],
				],
				'delivery_cost' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['cost']],
				],
				'delivery_city' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['city']['title']],
				],
				'delivery_ref_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['ref_id']],
				],
				'delivery_street_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery']['street_id']],
				],
				'payment_type' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['payment_type']],
				],
				'payment_type_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['payment_type_name']],
				],
				'status_payment_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_payment']['status_payment_id']],
				],
				'status_payment_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_payment']['name']],
				],
				'status_payment_value' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_payment']['value']],
				],
				'status_payment_created_at' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_payment']['created_at']],
				],
				'payment_invoice_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['payment_invoice_id']],
				],
				'credit_status' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['credit_status']],
				],
				'credit_broker' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['credit_broker']],
				],
				'feedback_count' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['feedback_count']],
				],
				'is_promo' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['is_promo']],
				],
			];
			// Products
			$order['PRODUCTS'] = [];
			$products_list = [];
			if (is_array($mp_order['purchases']) && !empty($mp_order['purchases'])) {
				$products_list = $mp_order['purchases'];
			}
			foreach ($products_list as $item) {
				$order['PRODUCTS'][] = [
					'PRODUCT_NAME'     => $item['item_name'],
					'PRODUCT_CODE'     => $item['item']['article'],
					'PRICE'            => $item['price_with_discount'],
					'CURRENCY'         => 'UAH',
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

	// --- EVENTS ---

	/**
	 * Before the profile saved
	 */
	public function eventPageEditBeforeSave($arFields) {
		$arFields['CONNECT_CRED']['password'] = base64_encode($arFields['CONNECT_CRED']['password']);
		return $arFields;
	}

	/**
	 * When get the profile
	 */
	public function eventPageEditGetProfile($arFields) {
		$arFields['CONNECT_CRED']['password'] = base64_decode($arFields['CONNECT_CRED']['password']);
		return $arFields;
	}
}
