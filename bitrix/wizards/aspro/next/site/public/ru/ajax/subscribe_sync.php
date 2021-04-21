<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
$bError = false;
if(!check_bitrix_sessid())
	$bError = true;
if($_POST['itemId'])
	$_SESSION['SUBSCRIBE_PRODUCT']['LIST_PRODUCT_ID'][$_POST['itemId']] = $_POST['itemEmail'];
else
	$bError = true;

echo Bitrix\Main\Web\Json::encode(
	array(
		'TYPE' => ($bError ? 'ERROR' : 'SUCCESS'),
		'message' => 'NO_DATA'
	)
);
?>