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
			
			if(COption::GetOptionString("elektroinstrument", "site_updated_3213", "N", $arResult["siteID"]) == "Y")
				return;			

			//REQUARE_WIZARD_UTILS//
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");

			if(!CModule::IncludeModule("iblock"))
				return;

            //IBLOCK_TYPES//
			$arTypes = array(
				array(
					"ID" => "seo",
					"SECTIONS" => "Y",
					"IN_RSS" => "N",
					"SORT" => 20,
					"LANG" => array()
				)				
			);
            
            GLOBAL $DB;
            CModule::IncludeModule("iblock");
			$iblockType = new CIBlockType;	

			foreach($arTypes as $arType) {
				$dbType = CIBlockType::GetList(array(), array("=ID" => $arType["ID"]));				
				if($dbType->Fetch())
					continue;
				
				$code = strtoupper($arType["ID"]);
					
				$arType["LANG"][LANGUAGE_ID]["NAME"] = GetMessage($code."_TYPE_NAME");
				$arType["LANG"][LANGUAGE_ID]["ELEMENT_NAME"] = GetMessage($code."_ELEMENT_NAME");
				if($arType["SECTIONS"] == "Y")
					$arType["LANG"][LANGUAGE_ID]["SECTION_NAME"] = GetMessage($code."_SECTION_NAME");				
				
				$DB->StartTransaction();
					
				$res = $iblockType->Add($arType);				
				if(!$res) {
					$DB->Rollback();
					$this->SetError("Error: ".$obBlocktype->LAST_ERROR);
				} else
					$DB->Commit();							
			};

			//CONTACT_FORM//
			$iblockXMLFile = $path."/xml/".LANGUAGE_ID."/contact_form.xml";
			$iblockCode = "contact_".$arResult["siteID"];
			$iblockType = "forms";

			$iblockID = false;
			$rsIblock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
			if($arIblock = $rsIblock->Fetch())
				$iblockID = $arIblock["ID"];

			if($iblockID == false) {
				$permissions = array(
					"1" => "X",
					"2" => "R"
				);

				$iblockID = WizardServices::ImportIBlockFromXML(
					$iblockXMLFile,
					$iblockCode,
					$iblockType,
					$arResult["siteID"],
					$permissions
				);

				if($iblockID < 1)
					return;

				//IBLOCK_FIELDS//
				$iblock = new CIBlock;
				$arFields = array(
					"ACTIVE" => "Y",
					"CODE" => $iblockCode, 
					"XML_ID" => $iblockCode,
				);
				$iblock->Update($iblockID, $arFields);

				//IBLOCK_PROPERTIES//
				$arProperty = array();
				$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $iblockID));
				while($arProp = $dbProperty->Fetch()) {
					$arProperty[$arProp["CODE"]] = $arProp["ID"];
				}
				
                //list user options
                CUserOptions::SetOption("list", "tbl_iblock_element_".md5($iblockType.".".$iblockID), array(
	               "columns" => "NAME,  PROPERTY_".$arProperty["NAME"].", PROPERTY_".$arProperty["EMAIL"].", PROPERTY_".$arProperty["MESSAGE"].", PROPERTY_".$arProperty["PHOTO"].", ACTIVE, SORT, TIMESTAMP_X, ID",
	               "by" => "timestamp_x",
	               "order" => "desc",
	               "page_size" => "20"
                ));
			} else {	
				$arSites = array(); 
				$db_res = CIBlock::GetSite($iblockID);
				while($res = $db_res->Fetch())
					$arSites[] = $res["LID"]; 
				if(!in_array($arResult["siteID"], $arSites)) {
					$arSites[] = $arResult["siteID"];
					$iblock = new CIBlock;
					$iblock->Update($iblockID, array("LID" => $arSites));
				}
			}			
					
			unset($iblockID, $iblockType, $iblockCode, $iblockXMLFile);
            
            //SEO//
			$iblockXMLFile = $path."/xml/".LANGUAGE_ID."/filter_seo.xml";
			$iblockCode = "filter_seo_".$arResult["siteID"];
			$iblockType = "seo";

			$iblockID = false;
			$rsIblock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
			if($arIblock = $rsIblock->Fetch())
				$iblockID = $arIblock["ID"];

			if($iblockID == false) {
				$permissions = array(
					"1" => "X",
					"2" => "R"
				);

				$iblockID = WizardServices::ImportIBlockFromXML(
					$iblockXMLFile,
					$iblockCode,
					$iblockType,
					$arResult["siteID"],
					$permissions
				);

				if($iblockID < 1)
					return;

				//IBLOCK_FIELDS//
				$iblock = new CIBlock;
				$arFields = array(
					"ACTIVE" => "Y",
					"CODE" => $iblockCode, 
					"XML_ID" => $iblockCode,
				);
				$iblock->Update($iblockID, $arFields);

				//IBLOCK_PROPERTIES//
				$arProperty = array();
				$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $iblockID));
				while($arProp = $dbProperty->Fetch()) {
					$arProperty[$arProp["CODE"]] = $arProp["ID"];
				}
				
                //list user options
                CUserOptions::SetOption("list", "tbl_iblock_element_".md5($iblockType.".".$iblockID), array(
	                "columns" => "NAME,  PROPERTY_".$arProperty["FILTER_URL_PAGE"].", PROPERTY_".$arProperty["FILTER_TITLE"].", PROPERTY_".$arProperty["FILTER_KEYWORDS"].", PROPERTY_".$arProperty["FILTER_HEADER"].", PROPERTY_".$arProperty["FILTER_DESCRIPTION"].",  PROPERTY_".$arProperty["FILTER_SEO_TEXT"].", ACTIVE, SORT, TIMESTAMP_X, ID",
	                "by" => "timestamp_x",
	                "order" => "desc",
	                "page_size" => "20"
                ));
			} else {	
				$arSites = array(); 
				$db_res = CIBlock::GetSite($iblockID);
				while($res = $db_res->Fetch())
					$arSites[] = $res["LID"]; 
				if(!in_array($arResult["siteID"], $arSites)) {
					$arSites[] = $arResult["siteID"];
					$iblock = new CIBlock;
					$iblock->Update($iblockID, array("LID" => $arSites));
				}
			}			
					
			unset($iblockID, $iblockType, $iblockCode, $iblockXMLFile);
			
			COption::SetOptionString("elektroinstrument", "site_updated_3213", "Y", false, $arResult["siteID"]);
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