<?
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

    $BXMAKER_MODULE_ID = "bxmaker.smsnotice";

    \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
    \Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);

    CUtil::InitJSCore('jquery');

    $oManagerTable = new \Bxmaker\SmsNotice\ManagerTable();
    $oManager = \Bxmaker\SmsNotice\Manager::getInstance();
    $oTemplate = new \Bxmaker\SmsNotice\TemplateTable();
    $oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
    $app = \Bitrix\Main\Application::getInstance();
    $req = $app->getContext()->getRequest();
    $asset = \Bitrix\Main\Page\Asset::getInstance();

    $dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(dirname(__FILE__)));
    $bUtf = $oManager::isUTF();

    $PREMISION_DEFINE = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

    if ($PREMISION_DEFINE == "D")
        $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
    if ($PREMISION_DEFINE == 'W') {
        $bReadOnly = false;
    } else  $bReadOnly = true;

    $bAjax = false;
    if ($req->isAjaxRequest() && $req->getPost('method') && $req->getPost('method') == 'get_content') {
        $bAjax = true;
    }

    if ($req->isPost() && check_bitrix_sessid('sessid') && $req->getPost('method') && $req->getPost('method') != 'get_content') {


        $arJson = array(
            'error'    => array(),
            'response' => array()
        );


        switch ($req->getPost('method')) {
            case 'send_sms':
                {


                    $arPhone = $oManager->getMultyPhone($req->getPost('phone'));

                    if (!$arPhone) {
                        $arJson['error'] = array(
                            'code' => 'invalid_phone',
                            'msg'  => GetMessage($BXMAKER_MODULE_ID . '.AJAX.INVALID_PHONE'),
                            'more' => array()
                        );
                        break;
                    }


                    $text = trim($oManager->restoreEncoding($req->getPost('text')));

                    if (!$text || \Bitrix\Main\Text\BinaryString::getLength($text) <= 0) {
                        $arJson['error'] = array(
                            'code' => 'invalid_text',
                            'msg'  => GetMessage($BXMAKER_MODULE_ID . '.AJAX.INVALID_TEXT'),
                            'more' => array()
                        );
                        break;
                    }


                    $arErrorsAll = array();
                    foreach ($arPhone as $phone) {

                        /**
                         * @var \Bxmaker\SmsNotice\Result $result
                         */
                        $result = $oManager->send($phone, $text);
                        if ($result->isSuccess()) {

                            switch ($result->getResult()) {
                                case \Bxmaker\SmsNotice\SMS_STATUS_SENT:
                                    {
                                        $arJson['response'][ $phone ] = GetMessage($BXMAKER_MODULE_ID . '.AJAX.SMS_STATUS_SENT') . ' ' . date('H:i:s');
                                        break;
                                    }
                                case \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED:
                                    {
                                        $arJson['response'][ $phone ] = GetMessage($BXMAKER_MODULE_ID . '.AJAX.SMS_STATUS_DELIVERED') . ' ' . date('H:i:s');
                                        break;
                                    }
                                case \Bxmaker\SmsNotice\SMS_STATUS_ERROR:
                                    {
                                        $arJson['response'][ $phone ] = $result->getMore('error_description');
                                        break;
                                    }
                                case \Bxmaker\SmsNotice\SMS_STATUS_WAIT:
                                    {
                                        $arJson['response'][ $phone ] = GetMessage($BXMAKER_MODULE_ID . '.AJAX.SMS_STATUS_WAIT') . ' ' . date('H:i:s');
                                        break;
                                    }
                            }
                        } else {
                            /**
                             * @var \Bxmaker\SmsNotice\Error $error
                             */
                            $arErrors = $result->getErrors();
                            foreach ($arErrors as $error) {
                                $arJson['response']['errors'][ $phone ] = $error->getMessage();
                            }
                        }
                    }

                    break;
                }
            case 'filter_phone':
                {

                    if (!\Bitrix\Main\Loader::includeModule('sale')) {
                        $arJson['error'] = array(
                            'code' => 'module_not_found',
                            'msg'  => GetMessage($BXMAKER_MODULE_ID . '.AJAX.GET_PHONE.MODULE_SALE_NOT_FOUND'),
                            'more' => array()
                        );
                        break;
                    }

                    $arFilter = array();

                    if (strlen(trim($req->getPost('order_payed'))) && in_array($req->getPost('order_payed'), array(
                            '1',
                            '0'
                        ))) {
                        $arFilter['PAYED'] = $req->getPost('order_payed');
                    }

                    if (strlen(trim($req->getPost('order_canceled'))) && in_array($req->getPost('order_canceled'), array(
                            '1',
                            '0'
                        ))) {
                        $arFilter['CANCELED'] = $req->getPost('order_canceled');
                    }


                    if (strlen(trim($req->getPost('date_from'))) > 0) {
                        $arFilter['>DATE_INSERT'] = $req->getPost('date_from');
                    }

                    if (strlen(trim($req->getPost('date_to'))) > 0) {
                        $arFilter['<DATE_INSERT'] = $req->getPost('date_to');
                    }


                    $arOrderID = array();
                    $oPropValue = new \CSaleOrderPropsValue();
                    $oOrder = new \Bitrix\Sale\Internals\OrderTable();
                    $dbrOrder = $oOrder->getList(array(
                        'filter' => $arFilter,
                        'select' => array(
                            'ID',
                            'DATE_INSERT',
                            'PAYED',
                            'CANCELED',
                            'PERSON_TYPE_ID'
                        )
                    ));
                    while ($arOrder = $dbrOrder->fetch()) {
                        //разбиваем по типам плательщиков
                        $arOrderID[ $arOrder['PERSON_TYPE_ID'] ][] = $arOrder['ID'];
                    }


                    $arPhones = array();
                    foreach ($arOrderID as $ptid => $arId) {
                        if (count($arId) && strlen($oManager->getParam('HANDLER.PERSON_TYPE_' . $ptid, ''))) {

                            $dbrProp = $oPropValue->GetList(array( "SORT" => "ASC" ), array(
                                "ORDER_ID" => $arId,
                                "CODE"     => trim($oManager->getParam('HANDLER.PERSON_TYPE_' . $ptid))
                            ));

                            while ($arProp = $dbrProp->fetch()) {

                                if (strlen($arProp['VALUE'])) {
                                    $arPhones[] = $arProp['VALUE'];
                                }
                            }
                        }
                    }
                    $arPhones = array_unique($arPhones);


                    if (!empty($arPhones)) {
                        $arJson['response']['phones'] = implode(PHP_EOL, $arPhones);
                    } else {
                        $arJson['response']['phones'] = GetMessage($BXMAKER_MODULE_ID . '.AJAX.GET_PHONE.NOT_FOUND');
                    }

                    break;
                }
        }

        $oManager->getBase()->showJson($arJson);
    }


    $sTableID = 'bxmaker_smsnotice_list_table';
    $sCurPage = $APPLICATION->GetCurPage();
    $sSAPEdit = $BXMAKER_MODULE_ID . '_edit.php';


    $oSort = new CAdminSorting($sTableID, "SORT", "ASC");
    $sAdmin = new CAdminList($sTableID, $oSort);

    // меню

    // Массовые операции удаления ---------------------------------
    if ($arID = $sAdmin->GroupAction()) {
        $target =  $req->getPost('action_target');
        if($target == 'selected' && $req->getPost('action_button') == 'delete')
        {
            $oManagerTable->deleteAll();
        }
        else
        {
            switch ($req->getPost('action_button')) {
                case "delete":
                    foreach ($arID as $id) {
                        $res = $oManagerTable->delete($id);
                    }
                    break;
            }
        }
        
    }


    // сайты
    $arSite = array();
    $arSiteIdReference = array(
        'REFERENCE_ID' => array( '' ),
        'REFERENCE'    => array( GetMessage($BXMAKER_MODULE_ID . '_FILTER_NO_SELECT') )
    );
    $dbr = \CSite::GetList($by = 'sort', $order = 'asc');
    while ($ar = $dbr->Fetch()) {
        $arSite[ $ar['ID'] ] = '[' . $ar['ID'] . '] ' . $ar['NAME'];

        $arSiteIdReference['REFERENCE_ID'][] = $ar['ID'];
        $arSiteIdReference['REFERENCE'][] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
    }


    $arTypeIdReference = array(
        'REFERENCE_ID' => array( '' ),
        'REFERENCE'    => array( GetMessage($BXMAKER_MODULE_ID . '_FILTER_NO_SELECT') )
    );
    $dbrType = $oTemplateType->getList(array(
        'order' => array(
            'NAME' => 'ASC'
        )
    ));
    while ($arType = $dbrType->Fetch()) {
        $arTypeIdReference['REFERENCE_ID'][] = $arType['ID'];
        $arTypeIdReference['REFERENCE'][] = $arType['NAME'] . ' (' . $arType['CODE'] . ')' . ' [' . $arType['ID'] . ']';
    }

    $arStatusReference = array(
        'REFERENCE'    => array(
                GetMessage($BXMAKER_MODULE_ID . '_FILTER_NO_SELECT'),
                $oManager->getMsg('SMS_STATUS_SENT'),
                $oManager->getMsg('SMS_STATUS_DELIVERED'),
                $oManager->getMsg('SMS_STATUS_WAIT'),
                $oManager->getMsg('SMS_STATUS_ERROR'),
        ),
        'REFERENCE_ID' => array(
                '',
                \Bxmaker\SmsNotice\SMS_STATUS_SENT,
                \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED,
                \Bxmaker\SmsNotice\SMS_STATUS_WAIT,
                \Bxmaker\SmsNotice\SMS_STATUS_ERROR,
        ),
    );

    //шаблоны
    $arSiteData = $oManager->getSiteData();
    $arTemplate = array(
        'REFERENCE'    => array( GetMessage($BXMAKER_MODULE_ID . '_FORM.TEMPLATE_PLACEHOLDER') ),
        'REFERENCE_ID' => array( 0 )
    );
    $arTemplateData = array();
    $dbrTemplate = $oTemplate->getList(array(
        'order'  => array(
            'NAME' => 'ASC'
        ),
        'select' => array(
            '*',
            'TYPE' => 'TYPE.*'
        ),
        'filter' => array(
            'SITE.SID'  => $oManager->getCurrentSiteId(),
            '!=TYPE_ID' => 0
        )
    ));
    while ($ar = $dbrTemplate->fetch()) {
        $arTemplate['REFERENCE'][] = $ar['NAME'] . ' (' . $ar['TYPECODE'] . ') ' . '[' . $ar['ID'] . ']';
        $arTemplate['REFERENCE_ID'][] = $ar['ID'];

        $ar['TYPEDESCR'] = GetMessage($BXMAKER_MODULE_ID . '.AJAX.TEMPLATE_TYPE_FIELD_SITE_NAME') . "\n" . GetMessage($BXMAKER_MODULE_ID . '.AJAX.TEMPLATE_TYPE_FIELD_SERVER_NAME') . "\n" . $ar['TYPEDESCR'];
        $ar['TYPEVALUE']['#SITE_NAME#'] = (isset($arSiteData['SITE_NAME']) ? $arSiteData['SITE_NAME'] : '');
        $ar['TYPEVALUE']['#SERVER_NAME#'] = (isset($arSiteData['SERVER_NAME']) ? $arSiteData['SERVER_NAME'] : '');

        $arTemplateData[ $ar['ID'] ] = $ar;

    }

    $arEmailType = array();
    $dbrEventType = \CEventType::GetList(array( 'LID' => LANGUAGE_ID ), array( 'SORT' => 'ASC' ));
    while ($arEventType = $dbrEventType->Fetch()) {
        $arEmailType[ $arEventType['EVENT_NAME'] ] = '[' . $arEventType['EVENT_NAME'] . ']' . $arEventType['NAME'];
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
    $FilterArr = Array(
        "find_site_id",
        "find_created",
        "find_status",
        "find_type",
        "find_phone"
    );
    $arFilter = array();

    // инициализируем фильтр
    $sAdmin->InitFilter($FilterArr);

    // если все значения фильтра корректны, обработаем его
    if (CheckFilter()) {
        $arFilter = Array();

        if (strlen(trim($find_site_id)) > 0 && in_array(trim($find_site_id), $arSiteIdReference['REFERENCE_ID'])) {
            $arFilter['SITE_ID'] = trim($find_site_id);
        }

        if (strlen(trim($find_created_from)) > 0 || strlen(trim($find_created_to)) > 0) {
            if (strlen(trim($find_created_from)) > 0 && strlen(trim($find_created_to)) > 0) {
                $dateStart = new \Bitrix\Main\Type\DateTime($find_created_from);
                $dateStart->setTime(0, 0, 0);
                $dateStop = new \Bitrix\Main\Type\DateTime($find_created_to);
                $dateStop->setTime(23, 23, 59);
                $arFilter['><CREATED'] = array(
                    $dateStart,
                    $dateStop
                );
            } elseif (strlen(trim($find_created_from)) > 0) {
                $dateStart = new \Bitrix\Main\Type\DateTime($find_created_from);
                $dateStart->setTime(0, 0, 0);

                $arFilter['>CREATED'] = $dateStart;
            } elseif (strlen(trim($find_created_to)) > 0) {
                $dateStop = new \Bitrix\Main\Type\DateTime($find_created_to);
                $dateStop->setTime(23, 23, 59);
                $arFilter['<CREATED'] = $dateStop;
            }
        }


        if (strlen(trim($find_type)) > 0 && in_array($find_type, $arTypeIdReference['REFERENCE_ID'])) {
            $arFilter['TYPE_ID'] = $find_type;
        }

        if (strlen(trim($find_status)) > 0 && in_array($find_status, $arStatusReference['REFERENCE_ID'])) {
            $arFilter['STATUS'] = $find_status;
        }

        if (strlen(trim($find_phone)) > 0) {
            $arFilter['PHONE'] = $oManager->getPreparePhone($find_phone);
        }

    }

    // Сортировка ------------------------------
    $by = 'ID';
    if (isset($_GET['by']) && in_array($_GET['by'], array(
            'ID',
            'PHONE',
            'STATUS',
            'CREATED'
        )))
        $by = $_GET['by'];
    $arOrder = array( $by => ($_GET['order'] == 'ASC' ? 'ASC' : 'DESC') );


    // Постраничная навигация ------------------
    $navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize($sTableID, array(
        'nPageSize' => 20,
        'sNavID'    => $APPLICATION->GetCurPage()
    )));
    $usePageNavigation = true;
    if ($navyParams['SHOW_ALL']) {
        $usePageNavigation = false;
    } else {
        $navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
        $navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
    }


    // Запрос -----------------------------------
    $arQuery = array(
        'select' => array(
            '*',
            'TYPE_NAME' => 'TYPE.NAME'
        ),
        'order'  => $arOrder,
        'filter' => $arFilter
    );
    if ($usePageNavigation) {

        $totalCount = 0;
        $totalPages = 0;
        $dbrCount = $oManagerTable->getList(array(
            'select' => array( 'CNT' ),
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
            $arQuery['limit'] = $navyParams['SIZEN'];
            $arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
        } else {
            $navyParams['PAGEN'] = 1;
            $arQuery['limit'] = $navyParams['SIZEN'];
            $arQuery['offset'] = 0;
        }
    }

    $dbResultList = new CAdminResult($oManagerTable->getList($arQuery), $sTableID);
    if ($usePageNavigation) {
        $dbResultList->NavStart($arQuery['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
        $dbResultList->NavRecordCount = $totalCount;
        $dbResultList->NavPageCount = $totalPages;
        $dbResultList->NavPageNomer = $navyParams['PAGEN'];
    } else {
        $dbResultList->NavStart();
    }


    $sAdmin->NavText($dbResultList->GetNavPrint(GetMessage($BXMAKER_MODULE_ID . '_PAGE_LIST_TITLE_NAV_TEXT')));

    $sAdmin->AddHeaders(array(
        array(
            "id"      => 'ID',
            "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.ID'),
            "sort"    => 'ID',
            "default" => true
        ),
        array(
            "id"      => 'PHONE',
            "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.PHONE'),
            "sort"    => 'PHONE',
            "default" => true
        ),
        array(
            "id"      => 'STATUS',
            "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.STATUS'),
            "sort"    => 'STATUS',
            "default" => true
        ),
        array(
            "id"      => 'TEXT',
            "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.TEXT'),
            "sort"    => 'TEXT',
            "default" => true
        ),
        array(
            "id"      => 'SITE_ID',
            "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.SITE_ID'),
            "sort"    => 'SITE_ID',
            "default" => true
        ),
        array(
            "id"      => 'CREATED',
            "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.CREATED'),
            "sort"    => 'CREATED',
            "default" => true
        ),
        array(
            "id"      => 'COMMENT',
            "content" => GetMessage($BXMAKER_MODULE_ID . '_HEAD.COMMENT'),
            "sort"    => 'COMMENT',
            "default" => true
        ),


    ));


    while ($sArActions = $dbResultList->NavNext(true, 's_')) {


        $row = &$sAdmin->AddRow($s_ID, $sArActions);
        $row->AddField('PHONE', $s_PHONE);

        $error_status = '';
        switch ($s_STATUS) {
            case \Bxmaker\SmsNotice\SMS_STATUS_ERROR:
                {

                    if (isset($s_PARAMS['error_description'])) {
                        $error_status .= '<br>' . $s_PARAMS['error_description'];
                    }
                    break;
                }
        }
        $row->AddField('STATUS', GetMessage($BXMAKER_MODULE_ID . '_HEAD.STATUS_' . $s_STATUS) . $error_status);

        if ($s_TYPE_ID) {
            $row->AddField('TEXT', ($s_TYPE_NAME ? '<small><b>' . $s_TYPE_NAME . '</b></small><br>' : '') . $s_TEXT);
        } elseif ($s_EVENT) {
            $row->AddField('TEXT', ($arEmailType[ $s_EVENT ] ? '<small><b>' . $arEmailType[ $s_EVENT ] . '</b></small><br>' : '') . $s_TEXT);
        } else {
            $row->AddField('TEXT', $s_TEXT);
        }

        $errorString = '';

        if (strlen(trim($s_COMMENT)) > 0) {
            $errorString = '<div class="bxmaker__smsnotice__send-list-error" > <span>' . TruncateText($s_COMMENT, 30) . '</span><br><span class="bxmaker__smsnotice__send-list-error-btn">' . GetMessage($BXMAKER_MODULE_ID . '_SHOW_ERROR_DETAIL_TEXT') . '</span><div class="more">' . $s_COMMENT . '</div></div>';
        }


        $row->AddField('CREATED', $s_CREATED);
        $row->AddField('COMMENT', $errorString);
        $row->AddField('SITE_ID', (isset($arSite[ $s_SITE_ID ]) ? $arSite[ $s_SITE_ID ] : ''));
        $row->AddField('ID', $s_ID);

    }


    $sAdmin->AddFooter(array(
        array(
            "title" => GetMessage($BXMAKER_MODULE_ID . '_LIST_SELECTED'),
            "value" => $dbResultList->SelectedRowsCount()
        ),
        array(
            "counter" => true,
            "title"   => GetMessage($BXMAKER_MODULE_ID . '_LIST_CHECKED'),
            "value"   => "0"
        ),
    ));

    if (!$bReadOnly) {
        $sAdmin->AddGroupActionTable(array(
            "delete" => GetMessage($BXMAKER_MODULE_ID . '_LIST_DELETE'),
        ));
    }


    $sAdmin->CheckListMode();
    $APPLICATION->SetTitle(GetMessage($BXMAKER_MODULE_ID . '_PAGE_LIST_TITLE'));

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


    \Bxmaker\SmsNotice\Manager::getInstance()->showDemoMessage();
    \Bxmaker\SmsNotice\Manager::getInstance()->addAdminPageCssJs();

    $arServiceParam = $oManager->getServiceParam();
    $balance = $oManager->getBalance();


    $arSelectYesNoEmpty = array(
        'REFERENCE'    => array(
            GetMessage($BXMAKER_MODULE_ID . '_FORM.SELECT_EMPTY'),
            GetMessage($BXMAKER_MODULE_ID . '_FORM.SELECT_NO'),
            GetMessage($BXMAKER_MODULE_ID . '_FORM.SELECT_YES')
        ),
        'REFERENCE_ID' => array(
            '',
            '0',
            '1'
        )
    );


    // создадим объект фильтра
    $oFilter = new CAdminFilter($sTableID . "_filter", array(
        GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_SITE_ID"),
        GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_CREATED"),
        GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_STATUS"),
        GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_TYPE"),
        GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_PHONE"),
    ));

?>

    <div class="bxmaker__smsnotice__send">

        <div class="bxmaker__smsnotice__send-box bxmaker__smsnotice__send-box--msg">
            <div class="bxmaker__smsnotice__send-box-title"
                 data-msg-title="<?= GetMessage($BXMAKER_MODULE_ID . '_FORM.MSG_LIST_LABEL'); ?>"
                 data-result-title="<?= GetMessage($BXMAKER_MODULE_ID . '_FORM.RESULT_MSG'); ?>">
                <?= GetMessage($BXMAKER_MODULE_ID . '_FORM.MSG_LIST_LABEL'); ?>
            </div>
            <div class="bxmaker__smsnotice__send-box-content">
                <div class="bxmaker__smsnotice__send-msg">

                </div>
            </div>
        </div>


        <div class="bxmaker__smsnotice__send-box bxmaker__smsnotice__send-box--message ">
            <div class="bxmaker__smsnotice__send-box-title"><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.LABEL'); ?></div>
            <div class="bxmaker__smsnotice__send-box-content">

                <? if ($arServiceParam): ?>
                    <div class="bxmaker__smsnotice__send-box-content-info">
                        <b><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.LABEL.SITE'); ?></b> <?= $arSite[ $oManager->getCurrentSiteId() ]; ?>
                        <br/>
                        <b><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.LABEL.SERVICE'); ?></b> [<?= $arServiceParam['ID']; ?>] <?= $arServiceParam['NAME']; ?>.
                        <br/>
                        <b><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.LABEL.BALANCE'); ?></b> <?= ($balance->isSuccess() ? $balance->getResult() : implode(', ', $balance->getErrorMessages())); ?>
                    </div>

                <? endif; ?>

                <div class="row_item">
                    <small><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.TEMPLATE'); ?></small>
                    <?= SelectBoxFromArray('template', $arTemplate); ?>
                </div>

                <div class="row_item textarea_box">
                    <small><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.TEXT'); ?></small>
                    <br/>
                    <textarea name="text" rows="4" cols="10"></textarea>
                </div>


                <div class="info_box">
                    <span class="text_size"><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.INFO'); ?></span>
                    <span class="btn_clean"><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.BTN_CLEAN'); ?></span>
                </div>

                <div class="row_item translit_box">
                    <input type="checkbox" name="translit" id="translit" value="Y"/>
                    <label for="translit"><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.BTN_TRANSLIT'); ?></label>
                </div>

                <div class="fileds_box">

                </div>

                <div class="row_item">
                    <input class="bxmaker__smsnotice__send-box-content-btn adm-btn btn_send " type="button" value="<?= GetMessage($BXMAKER_MODULE_ID . '_FORM.SENT'); ?>"/>
                </div>
            </div>
        </div>


        <div class="bxmaker__smsnotice__send-box bxmaker__smsnotice__send-box--phone ">
            <div class="bxmaker__smsnotice__send-box-title"><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.PHONE_LIST_LABEL'); ?></div>

            <div class="bxmaker__smsnotice__send-box-content bxmaker__smsnotice__send-box-content--filter ">

                <div class="bxmaker__smsnotice__send-filter-view"><span><?= GetMessage($BXMAKER_MODULE_ID . '_FILTER.SHOW'); ?></span></div>

                <div class="bxmaker__smsnotice__send-filter-row">
                    <div class="bxmaker__smsnotice__send-filter-row-label"><?= GetMessage($BXMAKER_MODULE_ID . '_FILTER.ORDER_PAYED'); ?></div>
                    <div class="bxmaker__smsnotice__send-filter-row-value"><?= SelectBoxFromArray('ORDER_PAYED', $arSelectYesNoEmpty); ?></div>
                </div>
                <div class="bxmaker__smsnotice__send-filter-row">
                    <div class="bxmaker__smsnotice__send-filter-row-label"><?= GetMessage($BXMAKER_MODULE_ID . '_FILTER.ORDER_CANCELED'); ?></div>
                    <div class="bxmaker__smsnotice__send-filter-row-value"><?= SelectBoxFromArray('ORDER_CANCELED', $arSelectYesNoEmpty); ?></div>
                </div>
                <div class="bxmaker__smsnotice__send-filter-row bxmaker__smsnotice__send-filter-row--date">
                    <div class="bxmaker__smsnotice__send-filter-row-label"><?= GetMessage($BXMAKER_MODULE_ID . '_FILTER.DATE_FROM'); ?></div>
                    <div class="bxmaker__smsnotice__send-filter-row-value">
                        <?= CalendarDate("DATE_FROM", date('d.m.Y H:i:s'), "", "15", "") ?>
                    </div>
                </div>
                <div class="bxmaker__smsnotice__send-filter-row bxmaker__smsnotice__send-filter-row--date">
                    <div class="bxmaker__smsnotice__send-filter-row-label"><?= GetMessage($BXMAKER_MODULE_ID . '_FILTER.DATE_TO'); ?></div>
                    <div class="bxmaker__smsnotice__send-filter-row-value">
                        <?= CalendarDate("DATE_TO", date('d.m.Y H:i:s'), "", "15", "") ?>
                    </div>
                </div>
                <div class="bxmaker__smsnotice__send-filter-row">
                    <input type="button" class="adm-btn bxmaker__smsnotice__send-box-content-btn btn_filter" value="<?= GetMessage($BXMAKER_MODULE_ID . '_FILTER.BTN'); ?>"/>
                </div>

            </div>

            <div class="bxmaker__smsnotice__send-box-content ">

                <div class="text_box">
                    <small><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.PHONE_LIST_TEXT'); ?> </small>
                    <br><br>
                    <textarea name="phone" id="" cols="30" rows="15"></textarea>
                </div>

            </div>
        </div>

        <br>
        <div class="bxmaker__smsnotice__send-box bxmaker__smsnotice__send-box--error">
            <div class="close"><?= GetMessage($BXMAKER_MODULE_ID . '_FORM.CLOSE_ERROR'); ?></div>
            <div class="descr">

            </div>
        </div>

    </div>

    <br/>


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
            <td><?= GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_CREATED") ?>:</td>
            <td>
                <? echo CalendarPeriod("find_created_from", $find_created_from, "find_created_to", $find_created_to, "find_form", "Y") ?>
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_STATUS") ?>:</td>
            <td>
                <?
                    echo SelectBoxFromArray("find_status", $arStatusReference, $find_status);
                ?>
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_TYPE") ?>:</td>
            <td>
                <?
                    echo SelectBoxFromArray("find_type", $arTypeIdReference, $find_type);
                ?>
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . "_HEAD.FILTER_PHONE") ?>:</td>
            <td>
                <?
                    echo InputType("text", "find_phone", $find_phone, '');
                ?>
            </td>
        </tr>
        <?
            $oFilter->Buttons(array(
                "table_id" => $sTableID,
                "url"      => $APPLICATION->GetCurPage(),
                "form"     => "find_form"
            ));
            $oFilter->End();
        ?>
    </form>


    <h2><?= GetMessage($BXMAKER_MODULE_ID . '_LIST_LABEL'); ?></h2>

    <div class="bxmaker__smsnotice__send-list">
        <?

            if ($bAjax) {
                $APPLICATION->RestartBuffer();
                ob_start();
            }

            $sAdmin->DisplayList();

            if ($bAjax) {


                echo ob_get_clean();

                die();
            }
        ?>
    </div>

    <script type="text/javascript">
        BX.message({
            'bxmaker_smsnotice_template_type': <?=json_encode($APPLICATION->ConvertCharsetArray($arTemplateData, LANG_CHARSET, 'UTF-8'));?>,
            'bxmaker_smsnotice_translit': <?=GetMessage($BXMAKER_MODULE_ID . '.translit');?>
        });
    </script>

<?
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>