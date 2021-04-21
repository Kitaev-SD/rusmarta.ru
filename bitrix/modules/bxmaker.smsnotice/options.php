<?
    global $APPLICATION;

    $BXMAKER_MODULE_ID = 'bxmaker.smsnotice';
    $BXMAKER_SMSNOTICE_MSG = 'bxmaker.smsnotice.options.';

    \Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
    \Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);

    $PERMISSION = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

    $app = \Bitrix\Main\Application::getInstance();
    $req = $app->getContext()->getRequest();

    if ($PERMISSION != "W") {
        $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
        die();
    }

    CUtil::InitJSCore('jquery');

    \Bitrix\Main\Loader::includeModule('sale');


    $bModuleInstalled = array(
        'sale' => \Bitrix\Main\ModuleManager::isModuleInstalled('sale'),
        'bxmaker.authusherphone' => \Bitrix\Main\ModuleManager::isModuleInstalled('bxmaker.authusherphone')
    );

    $dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(dirname(__FILE__))) . '/admin';


    $arOptions = array(
        //	array(
        //		'KEY'     => 'GEN', // COption::GetString('bxmaker.smsnotice', 'GEN.DEBUG', '');
        //		'NAME'    => GetMessage('AP_EDIT_TAB.GEN'),
        //		'OPTIONS' => array(
        //
        //		)
        //	)
    );


    // доступные шаблоны сообщений
    $arTemplateTypeList = array(
        'REFERENCE' => array(
            GetMessage($BXMAKER_SMSNOTICE_MSG . 'NO_SELECT')
        ),
        'REFERENCE_ID' => array(
            ''
        )
    );
    $oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
    $dbrTemplateType = $oTemplateType->getList();
    while ($arTemplateType = $dbrTemplateType->fetch()) {
        $arTemplateTypeList['REFERENCE'][] = '[' . $arTemplateType['CODE'] . '] ' . $arTemplateType['NAME'];
        $arTemplateTypeList['REFERENCE_ID'][] = $arTemplateType['CODE'];
    }

    // получаем массив сайтов
    $arSite = array();
    $dbr = \CSite::GetList($by = 'sort', $order = 'asc');
    while ($ar = $dbr->Fetch()) {
        $arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
    }

    //группы пользвоателей
    $arGroup = array(
        'REFERENCE' => array(),
        'REFERENCE_ID' => array()
    );
    $oGroup = new Bitrix\Main\GroupTable();
    $dbr = $oGroup->getList();
    while ($ar = $dbr->fetch()) {
        $arGroup['REFERENCE'][] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
        $arGroup['REFERENCE_ID'][] = $ar['ID'];

    }

    // Статусы заказов
    $arStatuses = array();
    // Статусы отгрузок
    $arShipmentStatuses = array();

    if (CModule::IncludeModule('sale')) {

        if ($bModuleInstalled['sale']) {
            $oSaleStatus = new \CSaleStatus();
            $dbrSaleStatus = $oSaleStatus->GetList(array("SORT" => 'ASC'), array('LID' => LANGUAGE_ID));

            $dbrSaleStatus = \CSaleStatus::GetList(
                array('SORT' => 'ASC'),
                array('LID' => LANGUAGE_ID),
                false,
                false,
                array('ID', 'SORT', /*'TYPE',*/
                    'NOTIFY', 'LID', 'NAME', 'DESCRIPTION', $by)
            );
            while ($arSaleStatus = $dbrSaleStatus->Fetch()) {
                $arStatuses[$arSaleStatus['ID']] = $arSaleStatus;
            }
        }

        //округление стоимости заказа
        $arOrderPriceRoundValues = array(
            'REFERENCE' => array(
                GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023.5360')),
                GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023.536')),
                GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023.53')),
                GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023.5')),
                GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023')),
            ),
            'REFERENCE_ID' => array('4', '3', '2', '1', '0')
        );


//        $shipmetnStatuses = \Bitrix\Sale\DeliveryStatus::getInitialStatus();
//        $arShipmentStatuses = \Bitrix\Sale\DeliveryStatus::getAllowedGroupStatuses(1, $shipmetnStatuses);

        $dbrShipmentStatus = \Bitrix\Sale\Internals\StatusTable::getList(array(
            'select' => array('ID', 'NAME' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME'),
            'filter' => array('=TYPE' => \Bitrix\Sale\DeliveryStatus::TYPE, '=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => LANGUAGE_ID),
            'order' => array('SORT'),
        ));
        while ($arShipmentStatusItem = $dbrShipmentStatus->fetch()) {
            $arShipmentStatuses[$arShipmentStatusItem['ID']] = $arShipmentStatusItem['NAME'];
        }

    }


    foreach ($arSite as $sid => $sname) {


        $key = 'HANDLER';
        $arOptionCurrent = array(
            'KEY' => $key, // COption::GetString('bxmaker.smsnotice', 'HANDLER.DEBUG', '');
            'NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'TAB.SITE', array('#SITE#' => $sname)),
            'OPTIONS' => array()
        );


        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'CODE' => 'DEBUG',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'DEBUG'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT_VALUE' => 'N'
        );
        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'CODE' => 'LOG',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'LOG'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT_VALUE' => 'N'
        );
        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'CODE' => 'CLEAN_SMS_HISTORY',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'CLEAN_SMS_HISTORY'),
            'TYPE' => 'STRING',
            'DEFAULT_VALUE' => '30'
        );
        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'CODE' => 'SKIP_GROUP',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'SKIP_GROUP'),
            'TYPE' => 'MULTY_LIST',
            'DEFAULT_VALUE' => '',
            'VALUES' => $arGroup
        );

        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'CODE' => 'REPIRE_PHONE',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'REPIRE_PHONE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT_VALUE' => 'Y',
        );

        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'CODE' => 'EMAIL_TO_PHONE',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'EMAIL_TO_PHONE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT_VALUE' => 'Y',
        );


        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'GROUP_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'GROUP_NAME.OTPRAVKA'),
            'CODE' => 'WAIT_SENDING',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'WAIT_SENDING'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT_VALUE' => 'N',
        );
        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'CODE' => 'QUEUE_LIMIT',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'QUEUE_LIMIT'),
            'TYPE' => 'STRING',
            'DEFAULT_VALUE' => '200',
        );


        // ИНтернет-магазин
        if (CModule::IncludeModule('sale')) {


            //Округлять
            $arOptionCurrent['OPTIONS'][] = array(
                'SID' => $sid,
                'GROUP' => 'SALE',
                'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_PRICE_ROUND'),
                'CODE' => 'ORDER_PRICE_ROUND',
                'TYPE' => 'LIST',
                'VALUES' => $arOrderPriceRoundValues,
                'DEFAULT_VALUE' => '',
            );


            //Типы плательщиков
            $arPersoneType = array();
            $dbrPType = CSalePersonType::GetList(Array("SORT" => "ASC"), Array("LID" => $sid));
            while ($arPType = $dbrPType->Fetch()) {


                // свойства текущего типа плательщика
                $arOrderProps = array(
                    'REFERENCE' => array(
                        GetMessage($BXMAKER_SMSNOTICE_MSG . 'NO_SELECT')
                    ),
                    'REFERENCE_ID' => array(
                        ''
                    )
                );
                $dbrProp = CSaleOrderProps::GetList(array('SORT' => 'ASC'), array('PERSON_TYPE_ID' => $arPType['ID']));
                while ($arProp = $dbrProp->Fetch()) {

                    $arOrderProps['REFERENCE'][] = $arProp['NAME'];
                    $arOrderProps['REFERENCE_ID'][] = $arProp['CODE'];
                }

                // номер телефона
                $arOptionCurrent['OPTIONS'][] = array(
                    'SID' => $sid,
                    'GROUP' => 'SALE',
                    'GROUP_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'PERSON_TYPE_GROUP', array('#NAME#' => $arPType['NAME'])),
                    'CODE' => 'PERSON_TYPE_' . $arPType['ID'],
                    'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'PERSON_TYPE'),
                    'TYPE' => 'LIST',
                    'VALUES' => $arOrderProps,
                    'DEFAULT_VALUE' => '',
                );

                // сохранять пользователю номер телефона
                $arOptionCurrent['OPTIONS'][] = array(
                    'SID' => $sid,
                    'GROUP' => 'SALE',
                    'CODE' => 'SAVE_PHONE_' . $arPType['ID'],
                    'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'SAVE_PHONE'),
                    'TYPE' => 'CHECKBOX',
                    'VALUES' => '',
                    'DEFAULT_VALUE' => 'N',
                );

                // Магазин
                //Новый заказ
                $arOptionCurrent['OPTIONS'][] = array(
                    'SID' => $sid,
                    'GROUP' => 'SALE',
                    'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_NEW_TEMPLATE_TYPE'),
                    'CODE' => 'ORDER_NEW_TEMPLATE_TYPE_' . $arPType['ID'],
                    'TYPE' => 'LIST',
                    'VALUES' => $arTemplateTypeList,
                    'DEFAULT_VALUE' => '',
                );

                //установлен трекер
                $arOptionCurrent['OPTIONS'][] = array(
                    'SID' => $sid,
                    'GROUP' => 'SALE',
                    'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_TRACKING_NUMBER_TEMPLATE_TYPE'),
                    'CODE' => 'ORDER_TRACKING_NUMBER_TEMPLATE_TYPE_' . $arPType['ID'],
                    'TYPE' => 'LIST',
                    'VALUES' => $arTemplateTypeList,
                    'DEFAULT_VALUE' => '',
                );

                $arOptionCurrent['OPTIONS'][] = array(
                    'SID' => $sid,
                    'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_CANCELED_TEMPLATE_TYPE'),
                    'CODE' => 'ORDER_CANCELED_TEMPLATE_TYPE_' . $arPType['ID'],
                    'TYPE' => 'LIST',
                    'VALUES' => $arTemplateTypeList,
                    'DEFAULT_VALUE' => '',
                );
                $arOptionCurrent['OPTIONS'][] = array(
                    'SID' => $sid,
                    'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_PAY_TEMPLATE_TYPE'),
                    'CODE' => 'ORDER_PAY_TEMPLATE_TYPE_' . $arPType['ID'],
                    'TYPE' => 'LIST',
                    'VALUES' => $arTemplateTypeList,
                    'DEFAULT_VALUE' => '',
                );

                $arOptionCurrent['OPTIONS'][] = array(
                    'SID' => $sid,
                    'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_CHECK_TEMPLATE_TYPE'),
                    'CODE' => 'ORDER_CHECK_TEMPLATE_TYPE_' . $arPType['ID'],
                    'TYPE' => 'LIST',
                    'VALUES' => $arTemplateTypeList,
                    'DEFAULT_VALUE' => '',
                );

                //Статусы заказов
                foreach ($arStatuses as $status_id => $arItem) {
                    $arOptionCurrent['OPTIONS'][] = array(
                        'SID' => $sid,
                        'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_STATUS_TEMPLATE_TYPE', array('#STATUS#' => $arItem['NAME'], '#STATUS_ID#' => $status_id)),
                        'CODE' => 'ORDER_STATUS_' . $arItem['ID'] . '_TEMPLATE_TYPE_' . $arPType['ID'],
                        'TYPE' => 'LIST',
                        'DEFAULT_VALUE' => '',
                        'VALUES' => $arTemplateTypeList
                    );
                }

                //Статусы отгрузки
                foreach ($arShipmentStatuses as $status_id => $name) {
                    $arOptionCurrent['OPTIONS'][] = array(
                        'SID' => $sid,
                        'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'ORDER_SHIPMENT_STATUS_TEMPLATE_TYPE', array('#STATUS#' => $name, '#STATUS_ID#' => $status_id)),
                        'CODE' => 'ORDER_SHIPMENT_STATUS_' . $status_id . '_TEMPLATE_TYPE_' . $arPType['ID'],
                        'TYPE' => 'LIST',
                        'DEFAULT_VALUE' => '',
                        'VALUES' => $arTemplateTypeList
                    );
                }
            }
        }

        // Модуль bxmaker.authuserphone - авторизация по телефону
        if (CModule::IncludeModule('bxmaker.authuserphone')) {
            // поумолчанию все включено
        }


        // Пользователи
        $arUserFields = array(
            'REFERENCE' => array(
                GetMessage($BXMAKER_SMSNOTICE_MSG . 'NO_SELECT'), 'LOGIN', 'PERSONAL_MOBILE', 'PERSONAL_PHONE', 'PHONE_NUMBER'
            ),
            'REFERENCE_ID' => array(
                '', 'LOGIN', 'PERSONAL_MOBILE', 'PERSONAL_PHONE', 'PHONE_NUMBER'
            )
        );
        $dbr = \CUserTypeEntity::GetList(array(), array(
            'ENTITY_ID' => 'USER'
        ));
        while ($ar = $dbr->fetch()) {
            $arUserFields['REFERENCE'][] = $ar['FIELD_NAME'];
            $arUserFields['REFERENCE_ID'][] = $ar['FIELD_NAME'];
        }
        
        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'GROUP' => 'USER',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'USER_PHONE_FIELD'),
            'CODE' => 'USER_PHONE_FIELD',
            'TYPE' => 'LIST',
            'VALUES' => $arUserFields,
            'DEFAULT_VALUE' => '',
        );
        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'GROUP' => 'USER',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'USER_ADD_TEMPLATE_TYPE'),
            'CODE' => 'USER_ADD_TEMPLATE_TYPE',
            'TYPE' => 'LIST',
            'VALUES' => $arTemplateTypeList,
            'DEFAULT_VALUE' => '',
        );
        $arOptionCurrent['OPTIONS'][] = array(
            'SID' => $sid,
            'GROUP' => 'USER',
            'CODE_NAME' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'USER_UPDATE_TEMPLATE_TYPE'),
            'CODE' => 'USER_UPDATE_TEMPLATE_TYPE',
            'TYPE' => 'LIST',
            'VALUES' => $arTemplateTypeList,
            'DEFAULT_VALUE' => '',
        );


        $arOptions[] = $arOptionCurrent;
    }


    //////////////////////////////////////////////////////////////////////////////

    if ($PERMISSION == "W") {
        $oOption = new \Bitrix\Main\Config\Option();

        if (($apply || $save) && check_bitrix_sessid() && $req->isPost()) {
            foreach ($arOptions as $arOption) {
                $key = $arOption['KEY'];

                foreach ($arOption['OPTIONS'] as $arItem) {

                    switch ($arItem['TYPE']) {
                        case 'STRING':
                            {
                                $oOption->set($BXMAKER_MODULE_ID, $key . '.' . $arItem['CODE'], ($req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID']) ? trim($req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID'])) : ''), $arItem['SID']);
                                break;
                            }
                        case 'CHECKBOX':
                            {
                                $oOption->set($BXMAKER_MODULE_ID, $key . '.' . $arItem['CODE'], ($req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID']) && $req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID']) == 'Y' ? 'Y' : 'N'), $arItem['SID']);
                                break;
                            }
                        case 'LIST':
                            {
                                $oOption->set($BXMAKER_MODULE_ID, $key . '.' . $arItem['CODE'], (!is_null($req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID'])) && in_array($req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID']), $arItem['VALUES']['REFERENCE_ID']) ? $req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID']) : ''), $arItem['SID']);
                                break;
                            }
                        case 'MULTY_LIST':
                            {

                                $strMultyVal = '';
                                if ($req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID'])
                                    && is_array($req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID']))
                                ) {

                                    $ar = array_diff(array_intersect($req->getPost($key . '_' . $arItem['CODE'] . '_' . $arItem['SID']), $arItem['VALUES']['REFERENCE_ID']), array('', ' '));
                                    if (count($ar)) {
                                        $strMultyVal = implode(',', $ar);
                                    }
                                }

                                $oOption->set(
                                    $BXMAKER_MODULE_ID,
                                    $key . '.' . $arItem['CODE'],
                                    $strMultyVal,
                                    $arItem['SID']);

                                break;
                            }
                    }
                }
            }
        }
    }


