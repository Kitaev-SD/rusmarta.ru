<?

namespace Acrit\Core\Orders;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class OrdersInfo {

	/**
	 * Get properties for external order ID
	 */
	public static function getOrderExtIDFields(array $profile=[]) {
		$result = [];
		// Base field
		$result[] = [
			'id' => 'XML_ID',
			'name' => Loc::getMessage('ACRIT_CRM_PLUGIN_ORDERID_FIELD_XML_ID'),
		];
		// Properties
		$db = \Bitrix\Sale\Property::getList([
			'order' => ['ID' => 'asc'],
			'select' => ['ID', 'NAME', 'PERSON_TYPE_ID', 'TYPE', 'MULTIPLE'],
		]);
		while ($prop = $db->Fetch()) {
			// Check props sync availibility
			if (!in_array($prop['TYPE'], ['STRING', 'NUMBER'])) {
				continue;
			}
			// Add to the result
			if ($profile['FIELDS']['table_compare'][$prop['ID']]['value']) {
				$result[] = [
					'id'   => $prop['ID'],
					'name' => $prop['NAME'],
				];
			}
		}
		return $result;
	}

	/**
	 * Fields for the order user
	 */
	public static function getUserFields() {
		$result = [
			'LOGIN' =>[
				'name' => Loc::getMessage("ORDERS_PORTAL_LOGIN"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'LOGIN',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_LOGIN_HINT"),
			],
			'LAST_NAME' => [
				'name' => Loc::getMessage("ORDERS_PORTAL_LAST_NAME"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'LAST_NAME',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_LAST_NAME_HINT"),
			],
			'NAME' => [
				'name' => Loc::getMessage("ORDERS_PORTAL_NAME"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'NAME',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_NAME_HINT"),
			],
			'SECOND_NAME' => [
				'name' => Loc::getMessage("ORDERS_PORTAL_SECOND_NAME"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'SECOND_NAME',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_SECOND_NAME_HINT"),
			],
			'EMAIL' =>[
				'name' => Loc::getMessage("ORDERS_PORTAL_EMAIL"),
				'direction' => Plugin::SYNC_STOC,
				'default' => 'EMAIL',
				'hint' => Loc::getMessage("ORDERS_PORTAL_CONTACT_EMAIL_HINT"),
			],
		];
		return $result;
	}

	/**
	 * Site users
	 */
	public static function getUsers($search='') {
		$result = [];
		$fields = [
			'select' => ['ID', 'SHORT_NAME', 'EMAIL'],
			'order' => ['ID' => 'DESC'],
		];
		if ($search) {
			$fields['filter'][] = [
				'LOGIC' => 'OR',
				'SHORT_NAME' => $search . '%',
				'EMAIL' => $search . '%',
			];
//			$fields['filter']['SHORT_NAME'] = $search . '%';
//			$fields['filter']['EMAIL'] = $search . '%';
		}
		$db = \Bitrix\Main\UserTable::getList($fields);
		while ($item = $db->fetch()) {
			$result[] = [
				'id' => $item['ID'],
				'name' => $item['SHORT_NAME'],
				'code' => $item['EMAIL'],
			];
		}
		return $result;
	}

	/**
	 * Get site user
	 */
	public static function getUser($id) {
		$result = false;
		$fields = [
			'filter' => ['ID' => $id],
			'select' => ['ID', 'SHORT_NAME', 'EMAIL'],
			'order' => ['ID' => 'DESC'],
		];
		$db = \Bitrix\Main\UserTable::getList($fields);
		if ($item = $db->fetch()) {
			$result = [
				'id' => $item['ID'],
				'name' => $item['SHORT_NAME'],
				'code' => $item['EMAIL'],
			];
		}
		return $result;
	}

	/**
	 * Site users group
	 */
	public static function getUserGroups() {
		$result = [];
		$filter = [];
		$db = \CGroup::GetList(($by="c_sort"), ($order="asc"), $filter);
		while ($item = $db->Fetch()) {
			$result[$item['ID']] = $item['NAME'];
		}
		return $result;
	}

	/**
	 * Order statuses
	 */
	public static function getStatuses() {
		$result = [];
		$filter = [
			'LID' => LANGUAGE_ID,
			'TYPE' => 'O',
		];
		$select = ['ID', 'NAME'];
		$db = \CSaleStatus::GetList(['SORT' => 'ASC'], $filter, false, false, $select);
		while ($item = $db->Fetch()) {
			$result[] = [
				'id' => $item['ID'],
				'name' => $item['NAME'],
			];
		}
		return $result;
	}

	/**
	 * List of person types
	 */
	public static function getPersonTypes() {
		$result = [];
		$filter = [];
		$select = ['ID', 'NAME'];
		$db = \CSalePersonType::GetList(['SORT' => 'ASC'], $filter, false, false, $select);
		while ($item = $db->Fetch()) {
			$result[] = [
				'id' => $item['ID'],
				'name' => $item['NAME'],
			];
		}
		return $result;
	}

	/**
	 * Order properties
	 */
	public static function getProps() {
		$result = [];
		$db = \Bitrix\Sale\Property::getList([
			'order' => ['ID' => 'asc'],
			'select' => ['ID', 'NAME', 'PERSON_TYPE_ID', 'TYPE', 'MULTIPLE'],
		]);
		while ($prop = $db->Fetch()) {
			// Check props sync availibility
			if (!in_array($prop['TYPE'], Plugin::PROPS_AVAILABLE)) {
				continue;
			}
			$prop['SYNC_DIR'] = Plugin::SYNC_ALL;
			switch ($prop['TYPE']) {
				case 'FILE':
					$prop['SYNC_DIR'] = Plugin::SYNC_STOC;
					if ($prop['MULTIPLE'] == 'Y') {
						continue 2;
					}
					break;
				case 'CHECKBOX':
				case 'RADIO':
					if ($prop['MULTIPLE'] == 'Y') {
						continue 2;
					}
					break;
				case 'LOCATION':
					$prop['SYNC_DIR'] = Plugin::SYNC_STOC;
					break;
				default:
			}
			// Hints
			$prop['HINT'] = Loc::getMessage("SP_CI_PROP_".$prop['TYPE']."_HINT");
			// Add to the result
			$result[$prop['PERSON_TYPE_ID']][] = [
				'ID' => $prop['ID'],
				'NAME' => $prop['NAME'],
				'SYNC_DIR' => $prop['SYNC_DIR'],
				'HINT' => $prop['HINT'],
			];
		}
		return $result;
	}

}
