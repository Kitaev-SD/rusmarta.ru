<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

$arListAspectRatio = array(
	'DEFAULT' => Loc::getMessage('SLIDER_ASPECT_RATIO_DEFAULT'),
	'16_7' => Loc::getMessage('SLIDER_ASPECT_RATIO_16_7'),
	'16_9' => Loc::getMessage('SLIDER_ASPECT_RATIO_16_9')
);

$arTemplateParameters['SLIDER_AUTOPLAY'] = array(
	'PARENT' => 'ADDITIONAL_SETTINGS',
	'NAME' => Loc::getMessage('SLIDER_AUTOPLAY'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'REFRESH' => 'Y'
);

if(!isset($arCurrentValues['SLIDER_AUTOPLAY']) || $arCurrentValues['SLIDER_AUTOPLAY'] == 'Y') {
	$arTemplateParameters['SLIDER_DELAY'] = array(
		'PARENT' => 'ADDITIONAL_SETTINGS',
		'NAME' => Loc::getMessage('SLIDER_DELAY'),
		'TYPE' => 'STRING',
		'DEFAULT' => '3000'
	);
}

$arTemplateParameters['SLIDER_ASPECT_RATIO'] = array(
	'PARENT' => 'ADDITIONAL_SETTINGS',
	'NAME' => Loc::getMessage('SLIDER_ASPECT_RATIO'),
	'TYPE' => 'LIST',
	'VALUES' => $arListAspectRatio,
	'DEFAULT' => 'DEFAULT'
);



if(Loader::includeModule('iblock')){
	$arSort = CIBlockParameters::GetElementSortFields(
		array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
		array('KEY_LOWERCASE' => 'Y')
	);
	$arPrice = array();
	
	if(Loader::includeModule('catalog')) {
		$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
		$rsPrice = CCatalogGroup::GetList($v1='sort', $v2='asc');
		
		while($arr=$rsPrice->Fetch()) {
			$arPrice[$arr['NAME']] = "[{$arr['NAME']}] {$arr['NAME_LANG']}";
		}
	}
}

$arTemplateParameters['PRICE_CODE'] = array(
	'PARENT' => 'PRICES',
	'NAME' => Loc::getMessage('IBLOCK_PRICE_CODE'),
	'TYPE' => 'LIST',
	'MULTIPLE' => 'Y',
	'VALUES' => $arPrice,
);

$arTemplateParameters['PRICE_VAT_INCLUDE'] = array(
	'PARENT' => 'PRICES',
	'NAME' => Loc::getMessage('IBLOCK_VAT_INCLUDE'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

if(CModule::IncludeModule('currency')) {
	$arTemplateParameters['CONVERT_CURRENCY'] = array(
		'PARENT' => 'PRICES',
		'NAME' => Loc::getMessage('CONVERT_CURRENCY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);

	if(isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY']) {
		$arCurrencyList = array();
		$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
		
		while($arCurrency = $rsCurrencies->Fetch()) {
			$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
		}
		
		$arTemplateParameters['CURRENCY_ID'] = array(
			'PARENT' => 'PRICES',
			'NAME' => Loc::getMessage('CURRENCY_ID'),
			'TYPE' => 'LIST',
			'VALUES' => $arCurrencyList,
			'DEFAULT' => CCurrency::GetBaseCurrency(),
			'ADDITIONAL_VALUES' => 'Y',
		);
	}
}