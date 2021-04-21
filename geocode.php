<?php
/**
 * Created by PhpStorm.
 * Developer: s.skubach, 2017
 */

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__));
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Text\Encoding;
CModule::IncludeModule("iblock");

echo "start ".(date('d.m.Y H:i:s')).PHP_EOL;
$res = CIBlockElement::GetList(array(), array(
    'IBLOCK_ID' => CDEK_IBLOCK_ID
), false, false, array("ID", "NAME", "CODE", "PROPERTY_ADDRESS", "PROPERTY_COORDS"));
while($arFields = $res->GetNext())
{
    $street = $arFields['PROPERTY_ADDRESS_VALUE'];
    $coords = $arFields['PROPERTY_COORDS_VALUE'];
//    $coords = explode(',', $coords, 2);
//    CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array("COORDS" => $coords[1].','.$coords[0]));

    // обновляем символьный код
    $code = ToLower($arFields['CODE']);
    $el = new CIBlockElement;
    $arLoadProductArray = Array(
        "CODE"    => $code
    );
    $el->Update($arFields['ID'], $arLoadProductArray);

    // обновляем координаты по адресу
    if (!empty($coords)){
        continue;
    }
    $street = urlencode($street);
	if( $curl = curl_init() ) {
		curl_setopt($curl, CURLOPT_URL, "https://geocode-maps.yandex.ru/1.x/?format=json&geocode=".$street);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		$out = curl_exec($curl);
		$json = json_decode($out, true);
			
		$coords = $json['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
		$coords = str_replace(' ', ',', $coords);
		$coords = explode(',', $coords, 2);
		$coords = $coords[1].','.$coords[0];
		CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array("COORDS" => $coords));  
		curl_close($curl); 
	}
	
	
   /* $json = file_get_contents("https://geocode-maps.yandex.ru/1.x/?format=json&geocode=".$street);
    $json = json_decode($json, true);
		
    $coords = $json['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
	$coords = str_replace(' ', ',', $coords);
    $coords = explode(',', $coords, 2);
    $coords = $coords[1].','.$coords[0];*/

    //CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array("COORDS" => $coords));
}
die("end ".(date('d.m.Y H:i:s')));