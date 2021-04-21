<?
	use \Bitrix\Main\Loader as Loader;
	use \Bitrix\Main\Localization\Loc as Loc;


	global $APPLICATION;

	Loc::loadMessages(__FILE__);
	Loader::includeModule('bxmaker.geoip');
	$bSale = (Loader::includeModule('sale') ? true : false);

	$MODULE_ID  = 'bxmaker.geoip';
	$PERMISSION = $APPLICATION->GetGroupRight($MODULE_ID);

	$oManager = \Bxmaker\GeoIP\Manager::getInstance();
	$app      = \Bitrix\Main\Application::getInstance();
	$req      = $app->getContext()->getRequest();

	if ($PERMISSION != "W") {
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
		die();
	}

	$app = \Bitrix\Main\Application::getInstance();
	$req = $app->getContext()->getRequest();

	if ($req->isPost() && check_bitrix_sessid('sessid') && $req->getPost('method') && $req->getPost('method') == 'searchLocation') {

		$arJson = array(
			'error'    => array(),
			'response' => array()
		);

		do {

			$city = $oManager->restoreEncoding($req->getPost('city'));
			if (\Bitrix\Main\Text\BinaryString::getLength(trim($city)) <= 2) {
				$arJson['error'] = array(
					'error_code' => 'invalid_city',
					'error_msg'  => GetMessage($MODULE_ID . '.AJAX.INVALID_CITY'),
					'error_more' => array()
				);
				break;
			}

			$arLoc = $oManager->searchLocation($city);
			if (count($arLoc)) {
				$arLocTmp = array();
				foreach ($arLoc as $item) {
					$arLocTmp[] = $item;
				}

				$arJson['response'] = array(
					'items' => $arLocTmp,
					'count' => count($arLocTmp),
					'msg'   => ''
				);
			}
			else {
				$arJson['response'] = array(
					'items' => array(),
					'count' => 0,
					'msg'   => GetMessage($MODULE_ID . '.AJAX.SEARCH_LOCATION_EMPTY'),
				);
			}

		} while (false);

		$APPLICATION->RestartBuffer();

		$oManager->showJson($arJson);
	}


	// получаем массив сайтов
	$arDeliveryArray = array();


	$arSite = array();
	$db     = \Bitrix\Main\SiteTable::getList(
		array(
			'filter' => array('ACTIVE' => 'Y'),
			'order'  => array('SORT' => 'ASC')
		)
	);

	while ($site = $db->fetch()) {

		$arSite[$site['LID']] = '[' . $site['LID'] . '] ' . $site['NAME'];
	}

	$arSiteLang = array(
		'REFERENCE_ID' => array(),
		'REFERENCE'    => array()
	);
	$rsLang     = \CLanguage::GetList($by = "def", $order = "desc", Array());
	while ($arLang = $rsLang->Fetch()) {
		$arSiteLang['REFERENCE_ID'][] = $arLang['LID'];
		$arSiteLang['REFERENCE'][]    = '['.$arLang['LID'] .'] '.$arLang['NAME'];
	}


	////////////////////////
	// параметры по сайтам
	foreach ($arSite as $sid => $sname) {

		$arOptionCurrent = array(
			'SID'         => $sid,
			'NAME'        => GetMessage('AP_EDIT_TAB.SITE', array('#SITE#' => $sname)),
			'DESCRIPTION' => GetMessage('AP_EDIT_TAB.SITE_DESCRIPTION', array('#SITE#' => $sname)),
			'OPTIONS'     => array()
		);


		$arOptionCurrent['OPTIONS'][] = array(
			'GROUP'         => 'MAIN',
			'CODE_NAME'     => GetMessage('AP_OPTION.DEBUG'),
			'CODE'          => 'DEBUG',
			'TYPE'          => 'CHECKBOX',
			'DEFAULT_VALUE' => 'N',
		);

		$arOptionCurrent['OPTIONS'][] = array(
			'GROUP'         => 'LOCATION',
			'CODE_NAME'     => GetMessage('AP_OPTION.DEFAULT_CITY'),
			'CODE'          => 'DEFAULT_CITY',
			'TYPE'          => 'STRING',
			'DEFAULT_VALUE' => '',
		);

		$arOptionCurrent['OPTIONS'][] = array(
			'GROUP'     => 'LOCATION',
			'CODE_NAME' => GetMessage('AP_OPTION.LOCATION_LANG'),
			'CODE'      => 'LOCATION_LANG',
			'TYPE'      => 'LIST',
			'VALUES'    => $arSiteLang
		);

		$arOptionCurrent['OPTIONS'][] = array(
			'GROUP'         => 'LOCATION',
			'CODE_NAME'     => GetMessage('AP_OPTION.DEFAULT_CITY_ID'),
			'CODE'          => 'DEFAULT_CITY_ID',
			'TYPE'          => 'STRING',
			'DEFAULT_VALUE' => 0,
		);


		//YANDEX -----

		$arOptionCurrent['OPTIONS'][] = array(
			'GROUP'         => 'LOCATION',
			'CODE_NAME'     => GetMessage('AP_OPTION.USE_YANDEX'),
			'CODE'          => 'USE_YANDEX',
			'TYPE'          => 'CHECKBOX',
			'DEFAULT_VALUE' => 'Y',
		);


		$arOptionCurrent['OPTIONS'][] = array(
			'GROUP'         => 'SEARCH',
			'CODE_NAME'     => GetMessage('AP_OPTION.USE_YANDEX_SEARCH'),
			'CODE'          => 'USE_YANDEX_SEARCH',
			'TYPE'          => 'CHECKBOX',
			'DEFAULT_VALUE' => 'N',
		);

		$arOptionCurrent['OPTIONS'][] = array(
			'GROUP'         => 'SEARCH',
			'CODE_NAME'     => GetMessage('AP_OPTION.YANDEX_SEARCH_SKIP_WORDS'),
			'CODE'          => 'YANDEX_SEARCH_SKIP_WORDS',
			'TYPE'          => 'STRING',
			'DEFAULT_VALUE' => GetMessage('AP_OPTION.YANDEX_SEARCH_SKIP_WORDS.DEFAULT'),
		);


		$arOptions[] = $arOptionCurrent;
	}


	//////////////////////////////////////////////////////////////////////////////
	// СОХРАНЕНИЕ
	if ($PERMISSION == "W") {
		$oOption = new \Bitrix\Main\Config\Option();

		if (($apply || $save) && check_bitrix_sessid() && $req->isPost()) {
			foreach ($arOptions as $arOption) {

				foreach ($arOption['OPTIONS'] as $arItem) {
					switch ($arItem['TYPE']) {
						case 'TEXT':
						case 'STRING': {

							$oOption->set($MODULE_ID, $arItem['CODE'], ($req->getPost($arOption['SID'] . $arItem['CODE']) ? trim($req->getPost($arOption['SID'] . $arItem['CODE'])) : ''), $arOption['SID']);
						}
							break;
						case 'CHECKBOX': {
							$oOption->set($MODULE_ID, $arItem['CODE'], ($req->getPost($arOption['SID'] . $arItem['CODE']) && $req->getPost($arOption['SID'] . $arItem['CODE']) == 'Y' ? 'Y' : 'N'), $arOption['SID']);
						}
							break;
						case 'LIST': {
							$oOption->set(
								$MODULE_ID,
								$arItem['CODE'],
								($req->getPost($arOption['SID'] . $arItem['CODE']) && in_array($req->getPost($arOption['SID'] . $arItem['CODE']), $arItem['VALUES']['REFERENCE_ID'])
									? $req->getPost($arOption['SID'] . $arItem['CODE'])
									: ''),
								$arOption['SID']);
						}
							break;
						case 'MULTY_LIST': {

							$strMultyVal = '';
							if ($req->getPost($arOption['SID'] . $arItem['CODE']) && is_array($req->getPost($arOption['SID'] . $arItem['CODE']))) {
								$ar = array_diff(array_intersect($req->getPost($arOption['SID'] . $arItem['CODE']), $arItem['VALUES']['REFERENCE_ID']), array('', ' '));
								if (count($ar)) {
									$strMultyVal = implode('|', $ar);
								}
							}

							$oOption->set(
								$MODULE_ID,
								$arItem['CODE'],
								$strMultyVal,
								$arOption['SID']);
						}
							break;
					}
				}
			}


			//сброс кэша
			if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {
				$GLOBALS['CACHE_MANAGER']->ClearByTag('bxmaker_geoip_city');
				$GLOBALS['CACHE_MANAGER']->ClearByTag('bxmaker_geoip_city_line');
				$GLOBALS['CACHE_MANAGER']->ClearByTag('bxmaker_geoip_message');
				$GLOBALS['CACHE_MANAGER']->ClearByTag('bxmaker_geoip_delivery');
			}

		}
	}
	////////////////////////////////////////////////////////////////////////////

	$oManager->addAdminPageCssJs();

	// TABS
	$tabs = array();
	foreach ($arOptions as $k => $arOption) {
		$tabs[] = array(
			'DIV'   => $arOption['KEY'] . $k,
			'TAB'   => $arOption['NAME'],
			'ICON'  => '',
			'TITLE' => (isset($arOption['DESCRIPTION']) ? $arOption['DESCRIPTION'] : $arOption['NAME'])
		);
	}

	$tab = new CAdminTabControl('options_tabs', $tabs);

	$oManager->showDemoMessage();

	$tab->Begin();
