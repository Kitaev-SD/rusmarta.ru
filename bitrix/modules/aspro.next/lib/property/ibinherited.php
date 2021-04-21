<?
namespace Aspro\Next\Property;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class IBInherited{
	static function OnIBlockPropertyBuildList(){
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'SAsproIBInherited',
			'DESCRIPTION' => Loc::getMessage('IBINHERITED_PROP_TITLE'),
			'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
			'PrepareSettings' => array(__CLASS__, 'PrepareSettings'),
		);
	}

	static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName){
        return '';
	}

	static function ConvertFromDB($arProperty, $value){
		if(!strlen($value['VALUE'])){
			$value['VALUE'] = $arProperty['DEFAULT_VALUE'];
		}

		return $value;
	}

	static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){
		$bEditProperty = $strHTMLControlName['MODE'] === 'EDIT_FORM';
		$bDetailPage = $strHTMLControlName['MODE'] === 'FORM_FILL';

		if($bEditProperty || $bDetailPage){
			$iblockId = isset($arProperty['USER_TYPE_SETTINGS']) && isset($arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID']) ? $arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID'] : 0;
			if($iblockId){
				Loader::includeModule('iblock');
				require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/prolog.php');

				$entityType = isset($arProperty['USER_TYPE_SETTINGS']) && isset($arProperty['USER_TYPE_SETTINGS']['ENTITY_TYPE']) ? $arProperty['USER_TYPE_SETTINGS']['ENTITY_TYPE'] : 'E';

				if($bDetailPage){
					$str_IPROPERTY_TEMPLATES = array(
						$arProperty['CODE'] => array(
							'TEMPLATE' => $value && strlen($value['VALUE']) ? $value['VALUE'] : $arProperty['DEFAULT_VALUE'],
							'INHERITED' => $value && strlen($value['VALUE']) ? 'N' : 'Y',
						)
					);
				}
				else{
					$str_IPROPERTY_TEMPLATES = array(
						$arProperty['CODE'] => array(
							'TEMPLATE' => $value && strlen($value['VALUE']) ? $value['VALUE'] : '',
							'INHERITED' => 'N',
						)
					);
				}
				?>
				<?=self::IBlockInheritedPropertyInput($iblockId, $arProperty['CODE'], $str_IPROPERTY_TEMPLATES, $entityType, ($bDetailPage ? Loc::getMessage('IBEL_E_SEO_OVERWRITE') : ''), $strHTMLControlName['VALUE'], $strHTMLControlName['VALUE'])?>
				<?
			}
			else{
				echo Loc::getMessage('IBINHERITED_PROP_ERROR_EMPTY_IBLOCK');
			}
		}
	}

	static function PrepareSettings($arFields){
		$arFields['USER_TYPE_SETTINGS']['ENTITY_TYPE'] = isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['ENTITY_TYPE']) ? intval($arFields['USER_TYPE_SETTINGS']['ENTITY_TYPE']) : 'E';

		$arFields['USER_TYPE_SETTINGS']['IBLOCK_ID'] = isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['IBLOCK_ID']) ? intval($arFields['USER_TYPE_SETTINGS']['IBLOCK_ID']) : false;

		$arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID'] = isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID']) ? trim($arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID']) : false;

		$arFields['FILTRABLE'] = $arFields['SMART_FILTER'] = $arFields['SEARCHABLE'] = $arFields['MULTIPLE'] = $arFields['WITH_DESCRIPTION'] = 'N';
		$arFields['MULTIPLE_CNT'] = 1;

        return $arFields;
	}

	static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields){
		$arPropertyFields = array(
            'HIDE' => array(
            	'SMART_FILTER',
            	'FILTRABLE',
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
            	'DEFAULT_VALUE' => '',
            ),
        );

		$entityType = $arProperty['USER_TYPE_SETTINGS']['ENTITY_TYPE'];
		$html = '<tr><td width="40%">'.Loc::getMessage('IBINHERITED_PROP_ENTITY_TYPE_TITLE').'</td>'.
			'<td><select name="'.$strHTMLControlName['NAME'].'[ENTITY_TYPE]"><option value="E" '.($entityType === 'E' ? 'selected' : '').'>'.Loc::getMessage('IBINHERITED_PROP_ENTITY_TYPE_ELEMENT').'</option><option value="S" '.($entityType === 'S' ? 'selected' : '').'>'.Loc::getMessage('IBINHERITED_PROP_ENTITY_TYPE_SECTION').'</option></select></td></tr>';

		$iblockId = $arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID'];
		$b_f = ($arProperty['PROPERTY_TYPE'] == 'G' || ($arProperty['PROPERTY_TYPE'] == 'E' && $arProperty['USER_TYPE'] == BT_UT_SKU_CODE) ? array('!ID' => $iblockId) : array());
		$html .= '<tr><td width="40%">'.Loc::getMessage('BT_ADM_IEP_PROP_LINK_IBLOCK').'</td>'.
			'<td>'.GetIBlockDropDownList($iblockId, $strHTMLControlName['NAME'].'[IBLOCK_TYPE_ID]', $strHTMLControlName['NAME'].'[IBLOCK_ID]', $b_f, 'class="adm-detail-iblock-types"', 'class="adm-detail-iblock-list"').'</td></tr>';

		return $html;
	}

	static protected function IBlockInheritedPropertyInput($iblock_id, $code, $data, $type, $checkboxLabel = '', $id = '', $name = '')
	{
	    $inherited = ($data[$code]["INHERITED"] !== "N") && ($checkboxLabel !== "");
	    $inputId = $id ? $id : "IPROPERTY_TEMPLATES_".$code;
	    $inputName = $name ? $name : "IPROPERTY_TEMPLATES[".$code."][TEMPLATE]";
	    $menuId = "mnu_".$inputId;
	    $resultId = "result_".$inputId;
	    $checkboxId = "ck_".$inputId;

	    if ($type === "S"){
	        $menuItems = \CIBlockParameters::GetInheritedPropertyTemplateSectionMenuItems($iblock_id, "InheritedPropertiesTemplates.insertIntoInheritedPropertiesTemplate", $menuId, $inputId);
	    }
	    else{
	        $menuItems = \CIBlockParameters::GetInheritedPropertyTemplateElementMenuItems($iblock_id, "InheritedPropertiesTemplates.insertIntoInheritedPropertiesTemplate", $menuId, $inputId);
	    }

	    $menuItems[count($menuItems) - 1]['MENU'][] = array(
	    	'TEXT' => Loc::getMessage('IBINHERITED_PROP_MENU_ITEM_IPV_TITLE'),
	    	'ONCLICK' => 'InheritedPropertiesTemplates.insertIntoInheritedPropertiesTemplate(\'{=this.inheritedproperty}\', \''.$menuId.'\', \''.$inputName.'\')',
	    );

	    $u = new \CAdminPopupEx($menuId, $menuItems, array("zIndex" => 2000));
	    $result = $u->Show(true)
	        .'<script>
	            window.ipropTemplates[window.ipropTemplates.length] = {
	            "ID": "'.$code.'",
	            "INPUT_ID": "'.$inputId.'",
	            "RESULT_ID": "'.$resultId.'",
	            "TEMPLATE": ""
	            };
	        </script>'
	        .'<input type="hidden" name="'.$inputName.'" value="'.htmlspecialcharsbx($data[$code]["TEMPLATE"]).'" />'
	        .'<textarea onclick="InheritedPropertiesTemplates.enableTextArea(\''.$inputId.'\')" name="'.$inputName.'" id="'.$inputId.'" '.($inherited? 'readonly="readonly"': '').' cols="55" rows="1" style="width:90%">'
	        .htmlspecialcharsbx($data[$code]["TEMPLATE"])
	        .'</textarea>'
	        .'<input style="float:right" type="button" id="'.$menuId.'" '.($inherited? 'disabled="disabled"': '').' value="...">'
	        .'<br>'
	    ;

	    if ($checkboxLabel != "")
	    {
	        $result .= '<div style="display:none;"><input type="hidden" name="'.$checkboxId.'[INHERITED]" value="Y">'
	            .'<input type="checkbox" name="'.$checkboxId.'[INHERITED]" id="'.$checkboxId.'" value="N" '
	            .'onclick="InheritedPropertiesTemplates.updateInheritedPropertiesTemplates()" '.(!$inherited? 'checked="checked"': '').'>'
	            .'<label for="'.$checkboxId.'">'.$checkboxLabel.'</label><br></div>'
	        ;
	    }

	    if (preg_match("/_FILE_NAME\$/", $code))
	    {
	        $result .= '<input type="hidden" name="IPROPERTY_TEMPLATES['.$code.'][LOWER]" value="N">'
	            .'<input type="checkbox" name="IPROPERTY_TEMPLATES['.$code.'][LOWER]" id="lower_'.$code.'" value="Y" '
	            .'onclick="InheritedPropertiesTemplates.enableTextArea(\''.$inputId.'\');InheritedPropertiesTemplates.updateInheritedPropertiesValues(false, true)" '.($data[$code]["LOWER"] !== "Y"? '': 'checked="checked"').'>'
	            .'<label for="lower_'.$code.'">'.Loc::getMessage("IBLOCK_AT_FILE_NAME_LOWER").'</label><br>'
	        ;
	        $result .= '<input type="hidden" name="IPROPERTY_TEMPLATES['.$code.'][TRANSLIT]" value="N">'
	            .'<input type="checkbox" name="IPROPERTY_TEMPLATES['.$code.'][TRANSLIT]" id="translit_'.$code.'" value="Y" '
	            .'onclick="InheritedPropertiesTemplates.enableTextArea(\''.$inputId.'\');InheritedPropertiesTemplates.updateInheritedPropertiesValues(false, true)" '.($data[$code]["TRANSLIT"] !== "Y"? '': 'checked="checked"').'>'
	            .'<label for="translit_'.$code.'">'.Loc::getMessage("IBLOCK_AT_FILE_NAME_TRANSLIT").'</label><br>'
	        ;
	        $result .= '<input size="2" maxlength="1" type="text" name="IPROPERTY_TEMPLATES['.$code.'][SPACE]" id="space_'.$code.'" value="'.htmlspecialcharsbx($data[$code]["SPACE"]).'" '
	            .'onchange="InheritedPropertiesTemplates.updateInheritedPropertiesValues(false, true)">'.Loc::getMessage("IBLOCK_AT_FILE_NAME_SPACE").'<br>'
	        ;
	    }
	    $result .= '<b><div id="'.$resultId.'"></div></b>';

	    return $result;
	}

	static function modifyItemTemplates($arParams, &$arItem){
		if($arParams && $arParams['IBINHERIT_TEMPLATES'] && $arItem){
			$arIBInheritTemplates = $arParams['IBINHERIT_TEMPLATES'];

			if(
				strlen($arIBInheritTemplates['ELEMENT_PAGE_TITLE']) ||
				strlen($arIBInheritTemplates['ELEMENT_PREVIEW_PICTURE_FILE_ALT']) ||
				strlen($arIBInheritTemplates['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'])
			){
				$ipropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates($arParams['IBLOCK_ID'], $arItem['ID']);
				$arFields = array(
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'IBLOCK_SECTION_ID' => $arItem['IBLOCK_SECTION_ID'],
					'NAME' => $arItem['NAME'],
					'CODE' => $arItem['CODE'],
					'PREVIEW_TEXT' => $arItem['PREVIEW_TEXT'],
					'DETAIL_TEXT' => $arItem['DETAIL_TEXT'],
				);

				if($ipropTemplates){
					$values = $ipropTemplates->getValuesEntity();
					$entity = $values->createTemplateEntity();
					$entity->setFields($arFields);
					$templates = $ipropTemplates->findTemplates();

					if(!$arItem['IPROPERTY_VALUES']){
						$arItem['IPROPERTY_VALUES'] = array();
					}

					if(strlen($arIBInheritTemplates['ELEMENT_PAGE_TITLE'])){
						$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);

						$temp = str_replace('{=this.inheritedproperty}', $elementName, $arIBInheritTemplates['ELEMENT_PAGE_TITLE']);

						$template = \Bitrix\Iblock\Template\Helper::convertArrayToModifiers(
							array(
								'TEMPLATE' => $temp,
								'INHERITED' => 'N',
							)
						);

						$arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] = htmlspecialchars_decode(\Bitrix\Main\Text\HtmlFilter::encode(\Bitrix\Iblock\Template\Engine::process($entity, $template)));
					}

					if(strlen($arIBInheritTemplates['ELEMENT_PREVIEW_PICTURE_FILE_ALT'])){
						$a_alt = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'] : $arItem['NAME']);

						$temp = str_replace('{=this.inheritedproperty}', $a_alt, $arIBInheritTemplates['ELEMENT_PREVIEW_PICTURE_FILE_ALT']);

						$template = \Bitrix\Iblock\Template\Helper::convertArrayToModifiers(
							array(
								'TEMPLATE' => $temp,
								'INHERITED' => 'N',
							)
						);

						$arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'] = htmlspecialchars_decode(\Bitrix\Main\Text\HtmlFilter::encode(\Bitrix\Iblock\Template\Engine::process($entity, $template)));
					}

					if(strlen($arIBInheritTemplates['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'])){
						$a_title = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] : $arItem['NAME']);

						$temp = str_replace('{=this.inheritedproperty}', $a_title, $arIBInheritTemplates['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']);

						$template = \Bitrix\Iblock\Template\Helper::convertArrayToModifiers(
							array(
								'TEMPLATE' => $temp,
								'INHERITED' => 'N',
							)
						);

						$arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] = htmlspecialchars_decode(\Bitrix\Main\Text\HtmlFilter::encode(\Bitrix\Iblock\Template\Engine::process($entity, $template)));
					}
				}
			}

			if(
				$arItem['OFFERS'] &&
				(
					strlen($arIBInheritTemplates['SKU_PAGE_TITLE']) ||
					strlen($arIBInheritTemplates['SKU_PREVIEW_PICTURE_FILE_ALT']) ||
					strlen($arIBInheritTemplates['SKU_PREVIEW_PICTURE_FILE_TITLE'])
				)
			){
				foreach($arItem['OFFERS'] as $keyOffer => &$arOffer){
					if(!isset($arItem['OFFERS_SELECTED']) || ($arItem['OFFERS_SELECTED'] == $keyOffer)){
						$ipropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates($arOffer['IBLOCK_ID'], $arOffer['ID']);
						$arFields = array(
							'IBLOCK_ID' => $arOffer['IBLOCK_ID'],
							'IBLOCK_SECTION_ID' => $arOffer['IBLOCK_SECTION_ID'],
							'NAME' => $arOffer['NAME'],
							'CODE' => $arOffer['CODE'],
							'PREVIEW_TEXT' => $arOffer['PREVIEW_TEXT'],
							'DETAIL_TEXT' => $arOffer['DETAIL_TEXT'],
						);

						if($ipropTemplates){
							$values = $ipropTemplates->getValuesEntity();
							$entity = $values->createTemplateEntity();
							$entity->setFields($arFields);
							$templates = $ipropTemplates->findTemplates();

							if(!$arOffer['IPROPERTY_VALUES']){
								$arOffer['IPROPERTY_VALUES'] = array();
							}

							if(strlen($arIBInheritTemplates['SKU_PAGE_TITLE'])){
								$elementName = ((isset($arOffer['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arOffer['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arOffer['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arOffer['NAME']);

								$temp = str_replace('{=this.inheritedproperty}', $elementName, $arIBInheritTemplates['SKU_PAGE_TITLE']);

								$template = \Bitrix\Iblock\Template\Helper::convertArrayToModifiers(
									array(
										'TEMPLATE' => $temp,
										'INHERITED' => 'N',
									)
								);

								$arOffer['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] = htmlspecialchars_decode(\Bitrix\Main\Text\HtmlFilter::encode(\Bitrix\Iblock\Template\Engine::process($entity, $template)));
							}

							if(strlen($arIBInheritTemplates['SKU_PREVIEW_PICTURE_FILE_ALT'])){
								$a_alt = ((isset($arOffer['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']) && $arOffer['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']) ? $arOffer['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'] : $arOffer['NAME']);

								$temp = str_replace('{=this.inheritedproperty}', $a_alt, $arIBInheritTemplates['SKU_PREVIEW_PICTURE_FILE_ALT']);

								$template = \Bitrix\Iblock\Template\Helper::convertArrayToModifiers(
									array(
										'TEMPLATE' => $temp,
										'INHERITED' => 'N',
									)
								);

								$arOffer['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'] = htmlspecialchars_decode(\Bitrix\Main\Text\HtmlFilter::encode(\Bitrix\Iblock\Template\Engine::process($entity, $template)));
							}

							if(strlen($arIBInheritTemplates['SKU_PREVIEW_PICTURE_FILE_TITLE'])){
								$a_title = ((isset($arOffer['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $arOffer['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) ? $arOffer['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] : $arOffer['NAME']);

								$temp = str_replace('{=this.inheritedproperty}', $a_title, $arIBInheritTemplates['SKU_PREVIEW_PICTURE_FILE_TITLE']);

								$template = \Bitrix\Iblock\Template\Helper::convertArrayToModifiers(
									array(
										'TEMPLATE' => $temp,
										'INHERITED' => 'N',
									)
								);

								$arOffer['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] = htmlspecialchars_decode(\Bitrix\Main\Text\HtmlFilter::encode(\Bitrix\Iblock\Template\Engine::process($entity, $template)));
							}
						}
					}
				}
			}
		}
	}
}
