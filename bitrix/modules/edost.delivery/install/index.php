<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));

Class edost_delivery extends CModule {

	var $MODULE_ID = 'edost.delivery';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';
	var $NEED_MAIN_VERSION = '16.5.11';
	var $NEED_MODULES = array('main', 'sale');

	function edost_delivery() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_URI  = "http://www.edost.ru";
		$this->PARTNER_NAME = GetMessage('EDOST_DELIVERY_PARTNER_NAME');
		$this->MODULE_NAME = GetMessage('EDOST_DELIVERY_INSTALL_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('EDOST_DELIVERY_INSTALL_DESCRIPTION');

	}

	function DoInstall() {
		global $DOCUMENT_ROOT, $APPLICATION;

		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES))
			foreach ($this->NEED_MODULES as $module) if (!IsModuleInstalled($module))
				$this->ShowForm('ERROR', GetMessage('EDOST_DELIVERY_NEED_MODULES', array('#MODULE#' => $module)));

		if (version_compare(SM_VERSION, $this->NEED_MAIN_VERSION) < 0) $this->ShowForm('ERROR', GetMessage('EDOST_DELIVERY_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));
		else if (\Bitrix\Main\Config\Option::get('main', '~sale_converted_15', 'N') != 'Y') $this->ShowForm('ERROR', GetMessage('EDOST_DELIVERY_NEED_CONVERTED'));
		else {
			$this->InstallFiles();

			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepDelivery', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepDeliveryHandler');
			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepPaySystem', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepPaySystemHandler');
			RegisterModuleDependences('sale', 'OnBeforeOrderAdd', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCBeforeOrderAdd');
			RegisterModuleDependences('sale', 'OnOrderRemindSendEmail', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderRemindSendEmail');
			RegisterModuleDependences('main', 'OnAdminTabControlBegin', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnAdminTabControlBegin');
//			RegisterModuleDependences('sale', 'OnSaleCalculateOrderPaySystem', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCCalculateOrderPaySystem'); // удалено в 2.5.1
//			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepPersonType', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepPersonType'); // удалено в 2.5.0
//			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepOrderProps', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepOrderPropsHandler'); // удалено в 2.5.0
//			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepComplete', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepComplete'); // удалено в 2.5.0
//			RegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnBeforeProlog'); // удалено в 2.5.0

			RegisterModuleDependences('sale', 'OnSaleOrderBeforeSaved', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleOrderBeforeSaved');
			RegisterModuleDependences('sale', 'OnSaleOrderSaved', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleOrderSaved');
			RegisterModuleDependences('sale', 'OnSaleComponentOrderProperties', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleComponentOrderProperties');
			RegisterModuleDependences('sale', 'OnSaleComponentOrderDeliveriesCalculated', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleComponentOrderDeliveriesCalculated');
//			RegisterModuleDependences('sale', 'OnSaleComponentOrderCreated', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleComponentOrderCreated');

			RegisterModuleDependences('main', 'OnProlog', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnProlog'); // добавлено в 2.5.3

			RegisterModule($this->MODULE_ID);

			$this->ShowForm('OK', GetMessage('EDOST_DELIVERY_INSTALL_OK'));
		}

	}

	function DoUninstall() {
		global $DOCUMENT_ROOT, $APPLICATION;

		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepDelivery', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepDeliveryHandler');
		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepPaySystem', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepPaySystemHandler');
		UnRegisterModuleDependences('sale', 'OnBeforeOrderAdd', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCBeforeOrderAdd');
		UnRegisterModuleDependences('sale', 'OnOrderRemindSendEmail', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderRemindSendEmail');
		UnRegisterModuleDependences('main', 'OnAdminTabControlBegin', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnAdminTabControlBegin');
//		UnRegisterModuleDependences('sale', 'OnSaleCalculateOrderPaySystem', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCCalculateOrderPaySystem'); // удалено в 2.5.1
//		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepPersonType', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepPersonType'); // удалено в 2.5.0
//		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepOrderProps', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepOrderPropsHandler'); // удалено в 2.5.0
//		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepComplete', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepComplete'); // удалено в 2.5.0
//		UnRegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnBeforeProlog'); // удалено в 2.5.0

		UnRegisterModuleDependences('sale', 'OnSaleOrderBeforeSaved', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleOrderBeforeSaved');
		UnRegisterModuleDependences('sale', 'OnSaleOrderSaved', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleOrderSaved');
		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderProperties', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleComponentOrderProperties');
		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderDeliveriesCalculated', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleComponentOrderDeliveriesCalculated');
//		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderCreated', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSaleComponentOrderCreated');

		UnRegisterModuleDependences('main', 'OnProlog', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnProlog'); // добавлено в 2.5.3

		UnRegisterModule($this->MODULE_ID);

		$this->UnInstallFiles();
		$this->ShowForm('OK', GetMessage('EDOST_DELIVERY_INSTALL_DEL'));
	}

	function InstallFiles()	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/delivery_edost/delivery_edost.php', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_delivery/delivery_edost.php', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/edostpaycod', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment/edostpaycod', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/delivery_edost_img', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/delivery_edost_img', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes', true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/templates", $_SERVER["DOCUMENT_ROOT"]."/bitrix/templates", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		return true;
	}

	function UnInstallFiles() {
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/delivery_edost/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_delivery/');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/edostpaycod', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment/edostpaycod');
		DeleteDirFilesEx('/bitrix/images/delivery_edost_img/');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes/.default', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default');
		DeleteDirFilesEx('/bitrix/themes/.default/icons/edost.delivery/');
		DeleteDirFilesEx("/bitrix/components/edost/delivery/");
		DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.order.ajax/edost_2019/");
		DeleteDirFilesEx("/bitrix/js/edost.delivery/");
		return true;
	}

	private function ShowForm($type, $message, $buttonName = '')
	{
		$keys = array_keys($GLOBALS);

		for ($i = 0; $i < count($keys); $i++)
			if ($keys[$i] != 'i' && $keys[$i] != 'GLOBALS' && $keys[$i] != 'strTitle' && $keys[$i] != 'filepath')
				global ${$keys[$i]};

//		$PathInstall = str_replace('\\', '/', __FILE__);
//		$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
//		IncludeModuleLangFile($PathInstall.'/install.php');

		$APPLICATION->SetTitle(GetMessage('EDOST_DELIVERY_INSTALL_NAME'));

		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
		?>
		<form action="<?= $APPLICATION->GetCurPage()?>" method="get">
		<p>
			<input type="hidden" name="lang" value="<?= LANG?>" />
			<input type="submit" value="<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK')?>" />
		</p>
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

		die();
	}

}
?>