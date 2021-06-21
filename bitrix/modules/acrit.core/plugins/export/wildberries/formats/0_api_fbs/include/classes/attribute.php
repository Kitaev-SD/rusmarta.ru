<?
/**
 * Acrit Core: Wildberries sql
 * @documentation https://suppliers.wildberries.ru/remote-wh-site/api-content.html
 */

namespace Acrit\Core\Export\Plugins\WildberriesHelpers;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Entity;

Helper::loadMessages(__FILE__);

class AttributeTable extends Entity\DataManager {
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'acrit_wb_attribute';
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
			'CATEGORY_NAME' => new Entity\StringField('CATEGORY_NAME', array(
				'title' => Helper::getMessage($strLang.'CATEGORY_NAME'),
			)),
			'HASH' => new Entity\StringField('HASH', array(
				'title' => Helper::getMessage($strLang.'HASH'),
			)),
			'NAME' => new Entity\StringField('NAME', array(
				'title' => Helper::getMessage($strLang.'NAME'),
			)),
			'TYPE' => new Entity\StringField('TYPE', array(
				'title' => Helper::getMessage($strLang.'TYPE'),
			)),
			'SORT' => new Entity\IntegerField('SORT', array(
				'title' => Helper::getMessage($strLang.'SORT'),
			)),
			'USE_ONLY_DICTIONARY_VALUES' => new Entity\StringField('USE_ONLY_DICTIONARY_VALUES', array(
				'title' => Helper::getMessage($strLang.'USE_ONLY_DICTIONARY_VALUES'),
			)),
			'MAX_COUNT' => new Entity\IntegerField('MAX_COUNT', array(
				'title' => Helper::getMessage($strLang.'MAX_COUNT'),
			)),
			'IS_REQUIRED' => new Entity\StringField('IS_REQUIRED', array(
				'title' => Helper::getMessage($strLang.'IS_REQUIRED'),
			)),
			'IS_AVAILABLE' => new Entity\StringField('IS_AVAILABLE', array(
				'title' => Helper::getMessage($strLang.'IS_AVAILABLE'),
			)),
			'IS_NUMBER' => new Entity\StringField('IS_NUMBER', array(
				'title' => Helper::getMessage($strLang.'IS_NUMBER'),
			)),
			'UNIT' => new Entity\StringField('UNIT', array(
				'title' => Helper::getMessage($strLang.'UNIT'),
			)),
			'UNITS' => new Entity\StringField('UNITS', array(
				'title' => Helper::getMessage($strLang.'UNITS'),
			)),
			'DICTIONARY' => new Entity\StringField('DICTIONARY', array(
				'title' => Helper::getMessage($strLang.'DICTIONARY'),
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
