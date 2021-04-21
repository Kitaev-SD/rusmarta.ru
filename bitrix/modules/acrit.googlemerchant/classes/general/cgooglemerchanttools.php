<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog;

\Bitrix\Main\Loader::includeModule( "acrit.googlemerchant" );

Loc::loadMessages( __FILE__ );

class CAcritGooglemerchantTools{
    protected static $arSectionTreeCache = array();

    public function GetHttpHost(){
        $arHttpHost = explode( ":", $_SERVER["HTTP_HOST"] );
        return $arHttpHost[0];
    }

    public function RoundNumber( $number, $precision, $mode, $precision_default = false ){
        switch( $mode ){
            case "UP":
                $mode = PHP_ROUND_HALF_UP;
                break;
            case "DOWN":
                $mode = PHP_ROUND_HALF_DOWN;
                break;
            case "EVEN":
                $mode = PHP_ROUND_HALF_EVEN;
                break;
            case "ODD":
                $mode = PHP_ROUND_HALF_ODD;
                break;
            default:
                $mode = PHP_ROUND_HALF_UP;
                break;
        }

        if( !is_numeric( $number ) && !is_float( $number ) ){
            return $number;
        }

        if( is_numeric( $precision ) ){
            return round( $number, abs( $precision ), $mode );
        }
        elseif( $precision_default !== false ){
            return round( $number, abs( $precision_default ), $mode );
        }

        return $number;
    }

    public function BitrixRoundNumber( $priceValue, $priceCode ){
        \Bitrix\Main\Loader::includeModule( "catalog" );

        $resultPrice = false;
        $arPriceCode = explode( "_", $priceCode );
        $arBitrixRoundRules = \Bitrix\Catalog\Product\Price::getRoundRules( $arPriceCode[1] );
        $resultPrice = Catalog\Product\Price::roundValue( $priceValue, $arBitrixRoundRules[0]["ROUND_PRECISION"], $arBitrixRoundRules[0]["ROUND_TYPE"] );

        return $resultPrice;
    }

    private function ArrayMultiply( &$arResult, $arTuple, $arTemp = array() ){
        if( $arTuple ){
            reset( $arTuple );
            list( $key, $head ) = each( $arTuple );
            unset( $arTuple[$key] );
            $arTemp[$key] = false;
            if( is_array( $head ) ){
                if( empty( $head ) ){
                    if( empty( $arTuple ) )
                        $arResult[] = $arTemp;
                    else
                        self::ArrayMultiply( $arResult, $arTuple, $arTemp );
                }
                else{
                    foreach( $head as $value ){
                        $arTemp[$key] = $value;
                        if( empty( $arTuple ) )
                            $arResult[] = $arTemp;
                        else
                            self::ArrayMultiply( $arResult, $arTuple, $arTemp );
                    }
                }
            }
            else{
                $arTemp[$key] = $head;
                if( empty( $arTuple ) )
                    $arResult[] = $arTemp;
                else
                    self::ArrayMultiply( $arResult, $arTuple, $arTemp );
            }
        }
        else{
            $arResult[] = $arTemp;
        }
    }

    public function ExportArrayMultiply( &$arResult, $arTuple, $arTemp = array() ){
        if( count( $arTuple ) == 0 ){
            $arResult[] = $arTemp;
        }
        else{
            $head = array_shift( $arTuple );
            $arTemp[] = false;
            if( is_array( $head ) ){
                if( empty( $head ) ){
                    $arTemp[count( $arTemp ) - 1] = "";
                    self::ArrayMultiply( $arResult, $arTuple, $arTemp );
                }
                else{
                    foreach( $head as $key => $value ){
                        $arTemp[count( $arTemp ) - 1] = $value;
                        self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
                    }
                }
            }
            else{
                $arTemp[count( $arTemp ) - 1] = $head;
                self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
            }
        }
    }

    /*public function ExportArrayMultiply( &$arResult, $arTuple, $arTemp = array() ){
        if( empty( $arTuple ) ){
            $arResult[] = $arTemp;
        }
        else{
            $head = array_shift( $arTuple );
            $arTemp[] = false;
            if( is_array( $head ) ){
                if( empty( $head ) ){
                    $arTemp[count( $arTemp ) - 1] = "";
                    self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
                }
                else{
                    foreach( $head as &$value ){
                        $arTemp[count( $arTemp ) - 1] = $value;
                        self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
                    }
                    if( isset( $value ) ){
                        unset( $value );
                    }
                }
            }
            else{
                $arTemp[count( $arTemp ) - 1] = $head;
                self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
            }
        }
    }*/

    public function GetYandexDateTime( $dateTime ){
        global $DB;
        $resultTime = false;

        $localTime = new DateTime();
        $dateTimeZoneDiff = $localTime->getOffset() / 3600;

        $dateTimeZone = ( ( intval( $dateTimeZoneDiff ) > 0 ) ? "+" : "-" ).date( "H:i", mktime( $dateTimeZoneDiff, 0, 0, 0, 0, 0 ) );

        $dateTimeValue = MakeTimeStamp( $dateTime );
        $dateTimeFormattedValue = date( "Y-m-d", $dateTimeValue )."T".date( "H:i:s", $dateTimeValue );

        $resultTime = $dateTimeFormattedValue.$dateTimeZone;

        return $resultTime;
    }

    public function GetIblockUserFields( $iblockId ){
        $result = false;
        $dbSectionUserFields = CUserTypeEntity::GetList(
            array(),
            array(
                "ENTITY_ID" => "IBLOCK_".$iblockId."_SECTION",
                "LANG" => LANGUAGE_ID
            )
        );

        while( $arSectionUserFields = $dbSectionUserFields->Fetch() ){
            if( !$result ) $result = array();
            $result[] = $arSectionUserFields;
        }

        return $result;
    }

