<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arSetting;

use Bitrix\Main\Page\Asset;

$strMainID = $this->GetEditAreaId($arResult["ID"]);

//META_PROPERTY//
Asset::getInstance()->addString("<meta property='og:title' content='".$arResult['NAME']."' />", true);
Asset::getInstance()->addString("<meta property='og:description' content='".strip_tags($arResult['PREVIEW_TEXT'])."' />", true);
Asset::getInstance()->addString("<meta property='og:url' content='".$APPLICATION->GetCurPage()."' />", true);
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):
	foreach($arResult["OFFERS"] as $key => $arOffer):
		if(is_array($arOffer["DETAIL_PICTURE"])):
			Asset::getInstance()->addString("<meta property='og:image' content='".$arOffer['DETAIL_PICTURE']['SRC']."' />", true);
		else:
			Asset::getInstance()->addString("<meta property='og:image' content='".$arResult['DETAIL_PICTURE']['SRC']."' />", true);
		endif;
	endforeach;
else:
	if(is_array($arResult["DETAIL_PICTURE"])):
		Asset::getInstance()->addString("<meta property='og:image' content='".$arResult['DETAIL_PICTURE']['SRC']."' />", true);
	else:
		Asset::getInstance()->addString("<meta property='og:image' content='".SITE_TEMPLATE_PATH."/images/no-photo.jpg' />", true);
	endif;
endif;
if(count($arResult["MORE_PHOTO"]) > 0):
	foreach($arResult["MORE_PHOTO"] as $PHOTO):
		$APPLICATION->AddHeadString("<meta property='og:image' content='".$PHOTO['SRC']."' />", true);
	endforeach;
endif;

