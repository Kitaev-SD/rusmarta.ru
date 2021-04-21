<?
namespace Aspro\Next\Property;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class CustomFilter{
	static function OnIBlockPropertyBuildList(){
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'SAsproCustomFilter',
			'DESCRIPTION' => Loc::getMessage('CUSTOM_FILTER_PROP_TITLE'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
			'PrepareSettings' => array(__CLASS__, 'PrepareSettings'),
		);
	}

	static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName){
        return '';
	}

	static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){
		static $cache, $jsFile, $ajaxFile;

		$html = '';

		$bEditProperty = $strHTMLControlName['MODE'] === 'EDIT_FORM';
		$bDetailPage = $strHTMLControlName['MODE'] === 'FORM_FILL';

		if($bDetailPage){
			if(!isset($cache)){
				$cache = array();
				$jsFile = '/bitrix/js/aspro.next/customfilter_control.js';
				$ajaxFile = '/bitrix/tools/aspro.next/customfilter_ajax.php';
				if(!file_exists($_SERVER['DOCUMENT_ROOT'].$jsFile) || !file_exists($_SERVER['DOCUMENT_ROOT'].$ajaxFile)){
					$jsFile = $ajaxFile = false;
				}
				else{
					$GLOBALS['APPLICATION']->AddHeadScript($jsFile);
				}
			}

			if($jsFile){
				if(Loader::includeModule('fileman')){
					$val = strlen($value['VALUE']) ? $value['VALUE'] : '[]';

					$iblockId = isset($arProperty['USER_TYPE_SETTINGS']) && isset($arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID']) ? $arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID'] : 0;
					$offersIblockId = false;

					$useOffersIblock = isset($arProperty['USER_TYPE_SETTINGS']) && isset($arProperty['USER_TYPE_SETTINGS']['USE_OFFERS_IBLOCK']) ? ($arProperty['USER_TYPE_SETTINGS']['USE_OFFERS_IBLOCK'] === 'Y' ? 'Y' : 'N') : 'N';

					if($iblockId){
						if($useOffersIblock === 'Y'){
							if(!isset($cache[$iblockId])){
								if(Loader::includeModule('catalog')){
									$arInfo = \CCatalogSKU::GetInfoByProductIBlock($iblockId);
									$offersIblockId = $cache[$iblockId] = $arInfo ? $arInfo['IBLOCK_ID'] : false;
								}
							}
							else{
								$offersIblockId = $cache[$iblockId];
							}
						}

						$html = '<input type="hidden" id="'.$strHTMLControlName['VALUE'].'" name="'.$strHTMLControlName['VALUE'].'" value="'.htmlspecialcharsbx(is_array($val) ? reset($val) : $val).'" data-bx-property-id="'.$arProperty['CODE'].'" data-bx-comp-prop="true" />';
						$html .= "\n".'<script>'.
							'var tv = BX(\'tr_PROPERTY_'.$arProperty['ID'].'\');'.
							'if(tv){'.
								'var iv = BX(\''.$strHTMLControlName['VALUE'].'\');'.
								'if(iv){'.
									'var td = BX.findParent(iv, {tag: \'td\'});'.
									'if(td){'.
										'var tdd = BX.findChildren(td, {tag: \'div\'}, true);'.
										'if(tdd){'.
											'for(var i in tdd){'.
												'BX.cleanNode(tdd[i]);'.
											'}'.
										'}'.
										'initAsproNextCustomFilterControl({'.
											'data: \'{"iblockId":'.$iblockId.($offersIblockId ? ',"offersIblockId":'.$offersIblockId : '').'}\','.
											'oCont: td,'.
											'oInput: iv,'.
											'propertyID: \''.$arProperty['CODE'].'\','.
											'propertyParams: {'.
												'DEFAULT: \'\','.
												'ID: \''.$arProperty['CODE'].'\','.
												'IBLOCK_ID: \''.$arProperty['IBLOCK_ID'].'\','.
												'JS_DATA: \'{"iblockId":'.$iblockId.($offersIblockId ? ',"offersIblockId":'.$offersIblockId : '').'}\','.
												'JS_EVENT: \'initAsproNextCustomFilterControl\','.
												'AJAX_FILE: \''.$ajaxFile.'\','.
												'NAME: \''.Loc::getMessage('CUSTOM_FILTER_PROP_NAME').'\','.
												'JS_MESSAGES: \'{"invalid": "'.Loc::getMessage('CUSTOM_FILTER_PROP_INVALID').'"}\','.
												'MULTIPLE: \'N\','.
												'PARENT: \'DATA_SOURCE\','.
												'ROWS: 0,'.
												'TOOLTIP: \'\','.
												'TYPE: \'CUSTOM\','.
												'_propId: \''.$strHTMLControlName['VALUE'].'\''.
											'}'.
										'});'.
									'}'.
								'}'.
							'}'.
							'</script>';
					}
					else{
						$message = new \CAdminMessage(Loc::getMessage('CUSTOM_FILTER_PROP_ERROR_EMPTY_IBLOCK'));
						echo $message->Show();
					}
				}
			}
			else{
				$html = '<input type="text" id="'.$strHTMLControlName['VALUE'].'" name="'.$strHTMLControlName['VALUE'].'" value="'.htmlspecialcharsbx(is_array($val) ? reset($val) : $val).'" data-bx-property-id="'.$arProperty['CODE'].'" data-bx-comp-prop="true" />';
			}
		}

		return $html;
	}

	static function PrepareSettings($arFields){
		$arFields['USER_TYPE_SETTINGS']['IBLOCK_ID'] = isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['IBLOCK_ID']) ? intval($arFields['USER_TYPE_SETTINGS']['IBLOCK_ID']) : false;

		$arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID'] = isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID']) ? trim($arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID']) : false;

		$arFields['USER_TYPE_SETTINGS']['USE_OFFERS_IBLOCK'] = isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['USE_OFFERS_IBLOCK']) ? ($arFields['USER_TYPE_SETTINGS']['USE_OFFERS_IBLOCK'] === 'Y' ? 'Y' : 'N') : 'N';

		$arFields['FILTRABLE'] = $arFields['SMART_FILTER'] = $arFields['SEARCHABLE'] = $arFields['MULTIPLE'] = $arFields['WITH_DESCRIPTION'] = 'N';
		$arFields['MULTIPLE_CNT'] = 1;

        return $arFields;
	}

	static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields){
		$arPropertyFields = array(
            'HIDE' => array(
            	'SMART_FILTER',
            	'FILTRABLE',
            	'DEFAULT_VALUE',
            	'SEARCHABLE',
            	'MULTIPLE_CNT',
            	'COL_COUNT',
            	'MULTIPLE',
            	'WITH_DESCRIPTION',
            	'FILTER_HINT',
            ),
            'SET' => array(
            	'SMART_FILTER' => 'N',
            	'FILTRABLE' => 'N',
            	'SEARCHABLE' => 'N',
            	'MULTIPLE_CNT' => '1',
            	'MULTIPLE' => 'N',
            	'WITH_DESCRIPTION' => 'N',
            ),
        );

		$iblockId = $arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID'];
		$useOffersIblock = $arProperty['USER_TYPE_SETTINGS']['USE_OFFERS_IBLOCK'];
		$b_f = ($arProperty['PROPERTY_TYPE'] == 'G' || ($arProperty['PROPERTY_TYPE'] == 'E' && $arProperty['USER_TYPE'] == BT_UT_SKU_CODE) ? array('!ID' => $iblockId) : array());
		$html = '<tr><td width="40%">'.GetMessage('BT_ADM_IEP_PROP_LINK_IBLOCK').'</td>'.
			'<td>'.GetIBlockDropDownList($iblockId, $strHTMLControlName['NAME'].'[IBLOCK_TYPE_ID]', $strHTMLControlName['NAME'].'[IBLOCK_ID]', $b_f, 'class="adm-detail-iblock-types"', 'class="adm-detail-iblock-list"').'</td></tr>'.
			'<tr><td width="40%">'.GetMessage('BT_ADM_IEP_USE_OFFERS_IBLOCK').'</td>'.
			'<td width="60%"><input type="checkbox" '.($useOffersIblock === 'Y' ? 'checked' : '').' name="'.$strHTMLControlName['NAME'].'[USE_OFFERS_IBLOCK]" value="Y" /></td></tr>';

		return $html;
	}

	static function getSettingsIblockId($propertyId_or_Code, $propertyIblockId){
		static $arCache;

		if(!isset($arCache)){
			$arCache = array();
		}

		$iblockId = false;

		if(!isset($arCache[$propertyId_or_Code.'_'.$propertyIblockId])){
			Loader::includeModule('iblock');

			$dbRes = \CIBlockProperty::GetByID($propertyId_or_Code, $propertyIblockId);
			if($arProperty = $dbRes->Fetch()){
				$iblockId = $arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID'];
			}

			$arCache[$propertyId_or_Code.'_'.$propertyIblockId] = $iblockId;
		}
		else{
			$iblockId = $arCache[$propertyId_or_Code.'_'.$propertyIblockId];
		}

		return $iblockId;
	}
}
