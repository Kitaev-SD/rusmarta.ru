<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;
	Loc::loadLanguageFile(__FILE__);

	$BXMAKER_COMPONENT_NAME = 'BXMAKER.GEOIP.DELIVERY.PARAMS.';

	// ןאנאלעונû
	$arComponentParameters = array(
		"GROUPS"     => array(),
		"PARAMETERS" => array(
			'CACHE_TIME' => array(),
			'PRODUCT_ID' => array(
				"PARENT"  => "BASE",
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'PRODUCT_ID'),
				'TYPE'    => 'STRING',
				'DEFAULT' => ''
			),
			'SHOW_PARENT' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'SHOW_PARENT'),
				'TYPE'    => 'CHECKBOX',
				'DEFAULT' => 'N'
			),
			'IMG_SHOW' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'IMG_SHOW'),
				'TYPE'    => 'CHECKBOX',
				'DEFAULT' => 'N'
			),
			'IMG_WIDTH' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'IMG_WIDTH'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '30'
			),
			'IMG_HEIGHT' => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'IMG_HEIGHT'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '30'
			),
			'PROLOG'    => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'PROLOG'),
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'PROLOG_DEFAULT')
			),
			'EPILOG'    => array(
				'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'EPILOG'),
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'EPILOG_DEFAULT')
			),
		),
	);


