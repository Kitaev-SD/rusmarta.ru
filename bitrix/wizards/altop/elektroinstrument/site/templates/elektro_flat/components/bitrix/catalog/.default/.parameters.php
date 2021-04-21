<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;

if(!Loader::includeModule("iblock"))
	return;

CBitrixComponent::includeComponentClass("bitrix:catalog.section");

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

$arSortOffers = CIBlockParameters::GetElementSortFields(
	array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO", "catalog_PRICE_1"),
	array("KEY_LOWERCASE" => "Y")
);

$arAscDescOffers = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arSortOffers["PRICE"] = GetMessage("IBLOCK_SORT_OFFERS_PRICE");
$arSortOffers["PROPERTIES"] = GetMessage("IBLOCK_SORT_OFFERS_PROPERTIES");

$catalogIncluded = Loader::includeModule("catalog");
$iblockExists = (!empty($arCurrentValues["IBLOCK_ID"]) && (int)$arCurrentValues["IBLOCK_ID"] > 0);
$offers = false;
if($catalogIncluded && $iblockExists) {
	$offers = CCatalogSku::GetInfoByProductIBlock($arCurrentValues["IBLOCK_ID"]);
}

$arTreeOfferPropList = array();
if(!empty($offers)) {
	$rsProps = CIBlockProperty::GetList(array("SORT" => "ASC", "ID" => "ASC"), array("IBLOCK_ID" => $offers["IBLOCK_ID"], "ACTIVE" => "Y"));
	while($arProp = $rsProps->Fetch()) {
		if($arProp["ID"] == $offers["SKU_PROPERTY_ID"])
			continue;	
		$strPropName = "[".$arProp["ID"]."]".("" != $arProp["CODE"] ? "[".$arProp["CODE"]."]" : "")." ".$arProp["NAME"];
		if("" == $arProp["CODE"])
			$arProp["CODE"] = $arProp["ID"];
		
		if($arProp["PROPERTY_TYPE"] == "S")
			$arTreeOfferPropList[$arProp["CODE"]] = $strPropName;
	}
}

