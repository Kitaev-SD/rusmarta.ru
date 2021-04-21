<?
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

    $BXMAKER_MODULE_ID = "bxmaker.smsnotice";

    \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
    \Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);


    $oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();

    $app = \Bitrix\Main\Application::getInstance();
    $req = $app->getContext()->getRequest();


    $sTableID = $BXMAKER_MODULE_ID;
    $sCurPage = $APPLICATION->GetCurPage();
    $page_prefix = $BXMAKER_MODULE_ID . '_template_type_';
    $errors = null;

// ÏÐÎÂÅÐÊÀ ÏÐÀÂ ÄÎÑÒÓÏÀ
    $PREMISION_DEFINE = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

    if ($PREMISION_DEFINE != 'W') {
        $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
        die();
    }


// Íàâèãàöèÿ íàä ôîðìîé
    $oMenu = new CAdminContextMenu(array(
        array(
            "TEXT" => GetMessage($BXMAKER_MODULE_ID . '.NAV_BTN.RETURN'),
            "LINK" => $page_prefix . 'list.php?lang=' . LANG,
            "TITLE" => GetMessage($BXMAKER_MODULE_ID . '.NAV_BTN.RETURN'),
        ),
    ));


// âèçóàëèçàòîðû
    $fname = 'edit_table';

// ÐÅÄÀÊÒÈÐÎÂÀÍÈÅ
    $ID = intval($req->get('ID'));
    $COPY_ID = intval($req->get('COPY'));
    if ($ID || $COPY_ID) {
        $dbr = $oTemplateType->getList(array(
            'filter' => array(
                'ID' => ($COPY_ID ?: $ID)
            )
        ));
        if ($ar = $dbr->fetch()) {
            $arResult = $ar;
        }
    }


// ÑÎÕÐÀÍÅÍÈÅ. ÏÐÈÌÅÍÅÍÈÅ
    if (($apply || $save) && check_bitrix_sessid() && $req->isPost()) {

        do {

            $errors = array();
            $arFields = array();

            $arFields['NAME'] = ($req->getPost('NAME') ? trim($req->getPost('NAME')) : '');
            $arFields['CODE'] = ($req->getPost('CODE') ? trim($req->getPost('CODE')) : '');
            $arFields['DESCR'] = ($req->getPost('DESCR') ? trim($req->getPost('DESCR')) : '');
            $arResult = $arFields;

            if (strlen(trim($arFields['NAME'])) <= 0) {
                $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.EMPTY_NAME'));
                break;
            }
            if (strlen(trim($arFields['CODE'])) <= 0) {
                $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.EMPTY_CODE'));
                break;
            }
//        if (strlen(trim($arFields['DESCR'])) <= 0) {
//            $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.EMPTY_DESCR'));
//            break;
//        }

            $arCodeFilter = array(
                'CODE' => trim($arFields["CODE"])
            );
            if ($req->getQuery('ID')) {
                $arCodeFilter['!ID'] = intval($req->getQuery('ID'));
            }
            $dbr = $oTemplateType->getList(array(
                'filter' => $arCodeFilter
            ));
            if ($dbr->fetch()) {
                $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($BXMAKER_MODULE_ID . '.FIELD_ERROR.REPEAT_CODE'));
                break;
            }

            if (empty($errors)) {
                if ($req->get('ID')) {
                    // îáíîëâíåèå
                    $result = $oTemplateType->update(intval($req->get('ID')), $arFields);

                    if ($result->isSuccess()) {
                        if ($apply) {
                            LocalRedirect($APPLICATION->GetCurPageParam());
                        } elseif ($save) {
                            LocalRedirect('/bitrix/admin/' . $page_prefix . 'list.php?lang=' . LANG);
                        }
                    } else {
                        $errors = $result->getErrors();
                    }
                } else {
                    // îáàâëåíèå
                    $result = $oTemplateType->add($arFields);
                    if ($result->isSuccess()) {
                        if ($apply) {
                            LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $result->getId(), array('ID')));
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


    <form action="<? $APPLICATION->GetCurPage() ?>" method="POST" name="<?= $fname ?>">
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
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.NAME'); ?> <span class="reg">*</span></td>
            <td><?= InputType('text', 'NAME', $arResult['NAME'], ''); ?></td>
        </tr>

        <tr>
            <td><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.CODE'); ?> <span class="reg">*</span></td>
            <td><?= InputType('text', 'CODE', $arResult['CODE'], ''); ?></td>
        </tr>

        <tr class="content_type-html-row">
            <td style="text-align:center;" colspan="2"><?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.DESCR'); ?> </td>
        </tr>
        <tr class="content_type-html-row">
            <td colspan="2">
                <textarea name="DESCR" rows="20" style="width: 100%;" placeholder="<?= GetMessage($BXMAKER_MODULE_ID . '.FIELD_LABEL.DESCR_PLACEHOLDER'); ?>"><?= $arResult['DESCR']; ?></textarea>

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