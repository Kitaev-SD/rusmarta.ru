<?
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

    $BXMAKER_MODULE_ID = "bxmaker.smsnotice";

    \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
    \Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);

    

    CUtil::InitJSCore('jquery');
    $dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(dirname(__FILE__)));


    $oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
    $oTemplateSite = new \Bxmaker\SmsNotice\Template\SiteTable();
    $oTemplate = new \Bxmaker\SmsNotice\TemplateTable();
    $oManager = \Bxmaker\SmsNotice\Manager::getInstance();
    $oTemplateCondition = new \Bxmaker\SmsNotice\Template\Condition();

    $app = \Bitrix\Main\Application::getInstance();
    $req = $app->getContext()->getRequest();

    $bUtf = defined("BX_UTF");


    if ($req->isPost() && check_bitrix_sessid('sessid') && $req->getPost('method') && $req->getPost('method') == 'getTemplateTypeFields') {
        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');

        $arJson = array(
            'error' => array(),
            'response' => array()
        );

        do {


            if (!$req->getPost('type') || strlen(trim($req->getPost('type'))) <= 0) {
                $arJson['error'] = array(
                    'code' => 'invalid_text',
                    'msg' => GetMessage($BXMAKER_MODULE_ID . '.AJAX.INVALID_TYPE'),
                    'more' => array()
                );
                break;
            }

            $bdr = $oTemplateType->getList(array(
                'filter' => array(
                    'ID' => $req->getPost('type')
                )
            ));
            if ($ar = $bdr->fetch()) {
                $fields = GetMessage($BXMAKER_MODULE_ID . '.AJAX.TEMPLATE_TYPE_FIELD_SITE_NAME');
                $fields .= GetMessage($BXMAKER_MODULE_ID . '.AJAX.TEMPLATE_TYPE_FIELD_SERVER_NAME');
                $fields .= nl2br(trim($ar['DESCR'], "\r\n "));

                $ar['DESCR'] = $fields;

                $arJson['response'] = array(
                    'item' => $ar
                );
            } else {
                $arJson['error'] = array(
                    'code' => '0',
                    'msg' => GetMessage($BXMAKER_MODULE_ID . '.AJAX.UNKNOW_TYPE'),
                    'more' => ''
                );
                break;

            }
        } while (false);


        if (!empty($arJson['error'])) {
            echo json_encode(array(
                'error' => ($bUtf ? $arJson['error'] : $APPLICATION->ConvertCharsetArray($arJson['error'], LANG_CHARSET, 'UTF-8')),
                'status' => 0
            ));
        } else {
            echo json_encode(array(
                'response' => ($bUtf ? $arJson['response'] : $APPLICATION->ConvertCharsetArray($arJson['response'], LANG_CHARSET, 'UTF-8')),
                'status' => 1
            ));

        }
        die();
    }


    $sTableID = $BXMAKER_MODULE_ID;
    $sCurPage = $APPLICATION->GetCurPage();
    $page_prefix = $BXMAKER_MODULE_ID . '_template_';
    $errors = null;

    // ПРОВЕРКА ПРАВ ДОСТУПА
    $PREMISION_DEFINE = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

    if ($PREMISION_DEFINE != 'W') {
        $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
        die();
    }


    // Навигация над формой
    $oMenu = new CAdminContextMenu(array(
        array(
            "TEXT" => GetMessage($BXMAKER_MODULE_ID . '.NAV_BTN.RETURN'),
            "LINK" => $page_prefix . 'list.php?lang=' . LANG,
            "TITLE" => GetMessage($BXMAKER_MODULE_ID . '.NAV_BTN.RETURN'),
        ),
    ));


    // визуализаторы
    $fname = 'bxmaker__smsnotice__template__edit';

    // РЕДАКТИРОВАНИЕ
    $arSID = array();
    if ($req->get('ID') || $req->get('COPY_ID')) {
        $dbr = $oTemplate->getList(array(
            'filter' => array(
                'ID' => (intval($req->get('COPY_ID')) > 0 ? intval($req->get('COPY_ID')) : intval($req->get('ID')))
            )
        ));
        if ($ar = $dbr->fetch()) {
            $arResult = $ar;
        }

        // привязки
        $dbr = $oTemplateSite->getList(array(
            'filter' => array(
                'TID' => (intval($req->get('COPY_ID')) > 0 ? intval($req->get('COPY_ID')) : intval($req->get('ID')))
            )));
        while ($ar = $dbr->fetch()) {
            $arSID[$ar['ID']] = $ar['SID'];
        }
    }

    // типы
    $arTemplateType = array(
        'REFERENCE' => array(GetMessage($BXMAKER_MODULE_ID . '.FIELD.TYPE_SELECT')),
        'REFERENCE_ID' => array('')
    );
    $dbr = $oTemplateType->getList();
    while ($ar = $dbr->fetch()) {
        $arTemplateType['REFERENCE'][] = '[' . $ar['CODE'] . '] ' . $ar['NAME'];
        $arTemplateType["REFERENCE_ID"][] = $ar['ID'];
    }

    // сайты
    $arSite = array();
    $dbr = \CSite::GetList($by = 'sort', $order = 'asc');
    while ($ar = $dbr->Fetch()) {
        $arSite[$ar['ID']] = $ar;
    }

    //типы почтовых событий
    $arEventTypes = array();
    $arEventTypeList = array(
        'REFERENCE' => array(GetMessage($BXMAKER_MODULE_ID . '.FIELD.TYPE_SELECT')),
        'REFERENCE_ID' => array('')
    );
    $dbrEventType = \CEventType::GetList(array('LID' => LANGUAGE_ID), array('SORT' => 'ASC'));
    while ($arEventType = $dbrEventType->Fetch()) {
        $arEventTypes[$arEventType['EVENT_NAME']] = $arEventType['DESCRIPTION'];

        $arEventTypeList['REFERENCE_ID'][] = $arEventType['EVENT_NAME'];
        $arEventTypeList['REFERENCE'][] = '[' . $arEventType['EVENT_NAME'] . '] ' . $arEventType['NAME'];
    }

    // СОХРАНЕНИЕ. ПРИМЕНЕНИЕ
    if (($apply || $save) && check_bitrix_sessid() && $req->isPost()) {

        do {

            $errors = array();
            $arFields = array();

            $arFields['TYPE_ID'] = ($req->getPost('TYPE_ID') && in_array($req->getPost('TYPE_ID'), $arTemplateType["REFERENCE_ID"]) ? trim($req->getPost('TYPE_ID')) : '');
            $arFields['NAME'] = ($req->getPost('NAME') ? trim($req->getPost('NAME')) : '');
            $arFields['ACTIVE'] = ($req->getPost('ACTIVE') ? true : false);
            $arFields['TRANSLIT'] = ($req->getPost('TRANSLIT') ? true : false);
            $arFields['PHONE'] = ($req->getPost('PHONE') ? trim($req->getPost('PHONE')) : '');
            $arFields['PHONE_COPY'] = ($req->getPost('PHONE_COPY') ? trim($req->getPost('PHONE_COPY')) : '');
            $arFields['TEXT'] = ($req->getPost('TEXT') ? preg_replace("#\x20+#", ' ', trim($req->getPost('TEXT'))) : '');
            $arFields['EVENT'] = null;
            $arFields['PARAMS'] = array(
                'CONDITIONS' => $oTemplateCondition->parse()
            );

            if ($req->getPost('EVENT_ON')) {
                $arFields['EVENT'] = ($req->getPost('EVENT') && in_array($req->getPost('EVENT'), $arEventTypeList["REFERENCE_ID"]) ? trim($req->getPost('EVENT')) : '');
                $arFields['TYPE_ID'] = 0;

                if (strlen(trim($arFields['EVENT'])) <= 0) {
                    $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.EMPTY_EVENT'));
                    break;
                }
            }

            $arResult = $arFields;

            if (strlen(trim($arFields['NAME'])) <= 0) {
                $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.EMPTY_NAME'));
                break;
            }
            if (strlen(trim($arFields['TYPE_ID'])) <= 0) {
                $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.EMPTY_TYPE'));
                break;
            }
            if (strlen(trim($arFields['PHONE'])) <= 0) {
                $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.EMPTY_PHONE'));
                break;
            }

            $arTemplateSite = array();
            if ($req->getPost('SITE')) {
                foreach ((array)$req->getPost('SITE') as $sid => $val) {
                    if (isset($arSite[$sid])) {
                        $arTemplateSite[] = $sid;
                    }
                }
            }

            if (count($arTemplateSite) <= 0) {
                $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.EMPTY_SITE'));
                break;
            }


            if (empty($errors)) {
                if ($req->get('ID')) {
                    // обнолвнеие
                    $result = $oTemplate->update(intval($req->get('ID')), $arFields);

                    if ($result->isSuccess()) {


                        $arSidDelete = array_diff(array_values($arSID), $arTemplateSite);
                        $arSidAdd = array_diff($arTemplateSite, array_values($arSID));
                        // развернем массив для удобства удаления
                        $arSIDflip = array_flip($arSID);

                        //удаляем
                        foreach ($arSidDelete as $sid) {
                            $oTemplateSite->delete($arSIDflip[$sid]);
                        }
                        //добавляем
                        foreach ($arSidAdd as $sid) {
                            $r = $oTemplateSite->add(array(
                                'TID' => intval($req->get('ID')),
                                'SID' => $sid
                            ));
                        }

                        if ($apply) {
                            LocalRedirect($APPLICATION->GetCurPageParam());
                        } elseif ($save) {
                            LocalRedirect('/bitrix/admin/' . $page_prefix . 'list.php?lang=' . LANG);
                        }
                    } else {
                        $errors = $result->getErrors();
                    }
                } else {
                    // обавление
                    $result = $oTemplate->add($arFields);
                    if ($result->isSuccess()) {

                        //добавляем
                        foreach ($arTemplateSite as $sid) {
                            $oTemplateSite->add(array(
                                'TID' => intval($result->getId()),
                                'SID' => $sid
                            ));
                        }

                        if ($apply) {
                            LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $result->getId(), array('ID', 'COPY_ID')));
                        } elseif ($save) {
                            LocalRedirect('/bitrix/admin/' . $page_prefix . 'list.php?lang=' . LANG);
                        }
                    } else {
                        $errors = $result->getErrors();
                    }
                }
            }

        } while (false);

    }


    $tab = new CAdminTabControl('edit', array(
        array(
            'DIV' => 'edit',
            'TAB' => GetMessage($BXMAKER_MODULE_ID . '.TAB.EDIT'),
            'ICON' => '',
            'TITLE' => GetMessage($BXMAKER_MODULE_ID . '.TAB.EDIT')),
    ));

    $APPLICATION->SetTitle(GetMessage($BXMAKER_MODULE_ID . '.PAGE_TITLE'));

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


    \Bxmaker\SmsNotice\Manager::getInstance()->showDemoMessage();
    \Bxmaker\SmsNotice\Manager::getInstance()->addAdminPageCssJs();


    if ($errors && is_array($errors)) {
        $arStr = array();
        foreach ($errors as $error) {
            $arStr[] = $error->getMessage();
        }
        \CAdminMessage::ShowMessage(implode('<br />', $arStr));
    }


    $oMenu->Show();


