<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep {
	function InitStep() {
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "elektroinstrument";
	}
}

class SelectTemplateStep extends CSelectTemplateWizardStep {
	function InitStep() {
		parent::InitStep();
		$this->SetNextStep("site_settings");
	}

	function OnPostForm() {
		$wizard =& $this->GetWizard();
		
		$proactive = COption::GetOptionString("statistic", "DEFENCE_ON", "N");
		if($proactive == "Y") {
			COption::SetOptionString("statistic", "DEFENCE_ON", "N");
			$wizard->SetVar("proactive", "Y");
		} else {
			$wizard->SetVar("proactive", "N");			
		}

		if($wizard->IsNextButtonClick()) {
			$arTemplates = array("elektro_flat");

			$templateID = $wizard->GetVar("wizTemplateID");

			if(!in_array($templateID, $arTemplates))
				$this->SetError(GetMessage("wiz_template"));

			if(in_array($templateID, $arTemplates))
				$wizard->SetVar("templateID", "elektro_flat");
		}
	}

	function ShowStep() {
		$wizard =& $this->GetWizard();

		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		$arTemplates = WizardServices::GetTemplates($templatesPath);

		$arTemplateOrder = array();

		if(in_array("elektro_flat", array_keys($arTemplates))) {
			$arTemplateOrder[] = "elektro_flat";
		}

		$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "elektro_flat", $wizard->GetVar("siteID"));
		if(!in_array($defaultTemplateID, array("elektro_flat"))) $defaultTemplateID = "elektro_flat";
		$wizard->SetDefaultVar("wizTemplateID", $defaultTemplateID);

		$arTemplateInfo = array(
			"elektro_flat" => array(
				"NAME" => GetMessage("WIZ_TEMPLATE_NAME"),
				"DESCRIPTION" => "",
				"PREVIEW" => $wizard->GetPath()."/site/templates/elektro_flat/lang/".LANGUAGE_ID."/preview.gif",
				"SCREENSHOT" => $wizard->GetPath()."/site/templates/elektro_flat/lang/".LANGUAGE_ID."/screen.gif",
			),
		);
		
		global $SHOWIMAGEFIRST;
		$SHOWIMAGEFIRST = true;

		$this->content .= "<div class='inst-template-list-block'>";
		foreach($arTemplateOrder as $templateID) {
			$arTemplate = $arTemplateInfo[$templateID];

			if(!$arTemplate)
				continue;

			$this->content .= "<div class='inst-template-description'>";
			$this->content .= $this->ShowRadioField("wizTemplateID", $templateID, Array("id" => $templateID, "class" => "inst-template-list-inp"));

			global $SHOWIMAGEFIRST;
			$SHOWIMAGEFIRST = true;

			if($arTemplate["SCREENSHOT"] && $arTemplate["PREVIEW"])
				$this->content .= CFile::Show2Images($arTemplate["PREVIEW"], $arTemplate["SCREENSHOT"], 150, 150, " class='inst-template-list-img'");
			else
				$this->content .= CFile::ShowImage($arTemplate["SCREENSHOT"], 150, 150, " class='inst-template-list-img'", "", true);

			$this->content .= "<label for='".$templateID."' class='inst-template-list-label'>".$arTemplate["NAME"]."</label>";
			$this->content .= "</div>";
		}

		$this->content .= "</div>";
	}
}

