<?
/**
 * Acrit Core: create tables for ozon
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins\OzonRuHelpers;

use
	\Acrit\Core\Helper;

$obDatabase = \Bitrix\Main\Application::getConnection();

$arTables = [
	'acrit_ozon_attribute' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'CATEGORY_ID' => 'int(11) NOT NULL',
		'ATTRIBUTE_ID' => 'int(11) NOT NULL',
		'DICTIONARY_ID' => 'int(11) DEFAULT NULL',
		'NAME' => 'VARCHAR(255) NOT NULL',
		'DESCRIPTION' => 'TEXT DEFAULT NULL',
		'TYPE' => 'VARCHAR(50) NOT NULL',
		'IS_COLLECTION' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'IS_REQUIRED' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'GROUP_ID' => 'int(11) DEFAULT NULL',
		'GROUP_NAME' => 'VARCHAR(255) DEFAULT NULL',
		'LAST_VALUES_COUNT' => 'int(11) DEFAULT NULL',
		'LAST_VALUES_DATETIME' => 'DATETIME DEFAULT NULL',
		'LAST_VALUES_ELAPSED_TIME' => 'int(11) DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_ozon_attribute_1` (`ATTRIBUTE_ID`)',
	],
	'acrit_ozon_attribute_value' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'CATEGORY_ID' => 'int(11) NOT NULL',
		'ATTRIBUTE_ID' => 'int(11) NOT NULL',
		'DICTIONARY_ID' => 'int(11) NOT NULL',
		'VALUE_ID' => 'int(11) NOT NULL',
		'VALUE' => 'VARCHAR(255) NOT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_ozon_attribute_value_1` (`VALUE_ID`)',
	],
	'acrit_ozon_category' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'CATEGORY_ID' => 'int(11) NOT NULL',
		'NAME' => 'TEXT NOT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_ozon_category_1` (`CATEGORY_ID`)',
	],
	'acrit_ozon_task' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'TASK_ID' => 'int(11) NOT NULL',
		'PRODUCTS_COUNT' => 'int(11) NOT NULL',
		'JSON' => 'LONGTEXT DEFAULT NULL',
		'RESPONSE' => 'TEXT DEFAULT NULL',
		'STATUS' => 'TEXT DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'STATUS_DATETIME' => 'DATETIME DEFAULT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_ozon_task_1` (`TASK_ID`)',
		'KEY `ix_perf_acrit_ozon_task_2` (`PROFILE_ID`)',
	],
	'acrit_ozon_history' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'TASK_ID' => 'int(11) NOT NULL',
		'TASK_ID_OZON' => 'int(11) NOT NULL',
		'OFFER_ID' => 'VARCHAR(255) NOT NULL',
		'PRODUCT_ID' => 'int(11) NOT NULL',
		'ELEMENT_ID' => 'int(11) DEFAULT NULL',
		'STOCK_VALUE' => 'int(11) DEFAULT NULL',
		'JSON' => 'LONGTEXT NOT NULL',
		'RESPONSE' => 'TEXT DEFAULT NULL',
		'STOCK_UPDATED' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'STOCK_ERRORS' => 'VARCHAR(255) DEFAULT NULL',
		'STATUS' => 'TEXT DEFAULT NULL',
		'STATUS_DATETIME' => 'DATETIME DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_ozon_history_1` (`PROFILE_ID`, `MODULE_ID`)',
		'KEY `ix_perf_acrit_ozon_history_2` (`TASK_ID_OZON`)',
		'KEY `ix_perf_acrit_ozon_history_3` (`TASK_ID`, `OFFER_ID`)',
	],
	'acrit_ozon_history_stock' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'HISTORY_ID' => 'int(11) NOT NULL',
		'OFFER_ID' => 'VARCHAR(255) NOT NULL',
		'PRODUCT_ID' => 'int(11) DEFAULT NULL',
		'WAREHOUSE_ID' => 'int(11) DEFAULT NULL',
		'STOCK' => 'int(11) DEFAULT NULL',
		'UPDATED' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'ERRORS' => 'VARCHAR(255) DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
	],
];
foreach($arTables as $strTableName => $arFields){
	$strSql = sprintf("SHOW TABLES LIKE '%s';", $strTableName);
	if(!$obDatabase->query($strSql)->fetch()){
		foreach($arFields as $key => $strValue){
			if(!is_numeric($key)){
				$arFields[$key] = sprintf('%s %s', $key, $strValue);
			}
		}
		$strSql = sprintf('CREATE TABLE IF NOT EXISTS `%s`(%s);', $strTableName, 
			PHP_EOL.implode(','.PHP_EOL, $arFields).PHP_EOL);
			$obDatabase->query($strSql);
	}
}

$strSql = "SHOW COLUMNS FROM acrit_ozon_history LIKE 'TASK_ID_OZON'";
if(!$obDatabase->query($strSql)->fetch()){
	$obDatabase->startTransaction();
	$obDatabase->query("ALTER TABLE acrit_ozon_history CHANGE TASK_ID TASK_ID_OZON int(11) NOT NULL;");
	$obDatabase->query("ALTER TABLE acrit_ozon_history ADD TASK_ID int(11) NOT NULL AFTER PROFILE_ID;");
	$this->doMigrateTaskId();
	$obDatabase->commitTransaction();
}
