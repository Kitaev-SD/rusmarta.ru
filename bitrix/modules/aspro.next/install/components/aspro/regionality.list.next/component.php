<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $arTheme, $APPLICATION;
?>
<?if(\Bitrix\Main\Loader::includeModule('aspro.next'))
{
	$arParams['POPUP'] = (isset($arParams['POPUP']) ? $arParams['POPUP'] : 'N');
	$arResult['POPUP'] = ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') || $arParams['POPUP'] == 'Y');

	if(!$arResult['POPUP']  || ($arResult['POPUP'] && \Bitrix\Main\Config\Option::get('aspro.next', 'REGIONALITY_SEARCH_ROW', 'N') != 'Y'))
	{
		$arResult['REGIONS'] = CNextRegionality::getRegions();
		$arResult['CURRENT_REGION'] = CNextRegionality::getCurrentRegion();
		$arResult['REAL_REGION'] = CNextRegionality::getRealRegionByIP();
		$arResult['REGION_SELECTED'] = isset($_COOKIE['current_region']) && $_COOKIE['current_region'];
		$arResult['REGION_GEOIP_ERROR'] = !isset($_SESSION['GEOIP']) || isset($_SESSION['GEOIP']['message']);
		$arResult['SHOW_REGION_CONFIRM'] = !$arResult['REGION_GEOIP_ERROR'] && $arResult['REAL_REGION'] && $arResult['REAL_REGION']['ID'] != $arResult['CURRENT_REGION']['ID'] && !($arResult['REGION_SELECTED'] && $arResult['CURRENT_REGION']['ID'] == $_COOKIE['current_region']);
	}

	if($arResult['REGIONS'])
	{
		$arResult['HOST'] = (CMain::IsHTTPS() ? 'https://' : 'http://');
		$arResult['URI'] = $APPLICATION->GetCurUri();
		if(isset($arParams['URL']) && $arParams['URL'] != $_SERVER['REQUEST_URI'])
			$arResult['URI'] = $arParams['URL'];

		if($arResult['POPUP'])
			$type_regions = $arTheme['REGIONALITY_TYPE'];
		else
			$type_regions = $arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['REGIONALITY_TYPE']['VALUE'];

		$arSectionsID = array();
		foreach($arResult['REGIONS'] as $id => $arRegionItem)
		{
			$arResult['REGIONS'][$id]['URL'] = $arResult['URI'];
			if($arRegionItem['PROPERTY_MAIN_DOMAIN_VALUE'] && $type_regions == 'SUBDOMAIN')
				$arResult['REGIONS'][$id]['URL'] = $arResult['HOST'].$arRegionItem['PROPERTY_MAIN_DOMAIN_VALUE'].$arResult['URI'];
			if($arRegionItem['IBLOCK_SECTION_ID'])
				$arSectionsID[$arRegionItem['IBLOCK_SECTION_ID']] = $arRegionItem['IBLOCK_SECTION_ID'];
		}

		if($arResult['POPUP'])
		{
			$arResult['FAVORITS'] = array();
			foreach($arResult['REGIONS'] as $arItem)
			{
				if($arItem['PROPERTY_FAVORIT_LOCATION_VALUE'] == 'Y')
					$arResult['FAVORITS'][] = $arItem;
			}
			$arResult['SECTION_LEVEL1'] = $arResult['SECTION_LEVEL2'] = array();
			$arSectionsName = array();
			if($arSectionsID)
			{
				$arSections = CNextCache::CIBlockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('GROUP' => 'ID', 'TAG' => CNextCache::GetIBlockCacheTag(CNextCache::$arIBlocks[SITE_ID]['aspro_next_regionality']['aspro_next_regions'][0]))), array('GLOBAL_ACTIVE' => 'Y', 'ID' => $arSectionsID, 'IBLOCK_ID' => CNextCache::$arIBlocks[SITE_ID]['aspro_next_regionality']['aspro_next_regions'][0]), false, array('ID', 'IBLOCK_ID', 'NAME', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL'));
				if($arSections)
				{
					$arSections2 = array();
					foreach($arSections as $key => $arSection)
					{
						$arSectionsName[$key] = $arSection['NAME'];
						if($arSection['DEPTH_LEVEL'] > 1)
						{
							unset($arSections[$key]);
							$arTmpSection = CNextCache::CIBlockSection_GetList(array('CACHE' => array('MULTI' => 'N', 'TAG' => CNextCache::GetIBlockCacheTag(CNextCache::$arIBlocks[SITE_ID]['aspro_next_regionality']['aspro_next_regions'][0]))), array('GLOBAL_ACTIVE' => 'Y', '<=LEFT_BORDER' => $arSection['LEFT_MARGIN'], '>=RIGHT_BORDER' => $arSection['RIGHT_MARGIN'], 'DEPTH_LEVEL' => 1, 'IBLOCK_ID' => CNextCache::$arIBlocks[SITE_ID]['aspro_next_regionality']['aspro_next_regions'][0]), false, array('ID', 'IBLOCK_ID', 'NAME', 'SORT'));
							$arSections[$arTmpSection['ID']] = $arTmpSection;
							$arSections2[$arTmpSection['ID']][$arSection['ID']] = $arSection;
							$arSectionsName[$key] .= ' / '.$arTmpSection['NAME'];
						}
					}
					\Bitrix\Main\Type\Collection::sortByColumn($arSections, array('SORT' => array(SORT_NUMERIC, SORT_ASC), 'NAME' => SORT_ASC), '', null, true);
					$arResult['SECTION_LEVEL1'] = $arSections;
					$arResult['SECTION_LEVEL2'] = $arSections2;
				}
			}
			foreach($arResult['REGIONS'] as $id => $arRegionItem)
			{
				$arResult['JS_REGIONS'][] = array(
					'label' => $arRegionItem['NAME'],
					'HREF' => $arRegionItem['URL'],
					'REGION' => (($arSectionsName && ($arRegionItem['IBLOCK_SECTION_ID'] && isset($arSectionsName[$arRegionItem['IBLOCK_SECTION_ID']]))) ? $arSectionsName[$arRegionItem['IBLOCK_SECTION_ID']] : ''),
					'ID' => $arRegionItem['ID'],
				);
			}
		}
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery-ui.min.js');
		$this->IncludeComponentTemplate();
	}
	elseif($arResult['POPUP'] && \Bitrix\Main\Config\Option::get('aspro.next', 'REGIONALITY_SEARCH_ROW', 'N') == 'Y')
	{
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery-ui.min.js');
		$this->IncludeComponentTemplate();
	}
	else
		return;
}
else
	return;
?>