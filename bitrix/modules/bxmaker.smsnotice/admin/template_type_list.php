<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$BXMAKER_MODULE_ID = "bxmaker.smsnotice";

\Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
\Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);

$oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();

$PREMISION_DEFINE = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
if ($PREMISION_DEFINE == 'W') $bReadOnly = false;
else  $bReadOnly = true;

$sTableID = 'bxmaker_smsnotice';
$sCurPage = $APPLICATION->GetCurPage();
$sSAPEdit = $BXMAKER_MODULE_ID . '_template_type_edit.php';


$oSort = new CAdminSorting($sTableID, "SORT", "ASC");
$sAdmin = new CAdminList($sTableID, $oSort);

// меню
$sContent = array(
    array(
        "TEXT" => GetMessage($BXMAKER_MODULE_ID . '_MENU_BTN_NEW_TITLE'),
        "LINK" => $sSAPEdit . "?lang=" . LANG,
        "TITLE" => GetMessage($BXMAKER_MODULE_ID . '_MENU_BTN_NEW_TITLE'),
        "ICON" => "btn_new",
    ),
);
$sMenu = new CAdminContextMenu($sContent);


// Массовые операции удаления ---------------------------------
if ($arID = $sAdmin->GroupAction()) {

    switch ($req->get('action_button')) {
        case "delete":
            foreach ($arID as $id) {
                $res = $oTemplateType->delete($id);
            }
            break;
    }
}


// Сортировка ------------------------------
$by = 'ID';
if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'NAME', 'CODE'))) $by = $_GET['by'];
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

$dbResultList = $oTemplateType->getList($arQuery);

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
        "id"      => 'CODE',
        "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.CODE'),
        "sort"    => 'CODE',
        "default" => true
    ),
//    array(
//        "id"      => 'COUNT',
//        "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.COUNT'),
//        "sort"    => 'COUNT',
//        "default" => false
//    )
));


while ($sArActions = $dbResultList->NavNext(true, 's_')) {

    $row = &$sAdmin->AddRow($s_ID, $sArActions);


    $arActions = Array();
    $arActions[] = array(
        "ICON" => "edit",
        "TEXT" => GetMessage($BXMAKER_MODULE_ID . '_MENU_EDIT'),
        "ACTION" => $sAdmin->ActionRedirect($sSAPEdit . "?ID=" . $s_ID . "&lang=" . LANG . ""),
        "DEFAULT" => true
    );
    $arActions[] = array(
        "ICON" => "copy",
        "TEXT" => GetMessage($BXMAKER_MODULE_ID . '_MENU_COPY'),
        "ACTION" => $sAdmin->ActionRedirect($sSAPEdit . "?COPY=" . $s_ID . "&lang=" . LANG . ""),
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
            "delete" => GetMessage($BXMAKER_MODULE_ID . '_LIST_DELETE'),
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