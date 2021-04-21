<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arSetting;

use Bitrix\Main\Loader,
	Bitrix\Main\Page\Asset;

//SET_CURRENCIES//
if(!empty($templateData["CURRENCIES"])) {
	$loadCurrency = Loader::includeModule("currency");
	
	CJSCore::Init(array("currency"));?>
	
	<script type="text/javascript">
		BX.Currency.setCurrencies(<?=$templateData["CURRENCIES"]?>);
	</script>
<?}

//JS_OBJ//
if(isset($templateData["JS_OBJ"])) {?>
	<script type="text/javascript">
		BX.ready(BX.defer(function(){
			if(!!window.<?=$templateData["JS_OBJ"];?>) {
				window.<?=$templateData["JS_OBJ"];?>.allowViewedCount(true);
			}
		}));
	</script>
<?}

//META_PROPERTY//
$APPLICATION->SetPageProperty("ogtype", "product");
if(isset($arResult["JS_OFFERS"]) && !empty($arResult["JS_OFFERS"])):
	foreach($arResult["JS_OFFERS"] as $key => $arOffer):
		if(is_array($arOffer["DETAIL_PICTURE"])):
			$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$arOffer['DETAIL_PICTURE']['SRC']);
			$APPLICATION->SetPageProperty("ogimagewidth", $arOffer["DETAIL_PICTURE"]["WIDTH"]);
			$APPLICATION->SetPageProperty("ogimageheight", $arOffer["DETAIL_PICTURE"]["HEIGHT"]);
		else:
			$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$arResult['DETAIL_PICTURE']['SRC']);
			$APPLICATION->SetPageProperty("ogimagewidth", $arResult["DETAIL_PICTURE"]["WIDTH"]);
			$APPLICATION->SetPageProperty("ogimageheight", $arResult["DETAIL_PICTURE"]["HEIGHT"]);
		endif;
	endforeach;
else:
	if(is_array($arResult["DETAIL_PICTURE"])):
		$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$arResult['DETAIL_PICTURE']['SRC']);
		$APPLICATION->SetPageProperty("ogimagewidth", $arResult["DETAIL_PICTURE"]["WIDTH"]);
		$APPLICATION->SetPageProperty("ogimageheight", $arResult["DETAIL_PICTURE"]["HEIGHT"]);
	else:
		$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.SITE_TEMPLATE_PATH."/images/no-photo.jpg");
		$APPLICATION->SetPageProperty("ogimagewidth", "150");
		$APPLICATION->SetPageProperty("ogimageheight", "150");
	endif;
