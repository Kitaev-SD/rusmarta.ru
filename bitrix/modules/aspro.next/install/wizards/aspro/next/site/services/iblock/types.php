<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

use \Bitrix\Main\Config\Option;

if( Option::get("aspro.next", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA )
	return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";
	
$arTypes = Array(
	Array(
		"ID" => "aspro_next_catalog",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 100,
		"LANG" => Array(0 => "ru"),
	),
	Array(
		"ID" => "aspro_next_content",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 200,
		"LANG" => Array(0 => "ru"),
	),
	Array(
		"ID" => "aspro_next_adv",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array(0 => "ru"),
	),
	Array(
		"ID" => "aspro_next_regionality",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array(0 => "ru"),
	)
);
$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];
	
$iblockType = new CIBlockType;
foreach($arTypes as $arType)
{
	$dbType = CIBlockType::GetList(Array(),Array("=ID" => $arType["ID"]));
	if($dbType->Fetch())
		continue;

	foreach($arLanguages as $languageID)
	{
		WizardServices::IncludeServiceLang("types.php", $languageID);
		$code = strtoupper($arType["ID"]);
		$arType["LANG"][$languageID]["NAME"] = GetMessage($code."_TYPE_NAME");
		$arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage($code."_ELEMENT_NAME");

		if ($arType["SECTIONS"] == "Y")
			$arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage($code."_SECTION_NAME");
	}
	$newIblockType = $iblockType->Add($arType);
	
    if(IntVal($newIblockType)<=0)
    { $GLOBALS["ASPRO_NEXT_WIZARD_LAST_ERROR"]["UNABLE_TO_CREATE_IBLOCK_TYPE_".strtoupper($arType["ID"])] = $iblockType->LAST_ERROR;}
}

// replace macros IBLOCK_NEXT_CATALOG_TYPE & IBLOCK_NEXT_CONTENT_TYPE & IBLOCK_NEXT_ADV_TYPE
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_NEXT_CATALOG_TYPE" => "aspro_next_catalog"));
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_NEXT_CONTENT_TYPE" => "aspro_next_content"));
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_NEXT_ADV_TYPE" => "aspro_next_adv"));
CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_NEXT_CATALOG_TYPE" => "aspro_next_catalog"));
CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_NEXT_CONTENT_TYPE" => "aspro_next_content"));
CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_NEXT_ADV_TYPE" => "aspro_next_adv"));

Option::set('iblock','combined_list_mode','Y');
?>