<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<?$APPLICATION->AddChainItem("Регистрация");?>
<?$APPLICATION->SetTitle("Регистрация");?>
<?global $USER, $APPLICATION;
if( !$USER->IsAuthorized() ){?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.register",
		"aspro",
		Array(
			"USER_PROPERTY_NAME" => "",
			"SHOW_FIELDS" => array( "LAST_NAME", "NAME", "SECOND_NAME", "PERSONAL_PHONE" ),
			"REQUIRED_FIELDS" => array( "NAME","PERSONAL_PHONE", "EMAIL" ),
			"AUTH" => "Y",
			"USE_BACKURL" => "Y",
			"SUCCESS_PAGE" => $arParams["PERSONAL"],
			"SET_TITLE" => "N",
			"USER_PROPERTY" => array()
		)
	);?>
<?}else{
	LocalRedirect( $arParams["PERSONAL"] );
}?>