<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php"); ?>
<?
	//error_reporting(E_ALL);

	IncludeModuleLangFile(__FILE__);

	$BXMAKER_MODULE_ID = 'bxmaker.geoip';


	\Bitrix\Main\Loader::IncludeModule($BXMAKER_MODULE_ID);

	// ПРОВЕРКА ПРАВ ДОСТУПА
	$PREMISION_DEFINE = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

	if ($PREMISION_DEFINE != 'W') {
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
		die();
	}

	$oManager     = \Bxmaker\GeoIP\Manager::getInstance();
	$oDomainTable = new \Bxmaker\GeoIP\DomainTable();
	$app          = \Bitrix\Main\Application::getInstance();
	$req          = $app->getContext()->getRequest();
	$oOption      = new \Bitrix\Main\Config\Option();


	// сайты
	$arSite          = array();
	$arSiteReference = array(
		'REFERENCE'    => array(),
		'REFERENCE_ID' => array()
	);
	$dbr             = \CSite::GetList($by = 'sort', $order = 'asc');
	while ($ar = $dbr->Fetch()) {
		$arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
	}


	// визуализаторы
	$fname = 'bxmaker__geoip__domain__edit';


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
					'error_msg'  => GetMessage($BXMAKER_MODULE_ID . '.AJAX.INVALID_CITY'),
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
					'count' => count($arLocTmp)
				);
			}
			else {
				$arJson['response'] = array(
					'items' => array(),
					'count' => 0,
					'msg'   => GetMessage($BXMAKER_MODULE_ID . '.AJAX.SEARCH_LOCATION_EMPTY'),
				);
			}

		} while (false);

		$APPLICATION->RestartBuffer();

		$oManager->showJson($arJson);
	}


	// группы местоположений
	$arLocGroups = array(
		'REFERENCE'    => array(''),
		'REFERENCE_ID' => array('')
	);
	if (\Bitrix\Main\Loader::includeModule('sale')) {
		$oLocGroup   = new CSaleLocationGroup();
		$dbrLocGroup = $oLocGroup->GetList(Array("NAME" => "ASC"), array(), LANGUAGE_ID);
		while ($arLocGroup = $dbrLocGroup->Fetch()) {
			$arLocGroups['REFERENCE'][]    = $arLocGroup['NAME'];
			$arLocGroups['REFERENCE_ID'][] = $arLocGroup['ID'];
		}
	}



	$bPostRepeat = false;
	$arError     = array();

	// СОХРАНЕНИЕ. ПРИМЕНЕНИЕ
	if ((!!$req->get('apply') || !!$req->get('save')) && check_bitrix_sessid() && $req->isPost()) {

		$arFields = array();

		foreach ($arSite as $siteID => $siteName) {

			$bSubdomainOn = $req->getPost('SUBDOMAIN_ON');
			$oOption->set($BXMAKER_MODULE_ID, 'SUBDOMAIN_ON', ($bSubdomainOn[$siteID] == 'Y' ? 'Y' : 'N'), $siteID);

			$baseDomain = $req->getPost('BASE_DOMAIN');
			$oOption->set($BXMAKER_MODULE_ID, 'BASE_DOMAIN', preg_replace('/\s+/', '', (!!$baseDomain[$siteID] ? $baseDomain[$siteID] : $oManager->getHttpHost())), $siteID);

			$arSubDomain = (array)$req->getPost('SUBDOMAIN');
			if (isset($arSubDomain[$siteID])) {

				// получаем имеющиеся
				$arLocationDomainID = array();
				$dbrCurrent         = $oDomainTable->getList(array(
					'filter' => array(
						'SID' => $siteID,
						'GROUP' => null
					)
				));
				while ($arCurrent = $dbrCurrent->Fetch()) {
					$arLocationDomainID[$arCurrent['LOCATION_ID']] = $arCurrent['ID'];
				}

				// собираем новые
				$arLocationDomainNew = array();
				foreach ($arSubDomain[$siteID] as $key => $domain) {
					if (preg_match('/^l([\d]+)$/', $key, $match) && !!trim($domain) && !in_array($arLocationDomainNew, trim($domain))) {
						$arLocationDomainNew[$match[1]] = trim($domain);
					}
				}

				// удаляем те которых нет
				$arDiffLocationID = array_diff(array_keys($arLocationDomainID), array_keys($arLocationDomainNew));
				if (count($arDiffLocationID)) {
					foreach ($arDiffLocationID as $locationId) {
						$oDomainTable->delete(intval($arLocationDomainID[$locationId]));
						unset($arLocationDomainID[$locationId]);
					}
				}

				// обновление имеющихся
				$arExistLocationID = array_intersect(array_keys($arLocationDomainNew), array_keys($arLocationDomainID));
				if (count($arExistLocationID)) {
					foreach ($arExistLocationID as $locationId) {
						$oDomainTable->update(intval($arLocationDomainID[$locationId]), array('VALUE' => $arLocationDomainNew[$locationId]));
						unset($arLocationDomainNew[$locationId]);
					}
				}


				// добавление новых
				if (count($arLocationDomainNew)) {
					foreach ($arLocationDomainNew as $locationId => $domain) {
						$oDomainTable->add(array(
							'SID'         => $siteID,
							'LOCATION_ID' => $locationId,
							'VALUE'       => $arLocationDomainNew[$locationId]
						));
					}
				}

			}

			$arSubDomainGroup = (array)$req->getPost('SUBDOMAIN_GROUP');
			if (isset($arSubDomainGroup[$siteID])) {

				// получаем имеющиеся
				$arLocationDomainID = array();
				$dbrCurrent         = $oDomainTable->getList(array(
					'filter' => array(
						'SID' => $siteID,
						'!GROUP' => null
					)
				));
				while ($arCurrent = $dbrCurrent->Fetch()) {
					$arLocationDomainID[$arCurrent['GROUP']] = $arCurrent['ID'];
				}


				// собираем новые
				$arLocationDomainNew = array();
				foreach ($arSubDomainGroup[$siteID] as $key => $domain) {
					if (preg_match('/^g([\d]+)$/', $key, $match) && !!trim($domain) && !in_array($arLocationDomainNew, trim($domain))) {
						if(intval($match[1]))
                        {
                            $arLocationDomainNew[intval($match[1])] = trim($domain);
						}
					}
				}

				// удаляем те которых нет
				$arDiffLocationID = array_diff(array_keys($arLocationDomainID), array_keys($arLocationDomainNew));
				if (count($arDiffLocationID)) {
					foreach ($arDiffLocationID as $locationId) {
						$oDomainTable->delete(intval($arLocationDomainID[$locationId]));
						unset($arLocationDomainID[$locationId]);
					}
				}

				// обновление имеющихся
				$arExistLocationID = array_intersect(array_keys($arLocationDomainNew), array_keys($arLocationDomainID));
				if (count($arExistLocationID)) {
					foreach ($arExistLocationID as $locationId) {
						$oDomainTable->update(intval($arLocationDomainID[$locationId]), array('VALUE' => $arLocationDomainNew[$locationId]));
						unset($arLocationDomainNew[$locationId]);
					}
				}


				// добавление новых
				if (count($arLocationDomainNew)) {
					foreach ($arLocationDomainNew as $locationId => $domain) {
						$oDomainTable->add(array(
							'SID'         => $siteID,
							'GROUP' => $locationId,
							'VALUE'       => $arLocationDomainNew[$locationId]
						));
					}
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

		LocalRedirect('/bitrix/admin/' . $BXMAKER_MODULE_ID . '_domain_edit.php?lang=' . LANG);
	}


	// РЕДАКТИРОВАНИЕ
	$arResult['ITEMS'] = array();

	if (!$apply && !$save && !$_POST) {
		// местоположения
		$arLocationID = array();
		$dbr          = $oDomainTable->getList(array(
			'select' => array('*'),
			'order'  => array('ID' => 'ASC')
		));
		while ($ar = $dbr->Fetch()) {

			$ar['NAME'] = '';

			$arResult['ITEMS'][$ar['SID']][$ar['ID']] = $ar;

			$arLocationID[$ar['LOCATION_ID']] = '';
		}
		if (count($arLocationID)) {
			$ar = $oManager->getLocationByID(array_keys($arLocationID));

			foreach ($ar as $location) {
				$arLocationID[$location['location']] = $location['city'];
			}
		}


		foreach ($arResult['ITEMS'] as $sid => &$arItems) {
			foreach ($arItems as &$item) {
				$item['NAME'] = $arLocationID[$item['LOCATION_ID']];
			}
		}

		unset($arItems, $item);
	}

	$tab = new CAdminTabControl('edit', array(
		array(
			'DIV'   => 'edit',
			'TAB'   => GetMessage($BXMAKER_MODULE_ID . '.TAB.EDIT'),
			'ICON'  => '',
			'TITLE' => GetMessage($BXMAKER_MODULE_ID . '.TAB.EDIT')
		)
	));

	$arTabsAdd = array();
	foreach ($arSite as $siteId => $siteName) {
		$arTabsAdd[] = array(
			'DIV'   => 'edit_' . $siteId,
			'TAB'   => GetMessage($BXMAKER_MODULE_ID . '.TAB.EDIT', array('#SITE#' => $siteName)),
			'ICON'  => '',
			'TITLE' => GetMessage($BXMAKER_MODULE_ID . '.TAB.EDIT', array('#SITE#' => $siteName))
		);
	}

	$tab = new CAdminTabControl('edit', $arTabsAdd);

	$APPLICATION->SetTitle(GetMessage($BXMAKER_MODULE_ID . '.PAGE_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

	$oManager->addAdminPageCssJs();
?>


    <form action="<? $APPLICATION->GetCurPage() ?>" method="POST" name="<?= $fname ?>" class="<?= $fname ?>">
		<? echo bitrix_sessid_post(); ?>

		<? $tab->Begin(); ?>

		<? foreach ($arSite as $siteID => $siteName): ?>

			<? $tab->BeginNextTab(); ?>

			<? if (count($arError)): ?>
                <tr>
                    <td colspan="2">
                        <div class="error_box">
							<?= implode('<br>', $arError); ?>
                        </div>
                    </td>
                </tr>
			<? endif; ?>


            <tr>
                <td>
					<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.DOMAIN_ON'); ?>
                </td>
                <td>
					<?php
						$subdomainOn = $oOption->get($BXMAKER_MODULE_ID, 'SUBDOMAIN_ON', 'N', $siteID);
					?>
					<?= InputType('checkbox', 'SUBDOMAIN_ON[' . $siteID . ']', 'Y', $subdomainOn, false); ?>
                </td>
            </tr>

            <tr>
                <td>
					<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.BASE_DOMAIN'); ?>
                </td>
                <td>
					<?php
						$baseDomain = $oOption->get($BXMAKER_MODULE_ID, 'BASE_DOMAIN', $oManager->getHttpHost(), $siteID);
					?>
					<?= InputType('text', 'BASE_DOMAIN[' . $siteID . ']', $baseDomain, ''); ?>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="data_box" data-sid="<?= $siteID; ?>">
					<?
						echo '<script type="javascript/bxaker-tpl" id="bxmaker_geoip_data_row_tpl">';
					?>

                    <div class="row_item">
                        <div class="city_box">
                            <input type="text" name="LOCATION_NAME[<?= $siteID; ?>][]" value="" autocomplete="off" placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.PLACEHOLDER.CITY'); ?>"/>
                            <div class="option_box"></div>
                        </div>
                        <div class="subdomain_box">
                            <input type="text" name="SUBDOMAIN[<?= $siteID; ?>][]" value="" autocomplete="off" placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.PLACEHOLDER.SUBDOMAIN'); ?>"/>
                        </div>
                        <div class="action_box">
                            <div class="btn adm-btn btn_delete"><?= GetMessage($BXMAKER_MODULE_ID . '.BTN_DELETE'); ?></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

					<?
						echo '</script>';
					?>

                    <div class="row_box sortable_box">

                        <div class="row_item header">
                            <div class="city_box"><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.HEADER.CITY'); ?></div>
                            <div class="subdomain_box"><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.HEADER.SUBDOMAIN'); ?></div>
                            <div class="action_box"></div>
                            <div class="clearfix"></div>
                        </div>


						<? foreach ($arResult['ITEMS'][$siteID] as $item): ?>
							<?php
							if(!$item['LOCATION_ID']) continue;
							?>
                            <div class="row_item finish_item">
                                <div class="city_box">
                                    <p><?= $item['NAME']; ?></p>
                                    <div class="option_box"></div>
                                </div>
                                <div class="subdomain_box">
                                    <input type="text" name="SUBDOMAIN[<?= $siteID; ?>][l<?= $item['LOCATION_ID']; ?>]" value="<?= $item['VALUE'] ?>" autocomplete="off"
                                           placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.PLACEHOLDER.SUBDOMAIN'); ?>"/>
                                </div>
                                <div class="action_box">
                                    <div class="btn adm-btn btn_delete"><?= GetMessage($BXMAKER_MODULE_ID . '.BTN_DELETE'); ?></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
						<? endforeach; ?>


                        <div class="row_item">
                            <div class="city_box">
                                <input type="text" name="LOCATION_NAME[<?= $siteID; ?>][]" autocomplete="off" value=""
                                       placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.PLACEHOLDER.CITY'); ?>"/>
                                <div class="option_box"></div>
                            </div>
                            <div class="subdomain_box">
                                <input type="text" name="SUBDOMAIN[<?= $siteID; ?>][]" value="" autocomplete="off"
                                       placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.PLACEHOLDER.SUBDOMAIN'); ?>"/>
                            </div>
                            <div class="action_box">
                                <div class="btn adm-btn btn_delete"><?= GetMessage($BXMAKER_MODULE_ID . '.BTN_DELETE'); ?></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </div>

                    <div class="clearfix"></div>
                    <div class="btn_box">
                        <div class="btn btn_add adm-btn"><?= GetMessage($BXMAKER_MODULE_ID . '.BTN_ADD'); ?></div>
                    </div>


                </td>
            </tr>

            <tr>
                <td colspan="2" class="data_group_box" data-sid="<?= $siteID; ?>">
					<?
						echo '<script type="javascript/bxaker-tpl" id="bxmaker_geoip_data_group_row_tpl">';
					?>

                    <div class="row_item">
                        <div class="group_box">
							<?= SelectBoxFromArray('GROUP['.$siteID.'][]', $arLocGroups); ?>
                        </div>
                        <div class="subdomain_box">
                            <input type="text" name="SUBDOMAIN_GROUP[<?= $siteID; ?>][]" value="" autocomplete="off" placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.PLACEHOLDER.SUBDOMAIN'); ?>"/>
                        </div>
                        <div class="action_box">
                            <div class="btn adm-btn btn_delete"><?= GetMessage($BXMAKER_MODULE_ID . '.BTN_DELETE'); ?></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

					<?
						echo '</script>';
					?>

                    <div class="row_box sortable_box">

                        <div class="row_item header">
                            <div class="group_box"><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.HEADER.GROUP'); ?></div>
                            <div class="subdomain_box"><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.HEADER.SUBDOMAIN'); ?></div>
                            <div class="action_box"></div>
                            <div class="clearfix"></div>
                        </div>


						<? foreach ($arResult['ITEMS'][$siteID] as $item): ?>
                            <?php
                            if(!$item['GROUP']) continue;
                            ?>
                            <div class="row_item finish_item">
                                <div class="group_box">
									<?= SelectBoxFromArray('GROUP['.$siteID.'][]', $arLocGroups, $item['GROUP']); ?>
                                </div>
                                <div class="subdomain_box">
                                    <input type="text" name="SUBDOMAIN_GROUP[<?= $siteID; ?>][g<?= $item['GROUP']; ?>]" value="<?= $item['VALUE'] ?>" autocomplete="off"
                                           placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.PLACEHOLDER.SUBDOMAIN'); ?>"/>
                                </div>
                                <div class="action_box">
                                    <div class="btn adm-btn btn_delete"><?= GetMessage($BXMAKER_MODULE_ID . '.BTN_DELETE'); ?></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
						<? endforeach; ?>


                        <div class="row_item">
                            <div class="group_box">
								<?= SelectBoxFromArray('GROUP['.$siteID.'][]', $arLocGroups); ?>
                            </div>
                            <div class="subdomain_box">
                                <input type="text" name="SUBDOMAIN_GROUP[<?= $siteID; ?>][]" value="" autocomplete="off"
                                       placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD.PLACEHOLDER.SUBDOMAIN'); ?>"/>
                            </div>
                            <div class="action_box">
                                <div class="btn adm-btn btn_delete"><?= GetMessage($BXMAKER_MODULE_ID . '.BTN_DELETE'); ?></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </div>

                    <div class="clearfix"></div>
                    <div class="btn_box">
                        <div class="btn btn_add adm-btn"><?= GetMessage($BXMAKER_MODULE_ID . '.BTN_ADD'); ?></div>
                    </div>


                </td>
            </tr>

		<? endforeach; ?>

		<? $tab->EndTab(); ?>


		<? $tab->Buttons(array("disabled" => ($PREMISION_DEFINE != "W"),)); ?>
		<? $tab->End(); ?>
    </form>


<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>