class SiteSettingsStep extends CSiteSettingsWizardStep{
	function InitStep() {
		parent::InitStep();
		$this->SetPrevStep("select_template");
		
		$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		
		$wizard =& $this->GetWizard();
		$siteID = $wizard->GetVar("siteID");
		
		if(COption::GetOptionString("elektroinstrument", "wizard_installed", "N", $siteID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
			$this->SetNextStep("data_install");
		else {
			$this->SetNextStep("catalog_settings");
		}
		
		$wizard->SetDefaultVars(
			Array(
				"siteName" => GetMessage("WIZ_SITE_NAME_DEF"),
				"siteEmail" => COption::GetOptionString("elektroinstrument", "siteEmail", "info@".$_SERVER["SERVER_NAME"], $siteID)
			)
		);
	}

	function ShowStep() {
		$wizard =& $this->GetWizard();

		$this->content .= "<div class='wizard-input-form'>";

		$this->content .= "
		<div class='wizard-input-form-block'>
			<label for='siteName' class='wizard-input-title'>".GetMessage("WIZ_SITE_NAME")."</label>
			".$this->ShowInputField("text", "siteName", array("id" => "siteName", "class" => "wizard-field"))."
		</div>";		
		
		$this->content .= "<div class='wizard-input-form-block'>
			<label for='siteEmail' class='wizard-input-title'>".GetMessage("WIZ_SITE_EMAIL")."</label>
			".$this->ShowInputField("text", "siteEmail", array("id" => "siteEmail", "class" => "wizard-field"))."
		</div>";
		
		$firstStep = COption::GetOptionString("main", "wizard_first".substr($wizard->GetID(), 7)."_".$wizard->GetVar("siteID"), false, $wizard->GetVar("siteID"));
		
		//install Demo data		
		if($firstStep == "Y") {
			$this->content .= "
			<div class='wizard-input-form-block'".(LANGUAGE_ID != "ru" ? " style='display:none'" : "").">
				".$this->ShowCheckboxField("installDemoData", "Y", array("id" => "installDemoData"))."
				<label for='installDemoData'>".GetMessage("wiz_structure_data")."</label>
			</div>";
		} else {
			$this->content .= $this->ShowHiddenField("installDemoData", "Y");
		}
		
		$this->content .= "</div>";
	}
}

class CatalogSettings extends CWizardStep{
	function InitStep() {
		$this->SetStepID("catalog_settings");
		$this->SetTitle(GetMessage("WIZ_STEP_CT"));
		if(LANGUAGE_ID != "ru")
			$this->SetNextStep("pay_system");
		else
			$this->SetNextStep("shop_settings");
		$this->SetPrevStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteID = $wizard->GetVar("siteID");
		
		$subscribe = COption::GetOptionString("sale", "subscribe_prod", "");
		$arSubscribe = unserialize($subscribe);

		$wizard->SetDefaultVars(
			Array(
				"catalogSubscribe" => (isset($arSubscribe[$siteID])) ? ($arSubscribe[$siteID]["use"] == "Y" ? "Y" : false) : "Y",
				"useStoreControl" => COption::GetOptionString("catalog", "default_use_store_control", "Y"),
				"productReserveCondition" => COption::GetOptionString("sale", "product_reserve_condition", "P")
			)
		);
	}

	function ShowStep() {
		$wizard =& $this->GetWizard();
		
		$this->content .= "
			<div class='wizard-input-form-block'>
				<div class='wizard-catalog-title'>".GetMessage("WIZ_CATALOG_USE_STORE_CONTROL")."</div>
				<div>
					<div class='wizard-catalog-form-item'>
						".$this->ShowCheckboxField("useStoreControl", "Y", array("id" => "use-store-control"))
						."<label for='use-store-control'>".GetMessage("WIZ_STORE_CONTROL")."</label>
					</div>";

		$arConditions = array(
			"O" => GetMessage("SALE_PRODUCT_RESERVE_1_ORDER"),
			"P" => GetMessage("SALE_PRODUCT_RESERVE_2_PAYMENT"),
			"D" => GetMessage("SALE_PRODUCT_RESERVE_3_DELIVERY"),
			"S" => GetMessage("SALE_PRODUCT_RESERVE_4_DEDUCTION")
		);

		foreach($arConditions as $conditionID => $conditionName) {
			$arReserveConditions[$conditionID] = $conditionName;
		}
		$this->content .= "
			<div class='wizard-catalog-form-item'>"
				.$this->ShowSelectField("productReserveCondition", $arReserveConditions).
				"<label>".GetMessage("SALE_PRODUCT_RESERVE_CONDITION")."</label>
			</div>";
		$this->content .= "</div>
			</div>";
	}
}

class ShopSettings extends CWizardStep {
	function InitStep() {
		$this->SetStepID("shop_settings");
		$this->SetTitle(GetMessage("WIZ_STEP_SS"));
		$this->SetNextStep("person_type");
		$this->SetPrevStep("catalog_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteID = $wizard->GetVar("siteID");
		
		$wizard->SetDefaultVars(
			Array(
				"shopLocalization" => COption::GetOptionString("elektroinstrument", "shopLocalization", "ru", $siteID),
				"shopEmail" => COption::GetOptionString("elektroinstrument", "shopEmail", "info@".$_SERVER["SERVER_NAME"], $siteID),
				"shopOfName" => COption::GetOptionString("elektroinstrument", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
				"shopLocation" => COption::GetOptionString("elektroinstrument", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
				"shopAdr" => COption::GetOptionString("elektroinstrument", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
				"shopINN" => COption::GetOptionString("elektroinstrument", "shopINN", "1234567890", $siteID),
				"shopKPP" => COption::GetOptionString("elektroinstrument", "shopKPP", "123456789", $siteID),
				"shopNS" => COption::GetOptionString("elektroinstrument", "shopNS", "0000 0000 0000 0000 0000", $siteID),
				"shopBANK" => COption::GetOptionString("elektroinstrument", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
				"shopBANKREKV" => COption::GetOptionString("elektroinstrument", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
				"shopKS" => COption::GetOptionString("elektroinstrument", "shopKS", "30101 810 4 0000 0000225", $siteID),
				
				"shopOfName_ua" => COption::GetOptionString("elektroinstrument", "shopOfName_ua", GetMessage("WIZ_SHOP_OF_NAME_DEF_UA"), $siteID),
				"shopLocation_ua" => COption::GetOptionString("elektroinstrument", "shopLocation_ua", GetMessage("WIZ_SHOP_LOCATION_DEF_UA"), $siteID),
				"shopAdr_ua" => COption::GetOptionString("elektroinstrument", "shopAdr_ua", GetMessage("WIZ_SHOP_ADR_DEF_UA"), $siteID),
				"shopEGRPU_ua" =>  COption::GetOptionString("elektroinstrument", "shopEGRPU_ua", "", $siteID),
				"shopINN_ua" =>  COption::GetOptionString("elektroinstrument", "shopINN_ua", "", $siteID),
				"shopNDS_ua" =>  COption::GetOptionString("elektroinstrument", "shopNDS_ua", "", $siteID),
				"shopNS_ua" =>  COption::GetOptionString("elektroinstrument", "shopNS_ua", "", $siteID),
				"shopBank_ua" =>  COption::GetOptionString("elektroinstrument", "shopBank_ua", "", $siteID),
				"shopMFO_ua" =>  COption::GetOptionString("elektroinstrument", "shopMFO_ua", "", $siteID),
				"shopPlace_ua" =>  COption::GetOptionString("elektroinstrument", "shopPlace_ua", "", $siteID),
				"shopFIO_ua" =>  COption::GetOptionString("elektroinstrument", "shopFIO_ua", "", $siteID),
				"shopTax_ua" =>  COption::GetOptionString("elektroinstrument", "shopTax_ua", "", $siteID),

				"installPriceBASE" => COption::GetOptionString("elektroinstrument", "installPriceBASE", "Y", $siteID),
			)
		);
	}

	function ShowStep() {
		$wizard =& $this->GetWizard();
		
		if(!CModule::IncludeModule("catalog")) {
			$this->content .= "<p style='color:red'>".GetMessage("WIZ_NO_MODULE_CATALOG")."</p>";
			$this->SetNextStep("shop_settings");
		} else {
			$this->content .=
				"<div class='wizard-catalog-title'>".GetMessage("WIZ_SHOP_LOCALIZATION")."</div>
				<div class='wizard-input-form-block' >".
					$this->ShowSelectField("shopLocalization", array(
						"ru" => GetMessage("WIZ_SHOP_LOCALIZATION_RUSSIA"),
						"ua" => GetMessage("WIZ_SHOP_LOCALIZATION_UKRAINE"),
						"kz" => GetMessage("WIZ_SHOP_LOCALIZATION_KAZAKHSTAN"),
						"by" => GetMessage("WIZ_SHOP_LOCALIZATION_BELARUS")
					), array("onchange" => "langReload()", "id" => "localization_select","class" => "wizard-field", "style"=>"padding:0 0 0 15px"))."
				</div>";

			$currentLocalization = $wizard->GetVar("shopLocalization");
			if(empty($currentLocalization))
				$currentLocalization = $wizard->GetDefaultVar("shopLocalization");

			$this->content .= "<div class='wizard-catalog-title'>".GetMessage("WIZ_STEP_SS")."</div>
				<div class='wizard-input-form'>";

			$this->content .= "
				<div class='wizard-input-form-block'>
					<label class='wizard-input-title' for='shopEmail'>".GetMessage("WIZ_SHOP_EMAIL")."</label>
					".$this->ShowInputField("text", "shopEmail", array("id" => "shopEmail", "class" => "wizard-field"))."
				</div>";

			//ru
			$this->content .= "<div id='ru_bank_details' class='wizard-input-form-block' style='display:".(($currentLocalization == "ru" || $currentLocalization == "kz" || $currentLocalization == "by") ? "block" : "none")."'>
				<div class='wizard-input-form-block'>
					<label class='wizard-input-title' for='shopOfName'>".GetMessage("WIZ_SHOP_OF_NAME")."</label>"
					.$this->ShowInputField("text", "shopOfName", array("id" => "shopOfName", "class" => "wizard-field"))."
				</div>";
	
			$this->content .= "
				<div class='wizard-input-form-block'>
					<label class='wizard-input-title' for='shopLocation'>".GetMessage("WIZ_SHOP_LOCATION")."</label>"
					.$this->ShowInputField("text", "shopLocation", array("id" => "shopLocation", "class" => "wizard-field"))."
				</div>";
	
			$this->content .= "
				<div class='wizard-input-form-block'>
					<label class='wizard-input-title' for='shopAdr'>".GetMessage("WIZ_SHOP_ADR")."</label>"
					.$this->ShowInputField("textarea", "shopAdr", array("rows" => "3", "id" => "shopAdr", "class" => "wizard-field"))."
				</div>";

			$this->content .= "
				<div class='wizard-catalog-title'>".GetMessage("WIZ_SHOP_BANK_TITLE")."</div>
				<table class='wizard-input-table'>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_INN").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopINN", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_KPP").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopKPP", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_NS").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopNS", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_BANK").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopBANK", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_BANKREKV").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopBANKREKV", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_KS").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopKS", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_STAMP").":</td>
						<td class='wizard-input-table-right'>".$this->ShowFileField("siteStamp", Array("show_file_info" => "N", "id" => "siteStamp"))."</td>
					</tr>
				</table>
			</div>";
			
			//ua
			$this->content .= "<div id='ua_bank_details' class='wizard-input-form-block' style='display:".(($currentLocalization == "ua") ? "block" : "none")."'>
				<div class='wizard-input-form-block'>
					<label class='wizard-input-title' for='shopOfName_ua'>".GetMessage("WIZ_SHOP_OF_NAME")."</label>"
					.$this->ShowInputField("text", "shopOfName_ua", array("id" => "shopOfName_ua", "class" => "wizard-field"))."
					<p style='color:grey; margin: 3px 0 7px;'>".GetMessage("WIZ_SHOP_OF_NAME_DESCR_UA")."</p>
				</div>";

			$this->content .= "<div class='wizard-input-form-block'>
					<label class='wizard-input-title' for='shopLocation_ua'>".GetMessage("WIZ_SHOP_LOCATION")."</label>"
					.$this->ShowInputField("text", "shopLocation_ua", array("id" => "shopLocation_ua", "class" => "wizard-field"))."
					<p style='color:grey; margin: 3px 0 7px;'>".GetMessage("WIZ_SHOP_LOCATION_DESCR_UA")."</p>
				</div>";


			$this->content .= "
				<div class='wizard-input-form-block'>
					<label class='wizard-input-title' for='shopAdr_ua'>".GetMessage("WIZ_SHOP_ADR")."</label>".
					$this->ShowInputField("textarea", "shopAdr_ua", array("rows" => "3", "id" => "shopAdr_ua", "class" => "wizard-field"))."
					<p style='color:grey; margin: 3px 0 7px;'>".GetMessage("WIZ_SHOP_ADR_DESCR_UA")."</p>
				</div>";

			$this->content .= "
				<div class='wizard-catalog-title'>".GetMessage("WIZ_SHOP_RECV_UA")."</div>
				<p>".GetMessage("WIZ_SHOP_RECV_UA_DESC")."</p>
				<table class='wizard-input-table'>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_EGRPU_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopEGRPU_ua", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_INN_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopINN_ua", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_NDS_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopNDS_ua", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_NS_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopNS_ua", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_BANK_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopBank_ua", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_MFO_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopMFO_ua", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_PLACE_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopPlace_ua", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_FIO_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopFIO_ua", array("class" => "wizard-field"))."</td>
					</tr>
					<tr>
						<td class='wizard-input-table-left'>".GetMessage("WIZ_SHOP_TAX_UA").":</td>
						<td class='wizard-input-table-right'>".$this->ShowInputField("text", "shopTax_ua", array("class" => "wizard-field"))."</td>
					</tr>
				</table>
			</div>";

			if(CModule::IncludeModule("catalog")) {
				$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID" => "1", "BUY" => "Y", "GROUP_ID" => 2));
				if(!$db_res->Fetch()) {
					$this->content .= "
					<div class='wizard-input-form-block'>
						<div class='wizard-catalog-title'>".GetMessage("WIZ_SHOP_PRICE_BASE_TITLE")."</div>
						<div class='wizard-input-form-block-content'>
							".GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1")."<br><br>
							".$this->ShowCheckboxField("installPriceBASE", "Y",
							(array("id" => "install-demo-data")))
							."<label for='install-demo-data'>".GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2")."</label><br />
						</div>
					</div>";
				}
			}
			
			$this->content .= "</div>";

			$this->content .= "
				<script>
					function langReload() {
						var objSel = document.getElementById('localization_select');
						var locSelected = objSel.options[objSel.selectedIndex].value;
						document.getElementById('ru_bank_details').style.display = (locSelected == 'ru' || locSelected == 'kz' || locSelected == 'by') ? 'block' : 'none';
						document.getElementById('ua_bank_details').style.display = (locSelected == 'ua') ? 'block' : 'none';
					}
				</script>";
		}
	}
	
	function OnPostForm() {
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteStamp", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
	}
}

class PersonType extends CWizardStep {
	function InitStep()
	{
		$this->SetStepID("person_type");
		$this->SetTitle(GetMessage("WIZ_STEP_PT"));
		$this->SetNextStep("pay_system");
		$this->SetPrevStep("shop_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);
		$siteID = $wizard->GetVar("siteID");

		if($shopLocalization == "ua")
			$wizard->SetDefaultVars(
				Array(
					"personType" => Array(
						"fiz" => "Y",
						"fiz_ua" => "Y",
						"ur" => "Y",
					)
				)
			);
		else
			$wizard->SetDefaultVars(
				Array(
					"personType" => Array(
						"fiz" =>  COption::GetOptionString("elektroinstrument", "personTypeFiz", "Y", $siteID),
						"ur" => COption::GetOptionString("elektroinstrument", "personTypeUr", "Y", $siteID),
					)
				)
			);
	}

	function ShowStep() {

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);

		$this->content .= "<div class='wizard-input-form'>";
		$this->content .= "
		<div class='wizard-input-form-block'>
			<div style='padding-top:15px'>
				<div class='wizard-input-form-field wizard-input-form-field-checkbox'>
					<div class='wizard-catalog-form-item'>
						".$this->ShowCheckboxField("personType[fiz]", "Y", (array("id" => "personTypeF"))).
						" <label for='personTypeF'>".GetMessage("WIZ_PERSON_TYPE_FIZ")."</label><br />
					</div>
					<div class='wizard-catalog-form-item'>
						".$this->ShowCheckboxField("personType[ur]", "Y", (array("id" => "personTypeU"))).
						" <label for='personTypeU'>".GetMessage("WIZ_PERSON_TYPE_UR")."</label><br />
					</div>";
				if($shopLocalization == "ua")
					$this->content .=
					"<div class='wizard-catalog-form-item'>"
						.$this->ShowCheckboxField("personType[fiz_ua]", "Y", (array("id" => "personTypeFua"))).
						" <label for='personTypeFua'>".GetMessage("WIZ_PERSON_TYPE_FIZ_UA")."</label>
					</div>";
				$this->content .= "
				</div>
			</div>
			<div class='wizard-catalog-form-item'>".GetMessage("WIZ_PERSON_TYPE")."<div>
		</div>";
		$this->content .= "</div>";
	}
	
	function OnPostForm() {
		$wizard = &$this->GetWizard();
		$personType = $wizard->GetVar("personType");

		if(empty($personType["fiz"]) && empty($personType["ur"]))
			$this->SetError(GetMessage("WIZ_NO_PT"));
	}
}

class PaySystem extends CWizardStep {
	function InitStep() {
		$this->SetStepID("pay_system");
		$this->SetTitle(GetMessage("WIZ_STEP_PS"));
		$this->SetNextStep("data_install");
		if(LANGUAGE_ID != "ru")
			$this->SetPrevStep("catalog_settings");
		else
			$this->SetPrevStep("person_type");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();

		if(LANGUAGE_ID == "ru") {
			$shopLocalization = $wizard->GetVar("shopLocalization", true);

			if($shopLocalization == "ua")
				$wizard->SetDefaultVars(
					Array(
						"paysystem" => Array(
							"cash" => "Y",
							"oshad" => "Y",
							"bill" => "Y",
						),
						"delivery" => Array(
							"courier" => "Y",
							"self" => "Y",
						)
					)
				);
			else
				$wizard->SetDefaultVars(
					Array(
						"paysystem" => Array(
							"cash" => "Y",
							"sber" => "Y",
							"bill" => "Y",
							"collect" => "Y"  //cash on delivery
						),
						"delivery" => Array(
							"courier" => "Y",
							"self" => "Y",
							"ruspost" => "N",
							"rus_post" => "N",
							"rus_post_first" => "N",
							"ua_post" => "N",
							"kaz_post" => "N"
						)
					)
				);
		} else {
			$wizard->SetDefaultVars(
				Array(
					"paysystem" => Array(
						"cash" => "Y",	
						"paypal" => "Y",
					),			
					"delivery" => Array(
						"courier" => "Y",	
						"self" => "Y",
						"dhl" => "Y",
						"ups" => "Y",
					)
				)
			);
		}
	}
	
	function OnPostForm() {
		$wizard = &$this->GetWizard();
		$paysystem = $wizard->GetVar("paysystem");

		if(empty($paysystem["cash"]) && empty($paysystem["sber"]) && empty($paysystem["bill"]) && empty($paysystem["paypal"]) && empty($paysystem["oshad"]) && empty($paysystem["collect"]))
			$this->SetError(GetMessage('WIZ_NO_PS'));
	}

	function ShowStep() {
		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);

		$personType = $wizard->GetVar("personType");

		$arAutoDeliveries = array();
		if(CModule::IncludeModule("sale")) {
			$dbRes = \Bitrix\Sale\Delivery\Services\Table::getList(array(
				"filter" => array(
					"=CLASS_NAME" => array(
						"\Sale\Handlers\Delivery\SpsrHandler",
						"\Bitrix\Sale\Delivery\Services\Automatic"
					)
				),
				"select" => array("ID", "CODE", "ACTIVE", "CLASS_NAME")
			));

			while($dlv = $dbRes->fetch()) {
				if($dlv["CLASS_NAME"] == "\Sale\Handlers\Delivery\SpsrHandler")
					$arAutoDeliveries["spsr"] = $dlv["ACTIVE"];
				elseif(!empty($dlv["CODE"]))
					$arAutoDeliveries[$dlv["CODE"]] = $dlv["ACTIVE"];
			}
		}
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		
		$this->content .= "<div class='wizard-input-form'>";
		$this->content .= "
		<div class='wizard-input-form-block'>
			<div class='wizard-catalog-title'>".GetMessage("WIZ_PAY_SYSTEM_TITLE")."</div>
			<div>
				<div class='wizard-input-form-field wizard-input-form-field-checkbox'>
					<div class='wizard-catalog-form-item'>
						".$this->ShowCheckboxField("paysystem[cash]", "Y", (array("id" => "paysystemC"))).
						" <label for='paysystemC'>".GetMessage("WIZ_PAY_SYSTEM_C")."</label>
					</div>";

				if(LANGUAGE_ID == "ru") {
					if($shopLocalization == "ua" && ($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y"))
						$this->content .=
							"<div class='wizard-catalog-form-item'>".
								$this->ShowCheckboxField("paysystem[oshad]", "Y", (array("id" => "paysystemO"))).
								" <label for='paysystemS'>".GetMessage("WIZ_PAY_SYSTEM_O")."</label>
							</div>";
					if($shopLocalization == "ru") {
						if($personType["fiz"] == "Y")
							$this->content .=
								"<div class='wizard-catalog-form-item'>".
									$this->ShowCheckboxField("paysystem[sber]", "Y", (array("id" => "paysystemS"))).
									" <label for='paysystemS'>".GetMessage("WIZ_PAY_SYSTEM_S")."</label>
								</div>";
						if($personType["fiz"] == "Y" || $personType["ur"] == "Y")
							$this->content .=
								"<div class='wizard-catalog-form-item'>".
									$this->ShowCheckboxField("paysystem[collect]", "Y", (array("id" => "paysystemCOL"))).
									" <label for='paysystemCOL'>".GetMessage("WIZ_PAY_SYSTEM_COL")."</label>
								</div>";
					}
					if($personType["ur"] == "Y") {
						$this->content .=
							"<div class='wizard-catalog-form-item'>".
								$this->ShowCheckboxField("paysystem[bill]", "Y", (array("id" => "paysystemB"))).
								" <label for='paysystemB'>";
						if($shopLocalization == "ua")
							$this->content .= GetMessage("WIZ_PAY_SYSTEM_B_UA");
						else
							$this->content .= GetMessage("WIZ_PAY_SYSTEM_B");
						$this->content .= "</label>
							</div>";
					}
				} else {
					$this->content .=
						"<div class='wizard-catalog-form-item'>".
							$this->ShowCheckboxField("paysystem[paypal]", "Y", (array("id" => "paysystemP"))).
							" <label for='paysystemP'>PayPal</label>
						</div>";
				}
				$this->content .= "</div>
			</div>
			<div class='wizard-catalog-form-item'>".GetMessage("WIZ_PAY_SYSTEM")."</div>
		</div>";
		if(LANGUAGE_ID != "ru" || LANGUAGE_ID == "ru" && (COption::GetOptionString("elektroinstrument", "wizard_installed", "N", $siteID) != "Y" || $shopLocalization == "ru" && ($arAutoDeliveries["ruspost"] != "Y" || $arAutoDeliveries["rus_post"] != "Y" || $arAutoDeliveries["rus_post_first"] != "Y") || $shopLocalization == "ua" && ($arAutoDeliveries["ua_post"] != "Y") || $shopLocalization == "kz" && ($arAutoDeliveries["kaz_post"] != "Y"))) {
			$this->content .= "
			<div class='wizard-input-form-block'>
				<div class='wizard-catalog-title'>".GetMessage("WIZ_DELIVERY_TITLE")."</div>
				<div>
					<div class='wizard-input-form-field wizard-input-form-field-checkbox'>";
						if(COption::GetOptionString("elektroinstrument", "wizard_installed", "N", $siteID) != "Y") {
							$this->content .= "<div class='wizard-catalog-form-item'>
								".$this->ShowCheckboxField("delivery[courier]", "Y", (array("id" => "deliveryC"))).
								" <label for='deliveryC'>".GetMessage("WIZ_DELIVERY_C")."</label>
							</div>
							<div class='wizard-catalog-form-item'>
								".$this->ShowCheckboxField("delivery[self]", "Y", (array("id" => "deliveryS"))).
								" <label for='deliveryS'>".GetMessage("WIZ_DELIVERY_S")."</label>
							</div>";
						}
						if(LANGUAGE_ID == "ru") {
							if($shopLocalization == "ru") {
								if ($arAutoDeliveries["ruspost"] != "Y") {
									\Bitrix\Sale\Delivery\Services\Manager::getHandlersList();
									$res = \Sale\Handlers\Delivery\AdditionalHandler::getSupportedServicesList();
									if(!empty($res['NOTES']) && is_array($res['NOTES']))
										$deliveryNotes = $res['NOTES'];
									else
										$this->content .= 
											"<div class='wizard-catalog-form-item'>".
												$this->ShowCheckboxField("delivery[ruspost]", "Y", (array("id" => "deliveryR"))).
												" <label for='deliveryR'>".GetMessage("WIZ_DELIVERY_R")."</label>
											</div>";
								}
								if($arAutoDeliveries["rus_post"] != "Y")
									$this->content .=
										"<div class='wizard-catalog-form-item'>".
											$this->ShowCheckboxField("delivery[rus_post]", "Y", (array("id" => "deliveryR2"))).
											" <label for='deliveryR2'>".GetMessage("WIZ_DELIVERY_R2")."</label>
										</div>";
								if($arAutoDeliveries["rus_post_first"] != "Y")
									$this->content .=
										"<div class='wizard-catalog-form-item'>".
											$this->ShowCheckboxField("delivery[rus_post_first]", "Y", (array("id" => "deliveryRF"))).
											" <label for='deliveryRF'>".GetMessage("WIZ_DELIVERY_RF")."</label>
										</div>";
							} elseif($shopLocalization == "ua") {
								if($arAutoDeliveries["ua_post"] != "Y")
									$this->content .=
										"<div class='wizard-catalog-form-item'>".
											$this->ShowCheckboxField("delivery[ua_post]", "Y", (array("id" => "deliveryU"))).
											" <label for='deliveryU'>".GetMessage("WIZ_DELIVERY_UA")."</label>
										</div>";
							} elseif($shopLocalization == "kz") {
								if($arAutoDeliveries["kaz_post"] != "Y")
									$this->content .=
										"<div class='wizard-catalog-form-item'>".
											$this->ShowCheckboxField("delivery[kaz_post]", "Y", (array("id" => "deliveryK"))).
											" <label for='deliveryK'>".GetMessage("WIZ_DELIVERY_KZ")."</label>
										</div>";
							}
						} else {
							$this->content .=
								"<div class='wizard-catalog-form-item'>".
									$this->ShowCheckboxField("delivery[dhl]", "Y", (array("id" => "deliveryD"))).
									" <label for='deliveryD'>DHL</label>
								</div>";
							$this->content .=
								"<div class='wizard-catalog-form-item'>".
									$this->ShowCheckboxField("delivery[ups]", "Y", (array("id" => "deliveryU"))).
									" <label for='deliveryU'>UPS</label>
								</div>";
						}
						$this->content .= "
					</div>
				</div>
				<div class='wizard-catalog-form-item'>".GetMessage("WIZ_DELIVERY")."</div>
			</div>";
		}

		$this->content .= "
		<div>
			<div class='wizard-catalog-title'>".GetMessage("WIZ_LOCATION_TITLE")."</div>
			<div>
				<div class='wizard-input-form-field wizard-input-form-field-checkbox'>";
		if(in_array(LANGUAGE_ID, array("ru", "ua"))) {
			$this->content .=
				"<div class='wizard-catalog-form-item'>".
					$this->ShowRadioField("locations_csv", "loc_ru.csv", $shopLocalization == "ru" ? array("id" => "loc_ru", "checked" => "checked") : array("id" => "loc_ru"))
					." <label for='loc_ru'>".GetMessage("WSL_STEP2_GFILE_RU")."</label>
				</div>";
			$this->content .=
				"<div class='wizard-catalog-form-item'>".
					$this->ShowRadioField("locations_csv", "loc_by.csv", $shopLocalization == "by" ? array("id" => "loc_by", "checked" => "checked") : array("id" => "loc_by"))
					." <label for='loc_by'>".GetMessage("WSL_STEP2_GFILE_BY")."</label>
				</div>";				
			$this->content .=
				"<div class='wizard-catalog-form-item'>".
					$this->ShowRadioField("locations_csv", "loc_ua.csv", $shopLocalization == "ua" ? array("id" => "loc_ua", "checked" => "checked") : array("id" => "loc_ua"))
					." <label for='loc_ua'>".GetMessage("WSL_STEP2_GFILE_UA")."</label>
				</div>";
			$this->content .=
				"<div class='wizard-catalog-form-item'>".
					$this->ShowRadioField("locations_csv", "loc_kz.csv", $shopLocalization == "kz" ? array("id" => "loc_kz", "checked" => "checked") : array("id" => "loc_kz"))
					." <label for='loc_kz'>".GetMessage("WSL_STEP2_GFILE_KZ")."</label>
				</div>";
		}		
		$this->content .=
			"<div class='wizard-catalog-form-item'>".
				$this->ShowRadioField("locations_csv", "", array("id" => "none"))
				." <label for='none'>".GetMessage("WSL_STEP2_GFILE_NONE")."</label>
			</div>";

		$this->content .= "
				</div>
			</div>
		</div>";

		$this->content .= "<div class='wizard-catalog-form-item'>".GetMessage("WIZ_DELIVERY_HINT")."</div>";

		$this->content .= "</div>";
	}
}

class DataInstallStep extends CDataInstallWizardStep {
	function CorrectServices(&$arServices) {
		$wizard =& $this->GetWizard();
		if($wizard->GetVar("installDemoData") != "Y") {}
	}
}

class FinishStep extends CFinishWizardStep {
	function InitStep() {
		$this->SetStepID("finish");
		$this->SetNextStep("finish");
		$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
		$this->SetNextCaption(GetMessage("wiz_go"));
	}

	function ShowStep() {
		$wizard =& $this->GetWizard();
		if($wizard->GetVar("proactive") == "Y")
			COption::SetOptionString("statistic", "DEFENCE_ON", "Y");
		
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);
		$siteDir = "/"; 
		if($arSite = $rsSites->Fetch())
			$siteDir = $arSite["DIR"]; 

		$wizard->SetFormActionScript(str_replace("//", "/", $siteDir."/?finish"));

		$this->CreateNewIndex();
		
		COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID); 
		
		$this->content .=
			"<table class='wizard-completion-table'>
				<tr>
					<td class='wizard-completion-cell'>"
						.GetMessage("FINISH_STEP_CONTENT").
					"</td>
				</tr>
			</table>";
				
		if($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");
	}
}?>