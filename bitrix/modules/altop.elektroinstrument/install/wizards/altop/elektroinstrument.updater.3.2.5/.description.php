<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arWizardDescription = Array(
	"NAME" => GetMessage("WIZARD_NAME"), 
	"DESCRIPTION" => GetMessage("WIZARD_DESC"), 
	"ICON" => "",
	"COPYRIGHT" => "ALTOP.RU",
	"VERSION" => "3.2.5",
	"DEPENDENCIES" => Array( 
		"altop.elektroinstrument" => "3.2.0"
	),
	"STEPS" => Array("Step0", "Step1", "Install", "FinalStep", "CancelStep")
);?>