//BACKGROUND_IMAGE//
if(empty($arResult["BACKGROUND_IMAGE"])):
	foreach($arResult["SECTION"]["PATH"] as $arSectionPath):
		$sectionPathIds[] = $arSectionPath["ID"];
	endforeach;	
	if(!empty($sectionPathIds)):
		$arFilter = array(
			"IBLOCK_ID" => $arResult["IBLOCK_ID"],
			"ACTIVE" => "Y",
			"GLOBAL_ACTIVE" => "Y",
			"ID" => $sectionPathIds
		);
		$cache_id = md5(serialize($arFilter));
		$cache_dir = "/catalog/detail";
		$obCache = new CPHPCache();
		if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_dir)) {
			$arCurSection = $obCache->GetVars();
		} elseif($obCache->StartDataCache()) {
			$rsSections = CIBlockSection::GetList(
				array("DEPTH_LEVEL" => "DESC"),	
				array("IBLOCK_ID" => $arResult["IBLOCK_ID"], "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "ID" => $sectionPathIds),
				false,
				array("ID", "IBLOCK_ID", "DEPTH_LEVEL", "UF_BACKGROUND_IMAGE")
			);
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache($cache_dir);
			$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
			while($arSection = $rsSections->GetNext()) {
				if(!isset($arCurSection["BACKGROUND_IMAGE"]) && $arSection["UF_BACKGROUND_IMAGE"] > 0) {
					$arCurSection["BACKGROUND_IMAGE"] = CFile::GetFileArray($arSection["UF_BACKGROUND_IMAGE"]);
				}
			}
			$CACHE_MANAGER->EndTagCache();
			$obCache->EndDataCache($arCurSection);
		}
	endif;
	if(isset($arCurSection["BACKGROUND_IMAGE"]) && is_array($arCurSection["BACKGROUND_IMAGE"])):
		$APPLICATION->SetPageProperty(
			"backgroundImage",
			'style="background-image:url(\''.CHTTP::urnEncode($arCurSection['BACKGROUND_IMAGE']['SRC'], 'UTF-8').'\')"'
		);
	endif;
endif;

//JS_OBJ//
if(isset($templateData['JS_OBJ'])):?>
	<script type="text/javascript">
		BX.ready(BX.defer(function(){
			if(!!window.<?=$templateData['JS_OBJ'];?>) {
				window.<?=$templateData['JS_OBJ'];?>.allowViewedCount(true);
			}
		}));
	</script>
<?endif;

//BUY_ONE_CLICK//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):
	foreach($arResult["OFFERS"] as $key => $arOffer):
		if($arOffer["CAN_BUY"] && $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] > 0):
			$properties = array();
			if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])):		
				$properties[] = array(
					"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
					"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
					"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
				);																
			endif;
			foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
				$properties[] = array(
					"NAME" => $propOffer["NAME"],
					"CODE" => $propOffer["CODE"],
					"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
				);
			}
			$properties = !empty($properties) ? strtr(base64_encode(addslashes(gzcompress(serialize($properties),9))), '+/=', '-_,') : "";
			global $arBuyOneClickFilter;
			$arBuyOneClickFilter = array(
				"ELEMENT_ID" => $arOffer["ID"],
				"ELEMENT_AREA_ID" => $strMainID."_".$arOffer["ID"],
				"ELEMENT_PROPS" => $properties,
				"SELECT_PROP_DIV" => $strMainID."_propdiv",
				"BUY_MODE" => "ONE",
				"BUTTON_ID" => "boc_anch_".$strMainID."_".$arOffer["ID"],
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_buy_one_click.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?endif;
	endforeach;
else:
	if($arResult["CAN_BUY"] && $arResult["MIN_PRICE"]["DISCOUNT_VALUE"] > 0):
		$properties = "";
		if(!empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])):				
			$properties[] = array(
				"NAME" => $arResult["PROPERTIES"]["ARTNUMBER"]["NAME"],
				"CODE" => $arResult["PROPERTIES"]["ARTNUMBER"]["CODE"],
				"VALUE" => $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]
			);
			$properties = strtr(base64_encode(addslashes(gzcompress(serialize($properties),9))), '+/=', '-_,');
		endif;
		global $arBuyOneClickFilter;
		$arBuyOneClickFilter = array(
			"ELEMENT_ID" => $arResult["ID"],
			"ELEMENT_AREA_ID" => $strMainID,
			"ELEMENT_PROPS" => $properties,
			"SELECT_PROP_DIV" => $strMainID."_propdiv",
			"BUY_MODE" => "ONE",
			"BUTTON_ID" => "boc_anch_".$strMainID,
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_buy_one_click.php"), false, array("HIDE_ICONS" => "Y"));?>
	<?endif;
endif;

//ASK_PRICE//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):
	foreach($arResult["OFFERS"] as $key => $arOffer):
		if($arOffer["CAN_BUY"] && $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):
			$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];
			$properties = array();
			foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
				$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
			}
			$properties = implode("; ", $properties);
			$elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;
			global $arAskPriceFilter;
			$arAskPriceFilter = array(
				"ELEMENT_ID" => $arOffer["ID"],
				"ELEMENT_AREA_ID" => $strMainID."_".$arOffer["ID"],
				"ELEMENT_NAME" => $elementName,
				"BUTTON_ID" => "ask_price_anch_".$strMainID."_".$arOffer["ID"],
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?endif;
	endforeach;
else:
	if($arResult["CAN_BUY"] && $arResult["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):
		global $arAskPriceFilter;
		$arAskPriceFilter = array(
			"ELEMENT_ID" => $arResult["ID"],
			"ELEMENT_AREA_ID" => $strMainID,
			"ELEMENT_NAME" => $arResult["NAME"],
			"BUTTON_ID" => "ask_price_anch_".$strMainID
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));?>
	<?endif;
endif;

//CHEAPER//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):
	if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
		foreach($arResult["OFFERS"] as $key => $arOffer):
			if($arOffer["CAN_BUY"] && $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] > 0):
				$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];
				$properties = array();
				foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
					$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
				}
				$properties = implode("; ", $properties);
				$elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;
				global $arCheaperFilter;
				$arCheaperFilter = array(
					"ELEMENT_ID" => $arOffer["ID"],
					"ELEMENT_AREA_ID" => $strMainID."_".$arOffer["ID"],
					"ELEMENT_NAME" => $elementName,
					"ELEMENT_PRICE" => $arOffer["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"],
					"BUTTON_ID" => "cheaper_anch_".$strMainID."_".$arOffer["ID"],
					"HIDE_ICONS" => "Y"
				);?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_cheaper.php"), false, array("HIDE_ICONS" => "Y"));?>
			<?endif;
		endforeach;
	endif;
