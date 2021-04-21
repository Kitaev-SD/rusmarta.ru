<?
namespace Aspro\Next\Property;

use Bitrix\Main\Localization\Loc,
	Bitrix\Iblock,
	Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class ListUsersGroups{
	static function OnIBlockPropertyBuildList(){
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'SAsproListUsersGroups',
			'DESCRIPTION' => Loc::getMessage('USERSGROUPS_LINK_PROP_TITLE'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array(__CLASS__, 'GetPropertyFieldHtmlMulty'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
		);
	}

	protected static function _getUserGroups(){
		static $arResult;

		if(!isset($arResult)){
			$arResult = array();

			$dbRes = \Bitrix\Main\GroupTable::getList(array(
			    'order' => array('C_SORT' => 'ASC'),
			    'filter'  => array('ACTIVE' => 'Y'),
			    'select'  => array(
			    	'ID',
			    	'NAME',
			    ),
			));
			while($arGroup = $dbRes->Fetch()){
				$arResult[$arGroup['ID']] = $arGroup['NAME'];
			}
		}

		return $arResult;
	}

	static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){
		$bEditProperty = $strHTMLControlName['MODE'] === 'EDIT_FORM';
		$bDetailPage = $strHTMLControlName['MODE'] === 'FORM_FILL';

		$arUserGroups = self::_getUserGroups();
		$val = ($value['VALUE'] ? $value['VALUE'] : $arProperty['DEFAULT_VALUE']);

		ob_start();
		?>
		<select name="<?=$strHTMLControlName['VALUE']?>">
			<?if($bEditProperty):?>
				<option value="">-</option>
			<?endif;?>
			<?foreach($arUserGroups as $id => $name):?>
				<option value="<?=$id?>"<?=($val == $id ? ' selected' : '')?>><?=('['.$id.'] '.$name)?></option>
			<?endforeach;?>
		</select>
		<?
		return ob_get_clean();
	}

	static function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName){
		$bEditProperty = $strHTMLControlName['MODE'] === 'EDIT_FORM';
		$bDetailPage = $strHTMLControlName['MODE'] === 'FORM_FILL';

		$arUserGroups = self::_getUserGroups();
		$arValues = ($value && is_array($value) ? array_column($value, 'VALUE') : array($arProperty['DEFAULT_VALUE']));

		ob_start();
		?>
		<select name="<?=$strHTMLControlName['VALUE']?>[]" multiple size="<?=$arProperty['MULTIPLE_CNT']?>">
			<?foreach($arUserGroups as $id => $name):?>
				<option value="<?=$id?>"<?=(in_array($id, $arValues) ? ' selected' : '')?>><?=('['.$id.'] '.$name)?></option>
			<?endforeach;?>
		</select>
		<?
		return ob_get_clean();
	}

	static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields){
		$arPropertyFields = array(
            'HIDE' => array(
            	'SMART_FILTER',
            	'SEARCHABLE',
            	'COL_COUNT',
            	'ROW_COUNT',
            	'FILTER_HINT',
            	'WITH_DESCRIPTION'
            ),
            'SET' => array(
            	'SMART_FILTER' => 'N',
            	'SEARCHABLE' => 'N',
            	'ROW_COUNT' => '10',
            	'WITH_DESCRIPTION' => 'N',
            ),
        );

		return $html;
	}
}
