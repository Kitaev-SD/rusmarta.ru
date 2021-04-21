<?
namespace Aspro\Next\Property;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class ListStores{
	static function OnIBlockPropertyBuildList(){
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'SAsproListStores',
			'DESCRIPTION' => Loc::getMessage('STORES_LINK_PROP_TITLE'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array(__CLASS__, 'GetPropertyFieldHtmlMulty'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
		);
	}

	static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){
		static $cache = array();
		$html = '';
		if(Loader::includeModule('catalog'))
		{
			$cache["STORES"] = array();
			$rsStore = \CCatalogStore::GetList( array("SORT" => "ASC"), array() );
			while($arStore = $rsStore->GetNext())
			{
				$cache["STORES"][] = $arStore;
			}

			$varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
			$val = ($value["VALUE"] ? $value["VALUE"] : $arProperty["DEFAULT_VALUE"]);
			if($arProperty['MULTIPLE'] == 'Y')
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'[]" multiple size="6" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
			else
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';

			$html .= '<option value="component" '.($val == "component" ? 'selected' : '').'>'.Loc::getMessage("FROM_COMPONENTS_TITLE").'</option>';
			foreach($cache["STORES"] as $arStore)
			{
				$html .= '<option value="'.$arStore["ID"].'"';
				if($val == $arStore["~ID"])
					$html .= ' selected';
				$html .= '>'.$arStore["TITLE"].'</option>';
			}
			$html .= '</select>';
		}
		return $html;
	}

	static function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName){
		static $cache = array();
		$html = '';
		if(Loader::includeModule('catalog'))
		{
			$cache["STORES"] = array();
			$rsStore = \CCatalogStore::GetList( array("SORT" => "ASC"), array() );
			while($arStore = $rsStore->GetNext())
			{
				$cache["STORES"][] = $arStore;
			}

			$varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
			$arValues = array();
			if($value && is_array($value))
			{
				foreach($value as $arValue)
				{
					$arValues[] = $arValue["VALUE"];
				}
			}
			else
				$arValues[] = $arProperty["DEFAULT_VALUE"];

			if($arProperty['MULTIPLE'] == 'Y')
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'[]" multiple size="6" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
			else
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';

			$html .= '<option value="component" '.(in_array("component", $arValues) ? 'selected' : '').'>'.Loc::getMessage("FROM_COMPONENTS_TITLE").'</option>';
			foreach($cache["STORES"] as $arStore)
			{
				$html .= '<option value="'.$arStore["ID"].'"';
				if(in_array($arStore["~ID"], $arValues))
					$html .= ' selected';
				$html .= '>'.$arStore["TITLE"].'</option>';
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
