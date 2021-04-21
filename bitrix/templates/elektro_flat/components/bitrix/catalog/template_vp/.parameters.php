<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

if(!Loader::includeModule('iblock'))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE_CATALOG"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty = array();
if(0 < intval($arCurrentValues["IBLOCK_ID"])) {
	$rsProp = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"], "ACTIVE" => "Y"));
	while($arr = $rsProp->Fetch()) {
		$code = $arr["CODE"];
		$label = "[".$arr["CODE"]."] ".$arr["NAME"];

		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$code] = $label;
	}
}

$arTemplateParameters = array(
	"PATH_TO_SHIPPING"=>array(
		"NAME" => GetMessage("PATH_TO_SHIPPING"),
		"TYPE" => "TEXT",
		"DEFAULT" => "/delivery/",	
	),
	"DISPLAY_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "180",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "225",
	),
	"DISPLAY_DETAIL_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"DISPLAY_DETAIL_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"DISPLAY_MORE_PHOTO_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_MORE_PHOTO_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"DISPLAY_MORE_PHOTO_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_MORE_PHOTO_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"SHARPEN" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_SHARPEN"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"PROPERTY_CODE_MOD" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("T_IBLOCK_PROPERTY_MOD"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty,
		"ADDITIONAL_VALUES" => "Y",
	),
	"IBLOCK_TYPE_REVIEWS" => array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_TYPE_REVIEWS"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y",
	),
	"IBLOCK_ID_REVIEWS" => array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_ID_REVIEWS"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	),
	"BUTTON_PAYMENTS_HREF" => array(
		"NAME" => GetMessage("BUTTON_PAYMENTS_HREF"),
		"TYPE" => "TEXT",
		"DEFAULT" => "/payments/",
	),
	"BUTTON_CREDIT_HREF" => array(
		"NAME" => GetMessage("BUTTON_CREDIT_HREF"),
		"TYPE" => "TEXT",
		"DEFAULT" => "/credit/",
	),
	"BUTTON_DELIVERY_HREF" => array(
		"NAME" => GetMessage("BUTTON_DELIVERY_HREF"),
		"TYPE" => "TEXT",
		"DEFAULT" => "/delivery/",
	)
);

if(ModuleManager::isModuleInstalled("sale")) {
	$arTemplateParameters['USE_BIG_DATA'] = array(
		'PARENT' => 'BIG_DATA_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_USE_BIG_DATA'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y'
	);
	if(!isset($arCurrentValues['USE_BIG_DATA']) || $arCurrentValues['USE_BIG_DATA'] == 'Y') {
		$rcmTypeList = array(
			'bestsell' => GetMessage('CP_BC_TPL_RCM_BESTSELLERS'),
			'personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL'),
			'similar_sell' => GetMessage('CP_BC_TPL_RCM_SOLD_WITH'),
			'similar_view' => GetMessage('CP_BC_TPL_RCM_VIEWED_WITH'),
			'similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR'),
			'any_similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR_ANY'),
			'any_personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL_WBEST'),
			'any' => GetMessage('CP_BC_TPL_RCM_RAND')
		);
		$arTemplateParameters['BIG_DATA_RCM_TYPE'] = array(
			'PARENT' => 'BIG_DATA_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => $rcmTypeList
		);
		unset($rcmTypeList);
	}
}?>