    public static function GetSectionTree( $IBLOCK_ID, $SECTION_ID, $arSelect = array() ){
        global $DB, $DBHost, $DBName, $DBLogin, $DBPassword;

        $IBLOCK_ID = (int)$IBLOCK_ID;

        $arFields = array(
            "ID" => "BS.ID",
            "CODE" => "BS.CODE",
            "XML_ID" => "BS.XML_ID",
            "EXTERNAL_ID" => "BS.XML_ID",
            "IBLOCK_ID" => "BS.IBLOCK_ID",
            "IBLOCK_SECTION_ID" => "BS.IBLOCK_SECTION_ID",
            "SORT" => "BS.SORT",
            "NAME" => "BS.NAME",
            "ACTIVE" => "BS.ACTIVE",
            "GLOBAL_ACTIVE" => "BS.GLOBAL_ACTIVE",
            "PICTURE" => "BS.PICTURE",
            "DESCRIPTION" => "BS.DESCRIPTION",
            "DESCRIPTION_TYPE" => "BS.DESCRIPTION_TYPE",
            "LEFT_MARGIN" => "BS.LEFT_MARGIN",
            "RIGHT_MARGIN" => "BS.RIGHT_MARGIN",
            "DEPTH_LEVEL" => "BS.DEPTH_LEVEL",
            "SEARCHABLE_CONTENT" => "BS.SEARCHABLE_CONTENT",
            "MODIFIED_BY" => "BS.MODIFIED_BY",
            "CREATED_BY" => "BS.CREATED_BY",
            "DETAIL_PICTURE" => "BS.DETAIL_PICTURE",
            "TMP_ID" => "BS.TMP_ID",

            "LIST_PAGE_URL" => "B.LIST_PAGE_URL",
            "SECTION_PAGE_URL" => "B.SECTION_PAGE_URL",
            "IBLOCK_TYPE_ID" => "B.IBLOCK_TYPE_ID",
            "IBLOCK_CODE" => "B.CODE",
            "IBLOCK_EXTERNAL_ID" => "B.XML_ID",
            "SOCNET_GROUP_ID" => "BS.SOCNET_GROUP_ID",
        );

        $arSqlSelect = array();
        foreach( $arSelect as $field ){
            $field = strtoupper( $field );
            if( isset( $arFields[$field] ) ){
                $arSqlSelect[$field] = $arFields[$field]." AS ".$field;
            }
        }

        if( isset( $arSqlSelect["DESCRIPTION"] ) ){
            $arSqlSelect["DESCRIPTION_TYPE"] = $arFields["DESCRIPTION_TYPE"]." AS DESCRIPTION_TYPE";
        }

        if( isset( $arSqlSelect["LIST_PAGE_URL"] ) || isset( $arSqlSelect["SECTION_PAGE_URL"] ) ){
            $arSqlSelect["ID"] = $arFields["ID"]." AS ID";
            $arSqlSelect["CODE"] = $arFields["CODE"]." AS CODE";
            $arSqlSelect["EXTERNAL_ID"] = $arFields["EXTERNAL_ID"]." AS EXTERNAL_ID";
            $arSqlSelect["IBLOCK_TYPE_ID"] = $arFields["IBLOCK_TYPE_ID"]." AS IBLOCK_TYPE_ID";
            $arSqlSelect["IBLOCK_ID"] = $arFields["IBLOCK_ID"]." AS IBLOCK_ID";
            $arSqlSelect["IBLOCK_CODE"] = $arFields["IBLOCK_CODE"]." AS IBLOCK_CODE";
            $arSqlSelect["IBLOCK_EXTERNAL_ID"] = $arFields["IBLOCK_EXTERNAL_ID"]." AS IBLOCK_EXTERNAL_ID";
            $arSqlSelect["GLOBAL_ACTIVE"] = $arFields["GLOBAL_ACTIVE"]." AS GLOBAL_ACTIVE";
        }

        if( !empty( $arSelect ) ){
            $field = "IBLOCK_SECTION_ID";
            $arSqlSelect[$field] = $arFields[$field]." AS ".$field;
            $strSelect = implode( ", ", $arSqlSelect );
        }
        else{
            $strSelect = "
                BS.*,
                B.LIST_PAGE_URL,
                B.SECTION_PAGE_URL,
                B.IBLOCK_TYPE_ID,
                B.CODE as IBLOCK_CODE,
                B.XML_ID as IBLOCK_EXTERNAL_ID,
                BS.XML_ID as EXTERNAL_ID
            ";
        }

        $key = md5( $strSelect );
        if( !isset( self::$arSectionTreeCache[$key] ) ){
            self::$arSectionTreeCache[$key] = array();
        }

        $sectionPath = array();
        do{
            $SECTION_ID = (int)$SECTION_ID;

            if( !isset( self::$arSectionTreeCache[$key][$SECTION_ID] ) ){
                $queryBox = "SELECT
                                    ".$strSelect."
                                FROM
                                    b_iblock_section BS
                                    INNER JOIN b_iblock B ON B.ID = BS.IBLOCK_ID
                                WHERE BS.ID=".$SECTION_ID."
                                    ".($IBLOCK_ID > 0 ? "AND BS.IBLOCK_ID=".$IBLOCK_ID : "")."
                            ";

                $rsSection = $DB->Query( $queryBox, true );
                if( $DB->GetErrorMessage() != "" ){
                    $DB->Disconnect();
                    if( $DB->Connect( $DBHost, $DBName, $DBLogin, $DBPassword ) && $DB->DoConnect() ){
                        $rsSection = $DB->Query( $queryBox, true );
                    }
                }

                self::$arSectionTreeCache[$key][$SECTION_ID] = $rsSection->Fetch();
            }

            if( self::$arSectionTreeCache[$key][$SECTION_ID] ){
                $sectionPath[] = self::$arSectionTreeCache[$key][$SECTION_ID];
                $SECTION_ID = self::$arSectionTreeCache[$key][$SECTION_ID]["IBLOCK_SECTION_ID"];
            }
            else{
                $SECTION_ID = 0;
            }
        } while( $SECTION_ID > 0 );

        $res = new CDBResult;
        $res->InitFromArray( array_reverse( $sectionPath ) );
        $res = new CIBlockResult( $res );
        $res->bIBlockSection = true;
        return $res;
    }

