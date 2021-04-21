<?

	namespace Bxmaker\GeoIP;

	use Bitrix\Main\Application;
	use Bitrix\Main\Entity;
	use Bitrix\Main\Loader;
	use Bitrix\Main\Localization\Loc;

	Loc::loadMessages(__FILE__);


	/**
	 * Class Handler
	 * @package Bxmaker\GeoIP
	 */
	class Handler
	{

		static private $module_id = 'bxmaker.geoip';


		public static function main_onBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu)
		{
			$arGlobalMenu['global_menu_bxmaker'] = Array(
				'menu_id'      => 'bxmaker',
				'text'         => Loc::getMessage(self::$module_id . '_HANDLER.GLOBAL_MENU_TEXT'),
				'title'        => Loc::getMessage(self::$module_id . '_HANDLER.GLOBAL_MENU_TITLE'),
				'sort'         => '250',
				'items_id'     => 'global_menu_bxmaker',
				'help_section' => 'bxmaker',
				'items'        => Array()
			);
		}


		/**
		 * после создания заказа
		 */
		static public function sale_OnSaleComponentOrderProperties(&$arUserResult, $request, &$arParams, &$arResult)
		{

			$geoManager = \Bxmaker\GeoIP\Manager::getInstance();

			/**
			 * @var $request \Bitrix\Main\HttpRequest
			 */

			$bChange = false;
			// если POST запрос
			if (intval($arUserResult['PERSON_TYPE_ID']) > 0) {
				if ($request->isPost()) {
					//смена типа плательщика
					if ($arUserResult['PERSON_TYPE_ID'] != $arUserResult['PERSON_TYPE_OLD'] && $arUserResult['PERSON_TYPE_OLD'] !== false) {
						if ($arUserResult['PROFILE_ID']) {
							//если можно сменить
							if ($geoManager->getParam('CHANGE_PROFILE_CITY', 'Y') == 'Y') {
								$bChange = true;
							}
						}
						else {
							// если не указан профиль, значит первый раз оформляется
							$bChange = true;
						}
					}
				}
				else {
					// еслиу указан профиль
					if ($arUserResult['PROFILE_ID']) {
						//если можно сменить
						if ($geoManager->getParam('CHANGE_PROFILE_CITY', 'Y') == 'Y') {
							$bChange = true;
						}
					}
					else {
						// если не указан профиль, значит первый раз оформляется
						$bChange = true;
					}
				}
			}


			//профиль знаем
			if ($bChange) {

				$app   = Application::getInstance();
				$order = \Bitrix\Sale\Order::create($app->getContext()->getSite());
				$order->isStartField();

				$propertyCollection = $order->getPropertyCollection();
				/** @var \Bitrix\Sale\PropertyValue $property */
				foreach ($propertyCollection as $property) {
					if ($property->isUtil()) {
						continue;
					}

					$arProperty = $property->getProperty();

					$curVal = null;

					if ($arProperty["IS_ZIP"] == "Y") {
						$arUserResult['ORDER_PROP'][$arProperty["ID"]] = $geoManager->getZip();
					}

					if ($arProperty["TYPE"] == 'LOCATION') {

						$curVal                                        = \CSaleLocation::getLocationCODEbyID($geoManager->getLocation());
						$arUserResult['ORDER_PROP'][$arProperty["ID"]] = $curVal;

					}


				}
			}

		}


		/**
		 * В форме оформления заказа подставляет  город
		 *
		 * @param $arResult
		 * @param $arUserResult
		 * @param $params
		 *
		 * @return bool
		 */
		static public function sale_OnSaleComponentOrderOneStepOrderProps(&$arResult, &$arUserResult, &$params)
		{

			$geoManager = \Bxmaker\GeoIP\Manager::getInstance();

			$location = $geoManager->getLocation();
			$zip      = $geoManager->getZip();

			if (Manager::isAdminSection()) return true;
			if (!intval($location)) return true;

			//есть профиль и изменять город не нужно
			if ($arUserResult['PROFILE_ID']) {
				if ($geoManager->getParam('CHANGE_PROFILE_CITY', 'Y') != 'Y') {
					return true;
				}
			}

			//если не первый запрос
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				do {
					//использование профиля
					if ($arUserResult['PROFILE_ID'] && $arUserResult['PROFILE_CHANGE'] == 'Y') {
						// если можно менть город
						if ($geoManager->getParam('CHANGE_PROFILE_CITY', 'Y') == 'Y') {
							// можно идти дальше и менять значение
							break;
						}
					}

					return true;

				} while (false);
			}


			if (isset($arResult['ORDER_PROP']['USER_PROPS_Y'])) {
				$oldValue = null;
				$newValue = null;
				foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as &$item) {


					if ($item['IS_LOCATION'] == 'Y') {
						foreach ($item['VARIANTS'] as $k => &$variant) {
							if (isset($variant['SELECTED']) && $variant['SELECTED'] == 'Y') {
								$oldValue = $k;
								unset($variant['SELECTED']);
							}
							if ($variant['ID'] == $location) {
								$newValue = $k;
							}

						}
						unset($variant);

						if (!is_null($newValue) && isset($item['VARIANTS'][$newValue])) {
							$item['VARIANTS'][$newValue]['SELECTED'] = 'Y';
							$arUserResult['DELIVERY_LOCATION']       = $item['VARIANTS'][$newValue]['ID'];
						}
						elseif (!is_null($oldValue) && isset($item['VARIANTS'][$oldValue])) {
							$item['VARIANTS'][$oldValue]['SELECTED'] = 'Y';
							$arUserResult['DELIVERY_LOCATION']       = $item['VARIANTS'][$oldValue]['ID'];
						}
					}

					if ($item['IS_ZIP'] == 'Y') {
						if ($zip != '000000' && ($item['VALUE'] == '' || $item['VALUE'] == '000000')) {
							$item['VALUE']                         = $zip;
							$arUserResult['DELIVERY_LOCATION_ZIP'] = $zip;
						}
					}
				}

				unset($item);
			}

			if (isset($arResult['ORDER_PROP']['USER_PROPS_N'])) {
				$oldValue = null;
				$newValue = null;
				foreach ($arResult['ORDER_PROP']['USER_PROPS_N'] as &$item) {

					if ($item['IS_LOCATION'] == 'Y') {
						foreach ($item['VARIANTS'] as $k => &$variant) {
							if (isset($variant['SELECTED']) && $variant['SELECTED'] == 'Y') {
								$oldValue = $k;
								unset($variant['SELECTED']);
							}
							if ($variant['ID'] == $location) {
								$newValue = $k;
							}
						}
						unset($variant);

						if (!is_null($newValue) && isset($item['VARIANTS'][$newValue])) {
							$item['VARIANTS'][$newValue]['SELECTED'] = 'Y';
							$arUserResult['DELIVERY_LOCATION']       = $item['VARIANTS'][$newValue]['ID'];
						}
						elseif (!is_null($oldValue) && isset($item['VARIANTS'][$oldValue])) {
							$item['VARIANTS'][$oldValue]['SELECTED'] = 'Y';
							$arUserResult['DELIVERY_LOCATION']       = $item['VARIANTS'][$oldValue]['ID'];
						}
					}

					if ($item['IS_ZIP'] == 'Y') {
						if ($zip != '000000' && ($item['VALUE'] == '' || $item['VALUE'] == '000000')) {
							$item['VALUE']                         = $zip;
							$arUserResult['DELIVERY_LOCATION_ZIP'] = $zip;
						}
					}
				}
				unset($item);
			}

			return true;
		}


		/**
		 * Обработка ajax
		 */
		static public function main_OnBeforeProlog()
		{
			global $APPLICATION;

			if (!Manager::isAdminSection()) {

				$req = Application::getInstance()->getContext()->getRequest();

				$oManager = Manager::getInstance();

				if ($req->getRequestMethod() == 'POST'
					&& !!$req->getPost('module')
					&& $req->getPost('module') == 'bxmaker.geoip'
				) {

					$msg = 'bxmaker.geoip_HANDLER.';

					$arJson = array(
						'response' => array(),
						'error'    => array()
					);

					if ($oManager->isExpired()) {
						$arJson['error'] = array(
							'CODE' => '0',
							'MSG'  => GetMessage($msg . 'DEMO_EXPIRED'),
							'MORE' => ''
						);
					}
					else {
						switch ($req->getPost('method')) {
							// локальная база
							case 'selectLocation': {

								if (!check_bitrix_sessid('sessid')) {
									$arJson['error'] = array(
										'CODE' => 'h1',
										'MSG'  => GetMessage($msg . 'ERROR_SESSID'),
										'MORE' => ''
									);
									break;
								}

								if ($oManager->isExpired()) {
									$arJson['error'] = array(
										'CODE' => 'h2',
										'MSG'  => GetMessage($msg . 'DEMO_EXPIRED'),
										'MORE' => ''
									);
									break;
								}

								if (intval($req->getPost('id')) <= 0) {
									$arJson['error'] = array(
										'CODE' => 'h4',
										'MSG'  => GetMessage($msg . 'ERROR_LOCATION_ID'),
										'MORE' => ''
									);
									break;
								}

								$oManager->selectLocation(intval($req->getPost('id')));

								$arJson['response'] = $oManager->getFullData();

								//проверка необходимости переадресации на поддомен
								if ($oManager->isNeedRedirectToSubdomain()) {
									$arJson['response']['redirect'] = $oManager->getSubdomainUrl();
								}

								break;
							}
							// город из яндекса
							case 'selectYandexLocation': {

								if (!check_bitrix_sessid('sessid')) {
									$arJson['error'] = array(
										'CODE' => 'h1',
										'MSG'  => GetMessage($msg . 'ERROR_SESSID'),
										'MORE' => ''
									);
									break;
								}

								if ($oManager->isExpired()) {
									$arJson['error'] = array(
										'CODE' => 'h2',
										'MSG'  => GetMessage($msg . 'DEMO_EXPIRED'),
										'MORE' => ''
									);
									break;
								}

								if (!$req->getPost('city') || strlen(trim($oManager->restoreEncoding($req->getPost('city')))) < 3) {
									$arJson['error'] = array(
										'CODE' => 'h5',
										'MSG'  => GetMessage($msg . 'ERROR_CITY'),
										'MORE' => ''
									);
									break;
								}
								if (!$req->getPost('country') || strlen(trim($oManager->restoreEncoding($req->getPost('country')))) < 3) {
									$arJson['error'] = array(
										'CODE' => 'h6',
										'MSG'  => GetMessage($msg . 'ERROR_COUNTRY'),
										'MORE' => ''
									);
									break;
								}

								$country  = $oManager->restoreEncoding(trim($req->getPost('country')));
								$city     = $oManager->restoreEncoding(trim($req->getPost('city')));
								$region   = $oManager->restoreEncoding(trim($req->getPost('region')));
								$area     = $oManager->restoreEncoding(trim($req->getPost('area')));
								$lng      = floatval($req->getPost('lng'));
								$lat      = floatval($req->getPost('lat'));
								$location = intval($req->getPost('location'));

								$country = strip_tags($country);
								$city    = strip_tags($city);
								$region  = strip_tags($region);
								$area    = strip_tags($area);


								//если поиск города по яндексу, то ничего не делаем
								if ($oManager->getParam('USE_YANDEX_SEARCH', 'N') == 'Y') {
									$oManager->setYandex(1);
									$oManager->setLocation($location);
									$oManager->setLocationCode($location);
									$oManager->setZip('000000');
									$oManager->setCountryId(0);
									$oManager->setRegionId(0);
									$oManager->setLat($lat);
									$oManager->setLng($lng);
									$oManager->setCountry($country);
									$oManager->setCity($city);
									$oManager->setRegion($region);
									$oManager->setArea($area);


									$oManager->saveCookie();
								}
								// иначе ищем соответствие
								else {

									if ($region == $city) $region = '';

									$arFoundLocation = $oManager->searchLocation($city);
									foreach ($arFoundLocation as $item) {

										$bCityEqual    = (\Bitrix\Main\Text\BinaryString::changeCaseToLower($item['city']) == \Bitrix\Main\Text\BinaryString::changeCaseToLower($city));
										$bCountryEqual = (\Bitrix\Main\Text\BinaryString::changeCaseToLower($item['country']) == \Bitrix\Main\Text\BinaryString::changeCaseToLower($country));
										$bRegionEqual  = (\Bitrix\Main\Text\BinaryString::changeCaseToLower($item['region']) == \Bitrix\Main\Text\BinaryString::changeCaseToLower($region));
										$bAreaEqual    = (\Bitrix\Main\Text\BinaryString::changeCaseToLower($item['area']) == \Bitrix\Main\Text\BinaryString::changeCaseToLower($area));


										if (($bCityEqual + $bCountryEqual + $bRegionEqual + $bAreaEqual) >= 3) {
											$oManager->selectLocation($item['location']);
											break;
										}
									}
								}

								$arJson['response'] = $oManager->getFullData();


								break;
							}
							// поиск
							case 'search': {

								if (!check_bitrix_sessid('sessid')) {
									$arJson['error'] = array(
										'CODE' => 'h6',
										'MSG'  => GetMessage($msg . 'ERROR_SESSID'),
										'MORE' => ''
									);
									break;
								}

								if (!$req->getPost('query') || strlen(trim($oManager->restoreEncoding($req->getPost('query')))) < 3) {
									$arJson['error'] = array(
										'CODE' => 'h7',
										'MSG'  => GetMessage($msg . 'ERROR_SEARCH_LENGTH'),
										'MORE' => ''
									);
									break;
								}

								if ($oManager->isExpired()) {
									$arJson['error'] = array(
										'CODE' => 'h8',
										'MSG'  => GetMessage($msg . 'DEMO_EXPIRED'),
										'MORE' => ''
									);
									break;
								}


								$arJson['response'] = array(
									'items' => array(),
									'count' => 0
								);

								$arJson['response']['items'] = $oManager->searchLocation($oManager->restoreEncoding($req->getPost('query')));
								$arJson['response']['count'] = count($arJson['response']['items']);

								break;
							}

							case 'getMessage': {

								if (!check_bitrix_sessid('sessid')) {
									$arJson['error'] = array(
										'CODE' => 'h9',
										'MSG'  => GetMessage($msg . 'ERROR_SESSID'),
										'MORE' => ''
									);
									break;
								}

								if ($oManager->isExpired()) {
									$arJson['error'] = array(
										'CODE' => 'h10',
										'MSG'  => GetMessage($msg . 'DEMO_EXPIRED'),
										'MORE' => ''
									);
									break;
								}


								ob_start();
								$APPLICATION->IncludeComponent('bxmaker:geoip.message', ($req->getPost('template') ? trim((string)$req->getPost('template')) : ''), array(
									'AJAX'                 => 'Y',
									'TYPE'                 => trim((string)$req->getPost('type')),
									"COMPONENT_TEMPLATE"   => ($req->getPost('template') ? trim((string)$req->getPost('template')) : ''),
									"CACHE_TYPE"           => "A",
									"CACHE_TIME"           => "3600",
									"COMPOSITE_FRAME_MODE" => "A",
									"COMPOSITE_FRAME_TYPE" => "AUTO"
								));
								$arJson['response']['html'] = ob_get_clean();


								break;
							}

							case 'getDelivery': {

								if (!check_bitrix_sessid('sessid')) {
									$arJson['error'] = array(
										'CODE' => 'h9',
										'MSG'  => GetMessage($msg . 'ERROR_SESSID'),
										'MORE' => ''
									);
									break;
								}

								if ($oManager->isExpired()) {
									$arJson['error'] = array(
										'CODE' => 'h10',
										'MSG'  => GetMessage($msg . 'DEMO_EXPIRED'),
										'MORE' => ''
									);
									break;
								}

								ob_start();
								$APPLICATION->IncludeComponent('bxmaker:geoip.delivery', ($req->getPost('template') ? trim((string)$req->getPost('template')) : ''), array(
									'AJAX'                 => 'Y',
									"COMPONENT_TEMPLATE"   => ($req->getPost('template') ? trim((string)$req->getPost('template')) : ''),
									"CACHE_TYPE"           => "A",
									"CACHE_TIME"           => "3600",
									"COMPOSITE_FRAME_MODE" => "A",
									"COMPOSITE_FRAME_TYPE" => "AUTO",
									"PRODUCT_ID"           => intval($req->getPost('productId')),
									"SHOW_PARENT"          => ($req->getPost('showParent') == 'Y' ? 'Y' : 'N'),
									"IMG_SHOW"             => ($req->getPost('imgShow') == 'Y' ? 'Y' : 'N'),
									"IMG_WIDTH"            => intval($req->getPost('imgWidth')),
									"IMG_HEIGHT"           => intval($req->getPost('imgHeight')),
									"PROLOG"               => "",
									"EPILOG"               => ""
								));
								$arJson['response']['html'] = ob_get_clean();


								break;
							}
							case 'checkNeedRedirect': {

								//проверка необходимости переадресации на поддомен
								$arJson['response']['need'] = $oManager->isNeedRedirectToSubdomain();
								$arJson['response']['redirect'] = $oManager->getSubdomainUrl();


								break;
							}
							// ok+
							default: {
								$arJson['error'] = array(
									'CODE' => 'h11',
									'MSG'  => GetMessage($msg . 'ERROR_METHOD'),
									'MORE' => ''
								);
								break;
							}
						}
					}

					$oManager->showJson($arJson);
				}
				elseif ($req->getRequestMethod() != 'POST') {
					//проверка необходимости переадресации на поддомен
//					if ($oManager->isNeedRedirectToSubdomain() && $oManager->getHttpHost() != $oManager->getSubdomainHost()) {
//
//
//						LocalRedirect($oManager->getSubdomainUrl());
//					}
				}
			}
		}
	}