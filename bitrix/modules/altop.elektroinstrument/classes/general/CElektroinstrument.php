<?if(!defined("ELEKTROINSTRUMENT_MODULE_ID")) define("ELEKTROINSTRUMENT_MODULE_ID", "altop.elektroinstrument");

IncludeModuleLangFile(__FILE__);

//initialize module parametrs list and default values
include_once __DIR__."/../../parametrs.php";

class CElektroinstrument {
	const MODULE_ID = ELEKTROINSTRUMENT_MODULE_ID;
	const PARTNER_NAME = "altop"; 
	const SOLUTION_NAME	= "elektroinstrument";

	static $arParametrsList = array();
	private static $arMetaParams = array();
	
	function checkModuleRight($reqRight = "R", $bShowError = false) {
		global $APPLICATION;
		
		if($APPLICATION->GetGroupRight(self::MODULE_ID) < $reqRight) {
			if($bShowError){
				$APPLICATION->AuthForm(GetMessage("ELEKTROINSTRUMENT_ACCESS_DENIED"));
			}
			return false;
		}
		
		return true;
	}

	function GetBackParametrsValues($SITE_ID, $bStatic = true){
		if($bStatic){
			static $arValues;
		}
		if($bStatic && $arValues === NULL || !$bStatic){
			$arDefaultValues = $arValues = array();
			if(self::$arParametrsList && is_array(self::$arParametrsList)){
				foreach(self::$arParametrsList as $blockCode => $arBlock){
					if($arBlock["OPTIONS"] && is_array($arBlock["OPTIONS"])){
						foreach($arBlock["OPTIONS"] as $optionCode => $arOption){
							$arDefaultValues[$optionCode] = $arOption["DEFAULT"];
						}
					}
				}
			}
			$arValues = unserialize(COption::GetOptionString(self::MODULE_ID, "OPTIONS", serialize(array()), $SITE_ID));		
			if($arValues && is_array($arValues)){
				foreach($arValues as $optionCode => $arOption){
					if(!isset($arDefaultValues[$optionCode])){
						unset($arValues[$optionCode]);
					}
				}
			}
			if($arDefaultValues && is_array($arDefaultValues)){
				foreach($arDefaultValues as $optionCode => $arOption){
					if(!isset($arValues[$optionCode])){
						$arValues[$optionCode] = $arOption;
					}
				}
			}
		}
		return $arValues;
	}

	function GetFrontParametrsValues($SITE_ID){
		if(!strlen($SITE_ID)) $SITE_ID = SITE_ID;
		$arBackParametrs = self::GetBackParametrsValues($SITE_ID);		
		$arValues = (array)$arBackParametrs;
		return $arValues;
	}
	
	function CheckColor($strColor) {
		if(strlen($strColor) > 0) {
			$strColor = str_replace("#", "", $strColor);
			if(strlen($strColor) < 6) {
				if(strlen($strColor) <> 3) {
					for($i = 0, $l = 6 - strlen($strColor); $i < $l; ++$i) {
						$strColor = $strColor."0";
					}					
				}
			} elseif(strlen($strColor) > 6) {
				$strColor = substr($strColor, 0, -(strlen($strColor) - 6));							
			}			
		} else {			
			$strColor = "fde037";
		}
		$strColor = "#".$strColor;
		return $strColor;
	}

