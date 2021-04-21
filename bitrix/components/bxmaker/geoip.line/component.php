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


	$this->setFrameMode(true);


	$arParams['CACHE_TIME'] = (isset($arParams['CACHE_TIME']) && intval($arParams['CACHE_TIME']) > 0 ? $arParams['CACHE_TIME'] : 8640000);
	$arParams['CACHE_TYPE'] = (isset($arParams['CACHE_TYPE']) && in_array($arParams['CACHE_TYPE'], array('N', 'Y', 'A')) ? $arParams['CACHE_TYPE'] : 'A');


	$PARTNER_COMPONENT_ID = 'BXMAKER.GEOIP.LINE';
	$MODULE_ID            = "bxmaker.geoip";

	if (!Loader::includeSharewareModule($MODULE_ID)) {
		ShowError(GetMessage($PARTNER_COMPONENT_ID . "_MODULE_NOT_INSTALLED"));
		return 0;
	}
	Loader::includeModule($MODULE_ID);


	if ($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N") {
		$CACHE_TIME = 0;
	}
	else {
		$CACHE_TIME = $arParams["CACHE_TIME"];
	}

	$CACHE_ID  = md5(implode('|', $arParams) . '|' . SITE_ID . '|' . $this->__template);
	$CACHE_DIR = "/bxmaker.geoip.epilog/";

	$cache = new CPHPCache;
	if ($cache->StartDataCache($arParams["CACHE_TIME"], $CACHE_ID, $CACHE_DIR)) {

		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($CACHE_DIR);
		// добавляем метку для кэша
		$CACHE_MANAGER->RegisterTag('bxmaker_geoip_params');


		$CACHE_MANAGER->EndTagCache();



		$cache->EndDataCache(
			array(
				'arResult' => $arResult
//				"templateCachedData" => $this->GetTemplateCachedData()
			)
		);
	}
	else {
		$vars     = $cache->GetVars();
//		$this->SetTemplateCachedData($vars['templateCachedData']);
		$arResult = $vars['arResult'];
	}

	$this->IncludeComponentTemplate();