<?
namespace Aspro\Next\Property\CustomFilter;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Config\Option,
	\Aspro\Next\CrossSales;

Loc::loadMessages(__FILE__);

class CondCtrl extends \CGlobalCondCtrlComplex{
	public static function GetClassName(){
		return __CLASS__;
	}

	public static function GetControlID(){
		return array('CondCrossIBProp');
	}

	public static function getLogicEvals($arLogics){
		$arAllLogics = array(
			'csEqual' => array(
				'ID' => 'csEqual',
				'VALUE' => 'csEqual',
				'LABEL' => Loc::getMessage('CUSTOM_FILTER_CONTROL_PROPERTY_LOGIC_EQUAL')
			),
			'csNotEqual' => array(
				'ID' => 'csNotEqual',
				'VALUE' => 'csNotEqual',
				'LABEL' => Loc::getMessage('CUSTOM_FILTER_CONTROL_PROPERTY_LOGIC_NOT_EQUAL')
			),
			'csIn' => array(
				'ID' => 'csIn',
				'VALUE' => 'csIn',
				'LABEL' => Loc::getMessage('CUSTOM_FILTER_CONTROL_PROPERTY_LOGIC_INTERSECT')
			),
			'csNotIn' => array(
				'ID' => 'csNotIn',
				'VALUE' => 'csNotIn',
				'LABEL' => Loc::getMessage('CUSTOM_FILTER_CONTROL_PROPERTY_LOGIC_NOT_INTERSECT')
			),
			'csSIn' => array(
				'ID' => 'csSIn',
				'VALUE' => 'csSIn',
				'LABEL' => Loc::getMessage('CUSTOM_FILTER_CONTROL_PROPERTY_LOGIC_STRICT_INTERSECT')
			)
		);

		foreach($arAllLogics as $code => $logic){
			if(!in_array($code, $arLogics)){
				unset($arAllLogics[$code]);
			}
		}

		return $arAllLogics;
	}

	public static function GetControlShow($arParams){
		$arResult = array();

		if(
			($propertyIblockId = self::_getPropertyIblockId()) &&
			($dataIblockId = self::_getDataIblockId())
		){
			$propertyCode = self::_getPropertyCode();
			if(CrossSales::isCrossSalesIblock($propertyIblockId)){
				if(self::_isCrossSalesExtProductsFilterProperty($propertyCode)){
					$arResult[] = self::_addCrossSalesIblockPropsControls($dataIblockId, $arParams);
				}
			}
		}

		return $arResult;
	}

	public static function GetControls($strControlID = false){
		$arControlList = array();

		if(
			($propertyIblockId = self::_getPropertyIblockId()) &&
			($dataIblockId = self::_getDataIblockId())
		){
			$propertyCode = self::_getPropertyCode();
			if(CrossSales::isCrossSalesIblock($propertyIblockId)){
				if(self::_isCrossSalesExtProductsFilterProperty($propertyCode)){
					$arControlList = array_merge(
						$arControlList,
						self::_getCrossSalesIblockPropsControls($dataIblockId, $strControlID)
					);
				}
			}
		}

		return $arControlList;
    }

    protected static function _getPropertyCode(){
    	return
    		isset($_REQUEST['property']) && is_array($_REQUEST['property']) ?
    			$_REQUEST['property']['id'] :
    			false;
    }

    protected static function _isCrossSalesExtProductsFilterProperty($propertyCode){
		return $propertyCode === CrossSales::PROPERTY_EXT_PRODUCTS_FILTER_CODE;
	}

    protected static function _getPropertyIblockId(){
    	return
    		isset($_REQUEST['property']) && is_array($_REQUEST['property']) ?
    			(
    				($iblockId = intval($_REQUEST['property']['iblockId'])) > 0 ?
    					$iblockId :
    					false
    			) :
    			false;
    }

    protected static function _getDataIblockId(){
    	return
    		isset($_REQUEST['data']) && is_array($_REQUEST['data']) ?
    			(
    				($iblockId = intval($_REQUEST['data']['iblockId'])) > 0 ?
    					$iblockId :
    					false
    			) :
    			false;
    }

