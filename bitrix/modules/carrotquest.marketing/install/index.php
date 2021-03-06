<?

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Sale;
use Bitrix\Catalog;
use CarrotQuest\Marketing\CarrotEvents;
use CarrotQuest\Marketing\CarrotEventsOrder;
use CarrotQuest\Marketing\CarrotEventsBasket;


$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang . "/lang/", "/install/index.php"));
Loc::loadMessages(__FILE__);

class carrotquest_marketing extends CModule
{
	var $MODULE_ID = "carrotquest.marketing";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	var $EVENTS_CLASS_MAIN = "CarrotQuest\\Marketing\\CarrotEvents";
	var $EVENTS_CLASS_BASKET = "CarrotQuest\\Marketing\\CarrotEventsBasket";
	var $EVENTS_CLASS_ORDER = "CarrotQuest\\Marketing\\CarrotEventsOrder";

	function __construct()
	{
		$arModuleVersion = array();

		include(__DIR__ . "/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		$this->MODULE_NAME = Loc::getMessage("CARROT_INTEGR_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("CARROT_INTEGR_INSTALL_DESCRIPTION");

		$this->PARTNER_NAME = "Carrot quest";
		$this->PARTNER_URI = "http://carrotquest.io";
	}


	function DoInstall()
	{
		global $APPLICATION;

		if (!ModuleManager::isModuleInstalled('sale') || !ModuleManager::isModuleInstalled('catalog')) {
			return false;
		}

		$strPath2step = str_replace("\\", "/", __FILE__);
		$strPath2step = substr($strPath2step, 0, strlen($strPath2step) - strlen("/index.php"));


		$this->InstallDB();
		$this->InstallEvents();

		if ('Y' !== Option::get('sale', 'product_viewed_save', 'N')) {
			Option::set('sale', 'product_viewed_save', 'Y');
		}

		$APPLICATION->IncludeAdminFile(GetMessage("CARROT_INTEGR_INSTALL_TITLE"), $strPath2step . "/step.php");

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION;

		$strPath2step = str_replace("\\", "/", __FILE__);
		$strPath2step = substr($strPath2step, 0, strlen($strPath2step) - strlen("/index.php"));

		$this->UninstallDB();
		$this->UninstallEvents();

		$APPLICATION->IncludeAdminFile(GetMessage("CARROT_INTEGR_INSTALL_TITLE"), $strPath2step . "/unstep.php");

		return true;
	}

	function InstallEvents()
	{
		// Install events

		ModuleManager::registerModule($this->MODULE_ID);
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->registerEventHandlerCompatible("main", "OnPageStart", $this->MODULE_ID, $this->EVENTS_CLASS_MAIN, "onPageStart");
		$eventManager->registerEventHandlerCompatible("main", "OnProlog", $this->MODULE_ID, $this->EVENTS_CLASS_MAIN, "onProlog");
		$eventManager->registerEventHandlerCompatible("main", "OnEpilog", $this->MODULE_ID, $this->EVENTS_CLASS_MAIN, "onEpilog");
		/*
		 * Bitrix core versions under 15 saves trigger names cropped and, as a result, they won't work
		 * Expand one column in the handlers table to at least 52 symbols before installing this module, for it to work correctly
		 * Table name: b_module_to_module
		 * Column name: MESSAGE_ID
		*/
		$eventManager->registerEventHandlerCompatible("catalog", "\\Bitrix\\Catalog\\CatalogViewedProduct::OnBeforeUpdate", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "onProductViewedUpdate");
		$eventManager->registerEventHandlerCompatible("catalog", "\\Bitrix\\Catalog\\CatalogViewedProduct::OnBeforeAdd", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "onProductViewedAdd");
		$eventManager->registerEventHandlerCompatible("sale", "OnSaleStatusOrderChange", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "onSaleStatusOrderChange");
		$eventManager->registerEventHandler("sale", "OnSaleBasketItemEntitySaved", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "newOnBasketAdd");
		$eventManager->registerEventHandler("sale", "OnSaleOrderSaved", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "newOnOrderAdd");
		$eventManager->registerEventHandler("sale", "OnSaleOrderPaid", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "newOnSaleOrderPaid");
		$eventManager->registerEventHandler("sale", "OnSaleOrderCanceled", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "newOnSaleCancelOrder");


		// legacy events
		$eventManager->registerEventHandlerCompatible("sale", "OnBasketAdd", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "onBasketAdd");
		$eventManager->registerEventHandlerCompatible("sale", "OnBeforeViewedAdd", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "onBeforeViewedAdd");
		$eventManager->registerEventHandlerCompatible("sale", "OnOrderAdd", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "onOrderAdd");
		$eventManager->registerEventHandlerCompatible("sale", "OnSalePayOrder", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "onSalePayOrder");
		$eventManager->registerEventHandlerCompatible("sale", "OnSaleCancelOrder", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "onSaleCancelOrder");
	}

	function UninstallEvents()
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->unRegisterEventHandler("main", "OnPageStart", $this->MODULE_ID, $this->EVENTS_CLASS_MAIN, "onPageStart");
		$eventManager->unRegisterEventHandler("main", "OnProlog", $this->MODULE_ID, $this->EVENTS_CLASS_MAIN, "onProlog");
		$eventManager->unRegisterEventHandler("main", "OnEpilog", $this->MODULE_ID, $this->EVENTS_CLASS_MAIN, "OnEpilog");


		$eventManager->unRegisterEventHandler("catalog", "\\Bitrix\\Catalog\\CatalogViewedProduct::OnBeforeUpdate", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "onProductViewedUpdate");
		$eventManager->unRegisterEventHandler("catalog", "\\Bitrix\\Catalog\\CatalogViewedProduct::OnBeforeAdd", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "onProductViewedAdd");
		$eventManager->unRegisterEventHandler("sale", "OnSaleOrderSaved", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "newOnOrderAdd");
		$eventManager->unRegisterEventHandler("sale", "OnSaleStatusOrderChange", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "onSaleStatusOrderChange");
		$eventManager->unRegisterEventHandler("sale", "OnSaleOrderPaid", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "newOnSaleOrderPaid");
		$eventManager->unRegisterEventHandler("sale", "OnSaleOrderCanceled", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "newOnSaleCancelOrder");
		$eventManager->unRegisterEventHandler("sale", "OnSaleBasketItemEntitySaved", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "newOnBasketAdd");

		// legacy events
		$eventManager->unRegisterEventHandler("sale", "OnBasketAdd", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "onBasketAdd");
		$eventManager->unRegisterEventHandler("sale", "OnBeforeViewedAdd", $this->MODULE_ID, $this->EVENTS_CLASS_BASKET, "onBeforeViewedAdd");
		$eventManager->unRegisterEventHandler("sale", "OnOrderAdd", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "onOrderAdd");
		$eventManager->unRegisterEventHandler("sale", "OnSalePayOrder", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "onSalePayOrder");
		$eventManager->unRegisterEventHandler("sale", "OnSaleCancelOrder", $this->MODULE_ID, $this->EVENTS_CLASS_ORDER, "onSaleCancelOrder");

		ModuleManager::unregisterModule($this->MODULE_ID);
		//COption::RemoveOption($this->MODULE_ID); // ???????????????? ???????????????? ???????????? ?????? ???????????????? ????????????
	}