?>
<?


    \Bxmaker\SmsNotice\Manager::getInstance()->showDemoMessage();
    \Bxmaker\SmsNotice\Manager::getInstance()->addAdminPageCssJs();


    // TABS
    $tabs = array();
    foreach ($arOptions as $k => $arOption) {
        $tabs[] = array(
            'DIV' => $arOption['KEY'] . $k,
            'TAB' => $arOption['NAME'],
            'ICON' => '',
            'TITLE' => (isset($arOption['DESCRIPTION']) ? $arOption['DESCRIPTION'] : $arOption['NAME'])
        );
    }

    $tabs[] = array(
        'DIV' => count($tabs),
        'TAB' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'TAB.ACCESS'),
        'ICON' => '',
        'TITLE' => GetMessage($BXMAKER_SMSNOTICE_MSG . 'TAB.ACCESS')
    );

    $tab = new CAdminTabControl('options_tabs', $tabs);

    $tab->Begin();
?>


<form class="bxmaker_smsnotice_option_edit_box" method="post"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<?= LANGUAGE_ID ?>&amp;mid_menu=<?= $mid_menu ?>"><?= bitrix_sessid_post(); ?>

    <?
        $oOption = new \Bitrix\Main\Config\Option();

        $i = 0;

        // проходим по блокам параметров
        foreach ($arOptions as $k => $arOption) {
            // новая влкадка
            $tab->BeginNextTab();


            $key = $arOption['KEY'];
            $group = '';
            $i++;

            if ($i >= 0) {
                ?>
                <tr>
                    <td colspan="2"
                        style="padding:15px; border:1px solid #00b058; background: rgba(0, 221, 98, 0.25);"><?= GetMessage($BXMAKER_SMSNOTICE_MSG . 'GROUP.TEMPLATE_TYPE_LIST_DESCRIPTION'); ?></td>
                </tr>
                <?
            }

            // параметры блока
            foreach ($arOption['OPTIONS'] as $arItem) {

                // Главный заголовок блока
                if (isset($arItem['GROUP']) && $arItem['GROUP'] != $group) {
                    $group = $arItem['GROUP'];
                    ?>
                    <tr class="heading">
                        <td colspan="2"><?= GetMessage($BXMAKER_SMSNOTICE_MSG . 'GROUP.' . $arItem['GROUP']); ?></td>
                    </tr>

                    <?
                }
                ?>

                <?
                // Подзаголовок
                if (isset($arItem['GROUP_NAME'])) {
                    ?>
                    <tr class="heading">
                        <td colspan="2" style="font-size: 0.9em;  background: #fff;"><?= $arItem['GROUP_NAME']; ?></td>
                    </tr>
                    <?
                }
                ?>

                <tr>
                    <td class="first" id="BXMAKER_FIELD_LINK_<?=$key . '_' . $arItem['CODE'];?>"
                        style="width:30%;"><?= (isset($arItem['CODE_NAME']) ? $arItem['CODE_NAME'] : GetMessage($BXMAKER_SMSNOTICE_MSG . '' . $arItem['CODE'])); ?></td>
                    <td><?
                            switch ($arItem['TYPE']) {
                                case 'STRING':
                                    {
                                        echo InputType('text', $key . '_' . $arItem['CODE'] . '_' . $arItem['SID'], $oOption->get($BXMAKER_MODULE_ID, $key . '.' . $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID']), '');
                                        break;
                                    }

                                case 'CHECKBOX':
                                    {
                                        echo InputType('checkbox', $key . '_' . $arItem['CODE'] . '_' . $arItem['SID'], 'Y', array($oOption->get($BXMAKER_MODULE_ID, $key . '.' . $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID'])));
                                        break;
                                    }

                                case 'LIST':
                                    {
                                        echo SelectBoxFromArray($key . '_' . $arItem['CODE'] . '_' . $arItem['SID'], $arItem['VALUES'], $oOption->get($BXMAKER_MODULE_ID, $key . '.' . $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID']));
                                        break;
                                    }

                                case 'MULTY_LIST':
                                    {

                                        $vals = $oOption->get($BXMAKER_MODULE_ID, $key . '.' . $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID']);

                                        echo SelectBoxMFromArray(
                                            $key . '_' . $arItem['CODE'] . '_' . $arItem['SID'] . '[]',
                                            $arItem['VALUES'],
                                            explode(',', $vals),
                                            '', '', 8
                                        );
                                        break;
                                    }
                            }
                            ShowJSHint(GetMessage($BXMAKER_SMSNOTICE_MSG . '' . $arItem['CODE'] . '.HELP'));

                        ?></td>
                    <td></td>
                </tr>
                <?
            }
        }

        $tab->BeginNextTab();

        echo '<input type="hidden" name="Update" value="Y">';

        $module_id = $BXMAKER_MODULE_ID;
        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');

        $tab->Buttons(array("disabled" => false));

        $tab->End();
    ?>
</form>
