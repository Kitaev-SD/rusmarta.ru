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
			
			if(COption::GetOptionString("elektroinstrument", "site_updated_324", "N", $arResult["siteID"]) == "Y")
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
			
			if(!CModule::IncludeModule("iblock"))
				return;

			//CATALOG//
			$iblockCatalogID = false;
			$rsIblock = CIBlock::GetList(array(), array("CODE" => "catalog_".$arResult["siteID"], "XML_ID" => "catalog_".$arResult["siteID"], "TYPE" => "catalog"));
			if($arIblock = $rsIblock->Fetch())
				$iblockCatalogID = $arIblock["ID"];
			
			//OFFERS//
			$iblockOffersID = false;
			$rsIblock = CIBlock::GetList(array(), array("CODE" => "offers_".$arResult["siteID"], "XML_ID" => "offers_".$arResult["siteID"], "TYPE" => "catalog"));
			if($arIblock = $rsIblock->Fetch())
				$iblockOffersID = $arIblock["ID"];

			
			//SLIDER//
			$iblockSliderID = false;
			$rsIblock = CIBlock::GetList(array(), array("CODE" => "slider_".$arResult["siteID"], "XML_ID" => "slider_".$arResult["siteID"], "TYPE" => "content"));
			if($arIblock = $rsIblock->Fetch())
				$iblockSliderID = $arIblock["ID"];
			
			if($iblockCatalogID > 0) {					
				$ibp = new CIBlockProperty;
				
				//PROPERTY_BACKGROUND_YOUTUBE//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "BACKGROUND_YOUTUBE", "IBLOCK_ID" => $iblockCatalogID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "BACKGROUND_YOUTUBE",
						"IBLOCK_ID" => $iblockCatalogID,
						"NAME" => GetMessage("PROPERTY_BACKGROUND_YOUTUBE"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "1",
						"PROPERTY_TYPE" => "S",
						"MULTIPLE" => "N"
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_OPEN_URL//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "OPEN_URL", "IBLOCK_ID" => $iblockSliderID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "OPEN_URL",
						"IBLOCK_ID" => $iblockSliderID,
						"NAME" => GetMessage("PROPERTY_OPEN_URL"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "2",
						"PROPERTY_TYPE" => "L",
						"MULTIPLE" => "N",
						"LIST_TYPE" => "C",
						"VALUES" => array(
							array(
								"VALUE" => GetMessage("PROPERTY_YES"),
								"XML_ID" => "Y",
								"SORT" => "1"
							)
						)
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_CODE_YOUTUBE//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "CODE_YOUTUBE", "IBLOCK_ID" => $iblockSliderID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "CODE_YOUTUBE",
						"IBLOCK_ID" => $iblockSliderID,
						"NAME" => GetMessage("PROPERTY_CODE_YOUTUBE"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "3",
						"PROPERTY_TYPE" => "S",
						"MULTIPLE" => "N"
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_PREVIEW_YOUTUBE//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "PREVIEW_YOUTUBE", "IBLOCK_ID" => $iblockSliderID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "PREVIEW_YOUTUBE",
						"IBLOCK_ID" => $iblockSliderID,
						"NAME" => GetMessage("PROPERTY_PREVIEW_YOUTUBE"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "4",
						"PROPERTY_TYPE" => "L",
						"MULTIPLE" => "N",
						"LIST_TYPE" => "C",
						"VALUES" => array(
							array(
								"VALUE" => GetMessage("PROPERTY_YES"),
								"XML_ID" => "Y",
								"SORT" => "1"
							)
						)
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_AUTOMATIC_PLAYBACK//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "AUTOMATIC_PLAYBACK", "IBLOCK_ID" => $iblockSliderID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "AUTOMATIC_PLAYBACK",
						"IBLOCK_ID" => $iblockSliderID,
						"NAME" => GetMessage("PROPERTY_AUTOMATIC_PLAYBACK"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "5",
						"PROPERTY_TYPE" => "L",
						"MULTIPLE" => "N",
						"LIST_TYPE" => "C",
						"HINT" => GetMessage("PROPERTY_AUTOMATIC_PLAYBACK_HINT"),
						"VALUES" => array(
							array(
								"VALUE" => GetMessage("PROPERTY_YES"),
								"XML_ID" => "Y",
								"SORT" => "1"
							)
						)
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_MUTE_AUDIO//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "MUTE_AUDIO", "IBLOCK_ID" => $iblockSliderID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "MUTE_AUDIO",
						"IBLOCK_ID" => $iblockSliderID,
						"NAME" => GetMessage("PROPERTY_MUTE_AUDIO"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "6",
						"PROPERTY_TYPE" => "L",
						"MULTIPLE" => "N",
						"LIST_TYPE" => "C",
						"VALUES" => array(
							array(
								"VALUE" => GetMessage("PROPERTY_YES"),
								"XML_ID" => "Y",
								"SORT" => "1"
							)
						)
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_PRODUCT_LINK//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "PRODUCT_LINK", "IBLOCK_ID" => $iblockSliderID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "PRODUCT_LINK",
						"IBLOCK_ID" => $iblockSliderID,
						"NAME" => GetMessage("PROPERTY_PRODUCT_LINK"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "7",
						"PROPERTY_TYPE" => "E",
						"MULTIPLE" => "N",
						"LINK_IBLOCK_ID" => $iblockCatalogID/*,
						"USER_TYPE_SETTINGS" => array("size"=>"1", "width"=>"0", "group"=>"N", "multiple"=>"Y")*/
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_PRODUCT_LOCATION//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "PRODUCT_LOCATION", "IBLOCK_ID" => $iblockSliderID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "PRODUCT_LOCATION",
						"IBLOCK_ID" => $iblockSliderID,
						"NAME" => GetMessage("PROPERTY_PRODUCT_LOCATION"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "8",
						"PROPERTY_TYPE" => "L",
						"MULTIPLE" => "N",
						"LIST_TYPE" => "L",
						"VALUES" => array(
							array(
								"VALUE" => GetMessage("PROPERTY_PRODUCT_LOCATION_LEFT"),
								"XML_ID" => "left",
								"SORT" => "1"
							),
							array(
								"VALUE" => GetMessage("PROPERTY_PRODUCT_LOCATION_RIGHT"),
								"XML_ID" => "right",
								"SORT" => "2"
							)
						)
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//PROPERTY_BACKGROUND_DIM_COLOR//
				$dbProperty = CIBlockProperty::GetList(array(), array("CODE" => "BACKGROUND_DIM_COLOR", "IBLOCK_ID" => $iblockSliderID));
				if(!$dbProperty->Fetch()) {
					$arFieldsAdd = array(
						"CODE" => "BACKGROUND_DIM_COLOR",
						"IBLOCK_ID" => $iblockSliderID,
						"NAME" => GetMessage("PROPERTY_BACKGROUND_DIM_COLOR"),
						"ACTIVE" => "Y",
						"IS_REQUIRED" => "N",
						"SORT" => "9",
						"PROPERTY_TYPE" => "S",
						"MULTIPLE" => "N"
					);
					if(!$ibp->Add($arFieldsAdd))
						return;
				}
				
				//SECTION_USER_FIELDS//
				$oUserTypeEntity = new CUserTypeEntity();

				//UF_YOUTUBE_BG//
				$dbRes = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockCatalogID."_SECTION", "FIELD_NAME" => "UF_YOUTUBE_BG"));
				if(!$dbRes->Fetch()) {
					$aUserFields = array(
						"ENTITY_ID" => "IBLOCK_".$iblockCatalogID."_SECTION",
						"FIELD_NAME" => "UF_YOUTUBE_BG",
						"USER_TYPE_ID" => "string",
						"XML_ID" => "UF_YOUTUBE_BG",
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
							LANGUAGE_ID => GetMessage("UF_YOUTUBE_BG")
						),
						"LIST_COLUMN_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_YOUTUBE_BG")
						),
						"LIST_FILTER_LABEL" => array(
							LANGUAGE_ID => GetMessage("UF_YOUTUBE_BG")
						)
					);
					if(!$oUserTypeEntity->Add($aUserFields))
						return;					
				}
				unset($aUserFields, $dbRes);
				
				//REPLACE_MACROS//
				CWizardUtil::ReplaceMacros($sitePath."include/viewed_products.php", array("ITEMS_IBLOCK_ID" => $iblockCatalogID));
				if($iblockOffersID > 0)
					CWizardUtil::ReplaceMacros($sitePath."include/viewed_products.php", array("OFFERS_IBLOCK_ID" => $iblockOffersID));
			}
			COption::SetOptionString("elektroinstrument", "site_updated_324", "Y", false, $arResult["siteID"]);
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