<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

if(!WIZARD_INSTALL_DEMO_DATA){
	return;
}

use \Bitrix\Main\Config\Option;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";

// copy files
CopyDirFiles(
    WIZARD_TEMPLATE_ABSOLUTE_PATH."/themes/",
    $bitrixTemplateDir."themes/",
    $rewrite = true, 
    $recursive = true,
    $delete_after_copy = false,
    $exclude = "description.php"
);

Option::set("main", "wizard_".WIZARD_TEMPLATE_ID."_theme_id", WIZARD_THEME_ID, "", WIZARD_SITE_ID);

// theme
Option::set('aspro.next', "THEME_SWITCHER", "N", WIZARD_SITE_ID);
Option::set('aspro.next', "BASE_COLOR", WIZARD_THEME_ID, WIZARD_SITE_ID);

// captcha colors
Option::set("main", "CAPTCHA_arBorderColor", "BDBDBD");
Option::set("main", "CAPTCHA_arTextColor_1", "636363");
Option::set("main", "CAPTCHA_arTextColor_2", "636363");

// color scheme for main.interface.grid/form
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".strToLower($GLOBALS["DB"]->type)."/favorites.php");
CUserOptions::SetOption("main.interface", "global", array("theme" => WIZARD_THEME_ID), true);
?>