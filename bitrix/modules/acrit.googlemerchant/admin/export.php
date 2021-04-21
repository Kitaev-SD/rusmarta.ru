<?php
$moduleID = "acrit.googlemerchant";
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$moduleID.'/prolog.php');
IncludeModuleLangFile(__FILE__);

# Redirect OLD -> NEW (if old core is disabled in options)
if(\Bitrix\Main\Config\Option::get($moduleID, 'disable_old_core') == 'Y'){
	LocalRedirect('/bitrix/admin/acrit_'.end(explode('.', $moduleID)).'_new_list.php?lang='.LANGUAGE_ID);
}

$moduleStatus = CModule::IncludeModuleEx( $moduleID );
if( $moduleStatus == MODULE_DEMO_EXPIRED ){
    $buyLicenceUrl = "http://www.acrit-studio.ru/market/rabota-s-torgovymi-ploshchadkami/eksport-tovarov-na-torgovye-portaly/?action=BUY&id=8319";
    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );?>
    <div class="adm-info-message">
        <div class="acrit_note_button">
            <a href="<?=$buyLicenceUrl?>" target="_blank" class="adm-btn adm-btn-save"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_DEMOEND_BUY_LICENCE_INFO" )?></a>
        </div>
        <div class="acrit_note_text"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_DEMOEND_PERIOD_INFO" );?></div>
        <div class="acrit_note_clr"></div>
    </div>
