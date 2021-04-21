<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<?$APPLICATION->AddChainItem("Забыли пароль");?>
<?$APPLICATION->SetTitle("Забыли пароль");?>
<?global $USER, $APPLICATION;
if( !$USER->IsAuthorized() ){?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:system.auth.forgotpasswd",
		"aspro",
		false
	);?>
<?}else{
	LocalRedirect( $arParams["PERSONAL"] );
}?>