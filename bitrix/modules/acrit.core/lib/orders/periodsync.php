<?php
/**
 *    Periodical synchronization
 */

namespace Acrit\Core\Orders;

use Bitrix\Main,
    Bitrix\Main\DB\Exception,
    Bitrix\Main\Config\Option,
	\Acrit\Core\Helper;

class PeriodSync
{
	static $MODULE_ID = '';

	public static function setModuleId($value) {
		self::$MODULE_ID = $value;
		Settings::setModuleId($value);
	}

	public static function set($profile_id) {
		$result = true;
		self::remove($profile_id);
		$profile = Helper::call(self::$MODULE_ID, 'OrdersProfiles', 'getProfiles', [$profile_id]);
		// Create agent
		if ($profile['SYNC']['add']['period']) {
			$sync_schedule = $profile['SYNC']['add']['period'];
			$agent_period = $sync_schedule * 60;
			if ($agent_period) {
				\CAgent::AddAgent("\\Acrit\\Core\\Orders\\PeriodSync::run('" . self::$MODULE_ID . "', $profile_id);", self::$MODULE_ID, "N", $agent_period);
			}
		}
		elseif (is_array($profile['SYNC']['add']) && !empty($profile['SYNC']['add'])) {
			foreach ($profile['SYNC']['add'] as $variant => $item) {
				$agent_period = $profile['SYNC']['add'][$variant]['period'];
				if ($profile['SYNC']['add'][$variant]['measure'] == 'h') {
					$agent_period *= 3600;
				}
				else {
					$agent_period *= 60;
				}
				if ($agent_period) {
					\CAgent::AddAgent("\\Acrit\\Core\\Orders\\PeriodSync::run('" . self::$MODULE_ID . "', $profile_id, $variant);", self::$MODULE_ID, "N", $agent_period);
				}
			}
		}
		return $result;
	}

	public static function remove($profile_id) {
		$result = true;
		// Remove agent
		\CAgent::RemoveAgent("\\Acrit\\Core\\Orders\\PeriodSync::run('".self::$MODULE_ID."', $profile_id);", self::$MODULE_ID);
		return $result;
	}

	// Run sync
	public static function run($module_id, $profile_id, $variant=0) {
		// Profile data
		$profile = Helper::call($module_id, 'OrdersProfiles', 'getProfiles', [$profile_id]);
		$sync_active = ($profile['ACTIVE'] == 'Y');
		// Run sync
		if ($sync_active) {
			Controller::setModuleId($module_id);
			Controller::setProfile($profile_id);
			$sync_interval = self::getSyncInterval($profile);
			Settings::set('last_update_ts', time());
			Controller::syncByPeriod($sync_interval);
		}
		return "\\Acrit\\Core\\Orders\\PeriodSync::run('$module_id', $profile_id);";
	}

	public static function getSyncInterval($profile, $variant=0) {
		if ($variant) {
			$profile_range = $profile['SYNC']['add'][$variant]['range'];
			if ($profile['SYNC']['add'][$variant]['measure'] == 'h') {
				$profile_range *= 3600;
			}
			else {
				$profile_range *= 60;
			}
		}
		else {
			$profile_range = (int) $profile['SYNC']['add']['period'] * 60 * 3;
		}
		$last_update_period = 0;
		$last_update_ts = Settings::get('last_update_ts');
		if ($last_update_ts) {
			$last_update_period = time() - $last_update_ts;
		}
		$sync_range = $last_update_period > $profile_range ? $last_update_period : $profile_range;
		return $sync_range;
	}
}