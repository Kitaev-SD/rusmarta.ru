<?if (CModule::IncludeModule("iblock"))
    IncludeModuleLangFile(__FILE__);
?>
<?if(!check_bitrix_sessid()) return;?>
<?
CAdminMessage::ShowMessage(Array(
    "TYPE" => "OK",
    "MESSAGE" => GetMessage("MODULE_INSTALL_COMPLETE"),
    "DETAILS" => GetMessage("MODULE_INSTALL_COMPLETE2"),
    "HTML" => true,
));
?>