    public function CheckCondition( $arItem, $code ){
        unset( $GLOBALS["CHECK_COND"] );
        if( is_array( $arItem["SECTION_ID"] ) && is_array( $arItem["SECTION_PARENT_ID"] ) )
            $arItem["SECTION_ID"] = array_merge( $arItem["SECTION_ID"], $arItem["SECTION_PARENT_ID"] );

        $GLOBALS["CHECK_COND"] = $arItem;

        return eval( "return $code;" );
    }

    public function GetStringCharset( $str ){
        $resEncoding = "cp1251";

        if( preg_match( "#.#u", $str ) ){
            $resEncoding = "utf8";
        }

        return $resEncoding;
    }

    public function GetSectionNavChain( $sectionId ){
        static $arResult = null;
        if( !is_null( $arResult ) )
            return $arResult;

        $arResult = array();

        $dbSectionList = CIBlockSection::GetNavChain(
            false,
            $sectionId
        );

        while( $arSection = $dbSectionList->GetNext() ){
            $arResult[] = $arSection["ID"];
        }

        return $arResult;
    }

    public function NormalisePath( $path ){
        $arPathParts = explode( "/", $path );
        $arSafe = array();
        foreach( $arPathParts as $idx => $pathPart ){
            if( empty( $pathPart ) || ( "." == $pathPart ) ){
                continue;
            }
            elseif( ".." == $pathPart ){
                array_pop( $arSafe );
                continue;
            }
            else{
                $arSafe[] = $pathPart;
            }
        }

        $path = "/".implode( "/", $arSafe );
        return $path;
    }

    public function GetSiteDocumentRoot( $siteId ){
        $result = false;

        $dbSite = CSite::GetByID( $siteId );
        if( ( $arSite = $dbSite->Fetch() ) && ( strlen( $arSite["DOC_ROOT"] ) > 0 ) ){
            $result = $arSite["DOC_ROOT"];
        }

        return $result;
    }

    public function GetFilePath( $fileId ){
        $result = false;

        global $DB;
        $strSql = "SELECT f.*,".$DB->DateToCharFunction( "f.TIMESTAMP_X" )." as TIMESTAMP_X FROM b_file f WHERE f.ID=".$fileId;
        $dbFile = $DB->Query( $strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__ );
        $result = CFile::GetFileSRC( $dbFile->Fetch() );

        return $result;
    }

    public function ArrayValidate( $arData ){
        $result = false;

        if( isset( $arData ) && is_array( $arData ) && !empty( $arData ) ){
            $result = true;
        }

        return $result;
    }

    public function GetProcessPriceId( $arProfile ){
        $result = false;
        if( self::ArrayValidate( $arProfile["XMLDATA"] ) ){
            foreach( $arProfile["XMLDATA"] as $arXmlItem ){
                if( $arXmlItem["CODE"] == "PRICE" ){
                    preg_match( "#[\d]+#", $arXmlItem["VALUE"], $arPriceId );
                    if( isset( $arPriceId[0] ) && !empty( $arPriceId[0] ) ){
                        $result = $arPriceId[0];
                    }
                }
            }
        }

        return $result;
    }

