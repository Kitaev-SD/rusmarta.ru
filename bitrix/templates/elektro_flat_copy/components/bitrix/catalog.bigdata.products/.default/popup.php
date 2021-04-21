<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Loader,
	Bitrix\Main\Application;

if(!Loader::includeModule("catalog"))
	return;

$request = Application::getInstance()->getContext()->getRequest();

if($request->isPost() && check_bitrix_sessid()) {
	$action = $request->getPost("action");	
	$arParams = $request->getPost("arParams");
	
	switch($action) {
		case "ask_price":
			//ASK_PRICE//
			global $arAskPriceFilter;
			$arAskPriceFilter = array(
				"ELEMENT_ID" => $arParams["ELEMENT_ID"],
				"ELEMENT_AREA_ID" => $arParams["ELEMENT_AREA_ID"],
				"ELEMENT_NAME" => $arParams["ELEMENT_NAME"]
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));?>
			<?break;		
		case "under_order":
			//UNDER_ORDER//
			global $arUnderOrderFilter;
			$arUnderOrderFilter = array(
				"ELEMENT_ID" => $arParams["ELEMENT_ID"],
				"ELEMENT_AREA_ID" => $arParams["ELEMENT_AREA_ID"],
				"ELEMENT_NAME" => $arParams["ELEMENT_NAME"]
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));?>
			<?break;
		case "props":
			//PROPS//
			$arParams = CUtil::JsObjectToPhp($arParams);
			$arParams = str_replace("true", "Y", $arParams);
			$arParams = str_replace("false", "N", $arParams);
			$elementId = $request->getPost("ELEMENT_ID");
			$strMainId = $request->getPost("STR_MAIN_ID");			
			$offersIblockId = $request->getPost("OFFERS_IBLOCK_ID");
			
			if($arParams["OFFERS_SORT_FIELD"] == "PRICE") {
				$dbPriceType = CCatalogGroup::GetList(array("SORT" => "ASC"),array('ID', 'NAME', 'CAN_ACCESS'));
				$flag = false;

				while ($arPriceType = $dbPriceType->Fetch()) {
				    if($arPriceType['CAN_ACCESS'] == "Y" && in_array($arPriceType['NAME'], $arParams["PRICE_CODE"])) {
				    	$arParams["OFFERS_SORT_FIELD_PP"] = "catalog_PRICE_".$arPriceType['ID'];
				    	$flag = true;
				    	break;
				    }
				}
				
				if(!$flag) {
					$arPriceBase = CCatalogGroup::GetBaseGroup();
			    	$arParams["OFFERS_SORT_FIELD_PP"] = "catalog_PRICE_".$arPriceBase['ID'];
				}

			} elseif($arParams["OFFERS_SORT_FIELD"] == "PROPERTIES") {
				$arParams["OFFERS_SORT_FIELD_PP"] = "SORT";
				$arParams["OFFERS_SORT_FIELD3"] = "PROPERTIES";
			} else {
				$arParams["OFFERS_SORT_FIELD_PP"] = $arParams["OFFERS_SORT_FIELD"];
			}
			?>
			<?$APPLICATION->IncludeComponent("bitrix:catalog.element", "props",
				array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"PROPERTY_CODE" => array(),
					"META_KEYWORDS" => "",
					"META_DESCRIPTION" => "",
					"BROWSER_TITLE" => "",
					"SET_CANONICAL_URL" => "N",
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
					"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
					"SECTION_ID_VARIABLE" => "SECTION_ID",
					"CHECK_SECTION_ID_VARIABLE" => "",
					"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
					"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"SET_TITLE" => "N",
					"SET_LAST_MODIFIED" => "N",
					"MESSAGE_404" => "",
					"SET_STATUS_404" => "N",
					"SHOW_404" => "N",
					"FILE_404" => "",
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"USE_PRICE_COUNT" => "N",
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"PRICE_VAT_SHOW_VALUE" => "N",
					"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
					"PRODUCT_PROPERTIES" => array(),
					"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
					"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
					"LINK_IBLOCK_TYPE" => "",
					"LINK_IBLOCK_ID" => "",
					"LINK_PROPERTY_SID" => "",
					"LINK_ELEMENTS_URL" => "",
					"OFFERS_CART_PROPERTIES" => $arParams["CART_PROPERTIES"][$arParams["IBLOCK_ID"]],
					"OFFERS_FIELD_CODE" => array(),
					"OFFERS_PROPERTY_CODE" => $arParams["OFFER_TREE_PROPS"][$offersIblockId],
					"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD_PP"],
					"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
					"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
					"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
					"OFFERS_SORT_FIELD3" => $arParams["OFFERS_SORT_FIELD3"],
					"ELEMENT_ID" => $elementId,
					"ELEMENT_CODE" => "",
					"SECTION_ID" => "",
					"SECTION_CODE" => "",
					"SECTION_URL" => "",
					"DETAIL_URL" => "",
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
					"USE_ELEMENT_COUNTER" => "Y",
					"SHOW_DEACTIVATED" => "N",
					"USE_MAIN_ELEMENT_SECTION" => "Y",		
					"ADD_SECTIONS_CHAIN" => "N",
					"ADD_ELEMENT_CHAIN" => "N",		
					"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
					"COMPARE_PATH" => "",
					"BACKGROUND_IMAGE" => "",
					"DISABLE_INIT_JS_IN_COMPONENT" => "",
					"SET_VIEWED_IN_COMPONENT" => "",
					"USE_STORE" => "N",
					"STORE_PATH" => "",
					"MAIN_TITLE" => "",
					"USE_MIN_AMOUNT" => "",
					"MIN_AMOUNT" => "",
					"STORES" => array(),
					"SHOW_EMPTY_STORE" => "",
					"SHOW_GENERAL_STORE_INFORMATION" => "",
					"USER_FIELDS" => array(),
					"FIELDS" => array(),		
					"DISPLAY_IMG_WIDTH" => $arParams["DISPLAY_IMG_WIDTH"],
					"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
					"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],					
					"STR_MAIN_ID" => $strMainId
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
			<?break;
	}
	die();
}?>