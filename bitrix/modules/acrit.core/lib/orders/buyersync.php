<?
/**
 * Users (buyers) data synchronization
 */

namespace Acrit\Core\Orders;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Bitrix\Main\Context,
	Bitrix\Currency\CurrencyManager,
	Bitrix\Sale\Order,
	Bitrix\Sale\Basket,
	Bitrix\Sale\Delivery,
	Bitrix\Sale\PaySystem,
	\Acrit\Core\Log,
	\Acrit\Core\Helper;
use PhpOffice\PhpSpreadsheet\Exception;

Loc::loadMessages(__FILE__);

class BuyerSync {

	/**
	 * Update user of order
	 */

	public static function runSync(array $ext_order, $profile) {
		$user_id = false;
		// User data
		$user_fields = self::getBuyerFields($ext_order, $profile);
		// Search user
		if (!empty($user_fields)) {
			$user_id = self::findBuyer($user_fields);
		}
		// Create user
		if (!$user_id) {
			$user_id = self::createBuyer($user_fields, $profile);
		}
		// Default buyer
		if (!$user_id) {
			$user_id = $profile['CONNECT_DATA']['buyer'];
		}
		return $user_id;
	}


	/**
	 * Get fields for search or generate buyer
	 */

	public static function getBuyerFields(array $ext_order, $profile) {
		$buyer_fields = [];
		$user_fields = $ext_order['FIELDS'];
		$comp_table = (array)$profile['CONTACTS']['table_compare'];
		foreach ($comp_table as $order_f_id => $ext_f_id) {
			// User fields
			if ($ext_f_id) {
				$value = $user_fields[$ext_f_id]['VALUE'][0];
				if ($value) {
					$buyer_fields[$order_f_id] = $value;
				} else {
					$buyer_fields[$order_f_id] = '';
				}
			}
		}
		// Default values
		$buyer_fields['EMAIL'] = $buyer_fields['EMAIL'] ? : $profile['CONTACTS']['email_def'];
		$email = $buyer_fields['EMAIL'];
		if (!empty($buyer_fields) && $email) {
			if ( ! $buyer_fields['LOGIN']) {
				$buyer_fields['LOGIN'] = $email;
			}
		}
		return $buyer_fields;
	}


	/**
	 * Try to find a buyer by login or email
	 */

	public static function findBuyer(array $fields) {
		$user_id = false;
		if ($fields['LOGIN']) {
			$db_user = \Bitrix\Main\UserTable::getList(array(
				'select' => ['ID'],
				'filter' => ['LOGIN' => $fields['LOGIN']]
			));
			if ($user_data = $db_user->fetch()) {
				$user_id = $user_data['ID'];
			}
		}
		if (!$user_id && $fields['EMAIL']) {
			$db_user = \Bitrix\Main\UserTable::getList(array(
				'select' => ['ID'],
				'filter' => ['EMAIL' => $fields['EMAIL']]
			));
			if ($user_data = $db_user->fetch()) {
				$user_id = $user_data['ID'];
			}
		}
		return $user_id;
	}


	/**
	 * Create new buyer
	 */

	public static function createBuyer(array $fields, array $profile) {
		$user_id = false;
		$fields['EMAIL'] = $fields['EMAIL'] ? : $profile['CONTACTS']['email_def'];
		$email = $fields['EMAIL'];
		if (!empty($fields) && $email) {
			if (!$fields['LOGIN']) {
				$fields['LOGIN'] = $email;
			}
			$fields['PASSWORD'] = md5($email . rand(1000, 9999));
			$user = new \CUser;
			$user_id = $user->Add($fields);
			if (!intval($user_id)) {
				Log::getInstance(Controller::$MODULE_ID)->add('(createBuyer) error ' . $user->LAST_ERROR, false, true);
			}
		}
		return $user_id;
	}

}
