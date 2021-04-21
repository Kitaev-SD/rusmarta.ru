<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?\Bitrix\Main\Loader::includeModule('iblock');
$arTabs = $arShowProp = array();
global $USER;

$arResult["SHOW_SLIDER_PROP"] = false;
if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arFilter = array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"]);
if($arParams["SECTION_ID"])
	$arFilter[]=array("SECTION_ID" => $arParams["SECTION_ID"], "INCLUDE_SUBSECTIONS" => "Y");
elseif($arParams["SECTION_CODE"])
	$arFilter[]=array("SECTION_CODE" => $arParams["SECTION_CODE"], "INCLUDE_SUBSECTIONS" => "Y");

global $arTheme, $isShowCatalogElements;
$bCatalogIndex = $isShowCatalogElements;

global $arTheme;
$arParams["SET_SKU_TITLE"] = ($arTheme["CHANGE_TITLE_ITEM"]["VALUE"] == "Y" ? "Y" : "");
$arParams["DISPLAY_TYPE"] = "block";

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

if($arParams["SHOW_BUY_BTN"] == "Y")
	$arParams['OFFER_TREE_PROPS'] = $arParams['OFFERS_PROPERTY_CODE'];
else
	$arParams['OFFER_TREE_PROPS'] = array();

if($arParams['OFFER_TREE_PROPS'])
{
	$keys = array_search('ARTICLE', $arParams['OFFER_TREE_PROPS']);
	if(false !== $keys)
		unset($arParams['OFFER_TREE_PROPS'][$keys]);
}


if(!in_array('DETAIL_PAGE_URL', $arParams['OFFERS_FIELD_CODE']))
	$arParams['OFFERS_FIELD_CODE'][] = 'DETAIL_PAGE_URL';
if(!in_array('NAME', $arParams['OFFERS_FIELD_CODE']))
	$arParams['OFFERS_FIELD_CODE'][] = 'NAME';

if($bCatalogIndex)
{
	$arShowProp = CNextCache::CIBlockPropertyEnum_GetList(Array("sort" => "asc", "id" => "desc", "CACHE" => array("TAG" => CNextCache::GetPropertyCacheTag($arParams["TABS_CODE"]))), Array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => $arParams["TABS_CODE"]));

	if($arShowProp)
	{
		if($arParams['STORES'])
		{
			foreach($arParams['STORES'] as $key => $store)
			{
				if(!$store)
					unset($arParams['STORES'][$key]);
			}
		}
		global $arRegion;
		$arFilterStores = array();
		if($arRegion)
		{
			$arParams['USE_REGION'] = 'Y';
			if($arRegion['LIST_PRICES'])
			{
				if(reset($arRegion['LIST_PRICES']) != 'component')
				{
					$arParams['PRICE_CODE'] = array_keys($arRegion['LIST_PRICES']);
					$arParams['~PRICE_CODE'] = array_keys($arRegion['LIST_PRICES']);
				}
			}
			if($arRegion['LIST_STORES'])
			{
				if(reset($arRegion['LIST_STORES']) != 'component')
				{
					$arParams['STORES'] = $arRegion['LIST_STORES'];
					$arParams['~STORES'] = $arRegion['LIST_STORES'];
				}

				if($arParams["HIDE_NOT_AVAILABLE"] == "Y")
				{
					$arTmpFilter["LOGIC"] = "OR";
					foreach($arParams['STORES'] as $storeID)
					{
						$arTmpFilter[] = array(">CATALOG_STORE_AMOUNT_".$storeID => 0);
					}
					$arFilterStores[] = $arTmpFilter;
				}
			}
		}
		
		foreach($arShowProp as $key => $prop)
		{
			$arItems = array();
			$arFilterProp = array("PROPERTY_".$arParams["TABS_CODE"]."_VALUE" => array($prop));

			$arItems = CNextCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" => "N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array_merge($arFilter, $arrFilter, $arrFilter, $arFilterProp), false, array("nTopCount" => 1), array("ID"));
			if($arItems)
			{
				$arTabs[$key] = array(
					"CODE" => $key,
					"TITLE" => $prop,
					"FILTER" => array_merge($arFilterProp, $arFilter, $arrFilter, $arFilterStores)
				);
				$arResult["SHOW_SLIDER_PROP"] = true;
			}
		}
	}
	else
	{
		return;
	}
	$arParams["PROP_CODE"] = $arParams["TABS_CODE"];
	$arResult["TABS"] = $arTabs;

	$arTransferParams = array(
		"SHOW_ABSENT" => $arParams["SHOW_ABSENT"],
		"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"LIST_OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"LIST_OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
		"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
		"SHOW_COUNTER_LIST" => $arParams["SHOW_COUNTER_LIST"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
		"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
		"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
		"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
		"USE_REGION" => $arParams["USE_REGION"],
		"STORES" => $arParams["STORES"],
		"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
		"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
		"ADD_PROPERTIES_TO_BASKET" => ($arParams["ADD_PROPERTIES_TO_BASKET"] != "N" ? "Y" : "N"),
		"SHOW_DISCOUNT_TIME_EACH_SKU" => $arParams["SHOW_DISCOUNT_TIME_EACH_SKU"],
		"SHOW_ARTICLE_SKU" => $arParams["SHOW_ARTICLE_SKU"],
		"OFFER_ADD_PICT_PROP" => ($arParams["ADD_PROPERTIES_TO_BASKET"] != "N" ? "Y" : "N"),
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
	);
	?>
	<div class="js_wrapper_items" data-params='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arTransferParams, false))?>'>
		<?$this->IncludeComponentTemplate();?>
	</div>
<?}
else
	return;?>