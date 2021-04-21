<?

	class CBXmaker_GeoIP_Manager
	{


		static private $instance = null;

		private $moduleID = 'bxmaker.geoip';
		private $bDemo = null;
		private $bDemoExpired = null;

		private $bDebug = false; //режим отладки
		private $bUseLocalBase = false; // использовать локальную базу местоположений


		private $oOption = null;
		private $oLocationTable = null;


		private $lang = 'ru';
		private $location = 0;
		private $location_code = 0;
		private $zip = 0;
		private $city = null;
		private $yandex = 0;

		private $baseDomain = null;
		private $host = null;
		private $subdomainCodeNew = '';


		private function __construct()
		{
			//
		}

		private function __clone()
		{
			//
		}

		protected function init()
		{

			$this->oOption       = new \Bitrix\Main\Config\Option();
			$this->bDebug        = ($this->getParam('DEBUG', 'N') == 'Y');
			$this->bUseLocalBase = true;
			$this->lang          = $this->getParam('LOCATION_LANG', LANGUAGE_ID);


			if (\Bitrix\Main\Loader::includeModule('sale')) {
				$this->bUseLocalBase  = false;
				$this->oLocationTable = new \Bitrix\Sale\Location\LocationTable();
			}
			else {
				$this->lang = ($this->lang != 'ru' ? 'en' : 'ru');
			}

		}

		/**
		 * –ежим отладки
		 * @return bool
		 */
		public function isDebug()
		{
			return $this->bDebug;
		}

		/**
		 * »спользовать локальную базу местоположений
		 * @return bool
		 */
		public function isUsedLocalBase()
		{
			return $this->bUseLocalBase;
		}


		/**
		 * ¬озвращает параметры модул€ (по умолчанию дл€ текущего сайта)
		 *
		 * @param        $name
		 * @param string $default_value
		 * @param null   $siteId
		 *
		 * @return string
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		public function getParam($name, $default_value = '', $siteId = null)
		{
			return $this->oOption->get($this->moduleID, $name, $default_value, (!is_null($siteId) ? $siteId : $this->getCurrentSiteId()));
		}


		/**
		 * ѕроверка демо режима
		 * @return bool
		 */
		final public function isDemo()
		{
			if (is_null($this->bDemo)) {
				$this->_checkDemo();
			}
			return $this->bDemo;
		}

		/**
		 * ѕроверка не истекло ли врем€ демо режима
		 * @return bool
		 */
		final public function isExpired()
		{
			if (is_null($this->bDemoExpired)) {
				$this->_checkDemo();
			}
			return $this->bDemoExpired;
		}

		/**
		 * »нициализаци€ параметров работы модул€
		 */
		final private function _checkDemo()
		{
			$module = new CModule();
			if ($module->IncludeModuleEx('bxmaker.geoip') == constant('MODU' . 'LE' . '_NO' . 'T_F' . 'OUND')) {
				$this->bDemo        = false;
				$this->bDemoExpired = false;
			}
			elseif ($module->IncludeModuleEx('bxmaker.geoip') == constant('MODU' . 'LE' . '_' . 'D' . 'E' . 'M' . 'O')) {
				$this->bDemo        = true;
				$this->bDemoExpired = false;
			}
			elseif ($module->IncludeModuleEx('bxmaker.geoip') == constant('MODU' . 'LE_D' . 'EMO_E' . 'XPI' . 'RED')) {
				$this->bDemo        = true;
				$this->bDemoExpired = true;
			}
		}


		/**
		 * ”становка выбоанного города, все зависимости будут проставлены автоматически
		 *
		 * @param $locationID
		 *
		 * @return bool
		 * @throws \Bitrix\Main\ArgumentException
		 */
		public function selectLocation($locationID)
		{
			$locationID = intval($locationID);

			if (!$locationID) {
				return false;
			}

			if ($this->isUsedLocalBase()) {
				$dbrLocation = \Bxmaker\GeoIP\Location\CityTable::getList(array(
					'select' => array('*', 'C_' => 'COUNTRY.*', 'R_' => 'REGION.*'),
					'filter' => array(
						'ID' => $locationID
					),
					'limit'  => 1
				));
				if ($arLocation = $dbrLocation->Fetch()) {

					$this->setZip('000000');
					$this->setLocation($arLocation['ID']);
					$this->setLocationCode($arLocation['ID']);
					$this->setCountryId($arLocation['COUNTRY_ID']);
					$this->setRegionId($arLocation['REGION_ID']);
					$this->setYandex(0);

					if ($this->lang == 'ru') {
						$this->setCountry($arLocation['C_NAME']);
						$this->setCity($arLocation['NAME']);
						$this->setRegion($arLocation['R_NAME']);
						$this->setArea($arLocation['AREA']);
					}
					else {
						$this->setCountry($arLocation['C_NAME_EN']);
						$this->setCity($arLocation['NAME_EN']);
						$this->setRegion($arLocation['R_NAME_EN']);
						$this->setArea($arLocation['AREA_EN']);
					}
					$this->saveCookie();

					return true;
				}
			}
			else {
				if (\CSaleLocation::isLocationProEnabled()) {

					$resLocation = $this->oLocationTable->getList(array(
						'select' => array('*', 'CITY_NAME' => 'NAME.NAME'),
						'filter' => array(
							'=NAME.LANGUAGE_ID' => $this->lang,
							'ID'                => $locationID
						),
						'limit'  => 1
					));
					if ($arLocation = $resLocation->fetch()) {

						$this->setYandex(0);
						$this->setZip('000000');
						$this->setLocation($arLocation['ID']);
						$this->setLocationCode(\CSaleLocation::getLocationCODEbyID($arLocation['ID']));
						$this->setCity($arLocation['CITY_NAME']);
						$this->setCountryId($arLocation['COUNTRY_ID']);
						$this->setRegionId($arLocation['REGION_ID']);
						$this->setLat($arLocation['LATITUDE']);
						$this->setLng($arLocation['LONGITUDE']);
						$this->setCountry('');
						$this->setRegion('');
						$this->setArea('');


						// собираем регионы
						$resParent = $this->oLocationTable->getList(array(
							'select' => array('ID', 'CODE', 'PARENT_ID', 'TYPE_ID', 'PARENT_NAME' => 'NAME.NAME'),
							'filter' => array(
								'=NAME.LANGUAGE_ID' => $this->lang,
								'<LEFT_MARGIN'      => $arLocation['LEFT_MARGIN'],
								'>=RIGHT_MARGIN'    => $arLocation['RIGHT_MARGIN'],
							),
							'order'  => array('LEFT_MARGIN' => 'ASC')
						));
						while ($arParent = $resParent->fetch()) {

							switch ($arParent['TYPE_ID']) {
								case $this->getLocationTypeID('COUNTRY'): {
									$this->setCountry($arParent['PARENT_NAME']);
									break;
								}
								case $this->getLocationTypeID('REGION'): {
									$this->setRegion($arParent['PARENT_NAME']);
									break;
								}
								case $this->getLocationTypeID('SUBREGION'): {
									$this->setArea($arParent['PARENT_NAME']);
									break;
								}
							}
						}

						$rsZipList = \CSaleLocation::GetLocationZIP($arLocation['ID']);
						if ($arZip = $rsZipList->Fetch()) {
							if (strlen($arZip["ZIP"]) > 0) {
								$this->setZip($arZip["ZIP"]);
							}
						}

						$this->saveCookie();

						return true;
					}

				}
				else {
					$resLocation = \CSaleLocation::GetList(
						array(
							"SORT"              => "ASC",
							"COUNTRY_NAME_LANG" => "ASC",
							"CITY_NAME_LANG"    => "ASC"
						),
						array(
							"LID" => $this->lang,
							"ID"  => $locationID
						),
						false,
						array('nPageSize' => 1),
						array()
					);
					if ($arLocation = $resLocation->Fetch()) {
						//ѕуть

						$this->setYandex(0);
						$this->setZip('000000');
						$this->setLocation($arLocation['ID']);
						$this->setLocationCode(\CSaleLocation::getLocationCODEbyID($arLocation['ID']));
						$this->setCity($arLocation['CITY_NAME']);
						$this->setCountryId($arLocation['COUNTRY_ID']);
						$this->setRegionId($arLocation['REGION_ID']);
						$this->setLat('');
						$this->setLng('');
						$this->setCountry($arLocation['COUNTRY_NAME']);
						$this->setRegion($arLocation['REGION_NAME']);
						$this->setArea('');

						$rsZipList = \CSaleLocation::GetLocationZIP($arLocation['ID']);
						if ($arZip = $rsZipList->Fetch()) {
							if (strlen($arZip["ZIP"]) > 0) {
								$this->setZip($arZip["ZIP"]);
							}
						}

						$this->saveCookie();

						return true;
					}
				}
			}

			return false;
		}


		/**
		 * ѕоиск местоположений по названию города
		 *
		 * @param     $city
		 * @param int $limit
		 *
		 * @return array
		 * @throws \Bitrix\Main\LoaderException
		 */
		public function searchLocation($city, $limit = 10)
		{
			$arSearch = array();

			if ($this->isExpired()) {
				return $arSearch;
			}


			if ($this->isUsedLocalBase()) {
				$filter = array();
				$order  = array();
				if ($this->lang == 'ru') {
					$filter['%=NAME'] = $city . '%';
					$order['NAME']    = 'ASC';
				}
				else {
					$filter['%=NAME_EN'] = $city . '%';
					$order['NAME_EN']    = 'ASC';
				}

				$dbrLocation = \Bxmaker\GeoIP\Location\CityTable::getList(array(
					'order'  => $order,
					'select' => array('*', 'C_' => 'COUNTRY.*', 'R_' => 'REGION.*'),
					'filter' => $filter,
					'limit'  => $limit
				));


				while ($arLocation = $dbrLocation->Fetch()) {

					$item = array(
						'location'   => $arLocation['ID'],
						'location_code'   => $arLocation['ID'],
						'city'       => '',
						'city_id'    => $arLocation['ID'],
						'country'    => '',
						'country_id' => $arLocation['COUNTRY_ID'],
						'region'     => '',
						'region_id'  => $arLocation['REGION_ID'],
						'area'       => '',
						'zip'        => '000000',
						'yandex'     => 0
					);


					if ($this->lang == 'ru') {
						$item['city']    = $arLocation['NAME'];
						$item['country'] = $arLocation['C_NAME'];
						$item['region']  = $arLocation['R_NAME'];
						$item['area']    = $arLocation['AREA'];
					}
					else {
						$item['city']    = $arLocation['NAME_EN'];
						$item['country'] = $arLocation['C_NAME_EN'];
						$item['region']  = $arLocation['R_NAME_EN'];
						$item['area']    = $arLocation['AREA_EN'];
					}

					$arSearch[] = $item;
				}
			}
			else {

				if (\CSaleLocation::isLocationProEnabled()) {

					$res = $this->oLocationTable->getList(array(
						'select' => array('*', 'CITY_NAME' => 'NAME.NAME'),
						'filter' => array(
							'=NAME.LANGUAGE_ID' => $this->lang,
							'%=NAME.NAME'       => $city . '%',
							'TYPE.CODE' => array('CITY', 'VILLAGE')
							//'!CITY_ID'          => false
						),
						'order'  => array('NAME.NAME' => 'ASC'),
						'limit'  => $limit
					));
					while ($arLocation = $res->fetch()) {


						$item = array(
							'location'   => $arLocation['ID'],
							'location_code'   => \CSaleLocation::getLocationCODEbyID($arLocation['ID']),
							'city'       => $arLocation['CITY_NAME'],
							'city_id'    => $arLocation['ID'],
							'country'    => '',
							'country_id' => $arLocation['COUNTRY_ID'],
							'region'     => '',
							'region_id'  => $arLocation['REGION_ID'],
							'area'       => '',
							'zip'        => '000000',
							'yandex'     => 0
						);


						// собираем регионы
						$resParent = $this->oLocationTable->getList(array(
							'select' => array('ID', 'CODE', 'PARENT_ID', 'TYPE_ID', 'PARENT_NAME' => 'NAME.NAME'),
							'filter' => array(
								'=NAME.LANGUAGE_ID' => $this->lang,
								'<LEFT_MARGIN'      => $arLocation['LEFT_MARGIN'],
								'>=RIGHT_MARGIN'    => $arLocation['RIGHT_MARGIN'],
							),
							'order'  => array('LEFT_MARGIN' => 'ASC')
						));
						while ($arParent = $resParent->fetch()) {

							switch ($arParent['TYPE_ID']) {
								case $this->getLocationTypeID('COUNTRY'): {
									$item['country'] = $arParent['PARENT_NAME'];
									break;
								}
								case $this->getLocationTypeID('REGION'): {
									$item['region'] = $arParent['PARENT_NAME'];
									break;
								}
								case $this->getLocationTypeID('CITY'): {
									$item['region'] = $arParent['PARENT_NAME'];
									break;
								}
								case $this->getLocationTypeID('SUBREGION'): {
									$item['area'] = $arParent['PARENT_NAME'];
									break;
								}

							}
						}

						$rsZipList = \CSaleLocation::GetLocationZIP($arLocation['ID']);
						if ($arZip = $rsZipList->Fetch()) {
							if (strlen($arZip["ZIP"]) > 0) {
								$item['zip'] = $arZip["ZIP"];
							}
						}

						$arSearch[] = $item;
					}
				}
				else {

					$dbLocation = \CSaleLocation::GetList(
						array(
							"COUNTRY_NAME_LANG" => "ASC",
							"CITY_NAME_LANG"    => "ASC"
						),
						array(
							"LID"        => $this->lang,
							"%CITY_NAME" => $city,
						),
						false,
						array('nPageSize' => $limit),
						array()
					);
					while ($arLocation = $dbLocation->Fetch()) {

						$item = array(
							'location'   => $arLocation['ID'],
                            'location_code'   => \CSaleLocation::getLocationCODEbyID($arLocation['ID']),
                            'city'       => $arLocation['CITY_NAME'],
							'city_id'    => $arLocation['ID'],
							'country'    => $arLocation['COUNTRY_NAME'],
							'country_id' => $arLocation['COUNTRY_ID'],
							'region'     => (string)$arLocation['REGION_NAME'],
							'region_id'  => $arLocation['REGION_ID'],
							'area'       => '',
							'zip'        => '000000',
							'yandex'     => 0
						);

						$rsZipList = \CSaleLocation::GetLocationZIP($arLocation['ID']);
						if ($arZip = $rsZipList->Fetch()) {
							if (strlen($arZip["ZIP"]) > 0) {
								$item['sip'] = $arZip["ZIP"];
							}
						}

						$arSearch[] = $item;
					}
				}
			}

			return $arSearch;
		}

		/**
		 * ¬озвращает список местополложений по ID  местоположени€
		 *
		 * @param array $arLocationId
		 */
		public function getLocationByID(array $arLocationId, $bUpperCase = false)
		{
			$arSearch = array();

			if ($this->isExpired() || empty($arLocationId)) {
				return $arSearch;
			}

			if ($this->isUsedLocalBase()) {
				$filter = array(
					'ID' => $arLocationId
				);
				$order  = array();
				if ($this->lang == 'ru') {
					$order['NAME'] = 'ASC';
				}
				else {
					$order['NAME_EN'] = 'ASC';
				}

				$dbrLocation = \Bxmaker\GeoIP\Location\CityTable::getList(array(
					'order'  => $order,
					'select' => array('*', 'C_' => 'COUNTRY.*', 'R_' => 'REGION.*'),
					'filter' => $filter
				));


				while ($arLocation = $dbrLocation->Fetch()) {

					$item = array(
						'location'   => $arLocation['ID'],
						'location_code'   => $arLocation['ID'],
						'city'       => '',
						'city_id'    => $arLocation['ID'],
						'country'    => '',
						'country_id' => $arLocation['COUNTRY_ID'],
						'region'     => '',
						'region_id'  => $arLocation['REGION_ID'],
						'area'       => '',
						'zip'        => '000000',
						'yandex'     => 0
					);


					if ($this->lang == 'ru') {
						$item['city']    = $arLocation['NAME'];
						$item['country'] = $arLocation['C_NAME'];
						$item['region']  = $arLocation['R_NAME'];
						$item['area']    = $arLocation['AREA'];
					}
					else {
						$item['city']    = $arLocation['NAME_EN'];
						$item['country'] = $arLocation['C_NAME_EN'];
						$item['region']  = $arLocation['R_NAME_EN'];
						$item['area']    = $arLocation['AREA_EN'];
					}

					$arSearch[$item['location']] = $item;
				}
			}
			else {

				if (\CSaleLocation::isLocationProEnabled()) {
					$res = $this->oLocationTable->getList(array(
						'select' => array('*', 'CITY_NAME' => 'NAME.NAME'),
						'filter' => array(
							'=NAME.LANGUAGE_ID' => $this->lang,
							'ID'                => $arLocationId
						),
						'order'  => array('NAME.NAME' => 'ASC'),
					));
					while ($arLocation = $res->fetch()) {

						$item = array(
							'location'   => $arLocation['ID'],
                            'location_code'   => \CSaleLocation::getLocationCODEbyID($arLocation['ID']),
                            'city'       => $arLocation['CITY_NAME'],
							'city_id'    => $arLocation['ID'],
							'country'    => '',
							'country_id' => $arLocation['COUNTRY_ID'],
							'region'     => '',
							'region_id'  => $arLocation['REGION_ID'],
							'area'       => '',
							'zip'        => '000000',
							'yandex'     => 0
						);


						// собираем регионы
						$resParent = $this->oLocationTable->getList(array(
							'select' => array('ID', 'CODE', 'PARENT_ID', 'TYPE_ID', 'PARENT_NAME' => 'NAME.NAME'),
							'filter' => array(
								'=NAME.LANGUAGE_ID' => $this->lang,
								'<LEFT_MARGIN'      => $arLocation['LEFT_MARGIN'],
								'>=RIGHT_MARGIN'    => $arLocation['RIGHT_MARGIN'],
							),
							'order'  => array('LEFT_MARGIN' => 'ASC')
						));
						while ($arParent = $resParent->fetch()) {

							switch ($arParent['TYPE_ID']) {
								case $this->getLocationTypeID('COUNTRY'): {
									$item['country'] = $arParent['PARENT_NAME'];
									break;
								}
								case $this->getLocationTypeID('REGION'): {
									$item['region'] = $arParent['PARENT_NAME'];
									break;
								}
								case $this->getLocationTypeID('CITY'): {
									$item['region'] = $arParent['PARENT_NAME'];
									break;
								}
								case $this->getLocationTypeID('SUBREGION'): {
									$item['area'] = $arParent['PARENT_NAME'];
									break;
								}

							}
						}

						$rsZipList = \CSaleLocation::GetLocationZIP($arLocation['ID']);
						if ($arZip = $rsZipList->Fetch()) {
							if (strlen($arZip["ZIP"]) > 0) {
								$item['zip'] = $arZip["ZIP"];
							}
						}

						$arSearch[$item['location']] = $item;
					}
				}
				else {

					$dbLocation = \CSaleLocation::GetList(
						array(
							"COUNTRY_NAME_LANG" => "ASC",
							"CITY_NAME_LANG"    => "ASC"
						),
						array(
							"LID" => $this->lang,
							"ID"  => $arLocationId,
						),
						false,
						false,
						array()
					);
					while ($arLocation = $dbLocation->Fetch()) {

						$item = array(
							'location'   => $arLocation['ID'],
                            'location_code'   => \CSaleLocation::getLocationCODEbyID($arLocation['ID']),
                            'city'       => $arLocation['CITY_NAME'],
							'city_id'    => $arLocation['ID'],
							'country'    => $arLocation['COUNTRY_NAME'],
							'country_id' => $arLocation['COUNTRY_ID'],
							'region'     => (string)$arLocation['REGION_NAME'],
							'region_id'  => $arLocation['REGION_ID'],
							'area'       => '',
							'zip'        => '000000',
							'yandex'     => 0
						);

						$rsZipList = \CSaleLocation::GetLocationZIP($arLocation['ID']);
						if ($arZip = $rsZipList->Fetch()) {
							if (strlen($arZip["ZIP"]) > 0) {
								$item['sip'] = $arZip["ZIP"];
							}
						}

						$arSearch[$item['location']] = $item;
					}
				}
			}

			if ($bUpperCase) {
				return $this->getToUpperCase($arSearch);
			}

			return $arSearch;
		}


		final public function setCity($value)
		{
			if ($this->isExpired()) {
				$value = $this->getMsg('DEMO_MODE_IS_EXPIRED');
			}

			$this->city                        = $value;
			$_SESSION['BXMAKER.GEOIP']['CITY'] = $value;
		}

		final public function setLocation($value)
		{
			if ($this->isExpired()) {
				$value = 0;
			}

			$this->location                        = $value;
			$_SESSION['BXMAKER.GEOIP']['LOCATION'] = $value;
		}

		final public function setLocationCode($value = '')
		{
			if ($this->isExpired()) {
				$value = '';
			}

			$this->location_code                        = $value;
			$_SESSION['BXMAKER.GEOIP']['LOCATION_CODE'] = $value;
		}



		final public function setZip($value)
		{
			if ($this->isExpired()) {
				$value = 0;
			}

			$this->zip                        = $value;
			$_SESSION['BXMAKER.GEOIP']['ZIP'] = $value;
		}


		final public function setYandex($value)
		{
			if ($this->isExpired()) {
				$value = 0;
			}

			$this->yandex                        = $value;
			$_SESSION['BXMAKER.GEOIP']['YANDEX'] = $value;
		}


		final public function getLocation()
		{
			return $this->location;
		}

		final public function getLocationCode()
		{
			return $this->location_code;
		}

		final public function getYandex()
		{
			return $this->yandex;
		}

		final public function getCity()
		{
			return $this->city;
		}

		final public function getZip()
		{
			return $this->zip;
		}


		public function getDeliveryItems($arFields)
		{
			$arReturnItems = array();


			if (!\Bitrix\Main\Loader::includeModule('sale') || !\Bitrix\Main\Loader::includeModule('catalog')) {
				return $arReturnItems;
			}

			$order = \Bitrix\Sale\Order::create($arFields['SITE_ID']);
			$order->isStartField();
			$order->setPersonTypeId($arFields['PERSONAL_TYPE']);

			// LOCATION SET
			$props = $order->getPropertyCollection();
			$loc   = $props->getDeliveryLocation();
			$arLoc = \Bitrix\Sale\Location\LocationTable::getById($arFields['LOCATION'])->fetch();
			if (!empty($arLoc) && is_object($loc)) {
				$loc->setField('VALUE', $arLoc['CODE']);
			}

			//basket
			$basket         = \Bitrix\Sale\Basket::create($arFields['SITE_ID']);
			$settableFields = array_flip(\Bitrix\Sale\BasketItemBase::getSettableFields());


			$basketItem = $basket->createItem('catalog', $arFields['PRODUCT_ID']);
			$basketItem->setField('PRODUCT_PROVIDER_CLASS', 'CCatalogProductProvider');
			$basketItem->setField('QUANTITY', $arFields['QUANTIYT']);
			$order->setBasket($basket);


			//shipment
			$shipmentCollection     = $order->getShipmentCollection();
			$shipment               = $shipmentCollection->createItem();
			$shipmentItemCollection = $shipment->getShipmentItemCollection();
			$shipment->setField('CURRENCY', $order->getCurrency());


			/** @var \Bitrix\Sale\BasketItem $item */

			foreach ($order->getBasket() as $item) {
				/** @var \Bitrix\Sale\ShipmentItem $shipmentItem */

				$shipmentItem = $shipmentItemCollection->createItem($item);
				$shipmentItem->setQuantity($item->getQuantity());
			}

			//delivery
			$arDeliveryServiceAll = \Bitrix\Sale\Delivery\Services\Manager::getRestrictedObjectsList($shipment);


			if (!empty($arDeliveryServiceAll)) {
				foreach ($arDeliveryServiceAll as $deliveryObj) {
					$arDelivery                   = array();
					$arDelivery['ID']             = $deliveryObj->getId();
					$arDelivery['NAME']           = $deliveryObj->getName();
					$arDelivery['PARENT_NAME']    = ($deliveryObj->isProfile() ? $deliveryObj->getParentService()->getName() : '');
					$arDelivery['DESCRIPTION']    = $deliveryObj->getDescription();
					$arDelivery['FIELD_NAME']     = 'DELIVERY_ID';
					$arDelivery["CURRENCY"]       = $order->getCurrency();
					$arDelivery['SORT']           = $deliveryObj->getSort();
					$arDelivery['EXTRA_SERVICES'] = $deliveryObj->getExtraServices()->getItems();
					//$arDelivery['STORE'] = Bitrix\Sale\Delivery\ExtraServices\Manager::getStoresList($deliveryObj->getId());

					if (intval($deliveryObj->getLogotip()) > 0) {
						$arDelivery["LOGOTIP"] = CFile::GetFileArray($deliveryObj->getLogotip());
					}

					if (!empty($arDelivery['STORE']) && is_array($arDelivery['STORE'])) {
						foreach ($arDelivery['STORE'] as $val) {
							$arStoreId[$val] = $val;
						}
					}

					$calcResult = $deliveryObj->calculate($shipment);


					$arDelivery['SORT']           = $deliveryObj->getSort();
					$arDelivery["PRICE_FORMATED"] = SaleFormatCurrency(round($calcResult->getPrice()), $deliveryObj->getCurrency());
					$arDelivery["CURRENCY"]       = $deliveryObj->getCurrency();
					$arDelivery["PRICE"]          = $calcResult->getPrice();
					$arDelivery["DELIVERY_PRICE"] = roundEx($calcResult->getPrice(), SALE_VALUE_PRECISION);
					$arDelivery["PERIOD_TEXT"]    = $calcResult->getPeriodDescription();
					$arDelivery["PERIOD_FROM"]    = intval($calcResult->getPeriodFrom());
					$arDelivery["PERIOD_TO"]      = intval($calcResult->getPeriodTo());
					$arDelivery["IS_PROFILE"]     = $deliveryObj->isProfile();


					$arReturnItems[$deliveryObj->getId()] = $arDelivery;
				}
			}

			return $arReturnItems;
		}


		public function isNeedRedirectToSubdomain()
		{
			$siteId  = $this->getCurrentSiteId();
			$oDomain = new \BXmaker\GeoIp\DomainTable();

			if ($this->getParam('SUBDOMAIN_ON', 'N') == 'Y') {
				$subDomainCode = $this->getCurrentSubdomainCode();



				// провер€ем существование поддомена
				if (!!$subDomainCode && $subDomainCode != 'www') {
					if (!$oDomain->getList(array(
						'filter' => array(
							'SID'   => $siteId,
							'VALUE' => $subDomainCode
						)
					))->fetch()
					) {
						$this->subdomainCodeNew = '';
						return true;
					}
				}


				if(\Bitrix\Main\Loader::includeModule('sale'))
				{
					$arLocationGroupId = array('-1');
					$dbGroup = \CSaleLocationGroup::GetLocationList(array("LOCATION_ID" => $this->getLocation()));
					while ($arGroup = $dbGroup->Fetch()) {

						$arLocationGroupId[] = $arGroup['LOCATION_GROUP_ID'];
					}

					$dbrDomain = $oDomain->getList(array(
						'filter' =>  array(
							'LOGIC' => 'OR',
							array(
								// 'LOGIC' => 'AND'
								'=SID'         => $siteId,
								'=LOCATION_ID' => $this->getLocation()
							),
							array(
								'=SID'         => $siteId,
								'=GROUP' =>  $arLocationGroupId
							)
						),
						'limit' => 1
					));
				}
				else
				{
					$dbrDomain = $oDomain->getList(array(
						'filter' => array(
							'SID'         => $siteId,
							'LOCATION_ID' => $this->getLocation()
						),
						'limit' => 1
					));
				}

				// у текущего местоположени€ есть поддомен
				if ($arDomain = $dbrDomain->fetch()) {
					$value = \Bitrix\Main\Text\BinaryString::changeCaseToLower($arDomain['VALUE']);


					if ($value != $subDomainCode) {
						$this->subdomainCodeNew = $value;
						return true;
					}

				}
				//  у текущего местоположени€ нет поддомена
				else
				{
					//если мы на поддомене, то перекидываем на основной домен
					if(!!$this->getCurrentSubdomainCode() && $this->getCurrentSubdomainCode() != 'www')
					{
						$this->subdomainCodeNew = '';
						return true;
					}
				}
			}

			$this->subdomainCodeNew = $this->getCurrentSubdomainCode();

			return false;
		}

		public function getSubdomainHost() {
			return (!!$this->subdomainCodeNew ? $this->subdomainCodeNew . '.' : '') . $this->getBaseDomain();
		}

		public function getSubdomainUrl()
		{
			$app    = \Bitrix\Main\Application::getInstance();
			$server = $app->getContext()->getServer();

			$url = ($server->getServerPort() == 443 ? 'https' : 'http') . '://';
			$url .= (!!$this->subdomainCodeNew ? $this->subdomainCodeNew . '.' : '') . $this->getBaseDomain();

			if ($server->getServerPort() != 80 && $server->getServerPort() != 443) {
				$url .= ":" . $server->getServerPort();
			}

			$url .= $server->get('REQUEST_URI');

			return $url;
		}

		public function getHttpHost()
		{
			if (is_null($this->host)) {
				$this->host = \Bitrix\Main\Text\BinaryString::changeCaseToLower(preg_replace('/(:[\d]+)$/', '', \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getHttpHost()));
			}
			return $this->host;
		}

		/**
		 * ¬озвращает базовый домен текущего сайта
		 * @return mixed|null|string
		 */
		public function getBaseDomain()
		{
			if (is_null($this->baseDomain)) {
				$currentHost = $this->getHttpHost();
				if ($this->getParam('SUBDOMAIN_ON', 'N') == 'Y') {
					$this->baseDomain = $currentHost;
					$arBaseDomain     = array_diff(explode(',', $this->getParam('BASE_DOMAIN', '')), array(''));

					//определ€ем текущий базовый домен
					foreach ($arBaseDomain as $domain) {
						if (\Bitrix\Main\Text\BinaryString::getPositionIgnoreCase($currentHost, $domain) !== false) {
							$this->baseDomain = \Bitrix\Main\Text\BinaryString::changeCaseToLower($domain);
							break;
						}
					}
				}
				else {
					$this->baseDomain = $currentHost;
				}
			}
			return $this->baseDomain;
		}


		/**
		 * ¬озвращает код поддомена
		 * @return string
		 */
		public function getCurrentSubdomainCode()
		{
			$currentHost = $this->getHttpHost();

			return trim(str_replace($this->getBaseDomain(), '', $currentHost), ' .');
		}

	}


?>