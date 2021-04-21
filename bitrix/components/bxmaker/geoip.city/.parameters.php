<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;

	Loc::loadLanguageFile(__FILE__);

	$BXMAKER_COMPONENT_NAME = 'BXMAKER.GEOIP.CITY.';

	// показ города, поумолчанию да
	// вывод таблички с вопросом о правильности определение, по умолчанию да
	// вопрос который на табличке - стандатный
	// вывод таблички с сылкой на страницу доставки и оплаты, по умолчанию да
	// вопрос на табличке  когда город уже определен верно, тект ведущий на страницу с информацией о доставке
	// вывод строки поиска, по умолчанию да
	// вывод списка городов, поумолчанию да
	// группа местоположений, если не задано то сортировкой все подряд - и ссылка где их можно задать


	// парамтеры
	$arComponentParameters = array(
		"GROUPS"     => array(),
		"PARAMETERS" => array(
			'CACHE_TIME' => array()
		),
	);


	if (\Bitrix\Main\Loader::includeModule('bxmaker.geoip')) {

		$oManager   = \Bxmaker\GeoIP\Manager::getInstance();
		$oFavorites = new \Bxmaker\GeoIP\FavoritesTable();

		$arComponentParameters['PARAMETERS']['RELOAD_PAGE'] = array(
			"PARENT"            => "BASE",
			"NAME"              => Loc::getMessage($BXMAKER_COMPONENT_NAME . "RELOAD_PAGE"),
			"TYPE"              => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH"           => "N",
			"DEFAULT"           => "N",
		);

		// показ города, поумолчанию да
		$arComponentParameters['PARAMETERS']['CITY_SHOW'] = array(
			"PARENT"            => "BASE",
			"NAME"              => Loc::getMessage($BXMAKER_COMPONENT_NAME . "CITY_SHOW"),
			"TYPE"              => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH"           => "N",
			"DEFAULT"           => "Y",
		);


		$arComponentParameters['PARAMETERS']['CITY_LABEL'] = array(
			"PARENT"  => "BASE",
			'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'CITY_LABEL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'CITY_LABEL_DEFAULT')
		);

		$arComponentParameters['PARAMETERS']['QUESTION_SHOW'] = array(
			"PARENT"            => "BASE",
			"NAME"              => Loc::getMessage($BXMAKER_COMPONENT_NAME . "QUESTION_SHOW"),
			"TYPE"              => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH"           => "N",
			"DEFAULT"           => "Y",
		);

		$arComponentParameters['PARAMETERS']['QUESTION_TEXT'] = array(
			"PARENT"  => "BASE",
			'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'QUESTION_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'QUESTION_TEXT_DEFAULT')
		);

		$arComponentParameters['PARAMETERS']['INFO_SHOW'] = array(
			"PARENT"            => "BASE",
			"NAME"              => Loc::getMessage($BXMAKER_COMPONENT_NAME . "INFO_SHOW"),
			"TYPE"              => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH"           => "N",
			"DEFAULT"           => "Y",
		);

		$arComponentParameters['PARAMETERS']['INFO_TEXT'] = array(
			"PARENT"  => "BASE",
			'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INFO_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INFO_TEXT_DEFAULT')
		);

		$arComponentParameters['PARAMETERS']['BTN_EDIT'] = array(
			"PARENT"  => "BASE",
			'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'BTN_EDIT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'BTN_EDIT_DEFAULT')
		);


		$arComponentParameters['PARAMETERS']['POPUP_LABEL'] = array(
			"PARENT"  => "BASE",
			'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'POPUP_LABEL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'POPUP_LABEL_DEFAULT')
		);


		// вывод строки поиска, по умолчанию да
		$arComponentParameters['PARAMETERS']['SEARCH_SHOW'] = array(
			"PARENT"            => "BASE",
			"NAME"              => Loc::getMessage($BXMAKER_COMPONENT_NAME . "SEARCH_SHOW"),
			"TYPE"              => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH"           => "N",
			"DEFAULT"           => "Y",
		);

		$arComponentParameters['PARAMETERS']['INPUT_LABEL'] = array(
			"PARENT"  => "BASE",
			'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INPUT_LABEL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'INPUT_LABEL_DEFAULT')
		);

		$arComponentParameters['PARAMETERS']['MSG_EMPTY_RESULT'] = array(
			"PARENT"  => "BASE",
			'NAME'    => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'MSG_EMPTY_RESULT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage($BXMAKER_COMPONENT_NAME . 'MSG_EMPTY_RESULT_DEFAULT')
		);


		// вывод списка городов, поумолчанию да
		$arComponentParameters['PARAMETERS']['FAVORITE_SHOW'] = array(
			"PARENT"            => "BASE",
			"NAME"              => Loc::getMessage($BXMAKER_COMPONENT_NAME . "FAVORITE_SHOW"),
			"TYPE"              => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH"           => "N",
			"DEFAULT"           => "Y",
		);

		$arComponentParameters['PARAMETERS']['CITY_COUNT'] = array(
			"PARENT"            => "BASE",
			"NAME"              => Loc::getMessage($BXMAKER_COMPONENT_NAME . "CITY_COUNT"),
			"TYPE"              => "STRING",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH"           => "N",
			"DEFAULT"           => "30",
		);


		$arFavorites  = array(
			0 => Loc::getMessage($BXMAKER_COMPONENT_NAME . "FID_DEFAULT")
		);
		$dbrFavorites = $oFavorites->getList(array(
			'filter' => array(
				'SITE.SID' => $oManager->getCurrentSiteId()
			),
			'order'  => array(
				'ID' => 'ASC'
			)
		));
		while ($ar = $dbrFavorites->fetch()) {
			$arFavorites[$ar['ID']] = $ar['NAME'];
		}

		// группа местоположений, если не задано то сортировкой все подряд - и ссылка где их можно задать
		$arComponentParameters['PARAMETERS']['FID'] = array(
			"PARENT"            => "BASE",
			"NAME"              => Loc::getMessage($BXMAKER_COMPONENT_NAME . "FID"),
			"TYPE"              => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES"            => $arFavorites,
			"REFRESH"           => "N"
		);


	}


?>