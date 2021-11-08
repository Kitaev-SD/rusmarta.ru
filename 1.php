<?//exit;
use Bitrix\Main\Application;

$_SERVER["DOCUMENT_ROOT"]="/var/www/rusmarta.ru";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
echo "<pre>";
$arSelect = Array('ID','NAME','DETAIL_TEXT','DATE_CREATE_UNIX','PROPERTY_OBJECT_ID','PROPERTY_USER_ID','PROPERTY_USER_IP','PROPERTY_COMMENT_URL');
//$arSelect = Array();
$arFilter = Array('IBLOCK_ID' => 18, 'ACTIVE' => 'Y');
$res = CIBlockElement::GetList(Array('DATE_CREATE_UNIX'=>'ASC'), $arFilter, false, false, $arSelect);
$comments=[];
while($ob = $res->Fetch()) { 
	if (isset($comments[$ob['PROPERTY_OBJECT_ID_VALUE']])){
		$comments[$ob['PROPERTY_OBJECT_ID_VALUE']]['reviews'][]=array(
																    'id' =>  $ob['ID'],
																    'star'=> '5',
																    'ip' => iconv("windows-1251","utf-8", $ob['PROPERTY_USER_IP_VALUE']),
																    'status' => 'approved',
																    'comment'=> iconv("windows-1251","utf-8", strip_tags($ob['DETAIL_TEXT'])),
																    'created' => $ob['DATE_CREATE_UNIX']*1000,
																    'name' => iconv("windows-1251","utf-8",  $ob['PROPERTY_USER_ID_VALUE']),
																    //'email' => 'info@rusmarta.ru'
																    );
	} else {
		$comments[$ob['PROPERTY_OBJECT_ID_VALUE']]=array(
			'chan' => $ob['PROPERTY_OBJECT_ID_VALUE'],
			'url' => 'https://rusmarta.ru'.iconv("windows-1251","utf-8", $ob['PROPERTY_COMMENT_URL_VALUE']),
			'title' => 'Отзывы на товар '.iconv("windows-1251","utf-8", $ob['PROPERTY_OBJECT_ID_VALUE']),
			'reviews'=>array(array(
							    'id' => $ob['ID'],
							    'star'=>'5',
								    'ip' => iconv("windows-1251","utf-8", $ob['PROPERTY_USER_IP_VALUE']),
								    'status' => 'approved',
								    'comment'=> iconv("windows-1251","utf-8", strip_tags($ob['DETAIL_TEXT'])),
								    'created' => $ob['DATE_CREATE_UNIX']*1000,
								    'name' => iconv("windows-1251","utf-8",  $ob['PROPERTY_USER_ID_VALUE']),
								    //'email' => 'info@rusmarta.ru'
							    ))
		);
		
	}
	
}
//var_dump($comments);exit;
//var_dump(json_encode($comments[9963]));//exit;
/*file_put_contents('/var/www/rusmarta.ru/comments.json',);
exit;*/
function sendReviewsRequest($fields){
    $postfields = json_encode($fields);
    $curl_fields = array(
	    'id' => 78187,
	    'accountApiKey' => '175l3Sa5FO8ijM8nfQryC0YsHwXX8gKJWYu1O204bYuxa8wXqd5mqb75OrlbxmT3',
        'siteApiKey' => 'yuP7gBA67pbFyz37nrWKuoshOJyZIBxfJbXU5hNmEmg9ek7YcA9DlrVKEvmZICaa',
        'reviews' => $postfields
    );
    //var_dump(http_build_query($curl_fields));
	$curl=curl_init('http://cackle.me/api/3.0/review/post.json');
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_POST,true);
	curl_setopt($curl,CURLINFO_HEADER_OUT,true);
	curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($curl_fields));
	$result = curl_exec($curl);
	curl_close($curl);
	return $result;
}
/*var_dump(sendReviewsRequest($comments[9963]));
exit;*/
foreach ($comments as $key => $comment) {
	sendReviewsRequest($comment);
	sleep(5);
}
echo 'done!';
exit;














echo "<pre style='display:flex'>";
$order = array('sort' => 'asc');
$tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
$qrElementes = CUser::GetList($order, $tmp);
$arUsers = [];
while ($arElement = $qrElementes->Fetch()) {
	$arUsers[] = $arElement;
	var_dump($arUsers);
}
var_dump($arUsers);exit;
file_put_contents('/var/www/rusmarta.ru/users.txt', json_encode($arUsers));