	function UpdateParametrsValues() {
		$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();		
		$theme = $request->getPost("THEME");
		if(self::$arParametrsList && is_array(self::$arParametrsList)) {
			foreach(self::$arParametrsList as $blockCode => $arBlock) {
				if($arBlock["OPTIONS"] && is_array($arBlock["OPTIONS"])) {
					foreach($arBlock["OPTIONS"] as $optionCode => $arOption) {
						if($arOption["IN_SETTINGS_PANEL"] == "Y" && $theme == "default") {
							$newVal = $arOption["DEFAULT"];
						} else {
							$post = $request->getPost($optionCode);
							if($arOption["IN_SETTINGS_PANEL"] != "Y")
								$post = unserialize(base64_decode(strtr($post, "-_,", "+/=")));
							if($optionCode == "COLOR_SCHEME_CUSTOM"){
								$post = self::CheckColor($post);
							} elseif($optionCode == "SITE_BACKGROUND") {					
								if($post != "N")
									$post = "Y";
							} elseif($optionCode == "SITE_BACKGROUND_PICTURE") {
								$siteBgs = unserialize(base64_decode(strtr($request->getPost("SITE_BACKGROUNDS"), "-_,", "+/=")));
								$postSiteBg = $request->getPost("SITE_BACKGROUND");
								foreach($siteBgs as $arSiteBg) {
									if($postSiteBg == $arSiteBg)
										$post = Bitrix\Main\Config\Option::get(self::MODULE_ID, "SITE_BACKGROUND_".$arSiteBg);
								}
							}
							$newVal = $post;
							if($arOption["TYPE"] == "multiselectbox") {
								if(!is_array($newVal))
									$newVal = array();
							}
						}
						$arTab["OPTIONS"][$optionCode] = $newVal;
					}
				}
			}
		}
		Bitrix\Main\Config\Option::set(self::MODULE_ID, "OPTIONS", serialize((array)$arTab["OPTIONS"]), SITE_ID);
		
		if(CHTMLPagesCache::isOn()) {
			$staticHtmlCache = Bitrix\Main\Data\StaticHtmlCache::getInstance();
			$staticHtmlCache->deleteAll();
		}
		
		BXClearCache(true, "/".SITE_ID."/bitrix/catalog.section/");
		BXClearCache(true, "/".SITE_ID."/bitrix/catalog.element/");		
		BXClearCache(true, "/".SITE_ID."/bitrix/catalog.set.constructor/");
		BXClearCache(true, "/".SITE_ID."/bitrix/sale.gift.basket/");
		BXClearCache(true, "/".SITE_ID."/bitrix/sale.products.gift/");
		BXClearCache(true, "/".SITE_ID."/bitrix/sale.products.gift.section/");
	}

	function GenerateColorScheme() {		
		$arBackParametrs = self::GetBackParametrsValues(SITE_ID);		
		$colorScheme = $arBackParametrs["COLOR_SCHEME"];
		$arColorSchemes = self::$arParametrsList["MAIN"]["OPTIONS"]["COLOR_SCHEME"]["LIST"];		
		if(!class_exists("lessc"))
			include_once __DIR__."/../../less/lessc.inc.php";		
		$less = new lessc;
		try {
			if($colorScheme == "CUSTOM")
				$less->setVariables(array("bcolor" => $arBackParametrs["COLOR_SCHEME_CUSTOM"]));
			elseif($arColorSchemes && is_array($arColorSchemes))
				$less->setVariables(array("bcolor" => $arColorSchemes[$colorScheme]["COLOR"]));
			$less->setFormatter("compressed");
			if(defined("SITE_TEMPLATE_PATH")) {
				$schemeDirPath = $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/schemes/".$colorScheme.($colorScheme == "CUSTOM" ? "_".SITE_ID : "")."/";
				if(!is_dir($schemeDirPath))
					mkdir($schemeDirPath, 0755, true);
				$inputFile = __DIR__."/../../less/colors.less";
				$outputFile = $schemeDirPath."colors.min.css";
				
				$cache = $less->cachedCompile($inputFile);
				if(md5(file_get_contents($outputFile)) != md5($cache["compiled"]))
					file_put_contents($outputFile, $cache['compiled']);				
			}
		} catch(exception $e) {
			echo "Fatal error: ".$e->getMessage();
			die();
		}
	}

