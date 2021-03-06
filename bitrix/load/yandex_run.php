<?
//<title>Yandex</title>
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @var int $IBLOCK_ID */
/** @var string $SETUP_SERVER_NAME */
/** @var string $SETUP_FILE_NAME */
/** @var array $V */
/** @var array|string $XML_DATA */
/** @var bool $firstStep */
/** @var int $CUR_ELEMENT_ID */
/** @var bool $finalExport */
/** @var bool $boolNeedRootSection */
/** @var int $intMaxSectionID */

use Bitrix\Currency,
	Bitrix\Iblock,
	Bitrix\Catalog;

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/vsfr.merchant/export_yandex.php');

$MAX_EXECUTION_TIME = (isset($MAX_EXECUTION_TIME) ? (int)$MAX_EXECUTION_TIME : 0);
if ($MAX_EXECUTION_TIME <= 0)
	$MAX_EXECUTION_TIME = 0;
if (defined('BX_CAT_CRON') && BX_CAT_CRON == true)
{
	$MAX_EXECUTION_TIME = 0;
	$firstStep = true;
}
if (defined("CATALOG_EXPORT_NO_STEP") && CATALOG_EXPORT_NO_STEP == true)
{
	$MAX_EXECUTION_TIME = 0;
	$firstStep = true;
}
if ($MAX_EXECUTION_TIME == 0)
	set_time_limit(0);
if (!isset($firstStep))
	$firstStep = true;

$pageSize = 100;
$navParams = array('nTopCount' => $pageSize);

$SETUP_VARS_LIST = 'IBLOCK_ID,V,XML_DATA,SETUP_SERVER_NAME,SETUP_FILE_NAME,USE_HTTPS,FILTER_AVAILABLE,DISABLE_REFERERS,MAX_EXECUTION_TIME';
$INTERNAL_VARS_LIST = 'intMaxSectionID,boolNeedRootSection,arSectionIDs,arAvailGroups';

global $USER, $APPLICATION;
$bTmpUserCreated = false;
if (!CCatalog::IsUserExists())
{
	$bTmpUserCreated = true;
	if (isset($USER))
		$USER_TMP = $USER;
	$USER = new CUser();
}

CCatalogDiscountSave::Disable();
/** @noinspection PhpDeprecationInspection */
CCatalogDiscountCoupon::ClearCoupon();
if ($USER->IsAuthorized())
{
	/** @noinspection PhpDeprecationInspection */
	CCatalogDiscountCoupon::ClearCouponsByManage($USER->GetID());
}

$arYandexFields = array(
	'typePrefix', 'vendor', 'vendorCode', 'model',
	'author', 'name', 'publisher', 'series', 'year',
	'ISBN', 'volume', 'part', 'language', 'binding',
	'page_extent', 'table_of_contents', 'performed_by', 'performance_type',
	'storage', 'format', 'recording_length', 'artist', 'title', 'year', 'media',
	'starring', 'director', 'originalName', 'country', 'aliases',
	'description', 'sales_notes', 'promo', 'provider', 'tarifplan',
	'xCategory', 'additional', 'worldRegion', 'region', 'days', 'dataTour',
	'hotel_stars', 'room', 'meal', 'included', 'transport', 'price_min', 'price_max',
	'options', 'manufacturer_warranty', 'country_of_origin', 'downloadable', 'adult', 'param',
	'place', 'hall', 'hall_part', 'is_premiere', 'is_kids', 'date',
	'mobile_link', 'availability_date', 'cost_of_goods_sold', 'expiration_date', 'sale_price_effective_date', 'unit_pricing_measure', 'unit_pricing_base_measure', 'installment', 'subscription_cost', 'loyalty_points', 'google_product_category', 'product_type', 'brand', 'gtin', 'mpn', 'identifier_exists', 'condition', 'adult', 'multipack', 'is_bundle', 'energy_efficiency_class', 'energy_efficiency_class', 'min_energy_efficiency_class', 'max_energy_efficiency_class', 'age_group', 'color', 'gender', 'material', 'pattern', 'size', 'size_type', 'size_system', 'item_group_id', 'ads_redirect', 'custom_label_1', 'custom_label_2', 'custom_label_3', 'custom_label_4', 'promotion_id', 'excluded_destination', 'included_destination', 'shipping', 'shipping_label', 'shipping_weight', 'shipping_length', 'shipping_width', 'shipping_height', 'min_handling_time', 'max_handling_time', 'tax', 'tax_category'
);

$formatList = array(
	'none' => array(
		'brand', 'google_product_category', 'gtin', 'custom_label_1', 'custom_label_2'
	),
	'merchant' => array(
		'mobile_link', 'availability_date', 'cost_of_goods_sold', 'expiration_date', 'sale_price_effective_date', 'unit_pricing_measure', 'unit_pricing_base_measure', 'installment', 'subscription_cost', 'loyalty_points', 'google_product_category', 'product_type', 'brand', 'gtin', 'mpn', 'identifier_exists', 'condition', 'adult', 'multipack', 'is_bundle', 'energy_efficiency_class', 'energy_efficiency_class', 'min_energy_efficiency_class', 'max_energy_efficiency_class', 'age_group', 'color', 'gender', 'material', 'pattern', 'size', 'size_type', 'size_system', 'item_group_id', 'ads_redirect', 'custom_label_1', 'custom_label_2', 'custom_label_3', 'custom_label_4', 'promotion_id', 'excluded_destination', 'included_destination', 'shipping', 'shipping_label', 'shipping_weight', 'shipping_length', 'shipping_width', 'shipping_height', 'min_handling_time', 'max_handling_time', 'tax', 'tax_category'
	),
	/*
	'book' => array(
		'author', 'publisher', 'series', 'year', 'ISBN', 'volume', 'part', 'language', 'binding',
		'page_extent', 'table_of_contents', 'sales_notes'
	),
	'audiobook' => array(
		'author', 'publisher', 'series', 'year', 'ISBN', 'performed_by', 'performance_type',
		'language', 'volume', 'part', 'format', 'storage', 'recording_length', 'table_of_contents'
	),
	'artist.title' => array(
		'title', 'artist', 'director', 'starring', 'originalName', 'country', 'year', 'media', 'adult'
	)*/
);

if (!function_exists("yandex_replace_special"))
{
	function yandex_replace_special($arg)
	{
		if (in_array($arg[0], array("&quot;", "&amp;", "&lt;", "&gt;")))
			return $arg[0];
		else
			return " ";
	}
}

if (!function_exists("yandex_text2xml"))
{
	function yandex_text2xml($text, $bHSC = false, $bDblQuote = false)
	{
		global $APPLICATION;

		$bHSC = (true == $bHSC ? true : false);
		$bDblQuote = (true == $bDblQuote ? true: false);

		if ($bHSC)
		{
			$text = htmlspecialcharsbx($text);
			if ($bDblQuote)
				$text = str_replace('&quot;', '"', $text);
		}
		$text = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $text);
		$text = str_replace("'", "&apos;", $text);
		$text = $APPLICATION->ConvertCharset($text, LANG_CHARSET, 'UTF-8');
		return $text;
	}
}

