<?
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
if ($_REQUEST['check_https'] == 'Y')
{
	$arResult = array(
		'SUCCESS' => 'N'
	);

	$ch = curl_init();
	
	$options = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'https://'.$_SERVER['SERVER_NAME'],
			CURLOPT_HEADER => false,
			CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
	);
	curl_setopt_array($ch, $options);
	
	$bResult1 = curl_exec($ch);
	
	$options = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'https://'.$_SERVER['SERVER_NAME'],
			CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1
	);
	curl_setopt_array($ch, $options);
	
	$bResult2 = curl_exec($ch);
	
	curl_close($ch);
	
	if ($bResult1 && $bResult2)
		$arResult['SUCCESS'] = 'Y';
	
	echo CUtil::PhpToJSObject($arResult);
}
