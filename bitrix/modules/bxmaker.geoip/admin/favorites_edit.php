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

	$oManager       = \Bxmaker\GeoIP\Manager::getInstance();
	$oFavorites     = new \Bxmaker\GeoIP\FavoritesTable();
	$oFavoritesCity = new \Bxmaker\GeoIP\Favorites\CityTable();
	$oFavoritesSite = new \Bxmaker\GeoIP\Favorites\SiteTable();
	$app = \Bitrix\Main\Application::getInstance();
	$req = $app->getContext()->getRequest();

	$currentId = intval($req->getQuery('ID'));

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


	// Навигация над формой
	$oMenu = new CAdminContextMenu(array(
		array(
			"TEXT"  => GetMessage($MODULE_ID . '.NAV_BTN.RETURN'),
			"LINK"  => $MODULE_ID . '_favorites_list.php?lang=' . LANG,
			"TITLE" => GetMessage($MODULE_ID . '.NAV_BTN.RETURN'),
		),
	));


	// визуализаторы
	$fname = 'favorites_edit_table';


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
					'count' => count($arLocTmp)
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


	$bPostRepeat = false;
	$arError     = array();

	// СОХРАНЕНИЕ. ПРИМЕНЕНИЕ
	if ((!!$req->get('apply') || !!$req->get('save')) && check_bitrix_sessid() && $req->isPost()) {

		$arFields = array();

		do {

			//базовые
			if (!$req->getPost('NAME') || strlen(trim($req->getPost('NAME'))) <= 0) {
				$arError[]   = GetMessage($MODULE_ID . '.ERROR.NAME');
				$bPostRepeat = true;
				break;
			}

			$arSid = (array)$req->getPost('SID');
			if (empty($arSid)) {
				$arError[]   = GetMessage($MODULE_ID . '.ERROR.SID');
				$bPostRepeat = true;
				break;
			}

			$bPostRepeat = true;

			// добавляем метку для кэша
			if (isset($CACHE_MANAGER)) {
				$CACHE_MANAGER->ClearByTag('bxmaker_geoip_favorites');
			}

			$favoritesID = 0;

			//если редактирование, ищем группу
			if (!!$req->getQuery('ID') && intval($req->getQuery('ID')) > 0) {

				$dbr = $oFavorites->getList(array(
					'filter' => array(
						'ID' => intval($req->getQuery('ID'))
					)
				));
				if ($ar = $dbr->Fetch()) {

					$favoritesID = $ar['ID'];
				}
			}

			// добавление избранного местоположения
			if ($favoritesID <= 0) {
				$res = $oFavorites->add(array(
					'NAME' => trim($req->getPost('NAME'))
				));
				if (!$res->isSuccess()) {
					$arError[] = GetMessage($MODULE_ID . '.ERROR.ADD_FAVORITES_MESSAGE');

					break;
				}

				$favoritesID = $res->getId();
			}


			// проверяем привязку к сайтам
			$arSiteCurrent = array();
			$dbrFavSite    = $oFavoritesSite->getList(array(
				'filter' => array(
					'FID' => $favoritesID
				)
			));
			while ($arFavSite = $dbrFavSite->fetch()) {
				$arSiteCurrent[$arFavSite['SID']] = $arFavSite['ID'];
			}


			if (is_array($req->getPost('SID'))) {
				foreach ($req->getPost('SID') as $sid => $val) {
					if (!isset($arSiteCurrent[$sid]) && $val == 'Y') {
						$oFavoritesSite->add(array(
							'FID' => $favoritesID,
							'SID' => $sid
						));
						unset($arSiteCurrent[$sid]);
					}
					elseif (isset($arSiteCurrent[$sid]) && $val == 'Y') {
						unset($arSiteCurrent[$sid]);
					}
				}
			}

			//удаление ненужных привязок
			foreach ($arSiteCurrent as $sid => $id) {
				$oFavoritesSite->delete($id);
			}

			// проверяем местоположения группы
			$arCityCurrent = array();
			$dbrFavCity    = $oFavoritesCity->getList(array(
				'filter' => array(
					'FID' => $favoritesID
				)
			));
			while ($arFavCity = $dbrFavCity->fetch()) {
				$arCityCurrent[$arFavCity['LOCATION_ID']] = array(
					'ID'   => $arFavCity['ID'],
					'SORT' => $arFavCity["SORT"]
				);
			}

			$arLocValue = array();
			if (is_array($req->getPost('LOCATION_ID'))) {
				foreach ($req->getPost('LOCATION_ID') as $key => $sort) {
					if (preg_match('/^l([\d]+)$/', $key, $match)) {
						$arLocValue[$match[1]] = $sort;
					}
				}
			}

			$arMark = (array) $req->getPost('MARK');


			foreach ($arLocValue as $loc_id => $sort) {
			    $mark = (isset($arMark['l'.$loc_id]) && $arMark['l'.$loc_id] == 'Y' ? true : false);

				if (!isset($arCityCurrent[$loc_id])) {
					$oFavoritesCity->add(array(
						'FID'         => $favoritesID,
						'LOCATION_ID' => $loc_id,
						'SORT'        => intval($sort),
                        'MARK' => $mark
					));
				}
				elseif (isset($arCityCurrent[$loc_id]) && ((intval($sort) != $arCityCurrent[$loc_id]["SORT"]) || ($mark != $arCityCurrent[$loc_id]["MARK"]))) {
					$oFavoritesCity->update($arCityCurrent[$loc_id]['ID'], array(
						'SORT' => intval($sort),
                        'MARK' => $mark
					));
				}

				unset($arCityCurrent[$loc_id]);
			}

			//удаление ненужных местоположений
			foreach ($arCityCurrent as $loc_id => $arItem) {
				$oFavoritesCity->delete($arItem['ID']);
			}

			//сброс кэша
			if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER']))
			{
				$GLOBALS['CACHE_MANAGER']->ClearByTag('bxmaker_geoip_city');
			}

			if (!!$req->getPost('apply')) {
				LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $favoritesID, array('ID')));
			}
			elseif (!!$req->getPost('save')) {
				LocalRedirect('/bitrix/admin/' . $MODULE_ID . '_favorites_list.php?lang=' . LANG);
			}

		} while (false);

	}

	// РЕДАКТИРОВАНИЕ
	$arResult['MAIN']  = array();
	$arResult['SID']   = array();
	$arResult['ITEMS'] = array();


	if (!$apply && !$save && $currentId && !$_POST) {

		//группа --
		$dbr = $oFavorites->getList(array(
			'select' => array('*'),
			'filter' => array('ID' => $currentId)
		));
		if ($ar = $dbr->Fetch()) {
			$arResult['MAIN'] = $ar;
		}

		// сайты
		$dbr = $oFavoritesSite->getList(array(
			'select' => array('*'),
			'filter' => array('FID' => $currentId)
		));
		while ($ar = $dbr->Fetch()) {
			$arResult['SID'][$ar['SID']] = 'Y';
		}

		// местоположения
		$arLocationID = array();
		$dbr            = $oFavoritesCity->getList(array(
			'select' => array('*'),
			'order'  => array('SORT' => 'ASC'),
			'filter' => array('FID' => $currentId)
		));
		while ($ar = $dbr->Fetch()) {

			$ar['NAME'] = '';

			$arResult['ITEMS'][$ar['ID']] = $ar;

			$arLocationID[$ar['LOCATION_ID']] = $ar['ID'];
		}

		if (count($arLocationID)) {
			$ar = $oManager->getLocationByID(array_keys($arLocationID));

			foreach ($ar as $location) {
				$arResult['ITEMS'][$arLocationID[$location['location']]]['NAME'] = $location['city'];
			}
		}
	}

	$tab = new CAdminTabControl('edit', array(
		array(
			'DIV'   => 'edit',
			'TAB'   => GetMessage($MODULE_ID . '.TAB.EDIT'),
			'ICON'  => '',
			'TITLE' => GetMessage($MODULE_ID . '.TAB.EDIT')
		)
	));

	$APPLICATION->SetTitle(GetMessage($MODULE_ID . '.PAGE_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

	$oMenu->Show();

	$oManager->addAdminPageCssJs();
?>


	<form action="<? $APPLICATION->GetCurPage() ?>" method="POST" name="<?= $fname ?>" class="bxmaker_geoip_favorites_edit_form">
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
			<td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.NAME'); ?> </td>
			<td><input type="text" name="NAME" value="<?

					if ($req->isPost() && !!$req->getPost('NAME')) {
						echo $req->getPost('NAME');
					}
					else {
						if (isset($arResult['MAIN']['NAME']) && strlen(trim($arResult['MAIN']['NAME'])) > 0) {
							echo $arResult['MAIN']['NAME'];
						}
					}
				?>" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.NAME'); ?>"/></td>
		</tr>
		<tr>
			<td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.SID'); ?> </td>
			<?
				$arSidPost = array();
				if ($req->isPost() && !!$req->getPost('SID')) {
					$arSidPost = $req->getPost('SID');
				}
				else {
					foreach ($arResult['SID'] as $sid => $val) {
						$arSidPost[$sid] = 'Y';
					}
				}

			?>
			<td>
				<?php
					foreach ($arSite as $sid => $sname) {
						echo InputType('checkbox', 'SID[' . $sid . ']', 'Y', $arSidPost[$sid], false, '<label for="' . $sid . '" >' . $sname . '</label><br>');
					}
				?>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="data_box">
				<?
					echo '<script type="javascript/bxaker-tpl" id="bxmaker_geoip_data_row_tpl">';
				?>

				<div class="row_item">
					<div class="city_box">
						<input type="text" name="LOCATION_NAME[]" value="" autocomplete="off" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.CITY'); ?>"/>
						<input type="hidden" name="LOCATION_ID[]" value=""/>
						<div class="option_box"></div>
					</div>
                    <div class="mark_box">
                        <input type="checkbox" name="MARK[]" value="Y" />
                    </div>
					<div class="action_box">
						<div class="btn adm-btn btn_delete"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
					</div>
					<div class="clearfix"></div>
				</div>

				<?
					echo '</script>';
				?>

				<div class="row_box sortable_box">

					<div class="row_item header">
						<div class="city_box"><?= GetMessage($MODULE_ID . '.FIELD.HEADER.CITY'); ?></div>
                        <div class="mark_box"><?= GetMessage($MODULE_ID . '.FIELD.HEADER.MARK'); ?></div>
						<div class="action_box"></div>
						<div class="clearfix"></div>
					</div>

					<? foreach ($arResult['ITEMS'] as $item): ?>
						<div class="row_item finish_item">
							<div class="city_box">
								<p><?= $item['NAME']; ?></p>
								<input type="hidden" name="LOCATION_ID[l<?= $item['LOCATION_ID']; ?>]" value="<?= $item['SORT']; ?>"/>
								<div class="option_box"></div>
							</div>
                            <div class="mark_box">
                                <input type="checkbox" name="MARK[l<?= $item['LOCATION_ID']; ?>]" value="Y" <?= $item['MARK'] ? ' checked="checked" ' : ''; ?> />
                            </div>
							<div class="action_box">
								<div class="btn adm-btn btn_delete"><?= GetMessage($MODULE_ID . '.BTN_DELETE'); ?></div>
							</div>
							<div class="clearfix"></div>
						</div>
					<? endforeach; ?>


					<div class="row_item">
						<div class="city_box">
							<input type="text" name="LOCATION_NAME[]"  autocomplete="off" value="" placeholder="<?= GetMessage($MODULE_ID . '.FIELD.PLACEHOLDER.CITY'); ?>"/>
							<input type="hidden" name="LOCATION_ID[]" value=""/>
							<div class="option_box"></div>
						</div>
                        <div class="mark_box">
                            <input type="checkbox" name="MARK[]" value="Y" />
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

		<? $tab->EndTab(); ?>
		<? $tab->Buttons(array("disabled" => ($PREMISION_DEFINE != "W"),)); ?>
		<? $tab->End(); ?>
	</form>


<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>