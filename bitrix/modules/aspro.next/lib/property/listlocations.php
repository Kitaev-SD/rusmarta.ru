<?
namespace Aspro\Next\Property;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class ListLocations{
	static function OnIBlockPropertyBuildList(){
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'SAsproListLocations',
			'DESCRIPTION' => Loc::getMessage('LOCATIONS_LINK_PROP_TITLE'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
		);
	}

	static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){
		static $cache = array();
		$html = '';
		if(Loader::includeModule('sale'))
		{
			$cache["LOCATIONS"] = array();
			$rsLoc = \CSaleLocation::GetList(array("CITY_NAME" => "ASC"), array());
			while($arLoc = $rsLoc->GetNext())
			{
				if($arLoc["CITY_NAME"])
					$cache["LOCATIONS"][$arLoc["ID"]] = $arLoc;
			}

			$varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
			$val = ($value["VALUE"] ? $value["VALUE"] : $arProperty["DEFAULT_VALUE"]);
			$html = '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">
			<option value="" >-</option>';
			foreach($cache["LOCATIONS"] as $arLocation)
			{
				$html .= '<option value="'.$arLocation["ID"].'"';
				if($val == $arLocation["~ID"])
					$html .= ' selected';
				$html .= '>'.$arLocation["CITY_NAME"].'</option>';
			}
			$html .= '</select>';
		}
		return $html;
	}

	static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields){
		$arPropertyFields = array(
            'HIDE' => array(
            	'SMART_FILTER',
            	'SEARCHABLE',
            	'COL_COUNT',
            	'ROW_COUNT',
            	'FILTER_HINT',
            ),
            'SET' => array(
            	'SMART_FILTER' => 'N',
            	'SEARCHABLE' => 'N',
            	'ROW_COUNT' => '10',
            ),
        );

		return $html;
	}
}
