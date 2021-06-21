<?
$strModuleId = 'acrit.Googlemerchant';

// Agent for autobackup
\CAgent::addAgent('\Acrit\Googlemerchant\Backup::autobackup();', $strModuleId, 'N', 3600);

// Agent for autoclean
\CAgent::addAgent('\Acrit\Core\Export\Cleaner::agent(\''.$strModuleId.'\');', 'acrit.core', 'N', 24*60*60);

?>