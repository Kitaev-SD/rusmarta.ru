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

class ImageTable extends Entity\DataManager {
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'acrit_wb_image';
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
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Helper::getMessage($strLang.'PROFILE_ID'),
			)),
			'MODULE_ID' => new Entity\StringField('MODULE_ID', array(
				'title' => Helper::getMessage($strLang.'MODULE_ID'),
			)),
			'ELEMENT_ID' => new Entity\IntegerField('ELEMENT_ID', array(
				'title' => Helper::getMessage($strLang.'ELEMENT_ID'),
			)),
			'UUID' => new Entity\StringField('UUID', array(
				'title' => Helper::getMessage($strLang.'UUID'),
			)),
			'URL' => new Entity\StringField('URL', array(
				'title' => Helper::getMessage($strLang.'URL'),
			)),
			'TYPE' => new Entity\StringField('TYPE', array(
				'title' => Helper::getMessage($strLang.'TYPE'),
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
