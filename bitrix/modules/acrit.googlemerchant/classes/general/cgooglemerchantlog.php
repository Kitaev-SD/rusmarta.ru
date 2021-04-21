<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule( "acrit.googlemerchant" );

Loc::loadMessages( __FILE__ );

class CAcritGooglemerchantLog{
    public $logFilename = "/upload/acrit.googlemerchant/";
    private $profileId;
    private $session;
    
    private $profileEncoding = array(
        "utf8" => "utf-8",
        "cp1251" => "windows-1251",
    );

    public function __construct( $profileId = false ){
        $this->profileId = $profileId;
    }
    
    public static function AcritDump( $dumpData, $clear = FALSE, $depth = 0 ){
        $fileName = "acrit_googlemerchant_dump.txt";
        $file = $_SERVER["DOCUMENT_ROOT"]."/upload/".$fileName;

        $depthSign = "----";

        $strResult = "";
        $strDepth  = "";
        $nextDepth = $depth + 1;

        if( isset( $dumpData )
            && filter_var( $depth ) !== FALSE
            && $depth >= 0
            && is_bool( $clear ) ){

            if( $depth == 0
                && $clear ){

                file_put_contents( $file, "" );
            }
            else{
                for( $ico = 0; $ico < (int) $depth; $ico += 1 ){
                    $strDepth .= $depthSign;
                }
                $strDepth .= " ";
            }

            if( is_array( $dumpData ) ){
                foreach( $dumpData as $key => $value ){
                    if( is_array( $value ) ){
                        $strResult .= $strDepth.$key." = Array:\n";
                        file_put_contents( $file, $strResult, FILE_APPEND );
                        $strResult = "";

                        self::AcritDump( $value, $clear, $nextDepth );
                    }
                    elseif( is_null( $value ) ){
                        $strResult .= $strDepth.$key." = *NULL*\n";
                    }
                    elseif( $value === FALSE ){
                        $strResult .= $strDepth.$key." = *FALSE*\n";
                    }
                    elseif( is_string( $value )
                        && strlen( $value ) <= 0 ){

                        $strResult .= $strDepth.$key." = *EMPTY STRING*\n";
                    }
                    else{
                        $strResult .= $strDepth.$key." = ".$value."\n";
                    }
                }
            }
            elseif( is_null( $dumpData ) ){
                $strResult = "*NULL*\n";
            }
            elseif( $dumpData === FALSE ){
                $strResult = "*FALSE*\n";
            }
            elseif( is_string( $dumpData )
                && strlen( $dumpData ) <= 0 ){

                $strResult = "*EMPTY STRING*\n";
            }
            else{
                $strResult = $dumpData."\n";
            }
        }

        if( $depth === 0 ){
            $strResult .= "____________________________________________________\n\n";
        }

        if( strlen( $strResult ) > 0 ){
            file_put_contents( $file, $strResult, FILE_APPEND );
        }
    }

