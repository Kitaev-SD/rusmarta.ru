<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;


global $APPLICATION;
//$APPLICATION->SetAdditionalCSS($templateFolder."/css/fast_one_click.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.inputmask.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.validate.min.js");
