<?
/**
 *	Class to clean old data
 */
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

class Cleaner {

	protected static $cleanMap = [
		# General history
		'acrit_#MODULE_CODE#_new_history' => 'DATE_START',
		# Ozon
		'acrit_ozon_task' => 'TIMESTAMP_X',
		'acrit_ozon_history' => 'TIMESTAMP_X',
		'acrit_ozon_history_stock' => 'TIMESTAMP_X',
		# Wildberries
		'acrit_wb_task' => 'TIMESTAMP_X',
		'acrit_wb_image' => 'TIMESTAMP_X',
		'acrit_wb_history' => 'TIMESTAMP_X',
		'acrit_wb_history_stock' => 'TIMESTAMP_X',
	];

	/**
	 * Get agent execute name
	 */
	public static function getAgentName($moduleId){
		return sprintf('\%s::%s(\'%s\');', __CLASS__, 'agent', $moduleId);
	}

	/**
	 * Clean all old data
	 */
	public static function agent($moduleId){
		static::execute($moduleId);
		return static::getAgentName($moduleId);
	}

	/**
	 * Execute agent
	 */
	protected static function execute($moduleId){
		if(static::isAutoCleanHistoryOn($moduleId)){
			if(($days = static::getAutoCleanHistoryDays($moduleId)) > 0){
				foreach(static::$cleanMap as $table => $field){
					$table = static::prepareTableName($table, $moduleId);
					if(static::isTableExists($table)){
						static::deleteOldItemsFromTable($table, $field, $days);
					}
				}
			}
		}
	}

	/**
	 * Check auto clean is turned on
	 */
	protected static function prepareTableName($table, $moduleId){
		$moduleCode = end(explode('.', $moduleId));
		$replace = [
			'#MODULE_ID#' => $moduleId,
			'#MODULE_CODE#' => $moduleCode,
		];
		return str_replace(array_keys($replace), array_values($replace), $table);
	}

	/**
	 * Check auto clean is turned on
	 */
	protected static function isAutoCleanHistoryOn($moduleId){
		return Helper::getOption($moduleId, 'auto_clean_history') == 'Y';
	}

	/**
	 * Get autoclean days
	 */
	protected static function getAutoCleanHistoryDays($moduleId){
		$days = intVal(Helper::getOption($moduleId, 'auto_clean_history_days'));
		if($days <= 0){
			$days = 0;
		}
		return $days;
	}

	/**
	 * Check table is exists
	 */
	protected static function isTableExists($table){
		$sql = sprintf("SHOW TABLES LIKE '%s';", $table);
		return !!\Bitrix\Main\Application::getConnection()->query($sql)->fetch();
	}

	/**
	 * Delete old items from table with check by selected date column and days
	 */
	protected static function deleteOldItemsFromTable($table, $field, $days){
		$sql = sprintf("DELETE FROM %s WHERE %s < (NOW() - INTERVAL %d DAY);", $table, $field, $days);
		\Bitrix\Main\Application::getConnection()->query($sql);
	}

}
?>