    public function Init( $profile ){
        $sessionData = AcritGooglemerchantSession::GetSession( $profile["ID"] );

        $sessionData["GOOGLEMERCHANT"]["LOG"][$profile["ID"]] = array(
            "IBLOCK" => 0,
            "SECTIONS" => 0,
            "PRODUCTS" => 0,
            "PRODUCTS_EXPORT" => 0,
            "PRODUCTS_ERROR" => 0,
            "FILE" => "",
            "LAST_START_EXPORT" => date( "d.m.Y H:i:s", time() )
        );
        $profileObj = new CGooglemerchantProfile();
        
        if( CModule::IncludeModule( "catalog" ) ){
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profile["ID"]]["IBLOCK"] = count( $profileObj->PrepareIBlock( $profile["IBLOCK_ID"], $profile["USE_SKU"] ) );
        }
        else{
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profile["ID"]]["IBLOCK"] = count( $profileObj->PrepareIBlock( $profile["IBLOCK_ID"], false ) );
        }

        if( $profile["CHECK_INCLUDE"] != "Y" ){
            $sections = array();
            $dbSection = CIBlockSection::GetList(
                array(),
                array(
                    "ID" => $profile["CATEGORY"]
                ),
                false,
                array(
                    "ID",
                    "LEFT_MARGIN",
                    "RIGHT_MARGIN"
                )
            );
            
            $arFilter = array( "LOGIC" => "OR" );
            
            while( $arSection = $dbSection->GetNext() ){
                $arFilter = array(
                    ">LEFT_MARGIN" => $arSection["LEFT_MARGIN"],
                    "<RIGHT_MARGIN" => $arSection["RIGHT_MARGIN"],
                    "IBLOCK_ID" => $profile["IBLOCK_ID"]
                );
                
                $dbSection2 = CIBlockSection::GetList(
                    array(),
                    $arFilter,
                    false,
                    array( "ID" )
                );
                
                while( $arSection2 = $dbSection2->GetNext() )
                    $sections[] = $arSection2["ID"];
            }
            $sections = array_unique( $sections );
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profile["ID"]]["SECTIONS"] = count( $sections );
        }
        else{
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profile["ID"]]["SECTIONS"] = count($profile["CATEGORY"]);
        }
        $this->profileId = $profile["ID"];
        CheckDirPath( $_SERVER["DOCUMENT_ROOT"].$this->logFilename );
        $this->logFilename = $this->logFilename."log_export_".$this->profileId.".txt";
        $sessionData["GOOGLEMERCHANT"]["LOG"][$profile["ID"]]["FILE"] = $this->logFilename;
        file_put_contents( $_SERVER["DOCUMENT_ROOT"].$this->logFilename, "" );
        AcritGooglemerchantSession::SetSession( $profile["ID"], $sessionData );
    }
    
    public function IncIblock(){
        $sessionData = AcritGooglemerchantSession::GetSession( $this->profileId );
        $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profileId]["IBLOCK"]++;
        AcritGooglemerchantSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function IncSection(){
        $sessionData = AcritGooglemerchantSession::GetSession( $this->profileId );
        $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profileId]["SECTIONS"]++;
        AcritGooglemerchantSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function IncProduct( $cnt = 0 ){
        $sessionData = AcritGooglemerchantSession::GetSession( $this->profileId );
        if( !intval( $cnt ) )
            $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profileId]["PRODUCTS"]++;
        else
            $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profileId]["PRODUCTS"] += $cnt;
        
        AcritGooglemerchantSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function IncProductExport(){
        $sessionData = AcritGooglemerchantSession::GetSession( $this->profileId );
        $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profileId]["PRODUCTS_EXPORT"]++;
        AcritGooglemerchantSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function IncProductError(){
        $sessionData = AcritGooglemerchantSession::GetSession( $this->profileId );
        $sessionData["GOOGLEMERCHANT"]["LOG"][$this->profileId]["PRODUCTS_ERROR"]++;
        AcritGooglemerchantSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function AddMessage( $message ){
        $messageProfileId = $this->profileId;
                                      
        $arMbStringData = mb_get_info();
        
        if( is_array( $arMbStringData ) && !empty( $arMbStringData ) ){
            if( mb_stripos( $this->logFilename, $messageProfileId ) === false ){
                $this->logFilename = $this->logFilename."log_export_".$messageProfileId.".txt";
            }
        }
        else{
             if( stripos( $this->logFilename, $messageProfileId ) === false ){
                $this->logFilename = $this->logFilename."log_export_".$messageProfileId.".txt";
            }
        }
        
        if( is_file( $_SERVER["DOCUMENT_ROOT"].$this->logFilename ) ){
            file_put_contents( $_SERVER["DOCUMENT_ROOT"].$this->logFilename, $message, FILE_APPEND );    
        }
    }
    
    public function GetLog( $profileID, $bSendEmailReport = true ){
        $obProfile = new CGooglemerchantProfileDB();
        $arProfile = $obProfile->GetByID( $profileID );
                  
        $arSessionData = AcritGooglemerchantSession::GetAllSession( $profileID );
        $sessionData = array();
        if( !empty( $arSessionData ) ){
            $sessionData = $arSessionData[0];
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] = intval( $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] );
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"] = intval( $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"] );
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] = intval( $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] );
            
            unset( $arSessionData[0] );
            
            foreach( $arSessionData as $sData ){
                $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] += intval( $sData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] );
                $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"] += intval( $sData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"] );
                $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] += intval( $sData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] );
            }
            
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] = $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] - $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"];
        }    
        
        if( $bSendEmailReport ){
            $obProfile = new CGooglemerchantProfileDB();
            $arProfile = $obProfile->GetByID( $profileID );
            
            if( check_email( $arProfile["SEND_LOG_EMAIL"] ) ){
                $messageTitle = GetMessage( "ACRIT_LOG_SEND_TITLE" ).$arProfile["DOMAIN_NAME"];
                $messageBlock = GetMessage( "ACRIT_LOG_SEND_OFFERS" ).$sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"]."\n".
                GetMessage( "ACRIT_LOG_SEND_OFFERS_TERM" ).$sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"]."\n".
                GetMessage( "ACRIT_LOG_SEND_OFFERS_ERROR" ).$sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"]."\n".
                GetMessage( "ACRIT_LOG_SEND_DATE" ).$sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["LAST_START_EXPORT"]."\n\n".
                GetMessage( "ACRIT_LOG_PROFILE" ).$arProfile["SITE_PROTOCOL"]."://".$arProfile["DOMAIN_NAME"]."/bitrix/admin/acrit_googlemerchant_edit.php?ID=".$profileID."\n".            
                GetMessage( "ACRIT_LOG_SEND_FILE" ).$arProfile["SITE_PROTOCOL"]."://".$arProfile["DOMAIN_NAME"].$sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["FILE"];            
                
                $headers = "Content-type: text/plain; charset=".LANG_CHARSET;
                bxmail( $arProfile["SEND_LOG_EMAIL"], $messageTitle, $messageBlock, $headers );
            }    
        }
        
        return $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID];
    }
    
    public function GetLogArray( $profileID ){
        $arLogArray = array();
        
        $obProfile = new CGooglemerchantProfileDB();
        $arProfile = $obProfile->GetByID( $profileID );
        
        $arSessionData = AcritGooglemerchantSession::GetAllSession( $profileID );
        $sessionData = array();
        if( !empty( $arSessionData ) ){
            $sessionData = $arSessionData[0];
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] = intval( $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] );
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"] = intval( $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"] );
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] = intval( $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] );
            
            unset( $arSessionData[0] );
            
            foreach( $arSessionData as $sData ){
                $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] += intval( $sData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] );
                $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"] += intval( $sData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"] );
                $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] += intval( $sData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] );
            }
            
            $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"] = $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"] - $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"];
        }
        
        $arLogArray["PRODUCTS"] = $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS"];
        $arLogArray["PRODUCTS_EXPORT"] = $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_EXPORT"];
        $arLogArray["PRODUCTS_ERROR"] = $sessionData["GOOGLEMERCHANT"]["LOG"][$profileID]["PRODUCTS_ERROR"];
        
        return $arLogArray;
    }
}