<?
/**
 * Acrit Core: ozon.ru history items
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins\OzonRuHelpers;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Entity;

Helper::loadMessages(__FILE__);

class HistoryStockTable extends Entity\DataManager {
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'acrit_ozon_history_stock';
	}
	
	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		\Acrit\Core\Export\Exporter::getLangPrefix(realpath(__DIR__.'/../../../class.php'), $strLang, $strHead, 
			$strName, $strHint);
		return array(
			'ID' => new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Helper::getMessage($strLang.'ID'),
			)),
			'MODULE_ID' => new Entity\IntegerField('MODULE_ID', array(
				'title' => Helper::getMessage($strLang.'MODULE_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Helper::getMessage($strLang.'PROFILE_ID'),
			)),
			'HISTORY_ID' => new Entity\IntegerField('HISTORY_ID', array(
				'title' => Helper::getMessage($strLang.'HISTORY_ID'),
			)),
			'OFFER_ID' => new Entity\StringField('OFFER_ID', array(
				'title' => Helper::getMessage($strLang.'OFFER_ID'),
			)),
			'PRODUCT_ID' => new Entity\StringField('PRODUCT_ID', array(
				'title' => Helper::getMessage($strLang.'PRODUCT_ID'),
			)),
			'WAREHOUSE_ID' => new Entity\IntegerField('WAREHOUSE_ID', array(
				'title' => Helper::getMessage($strLang.'WAREHOUSE_ID'),
			)),
			'STOCK' => new Entity\IntegerField('STOCK', array(
				'title' => Helper::getMessage($strLang.'STOCK'),
			)),
			'UPDATED' => new Entity\StringField('UPDATED', array(
				'title' => Helper::getMessage($strLang.'UPDATED'),
			)),
			'ERRORS' => new Entity\StringField('ERRORS', array(
				'title' => Helper::getMessage($strLang.'ERRORS'),
			)),
			'SESSION_ID' => new Entity\StringField('SESSION_ID', array(
				'title' => Helper::getMessage($strLang.'SESSION_ID'),
			)),
			'TIMESTAMP_X' => new Entity\DatetimeField('TIMESTAMP_X', array(
				'title' => Helper::getMessage($strLang.'TIMESTAMP_X'),
			)),
		);
	}
	
	/**
	 * Delete by filter
	 *
	 * @return array
	 */
	public static function deleteByFilter($arFilter=null) {
		$strTable = static::getTableName();
		$strSql = "DELETE FROM `{$strTable}` WHERE 1=1";
		if(is_array($arFilter)){
			foreach($arFilter as $strField => $strValue){
				$strEqual = '=';
				if(preg_match('#^(.*?)([A-z0-9_]+)(.*?)$#', $strField, $arMatch)){
					$strField = $arMatch[2];
					if($arMatch[1] == '!'){
						$strEqual = '!=';
					}
				}
				$strField = \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strField);
				$strValue = \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strValue);
				if(is_numeric($strField)){
					$strSql .= " AND ({$strValue})";
				}
				else{
					$strSql .= " AND (`{$strField}`{$strEqual}'{$strValue}')";
				}
			}
			$strSql .= ';';
		}
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}

}
