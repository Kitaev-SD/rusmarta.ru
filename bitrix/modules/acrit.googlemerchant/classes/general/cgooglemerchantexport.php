<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule( "catalog" );
\Bitrix\Main\Loader::includeModule( "acrit.googlemerchant" );

Loc::loadMessages( __FILE__ );

class CAcritGooglemerchantExport{
    private $profile;
    private $dbMan;
    static $fileExport;
    static $fileExportOut;
    static $firstStepFilename;
    private $baseDir;
    private $lockDir;
    private $documentRoot;
    private $sFileEncoding;

    private $siteEncoding = array(
        "utf-8" => "utf8",
        "UTF-8" => "utf8",
        "WINDOWS-1251" => "cp1251",
        "windows-1251" => "cp1251"
    );

    private $profileEncoding = array(
        "utf8" => "utf-8",
        "cp1251" => "windows-1251",
    );

    const PREPEND = 1;
    const APPEND = 0;
    const REWRITE = 2;

    public function __construct( $profileID ){
        global $exportstep;
        $this->lockDir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.googlemerchant/";


        $this->dbMan = new CGooglemerchantProfileDB();
        $this->profile = $this->dbMan->GetByID( $profileID );

        $siteDocumentRoot = CAcritGooglemerchantTools::GetSiteDocumentRoot( $this->profile["LID"] );
        $this->documentRoot = ( $siteDocumentRoot ) ? $siteDocumentRoot : $_SERVER["DOCUMENT_ROOT"];

        $this->baseDir = $this->documentRoot."/acrit.googlemerchant/";

        self::$fileExport = $this->documentRoot.$this->profile["SETUP"]["URL_DATA_FILE"];
        $this->originalName = self::$fileExport;
        $this->originalNamePath = $this->profile["SETUP"]["URL_DATA_FILE"];
        if( empty( self::$fileExport ) || self::$fileExport == $this->documentRoot ){
            $sessionData = AcritGooglemerchantSession::GetSession( $profileID );
            if( !$exportstep || ( $exportstep == 1 ) ){
                self::$fileExport = $this->baseDir."market_".date( "d_m_Y_H_i", time() ).".xml";
                $sessionData["GOOGLEMERCHANT"]["TMP_NAME"][$this->profile["ID"]] = self::$fileExport;
            }
            else{
                self::$fileExport = $sessionData["GOOGLEMERCHANT"]["TMP_NAME"][$this->profile["ID"]];
            }
        }

        if( strlen( strstr( self::$fileExport, $this->baseDir ) ) > 0 ){
            self::$fileExport = str_replace( $this->baseDir, $this->documentRoot."/upload/acrit.googlemerchant/", self::$fileExport );
            CheckDirPath( dirname( self::$fileExport )."/" );
        }

        self::$fileExportOut = self::$fileExport;
        self::$fileExport = self::$fileExportOut.".tmp";

        $this->sFileEncoding = mb_internal_encoding();

        if( isset( $sessionData ) && !empty( $sessionData ) ){
            AcritGooglemerchantSession::SetSession( $profileID, $sessionData );
        }
    }

