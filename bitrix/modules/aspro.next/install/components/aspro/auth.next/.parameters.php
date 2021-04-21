<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"SEF_MODE" => Array(
			"auth" => array(
				"NAME" => GetMessage("T_AUTH"),
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"registration" => array(
				"NAME" => GetMessage("T_REGISTRATION"),
				"DEFAULT" => "registration/",
				"VARIABLES" => array(),
			),
			"forgot_password" => array(
				"NAME" => GetMessage("T_REPAIR_PWD"),
				"DEFAULT" => "forgot-password/",
				"VARIABLES" => array(),
			),
			"change_password" => array(
				"NAME" => GetMessage("T_CHANGE_PWD"),
				"DEFAULT" => "change-password/",
				"VARIABLES" => array(),
			),
			"confirm" => array(
				"NAME" => GetMessage("T_CONFIRM_PWD"),
				"DEFAULT" => "confirm-password/",
				"VARIABLES" => array(),
			),
			"confirm_registration" => array(
				"NAME" => GetMessage("T_CONFIRM_REGISTRATION"),
				"DEFAULT" => "confirm-registration/",
				"VARIABLES" => array(),
			)
		),
		"PERSONAL" => Array(
			"NAME" => GetMessage("T_LK"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/",
			"PARENT" => "BASE"
		)
	)
);
?>