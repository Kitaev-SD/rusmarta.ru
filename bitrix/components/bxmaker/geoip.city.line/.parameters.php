<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;

	Loc::loadMessages(__FILE__);

	$BXMAKER_COMPONENT_NAME = 'BXMAKER.GEOIP.CITY.LINE.PARAMS.';

	// ןאנאלעונû
	$arComponentParameters = array(
		"GROUPS"     => array(),
		"PARAMETERS" => array(
			'CACHE_TIME'    => array(),
			'CITY_LABEL'    => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'CITY_LABEL'),
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'CITY_LABEL_DEFAULT')
			),
			'QUESTION_SHOW' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'QUESTION_SHOW'),
				'TYPE'    => 'CHECKBOX',
				'DEFAULT' => 'Y'
			),
			'QUESTION_TEXT' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'QUESTION_TEXT'),
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'QUESTION_TEXT_DEFAULT')
			),
			'INFO_SHOW' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INFO_SHOW'),
				'TYPE'    => 'CHECKBOX',
				'DEFAULT' => 'Y'
			),
			'INFO_TEXT' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INFO_TEXT'),
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INFO_TEXT_DEFAULT')
			),
			'BTN_EDIT' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'BTN_EDIT'),
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'BTN_EDIT_DEFAULT')
			)
		),
	);





