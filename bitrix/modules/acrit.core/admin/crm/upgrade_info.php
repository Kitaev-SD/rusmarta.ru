<?
use \Bitrix\Main\Localization\Loc;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strModuleId.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

IncludeModuleLangFile(__FILE__);
?>
    <style>
        .acrit-wrap { background: #f6f6f6; padding: 20px; }
        .acrit-wrap a { color: #4ead00; }
        .acrit-warning { padding: 10px; background: #ffeeee; display: inline-block; }
        .acrit-description {  }
    </style>
    <div class="acrit-wrap acrit-page-upgradelink">
        <h1 class="adm-title"><?=Loc::getMessage('ACRIT_EXP_404_ORDERS_TITLE');?></h1>
        <div class="acrit-warning">
			<?=Loc::getMessage('ACRIT_EXP_404_ORDERS_WARNING');?>
        </div>
        <div class="acrit-description">
			<?=Loc::getMessage('ACRIT_EXP_404_ORDERS_DESCRIPTION');?>
        </div>
    </div>
<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>