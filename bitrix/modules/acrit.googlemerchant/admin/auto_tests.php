<?
if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

IncludeModuleLangFile( __FILE__ );

$arAutoProblems = $arAutoProblemsToSupportMessage = array();

if( !function_exists( "curl_setopt" ) ){
    $arAutoProblems[] = GetMessage( "AS_GOOGLEMERCHANT_NO_CURL_WARNING" );
    $arAutoProblemsToSupportMessage[] = GetMessage( "AS_GOOGLEMERCHANT_NO_CURL_WARNING_TP" );
}

if( !function_exists( "mb_convert_encoding" ) ){
    $arAutoProblems[] = GetMessage( "AS_GOOGLEMERCHANT_NO_MBSTRING_WARNING" );
    $arAutoProblemsToSupportMessage[] = GetMessage( "AS_GOOGLEMERCHANT_NO_MBSTRING_WARNING_TP" );
}

if( !function_exists( "simplexml_load_string" ) ){
    $arAutoProblems[] = GetMessage( "AS_GOOGLEMERCHANT_NO_SIMPLEXML_WARNING" );
    $arAutoProblemsToSupportMessage[] = GetMessage( "AS_GOOGLEMERCHANT_NO_SIMPLEXML_WARNING_TP" );
}

if( !function_exists( "json_decode" ) ){
	$arAutoProblems[] = GetMessage( "AS_GOOGLEMERCHANT_NO_JSON_WARNING" );
	$arAutoProblemsToSupportMessage[] = GetMessage( "AS_GOOGLEMERCHANT_NO_JSON_WARNING_TP" );
}

if( !class_exists( "ZipArchive" ) ){
    $arAutoProblems[] = GetMessage( "AS_GOOGLEMERCHANT_NO_ZIP_WARNING" );
    $arAutoProblemsToSupportMessage[] = GetMessage( "AS_GOOGLEMERCHANT_NO_ZIP_WARNING_TP" );
}

if( !class_exists( "XMLWriter" ) ){
    $arAutoProblems[] = GetMessage( "AS_GOOGLEMERCHANT_NO_XMLWRITER_WARNING" );
    $arAutoProblemsToSupportMessage[] = GetMessage( "AS_GOOGLEMERCHANT_NO_XMLWRITER_WARNING_TP" );
}

$maxInputVars = @ini_get( "max_input_vars" );
if( $maxInputVars < 20000 ){
    $arAutoProblems[] = GetMessage( "ACRIT_MAX_INPUT_VARS_RECOMENDATION" ).$maxInputVars."<br/>";
}

$dbMaxAllowedPacket = $DB->Query( "SHOW VARIABLES LIKE 'max_allowed_packet'" );
if( $arMaxAllowedPacket = $dbMaxAllowedPacket->Fetch() ){
    if( $arMaxAllowedPacket["Value"] < 1073741824 ){
        $arAutoProblems[] = GetMessage( "ACRIT_MAX_ALLOWED_PACKET_RECOMENDATION" ).$arMaxAllowedPacket["Value"]."<br/>";
    }
}

$dbWaitTimeout = $DB->Query( "SHOW VARIABLES LIKE 'wait_timeout'" );
if( $arWaitTimeout = $dbWaitTimeout->Fetch() ){
    if( $arWaitTimeout["Value"] < 28800 ){
        $arAutoProblems[] = GetMessage( "ACRIT_WAIT_TIMEOUT_RECOMENDATION" ).$arWaitTimeout["Value"]."<br/>";
    }
}

if( count( $arAutoProblems ) > 0 ){
	echo BeginNote();
	echo implode( "<br />", $arAutoProblems );
	echo EndNote();
}
?>