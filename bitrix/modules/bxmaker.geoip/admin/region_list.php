<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

	$MODULE_ID = 'bxmaker.geoip';

	use \Bitrix\Main\Localization\Loc as Loc;

	\Bitrix\Main\Loader::includeModule($MODULE_ID);

	Loc::loadMessages(__FILE__);

	$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);

	if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	if ($PREMISION_DEFINE == 'W') {
		$bReadOnly = false;
	}
	else  $bReadOnly = true;


	$sTableID = 'bxmaker_geoip_region_list_table';
	$sCurPage = $APPLICATION->GetCurPage();
	$sSAPEdit = $MODULE_ID . '_region_edit.php';


	$oManager = \Bxmaker\GeoIP\Manager::getInstance();
	$oCountry = new \Bxmaker\GeoIP\Location\CountryTable();
	$oRegion  = new \Bxmaker\GeoIP\Location\RegionTable();
	$oCity    = new \Bxmaker\GeoIP\Location\CityTable();
	$app = \Bitrix\Main\Application::getInstance();
	$req = $app->getContext()->getRequest();


	$oSort  = new CAdminSorting($sTableID, "ID", "DESC");
	$sAdmin = new CAdminList($sTableID, $oSort);

	// ��������� ��� ������
	$oMenu = new CAdminContextMenu(array(
		array(
			"TEXT"  => GetMessage($MODULE_ID . '_BNT_ADD'),
			"LINK"  => $MODULE_ID . '_region_edit.php?lang=' . LANG,
			"TITLE" => GetMessage($MODULE_ID . '_BNT_ADD'),
		),
	));


	//  ������
	$arCountryList = array(
		'REFERENCE_ID' => array(''),
		'REFERENCE'    => array(GetMessage($MODULE_ID . '_NO_SELECTED')),
	);
	$arCountryListId = array();

	$dbrCountry = $oCountry->getList();
	while ($arCountry = $dbrCountry->fetch()) {
		$arCountryList['REFERENCE_ID'][] = $arCountry['ID'];
		$arCountryList['REFERENCE'][]    = $arCountry['NAME'];
		$arCountryListId[$arCountry['ID']] = $arCountry['NAME'];
	}


	// �������� �������� �������� ---------------------------------
	if ($arID = $sAdmin->GroupAction()) {
		if (!$bReadOnly) {
			switch ($req->getPost('action_button')) {
				case "delete":
					foreach ($arID as $id) {
						$res = $oRegion->delete($id);
					}
					break;
			}
		}
	}

	// ���������� ����������������� ���������
	if($sAdmin->EditAction() && $PREMISION_DEFINE=="W")
	{
		// ������� �� ������ ���������� ���������
		foreach($FIELDS as $ID=>$arFields)
		{
			// ���� �������� ������ �� ���������� - �� ����� ��������� ������� ��������
			if(!$sAdmin->EditAction($ID))	continue;

			// �������� ��������� ������� ��������
			$DB->StartTransaction();
			$ID = intval($ID);
			if(($rsData = $oRegion->GetByID($ID)) && ($arData = $rsData->Fetch()))
			{
				foreach($arFields as $key=>$value)
					$arData[$key]=$value;

				$resUpdate = $oRegion->Update($ID, $arData);
				if(!$resUpdate->isSuccess())
				{
					$sAdmin->AddGroupError(implode(', ', $resUpdate->getErrorMessages()), $ID);
					$DB->Rollback();
				}
			}
			else
			{
				$sAdmin->AddGroupError(Loc::getMessage($MODULE_ID . '.ERROR_UPDATE_ROW'), $ID);
				$DB->Rollback();
			}
			$DB->Commit();
		}
	}


	/// ������ ----------------------------------------
	// �������� �������� ������� ��� �������� ������� � ��������� �������
	function CheckFilter()
	{
		global $FilterArr, $sAdmin;
		foreach ($FilterArr as $f) {
			global $$f;
		}

		/*	����� ��������� �������� ���������� $find_��� �, � ������ ������������� ������, �������� $sAdmin->AddFilterError("�����_������"). */

		return count($sAdmin->arFilterErrors) == 0; // ���� ������ ����, ������ false;
	}

	// ������ �������� �������
	$FilterArr = Array(
		"find_id",
		"find_name",
		"find_country",
	);

	// �������������� ������
	$sAdmin->InitFilter($FilterArr);

	// ���� ��� �������� ������� ���������, ���������� ���
	if (CheckFilter()) {
		// �������� ������ ���������� ��� ������� CRubric::GetList() �� ������ �������� �������
		$arFilter = Array();


		if (strlen(trim($find_id)) > 0) {
			$arFilter['ID'] = trim($find_id);
		}
		if (strlen(trim($find_name)) > 0) {
			$arFilter['NAME'] = trim($find_name);
		}
		if (strlen(trim($find_country)) > 0) {
			$arFilter['COUNTRY_ID'] = trim($find_country);
		}
	}


	// ���������� ------------------------------
	$by = 'ID';
	if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'NAME', 'NAME_EX', 'COUNTRY_ID'))) $by = $_GET['by'];
	$arOrder = array($by => (in_array($_GET['order'], array('asc','ASC')) ? 'ASC' : 'DESC'));


	// ������������ ��������� ------------------
	$navyParams        = CDBResult::GetNavParams(CAdminResult::GetNavSize(
		$sTableID,
		array('nPageSize' => 50, 'sNavID' => $APPLICATION->GetCurPage())
	));
	$usePageNavigation = true;
	if ($navyParams['SHOW_ALL']) {
		$usePageNavigation = false;
	}
	else {
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}

	// ������ -----------------------------------
	$arQuery = array(
		'select' => array('*'),
		'order'  => $arOrder,
		'filter' => $arFilter

	);
	if ($usePageNavigation) {

		$totalCount = 0;
		$totalPages = 0;
		$dbrCount   = $oRegion->getList(array(
			'select' => array('CNT'),
			'filter' => $arFilter
		));
		if ($ar = $dbrCount->fetch()) {
			$totalCount = $ar['CNT'];
		}

		if ($totalCount > 0) {
			$totalPages = ceil($totalCount / $navyParams['SIZEN']);
			if ($navyParams['PAGEN'] > $totalPages) {
				$navyParams['PAGEN'] = $totalPages;
			}
			$arQuery['limit']  = $navyParams['SIZEN'];
			$arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
		}
		else {
			$navyParams['PAGEN'] = 1;
			$arQuery['limit']    = $navyParams['SIZEN'];
			$arQuery['offset']   = 0;
		}
	}
	else {
		$totalCount = 0;
		$totalPages = 0;
	}

	$dbResultList = new CAdminResult($oRegion->getList($arQuery), $sTableID);
	if ($usePageNavigation) {
		$dbResultList->NavStart($arQuery['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
		$dbResultList->NavRecordCount = $totalCount;
		$dbResultList->NavPageCount   = $totalPages;
		$dbResultList->NavPageNomer   = $navyParams['PAGEN'];
	}
	else {
		$dbResultList->NavStart();
	}


	$sAdmin->NavText($dbResultList->GetNavPrint(GetMessage($MODULE_ID . '_PAGE_LIST_TITLE_NAV_TEXT')));

	$sAdmin->AddHeaders(array(
		array(
			"id"      => 'ID',
			"content" => GetMessage($MODULE_ID . '_HEAD.ID'),
			"sort"    => 'ID',
			"default" => true
		),
		array(
			"id"      => 'NAME',
			"content" => GetMessage($MODULE_ID . '_HEAD.NAME'),
			"sort"    => 'NAME',
			"default" => true
		),
		array(
			"id"      => 'NAME_EN',
			"content" => GetMessage($MODULE_ID . '_HEAD.NAME_EN'),
			"sort"    => 'NAME_EN',
			"default" => true
		),
		array(
			"id"      => 'COUNTRY_ID',
			"content" => GetMessage($MODULE_ID . '_HEAD.COUNTRY_ID'),
			"sort"    => 'COUNTRY_ID',
			"default" => true
		)

	));

	$arFields = array();
	while ($item = $dbResultList->fetch()) {

		$row = &$sAdmin->AddRow($item['ID'], $item);

		$row->AddField('ID', $item['ID']);

		$row->AddInputField('NAME', array('value' => $item['NAME']));

		$row->AddInputField('NAME_EN', array('value' => $item['NAME_EN']));

		$row->AddSelectField('COUNTRY_ID',  $arCountryListId, array('value' => $item['COUNTRY_ID']));


		//		$arActions = Array();
		//		$arActions[] = array(
		//			"ICON" => "edit",
		//			"TEXT" => GetMessage($MODULE_ID . '_LIST_EDIT'),
		//			"ACTION" => $sAdmin->ActionRedirect($sSAPEdit."?ID=".$item['ID']."&lang=".LANG.""),
		//			"DEFAULT" => true
		//		);
		//
		//
		//		$row->AddActions($arActions);

	}

	$sAdmin->AddFooter(
		array(
			array(
				"title" => GetMessage($MODULE_ID . '_LIST_SELECTED'),
				"value" => $dbResultList->SelectedRowsCount()
			),
			array(
				"counter" => true,
				"title"   => GetMessage($MODULE_ID . '_LIST_CHECKED'),
				"value"   => "0"
			),
		)
	);

	if (!$bReadOnly) {
		$sAdmin->AddGroupActionTable(
			array(
				"delete" => GetMessage($MODULE_ID . '_LIST_DELETE'),
			)
		);
	}


	$sAdmin->CheckListMode();
	$APPLICATION->SetTitle(GetMessage($MODULE_ID . '_PAGE_LIST_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


	// �������� ������ �������
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		array(
			"ID",
			GetMessage($MODULE_ID . "_HEAD.FILTER_NAME"),
			GetMessage($MODULE_ID . "_HEAD.FILTER_COUNTRY_ID")
		)
	);
?>
    <form name="find_form" method="get" action="<? echo $APPLICATION->GetCurPage(); ?>">
		<? $oFilter->Begin(); ?>
        <tr>
            <td><?= "ID" ?>:</td>
            <td>
                <input type="text" name="find_id" size="47" value="<? echo htmlspecialchars($find_id) ?>">
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_ID . "_HEAD.FILTER_NAME") ?>:</td>
            <td>
                <input type="text" name="find_name" size="47" value="<? echo htmlspecialchars($find_name) ?>">
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_ID . "_HEAD.FILTER_COUNTRY_ID") ?>:</td>
            <td>
				<?= SelectBoxFromArray('find_country', $arCountryList, $find_country); ?>
            </td>
        </tr>
		<?
			$oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
			$oFilter->End();
		?>
    </form>

<?

	//	$oMenu->Show();

	$sAdmin->DisplayList();

?>


<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>