if (!function_exists('yandex_get_value'))
{
function yandex_get_value($arOffer, $param, $PROPERTY, $arProperties, $arUserTypeFormat, $usedProtocol)
{
	global $iblockServerName;

	$strProperty = '';
	$bParam = (strncmp($param, 'PARAM_', 6) == 0);
	if (isset($arProperties[$PROPERTY]) && !empty($arProperties[$PROPERTY]))
	{
		$iblockProperty = $arProperties[$PROPERTY];
		$PROPERTY_CODE = $iblockProperty['CODE'];
		if (!isset($arOffer['PROPERTIES'][$PROPERTY_CODE]) && !isset($arOffer['PROPERTIES'][$PROPERTY]))
			return $strProperty;
		$arProperty = (
			isset($arOffer['PROPERTIES'][$PROPERTY_CODE])
			? $arOffer['PROPERTIES'][$PROPERTY_CODE]
			: $arOffer['PROPERTIES'][$PROPERTY]
		);
		if ($arProperty['ID'] != $PROPERTY)
			return $strProperty;

		$value = '';
		$description = '';
		switch ($iblockProperty['PROPERTY_TYPE'])
		{
			case 'USER_TYPE':
				if ($iblockProperty['MULTIPLE'] == 'Y')
				{
					if (!empty($arProperty['~VALUE']))
					{
						$arValues = array();
						foreach($arProperty["~VALUE"] as $oneValue)
						{
							$isArray = is_array($oneValue);
							if (
								($isArray && !empty($oneValue))
								|| (!$isArray && $oneValue != '')
							)
							{
								$arValues[] = call_user_func_array($arUserTypeFormat[$PROPERTY],
									array(
										$iblockProperty,
										array("VALUE" => $oneValue),
										array('MODE' => 'SIMPLE_TEXT'),
									)
								);
							}
						}
						$value = implode(', ', $arValues);
					}
				}
				else
				{
					$isArray = is_array($arProperty['~VALUE']);
					if (
						($isArray && !empty($arProperty['~VALUE']))
						|| (!$isArray && $arProperty['~VALUE'] != '')
					)
					{
						$value = call_user_func_array($arUserTypeFormat[$PROPERTY],
							array(
								$iblockProperty,
								array("VALUE" => $arProperty["~VALUE"]),
								array('MODE' => 'SIMPLE_TEXT'),
							)
						);
					}
				}
				break;
			case Iblock\PropertyTable::TYPE_ELEMENT:
				if (!empty($arProperty['VALUE']))
				{
					$arCheckValue = array();
					if (!is_array($arProperty['VALUE']))
					{
						$arProperty['VALUE'] = (int)$arProperty['VALUE'];
						if ($arProperty['VALUE'] > 0)
							$arCheckValue[] = $arProperty['VALUE'];
					}
					else
					{
						foreach ($arProperty['VALUE'] as $intValue)
						{
							$intValue = (int)$intValue;
							if ($intValue > 0)
								$arCheckValue[] = $intValue;
						}
						unset($intValue);
					}
					if (!empty($arCheckValue))
					{
						$filter = array(
							'@ID' => $arCheckValue
						);
						if ($iblockProperty['LINK_IBLOCK_ID'] > 0)
							$filter['=IBLOCK_ID'] = $iblockProperty['LINK_IBLOCK_ID'];

						$iterator = Iblock\ElementTable::getList(array(
							'select' => array('ID', 'NAME'),
							'filter' => array($filter)
						));
						while ($row = $iterator->fetch())
						{
							$value .= ($value ? ', ' : '').$row['NAME'];
						}
						unset($row, $iterator);
					}
				}
				break;
			case Iblock\PropertyTable::TYPE_SECTION:
				if (!empty($arProperty['VALUE']))
				{
					$arCheckValue = array();
					if (!is_array($arProperty['VALUE']))
					{
						$arProperty['VALUE'] = (int)$arProperty['VALUE'];
						if ($arProperty['VALUE'] > 0)
							$arCheckValue[] = $arProperty['VALUE'];
					}
					else
					{
						foreach ($arProperty['VALUE'] as $intValue)
						{
							$intValue = (int)$intValue;
							if ($intValue > 0)
								$arCheckValue[] = $intValue;
						}
						unset($intValue);
					}
					if (!empty($arCheckValue))
					{
						$filter = array(
							'@ID' => $arCheckValue
						);
						if ($iblockProperty['LINK_IBLOCK_ID'] > 0)
							$filter['=IBLOCK_ID'] = $iblockProperty['LINK_IBLOCK_ID'];

						$iterator = Iblock\SectionTable::getList(array(
							'select' => array('ID', 'NAME'),
							'filter' => array($filter)
						));
						while ($row = $iterator->fetch())
						{
							$value .= ($value ? ', ' : '').$row['NAME'];
						}
						unset($row, $iterator);
					}
				}
				break;
			case Iblock\PropertyTable::TYPE_LIST:
				if (!empty($arProperty['~VALUE']))
				{
					if (is_array($arProperty['~VALUE']))
						$value .= implode(', ', $arProperty['~VALUE']);
					else
						$value .= $arProperty['~VALUE'];
				}
				break;
			case Iblock\PropertyTable::TYPE_FILE:
				if (!empty($arProperty['VALUE']))
				{
					if (is_array($arProperty['VALUE']))
					{
						foreach ($arProperty['VALUE'] as $intValue)
						{
							$intValue = (int)$intValue;
							if ($intValue > 0)
							{
								if ($ar_file = CFile::GetFileArray($intValue))
								{
									if(substr($ar_file["SRC"], 0, 1) == "/")
										$strFile = $usedProtocol.$iblockServerName.CHTTP::urnEncode($ar_file['SRC'], 'utf-8');
									else
										$strFile = $ar_file["SRC"];
									$value .= ($value ? ', ' : '').$strFile;
								}
							}
						}
						unset($intValue);
					}
					else
					{
						$arProperty['VALUE'] = (int)$arProperty['VALUE'];
						if ($arProperty['VALUE'] > 0)
						{
							if ($ar_file = CFile::GetFileArray($arProperty['VALUE']))
							{
								if(substr($ar_file["SRC"], 0, 1) == "/")
									$strFile = $usedProtocol.$iblockServerName.CHTTP::urnEncode($ar_file['SRC'], 'utf-8');
								else
									$strFile = $ar_file["SRC"];
								$value = $strFile;
							}
						}
					}
				}
				break;
			default:
				if ($bParam && $iblockProperty['WITH_DESCRIPTION'] == 'Y')
				{
					$description = $arProperty['~DESCRIPTION'];
					$value = $arProperty['~VALUE'];
				}
				else
				{
					$value = is_array($arProperty['~VALUE']) ? implode(', ', $arProperty['~VALUE']) : $arProperty['~VALUE'];
				}
		}

		// !!!! check multiple properties and properties like CML2_ATTRIBUTES svoistva

		if ($bParam)
		{
			if (is_array($description))
			{
				foreach ($value as $key => $val)
				{
					$strProperty .= $strProperty ? "\n" : "";
					$strProperty .= '<param name="'.yandex_text2xml($description[$key], true).'">'.
						yandex_text2xml($val, true).'</param>';
				}
			}
			else
			{
				$strProperty .= '<param name="'.yandex_text2xml($iblockProperty['NAME'], true).'">'.
					yandex_text2xml($value, true).'</param>';
			}
		}
		elseif(!empty($value))
		{
			$param_h = yandex_text2xml($param, true);
			$strProperty .= '<g:'.$param_h.'>'.yandex_text2xml($value, true).'</g:'.$param_h.'>';
		}

		unset($iblockProperty);
	}

	return $strProperty;
}
}

if (!function_exists('yandexPrepareItems'))
{
	function yandexPrepareItems(array &$list, array $options)
	{
		foreach (array_keys($list) as $index)
		{
			$row = &$list[$index];

			$row['DETAIL_PAGE_URL'] = (string)$row['DETAIL_PAGE_URL'];
			if ($row['DETAIL_PAGE_URL'] !== '')
			{
				$safeRow = array();
				foreach ($row as $field => $value)
				{
					if ($field == 'PREVIEW_TEXT' || $field == 'DETAIL_TEXT')
						continue;
					if (strncmp($field, 'CATALOG_', 8) == 0)
						continue;
					if (is_array($value))
						continue;
					if (preg_match("/[;&<>\"]/", $value))
						$safeRow[$field] = htmlspecialcharsEx($value);
					else
						$safeRow[$field] = $value;
					$safeRow['~'.$field] = $value;
				}
				unset($field, $value);
				$row['DETAIL_PAGE_URL'] = \CIBlock::ReplaceDetailUrl($safeRow['~DETAIL_PAGE_URL'], $safeRow, true, 'E');
				unset($safeRow);
			}

			if ($row['DETAIL_PAGE_URL'] == '')
				$row['DETAIL_PAGE_URL'] = '/';
			else
				$row['DETAIL_PAGE_URL'] = str_replace(' ', '%20', $row['DETAIL_PAGE_URL']);

			$row['PICTURE'] = false;
			$row['DETAIL_PICTURE'] = (int)$row['DETAIL_PICTURE'];
			$row['PREVIEW_PICTURE'] = (int)$row['PREVIEW_PICTURE'];
			if ($row['DETAIL_PICTURE'] > 0 || $row['PREVIEW_PICTURE'] > 0)
			{
				$pictureFile = CFile::GetFileArray($row['DETAIL_PICTURE'] > 0 ? $row['DETAIL_PICTURE'] : $row['PREVIEW_PICTURE']);
				if (!empty($pictureFile))
				{
					if (strncmp($pictureFile['SRC'], '/', 1) == 0)
						$picturePath = $options['PROTOCOL'].$options['SITE_NAME'].CHTTP::urnEncode($pictureFile['SRC'], 'utf-8');
					else
						$picturePath = $pictureFile['SRC'];
					$row['PICTURE'] = $picturePath;
					unset($picturePath);
				}
				unset($pictureFile);
			}

			$row['DESCRIPTION'] = '';
			if ($row['PREVIEW_TEXT'] !== null)
			{
				$row['DESCRIPTION'] = yandex_text2xml(
					TruncateText(
						$row['PREVIEW_TEXT_TYPE'] == 'html'
						? strip_tags(preg_replace_callback("'&[^;]*;'", 'yandex_replace_special', $row['PREVIEW_TEXT']))
						: preg_replace_callback("'&[^;]*;'", 'yandex_replace_special', $row['PREVIEW_TEXT']),
						$options['MAX_DESCRIPTION_LENGTH']
					),
					true
				);
			}

			unset($row);
		}
		unset($index);
	}
}

