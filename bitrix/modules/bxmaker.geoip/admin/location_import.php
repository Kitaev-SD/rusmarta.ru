<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

	use Bitrix\Main\Localization\Loc as Loc;

	$MODULE_ID   = 'bxmaker.geoip';
	$MODULE_CODE = 'bxmaker_geoip';

	Loc::loadLanguageFile(__FILE__);

	\Bitrix\Main\Loader::includeModule($MODULE_ID);

	global $APPLICATION;

	$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);


	$oManager = \Bxmaker\GeoIP\Manager::getInstance();
	$oCountry = new \Bxmaker\GeoIP\Location\CountryTable();
	$oRegion  = new \Bxmaker\GeoIP\Location\RegionTable();
	$oCity    = new \Bxmaker\GeoIP\Location\CityTable();

	$app   = \Bitrix\Main\Application::getInstance();
	$req   = $app->getContext()->getRequest();
	$asset = \Bitrix\Main\Page\Asset::getInstance();


	$sTableID = $MODULE_ID;
	$sCurPage = $APPLICATION->GetCurPage();


	$arCountry = array(
		'REFERENCE_ID' => array(
			'RU',
			'UA',
			'KZ'
		),
		'REFERENCE'    => array(
			Loc::getMessage($MODULE_ID . '_COUNTRY_CODE.RU'),
			Loc::getMessage($MODULE_ID . '_COUNTRY_CODE.UA'),
			Loc::getMessage($MODULE_ID . '_COUNTRY_CODE.KZ')
		)
	);


	//AJAX ------------
	if ($req->isPost() && check_bitrix_sessid('sessid') && $req->getPost('method') && $req->getPost('method') == 'import_start') {


		$arJson = array(
			'error'    => array(),
			'response' => array()
		);

		do {

			if ($PREMISION_DEFINE != 'W') {
				$arJson['error'] = array(
					'code' => '',
					'msg'  => GetMessage('ACCESS_DENIED'),
					'more' => array()
				);
				break;
			}

			$arFields = array();

			// файлы локлаьный
			$countryFile = $_SERVER['DOCUMENT_ROOT'] . '/upload/bxmaker/geoip/country_file.txt';
			$cityFile    = $_SERVER['DOCUMENT_ROOT'] . '/upload/bxmaker/geoip/city_file.txt';
			$regionFile  = $_SERVER['DOCUMENT_ROOT'] . '/upload/bxmaker/geoip/region_file.txt';


			$bStop   = (!!$req->getPost('stop') && $req->getPost('stop') == 1 ? true : false); // запрос на остановку
			$step    = intval($req->getPost('step')); //импорт городов, иначе стран
			$posFile = (!!$req->getPost('pos') && intval($req->getPost('pos')) > 0 ? intval($req->getPost('pos')) : 0); // позици€ в файле
			$iCount  = (!!$req->getPost('count') ? intval($req->getPost('count')) : -1); //импорт городов, иначе стран
			$fseek   = (!!$req->getPost('fseek') ? intval($req->getPost('fseek')) : 0); //смещение файла
			$country = (!!$req->getPost('country') ? \Bitrix\Main\Text\BinaryString::changeCaseToLower(trim($req->getPost('country'))) : 'ru'); //смещение файла


			// файлы источники
			$countryFileSource = 'https://bxmaker.ru/upload/module/geoip/2.0.0/' . $country . '/country.csv';
			$regionFileSource  = 'https://bxmaker.ru/upload/module/geoip/2.0.0/' . $country . '/region.csv';
			$cityFileSource    = 'https://bxmaker.ru/upload/module/geoip/2.0.0/' . $country . '/city.csv';


			//если нужно остановить импорт
			if ($bStop) {
				//удаление файла
				@unlink($cityFile);
				@unlink($countryFile);
				@unlink($regionFile);

				$arJson['response'] = array(
					'continue' => 0,
					'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STOPED'),
				);
				break;
			}


			//загрузка, если не задано смещение
			if ($step <= 0) {
				//удаление файла
				@unlink($cityFile);
				@unlink($countryFile);

				// формирование файла
				CheckDirPath($_SERVER['DOCUMENT_ROOT'] . '/upload/bxmaker/geoip/');
				if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/bxmaker/geoip/.htaccess')) {
					file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/bxmaker/geoip/.htaccess', 'Deny from All' . PHP_EOL . 'Options -Indexes ');
				}

				try {


					// загрузка файлов
					$oHttp = new \Bitrix\Main\Web\HttpClient();
					if (!$oHttp->download($countryFileSource, $countryFile)) {
						$arJson['error'] = array(
							'code' => '',
							'msg'  => GetMessage($MODULE_ID . '.AJAX.ERROR_DOWNLOAD_COUNTRY'),
							'more' => array()
						);
						break;
					}

					if (!$oHttp->download($regionFileSource, $regionFile)) {
						$arJson['error'] = array(
							'code' => '',
							'msg'  => GetMessage($MODULE_ID . '.AJAX.ERROR_DOWNLOAD_REGION'),
							'more' => array()
						);
						break;
					}


					if (!$oHttp->download($cityFileSource, $cityFile)) {
						$arJson['error'] = array(
							'code' => '',
							'msg'  => GetMessage($MODULE_ID . '.AJAX.ERROR_DOWNLOAD_CITY'),
							'more' => array()
						);
						break;
					}


					// очищаем
					$dbrCountry = $oCountry->getList(array(
							'filter' => array(
								'CODE' => trim($country)
							)
						)
					);
					if($arCountry = $dbrCountry->fetch())
					{
						$oCity->onCountryDelete($arCountry['ID']);
						$oRegion->onCountryDelete($arCountry['ID']);
						$oCountry->delete($arCountry['ID']);
					}


					$arJson['response'] = array(
						'continue' => 1,
						'step'     => 1,
						'count'    => -1,
						'pos'      => 0,
						'fseek'    => 0,
						'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STATUS_CLEAN')
					);
					break;

				} catch (\Exception $e) {
					$arJson['error'] = array(
						'code' => $e->getCode(),
						'msg'  => $e->getMessage(),
						'more' => array()
					);
					break;
				}


			}

			// загрузка стран
            elseif ($step == 1) {

				//считаем количество строк
				if ($iCount < 0) {
					$handle = fopen($countryFile, "r");
					$iCount = 0;
					while (!feof($handle) && fgets($handle)) {
						$iCount++;
					}
					fclose($handle);


					$arJson['response'] = array(
						'continue' => 1,
						'step'     => $step,
						'count'    => $iCount,
						'pos'      => 0,
						'fseek'    => 0,
						'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STATUS_COUNTRY', array(
							'#I#'       => 0,
							'#COUNT#'   => $iCount,
							'#PERCENT#' => 0
						))
					);
					break;
				}

				// если строки посчитаны, испортируем
				$arItems = array();
				$n       = 0;
				$handle  = fopen($countryFile, "r");
				fseek($handle, $fseek);


				while ($n < 500 && !feof($handle)) {
					$n++;
					$posFile++;
					$arCountry = explode(";", $oManager->prepareFromWindows1251(fgets($handle)));

					if (count($arCountry) > 1) {
						try {
							$addCountry = $oCountry->add(array(
								'ID'      => intval($arCountry[0]),
								'NAME'    => trim($arCountry[1]),
								'NAME_EN' => trim($arCountry[2]),
								'CODE'    => trim($country)
							));

							if (!$addCountry->isSuccess()) {
								$arJson['error'] = array(
									'code' => 'error_country',
									'msg'  => implode(', ', $addCountry->getErrorMessages()),
									'more' => array()
								);
								fclose($handle);
								break;
							}

						} catch (\Exception $e) {
							$arJson['error'] = array(
								'code' => $e->getCode(),
								'msg'  => $e->getMessage(),
								'more' => $e->getTrace()
							);
							break;
						}

					}

				}
				$bComplete = feof($handle);
				$fseek     = ftell($handle);
				fclose($handle);


				$arJson['response'] = array(
					'continue' => 1,
					'step'     => ($bComplete ? 2 : 1),
					'count'    => ($bComplete ? -1 : $iCount),
					'pos'      => ($bComplete ? 0 : $posFile),
					'fseek'    => ($bComplete ? 0 : $fseek),
					'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STATUS_COUNTRY', array(
						'#I#'       => $posFile,
						'#COUNT#'   => $iCount,
						'#PERCENT#' => round(($posFile / $iCount) * 100, 2)
					))
				);
				break;

			}
			// загрузка регионов
            elseif ($step == 2) {

				//считаем количество строк
				if ($iCount < 0) {
					$handle = fopen($regionFile, "r");
					$iCount = 0;
					while (!feof($handle) && fgets($handle)) {
						$iCount++;
					}
					fclose($handle);

					$arJson['response'] = array(
						'continue' => 1,
						'step'     => 2,
						'count'    => $iCount,
						'pos'      => 0,
						'fseek'    => 0,
						'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STATUS_REGION', array(
							'#I#'       => 0,
							'#COUNT#'   => $iCount,
							'#PERCENT#' => 0
						))
					);
					break;
				}

				// если строки посчитаны, импортируем
				$arItems = array();
				$n       = 0;
				$handle  = fopen($regionFile, "r");
				fseek($handle, $fseek);
				while ($n < 1000 && !feof($handle)) {
					$n++;
					$posFile++;
					$arRegion = explode(";", $oManager->prepareFromWindows1251(fgets($handle)));

					if (count($arRegion) > 1) {
						try {
							usleep(1000);
							$addRegion = $oRegion->add(array(
								'COUNTRY_ID' => intval($arRegion[0]),
								'ID'         => intval($arRegion[1]),
								'NAME'       => trim($arRegion[2])
							));

							if (!$addRegion->isSuccess()) {
								$arJson['error'] = array(
									'code' => 'error_region',
									'msg'  => implode(', ', $addRegion->getErrorMessages()),
									'more' => array()
								);
								fclose($handle);
								break;
							}

						} catch (\Exception $e) {
							$arJson['error'] = array(
								'code' => $e->getCode(),
								'msg'  => $e->getMessage(),
								'more' => $e->getTrace()
							);
							break;
						}
					}
				}
				$bComplete = feof($handle);
				$fseek     = ftell($handle);
				fclose($handle);

				$arJson['response'] = array(
					'continue' => 1,
					'step'     => ($bComplete ? 3 : 2),
					'count'    => ($bComplete ? -1 : $iCount),
					'pos'      =>($bComplete ? 0 : $posFile) ,
					'fseek'    => $fseek,
					'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STATUS_REGION', array(
						'#I#'       => $posFile,
						'#COUNT#'   => $iCount,
						'#PERCENT#' => round(($posFile / $iCount) * 100, 2)
					))
				);
				break;
			}
			else {

				//считаем количество строк
				if ($iCount < 0) {
					$handle = fopen($cityFile, "r");
					$iCount = 0;
					while (!feof($handle) && fgets($handle)) {
						$iCount++;
					}
					fclose($handle);

					$arJson['response'] = array(
						'continue' => 1,
						'step'     => 3,
						'count'    => $iCount,
						'pos'      => 0,
						'fseek'    => 0,
						'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STATUS_CITY', array(
							'#I#'       => 0,
							'#COUNT#'   => $iCount,
							'#PERCENT#' => 0
						))
					);
					break;
				}

				// если строки посчитаны, импортируем
				$arItems = array();
				$n       = 0;
				$handle  = fopen($cityFile, "r");
				fseek($handle, $fseek);
				while ($n < 1000 && !feof($handle)) {
					$n++;
					$posFile++;
					$arCity = explode(";", $oManager->prepareFromWindows1251(fgets($handle)));

					if (count($arCity) == 6) {
						try {
							usleep(1000);
							$addCity = $oCity->add(array(
								'COUNTRY_ID' => intval($arCity[0]),
								'REGION_ID'  => intval($arCity[1]),
								'ID'         => intval($arCity[2]),
								'NAME'       => trim($arCity[3]),
								'NAME_EN'    => trim($arCity[4]),
								'AREA'       => trim($arCity[5]),
								'AREA_EN'    => $oManager->translit($arCity[5])
							));

							if (!$addCity->isSuccess()) {
								$arJson['error'] = array(
									'code' => 'error_city',
									'msg'  => implode(', ', $addCity->getErrorMessages()),
									'more' => array()
								);
								fclose($handle);
								break;
							}

						} catch (\Exception $e) {
							$arJson['error'] = array(
								'code' => $e->getCode(),
								'msg'  => $e->getMessage(),
								'more' => $e->getTrace()
							);
							break;
						}
					}
				}
				$bComplete = feof($handle);
				$fseek     = ftell($handle);
				fclose($handle);

				$arJson['response'] = array(
					'continue' => ($bComplete ? 0 : 1),
					'step'     => 3,
					'count'    => $iCount,
					'pos'      => $posFile,
					'fseek'    => $fseek,
					'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STATUS_CITY', array(
						'#I#'       => $posFile,
						'#COUNT#'   => $iCount,
						'#PERCENT#' => round(($posFile / $iCount) * 100, 2)
					))
				);
				if (!$bComplete) {
					break;
				}
			}


			//удаление файла
			@unlink($cityFile);
			@unlink($countryFile);


			$arJson['response'] = array(
				'continue' => 0,
				'msg'      => GetMessage($MODULE_ID . '.AJAX.IMPORT_STATUS_OK'),
			);


		} while (false);

		//$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		$oManager->showJson($arJson);
	}

	if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	if ($PREMISION_DEFINE == 'W') {
		$bReadOnly = false;
	}
	else  $bReadOnly = true;


	// визуализаторы
	$fname = 'bxmaker_geoip_location_import_edit_form';


	$tab = new CAdminTabControl('edit', array(
		array(
			'DIV'   => 'edit',
			'TAB'   => GetMessage($MODULE_ID . '.TAB.EDIT'),
			'ICON'  => '',
			'TITLE' => GetMessage($MODULE_ID . '.TAB.EDIT')),
	));

	$APPLICATION->SetTitle(GetMessage($MODULE_ID . '.PAGE_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


	$oManager->showDemoMessage();
	$oManager->addAdminPageCssJs();


?>

    <form action="<? $APPLICATION->GetCurPage() ?>" method="POST" name="<?= $fname ?>" class="bxmaker_geoip_location_import_box">
		<? echo bitrix_sessid_post(); ?>

		<? $tab->Begin(); ?>
		<? $tab->BeginNextTab(); ?>

        <tr>
            <td colspan="2">
                <div class="info_box"><?= GetMessage($MODULE_ID . '.IMPORT_INFO_BOX'); ?></div>
            </td>
        </tr>


        <tr>
            <td colspan="2">
                <div class="msg_box"></div>
            </td>
        </tr>

        <tr>
            <td>
				<?= Loc::getMessage($MODULE_ID . '_COUNTRY_FIELD'); ?>
            </td>
            <td>
				<?= SelectBoxFromArray('country', $arCountry); ?>
            </td>
        </tr>

		<? $tab->EndTab(); ?>
		<? $tab->Buttons(); ?>

        <!--  нопки -->

        <div class="btn btn-start adm-btn adm-btn-save "><?= GetMessage($MODULE_ID . '.BTN_IMPORT'); ?></div>
        <div class="btn btn-stop adm-btn  "><?= GetMessage($MODULE_ID . '.BTN_STOP'); ?></div>

		<? $tab->End(); ?>
    </form>


<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>