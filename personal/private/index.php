<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?>

<?/*if (!$_GET['change_password']) {?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:system.auth.forgotpasswd", 
		"", 
		Array(),
		false
	);?>
<?} else {?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:system.auth.changepasswd",  
		"", 
		Array(),
		false
	);?>
<?}?>

<?$APPLICATION->IncludeComponent(
	"bitrix:system.auth.form",
	"messages", 
	Array(
		"FORGOT_PASSWORD_URL" => "",
		"PROFILE_URL" => "",
		"REGISTER_URL" => "",
		"SHOW_ERRORS" => "Y"
	)
);
*/

//use Bitrix\Main\Diag\Debug;
//Debug::dump($_SERVER); 
//Debug::dump($USER); 
?>

<?$APPLICATION->IncludeComponent("bitrix:main.profile","",Array(
        "USER_PROPERTY_NAME" => "",
        "SET_TITLE" => "Y", 
        "AJAX_MODE" => "N", 
        "USER_PROPERTY" => Array(), 
        "SEND_INFO" => "Y", 
        "CHECK_RIGHTS" => "Y",  
        "AJAX_OPTION_JUMP" => "N", 
        "AJAX_OPTION_STYLE" => "Y", 
        "AJAX_OPTION_HISTORY" => "N" 
    )
);?> 

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>