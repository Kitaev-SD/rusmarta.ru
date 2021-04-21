<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

	$BXMAKER_MODULE_ID = "bxmaker.smsnotice";

	
	\Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
	\Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);

	CJSCore::Init(array('jquery'));

	$oTemplateType = new Bxmaker\SmsNotice\Template\TypeTable();
	$oTemplate     = new \Bxmaker\SmsNotice\TemplateTable();
	$oTemplateSite = new \Bxmaker\SmsNotice\Template\SiteTable();
	$app           = \Bitrix\Main\Application::getInstance();
	$req           = $app->getContext()->getRequest();

	$PREMISION_DEFINE = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

	if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	if ($PREMISION_DEFINE == 'W') {
		$bReadOnly = false;
	}
	else  $bReadOnly = true;

	$sTableID = 'bxmaker_smsnotice';
	$sCurPage = $APPLICATION->GetCurPage();
	$sSAPEdit = $BXMAKER_MODULE_ID . '_template_edit.php';


	$oSort  = new CAdminSorting($sTableID, "SORT", "ASC");
	$sAdmin = new CAdminList($sTableID, $oSort);

	// меню
	$sContent = array(
		array(
			"TEXT"  => GetMessage($BXMAKER_MODULE_ID . '_MENU_BTN_NEW_TITLE'),
			"LINK"  => $sSAPEdit . "?lang=" . LANG,
			"TITLE" => GetMessage($BXMAKER_MODULE_ID . '_MENU_BTN_NEW_TITLE'),
			"ICON"  => "btn_new",
		),
	);
	$sMenu    = new CAdminContextMenu($sContent);


	// Массовые операции удаления ---------------------------------
	if ($arID = $sAdmin->GroupAction()) {

		switch ($req->get('action')) {
			case "deactivate":
				foreach ($arID as $id) {
					$res = $oTemplate->update($id, array('ACTIVE' => false));
				}
				break;
			case "active":
				foreach ($arID as $id) {
					$res = $oTemplate->update($id, array('ACTIVE' => true));
				}
				break;
		}

		switch ($req->getPost('action_button')) {
			case "delete":
				foreach ($arID as $id) {
					$res = $oTemplate->delete($id);
				}
				break;
		}
	}



	$arEmailType = array();
	$dbrEventType    = \CEventType::GetList(array('LID' => LANGUAGE_ID), array('SORT' => 'ASC'));
	while ($arEventType = $dbrEventType->Fetch()) {
		$arEmailType[$arEventType['EVENT_NAME']] =  $arEventType['NAME'];
	}


	// сайты
	$arSite = array();
	$dbr    = \CSite::GetList($by = 'sort', $order = 'asc');
	while ($ar = $dbr->Fetch()) {
		$arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
	}


	// Сортировка ------------------------------
	$by = 'ID';
	if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'NAME', 'TYPE_ID', 'ACTIVE','TRANSLIT'))) $by = $_GET['by'];
	$arOrder = array($by => ($_GET['order'] == 'ASC' ? 'ASC' : 'DESC'));

	// Постраничная навигация ------------------
	$navyParams        = CDBResult::GetNavParams(CAdminResult::GetNavSize(
		$sTableID,
		array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())
	));
	$usePageNavigation = true;
	if ($navyParams['SHOW_ALL']) {
		$usePageNavigation = false;
	}
	else {
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}

	// Запрос -----------------------------------
	$arQuery = array(
		'select' => array(
			'*', 'TP_' => 'TYPE'
		),
		'order'  => $arOrder
	);
	if ($usePageNavigation) {
		$arQuery['limit']  = $navyParams['SIZEN'];
		$arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
	}

	$dbResultList = $oTemplate->getList($arQuery);

	$dbResultList = new CAdminResult($dbResultList, $sTableID);

	$dbResultList->NavStart();


	$sAdmin->NavText($dbResultList->GetNavPrint(GetMessage($BXMAKER_MODULE_ID . '_PAGE_LIST_TITLE_NAV_TEXT')));

	$sAdmin->AddHeaders(array(
		array(
			"id"      => 'ID',
			"content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.ID'),
			"sort"    => 'ID',
			"default" => true
		),
		array(
			"id"      => 'NAME',
			"content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.NAME'),
			"sort"    => 'NAME',
			"default" => true
		),
		array(
			"id"      => 'TYPE_ID',
			"content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.TYPE'),
			"sort"    => 'TYPE_ID',
			"default" => true
		),
		array(
			"id"      => 'ACTIVE',
			"content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.ACTIVE'),
			"sort"    => 'ACTIVE',
			"default" => true
		),
		array(
			"id"      => 'SITE',
			"content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.SITE'),
			"sort"    => '',
			"default" => true
		),
		array(
			"id"      => 'TRANSLIT',
			"content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.TRANSLIT'),
			"sort"    => 'TRANSLIT',
			"default" => true
		),
		array(
			"id"      => 'EVENT',
			"content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.EVENT'),
			"sort"    => 'EVENT',
			"default" => true
		)
	));


	while ($sArActions = $dbResultList->NavNext(true, 's_')) {

		$arSID  = array();
		$dbrSID = $oTemplateSite->getList(array(
			'filter' => array(
				'TID' => $s_ID
			)
		));
		while ($ar = $dbrSID->fetch()) {
			$arSID[] = (isset($arSite[$ar['SID']]) ? $arSite[$ar['SID']] : $ar['SID']);
		}


		$row = &$sAdmin->AddRow($s_ID, $sArActions);

		if (!!$s_EVENT) {
			$row->AddField('TYPE_ID', (isset($arEmailType[$s_EVENT]) ? $arEmailType[$s_EVENT] . '<br><small>' . $s_EVENT . '</small>' : '*'. $s_EVENT));
		}
		else {
			$row->AddField('TYPE_ID', (isset($s_TP_NAME) ? $s_TP_NAME . '<br><small>' . $s_TP_CODE . '</small>' : $s_TYPE_ID));
		}


		$row->AddField('NAME', $s_NAME);
		$row->AddField('ACTIVE', GetMessage($BXMAKER_MODULE_ID . '_HEAD.ACTIVE_' . $s_ACTIVE));

		$row->AddField('TRANSLIT', GetMessage($BXMAKER_MODULE_ID . '_HEAD.TRANSLIT_' . $s_TRANSLIT));

		$row->AddField('EVENT', (!!$s_EVENT ? GetMessage($BXMAKER_MODULE_ID . '_HEAD.ACTIVE_' . 1) : GetMessage($BXMAKER_MODULE_ID . '_HEAD.ACTIVE_' . 0)));


		$row->AddField('SITE', implode('<br />', $arSID));

		$arActions   = Array();
        $arActions[] = array(
            "ICON"    => "edit",
            "TEXT"    => GetMessage($BXMAKER_MODULE_ID . '_MENU_EDIT'),
            "ACTION"  => $sAdmin->ActionRedirect($sSAPEdit . "?ID=" . $s_ID . "&lang=" . LANG . ""),
            "DEFAULT" => true
        );

        $arActions[] = array(
            "ICON"    => "copy",
            "TEXT"    => GetMessage($BXMAKER_MODULE_ID . '_MENU_COPY'),
            "ACTION"  => $sAdmin->ActionRedirect($sSAPEdit . "?COPY_ID=" . $s_ID . "&lang=" . LANG . ""),
            "DEFAULT" => true
        );

		$row->AddActions($arActions);

	}


	$sAdmin->AddFooter(
		array(
			array(
				"title" => GetMessage($BXMAKER_MODULE_ID . '_LIST_SELECTED'),
				"value" => $dbResultList->SelectedRowsCount()
			),
			array(
				"counter" => true,
				"title"   => GetMessage($BXMAKER_MODULE_ID . '_LIST_CHECKED'),
				"value"   => "0"
			),
		)
	);

	if (!$bReadOnly) {
		$sAdmin->AddGroupActionTable(
			array(
				"delete"     => GetMessage($BXMAKER_MODULE_ID . '_LIST_DELETE'),
				"active"     => GetMessage($BXMAKER_MODULE_ID . '_LIST_ACTIVE'),
				"deactivate" => GetMessage($BXMAKER_MODULE_ID . '_LIST_DEACTIVATE'),
			)
		);
	}


	$sAdmin->CheckListMode();
	$APPLICATION->SetTitle(GetMessage($BXMAKER_MODULE_ID . '_PAGE_LIST_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


	\Bxmaker\SmsNotice\Manager::getInstance()->showDemoMessage();
	\Bxmaker\SmsNotice\Manager::getInstance()->addAdminPageCssJs();


	$sMenu->Show();
	$sAdmin->DisplayList();

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>