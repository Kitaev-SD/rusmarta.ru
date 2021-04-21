<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$BXMAKER_MODULE_ID = "bxmaker.smsnotice";

\Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
\Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);


$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$oServiceTable = new \Bxmaker\SmsNotice\ServiceTable();
$oService = new \Bxmaker\SmsNotice\Service();

$PREMISION_DEFINE = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
if ($PREMISION_DEFINE == 'W') $bReadOnly = false;
else  $bReadOnly = true;

$sTableID = 'bxmaker_smsnotice_service_lsit';
$sCurPage = $APPLICATION->GetCurPage();
$sSAPEdit = $BXMAKER_MODULE_ID . '_service_edit.php';


$oSort = new CAdminSorting($sTableID, "SORT", "ASC");
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
$sMenu = new CAdminContextMenu($sContent);


// Массовые операции удаления ---------------------------------
if ($arID = $sAdmin->GroupAction()) {

    switch ($req->get('action')) {
        case "deactivate":
            foreach ($arID as $id) {
                $res = $oServiceTable->update($id, array('ACTIVE' => false));
            }
            break;
        case "active":
            foreach ($arID as $id) {
                $res = $oServiceTable->update($id, array('ACTIVE' => true));
            }
            if (count($arID) && intval($arID[count($arID) - 1]) > 0) {
                $dbr = $oServiceTable->getList(array(
                    'filter' => array('!ID' => $arID[count($arID) - 1], 'ACTIVE' => true)
                ));
                while ($ar = $dbr->fetch()) {
                    $res = $oServiceTable->update($ar['ID'], array('ACTIVE' => false));
                }
            }
            break;
    }

    if ($req->get('action_button')) {
        switch ($req->get('action_button')) {
            case "delete":
                $dbr = $oServiceTable->getList(array(
                    'filter' => array('ID' => $arID)
                ));
                while ($ar = $dbr->fetch()) {
                    $res = $oServiceTable->delete($ar['ID']);
                }
                break;
        }
    }


}


// сайты
$arSite = array();
$dbr = \CSite::GetList($by = 'sort', $order = 'asc');
while ($ar = $dbr->Fetch()) {
    $arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
}

$arServices = $oService->getArray();


// Сортировка ------------------------------
$by = 'ACTIVE';
if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'CODE', 'NAME', 'ACTIVE'))) $by = $_GET['by'];
$arOrder = array($by => ($_GET['order'] == 'ASC' ? 'ASC' : 'DESC'));

// Постраничная навигация ------------------
$navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize(
    $sTableID,
    array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())
));
$usePageNavigation = true;
if ($navyParams['SHOW_ALL']) {
    $usePageNavigation = false;
} else {
    $navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
    $navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
}

// Запрос -----------------------------------
$arQuery = array(
    'select' => array('*'),
    'order'  => $arOrder
);
if ($usePageNavigation) {
    $arQuery['limit'] = $navyParams['SIZEN'];
    $arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}

$dbResultList = $oServiceTable->getList($arQuery);

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
        "id"      => 'ACTIVE',
        "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.ACTIVE'),
        "sort"    => 'ACTIVE',
        "default" => true
    ),
    array(
        "id"      => 'NAME',
        "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.NAME'),
        "sort"    => 'NAME',
        "default" => true
    ),
    array(
        "id"      => 'CODE',
        "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.CODE'),
        "sort"    => 'CODE',
        "default" => true
    ),
    array(
        "id"      => 'SITE_ID',
        "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.SITE_ID'),
        "sort"    => 'SITE_ID',
        "default" => true
    )
));


while ($sArActions = $dbResultList->NavNext(true, 's_')) {

    $row = &$sAdmin->AddRow($s_ID, $sArActions);
    $row->AddField('CODE', (isset($arServices[$s_CODE]['NAME']) ? $arServices[$s_CODE]['NAME'] : $s_CODE));
    $row->AddField('NAME', $s_NAME);
    $row->AddField('ACTIVE', GetMessage($BXMAKER_MODULE_ID . '_HEAD.ACTIVE_' . $s_ACTIVE));
    $row->AddField('ID', $s_ID);
    $row->AddField('SITE_ID', (isset($arSite[$s_SITE_ID]) ? $arSite[$s_SITE_ID] : $s_SITE_ID));

    $arActions = Array();
    $arActions[] = array(
        "ICON"    => "edit",
        "TEXT"    => GetMessage($BXMAKER_MODULE_ID . '_MENU_EDIT'),
        "ACTION"  => $sAdmin->ActionRedirect($sSAPEdit . "?ID=" . $s_ID . "&lang=" . LANG . ""),
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