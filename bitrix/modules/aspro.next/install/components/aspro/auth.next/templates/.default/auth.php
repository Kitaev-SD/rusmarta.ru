<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>

<?global $USER, $APPLICATION;
if( !$USER->IsAuthorized() ){?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:system.auth.form",
		"next",
		Array(
			"REGISTER_URL" => $arResult["SEF_FOLDER"].$arResult["URL_TEMPLATES"]["registration"],
			"FORGOT_PASSWORD_URL" => $arResult["SEF_FOLDER"].$arResult["URL_TEMPLATES"]["forgot_password"],
			"PROFILE_URL" => $arResult["SEF_FOLDER"],
			"SHOW_ERRORS" => "Y",
		)
	);?>
<?}else{?>
	<?$APPLICATION->IncludeComponent("bitrix:main.profile", "next", array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"SET_TITLE" => "N",
		"SEND_INFO" => "N",
		"CHECK_RIGHTS" => "N",
		"USER_PROPERTY_NAME" => "",
		"AJAX_OPTION_ADDITIONAL" => ""
		),
		false
	);?>
<?}?>