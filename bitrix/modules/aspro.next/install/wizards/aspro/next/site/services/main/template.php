<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;

if(!WIZARD_INSTALL_DEMO_DATA){
	return;
}

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";

// copy files
CopyDirFiles(
	WIZARD_TEMPLATE_ABSOLUTE_PATH,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true, 
	$delete_after_copy = false,
	$exclude = "themes"
);

//default
CopyDirFiles(
	WIZARD_ABSOLUTE_PATH."/site/templates/.default/",
	$_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/",
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false
);

//aspro_mail
CopyDirFiles(
	WIZARD_ABSOLUTE_PATH."/site/templates/aspro_mail/",
	$_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/aspro_mail/",
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false
);

if(!file_exists($bitrixTemplateDir."/js/custom.js"))
	@copy(WIZARD_ABSOLUTE_PATH."/site/services/main/custom/custom.js", $bitrixTemplateDir."/js/custom.js");
if(!file_exists($bitrixTemplateDir."/css/custom.css"))
	@copy(WIZARD_ABSOLUTE_PATH."/site/services/main/custom/custom.css", $bitrixTemplateDir."/css/custom.css");
// replace macros SITE_DIR & SITE_ID
CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("SITE_ID" => WIZARD_SITE_ID));
CWizardUtil::ReplaceMacros($bitrixTemplateDir."js/general.js", Array("SITE_DIR" => WIZARD_SITE_DIR));

// attach template to default site
if($arSite = CSite::GetByID(WIZARD_SITE_ID)->Fetch()){
	$obTemplate = CSite::GetTemplateList(WIZARD_SITE_ID);
	$arTemplates = array();
	$found = false;
	while ($arTemplate = $obTemplate->Fetch()){
		if(!$found && !strlen($arTemplate["CONDITION"])){
			$arTemplate["TEMPLATE"] = WIZARD_TEMPLATE_ID;
			$found = true;
		}
		if($arTemplate["TEMPLATE"] == "empty"){
			continue;
		}
		$arTemplates[]= $arTemplate;
	}
	if (!$found){
		$arTemplates[]= array("CONDITION" => "", "SORT" => 150, "TEMPLATE" => WIZARD_TEMPLATE_ID);
	}

	$obSite = new CSite();
	$arFields = array("TEMPLATE" => $arTemplates, "DIR" => str_replace('//', '/', str_replace('//', '/', '/'.$arSite["DIR"].'/')));
	$obSite->Update(WIZARD_SITE_ID, $arFields);
}

\Bitrix\Main\Config\Option::set("main", "wizard_template_id", WIZARD_TEMPLATE_ID, WIZARD_SITE_ID);
?>
