<?

	\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

	$MODULE_ID   = 'bxmaker.geoip';
	$MODULE_CODE = 'bxmaker_geoip';

	$moduleSort = 10000;
	$i          = 0;
	$MOD_RIGHT  = $APPLICATION->GetGroupRight($MODULE_ID);

	if ($MOD_RIGHT > "D") {
		$aMenu = array(
			"parent_menu" => "global_menu_bxmaker", // поместим в раздел "Контент"
			"sort"        => $moduleSort,
			"section"     => $MODULE_ID,             // вес пункта меню
			"url"         => '/bitrix/admin/settings.php?lang=' . LANGUAGE_ID . '&mid=' . $MODULE_ID . '&mid_menu=1',
			// "url"         => '/bitrix/admin/' . $MODULE_ID.'_list.php?lang='.LANGUAGE_ID,
			"text"        => GetMessage($MODULE_CODE . '_MAIN_MENU_LINK_NAME'),       // текст пункта меню
			"title"       => GetMessage($MODULE_CODE . '_MAIN_MENU_LINK_DESCRIPTION'), // текст всплывающей подсказки
			"icon"        => $MODULE_CODE . '_icon', // малая иконка
			"page_icon"   => $MODULE_CODE . '_page_icon', // большая иконка
			"items_id"    => $MODULE_CODE . '_main_menu_items',  // идентификатор ветви
			"items"       => array()
		);


		$arFiles = array(
			// filename => filename_ext more_url
			'list' => array('edit'),
			'favorites_list' => array('favorites_edit'),
			'country_list' => array('country_edit'),
			'region_list' => array('region_edit'),
			'city_list' => array('city_edit'),
			'location_import' => array(),
			'domain_edit' => array()
		);


		$i++;
		foreach ($arFiles as $fname => $arExtFname) {


			$arTmp = array(
				'url'       => '/bitrix/admin/' . $MODULE_ID . '_' . $fname . '.php?lang=' . LANGUAGE_ID,
				'more_url'  => array(),
				'module_id' => $MODULE_ID,
				'text'      => GetMessage($MODULE_CODE . '_' . $fname . '_MENU_LINK_NAME'),
				"title"     => GetMessage($MODULE_CODE . '_' . $fname . '_MENU_LINK_DESCRIPTION'),
				//"icon"        => $MODULE_CODE.'_'.$item.'_icon', // малая иконка
				// "page_icon"   => $MODULE_CODE.'_'.$item.'_page_icon', // большая иконка
				'sort'      => $moduleSort + $i,
			);

			foreach($arExtFname as $extfname)
			{
				$arTmp['more_url'][] = '/bitrix/admin/' . $MODULE_ID . '_' . $extfname . '.php?lang=' . LANGUAGE_ID;
			}

			$aMenu['items'][] = $arTmp;
		}


		$aMenu['items'][] = array(
			'url'       => '/bitrix/admin/settings.php?lang=' . LANGUAGE_ID . '&mid=' . $MODULE_ID . '&mid_menu=1',
			'more_url'  => array(),
			'module_id' => $MODULE_ID,
			'text'      => GetMessage($MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'),
			"title"     => GetMessage($MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'),
			'sort'      => $moduleSort + $i,
		);


		$aModuleMenu[] = $aMenu;
		return $aModuleMenu;
	}
	return false;