    protected static function _addCrossSalesIblockPropsControls($iblockId, $arParams){
		$arResult = array(
			'controlgroup' => true,
			'group' =>  false,
			'label' => Loc::getMessage('CUSTOM_FILTER_CONTROL_CROSSALES_PROPERTY_GROUP'),
			'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
			'children' => array()
		);

    	$arControls = self::_getCrossSalesIblockPropsControls($iblockId);
		foreach($arControls as &$arOneControl){
			$arResult['children'][] = array(
				'controlId' => $arOneControl['ID'],
				'group' => false,
				'label' => $arOneControl['LABEL'],
				'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
				'control' => array(
					array(
						'id' => 'prefix',
						'type' => 'prefix',
						'text' => $arOneControl['PREFIX']
					),
					static::GetLogicAtom($arOneControl['LOGIC']),
					static::GetValueAtom($arOneControl['JS_VALUE'])
				)
			);
		}
		unset($arOneControl);

		return $arResult;
    }

    protected static function _getCrossSalesIblockPropsControls($iblockId, $strControlID = false){
    	$arControlList = array();
		$arTypes = array('E', 'G', 'L', 'N', 'S');
		$arLogicsByType = array(
			'NS' => array('csEqual', 'csNotEqual'),
			'NM' => array('csIn', 'csSIn', 'csNotIn'),
			'SS' => array('csEqual', 'csNotEqual'),
			'SM' => array('csIn', 'csSIn', 'csNotIn'),
			'LS' => array('csEqual', 'csNotEqual'),
			'LM' => array('csIn', 'csSIn', 'csNotIn'),
			'ES' => array('csEqual', 'csNotEqual'),
			'EM' => array('csIn', 'csSIn', 'csNotIn'),
			'GS' => array('csEqual', 'csNotEqual'),
			'GM' => array('csIn', 'csSIn', 'csNotIn'),
		);
		$arExcludeUserTypes = self::getCrossSalesExcludePropertyUserTypes();
		$dbRes = \CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'NAME' => 'ASC'),
			array('IBLOCK_ID' => $iblockId)
		);
		while($arProperty = $dbRes->Fetch()){
			if(
				!in_array($arProperty['PROPERTY_TYPE'], $arTypes) ||
				in_array($arProperty['USER_TYPE'], $arExcludeUserTypes)
			){
				continue;
			}

			$type = $arProperty['PROPERTY_TYPE'].($arProperty['MULTIPLE'] === 'Y' ? 'M' : 'S');
			$fieldId = 'CondCrossIBProp:'.$arProperty['IBLOCK_ID'].':'.$arProperty['ID'];

			$arControlList[$fieldId] = array(
				'ID' => $fieldId,
				'FIELD' => str_replace(':', '_', $fieldId),
				'FIELD_TYPE' => 'text',
				'LABEL' => $arProperty['NAME'],
				'CLASS_ID' => 'CondCrossIBProp',
				'PREFIX' => Loc::getMessage('CUSTOM_FILTER_CONTROL_PROPERTY_PREFIX').' '.$arProperty['NAME'],
				//'LOGIC' => static::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'LOGIC' => static::getLogicEvals($arLogicsByType[$type]),
				'MULTIPLE' => 'N',
				'PARENT' => true,
				'JS_VALUE' => array(
					'type' => 'lazySelect',
					'load_url' => '/bitrix/tools/aspro.next/customfilter_ajax.php',
					'load_params' => array(
						'action' => 'get_crosssales_iblockprops',
						'propertyId' => $arProperty['ID'],
						'iblockId' => $iblockId,
						'lang' => LANGUAGE_ID,
					),
				),
				'PHP_VALUE' => '',
			);
		}

        if($strControlID === false){
			return $arControlList;
		}
		elseif(isset($arControlList[$strControlID])){
			return $arControlList[$strControlID];
		}
		else{
			return false;
		}
    }

    public static function getCrossSalesExcludePropertyUserTypes(){
    	return array(
			'SAsproCustomFilter',
			'SAsproIBInherited',
			'SAsproService',
			'SAsproYaDirectQuery',
		);
    }
}
