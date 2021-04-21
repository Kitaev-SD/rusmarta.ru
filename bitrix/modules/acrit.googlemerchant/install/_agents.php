<?
$strModuleId = 'acrit.googlemerchant';

// Agent for autobackup
\CAgent::AddAgent('Acrit\GoogleMerchant\Backup::autobackup();', $strModuleId, 'N', 3600);

?>