$arIBlockFilterSEO = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocksFilterSEO=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("TYPE" => ($arCurrentValues["USE_FILTER_SEO_IBLOCK_TYPE"]!="-"?$arCurrentValues["USE_FILTER_SEO_IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocksFilterSEO[$arRes["ID"]] = $arRes["NAME"];


$arTemplateParameters = array(
	"SEARCH_PAGE_RESULT_COUNT" => array(
		"PARENT" => "SEARCH_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_SEARCH_PAGE_RESULT_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => "900",
	),
	"SEARCH_RESTART" => array(
		"PARENT" => "SEARCH_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_SEARCH_RESTART"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"SEARCH_NO_WORD_LOGIC" => array(
		"PARENT" => "SEARCH_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_SEARCH_NO_WORD_LOGIC"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SEARCH_USE_LANGUAGE_GUESS" => array(
		"PARENT" => "SEARCH_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_SEARCH_USE_LANGUAGE_GUESS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SEARCH_CHECK_DATES" => array(
		"PARENT" => "SEARCH_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_SEARCH_CHECK_DATES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"PATH_TO_SHIPPING" => array(
		"NAME" => GetMessage("PATH_TO_SHIPPING"),
		"TYPE" => "TEXT",
		"DEFAULT" => "/delivery/",	
	),
	"DISPLAY_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "178",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "178",
	),
	"DISPLAY_DETAIL_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "390",
	),
	"DISPLAY_DETAIL_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "390",
	),
	"DISPLAY_MORE_PHOTO_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_MORE_PHOTO_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "86",
	),
	"DISPLAY_MORE_PHOTO_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_MORE_PHOTO_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "86",
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
	),
	"USE_REVIEW" => array(		
		"DEFAULT" => "N",
		"HIDDEN" => "Y"
	),	
	"SHOW_TOP_ELEMENTS" => array(		
		"DEFAULT" => "N",
		"HIDDEN" => "Y"
	),	
	"SECTION_COUNT_ELEMENTS" => array(
		"HIDDEN" => "Y"
	),
	"SECTION_TOP_DEPTH" => array(		
		"HIDDEN" => "Y"
	),
	"ELEMENT_SORT_FIELD2" => array(		
		"HIDDEN" => "Y"
	),
	"ELEMENT_SORT_ORDER2" => array(		
		"HIDDEN" => "Y"
	),
	"ACTION_VARIABLE" => array(		
		"HIDDEN" => "Y"
	),
	"PRODUCT_ID_VARIABLE" => array(		
		"HIDDEN" => "Y"
	),
	"USE_PRODUCT_QUANTITY" => array(		
		"HIDDEN" => "Y"
	),
	"PRODUCT_QUANTITY_VARIABLE" => array(		
		"HIDDEN" => "Y"
	),
	"ADD_PROPERTIES_TO_BASKET" => array(		
		"HIDDEN" => "Y"
	),
	"PRODUCT_PROPS_VARIABLE" => array(		
		"HIDDEN" => "Y"
	),
	"PARTIAL_PRODUCT_PROPERTIES" => array(		
		"HIDDEN" => "Y"
	),
	"PRODUCT_PROPERTIES" => array(		
		"HIDDEN" => "Y"
	),
	"LINK_IBLOCK_TYPE" => array(		
		"HIDDEN" => "Y"
	),
	"LINK_IBLOCK_ID" => array(		
		"HIDDEN" => "Y"
	),
	"LINK_PROPERTY_SID" => array(		
		"HIDDEN" => "Y"
	),
	"LINK_ELEMENTS_URL" => array(		
		"HIDDEN" => "Y"
	),
	"USE_ALSO_BUY" => array(		
		"DEFAULT" => "N",
		"HIDDEN" => "Y"
	),
	"USE_GIFTS_MAIN_PR_SECTION_LIST" => array(		
		"DEFAULT" => "N",
		"HIDDEN" => "Y"
	),	
	"OFFERS_SORT_FIELD" => array(
		"PARENT" => "OFFERS_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arSortOffers,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "sort",
	),
	"OFFERS_SORT_ORDER" => array(
		"PARENT" => "OFFERS_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDescOffers,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_SORT_FIELD2" => array(
		"DEFAULT" => "sort",
		"HIDDEN" => "Y"
	),
	"OFFERS_SORT_ORDER2" => array(
		"DEFAULT" => "asc",
		"HIDDEN" => "Y"
	),
	"INSTANT_RELOAD" => array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("CP_BC_INSTANT_RELOAD"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	),
	"1CB_USE_FILE_FIELD" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("1CB_USE_FILE_FIELD"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y"
	),
	"USE_FILTER_SEO" => array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("F_USE_FILTER_SEO"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y"
	),
);


if($arCurrentValues["USE_FILTER_SEO"]=="Y") {       
    $arTemplateParameters["USE_FILTER_SEO_IBLOCK_TYPE"] = array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("F_IBLOCK_TYPE_FILTER_SEO"),
	    "TYPE" => "LIST",
		"VALUES" => $arIBlockFilterSEO,
		"REFRESH" => "Y",
	);
       
    $arTemplateParameters["USE_FILTER_SEO_IBLOCK"] = array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("F_IBLOCK_FILTER_SEO"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlocksFilterSEO,
		"REFRESH" => "Y",
	);    
}


