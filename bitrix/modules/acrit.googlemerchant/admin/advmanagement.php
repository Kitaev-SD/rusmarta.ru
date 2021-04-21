<?php
$moduleId = "acrit.googlemerchant";
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
IncludeModuleLangFile(__FILE__);

$moduleStatus = CModule::IncludeModuleEx( $moduleId );
if( $moduleStatus == MODULE_DEMO_EXPIRED ){
    $buyLicenceUrl = "http://www.acrit-studio.ru/market/rabota-s-torgovymi-ploshchadkami/eksport-tovarov-na-torgovye-portaly/?action=BUY&id=32914";
    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );?>
    <div class="adm-info-message">
        <div class="acrit_note_button">
            <a href="<?=$buyLicenceUrl?>" target="_blank" class="adm-btn adm-btn-save"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_DEMOEND_BUY_LICENCE_INFO" )?></a>
        </div>
        <div class="acrit_note_text"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_DEMOEND_PERIOD_INFO" );?></div>
        <div class="acrit_note_clr"></div>
    </div>
<?}
else{
    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$moduleId/include.php" );

    CModule::IncludeModule( $moduleId );

    CUtil::InitJSCore( array( "ajax", "jquery" ) );
    $APPLICATION->AddHeadScript( "/bitrix/js/iblock/iblock_edit.js" );
    $APPLICATION->AddHeadScript( "/bitrix/js/$moduleId/main.js" );
    $t = CJSCore::getExtInfo( "jquery" );

    $APPLICATION->SetTitle( GetMessage( "ACRIT_GOOGLEMERCHANT_ADVMANAGEMENT_TITLE" ) );

    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );

    echo BeginNote();
    echo GetMessage( "ACRIT_GOOGLEMERCHANT_ADVMANAGEMENT_TEXT" );
    echo EndNote();
}
?>

<?require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );?>