else:
	if($arResult["CAN_BUY"] && $arResult["MIN_PRICE"]["DISCOUNT_VALUE"] > 0):		
		global $arCheaperFilter;
		$arCheaperFilter = array(
			"ELEMENT_ID" => $arResult["ID"],
			"ELEMENT_AREA_ID" => $strMainID,
			"ELEMENT_NAME" => $arResult["NAME"],
			"ELEMENT_PRICE" => $arResult["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"],
			"BUTTON_ID" => "cheaper_anch_".$strMainID
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_cheaper.php"), false, array("HIDE_ICONS" => "Y"));?>
	<?endif;
endif;

//UNDER_ORDER//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):
	foreach($arResult["OFFERS"] as $key => $arOffer):
		if(!$arOffer["CAN_BUY"]):
			$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];
			$properties = array();
			foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
				$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
			}
			$properties = implode("; ", $properties);
			$elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;
			global $arUnderOrderFilter;
			$arUnderOrderFilter = array(
				"ELEMENT_ID" => $arOffer["ID"],
				"ELEMENT_AREA_ID" => $strMainID."_".$arOffer["ID"],
				"ELEMENT_NAME" => $elementName,
				"BUTTON_ID" => "under_order_anch_".$strMainID."_".$arOffer["ID"],
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?endif;
	endforeach;
else:
	if(!$arResult["CAN_BUY"]):
		global $arUnderOrderFilter;
		$arUnderOrderFilter = array(
			"ELEMENT_ID" => $arResult["ID"],
			"ELEMENT_AREA_ID" => $strMainID,
			"ELEMENT_NAME" => $arResult["NAME"],
			"BUTTON_ID" => "under_order_anch_".$strMainID
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));?>
	<?endif;
endif;

//GEOLOCATION_DELIVERY//
if($arSetting["USE_GEOLOCATION"]["VALUE"] == "Y" && $arSetting["GEOLOCATION_DELIVERY"]["VALUE"] == "Y"):
	if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):
		if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
			foreach($arResult["OFFERS"] as $key => $arOffer):
				if($arOffer["CAN_BUY"] && $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] > 0):?>
					<div id="geolocation-delivery-from-<?=$strMainID.'-'.$arOffer['ID']?>" class="geolocation-delivery-from hidden" style="display:none;">
						<?$APPLICATION->IncludeComponent("altop:geolocation.delivery", "",
							array(			
								"ELEMENT_ID" => $arOffer["ID"],
								"ELEMENT_COUNT" => $arOffer["CATALOG_MEASURE_RATIO"],
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],
								"CACHE_TIME" => $arParams["CACHE_TIME"]
							),
							false,
							array("HIDE_ICONS" => "Y")
						);?>
					</div>
				<?endif;
			endforeach;
		endif;
	else:
		if($arResult["CAN_BUY"] && $arResult["MIN_PRICE"]["DISCOUNT_VALUE"] > 0):?>
			<div id="geolocation-delivery-from" class="geolocation-delivery-from" style="display:none;">		
				<?$APPLICATION->IncludeComponent("altop:geolocation.delivery", "",
					array(			
						"ELEMENT_ID" => $arResult["ID"],
						"ELEMENT_COUNT" => $arResult["CATALOG_MEASURE_RATIO"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"]
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>	
			</div>
		<?endif;
	endif;
endif;

//SET_CONSTRUCTOR//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):
	if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
		foreach($arResult["OFFERS"] as $key => $arOffer):?>		
			<div id="set-constructor-from-<?=$strMainID.'-'.$arOffer['ID']?>" class="set-constructor-from hidden" style="display:none;">
				<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
					array(
						"IBLOCK_TYPE_ID" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arResult["OFFERS_IBLOCK"],						
						"ELEMENT_ID" => $arOffer["ID"],		
						"BASKET_URL" => $arParams["BASKET_URL"],
						"PRICE_CODE" => $arParams["PRICE_CODE"],
						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"]
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
			</div>
		<?endforeach;
	endif;
else:?>
	<div id="set-constructor-from" class="set-constructor-from" style="display:none;">
		<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
			array(
				"IBLOCK_TYPE_ID" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_ID" => $arResult["ID"],		
				"BASKET_URL" => $arParams["BASKET_URL"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"]
			),
			false,
			array("HIDE_ICONS" => "Y")
		);?>
	</div>
<?endif;

//ACCESSORIES//?>
<div id="accessories-from" class="accessories" style="display:none;">
	<?if(!empty($arResult["PROPERTIES"]["ACCESSORIES"]["VALUE"])):
		global $arAcsPrFilter;
		$arAcsPrFilter["ID"] = $arResult["PROPERTIES"]["ACCESSORIES"]["VALUE"];?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "filtered",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_SORT_FIELD" => "RAND",
				"ELEMENT_SORT_ORDER" => "ASC",
				"ELEMENT_SORT_FIELD2" => "",
				"ELEMENT_SORT_ORDER2" => "",
				"PROPERTY_CODE" => "",
				"SET_META_KEYWORDS" => "N",		
				"SET_META_DESCRIPTION" => "N",		
				"SET_BROWSER_TITLE" => "N",
				"SET_LAST_MODIFIED" => "N",
				"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
				"SHOW_ALL_WO_SECTION" => "Y",
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"FILTER_NAME" => "arAcsPrFilter",
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SET_TITLE" => "N",
				"MESSAGE_404" => "",
				"SET_STATUS_404" => "N",
				"SHOW_404" => "N",
				"FILE_404" => "",
				"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
				"PAGE_ELEMENT_COUNT" => "8",
				"LINE_ELEMENT_COUNT" => "",
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
				"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
				"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "N",
				"PAGER_TITLE" => "",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => "",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
				"PAGER_SHOW_ALL" => "N",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"PAGER_BASE_LINK" => "",
				"PAGER_PARAMS_NAME" => "",
				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
				"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
				"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
				"SECTION_ID" => "",
				"SECTION_CODE" => "",
				"SECTION_URL" => "",
				"DETAIL_URL" => "",
				"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"HIDE_NOT_AVAILABLE" => "N",
				"ADD_SECTIONS_CHAIN" => "N",		
				"COMPARE_PATH" => "",
				"BACKGROUND_IMAGE" => "",
				"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
				"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
				"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
				"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"]
			),
			false,
			array("HIDE_ICONS" => "Y")
		);?>
	<?endif;?>
</div>

<?//REVIEWS//
if(isset($arParams["IBLOCK_ID_REVIEWS"]) && intval($arParams["IBLOCK_ID_REVIEWS"]) > 0):
	$arResult["REVIEWS"]["IBLOCK_ID"] = $arParams["IBLOCK_ID_REVIEWS"];
else:
	$arFilter = array(
		"ACTIVE" => "Y",
		"SITE_ID" => SITE_ID,
		"TYPE" => "catalog",
		"CODE" => "comments_".SITE_ID
	);
	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/catalog/comments")) {
		$arResult["REVIEWS"]["IBLOCK_ID"] = $obCache->GetVars();		
	} elseif($obCache->StartDataCache()) {
		$res = CIBlock::GetList(array(), $arFilter, true);
		if($reviews_iblock = $res->Fetch()) {
			$arResult["REVIEWS"]["IBLOCK_ID"] = $reviews_iblock["ID"];
		}
		$obCache->EndDataCache($arResult["REVIEWS"]["IBLOCK_ID"]);
	}