?>

    <script type="text/javascript">
        var BXmakerSMSNoticeEventType = <?=json_encode($oManager->prepareEncoding($arEventTypes));?>;
    </script>


    <form action="<? $APPLICATION->GetCurPage() ?>" method="POST" name="<?= $fname ?>" class="<?= $fname; ?>">
        <? echo bitrix_sessid_post(); ?>



        <? $tab->Begin(); ?>
        <? $tab->BeginNextTab(); ?>


        <? if ($req->get('ID')): ?>
            <tr>
                <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.ID'); ?> </td>
                <td><?= $req->get('ID') ?></td>
            </tr>
        <? endif; ?>

        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.ACTIVE'); ?> <span class="reg">*</span></td>
            <td><?= InputType('checkbox', 'ACTIVE', 'Y', ($arResult['ACTIVE'] ? 'Y' : 'N'), ''); ?></td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.TRANSLIT'); ?></td>
            <td><?= InputType('checkbox', 'TRANSLIT', 'Y', ($arResult['TRANSLIT'] ? 'Y' : 'N'), ''); ?></td>
        </tr>

        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.NAME'); ?> <span class="reg">*</span></td>
            <td><?= InputType('text', 'NAME', $arResult['NAME'], ''); ?><?= ShowJSHint(GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.NAME_HINT')); ?> </td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.SITE'); ?> <span class="reg">*</span></td>
            <td>
                <?
                    foreach ($arSite as $sid => $arItem) {
                        echo '<p>' . InputType('checkbox', 'SITE[' . $sid . ']', 'Y', (in_array($sid, $arSID) ? 'Y' : ''), false, '[' . $arItem['ID'] . '] ' . $arItem['NAME']) . '</p>';
                    }
                ?>
            </td>
        </tr>


        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.EVENT_ON'); ?></td>
            <td><?= InputType('checkbox', 'EVENT_ON', 'Y', ($arResult['EVENT'] ? 'Y' : ''), ''); ?></td>
        </tr>

        <tr class="event_type_box <?= (!!$arResult['EVENT'] ? 'active' : ''); ?>">
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.EVENT'); ?><span class="reg">*</span></td>
            <td><?= SelectBoxFromArray('EVENT', $arEventTypeList, $arResult['EVENT'], ''); ?></td>
        </tr>

        <tr class="type_box <?= (!!$arResult['EVENT'] ? 'hide' : ''); ?>">
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.TYPE'); ?> <span class="reg">*</span></td>
            <td><?= SelectBoxFromArray('TYPE_ID', $arTemplateType, $arResult["TYPE_ID"]); ?></td>
        </tr>

        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.PHONE'); ?> <span class="reg">*</span></td>
            <td><?= InputType('text', 'PHONE', $arResult['PHONE'], ''); ?> <?= ShowJSHint(GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.PHONE_HINT')); ?></td>
        </tr>
        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.PHONE_COPY'); ?></td>
            <td><?= InputType('text', 'PHONE_COPY', $arResult['PHONE_COPY'], ''); ?> <?= ShowJSHint(GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.PHONE_COPY_HINT')); ?></td>
        </tr>


        <tr class="conditions_box">
            <td colspan="2">

                <div class="bxmaker__smsnotice__template__conditions-area" id="bxmaker__smsnotice__template__conditions-area"></div>
                <?
                    $oCondition = new \BXmaker\SmsNotice\Template\Condition('bxmaker__smsnotice__template__conditions-area');
                    $oCondition->show($arResult['PARAMS']['CONDITIONS']);
                ?>
            </td>
        </tr>


        <tr class="content_type-html-row">
            <td style="text-align:center;" colspan="2"><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.TEXT'); ?> <?= ShowJSHint(GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.TEXT_HINT')); ?></td>
        </tr>
        <tr class="content_type-html-row">
            <td colspan="2">
                <textarea name="TEXT" rows="10" placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.TEXT_PLACEHOLDER'); ?>"><?= $arResult['TEXT']; ?></textarea>
                <br/>

                <div class="template_fields_box">

                </div>
            </td>
        </tr>


        <? $tab->EndTab(); ?>
        <? $tab->Buttons(array("disabled" => ($PREMISION_DEFINE != "W"),)); ?>
        <? $tab->End(); ?>
    </form>

    <style type="text/css">
        form[name="<?= $fname ?>"] .reg {
            color: red;
        }
    </style>




<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>