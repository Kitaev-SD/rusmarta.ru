<?

	namespace Bxmaker\GeoIP\Manager;


	Class IPGeoBase
	{
		protected $baseDirNameRelative = ''; //директория с базой данных
		protected $baseDirName = ''; //директория с базой данных
		protected $baseCityFileName = ''; //файл с городами
		protected $baseCIDRFileName = ''; //

		protected $fhandleCIDR = null;
		protected $fhandleCity = null;
		protected $fSizeCIDR = 0;
		protected $fsizeCity = 0;
		protected $oManager = null;


		public function __construct()
		{
			//директория с файлами
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/bxmaker.ipgeo/ipgeobase/')) {
				$this->setBaseDirName('/local/php_interface/bxmaker.ipgeo/ipgeobase/');
			}
			else {
				CheckDirPath($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/bxmaker.ipgeo/ipgeobase/');
				$this->setBaseDirName('/bitrix/php_interface/bxmaker.ipgeo/ipgeobase/');
			}

			// наименования файлов
			$this->setBaseCityFileName('cities.txt');
			$this->setBaseCIDRFileName('cidr_optim.txt');
		}


		public function setBaseDirName($relativeDir)
		{
			$this->baseDirName         = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . trim($relativeDir, '/' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
			$this->baseDirNameRelative = DIRECTORY_SEPARATOR . trim($relativeDir, '/' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		}

		public function setBaseCityFileName($FileNameCity)
		{
			$this->baseCityFileName = trim($FileNameCity, '/' . DIRECTORY_SEPARATOR);
		}

		public function setBaseCIDRFileName($FileNameCIDR)
		{
			$this->baseCIDRFileName = trim($FileNameCIDR, '/' . DIRECTORY_SEPARATOR);
		}


		/*
		 * @brief Получение информации о городе по индексу
		 * @param idx индекс города
		 * @return массив или false, если не найдено
		 */
		private function getCityByIdx($idx)
		{
			rewind($this->fhandleCity);
			while (!feof($this->fhandleCity)) {
				$str      = fgets($this->fhandleCity);
				$arRecord = explode("\t", trim($str));
				if ($arRecord[0] == $idx) {
					return array('city'     => $arRecord[1],
								 'region'   => $arRecord[2],
								 'area' => $arRecord[3],
								 'lat'      => $arRecord[4],
								 'lng'      => $arRecord[5]);
				}
			}
			return false;
		}

		/*
		 * @brief Получение гео-информации по IP
		 * @param ip IPv4-адрес
		 * @return массив или false, если не найдено
		 */
		final public function getRecord($ip)
		{

			if (!is_resource($this->fhandleCIDR)) {
				if (file_exists($this->baseDirName . $this->baseCIDRFileName)) {
					$this->fhandleCIDR = fopen($this->baseDirName . $this->baseCIDRFileName, 'r');
					$this->fSizeCIDR   = filesize($this->baseDirName . $this->baseCIDRFileName);
				}
			}

			if (!is_resource($this->fhandleCity)) {
				if (file_exists($this->baseDirName . $this->baseCityFileName)) {
					$this->fhandleCity = fopen($this->baseDirName . $this->baseCityFileName, 'r');
					$this->fsizeCity   = filesize($this->baseDirName . $this->baseCityFileName);

				}
			}


			$ip = sprintf('%u', ip2long($ip));

			if (!is_resource($this->fhandleCIDR)) return false;
			if (!is_resource($this->fhandleCity)) return false;

			rewind($this->fhandleCIDR);
			$rad = floor($this->fSizeCIDR / 2);
			$pos = $rad;
			while (fseek($this->fhandleCIDR, $pos, SEEK_SET) != -1) {
				if ($rad) {
					$str = fgets($this->fhandleCIDR);
				}
				else {
					rewind($this->fhandleCIDR);
				}

				$str = fgets($this->fhandleCIDR);

				if (!$str) {
					return false;
				}

				$arRecord = explode("\t", trim($str));

				if (count($arRecord) < 5) return false;

				$rad = floor($rad / 2);
				if (!$rad && ($ip < $arRecord[0] || $ip > $arRecord[1])) {
					return false;
				}

				if ($ip < $arRecord[0]) {
					$pos -= $rad;
				}
				elseif ($ip > $arRecord[1]) {
					$pos += $rad;
				}
				else {
					$result = array('range' => $arRecord[2], 'cc' => $arRecord[3]);

					if ($arRecord[4] != '-' && $cityResult = $this->getCityByIdx($arRecord[4])) {
						$result += $cityResult;
					}

					return $result;
				}
			}
			return false;
		}


		public function updateBase()
		{
			global $APPLICATION;

			//сброс
			file_put_contents($this->baseDirName . 'log.txt', '');


			$oHTTP    = new \Bitrix\Main\Web\HttpClient();
			$oManager = \Bxmaker\GeoIP\Manager::getInstance();


			$this->log('Старт агента обновления базы IPGeo');

			$this->log('Создание временной папки');
			CheckDirPath($this->baseDirName . 'tmp/');

			$this->log('Скачиваем архив последней версии базы');
			if ($result = $oHTTP->download('http://ipgeobase.ru/files/db/Main/geo_files.tar.gz', $this->baseDirName . 'tmp/base.tar.gz')) {
				$oTar = \CBXArchive::GetArchive($this->baseDirName . 'tmp/base.tar.gz');


				$this->log('Распаковываем архив');
				if ($resultTar = $oTar->Unpack($this->baseDirName . 'tmp/')) {
					$this->log('Архив распакован');
					$this->log('Проверяем наличие нужных нам файлов');

					// cidr_optim.txt
					if (file_exists($this->baseDirName . 'tmp/cidr_optim.txt')) {

						$this->log('Файл cidr_optim.txt найден.');
						$this->log('Начинаем перекодировку данных cidr_optim.txt ', 1, 1);

						if ($f = fopen($this->baseDirName . 'tmp/cidr_optim.txt', 'rb')) {

							$this->log('Файл данных открыт', 1, 1);
							$this->log('Открываем временный файл', 1, 1);

							if ($fd = fopen($this->baseDirName . 'tmp/cidr_optim_tmp.txt', 'w')) {

								$this->log('Открыт временный файл', 1, 1);

								$i = 0;
								while (!feof($f)) {

									fputs($fd, ($oManager::isUTF() ? $APPLICATION->ConvertCharset(fgets($f), 'WINDOWS-1251', 'UTF-8') : fgets($f)));
									$i++;
								}

								$this->log('Обработано ' . $i . ' строк', 1, 1);
								$this->log('Закрываем временный файл', 1, 1);
								fclose($fd);

							}
							else {
								$this->log('Не удалось открыть временный файл', 0, 1);
							}

							$this->log('Закрываем файл данных', 1, 1);
							fclose($f);
						}
						else {
							$this->log('Не удалось открыть файл', 0, 1);
						}


						$this->log('Копируем временный файл вместо старого', 1, 1);


						if (file_put_contents($this->baseDirName . 'cidr_optim.txt', file_get_contents($this->baseDirName . 'tmp/cidr_optim_tmp.txt'))) {
							$this->log('Файл успешно скопирован', 1, 1);
						}
						else {
							$this->log('Не удалось скопирвоать файл', 0, 1);
						}


					}
					else {
						$this->log('Файл cidr_optim.txt не найден.', 0);
					}


					// cities.txt
					if (file_exists($this->baseDirName . 'tmp/cities.txt')) {

						$this->log('Файл cities.txt найден.');
						$this->log('Начинаем перекодировку данных cities.txt ', 1, 1);

						if ($f = fopen($this->baseDirName . 'tmp/cities.txt', 'rb')) {

							$this->log('Файл данных открыт', 1, 1);
							$this->log('Открываем временный файл', 1, 1);

							if ($fd = fopen($this->baseDirName . 'tmp/cities_tmp.txt', 'w')) {

								$this->log('Открыт временный файл', 1, 1);
								$i = 0;

								while (!feof($f)) {
									fputs($fd, ($oManager::isUTF() ? $APPLICATION->ConvertCharset(fgets($f), 'WINDOWS-1251', 'UTF-8') : fgets($f)));
									$i++;
								}
								$this->log('Обработано ' . $i . ' строк', 1, 1);
								$this->log('Закрываем временный файл', 1, 1);

								fclose($fd);
							}
							else {
								$this->log('Не удалось открыть временный файл', 0, 1);
							}

							$this->log('Закрываем файл данных', 1, 1);
							fclose($f);
						}
						else {
							$this->log('Не удалось открыть файл', 0, 1);
						}


						$this->log('Копируем временный файл вместо старого', 1, 1);

						if (file_put_contents($this->baseDirName . 'cities.txt', file_get_contents($this->baseDirName . 'tmp/cities_tmp.txt'))) {
							$this->log('Файл успешно скопирован', 1, 1);
						}
						else {
							$this->log('Не удалось скопирвоать файл', 0, 1);
						}


					}
					else {
						$this->log('Файл cities.txt не найден.', 0);
					}


				}
				else {
					$this->log('Не удалось распаковать архив', 0);
				}

				unset($oTar);

			}
			else {
				$this->log('Не удалось скачать файл', 0);
			}


			$this->log('Очищаем временную папку');

			if (DeleteDirFilesEx($this->baseDirNameRelative . 'tmp/')) {
				$this->log('Папка успешно очищена и удалена');
			}
			else {
				$this->log('Не удалось очистить временную папку', 0);
			}


			$this->log('Выполнение агента завершено');
			unset($oLog, $oHTTP, $result);
		}

		private function log($data, $isSuccess = 1, $iOffsetTab = 0)
		{
			switch ($isSuccess) {
				case 0: { // ошибка
					file_put_contents($this->baseDirName . 'log.txt', date('d-m-Y H:i:s') . str_repeat("\t", $iOffsetTab + 1) . '/// ERROR /// ' . (is_scalar($data) ? $data : var_export($data, true)) . ' ' . "\r\n", FILE_APPEND);
				}
					break;
				default: {
				file_put_contents($this->baseDirName . 'log.txt', date('d-m-Y H:i:s') . str_repeat("\t", $iOffsetTab + 1) . (is_scalar($data) ? $data : var_export($data, true)) . "\r\n", FILE_APPEND);
				}
				break;
			}
		}


	}