$arRunErrors = array();

if (isset($XML_DATA))
{
	if (is_string($XML_DATA) && CheckSerializedData($XML_DATA))
		$XML_DATA = unserialize(stripslashes($XML_DATA));
}
if (!isset($XML_DATA) || !is_array($XML_DATA))
	$arRunErrors[] = GetMessage('YANDEX_ERR_BAD_XML_DATA');

$yandexFormat = 'none';
if (isset($XML_DATA['TYPE']) && isset($formatList[$XML_DATA['TYPE']]))
	$yandexFormat = $XML_DATA['TYPE'];

$productFormat = ($yandexFormat != 'none' ? ' type="'.htmlspecialcharsbx($yandexFormat).'"' : '');

$fields = array();
$parametricFields = array();
$fieldsExist = !empty($XML_DATA['XML_DATA']) && is_array($XML_DATA['XML_DATA']);
$parametricFieldsExist = false;
if ($fieldsExist)
{
	foreach ($XML_DATA['XML_DATA'] as $key => $value)
	{
		if ($key == 'PARAMS')
			$parametricFieldsExist = (!empty($value) && is_array($value));
		if (is_array($value))
			continue;
		$value = (string)$value;
		if ($value == '')
			continue;
		$fields[$key] = $value;
	}
	unset($key, $value);
	$fieldsExist = !empty($fields);
}

if ($parametricFieldsExist)
{
	$parametricFields = $XML_DATA['XML_DATA']['PARAMS'];
	if (!empty($parametricFields))
	{
		foreach (array_keys($parametricFields) as $index)
		{
			if ((string)$parametricFields[$index] === '')
				unset($parametricFields[$index]);
		}
	}
	$parametricFieldsExist = !empty($parametricFields);
}

$needProperties = $fieldsExist || $parametricFieldsExist;
$yandexNeedPropertyIds = array();
if ($fieldsExist)
{
	foreach ($fields as $id)
		$yandexNeedPropertyIds[$id] = true;
	unset($id);
}
if ($parametricFieldsExist)
{
	foreach ($parametricFields as $id)
		$yandexNeedPropertyIds[$id] = true;
	unset($id);
}

$whiteList = array(
	'ID' => true,
	'~ID' => true,
	'PROPERTY_TYPE' => true,
	'~PROPERTY_TYPE' => true,
	'MULTIPLE' => true,
	'~MULTIPLE' => true,
	'USER_TYPE' => true,
	'~USER_TYPE' => true,
	'VALUE' => true,
	'~VALUE' => true,
	'VALUE_ENUM_ID' => true,
	'~VALUE_ENUM_ID' => true,
	'DESCRIPTION' => true,
	'~DESCRIPTION' => true
);

$IBLOCK_ID = (int)$IBLOCK_ID;
$db_iblock = CIBlock::GetByID($IBLOCK_ID);
if (!($ar_iblock = $db_iblock->Fetch()))
{
	$arRunErrors[] = str_replace('#ID#', $IBLOCK_ID, GetMessage('YANDEX_ERR_NO_IBLOCK_FOUND_EXT'));
}
/*elseif (!CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, 'iblock_admin_display'))
{
	$arRunErrors[] = str_replace('#IBLOCK_ID#',$IBLOCK_ID,GetMessage('CET_ERROR_IBLOCK_PERM'));
} */
else
{
	$SETUP_SERVER_NAME = trim($SETUP_SERVER_NAME);

	if (strlen($SETUP_SERVER_NAME) <= 0)
	{
		if (strlen($ar_iblock['SERVER_NAME']) <= 0)
		{
			$b = "sort";
			$o = "asc";
			$rsSite = CSite::GetList($b, $o, array("LID" => $ar_iblock["LID"]));
			if($arSite = $rsSite->Fetch())
				$ar_iblock["SERVER_NAME"] = $arSite["SERVER_NAME"];
			if(strlen($ar_iblock["SERVER_NAME"])<=0 && defined("SITE_SERVER_NAME"))
				$ar_iblock["SERVER_NAME"] = SITE_SERVER_NAME;
			if(strlen($ar_iblock["SERVER_NAME"])<=0)
				$ar_iblock["SERVER_NAME"] = COption::GetOptionString("main", "server_name", "");
		}
	}
	else
	{
		$ar_iblock['SERVER_NAME'] = $SETUP_SERVER_NAME;
	}
	$ar_iblock['PROPERTY'] = array();
	$rsProps = \CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'NAME' => 'ASC'),
		array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
	);
	while ($arProp = $rsProps->Fetch())
	{
		$arProp['ID'] = (int)$arProp['ID'];
		$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
		$arProp['CODE'] = (string)$arProp['CODE'];
		if ($arProp['CODE'] == '')
			$arProp['CODE'] = $arProp['ID'];
		$arProp['LINK_IBLOCK_ID'] = (int)$arProp['LINK_IBLOCK_ID'];
		$ar_iblock['PROPERTY'][$arProp['ID']] = $arProp;
	}
}

global $iblockServerName;
$iblockServerName = $ar_iblock["SERVER_NAME"];

$arProperties = array();
if (isset($ar_iblock['PROPERTY']))
	$arProperties = $ar_iblock['PROPERTY'];

$boolOffers = false;
$arOffers = false;
$arOfferIBlock = false;
$intOfferIBlockID = 0;
$arSelectOfferProps = array();
$arSelectedPropTypes = array(
	Iblock\PropertyTable::TYPE_STRING,
	Iblock\PropertyTable::TYPE_NUMBER,
	Iblock\PropertyTable::TYPE_LIST,
	Iblock\PropertyTable::TYPE_ELEMENT,
	Iblock\PropertyTable::TYPE_SECTION
);
$arOffersSelectKeys = array(
	YANDEX_SKU_EXPORT_ALL,
	YANDEX_SKU_EXPORT_MIN_PRICE,
	YANDEX_SKU_EXPORT_PROP,
);
$arCondSelectProp = array(
	'ZERO',
	'NONZERO',
	'EQUAL',
	'NONEQUAL',
);
$arSKUExport = array();

