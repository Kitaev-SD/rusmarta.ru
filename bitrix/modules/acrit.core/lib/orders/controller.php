<?php
/**
 * Controller
 */

namespace Acrit\Core\Orders;

\Bitrix\Main\Loader::includeModule("sale");

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Bitrix\Sale,
	\Acrit\Core\Log,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class Controller
{
	const APP_HANDLER = '/bitrix/acrit_#MODULE_ID#_crm_auth.php';
	const EVENTS_HANDLER = '/bitrix/acrit_#MODULE_ID#_crm_handler.php';
    public static $SERVER_ADDR;
    protected static $MANUAL_RUN = false;

	static $MODULE_ID = '';
	static $profile = false;

	public static function setModuleId($value) {
		self::$MODULE_ID = $value;
		Settings::setModuleId($value);
	}

	function setProfile(int $profile_id) {
		self::$profile = Helper::call(self::$MODULE_ID, 'OrdersProfiles', 'getProfiles', [$profile_id]);
		//\Helper::Log('(getOrderProfile) selected profile "' . self::$profile['id'] . '"');
	}

	public static function getAppHandler() {
		$module_code = str_replace('acrit.', '', self::$MODULE_ID);
		$link = str_replace('#MODULE_ID#', $module_code, self::APP_HANDLER);
		return $link;
	}

	public static function setBulkRun() {
		self::$MANUAL_RUN = true;
		Rest::setBulkRun();
    }

	public static function isBulkRun() {
		return self::$MANUAL_RUN;
    }

	/**
	 * Sync store with order
	 */

	public static function syncExtToStore($ext_order) {
		$incl_res = \Bitrix\Main\Loader::includeSharewareModule(self::$MODULE_ID);
		if ($incl_res == \Bitrix\Main\Loader::MODULE_NOT_FOUND || $incl_res == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
			return;
		}
		// Has synchronization active
		$sync_active = (self::$profile['ACTIVE'] == 'Y');
		if (!$sync_active) {
			return;
		}
		// Check order data
		$order_id = self::findOrder($ext_order);
		// Ignore old external orders
		$start_date_ts = self::getStartDateTs();
		if ($start_date_ts && $ext_order['DATE_INSERT'] < $start_date_ts) {
			return;
		}
		// Get profile
		if (!self::$profile) {
			Log::getInstance(self::$MODULE_ID)->add('(syncExtToStore) error: profile empty', false, true);
			return;
		}
		// Run sync
		OrderSync::runSync($order_id, $ext_order, self::$profile);
	}

	/**
	 * Search of deal
	 */

	public static function findOrder(array $ext_order) {
		$order_id = false;
		$res = \Bitrix\Sale\Order::getList([
			'select' => ['ID'],
			'filter' => [
				"XML_ID" => $ext_order['ID'],
			],
		]);
		if ($order = $res->fetch()){
			$order_id = $order['ID'];
		}
		return $order_id;
	}

	/**
	 * Sync all orders by period
	 */

	function syncByPeriod($sync_interval=0) {
		Log::getInstance(self::$MODULE_ID)->add('(syncByPeriod) run period ' . $sync_interval);
		// Get plugin object
		$plugin = false;
		if (strlen(self::$profile['PLUGIN'])) {
			$arProfilePlugin = Exporter::getInstance(self::$MODULE_ID)->getPluginInfo(self::$profile['PLUGIN']);
			if (is_array($arProfilePlugin)) {
				$strPluginClass = $arProfilePlugin['CLASS'];
				if (strlen($strPluginClass) && class_exists($strPluginClass)) {
					$plugin = new $strPluginClass(self::$MODULE_ID);
					$plugin->setProfileArray(self::$profile);
				}
			}
		}
		// List of orders, changed by last period (if period is not set than get all orders)
		if ($plugin) {
			$filter = [];
			if ($sync_interval > 0) {
				$filter['change_date_from'] = time() - $plugin->modifSyncInterval($sync_interval);
			}
			try {
				$orders_ids = $plugin->getOrdersIDsList($filter);
				Log::getInstance(self::$MODULE_ID)->add('(syncByPeriod) orders ' . print_r($orders_ids, true), false, true);
			} catch (\Exception $e) {
				Log::getInstance(self::$MODULE_ID)->add('(syncByPeriod) get orders error: "' . $e->getMessage() . '" [' . $e->getCode() . ']');
			}
			foreach ($orders_ids as $order_id) {
				$order_data = $plugin->getOrder($order_id);
				try {
					self::syncExtToStore($order_data);
				} catch (\Exception $e) {
					Log::getInstance(self::$MODULE_ID)->add('(syncByPeriod) can\'t sync of order ' . $order_data['ID']);
				}
			}
		}
		Log::getInstance(self::$MODULE_ID)->add('(syncByPeriod) success');
	}

	public static function getStartDateTs() {
		$start_date_ts = false;
		$start_date = self::$profile['CONNECT_CRED']['start_date'];
		if ($start_date) {
			$start_date_ts = strtotime(date('d.m.Y 00:00:00', strtotime($start_date)));
		}
		return $start_date_ts;
	}

	public static function getSiteDef() {
		$site_id = false;
		$site_default = false;
		$result = \Bitrix\Main\SiteTable::getList([]);
		while ($site = $result->fetch()) {
			if (!$site_default) {
				$site_default = $site;
			}
			if ($site['DEF'] == 'Y') {
				$site_default = $site;
			}
		}
		if ($site_default) {
			$site_id = $site_default['LID'];
		}
		return $site_id;
	}

}