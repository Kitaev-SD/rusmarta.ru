<?php
$moduleId = "acrit.googlemerchant";
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$moduleId.'/prolog.php');
IncludeModuleLangFile(__FILE__);

# Redirect OLD -> NEW (if old core is disabled in options)
if(\Bitrix\Main\Config\Option::get($moduleId, 'disable_old_core') == 'Y'){
	LocalRedirect('/bitrix/admin/acrit_'.end(explode('.', $moduleId)).'_new_list.php?lang='.LANGUAGE_ID);
}

$moduleStatus = CModule::IncludeModuleEx( $moduleId );
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
    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$moduleId/include.php" );

    CModule::IncludeModule( $moduleId );

    CUtil::InitJSCore( array( "ajax", "jquery" ) );
    $APPLICATION->AddHeadScript( "/bitrix/js/iblock/iblock_edit.js" );
    $APPLICATION->AddHeadScript( "/bitrix/js/$moduleId/main.js" );
    $t = CJSCore::getExtInfo( "jquery" );

    if( !is_array( $t ) || !isset( $t["js"] ) || !file_exists( $DOCUMENT_ROOT.$t["js"] ) ){
        $APPLICATION->ThrowException( GetMessage( "ACRIT_GOOGLEMERCHANT_JQUERY_REQUIRE" ) );
    }

    $POST_RIGHT = $APPLICATION->GetGroupRight( $moduleId );
    if( $POST_RIGHT == "D" )
        $APPLICATION->AuthForm( GetMessage( "ACCESS_DENIED" ) );

    if( !CModule::IncludeModule( "iblock" ) ){
        return false;
    }

    IncludeModuleLangFile( __FILE__ );

    $sTableID = "tbl_acritprofile";

    function CheckFilter(){
        global $FilterArr, $lAdmin;
        if( is_array( $FilterArr ) && !empty( $FilterArr ) ){
            foreach( $FilterArr as $f ){
                global $$f;
            }
        }
        return true;
    }

    $oSort = new CAdminSorting( $sTableID, "ID", "desc" );
    $lAdmin = new CAdminList( $sTableID, $oSort );
    $cData = new CGooglemerchantProfileDB();

    $FilterArr = array(
        "find",
        "find_id",
        "find_name",
        "find_active",
        "find_type",
        "find_type_run",
        "find_timestamp",
        "find_start_last_time",
    );

    $lAdmin->InitFilter( $FilterArr );
    if( CheckFilter() ){
        $arFilter = array(
            "ID" => ( $find != "" && $find_type == "id" ? $find : $find_id ),
            "NAME" => $find_name,
            "ACTIVE" => $find_active,
            "TYPE" => $find_type,
            "TYPE_RUN" => $find_type_run,
            "TIMESTAMP" => $find_timestamp_1,
            "START_LAST_TIME" => $find_start_last_time_1,
        );
    }

    if( $lAdmin->EditAction() && ( $POST_RIGHT == "W" ) ){
        if( is_array( $FIELDS ) && !empty( $FIELDS ) ){
            foreach( $FIELDS as $ID => $arFields ){
                if( !$lAdmin->IsUpdated( $ID ) ){
                    continue;
                }

                $DB->StartTransaction();
                $ID = IntVal($ID);
                if( !$ob->Update( $ID, $arFields ) ){
                    $lAdmin->AddUpdateError( GetMessage( "export_save_err" ).$ID.": ".$ob->LAST_ERROR, $ID );
                    $DB->Rollback();
                }
                $DB->Commit();
            }
        }
    }

    if( ( $arID = $lAdmin->GroupAction() ) && $POST_RIGHT == "W" ){
        // if selected "for all elements"
        if( $_REQUEST["action_target"] == "selected" ){
            $rsData = $cData->GetList(
                array( $by => $order ),
                $arFilter
            );

            while( $arRes = $rsData->Fetch() ){
                $arID[] = $arRes["ID"];
            }
        }

        if( is_array( $arID ) && !empty( $arID ) ){
            foreach( $arID as $ID ){
                if( strlen( $ID ) <= 0 )
                    continue;

                $ID = IntVal( $ID );

                switch( $_REQUEST["action"] ){
                    case "delete":
                        @set_time_limit( 0 );
                        $DB->StartTransaction();

                        CGooglemerchantAgent::DelAgent( $ID );
                        if( !$cData->Delete( $ID ) ){
                            $DB->Rollback();
                            $lAdmin->AddGroupError( GetMessage( "rub_del_err" ), $ID );
                        }

                        $DB->Commit();
                        break;

                    case "activate":
                    case "deactivate":
                        if( ( $rsData = $cData->GetByID( $ID ) ) ){
                            $rsData["ACTIVE"] = ( $_REQUEST["action"] == "activate" ? "Y" : "N" );

                            if( $rsData["SETUP"]["TYPE_RUN"] == "cron" ){
                                if( $rsData["ACTIVE"] != "Y" ){
                                    CGooglemerchantAgent::DelAgent( $ID );
                                }
                                else{
                                    CGooglemerchantAgent::AddAgent( $ID );
                                }
                            }
                            else{
                                CGooglemerchantAgent::DelAgent( $ID );
                            }

                            if( !$cData->Update( $ID, $rsData ) ){
                                $lAdmin->AddGroupError( GetMessage( "rub_save_error" ).$cData->LAST_ERROR, $ID );
                            }
                        }
                        else
                            $lAdmin->AddGroupError( GetMessage( "rub_save_error" )." ".GetMessage( "rub_no_rubric" ), $ID );
                        break;
                }
            }
        }
    }

    $lAdmin->AddHeaders(
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
                //"sort" => "start_last_time_x",
                "default" => true,
            ),
            array(
                "id" => "START_NEXT_TIME",
                "content" => GetMessage( "parser_start_next_time" ),
                //"sort" => "start_next_time",
                "default" => true,
            ),
            array(
                "id" => "UNLOADED_OFFERS",
                "content" => GetMessage( "parser_unloaded_offers" ),
                "sort" => "unloaded_offers",
                "default" => true,
            ),
            array(
                "id" => "UNLOADED_OFFERS_CORRECT",
                "content" => GetMessage( "parser_unloaded_offers_correct" ),
                "sort" => "unloaded_offers_correct",
                "default" => true,
            ),
            array(
                "id" => "UNLOADED_OFFERS_ERROR",
                "content" => GetMessage( "parser_unloaded_offers_error" ),
                "sort" => "unloaded_offers_error",
                "default" => true,
            ),
            array(
                "id" => "UNLOADED_OFFERS_STAT",
                "content" => GetMessage( "parser_unloaded_offers_stat" ),
                "default" => true,
            ),
            array(
                "id" => "UNLOADED_OFFERS_SITE",
                "content" => GetMessage( "parser_unloaded_offers_site" ),
                "default" => true,
            ),
        )
    );

    $rsData = $cData->GetList(
        array( $by => $order ),
        $arFilter
    );

    $rsData = new CAdminResult( $rsData, $sTableID );

    $rsData->NavStart();
    $lAdmin->NavText( $rsData->GetNavPrint( GetMessage( "parser_nav" ) ) );

    $rsIBlock = CIBlock::GetList(
        array( "name" => "asc" ),
        array( "ACTIVE" => "Y" )
    );

    while( $arr = $rsIBlock->Fetch() ){
        $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
        $arIBlockFilter["REFERENCE"][] = "[".$arr["ID"]."] ".$arr["NAME"];
        $arIBlockFilter["REFERENCE_ID"][] = $arr["ID"];
    }

    while( $arRes = $rsData->NavNext( true, "f_" ) ){
        $f_SETUP = unserialize( base64_decode( $f_SETUP ) );

        $exportTimeStamp = MakeTimeStamp( $f_SETUP["LAST_START_EXPORT"] );
        $profileTimeStamp = MakeTimeStamp( $arRes["TIMESTAMP_X"] );

        if( $arRes["ACTIVE"] != "Y" ){
            $statVal = "<div style='display: inline-block; width: 12px; height: 12px; margin: 3px 7px 0px 0px; border-radius: 50%; background: #a4a4a4;'></div> ".GetMessage( "parser_unloaded_offers_stat_unactive" );
        }
        elseif( !$exportTimeStamp ){
            $statVal = "<div style='display: inline-block; width: 12px; height: 12px; margin: 3px 7px 0px 0px; border-radius: 50%; background: #ff6600;'></div> ".GetMessage( "parser_unloaded_offers_stat_generate" );
        }
        elseif( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$moduleId."/export_{$arRes["ID"]}_run.lock" ) ){
            $statVal = "<div style='display: inline-block; width: 12px; height: 12px; margin: 3px 7px 0px 0px; border-radius: 50%; background: #ede73c;'></div> ".GetMessage( "parser_unloaded_offers_stat_in_process" );
        }
        elseif( $exportTimeStamp < $profileTimeStamp ){
            $statVal = "<div style='display: inline-block; width: 12px; height: 12px; margin: 3px 7px 0px 0px; border-radius: 50%; background: #ff6600;'></div> ".GetMessage( "parser_unloaded_offers_stat_regenerate" );
        }
        else{
            if( $f_SETUP["TYPE_RUN"] == "comp" ){
                $statVal = "<div style='display: inline-block; width: 12px; height: 12px; margin: 3px 7px 0px 0px; border-radius: 50%; background: #00cc33;'></div> ".GetMessage( "parser_unloaded_offers_stat_finished" );
            }
            else{
                $maxCronProducts = 0;
                if( is_array( $f_SETUP["CRON"] ) && !empty( $f_SETUP["CRON"] ) ){
                    foreach( $f_SETUP["CRON"] as $cronIndex => $arCronRow ){
                        if( $arCronRow["MAXIMUM_PRODUCTS"] > $maxCronProducts ){
                            $maxCronProducts = $arCronRow["MAXIMUM_PRODUCTS"];
                        }
                    }
                }

                if( !$maxCronProducts ){
                    $statVal = "<div style='display: inline-block; width: 12px; height: 12px; margin: 3px 7px 0px 0px; border-radius: 50%; background: #00cc33;'></div> ".GetMessage( "parser_unloaded_offers_stat_finished" );
                }
                else{
                    $unloandedPercent = floor( $arRes["UNLOADED_OFFERS_CORRECT"] / $maxCronProducts * 100 );
                    if( $unloandedPercent >= 100 ){
                        $statVal = "<div style='display: inline-block; width: 12px; height: 12px; margin: 3px 7px 0px 0px; border-radius: 50%; background: #00cc33;'></div> ".GetMessage( "parser_unloaded_offers_stat_finished" );
                    }
                    else{
                        $statVal = "<div style='display: inline-block; width: 12px; height: 12px; margin: 3px 7px 0px 0px; border-radius: 50%; background: #0099cc;'></div> ".GetMessage( "parser_unloaded_offers_stat_in_process_begin" )." ".$arRes["UNLOADED_OFFERS_CORRECT"]." ".GetMessage( "parser_unloaded_offers_stat_in_process_end" ).$unloandedPercent;
                    }
                }
            }
        }

        $row = & $lAdmin->AddRow( $f_ID, $arRes );
        $row->AddViewField( "NAME", '<a href="acrit_googlemerchant_edit.php?ID='.$f_ID."&amp;lang=".LANG.'" title="'.GetMessage( "parser_act_edit" ).'">'.$f_NAME."</a>" );
        $row->AddInputField( "NAME", array( "size" => 20 ) );
        $row->AddViewField( "START_LAST_TIME_X", $f_SETUP["LAST_START_EXPORT"] );
        $row->AddViewField( "START_NEXT_TIME", ( ( $f_TYPE_RUN == "cron" ) ? CGooglemerchantAgent::GetNextAgentTime( $f_ID ) : "" ) );
        $row->AddViewField( "UNLOADED_OFFERS_STAT", $statVal );
        $row->AddViewField( "UNLOADED_OFFERS_SITE", $arRes["LID"] );
        $row->AddViewField( "TYPE_RUN", $f_TYPE_RUN == "comp" ? GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_TYPE_COMPONENT" ) : GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_TYPE_CRON" ) );
        $arActions = array();
        if( $POST_RIGHT == "W" ){
            $arActions[] = array(
                "ICON" => "edit",
                "DEFAULT" => true,
                "TEXT" => GetMessage( "parser_act_edit" ),
                "ACTION" => $lAdmin->ActionRedirect( "acrit_googlemerchant_edit.php?ID=".$f_ID )
            );
        }

        if( $POST_RIGHT == "W" ){
            $arActions[] = array(
                "ICON" => "delete",
                "TEXT" => GetMessage( "parser_act_del" ),
                "ACTION" => "if(confirm('".GetMessage( "parser_act_del_conf" )."')) ".$lAdmin->ActionDoGroup( $f_ID, "delete" )
            );
        }

        if( $POST_RIGHT == "W" ){
            $arActions[] = array(
                "ICON" => "copy",
                "DEFAULT" => true,
                "TEXT" => GetMessage( "parser_act_copy" ),
                "ACTION" => $lAdmin->ActionRedirect( "acrit_googlemerchant_edit.php?copy=$f_ID&ID=$f_ID" )
            );
        }

        if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/$moduleId/export_{$arRes["ID"]}_run.lock" ) ){
            if( $POST_RIGHT == "W" ){
                $arActions[] = array(
                    "ICON" => "unlock",
                    "DEFAULT" => true,
                    "TEXT" => GetMessage( "parser_act_unlock" ),
                    "ACTION" => "UnlockExportExpress( ".$arRes["ID"]." );".$lAdmin->ActionRedirect( "acrit_googlemerchant_list.php" )
                );
            }
        }

        if( !file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/$moduleId/export_{$arRes["ID"]}_run.lock" )
            && ( $f_SETUP["TYPE_RUN"] == "comp" ) ){
                if( $POST_RIGHT == "W" ){
                    $runRow = "/bitrix/tools/$moduleId/acrit_googlemerchant.php?ID=".$arRes["ID"];

                    $arActions[] = array(
                        "ICON" => "run",
                        "DEFAULT" => true,
                        "TEXT" => GetMessage( "parser_act_run" ),
                        "ACTION" => "window.open( '$runRow', '_blank' ); return false;"
                    );
                }
        }

        if( $POST_RIGHT == "W" ){
            $arActions[] = array(
                "ICON" => "export",
                "DEFAULT" => true,
                "TEXT" => GetMessage( "parser_act_export" ),
                "ACTION" => $lAdmin->ActionRedirect( "acrit_googlemerchant_export.php?URL_DATA_FILE_EXPORT=/upload/acrit_googlemerchant_dump_".time().".txt&export_import=export&step=2&ID=$f_ID" )
            );
        }

        $arActions[] = array( "SEPARATOR" => true );
        if( is_set( $arActions[count( $arActions ) - 1], "SEPARATOR" ) ){
            unset( $arActions[count( $arActions ) - 1] );
        }

        $row->AddActions( $arActions );
    }

    $lAdmin->AddFooter(
        array(
            array(
                "title" => GetMessage( "MAIN_ADMIN_LIST_SELECTED" ),
                "value" => $rsData->SelectedRowsCount()
            ),
            array(
                "counter" => true,
                "title" => GetMessage( "MAIN_ADMIN_LIST_CHECKED" ),
                "value" => "0"
            ),
        )
    );

    $lAdmin->AddGroupActionTable(
        array(
            "delete" => GetMessage( "MAIN_ADMIN_LIST_DELETE" ),
            "activate" => GetMessage( "MAIN_ADMIN_LIST_ACTIVATE" ),
            "deactivate" => GetMessage( "MAIN_ADMIN_LIST_DEACTIVATE" ),
        )
    );

    $aContext = array(
        array(
            "TEXT" => GetMessage( "MAIN_ADD" ),
            "LINK" => "acrit_googlemerchant_edit.php?lang=".LANG,
            "TITLE" => GetMessage( "PARSER_ADD_TITLE" ),
            "ICON" => "btn_new",
        ),
				array(
					"TEXT" => GetMessage( "ACRIT_GOOGLEMERCHANT_NEW_MIGRATE" ),
					"LINK" => "acrit_googlemerchant_new_migrator.php?lang=".LANG,
					"TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_NEW_MIGRATE" ),
				),
    );

    $lAdmin->AddAdminContextMenu( $aContext );
    $lAdmin->CheckListMode();
    $APPLICATION->SetTitle( GetMessage( "post_title" ) );

    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );

    // Send message and show progress
    if( isset( $_REQUEST["parser_end"] ) && $_REQUEST["parser_end"] == 1 && isset( $_REQUEST["parser_id"] ) && $_REQUEST["parser_id"] > 0 ){
        if( isset( $_GET["SUCCESS"][0] ) ){
            foreach( $_GET["SUCCESS"] as $success ){
                CAdminMessage::ShowMessage(
                    array(
                        "MESSAGE" => $success,
                        "TYPE" => "OK"
                    )
                );
            }
        }

        if( isset( $_GET["ERROR"][0] ) ){
            foreach( $_GET["ERROR"] as $error ){
                CAdminMessage::ShowMessage( $error );
            }
        }
    }

    AcritLicence::Show();

    $bHasModuleUpdates = CGooglemerchantInformer::GetModuleUpdatesInfo();

    if( !$bHasModuleUpdates ){
        echo BeginNote();
        echo GetMessage( "ACRIT_GOOGLEMERCHANT_NO_UPDATES" );
        echo EndNote();
    }
    else{
        echo BeginNote();
        echo GetMessage( "ACRIT_GOOGLEMERCHANT_NEW_UPDATES" );
        echo EndNote();
    }

    /*if( CGooglemerchantInformer::CheckCRMIntergation() ){
        echo BeginNote();
        echo GetMessage( "ACRIT_BITRIX24_CONNECT" );
        echo EndNote();
    }*/

    echo BeginNote();
    echo GetMessage( "ACRIT_TIME_ZONES_DIFF_DATE" );
    echo EndNote();

    $lAdmin->DisplayList();
}?>

<?require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );?>