<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");
?>
<?$APPLICATION->IncludeComponent("aspro:auth.next", "main", array(
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "#SITE_DIR#auth/",
	"SEF_URL_TEMPLATES" => array(
		"auth" => "",
		"registration" => "registration/",
		"forgot" => "forgot-password/",
		"change" => "change-password/",
		"confirm" => "confirm-password/",
		"confirm_registration" => "confirm-registration/",
	),
	"PERSONAL" => "#SITE_DIR#personal/"
	),
	false
);?>
	
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>