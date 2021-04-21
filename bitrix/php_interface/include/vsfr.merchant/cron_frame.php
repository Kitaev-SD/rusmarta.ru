#!#PHP_PATH# -q
<?php
/* replace #PHP_PATH# to real path of php binary
For example:
/usr/local/php/bin/php
/opt/php71/bin/php
/user/bin/php
/usr/bin/perl
/usr/bin/env python
*/
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../../');  // replace realpath(dirname(__FILE__).'/../../../../') to real document root path	

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

if($argv[1] > 0)
{
	$profile_id = $argv[1];
}
else
{
	$profile_id = 1;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

set_time_limit (0);

if (CModule::IncludeModule("catalog"))
{
	$profile_id = intval($profile_id);
	if ($profile_id<=0) die("No profile_id");

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vsfr.merchant/mysql/vsfr_catalog_export.php");
	$ar_profile = CCatalogVSFRExport::GetByID($profile_id);
	if (!$ar_profile) die("No profile");

	$strFile = "/bitrix/php_interface/include/vsfr.merchant/".$ar_profile["FILE_NAME"]."_run.php";
	if (!file_exists($_SERVER["DOCUMENT_ROOT"].$strFile))
	{
		$strFile = "/bitrix/modules/vsfr.merchant/load/".$ar_profile["FILE_NAME"]."_run.php";
		if (!file_exists($_SERVER["DOCUMENT_ROOT"].$strFile))
		{
			die("No export script");
		}
	}

	$bFirstLoadStep = true;

	$arSetupVars = array();
	$intSetupVarsCount = 0;
	if ('Y' != $ar_profile["DEFAULT_PROFILE"])
	{
		parse_str($ar_profile["SETUP_VARS"], $arSetupVars);
		if (!empty($arSetupVars) && is_array($arSetupVars))
		{
			$intSetupVarsCount = extract($arSetupVars, EXTR_SKIP);
		}
	}

	CCatalogDiscountSave::Disable();
	include($_SERVER["DOCUMENT_ROOT"].$strFile);
	CCatalogDiscountSave::Enable();

	CCatalogVSFRExport::Update($profile_id, array(
		"=LAST_USE" => $DB->GetNowFunction()
		)
	);
}
?>