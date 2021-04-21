<?php
$moduleId = "acrit.googlemerchant";
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$moduleId.'/prolog.php');

use Bitrix\Main\SystemException;

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

    IncludeModuleLangFile( __FILE__ );

    global $ID, $PROFILE, $save, $apply, $copy;

    $lastException = false;

    $obProfile = new CGooglemerchantProfileDB();
    $obProfileUtils = new CGooglemerchantProfile();

    CUtil::InitJSCore( array( "ajax", "jquery" ) );
    $APPLICATION->AddHeadScript( "/bitrix/js/iblock/iblock_edit.js" );
    $APPLICATION->AddHeadScript( "/bitrix/js/acrit.googlemerchant/main.js" );
    $t = CJSCore::getExtInfo( "jquery" );

    if( !is_array( $t ) || !isset( $t["js"] ) || !file_exists( $DOCUMENT_ROOT.$t["js"] ) ){
        try{
            throw new SystemException( GetMessage( "ACRIT_GOOGLEMERCHANT_JQUERY_REQUIRE" ) );
        }
        catch( SystemException $exception ){
            global $lastException;
            $lastException = $exception->getMessage();
        }
    }

    if( !CModule::IncludeModule( "iblock" ) ){
        return false;
    }

    $catalog = ( !CModule::IncludeModule( "catalog" ) ) ? false : true;
    $currency = ( !CModule::IncludeModule( "currency" ) ) ? false : true;

    IncludeModuleLangFile( __FILE__ );

    $POST_RIGHT = $APPLICATION->GetGroupRight( $moduleId );
    if( $POST_RIGHT == "D" ){
        $APPLICATION->AuthForm( GetMessage( "ACCESS_DENIED" ) );
    }

    $ID = intval( $ID );        // Id of the edited record
    $bCopy = ( $action == "copy" );
    $message = null;
    $bVarsFromForm = false;

    if( isset( $ID ) && ( intval( $ID ) > 0 ) ){
        $arProfile = $obProfile->GetByID( $ID );
    }
    else{
        $arProfile = $obProfileUtils->GetDefaults();
    }

    $profileTitle = GetMessage( "ACRIT_GOOGLEMERCHANT_EDITPROFILE" ).": #".$arProfile["ID"]." ".$arProfile["NAME"];
    $APPLICATION->SetTitle( ( $ID > 0 ? $profileTitle : GetMessage( "ACRIT_GOOGLEMERCHANT_ADDPROFILE" ) ) );
    if( $copy && $ID > 0 ){
        unset( $arProfile["ID"] );

        $arProfile["TIMESTAMP_X"] = CDatabase::FormatDate( $arProfile["TIMESTAMP_X"], "YYYY-MM-DD HH:MI:SS", "DD.MM.YYYY HH:MI:SS" );

        $ID = $obProfile->Add( $arProfile );
        if( !$ID ){
            LocalRedirect( $APPLICATION->GetCurPageParam( "", array( "ID", "copy" ) ) );
        }
        else{
            LocalRedirect( $APPLICATION->GetCurPageParam( "ID=$ID", array( "ID", "copy" ) ) );
        }
        die();
    }

    $aContext = array(
        array(
            "TEXT" => GetMessage( "MAIN_ADD" ),
            "LINK" => "acrit_googlemerchant_edit.php?lang=".LANG,
            "TITLE" => GetMessage( "PARSER_ADD_TITLE" ),
            "ICON" => "btn_new",
        ),
    );

    // add attach it to list
    $sTableID = "tbl_acritprofile";
    $oSort = new CAdminSorting( $sTableID, "ID", "desc" );
    $lAdmin = new CAdminList( $sTableID, $oSort );
    $lAdmin->AddAdminContextMenu( $aContext );
    $lAdmin->CheckListMode();

    require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );

    AcritLicence::Show();

    $aMenu = array(
        array(
            "TEXT" => GetMessage( "ACRIT_GOOGLEMERCHANT_LIST" ),
            "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_LIST" ),
            "LINK" => "acrit_googlemerchant_list.php?lang=".LANG,
            "ICON" => "btn_list",
        ),
        array(
            "TEXT" => GetMessage( "ACRIT_GOOGLEMERCHANT_SAVE" ),
            "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_SAVE" ),
            "LINK" => "javascript:$('.adm-btn-save').click()",
            "ICON" => "",
        ),
        array(
            "TEXT" => GetMessage( "ACRIT_GOOGLEMERCHANT_INSTRUCTION" ),
            "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_INSTRUCTION" ),
            "LINK" => "http://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/",
            "LINK_PARAM" => "target='blank'",
            "ICON" => "",
        ),
    );

    if( $ID > 0 ){
        $aMenu[] = array( "SEPARATOR" => "Y" );
        $aMenu[] = array(
            "TEXT" => GetMessage( "ACRIT_GOOGLEMERCHANT_ADD" ),
            "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_ADD" ),
            "LINK" => "acrit_googlemerchant_edit.php?lang=".LANG,
            "ICON" => "btn_new",
        );
        $aMenu[] = array(
            "TEXT" => GetMessage( "ACRIT_GOOGLEMERCHANT_COPY" ),
            "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_COPY" ),
            "LINK" => "acrit_googlemerchant_edit.php?copy=$ID&ID=$ID&lang=".LANG,
            "ICON" => "btn_copy",
        );
        $aMenu[] = array(
            "TEXT" => GetMessage( "ACRIT_GOOGLEMERCHANT_DEL" ),
            "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_DEL" ),
            "LINK" => "javascript:if(confirm('".GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_DELETE_CONFIRM" )."'))window.location='acrit_googlemerchant_list.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
            "ICON" => "btn_delete",
        );
        $aMenu[] = array( "SEPARATOR" => "Y" );
        $aMenu[] = array(
            "TEXT" => GetMessage( "ACRIT_GOOGLEMERCHANT_RUN" ),
            "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_RUN" ),
            "LINK" => "/bitrix/tools/acrit.googlemerchant/acrit_googlemerchant.php?ID=".$ID,
						"PUBLIC" => "Y",
            "ICON" => "btn_start_catalog"
        );
    }
    $context = new CAdminContextMenu( $aMenu );
    $context->Show();

    $siteEncoding = array(
        "utf-8" => "utf8",
        "UTF8" => "utf8",
        "UTF-8" => "utf8",
        "WINDOWS-1251" => "cp1251",
        "windows-1251" => "cp1251",
        "CP1251" => "cp1251",
    );

    CModule::IncludeModule( "catalog" );

    function CheckFields(){
        global $PROFILE, $APPLICATION, $ID;
        if( intval( $ID ) > 0 ){
            $export = new CAcritGooglemerchantExport( $ID );
        }
        $requiredFields = array(
            "NAME", "CODE", "SHOPNAME", "COMPANY", "DOMAIN_NAME", "IBLOCK_ID"
        );

        foreach( $requiredFields as $field ){
            if( !$PROFILE[$field] ){
                try{
                    throw new SystemException( GetMessage( "ACRIT_GOOGLEMERCHANT_REQUIRED_INPUT_FIELD_FAIL", array( "#CODE#" => $field, "#NAME#" => GetMessage( "ACRIT_GOOGLEMERCHANT_STEP1_".$field ) ) ) );
                }
                catch( SystemException $exception ){
                    global $lastException;
                    $lastException = $exception->getMessage();
                }
                return false;
            }
        }

        foreach( $PROFILE["XMLDATA"] as $id => $field ){
            if( $field["REQUIRED"] == "Y" ){
                if( ( $field["TYPE"] == "field" && !$field["VALUE"] ) || ( $field["TYPE"] == "const" && !$field["CONTVALUE_TRUE"] ) || $field["TYPE"] == "none" ){
                    try{
                        throw new SystemException( GetMessage( "ACRIT_GOOGLEMERCHANT_REQUIRED_FIELD_FAIL", array( "#CODE#" => $field["CODE"], "#NAME#" => $field["NAME"] ) ) );
                    }
                    catch( SystemException $exception ){
                        global $lastException;
                        $lastException = $exception->getMessage();
                    }
                    return false;
                }
            }
        }

        if( $PROFILE["USE_MARKET_CATEGORY"] == "Y" ){
            if( is_array( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] )
                && !empty( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] )
            ){
                foreach( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] as $categoryItem ){
                    if( strlen( trim( $categoryItem ) ) <= 0 ){
                        try{
                            throw new SystemException( GetMessage( "ACRIT_GOOGLEMERCHANT_EMPTY_MARKET_CATEGORY_FIELDS" ) );
                        }
                        catch( SystemException $exception ){
                            global $lastException;
                            $lastException = $exception->getMessage();
                        }
                        return false;
                    }
                }

            }
            else{
                try{
                    throw new SystemException( GetMessage( "ACRIT_GOOGLEMERCHANT_EMPTY_MARKET_CATEGORY_FIELDS" ) );
                }
                catch( SystemException $exception ){
                    global $lastException;
                    $lastException = $exception->getMessage();
                }
                return false;
            }
        }

        return true;
    }

    function PrepareFields(){
        global $PROFILE, $APPLICATION, $ID;

        foreach( $PROFILE["XMLDATA"] as $id => $field ){
            if( $field["TYPE"] == "none" ){
                $PROFILE["XMLDATA"][$id]["VALUE"] = "";
                $PROFILE["XMLDATA"][$id]["CONTVALUE_TRUE"] = "";
                $PROFILE["XMLDATA"][$id]["CONTVALUE_FALSE"] = "";
            }
        }
        return true;
    }

    $fieldsCheck = true;

    $bUnlockMode = file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$moduleId."/export_{$ID}_run.unlock" );

    if( $_SERVER["REQUEST_METHOD"] == "POST" && ( !empty( $save ) || !empty( $apply ) ) && !$bUnlockMode && check_bitrix_sessid() ){
        if( is_array( $PROFILE["SETUP"]["IBLOCK_TREE"] ) && !empty( $PROFILE["SETUP"]["IBLOCK_TREE"] ) ){
            $PROFILE["IBLOCK_TYPE_ID"] = array();
            $PROFILE["IBLOCK_ID"] = array();
            $PROFILE["CATEGORY"] = array();

            foreach( $PROFILE["SETUP"]["IBLOCK_TREE"] as $selectedItem ){
                $arSelectedItemData = explode( ":", $selectedItem );

                switch( $arSelectedItemData[0] ){
                    case "ibtype":
                        $PROFILE["IBLOCK_TYPE_ID"][] = $arSelectedItemData[1];
                        break;
                    case "ib":
                        $PROFILE["IBLOCK_ID"][] = $arSelectedItemData[1];
                        break;
                    case "section":
                        $PROFILE["CATEGORY"][] = $arSelectedItemData[1];
                        break;
                    default:
                        break;
                }
            }

            unset( $PROFILE["SETUP"]["IBLOCK_TREE"] );
        }

        if( ( $fieldsCheck = CheckFields() ) ){
            PrepareFields();
            if( CModule::IncludeModule( "catalog" ) ){
                $obCond = new CAcritGooglemerchantCatalogCond();

                $boolCond = $obCond->Init( BT_COND_MODE_PARSE, 0, array() );
                if( !$boolCond ){
                    if( $lastException ){
                        CAdminMessage::ShowMessage(
                            array(
                                "MESSAGE" => $lastException,
                                "HTML" => "TRUE",
                            )
                        );
                    }
                }
            }

            foreach( $PROFILE["XMLDATA"] as $id => $field ){
                if( intval( $id ) > 0 ){
                    $PROFILE["XMLDATA"][$field["CODE"]] = $field;
                    unset( $PROFILE["XMLDATA"][$id] );
                }
            }
            foreach( $PROFILE["XMLDATA"] as $id => $field ){
                if( !empty( $field["CONDITION"] ) && CModule::IncludeModule( "catalog" ) ){
                    $PROFILE["XMLDATA"][$id]["CONDITION"] = $obCond->Parse( $field["CONDITION"] );
                }

                if( !isset( $field["PROCESS_LOGIC"] ) ){
                    $PROFILE["XMLDATA"][$id]["PROCESS_LOGIC"] = "N";
                }

                if( !isset( $field["DELETE_ONEMPTY"] ) ){
                    $PROFILE["XMLDATA"][$id]["DELETE_ONEMPTY"] = "N";
                }

                if( !isset( $field["DELETE_ONEMPTY_FORCE"] ) ){
                    $PROFILE["XMLDATA"][$id]["DELETE_ONEMPTY_FORCE"] = "N";
                }

                if( !isset( $field["DELETE_ONEMPTY_ROWFORCE"] ) ){
                    $PROFILE["XMLDATA"][$id]["DELETE_ONEMPTY_ROWFORCE"] = "N";
                }

                if( !isset( $field["HTML_ENCODE"] ) ){
                    $PROFILE["XMLDATA"][$id]["HTML_ENCODE"] = "N";
                }

                if( !isset( $field["HTML_ENCODE_CUT"] ) ){
                    $PROFILE["XMLDATA"][$id]["HTML_ENCODE_CUT"] = "N";
                }

                if( !isset( $field["HTML_TO_TXT"] ) ){
                    $PROFILE["XMLDATA"][$id]["HTML_TO_TXT"] = "N";
                }

                if( !isset( $field["SKIP_UNTERM_ELEMENT"] ) ){
                    $PROFILE["XMLDATA"][$id]["SKIP_UNTERM_ELEMENT"] = "N";
                }
            }

            if( !empty( $PROFILE["CONDITION"] ) && CModule::IncludeModule( "catalog" ) ){
                $PROFILE["CONDITION"] = $obCond->Parse( $PROFILE["CONDITION"] );
            }

            $titlePos = stripos( $PROFILE["FORMAT"], "<title>" );
            if( $titlePos !== false ){
                $titleFinalPos = stripos( $PROFILE["FORMAT"], "<link>" );
                if( $titleFinalPos !== false ){
                    $PROFILE["FORMAT"] = substr_replace( $PROFILE["FORMAT"], "", $titlePos, ( $titleFinalPos - $titlePos ) );
                }
            }

            $PROFILE["FORMAT"] = str_replace( "<link>", "<title>".$PROFILE["GOOGLE_GOOGLEFEED"]."</title>".PHP_EOL."<link>", $PROFILE["FORMAT"] );

            $temp = array();

            if( isset( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] ) && is_array( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] ) ){
	            $bNotEmptyMarketCategoryListValue = false;
                foreach( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] as $k => $v ){
	                $bNotEmptyCurrentMarketCategoryListValue = false;
                    if( strlen( trim( $v ) ) > 0 ){
                        if( !$bNotEmptyMarketCategoryListValue ){
                            $bNotEmptyMarketCategoryListValue = true;
                        }
                        $bNotEmptyCurrentMarketCategoryListValue = true;
                    }

                    if( $bNotEmptyCurrentMarketCategoryListValue ){
                        $rsParentSection = CIBlockSection::GetByID( $k );
	                    if( $arParentSection = $rsParentSection->GetNext() ){
	                        $temp[$k] = $arParentSection["IBLOCK_SECTION_ID"];

	                        $arFilter = array(
                                "IBLOCK_ID" => $arParentSection["IBLOCK_ID"],
                                ">LEFT_MARGIN" => $arParentSection["LEFT_MARGIN"],
                                "<RIGHT_MARGIN" => $arParentSection["RIGHT_MARGIN"],
                                ">DEPTH_LEVEL" => $arParentSection["DEPTH_LEVEL"]
                            );

	                        $rsSect = CIBlockSection::GetList(
                                array(
                                    "left_margin" => "asc"
                                ),
                                $arFilter,
                                false,
                                array(
                                    "ID",
                                    "IBLOCK_SECTION_ID"
                                )
                            );

                            while( $arSect = $rsSect->GetNext() ){
	                            $temp[$arSect["ID"]] = $arSect["IBLOCK_SECTION_ID"];
	                        }
	                    }
                    }
	            }
		    }

            if( $bNotEmptyMarketCategoryListValue ){
                $maxDepth = 0;
                $i = 0;
                $rsSect = CIBlockSection::GetList( array( "DEPTH_LEVEL" => "DESC" ), array( ">IBLOCK_ID" => 0 ), false, array( "DEPTH_LEVEL" ) );
                while( $arSect = $rsSect->GetNext() ){
                    $i++;
                    $maxDepth = $arSect["DEPTH_LEVEL"];
                    if( $i == 1 ){
                        break;
                    }
                }

                foreach( $temp as $k => $v ){
                    $tempCatName = "";
                    if( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k] ){
                        $tempCatName = $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k];
                    }

                    $j = 0;
                    while( $j++ < $maxDepth ){
                        foreach( $temp as $k_ => $v_ ){
                            if( $v_ == $k ){
                                if( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k_] ){
                                    $tempCatName = $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k_];
                                }
                                else{
                                    if( $tempCatName ){
                                        $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k_] = $tempCatName;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else{
                $PROFILE["MARKET_CATEGORY"] = null;
            }
            unset( $temp );

            if( $PROFILE["CURRENCY"]["CONVERT_CURRENCY"] != "Y" ){
                $PROFILE["CURRENCY"] = null;
            }

            if( isset( $ID ) && ( intval( $ID ) > 0 ) ){
                if(
                    ( $arProfile["TYPE_RUN"] == "comp" )
                    && !isset( $PROFILE["TYPE_RUN"] )
                    && isset( $PROFILE["SETUP"]["TYPE_RUN"] )
                    && ( $PROFILE["SETUP"]["TYPE_RUN"] == "cron" )
                ){
                    $arIntersectAgents = CGooglemerchantAgent::GetIntersectAgents( $PROFILE["SETUP"]["CRON"] );
                    if( is_array( $arIntersectAgents ) && !empty( $arIntersectAgents ) ){
                        echo BeginNote();
                        echo GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_HAS_INTERSECT_AGENTS" );
                        foreach( $arIntersectAgents as $arIntersectAgentsItem ){
                            echo $arIntersectAgentsItem["NAME"].": ".$arIntersectAgentsItem["NEXT_EXEC"]."<br/>";
                        }
                        echo GetMessage( "ACRIT_GOOGLEMERCHANT_PROFILE_HAS_INTERSECT_AGENTS_END" );
                        echo EndNote();
                    }
                }
            }

            if( is_array( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] ) && !empty( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] ) ){
                array_walk( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"], "CAcritGooglemerchantStringProcess::AcritArrayTrimFunc" );
            }

            $arFields = array(
                "ACTIVE" => ( $PROFILE["ACTIVE"] != "Y" ) ? "N" : "Y",
                "NAME" => $PROFILE["NAME"],
                "CODE" => $PROFILE["CODE"],
                "DESCRIPTION" => $PROFILE["DESCRIPTION"],
                "SHOPNAME" => $PROFILE["SHOPNAME"],
                "COMPANY" => $PROFILE["COMPANY"],
                "DOMAIN_NAME" => $PROFILE["DOMAIN_NAME"],
                "LID" => $PROFILE["LID"],
                "ENCODING" => $PROFILE["ENCODING"],
                "IBLOCK_TYPE_ID" => $PROFILE["IBLOCK_TYPE_ID"],
                "IBLOCK_ID" => $PROFILE["IBLOCK_ID"],
                "CATEGORY" => $PROFILE["CATEGORY"],
                "USE_SKU" => ( $PROFILE["USE_SKU"] != "Y" ) ? "N" : "Y",
                "CHECK_INCLUDE" => ( $PROFILE["CHECK_INCLUDE"] != "Y" ) ? "N" : "Y",
                "VIEW_CATALOG" => ( $PROFILE["VIEW_CATALOG"] != "Y" ) ? "N" : "Y",
                "OTHER" => ( $PROFILE["OTHER"] != "Y" ) ? "N" : "Y",
                "TYPE" => $PROFILE["TYPE"],
                "NAMESCHEMA" => $PROFILE["NAMESCHEMA"],
                "FORMAT" => $PROFILE["FORMAT"],
                "OFFER_TEMPLATE" => $PROFILE["OFFER_TEMPLATE"],
                "CURRENCY_TEMPLATE" => $PROFILE["CURRENCY_TEMPLATE"],
                "CATEGORY_TEMPLATE" => $PROFILE["CATEGORY_TEMPLATE"],
                "CATEGORY_INNER_TEMPLATE" => $PROFILE["CATEGORY_INNER_TEMPLATE"],
                "DATEFORMAT" => $PROFILE["DATEFORMAT"],
                "SITE_PROTOCOL" => $PROFILE["SITE_PROTOCOL"],
                "XMLDATA" => $PROFILE["XMLDATA"],
                "CONVERT_DATA" => $PROFILE["CONVERT_DATA"],
                "CONDITION" => $PROFILE["CONDITION"],
                "CURRENCY" => $PROFILE["CURRENCY"],
                "MARKET_CATEGORY" => $PROFILE["MARKET_CATEGORY"],
                "EXPORT_PARENT_CATEGORIES" => ( $PROFILE["EXPORT_PARENT_CATEGORIES"] == "Y" ) ? "Y" : "N",
                "EXPORT_PARENT_CATEGORIES_TO_OFFER" => ( $PROFILE["EXPORT_PARENT_CATEGORIES_TO_OFFER"] == "Y" ) ? "Y" : "N",
                "EXPORT_OFFER_CATEGORIES_TO_OFFER" => ( $PROFILE["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ) ? "Y" : "N",
                "EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS" => ( $PROFILE["EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS"] == "Y" ) ? "Y" : "N",
                "EXPORT_DATA_OFFER" => ( $PROFILE["EXPORT_DATA_OFFER"] == "Y" ) ? "Y" : "N",
								"SKIP_WITH_SKU" => ( $PROFILE["SKIP_WITH_SKU"] == "Y" ) ? "Y" : "N",
                "EXPORT_DATA_OFFER_WITH_SKU_DATA" => ( $PROFILE["EXPORT_DATA_OFFER_WITH_SKU_DATA"] == "Y" ) ? "Y" : "N",
                "EXPORT_DATA_SKU" => ( $PROFILE["EXPORT_DATA_SKU"] == "Y" ) ? "Y" : "N",
                "EXPORT_DATA_SKU_BY_OFFER" => ( $PROFILE["EXPORT_DATA_SKU_BY_OFFER"] == "Y" ) ? "Y" : "N",
                "USE_COMPRESS" => ( $PROFILE["USE_COMPRESS"] == "Y" ) ? "Y" : "N",
                "USE_MARKET_CATEGORY" => ( $PROFILE["USE_MARKET_CATEGORY"] == "Y" ) ? "Y" : "N",
                "CHANGE_MARKET_CATEGORY" => ( $PROFILE["CHANGE_MARKET_CATEGORY"] == "Y" ) ? "Y" : "N",
                "SETUP" => $PROFILE["SETUP"],
                "USE_VARIANT" => "N",
                "VARIANT" => null,
                "OZON_APPID" => null,
                "OZON_APPKEY" => null,
                "HOTLINE_FIRM_ID" => null,
                "GOOGLE_GOOGLEFEED" => $PROFILE["GOOGLE_GOOGLEFEED"],
                "TIMESTAMP_X" => date( "d.m.Y H:i:s" ),
                "SEND_LOG_EMAIL" => ( check_email( $PROFILE["SEND_LOG_EMAIL"] ) ? $PROFILE["SEND_LOG_EMAIL"] : "" ),
                "USE_IBLOCK_CATEGORY" => ( $PROFILE["USE_IBLOCK_CATEGORY"] == "Y" ) ? "Y" : "N",
                "USE_EMPTY_TAG_CUT" => ( $PROFILE["USE_EMPTY_TAG_CUT"] == "Y" ) ? "Y" : "N",
                "USE_AUTOPRICE" => ( $PROFILE["USE_AUTOPRICE"] == "Y" ) ? "Y" : "N",
            );

            if( $ID ){
                $obProfile->Update( $ID, $arFields );
            }
            else{
                $ID = $obProfile->Add( $arFields );
            }

            switch( $PROFILE["SETUP"]["TYPE_RUN"] ){
                case "cron":
                    if( $PROFILE["ACTIVE"] == "Y" ){
                        CGooglemerchantAgent::AddAgent( $ID );
                    }
                    break;
                case "comp":
                    CGooglemerchantAgent::DelAgent( $ID );
                    break;
            }

            if( $PROFILE["ACTIVE"] != "Y" ){
                CGooglemerchantAgent::DelAgent( $ID );
            }

            if( $save ){
                LocalRedirect( "acrit_googlemerchant_list.php" );
                die();
            }
            else{
                $query = parse_url( $_SERVER["REQUEST_URI"], PHP_URL_QUERY );
                parse_str( $query, $arQuery );

                if( !isset( $arQuery["ID"] ) || empty( $arQuery["ID"] ) ){
                    $arQuery["ID"] = $ID;
                    LocalRedirect( "acrit_googlemerchant_edit.php?".http_build_query( $arQuery ) );
                    die();
                }
            }
        }
    }

    if( $fieldsCheck ){
        if( isset( $ID ) && ( intval( $ID ) > 0 ) ){
            $arProfile = $obProfile->GetByID( $ID );
        }
    }
    else{
        $arProfile = $PROFILE;
    }

    if( !isset( $_REQUEST["ajax"] ) && !isset( $_REQUEST["ib"] ) && !isset( $_REQUEST["ajax_start"] ) && !isset( $_REQUEST["ajax_count"] ) && !isset( $_POST["auth"] ) ){
        $arTabs = array(
            array(
                "DIV" => "step3",
                "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB3" ),
                "ICON" => "main_user_edit",
                "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB3" )
            ),
        );

        $arTabs[] = array( "DIV" => "step18", "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB18" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB18" ) );
        $arTabs[] = array( "DIV" => "step1", "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB1" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB1" ) );
        $arTabs[] = array( "DIV" => "step4", "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB4" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB4" ) );
        $arTabs[] = array( "DIV" => "step5", "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB5" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB5" ) );

        if( CModule::IncludeModule( "catalog" ) ){
            $arTabs[] = array( "DIV" => "step7", "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB7" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB7" ) );
            $arTabs[] = array( "DIV" => "step6", "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB6" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB6" ) );
        }

        $arTabs[] = array( "DIV" => "step9", "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB9" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB9" ) );
        $arTabs[] = array( "DIV" => "step15", "TAB" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB15" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_GOOGLEMERCHANT_TAB15" ) );

        $tabControl = new CAdminTabControl( "tabControl", $arTabs );

        if( $lastException ){
            CAdminMessage::ShowMessage(
                array(
                    "MESSAGE" => $lastException,
                    "HTML" => "TRUE",
                )
            );
        }

        require __DIR__."/auto_tests.php";

        $bNeedPayment = CAcritGooglemerchantTools::CheckProfileDefaults( $arProfile );
        $needPaymentUrl = "https://www.acrit-studio.ru/services/settings-trading-platforms/";

        $bDemo = false;
        $moduleStatus = CModule::IncludeModuleEx( $moduleId );
        if( $moduleStatus != 1 ){
            $bDemo = true;

            $timeDemoOff = "";
            if( $info = CModule::CreateModuleObject( $moduleId ) ){
                $timeDemoOff = ConvertTimeStamp( $GLOBALS["SiteExpireDate_".str_replace( ".", "_", $info->MODULE_ID )], "SHORT" );
            }
        }

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

        $buyLicenceUrl = "http://www.acrit-studio.ru/market/rabota-s-torgovymi-ploshchadkami/eksport-tovarov-na-torgovye-portaly/?action=BUY&id=8319";
        ?>
        <div class="adm-info-message"<?if( $bDemo ){?> style="float: left;"<?}?>><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_CHANGE_PROFILE_INFO" );?></div>

        <?if( $bNeedPayment ){?>
            <div class="adm-info-message" style="float: right; padding-left: 20px;">
                <div style="padding-left: 20px; float: right;">
                    <a href="<?=$needPaymentUrl?>" target="_blank" class="adm-btn adm-btn-save"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_NEED_PAYMENT_INFO" )?></a>
                </div>
                <div style="float: left; padding-top: 5px;"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_NEED_PAYMENT_INFO_TEXT" )."<b>".$timeDemoOff."</b>";?></div>
                <div style="clear: both;"></div>
            </div>
        <?}?>

        <?if( $bDemo ){?>
            <div class="adm-info-message" style="float: right; padding-left: 20px;">
                <div style="padding-left: 20px; float: right;">
                    <a href="<?=$buyLicenceUrl?>" target="_blank" class="adm-btn adm-btn-save"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_BUY_LICENCE_INFO" )?></a>
                </div>
                <div style="float: left; padding-top: 5px;"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_DEMO_PERIOD_INFO" )."<b>".$timeDemoOff."</b>";?></div>
                <div style="clear: both;"></div>
            </div>
        <?}?>

        <div style="clear: both;"></div>

        <form method="POST" action="" ENCTYPE="multipart/form-data" id="googlemerchant_form" name="googlemerchant_form">
        <?// check session id?>
            <?=bitrix_sessid_post();?>
            <?// show bookmark headers
            $tabControl->Begin();?>
            <div id="waitContainer" style="position: fixed; float: right; width: 100%; right: 0;"></div>
            <?foreach( $arTabs as $tab ){
                $tabControl->BeginNextTab();
                require( __DIR__."/tabs/{$tab["DIV"]}.php" );
            }

    	    // end of form - show save buttons
            $tabControl->Buttons(
                array(
                    "disabled" => ( ( $POST_RIGHT < "W" ) || file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$moduleId."/export_{$arProfile["ID"]}_run.unlock" ) ),
                    "back_url" => "acrit_googlemerchant_list.php?lang=".LANG,
                )
            );

    	    // end of bookmark interface
            $tabControl->End();
            $tabControl->ShowWarnings( "googlemerchant_form", $message );
            ?>
        </form>

        <form target="_blank" name="fticket" action="<?=GetMessage( "A_SUPPORT_URL" );?>" method="POST">
            <input type="hidden" name="send_ticket" value="Y">
            <input type="hidden" name="ticket_title" value="<?=GetMessage( "SC_RUS_L1" )." ".htmlspecialcharsbx( $_SERVER["HTTP_HOST"] );?>">
            <input type="hidden" name="ticket_text" value="Y">
            <input type="hidden" name="ticket_log" value="Y">
        </form>
        <script type="text/javascript">
            function SubmitToSupport(){
                var frm = document.forms.fticket;

                frm.ticket_text.value = BX( 'ticket_text_proxy' ).value;

                if( frm.ticket_text.value == '' ){
                    alert( '<?=GetMessage( "SC_NOT_FILLED" )?>' );
                    return;
                }
                frm.ticket_log.value = BX( 'ticket_text_log' ).value;
                frm.submit();
            }
        </script>
        <script>
            function ShowMarketCategoryList( categoryId, listContainer ){
                MarketCategoryItem = categoryId;
                listContainer = typeof listContainer == 'undefined' ? 'market_category_list' : listContainer;
                if( typeof MarketCategoryObject == 'undefined' ){
                    MarketCategoryObject = new BX.PopupWindow( 'my_answer', null, {
                        content: BX( listContainer ),
                        closeIcon: { right: '20px', top: '20px' },
                        titleBar: { content: BX.create( 'div', { html: '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_POPUP_TITLE" );?>', 'props': { 'className': 'access-title-bar' } } ) },
                        zIndex: 0,
                        offsetLeft: 0,
                        offsetTop: 0,
                        draggable: { restrict: false },
                        buttons: [
                        ],
                    });
                }
                MarketCategoryObject.show();
            }

            function ShowFieldsList( containerPrefix, fieldId, compositeId, selectedFieldId ){
                FieldsListItem = fieldId;

                listContainer = containerPrefix + fieldId + ( ( compositeId != false ) ? '-' + compositeId : '' ) + '-list';

                if( typeof FieldsListObject != 'undefined' ){
                    BX.remove( FieldsListObject );
                }

                var selectBlockHtml = $( '#select_fields' ).html();
                var selectBlockHtmlEx = $.parseHTML( selectBlockHtml );

                if( selectedFieldId.length > 0 ){
                    $( 'option', selectBlockHtmlEx ).each( function(){
                        if( $( this ).val() == selectedFieldId ){
                            $( this ).addClass( 'tagselected' );
                        }
                    });
                }
                $( '#' + listContainer + ' select' ).html( selectBlockHtmlEx );

                FieldsListObject = new BX.PopupWindow( 'select_field' + containerPrefix + fieldId + ( ( compositeId != false ) ? compositeId : '' ), null, {
                    content: BX( listContainer ),
                    closeIcon: { right: '20px', top: '20px' },
                    titleBar: { content: BX.create( 'div', { html: '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDS_LIST_POPUP_TITLE" );?>', 'props': { 'className': 'access-title-bar' } } ) },
                    zIndex: 0,
                    offsetLeft: 0,
                    offsetTop: 0,
                    draggable: { restrict: false },
                    buttons: [
                    ],
                });

                FieldsListObject.show();
								
								$('#'+listContainer+' input').trigger('keyup');
            }

            function ShowPropertyList( obj ){
                PropertyListItem = $( obj ).attr( 'name' );
                PropertyListItemValue = $( obj ).attr( 'data-value' );
                $( '#property_list select' ).val( $( 'input[name="' + PropertyListItemValue + '"]' ).val() );
                if( typeof PropertyListObject == 'undefined' ){
                    PropertyListObject = new BX.PopupWindow( 'property_list_answer', null, {
                        content: BX( 'property_list' ),
                        closeIcon: { right: '20px', top: '10px' },
                        titleBar: { content: BX.create( 'span', { html: '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_VARIANT_CATEGORY_POPUP_TITLE" );?>', 'props': { 'className': 'access-title-bar' } } ) },
                        zIndex: 0,
                        offsetLeft: 0,
                        offsetTop: 0,
                        draggable: { restrict: false },
                    });
                }
                PropertyListObject.show();
            }
        </script>
    <?}
}?>

<?require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );?>