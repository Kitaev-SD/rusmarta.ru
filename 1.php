<?
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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
