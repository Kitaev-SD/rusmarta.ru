<?
use
	\Acrit\Core\Helper;

# Definitions
$strCoreId = 'acrit.core';
$strModuleId = end(explode('/', str_replace('\\', '/', pathinfo(__FILE__, PATHINFO_DIRNAME))));
$strModuleCode = toUpper(preg_replace('#^.*?\.(.*?)$#', '$1', $strModuleId));

# General
define('ACRIT_CORE', $strCoreId);
IncludeModuleLangFile(__FILE__);
if(\Bitrix\Main\Loader::includeModule($strCoreId)){
	$arAutoload = [
		'ProfileTable',
		'ProfileIBlockTable',
		'ProfileFieldTable',
		'ProfileValueTable',
		'AdditionalFieldTable',
		'CategoryCustomNameTable',
		'CategoryRedefinitionTable',
		'ExportDataTable',
		'ExternalIdTable',
		'HistoryTable',
		//
		'ProfileFieldFeature',
		'Backup',
	];
	Helper::setModuleAutoloadClasses($strModuleId, $arAutoload, __NAMESPACE__);
	$GLOBALS['ACRIT_'.$strModuleCode.'_AUTOLOAD_CLASSES'] = &$arAutoload;
	if(Helper::getOption($strModuleId, 'disable_old_core') == 'Y'){
		return;
	}
}

# Old module code
IncludeModuleLangFile( __FILE__ );
use Bitrix\Highloadblock\HighloadBlockTable;

global $DBType;

$arClasses = array(
    "AcritLicence" => "classes/general/licence.php",
    "CGooglemerchantProfileDB" => "classes/mysql/cgooglemerchantprofiledb.php",
    "CGooglemerchantMarketDB" => "classes/mysql/cgooglemerchantpro_marketdb.php",
    "CGooglemerchantProfile" => "classes/general/cgooglemerchantprofile.php",

    "CAcritGlobalCondCtrl" => "classes/general/cgooglemerchantcond.php",
    "CAcritGlobalCondCtrlComplex" => "classes/general/cgooglemerchantcond.php",
    "CAcritGlobalCondCtrlAtoms" => "classes/general/cgooglemerchantcond.php",
    "CAcritGlobalCondCtrlGroup" => "classes/general/cgooglemerchantcond.php",
    "CAcritGlobalCondTree" => "classes/general/cgooglemerchantcond.php",
    "CAcritCatalogCondCtrl" => "classes/general/cgooglemerchantcond.php",
    "CAcritCatalogCondCtrlComplex" => "classes/general/cgooglemerchantcond.php",
    "CAcritCatalogCondCtrlGroup" => "classes/general/cgooglemerchantcond.php",
    "CAcritCatalogCondCtrlIBlockFields" => "classes/general/cgooglemerchantcond.php",
    "CAcritCatalogCondCtrlIBlockProps" => "classes/general/cgooglemerchantcond.php",
    "CAcritCatalogCondTree" => "classes/general/cgooglemerchantcond.php",

    "CAcritGooglemerchantCatalog" => "classes/general/cgooglemerchantfilter.php",
    "CAcritGooglemerchantPrices" => "classes/general/cgooglemerchantfilter.php",
    "CAcritGooglemerchantProps" => "classes/general/cgooglemerchantfilter.php",
    "CAcritGooglemerchantCatalogCond" => "classes/general/cgooglemerchantfilter.php",
    "CAcritGooglemerchantLog" => "classes/general/cgooglemerchantlog.php",
    "AcritGooglemerchantSession" => "classes/general/cgooglemerchantsession.php",
    "CAcritGooglemerchantUrlRewrite" => "classes/general/cgooglemerchanturlrewrite.php",
    "CAcritGooglemerchantTools" => "classes/general/cgooglemerchanttools.php",
    "CAcritGooglemerchantStringProcess" => "classes/general/cgooglemerchanttools.php",
    "CAcritGooglemerchantMarketCategories" => "classes/general/cgooglemerchanttools.php",

    "CAcritGooglemerchantExport" => "classes/general/cgooglemerchantexport.php",
    "CGooglemerchantAgent" => "classes/general/cgooglemerchantagent.php",
    "CGooglemerchantInformer" => "classes/general/cgooglemerchantinformer.php",
    "Threads" => "classes/general/threads.php",
    "ThreadsSession" => "classes/general/threads.php",
);

CModule::AddAutoloadClasses( "acrit.googlemerchant", $arClasses );

if( class_exists( "XMLWriter" ) ){
    if( !class_exists( "PHPExcel" ) && !class_exists( "PHPExcel_IOFactory" ) ){
        require_once( __DIR__."/classes/general/PHPExcel.php" );
        require_once( __DIR__."/classes/general/PHPExcel/IOFactory.php" );
    }
}

class CAcritGooglemerchantMenu{
    public function OnBuildGlobalMenu( &$aGlobalMenu, &$aModuleMenu ){
        global $USER, $APPLICATION, $adminMenu, $adminPage;
				if(\Bitrix\Main\Config\Option::get('acrit.googlemerchant', 'disable_old_core') == 'Y'){
					return;
				}
        if( is_array( $adminMenu->aGlobalMenu ) && key_exists( "global_menu_acrit", $adminMenu->aGlobalMenu ) ){
            return;
        }

        $acritMenuGroupName = COption::GetOptionString( "acrit.googlemerchant", "acritmenu_groupname" );
        if( strlen( trim( $acritMenuGroupName ) ) <= 0 ){
            $acritMenuGroupName = GetMessage( "ACRITMENU_GROUPNAME_DEFAULT" );
        }

        $aMenu = array(
            "menu_id" => "acrit",
            "sort" => 150,
            "text" => $acritMenuGroupName,
            "title" => GetMessage( "ACRIT_MENU_TITLE" ),
            "icon" => "clouds_menu_icon",
            "page_icon" => "clouds_page_icon",
            "items_id" => "global_menu_acrit",
            "items" => array()
        );
        $aGlobalMenu["global_menu_acrit"] = $aMenu;
    }
}

class CAcritGooglemerchantElement{
    public $profile = null;
    public $DEMO = 2;
    public $isDemo = true;
    public $DEMO_CNT;
    public $MODULEID = "acrit.googlemerchant";
    public $stepElements = 50;
    public $dateFields = array();
    public $arCsvSpecials = array();
    public $log;
    public $session;
    public $baseDateTimePatern;
    public $basePriceId;
    public $obProfileUtils;

    protected $profileEncoding = array(
        "utf8" => "utf-8",
        "cp1251" => "windows-1251",
    );

