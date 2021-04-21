<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

	$PARTNER_COMPONENT_ID = 'BXMAKER.GEOIP.MESSAGE.PARAMS.';

	$arTypes = array();
	if(\Bitrix\Main\Loader::includeModule('bxmaker.geoip'))
	{
		$oMesType = new \Bxmaker\GeoIP\Message\TypeTable();
		$oManager = \Bxmaker\GeoIP\Manager::getInstance();
		$dbrType  = $oMesType->getList(array(
			'filter' => array(
				'SITE_ID' => $oManager->getCurrentSiteId()
			),
			'order' => array(
				'TYPE' => 'ASC'
			)
		));
		while($arType = $dbrType->fetch())
		{
			$arTypes[$arType['TYPE']] = $arType['TYPE'];
		}
	}

	// ןאנאלעונû
	$arComponentParameters = array(
		"GROUPS"     => array(),
		"PARAMETERS" => array(
			'CACHE_TIME' => array(),
			'TYPE' => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage($PARTNER_COMPONENT_ID."TYPE"),
				"TYPE" => "LIST",
				"ADDITIONAL_VALUES" => "N",
				"VALUES" => $arTypes,
				"REFRESH" => "N"
			)
		),
	);




?>