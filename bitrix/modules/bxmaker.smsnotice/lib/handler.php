<?
    
    namespace Bxmaker\SmsNotice;
    
    
    use Bitrix\Main\Application;
    use \Bitrix\Main\Entity;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Type\DateTime;
    use Bitrix\Main\Loader;
    
    Loc::loadMessages(__FILE__);
    
    
    class Handler
    {
        
        static private $module_id = 'bxmaker.smsnotice';
        
        static private $arTmpData = array(); // временные данные для передачи между обработчиками
        
        
        public static function main_onBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu)
        {
            $arGlobalMenu['global_menu_bxmaker'] = array(
                'menu_id' => 'bxmaker',
                'text' => Loc::getMessage(self::$module_id . '.HANDLER.GLOBAL_MENU_TEXT'),
                'title' => Loc::getMessage(self::$module_id . '.HANDLER.GLOBAL_MENU_TITLE'),
                'sort' => '250',
                'items_id' => 'global_menu_bxmaker',
                'help_section' => 'bxmaker',
                'items' => array()
            );
        }
        
        /**
         * @return \Bitrix\Main\Config\Option
         */
        static private function option()
        {
            static $oOption;
            if (!isset($oOption)) {
                $oOption = new \Bitrix\Main\Config\Option();
            }
            return $oOption;
        }
        
        
        /**
         * Получение паарметра модуля
         *
         * @param        $name
         * @param string $default_value
         *
         * @return string
         * @throws \Bitrix\Main\ArgumentNullException
         */
        static private function getParam($name, $default_value = '', $siteId = null)
        {
            if (is_null($siteId)) {
                $siteId = Manager::getInstance()->getCurrentSiteId();
            }
            return self::option()->get(self::$module_id, $name, $default_value, $siteId);
        }
        
        
        //===========================================================================================
        // main
        //===========================================================================================
        
        public function main_OnBeforeUserAdd($arParams)
        {
            if (isset($arParams['LOGIN']) && isset($arParams['PASSWORD'])) {
                self::$arTmpData['main_user_add'][$arParams['LOGIN']] = $arParams['PASSWORD'];
            }
        }
        
        /**
         * После добавления/регистрации пользвоателя, главный модуль битрикса
         *
         * @param $arFields
         */
        public function main_OnAfterUserAdd($arFields)
        {
            if (isset($arFields['ID']) && intval($arFields['ID'])) {
                // проверяем указано ли поле в котором ранится номер телефона
                $field_phone = trim(self::getParam('HANDLER.USER_PHONE_FIELD', null));
                $template_type = trim(self::getParam('HANDLER.USER_ADD_TEMPLATE_TYPE', null));
                
                if ($field_phone && $template_type) {
                    
                    //получаем все необходимые поля
                    $arTemplateFields = array();
                    $oUser = new \CUser();
                    $dbr = $oUser->GetList($by = 'ID', $order = 'asc', array('ID' => intval($arFields['ID'])), array('SELECT' => array('UF_*')));
                    if ($arTemplateFields = $dbr->Fetch()) {
                        
                        // заменим поля на незашифрованные
                        if (isset($arFields['LOGIN']) && isset(self::$arTmpData['main_user_add'][$arFields['LOGIN']])) {
                            $arTemplateFields['PASSWORD'] = self::$arTmpData['main_user_add'][$arFields['LOGIN']];
                            
                            $_SESSION['BXMAKER_SMSNOTICE_AFTER_USER_ADD_' . $arFields['ID'] . '_PASSWORD'] = self::$arTmpData['main_user_add'][$arFields['LOGIN']];
                        } elseif (isset($_SESSION['BXMAKER_SMSNOTICE_AFTER_USER_ADD_' . $arFields['ID'] . '_PASSWORD'])) {
                            $arTemplateFields['PASSWORD'] = $_SESSION['BXMAKER_SMSNOTICE_AFTER_USER_ADD_' . $arFields['ID'] . '_PASSWORD'];
                        }
                        
                        $arTemplateFields['PHONE'] = (array_key_exists($field_phone,
                            $arTemplateFields) ? $arTemplateFields[$field_phone] : $field_phone);
                        
                        
                        if (Manager::getInstance()->isValidPhone($arTemplateFields['PHONE'])) {
                            unset($_SESSION['BXMAKER_SMSNOTICE_AFTER_USER_ADD_' . $arFields['ID'] . '_PASSWORD'], self::$arTmpData['main_user_add'][$arFields['LOGIN']]);
                        }
                        
                        
                        // отправка
                        $result = Manager::getInstance()->sendTemplate($template_type, $arTemplateFields);
                        if (!$result->isSuccess()) {
                            //                        echo '<pre>';
                            //                        print_r($result->getErrorMessages());
                            //                        echo '</pre>';
                        } else {
                            $errors = (array)$result->getMore('errors');
                            foreach ($errors as $error) {
                                //                            echo '<pre>';
                                //                            print_r($error);
                                //                            echo '</pre>';
                            }
                        }
                    }
                }
            }
        }
        
        
        public function main_OnBeforeUserUpdate($arParams)
        {
            if (isset($arParams['ID'])) {
                self::$arTmpData['main_user_update'][$arParams['ID']] = $arParams['PASSWORD'];
            }
        }
        
        /**
         * Обновление пароля
         *
         * @param $arFields
         */
        public function main_OnAfterUserUpdate($arFields)
        {
            if (isset($arFields['ID']) && isset($arFields['PASSWORD']) && !!$arFields['RESULT']) {
                // проверяем указано ли поле в котором ранится номер телефона
                $field_phone = trim(self::getParam('HANDLER.USER_PHONE_FIELD', null));
                $template_type = trim(self::getParam('HANDLER.USER_UPDATE_TEMPLATE_TYPE', null));
                
                if ($field_phone && $template_type) {
                    //получаем все необходимые поля
                    $arTemplateFields = array();
                    $oUser = new \CUser();
                    $dbr = $oUser->GetList($by = 'ID', $order = 'asc', array('ID' => intval($arFields['ID'])), array('SELECT' => array('UF_*')));
                    if ($arTemplateFields = $dbr->Fetch()) {
                        
                        // заменим поля на незашифрованные
                        if (isset(self::$arTmpData['main_user_update'][$arFields['ID']])) {
                            $arTemplateFields['PASSWORD'] = self::$arTmpData['main_user_update'][$arFields['ID']];
                        }
                        
                        $arTemplateFields['PHONE'] = (array_key_exists($field_phone,
                            $arTemplateFields) ? $arTemplateFields[$field_phone] : $field_phone);
                        
                        
                        // отправка
                        $result = Manager::getInstance()->sendTemplate($template_type, $arTemplateFields);
                        if (!$result->isSuccess()) {
                            //                        echo '<pre>';
                            //                        print_r($result->getErrorMessages());
                            //                        echo '</pre>';
                        } else {
                            $errors = (array)$result->getMore('errors');
                            foreach ($errors as $error) {
                                //                            echo '<pre>';
                                //                            print_r($error);
                                //                            echo '</pre>';
                            }
                        }
                        
                    }
                }
            }
        }
        
        
        //===========================================================================================
        // bxmaker.authuserphone
        //===========================================================================================
        public function bxmaker_authuserphone_onSendCode($arFields)
        {
        
            return Manager::getInstance()->sendTemplate('BXMAKER_AUTHUSERPHONE_SENDCODE', $arFields);
        }
        
        public function bxmaker_authuserphone_onUserAdd($arFields)
        {
            return Manager::getInstance()->sendTemplate('BXMAKER_AUTHUSERPHONE_USERADD', $arFields);
        }
        
        public function bxmaker_authuserphone_onUserChangePassword($arFields)
        {
            return Manager::getInstance()->sendTemplate('BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD', $arFields);
        }
        
        //===========================================================================================
        // sale Интернет-магазин стандартный
        //===========================================================================================
        
        //Перед добавлением заказа
        public function sale_OnBeforeOrderAdd($arFields)
        {
            // во время добавления заказа многократно происходит обноление заказа
            // чтобы не пложить сотню смс,  подвязываемся к событию отправления письма о новом заказе
            self::$arTmpData['sale_order_add_start'] = true;
        }
        
        //Добавлени заказа
        public function sale_OnOrderAdd($ID, $arFields)
        {
            unset(self::$arTmpData['sale_order_add_start']);
        }
        
        //Добавлени заказа
        public function sale_OnSaleOrderSaved($order)
        {
            /** @var  \Bitrix\Sale\Order $order */
            
            //self::sale_onEventOrderAdd($order->getId());
        }
        
        public static function sale_onEventOrderAdd($ORDER_ID)
        {
            if (strlen($ORDER_ID) > 0) {
                
                // не отправляем одно и тоже повторно
                if (isset(self::$arTmpData['sale_onEventOrderAdd_' . $ORDER_ID])) {
                    return;
                }
                self::$arTmpData['sale_onEventOrderAdd_' . $ORDER_ID] = true;
                
                $arOrderData = self::getOrderData($ORDER_ID);
                
                if (!is_null($arOrderData)) {
                    $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');
                    
                    // проверяем указано ли поле в котором ранится номер телефона
                    $userPhoneField = Manager::getInstance()->getUserPhoneField($arOrderData['SITE_ID']);
                    $field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                    $template_type = trim(self::getParam('HANDLER.ORDER_NEW_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                    
                    $savePhone = trim(self::getParam('HANDLER.SAVE_PHONE_' . $persone_type, 'N', $arOrderData['SITE_ID']));
                    
                    if ($field_phone && $savePhone == 'Y' && isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
                        $oUser = new \CUser();
                        $dbr = $oUser->GetList($by = '', $order = '', array('ID' => intval($arOrderData['USER_ID'])), array(
                            'SELECT' => array(
                                '*',
                                'UF_'
                            )
                        ));
                        if ($ar = $dbr->Fetch()) {
                            $ar['PHONE'] = (array_key_exists($userPhoneField, $ar) ? $ar[$userPhoneField] : '');
                            
                            if (!Manager::getInstance()->isValidPhone($ar['PHONE'])) {
                                if (Manager::getInstance()->isValidPhone($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
                                    // обновляем
                                    $oUser->Update($ar['ID'], array(
                                        $userPhoneField => Manager::getInstance()->getPreparePhone($arOrderData['PROPERTY_VALUE_' . $field_phone])
                                    ));
                                    
                                    if (isset($_SESSION['BXMAKER_SMSNOTICE_AFTER_USER_ADD_' . $ar['ID'] . '_PASSWORD'])) {
                                        // оповещаем
                                        self::main_OnAfterUserAdd(array(
                                            'ID' => $ar['ID'],
                                            'LOGIN' => $ar['LOGIN']
                                        ));
                                    }
                                    
                                }
                            }
                        }
                    }
                    
                    if ($field_phone && $template_type) {
                        
                        if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
                            // подготовим поле
                            $arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];
                            
                            Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
                        }
                    }
                }
                
                
            }
        }
        
        
        // перед обновлением заказа
        public function sale_OnBeforeOrderUpdate($ID, $arFields)
        {
            if (isset(self::$arTmpData['sale_order_add_start']) && self::$arTmpData['sale_order_add_start'] === true) {
                return;
            }
        }
        
        // Обновление заказ
        public function sale_OnOrderUpdate($ID, $arFields)
        {
            if (isset(self::$arTmpData['sale_order_add_start']) && self::$arTmpData['sale_order_add_start'] === true) {
                return;
            }
        }
        
        // заказ оплачен
        public function sale_OnSalePayOrder($ORDER_ID, $val)
        {
            if ($val == 'Y') {
                // не отправляем одно и тоже повторно
                if (isset(self::$arTmpData['sale_OnSalePayOrder' . $ORDER_ID])) {
                    return;
                }
                self::$arTmpData['sale_OnSalePayOrder' . $ORDER_ID] = true;
                
                $arOrderData = self::getOrderData($ORDER_ID, true);
                
                if (!is_null($arOrderData)) {
                    $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');
                    
                    // проверяем указано ли поле в котором ранится номер телефона
                    $field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                    $template_type = trim(self::getParam('HANDLER.ORDER_PAY_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                    
                    if ($field_phone && $template_type) {
                        
                        if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
                            // подготовим поле
                            $arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];
                            
                            Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
                        }
                    }
                }
                
                
            }
        }
        
        // заказ оплачен
        public function sale_OnSaleCancelOrder($ORDER_ID, $val)
        {
            
            if ($val == 'Y') {
                // не отправляем одно и тоже повторно
                if (isset(self::$arTmpData['sale_OnSaleCancelOrder' . $ORDER_ID])) {
                    return;
                }
                self::$arTmpData['sale_OnSaleCancelOrder' . $ORDER_ID] = true;
                
                $arOrderData = self::getOrderData($ORDER_ID, true);
                
                if (!is_null($arOrderData)) {
                    $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : 1);
                    
                    // проверяем указано ли поле в котором ранится номер телефона
                    $field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                    $template_type = trim(self::getParam('HANDLER.ORDER_CANCELED_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                    
                    
                    if ($field_phone && $template_type) {
                        
                        if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
                            // подготовим поле
                            $arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];
                            
                            Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
                        }
                    }
                }
                
            }
        }
        
        /**
         * Обработчик смены статуса заказа -----------------------
         *
         * @param $ORDER_ID
         * @param $STATUS_ID
         *
         * @throws \Bitrix\Main\ArgumentNullException
         * @throws \Bitrix\Main\LoaderException
         */
        public function sale_OnSaleStatusOrder($ORDER_ID, $STATUS_ID)
        {
            
            $STATUS_ID = (string)$STATUS_ID;
            
            // не отправляем одно и тоже повторно
            if (isset(self::$arTmpData['sale_OnSaleStatusOrder_' . $STATUS_ID . '_' . $ORDER_ID])) {
                return;
            }
            self::$arTmpData['sale_OnSaleStatusOrder_' . $STATUS_ID . '_' . $ORDER_ID] = true;
            
            $arOrderData = self::getOrderData($ORDER_ID, true);
            
            if (!is_null($arOrderData)) {
                $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');
                
                // проверяем указано ли поле в котором ранится номер телефона
                $field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                $template_type = trim(self::getParam('HANDLER.ORDER_STATUS_' . $STATUS_ID . '_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                
                if ($field_phone && $template_type) {
                    
                    if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
                        // подготовим поле
                        $arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];
                        
                        Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
                    }
                }
            }
            
            
        }
        
        
        /**
         * Возвращает массив данных для передачи в шаблон связанный с заказами стандартного модуля -  интернет-магазин
         *
         * @param $ORDER_ID
         *
         * @return array|bool|null
         * @throws \Bitrix\Main\LoaderException
         */
        public static function getOrderDataD7($ORDER_ID, $bRealId = false)
        {
            
            if (!Loader::includeModule('sale')) {
                return null;
            }
            
            // используются не реальные номера
            $orderDat = false;
            $bAccount = strlen(trim(\Bitrix\Main\Config\Option::get('sale', 'account_number_template', ''))) > 0;
            if (!$bAccount) {
                $bAccount = (\Bitrix\Main\Config\Option::get('sale', 'hideNumeratorSettings', '') != 'Y');
            }
            if (!$bRealId && $bAccount) {
                if ($orderDat = \Bitrix\Sale\Order::getList(array(
                    'filter' => array(
                        'ACCOUNT_NUMBER' => $ORDER_ID
                    )
                ))->fetch()) {
                    $ORDER_ID = $orderDat['ID'];
                }
            }
            
            if (intval($ORDER_ID) <= 0) {
                return false;
            }
            
            /** @var  \Bitrix\Sale\Order $order */
            $order = \Bitrix\Sale\Order::load($ORDER_ID);
            
            if (!is_null($order)) {
                $arOrderData = $order->getFieldValues();
                
                $arOrderData['ORDER_ID'] = $arOrderData['ACCOUNT_NUMBER'];
                $arOrderData['SITE_ID'] = $arOrderData['LID'];
                
                //ссылка для неавторизованных пользоваталей
                $arOrderData['ORDER_PUBLIC_URL'] = \Bitrix\Sale\Helpers\Order::getPublicLink($order);
                
                // проверяем указано ли поле в котором ранится номер телефона
                $presonTypeId = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '1');
                $phoneField = trim(self::getParam('HANDLER.PERSON_TYPE_' . $presonTypeId, 'PHONE', $arOrderData['SITE_ID']));
                $phoneDefault = null;
                $phone = null;
                
                
                //Свойства
                $collections = $order->getPropertyCollection();
                $arCollections = $collections->getArray();
                foreach ($arCollections['properties'] as $prop) {
                    
                    if ($prop['IS_PHONE'] == 'Y') {
                        $phoneDefault = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
                    }
                    
                    if ($prop['CODE'] == $phoneField) {
                        $phone = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
                    }
                    
                    
                    //					$arOrder['PROPERTY_VALUE_' . $prop['CODE']] = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
                    
                    // значения в виде массива получаем----------------------------
                    //объединеные
                    $arOrderData['PROPERTY_VALUE_' . $prop['CODE']] = implode(', ', $prop['VALUE']);
                    
                    // поэлементам
                    foreach ($prop['VALUE'] as $propValueIndex => $propValue) {
                        $arOrderData['PROPERTY_VALUE_' . $prop['CODE'] . '.' . $propValueIndex] = $propValue;
                    }
                    
                    //первый
                    $arOrderData['PROPERTY_VALUE_' . $prop['CODE'] . '.FIRST'] = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
                    
                    //последний
                    $arOrderData['PROPERTY_VALUE_' . $prop['CODE'] . '.LAST'] = (count($prop['VALUE']) && isset($prop['VALUE'][count($prop['VALUE']) - 1]) ? $prop['VALUE'][count($prop['VALUE']) - 1] : null);
                    
                    // для типа перечисление --------------------------
                    $arEnumValue = array();
                    if ($prop['TYPE'] == 'ENUM') {
                        foreach ($prop['VALUE'] as $propValue) {
                            $arEnumValue[] = (isset($prop['OPTIONS'][$propValue]) ? $prop['OPTIONS'][$propValue] : $propValue);
                        }
                    }
                    
                    $arOrderData['PROPERTY_VALUE_' . $prop['CODE'] . '.LIST_NAME'] = implode(', ', $arEnumValue);
                    
                    // по индексам --
                    foreach ($arEnumValue as $propValueIndex => $propValue) {
                        $arOrderData['PROPERTY_VALUE_' . $prop['CODE'] . '.LIST_NAME.' . $propValueIndex] = $propValue;
                    }
                    
                    //первый--
                    $arOrderData['PROPERTY_VALUE_' . $prop['CODE'] . '.LIST_NAME.FIRST'] = (isset($arEnumValue[0]) ? $arEnumValue[0] : null);
                    
                    //последний ---
                    $arOrderData['PROPERTY_VALUE_' . $prop['CODE'] . '.LIST_NAME.LAST'] = (count($arEnumValue) && isset($arEnumValue[count($arEnumValue) - 1]) ? $arEnumValue[count($arEnumValue) - 1] : null);
                    
                    
                }
                
                //если не удалось получить нмоер телефона из свойства заказа
                if (is_null($phone) || !Manager::getInstance()->isValidPhone($phone)) {
                    // то пытаемся взять номер телефона из свойства заказа которое отмечено как телефон
                    if (!is_null($phoneDefault) && Manager::getInstance()->isValidPhone($phoneDefault)) {
                        $phone = $phoneDefault;
                    }
                }
                
                $arOrderData['PHONE'] = Manager::getInstance()->getPreparePhone($phone);
                
                
                //доставка
                $arTrackNumber = array();
                $arOrderData['DELIVERY_NAME'] = '';
                $arOrderData['SHIPMENT_STATUS_ID'] = false;
                $shipmentCollection = $order->getShipmentCollection();
                foreach ($shipmentCollection as $shipment) {
                    
                    /**
                     * @var \Bitrix\Sale\Shipment $shipment
                     */
                    
                    if ($shipment->isSystem()) {
                        continue;
                    }
                    
                    $arOrderData['DELIVERY_NAME'] = $shipment->getDeliveryName();
                    
                    if (strlen(trim($track = $shipment->getField('TRACKING_NUMBER'))) > 0) {
                        $arTrackNumber[] = $track;
                    }
                    
                    if (!$arOrderData['SHIPMENT_STATUS_ID']) {
                        $arOrderData['SHIPMENT_STATUS_ID'] = $shipment->getField('STATUS_ID');
                    }
                }
                
                array_unique($arTrackNumber);
                
                $arOrderData['TRACKING_NUMBER'] = implode(', ', $arTrackNumber);
                
                
                //оплата
                $arOrderData['PAY_SYSTEM_NAME'] = '';
                $paymentCollection = $order->getPaymentCollection();
                /** @var \Bitrix\Sale\Payment $payment */
                foreach ($paymentCollection as $payment) {
                    $arOrderData['PAY_SYSTEM_NAME'] = $payment->getPaymentSystemName();
                }
                
                
                $dbrOrderStatus = \Bitrix\Sale\Internals\StatusTable::getList(array(
                    'select' => array(
                        'ID',
                        'NAME' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME'
                    ),
                    'filter' => array(
                        '=TYPE' => \Bitrix\Sale\OrderStatus::TYPE,
                        '=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => LANGUAGE_ID
                    ),
                    'order' => array('SORT'),
                ));
                while ($arOrderStatusItem = $dbrOrderStatus->fetch()) {
                    $arStatus[$arOrderStatusItem['ID']] = $arOrderStatusItem['NAME'];
                }
                
                
                $arOrderData['ORDER_STATUS_NAME'] = (isset($arStatus[$arOrderData['STATUS_ID']]) ? $arStatus[$arOrderData['STATUS_ID']] : $arOrderData['STATUS_ID']);
                
                $dbrShipmentStatus = \Bitrix\Sale\Internals\StatusTable::getList(array(
                    'select' => array(
                        'ID',
                        'NAME' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME'
                    ),
                    'filter' => array(
                        '=TYPE' => \Bitrix\Sale\DeliveryStatus::TYPE,
                        '=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => LANGUAGE_ID
                    ),
                    'order' => array('SORT'),
                ));
                while ($arShipmentStatusItem = $dbrShipmentStatus->fetch()) {
                    $arShipmentStatuses[$arShipmentStatusItem['ID']] = $arShipmentStatusItem['NAME'];
                }
                
                $arOrderData['ORDER_SHIPMENT_STATUS_NAME'] = (isset($arShipmentStatuses[$arOrderData['SHIPMENT_STATUS_ID']]) ? $arShipmentStatuses[$arOrderData['SHIPMENT_STATUS_ID']] : $arOrderData['SHIPMENT_STATUS_ID']);
                
                // стоимость заказа
                $orderPriceRound = intval(Manager::getInstance()->getParam('HANDLER.ORDER_PRICE_ROUND', '4', $arOrderData['SITE_ID']));
                $orderPriceRound = ($orderPriceRound >= 0 ? $orderPriceRound : 2);
                
                $arOrderData['PRICE_REST'] = number_format($arOrderData['PRICE'] - $arOrderData['SUM_PAID'], $orderPriceRound, '.', ' ');
                $arOrderData['PRICE'] = number_format($arOrderData['PRICE'], $orderPriceRound, '.', ' ');
                $arOrderData['SUM_PAID'] = number_format($arOrderData['SUM_PAID'], $orderPriceRound, '.', ' ');
                
                $arOrderData['PRICE_DELIVERY'] = number_format($arOrderData['PRICE_DELIVERY'], $orderPriceRound, '.', ' ');
                $arOrderData['DISCOUNT_VALUE'] = number_format($arOrderData['DISCOUNT_VALUE'], $orderPriceRound, '.', ' ');
                
                
                //событие подготовки данных по заказу -----------
                $event = new \Bitrix\Main\Event(self::$module_id, "OnPreparedOrderData", $arOrderData);
                $event->send();
                if ($event->getResults()) {
                    foreach ($event->getResults() as $evenResult) {
                        if ($evenResult->getResultType() == \Bitrix\Main\EventResult::SUCCESS) {
                            $arOrderMod = $evenResult->getParameters();
                            $arModified = array_diff($arOrderMod, $arOrderData);
                            if (!empty($arModified)) {
                                $arOrderData = array_replace($arOrderData, $arModified);
                            }
                        }
                    }
                }
                
                return $arOrderData;
            }
            
            
            return null;
            
        }
        
        
        /**
         * Возвращает массив данных для передачи в шаблон связанный с заказами стандартного модуля -  интернет-магазин
         *
         * @param $ORDER_ID
         *
         * @return array|bool|null
         * @throws \Bitrix\Main\LoaderException
         */
        public static function getOrderData($ORDER_ID, $bRealId = false)
        {
            if (!Loader::includeModule('sale')) {
                return null;
            }
            
            if (class_exists('\Bitrix\Sale\OrderStatus') && class_exists('\Bitrix\Sale\Order')) {
                return self::getOrderDataD7($ORDER_ID, $bRealId);
            }
            
            
            $oOrder = new \CSaleOrder();
            $oDelivery = new \CSaleDelivery();
            $oDeliveryHandler = new \CSaleDeliveryHandler();
            $oPaySystem = new \CSalePaySystem();
            $oOrderPropValue = new \CSaleOrderPropsValue();
            $oOrderStatus = new \CSaleStatus();
            
            
            // есо используются не реальные номера
            if (!$bRealId && strlen(trim(\Bitrix\Main\Config\Option::get('sale', 'account_number_template', ''))) > 0) {
                if ($orderDat = $oOrder->GetList(array(), array(
                    'ACCOUNT_NUMBER' => $ORDER_ID
                ))->fetch()) {
                    $ORDER_ID = $orderDat['ID'];
                }
            }
            
            
            if ($arOrderData = $oOrder->GetByID($ORDER_ID)) {
                
                $arOrderData['ORDER_ID'] = $arOrderData['ID'];
                
                if (isset($arOrderData['ACCOUNT_NUMBER'])) {
                    $arOrderData['ORDER_ID'] = $arOrderData['ACCOUNT_NUMBER'];
                }
                
                //-------------------------------------------
                // определяем службу доставки
                $arOrderData['DELIVERY_NAME'] = '';
                if (count($arDeliveryTmp = explode(':', $arOrderData['DELIVERY_ID'])) == 2) {
                    // автоматизированная
                    $bDeliveryFindStop = false;
                    $dbrDelivery = $oDeliveryHandler->GetList(array(
                        'SORT' => 'ASC',
                        'NAME' => 'ASC'
                    ), array(
                        'SID' => $arDeliveryTmp[0]
                    ));
                    while (($arDelivery = $dbrDelivery->Fetch()) && !$bDeliveryFindStop) {
                        foreach ($arDelivery['PROFILES'] as $deliveryProfileKey => $arDeliveryProfile) {
                            if ($deliveryProfileKey == $arDeliveryTmp[1]) {
                                $bDeliveryFindStop = true;
                                $arOrderData['DELIVERY_NAME'] = $arDelivery['NAME'];
                            }
                        }
                    }
                } else {
                    $arDelivery = $oDelivery->GetByID(intval($arOrderData['DELIVERY_ID']));
                    if (isset($arDelivery['NAME'])) {
                        $arOrderData['DELIVERY_NAME'] = $arDelivery["NAME"];
                    }
                }
                
                // -----------------------------------------
                // Система оплаты
                $arPaySystem = $oPaySystem->GetByID(intval($arOrderData['PAY_SYSTEM_ID']), $arOrderData['PERSON_TYPE_ID']);
                $arOrderData['PAY_SYSTEM_NAME'] = (isset($arPaySystem['PSA_NAME']) ? $arPaySystem['PSA_NAME'] : '');
                
                
                
                //-------------------------------------------
                // Свойства заказа
                $dbrOrderPropValue = $oOrderPropValue->GetList(array(), array('ORDER_ID' => $arOrderData['ID']));
                while ($arOrderPropValue = $dbrOrderPropValue->Fetch()) {
                    $arOrderData['PROPERTY_VALUE_' . $arOrderPropValue['CODE']] = $arOrderPropValue['VALUE'];
                }
                
                // Статусы заказов
                $arOrderStatus = $oOrderStatus->GetByID($arOrderData['STATUS_ID']);
                $arOrderData['ORDER_STATUS_NAME'] = (isset($arOrderStatus['NAME']) ? $arOrderStatus['NAME'] : $arOrderData['STATUS_ID']);
                
                $arOrderData['ORDER_SHIPMENT_STATUS_NAME'] = $arOrderData['STATUS_ID'];
                
                
                // стоимость заказа
                $priceRoundDefault = Manager::getInstance()->getParam('HANDLER.ORDER_PRICE_ROUND', '4', $arOrderData['SITE_ID']);
                
                $arOrderData['PRICE_REST'] = number_format($arOrderData['PRICE'] - $arOrderData['SUM_PAID'], $priceRoundDefault, '.', ' ');
                $arOrderData['PRICE'] = number_format($arOrderData['PRICE'], $priceRoundDefault, '.', ' ');
                $arOrderData['SUM_PAID'] = number_format($arOrderData['SUM_PAID'], $priceRoundDefault, '.', ' ');
                $arOrderData['PRICE_DELIVERY'] = number_format($arOrderData['PRICE_DELIVERY'], $priceRoundDefault, '.', ' ');
                $arOrderData['DISCOUNT_VALUE'] = number_format($arOrderData['DISCOUNT_VALUE'], $priceRoundDefault, '.', ' ');
                
                
                return $arOrderData;
            }
            
            return null;
        }
        
        
        public function main_OnBeforeEventAdd($event, $lid, $arFields)
        {
            $site_id = $lid;
            
            $eventCheck = 'SALE_CHECK_PRINT';
            if (\Bitrix\Main\Loader::includeModule('sale') && class_exists('\Bitrix\Sale\Notify')) {
                $classNotify = new \ReflectionClass('\Bitrix\Sale\Notify');
                if ($eventCheckConst = $classNotify->getConstant('EVENT_ON_CHECK_PRINT_SEND_EMAIL')) {
                    $eventCheck = $eventCheckConst;
                }
            }
            
            //отправка смс привязанных к почтовым событиям
            $oManager = \BXmaker\SmsNotice\Manager::getInstance();
            $oManager->sendTemplateEmail($event, $arFields, $site_id);
            
            switch ($event) {
                // Добавлен заказ
                case 'SALE_NEW_ORDER':
                    {
                        self::sale_onEventOrderAdd(isset($arFields['ORDER_ID']) ? $arFields['ORDER_ID'] : $arFields['ORDER_REAL_ID']);
                        break;
                    }
                case $eventCheck:
                    {
                        self::sale_onEventOrderCheck($arFields);
                        break;
                    }
            }
        }
        
        
        /**
         * Отправка трекера отправления
         *
         */
        public function sale_OnShipmentTrackingNumberChange($shipment)
        {
            \Bitrix\Main\Loader::includeModule('sale');
            
            $changedFields = $shipment->getFields()->getChangedValues();
            $orderId = $shipment->getFields()->get('ORDER_ID');
            $trackId = $changedFields['TRACKING_NUMBER'];
            
            $arOrderData = Handler::getOrderDataD7($orderId, true);
            
            if (is_null($arOrderData)) {
                return true;
            }
            
            $arOrderData['TRACKING_NUMBER'] = $trackId;
            
            $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '1');
            $template_type = trim(self::getParam('HANDLER.ORDER_TRACKING_NUMBER_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
            
            if (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) && $template_type) {
                
                // подготовим поле
                $arOrderData['PHONE'] = $arOrderData['PHONE'];
                $arOrderData['ORDER_ID'] = $arOrderData['ACCOUNT_NUMBER'];
                
                //отправка
                Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
                
                //AddMessage2Log(' bxmaker.smsnotice  '. $template_type );
            } else {
                AddMessage2Log(' bxmaker.smsnotice ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
            }
        }
        
        /**
         * Смена статуса
         *
         */
        public function sale_OnSaleStatusOrderChange($order, $value, $value_old)
        {
            \Bitrix\Main\Loader::includeModule('sale');
            
            /**
             * @var $order \Bitrix\Sale\Order
             */
            
            
            // не отправляем одно и тоже повторно
            if (isset(self::$arTmpData['sale_OnSaleStatusOrder_' . $value . '_' . $order->getId()])) {
                return;
            }
            self::$arTmpData['sale_OnSaleStatusOrder_' . $value . '_' . $order->getId()] = true;
            
            
            /** @var \Bitrix\Sale\Order $order */
            
            if ($value != $value_old) {
                $arOrderData = Handler::getOrderDataD7($order->getId(), true);
                
                if (!is_null($arOrderData)) {
                    $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '1');
                    $template_type = trim(self::getParam('HANDLER.ORDER_STATUS_' . $value . '_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
               
                    
                    if (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) && $template_type) {
                        
                        //отправка
                        Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
                    } else {
                        AddMessage2Log(' bxmaker.smsnotice ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
                    }
                }
                
            }
        }
        
        /**
         * Отмена заказа
         *
         */
        public function sale_OnSaleOrderCanceled($order)
        {
            \Bitrix\Main\Loader::includeModule('sale');
            
            // не отправляем одно и тоже повторно
            if (isset(self::$arTmpData['sale_OnSaleCancelOrder' . $order->getId()])) {
                return;
            }
            self::$arTmpData['sale_OnSaleCancelOrder' . $order->getId()] = true;
            
            /** @var \Bitrix\Sale\Order $order */
            $arOrderData = Handler::getOrderDataD7($order->getId(), true);
            
            if (is_null($arOrderData)) {
                return true;
            }
            
            if ($arOrderData['CANCELED'] == 'N') {
                return true;
            }
            
            $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '1');
            $template_type = trim(self::getParam('HANDLER.ORDER_CANCELED_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
            
            if (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) && $template_type) {
                
                //отправка
                Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
            } else {
                AddMessage2Log(' bxmaker.smsnotice ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
            }
        }
        
        /**
         * Оплата заказа
         *
         */
        public function sale_OnSaleOrderPaid($order)
        {
            \Bitrix\Main\Loader::includeModule('sale');
            /** @var \Bitrix\Sale\Order $order */
            if ($order->isPaid()) {
                
                // не отправляем одно и тоже повторно
                if (isset(self::$arTmpData['sale_OnSalePayOrder' . $order->getId()])) {
                    return;
                }
                self::$arTmpData['sale_OnSalePayOrder' . $order->getId()] = true;
                
                
                /** @var \Bitrix\Sale\Order $order */
                $arOrderData = Handler::getOrderDataD7($order->getId(), true);
                
                if (is_null($arOrderData)) {
                    return true;
                }
                
                if ($arOrderData['PAYED'] != 'Y') {
                    return;
                }
                
                $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '1');
                $template_type = trim(self::getParam('HANDLER.ORDER_PAY_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                
                if (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) && $template_type) {
                    
                    //отправка
                    Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
                } else {
                    AddMessage2Log(' bxmaker.smsnotice ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
                }
            }
            
            
        }
        
        /**
         * Отправка чека
         *
         * @param $arFields
         *
         * @return bool
         */
        public static function sale_onEventOrderCheck($arFields)
        {
            
            if (!isset($arFields['ORDER_ID'])) {
                return false;
            }
            
            $ORDER_ID = $arFields['ORDER_ID'];
            
            if (strlen($ORDER_ID) > 0 && isset($arFields['CHECK_LINK']) && strlen($arFields['CHECK_LINK']) > 0) {
                
                
                //				// не отправляем одно и тоже повторно
                //				if (isset(self::$arTmpData['sale_onEventOrderCheck_' . $ORDER_ID])) {
                //					return;
                //				}
                //				self::$arTmpData['sale_onEventOrderCheck_' . $ORDER_ID] = true;
                
                
                $arOrderData = self::getOrderData($ORDER_ID);
                
                if (!is_null($arOrderData)) {
                    $arOrderData['CHECK_LINK'] = $arFields['CHECK_LINK'];
                    
                    $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');
                    
                    // проверяем указано ли поле в котором ранится номер телефона
                    $userPhoneField = Manager::getInstance()->getUserPhoneField($arOrderData['SITE_ID']);
                    $field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                    $template_type = trim(self::getParam('HANDLER.ORDER_CHECK_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
                    
                    
                    if ($field_phone && $template_type) {
                        
                        if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
                            // подготовим поле
                            $arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];
                            
                            Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
                        }
                    }
                }
                
            }
        }
        
        
        public function sale_OnSaleStatusShipmentChange($shipment, $value, $oldValue)
        {
            /**
             * @var $shipment \Bitrix\Sale\Shipment
             * @var $order    \Bitrix\Sale\Order
             */
            
            if ($value == $oldValue) {
                return true;
            }
            if (!$collection = $shipment->getCollection()) {
                return true;
            }
            if (!$order = $collection->getOrder()) {
                return true;
            }
            
            $arOrderData = Handler::getOrderDataD7($order->getId(), true);
            
            if (is_null($arOrderData)) {
                return true;
            }
            
            
            $persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '1');
            $template_type = trim(self::getParam('HANDLER.ORDER_SHIPMENT_STATUS_' . $value . '_TEMPLATE_TYPE_' . $persone_type, null, $arOrderData['SITE_ID']));
            
            if (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) && $template_type) {
                
                //отправка
                Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
            } else {
                AddMessage2Log(' bxmaker.smsnotice ERROR   ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrderData['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
            }
            
            
        }
        
        /**
         * Обработчик дя страницы заказа, выводящйи историю смс по номеру телефона
         *
         * @param \CAdminTabControlDrag $form
         *
         * @throws \Bitrix\Main\SystemException
         */
        function OnAdminTabControlBegin(&$form)
        {
            if ($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/sale_order_view.php") {
                $req = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                $orderId = intval($req->get('ID'));
                
                \Bxmaker\SmsNotice\Manager::getInstance()->addAdminPageCssJs();
                
                $form->tabs[] = array(
                    "DIV" => "my_edit",
                    "TAB" => Loc::getMessage('bxmaker.smsnotice.HANDLER.SMS_FROM.TITLE'),
                    "ICON" => "bxmaker_smsnotice",
                    "TITLE" => Loc::getMessage('bxmaker.smsnotice.HANDLER.SMS_FROM.DESCRIPTION'),
                    "CONTENT" => '
                        <tr valign="top"><td colspan="2"><div id="bxmaker-smsnotice-order-sms" class="bxmaker-smsnotice-order-sms" data-id="' . $orderId . '"></div></td></tr>
                        <script type="text/javascript" >
                            var BXmakerSmsnoticeAdminOrderPageJs = new BXmakerSmsnoticeAdminOrderPage("#bxmaker-smsnotice-order-sms", ' . \Bitrix\Main\Web\Json::encode(array('orderId' => $orderId)) . ');
                        </script>
                    '
                );
            }
        }
        
     
        
    }