	function StartFallingSnow($SITE_TEMPLATE_PATH){
		if(!strlen($SITE_TEMPLATE_PATH)) $SITE_TEMPLATE_PATH = SITE_TEMPLATE_PATH;
		
		$snowJsOptions = "<script type='text/javascript'>
			$(function() {
				$(document).snowfall({
					flakeCount: 51,
					flakeColor: '#fff',
					flakeIndex: 999999,
					minSize: 2,
					maxSize: 7,
					minSpeed: 1,
					maxSpeed: 4,
					round: true,
					shadow: false
				});
			});
		</script>";
		
		$GLOBALS["APPLICATION"]->AddHeadString($snowJsOptions, true);
		$GLOBALS["APPLICATION"]->AddHeadScript($SITE_TEMPLATE_PATH."/js/snowfall.jquery.js");				
	}

	function start($siteID) {	
		return true;
	}
	
	function showPanel() {
		global $APPLICATION, $USER;
		if($USER->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == self::SOLUTION_NAME) {
			$APPLICATION->SetAdditionalCSS("/bitrix/wizards/".self::PARTNER_NAME."/".self::SOLUTION_NAME."/css/panel.css"); 
			
			$arMenu = array(
				array(
					"ACTION" => "jsUtils.Redirect([], \"".CUtil::JSEscape("/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardSiteID=".SITE_ID."&wizardName=".self::PARTNER_NAME.":".self::SOLUTION_NAME."&".bitrix_sessid_get())."\")",
					"ICON" => "bx-popup-item-wizard-icon",
					"TITLE" => GetMessage("STOM_BUTTON_TITLE_W1"),
					"TEXT" => GetMessage("STOM_BUTTON_NAME_W1"),
				),
			);

			$APPLICATION->AddPanelButton(
				array(
					"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=".self::PARTNER_NAME.":".self::SOLUTION_NAME."&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
					"ID" => self::SOLUTION_NAME."_wizard",
					"ICON" => "bx-panel-site-wizard-icon",
					"MAIN_SORT" => 2500,
					"TYPE" => "BIG",
					"SORT" => 10,	
					"ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
					"TEXT" => GetMessage("SCOM_BUTTON_NAME"),
					"MENU" => $arMenu,
				)
			);
		}
	}
	
