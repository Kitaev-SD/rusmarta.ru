<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Sale,	
	Bitrix\Sale\Location;

global $arSetting;

if(count($arResult["ORDERS"]) < 1)
	return;

if(!CModule::IncludeModule("iblock")  || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("sale"))
	return;

foreach($arResult["ORDERS"] as $key => $val) {	
	/***PAY_SYSTEM***/
	if(intval($val["ORDER"]["PAY_SYSTEM_ID"])) {
		$payment["PAY_SYSTEM"] = \Bitrix\Sale\PaySystem\Manager::getById($val["ORDER"]["PAY_SYSTEM_ID"]);				
	}
	if($payment["PAID"] != "Y" && $val["ORDER"]["CANCELED"] != "Y" && $val["ORDER"]["PAYED"] !== "Y") {		
		$service = new \Bitrix\Sale\PaySystem\Service($payment["PAY_SYSTEM"]);
		if($service) {
			$payment["CAN_REPAY"] = "Y";
			if($service->getField("NEW_WINDOW") == "Y") {
				$arResult["ORDERS"][$key]["ORDER"]["PSA_ACTION_FILE"] = htmlspecialcharsbx($arParams["PATH_TO_PAYMENT"]).'?ORDER_ID='.urlencode(urlencode($val["ORDER"]["ACCOUNT_NUMBER"])).'&PAYMENT_ID='.$payment['ID'];			
			}
		}
	}
	
	/***BASKET_ITEMS***/	
	if(isset($val["BASKET_ITEMS"]) && is_array($val["BASKET_ITEMS"])) foreach($val["BASKET_ITEMS"] as $key2 => $arBasketItems):		
		$ar = CIBlockElement::GetList(
			array(), 
			array("ID" => $arBasketItems["PRODUCT_ID"]), 
			false, 
			false, 
			array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
		)->Fetch();		
		if($ar["DETAIL_PICTURE"] > 0) {
			$arResult["ORDERS"][$key]["BASKET_ITEMS"][$key2]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 30, "height" => 30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		} else {
			$mxResult = CCatalogSku::GetProductInfo($ar["ID"]);
			if(is_array($mxResult)) {
				$ar = CIBlockElement::GetList(
					array(), 
					array("ID" => $mxResult["ID"]), 
					false, 
					false, 
					array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
				)->Fetch();
				if($ar["DETAIL_PICTURE"] > 0) {
					$arResult["ORDERS"][$key]["BASKET_ITEMS"][$key2]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 30, "height" => 30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				}
			}
		}		
		
		if(in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"]) && is_array(CCatalogSku::GetProductInfo($arBasketItems["PRODUCT_ID"]))) {
			$arResult["ORDERS"][$key]["BASKET_ITEMS"][$key2]["DETAIL_PAGE_URL"] .= "?offer=".$arBasketItems["PRODUCT_ID"];
		}
		
		/***BASKET_ITEMS_PROPS***/
		$dbProp = CSaleBasket::GetPropsList(
			array("SORT" => "ASC", "ID" => "ASC"),
			array("BASKET_ID" => $arBasketItems["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID", "SUM_OF_CHARGE"))
		);
		while($arProp = $dbProp->GetNext()) {
			$arResult["ORDERS"][$key]["BASKET_ITEMS"][$key2]["PROPS"][] = $arProp;
		}
	endforeach;

	/***ORDER_PROPS***/
	$this->requestData["ID"] = $val["ORDER"]["ID"];
	$this->order = Sale\Order::load($this->requestData["ID"]);
	$order = $this->order;	
	$propertyCollection = $order->getPropertyCollection();
	
	foreach($propertyCollection as $property) {
		if(empty($arParams["PROP_".$val["ORDER"]["PERSON_TYPE_ID"]]) || !in_array($property->getField("ORDER_PROPS_ID"), $arParams["PROP_".$val["ORDER"]["PERSON_TYPE_ID"]])) {
			$propertyList = array_merge($property->getFieldValues(), $property->getProperty());

			if($propertyList["ACTIVE"] == "Y" && $propertyList["UTIL"] == "N") {
				if(empty($propertyList["VALUE"])) {
					continue;
				}				

				if($propertyList["MULTIPLE"] === "Y") {
					if($propertyList["TYPE"] === "FILE") {
						$fileList = "";
						foreach($propertyList["VALUE"] as $fileElement) {
							$fileList = $fileList.CFile::ShowFile($fileElement["ID"], 0, 90, 90, true)."<br/>";
						}
						$propertyList["VALUE"] = $fileList;
					} elseif($propertyList["TYPE"] === "LOCATION") {
						foreach($propertyList["VALUE"] as $locationElement) {
							$propertyList["VALUE"] = $propertyList["VALUE"].Location\Admin\LocationHelper::getLocationStringByCode($locationElement["VALUE"])."<br/>";
						}
					} elseif($propertyList["TYPE"] === "ENUM") {
						foreach($propertyList["VALUE"] as $enumElement) {
							$enumList[] = $propertyList["OPTIONS"][$enumElement["VALUE"]];
						}
						$propertyList["VALUE"] = serialize($enumList);
					} else {
						$propertyList["VALUE"] = serialize($propertyList["VALUE"]);
					}
				} else {
					if($propertyList["TYPE"] === "FILE") {
						$propertyList["VALUE"] = CFile::ShowFile($propertyList["VALUE"]["ID"], 0, 90, 90, true);
					} elseif($propertyList["TYPE"] === "LOCATION") {
						$locationName = Location\Admin\LocationHelper::getLocationStringByCode($propertyList["VALUE"]);
						$propertyList["VALUE"] = $locationName;
					} elseif($propertyList["TYPE"] === "ENUM") {
						$propertyList["VALUE"] = $propertyList["OPTIONS"][$propertyList["VALUE"]];
					}
				}
				$arResult["ORDERS"][$key]["ORDER"]["ORDER_PROPS"][] = $propertyList;
			}
		}
	}	
}?>