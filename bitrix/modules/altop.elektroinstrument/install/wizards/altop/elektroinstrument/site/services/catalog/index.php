<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$catalogSubscribe = $wizard->GetVar("catalogSubscribe");
$curSiteSubscribe = ($catalogSubscribe == "Y") ? array("use" => "Y", "del_after" => "100") : array("del_after" => "100");
$subscribe = COption::GetOptionString("sale", "subscribe_prod", "");
$arSubscribe = unserialize($subscribe);
$arSubscribe[WIZARD_SITE_ID] = $curSiteSubscribe;
COption::SetOptionString("sale", "subscribe_prod", serialize($arSubscribe));

$useStoreControl = $wizard->GetVar("useStoreControl");
$useStoreControl = ($useStoreControl == "Y") ? "Y" : "N";
$curUseStoreControl = COption::GetOptionString("catalog", "default_use_store_control", "N");
COption::SetOptionString("catalog", "default_use_store_control", $useStoreControl);

$productReserveCondition = $wizard->GetVar("productReserveCondition");
$productReserveCondition = (in_array($productReserveCondition, array("O", "P", "D", "S"))) ? $productReserveCondition : "P";
COption::SetOptionString("sale", "product_reserve_condition", $productReserveCondition);

if(CModule::IncludeModule("catalog")) {
	if($useStoreControl == "Y" && $curUseStoreControl == "N") {
		$dbStores = CCatalogStore::GetList(array(), array("ACTIVE" => 'Y'));
		if(!$dbStores->Fetch()) {
			$arStoreFields[] =  array(
				"TITLE" => GetMessage("STORE_NAME_1"),
				"ACTIVE" => "Y",
				"ADDRESS" => GetMessage("STORE_ADR_1"),
				"DESCRIPTION" => "",
				"GPS_N" => GetMessage("STORE_GPS_N_1"),
				"GPS_S" => GetMessage("STORE_GPS_S_1"),
				"PHONE" => "",
				"SCHEDULE" => GetMessage("STORE_SCHEDULE_1"),
				"XML_ID" => "store_1",
			);
			$arStoreFields[] = array(
				"TITLE" => GetMessage("STORE_NAME_2"),
				"ACTIVE" => "Y",
				"ADDRESS" => GetMessage("STORE_ADR_2"),
				"DESCRIPTION" => "",
				"GPS_N" => GetMessage("STORE_GPS_N_2"),
				"GPS_S" => GetMessage("STORE_GPS_S_2"),
				"PHONE" => "",
				"SCHEDULE" => GetMessage("STORE_SCHEDULE_2"),
				"XML_ID" => "store_2",
			);
			if(count($arStoreFields) > 0) {
				foreach($arStoreFields as $arStore) {
					$newStoreId = CCatalogStore::Add($arStore);
					if($newStoreId > 0)
						CCatalogDocs::synchronizeStockQuantity($newStoreId);
				}
			}
		}
	}
}

if(COption::GetOptionString("elektroinstrument", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

COption::SetOptionString("catalog", "allow_negative_amount", "N");
COption::SetOptionString("catalog", "default_can_buy_zero", "N");
COption::SetOptionString("catalog", "default_quantity_trace", "Y");