$arCatalog = CCatalogSku::GetInfoByIBlock($IBLOCK_ID);
if (empty($arCatalog))
{
	$arRunErrors[] = str_replace('#ID#', $IBLOCK_ID, GetMessage('YANDEX_ERR_NO_IBLOCK_IS_CATALOG'));
}
else
{
	$arOffers = CCatalogSku::GetInfoByProductIBlock($IBLOCK_ID);
	if (!empty($arOffers['IBLOCK_ID']))
	{
		$intOfferIBlockID = $arOffers['IBLOCK_ID'];
		$rsOfferIBlocks = CIBlock::GetByID($intOfferIBlockID);
		if (($arOfferIBlock = $rsOfferIBlocks->Fetch()))
		{
			$boolOffers = true;
			$rsProps = \CIBlockProperty::GetList(
				array('SORT' => 'ASC', 'NAME' => 'ASC'),
				array('IBLOCK_ID' => $intOfferIBlockID, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
			);
			while ($arProp = $rsProps->Fetch())
			{
				$arProp['ID'] = (int)$arProp['ID'];
				if ($arOffers['SKU_PROPERTY_ID'] != $arProp['ID'])
				{
					$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
					$arProp['CODE'] = (string)$arProp['CODE'];
					if ($arProp['CODE'] == '')
						$arProp['CODE'] = $arProp['ID'];
					$arProp['LINK_IBLOCK_ID'] = (int)$arProp['LINK_IBLOCK_ID'];

					$ar_iblock['OFFERS_PROPERTY'][$arProp['ID']] = $arProp;
					$arProperties[$arProp['ID']] = $arProp;
					if (in_array($arProp['PROPERTY_TYPE'], $arSelectedPropTypes))
						$arSelectOfferProps[] = $arProp['ID'];
				}
			}
			$arOfferIBlock['LID'] = $ar_iblock['LID'];
		}
		else
		{
			$arRunErrors[] = GetMessage('YANDEX_ERR_BAD_OFFERS_IBLOCK_ID');
		}
	}
	if ($boolOffers)
	{
		if (empty($XML_DATA['SKU_EXPORT']))
		{
			$arRunErrors[] = GetMessage('YANDEX_ERR_SKU_SETTINGS_ABSENT');
		}
		else
		{
			$arSKUExport = $XML_DATA['SKU_EXPORT'];;
			if (empty($arSKUExport['SKU_EXPORT_COND']) || !in_array($arSKUExport['SKU_EXPORT_COND'],$arOffersSelectKeys))
			{
				$arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_CONDITION_ABSENT');
			}
			if (YANDEX_SKU_EXPORT_PROP == $arSKUExport['SKU_EXPORT_COND'])
			{
				if (empty($arSKUExport['SKU_PROP_COND']) || !is_array($arSKUExport['SKU_PROP_COND']))
				{
					$arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_ABSENT');
				}
				else
				{
					if (empty($arSKUExport['SKU_PROP_COND']['PROP_ID']) || !in_array($arSKUExport['SKU_PROP_COND']['PROP_ID'],$arSelectOfferProps))
					{
						$arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_ABSENT');
					}
					if (empty($arSKUExport['SKU_PROP_COND']['COND']) || !in_array($arSKUExport['SKU_PROP_COND']['COND'],$arCondSelectProp))
					{
						$arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_COND_ABSENT');
					}
					else
					{
						if ($arSKUExport['SKU_PROP_COND']['COND'] == 'EQUAL' || $arSKUExport['SKU_PROP_COND']['COND'] == 'NONEQUAL')
						{
							if (empty($arSKUExport['SKU_PROP_COND']['VALUES']))
							{
								$arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_VALUES_ABSENT');
							}
						}
					}
				}
			}
		}
	}
}

$propertyIdList = array_keys($arProperties);
if (empty($arRunErrors))
{
	if (
		$arCatalog['CATALOG_TYPE'] == CCatalogSku::TYPE_FULL
		|| $arCatalog['CATALOG_TYPE'] == CCatalogSku::TYPE_PRODUCT
	)
		$propertyIdList[] = $arCatalog['SKU_PROPERTY_ID'];
}

$arUserTypeFormat = array();
foreach($arProperties as $key => $arProperty)
{
	$arUserTypeFormat[$arProperty['ID']] = false;
	if ($arProperty['USER_TYPE'] == '')
		continue;

	$arUserType = \CIBlockProperty::GetUserType($arProperty['USER_TYPE']);
	if (isset($arUserType['GetPublicViewHTML']))
	{
		$arUserTypeFormat[$arProperty['ID']] = $arUserType['GetPublicViewHTML'];
		$arProperties[$key]['PROPERTY_TYPE'] = 'USER_TYPE';
	}
}

$bAllSections = false;
$arSections = array();
if (empty($arRunErrors))
{
	if (is_array($V))
	{
		foreach ($V as $key => $value)
		{
			if (trim($value)=="0")
			{
				$bAllSections = true;
				break;
			}
			$value = (int)$value;
			if ($value > 0)
			{
				$arSections[] = $value;
			}
		}
	}

	if (!$bAllSections && empty($arSections))
	{
		$arRunErrors[] = GetMessage('YANDEX_ERR_NO_SECTION_LIST');
	}
}

$selectedPriceType = 0;
if (!empty($XML_DATA['PRICE']))
{
	$XML_DATA['PRICE'] = (int)$XML_DATA['PRICE'];
	if ($XML_DATA['PRICE'] > 0)
	{
		$rsCatalogGroups = CCatalogGroup::GetGroupsList(array('CATALOG_GROUP_ID' => $XML_DATA['PRICE'],'GROUP_ID' => 2));
		if (!($arCatalogGroup = $rsCatalogGroups->Fetch()))
		{
			$arRunErrors[] = GetMessage('YANDEX_ERR_BAD_PRICE_TYPE');
		}
		else
		{
			$selectedPriceType = $XML_DATA['PRICE'];
		}
	}
	else
	{
		$arRunErrors[] = GetMessage('YANDEX_ERR_BAD_PRICE_TYPE');
	}
}

$usedProtocol = (isset($USE_HTTPS) && $USE_HTTPS == 'Y' ? 'https://' : 'https://');
$filterAvailable = (isset($FILTER_AVAILABLE) && $FILTER_AVAILABLE == 'Y');
$disableReferers = (isset($DISABLE_REFERERS) && $DISABLE_REFERERS == 'Y');

$itemOptions = array(
	'PROTOCOL' => $usedProtocol,
	'SITE_NAME' => $ar_iblock['SERVER_NAME'],
	'MAX_DESCRIPTION_LENGTH' => 3000
);

$itemFileName = '';
if (strlen($SETUP_FILE_NAME) <= 0)
{
	$arRunErrors[] = GetMessage("CATI_NO_SAVE_FILE");
}
elseif (preg_match(BX_CATALOG_FILENAME_REG,$SETUP_FILE_NAME))
{
	$arRunErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
}
else
{
	$SETUP_FILE_NAME = Rel2Abs("/", $SETUP_FILE_NAME);
}
if (empty($arRunErrors))
{
/*	if ($GLOBALS["APPLICATION"]->GetFileAccessPermission($SETUP_FILE_NAME) < "W")
	{
		$arRunErrors[] = str_replace('#FILE#', $SETUP_FILE_NAME,GetMessage('YANDEX_ERR_FILE_ACCESS_DENIED'));
	} */
	$itemFileName = $SETUP_FILE_NAME.'_items';
}

$itemsFile = null;

$BASE_CURRENCY = Currency\CurrencyManager::getBaseCurrency();

if ($firstStep)
{
	if (empty($arRunErrors))
	{
		CheckDirPath($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME);

		if (!$fp = @fopen($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, "wb"))
		{
			$arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, GetMessage('YANDEX_ERR_FILE_OPEN_WRITING'));
		}
		else
		{
			if (!@fwrite($fp, '<? header("Content-Type: text/xml; charset=UTF-8");?><? echo "<"."?xml version=\"1.0\"?".">"?>'))
			{
				$arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, GetMessage('YANDEX_ERR_SETUP_FILE_WRITE'));
				@fclose($fp);
			}
			else
			{
				fwrite($fp, ' ');
			}
		}
	}

	if (empty($arRunErrors))
	{
		@fwrite($fp, "\n\n");
		@fwrite($fp, "<rss xmlns:g=\"http://base.google.com/ns/1.0\" version=\"2.0\">\n");
		@fwrite($fp, "<channel>\n");

		@fwrite($fp, "<title>".$APPLICATION->ConvertCharset(htmlspecialcharsbx(COption::GetOptionString("main", "site_name", "")), LANG_CHARSET, 'UTF-8')."</title>\n");

		
		@fwrite($fp, "<link>https://".htmlspecialcharsbx($ar_iblock['SERVER_NAME'])."</link>\n");
		@fwrite($fp, "<description>".$APPLICATION->ConvertCharset(htmlspecialcharsbx(COption::GetOptionString("main", "site_name", "")), LANG_CHARSET, 'UTF-8')."</description>\n");

		
		
		$strTmp = "<currencies>\n";

		$RUR = 'RUB';
		$currencyIterator = Currency\CurrencyTable::getList(array(
			'select' => array('CURRENCY'),
			'filter' => array('=CURRENCY' => 'RUR')
		));
		if ($currency = $currencyIterator->fetch())
			$RUR = 'RUR';
		unset($currency, $currencyIterator);

		$arCurrencyAllowed = array($RUR, 'USD', 'EUR', 'UAH', 'BYR', 'BYN', 'KZT');

		if (is_array($XML_DATA['CURRENCY']))
		{
			foreach ($XML_DATA['CURRENCY'] as $CURRENCY => $arCurData)
			{
				if (in_array($CURRENCY, $arCurrencyAllowed))
				{
					$strTmp .= '<currency id="'.$CURRENCY.'"'
						.' rate="'.($arCurData['rate'] == 'SITE' ? CCurrencyRates::ConvertCurrency(1, $CURRENCY, $RUR) : $arCurData['rate']).'"'
						.($arCurData['plus'] > 0 ? ' plus="'.(int)$arCurData['plus'].'"' : '')
						." />\n";
				}
			}
			unset($CURRENCY, $arCurData);
		}
		else
		{
			$currencyIterator = Currency\CurrencyTable::getList(array(
				'select' => array('CURRENCY', 'SORT'),
				'filter' => array('@CURRENCY' => $arCurrencyAllowed),
				'order' => array('SORT' => 'ASC', 'CURRENCY' => 'ASC')
			));
			while ($currency = $currencyIterator->fetch())
				$strTmp .= '<currency id="'.$currency['CURRENCY'].'" rate="'.(CCurrencyRates::ConvertCurrency(1, $currency['CURRENCY'], $RUR)).'" />'."\n";
			unset($currency, $currencyIterator);
		}
		$strTmp .= "</currencies>\n";

		//fwrite($fp, $strTmp);
		unset($strTmp);

		//*****************************************//


		//*****************************************//
		$intMaxSectionID = 0;

		$strTmpCat = '';
		$strTmpOff = '';

		$arSectionIDs = array();
		$arAvailGroups = array();
		if (!$bAllSections)
		{
			for ($i = 0, $intSectionsCount = count($arSections); $i < $intSectionsCount; $i++)
			{
				$sectionIterator = CIBlockSection::GetNavChain($IBLOCK_ID, $arSections[$i], array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'LEFT_MARGIN', 'RIGHT_MARGIN'));
				$curLEFT_MARGIN = 0;
				$curRIGHT_MARGIN = 0;
				while ($section = $sectionIterator->Fetch())
				{
					$section['ID'] = (int)$section['ID'];
					$section['IBLOCK_SECTION_ID'] = (int)$section['IBLOCK_SECTION_ID'];
					if ($arSections[$i] == $section['ID'])
					{
						$curLEFT_MARGIN = (int)$section['LEFT_MARGIN'];
						$curRIGHT_MARGIN = (int)$section['RIGHT_MARGIN'];
						$arSectionIDs[$section['ID']] = $section['ID'];
					}
					$arAvailGroups[$section['ID']] = array(
						'ID' => $section['ID'],
						'IBLOCK_SECTION_ID' => $section['IBLOCK_SECTION_ID'],
						'NAME' => $section['NAME']
					);
					if ($intMaxSectionID < $section['ID'])
						$intMaxSectionID = $section['ID'];
				}
				unset($section, $sectionIterator);

				$filter = array("IBLOCK_ID" => $IBLOCK_ID, ">LEFT_MARGIN" => $curLEFT_MARGIN, "<RIGHT_MARGIN" => $curRIGHT_MARGIN, "ACTIVE" => "Y", "IBLOCK_ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y");
				$sectionIterator = CIBlockSection::GetList(array("LEFT_MARGIN" => "ASC"), $filter, false, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
				while ($section = $sectionIterator->Fetch())
				{
					$section['ID'] = (int)$section['ID'];
					$section['IBLOCK_SECTION_ID'] = (int)$section['IBLOCK_SECTION_ID'];
					$arAvailGroups[$section['ID']] = $section;
					if ($intMaxSectionID < $section['ID'])
						$intMaxSectionID = $section['ID'];
				}
				unset($section, $sectionIterator);
			}
		}
		else
		{
			$filter = array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "IBLOCK_ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y");
			$sectionIterator = CIBlockSection::GetList(array("LEFT_MARGIN" => "ASC"), $filter, false, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
			while ($section = $sectionIterator->Fetch())
			{
				$section['ID'] = (int)$section['ID'];
				$section['IBLOCK_SECTION_ID'] = (int)$section['IBLOCK_SECTION_ID'];
				$arAvailGroups[$section['ID']] = $section;
				$arSectionIDs[$section['ID']] = $section['ID'];
				if ($intMaxSectionID < $section['ID'])
					$intMaxSectionID = $section['ID'];
			}
			unset($section, $sectionIterator);
		}

		foreach ($arAvailGroups as $value)
			$strTmpCat .= '<category id="'.$value['ID'].'"'.($value['IBLOCK_SECTION_ID'] > 0 ? ' parentId="'.$value['IBLOCK_SECTION_ID'].'"' : '').'>'.yandex_text2xml($value['NAME'], true).'</category>'."\n";
		unset($value);

		$intMaxSectionID += 100000000;
	}

	//fwrite($fp, "<categories>\n");
	//fwrite($fp, $strTmpCat);
	fclose($fp);
	unset($strTmpCat);

	$boolNeedRootSection = false;

	$itemsFile = @fopen($_SERVER["DOCUMENT_ROOT"].$itemFileName, 'wb');
	if (!$itemsFile)
	{
		$arRunErrors[] = str_replace('#FILE#', $_SERVER['DOCUMENT_ROOT'].$itemFileName, GetMessage('YANDEX_ERR_FILE_OPEN_WRITING'));
	}
}
else
{
	$itemsFile = @fopen($_SERVER["DOCUMENT_ROOT"].$itemFileName, 'ab');
	if (!$itemsFile)
	{
		$arRunErrors[] = str_replace('#FILE#', $_SERVER['DOCUMENT_ROOT'].$itemFileName, GetMessage('YANDEX_ERR_FILE_OPEN_WRITING'));
	}
}

