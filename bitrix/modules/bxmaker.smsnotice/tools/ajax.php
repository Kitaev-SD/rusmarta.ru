<?
    define("NO_KEEP_STATISTIC", true);
//    define("NOT_CHECK_PERMISSIONS",true);
//    define("BX_NO_ACCELERATOR_RESET", true);
    define("STOP_STATISTICS", true);
    define("NO_AGENT_STATISTIC", "Y");
    define("DisableEventsCheck", true);
    define("NO_AGENT_CHECK", true);


    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

    $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
    $request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);


    if(\Bitrix\Main\Loader::includeModule('bxmaker.smsnotice'))
    {
        \Bxmaker\SmsNotice\Manager::getInstance()->adminPageAjaxHandler();
    }
    else
    {
        echo json_encode(array(
            'status' => 'error',
            'error' => array(
                'msg' => 'Module bxmaker.smsnotice is not installed',
                'code' => 'MODULE_NOT_INSTALLED'
            )
        ));
    }

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");