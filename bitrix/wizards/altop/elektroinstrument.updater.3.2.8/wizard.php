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
			$arSites[$arSite["ID"]] = $arSite["NAME"]." (".$arSite["ID"].")";
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
		$rsIblocks = CIBlock::GetList(array(), array("SITE_ID" => $siteID, "ACTIVE" => "Y"));
		while($arIblock = $rsIblocks->Fetch()) {			
			if($arIblock["IBLOCK_TYPE_ID"] == "catalog") {
				$mxResult = CCatalogSKU::GetInfoByProductIBlock($arIblock["ID"]);
				if(is_array($mxResult))
					$iblockCatalogId = $mxResult["PRODUCT_IBLOCK_ID"];
			} elseif($arIblock["IBLOCK_TYPE_ID"] == "content") {
				if($arIblock["CODE"] == "contacts_".$siteID)
					$iblockContactsId = $arIblock["ID"];
			}
		}
		$wizard->SetDefaultVars(
			array(
				"iblockCatalogId" => $iblockCatalogId,
				"iblockContactsId" => $iblockContactsId
			)
		);
	}

	function OnPostForm() {
		$wizard = &$this->GetWizard();		
		if($wizard->IsNextButtonClick()) {
			$path = $wizard->package->path;
			$arResult = $wizard->GetVars(true);
			
			if(COption::GetOptionString("elektroinstrument", "site_updated_328", "N", $arResult["siteID"]) == "Y")
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
				//REQUARE_WIZARD_UTILS//
				require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
				require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");
				
				//REPLACE_MACROS//							
				CWizardUtil::ReplaceMacros($sitePath."include/discount.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogId, "SITE_DIR" => $arResult["siteDir"]));
				CWizardUtil::ReplaceMacros($sitePath."include/newproduct.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogId, "SITE_DIR" => $arResult["siteDir"]));
				CWizardUtil::ReplaceMacros($sitePath."include/recommend.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogId, "SITE_DIR" => $arResult["siteDir"]));
				CWizardUtil::ReplaceMacros($sitePath."include/saleleader.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogId, "SITE_DIR" => $arResult["siteDir"]));
				CWizardUtil::ReplaceMacros($sitePath."include/slider_left.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogId, "SITE_DIR" => $arResult["siteDir"]));
				
				$dbProperty = CIBlockProperty::GetPropertyEnum("THIS_COLLECTION", array(), array("IBLOCK_ID" => $iblockCatalogId));
				if($arProp = $dbProperty->GetNext()) {
					CWizardUtil::ReplaceMacros($sitePath."include/recommend.php", array("ITEMS_PROPERTY_THIS_COLLECTION" => $arProp["PROPERTY_ID"], "ITEMS_PROPERTY_THIS_COLLECTION_VALUE" => $arProp["ID"]));
				}
			}

			$iblockContactsId = (int)$arResult["iblockContactsId"];
			if($iblockContactsId > 0) {
				$ibp = new CIBlockProperty;

				//PROPERTY_LOCATION//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "LOCATION", "IBLOCK_ID" => $iblockContactsId));
				if($arProperty = $dbProperty->Fetch()) {
					$arFieldsUpdate = array(
						"SORT" => "1"
					);
					if(!$ibp->Update($arProperty["ID"], $arFieldsUpdate))
						return;
				}
				
				//PROPERTY_PHONE_MASK//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "PHONE_MASK", "IBLOCK_ID" => $iblockContactsId));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "PHONE_MASK",
						"IBLOCK_ID" => $iblockContactsId,
						"NAME" => GetMessage("PROPERTY_PHONE_MASK"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "2",
						"PROPERTY_TYPE" => "S",
						"MULTIPLE" => "N"
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}

				//PROPERTY_VALIDATE_PHONE_MASK//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "VALIDATE_PHONE_MASK", "IBLOCK_ID" => $iblockContactsId));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "VALIDATE_PHONE_MASK",
						"IBLOCK_ID" => $iblockContactsId,
						"NAME" => GetMessage("PROPERTY_VALIDATE_PHONE_MASK"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "3",
						"PROPERTY_TYPE" => "S",
						"MULTIPLE" => "N"
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
			}

			COption::SetOptionString("elektroinstrument", "site_updated_328", "Y", false, $arResult["siteID"]);
		}
	}
	
	function ShowStep() {		
		$wizard = &$this->GetWizard();
		$siteID = $wizard->GetVar("siteID", true);
		
		CModule::IncludeModule("iblock");
		$rsIblocks = CIBlock::GetList(array(), array("SITE_ID" => $siteID, "ACTIVE" => "Y"));
		while($arIblock = $rsIblocks->Fetch()) {
			if($arIblock["IBLOCK_TYPE_ID"] == "catalog")
				$catalogIblocks[$arIblock["ID"]] = $arIblock["NAME"]." (".$arIblock["ID"].")";
			elseif($arIblock["IBLOCK_TYPE_ID"] == "content")
				$contentIblocks[$arIblock["ID"]] = $arIblock["NAME"]." (".$arIblock["ID"].")";
		}
		
		$this->content .= "<b>".GetMessage("WIZARD_STEP_INSTALL_DESCR_0")."</b><br /><br />";
		$this->content .= $this->ShowSelectField("iblockCatalogId", $catalogIblocks)."<br /><br />";
		$this->content .= "<b>".GetMessage("WIZARD_STEP_INSTALL_DESCR_1")."</b><br /><br />";
		$this->content .= $this->ShowSelectField("iblockContactsId", $contentIblocks);
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