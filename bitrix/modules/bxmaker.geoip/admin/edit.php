<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php"); ?>
<?
	//error_reporting(E_ALL);

	IncludeModuleLangFile(__FILE__);

	$MODULE_ID = 'bxmaker.geoip';


	\Bitrix\Main\Loader::IncludeModule($MODULE_ID);

	// ПРОВЕРКА ПРАВ ДОСТУПА
	$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);

	if ($PREMISION_DEFINE != 'W') {
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
		die();
	}

	$oMessageType = new \Bxmaker\GeoIP\Message\TypeTable();
	$oMessage     = new \Bxmaker\GeoIP\MessageTable();
	$oManager     = \Bxmaker\GeoIP\Manager::getInstance();
	$oFavorites   = new \Bxmaker\GeoIP\FavoritesTable();
	$app          = \Bitrix\Main\Application::getInstance();
	$req          = $app->getContext()->getRequest();

	$currentId = intval($req->getQuery('ID'));

	// группы местоположений
	$arLocGroups = array(
		'REFERENCE'    => array(''),
		'REFERENCE_ID' => array('')
	);
	if ($oManager->isModuleSaleInstalled()) {
		$oLocGroup   = new CSaleLocationGroup();
		$dbrLocGroup = $oLocGroup->GetList(Array("NAME" => "ASC"), array(), LANGUAGE_ID);
		while ($arLocGroup = $dbrLocGroup->Fetch()) {
			$arLocGroups['REFERENCE'][]    = $arLocGroup['NAME'];
			$arLocGroups['REFERENCE_ID'][] = $arLocGroup['ID'];
		}
	}
	else {
		$dbr = $oFavorites->getList();
		while ($ar = $dbr->Fetch()) {
			$arLocGroups['REFERENCE'][]    = $ar['NAME'];
			$arLocGroups['REFERENCE_ID'][] = $ar['ID'];
		}
	}


	// сайты
	$arSite          = array();
	$arSiteReference = array(
		'REFERENCE'    => array(),
		'REFERENCE_ID' => array()
	);
	$dbr             = \CSite::GetList($by = 'sort', $order = 'asc');
	while ($ar = $dbr->Fetch()) {
		$arSite[$ar['ID']]                 = '[' . $ar['ID'] . '] ' . $ar['NAME'];
		$arSiteReference['REFERENCE'][]    = $ar['NAME'];
		$arSiteReference['REFERENCE_ID'][] = $ar['ID'];
	}


	// Навигация над формой
	$oMenu = new CAdminContextMenu(array(
		array(
			"TEXT"  => GetMessage($MODULE_ID . '.NAV_BTN.RETURN'),
			"LINK"  => $MODULE_ID . '_list.php?lang=' . LANG,
			"TITLE" => GetMessage($MODULE_ID . '.NAV_BTN.RETURN'),
		),
	));


	// визуализаторы
	$fname = 'edit_table';


	$arError = array();

	$app = \Bitrix\Main\Application::getInstance();
	$req = $app->getContext()->getRequest();

	$bPostRepeat = false;

	// СОХРАНЕНИЕ. ПРИМЕНЕНИЕ
	if ((!!$req->get('apply') || !!$req->get('save')) && check_bitrix_sessid() && $req->isPost()) {

		$arFields = array();

		do {

			//базовые
			if (!$req->get('TYPE') || strlen(trim($req->get('TYPE'))) <= 0) {
				$arError[]   = GetMessage($MODULE_ID . '.ERROR.TYPE');
				$bPostRepeat = true;
				break;
			}

			if (!$req->get('SITE_ID') || !in_array($req->get('SITE_ID'), $arSiteReference['REFERENCE_ID'])) {
				$arError[]   = GetMessage($MODULE_ID . '.ERROR.SITE');
				$bPostRepeat = true;
				break;
			}

			// значение поумолчанию
			//			if (!$req->get('message_def') || strlen(trim($req->get('message_def'))) <= 0) {
			//				$bPostRepeat = true;
			//				$arError[] = GetMessage($MODULE_ID . '.ERROR.MESSAGE_DEF');
			//				break;
			//			}


			// добавляем метку для кэша
			$CACHE_MANAGER->ClearByTag('bxmaker_geoip_message');

			$typeId = 0;

			//если редактирование
			if (!!$req->getQuery('ID') && intval($req->getQuery('ID')) > 0) {

				$dbr = $oMessageType->getList(array(
					'filter' => array(
						'ID' => intval($req->getQuery('ID'))
					)
				));
				if ($ar = $dbr->Fetch()) {

					$typeId = $ar['ID'];

					// если изменился SITE_ID
					if ($ar['SITE_ID'] != trim($req->getPost('SITE_ID'))) {

						//првоеряем нет ли такого типа уже для другого сайта
						$dbr = $oMessageType->getList(array(
							'filter' => array(
								'TYPE'    => trim($req->getPost('TYPE')),
								'SITE_ID' => trim($req->getPost('SITE_ID'))
							)
						));
						if ($ar = $dbr->Fetch()) {
							$arError[] = GetMessage($MODULE_ID . '.ERROR.TYPE_SITE_EXISTS', array('#ID#' => $ar['ID']));;
							$bPostRepeat = true;
							break;
						}
					}


					$res = $oMessageType->update(intval($req->getQuery('ID')), array(
						'TYPE'    => trim($req->getPost('TYPE')),
						'SITE_ID' => trim($req->getPost('SITE_ID'))
					));
					if (!$res->isSuccess()) {
						$arError[]   = GetMessage($MODULE_ID . '.ERROR.UPDATE_TYPE_MESSAGE', array('#ERROR#' => implode(', ', $res->getErrorMessages())));
						$bPostRepeat = true;
						break;
					}


				}
				else {
					$arError[]   = GetMessage($MODULE_ID . '.ERROR.FIND_TYPE_MESSAGE');
					$bPostRepeat = true;
					break;
				}
			}
			else {
				// проверим не существует ли уже этот тип

				$dbr = $oMessageType->getList(array(
					'filter' => array(
						'TYPE'    => trim($req->getPost('TYPE')),
						'SITE_ID' => trim($req->getPost('SITE_ID'))
					)
				));
				if ($ar = $dbr->Fetch()) {
					$typeId = $ar['ID'];
				}
				else {
					// добавление
					$res = $oMessageType->add(array(
						'TYPE'    => trim($req->getPost('TYPE')),
						'SITE_ID' => trim($req->getPost('SITE_ID'))
					));
					if (!$res->isSuccess()) {
						$arError[]   = GetMessage($MODULE_ID . '.ERROR.ADD_TYPE_MESSAGE');
						$bPostRepeat = true;
						break;
					}

					$typeId = $res->getId();
				}
			}


			// ищем сообщения
			$arMessDef   = false;
			$arMess      = array();
			$arMessGroup = array();
			$dbrMess     = $oMessage->getList(array(
				'filter' => array(
					'TYPE_ID' => $typeId
				)
			));
			while ($ar = $dbrMess->Fetch()) {
				if (intval($ar['GROUP']) > 0) {
					$arMessGroup[$ar['ID']] = $ar;
				}
				else {
					if ($ar['DEF']) {
						$arMessDef = $ar;
					}
					else {
						$arMess[$ar['ID']] = $ar;
					}
				}

			}


			// сохраняем значение поумолчанию
			if (isset($arMessDef['ID'])) {
				if ($arMessDef['MESSAGE'] != trim($req->getPost('message_def'))) {
					$res = $oMessage->update($arMessDef['ID'], array(
						'MESSAGE' => trim($req->getPost('message_def'))
					));
					if (!$res->isSuccess()) {
						$arError[] = GetMessage($MODULE_ID . '.ERROR.UPDATE_MESSAGE_DEF', array('#ERROR#' => implode(', ', $res->getErrorMessages())));
					}
				}
			}
			else {
				$res = $oMessage->add(array(
					'TYPE_ID' => $typeId,
					'DEF'     => true,
					'MESSAGE' => trim($req->getPost('message_def')),
					'START'   => '00:00',
					'STOP'    => '23:59'
				));
				if (!$res->isSuccess()) {
					$arError[] = GetMessage($MODULE_ID . '.ERROR.ADD_MESSAGE_DEF', array('#ERROR#' => implode(', ', $res->getErrorMessages())));
				}
			}

			// сообщения теперь -------------------------------------

			// добавление новых
			$arNewCity  = (!!$req->getPost('city_new') && is_array($req->getPost('city_new')) ? $req->getPost('city_new') : array());
			$arNewMess  = (!!$req->getPost('message_new') && is_array($req->getPost('message_new')) ? $req->getPost('message_new') : array());
			$arNewStart = (!!$req->getPost('start_new') && is_array($req->getPost('start_new')) ? $req->getPost('start_new') : array());
			$arNewStop  = (!!$req->getPost('stop_new') && is_array($req->getPost('stop_new')) ? $req->getPost('stop_new') : array());
			foreach ($arNewCity as $i => $group) {
				if (strlen(trim($group)) <= 0) continue;

				$start = trim(isset($arNewStart[$i]) ? trim($arNewStart[$i]) : '');
				$stop  = trim(isset($arNewStop[$i]) ? trim($arNewStop[$i]) : '');

				if (!preg_match('/^\d\d:\d\d$/', $start)) {
					$start = '00:00';
				}

				if (!preg_match('/^\d\d:\d\d$/', $stop)) {
					$stop = '23:59';
				}

				$res = $oMessage->add(array(
					'TYPE_ID' => $typeId,
					'CITY'    => trim($group),
					'MESSAGE' => (isset($arNewMess[$i]) ? trim($arNewMess[$i]) : ''),
					'START'   => $start,
					'STOP'    => $stop
				));
				if (!$res->isSuccess()) {
					$arError[] = GetMessage($MODULE_ID . '.ERROR.ADD_MESSAGE_CITY', array('#CITY#' => trim($group), '#ERROR#' => implode(', ', $res->getErrorMessages())));
				}
			}

			// обновление
			$arMessIdUpdated = array();
			$arUpdateCity    = (!!$req->getPost('city') && is_array($req->getPost('city')) ? $req->getPost('city') : array());
			$arUpdateMess    = (!!$req->getPost('message') && is_array($req->getPost('message')) ? $req->getPost('message') : array());
			$arUpdateStart   = (!!$req->getPost('start') && is_array($req->getPost('start')) ? $req->getPost('start') : array());
			$arUpdateStop    = (!!$req->getPost('stop') && is_array($req->getPost('stop')) ? $req->getPost('stop') : array());


			foreach ($arUpdateCity as $i => $group) {
				if (strlen(trim($group)) <= 0) continue;
				if (!array_key_exists($i, $arMess)) continue;

				$arMessIdUpdated[] = $i;


				$start = trim(isset($arUpdateStart[$i]) ? trim($arUpdateStart[$i]) : '');
				$stop  = trim(isset($arUpdateStop[$i]) ? trim($arUpdateStop[$i]) : '');

				if (!preg_match('/^\d\d:\d\d$/', $start)) {
					$start = '00:00';
				}

				if (!preg_match('/^\d\d:\d\d$/', $stop)) {
					$stop = '23:59';
				}

				// проверяем изменилось ли что-нибудь
				$arUpdateFields = array();
				if (strtolower($arMess[$i]['CITY']) != strtolower(trim($group))) {
					$arUpdateFields['CITY'] = trim($group);
				}

				if (strtolower($arMess[$i]['MESSAGE']) != strtolower(trim((isset($arUpdateMess[$i]) ? trim($arUpdateMess[$i]) : '')))) {
					$arUpdateFields['MESSAGE'] = trim((isset($arUpdateMess[$i]) ? trim($arUpdateMess[$i]) : ''));
				}

				if (strtolower($arMess[$i]['START']) != $start) {
					$arUpdateFields['START'] = $start;
				}
				if (strtolower($arMess[$i]['START']) != $stop) {
					$arUpdateFields['STOP'] = $stop;
				}

				if (count($arUpdateFields)) {
					$res = $oMessage->update($i, $arUpdateFields);
					if (!$res->isSuccess()) {
						$arError[] = GetMessage($MODULE_ID . '.ERROR.UPDATE_MESSAGE_CITY', array('#CITY#' => trim($group), '#ERROR#' => implode(', ', $res->getErrorMessages())));
					}
				}
			}


			//удаление ненужных
			$arDeleteId = array_diff(array_keys($arMess), $arMessIdUpdated);
			foreach ($arDeleteId as $id) {
				$res = $oMessage->delete($id);
				if (!$res->isSuccess()) {
					$arError[] = GetMessage($MODULE_ID . '.ERROR.DELETE_MESSAGE_CITY', array('#CITY#' => trim($arMess[$id]['CITY']), '#ERROR#' => implode(', ', $res->getErrorMessages())));
				}
			}


			// группы местоположений --------------------------------


			// добавление новых
			$arNewCity  = (!!$req->getPost('group_new') && is_array($req->getPost('group_new')) ? $req->getPost('group_new') : array());
			$arNewMess  = (!!$req->getPost('group_message_new') && is_array($req->getPost('group_message_new')) ? $req->getPost('group_message_new') : array());
			$arNewStart = (!!$req->getPost('group_start_new') && is_array($req->getPost('group_start_new')) ? $req->getPost('group_start_new') : array());
			$arNewStop  = (!!$req->getPost('group_stop_new') && is_array($req->getPost('group_stop_new')) ? $req->getPost('group_stop_new') : array());
			foreach ($arNewCity as $i => $group) {
				if (strlen(trim($group)) <= 0) continue;

				$start = trim(isset($arNewStart[$i]) ? trim($arNewStart[$i]) : '');
				$stop  = trim(isset($arNewStop[$i]) ? trim($arNewStop[$i]) : '');

				if (!preg_match('/^\d\d:\d\d$/', $start)) {
					$start = '00:00';
				}

				if (!preg_match('/^\d\d:\d\d$/', $stop)) {
					$stop = '23:59';
				}

				$res = $oMessage->add(array(
					'TYPE_ID' => $typeId,
					'GROUP'   => trim($group),
					'MESSAGE' => (isset($arNewMess[$i]) ? trim($arNewMess[$i]) : ''),
					'START'   => $start,
					'STOP'    => $stop
				));
				if (!$res->isSuccess()) {
					$arError[] = GetMessage($MODULE_ID . '.ERROR.ADD_MESSAGE_GROUP', array('#GROUP' => $arMessGroup[trim($group)]['NAME'], '#ERROR#' => implode(', ', $res->getErrorMessages())));
				}
			}

			// обновление
			$arGroupIsset    = array();
			$arMessIdUpdated = array();
			$arUpdateCity    = (!!$req->getPost('group') && is_array($req->getPost('group')) ? $req->getPost('group') : array());
			$arUpdateMess    = (!!$req->getPost('group_message') && is_array($req->getPost('group_message')) ? $req->getPost('group_message') : array());
			$arUpdateStart   = (!!$req->getPost('group_start') && is_array($req->getPost('group_start')) ? $req->getPost('group_start') : array());
			$arUpdateStop    = (!!$req->getPost('group_stop') && is_array($req->getPost('group_stop')) ? $req->getPost('group_stop') : array());
			foreach ($arUpdateCity as $i => $group) {
				if (strlen(trim($group)) <= 0) continue;
				if (!array_key_exists($i, $arMessGroup)) continue;

				if (isset($arGroupIsset[trim($group)])) continue;
				$arGroupIsset[trim($group)] = 'Y';

				$arMessIdUpdated[] = $i;

				$start = trim(isset($arUpdateStart[$i]) ? trim($arUpdateStart[$i]) : '');
				$stop  = trim(isset($arUpdateStop[$i]) ? trim($arUpdateStop[$i]) : '');

				if (!preg_match('/^\d\d:\d\d$/', $start)) {
					$start = '00:00';
				}

				if (!preg_match('/^\d\d:\d\d$/', $stop)) {
					$stop = '23:59';
				}

				// проверяем изменилось ли что-нибудь
				$arUpdateFields = array();
				if (strtolower($arMessGroup[$i]['GROUP']) != strtolower(trim($group))) {
					$arUpdateFields['GROUP'] = trim($group);
				}

				if (strtolower($arMessGroup[$i]['MESSAGE']) != strtolower(trim((isset($arUpdateMess[$i]) ? trim($arUpdateMess[$i]) : '')))) {
					$arUpdateFields['MESSAGE'] = trim((isset($arUpdateMess[$i]) ? trim($arUpdateMess[$i]) : ''));
				}

				if (strtolower($arMess[$i]['START']) != $start) {
					$arUpdateFields['START'] = $start;
				}
				if (strtolower($arMess[$i]['START']) != $stop) {
					$arUpdateFields['STOP'] = $stop;
				}


				if (count($arUpdateFields)) {
					$res = $oMessage->update($i, $arUpdateFields);
					if (!$res->isSuccess()) {
						$arError[]
							= GetMessage($MODULE_ID . '.ERROR.UPDATE_MESSAGE_GROUP', array('#GROUP#' => $arMessGroup[trim($group)]['NAME'], '#ERROR#' => implode(', ', $res->getErrorMessages())));
					}
				}
			}

			//удаление ненужных
			$arDeleteId = array_diff(array_keys($arMessGroup), $arMessIdUpdated);
			foreach ($arDeleteId as $id) {
				$res = $oMessage->delete($id);
				if (!$res->isSuccess()) {
					$arError[] = GetMessage($MODULE_ID . '.ERROR.DELETE_MESSAGE_CITY', array('#GROUP#' => $arMessGroup[trim($id)]['NAME'], '#ERROR#' => implode(', ', $res->getErrorMessages())));
				}
			}


			if (count($arError)) {
				break;
			}


			if (!!$req->getPost('apply')) {
				LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $typeId, array('ID')));
			}
            elseif (!!$req->getPost('save')) {
				LocalRedirect('/bitrix/admin/' . $MODULE_ID . '_list.php');
			}

		} while (false);


	}

	// РЕДАКТИРОВАНИЕ
	$arResult                = array();
	$arResult['ITEMS']       = array();
	$arResult['GROUP_ITEMS'] = array();
	$arResult['ITEM_DEF']    = array();

	if (!$apply && !$save && $currentId && !$_POST) {

		$dbr = $oMessageType->getList(array(
			'filter' => array('ID' => $currentId)
		));
		if ($ar = $dbr->Fetch()) {
			$arResult = $ar;

			// сообщения
			$dbrMess = $oMessage->getList(array(
				'filter' => array(
					'TYPE_ID' => $arResult['ID']
				),
				'order'  => array(
					'CITY' => 'ASC'
				)
			));
			while ($arMess = $dbrMess->fetch()) {


				if (intval($arMess['GROUP']) > 0) {
					$arResult['GROUP_ITEMS'][] = $arMess;
				}
				else {
					if ($arMess['DEF']) {
						$arResult['ITEM_DEF'] = $arMess;
					}
					else {
						$arResult['ITEMS'][] = $arMess;
					}
				}


			}
		}
	}


	$tab = new CAdminTabControl('edit', array(
		array(
			'DIV'   => 'edit',
			'TAB'   => GetMessage($MODULE_ID . '.TAB.EDIT'),
			'ICON'  => '',
			'TITLE' => GetMessage($MODULE_ID . '.TAB.EDIT')
		),
		array(
			'DIV'   => 'edit_group',
			'TAB'   => GetMessage($MODULE_ID . '.TAB.EDIT_GROUP'),
			'ICON'  => '',
			'TITLE' => GetMessage($MODULE_ID . '.TAB.EDIT_GROUP')
		),
	));

	$APPLICATION->SetTitle(GetMessage($MODULE_ID . '.PAGE_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

	$oMenu->Show();

	$oManager->addAdminPageCssJs();
?>


    <form action="<? $APPLICATION->GetCurPage() ?>" method="POST" name="<?= $fname ?>" class="bxmaker_geoip_message_edit_form">
		<? echo bitrix_sessid_post(); ?>

		<? $tab->Begin(); ?>
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


		<? if ($currentId): ?>
            <tr>
                <td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.ID'); ?> </td>
                <td><?= $currentId; ?></td>
            </tr>
		<? endif; ?>

        <tr>
            <td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.TYPE'); ?> </td>
            <td><input type="text" name="TYPE" value="<?

					if ($req->isPost() && !!$req->getPost('TYPE')) {
						echo $req->getPost('TYPE');
					}
					else {
						if (isset($arResult['TYPE']) && strlen(trim($arResult['TYPE'])) > 0) {
							echo $arResult['TYPE'];
						}
					}
				?>" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.TYPE'); ?>"/></td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.SITE_ID'); ?> </td>
			<?

				if ($req->isPost() && !!$req->getPost('SITE_ID')) {
					$arResult['SITE_ID'] = $req->getPost('SITE_ID');
				}
			?>
            <td><?= SelectBoxFromArray('SITE_ID', $arSiteReference, $arResult['SITE_ID']); ?></td>
        </tr>

        <tr>
            <td colspan="2" class="data_box">
				<?
					echo '<script type="javascript/bxaker-tpl" id="bxmaker_geoip_data_row_tpl">';
				?>

                <div class="row_item">
                    <div class="city_box">
                        <input type="text" name="city_new[]" value="" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.CITY'); ?>"/>
                    </div>
                    <div class="message_box">
                        <textarea name="message_new[]" id="" rows="3" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.MESSAGE'); ?>"></textarea>
                    </div>
                    <div class="time_box">
						<?= GetMessage($MODULE_ID . '.FIELD.TIME_START'); ?>
                        <input type="text" name="start_new[]" maxlength="5" placeholder="00:00" value="00:00"/>
						<?= GetMessage($MODULE_ID . '.FIELD.TIME_STOP'); ?>
                        <input type="text" name="stop_new[]" maxlength="5" placeholder="23:59" value="23:59"/>
                    </div>
                    <div class="action_box">
                        <div class="btn adm-btn btn_delete"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
                    </div>
                    <div class="clearfix"></div>
                </div>

				<?
					echo '</script>';
				?>

                <div class="row_box">

                    <div class="row_item header">
                        <div class="city_box"><?= GetMessage($MODULE_ID . '.FIELD.ITEM.CITY'); ?></div>
                        <div class="message_box"><?= GetMessage($MODULE_ID . '.FIELD.ITEM.MESSAGE'); ?></div>
                        <div class="time_box"><?= GetMessage($MODULE_ID . '.FIELD.ITEM.TIME'); ?></div>
                        <div class="action_box"><?= GetMessage($MODULE_ID . '.FIELD.ITEM.ACTION'); ?></div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="row_item">
                        <div class="city_box">
							<?= GetMessage($MODULE_ID . '.FIELD.DEFAULT.CITY'); ?>
                        </div>
                        <div class="message_box">
							<textarea name="message_def" id="" rows="3" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.MESSAGE'); ?>"><?

									if ($req->isPost() && !!$req->getPost('message_def')) {
										echo $req->getPost('message_def');
									}
									else {
										if (isset($arResult['ITEM_DEF']['MESSAGE']) && strlen(trim($arResult['ITEM_DEF']['MESSAGE'])) > 0) {
											echo $arResult['ITEM_DEF']['MESSAGE'];
										}
									}


								?></textarea>
                        </div>
                        <div class="time_box">

                        </div>
                        <div class="action_box">

                        </div>

                        <div class="clearfix"></div>
                    </div>

					<? foreach ($arResult['ITEMS'] as $item): ?>
                        <div class="row_item">
                            <div class="city_box">
                                <input type="text" name="city[<?= $item['ID']; ?>]" value="<?= $item['CITY']; ?>" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.CITY'); ?>"/>
                            </div>
                            <div class="message_box">
								<textarea name="message[<?= $item['ID']; ?>]" id="" rows="3"
                                          placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.MESSAGE'); ?>"><?= $item['MESSAGE']; ?></textarea>
                            </div>
                            <div class="time_box">
								<?= GetMessage($MODULE_ID . '.FIELD.TIME_START'); ?>
                                <input type="text" name="start[<?= $item['ID']; ?>]" maxlength="5" placeholder="00:00" value="<?= $item['START']; ?>"/>
								<?= GetMessage($MODULE_ID . '.FIELD.TIME_STOP'); ?>
                                <input type="text" name="stop[<?= $item['ID']; ?>]" maxlength="5" placeholder="23:59" value="<?= $item['STOP']; ?>"/>
                            </div>
                            <div class="action_box">
                                <div class="btn adm-btn btn_delete"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
					<? endforeach; ?>

					<?
						if ($bPostRepeat && $req->isPost()) {
							$arNewCity  = (!!$req->getPost('city_new') && is_array($req->getPost('city_new')) ? $req->getPost('city_new') : array());
							$arNewMess  = (!!$req->getPost('message_new') && is_array($req->getPost('message_new')) ? $req->getPost('message_new') : array());
							$arNewStart = (!!$req->getPost('start_new') && is_array($req->getPost('start_new')) ? $req->getPost('start_new') : array());
							$arNewStop  = (!!$req->getPost('stop_new') && is_array($req->getPost('stop_new')) ? $req->getPost('stop_new') : array());
							foreach ($arNewCity as $i => $group) {
								if (strlen(trim($group)) <= 0) continue;

								?>
                                <div class="row_item">
                                    <div class="city_box">
                                        <input type="text" name="city_new[]" value="<?= trim($group); ?>" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.CITY'); ?>"/>
                                    </div>
                                    <div class="message_box">
										<textarea name="message_new[]" id="" rows="3"
                                                  placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.MESSAGE'); ?>"><?= (isset($arNewMess[$i]) ? trim($arNewMess[$i]) : ''); ?></textarea>
                                    </div>
                                    <div class="time_box">
										<?= GetMessage($MODULE_ID . '.FIELD.TIME_START'); ?>
                                        <input type="text" name="start_new[]" maxlength="5" placeholder="00:00" value="<?= (isset($arNewStart[$i]) ? trim($arNewStart[$i]) : ''); ?>"/>
										<?= GetMessage($MODULE_ID . '.FIELD.TIME_STOP'); ?>
                                        <input type="text" name="stop_new[]" maxlength="5" placeholder="23:59" value="<?= (isset($arNewStop[$i]) ? trim($arNewStop[$i]) : ''); ?>"/>
                                    </div>
                                    <div class="action_box">
                                        <div class="btn adm-btn btn_delete"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
								<?
							}
						}
					?>

                    <div class="row_item">
                        <div class="city_box">
                            <input type="text" name="city_new[]" value="" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.CITY'); ?>"/>
                        </div>
                        <div class="message_box">
                            <textarea name="message_new[]" id="" rows="3" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.MESSAGE'); ?>"></textarea>
                        </div>
                        <div class="time_box">
							<?= GetMessage($MODULE_ID . '.FIELD.TIME_START'); ?>
                            <input type="text" name="start_new[]" maxlength="5" placeholder="00:00" value="00:00"/>
							<?= GetMessage($MODULE_ID . '.FIELD.TIME_STOP'); ?>
                            <input type="text" name="stop_new[]" maxlength="5" placeholder="23:59" value="23:59"/>
                        </div>
                        <div class="action_box">
                            <div class="btn adm-btn btn_delete"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                </div>

                <div class="clearfix"></div>
                <div class="btn_box">
                    <div class="btn btn_add adm-btn"><?= GetMessage($MODULE_ID . '.BTN_ADD'); ?></div>
                </div>


            </td>
        </tr>

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
            <td colspan="2" class="data_box_group">
				<?
					echo '<script type="javascript/bxaker-tpl" id="bxmaker_geoip_group_data_row_tpl">';
				?>

                <div class="row_item">
                    <div class="city_box">
						<?= SelectBoxFromArray('group_new[]', $arLocGroups, $arResult['SITE_ID']); ?>
                    </div>
                    <div class="message_box">
                        <textarea name="group_message_new[]" id="" rows="3" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.GROUP_MESSAGE'); ?>"></textarea>
                    </div>
                    <div class="time_box">
						<?= GetMessage($MODULE_ID . '.FIELD.TIME_START'); ?>
                        <input type="text" name="group_start_new[]" maxlength="5" placeholder="00:00" value="00:00"/>
						<?= GetMessage($MODULE_ID . '.FIELD.TIME_STOP'); ?>
                        <input type="text" name="group_stop_new[]" maxlength="5" placeholder="23:59" value="23:59"/>
                    </div>
                    <div class="action_box">
                        <div class="btn adm-btn btn_delete_group"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
                    </div>
                    <div class="clearfix"></div>
                </div>

				<?
					echo '</script>';
				?>

                <div class="row_box">

                    <div class="row_item header">
                        <div class="city_box"><?= GetMessage($MODULE_ID . '.FIELD.ITEM.GROUP'); ?></div>
                        <div class="message_box"><?= GetMessage($MODULE_ID . '.FIELD.ITEM.MESSAGE'); ?></div>
                        <div class="time_box"><?= GetMessage($MODULE_ID . '.FIELD.ITEM.TIME'); ?></div>
                        <div class="action_box"><?= GetMessage($MODULE_ID . '.FIELD.ITEM.ACTION'); ?></div>
                        <div class="clearfix"></div>
                    </div>


					<? foreach ($arResult['GROUP_ITEMS'] as $item): ?>
                        <div class="row_item">
                            <div class="city_box">
								<?= SelectBoxFromArray('group[' . $item['ID'] . ']', $arLocGroups, $item['GROUP']); ?>
                            </div>
                            <div class="message_box">
								<textarea name="group_message[<?= $item['ID']; ?>]" id="" rows="3"
                                          placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.GROUP_MESSAGE'); ?>"><?= $item['MESSAGE']; ?></textarea>
                            </div>
                            <div class="time_box">
								<?= GetMessage($MODULE_ID . '.FIELD.TIME_START'); ?>
                                <input type="text" name="group_start[<?= $item['ID']; ?>]" maxlength="5" placeholder="00:00" value="<?= $item['START']; ?>"/>
								<?= GetMessage($MODULE_ID . '.FIELD.TIME_STOP'); ?>
                                <input type="text" name="group_stop[<?= $item['ID']; ?>]" maxlength="5" placeholder="23:59" value="<?= $item['STOP']; ?>"/>
                            </div>
                            <div class="action_box">
                                <div class="btn adm-btn btn_delete_group"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
					<? endforeach; ?>

					<?
						if ($bPostRepeat && $req->isPost()) {
							$arNewCity  = (!!$req->getPost('group_new') && is_array($req->getPost('group_new')) ? $req->getPost('group_new') : array());
							$arNewMess  = (!!$req->getPost('group_message_new') && is_array($req->getPost('group_message_new')) ? $req->getPost('group_message_new') : array());
							$arNewStart = (!!$req->getPost('group_start_new') && is_array($req->getPost('group_start_new')) ? $req->getPost('group_start_new') : array());
							$arNewStop  = (!!$req->getPost('group_stopt_new') && is_array($req->getPost('group_stopt_new')) ? $req->getPost('group_stopt_new') : array());
							foreach ($arNewCity as $i => $group) {
								if (strlen(trim($group)) <= 0) continue;

								?>
                                <div class="row_item">
                                    <div class="city_box">
										<?= SelectBoxFromArray('group_new[]', $arLocGroups, trim($group)); ?>
                                    </div>
                                    <div class="message_box">
										<textarea name="group_message_new[]" id="" rows="3"
                                                  placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.GROUP_MESSAGE'); ?>"><?= (isset($arNewMess[$i]) ? trim($arNewMess[$i]) : ''); ?></textarea>
                                    </div>
                                    <div class="time_box">
										<?= GetMessage($MODULE_ID . '.FIELD.TIME_START'); ?>
                                        <input type="text" name="group_start_new[]" maxlength="5" placeholder="00:00" value="<?= (isset($arNewStart[$i]) ? trim($arNewStart[$i]) : ''); ?>"/>
										<?= GetMessage($MODULE_ID . '.FIELD.TIME_STOP'); ?>
                                        <input type="text" name="group_stop_new[]" maxlength="5" placeholder="23:59" value="<?= (isset($arNewStop[$i]) ? trim($arNewStop[$i]) : ''); ?>"/>
                                    </div>
                                    <div class="action_box">
                                        <div class="btn adm-btn btn_delete_group"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
								<?
							}
						}
					?>

                    <div class="row_item">
                        <div class="city_box">

							<?= SelectBoxFromArray('group_new[]', $arLocGroups, ''); ?>
                        </div>
                        <div class="message_box">
                            <textarea name="group_message_new[]" id="" rows="3" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.GROUP_MESSAGE'); ?>"></textarea>
                        </div>
                        <div class="time_box">
							<?= GetMessage($MODULE_ID . '.FIELD.TIME_START'); ?>
                            <input type="text" name="group_start_new[]" maxlength="5" placeholder="00:00" value="00:00"/>
							<?= GetMessage($MODULE_ID . '.FIELD.TIME_STOP'); ?>
                            <input type="text" name="group_stop_new[]" maxlength="5" placeholder="23:59" value="23:59"/>
                        </div>
                        <div class="action_box">
                            <div class="btn adm-btn btn_delete_group"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                </div>

                <div class="clearfix"></div>
                <div class="btn_box">
                    <div class="btn btn_add_group adm-btn"><?= GetMessage($MODULE_ID . '.BTN_ADD'); ?></div>
                </div>


            </td>
        </tr>


		<? $tab->EndTab(); ?>
		<? $tab->Buttons(array("disabled" => ($PREMISION_DEFINE != "W"),)); ?>
		<? $tab->End(); ?>
    </form>


<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>