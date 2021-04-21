<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) 
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 

$arWizardDescription = array(
	"NAME" => GetMessage("PORTAL_WIZARD_NAME"), 
	"DESCRIPTION" => GetMessage("PORTAL_WIZARD_DESC"), 
	"VERSION" => "3.2.13",
	"START_TYPE" => "WINDOW",
	"WIZARD_TYPE" => "INSTALL",
	"IMAGE" => "/images/".LANGUAGE_ID."/solution.png",
	"PARENT" => "wizard_sol",
	"TEMPLATES" => array(
		array("SCRIPT" => "wizard_sol")
	),
	"STEPS" => array()
);

if(defined("WIZARD_DEFAULT_SITE_ID")) {
	if(LANGUAGE_ID == "ru")
		$arWizardDescription["STEPS"] = array("SelectTemplateStep", "SiteSettingsStep", "CatalogSettings", "ShopSettings", "PersonType", "PaySystem", "DataInstallStep" ,"FinishStep");
	else
		$arWizardDescription["STEPS"] = array("SelectTemplateStep", "SiteSettingsStep", "CatalogSettings", "PaySystem", "DataInstallStep" ,"FinishStep");
} else {
	if(LANGUAGE_ID == "ru")
		$arWizardDescription["STEPS"] = array("SelectSiteStep", "SelectTemplateStep", "SiteSettingsStep", "CatalogSettings", "ShopSettings", "PersonType", "PaySystem", "DataInstallStep" ,"FinishStep");
	else
		$arWizardDescription["STEPS"] = array("SelectSiteStep", "SelectTemplateStep", "SiteSettingsStep", "CatalogSettings", "PaySystem", "DataInstallStep" ,"FinishStep");
}?>