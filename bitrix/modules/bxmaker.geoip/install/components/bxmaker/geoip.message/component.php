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

	$PARTNER_COMPONENT_ID = 'BXMAKER.GEOIP.MESSAGE';
	$BXMAKER_MODULE_ID    = "bxmaker.geoip";

	if (!Loader::includeSharewareModule($BXMAKER_MODULE_ID)) {
		ShowError(GetMessage($PARTNER_COMPONENT_ID . "_MODULE_NOT_INSTALLED"));
		return false;
	}
	Loader::includeModule($BXMAKER_MODULE_ID);


	$oManager       = \Bxmaker\GeoIP\Manager::getInstance();
	$oFavoritesCity = new \Bxmaker\GeoIP\Favorites\CityTable();


	$arParams['CACHE_TYPE'] = (isset($arParams['CACHE_TYPE']) ? $arParams['CACHE_TYPE'] : 'A');
	$arParams['CACHE_TIME'] = (isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : 3600);
	$arParams['TYPE']       = (isset($arParams['TYPE']) && strlen(trim($arParams['TYPE'])) > 0 ? $arParams['TYPE'] : '');
	$arParams['CITY']       = (isset($arParams['CITY']) && strlen(trim($arParams['CITY'])) > 0 ? $arParams['CITY'] : $oManager->getCity());
	$arParams['LOCATION']   = (isset($arParams['LOCATION']) && strlen(trim($arParams['LOCATION'])) > 0 ? $arParams['LOCATION'] : $oManager->getLocation());
	$arParams['AJAX']       = (isset($arParams['AJAX']) && $arParams['AJAX'] == 'Y' ? 'Y' : 'N');


	$arParams['SUBDOMAIN_ON']  = $oManager->getParam('SUBDOMAIN_ON', 'N');
	$arParams['BASE_DOMAIN']   = $oManager->getParam('BASE_DOMAIN', $oManager->getBaseDomain());
	$arParams['COOKIE_PREFIX'] = $oManager->getCookiePrefix();


	if ($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N") {
		$CACHE_TIME = 0;
	}
	else {
		$CACHE_TIME = $arParams["CACHE_TIME"];
	}

	if ($this->StartResultCache($CACHE_TIME)) {


		$arResult = array(
			'DEFAULT' => array(),
			'CURRENT' => array(),
			'DEBUG'   => $oManager->getParam('DEBUG', 'N')
		);

		if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {
			$GLOBALS['CACHE_MANAGER']->RegisterTag('bxmaker_geoip_message');
		}


		$arLocationGroupId = array();
		if ($oManager->isModuleSaleInstalled()) {
			$dbGroup = \CSaleLocationGroup::GetLocationList(array("LOCATION_ID" => $arParams['LOCATION']));
			while ($arGroup = $dbGroup->Fetch()) {
				$arLocationGroupId[] = $arGroup['LOCATION_GROUP_ID'];
			}
			$arLocationGroupId = array_unique($arLocationGroupId);
		}
		else {
			$dbrGroup = $oFavoritesCity->getList(array(
				'filter' => array(
					'LOCATION_ID' => $arParams['LOCATION'],
					'SITE.SID' => $oManager->getCurrentSiteId()
				)
			));
			while($arGroup = $dbrGroup->fetch())
			{
				$arLocationGroupId[] = $arGroup['FID'];
			}
		}


		$arDefault  = array();
		$arCurrent  = array();
		$arFilterOr = array(
			'LOGIC' => 'OR', // по умолчанию элементы склеиваются через AND
			'=CITY' => $arParams['CITY'],
			'=DEF'  => true
		);
		if (count($arLocationGroupId)) {
			$arFilterOr['=GROUP'] = $arLocationGroupId;
		}


		$oMessage = new \Bxmaker\GeoIP\MessageTable();
		$dbr      = $oMessage->getList(array(
			'filter' => array(
				'TYPE.TYPE'    => $arParams['TYPE'],
				'TYPE.SITE_ID' => SITE_ID,
				$arFilterOr
			)
		));
		while ($ar = $dbr->fetch()) {

			if ($ar['DEF']) {
				$arResult['DEFAULT'] = $ar;
			}
			else {
				$arResult['CURRENT'] = $ar;
			}
		}

		if (empty($arResult['CURRENT'])) {
			$arResult['CURRENT'] = $arResult['DEFAULT'];
		}

		$arResult['CURRENT']['TIME'] = ($arResult['CURRENT']['START'] != '00:00' || $arResult['CURRENT']['STOP'] != '23:59' ? 'Y' : 'N'); //есть ли зависимость от времени

		$this->setResultCacheKeys(array());

		$this->IncludeComponentTemplate();
	}