	function correctInstall(){
		if(CModule::IncludeModule("main")) {
			if(COption::GetOptionString(self::MODULE_ID, "WIZARD_DEMO_INSTALLED") == "Y") {
				require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
				@set_time_limit(0);
				if(!CWizardUtil::DeleteWizard(self::PARTNER_NAME.":".self::SOLUTION_NAME)) {
					if(!DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/".self::PARTNER_NAME."/".self::SOLUTION_NAME."/")) {
						self::removeDirectory($_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/".self::PARTNER_NAME."/".self::SOLUTION_NAME."/");
					}
				}
				
				UnRegisterModuleDependences("main", "OnBeforeProlog", self::MODULE_ID, __CLASS__, "correctInstall"); 
				COption::SetOptionString(self::MODULE_ID, "WIZARD_DEMO_INSTALLED", "N");
			}
		}  
	}
	
	function DeleteEventTypeEventMessage(&$arFields) {
		if($arFields["IBLOCK_TYPE_ID"] == "forms" && !empty($arFields["CODE"])) {
			$eventName = "ALTOP_FORM_".$arFields["CODE"];
			
			$arMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE_ID" => $eventName))->Fetch();
			if(!empty($arMess))
				CEventMessage::Delete($arMess["ID"]);
			
			$arEvent = CEventType::GetByID($eventName, LANGUAGE_ID)->Fetch();
			if(!empty($arEvent))
				CEventType::Delete($eventName);
		}
	}

	function DoIBlockAfterSave($arg1, $arg2 = false) {
		$elementId = false;
		$iblockId = false;
		$offersIblockId = false;
		$offersPropertyId = false;
		
		if(CModule::IncludeModule("currency"))
			$strDefaultCurrency = CCurrency::GetBaseCurrency();
		
		if(is_array($arg2) && $arg2["PRODUCT_ID"] > 0) {
			$rsPriceElement = CIBlockElement::GetList(
				array(),
				array(
					"ID" => $arg2["PRODUCT_ID"],
				),
				false,
				false,
				array("ID", "IBLOCK_ID")
			);
			if($arPriceElement = $rsPriceElement->Fetch()) {
				$arCatalog = CCatalog::GetByID($arPriceElement["IBLOCK_ID"]);
				if(is_array($arCatalog)) {
					if($arCatalog["OFFERS"] == "Y") {
						$rsElement = CIBlockElement::GetProperty(
							$arPriceElement["IBLOCK_ID"],
							$arPriceElement["ID"],
							"sort",
							"asc",
							array("ID" => $arCatalog["SKU_PROPERTY_ID"])
						);
						$arElement = $rsElement->Fetch();
						if($arElement && $arElement["VALUE"] > 0) {
							$elementId = $arElement["VALUE"];
							$iblockId = $arCatalog["PRODUCT_IBLOCK_ID"];
							$offersIblockId = $arCatalog["IBLOCK_ID"];
							$offersPropertyId = $arCatalog["SKU_PROPERTY_ID"];
						}
					} elseif($arCatalog["OFFERS_IBLOCK_ID"] > 0) {
						$elementId = $arPriceElement["ID"];
						$iblockId = $arPriceElement["IBLOCK_ID"];
						$offersIblockId = $arCatalog["OFFERS_IBLOCK_ID"];
						$offersPropertyId = $arCatalog["OFFERS_PROPERTY_ID"];
					} else {
						$elementId = $arPriceElement["ID"];
						$iblockId = $arPriceElement["IBLOCK_ID"];
						$offersIblockId = false;
						$offersPropertyId = false;
					}
				}
			}
		} elseif(is_array($arg1) && $arg1["ID"] > 0 && $arg1["IBLOCK_ID"] > 0) {
			$elementId = $arg1["ID"];
			$iblockId = $arg1["IBLOCK_ID"];
			$arOffers = CIBlockPriceTools::GetOffersIBlock($arg1["IBLOCK_ID"]);
			if(is_array($arOffers)) {			
				$offersIblockId = $arOffers["OFFERS_IBLOCK_ID"];
				$offersPropertyId = $arOffers["OFFERS_PROPERTY_ID"];
			}
		}

		if($elementId > 0) {
			static $arPropCache = array();
			if(!array_key_exists($iblockId, $arPropCache)) {
				$rsProperty = CIBlockProperty::GetByID("MINIMUM_PRICE", $iblockId);
				if($arProperty = $rsProperty->Fetch())
					$arPropCache[$iblockId] = $arProperty["ID"];
				else
					$arPropCache[$iblockId] = false;
			}

			if($arPropCache[$iblockId] > 0) {
				if($offersIblockId > 0) {
					$rsOffers = CIBlockElement::GetList(
						array(),
						array(
							"ACTIVE" => "Y",
							"IBLOCK_ID" => $offersIblockId,
							"PROPERTY_".$offersPropertyId => $elementId,
						),
						false,
						false,
						array("ID")
					);
					while($arOffer = $rsOffers->Fetch())
						$arProductID[] = $arOffer["ID"];
						
					if(!is_array($arProductID))
						$arProductID = array($elementId);
				} else
					$arProductID = array($elementId);

				$minPrice = false;
				$minQuantity = false;
				
				$rsPrices = CPrice::GetList(
					array(),
					array(
						"PRODUCT_ID" => $arProductID,
					)
				);
				while($arPrice = $rsPrices->Fetch()) {
					if(CModule::IncludeModule("currency") && $strDefaultCurrency != $arPrice["CURRENCY"])
						$arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $strDefaultCurrency);
					
					$PRICE = $arPrice["PRICE"];
					
					$ar_res = CCatalogProduct::GetByID($arPrice["PRODUCT_ID"]);
					$QUANTITY = $ar_res["QUANTITY"];
					
					if($minPrice === false || $minPrice > $PRICE) {
						$minPrice = $PRICE;
						$minQuantity = $QUANTITY;
					}
				}

				if($minPrice !== false) {
					CIBlockElement::SetPropertyValuesEx(
						$elementId,
						$iblockId,
						array(
							"MINIMUM_PRICE" => $minPrice
						)
					);
									
					CCatalogProduct::Update(
						$elementId,
						array(
							"QUANTITY" => $minQuantity
						)
					);
				}
			}
		}
	}

