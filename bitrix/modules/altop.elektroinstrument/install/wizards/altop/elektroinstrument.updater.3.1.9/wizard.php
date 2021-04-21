<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class Step0 extends CWizardStep {
	function InitStep() {
		$this->SetTitle(GetMessage("WIZARD_STEP_0"));
		$this->SetStepID("step0");		
		$this->SetNextStep("install");
		$this->SetCancelStep("cancel");
	}

	function ShowStep() {
		$this->content = GetMessage("WIZARD_STEP_0_DESCR");
	}
}

class Install extends CWizardStep {
	function InitStep() {
		$this->SetTitle(GetMessage("WIZARD_STEP_INSTALL"));
		$this->SetStepID("install");		
		$this->SetNextStep("final");
		$this->SetCancelStep("cancel");
		
		$dbSite = CSite::GetDefList();
		if($arSite = $dbSite->Fetch()) {			
			$wizard = &$this->GetWizard();
			$wizard->SetDefaultVars(
				array(
					"siteID" => $arSite["ID"],
					"siteDir" => $arSite["DIR"]
				)
			);
		}
	}

	function OnPostForm() {
		$wizard = &$this->GetWizard();		
		if($wizard->IsNextButtonClick()) {
			$path = $wizard->package->path;
			$arResult = $wizard->GetVars(true);
			
			if(COption::GetOptionString("elektroinstrument", "site_updated_319", "N", $arResult["siteID"]) == "Y")
				return;
			
			//SITE_DIR//
			$dbSite = CSite::GetList($b = "SORT", $o = "ASC", array("ID" => $arResult["siteID"]));
			if($arSite = $dbSite->Fetch())
				$arResult["siteDir"] = $arSite["DIR"];

			if(empty($arResult["siteDir"]))
				$arResult["siteDir"] = "/";

			//SITE_PATH//
			$sitePath = $_SERVER["DOCUMENT_ROOT"].$arResult["siteDir"];

			//REQUARE_WIZARD_UTILS//
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");

			if(!CModule::IncludeModule("iblock"))
				return;

			//CATALOG//
			$iblockCatalogID = false;
			$rsIblock = CIBlock::GetList(array(), array("CODE" => "catalog_".$arResult["siteID"], "XML_ID" => "catalog_".$arResult["siteID"], "TYPE" => "catalog"));
			if($arIblock = $rsIblock->Fetch())
				$iblockCatalogID = $arIblock["ID"];
			
			//COLORS//
			$iblockColorsID = false;
			$rsIblock = CIBlock::GetList(array(), array("CODE" => "colors_".$arResult["siteID"], "XML_ID" => "colors_".$arResult["siteID"], "TYPE" => "catalog"));
			if($arIblock = $rsIblock->Fetch())
				$iblockColorsID = $arIblock["ID"];

			if($iblockCatalogID > 0) {					
				$ibp = new CIBlockProperty;
				
				//PROPERTY_THIS_COLLECTION//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "THIS_COLLECTION", "IBLOCK_ID" => $iblockCatalogID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "THIS_COLLECTION",
						"IBLOCK_ID" => $iblockCatalogID,
						"NAME" => GetMessage("PROPERTY_THIS_COLLECTION"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "7",
						"PROPERTY_TYPE" => "L",
						"MULTIPLE" => "N",
						"LIST_TYPE" => "C",
						"VALUES" => array(
							array(
								"VALUE" => "Да",
								"XML_ID" => "Y",
								"SORT" => "1"
							)
						)
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_COLLECTION_SECTION//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "COLLECTION_SECTION", "IBLOCK_ID" => $iblockCatalogID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "COLLECTION_SECTION",
						"IBLOCK_ID" => $iblockCatalogID,
						"NAME" => GetMessage("PROPERTY_COLLECTION_SECTION"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "7",
						"PROPERTY_TYPE" => "L",
						"MULTIPLE" => "N",
						"LIST_TYPE" => "L",
						"VALUES" => array(
							array(
								"VALUE" => GetMessage("PROPERTY_COLLECTION_SECTION_VALUE_1"),
								"XML_ID" => "0ff70089d4752a19f0f1cccdeb8321f9",
								"SORT" => "1"
							),
							array(
								"VALUE" => GetMessage("PROPERTY_COLLECTION_SECTION_VALUE_2"),
								"XML_ID" => "7f285f7cd80142db52c1dbd7bc3bd925",
								"SORT" => "2"
							),
							array(
								"VALUE" => GetMessage("PROPERTY_COLLECTION_SECTION_VALUE_3"),
								"XML_ID" => "2ee1473447f42579533d98d9e206ea54",
								"SORT" => "3"
							)
						)
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_COLLECTION//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "COLLECTION", "IBLOCK_ID" => $iblockCatalogID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "COLLECTION",
						"IBLOCK_ID" => $iblockCatalogID,
						"NAME" => GetMessage("PROPERTY_COLLECTION"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "7",
						"PROPERTY_TYPE" => "E",
						"MULTIPLE" => "Y",
						"LINK_IBLOCK_ID" => $iblockCatalogID
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_VERSIONS_PERFORMANCE//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "VERSIONS_PERFORMANCE", "IBLOCK_ID" => $iblockCatalogID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "VERSIONS_PERFORMANCE",
						"IBLOCK_ID" => $iblockCatalogID,
						"NAME" => GetMessage("PROPERTY_VERSIONS_PERFORMANCE"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "8",
						"PROPERTY_TYPE" => "E",
						"MULTIPLE" => "Y",
						"USER_TYPE" => "EList",
						"LINK_IBLOCK_ID" => $iblockColorsID,
						"USER_TYPE_SETTINGS" => array("size"=>"1", "width"=>"0", "group"=>"N", "multiple"=>"Y")
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}

				//SECTION_USER_FIELDS//
				$oUserTypeEntity = new CUserTypeEntity();

				//UF_VIEW_COLLECTION//
				$dbRes = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockCatalogID."_SECTION", "FIELD_NAME" => "UF_VIEW_COLLECTION"));
				if(!$dbRes->Fetch()) {
					$aUserFields = array(
						"ENTITY_ID" => "IBLOCK_".$iblockCatalogID."_SECTION",
						"FIELD_NAME" => "UF_VIEW_COLLECTION",
						"USER_TYPE_ID" => "boolean",
						"XML_ID" => "UF_VIEW_COLLECTION",
						"SORT" => 100,
						"MULTIPLE" => "N",
						"MANDATORY" => "N",
						"SHOW_FILTER" => "N",
						"SHOW_IN_LIST" => "",
						"EDIT_IN_LIST" => "",
						"IS_SEARCHABLE" => "N",
						"SETTINGS" => array(
							"DEFAULT_VALUE" => "N",
							"DISPLAY" => "CHECKBOX"
						),
						"EDIT_FORM_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_VIEW_COLLECTION")
						),
						"LIST_COLUMN_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_VIEW_COLLECTION")
						),
						"LIST_FILTER_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_VIEW_COLLECTION")
						)
					);
					if(!$oUserTypeEntity->Add($aUserFields))
						return;					
				}
				unset($aUserFields, $dbRes);	
			}
			COption::SetOptionString("elektroinstrument", "site_updated_319", "Y", false, $arResult["siteID"]);
		}
	}
	
	function ShowStep() {
		$arSites = array();
		$dbSite = CSite::GetList($b = "SORT", $o = "ASC", array("ACTIVE" => "Y"));
		while($arSite = $dbSite->Fetch()) {
			$arSites[$arSite["ID"]] = $arSite["NAME"]." (".$arSite["ID"].")";
			if($arSite["DEF"] == "Y")
				$defSite = $arSite["ID"];
		}
		
		$this->content .= "<b>".GetMessage("WIZARD_STEP_INSTALL_1")."</b><br /><br />";
		$this->content .= $this->ShowSelectField("siteID", $arSites);
	}
}

class FinalStep extends CWizardStep {
	function InitStep() {
		$this->SetTitle(GetMessage("WIZARD_STEP_FINAL"));
		$this->SetStepID("final");
		$this->SetCancelCaption(GetMessage("WIZARD_CLOSE"));
		$this->SetCancelStep("final");
	}

	function ShowStep() {
		$wizard = &$this->GetWizard();
		$arResult = $wizard->GetVars(true);

		$arSite = CSite::GetList($b = "SORT", $o = "ASC", array("ID" => $arResult["siteID"]))->Fetch();
		
		$this->content .= GetMessage("WIZARD_STEP_FINAL_DESCR", array("#SITE_NAME#" => $arSite["NAME"], "#SITE_ID#" => $arSite["ID"]));
	}
}

class CancelStep extends CWizardStep {
	function InitStep() {
		$this->SetTitle(GetMessage("WIZARD_STEP_CANCEL"));
		$this->SetStepID("cancel");
		$this->SetCancelCaption(GetMessage("WIZARD_CLOSE"));
		$this->SetCancelStep("cancel");
	}

	function ShowStep() {
		$this->content .= GetMessage("WIZARD_STEP_CANCEL_DESCR");
	}
}?>