	function InstallDB()
	{
		$rsData = CUserTypeEntity::GetList(array('ID' => 'ASC'), array("ENTITY_ID" => "USER", "FIELD_NAME" => "UF_CARROTQUEST_UID"));
		if (!$rsData->Fetch()) {
			$oUserTypeEntity = new CUserTypeEntity();

			$aUserFields = array(
				'ENTITY_ID' => 'USER',
				'FIELD_NAME' => 'UF_CARROTQUEST_UID',
				'USER_TYPE_ID' => 'string',
				'XML_ID' => 'XML_ID_CARROTQUEST_UID',
				'SORT' => 500,
				'MULTIPLE' => 'N',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y', //1
				'EDIT_IN_LIST' => 'N', //1
				'IS_SEARCHABLE' => 'N',
				'SETTINGS' => array(
					'DEFAULT_VALUE' => '',
					'SIZE' => '30',
					'ROWS' => '1',
					'MIN_LENGTH' => '0',
					'MAX_LENGTH' => '0',
					'REGEXP' => '',
				),
				'EDIT_FORM_LABEL' => array(
					'ru' => 'CarrotQuest UID',
					'en' => 'CarrotQuest UID',
				),
				'LIST_COLUMN_LABEL' => array(
					'ru' => 'CarrotQuest UID',
					'en' => 'CarrotQuest UID',
				),
				'LIST_FILTER_LABEL' => array(
					'ru' => 'CarrotQuest UID',
					'en' => 'CarrotQuest UID',
				),
				'ERROR_MESSAGE' => array(
					'ru' => '???? ?????????????? ?????????????? ???????? CarrotQuest UID',
					'en' => 'An error in completing the CarrotQuest UID field',
				),
				'HELP_MESSAGE' => array(
					'ru' => '',
					'en' => '',
				),
			);

			$iUserFieldId = $oUserTypeEntity->Add($aUserFields); // int
		}
	}

	function UninstallDB()
	{
		// NOTE: Decided not to remove field on uninstall to save data unharmed, in case people decide to return module

		// $oUserTypeEntity = new CUserTypeEntity();
		// $rsData = CUserTypeEntity::GetList( array('ID' => 'ASC'), array("ENTITY_ID"=>"USER","FIELD_NAME"=>"UF_CARROTQUEST_UID") );
		// while($arRes = $rsData->Fetch())
		// {
		// $oUserTypeEntity->Delete($arRes["ID"]);
		// }
	}
}