if (empty($arRunErrors))
{
	//*****************************************//
	\CCatalogProduct::setPriceVatIncludeMode(true);
	\CCatalogProduct::setUsedCurrency($BASE_CURRENCY);
	\CCatalogProduct::setUseDiscount(true);

	if ($selectedPriceType > 0)
	{
		$priceTypeList = array($selectedPriceType);
	}
	else
	{
		$priceTypeList = array();
		$priceIterator = Catalog\GroupAccessTable::getList(array(
			'select' => array('CATALOG_GROUP_ID'),
			'filter' => array('@GROUP_ID' => 2),
			'order' => array('CATALOG_GROUP_ID' => 'ASC')
		));
		while ($priceType = $priceIterator->fetch())
		{
			$priceTypeId = (int)$priceType['CATALOG_GROUP_ID'];
			$priceTypeList[$priceTypeId] = $priceTypeId;
			unset($priceTypeId);
		}
		unset($priceType, $priceIterator);
	}

	$needDiscountCache = \CIBlockPriceTools::SetCatalogDiscountCache($priceTypeList, array(2), $ar_iblock['LID']);

	$itemFields = array(
		'ID', 'LID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME',
		'PREVIEW_PICTURE', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL',
		'CATALOG_AVAILABLE', 'CATALOG_TYPE'
	);
	$offerFields = array(
		'ID', 'LID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME',
		'PREVIEW_PICTURE', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL'
	);

	$allowedTypes = array();
	switch ($arCatalog['CATALOG_TYPE'])
	{
		case CCatalogSku::TYPE_CATALOG:
			$allowedTypes = array(
				Catalog\ProductTable::TYPE_PRODUCT => true,
				Catalog\ProductTable::TYPE_SET => true
			);
			break;
		case CCatalogSku::TYPE_OFFERS:
			$allowedTypes = array(
				Catalog\ProductTable::TYPE_OFFER => true
			);
			break;
		case CCatalogSku::TYPE_FULL:
			$allowedTypes = array(
				Catalog\ProductTable::TYPE_PRODUCT => true,
				Catalog\ProductTable::TYPE_SET => true,
				Catalog\ProductTable::TYPE_SKU => true
			);
			break;
		case CCatalogSku::TYPE_PRODUCT:
			$allowedTypes = array(
				Catalog\ProductTable::TYPE_SKU => true
			);
			break;
	}

	$filter = array('IBLOCK_ID' => $IBLOCK_ID);
	if (!$bAllSections && !empty($arSectionIDs))
	{
		$filter['INCLUDE_SUBSECTIONS'] = 'Y';
		$filter['SECTION_ID'] = $arSectionIDs;
	}
	$filter['ACTIVE'] = 'Y';
	$filter['ACTIVE_DATE'] = 'Y';
	if ($filterAvailable)
		$filter['CATALOG_AVAILABLE'] = 'Y';

	$offersFilter = array('ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y');
	if ($filterAvailable)
		$offersFilter['CATALOG_AVAILABLE'] = 'Y';
	if (isset($allowedTypes[Catalog\ProductTable::TYPE_SKU]))
	{
		if ($arSKUExport['SKU_EXPORT_COND'] == YANDEX_SKU_EXPORT_PROP)
		{
			$strExportKey = '';
			$mxValues = false;
			if ($arSKUExport['SKU_PROP_COND']['COND'] == 'NONZERO' || $arSKUExport['SKU_PROP_COND']['COND'] == 'NONEQUAL')
				$strExportKey = '!';
			$strExportKey .= 'PROPERTY_'.$arSKUExport['SKU_PROP_COND']['PROP_ID'];
			if ($arSKUExport['SKU_PROP_COND']['COND'] == 'EQUAL' || $arSKUExport['SKU_PROP_COND']['COND'] == 'NONEQUAL')
				$mxValues = $arSKUExport['SKU_PROP_COND']['VALUES'];
			$offersFilter[$strExportKey] = $mxValues;
		}
	}

	do
	{
		if (isset($CUR_ELEMENT_ID) && $CUR_ELEMENT_ID > 0)
			$filter['>ID'] = $CUR_ELEMENT_ID;

		$existItems = false;

		$itemIdsList = array();
		$items = array();

		$skuIdsList = array();
		$simpleIdsList = array();

		$iterator = CIBlockElement::GetList(
			array('ID' => 'ASC'),
			$filter,
			false,
			$navParams,
			$itemFields
		);
		while ($row = $iterator->Fetch())
		{
			$finalExport = false; // items exist
			$existItems = true;

			$id = (int)$row['ID'];
			$CUR_ELEMENT_ID = $id;

			$row['CATALOG_TYPE'] = (int)$row['CATALOG_TYPE'];
			$elementType = $row['CATALOG_TYPE'];
			if (!isset($allowedTypes[$elementType]))
				continue;

			$row['SECTIONS'] = array();
			if ($needProperties || $needDiscountCache)
				$row['PROPERTIES'] = array();
			$row['PRICES'] = array();

			$items[$id] = $row;
			$itemIdsList[$id] = $id;

			if ($elementType == Catalog\ProductTable::TYPE_SKU)
				$skuIdsList[$id] = $id;
			else
				$simpleIdsList[$id] = $id;
		}
		unset($row, $iterator);

		if (!empty($items))
		{
			yandexPrepareItems($items, $itemOptions);

			foreach (array_chunk($itemIdsList, 500) as $pageIds)
			{
				$iterator = Iblock\SectionElementTable::getList(array(
					'select' => array('IBLOCK_ELEMENT_ID', 'IBLOCK_SECTION_ID'),
					'filter' => array('@IBLOCK_ELEMENT_ID' => $pageIds, '==ADDITIONAL_PROPERTY_ID' => null),
					'order' => array('IBLOCK_ELEMENT_ID' => 'ASC')
				));
				while ($row = $iterator->fetch())
				{
					$id = (int)$row['IBLOCK_ELEMENT_ID'];
					$sectionId = (int)$row['IBLOCK_SECTION_ID'];
					$items[$id]['SECTIONS'][$sectionId] = $sectionId;
					unset($sectionId, $id);
				}
				unset($row, $iterator);
			}
			unset($pageIds);

			if ($needProperties || $needDiscountCache)
			{
				if (!empty($propertyIdList))
				{
					\CIBlockElement::GetPropertyValuesArray(
						$items,
						$IBLOCK_ID,
						array(
							'ID' => $itemIdsList,
							'IBLOCK_ID' => $IBLOCK_ID
						),
						array('ID' => $propertyIdList),
						array('USE_PROPERTY_ID' => 'Y')
					);
				}

				if ($needDiscountCache)
				{
					foreach ($itemIdsList as $id)
					{
						\CCatalogDiscount::SetProductPropertiesCache($id, $items[$id]['PROPERTIES']);
					}
					unset($id);
				}

				if (!$needProperties)
				{
					foreach ($itemIdsList as $id)
						$items[$id]['PROPERTIES'] = array();
					unset($id);
				}
				else
				{
					foreach ($itemIdsList as $id)
					{
						if (empty($items[$id]['PROPERTIES']))
							continue;
						foreach (array_keys($items[$id]['PROPERTIES']) as $index)
						{
							$propertyId = $items[$id]['PROPERTIES'][$index]['ID'];
							if (!isset($yandexNeedPropertyIds[$propertyId]))
							{
								unset($items[$id]['PROPERTIES'][$index]);
							}
							else
							{
								$items[$id]['PROPERTIES'][$index] = array_intersect_key(
									$items[$id]['PROPERTIES'][$index],
									$whiteList
								);
							}
							unset($propertyId);
						}
						unset($index);
					}
					unset($id);
				}
			}

			if ($needDiscountCache)
			{
				\CCatalogDiscount::SetProductSectionsCache($itemIdsList);
				\CCatalogDiscount::SetDiscountProductCache($itemIdsList, array('IBLOCK_ID' => $IBLOCK_ID, 'GET_BY_ID' => 'Y'));
			}

			if (!empty($skuIdsList))
			{
				$offerPropertyFilter = array();
				if ($needProperties || $needDiscountCache)
				{
					if (!empty($propertyIdList))
						$offerPropertyFilter = array('ID' => $propertyIdList);
				}

				$offers = \CCatalogSku::getOffersList(
					$skuIdsList,
					$IBLOCK_ID,
					$offersFilter,
					$offerFields,
					$offerPropertyFilter
				);
				unset($offerPropertyFilter);

				if (!empty($offers))
				{
					$offerLinks = array();
					$offerIdsList = array();
					foreach (array_keys($offers) as $productId)
					{
						unset($skuIdsList[$productId]);
						$items[$productId]['OFFERS'] = array();
						foreach (array_keys($offers[$productId]) as $offerId)
						{
							$productOffer = $offers[$productId][$offerId];

							$productOffer['PRICES'] = array();
							if ($needDiscountCache)
								\CCatalogDiscount::SetProductPropertiesCache($offerId, $productOffer['PROPERTIES']);
							if (!$needProperties)
							{
								$productOffer['PROPERTIES'] = array();
							}
							else
							{
								if (!empty($productOffer['PROPERTIES']))
								{
									foreach (array_keys($productOffer['PROPERTIES']) as $index)
									{
										$propertyId = $productOffer['PROPERTIES'][$index]['ID'];
										if (!isset($yandexNeedPropertyIds[$propertyId]))
										{
											unset($productOffer['PROPERTIES'][$index]);
										}
										else
										{
											$productOffer['PROPERTIES'][$index] = array_intersect_key(
												$productOffer['PROPERTIES'][$index],
												$whiteList
											);
										}
										unset($propertyId);
									}
									unset($index);
								}
							}
							$items[$productId]['OFFERS'][$offerId] = $productOffer;
							unset($productOffer);

							$offerLinks[$offerId] = &$items[$productId]['OFFERS'][$offerId];
							$offerIdsList[$offerId] = $offerId;
						}
						unset($offerId);
					}
					if (!empty($offerIdsList))
					{
						yandexPrepareItems($offerLinks, $itemOptions);

						foreach (array_chunk($offerIdsList, 500) as $pageIds)
						{
							if ($needDiscountCache)
							{
								\CCatalogDiscount::SetProductSectionsCache($pageIds);
								\CCatalogDiscount::SetDiscountProductCache(
									$pageIds,
									array('IBLOCK_ID' => $arCatalog['IBLOCK_ID'], 'GET_BY_ID' => 'Y')
								);
							}

							if (!$filterAvailable)
							{
								$iterator = Catalog\ProductTable::getList(array(
									'select' => array('ID', 'AVAILABLE'),
									'filter' => array('@ID' => $pageIds)
								));
								while ($row = $iterator->fetch())
								{
									$id = (int)$row['ID'];
									$offerLinks[$id]['CATALOG_AVAILABLE'] = $row['AVAILABLE'];
								}
								unset($id, $row, $iterator);
							}

							$priceFilter = array(
								'@PRODUCT_ID' => $pageIds,
								'+<=QUANTITY_FROM' => 1,
								'+>=QUANTITY_TO' => 1,
							);
							if ($selectedPriceType > 0)
								$priceFilter['CATALOG_GROUP_ID'] = $selectedPriceType;
							else
								$priceFilter['@CATALOG_GROUP_ID'] = $priceTypeList;

							$priceIterator = \CPrice::GetListEx(
								array(),
								$priceFilter,
								false,
								false,
								array('ID', 'PRODUCT_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY')
							);
							while ($price = $priceIterator->Fetch())
							{
								$id = (int)$price['PRODUCT_ID'];
								$priceTypeId = (int)$price['CATALOG_GROUP_ID'];
								$offerLinks[$id]['PRICES'][$priceTypeId] = $price;
								unset($priceTypeId, $id);
							}
							unset($price, $priceIterator);
						}
						unset($pageIds);
					}

					unset($offerIdsList, $offerLinks);
				}
				unset($offers);

				if (!empty($skuIdsList))
				{
					foreach ($skuIdsList as $id)
					{
						unset($items[$id]);
						unset($itemIdsList[$id]);
					}
					unset($id);
				}
			}

			if (!empty($simpleIdsList))
			{
				foreach (array_chunk($simpleIdsList, 500) as $pageIds)
				{
					$priceFilter = array(
						'@PRODUCT_ID' => $pageIds,
						'+<=QUANTITY_FROM' => 1,
						'+>=QUANTITY_TO' => 1,
					);
					if ($selectedPriceType > 0)
						$priceFilter['CATALOG_GROUP_ID'] = $selectedPriceType;
					else
						$priceFilter['@CATALOG_GROUP_ID'] = $priceTypeList;

					$priceIterator = \CPrice::GetListEx(
						array(),
						$priceFilter,
						false,
						false,
						array('ID', 'PRODUCT_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY')
					);
					while ($price = $priceIterator->Fetch())
					{
						$id = (int)$price['PRODUCT_ID'];
						$priceTypeId = (int)$price['CATALOG_GROUP_ID'];
						$items[$id]['PRICES'][$priceTypeId] = $price;
						unset($priceTypeId, $id);
					}
					unset($price, $priceIterator);
				}
				unset($pageIds);
			}
		}

		$itemsContent = '';
		if (!empty($items))
		{
			foreach ($itemIdsList as $id)
			{
				$CUR_ELEMENT_ID = $id;

				$row = $items[$id];

				if (!empty($row['SECTIONS']))
				{
					foreach ($row['SECTIONS'] as $sectionId)
					{
						if (!isset($arAvailGroups[$sectionId]))
							continue;
						$row['CATEGORY_ID'] = $sectionId;
					}
					unset($sectionId);
				}
				else
				{
					$boolNeedRootSection = true;
					$row['CATEGORY_ID'] = $intMaxSectionID;
				}
				if (!isset($row['CATEGORY_ID']))
					continue;

				if ($row['CATALOG_TYPE'] == Catalog\ProductTable::TYPE_SKU && !empty($row['OFFERS']))
				{
					$minOfferId = null;
					$minOfferPrice = null;

					foreach (array_keys($row['OFFERS']) as $offerId)
					{
						if (empty($row['OFFERS'][$offerId]['PRICES']))
						{
							unset($row['OFFERS'][$offerId]);
							continue;
						}

						$fullPrice = 0;
						$minPrice = 0;
						$minPriceCurrency = '';

						$calculatePrice = CCatalogProduct::GetOptimalPrice(
							$row['OFFERS'][$offerId]['ID'],
							1,
							array(2),
							'N',
							$row['OFFERS'][$offerId]['PRICES'],
							$ar_iblock['LID'],
							array()
						);

						if (!empty($calculatePrice))
						{
							$minPrice = $calculatePrice['RESULT_PRICE']['DISCOUNT_PRICE'];
							$fullPrice = $calculatePrice['RESULT_PRICE']['BASE_PRICE'];
							$minPriceCurrency = $calculatePrice['RESULT_PRICE']['CURRENCY'];
						}
						unset($calculatePrice);
						if ($minPrice <= 0)
						{
							unset($row['OFFERS'][$offerId]);
							continue;
						}
						$row['OFFERS'][$offerId]['RESULT_PRICE'] = array(
							'MIN_PRICE' => $minPrice,
							'FULL_PRICE' => $fullPrice,
							'CURRENCY' => $minPriceCurrency
						);
						if ($minOfferPrice === null || $minOfferPrice > $minPrice)
						{
							$minOfferId = $offerId;
							$minOfferPrice = $minPrice;
						}
					}
					unset($offerId);

					if ($arSKUExport['SKU_EXPORT_COND'] == YANDEX_SKU_EXPORT_MIN_PRICE)
					{
						if ($minOfferId === null)
							$row['OFFERS'] = array();
						else
							$row['OFFERS'] = array($minOfferId => $row['OFFERS'][$minOfferId]);
					}
					if (empty($row['OFFERS']))
						continue;

					foreach ($row['OFFERS'] as $offer)
					{
						$str_AVAILABLE = ($offer['CATALOG_AVAILABLE'] == 'Y' ? 'in stock' : 'preorder');
						
						$itemsContent .= "<item>\n";
						
						
						$itemsContent .= "<link>".$usedProtocol.$ar_iblock['SERVER_NAME'].htmlspecialcharsbx($offer['DETAIL_PAGE_URL'])."?id=".$offer['ID']."</link>\n";
						

						$minPrice = $offer['RESULT_PRICE']['MIN_PRICE'];
						$fullPrice = $offer['RESULT_PRICE']['FULL_PRICE'];
						
						if($minPrice == $fullPrice)
						{
							$itemsContent .= "<g:price>".$minPrice." ".$offer['RESULT_PRICE']['CURRENCY']."</g:price>\n";	
						}
						else
						{
							$itemsContent .= "<g:price>".$fullPrice." ".$offer['RESULT_PRICE']['CURRENCY']."</g:price>\n";	
							$itemsContent .= "<g:sale_price>".$minPrice." ".$offer['RESULT_PRICE']['CURRENCY']."</g:sale_price>\n";
						}

						$itemsContent .= "<g:id>".$offer['ID']."</g:id>\n";
						
						$itemsContent .= "<g:condition>"."New"."</g:condition>\n";
						$itemsContent .= "<g:availability>".$str_AVAILABLE."</g:availability>\n";
						
						$itemsContent .= "<g:custom_label_0>".str_replace('.php_items', '', str_replace('/bitrix/catalog_export/', '', $itemFileName))."</g:custom_label_0>\n";

						$picture = (!empty($offer['PICTURE']) ? $offer['PICTURE'] : $row['PICTURE']);
						if (!empty($picture))
							$itemsContent .= "<g:image_link>".$picture."</g:image_link>\n";
						unset($picture);

						$y = 0;
						foreach ($arYandexFields as $key)
						{
							switch ($key)
							{
								case 'name':
									if ($yandexFormat == 'merchant' || $yandexFormat == 'artist.title')
										continue;

									$itemsContent .= "<title>".yandex_text2xml($offer['NAME'], true)."</title>\n";
									break;
								case 'description':
									$itemsContent .= "<description>";
									if($offer['DESCRIPTION'] !== '' || $row['DESCRIPTION'] !== '')
									{
										$itemsContent .= $offer['DESCRIPTION'] !== '' ? $offer['DESCRIPTION'] : $row['DESCRIPTION'];
									}
									else
									{
										$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($IBLOCK_ID, $offer['ID']); 
										$arElMetaProp = $ipropValues->getValues();
										
										$metaDescription = yandex_text2xml(TruncateText((preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arElMetaProp["ELEMENT_META_DESCRIPTION"])),255), true);
										
										$itemsContent .= $metaDescription;
									}
									$itemsContent .= "</description>\n";
									break;
								case 'param':
									if ($parametricFieldsExist)
									{
										foreach ($parametricFields as $paramKey => $prop_id)
										{
											$value = yandex_get_value(
												$offer,
												'PARAM_'.$paramKey,
												$prop_id,
												$arProperties,
												$arUserTypeFormat,
												$usedProtocol
											);
											if ($value == '')
											{
												$value = yandex_get_value(
													$row,
													'PARAM_'.$paramKey,
													$prop_id,
													$arProperties,
													$arUserTypeFormat,
													$usedProtocol
												);
											}
											if ($value != '')
												$itemsContent .= $value."\n";
											unset($value);
										}
										unset($paramKey, $prop_id);
									}
									break;
								case 'model':
								case 'title':
									if (!$fieldsExist || !isset($fields[$key]))
									{
										if (
											$key == 'title' && $yandexFormat == 'merchant' // vmesto model title
											||
											$key == 'title' && $yandexFormat == 'artist.title'
										)
											$itemsContent .= "<".$key.">".yandex_text2xml($offer['NAME'], true)."</".$key.">\n";
									}
									else
									{
										$value = yandex_get_value(
											$offer,
											$key,
											$fields[$key],
											$arProperties,
											$arUserTypeFormat,
											$usedProtocol
										);
										if ($value == '')
										{
											$value = yandex_get_value(
												$row,
												$key,
												$fields[$key],
												$arProperties,
												$arUserTypeFormat,
												$usedProtocol
											);
										}
										if ($value != '')
											$itemsContent .= $value."\n";
										unset($value);
									}
									break;
								case 'year':
								default:
									if ($key == 'year')
									{
										$y++;
										if ($yandexFormat == 'artist.title')
										{
											if ($y == 1)
												continue;
										}
										else
										{
											if ($y > 1)
												continue;
										}
									}
									if ($fieldsExist && isset($fields[$key]))
									{
										$value = yandex_get_value(
											$offer,
											$key,
											$fields[$key],
											$arProperties,
											$arUserTypeFormat,
											$usedProtocol
										);
										if ($value == '')
										{
											$value = yandex_get_value(
												$row,
												$key,
												$fields[$key],
												$arProperties,
												$arUserTypeFormat,
												$usedProtocol
											);
										}
										if ($value != '')
											$itemsContent .= $value."\n";
										unset($value);
									}
							}
						}

						$itemsContent .= '</item>'."\n";
					}
					unset($offer);
				}
				elseif (isset($simpleIdsList[$id]) && !empty($row['PRICES']))
				{
					$fullPrice = 0;
					$minPrice = 0;
					$minPriceCurrency = '';

					$calculatePrice = CCatalogProduct::GetOptimalPrice(
						$row['ID'],
						1,
						array(2),
						'N',
						$row['PRICES'],
						$ar_iblock['LID'],
						array()
					);

					if (!empty($calculatePrice))
					{
						$minPrice = $calculatePrice['RESULT_PRICE']['DISCOUNT_PRICE'];
						$fullPrice = $calculatePrice['RESULT_PRICE']['BASE_PRICE'];
						$minPriceCurrency = $calculatePrice['RESULT_PRICE']['CURRENCY'];
					}
					unset($calculatePrice);

					if ($minPrice <= 0)
						continue;

					$str_AVAILABLE = ($row['CATALOG_AVAILABLE'] == 'Y' ? 'in stock' : 'preorder');
					
					$itemsContent .= "<item>\n";
					

					$itemsContent .= "<link>".$usedProtocol.$ar_iblock['SERVER_NAME'].htmlspecialcharsbx($row['DETAIL_PAGE_URL'])."</link>\n";
					unset($referer);

						if($minPrice == $fullPrice)
						{
							$itemsContent .= "<g:price>".$minPrice." ".$minPriceCurrency."</g:price>\n";	
						}
						else
						{
							$itemsContent .= "<g:price>".$fullPrice." ".$minPriceCurrency."</g:price>\n";	
							$itemsContent .= "<g:sale_price>".$minPrice." ".$minPriceCurrency."</g:sale_price>\n";
						}

						$itemsContent .= "<g:id>".$row['ID']."</g:id>\n";
						
						$itemsContent .= "<g:condition>"."New"."</g:condition>\n";
						$itemsContent .= "<g:availability>".$str_AVAILABLE."</g:availability>\n";
						
						$itemsContent .= "<g:custom_label_0>".str_replace('.php_items', '', str_replace('/bitrix/catalog_export/', '', $itemFileName))."</g:custom_label_0>\n";
					
					if (!empty($row['PICTURE']))
						$itemsContent .= "<g:image_link>".$row['PICTURE']."</g:image_link>\n";

					$y = 0;
					foreach ($arYandexFields as $key)
					{
						switch ($key)
						{
							case 'name':
								if ($yandexFormat == 'merchant' || $yandexFormat == 'artist.title')
									continue;

								$itemsContent .= "<title>".yandex_text2xml($row['NAME'], true)."</title>\n";
								break;
							case 'description':
								$itemsContent .= "<description>";
								if($row['DESCRIPTION'] !== '')
								{
									$itemsContent .= $row['DESCRIPTION'];
								}
								else
								{
									$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($IBLOCK_ID, $row['ID']); 
									$arElMetaProp = $ipropValues->getValues();
									
									$metaDescription = yandex_text2xml(TruncateText((preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arElMetaProp["ELEMENT_META_DESCRIPTION"])),255), true);
									
									$itemsContent .= $metaDescription;
								}
								$itemsContent .= "</description>\n";
									
								break;
							case 'param':
								if ($parametricFieldsExist)
								{
									foreach ($parametricFields as $paramKey => $prop_id)
									{
										$value = yandex_get_value(
											$row,
											'PARAM_'.$paramKey,
											$prop_id,
											$arProperties,
											$arUserTypeFormat,
											$usedProtocol
										);
										if ($value != '')
											$itemsContent .= $value."\n";
										unset($value);
									}
									unset($paramKey, $prop_id);
								}
								break;
							case 'model':
							case 'title':
								if (!$fieldsExist || !isset($fields[$key]))
								{
									if (
										$key == 'title' && $yandexFormat == 'merchant'
										||
										$key == 'title' && $yandexFormat == 'artist.title'
									)
										$itemsContent .= "<".$key.">".yandex_text2xml($row['NAME'], true)."</".$key.">\n";
								}
								else
								{
									$value = yandex_get_value(
										$row,
										$key,
										$fields[$key],
										$arProperties,
										$arUserTypeFormat,
										$usedProtocol
									);
									if ($value != '')
										$itemsContent .= $value."\n";
									unset($value);
								}
								break;
							case 'year':
							default:
								if ($key == 'year')
								{
									$y++;
									if ($yandexFormat == 'artist.title')
									{
										if ($y == 1)
											continue;
									}
									else
									{
										if ($y > 1)
											continue;
									}
								}
								if ($fieldsExist && isset($fields[$key]))
								{
									$value = yandex_get_value(
										$row,
										$key,
										$fields[$key],
										$arProperties,
										$arUserTypeFormat,
										$usedProtocol
									);
									if ($value != '')
										$itemsContent .= $value."\n";
									unset($value);
								}
						}
					}

					$itemsContent .= "</item>\n";
				}

				unset($row);

				if ($MAX_EXECUTION_TIME > 0 && (getmicrotime() - START_EXEC_TIME) >= $MAX_EXECUTION_TIME)
					break;
			}
			unset($id);

			\CCatalogDiscount::ClearDiscountCache(array(
				'PRODUCT' => true,
				'SECTIONS' => true,
				'PROPERTIES' => true
			));
		}

		if ($itemsContent !== '')
			fwrite($itemsFile, $itemsContent);
		unset($itemsContent);

		unset($simpleIdsList, $skuIdsList);
		unset($items, $itemIdsList);
	}
	while ($MAX_EXECUTION_TIME == 0 && $existItems);
}

