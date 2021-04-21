<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)	die();

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";	

set_time_limit(0);

if (!CModule::IncludeModule("highloadblock"))
	return;

if (!WIZARD_INSTALL_DEMO_DATA)
	return;

$HL_ID = $_SESSION["NEXT_HBLOCK_CONTACT_ID"];
unset($_SESSION["NEXT_HBLOCK_CONTACT_ID"]);

//adding rows
WizardServices::IncludeServiceLang("references.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;
global $USER_FIELD_MANAGER;

if($HL_ID){
	$hldata = HL\HighloadBlockTable::getById($HL_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arProfits = array(
		"VISA" => array(
			"CLASS" => "fa-cc-visa",
			"XML_ID" => "zrtkk8xV",

		),
		"MASTERCARD" => array(
			"CLASS" => "fa-cc-mastercard",
			"XML_ID" => "mc",
		),
		"CACH" => array(
			"CLASS" => "fa-money",
			"XML_ID" => "cach",
		),
		"MIR" => array(
			"IMAGE" => "references_files/iblock/c89/c89d03bdcc3177d2f096d41d9a2c3108.png",
			"XML_ID" => "mir",
		),
	);
	$sort = 100;
	foreach($arProfits as $profitName => $arFile){
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_PROFIT_".$profitName),
			'UF_SORT' => $sort,
			// 'UF_DEF' => ($sort > 100) ? "0" : "1",
			'UF_XML_ID' => ($arFile["XML_ID"] ? $arFile["XML_ID"] : ToLower($profitName))
		);
		if($arFile["IMAGE"])
		{
			$arData["UF_FILE"] = array(
				'name' => ToLower($profitName).".png",
				'type' => 'image/png',
				'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$arFile["IMAGE"]
			);
		}
		if($arFile["DESC"])
			$arData["UF_DESCRIPTION"]=$arFile["DESC"];

		if($arFile["FULL_DESC"])
			$arData["UF_FULL_DESCRIPTION"]=$arFile["FULL_DESC"];
		
		if($arFile["LINK"])
			$arData["UF_LINK"]=$arFile["LINK"];
		
		if($arFile["CLASS"])
			$arData["UF_ICON_CLASS"]=$arFile["CLASS"];
		
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$HL_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$HL_ID, null, $arData);
		$result = $entity_data_class::add($arData);
		$sort += 100;
	}
}
?>