<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class Step0 extends CWizardStep {
	function InitStep() {
		$this->SetTitle(GetMessage("WIZARD_STEP_0"));
		$this->SetStepID("step0");		
		$this->SetNextStep("step1");
		$this->SetCancelStep("cancel");
	}

	function ShowStep() {
		$this->content = GetMessage("WIZARD_STEP_0_DESCR");
	}
}

class Step1 extends CWizardStep {
	function InitStep() {
		$this->SetTitle(GetMessage("WIZARD_STEP_1"));
		$this->SetNextStep("install");
		$this->SetStepID("step1");		
		$this->SetCancelStep("cancel");
		
		$dbSite = CSite::GetDefList();
		if($arSite = $dbSite->Fetch()) {			
			$wizard = &$this->GetWizard();
			$wizard->SetDefaultVars(
				array(
					"siteID" => $arSite["ID"]
				)
			);
		}
	}

	function ShowStep() {		
		$arSites = array();		
		$dbSite = CSite::GetList($b = "SORT", $o = "ASC", array("ACTIVE" => "Y"));
		while($arSite = $dbSite->Fetch()) {
			$arSites[$arSite["ID"]] = $arSite["NAME"];
			if($arSite["DEF"] == "Y")
				$defSite = $arSite["ID"];
		}
		
		$this->content .= "<b>".GetMessage("WIZARD_STEP_1_DESCR")."</b><br /><br />";
		$this->content .= $this->ShowSelectField("siteID", $arSites);
	}
}

class Install extends CWizardStep {
	function InitStep() {
		$this->SetTitle(GetMessage("WIZARD_STEP_INSTALL"));
		$this->SetStepID("install");		
		$this->SetNextStep("final");
		$this->SetCancelStep("cancel");
		
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("catalog");
		$wizard = &$this->GetWizard();
		$siteID = $wizard->GetVar("siteID", true);
		$rsIblocks = CIBlock::GetList(array(), array("TYPE" => "catalog", "SITE_ID" => $siteID, "ACTIVE" => "Y"));
		while($arIblock = $rsIblocks->Fetch()) {			
			$mxResult = CCatalogSKU::GetInfoByProductIBlock($arIblock["ID"]);
			if(is_array($mxResult)) {
				$wizard->SetDefaultVars(
					array(
						"iblockCatalogId" => $mxResult["PRODUCT_IBLOCK_ID"]
					)
				);
			}
		}
	}

	function OnPostForm() {
		$wizard = &$this->GetWizard();		
		if($wizard->IsNextButtonClick()) {
			$path = $wizard->package->path;
			$arResult = $wizard->GetVars(true);
			
			if(COption::GetOptionString("elektroinstrument", "site_updated_325", "N", $arResult["siteID"]) == "Y")
				return;
			
			//SITE_DIR//
			$dbSite = CSite::GetList($b = "SORT", $o = "ASC", array("ID" => $arResult["siteID"]));
			if($arSite = $dbSite->Fetch())
				$arResult["siteDir"] = $arSite["DIR"];

			if(empty($arResult["siteDir"]))
				$arResult["siteDir"] = "/";

			//SITE_PATH//
			$sitePath = $_SERVER["DOCUMENT_ROOT"].$arResult["siteDir"];
			
			//COPY_FILES//
			if(file_exists($_SERVER["DOCUMENT_ROOT"].$path."/public/".LANGUAGE_ID."/")) {
				CopyDirFiles(
					$_SERVER["DOCUMENT_ROOT"].$path."/public/".LANGUAGE_ID."/",
					$sitePath,
					$rewrite = true,
					$recursive = true,
					$delete_after_copy = false
				);
			}
			
			$iblockCatalogId = (int)$arResult["iblockCatalogId"];
			if($iblockCatalogId > 0) {
				//SECTION_USER_FIELDS//
				$oUserTypeEntity = new CUserTypeEntity();

				//UF_ICON//
				$dbRes = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockCatalogId."_SECTION", "FIELD_NAME" => "UF_ICON"));
				if(!$dbRes->Fetch()) {
					$aUserFields = array(
						"ENTITY_ID" => "IBLOCK_".$iblockCatalogId."_SECTION",
						"FIELD_NAME" => "UF_ICON",
						"USER_TYPE_ID" => "string",
						"XML_ID" => "UF_ICON",
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
							LANGUAGE_ID => GetMessage("UF_ICON")
						),
						"LIST_COLUMN_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_ICON")
						),
						"LIST_FILTER_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_ICON")
						)
					);
					if(!$oUserTypeEntity->Add($aUserFields))
						return;					
				}
				unset($aUserFields, $dbRes);

				//REQUARE_WIZARD_UTILS//
				require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
				require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");
				
				//REPLACE_MACROS//
				CWizardUtil::ReplaceMacros($sitePath."include/sections.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogId));				
				CWizardUtil::ReplaceMacros($sitePath."include/recommend.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogId, "SITE_DIR" => $arResult["siteDir"]));
				CWizardUtil::ReplaceMacros($sitePath.".left.menu_ext.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogId));
				
				$dbProperty = CIBlockProperty::GetPropertyEnum("THIS_COLLECTION", array(), array("IBLOCK_ID" => $iblockCatalogId));
				if($arProp = $dbProperty->GetNext()) {
					CWizardUtil::ReplaceMacros($sitePath."include/recommend.php", array("ITEMS_PROPERTY_THIS_COLLECTION" => $arProp["PROPERTY_ID"], "ITEMS_PROPERTY_THIS_COLLECTION_VALUE" => $arProp["ID"]));
				}
			}

			COption::SetOptionString("elektroinstrument", "site_updated_325", "Y", false, $arResult["siteID"]);
		}
	}
	
	function ShowStep() {		
		$wizard = &$this->GetWizard();
		$siteID = $wizard->GetVar("siteID", true);
		
		CModule::IncludeModule("iblock");
		$rsIblocks = CIBlock::GetList(array(), array("TYPE" => "catalog", "SITE_ID" => $siteID, "ACTIVE" => "Y"));
		while($arIblock = $rsIblocks->Fetch()) {
			$iblocks[$arIblock["ID"]] = $arIblock["NAME"]." (".$arIblock["ID"].")";
		}
		
		$this->content .= "<b>".GetMessage("WIZARD_STEP_INSTALL_DESCR")."</b><br /><br />";
		$this->content .= $this->ShowSelectField("iblockCatalogId", $iblocks);
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
}