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
			
			if(COption::GetOptionString("elektroinstrument", "site_updated_320", "N", $arResult["siteID"]) == "Y")
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

			if($iblockCatalogID > 0) {
				//SECTION_USER_FIELDS//
				$oUserTypeEntity = new CUserTypeEntity();

				//UF_SECTION_TITLE_H1//
				$dbRes = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockCatalogID."_SECTION", "FIELD_NAME" => "UF_SECTION_TITLE_H1"));
				if(!$dbRes->Fetch()) {
					$aUserFields = array(
						"ENTITY_ID" => "IBLOCK_".$iblockCatalogID."_SECTION",
						"FIELD_NAME" => "UF_SECTION_TITLE_H1",
						"USER_TYPE_ID" => "string",
						"XML_ID" => "UF_SECTION_TITLE_H1",
						"SORT" => 100,
						"MULTIPLE" => "N",
						"MANDATORY" => "N",
						"SHOW_FILTER" => "N",
						"SHOW_IN_LIST" => "",
						"EDIT_IN_LIST" => "",
						"IS_SEARCHABLE" => "N",
						"SETTINGS" => array(
							"SIZE" => "20",
							"ROWS" => "1"
						),
						"EDIT_FORM_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_SECTION_TITLE_H1")
						),
						"LIST_COLUMN_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_SECTION_TITLE_H1")
						),
						"LIST_FILTER_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_SECTION_TITLE_H1")
						)
					);
					if(!$oUserTypeEntity->Add($aUserFields))
						return;					
				}
				unset($aUserFields, $dbRes);	
			}
			COption::SetOptionString("elektroinstrument", "site_updated_320", "Y", false, $arResult["siteID"]);
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