    public function Export( $type = "", $cronpage = 0, $processId = 0 ){
        set_time_limit( 0 );
        global $APPLICATION, $USER, $DB, $exportstep, $end;

        if( ( !$this->profile ) || ( $this->profile["ACTIVE"] != "Y" ) ){
            return false;
        }

        $bBaseCron = ( $type == "cron" ) || ( $type == "agent" );

        if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.googlemerchant/export_{$this->profile["ID"]}_run.unlock" )
            || ( $bBaseCron && file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.googlemerchant/export_{$this->profile["ID"]}_run.lock" ) && !$cronpage ) ){
            if( !$bBaseCron ){
                $this->Unlock();
            }
            return false;
        }

        $cronrun = ( $type == "cron" ) || ( $type == "agent" ) || ( ( $type == "" ) && ( intval( $this->profile["SETUP"]["THREADS"] ) > 1 ) );
        if( !$cronpage )
            $cronpage = 0;

        if( $cronrun ){
            $exportstep = $cronpage;
            self::$firstStepFilename = self::$fileExport;
            self::$fileExport .= ".part".$cronpage;

            if( file_exists( self::$fileExport ) )
                unlink( self::$fileExport );
        }
        AcritGooglemerchantSession::Init( $exportstep );

        $bStepExport = false;
        if( ( $this->profile["SETUP"]["TYPE_RUN"] == "cron" ) && ( $this->profile["SETUP"]["CRON"][$processId]["IS_STEP_EXPORT"] == "Y" ) && ( intval( $this->profile["SETUP"]["CRON"][$processId]["STEP_EXPORT_CNT"] )  > 0 ) ){
            $bStepExport = true;
            $iLastSessionExportProductsCnt = intval( $this->profile["LOG"]["PRODUCTS_EXPORT"] );
        }

		$obLog = new CAcritGooglemerchantLog( $this->profile["ID"] );

        // Check on export process is already running
        if( $this->isLock() && ( !$exportstep || ( $exportstep == 1 ) ) ){
            if( $_REQUEST["unlock"] == "Y" ){
                unlink( $this->lockDir."export_{$this->profile["ID"]}_run.lock" );
            }
            else{
                require_once( $this->documentRoot."/bitrix/modules/main/include/prolog_admin_before.php" );
                $APPLICATION->AddHeadScript( "/bitrix/panel/main/admin-public.css" );
                if( $type != "cron" ){
                    echo '<div id="bx-admin-prefix">';
                    CAdminMessage::ShowMessage(
                        array(
                            "MESSAGE" => GetMessage( "ACRIT_GOOGLEMERCHANT_PROCESS_RUN" ),
                            "TYPE" => "FAIL",
                            "HTML" => "TRUE"
                        )
                    );
                    echo "</div>";
                }
                else{
                    $adminEmail = COption::GetOptionString( "main", "email_from" );
                    $subject = GetMessage( "ACRIT_GOOGLEMERCHANT_PROCESS_RUN_SUBJECT" );
                    $errorMessage = GetMessage( "ACRIT_GOOGLEMERCHANT_PROCESS_RUN_ERROR_MESSAGE" );
                    $errorMessage = str_replace( array( "#PROFILE_ID#", "#PROFILE_NAME#" ), array( $this->profile["ID"], $this->profile["NAME"] ), $errorMessage );
                }
                return false;
            }
        }

        $obProfileUtils = new CGooglemerchantProfile();
        $obProfileUtils->GetProfileData();

        if( CModule::IncludeModule( "catalog" ) ){
            $obCond = new CAcritGooglemerchantCatalogCond();
            CAcritGooglemerchantProps::$arIBlockFilter = $obProfileUtils->PrepareIBlock( $this->profile["IBLOCK_ID"], $this->profile["USE_SKU"] );
		    $obCond->Init( BT_COND_MODE_GENERATE, 0, array() );
            $this->profile["EVAL_FILTER"] = $obCond->Generate( $this->profile["CONDITION"], array( "FIELD" => '$GLOBALS["CHECK_COND"]' ) );
            $this->PrepareFieldFilter();
        }

        $elementsObj = new CAcritGooglemerchantElement( $this->profile );
        $elementsObj->SetCronPage($cronpage);

        if( !$end ){
            if( !$exportstep || $exportstep == 1 ){
                $exportstep = 1;
                if( file_exists( self::$fileExport ) )
                    unlink( self::$fileExport );

                AcritGooglemerchantSession::DeleteSession( $this->profile["ID"] );
            }

            if( $cronrun ){
                if( intval( $cronpage ) > 1 ){
                    $ProcessEnd = false;
                    $procResult = $elementsObj->Process( $exportstep, $cronrun, $this->profile["SETUP"]["FILE_TYPE"], self::$fileExport, $this->profile["SETUP"]["URL_DATA_FILE"], $ProcessEnd, $bStepExport, $iLastSessionExportProductsCnt, $processId );

                    echo serialize( array( "procResult" => ( $procResult == true ), "ProcessEnd" => $ProcessEnd ) );
                    exit();
                }
                else{
                    $this->Lock();
                    unlink( self::$fileExport );
                    $threads = new Threads();
                    $sessionData = AcritGooglemerchantSession::GetSession( $this->profile["ID"] );
                    if( isset( $sessionData["GOOGLEMERCHANT"]["THREAD"][$this->profile["ID"]] )
                        && is_array( $sessionData["GOOGLEMERCHANT"]["THREAD"][$this->profile["ID"]] ) ){
                        $sessionData["GOOGLEMERCHANT"]["THREAD"][$this->profile["ID"]] = array();
                        AcritGooglemerchantSession::SetSession( $this->profile["ID"] , $sessionData );
                    }

                    $tCnt = ( $bBaseCron ) ? ( intval( $this->profile["SETUP"]["CRON"][$processId]["THREADS"] ) > 0 ? intval( $this->profile["SETUP"]["CRON"][$processId]["THREADS"] ) : 1 ) : ( intval( $this->profile["SETUP"]["THREADS"] ) > 0 ? intval( $this->profile["SETUP"]["THREADS"] ) : 1 );
                    $cronpage = 2;

                    $ProcessEnd = false;
                    $allPages = $elementsObj->Process( 1, $cronrun, $this->profile["SETUP"]["FILE_TYPE"], self::$fileExport, $this->profile["SETUP"]["URL_DATA_FILE"], $ProcessEnd, $bStepExport, $iLastSessionExportProductsCnt, $processId );

                    $sessionData = AcritGooglemerchantSession::GetSession( $this->profile["ID"] );
                    $sessionData["GOOGLEMERCHANT"]["THREAD"][$this->profile["ID"]] = array();
                    $steps = $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profile["ID"]]["STEPS"];
                    $steps2 = $steps / $tCnt + ( ( $steps % $tCnt ) == 0 ? 0 : 1 );

                    for( $i = 0; $i < $steps2; $i++ ){
                        for( $j = 0; $j < $tCnt; $j++ ){
                            if( $cronpage > $steps )
                                break;

                            $threadsId = $threads->newThread(
                                $this->documentRoot."/bitrix/modules/acrit.googlemerchant/tools/cronrun_proc.php",
                                array(
                                    "documentRoot" => $this->documentRoot,
                                    "profileId" => $this->profile["ID"],
                                    "processId" => $processId,
                                    "phpPath" => trim( $this->profile["SETUP"]["CRON_OPTIONS"]["PHP"] ),
                                    "cronPage" => $cronpage++,
                                )
                            );
                        }

                        while( false !== ( $procResults = $threads->iteration() ) ){
                        }
                    }
                    $allPages = true;
                }
            }
            else{
                $this->Lock();
                $allPages = $elementsObj->Process( $exportstep, $cronrun, $this->profile["SETUP"]["FILE_TYPE"], self::$fileExport, $this->profile["SETUP"]["URL_DATA_FILE"] );
            }
            if( !$allPages && $allPages != -1 && !$cronrun ){
                $exportstep++;
                echo "<script>window.location=\"", $APPLICATION->GetCurPageParam( "exportstep=$exportstep", array( "exportstep", "unlock" ) ), "\"</script>";
                die();
            }
            else{
                global $DB;

                if( $this->profile["SETUP"]["FILE_TYPE"] != "csv" ){
                    $basePatern =  "Y-m-dTh:i:s±h:i";
				    $paternCharset = CAcritGooglemerchantTools::GetStringCharset( $basePatern );

				    if( $paternCharset == "cp1251" ){
                        $basePatern = $APPLICATION->ConvertCharset( $basePatern, "windows-1251", "UTF-8" );
				    }

				    $dateGenerate = ( $this->profile["DATEFORMAT"] == $basePatern ) ? CAcritGooglemerchantTools::GetYandexDateTime( date( "d.m.Y H:i:s" ) ) : date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), time() );

                    $baseDeliveryCost = $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["VALUE"];
                    if( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["TYPE"] == "const" ){
                        $baseDeliveryCost = $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["CONTVALUE_TRUE"];
                    }
                    elseif( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["TYPE"] == "complex" ){
                        if( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_TYPE"] == "const" ){
                            $baseDeliveryCost = $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_CONTVALUE"];
                        }
                        else{
                            $baseDeliveryCost = $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_VALUE"];
                        }
                    }

                    $baseDeliveryDays = $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["VALUE"];
                    if( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["TYPE"] == "const" ){
                        $baseDeliveryDays = $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["CONTVALUE_TRUE"];
                    }
                    elseif( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["TYPE"] == "complex" ){
                        if( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_TYPE"] == "const" ){
                            $baseDeliveryDays = $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_CONTVALUE"];
                        }
                        else{
                            $baseDeliveryDays = $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_VALUE"];
                        }
                    }

                    $baseDeliveryOrderBefore = $this->profile["XMLDATA"]["BASE_ORDER_BEFORE"]["VALUE"];
                    if( $this->profile["XMLDATA"]["BASE_ORDER_BEFORE"]["TYPE"] == "const" ){
                        $baseDeliveryOrderBefore = $this->profile["XMLDATA"]["BASE_ORDER_BEFORE"]["CONTVALUE_TRUE"];
                    }
                    elseif( $this->profile["XMLDATA"]["BASE_ORDER_BEFORE"]["TYPE"] == "complex" ){
                        if( $this->profile["XMLDATA"]["BASE_ORDER_BEFORE"]["COMPLEX_TRUE_TYPE"] == "const" ){
                            $baseDeliveryOrderBefore = $this->profile["XMLDATA"]["BASE_ORDER_BEFORE"]["COMPLEX_TRUE_CONTVALUE"];
                        }
                        else{
                            $baseDeliveryOrderBefore = $this->profile["XMLDATA"]["BASE_ORDER_BEFORE"]["COMPLEX_TRUE_VALUE"];
                        }
                    }

                    $defaultFields = array(
                        "#ENCODING#" => $this->profileEncoding[$this->profile["ENCODING"]],
                        "#DATE#" => $this->profile["DATEFORMAT"],
                        "#SHOP_NAME#" => $this->profile["SHOPNAME"],
                        "#COMPANY_NAME#" => $this->profile["COMPANY"],
                        "#SITE_URL#" => $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"],
                        "#DESCRIPTION#" => $this->profile["DESCRIPTION"],
                        "#CATEGORY#" => $this->GetCategoryXML( CAcritGooglemerchantTools::GetSections( $this->profile ) ),
                        "#CURRENCY#" => ( CModule::IncludeModule( "catalog" ) ) ? $this->GetCurrencyXML( CAcritGooglemerchantTools::GetCurrencies( $this->profile ) ) : "RUB",
                        "#DATE#" => $dateGenerate,
                        "#BASE_DELIVERY_COST#" => $baseDeliveryCost,
                        "#BASE_DELIVERY_DAYS#" => $baseDeliveryDays,
                        "#BASE_ORDER_BEFORE#" => $baseDeliveryOrderBefore,
                    );

                    $xmlHeader = explode( "#ITEMS#", $this->profile["FORMAT"] );
                    $xmlHeader[0] = str_replace( array_keys( $defaultFields ), array_values( $defaultFields ), $xmlHeader[0] );
                    $xmlHeader[1] = str_replace( array_keys( $defaultFields ), array_values( $defaultFields ), $xmlHeader[1] );

                    if(
                        ( ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["VALUE"] ) ) <= 0 )
                            && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_VALUE"] ) ) <= 0 )
                            && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["CONTVALUE_TRUE"] ) ) <= 0 )
                            && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_CONTVALUE"] ) ) <= 0 ) )
                        || ( ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["VALUE"] ) ) <= 0 )
                            && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_VALUE"] ) ) <= 0 )
                            && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["CONTVALUE_TRUE"] ) ) <= 0 )
                            && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_CONTVALUE"] ) ) <= 0 ) )
                    ){
                        $xmlHeader[0] = preg_replace( "#<delivery-options>.*</delivery-options>#is", "", $xmlHeader[0] );
                        $xmlHeader[1] = preg_replace( "#<delivery-options>.*</delivery-options>#is", "", $xmlHeader[1] );
                    }

                    self::Save( $xmlHeader[0], self::PREPEND );
                }

                if( $cronrun ){
                    if( file_exists( self::$firstStepFilename ) )
                        unlink( self::$firstStepFilename );

                    $fp = fopen( self::$firstStepFilename, "w" );
                    if( flock( $fp, LOCK_EX ) ){
                        $dirName = dirname( self::$firstStepFilename );
                        $files = scandir( $dirName );
                        natsort( $files );
                        foreach( $files as $file ){
                            if( ( $file == "." ) || ( $file == ".." ) )
                                continue;

                            if( ( false !== strpos( $file, basename( self::$firstStepFilename ) ) )
                                && ( $file != basename( self::$firstStepFilename ) ) ){
                                $partContent = file_get_contents( $dirName."/".$file );
                                fwrite( $fp, $partContent );
                                fflush( $fp );
                                unlink( $dirName."/".$file );
                            }
                        }
                    }
                    else{
                    }

                    flock( $fp, LOCK_UN );
                    fclose( $fp );
                    self::$fileExport = self::$firstStepFilename;
                }

                if( $this->profile["SETUP"]["FILE_TYPE"] != "csv" ){
                    self::Save( $xmlHeader[1] );

                    CGooglemerchantInformer::GetInformerData();

                    $this->SetExportFileEncoding();
                }

                if( CAcritGooglemerchantTools::HasBadCronExports( $this->profile ) ){
                    $arSupportData[] = GetMessage( "ACRIT_GOOGLEMERCHANT_PROCESS_CRON_BAD_EXPORTS" );
                }

                $this->profile["LOG"] = $obLog->GetLog( $this->profile["ID"], true, $arSupportData );

                $arLogArray = $obLog->GetLogArray( $this->profile["ID"] );
                if( !empty( $arLogArray ) ){
                    $this->profile["UNLOADED_OFFERS"] = $arLogArray["PRODUCTS"];
                    $this->profile["UNLOADED_OFFERS_CORRECT"] = $arLogArray["PRODUCTS_EXPORT"];
                    $this->profile["UNLOADED_OFFERS_ERROR"] = $arLogArray["PRODUCTS_ERROR"];
                }

                $this->profile["SETUP"]["LAST_START_EXPORT"] = $this->profile["LOG"]["LAST_START_EXPORT"];
                $this->profile["TIMESTAMP_X"] = CDatabase::FormatDate( $this->profile["TIMESTAMP_X"], "YYYY-MM-DD HH:MI:SS", "DD.MM.YYYY HH:MI:SS" );

                $this->dbMan->Update( $this->profile["ID"], $this->profile );
                $this->Unlock();
                AcritGooglemerchantSession::DeleteSession( $this->profile["ID"] );

                if( $this->profile["SETUP"]["FILE_TYPE"] != "xls" ){
                    self::SaveToOut();
                }

                if( $this->profile["USE_COMPRESS"] == "Y" ){
                    if( file_exists( $this->originalName ) ){
                        $zipSavePath = $this->originalName;
                    }
                    elseif( file_exists( $this->documentRoot."/upload/acrit.googlemerchant/".$this->originalNamePath ) ){
                        $zipSavePath = $this->documentRoot."/upload/acrit.googlemerchant/".$this->originalNamePath;
                    }
                    elseif( file_exists( $this->documentRoot."/upload".$this->originalNamePath ) ){
                        $zipSavePath = $this->documentRoot."/upload".$this->originalNamePath;
                    }

                    $zipPath = ( stripos( $zipSavePath, ".xml" ) !== false ) ? str_replace( ".xml", ".zip", $zipSavePath ) : str_replace( ".csv", ".zip", $zipSavePath );
                    $packarc = CBXArchive::GetArchive( $zipPath );

                    $fileQuickPath = str_replace( $this->documentRoot, "", $zipSavePath );
                    $arFileQuickPath = explode( "/", $fileQuickPath );
                    $fileQuickPathToDelete = "";
                    foreach( $arFileQuickPath as $filePathPartIndex => $filePathPart ){
                        if( $filePathPartIndex < count( $arFileQuickPath ) - 1 ){
                            $fileQuickPathToDelete .= $filePathPart."/";
                        }
                    }

                    $packarc->SetOptions(
                        array(
                            "COMPRESS" => true,
                            "STEP_TIME" => COption::GetOptionString( "fileman", "archive_step_time", 30 ),
                            "ADD_PATH" => false,
                            "REMOVE_PATH" => $this->documentRoot.$fileQuickPathToDelete,
                            "CHECK_PERMISSIONS" => false
                        )
                    );

                    $pArcResult = $packarc->Pack( $zipSavePath );
                    if( !$cronrun ){
                        LocalRedirect( str_replace( $this->documentRoot, "", $zipPath ) );
                    }
                }

                if( !$cronrun || ( $this->profile["SETUP"]["TYPE_RUN"] == "comp" ) ){
                    if( $this->profile["SETUP"]["FILE_TYPE"] != "csv" ){
                        LocalRedirect( str_replace( $this->documentRoot, "", $this->originalName )."?encoding=".$this->profileEncoding[$this->profile["ENCODING"]] );
                    }
                    else{
                        echo "<script>window.opener.location=\"", $this->profile["SETUP"]["URL_DATA_FILE"], "\"</script>";
                        echo "<script>window.close();</script>";
                    }
                }
            }
        }
    }

    private function Unlock(){
        if( file_exists( $this->lockDir."export_{$this->profile["ID"]}_run.lock" ) )
            unlink( $this->lockDir."export_{$this->profile["ID"]}_run.lock" );

        AcritGooglemerchantSession::DeleteSession( $this->profile["ID"], $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.googlemerchant/" );
    }

    private function Lock(){
        file_put_contents( $this->lockDir."export_{$this->profile["ID"]}_run.lock", "" );
    }

    public function isLock(){
        return file_exists( $this->lockDir."export_{$this->profile["ID"]}_run.lock" );
    }

    private function GetUAHRate(){
        $baseCurrency = CCurrency::GetBaseCurrency();

        $currencyRates = CGooglemerchantProfile::LoadCurrencyRates();
        $CURRENCIES = array();
        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
            $currencyTo = array();
            foreach( $currencies as $curr ){
                $curr2 = $this->profile["CURRENCY"][$curr]["CHECK"] == "Y" ? $this->profile["CURRENCY"][$curr]["CONVERT_TO"] : $curr;
                $rate = $baseCurrency == $curr2;

                if( $rate ){
                    $rate = 1;
                }
                else{
                    if( !key_exists( $this->profile["CURRENCY"][$curr]["RATE"], $currencyRates ) ){
                        $rate = CCurrencyRates::ConvertCurrency( 1, $this->profile["CURRENCY"][$curr]["CONVERT_TO"], $baseCurrency );
                    }
                    else{
                        $rate = $this->profile["CURRENCY"][$curr]["RATE"];
                    }
                }

                foreach( $currencyTo as $acur ){
                    if( $acur["CURRENCY"] == $curr2 )
                        continue 2;
                }

                $currencyTo[] = array(
                    "CURRENCY" => $curr2,
                    "RATE" => $rate,
                    "PLUS" => $this->profile["CURRENCY"][$curr]["PLUS"],
                );
            }
            $currencies = $currencyTo;
            unset( $currencyTo );
        }
        else{
            $currencyTo = array();
            foreach( $currencies as $curr ){
                $rate = $baseCurrency == $curr;

                if( $rate ){
                    $rate = 1;
                }
                else{
                    if( !key_exists( $this->profile["CURRENCY"][$curr]["RATE"], $currencyRates ) ){
                        $rate = CCurrencyRates::ConvertCurrency( 1, $curr, $baseCurrency );
                    }
                    else{
                        $rate = $this->profile["CURRENCY"][$curr]["RATE"];
                    }
                }

                foreach( $currencyTo as $acur ){
                    if( $acur["CURRENCY"] == $curr2 )
                        continue 2;
                }

                $currencyTo[] = array(
                    "CURRENCY" => $curr,
                    "RATE" => $rate,
                    "PLUS" => $this->profile["CURRENCY"][$curr]["PLUS"],
                );
            }
            $currencies = $currencyTo;
            unset( $currencyTo );
        }

        return $rate;
    }

    private function GetCurrencyXML( $currencies ){
        $baseCurrency = CCurrency::GetBaseCurrency();
        $currencyRates = CGooglemerchantProfile::LoadCurrencyRates();
        $CURRENCIES = array();

        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
            $currencyTo = array();
            foreach( $currencies as $curr ){
                $curr2 = ( $this->profile["CURRENCY"][$curr]["CHECK"] == "Y" ) ? $this->profile["CURRENCY"][$curr]["CONVERT_TO"] : $curr;
                $rate = $baseCurrency == $curr2;
                if( $rate ){
                    $rate = 1;
                }
                else{
                    if( !key_exists( $this->profile["CURRENCY"][$curr]["RATE"], $currencyRates ) ){
                        $rate = CCurrencyRates::ConvertCurrency( 1, $this->profile["CURRENCY"][$curr]["CONVERT_TO"], $baseCurrency );
                    }
                    else{
                        $rate = $this->profile["CURRENCY"][$curr]["RATE"];
                    }
                }

                foreach( $currencyTo as $acur ){
                    if( $acur["CURRENCY"] == $curr2 )
                        continue 2;
                }

                $currencyTo[] = array(
                    "CURRENCY" => $curr2,
                    "RATE" => $rate,
                    "PLUS" => $this->profile["CURRENCY"][$curr]["PLUS"],
                );
            }
            $currencies = $currencyTo;
            unset( $currencyTo );
        }
        else{
            $currencyTo = array();
            foreach( $currencies as $curr ){
                $rate = $baseCurrency == $curr;
                if( $rate ){
                    $rate = 1;
                }
                else{
                    if( !key_exists( $this->profile["CURRENCY"][$curr]["RATE"], $currencyRates ) ){
                        $rate = CCurrencyRates::ConvertCurrency( 1, $curr, $baseCurrency );
                    }
                    else{
                        $rate = $this->profile["CURRENCY"][$curr]["RATE"];
                    }
                }

                foreach( $currencyTo as $acur ){
                    if( $acur["CURRENCY"] == $curr2 )
                        continue 2;
                }

                $currencyTo[] = array(
                    "CURRENCY" => $curr,
                    "RATE" => $rate,
                    "PLUS" => $this->profile["CURRENCY"][$curr]["PLUS"],
                );
            }
            $currencies = $currencyTo;
            unset( $currencyTo );
        }

        foreach( $currencies as $curr ){
            $currencyTempalte = $this->profile["CURRENCY_TEMPLATE"];
            foreach($curr as $id => $value)
            {
                $currencyFields["#$id#"] = htmlspecialcharsbx(html_entity_decode($value));
            }
            $CURRENCIES[] = str_replace(array_keys($currencyFields), array_values($currencyFields), $currencyTempalte);
        }
        return implode( "", $CURRENCIES );
    }

    private function GetCategoryXML( $sections ){
        $arTerminalPathSections = array();
        $arParentCache = array();
        $arIBlocks= array();
        foreach( $sections as $sectionId ){
            $dbSectionList = CAcritGooglemerchantTools::GetSectionTree(
                false,
                $sectionId
            );

            while( $arSectionPath = $dbSectionList->GetNext() ){
                $arTerminalPathSections[] = $arSectionPath["ID"];
            }
        }

        sort( $arTerminalPathSections );
        $arTerminalPathSections = array_unique( $arTerminalPathSections );

        $sections = array_filter( $sections );
        if( empty( $sections ) )
            return "";

        $CATEGORIES = array();

        $fields = array(
            "ID" => "ID",
            "NAME" => "NAME",
            "IBLOCK_SECTION_ID" => "PARENT_ID",
        );

        $arSectionFilter = array();
        if( $this->profile["EXPORT_PARENT_CATEGORIES"] != "Y" ){
            $arSectionFilter["ID"] = $sections;
        }

        $dbSection = CIBlockSection::GetList(
            array(
                "ID" => "ASC",
                "LEFT_MARGIN" => "ASC"
            ),
            $arSectionFilter,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "DEPTH_LEVEL",
                "EXTERNAL_ID",
                "NAME",
                "IBLOCK_SECTION_ID"
            )
        );

        $sectionTempalte = $this->profile["CATEGORY_TEMPLATE"];

        if( $this->profile["EXPORT_PARENT_CATEGORIES"] == "Y" ){
            $innerXmlCategory = simplexml_load_string( $sectionTempalte );

            if( $innerXmlCategory ){
                $innerXmlCategory->addAttribute( "parentId", "#PARENT_ID#" );
                $sectionInnerTempalte = str_replace( '<?xml version="1.0"?>', "", $innerXmlCategory->asXML() );
            }
        }

        if( $this->profile["EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS"] == "Y" ){
            while( $arSection = $dbSection->GetNext() ){
                if( !in_array( $arSection["ID"], $arTerminalPathSections ) ){
                    continue;
                }

                if( !isset( $arIBlocks[$arSection["IBLOCK_ID"]] ) ){
                    $arIBlocks[$arSection["IBLOCK_ID"]] = CIBlock::GetArrayByID( $arSection["IBLOCK_ID"] );
                }
            }

            foreach( $arIBlocks as $arIBlock ){
                $sectionFields = array();
                foreach( $fields as $id => $value ){
                    if( isset( $arIBlock[$id] ) )
                       $sectionFields["#$fields[$id]#"] = htmlspecialcharsbx( html_entity_decode( $arIBlock[$id] ) );
                }

                if( ( strlen( $sectionInnerTempalte ) > 0 ) && intval( $sectionFields["#PARENT_ID#"] ) > 0 ){
                    $CATEGORIES[] = str_replace( array_keys( $sectionFields ), array_values( $sectionFields ), $sectionInnerTempalte );
                }
                else{
                    $CATEGORIES[] = str_replace( array_keys( $sectionFields ), array_values( $sectionFields ), $sectionTempalte );
                }

                $arFilter = array( "IBLOCK_ID" => $arIBlock["ID"], "ACTIVE" => "Y" );
                $arSelect = array( "ID", "NAME", "IBLOCK_SECTION_ID", "EXTERNAL_ID" );
                $dbSection = CIBlockSection::GetTreeList( $arFilter, $arSelect );

                while( $arSection = $dbSection->GetNext() ){
					$sectionFields = array();
                    $arParentCache[$arSection["ID"]] = $arSection["EXTERNAL_ID"];
                    $sectionFields["#PARENT_EXTERNAL_ID#"] = "";
                    if( !in_array( $arSection["ID"], $arTerminalPathSections ) ){
                        continue;
                    }

                    if( !empty( $arSection["IBLOCK_SECTION_ID"] ) ){
                        if( isset( $arParentCache[$arSection["IBLOCK_SECTION_ID"]] ) ){
                            $sectionFields["#PARENT_EXTERNAL_ID#"] = $arParentCache[$arSection["IBLOCK_SECTION_ID"]] ?: "";
                        }
                        else{
                            $dbParentSection = CIBlockSection::GetList(
                                array(),
                                array( "ID" => $arSection["IBLOCK_SECTION_ID"] ),
                                false,
                                array(
                                    "ID",
                                    "EXTERNAL_ID"
                                )
                            );

                            $arParentSection = $dbParentSection->GetNext();

                            $sectionFields["#PARENT_EXTERNAL_ID#"] = $arParentSection["EXTERNAL_ID"];
                            $arParentCache[$arSection["IBLOCK_SECTION_ID"]]=$arParentSection["EXTERNAL_ID"];
                        }
                    }

                    $sectionFields["#EXTERNAL_ID#"] = $arSection["EXTERNAL_ID"] ?: "";
                    foreach( $arSection as $id => $value ){
                        if( strlen( $fields[$id] ) )
                            $sectionFields["#$fields[$id]#"] = htmlspecialcharsbx( html_entity_decode( $value ) );
                    }

                    if( empty( $arSection["IBLOCK_SECTION_ID"] ) ){
                        $sectionFields["#PARENT_ID#"] = $arIBlock["ID"];
                    }

                    unset( $arSection );

                    if( ( strlen( $sectionInnerTempalte ) > 0 ) && intval( $sectionFields["#PARENT_ID#"] ) > 0 ){
                        $CATEGORIES[] = str_replace( array_keys( $sectionFields ), array_values( $sectionFields ), $sectionInnerTempalte );
                    }
                    else{
                        $CATEGORIES[] = str_replace( array_keys( $sectionFields ), array_values( $sectionFields ), $sectionTempalte );
                    }
                }
            }
        }
        else{
            while( $arSection = $dbSection->GetNext() ){
                $sectionFields = array();
                $arParentCache[$arSection["ID"]] = $arSection["EXTERNAL_ID"];
                $sectionFields["#PARENT_EXTERNAL_ID#"] = "";
                if( !in_array( $arSection["ID"], $arTerminalPathSections ) ){
                    continue;
                }

                if( !empty( $arSection["IBLOCK_SECTION_ID"] ) ){
                    if( isset( $arParentCache[$arSection["IBLOCK_SECTION_ID"]] ) ){
                       $sectionFields["#PARENT_EXTERNAL_ID#"] = $arParentCache[$arSection["IBLOCK_SECTION_ID"]] ?: "";
                    }
                    else{
                        $dbParentSection = CIBlockSection::GetList(
                            array(),
                            array( "ID" => $arSection["IBLOCK_SECTION_ID"] ),
                            false,
                            array(
                                "ID",
                                "EXTERNAL_ID"
                            )
                        );

                        $arParentSection = $dbParentSection->GetNext();
                        $sectionFields["#PARENT_EXTERNAL_ID#"] = $arParentSection["EXTERNAL_ID"];
                        $arParentCache[$arSection["IBLOCK_SECTION_ID"]] = $arParentSection["EXTERNAL_ID"];
                    }
                }

                $sectionFields["#EXTERNAL_ID#"] = $arSection["EXTERNAL_ID"] ?: "";
                foreach( $arSection as $id => $value ){
                    $sectionFields["#$fields[$id]#"] = htmlspecialcharsbx( html_entity_decode( $value ) );
                }

                unset( $arSection );

                if( ( strlen( $sectionInnerTempalte ) > 0 ) && intval( $sectionFields["#PARENT_ID#"] ) > 0 ){
                    $CATEGORIES[] = str_replace( array_keys( $sectionFields ), array_values( $sectionFields ), $sectionInnerTempalte );
                }
                else{
                    $CATEGORIES[] = str_replace( array_keys( $sectionFields ), array_values( $sectionFields ), $sectionTempalte );
                }
            }
        }
        unset( $arParentCache );

        return implode( "", $CATEGORIES );
    }

    private function PrepareFieldFilter(){
        foreach( $this->profile["XMLDATA"] as $id => $field ){
            if( empty( $field["CONDITION"]["CHILDREN"] ) ) continue;

            $obCond = new CAcritGooglemerchantCatalogCond();
            $obCond->Init( BT_COND_MODE_GENERATE, 0, array() );

            $this->profile["XMLDATA"][$id]["EVAL_FILTER"] = $obCond->Generate(
                $field["CONDITION"],
                array(
                    "FIELD" => '$GLOBALS["CHECK_COND"]'
                )
            );
        }
    }

    public static function Save( $data, $mode = self::APPEND ){
        if( !isset( self::$fileExport ) )
            return false;

        if( $mode == self::APPEND ){
            $fp = fopen( self::$fileExport, "a" );
            if( flock( $fp, LOCK_EX ) ){
                fwrite( $fp, $data );
                fflush( $fp );
                flock( $fp, LOCK_UN );
            }

            fclose( $fp );
        }
        elseif( $mode == self::PREPEND ){
            if( @file_exists( self::$fileExport ) ){
                $contents = @file_get_contents( self::$fileExport );
            }

            $fp = fopen( self::$fileExport, "w" );

            if( flock( $fp, LOCK_EX ) ){
                fwrite( $fp, $data );
                fflush( $fp );
                fwrite( $fp, $contents );
                fflush( $fp );
                flock( $fp, LOCK_UN );
            }

            fclose( $fp );

            unset( $contents );
        }
        else{
            $fp = fopen( self::$fileExport, "w" );
            if( flock( $fp, LOCK_EX ) ){
                fwrite( $fp, $data );
                fflush( $fp );
                flock( $fp, LOCK_UN );
            }

            fclose( $fp );
        }
        return true;
    }

    public static function SaveToOut(){
        if( !isset( self::$fileExport ) || !isset( self::$fileExportOut ) )
            return false;

        $contents = @file_get_contents( self::$fileExport );
        $contents = preg_replace( '#([\r\n]+\s*[\r\n]+)+#is', PHP_EOL, $contents );
        $contents = preg_replace( '#([\n]+\s*[\n]+)+#is', PHP_EOL, $contents );

        if( @file_exists( self::$fileExport ) ){
            unlink( self::$fileExport );
        }
        if( @file_exists( self::$fileExportOut ) ){
            unlink( self::$fileExportOut );
        }

        $fp = fopen( self::$fileExportOut, "a" );
        if( flock( $fp, LOCK_EX ) ){
            fwrite( $fp, $contents );
            fflush( $fp );
            flock( $fp, LOCK_UN );
        }

        fclose( $fp );

        return true;
    }

    private function SetExportFileEncoding(){
        if( $this->profile["ENCODING"] != $this->siteEncoding[ToLower( SITE_CHARSET )] ){
            $data = @file_get_contents( self::$fileExport );
            $encodingData = mb_convert_encoding( $data, $this->profile["ENCODING"], $this->siteEncoding[ToLower( SITE_CHARSET )] );
            if( !$encodingData ){
                return false;
            }
            unset( $data );
            self::Save( $encodingData, self::REWRITE );
        }
        return true;
    }
}