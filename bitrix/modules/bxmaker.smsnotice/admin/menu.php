<?

    \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);

    $BXMAKER_MODULE_ID = 'bxmaker.smsnotice';
    $BXMAKER_MODULE_CODE = 'bxmaker_smsnotice';

    $moduleSort = 10000;
    $i = 0;
    $MOD_RIGHT = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);


    if (\Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID) && $MOD_RIGHT > "D") {
        $aMenu = array(
            "parent_menu" => "global_menu_bxmaker", // поместим в раздел "Контент"
            "sort"        => $moduleSort,
            "section"     => $BXMAKER_MODULE_ID,             // вес пункта меню
            "url"         => '/bitrix/admin/' . $BXMAKER_MODULE_ID . '_list.php?lang=' . LANGUAGE_ID,
            "text"        => GetMessage($BXMAKER_MODULE_CODE . '_MAIN_MENU_LINK_NAME'),       // текст пункта меню
            "title"       => GetMessage($BXMAKER_MODULE_CODE . '_MAIN_MENU_LINK_DESCRIPTION'), // текст всплывающей подсказки
            "icon"        => $BXMAKER_MODULE_CODE . '_icon', // малая иконка
            "page_icon"   => $BXMAKER_MODULE_CODE . '_page_icon', // большая иконка
            "items_id"    => $BXMAKER_MODULE_CODE . '_main_menu_items',  // идентификатор ветви
            "items"       => array()
        );

        if (file_exists($path = dirname(__FILE__))) {
            if ($dir = opendir($path)) {
                $arFiles = array();

                while (false !== $item = readdir($dir)) {
                    if ($MOD_RIGHT == "W") {
                        if (!in_array($item, array('list.php', "service_list.php", 'template_list.php', 'template_type_list.php'))) continue;
                    }
                    elseif($MOD_RIGHT == "S")
                    {
                        if (!in_array($item, array('list.php'))) continue;
                    }
                    $arFiles[] = $item;
                }

                sort($arFiles);
                $i++;
                foreach ($arFiles as $item)
                    $aMenu['items'][] = array(
                        'url'       => '/bitrix/admin/' . $BXMAKER_MODULE_ID . '_' . $item . '?lang=' . LANGUAGE_ID,
                        'more_url'  => array(
                            '/bitrix/admin/' . $BXMAKER_MODULE_ID . '_' . str_replace('list.php', 'edit.php', $item) . '?lang=' . LANGUAGE_ID
                        ),
                        'module_id' => $BXMAKER_MODULE_ID,
                        'text'      => GetMessage($BXMAKER_MODULE_CODE . '_' . substr($item, 0, strpos($item, '.')) . '_MENU_LINK_NAME'),
                        "title"     => GetMessage($BXMAKER_MODULE_CODE . '_' . substr($item, 0, strpos($item, '.')) . '_MENU_LINK_DESCRIPTION'),
                        //"icon"        => $BXMAKER_MODULE_CODE.'_'.$item.'_icon', // малая иконка
                        // "page_icon"   => $BXMAKER_MODULE_CODE.'_'.$item.'_page_icon', // большая иконка
                        'sort'      => $moduleSort + $i,
                    );
            }
        }

        if (\Bxmaker\SmsNotice\Manager::getInstance()->isLogError()) {
            $aMenu['items'][] = array(
                'url'       => '/bitrix/admin/' . $BXMAKER_MODULE_ID . '_log.php?lang=' . LANGUAGE_ID,
                'more_url'  => array(
                    '/bitrix/admin/' . $BXMAKER_MODULE_ID . '_log.php?lang=' . LANGUAGE_ID
                ),
                'module_id' => $BXMAKER_MODULE_ID,
                'text'      => GetMessage($BXMAKER_MODULE_CODE . '_log_MENU_LINK_NAME'),
                "title"     => GetMessage($BXMAKER_MODULE_CODE . '_log_MENU_LINK_NAME'),
                'sort'      => $moduleSort + $i,
            );
        }


        if ($MOD_RIGHT == "W") {
            $aMenu['items'][] = array(
                'url'       => '/bitrix/admin/settings.php?lang=' . LANGUAGE_ID . '&mid=' . $BXMAKER_MODULE_ID . '&mid_menu=1',
                'more_url'  => array(),
                'module_id' => $BXMAKER_MODULE_ID,
                'text'      => GetMessage($BXMAKER_MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'),
                "title"     => GetMessage($BXMAKER_MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'),
                'sort'      => $moduleSort + $i,
            );
        }

        $aMenu['items'][] = array(
            'url'       => "javascript:window.open('https://bxmaker.ru/doc/smsnotice/', '_blank');",
            'more_url'  => array(),
            'module_id' => $BXMAKER_MODULE_ID,
            'text'      => GetMessage($BXMAKER_MODULE_CODE . '_DOC_MENU_LINK_NAME'),
            "title"     => GetMessage($BXMAKER_MODULE_CODE . '_DOC_MENU_LINK_NAME'),
            'sort'      => $moduleSort + $i,
        );

        $aModuleMenu[] = $aMenu;
        return $aModuleMenu;
    }
    return false;
