<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
	return;

if($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["action"]) > 0 && check_bitrix_sessid()) {
	$APPLICATION->RestartBuffer();

	switch($_POST["action"]) {		
		case "ajax_recount_prices":
			if(strlen($_POST["currency"]) > 0) {
				$arPices = array("sumValue" => "");
				
				if($_POST["sumPrice"]) {					
					$price = CCurrencyLang::GetCurrencyFormat($_POST["currency"], LANGUAGE_ID);
					if(empty($price["THOUSANDS_SEP"])):
						$price["THOUSANDS_SEP"] = " ";
					endif;
					if($price["HIDE_ZERO"] == "Y"):
						if(round($_POST["sumPrice"], $price["DECIMALS"]) == round($_POST["sumPrice"], 0)):
							$price["DECIMALS"] = 0;
						endif;
					endif;
					$currency = str_replace("#", " ", $price["FORMAT_STRING"]);
					
					$arPices["sumValue"] = number_format($_POST["sumPrice"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);
				}				

				if(SITE_CHARSET != "utf-8") {
					$arPices = $APPLICATION->ConvertCharsetArray($arPices, SITE_CHARSET, "utf-8");
				}
				
				echo json_encode($arPices);
			}
			break;
		
		case "catalogSetAdd2Basket":
			if(is_array($_POST["set_ids"])) {				
				foreach($_POST["set_ids"] as $key => $itemId) {
					$productProperties = array();
					$productArtNumber = array();
					$productSelectProps = array();
					
					/***OFFER_PROPERTIES***/
					$iblockInfo = CCatalogSKU::GetInfoByOfferIBlock($itemId["IBLOCK_ID"]);
					if(is_array($iblockInfo) && !empty($_POST["setOffersCartProps"])) {
						$productProperties = CIBlockPriceTools::GetOfferProperties(
							$itemId["ID"],
							$iblockInfo["PRODUCT_IBLOCK_ID"],
							$_POST["setOffersCartProps"]
						);
					}					
					
					/***ARTNUMBER***/					
					$rsProps = CIBlockElement::GetProperty(
						$itemId["IBLOCK_ID"],
						$itemId["ID"],
						array("sort" => "asc", "enum_sort" => "asc", "value_id" => "asc"),
						array("CODE" => "ARTNUMBER", "EMPTY" => "N")
					);
					while($oneProp = $rsProps->Fetch()) {
						$productArtNumber = array(
							"NAME" => $oneProp["NAME"],
							"CODE" => $oneProp["CODE"],
							"VALUE" => $oneProp["VALUE"],
							"SORT" => 0
						);
						array_unshift($productProperties, $productArtNumber);
					}

					/***SELECT_PROPS***/
					if($key == 0 && !empty($_POST["setSelectProps"])) {						
						$sortIndex = count($productProperties);
						$selectProps = explode("||", $_POST["setSelectProps"]);
						foreach($selectProps as $arSelProp):
							$productSelectProps = unserialize(base64_decode(strtr($arSelProp, "-_,", "+/="))) + array("SORT" => $sortIndex++);
							array_push($productProperties, $productSelectProps);
						endforeach;						
					}
					
					$ratio = 1;
					if($_POST["itemsRatio"][$itemId["ID"]])
						$ratio = $_POST["itemsRatio"][$itemId["ID"]];
					
					$resBasket = CSaleBasket::GetList(
						array(), 
						array(
							"PRODUCT_ID" => $itemId["ID"],
							"FUSER_ID" => CSaleBasket::GetBasketUserID(),
							"LID" => $_POST["lid"],
							"ORDER_ID" => "NULL",
							"DELAY" => "Y"
						), 
						false, 
						false, 
						array("ID")
					);
					if($ar = $resBasket->Fetch()) {
						CSaleBasket::Update($ar["ID"], array("QUANTITY" => $ratio, "DELAY" => "N"));
					} else {
						Add2BasketByProductID($itemId["ID"], $ratio, array("LID" => $_POST["lid"]), $productProperties);
					}
				}
			}			
			break;
	}
	die();
}?>