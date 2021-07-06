<?
/**
 * Acrit Core: Yandex marketplace
 */

namespace Acrit\Core\Export\Plugins\YandexMarketplaceHelpers;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Entity;

Helper::loadMessages(__FILE__);

class StockTable extends Entity\DataManager {
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'acrit_yandex_marketplace_stocks';
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
			'MODULE_ID' => new Entity\StringField('MODULE_ID', array(
				'title' => Helper::getMessage($strLang.'MODULE_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Helper::getMessage($strLang.'PROFILE_ID'),
			)),
			'ELEMENT_ID' => new Entity\IntegerField('ELEMENT_ID', array(
				'title' => Helper::getMessage($strLang.'ELEMENT_ID'),
			)),
			'SKU' => new Entity\StringField('SKU', array(
				'title' => Helper::getMessage($strLang.'SKU'),
			)),
			'WAREHOUSE_ID' => new Entity\IntegerField('WAREHOUSE_ID', array(
				'title' => Helper::getMessage($strLang.'WAREHOUSE_ID'),
			)),
			'TYPE' => new Entity\StringField('TYPE', array(
				'title' => Helper::getMessage($strLang.'TYPE'),
			)),
			'COUNT' => new Entity\IntegerField('COUNT', array(
				'title' => Helper::getMessage($strLang.'COUNT'),
			)),
			'UPDATED_AT' => new Entity\StringField('UPDATED_AT', array(
				'title' => Helper::getMessage($strLang.'UPDATED_AT'),
			)),
			'SESSION_ID' => new Entity\StringField('SESSION_ID', array(
				'title' => Helper::getMessage($strLang.'SESSION_ID'),
			)),
			'TIMESTAMP_X' => new Entity\DatetimeField('TIMESTAMP_X', array(
				'title' => Helper::getMessage($strLang.'TIMESTAMP_X'),
			)),
			'DATE_RESET' => new Entity\DatetimeField('DATE_RESET', array(
				'title' => Helper::getMessage($strLang.'DATE_RESET'),
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
