<?
use Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

$arPersonType = $arPaySystem = $arDelivery = $arDeliveryParents = array();

if(CModule::IncludeModule('sale')){
	$arPersonType = array('' => '-');
	$res = \Bitrix\Sale\PersonType::getList(
		array(
			'order' => array(
				'ID' => 'ASC',
			),
			'filter' => array(
				'ACTIVE' => 'Y',
			),
			'select' => array(
				'ID',
				'NAME',
			),
		)
	);
	while($arItem = $res->Fetch()){
		$arPersonType[$arItem['ID']] = $arItem['NAME'].' ['.$arItem['ID'].']';
	}

	$arPaySystem = array('' => '-');
	if($arCurrentValues['DELIVERY_WITHOUT_PAY_SYSTEM'] !== 'N'){
		$arPaySystem['0'] = Loc::getMessage('CD_P_ANY');
	}
	$arFilter = array(
		'ACTIVE' => 'Y',
	);
	if($arCurrentValues['PAY_FROM_ACCOUNT'] !== 'Y'){
		$arFilter['!ID'] = \Bitrix\Sale\PaySystem\Manager::getInnerPaySystemId();
	}
	$res = \Bitrix\Sale\Internals\PaySystemActionTable::getList(
		array(
			'order' => array(
				'ID' => 'ASC',
			),
			'filter' => $arFilter,
			'select' => array(
				'ID',
				'NAME',
			),
		)
	);
	while($arItem = $res->Fetch()){
		$arPaySystem[$arItem['ID']] = $arItem['NAME'].' ['.$arItem['ID'].']';
	}

	$arDelivery = $arDeliveryParents = array();
	$arFilter = array(
		'ACTIVE' => 'Y',
	);
	if($delivery = \Bitrix\Sale\Delivery\Services\Manager::getById(\Bitrix\Sale\Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId())){
		$arFilter['!ID'] = $delivery['ID'];
	}
	$res = \Bitrix\Sale\Delivery\Services\Table::getList(
		array(
			'order' => array(
				'ID' => 'ASC',
			),
			'filter' => $arFilter,
			'select' => array(
				'ID',
				'NAME',
				'PARENT_ID',
			),
		)
	);
	while($arItem = $res->Fetch()){
		$arDelivery[$arItem['ID']] = $arItem['NAME'].' ['.$arItem['ID'].']';

		if(!$arItem['PARENT_ID']){
			$arDeliveryParents[$arItem['ID']] = $arDelivery[$arItem['ID']];
		}
	}
}

