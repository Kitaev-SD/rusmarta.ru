<?
// php -d mbstring.func_overload=0 -f
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../../../../../';

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

\CModule::IncludeModule('acrit.googlemerchant');

$localFilePath = $argv[1];

$inputFileType = PHPExcel_IOFactory::identify( $localFilePath );
$objReader = PHPExcel_IOFactory::createReader( $inputFileType );
$objPHPExcel = $objReader->load( $localFilePath );
$arCategories = $objPHPExcel->getActiveSheet()->toArray();

echo json_encode($arCategories);
?>