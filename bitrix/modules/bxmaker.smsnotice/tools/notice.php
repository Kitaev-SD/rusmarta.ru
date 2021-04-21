<?
    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS",true);
    define('CHK_EVENT', true);

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

    if(\Bitrix\Main\Loader::includeModule('bxmaker.smsnotice'))
    {
        $result = \Bxmaker\SmsNotice\Manager::getInstance()->notice();
        if($result->isSuccess())
        {
            echo json_encode(array(
                'status' => 'ok'
            ));
        }
        else
        {
            $errors = $result->getErrors();

            @define("ERROR_404", "Y");
            CHTTP::SetStatus("404 Not Found");

            echo json_encode(array(
                'status' => 'error',
                'error' => array(
                    'msg' => $errors[0]->getMessage(),
                    'code' => $errors[0]->getCode()
                )
            ));
        }
    }
    else
    {
        @define("ERROR_404", "Y");
        CHTTP::SetStatus("404 Not Found");
    }

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");