if($arCurrentValues["1CB_USE_FILE_FIELD"] == "Y") {
	$arTemplateParameters["1CB_FILE_FIELD_MULTIPLE"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("1CB_FILE_FIELD_MULTIPLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y"
	);
	if($arCurrentValues["1CB_FILE_FIELD_MULTIPLE"] == "Y") {
		$arTemplateParameters["1CB_FILE_FIELD_MAX_COUNT"] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("1CB_FILE_FIELD_MAX_COUNT"),
			"TYPE" => "TEXT",
			"DEFAULT" => "5"
		);
	}
	$arTemplateParameters["1CB_FILE_FIELD_NAME"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("1CB_FILE_FIELD_NAME"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("1CB_FILE_FIELD_NAME_DEFAULT")
	);	
	$ar1CbFileFieldTypes = array(
		"" => GetMessage("1CB_FILE_FIELD_TYPE_ALL"),
		"jpg, gif, bmp, png, jpeg" => GetMessage("1CB_FILE_FIELD_TYPE_IMAGES"),
		"mp3, wav, midi, snd, au, wma" => GetMessage("1CB_FILE_FIELD_TYPE_SOUNDS"),
		"mpg, avi, wmv, mpeg, mpe, flv" => GetMessage("1CB_FILE_FIELD_TYPE_VIDEO"),
		"doc, docx, txt, rtf" => GetMessage("1CB_FILE_FIELD_TYPE_DOCS")
	);
	$arTemplateParameters["1CB_FILE_FIELD_TYPE"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("1CB_FILE_FIELD_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $ar1CbFileFieldTypes,
		"DEFAULT" => "",
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "N",
		"MULTIPLE" => "N"
	);	
}
 $arTemplateParameters['SHOW_MAX_QUANTITY'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY'),
		'TYPE' => 'LIST',
		'REFRESH' => 'Y',
		'MULTIPLE' => 'N',
		'VALUES' => array(
			'N' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_N'), //"не показывать";
			'Y' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_Y'),  //"с отображением реального остатка";
			'M' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_M')  //"с подменой остатка текстом";
		),
		'DEFAULT' => array('N')
	);
	if(isset($arCurrentValues['SHOW_MAX_QUANTITY'])) {
		if($arCurrentValues['SHOW_MAX_QUANTITY'] !== 'N') {
			$arTemplateParameters['MESS_SHOW_MAX_QUANTITY'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_SHOW_MAX_QUANTITY'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_SHOW_MAX_QUANTITY_DEFAULT')
			);
		}
		if($arCurrentValues['SHOW_MAX_QUANTITY'] === 'M') {
			$arTemplateParameters['RELATIVE_QUANTITY_FACTOR'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_RELATIVE_QUANTITY_FACTOR'),
				'TYPE' => 'STRING',
				'DEFAULT' => '5'
			);
			$arTemplateParameters['MESS_RELATIVE_QUANTITY_MANY'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_MANY'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_MANY_DEFAULT')
			);
			$arTemplateParameters['MESS_RELATIVE_QUANTITY_FEW'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_FEW'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_FEW_DEFAULT')
			);
		}
	}
$ar1CbRequiredFields = array(
	"NAME" => GetMessage("1CB_REQUIRED_FIELDS_NAME"),
    "PHONE" => GetMessage("1CB_REQUIRED_FIELDS_PHONE"),
    "EMAIL" => GetMessage("1CB_REQUIRED_FIELDS_EMAIL"),
	"MESSAGE" => GetMessage("1CB_REQUIRED_FIELDS_MESSAGE")
);
if($arCurrentValues["1CB_USE_FILE_FIELD"] == "Y") {
	$ar1CbRequiredFields["FILE"] = GetMessage("1CB_REQUIRED_FIELDS_FILE");
}

$arTemplateParameters["1CB_REQUIRED_FIELDS"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("1CB_REQUIRED_FIELDS"),
	"TYPE" => "LIST",
	"VALUES" => $ar1CbRequiredFields,
	"DEFAULT" => array("NAME", "PHONE"),
	"ADDITIONAL_VALUES" => "N",
	"REFRESH" => "N",
	"MULTIPLE" => "Y"
);