	function SetOrderPropertiesLocation(&$arUserResult, $request, &$arParams, &$arResult) {
		$arSetting = self::GetFrontParametrsValues(SITE_ID);
		if($arSetting["USE_GEOLOCATION"] != "Y" || $arSetting["GEOLOCATION_ORDER_CITY"] != "Y")
			return;
		
		$locationId = Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getCookie("GEOLOCATION_LOCATION_ID");
		if(intval($locationId) <= 0)
			return;

		$locationCode = CSaleLocation::getLocationCODEbyID($locationId);
		if(empty($locationCode))
			return;

		if(intval($arUserResult["PERSON_TYPE_ID"]) <= 0)
			return;

		if($request->isPost() && ($arUserResult["PERSON_TYPE_OLD"] == false || $arUserResult["PROFILE_CHANGE"] == "N"))
			return;
		
		$order = Bitrix\Sale\Order::create(Bitrix\Main\Context::getCurrent()->getSite());
		$propertyCollection = $order->getPropertyCollection();			
		foreach($propertyCollection as $property) {	
			$arProperty = $property->getProperty();
			if($arProperty["TYPE"] == "LOCATION") {
				$arUserResult["ORDER_PROP"][$arProperty["ID"]] = $locationCode;
			}
		}

		$arUserResult["DELIVERY_LOCATION"] = $locationId;
		$arUserResult["DELIVERY_LOCATION_BCODE"] = $locationCode;
	}
	
	function getBackground($SITE_ID) {
		global $APPLICATION;
		$arSetting = self::GetBackParametrsValues($SITE_ID, false);
		
		if($arSetting["SITE_BACKGROUND"] == "Y") {
			$bgSetting = array(
				'POSITION' => 'bg-position',
				'REPEAT_X' => $arSetting['SITE_BACKGROUND_REPEAT_X'] == 'Y' ? ' bg-repeat-x' : '',
				'REPEAT_Y' => $arSetting['SITE_BACKGROUND_REPEAT_Y'] == 'Y' ? ' bg-repeat-y' : '',
				'FIXED' => $arSetting['SITE_BACKGROUND_FIXED'] == 'Y' ? ' bg-fixed' : ''
			);
			
			$APPLICATION->SetPageProperty(
				'bgClass',
				" class=\"{$bgSetting['POSITION']}{$bgSetting['REPEAT_X']}{$bgSetting['REPEAT_Y']}{$bgSetting['FIXED']}\""
			);
			
			if($arSetting["SITE_BACKGROUND_PICTURE"] > 0) {
				$arFile = CFile::GetFileArray($arSetting["SITE_BACKGROUND_PICTURE"]);
				if(is_array($arFile)) {
					$APPLICATION->SetPageProperty(
						"backgroundImage",
						" style=\"background-image: url('".CHTTP::urnEncode($arFile["SRC"], "UTF-8")."')\""
					);
				}
			} elseif($arSetting["SITE_BACKGROUND_COLOR"]) {
				$APPLICATION->SetPageProperty(
					"backgroundImage",
					" style=\"background-color: ".$arSetting["SITE_BACKGROUND_COLOR"]."\""
				);
			}
		}
	}
}?>