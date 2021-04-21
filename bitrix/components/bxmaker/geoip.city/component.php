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
	$BXMAKER_MODULE_ID      = "bxmaker.geoip";

	if (!Loader::includeSharewareModule($BXMAKER_MODULE_ID)) {
		ShowError(GetMessage($BXMAKER_COMPONENT_NAME . "_MODULE_NOT_INSTALLED"));
		return 0;
	}

	$oManager = \Bxmaker\GeoIP\Manager::getInstance();


	$arParams['CACHE_TIME']       = (isset($arParams['CACHE_TIME']) && intval($arParams['CACHE_TIME']) > 0 ? $arParams['CACHE_TIME'] : 8640000);
	$arParams['CACHE_TYPE']       = (isset($arParams['CACHE_TYPE']) && in_array($arParams['CACHE_TYPE'], array('N', 'Y', 'A')) ? $arParams['CACHE_TYPE'] : 'A');
	$arParams['CITY_LABEL']       = trim($arParams['~CITY_LABEL']);
	$arParams['RELOAD_PAGE']      = ($arParams['RELOAD_PAGE'] == 'Y' ? 'Y' : 'N');
	$arParams['CITY_SHOW']        = ($arParams['CITY_SHOW'] == 'Y' ? 'Y' : 'N');
	$arParams['QUESTION_SHOW']    = ($arParams['~QUESTION_SHOW'] == 'Y' ? 'Y' : 'N');
	$arParams['INFO_SHOW']        = ($arParams['~INFO_SHOW'] == 'Y' ? 'Y' : 'N');
	$arParams['QUESTION_TEXT']    = (isset($arParams['~QUESTION_TEXT']) ? trim($arParams['~QUESTION_TEXT']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'QUESTION_TEXT_DEFAULT'));
	$arParams['INFO_TEXT']        = (isset($arParams['~INFO_TEXT']) ? trim($arParams['~INFO_TEXT']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INFO_TEXT_DEFAULT'));
	$arParams['BTN_EDIT']         = (isset($arParams['~BTN_EDIT']) ? trim($arParams['~BTN_EDIT']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'BTN_EDIT_DEFAULT'));
	$arParams['POPUP_LABEL']      = (isset($arParams['~POPUP_LABEL']) ? trim($arParams['~POPUP_LABEL']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'POPUP_LABEL_DEFAULT'));
	$arParams['INPUT_LABEL']      = (isset($arParams['INPUT_LABEL']) ? trim($arParams['INPUT_LABEL']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INPUT_LABEL_DEFAULT'));
	$arParams['MSG_EMPTY_RESULT'] = (isset($arParams['MSG_EMPTY_RESULT']) ? trim($arParams['MSG_EMPTY_RESULT']) : Loc::getMessage($BXMAKER_COMPONENT_NAME . 'MSG_EMPTY_RESULT_DEFAULT'));

	$arParams['SEARCH_SHOW']   = (isset($arParams['SEARCH_SHOW']) && $arParams['SEARCH_SHOW'] == 'Y' ? 'Y' : 'N');
	$arParams['FAVORITE_SHOW'] = (isset($arParams['FAVORITE_SHOW']) && $arParams['FAVORITE_SHOW'] == 'Y' ? 'Y' : 'N');
	$arParams['CITY_COUNT']    = (isset($arParams['CITY_COUNT']) && intval($arParams['CITY_COUNT']) > 0 ? intval($arParams['CITY_COUNT']) : 30);
	$arParams['FID']           = (isset($arParams['FID']) ? intval($arParams['FID']) : 0);


	$arParams['SUBDOMAIN_ON']  = $oManager->getParam('SUBDOMAIN_ON', 'N');
	$arParams['BASE_DOMAIN']   = $oManager->getParam('BASE_DOMAIN', $oManager->getBaseDomain());
	$arParams['SUB_DOMAIN']   = $oManager->getSubdomainHost();
	$arParams['COOKIE_PREFIX'] = $oManager->getCookiePrefix();



	if ($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N") {
		$CACHE_TIME = 0;
	}
	else {
		$CACHE_TIME = $arParams["CACHE_TIME"];
	}


	if ($this->StartResultCache($CACHE_TIME, $subCacheID)) {

		if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {
			$GLOBALS['CACHE_MANAGER']->RegisterTag('bxmaker_geoip_city');
		}

		$oFavoritesCity = new Bxmaker\GeoIP\Favorites\CityTable();

		$arResult['ITEMS'] = array();

		// есл нужно выводить список городов
		if ($arParams['FAVORITE_SHOW'] && $arParams['FID']) {

			//  полуаем список избранных местоположений
			$iFavoriteCity    = 1;
			$arFavoriteCityID = array();
			$dbrFavoriteCity  = $oFavoritesCity->getList(array(
				'order'  => array('SORT' => 'ASC'),
				'filter' => array(
					'FID' => intval($arParams['FID'])
				)
			));
			while ($arFavoriteCity = $dbrFavoriteCity->fetch()) {
				$arFavoriteCityID[$arFavoriteCity['LOCATION_ID']] = array(
					'SORT' => $arFavoriteCity["SORT"],
					'MARK' => $arFavoriteCity["MARK"]
				);
			}

			if (count($arFavoriteCityID)) {
				// если используется локальная база
				if ($oManager->isUsedLocalBase()) {

					$oLocationCity    = new \BXmaker\GeoIP\Location\CityTable();
					$oLocationRegion  = new \BXmaker\GeoIP\Location\RegionTable();
					$oLocationCountry = new \BXmaker\GeoIP\Location\CountryTable();

					$dbrLocationCity = $oLocationCity->getList(array(
						'filter' => array(
							'ID' => array_keys($arFavoriteCityID)
						)
					));
					while ($arLocationCity = $dbrLocationCity->fetch()) {
						$arResult['ITEMS'][] = array(
							'ID'      => $arLocationCity['ID'],
							'NAME'    => $arLocationCity['NAME'],
							'NAME_EN' => $arLocationCity['NAME_EN'],
							'SORT'    => $arFavoriteCityID[$arLocationCity['ID']]['SORT'],
							'MARK'    => $arFavoriteCityID[$arLocationCity['ID']]['MARK']
						);
					}


				}
				// если используются местоположения интернет-магазина
				elseif (Loader::includeModule('sale')) {

					if (\CSaleLocation::isLocationProEnabled()) {
						$res = \Bitrix\Sale\Location\LocationTable::getList(array(
							'select' => array('*', 'CITY_NAME' => 'NAME.NAME'),
							'order'  => array('SORT' => 'ASC'),
							'filter' => array(
								'=NAME.LANGUAGE_ID' => $oManager->getParam('LOCATION_LANG', LANGUAGE_ID),
								'!CITY_ID'          => false,
								'ID'                => array_keys($arFavoriteCityID)
							)
						));
						while ($arLocationCity = $res->fetch()) {
							$arResult['ITEMS'][] = array(
								'ID'      => $arLocationCity['ID'],
								'NAME'    => $arLocationCity['CITY_NAME'],
								'NAME_EN' => $arLocationCity['CITY_NAME'],
								'SORT'    => $arFavoriteCityID[$arLocationCity['ID']]['SORT'],
								'MARK'    => $arFavoriteCityID[$arLocationCity['ID']]['MARK'],
							);
						}
					}
					else {

						$res = \CSaleLocation::GetList(
							array(
								"SORT"              => "ASC",
								"COUNTRY_NAME_LANG" => "ASC",
								"CITY_NAME_LANG"    => "ASC"
							),
							array(
								"LID"        => $oManager->getParam('LOCATION_LANG', LANGUAGE_ID),
								'!CITY_NAME' => false,
								'ID'         => array_keys($arFavoriteCityID)
							)
						);
						while ($arLocationCity = $res->fetch()) {
							$arResult['ITEMS'][] = array(
								'ID'      => $arLocationCity['ID'],
								'NAME'    => $arLocationCity['CITY_NAME'],
								'NAME_EN' => $arLocationCity['CITY_NAME'],
								'SORT'    => $arFavoriteCityID[$arLocationCity['ID']]['SORT'],
								'MARK'    => $arFavoriteCityID[$arLocationCity['ID']]['MARK']
							);
						}
					}
				}

				\Bitrix\Main\Type\Collection::sortByColumn($arResult['ITEMS'], 'SORT');
			}
		}

		$arResult['DEBUG']                    = $oManager->getParam('DEBUG', 'N');
		$arResult['USE_YANDEX']               = $oManager->getParam('USE_YANDEX', 'Y');
		$arResult['USE_YANDEX_SEARCH']        = $oManager->getParam('USE_YANDEX_SEARCH', 'N');
		$arResult['YANDEX_SEARCH_SKIP_WORDS'] = $oManager->getParam('YANDEX_SEARCH_SKIP_WORDS', '');

		$arResult['YANDEX_SEARCH_SKIP_WORDS'] = preg_replace('/,\s*/', ',', $arResult['YANDEX_SEARCH_SKIP_WORDS']);

		
		$this->IncludeComponentTemplate();
	}