$arComponentParameters = array(
	'GROUPS' => array(
		'VISUAL_DETAIL' => array(
			'NAME' => GetMessage('CD_G_VISUAL_DETAIL_TITLE'),
			'SORT' => '500',
		),
		'VISUAL_PREVIEW' => array(
			'NAME' => GetMessage('CD_G_VISUAL_PREVIEW_TITLE'),
			'SORT' => '600',
		),
		'DEFAULT' => array(
			'NAME' => GetMessage('CD_G_DEFAULT_TITLE'),
			'SORT' => '700',
		),
		'MESSAGES' => array(
			'NAME' => GetMessage('CD_G_MESSAGES_TITLE'),
			'SORT' => '800',
		),
	),
	'PARAMETERS' => array(
		'SET_PAGE_TITLE' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CD_P_SET_PAGE_TITLE_TITLE'),
			'TYPE' => 'CHECKBOX',
			'ADDITIONAL_VALUES' => 'N',
			'DEFAULT' => 'Y',
			'REFRESH' => 'N',
		),
		'DELIVERY_NO_SESSION' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CD_P_DELIVERY_NO_SESSION_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'DELIVERY_WITHOUT_PAY_SYSTEM' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CD_P_PAY_DELIVERY_WITHOUT_PAY_SYSTEM_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
		),
		'PAY_FROM_ACCOUNT' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CD_P_PAY_FROM_ACCOUNT_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		),
		'SPOT_LOCATION_BY_GEOIP' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CD_P_SPOT_LOCATION_BY_GEOIP_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'USE_LAST_ORDER_DATA' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CD_P_USE_LAST_ORDER_DATA_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'USE_PROFILE_LOCATION' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CD_P_USE_PROFILE_LOCATION_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		),
		'SAVE_IN_SESSION' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CD_P_SAVE_IN_SESSION_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'SHOW_LOCATION_SOURCE' => array(
			'PARENT' => 'VISUAL_DETAIL',
			'NAME' => GetMessage('CD_P_SHOW_LOCATION_SOURCE_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		'CHANGEABLE_FIELDS' => array(
			'PARENT' => 'VISUAL_DETAIL',
			'NAME' => GetMessage('CD_P_CHANGEABLE_FIELDS_TITLE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => array(
				'LOCATION' => GetMessage('CD_P_CHANGEABLE_FIELDS_LOCATION'),
				'QUANTITY' => GetMessage('CD_P_CHANGEABLE_FIELDS_QUANTITY'),
				'PERSON_TYPE' => GetMessage('CD_P_CHANGEABLE_FIELDS_PERSON_TYPE'),
				'PAY_SYSTEM' => GetMessage('CD_P_CHANGEABLE_FIELDS_PAY_SYSTEM'),
				'ADD_BASKET' => GetMessage('CD_P_CHANGEABLE_FIELDS_ADD_BASKET'),
			),
			'DEFAULT' => array(
				0 => 'LOCATION',
				1 => 'QUANTITY',
				2 => 'ADD_BASKET',
			),
		),
		'SHOW_DELIVERY_PARENT_NAMES' => array(
			'PARENT' => 'VISUAL_DETAIL',
			'NAME' => GetMessage('CD_P_SHOW_DELIVERY_PARENT_NAMES_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'SHOW_MESSAGE_ON_CALCULATE_ERROR' => array(
			'PARENT' => 'VISUAL_DETAIL',
			'NAME' => GetMessage('CD_P_SHOW_MESSAGE_ON_CALCULATE_ERROR_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'PREVIEW_SHOW_DELIVERY_PARENT_ID' => array(
			'PARENT' => 'VISUAL_PREVIEW',
			'NAME' => GetMessage('CD_P_PREVIEW_SHOW_DELIVERY_PARENT_ID_TITLE'),
			'TYPE' => $arDeliveryParents ? 'LIST' : 'STRING',
			'MULTIPLE' => 'Y',
			'VALUES' => $arDeliveryParents,
			'DEFAULT' => array(
				0 => '',
			),
		),
		// 'PRODUCT_ID' => array(
		// 	'PARENT' => 'DEFAULT',
		// 	'NAME' => GetMessage('CD_P_PRODUCT_ID_TITLE'),
		// 	'TYPE' => 'STRING',
		// 	'DEFAULT' => '',
		// ),
		// 'PRODUCT_QUANTITY' => array(
		// 	'PARENT' => 'DEFAULT',
		// 	'NAME' => GetMessage('CD_P_PRODUCT_QUANTITY_TITLE'),
		// 	'TYPE' => 'STRING',
		// 	'DEFAULT' => '1',
		// ),
		'LOCATION_CODE' => array(
			'PARENT' => 'DEFAULT',
			'NAME' => GetMessage('CD_P_LOCATION_CODE_TITLE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),
	)
);

if($arCurrentValues['USE_PROFILE_LOCATION'] === 'Y'){
	// $arComponentParameters['PARAMETERS']['USER_PROFILE_ID'] = array(
	// 	'PARENT' => 'DEFAULT',
	// 	'NAME' => GetMessage('CD_P_USER_PROFILE_ID_TITLE'),
	// 	'TYPE' => 'STRING',
	// 	'DEFAULT' => '',
	// );
}

$arComponentParameters['PARAMETERS'] = array_merge(
	$arComponentParameters['PARAMETERS'],
	array(
		'PERSON_TYPE_ID' => array(
			'PARENT' => 'DEFAULT',
			'NAME' => GetMessage('CD_P_PERSON_TYPE_ID_TITLE'),
			'TYPE' => $arPersonType ? 'LIST' : 'STRING',
			'VALUES' => $arPersonType,
			'DEFAULT' => '',
		),
		'PAY_SYSTEM_ID' => array(
			'PARENT' => 'DEFAULT',
			'NAME' => GetMessage('CD_P_PAY_SYSTEM_ID_TITLE'),
			'TYPE' => $arPaySystem ? 'LIST' : 'STRING',
			'VALUES' => $arPaySystem,
			'DEFAULT' => '',
		),
		// 'DELIVERY_ID' => array(
		// 	'PARENT' => 'DEFAULT',
		// 	'NAME' => GetMessage('CD_P_DELIVERY_ID_TITLE'),
		// 	'TYPE' => $arDelivery ? 'LIST' : 'STRING',
		// 	'MULTIPLE' => 'Y',
		// 	'VALUES' => $arDelivery,
		// 	'DEFAULT' => array(
		// 		0 => '',
		// 	),
		// ),
		'ADD_BASKET' => array(
			'PARENT' => 'DEFAULT',
			'NAME' => GetMessage('CD_P_ADD_BASKET_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		// 'BUYER_STORE_ID' => array(
		// 	'PARENT' => 'DEFAULT',
		// 	'NAME' => GetMessage('CD_P_BUYER_STORE_ID_TITLE'),
		// 	'TYPE' => 'STRING',
		// 	'DEFAULT' => '',
		// ),
		'USE_CUSTOM_MESSAGES' => array(
			'PARENT' => 'MESSAGES',
			'NAME' => GetMessage('CD_P_USE_CUSTOM_MESSAGES_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		),
	)
);

if($arCurrentValues['USE_CUSTOM_MESSAGES'] === 'Y'){
	$arComponentParameters['PARAMETERS'] = array_merge(
		$arComponentParameters['PARAMETERS'],
		array(
			'MESS_DELIVERY_PAGE_TITLE' => array(
				'PARENT' => 'MESSAGES',
				'NAME' => GetMessage('CD_P_MESS_DELIVERY_PAGE_TITLE_TITLE'),
				'TYPE' => 'STRING',
				'DEFAULT' => Loc::getMessage('CD_P_MESS_DELIVERY_PAGE_TITLE_DEFAULT'),
			),
			'MESS_DELIVERY_DETAIL_TITLE' => array(
				'PARENT' => 'MESSAGES',
				'NAME' => GetMessage('CD_P_MESS_DELIVERY_DETAIL_TITLE_TITLE'),
				'TYPE' => 'STRING',
				'DEFAULT' => Loc::getMessage('CD_P_MESS_DELIVERY_DETAIL_TITLE_DEFAULT'),
			),
			'MESS_DELIVERY_CALC_ERROR_TITLE' => array(
				'PARENT' => 'MESSAGES',
				'NAME' => GetMessage('CD_P_MESS_DELIVERY_CALC_ERROR_TITLE_TITLE'),
				'TYPE' => 'STRING',
				'DEFAULT' => Loc::getMessage('CD_P_MESS_DELIVERY_CALC_ERROR_TITLE_DEFAULT'),
			),
			'MESS_DELIVERY_CALC_ERROR_TEXT' => array(
				'PARENT' => 'MESSAGES',
				'NAME' => GetMessage('CD_P_MESS_DELIVERY_CALC_ERROR_TEXT_TITLE'),
				'TYPE' => 'STRING',
				'DEFAULT' => Loc::getMessage('CD_P_MESS_DELIVERY_CALC_ERROR_TEXT_DEFAULT'),
			),
			'MESS_DELIVERY_PREVIEW_TITLE' => array(
				'PARENT' => 'MESSAGES',
				'NAME' => GetMessage('CD_P_MESS_DELIVERY_PREVIEW_TITLE_TITLE'),
				'TYPE' => 'STRING',
				'DEFAULT' => Loc::getMessage('CD_P_MESS_DELIVERY_PREVIEW_TITLE_DEFAULT'),
			),
			'MESS_DELIVERY_PREVIEW_MORE_TITLE' => array(
				'PARENT' => 'MESSAGES',
				'NAME' => GetMessage('CD_P_MESS_DELIVERY_PREVIEW_MORE_TITLE_TITLE'),
				'TYPE' => 'STRING',
				'DEFAULT' => Loc::getMessage('CD_P_MESS_DELIVERY_PREVIEW_MORE_TITLE_DEFAULT'),
			),
		)
	);
}
?>