<?}
else{
    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$moduleID/include.php" );

    $POST_RIGHT = $APPLICATION->GetGroupRight( $moduleID );
    if( $POST_RIGHT == "D" )
        $APPLICATION->AuthForm( GetMessage( "ACCESS_DENIED" ) );

    if( !CModule::IncludeModule( "iblock" ) ){
        return false;
    }

    IncludeModuleLangFile( __FILE__ );

    if( !$_REQUEST["export_import"] ){
        require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );

        AcritLicence::Show();?>
        <div class="adm-detail-content-wrap">
            <div class="adm-detail-content">
                <div class="adm-detail-content-item-block" style="height: auto; overflow-y: visible;">
                    <table class="adm-detail-content-table edit-table">
                        <tr class="heading"><td><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_TABLE_EXPORT_IMPORT_TITLE" )?></td></tr>
                        <tr align="center">
                            <td>
                                <form method="post">
                                    <div style="margin: 20px">
                                        <input type="radio" name="export_import" value="export">
                                        <label for="action"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_EXPORT_TITLE" )?></label>

                                        <input type="radio" name="export_import" value="import">
                                        <label for="action"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_IMPORT_TITLE" )?></label>
                                    </div>
                                    <input type="submit" class="adm-btn-save" value="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_PROCESS" )?>">
                                </form>
                                <br/><br/>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <?}

    //  profiles export
    if( $_REQUEST["export_import"] == "export" ){
        //  file selection
        if( $_REQUEST["step"] != 2 ){
            require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );
            AcritLicence::Show();
            ?>
            <div class="adm-detail-content-wrap">
                <div class="adm-detail-content">
                    <div class="adm-detail-content-item-block" style="height: auto; overflow-y: visible;">
                        <table class="adm-detail-content-table edit-table">
                            <tr class="heading"><td><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_TABLE_EXPORT_TITLE" )?></td></tr>
                            <tr align="center">
                                <td>
                                    <form name="googlemerchantfile_form">
                                        <br/>
                                        <label style="font-size: 14px"><b><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_EXPORT_FILE_TITLE" )?></b></label>
                                        &nbsp;&nbsp;&nbsp;
                                        <input type="text" name="URL_DATA_FILE_EXPORT">
                                        <input type="button" value="..." onclick="BtnClick()">
                                        <input type="hidden" name="export_import" value="export">
                                        <input type="hidden" name="step" value="2">
                                        <br/><br/>
                                        <a href="/bitrix/admin/acrit_googlemerchant_export.php" class="adm-btn"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_BACK" )?></a>&nbsp;&nbsp;&nbsp;
                                        <input type="submit" class="adm-btn-save" value="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_PROCESS" )?>">
                                    </form>
                                    <br/><br/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?
            CAdminFileDialog::ShowScript(
                array(
                    "event" => "BtnClick",
                    "arResultDest" => array(
                        "FORM_NAME" => "googlemerchantfile_form",
                        "FORM_ELEMENT_NAME" => "URL_DATA_FILE_EXPORT"
                    ),
                    "arPath" => array( "SITE" => SITE_ID, "PATH" => "/upload" ),
                    "select" => "F", // F - file only, D - folder only
                    "operation" => "S", // O - open, S - save
                    "showUploadTab" => true,
                    "showAddToMenuTab" => false,
                    "fileFilter" => "txt",
                    "allowAllFiles" => true,
                    "SaveConfig" => true,
                )
            );
        }
        //  show table & export
        else{
            $sTableID = "tbl_acritprofile";

            function CheckFilter(){
                global $arAdminListFilter, $obAdminList;
                foreach( $arAdminListFilter as $f ){
                    global $$f;
                }
                return true;
            }

            $obSort = new CAdminSorting( $sTableID, "ID", "desc" );
            $obAdminList = new CAdminList( $sTableID, $obSort );
            $obProfile = new CGooglemerchantProfileDB();

            $arAdminListFilter = array(
                "find",
                "find_id",
                "find_name",
                "find_active",
                "find_type",
                "find_type_run",
                "find_timestamp",
                "find_start_last_time",
            );

            $obAdminList->InitFilter( $arAdminListFilter );
            if( CheckFilter() ){
                $arFilter = array(
                    "ID" => ( $find != "" && $find_type == "id" ? $find : $find_id ),
                    "NAME" => $find_name,
                    "ACTIVE" => $find_active,
                    "TYPE" => $find_type,
                    "TYPE_RUN" => $find_type_run,
                    "TIMESTAMP" => $find_timestamp_1,
                    "START_LAST_TIME" => $find_start_last_time_1
                );
            }

            if( ( $arID = $obAdminList->GroupAction() ) && $POST_RIGHT == "W" ){
                // if selected "for all elements"
                if( $_REQUEST["action_target"] == "selected" ){
                    $arProfileList = $obProfile->GetList(
                        array( $by => $order ),
                        $arFilter
                    );

                    while( $arRes = $arProfileList->Fetch() )
                        $arID[] = $arRes["ID"];
                }

                if( $_REQUEST["URL_DATA_FILE_EXPORT"] ){
                    file_put_contents( $_SERVER["DOCUMENT_ROOT"].$_REQUEST["URL_DATA_FILE_EXPORT"], "" );
                    $arProfiles = array();
                    foreach( $arID as $ID ){
                        if( strlen( $ID ) <= 0 )
                            continue;

                        $ID = intval( $ID );

                        switch( $_REQUEST["action"] ){
                            case "export":
                                if( ( $arProfile = $obProfile->GetByID( $ID ) ) ){
                                    $profId = $arProfile["ID"];
                                    unset( $arProfile["ID"] );
                                    unset( $arProfile["START_LAST_TIME_X"] );
                                    unset( $arProfile["TIMESTAMP_X"] );
                                    $arProfiles[] = $arProfile;
                                    $message[] = "<li>[$profId] {$arProfile["NAME"]}</li>";
                                }
                                else
                                    $obAdminList->AddGroupError( GetMessage( "rub_save_error" )." ".GetMessage( "rub_no_rubric" ), $ID );
                                break;
                        }
                    }
                    $message = GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_EXPORTED1" )."<ul>".implode( "\r\n", $message )."</ul>"
                        .str_replace( "#FILE#", "http://".CAcritGooglemerchantTools::GetHttpHost().$_REQUEST["URL_DATA_FILE_EXPORT"], GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_EXPORTED2" ) );

                    CAdminMessage::ShowMessage( array( "MESSAGE" => $message, "TYPE" => "OK", "HTML" => true ) );

                    file_put_contents( $_SERVER["DOCUMENT_ROOT"].$_REQUEST["URL_DATA_FILE_EXPORT"], Bitrix\Main\Web\Json::encode( $arProfiles ) );
                }
            }
            elseif( intval( $_REQUEST["ID"] ) && ( $POST_RIGHT == "W" ) ){
                if( $_REQUEST["URL_DATA_FILE_EXPORT"] ){
                    file_put_contents( $_SERVER["DOCUMENT_ROOT"].$_REQUEST["URL_DATA_FILE_EXPORT"], "" );
                    $arProfiles = array();
                    if( strlen( $_REQUEST["ID"] ) > 0 ){
                        $ID = intval( $_REQUEST["ID"] );
                        if( ( $arProfile = $obProfile->GetByID( $ID ) ) ){
                            $profId = $arProfile["ID"];
                            unset( $arProfile["ID"] );
                            unset( $arProfile["START_LAST_TIME_X"] );
                            unset( $arProfile["TIMESTAMP_X"] );
                            $arProfiles[] = $arProfile;
                            $message[] = "<li>[$profId] {$arProfile["NAME"]}</li>";
                        }
                    }
                    $directMessage = GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_EXPORTED1" )."<ul>".implode( "\r\n", $message )."</ul>"
                        .str_replace( "#FILE#", "http://".CAcritGooglemerchantTools::GetHttpHost().$_REQUEST["URL_DATA_FILE_EXPORT"], GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_EXPORTED2" ) );

                    file_put_contents( $_SERVER["DOCUMENT_ROOT"].$_REQUEST["URL_DATA_FILE_EXPORT"], Bitrix\Main\Web\Json::encode( $arProfiles ) );
                }
            }

            $obAdminList->AddHeaders(
                array(
                    array(
                        "id" => "ID",
                        "content" => "ID",
                        "sort" => "id",
                        "align" => "right",
                        "default" => true,
                    ),
                    array(
                        "id" => "ACTIVE",
                        "content" => GetMessage( "parser_active" ),
                        "sort" => "active",
                        "align" => "left",
                        "default" => true,
                    ),
                    array(
                        "id" => "NAME",
                        "content" => GetMessage( "parser_name" ),
                        "sort" => "name",
                        "default" => true,
                    ),
                    array(
                        "id" => "TYPE",
                        "content" => GetMessage( "parser_type" ),
                        "sort" => "type",
                        "default" => true,
                    ),
                    array(
                        "id" => "TYPE_RUN",
                        "content" => GetMessage( "parser_type_run" ),
                        "sort" => "type_run",
                        "default" => true,
                    ),
                    array(
                        "id" => "TIMESTAMP_X",
                        "content" => GetMessage( "parser_updated" ),
                        "sort" => "timestamp_x",
                        "default" => true,
                    ),
                    array(
                        "id" => "START_LAST_TIME_X",
                        "content" => GetMessage( "parser_start_last_time" ),
                        "sort" => "start_last_time_x",
                        "default" => true,
                    ),
                )
            );

            $arProfileList = $obProfile->GetList(
                array( $by => $order ),
                $arFilter
            );

            $obAdminResult = new CAdminResult( $arProfileList, $sTableID );
            $obAdminResult->NavStart();
            $obAdminList->NavText( $obAdminResult->GetNavPrint( GetMessage( "parser_nav" ) ) );

            while( $arAdminResultRow = $obAdminResult->NavNext( true, "f_" ) ){
                $f_SETUP = unserialize( base64_decode( $f_SETUP ) );
                $row = &$obAdminList->AddRow( $f_ID, $arAdminResultRow );
                $row->AddViewField( "TYPE_RUN", $f_TYPE_RUN == "comp" ? GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_TYPE_COMPONENT" ) : GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_TYPE_CRON" ) );
                $row->AddViewField( "START_LAST_TIME_X", $f_SETUP["LAST_START_EXPORT"] );
            }

            $obAdminList->AddFooter(
                array(
                    array(
                        "title" => GetMessage( "MAIN_ADMIN_LIST_SELECTED" ),
                        "value" => $obAdminResult->SelectedRowsCount()
                    ),
                    array(
                        "counter" => true,
                        "title" => GetMessage( "MAIN_ADMIN_LIST_CHECKED" ),
                        "value" => "0"
                    ),
                )
            );

            $obAdminList->AddGroupActionTable(
                array(
                    "export" => GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_EXPORT_SHORT" ),
                )
            );

            $aContext = array();

            $obAdminList->AddAdminContextMenu( $aContext );
            $obAdminList->CheckListMode();
            $APPLICATION->SetTitle( GetMessage( "post_title" ) );

            require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );

            // Send message and show progress

            if( isset( $_REQUEST["parser_end"] ) && $_REQUEST["parser_end"] == 1 && isset( $_REQUEST["parser_id"] ) && $_REQUEST["parser_id"] > 0 ){
                if( isset( $_GET["SUCCESS"][0] ) ){
                    foreach( $_GET["SUCCESS"] as $success ){
                        CAdminMessage::ShowMessage( array( "MESSAGE" => $success, "TYPE" => "OK" ) );
                    }
                }
                if( isset( $_GET["ERROR"][0] ) ){
                    foreach( $_GET["ERROR"] as $error ){
                        CAdminMessage::ShowMessage( $error );
                    }
                }
            }

            AcritLicence::Show();
            if( strlen( $directMessage ) > 0 ){
                CAdminMessage::ShowMessage( array( "MESSAGE" => $directMessage, "TYPE" => "OK", "HTML" => true ) );
            }
            $obAdminList->DisplayList();
            ?>
            <br/>
            <a href="/bitrix/admin/acrit_googlemerchant_list.php" class="adm-btn"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST" )?></a>&nbsp;&nbsp;&nbsp;

            <?
        }
    }

    if( $_REQUEST["export_import"] == "import" ){
        require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );
        AcritLicence::Show();

        if( $_REQUEST["step"] != 2 ){?>
            <div class="adm-detail-content-wrap">
                <div class="adm-detail-content">
                    <div class="adm-detail-content-item-block" style="height: auto; overflow-y: visible;">
                        <table class="adm-detail-content-table edit-table">
                            <tr class="heading"><td><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_TABLE_IMPORT_TITLE" )?></td></tr>
                            <tr align="center">
                                <td>
                                    <form  name="googlemerchantfile_form" method="post">
                                        <br/>
                                        <label style="font-size: 14px"><b><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_IMPORT_FILE_TITLE" )?></b></label>
                                        &nbsp;&nbsp;&nbsp;
                                        <input type="text" name="URL_DATA_FILE_IMPORT">
                                        <input type="button" value="..." onclick="BtnClick()">
                                        <input type="hidden" name="export_import" value="import">
                                        <input type="hidden" name="step" value="2">
                                        <br/><br/>
                                        <a href="/bitrix/admin/acrit_googlemerchant_export.php" class="adm-btn"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_BACK" )?></a>&nbsp;&nbsp;&nbsp;
                                        <input type="submit" class="adm-btn-save" value="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_PROCESS" )?>">
                                    </form>
                                    <br/><br/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?
            CAdminFileDialog::ShowScript(
                array(
                    "event" => "BtnClick",
                    "arResultDest" => array(
                        "FORM_NAME" => "googlemerchantfile_form",
                        "FORM_ELEMENT_NAME" => "URL_DATA_FILE_IMPORT"
                    ),
                    "arPath" => array( "SITE" => SITE_ID, "PATH" => "/upload" ),
                    "select" => "F", // F - file only, D - folder only
                    "operation" => "O", // O - open, S - save
                    "showUploadTab" => true,
                    "showAddToMenuTab" => false,
                    "fileFilter" => "txt",
                    "allowAllFiles" => true,
                    "SaveConfig" => true,
                )
            );
        }
        else{
            $profilesRow = file_get_contents( $_SERVER["DOCUMENT_ROOT"].$_REQUEST["URL_DATA_FILE_IMPORT"] );
            $arProfileList = Bitrix\Main\Web\Json::decode( $profilesRow );
            $obProfile = new CGooglemerchantProfileDB();
            foreach( $arProfileList as $arProfile ){
                $profileId = $obProfile->Add( $arProfile );
                switch( $arProfile["SETUP"]["TYPE_RUN"] ){
                    case "cron":
                        CGooglemerchantAgent::AddAgent( $profileId );
                        break;
                    case "comp":
                        CGooglemerchantAgent::DelAgent( $profileId );
                        break;
                }
                $arMessageList[] = "<li>[$profileId] {$arProfile["NAME"]}</li>";
            }

            if( count( $arMessageList ) > 0 ){
                $arMessageList = GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST_EXPORTED3" )."<ul>".implode( "\r\n", $arMessageList )."</ul>";
                CAdminMessage::ShowMessage(
                    array(
                        "MESSAGE" => $arMessageList,
                        "TYPE" => "OK",
                        "HTML" => true
                    )
                );
            }
            ?>
                <br/>
                <a href="/bitrix/admin/acrit_googlemerchant_list.php" class="adm-btn"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_LIST" )?></a>
            <?
        }
    }
}?>

<?require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );?>