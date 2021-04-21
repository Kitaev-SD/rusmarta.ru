<?if(!defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED !==true) die();?>
<?
$arSubscription = array();
if(isset($arResult["RUBRICS"]) && $arResult["RUBRICS"])
{
	//get current user subscription from cookies
	$arSubscription = CSubscription::GetUserSubscription();
}
if($arSubscription["ID"])
{
	$arResult["USER_EMAIL"] = $arSubscription["EMAIL"];
}?>