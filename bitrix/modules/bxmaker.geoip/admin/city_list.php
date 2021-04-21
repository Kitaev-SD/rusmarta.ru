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


	$sTableID = 'bxmaker_geoip_loc_city_list_table';
	$sCurPage = $APPLICATION->GetCurPage();
	$sSAPEdit = $MODULE_ID . '_city_edit.php';


	$oManager = \Bxmaker\GeoIP\Manager::getInstance();
	$oCity    = new \Bxmaker\GeoIP\Location\CityTable();
	$oCountry = new \Bxmaker\GeoIP\Location\CountryTable();
	$app      = \Bitrix\Main\Application::getInstance();
	$req      = $app->getContext()->getRequest();


	$oSort  = new CAdminSorting($sTableID, "ID", "DESC");
	$sAdmin = new CAdminList($sTableID, $oSort);

	// Навигация над формой
	$oMenu = new CAdminContextMenu(array(
		array(
			"TEXT"  => GetMessage($MODULE_ID . '_BNT_ADD'),
			"LINK"  => $MODULE_ID . '_city_edit.php?lang=' . LANG,
			"TITLE" => GetMessage($MODULE_ID . '_BNT_ADD'),
		),
	));

	//страны
	$arCountryReference = array(
		'REFERENCE_ID' => array(''),
		'REFERENCE'    => array(GetMessage($MODULE_ID . '_COUNTRY_DEFAULT'))
	);
	$dbrCountry         = $oCountry->getList(array(
		'order' => array(
			'NAME' => 'ASC'
		)
	));
	while ($arCountry = $dbrCountry->fetch()) {
		$arCountryReference['REFERENCE_ID'][] = $arCountry['ID'];
		$arCountryReference['REFERENCE'][]    = $arCountry['NAME'];
	}


	// Массовые операции удаления ---------------------------------
	if ($arID = $sAdmin->GroupAction()) {
		if (!$bReadOnly) {
			switch ($req->getPost('action_button')) {
				case "delete":
					foreach ($arID as $id) {
						$res = $oCity->delete($id);
					}
					break;
			}
		}
	}


	/// Фильтр ----------------------------------------
	// проверку значений фильтра для удобства вынесем в отдельную функцию
	function CheckFilter()
	{
		global $FilterArr, $sAdmin;
		foreach ($FilterArr as $f) {
			global $$f;
		}

		/*	здесь проверяем значения переменных $find_имя и, в случае возникновения ошибки, вызываем $sAdmin->AddFilterError("текст_ошибки"). */

		return count($sAdmin->arFilterErrors) == 0; // если ошибки есть, вернем false;
	}

	// опишем элементы фильтра
	$FilterArr = Array(
		"find_id",
		"find_name",
		"find_country",
		"find_region",
		"find_area",
	);

	// инициализируем фильтр
	$sAdmin->InitFilter($FilterArr);

	// если все значения фильтра корректны, обработаем его
	if (CheckFilter()) {
		// создадим массив фильтрации для выборки CRubric::GetList() на основе значений фильтра
		$arFilter = Array();


		if (intval($find_id) > 0) {
			$arFilter['ID'] = intval($find_id);
		}

		if (strlen(trim($find_name)) > 0) {
			$arFilter['%=NAME'] = trim($find_name);
		}
		if (intval($find_country_id) > 0) {
			$arFilter['COUNTRY_ID'] = intval($find_country_id);
		}
		if (strlen(trim($find_region)) > 0) {
			$arFilter['%REGION.NAME'] = '%' . trim($find_region) . '%';
		}
		if (strlen(trim($find_area)) > 0) {
			$arFilter['%=AREA'] = '%' . trim($find_area) . '%';
		}
	}


	// Сортировка ------------------------------
	$by = 'ID';
	if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'NAME', 'COUNTRY.NAME', 'REGION.NAME', 'AREA'))) $by = $_GET['by'];
	$arOrder = array($by => (in_array($_GET['order'], array('asc', 'ASC')) ? 'ASC' : 'DESC'));


	// Постраничная навигация ------------------
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

	// Запрос -----------------------------------
	$arQuery = array(
		'select' => array('*', 'C_' => 'COUNTRY.*', 'R_' => 'REGION.*'),
		'order'  => $arOrder,
		'filter' => $arFilter

	);
	if ($usePageNavigation) {

		$totalCount = 0;
		$totalPages = 0;
		$dbrCount   = $oCity->getList(array(
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


	$dbResultList = new CAdminResult($oCity->getList($arQuery), $sTableID);
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
			"sort"    => 'COUNTRY.NAME',
			"default" => true
		),
		array(
			"id"      => 'REGION',
			"content" => GetMessage($MODULE_ID . '_HEAD.REGION'),
			"sort"    => 'REGION.NAME',
			"default" => true
		),
		array(
			"id"      => 'AREA',
			"content" => GetMessage($MODULE_ID . '_HEAD.AREA'),
			"sort"    => 'AREA',
			"default" => true
		)

	));

	$arFields = array();
	while ($item = $dbResultList->fetch()) {

		$row = &$sAdmin->AddRow($item['ID']);


		$row->AddField('ID', $item['ID']);
		$row->AddField('NAME', $item['NAME']);
		$row->AddField('NAME_EN', $item['NAME_EN']);
		$row->AddField('COUNTRY_ID', $item['C_NAME']);
		$row->AddField('REGION', $item['R_NAME']);
		$row->AddField('AREA', $item['AREA']);


		//		$arActions   = Array();
		//		$arActions[] = array(
		//			"ICON"    => "edit",
		//			"TEXT"    => GetMessage($MODULE_ID . '_LIST_EDIT'),
		//			"ACTION"  => $sAdmin->ActionRedirect($sSAPEdit . "?ID=" . $item['ID'] . "&lang=" . LANG . ""),
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


	// создадим объект фильтра
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		array(
			GetMessage($MODULE_ID . "_HEAD.FILTER_NAME"),
			GetMessage($MODULE_ID . "_HEAD.FILTER_REGION"),
			GetMessage($MODULE_ID . "_HEAD.FILTER_AREA"),
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
            <td><?= GetMessage($MODULE_ID . "_HEAD.FILTER_REGION") ?>:</td>
            <td>
                <input type="text" name="find_region" size="47" value="<? echo htmlspecialchars($find_region) ?>">
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_ID . "_HEAD.FILTER_AREA") ?>:</td>
            <td>
                <input type="text" name="find_area" size="47" value="<? echo htmlspecialchars($find_area) ?>">
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_ID . "_HEAD.FILTER_COUNTRY_ID") ?>:</td>
            <td>
				<?
					echo SelectBoxFromArray("find_country_id", $arCountryReference, $find_country_id);
				?>
            </td>
        </tr>


		<?
			$oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
			$oFilter->End();
		?>
    </form>

<?

	//$oMenu->Show();

	$sAdmin->DisplayList();

?>


<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>