$arTemplateParameters["RELATED_PRODUCTS_SHOW"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("RELATED_PRODUCTS_SHOW"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y"
);

$arTemplateParameters["NUMBER_ACCESSORIES"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("NUMBER_OF_OUTPUT_ACCESSORIES"),
	"TYPE" => "TEXT",
	"DEFAULT" => "8"
);

$arTemplateParameters["COUNT_REVIEW"] = array(
	"PARENT" => "REVIEW_SETTINGS",
	"NAME" => GetMessage("COUNT_REVIEW"),
	"TYPE" => "TEXT",
	"DEFAULT" => "5"
);

$lineElementCount = (int)$arCurrentValues["LINE_ELEMENT_COUNT"] ?: 4;
$pageElementCount = (int)$arCurrentValues["PAGE_ELEMENT_COUNT"] ?: 12;

$arTemplateParameters["LIST_PRODUCT_ROW_VARIANTS"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BC_TPL_PRODUCT_ROW_VARIANTS"),
	"TYPE" => "CUSTOM",
	"BIG_DATA" => "N",
	"COUNT_PARAM_NAME" => "PAGE_ELEMENT_COUNT",
	"JS_FILE" => CatalogSectionComponent::getSettingsScript($templateFolder, "dragdrop_add"),
	"JS_EVENT" => "initDraggableAddControl",
	"JS_MESSAGES" => Json::encode(array(
		"variant" => GetMessage("CP_BC_TPL_SETTINGS_VARIANT"),
		"delete" => GetMessage("CP_BC_TPL_SETTINGS_DELETE"),
		"quantity" => GetMessage("CP_BC_TPL_SETTINGS_QUANTITY"),
		"quantityBigData" => GetMessage("CP_BC_TPL_SETTINGS_QUANTITY_BIG_DATA")
	)),
	"JS_DATA" => Json::encode(CatalogSectionComponent::getTemplateVariantsMap()),
	"DEFAULT" => Json::encode(CatalogSectionComponent::predictRowVariants($lineElementCount, $pageElementCount))
);

$arTemplateParameters["DETAIL_MAIN_BLOCK_PROPERTY_CODE"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_MAIN_BLOCK_PROPERTY_CODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"SIZE" => (count($arProperty) > 5 ? 8 : 3),
	"VALUES" => $arProperty
);

$arTemplateParameters["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"SIZE" => (count($arTreeOfferPropList) > 5 ? 8 : 3),
	"VALUES" => $arTreeOfferPropList
);

$arTemplateParameters["LAZY_LOAD"] = array(
	"PARENT" => "PAGER_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_LAZY_LOAD"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);

if(isset($arCurrentValues["LAZY_LOAD"]) && $arCurrentValues["LAZY_LOAD"] === "Y") {
	$arTemplateParameters["MESS_BTN_LAZY_LOAD"] = array(
		"PARENT" => "PAGER_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MESS_BTN_LAZY_LOAD"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("CP_BC_TPL_MESS_BTN_LAZY_LOAD_DEFAULT")
	);
}

$arTemplateParameters["LOAD_ON_SCROLL"] = array(
	"PARENT" => "PAGER_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_LOAD_ON_SCROLL"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y"
);

if($arCurrentValues["USE_COMPARE"] == "Y") {
	$arTemplateParameters["DISPLAY_ELEMENT_SELECT_BOX"] = array(	
		"DEFAULT" => "N",
		"HIDDEN" => "Y"
	);
	if($arCurrentValues["DISPLAY_ELEMENT_SELECT_BOX"] == "Y") {
		$arTemplateParameters["ELEMENT_SORT_FIELD_BOX"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["ELEMENT_SORT_ORDER_BOX"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["ELEMENT_SORT_FIELD_BOX2"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["ELEMENT_SORT_ORDER_BOX2"] = array(		
			"HIDDEN" => "Y"
		);
	}
}

if($arCurrentValues["SHOW_TOP_ELEMENTS"] != "N") {
	$arTemplateParameters["TOP_ELEMENT_COUNT"] = array(		
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_LINE_ELEMENT_COUNT"] = array(		
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_ELEMENT_SORT_FIELD"] = array(		
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_ELEMENT_SORT_ORDER"] = array(		
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_ELEMENT_SORT_FIELD2"] = array(		
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_ELEMENT_SORT_ORDER2"] = array(		
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_PROPERTY_CODE"] = array(		
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_PROPERTY_CODE_MOBILE"] = array(		
		"HIDDEN" => "Y"
	);
	if(!empty($offers)) {
		$arTemplateParameters["TOP_OFFERS_FIELD_CODE"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["TOP_OFFERS_PROPERTY_CODE"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["TOP_OFFERS_LIMIT"] = array(		
			"HIDDEN" => "Y"
		);
	}
}

if($arCurrentValues["USE_FILTER"] == "Y") {
	$arTemplateParameters["FILTER_FIELD_CODE"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["FILTER_PROPERTY_CODE"] = array(
		"HIDDEN" => "Y"
	);
	if(!empty($offers)) {
		$arTemplateParameters["FILTER_OFFERS_FIELD_CODE"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["FILTER_OFFERS_PROPERTY_CODE"] = array(
			"HIDDEN" => "Y"
		);
	}
}

if(!isset($arCurrentValues["COMPATIBLE_MODE"]) || $arCurrentValues["COMPATIBLE_MODE"] != "N") {
	if(ModuleManager::isModuleInstalled("forum") && $arCurrentValues["USE_REVIEW"] == "Y") {
		$arTemplateParameters["MESSAGES_PER_PAGE"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["USE_CAPTCHA"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["REVIEW_AJAX_POST"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["PATH_TO_SMILE"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["FORUM_ID"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["URL_TEMPLATES_READ"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["SHOW_LINK_TO_FORUM"] = array(		
			"HIDDEN" => "Y"
		);
	}
}

if(ModuleManager::isModuleInstalled("sale")) {
	if($arCurrentValues["USE_ALSO_BUY"] == "Y") {
		$arTemplateParameters["ALSO_BUY_ELEMENT_COUNT"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["ALSO_BUY_MIN_BUYES"] = array(		
			"HIDDEN" => "Y"
		);
	}
	if($arCurrentValues["USE_GIFTS_DETAIL"] == "Y") {
		$arTemplateParameters["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"] = array(		
			"HIDDEN" => "Y"
		);		
		$arTemplateParameters["GIFTS_DETAIL_TEXT_LABEL_GIFT"] = array(		
			"HIDDEN" => "Y"
		);
	}
	if($arCurrentValues["USE_GIFTS_SECTION"] == "Y") {
		$arTemplateParameters["GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT"] = array(		
			"HIDDEN" => "Y"
		);		
		$arTemplateParameters["GIFTS_SECTION_LIST_TEXT_LABEL_GIFT"] = array(		
			"HIDDEN" => "Y"
		);
	}
	if($arCurrentValues["USE_GIFTS_DETAIL"] == "Y" || $arCurrentValues["USE_GIFTS_SECTION"] == "Y") {
		$arTemplateParameters["GIFTS_SHOW_DISCOUNT_PERCENT"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_OLD_PRICE"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_NAME"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_IMAGE"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_MESS_BTN_BUY"] = array(		
			"HIDDEN" => "Y"
		);
	}
	if($arCurrentValues["USE_GIFTS_MAIN_PR_SECTION_LIST"] == "Y") {
		$arTemplateParameters["GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE"] = array(		
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE"] = array(		
			"HIDDEN" => "Y"
		);
	}
}

if(ModuleManager::isModuleInstalled("sale")) {
	$arTemplateParameters["USE_BIG_DATA"] = array(
		"PARENT" => "BIG_DATA_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_USE_BIG_DATA"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y"
	);
	if(!isset($arCurrentValues["USE_BIG_DATA"]) || $arCurrentValues["USE_BIG_DATA"] == "Y") {
		$rcmTypeList = array(
			"bestsell" => GetMessage("CP_BC_TPL_RCM_BESTSELLERS"),
			"personal" => GetMessage("CP_BC_TPL_RCM_PERSONAL"),
			"similar_sell" => GetMessage("CP_BC_TPL_RCM_SOLD_WITH"),
			"similar_view" => GetMessage("CP_BC_TPL_RCM_VIEWED_WITH"),
			"similar" => GetMessage("CP_BC_TPL_RCM_SIMILAR"),
			"any_similar" => GetMessage("CP_BC_TPL_RCM_SIMILAR_ANY"),
			"any_personal" => GetMessage("CP_BC_TPL_RCM_PERSONAL_WBEST"),
			"any" => GetMessage("CP_BC_TPL_RCM_RAND")
		);
		$arTemplateParameters["BIG_DATA_RCM_TYPE"] = array(
			"PARENT" => "BIG_DATA_SETTINGS",
			"NAME" => GetMessage("CP_BC_TPL_BIG_DATA_RCM_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $rcmTypeList
		);
		unset($rcmTypeList);
		$arTemplateParameters["SHOW_FROM_SECTION"] = array(
			"PARENT" => "BIG_DATA_SETTINGS",
			"NAME" => GetMessage("CP_BCS_TPL_SHOW_FROM_SECTION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		);
	}
}?>