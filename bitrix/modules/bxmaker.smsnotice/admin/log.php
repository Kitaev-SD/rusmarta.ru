<?
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

    $BXMAKER_MODULE_ID = "bxmaker.smsnotice";


    use \Bitrix\Main\Type\DateTime as DateTime;

    CUtil::InitJSCore('jquery');

    \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
    \Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);

    $oManagerTable = new \Bxmaker\SmsNotice\ManagerTable();
    $oManager = \Bxmaker\SmsNotice\Manager::getInstance();
    $oLog = new \Bxmaker\SmsNotice\LogTable();

    $app = \Bitrix\Main\Application::getInstance();
    $req = $app->getContext()->getRequest();
    $asset = \Bitrix\Main\Page\Asset::getInstance();

    $PREMISION_DEFINE = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

    if ($PREMISION_DEFINE == "D")
        $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
    if ($PREMISION_DEFINE == 'W') {
        $bReadOnly = false;
    } else  $bReadOnly = true;


    $sTableID = 'bxmaker_smsnotice_log_table';
    $sCurPage = $APPLICATION->GetCurPage();

    $oSort = new CAdminSorting($sTableID, "ID", "DESC");
    $sAdmin = new CAdminList($sTableID, $oSort);

    // Массовые операции удаления ---------------------------------
    if ($arID = $sAdmin->GroupAction()) {
        $target =  $req->getPost('action_target');
        if($target == 'selected' && $req->getPost('action_button') == 'delete')
        {
            $oLog->deleteAll();
        }
        else
        {
            switch ($req->getPost('action_button')) {
                case "delete":
                    foreach ($arID as $id) {
                        $res = $oLog->delete($id);
                    }
                    break;
            }
        }
        
    }

    // сайты
    $arSite = array();
    $arSiteIdReference = array('REFERENCE_ID' => array(''), 'REFERENCE' => array(GetMessage($BXMAKER_MODULE_ID . '_FILTER_NO_SELECT')));
    $dbr = \CSite::GetList($by = 'sort', $order = 'asc');
    while ($ar = $dbr->Fetch()) {
        $arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];

        $arSiteIdReference['REFERENCE_ID'][] = $ar['ID'];
        $arSiteIdReference['REFERENCE'][] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
    }


    // Фильтр ----------------------------------------
    // проверку значений фильтра для удобства вынесем в отдельную функцию
    function CheckFilter()
    {
        global $FilterArr, $sAdmin;
        foreach ($FilterArr as $f) global $$f;

        /*	здесь проверяем значения переменных $find_имя и, в случае возникновения ошибки, вызываем $sAdmin->AddFilterError("текст_ошибки"). */

        return count($sAdmin->arFilterErrors) == 0; // если ошибки есть, вернем false;
    }

    // опишем элементы фильтра
    $FilterArr = Array("find_site_id", "find_date_insert", "find_type", "find_pid");

    // инициализируем фильтр
    $sAdmin->InitFilter($FilterArr);

    // если все значения фильтра корректны, обработаем его
    if (CheckFilter()) {
        $arFilter = Array();

        if (strlen(trim($find_site_id)) > 0 && in_array(trim($find_site_id), $arSiteIdReference['REFERENCE_ID'])) {
            $arFilter['SITE_ID'] = trim($find_site_id);
        }

        if (strlen(trim($find_date_from)) > 0 || strlen(trim($find_date_to)) > 0) {
            if (strlen(trim($find_date_from)) > 0 && strlen(trim($find_date_to)) > 0) {
                $dateStart = new DateTime($find_date_from);
                $dateStart->setTime(0, 0, 0);
                $dateStop = new DateTime($find_date_to);
                $dateStop->setTime(23, 23, 59);
                $arFilter['><DATE_INSERT'] = array($dateStart, $dateStop);
            } elseif (strlen(trim($find_date_from)) > 0) {
                $dateStart = new DateTime($find_date_from);
                $dateStart->setTime(0, 0, 0);

                $arFilter['>DATE_INSERT'] = $dateStart;
            } elseif (strlen(trim($find_date_to)) > 0) {
                $dateStop = new DateTime($find_date_to);
                $dateStop->setTime(23, 23, 59);
                $arFilter['<DATE_INSERT'] = $dateStop;
            }
        }

        if (strlen(trim($find_type)) > 0) {
            $arFilter['TYPE'] = $find_type;
        }

        if (strlen(trim($find_pid)) > 0) {
            $arFilter['PID'] = intval($find_pid);
        }
    }

    // Сортировка ------------------------------
    $by = 'ID';
    if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'PID', 'DATE_INSERT', 'SITE_ID', 'TYPE')))
        $by = $_GET['by'];
    $arOrder = array($by => ($_GET['order'] == 'ASC' ? 'ASC' : 'DESC'));


    // Постраничная навигация ------------------
    $navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize($sTableID, array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())));
    $usePageNavigation = true;
    if ($navyParams['SHOW_ALL']) {
        $usePageNavigation = false;
    } else {
        $navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
        $navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
    }


    // Запрос -----------------------------------
    $arQuery = array('select' => array('*'), 'order' => $arOrder, 'filter' => $arFilter);
    if ($usePageNavigation) {

        $totalCount = 0;
        $totalPages = 0;
        $dbrCount = $oLog->getList(array('select' => array('CNT'), 'filter' => $arFilter));
        if ($ar = $dbrCount->fetch()) {
            $totalCount = $ar['CNT'];
        }

        if ($totalCount > 0) {
            $totalPages = ceil($totalCount / $navyParams['SIZEN']);
            if ($navyParams['PAGEN'] > $totalPages) {
                $navyParams['PAGEN'] = $totalPages;
            }
            $arQuery['limit'] = $navyParams['SIZEN'];
            $arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
        } else {
            $navyParams['PAGEN'] = 1;
            $arQuery['limit'] = $navyParams['SIZEN'];
            $arQuery['offset'] = 0;
        }
    }

    $resultQuery = $oLog->getList($arQuery);
    $dbResultList = new CAdminResult($resultQuery, $sTableID);
    if ($usePageNavigation) {
        $dbResultList->NavStart($arQuery['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
        $dbResultList->NavRecordCount = $totalCount;
        $dbResultList->NavPageCount = $totalPages;
        $dbResultList->NavPageNomer = $navyParams['PAGEN'];
    } else {
        $dbResultList->NavStart();
    }

    $sAdmin->NavText($dbResultList->GetNavPrint(GetMessage($BXMAKER_MODULE_ID . '_PAGE_LIST_TITLE_NAV_TEXT')));

    $sAdmin->AddHeaders(array(array("id" => 'ID', "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.ID'), "sort" => 'ID', "default" => true), array("id" => 'PID', "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.PID'), "sort" => 'PID', "default" => true), array("id" => 'SITE_ID', "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.SITE_ID'), "sort" => 'SITE_ID', "default" => true), array("id" => 'DATE_INSERT', "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.DATE_INSERT'), "sort" => 'DATE_INSERT', "default" => true), array("id" => 'TYPE', "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.TYPE'), "sort" => 'TYPE', "default" => true), array("id" => 'TEXT', "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.TEXT'), "sort" => '', "default" => true),

    ));


    while ($arItem = $dbResultList->NavNext()) {

        $text = htmlspecialcharsbx($arItem['TEXT']);
        $textHtml = '<div class="bxmaker-smsnotice-log__preview">' . TruncateText($text, 300) . '</div>';
        $textHtml .= '<div class="bxmaker-smsnotice-log__text">' . $text . '</div>';

        if (strlen($text) > 300) {
            $textHtml .= '<span class="bxmaker-smsnotice-log__more" data-show="' . GetMessage($BXMAKER_MODULE_ID . '_MORE_SHOW') . '" data-hide="' . GetMessage($BXMAKER_MODULE_ID . '_MORE_HIDE') . '">' . GetMessage($BXMAKER_MODULE_ID . '_MORE_SHOW') . '</span>';
        }

        $row = &$sAdmin->AddRow($arItem['ID'], $arItem);
        $row->AddField('PID', $arItem['PID']);
        $row->AddField('TYPE', $arItem['TYPE']);
        $row->AddField('TEXT', $textHtml);
        $row->AddField('DATE_INSERT', $arItem['DATE_INSERT']);
        $row->AddField('SITE_ID', (isset($arSite[$arItem['SITE_ID']]) ? $arSite[$arItem['SITE_ID']] : ''));
        $row->AddField('ID', $arItem['ID']);

    }


    $sAdmin->AddFooter(array(array("title" => GetMessage($BXMAKER_MODULE_ID . '_LIST_SELECTED'), "value" => $dbResultList->SelectedRowsCount()), array("counter" => true, "title" => GetMessage($BXMAKER_MODULE_ID . '_LIST_CHECKED'), "value" => "0"),));

    if (!$bReadOnly) {
        $sAdmin->AddGroupActionTable(array("delete" => GetMessage($BXMAKER_MODULE_ID . '_LIST_DELETE'),));
    }


    $sAdmin->CheckListMode();
    $APPLICATION->SetTitle(GetMessage($BXMAKER_MODULE_ID . '_PAGE_LIST_TITLE'));

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


    \Bxmaker\SmsNotice\Manager::getInstance()->showDemoMessage();
    \Bxmaker\SmsNotice\Manager::getInstance()->addAdminPageCssJs();


    // создадим объект фильтра
    $oFilter = new CAdminFilter($sTableID . "_filter", array(GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_SITE_ID"), GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_DATE_INSERT"), GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_TYPE"), GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_PID")));

?>

    <form name="find_form" method="get" action="<? echo $APPLICATION->GetCurPage(); ?>">
        <? $oFilter->Begin(); ?>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_SITE_ID") ?>:</td>
            <td>
                <?
                    echo SelectBoxFromArray("find_site_id", $arSiteIdReference, $find_site_id);
                ?>
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_DATE_INSERT") ?>:</td>
            <td>
                <? echo CalendarPeriod("find_date_from", $find_date_from, "find_date_to", $find_date_to, "find_form", "Y") ?>
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_TYPE") ?>:</td>
            <td>
                <?
                    echo InputType("text", "find_type", $find_type, '');
                ?>
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_PID") ?>:</td>
            <td>
                <?
                    echo InputType("text", "find_pid", $find_pid, '');
                ?>
            </td>
        </tr>
        <?
            $oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
            $oFilter->End();
        ?>
    </form>


    <div class="bxmaker__smsnotice__log-list">
        <?
            $sAdmin->DisplayList();
        ?>
    </div>


<?
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>