?>

<?


?>

<form class="bxmaker__geoip__admin__options" method="post"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<?= LANGUAGE_ID ?>&amp;mid_menu=<?= $mid_menu ?>"><?= bitrix_sessid_post(); ?>
	<?
		$oOption = new \Bitrix\Main\Config\Option();

		$i = 0;

		// проходим по блокам параметров
		foreach ($arOptions as $k => $arOption) {
			// новая влкадка
			$tab->BeginNextTab();


			$group = '';
			$i++;

			/*if ($i >= 2) {
				?>
				<tr>
					<td colspan="2"
						style="padding:15px; border:1px solid #00b058; background: rgba(0, 221, 98, 0.25);"><?= GetMessage('AP_OPTION.GROUP.TEMPLATE_TYPE_LIST_DESCRIPTION'); ?></td>
				</tr>
			<?
			}*/

			// параметры блока
			foreach ($arOption['OPTIONS'] as $arItem) {

				// Главный заголовок блока
				if (isset($arItem['GROUP']) && $arItem['GROUP'] != $group) {
					$group = $arItem['GROUP'];
					?>
                    <tr class="heading">
                        <td colspan="3"><?= GetMessage('AP_OPTION.GROUP.' . $arItem['GROUP']); ?></td>
                    </tr>

					<?
				}
				?>

				<?
				// Подзаголовок
				if (isset($arItem['GROUP_NAME'])) {
					?>
                    <tr class="heading">
                        <td colspan="3" style="font-size: 0.9em;  background: #fff;"><?= $arItem['GROUP_NAME']; ?></td>
                    </tr>
					<?
				}
                elseif ($arItem['CODE'] == 'DEFAULT_CITY') {
					?>
                    <tr>
                        <td colspan="3">
							<?= (isset($arItem['CODE_NAME']) ? $arItem['CODE_NAME'] : GetMessage('AP_OPTION.' . $arItem['CODE'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="bxmaker__geoip__admin__options-city js-bxmaker__geoip__admin__options-city">
                            <input type="text" name="<?= $arOption['SID'] . $arItem['CODE']; ?>" autocomplete="off"
                                   value="<?= $oOption->get($MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arOption['SID']); ?>"/>
                            <input type="hidden" name="<?= $arOption['SID'] . 'DEFAULT_CITY_ID'; ?>" value="<?= $oOption->get($MODULE_ID, 'DEFAULT_CITY_ID', 0, $arOption['SID']); ?>"/>

                            <div class="bxmaker__geoip__admin__options-city-options js-bxmaker__geoip__admin__options-city-options "></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
							<?= GetMessage('AP_OPTION.' . $arItem['CODE'] . '.HELP'); ?>
                        </td>
                    </tr>
					<?
				}
                elseif ($arItem['CODE'] == 'DEFAULT_CITY_ID') {
					//
				}
				else {
					?>

                    <tr>
                        <td class="first" style="width:30%;"><?= (isset($arItem['CODE_NAME']) ? $arItem['CODE_NAME'] : GetMessage('AP_OPTION.' . $arItem['CODE'])); ?></td>
                        <td><?
								switch ($arItem['TYPE']) {
									case 'TEXT': {
										echo '<textarea  cols="40" rows="8" name="' . $arOption['SID'] . $arItem['CODE'] . '">' . $oOption->get($MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arOption['SID']) . '</textarea>';
										break;
									}
									case 'STRING': {
										echo InputType('text', $arOption['SID'] . $arItem['CODE'], $oOption->get($MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arOption['SID']), '');
										break;
									}
									case 'CHECKBOX': {
										echo InputType('checkbox', $arOption['SID'] . $arItem['CODE'], 'Y', array($oOption->get($MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arOption['SID'])));
										break;
									}
									case 'LIST': {
										echo SelectBoxFromArray($arOption['SID'] . $arItem['CODE'], $arItem['VALUES'], $oOption->get($MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arOption['SID']));
										break;
									}
									case 'MULTY_LIST': {
										$arMSVals = explode('|', $oOption->get($MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arOption['SID']));
										echo SelectBoxMFromArray(
											$arOption['SID'] . $arItem['CODE'] . '[]',
											$arItem['VALUES'],
											$arMSVals,
											'', '', 12
										);
										break;
									}
								}
								//ShowJSHint(GetMessage('AP_OPTION.' . str_replace($arOption['SID'], '', $arItem['CODE']) . '.HELP'));

							?></td>
                        <td class="bxmaker_edit_table_td_descr"><?= GetMessage('AP_OPTION.' . $arItem['CODE'] . '.HELP'); ?></td>
                    </tr>
					<?
				}
			}
		}

	?>

    <tr class="heading">
        <td colspan="3"><?= GetMessage('AP_OPTION.SETTING_INFO'); ?></td>
    </tr>
    <tr class="">
        <td colspan="3" style="">
			<? //= GetMessage('AP_OPTION.SETTING_INFO_TEXT'); ?>
        </td>
    </tr>


	<?


		$tab->Buttons(array("disabled" => false));

		$tab->End();
	?>
</form>