    public function __construct( $profile ){
        global $APPLICATION;

        $this->iblockIncluded = @CModule::IncludeModule( "iblock" );
        $this->hlBlockIncluded = @CModule::IncludeModule( "highloadblock" );
        $this->saleIncluded = @CModule::IncludeModule( "sale" );
        $this->catalogIncluded = @CModule::IncludeModule( "catalog" );

        $this->DEMO = CModule::IncludeModuleEx( $this->MODULEID );
        if( $this->DEMO == 1 )
            $this->isDemo = false;

        $this->DEMO_CNT = 50;
        $this->profile = $profile;

        $this->obProfileUtils = new CGooglemerchantProfile();
        $this->profile["PROFILE_CATEGORIES"] = $this->obProfileUtils->GetSections(
            $this->profile["IBLOCK_ID"],
            $this->profile["CHECK_INCLUDE"] == "Y",
            true
        );

        if( intval( $this->profile["SETUP"]["EXPORT_STEP"] ) > 0 )
            $this->stepElements = $this->profile["SETUP"]["EXPORT_STEP"];

        $this->dateFields = array(
            "TIMESTAMP_X",
            "DATE_CREATE",
            "DATE_ACTIVE_FROM",
            "DATE_ACTIVE_TO"
        );

        $this->arCsvSpecials = array(
        );

        $this->log = new CAcritGooglemerchantLog( $this->profile["ID"] );

        $this->baseDateTimePatern = "Y-m-dTh:i:s±h:i";

        $paternCharset = CAcritGooglemerchantTools::GetStringCharset( $this->baseDateTimePatern );

        if( $paternCharset == "cp1251" ){
            $this->baseDateTimePatern = $APPLICATION->ConvertCharset( $this->baseDateTimePatern, "windows-1251", "UTF-8" );
        }

        $dateGenerate = ( $this->profile["DATEFORMAT"] == $this->baseDateTimePatern ) ? CAcritGooglemerchantTools::GetYandexDateTime( date( "d.m.Y H:i:s" ) ) : date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), time() );

        $this->defaultFields = array(
            "#ENCODING#" => $this->profileEncoding[$this->profile["ENCODING"]],
            "#DATE#" => $this->profile["DATEFORMAT"],
            "#SHOP_NAME#" => $this->profile["SHOPNAME"],
            "#COMPANY_NAME#" => $this->profile["COMPANY"],
            "#SITE_URL#" => $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"],
            "#PROFILE_DESCRIPTION#" => $this->profile["DESCRIPTION"],
            "#DATE#" => $dateGenerate,
        );

        $this->basePriceId = CAcritGooglemerchantTools::GetProcessPriceId( $this->profile );
    }

    public static function OnBeforePropertiesSelect( &$arFields ){
        foreach( $arFields as $Key => &$arValue ){
            if( is_array( $arValue ) ){
                foreach( $arValue as &$Value ){
                    $arProperty = explode( "-", $Value );
                    $cProperty = count( $arProperty );
                    if( $cProperty == 3 ){
                        $Value = "PROPERTY_".$arProperty[2]."_DISPLAY_VALUE";
                    }
                }
            }
            else{
                $arProperty = explode( "-", $arValue );
                $cProperty = count( $arProperty );
                if( $cProperty == 3 ){
                    $arValue = "PROPERTY_".$arProperty[2]."_DISPLAY_VALUE";
                }
            }
        }
    }

    public function GetElementCount(){
        return $this->elementCount;
    }

    protected function DemoCount(){
        $arSessionData = AcritGooglemerchantSession::GetAllSession( $this->profile["ID"] );
        $demoCnt = 0;
        if( !empty( $arSessionData ) ){
            foreach( $arSessionData as $arSessionDataItem ){
                $demoCnt += $arSessionDataItem["GOOGLEMERCHANT"][$this->profile["ID"]]["DEMO_COUNT"];
            }
        }

        return ( $demoCnt > $this->DEMO_CNT );
    }

    protected function DemoCountInc(){
        $sessionData = AcritGooglemerchantSession::GetSession( $this->profile["ID"] );
        if( !isset( $sessionData["GOOGLEMERCHANT"][$this->profile["ID"]]["DEMO_COUNT"] ) )
            $sessionData["GOOGLEMERCHANT"][$this->profile["ID"]]["DEMO_COUNT"] = 0;

        $sessionData["GOOGLEMERCHANT"][$this->profile["ID"]]["DEMO_COUNT"]++;
        AcritGooglemerchantSession::SetSession( $this->profile["ID"], $sessionData );
    }

    public function ExportConvertCharset( $field ){
        global $APPLICATION;
        $result = "";

        $paternCharset = CAcritGooglemerchantTools::GetStringCharset( $field );
        $result = $APPLICATION->ConvertCharset( $field, $paternCharset, $this->profileEncoding[$this->profile["ENCODING"]] );

        return $result;
    }

    public function CalcProcessXMLLoadingByOneProduct(){
        $calcTimeStart = getmicrotime();

        $dbElements = self::PrepareProcess();
        if( !is_object( $dbElements ) ) return false;

        $sessionData = AcritGooglemerchantSession::GetSession( $this->profile["ID"] );
        $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profile["ID"]]["STEPS"] = $this->isDemo ? 1 : $dbElements->NavPageCount;
        AcritGooglemerchantSession::SetSession( $this->profile["ID"], $sessionData );

        while( $arElement = $dbElements->GetNextElement() ){
            $variantItems = array();
            $arItem = $this->ProcessElement( $arElement );

            if( !$arItem )
                continue;

            unset( $arItem );
        }

        unset( $arElement, $arItem );

        CAcritGooglemerchantTools::SaveCurrencies( $this->profile, $this->currencyList );

        return round( getmicrotime() - $calcTimeStart, 3 );
    }

    public function Process( $page = 1, $cronrun = false, $fileType = "xml", $fileExport = false, $fileExportName = false, &$_ProcessEnd = false, $bStepExport = false, $iLastSessionExportProductsCnt = 0, $processId = 0 ){
        global $fileExportDataSize, $fileExportData, $ProcessEnd;
        $fileThread = false;

        $arIblockData = CAcritGooglemerchantTools::GetSimpleData();
        $arIblockDataRows = unserialize( base64_decode( $arIblockData ) );

        if( !in_array( $this->profile["TYPE"], $arIblockDataRows ) ){
            return false;
        }

        $this->SetProcessStart( $fileThread );
        if( $fileType == "csv" ){
            $ret = self::ProcessCSV( $page, $cronrun, $fileExport, $fileExportName );
        }
        else{
            $ret = self::ProcessXML( $page, $cronrun, $bStepExport, $iLastSessionExportProductsCnt, $processId );
        }

        $this->SetProcessEnd( $fileThread );
        while( true !== $ProcessEnd ){
        }
        $_ProcessEnd = $ProcessEnd;

        return $ret;
    }

    public function PrepareProcess( $page = 1, $bStepExport = false ){
        if( $page == 1 ){
            $this->log->Init( $this->profile );
            $this->page = $page;
        }

        $this->currencyRates = CGooglemerchantProfile::LoadCurrencyRates();
        $iblockList = $this->PrepareIBlock();

        if( empty( $iblockList ) ){
            return true;
        }

        $pregMatchExp = GetMessage( "ACRIT_GOOGLEMERCHANT_A_AA_A" );

        preg_match_all( "/.*(<[\w\d_-]+).*(#[\w\d_-]+:*[\w\d_-]+#).*(<\/.+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );
        preg_match_all( "/(#[\w\d_-]+:*[\w\d_-]+#)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches["ALL_TAGS"] );

        // install for all templates #EXAMPLE# null value, so that you can remove
        $this->templateValuesDefaults = array();
        foreach( $this->arMatches["ALL_TAGS"][0] as $match ){
            $this->templateValuesDefaults[$match] = "";
        }
        $this->templateValuesDefaults["#MARKET_CATEGORY#"] = "";

        // get the properties used in the templates
        $this->useProperties = array(
            "ID" => array()
        );

        $this->usePrices = array();
        foreach( $this->profile["XMLDATA"] as $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] )
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] )
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) || ( $field["TYPE"] == "composite" ) ){

                if( $field["TYPE"] == "composite" ){
                    foreach( $field["COMPOSITE_TRUE"] as $compositeFieldIndex => $compositeField ){
                        if( $compositeField["COMPOSITE_TRUE_TYPE"] == "field" ){
                            $arValue = explode( "-", $compositeField["COMPOSITE_TRUE_VALUE"] );

                            switch( count( $arValue ) ){
                                case 1:
                                    $this->useFields[] = $arValue[0];
                                    break;
                                case 2:
                                    $this->usePrices[] = $arValue[1];
                                    break;
                                case 3:
                                    $this->useProperties["ID"][] = $arValue[2];
                                    break;
                            }
                        }
                    }

                    foreach( $field["COMPOSITE_FALSE"] as $compositeFieldIndex => $compositeField ){
                        if( $compositeField["COMPOSITE_FALSE_TYPE"] == "field" ){
                            $arValue = explode( "-", $compositeField["COMPOSITE_FALSE_VALUE"] );

                            switch( count( $arValue ) ){
                                case 1:
                                    $this->useFields[] = $arValue[0];
                                    break;
                                case 2:
                                    $this->usePrices[] = $arValue[1];
                                    break;
                                case 3:
                                    $this->useProperties["ID"][] = $arValue[2];
                                    break;
                            }
                        }
                    }
                }
                else{
                    if( $field["TYPE"] == "field" ){
                        $fieldValue = $field["VALUE"];
                        $arValue = explode( "-", $fieldValue );

                        switch( count( $arValue ) ){
                            case 1:
                                $this->useFields[] = $arValue[0];
                                break;
                            case 2:
                                $this->usePrices[] = $arValue[1];
                                break;
                            case 3:
                                $this->useProperties["ID"][] = $arValue[2];
                                break;
                        }
                    }
                    elseif( $field["TYPE"] == "complex" ){
                        $fieldValue = $field["COMPLEX_TRUE_VALUE"];
                        $arValue = explode( "-", $fieldValue );

                        switch( count( $arValue ) ){
                            case 1:
                                $this->useFields[] = $arValue[0];
                                break;
                            case 2:
                                $this->usePrices[] = $arValue[1];
                                break;
                            case 3:
                                $this->useProperties["ID"][] = $arValue[2];
                                break;
                        }

                        $fieldValue = $field["COMPLEX_FALSE_VALUE"];
                        $arValue = explode( "-", $fieldValue );

                        switch( count( $arValue ) ){
                            case 1:
                                $this->useFields[] = $arValue[0];
                                break;
                            case 2:
                                $this->usePrices[] = $arValue[1];
                                break;
                            case 3:
                                $this->useProperties["ID"][] = $arValue[2];
                                break;
                        }
                    }

                    if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                        $arElementConfig["DELAY"] = true;
                    }
                }

                if( $field["CONDITION"]["CHILDREN"] ){
                    if( !function_exists( findChildren ) ){
                        function findChildren( $children ){
                            $retVal = array();
                            foreach( $children as $child ){
                                if( strstr( $child["CLASS_ID"], "CondIBProp" ) ){
                                    $arProp = explode( ":", $child["CLASS_ID"] );
                                    $retVal[] = $arProp[2];
                                }
                                if( $child["CHILDREN"] ){
                                    $retVal = array_merge( $retVal, findChildren( $child["CHILDREN"] ) );
                                }
                            }
                            return $retVal;
                        }
                    }
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], findChildren( $field["CONDITION"]["CHILDREN"] ) );
                }
            }

            if( $field["EVAL_FILTER"] ){
                preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );
                if( is_array( $filterProps[1] ) ){
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
                }
                if( is_array( $filterProps[2] ) ){
                    $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
                }
            }
        }
        preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );

        if( is_array( $filterProps[1] ) ){
            $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
        }
        if( is_array( $filterProps[2] ) ){
            $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
        }
        $dbEvents = GetModuleEvents( "acrit.googlemerchant", "OnBeforePropertiesSelect" );
        $eventResult = array();
        while( $arEvent = $dbEvents->Fetch() ){
            ExecuteModuleEventEx(
                $arEvent,
                array(
                    array(
                        "ID" => $this->profile["ID"],
                        "CODE" => $this->profile["CODE"],
                        "NAME" => $this->profile["NAME"]
                    ),
                    &$eventResult
                )
            );
        }

        foreach( $eventResult as $arValue ){
            if( is_array( $arValue ) ){
                foreach( $arValue as $Value ){
                    $arProperty = explode( "-", $Value );
                    if( count( $arProperty ) == 3 ){
                        $this->useProperties["ID"][] = $arProperty[2];
                    }
                }
            }
            else{
                $arProperty = explode( "-", $arValue );
                if( count( $arProperty ) == 3 ){
                    $this->useProperties["ID"][] = $arProperty[2];
                }
            }
        }
        $this->useProperties["ID"] = array_unique( $this->useProperties["ID"] );
        $this->useProperties["ID"] = array_filter( $this->useProperties["ID"] );

        $this->currencyList = array();
        $arOrder = array(
            "IBLOCK_ID" => "ASC",
            "ID" => "ASC",
        );

        $arFilter = array(
            "IBLOCK_ID" => $iblockList,
            "SECTION_ID" => $this->profile["CATEGORY"],
        );

        if( $this->profile["CHECK_INCLUDE"] != "Y" ){
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        }

        $arNavStartParams = array(
            "nPageSize" => $this->stepElements,
            "iNumPage" => $page
        );

        $dbElements = CIBlockElement::GetList(
            $arOrder,
            $arFilter,
            false,
            (  $bStepExport ) ? false : $arNavStartParams,
            array()
        );

        return $dbElements;
    }

    public function ProcessBasicCsv( $dbElements, $fileExport, $page ){
        if( !$dbElements->SelectedRowsCount() || !$fileExport ){
            return false;
        }

        if( $page === 1 ){
            $arPaternFields = array();
        }

        while( $dbElement = $dbElements->GetNextElement() ){
            $arRowToCsv = $this->ProcessElement( $dbElement, false, true );
            if( $arRowToCsv ){
                if( empty( $arPaternFields ) && ( $page === 1 ) ){
                    foreach( $arRowToCsv as $colIndex => $colValue ){
                        $arPaternFields[] = $colIndex;
                    }
                }
                $arProcess[] = $arRowToCsv;

                $arItem = $this->GetElementProperties( $dbElement );
                if( $this->catalogIncluded && ( $this->profile["USE_SKU"] == "Y" ) && ( $this->catalogSKU[$arItem["IBLOCK_ID"]] ) ){
                    $arOfferFilter = array(
                        "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                        "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
                    );

                    $dbOfferElements = CIBlockElement::GetList(
                        array(),
                        $arOfferFilter,
                        false,
                        false,
                        array()
                    );

                    while( $arOfferElement = $dbOfferElements->GetNextElement() ){
                        $arOfferRowToCsv = $this->ProcessElement( $arOfferElement, $arItem, true );

                        if( !$arOfferRowToCsv ) continue;

                        if( empty( $arPaternFields ) && ( $page === 1 ) ){
                            foreach( $arOfferRowToCsv as $colIndex => $colValue ){
                                $arPaternFields[] = $colIndex;
                            }
                        }
                        $arProcess[] = $arOfferRowToCsv;
                    }
                }
            }
        }

        $csvFile = new CCSVData();
        $csvFile->SetFieldsType( "R" );
        $delimiter_r_char = ";";
        $csvFile->SetDelimiter( $delimiter_r_char );

        $arResFields = array();

        if( $page === 1 ){
            $arTuple = array();
            foreach( $arPaternFields as $paternField ){
                if( $paternField == "ID" ){
                    $paternField = "Id"; //!!excel csv id fix - hello Billy!
                }
                $arTuple[] = $this->ExportConvertCharset( $paternField );
            }

            CAcritGooglemerchantTools::ExportArrayMultiply( $arResFields, $arTuple );
        }

        foreach( $arProcess as $arRow ){
            $arTuple = array();
            foreach( $arRow as $colValue ){
                if( is_array( $colValue ) && empty( $colValue ) ){ //!!some fix
                    $colValue = "";
                }
                $arTuple[] = $this->ExportConvertCharset( $colValue );
            }
            CAcritGooglemerchantTools::ExportArrayMultiply( $arResFields, $arTuple );
        }

        foreach( $arResFields as $arTuple ){
            $csvFile->SaveFile( $fileExport, $arTuple );
        }

        $csvFile->CloseFile();
    }

    public function ProcessCSV( $page = 1, $cronrun = false, $fileExport = false, $fileExportName = false ){
        global $APPLICATION;
        if( !$fileExport || !$fileExportName ) return false;

        $dbElements = self::PrepareProcess( $page );
        if( !is_object( $dbElements ) ) return false;

        $navPageCount = ( intval( $dbElements->NavPageCount ) > 0 ) ? $dbElements->NavPageCount : ceil( $dbElements->SelectedRowsCount() / $this->stepElements );

        $sessionData = AcritGooglemerchantSession::GetSession( $this->profile["ID"] );
        $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profile["ID"]]["STEPS"] = $navPageCount;

        AcritGooglemerchantSession::SetSession( $this->profile["ID"], $sessionData );

        self::ProcessBasicCsv( $dbElements, $fileExport, $page );

        if( !$cronrun ){
            echo '<div id="csv_process" style="width: 100%; text-align: center; font-size: 18px; margin: 40px 0; padding: 40px 0; border: 1px solid #ccc; border-radius: 6px; background: #f5f5f5;">',
            GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_EXPORT_RUN" ), "<br/>",
            str_replace( array( "#PROFILE_ID#", "#PROFILE_NAME#" ), array( $this->profile["ID"], $this->profile["NAME"] ), GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_STEP_PROFILE" ) ), "<br/>",
            str_replace( array( "#STEP#", "#COUNT#" ), array( $page, $navPageCount ), GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_STEP_RUN" ) ),
            "</div>";
        }

        if( $page >= $dbElements->NavPageCount )
            return true;

        return false;
    }

    public function ProcessXML( $page = 1, $cronrun = false, $bStepExport = false, $iLastSessionExportProductsCnt = 0, $processId = 0 ){
        $dbElements = self::PrepareProcess( $page, $bStepExport );
        if( !is_object( $dbElements ) ) return false;

        $navPageCount = ( intval( $dbElements->NavPageCount ) > 0 ) ? $dbElements->NavPageCount : ceil( $dbElements->SelectedRowsCount() / $this->stepElements );

        $sessionData = AcritGooglemerchantSession::GetSession( $this->profile["ID"] );
        $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profile["ID"]]["STEPS"] = $navPageCount;
        AcritGooglemerchantSession::SetSession( $this->profile["ID"], $sessionData );

        $bSchemeUseOffer = false;
        $bSchemeUseOfferSku = false;
        $bSchemeUseSku = false;
        $bSchemeUseSkuByOffer = false;

        if( ( $this->profile["EXPORT_DATA_OFFER"] == "Y" ) ){
            $bSchemeUseOffer = true;
        }

        if( $this->profile["EXPORT_DATA_OFFER_WITH_SKU_DATA"] == "Y" ){
            $bSchemeUseOfferSku = true;
        }

        if( $this->profile["EXPORT_DATA_SKU"] == "Y" ){
            $bSchemeUseSku = true;
        }

        if( $this->profile["EXPORT_DATA_SKU_BY_OFFER"] == "Y" ){
            $bSchemeUseSkuByOffer = true;
        }

        while( $arElement = $dbElements->GetNextElement() ){
            $processSessionData = AcritGooglemerchantSession::GetSession( $this->profile["ID"] );

            if( $bStepExport ){
                if( ( $processSessionData["GOOGLEMERCHANT"]["LOG"][$this->profile["ID"]]["PRODUCTS_EXPORT"] >= ( $iLastSessionExportProductsCnt + $this->profile["SETUP"]["CRON"][$processId]["STEP_EXPORT_CNT"] ) )
                    || ( ( intval( $this->profile["SETUP"]["CRON"][$processId]["MAXIMUM_PRODUCTS"] ) > 0 ) && ( $processSessionData["GOOGLEMERCHANT"]["LOG"][$this->profile["ID"]]["PRODUCTS_EXPORT"] >= $this->profile["SETUP"]["CRON"][$processId]["MAXIMUM_PRODUCTS"] ) ) ){
                    break;
                }
            }

            if( $bSchemeUseOffer || $bSchemeUseOfferSku || $bSchemeUseSkuByOffer ){
                $arOfferElementResult = array();
                $this->delay = "";
                $arItem = $this->ProcessElement( $arElement, false, false, $arElementConfig );

                if( !$arItem )
                    continue;
            }

            // if you enable the processing trade offers, we look for and process trade offers
            if( $this->catalogIncluded && ( $this->profile["USE_SKU"] == "Y" ) && ( $bSchemeUseSku || $bSchemeUseSkuByOffer ) ){
                if( !$bSchemeUseOffer && !$bSchemeUseOfferSku && !$bSchemeUseSkuByOffer ){
                    $arItem = $arElement->GetFields();
                }

                if( ( $arItem["ACTIVE"] == "Y" ) && ( $this->catalogSKU[$arItem["IBLOCK_ID"]] ) ){
                    if( isset( $arElementConfig["DELAY"] ) && ( $arElementConfig["DELAY"] == true ) )
                        $arElementConfig["DELAY_SKU"] = true;

                    $arOfferFilter = array(
                        "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                        "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
                    );

                    $dbOfferElements = CIBlockElement::GetList(
                        array(),
                        $arOfferFilter,
                        false,
                        false,
                        array()
                    );

                    $bExportStepFinish = false;
                    while( $arOfferElement = $dbOfferElements->GetNextElement() ){
                        $processSessionData = AcritGooglemerchantSession::GetSession( $this->profile["ID"] );

                        if( $bStepExport ){
                            if( ( $processSessionData["GOOGLEMERCHANT"]["LOG"][$this->profile["ID"]]["PRODUCTS_EXPORT"] >= ( $iLastSessionExportProductsCnt + $this->profile["SETUP"]["CRON"][$processId]["STEP_EXPORT_CNT"] ) )
                                || ( ( intval( $this->profile["SETUP"]["CRON"][$processId]["MAXIMUM_PRODUCTS"] ) > 0 ) && ( $processSessionData["GOOGLEMERCHANT"]["LOG"][$this->profile["ID"]]["PRODUCTS_EXPORT"] >= $this->profile["SETUP"]["CRON"][$processId]["MAXIMUM_PRODUCTS"] ) ) ){
                                $bExportStepFinish = true;
                                break;
                            }
                        }

                        $arOfferItem = $this->ProcessElement( $arOfferElement, $arItem, false, $arElementConfig, $arOfferElementResult );
                        unset( $arOfferItem );

                        if( $this->isDemo && $this->DemoCount() ){
                            break;
                        }
                    }

                    if( $bExportStepFinish ){
                        break;
                    }
                }
            }

            if( $this->isDemo && $this->DemoCount() ){
                break;
            }

            unset( $arItem );

            if( isset( $arElementConfig["DELAY"] ) && $arElementConfig["DELAY"] == true ){
                $arElementConfig["DELAY_FLUSH"] = true;
                if( isset( $field["MINIMUM_OFFER_PRICE"] ) && $field["MINIMUM_OFFER_PRICE"] == "Y" ){
                    $arElementConfig["MINIMUM_OFFER_PRICE"] = "Y";
                }
                $this->ProcessElement( $arElement, false, false, $arElementConfig, $arOfferElementResult );

                unset( $arElementConfig["DELAY_SKU"] );
                unset( $arElementConfig["DELAY_FLUSH"] );
                if( isset( $arElementConfig["MINIMUM_OFFER_PRICE"] ) )
                    unset( $arElementConfig["MINIMUM_OFFER_PRICE"] );
            }
        }

        unset( $arElement, $arItem );

        if( !$cronrun ){
            echo '<div style="width: 100%; text-align: center; font-size: 18px; margin: 40px 0; padding: 40px 0; border: 1px solid #ccc; border-radius: 6px; background: #f5f5f5;">',
            GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_EXPORT_RUN" ), "<br/>",
            str_replace( array( "#PROFILE_ID#", "#PROFILE_NAME#" ), array( $this->profile["ID"], $this->profile["NAME"] ), GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_STEP_PROFILE" ) ), "<br/>",
            str_replace( array( "#STEP#", "#COUNT#" ), array( $page, $navPageCount ), GetMessage( "ACRIT_GOOGLEMERCHANT_RUN_STEP_RUN" ) ),
            "</div>";
        }

        CAcritGooglemerchantTools::SaveCurrencies( $this->profile, $this->currencyList );

        if( $this->isDemo && $this->DemoCount() ){
            return true;
        }

        if( $page >= $dbElements->NavPageCount ){
            return true;
        }

        return false;
    }

    private function PrepareItemToProcess( $arElement, $arProductSKU = false ){
        $this->AddResolve();
        $arItem = $this->GetElementProperties( $arElement );

				if(!$arProductSKU
						&& ( intval( $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"] ) > 0 )
						&& ( intval( $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] ) > 0 )) {
						$arCheckOfferFilter = array(
								"IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
								"PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
						);
						$dbCheckOfferElements = CIBlockElement::GetList(
								array(),
								$arCheckOfferFilter,
								false,
								false,
								array()
						);
						$arItem['_OFFERS_COUNT'] = $dbCheckOfferElements->SelectedRowsCount();
				}
				if( !$arProductSKU && ( $this->profile["EXPORT_DATA_OFFER_WITH_SKU_DATA"] != "Y" )){
						if( $arItem['_OFFERS_COUNT'] > 0 ){
								return $arItem;
						}
				}

        if( ( $this->profile["EXPORT_DATA_OFFER_WITH_SKU_DATA"] == "Y" ) && !$arProductSKU && $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"] ){

						$arOfferWithSkuSort = array(
								"CATALOG_PRICE_".$this->basePriceId => "ASC",
						);

						$arOfferFilter = array(
                "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"],
                "!CATALOG_PRICE_".$this->basePriceId => false
            );

						if($this->profile["SETUP"]["OFFER_WITH_SKU_USE_QUANTITY"]=='Y') {
								$arOfferFilter['!CATALOG_QUANTITY'] = false;
						}

            $dbOfferElements = CIBlockElement::GetList(
                $arOfferWithSkuSort,
                $arOfferFilter,
                false,
                false,
                array()
            );

	        $bStoresSum = false;

            if( $arOfferElement = $dbOfferElements->GetNextElement() ){
                $arOfferItem = $this->GetElementProperties( $arOfferElement );

                foreach( $arOfferItem as $itemIndex => $itemValue ){
                    if( ( is_array( $arItem[$itemIndex] ) && empty( $arItem[$itemIndex] ) )
                        || ( !is_array( $arItem[$itemIndex] ) && ( strlen( trim( $arItem[$itemIndex] ) ) <= 0 ) )
                        || ( !$arItem[$itemIndex] )
                        || !isset( $arItem[$itemIndex] )
                    ){
						$arItem[$itemIndex] = $itemValue;

	                    if (strpos($itemIndex, 'STORE_AMOUNT') !== false) {
		                    $bStoresSum = true;
	                    }
                    }
                }
            }
	        if ($bStoresSum) {
		        while ($arOfferElement = $dbOfferElements->GetNextElement()) {
			        $arOfferItem = $this->GetElementProperties( $arOfferElement );

			        foreach( $arOfferItem as $itemIndex => $itemValue ) {
				        if (isset($arItem[$itemIndex]) && strpos($itemIndex, 'STORE_AMOUNT') !== false) {
					        $arItem[$itemIndex] += $itemValue;
				        }
			        }
		        }
	        }
        }

        // add product properties and fields to product offers
        if( $this->catalogIncluded && is_array( $arProductSKU ) ){
            $excludeFields = array(
                "NAME",
                "PREVIEW_TEXT",
                "DETAIL_TEXT",
                "PREVIEW_PICTURE",
                "DETAIL_PICTURE",
                "CATALOG_QUANTITY",
                "CATALOG_QUANTITY_RESERVED",
                "CATALOG_WEIGHT",
                "CATALOG_WIDTH",
                "CATALOG_LENGTH",
                "CATALOG_HEIGHT",
                "CATALOG_PURCHASING_PRICE",
                "CATALOG_BARCODE",
            );

	        if ($this->profile["SETUP"]["SKU_USE_CANONICAL"] == 'Y' && trim($arProductSKU['CANONICAL_PAGE_URL']) != '') {
		        $arProductSKU['CANONICAL_PAGE_URL'] = preg_replace('#https?://[^/]+#i', '', $arProductSKU['CANONICAL_PAGE_URL']);
		        $arProductSKU['DETAIL_PAGE_URL'] = $arProductSKU['CANONICAL_PAGE_URL'];
		        $arItem['DETAIL_PAGE_URL'] = $arProductSKU['DETAIL_PAGE_URL'];
	        }

            foreach( $arProductSKU as $key => $value ){
                if( !isset( $arItem[$key] ) || empty( $arItem[$key] ) ){
                    if( !in_array( $key, $excludeFields ) && strpos($key, 'STORE_AMOUNT') === false){
                        $arItem[$key] = $value;
                    }
                }
            }

            if( array_key_exists( "DETAIL_PICTURE", $arProductSKU ) ){
                $arProductSKU["DETAIL_PICTURE"] = CFile::GetPath( $arProductSKU["~DETAIL_PICTURE"] );
            }
            if( array_key_exists( "PREVIEW_PICTURE", $arProductSKU ) ){
                $arProductSKU["PREVIEW_PICTURE"] = CFile::GetPath( $arProductSKU["~PREVIEW_PICTURE"] );
            }

            $arItem["ELEMENT_ID"] = $arProductSKU["ID"];
            $arItem["IBLOCK_SECTION_ID"] = $arProductSKU["IBLOCK_SECTION_ID"];
            foreach( $this->profile["NAMESCHEMA"] as $key => $value ){
                switch( $value ){
                    case $key."_OFFER":
                        if( $key == "CATALOG_PRICE" ){
                            foreach( $this->usePrices as $priceType ){
                                $arItem[$key."_".$priceType] = $arProductSKU[$key."_".$priceType];
                                $arItem[$key."_".$priceType] = ( $arItem[$key."_".$priceType] );
                            }
                        }
                        else{
                            $arItem[$key] = $arProductSKU[$key];
                            $arItem[$key] = ( $arItem[$key] );
                        }
                        break;
                    case $key."_OFFER_SKU":
                        if( $key == "CATALOG_PRICE" ){
                            foreach( $this->usePrices as $priceType ){
                                $arItem[$key."_".$priceType] = $arProductSKU[$key."_".$priceType];
                                $arItem[$key."_".$priceType] = ( $arItem[$key."_".$priceType] );
                            }
                        }
                        $arItem[$key] = implode( " ", array( $arProductSKU[$key], $arItem[$key] ) );
                        $arItem[$key] = ( $arItem[$key] );
                        break;
                    case $key."_OFFER_IF_SKU_EMPTY":
                        if( $key == "CATALOG_PRICE" ){
                            foreach( $this->usePrices as $priceType ){
                                if( !isset( $arItem[$key."_".$priceType] ) || empty( $arItem[$key."_".$priceType] ) ){
                                    if( isset( $arProductSKU[$key."_".$priceType] ) && !empty( $arProductSKU[$key."_".$priceType] ) ){
                                        $value = $arProductSKU[$key."_".$priceType];
                                        if( is_array( $value ) ){
                                            foreach( $value as $_key => $_value )
                                                $arItem[$key."_".$priceType][$_key] = ( $_value );
                                        }
                                        else{
                                            $arItem[$key."_".$priceType] = $value;
                                            $arItem[$key."_".$priceType] = ( $arItem[$key."_".$priceType] );
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            if( !isset( $arItem[$key] ) || empty( $arItem[$key] ) ){
                                if( isset( $arProductSKU[$key] ) && !empty( $arProductSKU[$key] ) ){
                                    $value = $arProductSKU[$key];
                                    if( is_array( $value ) ){
                                        foreach( $value as $_key => $_value )
                                            $arItem[$key][$_key] = ( $_value );
                                    }
                                    else{
                                        $arItem[$key] = $value;
                                        $arItem[$key] = ( $arItem[$key] );
                                    }
                                }
                            }
                        }

                        break;
                }
            }
        }
        else{
            $arItem["GROUP_ITEM_ID"] = $arItem["ID"];
        }

        return $arItem;
    }

    // get product properties, template creation, set fields values, write it in file
    private function ProcessElement( $arElement, $arProductSKU = false, $bCsvMode = false, $arItemConfig = array(), &$arOfferElementResult = array() ){
        static $arSectionCache;
        global $DB, $USER;
        $skipElement = false;
        $this->xmlCode = false;
        $_arOfferElementResult = array();

        $arItem = self::PrepareItemToProcess( $arElement, $arProductSKU );

        // check element on basic profile conditions
        if( $this->catalogIncluded ){
            $profileData = $this->profile;
						
						if($profileData['SKIP_WITH_SKU']=='Y' && !$arProductSKU && $arItem['_OFFERS_COUNT']>0) {
							$skipElement = true;
						}

            if( $profileData["SETUP"]["VALIDATE_CONDITIONS"] == "Y" ){
                $sBeforeConditions = serialize( $profileData["CONDITION"] );

                foreach( $profileData["CONDITION"]["CHILDREN"] as $condtionIndex => $arCondtion ){
                    if( isset( $arCondtion["CHILDREN"] ) && is_array( $arCondtion["CHILDREN"] ) && !empty( $arCondtion["CHILDREN"] ) ){
                        foreach( $arCondtion["CHILDREN"] as $condtionChildIndex => $arChildCondtion ){
                            if( isset( $arChildCondtion["CLASS_ID"] ) && !empty( $arChildCondtion["CLASS_ID"] ) ){
                                if( stripos( $arChildCondtion["CLASS_ID"], "CondIBProp" ) !== false ){
                                    $aConditionPartData = explode( ":", $arChildCondtion["CLASS_ID"] );

                                    if( $aConditionPartData[1] != $arItem["IBLOCK_ID"] ){
                                        unset( $profileData["CONDITION"]["CHILDREN"][$condtionIndex]["CHILDREN"][$condtionChildIndex] );
                                        if( empty( $profileData["CONDITION"]["CHILDREN"][$condtionIndex]["CHILDREN"] ) ){
                                            unset( $profileData["CONDITION"]["CHILDREN"][$condtionIndex] );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else{
                        if( isset( $arCondtion["CLASS_ID"] ) && !empty( $arCondtion["CLASS_ID"] ) ){
                            if( stripos( $arCondtion["CLASS_ID"], "CondIBProp" ) !== false ){
                                $aConditionPartData = explode( ":", $arCondtion["CLASS_ID"] );

                                if( $aConditionPartData[1] != $arItem["IBLOCK_ID"] ){
                                    unset( $profileData["CONDITION"]["CHILDREN"][$condtionIndex] );
                                    if( empty( $profileData["CONDITION"]["CHILDREN"] ) ){
                                        unset( $profileData["CONDITION"]["CHILDREN"] );
                                    }
                                }
                            }
                        }
                    }
                }

                $sAfterConditions = serialize( $profileData["CONDITION"] );

                if( $sBeforeConditions !== $sAfterConditions ){
                    $obCond = new CAcritGooglemerchantCatalogCond();
                    CAcritGooglemerchantProps::$arIBlockFilter = CGooglemerchantProfile::PrepareIBlock( $profileData["IBLOCK_ID"], $profileData["USE_SKU"] );
                    $obCond->Init( BT_COND_MODE_GENERATE, 0, array() );
                    $profileData["EVAL_FILTER"] = $obCond->Generate( $profileData["CONDITION"], array( "FIELD" => '$GLOBALS["CHECK_COND"]' ) );
                }
            }

            if( !CAcritGooglemerchantTools::CheckCondition( $arItem, $profileData["EVAL_FILTER"] ) ){
                if( !$arProductSKU && ( $profileData["EXPORT_DATA_SKU_BY_OFFER"] == "Y" ) ){
                    $returnResult = false;
                    return $returnResult;
                }
                else{
                    $returnResult = ( $bCsvMode ) ? false : $arItem;
                    return $returnResult;
                }
            }
            elseif( !$arProductSKU && ( $profileData["EXPORT_DATA_SKU_BY_OFFER"] == "Y" ) ){
                $returnResult = ( $bCsvMode ) ? false : $arItem;
                return $returnResult;
            }
        }

        // inc statistic product counter
        $this->log->IncProduct();

        if( $bCsvMode ){
            $templateValues = array();
        }
        else{
            $itemTemplate = $this->profile["OFFER_TEMPLATE"];
            $templateValues = $this->templateValuesDefaults;
        }


        if( empty( $arSectionCache[$arItem["IBLOCK_ID"]] ) ){
            $rs = CIBlockSection::GetList(
                array(
                    "LEFT_MARGIN" => "ASC"
                ),
                array(
                    "IBLOCK_ID" => $arItem["IBLOCK_ID"]
                )
            );
            while( $ar = $rs->GetNext( false, false ) ){
                if( intval( $ar["PICTURE"] ) ){
                    $ar["PICTURE"] = CAcritGooglemerchantTools::GetFilePath( $ar["PICTURE"] );
                }
                if( intval( $ar["DETAIL_PICTURE"] ) ){
                    $ar["DETAIL_PICTURE"] = CAcritGooglemerchantTools::GetFilePath( $ar["DETAIL_PICTURE"] );
                }

                $arSectionCache[$arItem["IBLOCK_ID"]][$ar["ID"]] = $ar;
            }
        }

        if( !$bCsvMode ){
            $arItemSections = array();
            if( $this->profile["EXPORT_PARENT_CATEGORIES_TO_OFFER"] == "Y" ){
                $arItemSections = $arItem["SECTION_PARENT_ID"];

                if( $this->profile["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ){
                    foreach( $arItem["SECTION_ID"] as $itemSectionId ){
                        if( !in_array( $itemSectionId, $arItemSections ) ){
                            $arItemSections[] = $itemSectionId;
                        }
                    }
                }
            }
            elseif( $this->profile["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ){
                $arItemSections = $arItem["SECTION_ID"];
            }

            $sectionExportRow = "";
            if( !empty( $arItemSections ) ){
                foreach( $arItemSections as $arItemSectionsId ){
                    $sectionExportRow .= "<categoryId>".$arItemSectionsId."</categoryId>".PHP_EOL;
                }

                $itemTemplate = str_replace( "<categoryId>#CATEGORYID#</categoryId>", $sectionExportRow, $itemTemplate );
            }

            $templateValues["#GROUP_ITEM_ID#"] = $arItem["GROUP_ITEM_ID"];
        }

        $arItemMain = $arItem;
        $fieldPrePostfix = ( $bCsvMode ) ? "" : "#";

        foreach( $this->profile["XMLDATA"] as $xmlCode => $field ){
            $this->xmlCode = $xmlCode;
            $arItem = $arItemMain;
            $fieldIndex = $fieldPrePostfix.$field["CODE"].$fieldPrePostfix;

            $useCondition = ( $field["USE_CONDITION"] == "Y" );
            if( $useCondition ){
                $conditionTrue = ( CAcritGooglemerchantTools::CheckCondition( $arItem, $field["EVAL_FILTER"] ) == true );
            }

            if( $useCondition && !$conditionTrue ){
                if( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_FALSE_TYPE"] == "const" ) ) ){

                    $field["CONTVALUE_FALSE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_FALSE"] : $field["COMPLEX_FALSE_CONTVALUE"];
                    $templateValues[$fieldIndex] = $field["CONTVALUE_FALSE"];
                }
                else{
                    if( $field["TYPE"] == "composite" ){
                        $compositeValue = "";
                        $compositeFalseDivider = ( strlen( $field["COMPOSITE_FALSE_DIVIDER"] ) > 0 ) ? $field["COMPOSITE_FALSE_DIVIDER"] : " ";
                        foreach( $field["COMPOSITE_FALSE"] as $compositeFieldIndex => $compositeField ){
                            if( $compositeFieldIndex > 1 ){
                                $compositeValue .= $compositeFalseDivider;
                            }
                            if( $compositeField["COMPOSITE_FALSE_TYPE"] == "const" ){
                                $compositeValue .= CAcritGooglemerchantTools::RoundNumber( $compositeField["COMPOSITE_FALSE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $compositeField["COMPOSITE_FALSE_TYPE"] == "field" ){
                                $compositeValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $compositeValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $compositeField["COMPOSITE_FALSE_VALUE"] );

                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $compositeField["COMPOSITE_FALSE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $compositeField["COMPOSITE_FALSE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );

                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $arItem[$compositeField["COMPOSITE_FALSE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;
                                            $compositeValueTmp = $arItem["CATALOG_".$arValue[1]];

                                            if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                            }
                                            else{
                                                preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                            }

                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $compositeValueTmp = $convertFrom;
                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $compositeValueTmp;
                                                }

                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $compositeValueTmp = $convertTo;
                                                        $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $compositeValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }
                                                        }
                                                        else{
                                                            $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $compositeValueTmp += $compositeValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $compositeValueTmp;
                                                    }
                                                }
                                            }

                                            if( stripos( $arValue[1], "_WD" ) !== false ){
                                                if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                    in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                        $compositeValueTmp,
                                                        $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                        $arDiscounts
                                                    );

                                                    $discount = $compositeValueTmp - $discountPrice;
                                                }
                                                else{
                                                    $discountPrice = $compositeValueTmp;
                                                    $discount = 0;
                                                }

                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                                $compositeValueTmp = $discountPrice;
                                                $values = $compositeValueTmp;
                                            }

                                            if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                                $compositeValueTmp = CAcritGooglemerchantTools::BitrixRoundNumber( $compositeValueTmp, $arValue[1] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            if( is_array( $arProductSKU ) ){
                                                $values = $compositeValueTmp;
                                            }

                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritGooglemerchantTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }

                                                if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                                }
                                                else{
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                                }

                                                if( is_array( $arProcessValues ) ){
                                                    $arProcessValuesMultiproFormat = CAcritGooglemerchantTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                    $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                    $compositeValueTmp = array();
                                                    foreach( $arProcessValues as $val ){
                                                        if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                            if( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                                $compositeValueTmp[] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            }
                                                        }
                                                        else{
                                                            $compositeValueTmp[] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }

                                                    $compositeValueTmpStr = "";
                                                    if( !empty( $compositeValueTmp ) ){
                                                        foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                            if( $compositeValueTmpIndex ){
                                                                $compositeValueTmpStr .= $compositeFalseDivider;
                                                            }
                                                            $compositeValueTmpStr .= $compositeValueTmpItem;
                                                        }
                                                    }

                                                    if( strlen( $compositeValueTmpStr ) > 0 ){
                                                        $compositeValueTmp = $compositeValueTmpStr;
                                                    }

                                                    if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                        $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                        $compositeValueTmp = implode( $fieldMultipropDivider, $compositeValueTmp );
                                                    }
                                                }
                                                else{
                                                    $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 5:
                                            $arItem = $arItemMain;

                                            $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                            $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritGooglemerchantTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $compositeValueTmp = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $compositeValueTmp[] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $compositeValueTmp[] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                $compositeValueTmpStr = "";
                                                if( !empty( $compositeValueTmp ) ){
                                                    foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                        if( $compositeValueTmpIndex ){
                                                            $compositeValueTmpStr .= $compositeFalseDivider;
                                                        }
                                                        $compositeValueTmpStr .= $compositeValueTmpItem;
                                                    }
                                                }

                                                if( strlen( $compositeValueTmpStr ) > 0 ){
                                                    $compositeValueTmp = $compositeValueTmpStr;
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $compositeValueTmp = implode( $fieldMultipropDivider, $compositeValueTmp );
                                                }
                                            }
                                            else{
                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            break;
                                    }
                                }
                                $compositeValue .= $compositeValueTmp;
                            }
                        }
                        $templateValues[$fieldIndex] =  CAcritGooglemerchantTools::RoundNumber( $compositeValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                    }
                    else{
                        $field["VALUE"] = $field["COMPLEX_FALSE_VALUE"];
                        if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                            $templateValues[$fieldIndex] = detailLink( $arItem["ID"] );
                            if( !$bCsvMode ){
                                $linkParamSymbolIndex = stripos( $itemTemplate, "?" );
                                $linkUtmSymbolIndex = stripos( $itemTemplate, "?utm_source" );
                                if( $linkParamSymbolIndex != $linkUtmSymbolIndex ){
                                    $itemTemplate = str_replace( "?utm_source", "&amp;utm_source", $itemTemplate );
                                }
                            }
                        }
                        else{
                            if( function_exists( "acritRedefine" ) ){
                                $templateValues[$fieldIndex] = acritRedefine( $fieldIndex, $arItem["ID"], $this->profile["ID"] );
                            }

                            if( !$templateValues[$fieldIndex] ){
                                $arValue = explode( "-", $field["VALUE"] );

                                switch( count( $arValue ) ){
                                    case 1:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        if( strpos( $field["VALUE"], "." ) !== false ){
                                            $arField = explode( ".", $field["VALUE"] );
                                            switch( $arField[0] ){
                                                case "SECTION":
                                                    $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                    $value = $curSection[$arField[1]] ? : "";
                                                    break;
                                                default:
                                                    $value = "";
                                            }
                                            unset( $arField );
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $arItem[$field["VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        $arItem = $arItemMain;
                                        break;
                                    case 2:
                                        $values = null;
                                        $templateValues[$fieldIndex] = $arItem["CATALOG_".$arValue[1]];

                                        if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                            preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                        }
                                        else{
                                            preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                        }

                                        $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                        if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                            $templateValues[$fieldIndex] = $convertFrom;
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values = $templateValues[$fieldIndex];
                                            }

                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    $templateValues[$fieldIndex] = $convertTo;
                                                    $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values=$templateValues[$fieldIndex];
                                                    }
                                                }
                                            }
                                        }
                                        elseif( !empty( $arPriceCode[0] ) ){
                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                        $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                $arItem["CATALOG_".$arValue[1]],
                                                                $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                $convertTo
                                                            ),
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values=$templateValues[$fieldIndex];
                                                        }

                                                    }
                                                    else{
                                                        $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $templateValues[$fieldIndex];
                                                        }
                                                    }
                                                }
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            else{
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $templateValues[$fieldIndex] += $templateValues[$fieldIndex] *
                                                floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if(is_array( $arProductSKU )){
                                                    $values = $templateValues[$fieldIndex];
                                                }
                                            }
                                        }

                                        if( stripos( $arValue[1], "_WD" ) !== false ){
                                            if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                    $templateValues[$fieldIndex],
                                                    $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                    $arDiscounts
                                                );

                                                $discount = $templateValues[$fieldIndex] - $discountPrice;
                                            }
                                            else{
                                                $discountPrice = $templateValues[$fieldIndex];
                                                $discount = 0;
                                            }

                                            $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                            $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                            $templateValues[$fieldIndex] = $discountPrice;
                                            $values = $templateValues[$fieldIndex];
                                        }

                                        if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::BitrixRoundNumber( $templateValues[$fieldIndex], $arValue[1] );
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        if( is_array( $arProductSKU ) ){
                                            $values = $templateValues[$fieldIndex];
                                        }

                                        if( is_array( $arProductSKU )&& !is_null( $values ) )
                                            $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                        if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) && ( $arItemConfig["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                            if( isset( $arOfferElementResult[$xmlCode][$field["CODE"]] ) && count( $arOfferElementResult[$xmlCode][$field["CODE"]] ) ){
                                                if( isset( $field["MINIMUM_OFFER_PRICE_CODE"] ) && strlen( $field["MINIMUM_OFFER_PRICE_CODE"] ) ){
                                                    $templateValues[$fieldPrePostfix.$field["MINIMUM_OFFER_PRICE_CODE"].$fieldPrePostfix] = min( $arOfferElementResult[$xmlCode][$field["CODE"]] );
                                                }
                                            }
                                        }
                                        elseif( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                        }
                                        break;
                                    case 3:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        if( $arValue[0] == $arItem["IBLOCK_ID"] || $arValue[0] == $arProductSKU["IBLOCK_ID"] ){
                                            if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritGooglemerchantTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                            }
                                            else{
                                                $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                            }

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritGooglemerchantTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $templateValues[$fieldIndex] = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $templateValues[$fieldIndex] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $templateValues[$fieldIndex][] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $templateValues[$fieldIndex][] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $templateValues[$fieldIndex] = implode( $fieldMultipropDivider, $templateValues[$fieldIndex] );
                                                }
                                            }
                                            else{
                                                $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                        }
                                        $arItem = $arItemMain;
                                        break;
                                    case 5:
                                        $arItem = $arItemMain;

                                        $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                        $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                        if( is_array( $arProcessValues ) ){
                                            $arProcessValuesMultiproFormat = CAcritGooglemerchantTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                            $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                            $templateValues[$fieldIndex] = array();
                                            foreach( $arProcessValues as $val ){
                                                if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                    if( count( $templateValues[$fieldIndex] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                        $templateValues[$fieldIndex][] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }
                                                else{
                                                    $templateValues[$fieldIndex][] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }

                                            if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                $templateValues[$fieldIndex] = implode( $fieldMultipropDivider, $templateValues[$fieldIndex] );
                                            }
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }

                                        break;
                                }
                            }
                        }
                    }
                }
            }
            else{
                // field or property
                if( ( $field["TYPE"] == "field" )
                    || ( $field["TYPE"] == "composite" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "field" ) ) ){

                    if( $field["TYPE"] == "composite" ){
                        $compositeValue = "";
                        $compositeTrueDivider = ( strlen( $field["COMPOSITE_TRUE_DIVIDER"] ) > 0 ) ? $field["COMPOSITE_TRUE_DIVIDER"] : " ";
                        foreach( $field["COMPOSITE_TRUE"] as $compositeFieldIndex => $compositeField ){
                            if( $compositeFieldIndex > 1 ){
                                $compositeValue .= $compositeTrueDivider;
                            }
                            if( $compositeField["COMPOSITE_TRUE_TYPE"] == "const" ){
                                $compositeValue .= CAcritGooglemerchantTools::RoundNumber( $compositeField["COMPOSITE_TRUE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $compositeField["COMPOSITE_TRUE_TYPE"] == "field" ){
                                $compositeValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $compositeValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $compositeField["COMPOSITE_TRUE_VALUE"] );

                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $compositeField["COMPOSITE_TRUE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $compositeField["COMPOSITE_TRUE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );

                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $arItem[$compositeField["COMPOSITE_TRUE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;
                                            $compositeValueTmp = $arItem["CATALOG_".$arValue[1]];

                                            if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                            }
                                            else{
                                                preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                            }

                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $compositeValueTmp = $convertFrom;
                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $compositeValueTmp;
                                                }

                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $compositeValueTmp = $convertTo;
                                                        $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $compositeValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }
                                                        }
                                                        else{
                                                            $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $compositeValueTmp += $compositeValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $compositeValueTmp;
                                                    }
                                                }
                                            }

                                            if( stripos( $arValue[1], "_WD" ) !== false ){
                                                if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                    in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                        $compositeValueTmp,
                                                        $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                        $arDiscounts
                                                    );

                                                    $discount = $compositeValueTmp - $discountPrice;
                                                }
                                                else{
                                                    $discountPrice = $compositeValueTmp;
                                                    $discount = 0;
                                                }

                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                                $compositeValueTmp = $discountPrice;
                                                $values = $compositeValueTmp;
                                            }

                                            if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                                $compositeValueTmp = CAcritGooglemerchantTools::BitrixRoundNumber( $compositeValueTmp, $arValue[1] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            if( is_array( $arProductSKU ) ){
                                                $values = $compositeValueTmp;
                                            }

                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritGooglemerchantTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }

                                                if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                                }
                                                else{
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                                }

                                                if( is_array( $arProcessValues ) ){
                                                    $arProcessValuesMultiproFormat = CAcritGooglemerchantTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                    $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                    $compositeValueTmp = array();
                                                    foreach( $arProcessValues as $val ){
                                                        if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                            if( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                                $compositeValueTmp[] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            }
                                                        }
                                                        else{
                                                            $compositeValueTmp[] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }

                                                    $compositeValueTmpStr = "";
                                                    if( !empty( $compositeValueTmp ) ){
                                                        foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                            if( $compositeValueTmpIndex ){
                                                                $compositeValueTmpStr .= $compositeTrueDivider;
                                                            }
                                                            $compositeValueTmpStr .= $compositeValueTmpItem;
                                                        }
                                                    }

                                                    if( strlen( $compositeValueTmpStr ) > 0 ){
                                                        $compositeValueTmp = $compositeValueTmpStr;
                                                    }

                                                    if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                        $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                        $compositeValueTmp = implode( $fieldMultipropDivider, $compositeValueTmp );
                                                    }
                                                }
                                                else{
                                                    $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 5:
                                            $arItem = $arItemMain;

                                            $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                            $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritGooglemerchantTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $compositeValueTmp = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $compositeValueTmp[] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $compositeValueTmp[] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                $compositeValueTmpStr = "";
                                                if( !empty( $compositeValueTmp ) ){
                                                    foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                        if( $compositeValueTmpIndex ){
                                                            $compositeValueTmpStr .= $compositeTrueDivider;
                                                        }
                                                        $compositeValueTmpStr .= $compositeValueTmpItem;
                                                    }
                                                }

                                                if( strlen( $compositeValueTmpStr ) > 0 ){
                                                    $compositeValueTmp = $compositeValueTmpStr;
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $compositeValueTmp = implode( $fieldMultipropDivider, $compositeValueTmp );
                                                }
                                            }
                                            else{
                                                $compositeValueTmp = CAcritGooglemerchantTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            $arItem = $arItemMain;
                                            break;
                                    }
                                }
                                $compositeValue .= $compositeValueTmp;
                            }
                        }
                        $templateValues[$fieldIndex] =  CAcritGooglemerchantTools::RoundNumber( $compositeValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                    }
                    else{
                        $field["VALUE"] = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];

                        if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                            $templateValues[$fieldIndex] = detailLink( $arItem["ID"] );
                            if( !$bCsvMode ){
                                $linkParamSymbolIndex = stripos( $itemTemplate, "?" );
                                $linkUtmSymbolIndex = stripos( $itemTemplate, "?utm_source" );
                                if( $linkParamSymbolIndex != $linkUtmSymbolIndex ){
                                    $itemTemplate = str_replace( "?utm_source", "&amp;utm_source", $itemTemplate );
                                }
                            }
                        }
                        else{
                            if( function_exists( "acritRedefine" ) ){
                                $templateValues[$fieldIndex] = acritRedefine( $fieldIndex, $arItem["ID"], $this->profile["ID"] );
                            }

                            if( !$templateValues[$fieldIndex] ){
                                $arValue = explode( "-", $field["VALUE"] );

                                switch( count( $arValue ) ){
                                    case 1:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        if( strpos( $field["VALUE"], "." ) !== false ){
                                            $arField = explode( ".", $field["VALUE"] );
                                            switch( $arField[0] ){
                                                case "SECTION":
                                                    $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                    $value = $curSection[$arField[1]] ?: "";
                                                    break;
                                                default:
                                                    $value = "";
                                            }
                                            unset( $arField );
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $arItem[$field["VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        $arItem = $arItemMain;
                                        break;
                                    case 2:
                                        $values = null;
                                        $templateValues[$fieldIndex] = $arItem["CATALOG_".$arValue[1]];

                                        if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                            preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                        }
                                        else{
                                            preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                        }

                                        $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                        if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                            $templateValues[$fieldIndex] = $convertFrom;
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values = $templateValues[$fieldIndex];
                                            }

                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    $templateValues[$fieldIndex] = $convertTo;
                                                    $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $templateValues[$fieldIndex];
                                                    }
                                                }
                                            }
                                        }
                                        elseif( !empty( $arPriceCode[0] ) ){
                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                        $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                $arItem["CATALOG_".$arValue[1]],
                                                                $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                $convertTo
                                                            ),
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $templateValues[$fieldIndex];
                                                        }
                                                    }
                                                    else{
                                                        $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );

                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $templateValues[$fieldIndex];
                                                        }
                                                    }
                                                }
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            else{
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $templateValues[$fieldIndex] += $templateValues[$fieldIndex] *
                                                floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $templateValues[$fieldIndex];
                                                }
                                            }
                                        }

                                        if( stripos( $arValue[1], "_WD" ) !== false ){
                                            if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                    $templateValues[$fieldIndex],
                                                    $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                    $arDiscounts
                                                );

                                                $discount = $templateValues[$fieldIndex] - $discountPrice;
                                            }
                                            else{
                                                $discountPrice = $templateValues[$fieldIndex];
                                                $discount = 0;
                                            }

                                            $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                            $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                            $templateValues[$fieldIndex] = $discountPrice;
                                            $values = $templateValues[$fieldIndex];
                                        }

                                        if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::BitrixRoundNumber( $templateValues[$fieldIndex], $arValue[1] );
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        if( is_array( $arProductSKU ) ){
                                            $values = $templateValues[$fieldIndex];
                                        }

                                        if( is_array( $arProductSKU )&& !is_null( $values ) )
                                            $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                        if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) && ( $arItemConfig["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                            if( count( $arOfferElementResult[$xmlCode][$field["CODE"]] ) ){
                                                if( isset( $field["MINIMUM_OFFER_PRICE_CODE"] ) && strlen( $field["MINIMUM_OFFER_PRICE_CODE"] ) ){
                                                    $templateValues[$fieldPrePostfix.$field["MINIMUM_OFFER_PRICE_CODE"].$fieldPrePostfix] = min( $arOfferElementResult[$xmlCode][$field["CODE"]] );
                                                }
                                            }
                                        }
                                        elseif( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                        }
                                        break;
                                    case 3:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                            if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritGooglemerchantTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                            }
                                            else{
                                                $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                            }

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritGooglemerchantTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $templateValues[$fieldIndex] = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $templateValues[$fieldIndex] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $templateValues[$fieldIndex][] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $templateValues[$fieldIndex][] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $templateValues[$fieldIndex] = implode( $fieldMultipropDivider, $templateValues[$fieldIndex] );
                                                }
                                            }
                                            else{
                                                $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                        }
                                        $arItem = $arItemMain;
                                        break;
                                    case 5:
                                        $arItem = $arItemMain;

                                        $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                        $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                        if( is_array( $arProcessValues ) ){
                                            $arProcessValuesMultiproFormat = CAcritGooglemerchantTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                            $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                            $templateValues[$fieldIndex] = array();
                                            foreach( $arProcessValues as $val ){
                                                if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                    if( count( $templateValues[$fieldIndex] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                        $templateValues[$fieldIndex][] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }
                                                else{
                                                    $templateValues[$fieldIndex][] = CAcritGooglemerchantTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }

                                            if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                $templateValues[$fieldIndex] = implode( $fieldMultipropDivider, $templateValues[$fieldIndex] );
                                            }
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }

                                        break;
                                }
                            }
                        }
                    }
                }
                elseif( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "const" ) ) ){ // const

                    $field["CONTVALUE_TRUE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_TRUE"] : $field["COMPLEX_TRUE_CONTVALUE"];
                    $templateValues[$fieldIndex] =  CAcritGooglemerchantTools::RoundNumber( $field["CONTVALUE_TRUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                }
                else{
                    $templateValues[$fieldIndex] = "";
                }
            }

            if( $field["EXPORT_ROWCATEGORY_PARENT_LIST"] == "Y" ){
                $dbSectionRowCategoryParentList = CIBlockSection::GetNavChain( false, $templateValues[$fieldIndex] );
                if( $dbSectionRowCategoryParentList->SelectedRowsCount() ){
                    $sRowCategoryParentList = "";
                    while( $arSectionRowCategoryParentList = $dbSectionRowCategoryParentList->GetNext() ){
                        $sRowCategoryParentList = ( strlen( $sRowCategoryParentList ) <= 0 ) ? $arSectionRowCategoryParentList["NAME"] : $sRowCategoryParentList." > ".$arSectionRowCategoryParentList["NAME"];
                    }

                    if( strlen( $sRowCategoryParentList ) > 0 ){
                        $templateValues[$fieldIndex] = $sRowCategoryParentList;
                    }
                }
            }
            else{
                if( $DB->IsDate( $templateValues[$fieldIndex] ) && ( $this->profile["DATEFORMAT"] == $this->baseDateTimePatern ) ){
                    $templateValues[$fieldIndex] = CAcritGooglemerchantTools::RoundNumber( CAcritGooglemerchantTools::GetYandexDateTime( $templateValues[$fieldIndex] ), $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );

                    $dateTimeValue = MakeTimeStamp( "" );
                    $dateTimeFormattedValue = date( "Y-m-d", $dateTimeValue );
                    if( stripos( $templateValues[$fieldIndex], $dateTimeFormattedValue ) !== false ){
                        $skipElement = true;
                        $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "#FIELD#", $fieldIndex, GetMessage( "ACRIT_GOOGLEMERCHANT_REQUIRED_FIELD_SKIP" ) ) );
                        $this->log->IncProductError();
                    }
                }

                if( $bCsvMode ){
                    $templateValues = CAcritGooglemerchantStringProcess::ProcessTagOptions(
                        $templateValues,
                        $field,
                        $fieldIndex
                    );
                }
                else{
                    $templateValues = CAcritGooglemerchantStringProcess::ProcessTagOptions(
                        $templateValues,
                        $field,
                        $fieldIndex,
                        true,
                        $itemTemplate,
                        $this->arMatches
                    );
                }
            }

            if( ( $field["REQUIRED"] == "Y" ) && ( empty( $templateValues[$fieldIndex] ) || !isset( $templateValues[$fieldIndex] ) ) ){
                $skipElement = true;
                $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "#FIELD#", $fieldIndex, GetMessage( "ACRIT_GOOGLEMERCHANT_REQUIRED_FIELD_SKIP" ) ) );
                $this->log->IncProductError();
            }
        }
        $arItem = $arItemMain;

        array_walk( $templateValues, function( &$value ){
            if( is_array( $value ) ){
                foreach( $value as $id => $val )
                    $value[$id] = $val;
            }
            else
            $value = $value;
        });

        if( !$bCsvMode ){
            // set market category if it checked
            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = "";
            if( $this->profile["USE_MARKET_CATEGORY"] == "Y" ){
                $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
            }

            $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"];

            if( $this->profile["SETUP"]["USE_CATEGORY_REDEFINE_TAG"] == "Y" ){
                if( strlen( trim( $this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"] ) ) > 0 ){
                    $marketCategoryValue = $arMarketCategoryList[$templateValues[$fieldPrePostfix.$this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"].$fieldPrePostfix]];
                    $profileCategoryValue = $this->profile["PROFILE_CATEGORIES"][$templateValues[$fieldPrePostfix.$this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"].$fieldPrePostfix]]["NAME"];

                    if( strlen( trim( $marketCategoryValue ) ) > 0 ){
                        $templateValues[$fieldPrePostfix.$this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"].$fieldPrePostfix] = trim( $marketCategoryValue );
                    }
                    elseif( strlen( trim( $profileCategoryValue ) ) > 0 ){
                        $templateValues[$fieldPrePostfix.$this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"].$fieldPrePostfix] = trim( $profileCategoryValue );
                    }
                }
            }

            //for some realty
            if( isset( $templateValues[$fieldPrePostfix."PRICE_VALUE".$fieldPrePostfix] ) ){
                $templateValues[$fieldPrePostfix."PRICE_VALUE".$fieldPrePostfix] = intval( $templateValues[$fieldPrePostfix."PRICE_VALUE".$fieldPrePostfix] );
            }

            if( isset( $templateValues[$fieldPrePostfix."OBJECT_IMAGE".$fieldPrePostfix] ) ){
                if( !file_exists( $templateValues[$fieldPrePostfix."OBJECT_IMAGE".$fieldPrePostfix] ) ){
                    $templateValues[$fieldPrePostfix."OBJECT_IMAGE".$fieldPrePostfix] = $this->defaultFields[$fieldPrePostfix."SITE_URL".$fieldPrePostfix].$templateValues[$fieldPrePostfix."OBJECT_IMAGE".$fieldPrePostfix];
                }
            }

            // set values
            $itemTemplate = str_replace( array_keys( $this->defaultFields ), array_values( $this->defaultFields ), $itemTemplate );
            $itemTemplate = str_replace( array_keys( $templateValues ), array_values( $templateValues ), $itemTemplate );

            // removes empty first level tags, if there is no nesting
            $itemTemplate = preg_replace( "/(\r\n[\t]*\r\n)/", "\r\n", $itemTemplate );
            $itemTemplate = preg_replace( "/(\r\n\r\n)/", "\r\n", $itemTemplate );

            $itemTemplate = preg_replace( "/\s\w+\W*\w*=\"\"/", "", $itemTemplate );
            if( $this->profile["USE_EMPTY_TAG_CUT"] == "Y" ){
                $itemTemplate = preg_replace( "#(<\S+/>)#i", "", $itemTemplate );
								$strPattern = "#(<(.*)>\s*<\/\\2>)#is";
								while(preg_match( $strPattern, $itemTemplate )) {
									$itemTemplate = preg_replace( $strPattern,  "", $itemTemplate );
								}
            }

            if( !empty( $this->profile["CONVERT_DATA"] ) ){
                foreach( $this->profile["CONVERT_DATA"] as $arConvertBlock ){
                    $itemTemplate = str_replace( $arConvertBlock[0], $arConvertBlock[1], $itemTemplate );
                }
            }

            if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                $variantContainerId = $arItem["IBLOCK_ID"];
            }
            elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                $variantContainerId = $arItem["IBLOCK_PRODUCT_SECTION_ID"];
            }
            else{
                $variantContainerId = $arItem["IBLOCK_SECTION_ID"];
            }

            if( !$skipElement ){
                if( is_array( $_arOfferElementResult ) && count( $_arOfferElementResult ) ){
                    $arOfferElementResult = array_merge_recursive( $arOfferElementResult, $_arOfferElementResult );
                }
                $processElementId = ( intval( $arItem["ELEMENT_ID"] ) > 0 ) ? $arItem["ELEMENT_ID"] : $arItem["ID"];
                $dbElementGroups = CIBlockElement::GetElementGroups( $processElementId, true );
                $arItemSections = array();
                while( $arElementGroups = $dbElementGroups->Fetch() ){
                    $arItemSections[] = $arElementGroups["ID"];
                }

                CAcritGooglemerchantTools::SaveSections( $this->profile, $arItemSections );
                $this->DemoCountInc();

                if( isset( $arItemConfig["DELAY_FLUSH"] ) && ( $arItemConfig["DELAY_FLUSH"] === true ) ){
                    CAcritGooglemerchantExport::Save( $itemTemplate.$this->delay );
                    $this->delay = "";
                }
                elseif( isset( $arItemConfig["DELAY_SKU"] ) && ( $arItemConfig["DELAY_SKU"] === true ) ){
                    $this->delay .= $itemTemplate;
                    $this->log->IncProductExport();
                }
                elseif( isset( $arItemConfig["DELAY"] ) && ( $arItemConfig["DELAY"] === true ) ){
                    $this->log->IncProductExport();
                }
                else{
                    CAcritGooglemerchantExport::Save( $itemTemplate );
                    $this->log->IncProductExport();
                }
            }
            unset( $arElement, $dbPrices, $arQuantity );
            return $arItem;
        }
        else{
            if( !$skipElement ){
                $this->log->IncProductExport();
            }
            return !empty( $templateValues ) ? $templateValues : false;
        }
    }

    // searching product offers IB and remove them from list if them active and isset parent product offers IB
    protected function PrepareIBlock(){
        $excludeIBlock = array();
        $this->catalogSKU = array();
        $ibv = $this->iblockE;
        $ibd = $this->iblockD;

        if(
            ( ( $this->profile["USE_SKU"] == "Y" ) || ( $this->profile["TYPE"] == "advantshop" ) )
            && ( CAcritGooglemerchantTools::ArrayValidate( $this->profile["IBLOCK_ID"] ) )
        ){
            foreach( $this->profile["IBLOCK_ID"] as $iblocID ){
                if( $this->catalogIncluded ){
                    if( $arIBlock = CCatalog::GetByID( $iblocID ) ){
                        if( intval( $arIBlock["PRODUCT_IBLOCK_ID"] ) > 0 && in_array( $arIBlock["PRODUCT_IBLOCK_ID"], $this->profile["IBLOCK_ID"] ) )
                            $excludeIBlock[] = $arIBlock["IBLOCK_ID"];
                        if( intval( $arIBlock["OFFERS_IBLOCK_ID"] ) > 0 )
                            $this->catalogSKU[$arIBlock["IBLOCK_ID"]] = $arIBlock;
                    }
                }
            }
        }

        return array_diff( ( is_array( $this->profile["IBLOCK_ID"] ) ? $this->profile["IBLOCK_ID"] : array() ), $excludeIBlock );
    }

    // get product hl properties by hlBlockId
    private function GetElementHLProperties( $hlBlockId, $hlBasePropId = false, $arItem = false ){
        if( !isset( $hlBlockId ) || ( intval( $hlBlockId ) <= 0 ) ){
            return false;
        }

        $arEntityHLFieldsList = array();

        $dbHlElemProp = CIBlockElement::GetProperty( $arItem["IBLOCK_ID"], $arItem["ID"], array(), array( "ID" => $hlBasePropId ) );
        if( $arHlElemProp = $dbHlElemProp->GetNext() ){
            $arHLBlock = HighloadBlockTable::getList(
                array(
                    "filter" => array(
                        "=ID" => $hlBlockId,
                    )
                )
            )->fetch();

            $dbEntityHLFields = CUserTypeEntity::GetList(
                array(
                    "ID" => "ASC"
                ),
                array(
                    "ENTITY_ID" => "HLBLOCK_".$hlBlockId
                )
            );


            $iii = 0;
            while( $arEntityHLFieldsRow = $dbEntityHLFields->Fetch() ){
                $arEntityHLFieldsList[$arEntityHLFieldsRow["FIELD_NAME"]] = $arEntityHLFieldsRow;
                $iii++;
            }

            $obEntity = HighloadBlockTable::compileEntity( $arHLBlock );
            $strEntityDataClass = $obEntity->getDataClass();

            $dbHLBlockRow = $strEntityDataClass::getList( array( "filter" => array( "UF_XML_ID" => $arHlElemProp["VALUE"] ) ) );

            while( $arHLBlockRow = $dbHLBlockRow->fetch() ){
                foreach( $arEntityHLFieldsList as $entityHLFieldsListIndex => $arEntityHLFields ){
                    if( $arEntityHLFields["USER_TYPE_ID"] == "file" ){
                        $arEntityHLFieldsList[$entityHLFieldsListIndex]["VALUE"] = CFile::GetPath( $arHLBlockRow[$entityHLFieldsListIndex] );
                    }
                    else{
                        $arEntityHLFieldsList[$entityHLFieldsListIndex]["VALUE"] = $arHLBlockRow[$entityHLFieldsListIndex];
                    }
                }
            }
        }

        return $arEntityHLFieldsList;
    }

    // get product fields and properties used in template and conditions
    private function GetElementProperties( $arElement ){
        global $DB, $USER;

        if( !is_object( $USER ) )
            $USER = new CUser();

        $arItem = $arElement->GetFields();
        foreach( $arItem as $itemElementIndex => $itemElementValue ){
            if( isset( $arItem["~".$itemElementIndex] ) ){
                $arItem[$itemElementIndex] = $arItem["~".$itemElementIndex];
            }
        }

        if( in_array( "DETAIL_PICTURE", $this->useFields ) ){
            $arItem["DETAIL_PICTURE"] = CFile::GetPath($arItem["DETAIL_PICTURE"]);
        }
        if( in_array( "PREVIEW_PICTURE", $this->useFields ) ){
            $arItem["PREVIEW_PICTURE"] = CFile::GetPath( $arItem["PREVIEW_PICTURE"] );
        }

        foreach( $arItem as $key => &$value ){
            if( in_array( $key, $this->dateFields ) ){
                $value = date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), strtotime( $value ) );
            }
        }

        $arItem["SECTION_ID"] = array();
        $arItem["IBLOCK_SECTION_NAME"] = array();

        $dbSomeSections = CIBlockElement::GetElementGroups( $arItem["ID"], true );
        while( $arSection = $dbSomeSections->Fetch() ){
            if( in_array( "IBLOCK_SECTION_NAME", $this->useFields ) ){
                $arItem["IBLOCK_SECTION_NAME"] = $arSection["NAME"];
            }
            $arItem["SECTION_ID"][] = $arSection["ID"];
        }

        $arItem["SECTION_PARENT_ID"] = array();
        $dbParentSection = CIBlockSection::GetNavChain( false, $arItem["IBLOCK_SECTION_ID"] );
        while( $arParentSection = $dbParentSection->GetNext() ){
            $arItem["SECTION_PARENT_ID"][] = $arParentSection["ID"];
        }

        $arSectionFilter = array(
            "IBLOCK_ID" => $arItem["IBLOCK_ID"],
            "ID" => $arItem["IBLOCK_SECTION_ID"],
        );

        $dbSectionList = CIBlockSection::GetList(
            array(),
            $arSectionFilter,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "IBLOCK_SECTION_ID",
                "NAME",
                "UF_*",
            )
        );

        $arSectionUserFields = CAcritGooglemerchantTools::GetIblockUserFields( $arItem["IBLOCK_ID"] );
        if( $arSectionList = $dbSectionList->GetNext() ){
            if( ( $arSectionList = $dbSectionList->GetNext() ) && is_array( $arSectionUserFields ) && !empty( $arSectionUserFields ) ){
                if( in_array( $arSectionUserFieldsItem["FIELD_NAME"], $this->useFields ) ){
                    $arItem[$arSectionUserFieldsItem["FIELD_NAME"]] = $arSectionList[$arSectionUserFieldsItem["FIELD_NAME"]];
                    $value = $arSectionList[$arSectionUserFieldsItem["FIELD_NAME"]];
                    if( $this->GetResolveProperties( $arSectionUserFieldsItem, $arSectionUserFieldsItem["FIELD_NAME"], "FIELDS", $value ) ){
                        $arItem[$arSectionUserFieldsItem["FIELD_NAME"]] = $value;
                    }
                }
            }
        }

        if( count( $this->useProperties["ID"] ) ){
            $arProperties = CAcritGooglemerchantTools::GetProperties( $arItem, array( "ID" => $this->useProperties["ID"] ) );
            foreach( $this->useProperties["ID"] as $usePropID ){
                if( !isset( $arProperties[$usePropID] ) ){
                    $arItem["PROPERTY_{$usePropID}_VALUE"] = array();
                }
            }

            foreach( $arProperties as $property ){
                if( $property["USER_TYPE"] == "DateTime" ){
                    $property["DISPLAY_VALUE"] = date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), strtotime( $property["VALUE"] ) );
                }
                elseif( $property["PROPERTY_TYPE"] == "E" ){
                    $property["ORIGINAL_VALUE"] = array();
                    if( !empty( $property["VALUE"] ) ){
                        $dbPropE = CIBlockElement::GetList(
                            array(),
                            array(
                                "ID" => $property["VALUE"]
                            ),
                            false,
                            false,
                            array( "ID", "NAME" )
                        );
                        while( $arPropE = $dbPropE->GetNext() ){
                            $property["DISPLAY_VALUE"][] = $arPropE["NAME"];
                            $property["ORIGINAL_VALUE"][] = $arPropE["ID"];
                        }
                    }
                }
                elseif( $this->GetResolveProperties( $property, $property["ID"], "PROPERTIES" ) ){
                }
                else{
                    $property = CIBlockFormatProperties::GetDisplayValue( $arItem, $property, "acrit_googlemerchant_event" );
                    if( empty( $property["VALUE_ENUM_ID"] ) ){
                        if( !is_array( $property["DISPLAY_VALUE"] ) )
                            $property["ORIGINAL_VALUE"] = array( $property["DISPLAY_VALUE"] );
                        else
                            $property["ORIGINAL_VALUE"] = $property["DISPLAY_VALUE"];
                    }
                    else{
                        if( !is_array( $property["VALUE_ENUM_ID"] ) )
                            $property["ORIGINAL_VALUE"] = array( $property["VALUE_ENUM_ID"] );
                        else
                            $property["ORIGINAL_VALUE"] = $property["VALUE_ENUM_ID"];
                    }
                }
                if( $property["PROPERTY_TYPE"] == "F" ){
                    $property["DISPLAY_VALUE"] = array();
                    if( count( $property["ORIGINAL_VALUE"] ) > 1 ){
                        if( is_array( $property["FILE_VALUE"] ) && !empty( $property["FILE_VALUE"] ) ){
                            foreach( $property["FILE_VALUE"] as $file ){
                                $property["DISPLAY_VALUE"][] = $file["SRC"];
                            }
                        }
                        elseif( is_array( $property["VALUE"] ) && !empty( $property["VALUE"] ) ){
                            foreach( $property["VALUE"] as $file ){
                                $property["DISPLAY_VALUE"][] = CFile::GetPath( $file );
                            }
                        }
                    }
                    else{
                        if( isset( $property["VALUE"] ) && !empty( $property["VALUE"] ) ){
                            $property["DISPLAY_VALUE"] = $property["FILE_VALUE"]["SRC"];
                        }
                        elseif( isset( $property["VALUE"] ) && !empty( $property["VALUE"] ) ){
                            $property["DISPLAY_VALUE"] = CFile::GetPath( $property["VALUE"] );
                        }
                    }
                }
                $arItem["PROPERTY_{$property["ID"]}_DISPLAY_VALUE"] = $property["DISPLAY_VALUE"];
                $arItem["PROPERTY_{$property["CODE"]}_DISPLAY_VALUE"] = $arItem["PROPERTY_{$property["ID"]}_VALUE"];
                $arItem["PROPERTY_{$property["ID"]}_VALUE"] = $property["ORIGINAL_VALUE"];
                $arItem["PROPERTY_{$property["CODE"]}_VALUE"] = $arItem["PROPERTY_{$property["ID"]}_VALUE"];
            }
        }
        if( $this->catalogIncluded ){
            $arProduct = CCatalogProduct::GetByID( $arItem["ID"] );

            $arItem["CATALOG_QUANTITY"] = $arProduct["QUANTITY"];
            $arItem["CATALOG_QUANTITY_RESERVED"] = $arProduct["QUANTITY_RESERVED"];
            $arItem["CATALOG_WEIGHT"] = $arProduct["WEIGHT"];
            $arItem["CATALOG_WIDTH"] = $arProduct["WIDTH"];
            $arItem["CATALOG_LENGTH"] = $arProduct["LENGTH"];
            $arItem["CATALOG_HEIGHT"] = $arProduct["HEIGHT"];

            $dbPrices = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $arItem["ID"]
                )
            );

            while( $arPrice = $dbPrices->Fetch() ){
                if( ( ( intval( $arPrice["QUANTITY_FROM"] ) > 0 ) || ( intval( $arPrice["QUANTITY_TO"] ) > 0 ) )
                    && ( intval( $arPrice["QUANTITY_FROM"] ) !== 1 )
                ){
                    continue;
                }

                if( in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_WD", $this->usePrices ) ||
                    in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_D", $this->usePrices ) ){
                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arPrice["ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );
                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                        $arPrice["PRICE"],
                        $arPrice["CURRENCY"],
                        $arDiscounts
                    );
                    $discount = $arPrice["PRICE"] - $discountPrice;
                }
                else{
                    $discountPrice = $arPrice["PRICE"];
                    $discount = 0;
                }

                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}"] = $arPrice["PRICE"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_ID"] = $arPrice["ID"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD_ID"] = $arPrice["ID"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_PRICEID"] = $arPrice["CATALOG_GROUP_ID"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD_PRICEID"] = $arPrice["CATALOG_GROUP_ID"];
                //$arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD"] = $discountPrice;
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD"] = $arPrice["PRICE"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_D"] = $discount;
                $arItem["CATALOG_PRICE{$arPrice["CATALOG_GROUP_ID"]}"] = $arPrice["PRICE"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD_CURRENCY"] = $arPrice["CURRENCY"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_CURRENCY"] = $arPrice["CURRENCY"];
            }

            if( !isset( $arItem["CATALOG_PRICE_{$this->basePriceId}"] ) && ( $this->profile["USE_AUTOPRICE"] == "Y" ) ){
                if( $arMinimalPrice = CCatalogProduct::GetOptimalPrice( $arItem["ID"], 1, array( 2 ), "N", array(), $this->profile["LID"], array() ) ){
                    $checkedPriceId = $arMinimalPrice["PRICE"]["CATALOG_GROUP_ID"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}"] = $arItem["CATALOG_PRICE_{$checkedPriceId}"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_ID"] = $arMinimalPrice["ID"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_WD_ID"] = $arMinimalPrice["ID"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_PRICEID"] = $this->basePriceId;
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_WD_PRICEID"] = $this->basePriceId;
                    //$arItem["CATALOG_PRICE_{$this->basePriceId}_WD"] = $arItem["CATALOG_PRICE_{$checkedPriceId}_WD"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_WD"] = $arItem["CATALOG_PRICE_{$checkedPriceId}"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_D"] = $arItem["CATALOG_PRICE_{$checkedPriceId}_D"];
                    $arItem["CATALOG_PRICE{$this->basePriceId}"] = $arItem["CATALOG_PRICE{$checkedPriceId}"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_WD_CURRENCY"] = $arItem["CATALOG_PRICE_{$checkedPriceId}_CURRENCY"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_CURRENCY"] = $arItem["CATALOG_PRICE_{$checkedPriceId}_CURRENCY"];
                }
            }

            if( in_array( "PURCHASING_PRICE", $this->usePrices ) ){
                $arItem["CATALOG_PURCHASING_PRICE"] = $arProduct["PURCHASING_PRICE"];
                $arItem["CATALOG_PURCHASING_PRICE_CURRENCY"] = $arProduct["PURCHASING_CURRENCY"];
            }

            $dbStoreProduct = CCatalogStoreProduct::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $arProduct["ID"]
                ),
                false,
                false,
                array()
            );
            while( $arStore = $dbStoreProduct->Fetch() ){
                $arItem["CATALOG_STORE_AMOUNT_".$arStore["STORE_ID"]] = $arStore["AMOUNT"];
            }

            $dbBarCode = CCatalogStoreBarCode::getList(
                array(),
                array(
                    "PRODUCT_ID" => $arProduct["ID"]
                ),
                false,
                false,
                array()
            );
            if( $arBarCode = $dbBarCode->Fetch() ){
                $arItem["CATALOG_BARCODE"] = $arBarCode["BARCODE"];
            }
        }
        unset( $arProperties, $arProduct, $dbPrices, $arPrice );

        return $arItem;
    }

    protected function GetResolveProperties( &$item, $id, $type, &$value = "" ){
        if( ( $this->xmlCode === false ) || !isset( $this->useResolve[$this->xmlCode][$type][$id] ) ) return false;
        $resolve = $this->useResolve[$this->xmlCode][$type][$id];

        switch( $type ){
            case "PROPERTIES":
                if( ( $item["PROPERTY_TYPE"] == "S" ) && ( $item["USER_TYPE"] == "UserID" ) ){
                    $rsUser = CUser::GetByID( $item["VALUE"] );
                    $arUser = $rsUser->Fetch();
                    if( array_key_exists( $resolve, $arUser ) ){
                        $item["VALUE"] = $arUser[$resolve];
                        $item["~VALUE"] = $arUser[$resolve];
                        $item["DISPLAY_VALUE"] = $arUser[$resolve];
                        $item["ORIGINAL_VALUE"] = $arUser[$resolve];
                    }
                    return true;
                }
                break;
        }
    }

    protected function AddResolve(){
        foreach( $this->profile["XMLDATA"] as $xmlCode => $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] )
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] )
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) ){

                $fieldValue = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                $arValue = explode( "-", $fieldValue );
                switch( count( $arValue ) ){
                    case 1:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) > 0 ){
                            $this->useResolve[$xmlCode]["FIELDS"][$arValue[0]] = $field["RESOLVE"];
                        }
                        break;
                    case 2:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) ){
                            $this->useResolve[$xmlCode]["PRICES"][$arValue[1]] = $field["RESOLVE"];
                        }
                        break;
                    case 3:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) ){
                            $this->useResolve[$xmlCode]["PROPERTIES"][$arValue[2]] = $field["RESOLVE"];
                        }
                        break;
                }
            }
        }
    }

    public function SetCronPage( $cronpage ){
        $this->cronpage = $cronpage;
    }

    public function SetProcessEnd( $fileExport ){
        global $ProcessEnd;
        $ProcessEnd = true;
    }

    public function SetProcessStart( $fileExport ){
        if( false === $fileExport ) return;
    }
}
?>