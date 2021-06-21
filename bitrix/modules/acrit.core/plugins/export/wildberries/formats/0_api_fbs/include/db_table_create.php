<?
/**
 * Acrit Core: create tables for wildberries
 */

namespace Acrit\Core\Export\Plugins\WildberriesHelpers;

use
	\Acrit\Core\Helper;

$arTables = [
	'acrit_wb_attribute' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'CATEGORY_NAME' => 'VARCHAR(255) NOT NULL',
		'HASH' => 'VARCHAR(16) NOT NULL',
		'NAME' => 'VARCHAR(1024) NOT NULL',
		'TYPE' => 'CHAR(1) NOT NULL',
		'SORT' => 'int(11) NOT NULL DEFAULT 100',
		'USE_ONLY_DICTIONARY_VALUES' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'MAX_COUNT' => 'int(11) DEFAULT NULL',
		'IS_REQUIRED' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'IS_AVAILABLE' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'IS_NUMBER' => 'CHAR(1) NOT NULL DEFAULT \'N\'',
		'UNIT' => 'VARCHAR(10) DEFAULT NULL',
		'UNITS' => 'VARCHAR(255) DEFAULT NULL',
		'DICTIONARY' => 'VARCHAR(50) DEFAULT NULL',
		'SESSION_ID' => 'VARCHAR(32) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_wb_attribute_1` (`HASH`)',
	],
	'acrit_wb_image' => [
		'ID' => 'int(11) NOT NULL auto_increment',
		'MODULE_ID' => 'VARCHAR(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int(11) NOT NULL',
		'ELEMENT_ID' => 'int(11) NOT NULL',
		'UUID' => 'VARCHAR(36) NOT NULL',
		'URL' => 'VARCHAR(1024) NOT NULL',
		'TYPE' => 'VARCHAR(10) NOT NULL',
		'TIMESTAMP_X' => 'DATETIME NOT NULL',
		'PRIMARY KEY (ID)',
		'KEY `ix_perf_acrit_wb_image_1` (`UUID`)',
		'KEY `ix_perf_acrit_wb_image_2` (`ELEMENT_ID`,`PROFILE_ID`)',
	],
	'acrit_wb_task' => [
		'ID' => 'int NOT NULL AUTO_INCREMENT',
		'MODULE_ID' => 'varchar(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int NOT NULL',
		'TASK_UUID' => 'varchar(36) NOT NULL',
		'SUPPLIER_ID' => 'varchar(36) NOT NULL',
		'PRODUCTS_COUNT' => 'int DEFAULT \'0\'',
		'STOCKS_REQUEST' => 'longtext',
		'STOCKS_RESPONSE' => 'longtext',
		'STOCKS_RESPONSE_CODE' => 'int DEFAULT NULL',
		'SESSION_ID' => 'varchar(32) NOT NULL',
		'TIMESTAMP_X' => 'datetime NOT NULL',
		'PRIMARY KEY (`ID`)',
	],
	'acrit_wb_history' => [
		'ID' => 'int NOT NULL AUTO_INCREMENT',
		'MODULE_ID' => 'varchar(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int NOT NULL',
		'TASK_ID' => 'int NOT NULL',
		'REQUEST_ID' => 'varchar(36) NOT NULL',
		'SUPPLIER_VENDOR_CODE' => 'varchar(36) NOT NULL',
		'CARD_ID' => 'varchar(36) DEFAULT NULL',
		'IMT_ID' => 'int DEFAULT NULL',
		'NOMENCLATURE_ID' => 'varchar(36) DEFAULT NULL',
		'NM_ID' => 'int DEFAULT NULL',
		'VENDOR_CODE' => 'varchar(255) DEFAULT NULL',
		'ELEMENT_ID' => 'int DEFAULT NULL',
		'STOCK_VALUE' => 'int DEFAULT NULL',
		'JSON' => 'longtext NOT NULL',
		'RESPONSE' => 'text',
		'SUCCESS' => 'char(1) DEFAULT \'N\'',
		'CHRT_ID' => 'text',
		'BARCODE' => 'text',
		'STOCK_UPDATED' => 'char(1) NOT NULL DEFAULT \'N\'',
		'STOCK_ERRORS' => 'varchar(255) DEFAULT NULL',
		'STATUS' => 'text',
		'STATUS_DATETIME' => 'datetime DEFAULT NULL',
		'SESSION_ID' => 'varchar(32) NOT NULL',
		'TIMESTAMP_X' => 'datetime NOT NULL',
		'PRIMARY KEY (`ID`)',
		'KEY `ix_perf_acrit_wb_history_1` (`TASK_ID`,`PROFILE_ID`)',
	],
	'acrit_wb_history_stock' => [
		'ID' => 'int NOT NULL AUTO_INCREMENT',
		'MODULE_ID' => 'varchar(50) DEFAULT \'acrit.exportproplus\'',
		'PROFILE_ID' => 'int NOT NULL',
		'TASK_ID' => 'int NOT NULL',
		'NM_ID' => 'int NOT NULL',
		'CHRT_ID' => 'int NOT NULL',
		'PRICE' => 'double NOT NULL',
		'QUANTITY' => 'double NOT NULL',
		'STORE_ID' => 'int DEFAULT NULL',
		'SUCCESS' => 'char(1) DEFAULT \'N\'',
		'SESSION_ID' => 'varchar(32) NOT NULL',
		'TIMESTAMP_X' => 'datetime NOT NULL',
		'PRIMARY KEY (`ID`)',
		'KEY `ix_perf_acrit_wb_history_st_1` (`NM_ID`,`TASK_ID`)',
	],
];
foreach($arTables as $strTableName => $arFields){
	$strSql = sprintf("SHOW TABLES LIKE '%s';", $strTableName);
	if(!\Bitrix\Main\Application::getConnection()->query($strSql)->fetch()){
		foreach($arFields as $key => $strValue){
			if(!is_numeric($key)){
				$arFields[$key] = sprintf('%s %s', $key, $strValue);
			}
		}
		$strSql = sprintf('CREATE TABLE IF NOT EXISTS `%s`(%s);', $strTableName, 
			PHP_EOL.implode(','.PHP_EOL, $arFields).PHP_EOL);
		\Bitrix\Main\Application::getConnection()->query($strSql);
	}
}
