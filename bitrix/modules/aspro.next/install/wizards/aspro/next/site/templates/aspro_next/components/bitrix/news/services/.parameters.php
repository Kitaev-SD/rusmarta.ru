<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arGalleryType = array('big' => GetMessage('GALLERY_BIG'), 'small' => GetMessage('GALLERY_SMALL'));
$landingIBlockID = $tizerIBlockID = '';
/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.next')){
	$arPageBlocks = CNext::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CNext::GetComponentTemplatePageBlocksParams($arPageBlocks);
	CNext::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams); // add option value FROM_MODULE

	$landingIBlockID = CNextCache::$arIBlocks[$_REQUEST["src_site"]]["aspro_next_catalog"]["aspro_next_catalog_info"][0];
	$tizerIBlockID = CNextCache::$arIBlocks[$_REQUEST["src_site"]]["aspro_next_content"]["aspro_next_tizers"][0];
}

$arListView = array(
	'slider' => GetMessage("SLIDER_VIEW"),
	'block' => GetMessage("BLOCK_VIEW"),
);

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'SHOW_DETAIL_LINK' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('SHOW_DETAIL_LINK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'IMAGE_POSITION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 250,
		'NAME' => GetMessage('IMAGE_POSITION'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'left' => GetMessage('IMAGE_POSITION_LEFT'),
			'right' => GetMessage('IMAGE_POSITION_RIGHT'),
		),
		'DEFAULT' => 'left',
	),
	'IMAGE_CATALOG_POSITION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 250,
		'NAME' => GetMessage('IMAGE_CATALOG_POSITION'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'left' => GetMessage('IMAGE_POSITION_LEFT'),
			'right' => GetMessage('IMAGE_POSITION_RIGHT'),
		),
		'DEFAULT' => 'left',
	),
	'SHOW_SECTION_PREVIEW_DESCRIPTION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('T_SHOW_SECTION_PREVIEW_DESCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_SECTION_DESCRIPTION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('T_SHOW_SECTION_DESCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	/*'IMAGE_WIDE' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('T_IMAGE_WIDE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),*/
	'LINE_ELEMENT_COUNT' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('T_LINE_ELEMENT_COUNT'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'2' => 2,
			'3' => 3,
		),
	),
	'LINE_ELEMENT_COUNT_LIST' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('T_LINE_ELEMENT_COUNT_LIST'),
		'TYPE' => 'STRING',
		'DEFAULT' => 3,
	),
	'SHOW_CHILD_SECTIONS' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('SHOW_CHILD_SECTIONS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_NEXT_ELEMENT' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('T_SHOW_NEXT_ELEMENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'USE_SHARE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'GALLERY_TYPE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('GALLERY_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $arGalleryType,
		'DEFAULT' => 'small',
	),
	"LIST_VIEW" => array(
		"NAME" => GetMessage("LIST_VIEW"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arListView,
		"ADDITIONAL_VALUES" => "N",
		"DEFAULT" => "slider"
	),
	'LINKED_ELEMENST_PAGE_COUNT' => array(
		'SORT' => 704,
		'NAME' => GetMessage('LINKED_ELEMENST_PAGE_COUNT'),
		'TYPE' => 'TEXT',
		"PARENT" => "DETAIL_SETTINGS",
		'DEFAULT' => '20',
	),
	"SHOW_DISCOUNT_PERCENT_NUMBER" => array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('SHOW_DISCOUNT_PERCENT_NUMBER_NAME'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'S_ASK_QUESTION' => array(
		'SORT' => 700,
		'NAME' => GetMessage('S_ASK_QUESTION'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'S_ORDER_SERVISE' => array(
		'SORT' => 701,
		'NAME' => GetMessage('S_ORDER_SERVISE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'TIZERS_IBLOCK_ID' => array(
		'SORT' => 701,
		'NAME' => GetMessage('T_TIZERS_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => $tizerIBlockID,
	),
	'LANDING_IBLOCK_ID' => array(
		'SORT' => 701,
		'NAME' => GetMessage('T_LANDING_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => $landingIBlockID,
	),
	'LANDING_SECTION_COUNT' => array(
		'SORT' => 701,
		'NAME' => GetMessage('T_LANDING_SECTION_COUNT'),
		'TYPE' => 'TEXT',
		'DEFAULT' => 10,
	),
	'LANDING_TITLE' => array(
		'SORT' => 701,
		'NAME' => GetMessage('T_LANDING_TITLE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => GetMessage('V_LANDING_TITLE'),
	),
	/*'S_ASK_QUESTION_LANDING' => array(
		'SORT' => 701,
		'NAME' => GetMessage('T_S_ASK_QUESTION'),
		'TYPE' => 'TEXT',
		'DEFAULT' => GetMessage('V_S_ASK_QUESTION'),
	),*/
	'FORM_ID_ORDER_SERVISE' => array(
		'SORT' => 701,
		'NAME' => GetMessage('T_FORM_ID_ORDER_SERVISE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_GALLERY' => array(
		'SORT' => 702,
		'NAME' => GetMessage('T_GALLERY'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_DOCS' => array(
		'SORT' => 703,
		'NAME' => GetMessage('T_DOCS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_GOODS' => array(
		'SORT' => 704,
		'NAME' => GetMessage('T_GOODS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_SERVICES' => array(
		'SORT' => 705,
		'NAME' => GetMessage('T_SERVICES'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_PROJECTS' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_PROJECTS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_REVIEWS' => array(
		'SORT' => 707,
		'NAME' => GetMessage('T_REVIEWS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_STAFF' => array(
		'SORT' => 708,
		'NAME' => GetMessage('T_STAFF'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_NEXT_LINK' => array(
		'SORT' => 707,
		'NAME' => GetMessage('T_NEXT_LINK'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_PREV_LINK' => array(
		'SORT' => 707,
		'NAME' => GetMessage('T_PREV_LINK'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	)
));

$arPrice = array();
if (\Bitrix\Main\Loader::includeModule('catalog'))
{
	$arPrice = CCatalogIBlockParameters::getPriceTypesList();
	$arTemplateParameters['PRICE_CODE'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('PRICE_CODE_TITLE'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arPrice,
	);

	$arStore = array();
	global $USER_FIELD_MANAGER;
	$storeIterator = CCatalogStore::GetList(
		array(),
		array('ISSUING_CENTER' => 'Y'),
		false,
		false,
		array('ID', 'TITLE')
	);
	while ($store = $storeIterator->GetNext())
		$arStore[$store['ID']] = "[".$store['ID']."] ".$store['TITLE'];

	$userFields = $USER_FIELD_MANAGER->GetUserFields("CAT_STORE", 0, LANGUAGE_ID);
	$propertyUF = array();

	foreach($userFields as $fieldName => $userField)
		$propertyUF[$fieldName] = $userField["LIST_COLUMN_LABEL"] ? $userField["LIST_COLUMN_LABEL"] : $fieldName;

	$arTemplateParameters['STORES'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('STORES'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arStore,
		'ADDITIONAL_VALUES' => 'Y'
	);
	$arTemplateParameters['HIDE_NOT_AVAILABLE'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('T_HIDE_NOT_AVAILABLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
}
?>