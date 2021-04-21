<?

	namespace Bxmaker\GeoIP;

	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Loader;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;
	use Bitrix\Seo\Engine\Bitrix;

	Loc::loadMessages(__FILE__);


	Class Manager extends \CBXmaker_GeoIP_Manager
	{
		private $module_id = 'bxmaker.geoip';

		static private $instance = null;


		private $oOption = null;
		private $oIPGeoBase = null;

		private $ip = null;
		private $range = null;
		private $country = null;
		private $countryId = null;
		private $region = null;
		private $regionId = null;
		private $area = null;
		private $lat = null;
		private $lng = null;
		private $success = false;


		private $arLocationTypeID = null; // идентификаторы типов местоположений - город, страна и тп


		private function __construct($ip = null)
		{
			parent::init();

			$this->setIP($ip);

			$this->isNeedRedirectToSubdomain();

		}

		private function __clone()
		{

		}

		/**
		 * @return Manager
		 */
		final static public function getInstance()
		{
			if (is_null(self::$instance)) {
				$c              = __CLASS__;
				self::$instance = new $c();

			}
			return self::$instance;
		}

		public function isModuleSaleInstalled()
		{
			return Loader::includeModule('sale');
		}

		/**
		 * –абота с обектами в демо режиме
		 *
		 * @param $name
		 *
		 * @return null
		 */
		protected function getObj($name)
		{
			return (isset($this->$name) ? $this->$name : null);
		}


		/**
		 * ѕроверка, кодировка сайта UTF-8 или нет
		 * @return bool
		 */
		final static public function isUTF()
		{
			return (defined('BX_UTF') && BX_UTF === true);
		}

		final static public function isAdminSection()
		{
			return (defined('ADMIN_SECTION') && defined('ADMIN_SECTION') === true);
		}

		final function restoreEncoding($data)
		{
			if (self::isUTF()) return $data;
			return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'UTF-8', 'WINDOWS-1251');
		}

		final function prepareEncoding($data)
		{
			if (self::isUTF()) return $data;
			return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'WINDOWS-1251', 'UTF-8');
		}

		final function prepareFromWindows1251($data)
		{
			if (!self::isUTF()) return $data;
			return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'WINDOWS-1251', 'UTF-8');
		}

		/**
		 * ѕриводит все ключи массива к нижнему регистру
		 *
		 * @param $ar
		 *
		 * @return array
		 */
		final private function getToLowerKeys($ar)
		{
			if (is_array($ar)) {
				$ar = array_change_key_case($ar, CASE_LOWER);

				foreach ($ar as $key => &$val) {
					$val = $this->getToLowerKeys($val);
				}
				unset($val);
			}
			return $ar;
		}

		/**
		 * ѕриводит все ключи массива к верхнему регистру
		 *
		 * @param $ar
		 *
		 * @return array
		 */
		final private function getToUpperKeys($ar)
		{
			if (is_array($ar)) {
				$ar = array_change_key_case($ar, CASE_UPPER);

				foreach ($ar as $key => &$val) {
					$val = $this->getToUpperKeys($val);
				}
				unset($val);
			}
			return $ar;
		}


		/**
		 * ¬ыводит данные дл€
		 *
		 * @param $arResult
		 */
		final public function showJson($arResult)
		{
			global $APPLICATION;

			header('Content-Type: application/json');
			if (!empty($arResult['error'])) {
				echo json_encode(array(
					'error' => $this->prepareEncoding($this->getToLowerKeys($arResult['error']))
				));
			}
			else {
				echo json_encode(array(
					'response' => $this->prepareEncoding($this->getToLowerKeys($arResult['response']))
				));
			}
			die();
		}


		/**
		 * ¬озвращает текущий идентификатор сайта
		 * @return string
		 */
		public function getCurrentSiteId()
		{
			// если админка то определ€ем сайт поумолчанию или  по текущему домену
			if ($this->isAdminSection() || SITE_ID == LANGUAGE_ID) {

				if (!$this->siteID) {
					$host = Application::getInstance()->getContext()->getRequest()->getHttpHost();
					$host = preg_replace('/(:[\d]+)/', '', $host);

					//ищем по домену
					$oSite = new \CSite();
					$dbr   = $oSite->GetList($by = 'sort', $order = 'asc', array(
						'ACTIVE' => 'Y',
						'DOMAIN' => $host
					));
					if ($ar = $dbr->Fetch()) {
						$this->siteID = $ar['LID'];
					}
					else {
						// сайт поумолчанию
						$dbr = $oSite->GetList($by = 'sort', $order = 'asc', array(
							'DEFAULT' => 'Y'
						));
						if ($ar = $dbr->Fetch()) {
							$this->siteID = $ar['LID'];
						}
					}
				}
				return $this->siteID;
			}

			return SITE_ID;
		}

		/**
		 * ¬озвращает сообщение
		 *
		 * @param      $name
		 * @param null $arReplace
		 *
		 * @return mixed
		 */
		protected
		function getMsg($name, $arReplace = null)
		{
			return GetMessage($this->module_id . '.' . $name, $arReplace);
		}

		/**
		 * добавление стилей на страницу административную
		 */
		public function addAdminPageCssJs()
		{
			\CUtil::InitJSCore('jquery');

			//дл€ избранных метоположений доп библиотеки
			\Bitrix\Main\Page\Asset::getInstance()->addCss('//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css', true);
			\Bitrix\Main\Page\Asset::getInstance()->addJs('//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', true);


			$path = getLocalPath('modules/' . $this->module_id);
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/css/style.css')) {
				echo '<style type="text/css" >' . file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/css/style.css') . '</style>';
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/js/script.js')) {
				echo '<script type="text/javascript" >' . file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/js/script.js') . '</script>';
			}
		}

		public function showDemoMessage()
		{
			if ($this->isDemo()) {
				if ($this->isExpired()) {
					echo '<div class="ap-bxmaker-geoip-notice-box expired" >' . $this->getMsg('DEMO_EXPIRED_NOTICE') . '</div>';
				}
				else {

					echo '<div class="ap-bxmaker-geoip-notice-box " >' . $this->getMsg('DEMO_NOTICE') . '</div>';
				}
			}
		}


		public static function getFirstNotEmpty(array $arValue)
		{
			foreach ($arValue as $value) {
				if (\Bitrix\Main\Text\BinaryString::getLength(trim($value))) {
					return trim($value);
				}
			}
			return '';
		}

		/**
		 * ѕодготовка строки дл€ использовани€ в аттрибутах html тегов
		 *
		 * @param $text
		 *
		 * @return mixed
		 */
		public function getPreparedForHtmlAttr($text)
		{
			return preg_replace('/"/', '\"', $text);
		}


		/**
		 * «амена символов транслитом
		 *
		 * @param $str
		 *
		 * @return string
		 */
		public function translit($str)
		{
			static $search = array();


			$lang = 'ru';

			if (!isset($search[$lang])) {
				$mess       = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/js_core_translit.php", $lang, true);
				$trans_from = explode(",", $mess["TRANS_FROM"]);
				$trans_to   = explode(",", $mess["TRANS_TO"]);
				foreach ($trans_from as $i => $from) {
					$search[$lang][$from] = $trans_to[$i];
				}
			}


			$len     = strlen($str);
			$str_new = '';

			for ($i = 0; $i < $len; $i++) {
				$chr = substr($str, $i, 1);

				if (preg_match("/[a-zA-Z0-9]/" . BX_UTF_PCRE_MODIFIER, $chr)) {
					$str_new .= $chr;
				}
				else {
					if (array_key_exists($chr, $search[$lang])) {
						$str_new .= $search[$lang][$chr];
					}
					else {
						$str_new .= $chr;
					}
				}
			}

			$str_new = preg_replace('/\s+/', ' ', $str_new);

			return trim($str_new);
		}


		/**
		 * ќпределение местоположени€ по IP при инициализации или данные берем из сессии
		 *
		 * @param $ip
		 *
		 * @return bool
		 * @throws \Bitrix\Main\ArgumentException
		 * @throws \Bitrix\Main\LoaderException
		 */
		public function setIP($ip)
		{
			if (!is_null($ip)) {
				$this->ip = $ip;
			}
			else {
				$this->defineIP();
			}


			if ($this->tryRestoreFromSession()) {
				//  восстановлено
				return true;
			}
			elseif ($this->tryRestoreFromCookie()) {
				//восстановлено
				return true;
			}


			if (is_null($this->oIPGeoBase)) {
				$this->oIPGeoBase = new Manager\IPGeoBase();
			}

			//определение по базе
			$result = $this->oIPGeoBase->getRecord($this->ip);
			if (isset($result['city']) && isset($result['region'])) {

				$this->setRange($result['range']);
				$this->setCountry($result['cc']);
				$this->setCountryId(0);
				$this->setCity($result['city']);
				$this->setRegion($result['region']);
				$this->setRegionId(0);
				$this->setArea('');
				$this->setLat($result['lat']);
				$this->setLng($result['lng']);
				$this->setLocation(0);
				$this->setLocationCode();
				$this->setZip('000000');
				$this->setYandex(1);


				//если поиск города по €ндексу, то ничего не делаем
				if ($this->getParam('USE_YANDEX_SEARCH', 'N') == 'Y') {
					$this->saveCookie();
				}
				// иначе ищем соответствие
				else {

					if ($result['city'] == $result['region']) $result['region'] = '';

					$arFoundLocation = $this->searchLocation($result['city']);
					foreach ($arFoundLocation as $item) {

						$bCityEqual   = (\Bitrix\Main\Text\BinaryString::changeCaseToLower($item['city']) == \Bitrix\Main\Text\BinaryString::changeCaseToLower($result['city']));
						$bRegionEqual = (\Bitrix\Main\Text\BinaryString::changeCaseToLower($item['region']) == \Bitrix\Main\Text\BinaryString::changeCaseToLower($result['region']));

						if ($bCityEqual && $bRegionEqual) {
							$this->selectLocation($item['location']);
							return true;
						}
					}
				}
			}


			$this->setDefaultLocation();


			return true;
		}


		/**
		 * ѕопытка восстановлени€ данных из сессии
		 * @return bool
		 */
		public function tryRestoreFromSession()
		{
			// если есть данные в сессии
			if (isset($_SESSION['BXMAKER.GEOIP']['CITY'])) {

				//дополнительно провер€ем cookie
				if(isset($_COOKIE[$this->getCookiePrefix(true) . 'city']) && $_SESSION['BXMAKER.GEOIP']['CITY']!= $this->restoreEncoding($_COOKIE[$this->getCookiePrefix(true) . 'city']))
				{
					return false;
				}

				$this->setLocation($_SESSION['BXMAKER.GEOIP']['LOCATION']);
				$this->setLocationCode($_SESSION['BXMAKER.GEOIP']['LOCATION_CODE']);
				$this->setCity($_SESSION['BXMAKER.GEOIP']['CITY']);
				$this->setCountry($_SESSION['BXMAKER.GEOIP']['COUNTRY']);
				$this->setCountryId($_SESSION['BXMAKER.GEOIP']['COUNTRY_ID']);
				$this->setRegion($_SESSION['BXMAKER.GEOIP']['REGION']);
				$this->setRegionId($_SESSION['BXMAKER.GEOIP']['REGION_ID']);
				$this->setArea($_SESSION['BXMAKER.GEOIP']['AREA']);
				$this->setZip($_SESSION['BXMAKER.GEOIP']['ZIP']);
				$this->setRange($_SESSION['BXMAKER.GEOIP']['RANGE']);
				$this->setYandex($_SESSION['BXMAKER.GEOIP']['YANDEX']);
				$this->setLat($_SESSION['BXMAKER.GEOIP']['LAT']);
				$this->setLng($_SESSION['BXMAKER.GEOIP']['LNG']);

				if (!isset($_COOKIE[$this->getCookiePrefix(true) . 'city']) || \Bitrix\Main\Text\BinaryString::getLength(trim($_COOKIE[$this->getCookiePrefix(true) . 'location'])) <= 0) {
					$this->saveCookie();
				}

				return true;
			}

			return false;
		}

		/**
		 * ѕопытка восстановлени€ данных из cookie
		 * @return bool
		 */
		public function tryRestoreFromCookie()
		{
			//проверка существовани€ всех полей
			$arFields = array(
				'city', 'city_id', 'location', 'location_code', 'country', 'country_id', 'region', 'region_id', 'area', 'lat', 'lng', 'zip', 'yandex'// 'range'
			);

			//			foreach ($arFields as $field) {
			//				if (!isset($_COOKIE[$this->getCookiePrefix(true) . '' . $field])) {
			//					return false;
			//				}
			//			}


			if (!isset($_COOKIE[$this->getCookiePrefix(true) . 'city'])) {
				return false;
			}

			// если есть данные в куках, и не используетс€ поиск из €ндекса
			if (intval($_COOKIE[$this->getCookiePrefix(true) . 'location']) > 0 && $this->getParam('USE_YANDEX_SEARCH', 'N') != 'Y') {
				$this->selectLocation(intval($_COOKIE[$this->getCookiePrefix(true) . 'location']));
				return true;
			}


			$country  = $this->restoreEncoding(trim($_COOKIE[$this->getCookiePrefix(true) . 'country']));
			$city     = $this->restoreEncoding(trim($_COOKIE[$this->getCookiePrefix(true) . 'city']));
			$region   = $this->restoreEncoding(trim($_COOKIE[$this->getCookiePrefix(true) . 'region']));
			$area     = $this->restoreEncoding(trim($_COOKIE[$this->getCookiePrefix(true) . 'area']));
			$lng      = floatval($this->restoreEncoding(trim($_COOKIE[$this->getCookiePrefix(true) . 'lng'])));
			$lat      = floatval($this->restoreEncoding(trim($_COOKIE[$this->getCookiePrefix(true) . 'lat'])));
			$location = intval($this->restoreEncoding(trim($_COOKIE[$this->getCookiePrefix(true) . 'location'])));
			$location_code = intval($this->restoreEncoding(trim($_COOKIE[$this->getCookiePrefix(true) . 'location_code'])));

			$country = strip_tags($country);
			$city    = strip_tags($city);
			$region  = strip_tags($region);
			$area    = strip_tags($area);


			$this->setYandex(1);
			$this->setLocation($location);
			$this->setLocationCode($location_code);
			$this->setZip('000000');
			$this->setCountryId(0);
			$this->setRegionId(0);
			$this->setLat($lat);
			$this->setLng($lng);
			$this->setCountry($country);
			$this->setCity($city);
			$this->setRegion($region);
			$this->setArea($area);
			$this->setRange(0);

			return true;

		}

		/**
		 * ”становка местоположени€ по умолчанию, город беретс€ из настроек модул€
		 * @throws \Bitrix\Main\ArgumentException
		 * @throws \Bitrix\Main\LoaderException
		 */
		public function setDefaultLocation()
		{
			// «начени€ поумолчанию
			$defaultCityID = $this->getParam('DEFAULT_CITY_ID', 0);

			$this->setYandex(1);
			$this->setLocation(0);
			$this->setLocationCode();
			$this->setZip('000000');
			$this->setCountryId(0);
			$this->setRegionId(0);
			$this->setLat(0);
			$this->setLng(0);
			$this->setCountry('');
			$this->setCity($this->getParam('DEFAULT_CITY', $this->getMsg('DEFAULT_CITY')));
			$this->setRegion('');
			$this->setArea('');
			$this->setRange(0);

			$this->selectLocation($defaultCityID);

			return true;
		}


		/**
		 * ”становка кук с даными о местоположении
		 */
		public function saveCookie()
		{


			if (!!$this->getCurrentSubdomainCode()) {

				//setcookie(session_name(), session_id(), time() - 1000,"/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());

				setcookie($this->getCookiePrefix() . 'location', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'location_code', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'city', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'city_id', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'country', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'country_id', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'region', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'region_id', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'area', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'zip', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'range', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'lat', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'lng', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'yandex', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());

				setcookie($this->getCookiePrefix() . 'yandex_location_defined', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());
				setcookie($this->getCookiePrefix() . 'yandex_location_defined_check', '', time() - 60 * 60 * 24, "/", $this->getCurrentSubdomainCode() . '.' . $this->getBaseDomain());

			}

			//setcookie(session_name(), session_id(), null,"/", $this->getBaseDomain());

			setcookie($this->getCookiePrefix() . 'location', $this->prepareEncoding($this->getLocation()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'location_code', $this->prepareEncoding($this->getLocationCode()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'city', $this->prepareEncoding($this->getCity()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'city_id', $this->prepareEncoding($this->getLocation()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'country', $this->prepareEncoding($this->getCountry()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'country_id', $this->prepareEncoding($this->getCountryId()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'region', $this->prepareEncoding($this->getRegion()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'region_id', $this->prepareEncoding($this->getRegionId()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'area', $this->prepareEncoding($this->getArea()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'zip', $this->prepareEncoding($this->getZip()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'range', $this->prepareEncoding($this->getRange()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'lat', $this->prepareEncoding($this->getLat()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'lng', $this->prepareEncoding($this->getLng()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
			setcookie($this->getCookiePrefix() . 'yandex', $this->prepareEncoding($this->getYandex()), time() + 60 * 60 * 24 * 365, "/", $this->getBaseDomain());
		}

		public function getCookiePrefix($bCookieCheck = false)
		{
			$prefix = 'bxmaker.geoip.2.3.8_' . $this->getCurrentSiteId() . '_';

			if ($bCookieCheck) {
				return str_replace('.', '_', $prefix);
			}
			else {
				return $prefix;
			}
		}


		/**
		 * ќпределение IP исход€ из заголовков
		 * @return bool
		 */
		private function defineIP()
		{

			$arHeader = array(
				'CLIENT_IP',
				'FORWARDED',
				'FORWARDED_FOR',
				'FORWARDED_FOR_IP',
				'HTTP_CLIENT_IP',
				'HTTP_FORWARDED',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED_FOR_IP',
				'HTTP_PC_REMOTE_ADDR',
				'HTTP_PROXY_CONNECTION',
				'HTTP_VIA',
				'HTTP_X_FORWARDED',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED_FOR_IP',
				'HTTP_X_IMFORWARDS',
				'HTTP_XROXY_CONNECTION',
				'VIA',
				'X_FORWARDED',
				'X_FORWARDED_FOR'
			);
			$regEx    = "/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/";

			foreach ($arHeader as $header) {
				if (isset($_SERVER[$header])) {

					if (stristr(',', $_SERVER[$header]) !== false) {
						//(z.B.: X-Forwarded-For: client1, proxy1, proxy2)
						$ip = trim(array_shift(explode(',', $_SERVER[$header])));

						// if IPv4 address remove port if exists
						if (preg_match($regEx, $ip) && ($pos = stripos($ip, ':')) !== false) {
							$ip = substr($ip, 0, $pos);
						}

						$this->ip = $ip;
						return true;
					}

					$this->ip = $_SERVER[$header];
					return true;
				}
			}

			$this->ip = $_SERVER['REMOTE_ADDR'];

			if ($this->ip == '127.0.0.1') {
				$this->ip = '62.105.128.0';//москва
			}

			return true;
		}


		public function getIP()
		{
			return $this->ip;
		}

		public function getRange()
		{
			return $this->range;
		}

		public function getCountry()
		{
			return $this->country;
		}

		public function getCountryId()
		{
			return $this->countryId;
		}


		public function getRegion()
		{
			return $this->region;
		}

		public function getRegionId()
		{
			return $this->regionId;
		}

		public function getArea()
		{
			return $this->area;
		}

		public function getLat()
		{
			return $this->lat;
		}

		public function getLng()
		{
			return $this->lng;
		}

		/**
		 * ћассив всех данных о местоположении пользовател€
		 * @return array
		 */
		public function getFullData()
		{
			return array(
				'location'   => $this->getLocation(),
				'location_code'   => $this->getLocationCode(),
				'city'       => $this->getCity(),
				'city_id'    => $this->getLocation(),
				'country'    => $this->getCountry(),
				'country_id' => $this->getCountryId(),
				'region'     => $this->getRegion(),
				'region_id'  => $this->getRegionId(),
				'area'       => $this->getArea(),
				'zip'        => $this->getZip(),
				'range'      => $this->getRange(),
				'lng'        => $this->getLng(),
				'lat'        => $this->getLat(),
				'yandex'     => $this->getYandex()
			);

		}


		public function isSuccess()
		{
			return ($this->success === true);
		}


		public function setCountry($value)
		{
			$this->country                        = $value;
			$_SESSION['BXMAKER.GEOIP']['COUNTRY'] = $value;
		}

		public function setCountryId($value)
		{
			$this->countryId                         = $value;
			$_SESSION['BXMAKER.GEOIP']['COUNTRY_ID'] = $value;
		}

		public function setRange($value)
		{
			$this->range                        = $value;
			$_SESSION['BXMAKER.GEOIP']['RANGE'] = $value;
		}

		public function setRegion($value)
		{
			$this->region                        = $value;
			$_SESSION['BXMAKER.GEOIP']['REGION'] = $value;
		}

		public function setRegionId($value)
		{
			$this->regionId                         = $value;
			$_SESSION['BXMAKER.GEOIP']['REGION_ID'] = $value;
		}


		public function setArea($value)
		{
			$this->area                        = $value;
			$_SESSION['BXMAKER.GEOIP']['AREA'] = $value;
		}

		public function setLat($value)
		{
			$this->lat                        = $value;
			$_SESSION['BXMAKER.GEOIP']['LAT'] = $value;
		}

		public function setLng($value)
		{
			$this->lng                        = $value;
			$_SESSION['BXMAKER.GEOIP']['LNG'] = $value;
		}


		/**
		 * ¬озвращает идентификатор типа местоположени€ по коду
		 *
		 * @param $type - COUNTRY, COUNTRY_AREA, REGION, SUBREGION, CITY, VILLAGE, STREET
		 *
		 * @return int
		 * @throws \Bitrix\Main\ArgumentException
		 */
		public function getLocationTypeID($type)
		{
			if ($this->isUsedLocalBase()) return 0;

			if (is_null($this->arLocationTypeID)) {
				$this->arLocationTypeID = array();
				$dbrType                = \Bitrix\Sale\Location\TypeTable::getList(array());
				while ($arType = $dbrType->fetch()) {
					$this->arLocationTypeID[$arType['CODE']] = $arType['ID'];
				}
			}
			if (isset($this->arLocationTypeID[$type])) {
				return $this->arLocationTypeID[$type];
			}
			return 0;
		}


		/**
		 * ‘ќрматирование вывода даты, например 5 марта
		 *
		 * @return string
		 */
		public function getDeliveryPeriodFormat($daysFrom, $daysTo, $empty = '-')
		{
			if (intval($daysTo) <= 0) return $empty;

			$str     = '';
			$int     = $daysTo;
			$arWords = array(
				$this->getMsg('DAY1'),
				$this->getMsg('DAY22'),
				$this->getMsg('DAY5'),
			);


			//ќставл€ем две последние цифры от $num
			$int = substr((string)$int, -2);

			$name = $arWords[2];

			if ($int > 10 && $int < 15) {
				$name = $arWords[2];
			}
			else {
				$int = substr((string)$int, -1);

				if ($int == 1) {
					$name = $arWords[0];
				}
				else {
					if ($int >= 2 && $int < 5) {
						$name = $arWords[1];
					}
				}
			}

			return $daysFrom . '-' . $daysTo . ' ' . $name;
		}

		public function getDeliveryPriceFormat($price)
		{
			if (Loader::IncludeModule("currency")) {
				return \CurrencyFormat($price, \CCurrency::GetBaseCurrency());
			}

			return number_format($price, 0, '.', ' ') . $this->getMsg('CURRENCY_SHORT');
		}


	}