if (empty($arRunErrors))
{
	if (is_resource($itemsFile))
		@fclose($itemsFile);
	unset($itemsFile);
}

if (empty($arRunErrors))
{
	if ($MAX_EXECUTION_TIME == 0)
		$finalExport = true;
	if ($finalExport)
	{
		$content = '';
		/*if ($boolNeedRootSection)
			$content .= '<category id="'.$intMaxSectionID.'">'.yandex_text2xml(GetMessage('YANDEX_ROOT_DIRECTORY'), true).'</category>'."\n";
		$content .= "</categories>\n";
		$content .= "<offers>\n";*/

		$items = file_get_contents($_SERVER["DOCUMENT_ROOT"].$itemFileName);
		if ($items === false)
		{
			$arRunErrors[] = GetMessage('YANDEX_STEP_ERR_DATA_FILE_NOT_READ');
		}
		else
		{
			$content .= $items;
			unset($items);
			$content .= "</channel>\n"."</rss>\n";

			file_put_contents($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, $content, FILE_APPEND);
		}
		unlink($_SERVER["DOCUMENT_ROOT"].$itemFileName);
	}
}

CCatalogDiscountSave::Enable();

if (!empty($arRunErrors))
	$strExportErrorMessage = implode('<br />',$arRunErrors);

if ($bTmpUserCreated)
{
	if (isset($USER_TMP))
	{
		$USER = $USER_TMP;
		unset($USER_TMP);
	}
}