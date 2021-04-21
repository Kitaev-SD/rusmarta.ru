<?php

IncludeModuleLangFile(__FILE__);

Class tinkoff_payment extends CModule
{
    const MODULE_ID = 'tinkoff.payment';
    var $MODULE_ID = 'tinkoff.payment';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("tinkoff_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("tinkoff_MODULE_DESC");
        $this->PARTNER_NAME = GetMessage("tinkoff_MODULE_NAME");
        $this->PARTNER_URI = "https://oplata.tinkoff.ru";
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function rmFolder($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->rmFolder($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);

        return true;
    }

    function copyDir($source, $destination)
    {
        if (is_dir($source)) {
            @mkdir($destination, 0755);
            $directory = dir($source);
            while (FALSE !== ($readdirectory = $directory->read())) {
                if ($readdirectory == '.' || $readdirectory == '..') continue;
                $PathDir = $source . '/' . $readdirectory;
                if (is_dir($PathDir)) {
                    $this->copyDir($PathDir, $destination . '/' . $readdirectory);
                    continue;
                }
                copy($PathDir, $destination . '/' . $readdirectory);
            }
            $directory->close();
        } else {
            copy($source, $destination);
        }
    }

    function InstallFiles($arParams = array())
    {
        if (!is_dir($ipn_dir = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/')) {
            mkdir($ipn_dir, 0755);
        }
        if (is_dir($source = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install')) {
            $this->copyDir($source . "/handler", $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/payment');
            $this->copyDir($source . "/handler", $ipn_dir);

            if (!is_dir($personal_dir = $_SERVER['DOCUMENT_ROOT'] . '/personal/')) {
                mkdir($personal_dir, 0755);
            }
            if (!is_dir($order_dir = $_SERVER['DOCUMENT_ROOT'] . '/personal/order/')) {
                mkdir($order_dir, 0755);
            }
            copy($source . "/notifications/notification.php", $order_dir . 'notification.php');
            copy($source . "/notifications/result.php", $order_dir . 'result.php');
        }
        return true;
    }

    function UnInstallFiles()
    {
        $this->rmFolder($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/payment/tinkoff');
        $this->rmFolder($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/tinkoff');
        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;

        $this->InstallFiles();

        RegisterModule(self::MODULE_ID);
    }

    function DoUninstall()
    {
        global $APPLICATION;

        UnRegisterModule(self::MODULE_ID);

        $this->UnInstallFiles();
    }
}

?>