endif;
if(count($arResult["MORE_PHOTO"]) > 0):
	foreach($arResult["MORE_PHOTO"] as $PHOTO):
		$APPLICATION->AddHeadString("<meta property='og:image' content='".(CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$PHOTO['SRC']."' />", true);
	endforeach;
endif;

//BACKGROUND_IMAGE//
if(empty($arResult["BACKGROUND_IMAGE"]) || empty($arResult["BACKGROUND_YOUTUBE"])):
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
				array("ID", "IBLOCK_ID", "DEPTH_LEVEL", "UF_BACKGROUND_IMAGE", "UF_YOUTUBE_BG")
			);
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache($cache_dir);
			$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
			while($arSection = $rsSections->GetNext()) {
				if(!isset($arCurSection["BACKGROUND_IMAGE"]) && $arSection["UF_BACKGROUND_IMAGE"] > 0) {
					$arCurSection["BACKGROUND_IMAGE"] = CFile::GetFileArray($arSection["UF_BACKGROUND_IMAGE"]);
				}
				if(!isset($arCurSection["BACKGROUND_YOUTUBE"]) && !empty($arSection["UF_YOUTUBE_BG"])) {
					$arCurSection["BACKGROUND_YOUTUBE"] = $arSection["UF_YOUTUBE_BG"];
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

if((!empty($arResult["BACKGROUND_YOUTUBE"]) || !empty($arCurSection["BACKGROUND_YOUTUBE"])) && $arSetting['SITE_BACKGROUND']['VALUE'] === 'Y'):
	$sBackgroundYouTube = (!empty($arResult["BACKGROUND_YOUTUBE"])? $arResult["BACKGROUND_YOUTUBE"]: $arCurSection["BACKGROUND_YOUTUBE"]);
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.mb.YTPlayer.min.js');
	$APPLICATION->AddHeadString("<script>
		$(function(){
			$('body').prepend(\"<div id='bgVideoYT'></div>\");
			$('#bgVideoYT').YTPlayer({
				videoURL: '{$sBackgroundYouTube}',
				mute: true,
				showControls: false,
				quality: 'defaul',
				containment: 'body',
				optimizeDisplay: true,
				startAt: 0,
				autoPlay: true,
				realfullscreen: true,
				stopMovieOnBlur: true,
				showYTLogo: false,
				gaTrack: false
			});
		});
		</script>", true);
endif;

if(!($arParams["AJAX_OPTION_HISTORY"] == "Y" && $arParams["AJAX_MODE"] == "Y")) {
// if(($arParams["AJAX_OPTION_HISTORY"] !== "Y" && $arParams["AJAX_MODE"] == "Y") || $arParams["AJAX_MODE"] == "Y" || $arParams["AJAX_MODE"] !== "Y") {
	//SUBSCRIBE//
	if(isset($arResult["JS_OFFERS"]) && !empty($arResult["JS_OFFERS"])):
		if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):		
			$arOffer = $arResult["JS_OFFERS"][$arResult["OFFERS_SELECTED"]];
			if(!$arOffer["CAN_BUY"] && $arResult["CATALOG_SUBSCRIBE"] == 'Y'):?>		
				<div id="catalog-subscribe-from" class="catalog-subscribe-from" style="display:none;">
					<?$APPLICATION->includeComponent("bitrix:catalog.product.subscribe", "",
						array(
							"PRODUCT_ID" => $arOffer["ID"],
							"USE_CAPTCHA" => $arResult["USE_CAPTCHA"],
							"BUTTON_ID" => "subscribe_product_".$arResult["STR_MAIN_ID"]."_".$arOffer["ID"],
							"BUTTON_CLASS" => "btn_buy subscribe_anch"
						),
						false,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			<?endif;
		endif;
	else:
		if(!$arResult["CAN_BUY"] && $arResult["CATALOG_SUBSCRIBE"] == 'Y'):?>
			<div id="catalog-subscribe-from" class="catalog-subscribe-from" style="display:none;">
				<?$APPLICATION->includeComponent("bitrix:catalog.product.subscribe", "",
					array(
						"PRODUCT_ID" => $arResult["ID"],
						"USE_CAPTCHA" => $arResult["USE_CAPTCHA"],
						"BUTTON_ID" => "subscribe_product_".$arResult["STR_MAIN_ID"],
						"BUTTON_CLASS" => "btn_buy subscribe_anch"
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
			</div>
		<?endif;
	endif;

	//GEOLOCATION_DELIVERY//
	if($arSetting["USE_GEOLOCATION"]["VALUE"] == "Y" && $arSetting["GEOLOCATION_DELIVERY"]["VALUE"] == "Y" && !$arResult["COLLECTION"]["THIS"]):
		if(isset($arResult["JS_OFFERS"]) && !empty($arResult["JS_OFFERS"])):
			if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
				$arOffer = $arResult["JS_OFFERS"][$arResult["OFFERS_SELECTED"]];
				if($arOffer["CAN_BUY"] && $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]]["RATIO_PRICE"] > 0):?>
					<div id="geolocation-delivery-from" class="geolocation-delivery-from" style="display:none;">
						<?$APPLICATION->IncludeComponent("altop:geolocation.delivery", "",
							array(			
								"ELEMENT_ID" => $arOffer["ID"],
								"ELEMENT_COUNT" => $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]]["MIN_QUANTITY"],
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],
								"CACHE_TIME" => $arParams["CACHE_TIME"]
							),
							false,
							array("HIDE_ICONS" => "Y")
						);?>
					</div>
				<?endif;
			endif;
		else:
			if($arResult["CAN_BUY"] && $arResult["MIN_PRICE"]["RATIO_PRICE"] > 0):?>
				<div id="geolocation-delivery-from" class="geolocation-delivery-from" style="display:none;">		
					<?$APPLICATION->IncludeComponent("altop:geolocation.delivery", "",
						array(			
							"ELEMENT_ID" => $arResult["ID"],
							"ELEMENT_COUNT" => $arResult["MIN_PRICE"]["MIN_QUANTITY"],
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
	if(isset($arResult["JS_OFFERS"]) && !empty($arResult["JS_OFFERS"])):
		if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):		
			$arOffer = $arResult["JS_OFFERS"][$arResult["OFFERS_SELECTED"]];?>		
			<div id="set-constructor-from" class="set-constructor-from" style="display:none;">
				<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
					array(
						"IBLOCK_TYPE_ID" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arResult["OFFERS_IBLOCK"],						
						"ELEMENT_ID" => $arOffer["ID"],		
						"BASKET_URL" => $arParams["BASKET_URL"],
						"PRICE_CODE" => $arParams["PRICE_CODE"],
						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"] == 1 ? "Y" : "N",
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
						"STR_MAIN_ID" => $arResult["STR_MAIN_ID"]."_".$arOffer["ID"]
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
			</div>
		<?endif;
	else:?>
		<div id="set-constructor-from" class="set-constructor-from" style="display:none;">
			<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
				array(
					"IBLOCK_TYPE_ID" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"ELEMENT_ID" => $arResult["ID"],		
					"BASKET_URL" => $arParams["BASKET_URL"],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"] == 1 ? "Y" : "N",
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
					"STR_MAIN_ID" => $arResult["STR_MAIN_ID"]
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
		</div>
	<?endif;

	//COLLECTION
	if($arResult["COLLECTION"]["THIS"]) {?>
		<div id="collection-from" class="collection" style="display:none">
			<?global $arCollectionFilter;
			$arCollectionFilter["ID"] = $arResult["COLLECTION"]["VALUE"];?>
			<?$content = $APPLICATION->IncludeComponent("bitrix:catalog.section", "items_collection",
				array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
					"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
					"ELEMENT_SORT_FIELD2" => "SORT",
					"ELEMENT_SORT_ORDER2" => "ASC",
					"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
					"SET_META_KEYWORDS" => "N",		
					"SET_META_DESCRIPTION" => "N",		
					"SET_BROWSER_TITLE" => "N",
					"SET_LAST_MODIFIED" => "N",
					"INCLUDE_SUBSECTIONS" => "Y",
					"SHOW_ALL_WO_SECTION" => "Y",
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
					"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
					"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
					"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
					"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
					"FILTER_NAME" => "arCollectionFilter",
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"SET_TITLE" => "N",
					"MESSAGE_404" => "",
					"SET_STATUS_404" => "N",
					"SHOW_404" => "N",
					"FILE_404" => "",
					"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"] == 1 ? "Y" : "N",
					"PAGE_ELEMENT_COUNT" => "900",
					"LINE_ELEMENT_COUNT" => "",
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"] == 1 ? "Y" : "N",
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"] == 1 ? "Y" : "N",
					"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"] == 1 ? "Y" : "N",
					"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
					"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
					"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"PAGER_TITLE" => "",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => "",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"PAGER_BASE_LINK" => "",
					"PAGER_PARAMS_NAME" => "",
					"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
					"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
					"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
					"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
					"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
					"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
					"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
					"OFFERS_LIMIT" => "",
					"SECTION_ID" => "",
					"SECTION_CODE" => "",
					"SECTION_URL" => "",
					"DETAIL_URL" => "",
					"USE_MAIN_ELEMENT_SECTION" => "Y",
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"HIDE_NOT_AVAILABLE" => "N",
					"ADD_SECTIONS_CHAIN" => "N",		
					"COMPARE_PATH" => $arParams["COMPARE_PATH"],
					"BACKGROUND_IMAGE" => "",
					"DISABLE_INIT_JS_IN_COMPONENT" => "",
					"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
					"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
					"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"]
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
		</div>
	<?}

	//ACCESSORIES//?>
	<div id="accessories-from" class="accessories" style="display:none;">
		<?if(!empty($arResult["PROPERTY_ACCESSORIES_ID"])):
			global $arAcsPrFilter;
			$arAcsPrFilter["ID"] = $arResult["PROPERTY_ACCESSORIES_ID"];?>		
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
					"INCLUDE_SUBSECTIONS" => "Y",
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
					"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"] == 1 ? "Y" : "N",
					"PAGE_ELEMENT_COUNT" => (isset($arParams['NUMBER_ACCESSORIES'])? $arParams['NUMBER_ACCESSORIES']: "8"),
					"LINE_ELEMENT_COUNT" => "",
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"] == 1 ? "Y" : "N",
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"] == 1 ? "Y" : "N",
					"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"] == 1 ? "Y" : "N",
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
					"OFFERS_SORT_FIELD3" => $arParams["OFFERS_SORT_FIELD3"],
					"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
					"SECTION_ID" => "",
					"SECTION_CODE" => "",
					"SECTION_URL" => "",
					"DETAIL_URL" => "",
					"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"] == 1 ? "Y" : "N",
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
					"ADD_SECTIONS_CHAIN" => "N",		
					"COMPARE_PATH" => "",
					"BACKGROUND_IMAGE" => "",
					"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
					"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
					"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
					"PROPERTY_CODE_MOD" => $arParams["PROPERTY_CODE_MOD"],
                    "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
		            "MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
		            "RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
		            "MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
		            "MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
		<?endif;?>
	</div>

	<?//CATALOG_REVIEWS//
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
		<?$APPLICATION->IncludeComponent("altop:catalog.reviews.list", "",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arResult["REVIEWS"]["IBLOCK_ID"],
				"ELEMENT_ID" => $arResult["ID"],
				"ELEMENT_AREA_ID" => $arResult["STR_MAIN_ID"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"COUNT_REVIEW" => $arParams["COUNT_REVIEW"]
			),
			false,
			array("HIDE_ICONS" => "Y")
		);?>
	</div>

	<?//STORES//
	if($arParams["USE_STORE"] == "Y" && !$arResult["COLLECTION"]["THIS"]):
		if(isset($arResult["JS_OFFERS"]) && !empty($arResult["JS_OFFERS"])):
			if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
				$arOffer = $arResult["JS_OFFERS"][$arResult["OFFERS_SELECTED"]];?>
				<div id="catalog-detail-stores-from" class="catalog-detail-stores-from" style="display:none;">
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
							"STORES" => $arParams["STORES"],
							"SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
							"SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
							"USER_FIELDS" => $arParams["USER_FIELDS"],
							"FIELDS" => $arParams["FIELDS"]
						),
						false,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			<?endif;
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
						"STORES" => $arParams["STORES"],
						"SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
						"SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
						"USER_FIELDS" => $arParams["USER_FIELDS"],
						"FIELDS" => $arParams["FIELDS"]
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
			</div>						
		<?endif;
	endif;
}?>