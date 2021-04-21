<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)	die();

if(!CModule::IncludeModule("sale"))
	return;

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite->Fetch())
	$lang = $arSite["LANGUAGE_ID"];
if(strlen($lang) <= 0)
	$lang = "ru";
$bRus = false;
if($lang == "ru")
	$bRus = true;

if($bRus || COption::GetOptionString("elektroinstrument", "wizard_installed", "N", WIZARD_SITE_ID) != "Y" || WIZARD_INSTALL_DEMO_DATA) {
	$loc_file = $wizard->GetVar("locations_csv");

	$typeTableFreshEnough = false; 
	if($GLOBALS['DB']->Query("select DISPLAY_SORT from b_sale_loc_type WHERE 1=0", true))
		$typeTableFreshEnough = true;

	if(strlen($loc_file) > 0) {
		define("LOC_STEP_LENGTH", 20);
		
		require($_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/locations/file_map.php");

		$file_url = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/locations/bundles/".$LOCATION_FILE_MAP[$loc_file];
		$type_file_url = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/locations/type".($typeTableFreshEnough ? ".v2" : "").".csv";
		$service_file_url = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/locations/externalservice.csv";

		if(isset($LOCATION_FILE_MAP[$loc_file]) && file_exists($file_url)) {
			if(!isset($_SESSION["LOC_IMPORT_DESC"]) || ($file_url != $_SESSION["LOC_IMPORT_DESC"]["FILE"])) {
				$_SESSION["LOC_IMPORT_DESC"] = array(
					"POS" => 0,
					"FILE" => $file_url,
					"TYPE_FILE" => $type_file_url,
					"SERVICE_FILE" => $service_file_url,
					"TIME_LIMIT" => LOC_STEP_LENGTH,
					"STEP" => "import"
				);
			}

			$done = \Bitrix\Sale\Location\Import\ImportProcess::importFile($_SESSION["LOC_IMPORT_DESC"]);
			
			if($done) {
				unset($_SESSION["LOC_IMPORT_DESC"]);
				//go farther to other steps
			} else {
				$this->repeatCurrentService = true; //go to the next iteration of the same step
			}
		}
	}
}?>