    public function GetSections( $arProfile ){
        $arSessionData = AcritGooglemerchantSession::GetAllSession( $arProfile["ID"] );
        $sessionData = array();
        if( !empty( $arSessionData ) ){
            $sessionData = $arSessionData[0];
            if( !is_array( $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"] ) )
                $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"] = array();

            unset( $arSessionData[0] );
            foreach( $arSessionData as $sData ){
                if( is_array( $sData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"] ) ){
                    $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"] = array_merge(
                        $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"],
                        $sData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"]
                    );
                }
            }
        }
        return array_unique( $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"] );
    }

    public function GetCurrencies( $arProfile ){
        $arSessionData = AcritGooglemerchantSession::GetAllSession( $arProfile["ID"] );
        $sessionData = array();
        if( !empty( $arSessionData ) ){
            $sessionData = $arSessionData[0];
            if( !is_array( $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"] ) )
                $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"] = array();

            unset( $arSessionData[0] );
            foreach( $arSessionData as $sData ){
                if( is_array( $sData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"] ) ){
                    $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"] = array_merge(
                        $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"],
                        $sData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"]
                    );
                }
            }
        }
        return array_unique( array_filter( $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"] ) );
    }

    public function SaveSections( $arProfile, $sections ){
        if( is_array( $sections ) ){
            $sessionData = AcritGooglemerchantSession::GetSession( $arProfile["ID"] );

            if( !is_array( $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"] ) )
                $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"] = array();

            $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"] = array_merge(
                $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CATEGORY"],
                $sections
            );
            AcritGooglemerchantSession::SetSession( $arProfile["ID"], $sessionData );
        }
    }

    public function SaveCurrencies( $arProfile, $currencies ){
        if( is_array( $currencies ) ){
            $sessionData = AcritGooglemerchantSession::GetSession( $arProfile["ID"] );
            if( !is_array( $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"] ) )
                $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"] = array();

            $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"] = array_merge(
                $sessionData["GOOGLEMERCHANT"][$arProfile["ID"]]["CURRENCY"],
                $currencies
            );
            AcritGooglemerchantSession::SetSession( $arProfile["ID"], $sessionData );
        }
    }

    public function GetProperties( $arItem, $arFilter ){
        $props = CIBlockElement::GetProperty( $arItem["IBLOCK_ID"], $arItem["ID"], array(), $arFilter );

        $arAllProps = array();
        while( $arProp = $props->Fetch() ){
            if( strlen( trim( $arProp["CODE"] ) ) > 0 )
                $PIND = $arProp["CODE"];
            else
                $PIND = $arProp["ID"];

            $arProp["ORIGINAL_VALUE"] = $arProp["VALUE"];

            if( $arProp["PROPERTY_TYPE"] == "L" ){
                if( $arProp["MULTIPLE"] != "Y" )
                    $arProp["ORIGINAL_VALUE"] = array( $arProp["ORIGINAL_VALUE"] );
                $arProp["VALUE_ENUM_ID"] = $arProp["VALUE"];
                $arProp["VALUE"] = $arProp["VALUE_ENUM"];
            }

            if( is_array( $arProp["VALUE"] ) || ( strlen( $arProp["VALUE"] ) > 0 ) ){
                $arProp["~VALUE"] = $arProp["VALUE"];
                if( is_array( $arProp["VALUE"] ) || preg_match( "/[;&<>\"]/", $arProp["VALUE"] ) )
                    $arProp["VALUE"] = htmlspecialcharsex( $arProp["VALUE"] );
                $arProp["~DESCRIPTION"] = $arProp["DESCRIPTION"];
                if( preg_match( "/[;&<>\"]/", $arProp["DESCRIPTION"] ) )
                    $arProp["DESCRIPTION"] = htmlspecialcharsex( $arProp["DESCRIPTION"] );
            }
            else{
                $arProp["VALUE"] = $arProp["~VALUE"] = "";
                $arProp["DESCRIPTION"] = $arProp["~DESCRIPTION"] = "";
            }

            if( $arProp["MULTIPLE"] == "Y" ){
                if( array_key_exists( $PIND, $arAllProps ) ){
                    $arTemp = &$arAllProps[$PIND];
                    if( $arProp["VALUE"] !== "" ){
                        if( is_array( $arTemp["VALUE"] ) ){
                            $arTemp["ORIGINAL_VALUE"][] = $arProp["ORIGINAL_VALUE"];
                            $arTemp["VALUE"][] = $arProp["VALUE"];
                            $arTemp["~VALUE"][] = $arProp["~VALUE"];
                            $arTemp["DESCRIPTION"][] = $arProp["DESCRIPTION"];
                            $arTemp["~DESCRIPTION"][] = $arProp["~DESCRIPTION"];
                            $arTemp["PROPERTY_VALUE_ID"][] = $arProp["PROPERTY_VALUE_ID"];
                            if( $arProp["PROPERTY_TYPE"] == "L" ){
                                $arTemp["VALUE_ENUM_ID"][] = $arProp["VALUE_ENUM_ID"];
                                $arTemp["VALUE_ENUM"][] = $arProp["VALUE_ENUM"];
                                $arTemp["VALUE_XML_ID"][] = $arProp["VALUE_XML_ID"];
                            }
                        }
                        else{
                            $arTemp["ORIGINAL_VALUE"] = array( $arProp["ORIGINAL_VALUE"] );
                            $arTemp["VALUE"] = array( $arProp["VALUE"] );
                            $arTemp["~VALUE"] = array( $arProp["~VALUE"] );
                            $arTemp["DESCRIPTION"] = array( $arProp["DESCRIPTION"] );
                            $arTemp["~DESCRIPTION"] = array( $arProp["~DESCRIPTION"] );
                            $arTemp["PROPERTY_VALUE_ID"] = array( $arProp["PROPERTY_VALUE_ID"] );
                            if( $arProp["PROPERTY_TYPE"] == "L" ){
                                $arTemp["VALUE_ENUM_ID"] = array( $arProp["VALUE_ENUM_ID"] );
                                $arTemp["VALUE_ENUM"] = array( $arProp["VALUE_ENUM"] );
                                $arTemp["VALUE_XML_ID"] = array( $arProp["VALUE_XML_ID"] );
                                $arTemp["VALUE_SORT"] = array( $arProp["VALUE_SORT"] );
                                $arTemp["ORIGINAL_VALUE"] = array( $arProp["ORIGINAL_VALUE"] );
                            }
                        }
                    }
                }
                else{
                    $arProp["~NAME"] = $arProp["NAME"];
                    if( preg_match( "/[;&<>\"]/", $arProp["NAME"] ) )
                        $arProp["NAME"] = htmlspecialcharsex( $arProp["NAME"] );

                    $arProp["~DEFAULT_VALUE"] = $arProp["DEFAULT_VALUE"];

                    if( is_array( $arProp["DEFAULT_VALUE"] ) || preg_match( "/[;&<>\"]/", $arProp["DEFAULT_VALUE"] ) )
                        $arProp["DEFAULT_VALUE"] = htmlspecialcharsex( $arProp["DEFAULT_VALUE"] );

                    if( $arProp["VALUE"] !== "" ){
                        $arProp["ORIGINAL_VALUE"] = array( $arProp["ORIGINAL_VALUE"] );
                        $arProp["VALUE"] = array( $arProp["VALUE"] );
                        $arProp["~VALUE"] = array( $arProp["~VALUE"] );
                        $arProp["DESCRIPTION"] = array( $arProp["DESCRIPTION"] );
                        $arProp["~DESCRIPTION"] = array( $arProp["~DESCRIPTION"] );
                        $arProp["PROPERTY_VALUE_ID"] = array( $arProp["PROPERTY_VALUE_ID"] );
                        if( $arProp["PROPERTY_TYPE"] == "L" ){
                            $arProp["VALUE_ENUM_ID"] = array( $arProp["VALUE_ENUM_ID"] );
                            $arProp["VALUE_ENUM"] = array( $arProp["VALUE_ENUM"] );
                            $arProp["VALUE_XML_ID"] = array( $arProp["VALUE_XML_ID"] );
                            $arProp["VALUE_SORT"] = array( $arProp["VALUE_SORT"] );
                        }
                    }
                    else{
                        $arProp["ORIGINAL_VALUE"] = false;
                        $arProp["VALUE"] = false;
                        $arProp["~VALUE"] = false;
                        $arProp["DESCRIPTION"] = false;
                        $arProp["~DESCRIPTION"] = false;
                        $arProp["PROPERTY_VALUE_ID"] = false;
                        if( $arProp["PROPERTY_TYPE"] == "L" ){
                            $arProp["VALUE_ENUM_ID"] = false;
                            $arProp["VALUE_ENUM"] = false;
                            $arProp["VALUE_XML_ID"] = false;
                            $arProp["VALUE_SORT"] = false;
                        }
                    }
                    $arAllProps[$PIND] = $arProp;
                }
            }
            else{
                $arProp["~NAME"] = $arProp["NAME"];

                if( preg_match( "/[;&<>\"]/", $arProp["NAME"] ) )
                    $arProp["NAME"] = htmlspecialcharsex( $arProp["NAME"] );

                $arProp["~DEFAULT_VALUE"] = $arProp["DEFAULT_VALUE"];

                if( is_array( $arProp["DEFAULT_VALUE"] ) || preg_match( "/[;&<>\"]/", $arProp["DEFAULT_VALUE"] ) )
                    $arProp["DEFAULT_VALUE"] = htmlspecialcharsex( $arProp["DEFAULT_VALUE"] );

                $arAllProps[$PIND] = $arProp;
            }
        }

        return $arAllProps;
    }

    public function GetSimpleData(){
        return GetMessage( "ACRIT_TOOLS_SOMEDATA" );
    }

    public function HasBadCronExports( $arProfile ){
        $result = false;

        $arPath = explode( "/", $arProfile["SETUP"]["URL_DATA_FILE"] );
        $exportPath = ( $arPath[1] == "acrit.googlemerchant" ) ? $_SERVER["DOCUMENT_ROOT"]."/upload/".$arPath[1]."/" : $_SERVER["DOCUMENT_ROOT"]."/".$arPath[1]."/";
        if( $handle = opendir( $exportPath ) ){
            while( false !== ( $file = readdir( $handle ) ) ){
                if( stripos( $file, $arPath[2].".part" ) !== false ){
                    $result = true;
                    break;
                }
            }
            closedir( $handle );
        }

        return $result;
    }

    public function ClearExportSession( $profileId ){
        AcritGooglemerchantSession::DeleteSession( $profileId, $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.googlemerchant/" );
    }

    public function ParseMultiproFormat( $arProcessValues, $arProfile, $fieldCode ){
        $arProcessValuesSelectedMultiprop = array();

        $arSelectedMultipropIndexes = array();
        $fieldMultipropFormat = trim( $arProfile["XMLDATA"][$fieldCode]["MULTIPROP_FORMAT"] );
        if( strlen( $fieldMultipropFormat ) > 0 ){
            $arFieldMultipropFormatParts = explode( ",", $fieldMultipropFormat );

            if( is_array( $arFieldMultipropFormatParts ) && !empty( $arFieldMultipropFormatParts ) ){
                foreach( $arFieldMultipropFormatParts as $arFieldMultipropFormatPartsItem ){
                    $arFieldMultipropFormatAtom = explode( "-", trim( $arFieldMultipropFormatPartsItem ) );
                    switch( count( $arFieldMultipropFormatAtom ) ){
                        case 2:
                            for( $multipropIndex = $arFieldMultipropFormatAtom[0]; $multipropIndex < ( $arFieldMultipropFormatAtom[1] + 1 ); $multipropIndex++ ){
                                $arSelectedMultipropIndexes[] = $multipropIndex;
                            }
                            break;
                        case 1:
                        default:
                            $arSelectedMultipropIndexes[] = intval( $arFieldMultipropFormatAtom[0] );
                            break;
                    }
                }
            }
        }

        foreach( $arSelectedMultipropIndexes as $selectedMultipropIndexesItem ){
            if( isset( $arProcessValues[$selectedMultipropIndexesItem] ) ){
                $arProcessValuesSelectedMultiprop[] = $arProcessValues[$selectedMultipropIndexesItem];
            }
        }

        return $arProcessValuesSelectedMultiprop;
    }

    public function CheckProfileDefaults( $arProfile ){
        $bNeedPayment = false;
        if( ( $arProfile["SETUP"]["FILE_TYPE"] == "csv" ) || ( $arProfile["SETUP"]["FILE_TYPE"] == "xls" ) ){
            $bNeedPayment = true;
        }
        else{
            $obProfileUtils = new CGooglemerchantProfile();
            $arProfileDefaults = $obProfileUtils->GetDefaults( $arProfile["TYPE"] );

            if(
                ( count( $arProfileDefaults["XMLDATA"] ) != count( $arProfile["XMLDATA"] ) )
                //|| ( strlen( trim( $arProfileDefaults["FORMAT"] ) ) != strlen( trim( $arProfile["FORMAT"] ) ) )
                //|| ( strlen( trim( $arProfileDefaults["OFFER_TEMPLATE"] ) ) != strlen( trim( $arProfile["OFFER_TEMPLATE"] ) ) )
                //|| ( strlen( trim( $arProfileDefaults["CATEGORY_TEMPLATE"] ) ) != strlen( trim( $arProfile["CATEGORY_TEMPLATE"] ) ) )
                //|| ( strlen( trim( $arProfileDefaults["CURRENCY_TEMPLATE"] ) ) != strlen( trim( $arProfile["CURRENCY_TEMPLATE"] ) ) )
            ){
                $bNeedPayment = true;
            }
            else{
                $arProfileTagKeys = array_keys( $arProfile["XMLDATA"] );
                $arProfileDefaultsTagKeys = array_keys( $arProfileDefaults["XMLDATA"] );

                $arKeysDiff = array_diff( ( is_array( $rProfileTagKeys ) ? $rProfileTagKeys : array() ), $arProfileDefaultsTagKeys );
                if( is_array( $arKeysDiff ) && !empty( $arKeysDiff ) ){
                    $bNeedPayment = true;
                }
            }
        }

        return $bNeedPayment;
    }

    public function IsFunctionEnabled( $functionName ){
        $functionName = strtolower( trim( $functionName ) );
        if( $functionName == "" ){
            return false;
        }

        $arDisabledFunctions = explode( ",", @ini_get( "disable_functions" ) );
        if( empty( $arDisabledFunctions ) ){
            $arDisabledFunctions = array();
        }
        else {
            $arDisabledFunctions = array_map( "trim", array_map( "strtolower", $arDisabledFunctions ) );
        }

        return ( function_exists( $functionName ) && is_callable( $functionName ) && !in_array( $functionName, $arDisabledFunctions ) );
    }

    public function GetPotentialPhpPaths(){
        $arPaths = false;

        if( self::IsFunctionEnabled( "exec" ) ){
            $sPaths = exec( "whereis php" );
            $arPaths = explode( " ", $sPaths );
            $arPaths[0] = exec( "which php" );

            $arPaths = array_map( "trim", $arPaths );
            $arPaths = array_unique( $arPaths );
        }

        return $arPaths;
    }
}

class CAcritGooglemerchantStringProcess{
    public function AcritTruncateText( $text, $lenght ){
        $truncatedString = $text;

        $arMbStringData = mb_get_info();

        if( ( strlen( $truncatedString ) > $lenght ) && is_array( $arMbStringData ) && !empty( $arMbStringData ) ){
            $truncatedString = rtrim( mb_substr( $truncatedString, 0, $lenght, mb_detect_encoding( $truncatedString, "auto" ) ) )."...";
        }

        return $truncatedString;
    }

    public function AcritArrayTrimFunc( &$value ){
        $value = trim( $value );
    }

    public function ConvertData( $sConvertField, $arConvertPatterns ){
        if( is_array( $arConvertPatterns ) && !empty( $arConvertPatterns ) ){
            foreach( $arConvertPatterns as $arConvertBlock ){
                $sConvertField = str_replace( $arConvertBlock[0], $arConvertBlock[1], $sConvertField );
            }
        }

        return $sConvertField;
    }

    public function AcritHtmlToTxt( $sConvertField ){
        $sConvertField = HTMLToTxt( $sConvertField );

        return $sConvertField;
    }

    public function HtmlEncodeCut( $convertField ){
        global $APPLICATION;

        if( !empty( $convertField ) ){
            if( is_array( $convertField ) ){
                foreach( $convertField as &$val ){
                    $templateValueCharset = CAcritGooglemerchantTools::GetStringCharset( $val );
                    if( $templateValueCharset == "cp1251" ){
                        $convertedTemplateValue = $APPLICATION->ConvertCharset( $val, "windows-1251", "UTF-8" );
                        $convertedTemplateValue = preg_replace( "/&(amp;)?(.+?);/", "", $convertedTemplateValue );
                        $convertedTemplateValue = preg_replace( "/&(amp;)?#\d+;/", "", $convertedTemplateValue );
                        $val = $APPLICATION->ConvertCharset( $convertedTemplateValue, "UTF-8", "windows-1251" );
                    }
                    else{
                        $val = preg_replace( "/&(amp;)?(.+?);/", "", $val );
                        $val = preg_replace( "/&(amp;)?#\d+;/", "", $val );
                    }
                }
            }
            else{
                $templateValueCharset = CAcritGooglemerchantTools::GetStringCharset( $convertField );
                if( $templateValueCharset == "cp1251" ){
                    $convertedTemplateValue = $APPLICATION->ConvertCharset( $convertField, "windows-1251", "UTF-8" );
                    $convertedTemplateValue = preg_replace( "/&(amp;)?(.+?);/", "", $convertedTemplateValue );
                    $convertedTemplateValue = preg_replace( "/&(amp;)?#\d+;/", "", $convertedTemplateValue );
                    $convertField = $APPLICATION->ConvertCharset( $convertedTemplateValue, "UTF-8", "windows-1251" );
                }
                else{
                    $convertField = preg_replace( "/&(amp;)?(.+?);/", "", $convertField );
                    $convertField = preg_replace( "/&(amp;)?#\d+;/", "", $convertField );
                }
            }
        }

        return $convertField;
    }

    public function HtmlEncode( $convertField ){
        if( !empty( $convertField ) ){
            if( is_array( $convertField ) ){
                foreach( $convertField as &$val ){
                    $val = htmlspecialcharsbx( $val );
                }
            }
            else{
                $convertField = htmlspecialcharsbx( $convertField );
            }
        }

        return $convertField;
    }

    public function UrlEncode( $convertField ){
        if( !empty( $convertField ) ){
            if( is_array( $convertField ) ){
                foreach( $convertField as &$val ){
                    $val = str_replace( array( " " ), array( "%20" ), $val );
                }
            }
            else{
               $convertField = str_replace( array( " " ), array( "%20" ), $convertField );
            }
        }

        return $convertField;
    }

    public function ConvertCase( $convertField ){
        if( is_array( $convertField ) ){
            foreach( $convertField as &$val ){
                $val = explode( ".", $val );
                foreach( $val as &$tmpStr ){
                    $tmpStr = strtolower( trim( $tmpStr ) );
                    $strWords = explode( " ", $tmpStr );
                    if( ( strlen( $strWords[0] ) > 0 ) && ( count( $strWords ) > 1 ) )
                        $strWords[0] = mb_convert_case( $strWords[0], MB_CASE_TITLE );
                    $tmpStr = implode( " ", $strWords );
                }
                $val = implode( ". ", $val );
            }
        }
        else{
            $arTmp = explode( ".", $convertField );

            foreach( $arTmp as &$tmpStr ){
                $tmpStr = ToLower( trim( $tmpStr ) );

                $strWords = explode( " ", $tmpStr );

                if( ( strlen( $strWords[0] ) > 0 ) && ( count( $strWords ) > 1 ) ){
                    $templateValueCharset = CAcritGooglemerchantTools::GetStringCharset( $convertField );

                    if( $templateValueCharset == "cp1251" ){
                        $strWords[0] = mb_convert_case( $strWords[0], MB_CASE_TITLE, "WINDOWS-1251" );
                    }
                    else{
                        $strWords[0] = mb_convert_case( $strWords[0], MB_CASE_TITLE );
                    }
                }

                $tmpStr = implode( " ", $strWords );
            }
            $convertField = implode( ". ", $arTmp );
        }

        return $convertField;
    }

    public function DeleteEmptyRow( $itemTemplate, &$templateValues, $arMatches, $match, $baseTagMatchId, $bDeleteEmpty = false, $bDeleteEmptyForse = false, $bDeleteEmptyRowForse = false ){
        if( $bDeleteEmptyRowForse ){
            if( $templateValues[$match] == "" ){
                preg_match_all( "/.*<[\w\d_-]+([^<>]*)>(.*#.+#.*)?<\/.+>/", $arMatches[0][$baseTagMatchId], $curTagData );

                if( ( stripos( $curTagData[1][0], $match ) !== false ) ||
                    ( stripos( $curTagData[2][0], $match ) !== false )
                ){
                    $itemTemplate = str_replace( $arMatches[0][$baseTagMatchId], "", $itemTemplate );
                }
                else{
                    $itemTemplate = str_replace( $match, "", $itemTemplate );
                }
            }
            elseif( is_array( $templateValues[$match] ) ){
                $replacementValue = array();
                for( $i = 0; $i < count( $templateValues[$match] ); $i++ ){
                    $newName = preg_replace( "/\#((.)+)\#/", "#$1_LISTVAL_ITEM_$i#", $arMatches[2][$baseTagMatchId] );
                    $replacementValue[] = str_replace( $arMatches[2][$baseTagMatchId], $newName, $arMatches[0][$baseTagMatchId] );
                    $templateValues[$newName] = $templateValues[$match][$i];
                }

                $itemTemplate = str_replace( $arMatches[0][$baseTagMatchId], implode( PHP_EOL, $replacementValue ), $itemTemplate );
            }
        }
        elseif( $bDeleteEmptyForse ){
            if( $templateValues[$match] == "" ){
                preg_match_all( "/.*<[\w\d_-]+[^<>]*>(.*#[\w\d_-]+:*[\w\d_-]+#.*)<\/.+>/", $arMatches[0][$baseTagMatchId], $curTagData );
                if( $match == $curTagData[1][0] ){
                    $itemTemplate = str_replace( $arMatches[0][$baseTagMatchId], "", $itemTemplate );
                }
                else{
                    $itemTemplate = str_replace( $match, "", $itemTemplate );
                }
            }
            elseif( is_array( $templateValues[$match] ) ){
                $replacementValue = array();
                for( $i = 0; $i < count( $templateValues[$match] ); $i++ ){
                    $newName = preg_replace( "/\#((.)+)\#/", "#$1_LISTVAL_ITEM_$i#", $arMatches[2][$baseTagMatchId] );
                    $replacementValue[] = str_replace( $arMatches[2][$baseTagMatchId], $newName, $arMatches[0][$baseTagMatchId] );
                    $templateValues[$newName] = $templateValues[$match][$i];
                }

                $itemTemplate = str_replace( $arMatches[0][$baseTagMatchId], implode( PHP_EOL, $replacementValue ), $itemTemplate );
            }
        }
        elseif( $bDeleteEmpty ){
            if( $templateValues[$match] == "" ){
                preg_match_all( "/.*<[\w\d_-]+>(.*#[\w\d_-]+:*[\w\d_-]+#.*)<\/.+>/", $arMatches[0][$baseTagMatchId], $curTagData );
                if( $match == $curTagData[1][0] ){
                    $itemTemplate = str_replace( $arMatches[0][$baseTagMatchId], "", $itemTemplate );
                }
                else{
                    $itemTemplate = str_replace( $match, "", $itemTemplate );
                }
            }
            elseif( is_array( $templateValues[$match] ) ){
                $replacementValue = array();
                for( $i = 0; $i < count( $templateValues[$match] ); $i++ ){
                    $newName = preg_replace( "/\#((.)+)\#/", "#$1_LISTVAL_ITEM_$i#", $arMatches[2][$baseTagMatchId] );
                    $replacementValue[] = str_replace( $arMatches[2][$baseTagMatchId], $newName, $arMatches[0][$baseTagMatchId] );
                    $templateValues[$newName] = $templateValues[$match][$i];
                }

                $itemTemplate = str_replace( $arMatches[0][$baseTagMatchId], implode( PHP_EOL, $replacementValue ), $itemTemplate );
            }
        }

        return $itemTemplate;
    }

    public function ProcessTagOptions( $templateValues, $field, $propId, $bXmlExport = false, &$itemTemplate = false, $arMatches = false ){
        if( is_array( $templateValues ) && !empty( $templateValues )
            && is_array( $field ) && !empty( $field )
            && !empty( $propId )
        ){
            $templateValues[$propId] = self::ConvertData(
                $templateValues[$propId],
                $field["CONVERT_DATA"]
            );

            if( ( $field["HTML_TO_TXT"] == "Y" ) && !is_array( $templateValues[$propId] ) ){
                $templateValues[$propId] = self::AcritHtmlToTxt( $templateValues[$propId] );
            }

            if( $field["HTML_ENCODE"] == "Y" ){
                 $templateValues[$propId] = self::HtmlEncode(
                    $templateValues[$propId]
                 );
            }

            if( $field["HTML_ENCODE_CUT"] == "Y" ){
                $templateValues[$propId] = self::HtmlEncodeCut(
                    $templateValues[$propId]
                );
            }

            if( $field["URL_ENCODE"] == "Y" ){
                $templateValues[$propId] = self::UrlEncode(
                    $templateValues[$propId]
                );
            }

            if( $field["CONVERT_CASE"] == "Y" ){
                $templateValues[$propId] = self::ConvertCase(
                    $templateValues[$propId]
                );
            }

            if( intval( $field["TEXT_LIMIT"] ) > 0 ){
                $templateValues[$propId] = self::AcritTruncateText( $templateValues[$propId], $field["TEXT_LIMIT"] );
            }

            $fieldPrePostfix = ( $bXmlExport ) ? "#" : "";
            if( ( intval( $templateValues[$fieldPrePostfix."OLDPRICE".$fieldPrePostfix] ) > 0 )
                && ( intval( $templateValues[$fieldPrePostfix."OLDPRICE".$fieldPrePostfix] ) <= intval( $templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix] ) )
                && ( $field["REQUIRED"] != "Y" )
            ){
                $templateValues[$fieldPrePostfix."OLDPRICE".$fieldPrePostfix] = "";
            }

            if( $bXmlExport && ( strlen( $itemTemplate ) > 0 )
                && is_array( $arMatches ) && !empty( $arMatches )
            ){
                $baseTagMatchId = array_search( $propId, $arMatches[2] );
                $itemTemplate = self::DeleteEmptyRow(
                    $itemTemplate,
                    $templateValues,
                    $arMatches,
                    $propId,
                    $baseTagMatchId,
                    ( $field["DELETE_ONEMPTY"] == "Y" ),
                    ( $field["DELETE_ONEMPTY_FORCE"] == "Y" ),
                    ( $field["DELETE_ONEMPTY_ROWFORCE"] == "Y" )
                );
            }
        }

        return $templateValues;
    }
}

class CAcritGooglemerchantMarketCategories{
    const YANDEX_CATEGORY_SERVER_PATH = "http://download.cdn.yandex.net/market/market_categories.xls";
    const GOOGLE_CATEGORY_SERVER_PATH = "http://www.google.com/basepages/producttype/taxonomy-with-ids.ru-RU.xls";

    private function GetCategoryArray( $localFilePath ){
	    if (ini_get('mbstring.func_overload') == 0) {
		    // dont work on mbstring.func_overload=2
		    $inputFileType = PHPExcel_IOFactory::identify($localFilePath);
		    $objReader = PHPExcel_IOFactory::createReader($inputFileType);

		    $objPHPExcel = $objReader->load($localFilePath);
		    $arCategories = $objPHPExcel->getActiveSheet()->toArray();
	    } else {
		    exec('php -d mbstring.func_overload=0 -f ' . __DIR__ . '/readExcelShell.php "' . $localFilePath . '"', $o);
		    $arCategories = json_decode(implode('', $o));
		    unset($o);
	    }
	    return $arCategories;
    }

    private function GetLocalFilePath( $yandexCategoryServerPath ){
        $yandexCategoryServerPathParts = explode( "/", $yandexCategoryServerPath );
        $yandexCategoryFileName = end( $yandexCategoryServerPathParts );
        $yandexCategoryLocalPath = $_SERVER["DOCUMENT_ROOT"]."/upload/acrit.googlemerchant/".$yandexCategoryFileName;

        copy($yandexCategoryServerPath, $yandexCategoryLocalPath);

        return ( file_exists( $yandexCategoryLocalPath ) ? $yandexCategoryLocalPath : false );
    }

    private function GetCorrectCharsetRow( $inputRow ){
        global $APPLICATION;

        $itemCharset = CAcritGooglemerchantTools::GetStringCharset( $inputRow );
        if( $itemCharset == "utf8" && ini_get('mbstring.func_overload') == 0){
            $itemRow = $APPLICATION->ConvertCharset( $inputRow, "UTF-8", "windows-1251" );
        }
        else{
            $itemRow = $inputRow;
        }

        return $itemRow;
    }

    private function GetGoogleCategoryRow( $categoryServerPath ){
        if( $categoryLocalPath = self::GetLocalFilePath( $categoryServerPath ) ){
            $arCategories = self::GetCategoryArray( $categoryLocalPath );
        }

        $sCategoryRow = "";
        foreach( $arCategories as $itemIndex => $arCategoriesItem ){
            $categoriesItemRow = "";
            foreach( $arCategoriesItem as $indexItemPart => $categoriesItemPart ){
                if( $indexItemPart && !empty( $categoriesItemPart ) ){
                    $categoriesItemRow .= $categoriesItemPart.( !empty( $arCategoriesItem[$indexItemPart + 1] ) ? " > " : "" );
                }
            }

            $itemRow = self::GetCorrectCharsetRow( $categoriesItemRow );
            $sCategoryRow .= $itemRow.( ( $itemIndex < ( count( $arCategories ) - 1 ) ) ? "\n" : "" );
        }

        return $sCategoryRow;
    }

    public function Process(){
        $sGoogleCategoryRow = self::GetGoogleCategoryRow( self::GOOGLE_CATEGORY_SERVER_PATH );

        if( strlen( $sGoogleCategoryRow ) > 0 ){
            $obMarket = new CGooglemerchantMarketDB();
            $arMarketCategories = $obMarket->GetList();

            $iGoogleMarketCategoryId = false;
            foreach( $arMarketCategories as $marketCategoriesItemIndex => $arMarketCategoriesItem ){
                if( $arMarketCategoriesItem["name"] == "Google Merchant" ){
                    $iGoogleMarketCategoryId = $marketCategoriesItemIndex;
                    $arMarketCategories[$marketCategoriesItemIndex]["data"] = $sGoogleCategoryRow;
                }
            }

            if( $iGoogleMarketCategoryId ){
                $obMarket->Update( $arMarketCategories[$iGoogleMarketCategoryId]["id"], $arMarketCategories[$iGoogleMarketCategoryId] );
            }
            else{
                $obMarket->Add(
                    array(
                        "name" => "Google Merchant",
                        "data" => $sGoogleCategoryRow
                    )
                );
            }
        }
    }
}