endif;?>
<div id="catalog-reviews-from" style="display:none;">
	<?$APPLICATION->IncludeComponent("altop:catalog.reviews", "",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arResult["REVIEWS"]["IBLOCK_ID"],
			"ELEMENT_ID" => $arResult["ID"],
			"ELEMENT_AREA_ID" => $strMainID,
			"PRE_MODERATION" => "Y",
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"]
		),
		false
	);?>
</div>

<?//STORES//
if($arParams["USE_STORE"] == "Y"):
	if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):
		if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):					
			foreach($arResult["OFFERS"] as $key => $arOffer):?>
				<div id="catalog-detail-stores-from-<?=$strMainID.'-'.$arOffer['ID']?>" class="catalog-detail-stores-from hidden" style="display:none;">
					<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount",	".default",
						array(
							"ELEMENT_ID" => $arOffer["ID"],
							"STORE_PATH" => $arParams["STORE_PATH"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"MAIN_TITLE" => $arParams["MAIN_TITLE"],
							"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
							"SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
							"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
							"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],									
							"STORES" => $arParams['STORES'],
							"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
							"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
							"USER_FIELDS" => $arParams['USER_FIELDS'],
							"FIELDS" => $arParams['FIELDS']
						),
						false,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			<?endforeach;
		endif;
	else:?>
		<div id="catalog-detail-stores-from" class="catalog-detail-stores-from" style="display:none;">
			<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount",	".default",
				array(
					"ELEMENT_ID" => $arResult["ID"],
					"STORE_PATH" => $arParams["STORE_PATH"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"MAIN_TITLE" => $arParams["MAIN_TITLE"],
					"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
					"SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
					"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
					"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
					"STORES" => $arParams['STORES'],
					"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
					"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
					"USER_FIELDS" => $arParams['USER_FIELDS'],
					"FIELDS" => $arParams['FIELDS']
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
		</div>						
	<?endif;
endif;?>