<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	/** @var CBitrixComponent $this */
	/** @var array $arParams */
	/** @var array $arResult */
	/** @var string $componentPath */
	/** @var string $componentName */
	/** @var string $componentTemplate */
	/** @global CDatabase $DB */
	/** @global CUser $USER */
	/** @global CMain $APPLICATION */
	/** @global CCacheManager $CACHE_MANAGER */


	use Bitrix\Main\Loader;
	use \Bitrix\Main\Localization\Loc as Loc;

	Loc::loadMessages(__FILE__);

	$this->setFrameMode(true);

	\CJSCore::Init();


	$BXMAKER_COMPONENT_NAME = 'BXMAKER.GEOIP.DELIVERY';
	$BXMAKER_MODULE_ID      = "bxmaker.geoip";

	if (!Loader::includeSharewareModule($BXMAKER_MODULE_ID)) {
		ShowError(GetMessage($BXMAKER_COMPONENT_NAME . "_MODULE_NOT_INSTALLED"));
		return false;
	}

	$oManager             = \Bxmaker\GeoIP\Manager::getInstance();


	$arParams['CACHE_TIME'] = (isset($arParams['CACHE_TIME']) && intval($arParams['CACHE_TIME']) > 0 ? $arParams['CACHE_TIME'] : 8640000);
	$arParams['CACHE_TYPE'] = (isset($arParams['CACHE_TYPE']) && in_array($arParams['CACHE_TYPE'], array('N', 'Y', 'A')) ? $arParams['CACHE_TYPE'] : 'A');

	$arParams['PRODUCT_ID'] = (isset($arParams['PRODUCT_ID']) ? $arParams['PRODUCT_ID'] : 0);
	$arParams['SHOW_PARENT']   = (isset($arParams['SHOW_PARENT']) && $arParams['SHOW_PARENT'] == 'Y' ? 'Y' : 'N');
	$arParams['IMG_SHOW']   = (isset($arParams['IMG_SHOW']) && $arParams['IMG_SHOW'] == 'Y' ? 'Y' : 'N');
	$arParams['IMG_WIDTH']  = (isset($arParams['IMG_WIDTH']) && intval($arParams['IMG_WIDTH']) ? intval($arParams['IMG_WIDTH']) : '30');
	$arParams['IMG_HEIGHT'] = (isset($arParams['IMG_HEIGHT']) && intval($arParams['IMG_HEIGHT']) ? intval($arParams['IMG_HEIGHT']) : '30');

	$arParams['PROLOG'] = (isset($arParams['PROLOG']) ? trim($arParams['PROLOG']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'PROLOG'));
	$arParams['EPILOG'] = (isset($arParams['EPILOG']) ? trim($arParams['EPILOG']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'EPILOG'));

	$arParams['QUANTITY'] = max(intval($arParams['QUANTITY']), 1);
	$arParams['PERSONAL_TYPE'] = intval($arParams['PERSONAL_TYPE']);


	$arParams['AJAX'] = (isset($arParams['AJAX']) && $arParams['AJAX'] == 'Y' ? 'Y' : 'N');


	$arParams['SUBDOMAIN_ON']           = $oManager->getParam('SUBDOMAIN_ON', 'N');
	$arParams['BASE_DOMAIN']           = $oManager->getParam('BASE_DOMAIN', $oManager->getBaseDomain());
	$arParams['COOKIE_PREFIX'] = $oManager->getCookiePrefix();


	$arParams['LOCATION'] = (isset($arParams['LOCATION']) && intval($arParams['LOCATION']) ? trim($arParams['LOCATION']) : $oManager->getLocation());
	$arParams['CITY'] = (isset($arParams['CITY']) && intval($arParams['CITY']) ? trim($arParams['CITY']) : $oManager->getCity());


	if ($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N") {
		$CACHE_TIME = 0;
	}
	else {
		$CACHE_TIME = $arParams["CACHE_TIME"];
	}

	if ($this->startResultCache($CACHE_TIME)) {

		if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {
			$GLOBALS['CACHE_MANAGER']->RegisterTag('bxmaker_geoip_delivery');
		}

		if (!CModule::IncludeModule('catalog') && !CModule::IncludeModule('sale')) {
			$this->abortResultCache();
		}
		else {

			$arResult = array(
				'DEFAULT_CITY' => $oManager->getParam('DEFAULT_CITY', Loc::getMessage($BXMAKER_COMPONENT_NAME . 'DEFAULT_CITY')),
				'DEBUG'        => $oManager->getParam('DEBUG', 'N'),
				'ITEMS'        => array()
			);

			$oCatalogSKU = new \CCatalogSKU();
			$oProduct    = new \CCatalogProduct();

			// Определеяем цену
			$price    = 0;
			$arOffers = $oCatalogSKU->getOffersList(array($arParams['PRODUCT_ID']));
			if (count($arOffers)) {
				foreach ($arOffers[$arParams['PRODUCT_ID']] as $arOffer) {
					$arProductPrice = $oProduct->GetOptimalPrice($arOffer['ID'], $arParams['QUANTITY'], $arParams['USER_GROUPS']);

					if (!$price || $price > $arProductPrice['DISCOUNT_PRICE']) {
						$price                  = $arProductPrice['DISCOUNT_PRICE'];
						$arParams['PRODUCT_ID'] = $arOffer['ID'];
					}
				}
			}


			//persona type
			if(!$arParams['PERSONAL_TYPE'] && Loader::includeModule('sale')){
				$dbPersonType = \CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("ACTIVE" => "Y", "LID" => SITE_ID));
				if ($arPersonType = $dbPersonType->GetNext()) {
					$arParams['PERSONAL_TYPE'] = $arPersonType["ID"];
				}
			}


			$arFields = array(
				'PRODUCT_ID' => $arParams['PRODUCT_ID'],
				'QUANTIYT'   => intval($arParams['QUANTITY']),
				'SITE_ID'    => SITE_ID,
				'LOCATION'   => $arParams['LOCATION'],
				'PERSONAL_TYPE' => $arParams['PERSONAL_TYPE']
			);

			$arResult['ITEMS'] = $oManager->getDeliveryItems($arFields);



		}



		$this->setResultCacheKeys(array());
		$this->IncludeComponentTemplate();
	}



