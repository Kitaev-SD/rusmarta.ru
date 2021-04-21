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

	global $CACHE_MANAGER;
	use Bitrix\Main\Loader;
	use Bitrix\Main\Localization\Loc;

	Loc::loadLanguageFile(__FILE__);

	$this->setFrameMode(true);

	\CJSCore::Init();

	$BXMAKER_COMPONENT_NAME = 'BXMAKER.GEOIP.CITY.LINE';
	$BXMAKER_MODULE_ID    = "bxmaker.geoip";

	if (!Loader::includeSharewareModule($BXMAKER_MODULE_ID)) {
		ShowError(GetMessage($BXMAKER_COMPONENT_NAME . "_MODULE_NOT_INSTALLED"));
		return 0;
	}

	$oManager             = \Bxmaker\GeoIP\Manager::getInstance();


	$arParams['CACHE_TIME'] = (isset($arParams['CACHE_TIME']) && intval($arParams['CACHE_TIME']) > 0 ? $arParams['CACHE_TIME'] : 8640000);
	$arParams['CACHE_TYPE'] = (isset($arParams['CACHE_TYPE']) && in_array($arParams['CACHE_TYPE'], array('N', 'Y', 'A')) ? $arParams['CACHE_TYPE'] : 'A');

	$arParams['CITY_LABEL'] = trim($arParams['CITY_LABEL']);
	$arParams['QUESTION_SHOW'] = ($arParams['QUESTION_SHOW'] == 'Y' ? 'Y' : 'N');
	$arParams['INFO_SHOW'] = ($arParams['INFO_SHOW'] == 'Y' ? 'Y' : 'N');
	$arParams['QUESTION_TEXT'] = (isset($arParams['~QUESTION_TEXT']) ? trim($arParams['~QUESTION_TEXT']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'QUESTION_TEXT_DEFAULT'));
	$arParams['INFO_TEXT'] =  (isset($arParams['~INFO_TEXT']) ? trim($arParams['~INFO_TEXT']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INFO_TEXT_DEFAULT'));
	$arParams['BTN_EDIT'] = (isset($arParams['BTN_EDIT']) ? trim($arParams['BTN_EDIT']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'BTN_EDIT_DEFAULT'));


	$arParams['SUBDOMAIN_ON']           = $oManager->getParam('SUBDOMAIN_ON', 'N');
	$arParams['BASE_DOMAIN']           = $oManager->getParam('BASE_DOMAIN', $oManager->getBaseDomain());
	$arParams['COOKIE_PREFIX'] = $oManager->getCookiePrefix();


	if ($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N") {
		$CACHE_TIME = 0;
	}
	else {
		$CACHE_TIME = $arParams["CACHE_TIME"];
	}

	if ($this->StartResultCache($CACHE_TIME)) {

		if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER']))
		{
			$GLOBALS['CACHE_MANAGER']->RegisterTag('bxmaker_geoip_city_line');
		}

		$oManager = \Bxmaker\GeoIP\Manager::getInstance();

		$arResult['CITY_DEFAULT'] = $oManager->getParam('DEFAULT_CITY', Loc::getMessage($BXMAKER_COMPONENT_NAME.'_CITY_DEFAULT'));
		$arResult['DEBUG'] = $oManager->getParam('DEBUG', 'N');


		$this->setResultCacheKeys(array());
		$this->IncludeComponentTemplate();
	}

