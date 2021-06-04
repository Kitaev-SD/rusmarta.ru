<?php

IncludeModuleLangFile(__FILE__);

class tinkoff_payment extends CModule
{
    const MODULE_ID = 'tinkoff.payment';
    public $MODULE_ID = 'tinkoff.payment';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public $strError = '';

    public function __construct()
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

    public function InstallEvents()
    {
        return true;
    }

    public function UnInstallEvents()
    {
        return true;
    }

    public function rmFolder($dir)
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

    public function copyDir($source, $destination)
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

    public function InstallFiles($arParams = array())
    {
        if (!is_dir($ipn_dir = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/')) {
            mkdir($ipn_dir, 0755);
        }
        if (is_dir($source = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install')) {
            // bitrix/modules/sale/payment - системный обработчик
            $this->copyDir($source . "/handler", $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/payment');
            // php_interface/include/sale_payment - пользовательский обработчик
            $this->copyDir($source . "/handler", $ipn_dir);

            if (!is_dir($personal_dir = $_SERVER['DOCUMENT_ROOT'] . '/personal/')) {
                mkdir($personal_dir, 0755);
            }
            if (!is_dir($order_dir = $_SERVER['DOCUMENT_ROOT'] . '/personal/order/')) {
                mkdir($order_dir, 0755);
            }
            copy($source . "/notifications/notification.php", $order_dir . 'notification.php');
            copy($source . "/notifications/success.php", $order_dir . 'success.php');
            copy($source . "/notifications/failed.php", $order_dir . 'failed.php');
        }
        return true;
    }

    public function UnInstallFiles()
    {
        $this->rmFolder($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/payment/tinkoff');
        $this->rmFolder($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/tinkoff');
        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION;
        global $DB;

        $strSql = "
        CREATE TABLE IF NOT EXISTS tinkoffRefund (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, OrderId VARCHAR(20), PaymentId VARCHAR(20) UNIQUE, DataCreated timestamp default now() on update now())
        ";
        $result = $DB->Query($strSql);

        $this->InstallFiles();

        RegisterModule(self::MODULE_ID);
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        UnRegisterModule(self::MODULE_ID);

        $this->UnInstallFiles();
    }
}


