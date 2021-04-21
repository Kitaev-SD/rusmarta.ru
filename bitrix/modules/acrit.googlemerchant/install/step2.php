<?
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile( __FILE__ );
$strModuleCode = preg_replace('#^acrit\.(.*?)$#i', '$1', $GLOBALS['ACRIT_MODULE_ID']);

global $APPLICATION, $dbstep, $DB;
if(!$dbstep)
	$dbstep = 1;

$siteEncoding = array(
	"utf-8" => "utf8",
	"UTF-8" => "utf8",
	"WINDOWS-1251" => "cp1251",
	"windows-1251" => "cp1251",
);
	
$redirect = false;

set_time_limit(0);

if( file_exists( __DIR__."/db/category" ) ){
	$files = scandir( __DIR__."/db/category" );
	if( is_array( $files ) ){
		$cntFile = 0;
		foreach( $files as $file ){
			if( $file == "." || $file == ".." )
				continue;
			
            if( !preg_match( "#market[0-9]+.sql#", $file ) )
				continue;
			
            $cntFile++;
			if( $cntFile == $dbstep ){
				if( $siteEncoding[strtoupper( SITE_CHARSET )] != "cp1251" ){
					$marketData = file_get_contents( __DIR__."/db/category/market$dbstep.sql" );
					$marketDataUTF8 = mb_convert_encoding( $marketData, "utf8", "cp1251" );
					$marketData = file_put_contents( __DIR__."/db/category/market$dbstep.sql", $marketDataUTF8 );
					unset( $marketData, $marketDataUTF8 );
				}
				$APPLICATION->setTitle(GetMessage("ACRIT_".toUpper($strModuleCode)."_STEP_2_DBINSTALL", ['#STEP#' => $dbstep]));
				print GetMessage('ACRIT_'.toUpper($strModuleCode).'_STEP_2_DETAILS');
				$DB->RunSQLBatch( __DIR__."/db/category/market$dbstep.sql" );
			}
		}
		if( $cntFile > $dbstep ){
			$dbstep++;
			$redirectUrl = $APPLICATION->GetCurPageParam( "dbstep=$dbstep", array( "dbstep" ) );
			$redirect = true;
			?>
			<script>
			setTimeout(function(){
				window.location = '<?=$redirectUrl?>';
			}, 1);
			</script>
			<?
			return;
		}
	}
	if( !$redirect ){
		DeleteDirFilesEx( str_replace( $_SERVER["DOCUMENT_ROOT"], "", __DIR__."/db/category" ) );
	}
}
if( !$redirect ){
	LocalRedirect( $APPLICATION->GetCurPageParam( "step=3&install=Y", array( "dbstep", "step" ) ) );
}
?>