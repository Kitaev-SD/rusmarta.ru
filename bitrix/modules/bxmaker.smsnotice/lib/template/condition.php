<?php
    
    namespace Bxmaker\SmsNotice\Template;
    
    use Bitrix\Main\Application;
    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Web\Json;
    
    Loc::loadMessages(__FILE__);
    
    
    class Condition
    {
        private $container = 'conditions';
        private $prefix = 'rule';
        private $separator = '__';
        
        public function __construct($parentContainer = 'conditions', $prefix = 'rule', $separator = '__')
        {
            $this->container = $parentContainer;
            $this->prefix = $prefix;
            $this->separator = $separator;
        }
        
        //-----------------------------------------------------------------------------------
        
        /**
         * Вывод блока с условиями
         *
         * @param bool $value
         */
        public function show($conditions = false)
        {
            \CUtil::InitJSCore('jquery');
            
            $arData = array(
                'container' => $this->container,
                'prefix' => $this->prefix,
                'separator' => $this->separator,
                'msg' => array(
                    'add_control' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.ADD_CONTROL'),
                    'condition_error' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.CONDITION_ERROR'),
                    'delete_control' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.DELETE_CONTROL'),
                    'select_control' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.SELECT_CONTROL'),
                ),
                'controls' => array(
                    CondGroup::getJsControl(),
                    CondDeliveryId::getJsControl(),
                    CondPaymentId::getJsControl(),
                    CondUrlId::getJsControl(),
                    CondTimeId::getJsControl(),
                    CondWeekdayId::getJsControl(),
                ),
                'conditions' => $conditions
            );
            
            
            ?>
            <script type="text/javascript">
                window.BXmakerSmsNoticeConditionData = <?=Json::encode($arData);?>
            </script>
            <?
        }
        
        
        //-----------------------------------------------------------------------------------
        
        /**
         * Парсинг условий из $_POST
         */
        public function parse()
        {
            $arReturn = array();
            
            $app = Application::getInstance();
            $req = $app->getContext()->getRequest();
            
            if ($rules = $req->getPost($this->prefix)) {
                $arRuleIndexToIndex = array();
                
                foreach ($rules as $ruleIndex => $rule) {
                    $indexPath = explode($this->separator, $ruleIndex);
                    
                    if (count($indexPath) <= 0) {
                        continue;
                    } elseif (count($indexPath) == 1) {
                        if ($result = $this->__parseCondition($rule)) {
                            $arReturn[] = $result;
                            $index = count($arReturn) - 1;
                            $arRuleIndexToIndex[$ruleIndex] =& $arReturn[$index];
                        }
                    } else {
                        if ($result = $this->__parseCondition($rule)) {
                            $key = implode($this->separator, array_slice($indexPath, 0, -1));
                            $arRuleIndexToIndex[$key]['childs'][] = $result;
                            $index = count($arRuleIndexToIndex[$key]['childs']) - 1;
                            $arRuleIndexToIndex[$ruleIndex] =& $arRuleIndexToIndex[$key]['childs'][$index];
                        }
                    }
                }
                unset($arRuleIndexToIndex, $rules, $ruleIndex, $rule, $indexPath, $index, $key);
            }
            
            
            return $arReturn;
        }
        
        
        /**
         * определеяем класс который отвечает за условия, парсинг и генерацию
         *
         * @param $cond
         *
         * @return bool
         */
        private function __parseCondition($cond)
        {
            if (isset($cond['controlId']) && class_exists(__NAMESPACE__ . '\\' . $cond['controlId'])) {
                $class = __NAMESPACE__ . '\\' . $cond['controlId'];
                return $class::parse($cond);
            }
            return false;
        }
        
        //-----------------------------------------------------------------------------------
        
        
        public function checkItemConditions($arConditions, $arItem)
        {
            if (empty($arConditions[0])) {
                return true;
            }
            
            $conditionEval = ConditionBase::generateConditionLevel($arConditions[0]);
            if (strlen(trim($conditionEval)) > 0) {
                $result = eval(" return (" . trim($conditionEval) . "); ");
                return $result;
            }
            
            return true;
        }
        
        
    }
    
    
    /**
     * Типы условий ----------------------------
     * Class CondCtrl
     *
     * @package Bxmaker\SmsNotice\Template
     */
    class CondCtrl
    {
        const LOGIC_EQUAL = 'equal';
        const LOGIC_NOT = 'not';
        const LOGIC_START = 'start';
        const LOGIC_END = 'end';
        const LOGIC_HAS = 'has';
        const LOGIC_LT = 'lt'; //less-than
        const LOGIC_LTE = 'lte'; //less-than-or-equal,
        const LOGIC_MT = 'mt';//more-than
        const LOGIC_MTE = 'mte';//more-than-or-equal,
        
        public static function getTypePrefix($name, $text)
        {
            return array(
                'type' => 'prefix',
                'name' => $name,
                'text' => $text
            );
        }
        
        /**
         * @param            $name
         * @param array|null $values
         *
         * @return array
         */
        public static function getTypeLogic($name, $values = null)
        {
            if (is_null($values)) {
                $values = array(
                    self::LOGIC_EQUAL => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.EQUAL'),
                    self::LOGIC_NOT => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.NOT_EQUAL')
                );
            }
            $keys = array_keys($values);
            return self::getTypeSelect($name, $values, reset($keys));
        }
        
        public static function getTypeInput($name, $defaultValue = '', $firstOption = '...')
        {
            return array(
                'type' => 'input',
                'name' => $name,
                'defaultValue' => $defaultValue,
                'first_option' => $firstOption
            );
        }
        
        public static function getTypeSelect($name, $values, $defaultValue = '', $firstOption = '...')
        {
            return array(
                'type' => 'select',
                'name' => $name,
                'values' => $values,
                'defaultText' => (!!$defaultValue && !!$values[$defaultValue] ? $values[$defaultValue] : $firstOption),
                'defaultValue' => $defaultValue,
                'first_option' => $firstOption
            );
        }
        
        /**
         * Проверка установленности дополительной логики агрегации
         *
         * @param $cond
         * @param $control
         *
         * @return bool
         */
        public function checkParseConditionControl($cond, $control)
        {
            switch ($control['type']) {
                case 'logic':
                case 'select':
                    {
                        if (!array_key_exists($control['name'], $cond)) {
                            return false;
                        }
                        if (!array_key_exists($cond[$control['name']], $control['values'])) {
                            return false;
                        }
                        return true;
                    }
                case 'input':
                case 'prefix':
                    {
                        return true;
                    }
            }
            
            return false;
        }
        
        /**
         * Получаем значение доп логики агрегации, после проверки checkParseConditionControl
         *
         * @param $cond
         * @param $control
         */
        public function getParseConditionControl($cond, $control)
        {
            $value = '';
            switch ($control['type']) {
                case 'logic':
                case 'select':
                    {
                        $name = $control['name'];
                        if (isset($cond[$name]) && isset($control['values'][$cond[$name]])) {
                            $value = $cond[$name];
                            
                        } elseif (!!$control['defaultValue'] && isset($control['values'][$control['defaultValue']])) {
                            $value = $control['defaultValue'];
                            
                        } else {
                            $value = current($control['values']);
                        }
                        break;
                    }
                case 'prefix':
                    {
                        $value = true;
                        break;
                    }
                case 'input':
                    {
                        return (isset($cond[$control['name']]) ? trim($cond[$control['name']]) : '');
                        break;
                    }
            }
            return $value;
            
        }
        
        
    }
    
    /*
     * Бзовый класс условий
     */
    
    class ConditionBase
    {
        public static function getJsControl()
        {
            return false;
        }
        
        public static function parse($cond)
        {
            $arReturn = array();
            
            $arJsControl = static::getJsControl();
            
            do {
                $arReturn = array(
                    'controlId' => $cond['controlId'],
                );
                
                if (isset($arJsControl['control'])) {
                    foreach ($arJsControl['control'] as $control) {
                        
                        if (!CondCtrl::checkParseConditionControl($cond, $control)) {
                            return false;
                        } else {
                            $arReturn[$control['name']] = CondCtrl::getParseConditionControl($cond, $control);
                        }
                    }
                }
                $arReturn['childs'] = array();
                
                return $arReturn;
            } while (false);
            
            return false;
        }
        
        public static function generateConditionLevel($cond)
        {
            if (isset($cond['controlId']) && class_exists(__NAMESPACE__ . '\\' . $cond['controlId'])) {
                $class = __NAMESPACE__ . '\\' . $cond['controlId'];
                return $class::generateConditionEval($cond);
            }
            return false;
        }
        
        public static function generateConditionEval($cond)
        {
            return false;
        }
    }
    
    /**
     * Группа условий ----------------------------
     */
    class CondGroup extends ConditionBase
    {
        
        const controlId = 'CondGroup';
        
        public static function getJsControl()
        {
            return array(
                'controlId' => static::controlId,
                'defaultText' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.SELECT_VALUE'),
                'label' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.GROUP_CONDITION'),
                'group' => true,
                'showIn' => array(static::controlId),
                'control' => array(
                    CondCtrl::getTypeSelect('aggregator', array(
                        'and' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.ALL_CONDITION'),
                        'or' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.ONE_OF_CONDITION')
                    ), 'and', false),
                    CondCtrl::getTypeSelect('type', array(
                        'true' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.CONDITIONS_TRUE'),
                        'false' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.CONDITIONS_FALSE')
                    ), 'true', false)
                ),
                'visual' => array(
                    'controls' => array(
                        'aggregator',
                        'type'
                    ),
                    'logic' => array(
                        array(
                            'style' => "condition-logic-and",
                            'message' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.CONDITIONS_LOGIC_AND'),
                        ),
                        array(
                            'style' => "condition-logic-and",
                            'message' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.CONDITIONS_LOGIC_AND_NO'),
                        ),
                        array(
                            'style' => "condition-logic-or",
                            'message' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.CONDITIONS_LOGIC_OR'),
                        ),
                        array(
                            'style' => "condition-logic-or",
                            'message' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.CONDITIONS_LOGIC_OR_NO'),
                        ),
                    ),
                    'values' => array(
                        array(
                            'aggregator' => 'and',
                            'type' => 'true',
                        ),
                        array(
                            'aggregator' => 'and',
                            'type' => 'false',
                        ),
                        array(
                            'aggregator' => 'or',
                            'type' => 'true',
                        ),
                        array(
                            'aggregator' => 'or',
                            'type' => 'false',
                        )
                    )
                )
            );
        }
        
        public static function generateConditionEval($cond)
        {
            $arResult = array();
            
            if (count($cond['childs'])) {
                foreach ($cond['childs'] as $child) {
                    if ($str = static::generateConditionLevel($child)) {
                        $arResult[] = '(' . trim($str) . ')';
                    }
                }
            }
            
            if (empty($arResult)) {
                return false;
            } else {
                $logic = ($cond['type'] == 'true' ? '' : '!');
                return $logic . implode(' ' . $cond['aggregator'] . ' ' . $logic, $arResult);
            }
        }
        
    }
    
    /**
     * Условия по способу доставки -------------------
     */
    class CondDeliveryId extends ConditionBase
    {
        const controlId = 'CondDeliveryId';
        
        public static function getJsControl()
        {
            return array(
                'controlId' => static::controlId,
                'defaultText' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.SELECT_VALUE'),
                'label' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.DELIVERY_METHOD'),
                'group' => false,
                'showIn' => array(CondGroup::controlId),
                'control' => array(
                    CondCtrl::getTypePrefix('prefix', Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.DELIVERY_METHOD')),
                    CondCtrl::getTypeLogic('logic'),
                    CondCtrl::getTypeSelect('value', self::getDeliveryList())
                )
            );
        }
        
        public function getDeliveryList()
        {
            static $arReturn;
            
            if (!isset($arReturn)) {
                $arReturn = array();
                
                if (Loader::includeModule('sale')) {
                    
                    $res = \Bitrix\Sale\Delivery\Services\Table::getList(array(
                        'filter' => array('ACTIVE' => 'Y'),
                        'select' => array(
                            'ID',
                            'NAME'
                        )
                    ));
                    while ($item = $res->Fetch()) {
                        $arReturn[$item['ID']] = '[' . $item['ID'] . '] ' . $item['NAME'];
                    }
                }
            }
            
            
            return $arReturn;
        }
        
        public static function generateConditionEval($cond)
        {
            //fix
            if (is_array($cond['value'])) {
                $cond['value'] = reset($cond['value']);
            }
            $value = \Bitrix\Main\Text\BinaryString::changeCaseToLower($cond['value']);
            $value = intval($value);
            
            switch ($cond['logic']) {
                case CondCtrl::LOGIC_EQUAL:
                    {
                        return '(intval($arItem["DELIVERY_ID"])  === ' . $value . ')';
                        break;
                    }
                case CondCtrl::LOGIC_NOT:
                    {
                        return '(intval($arItem["DELIVERY_ID"])  !== ' . $value . ')';
                        break;
                    }
            }
            
            return false;
        }
        
    }
    
    
    /**
     * Фильтрация по url
     * Class CondUrlId
     *
     * @package Bxmaker\SmsNotice\Template
     */
    class CondUrlId extends ConditionBase
    {
        const controlId = 'CondUrlId';
        
        public static function getJsControl()
        {
            return array(
                'controlId' => static::controlId,
                'defaultText' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.SELECT_VALUE'),
                'label' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.URL_CONDITION_TITLE'),
                'group' => false,
                'showIn' => array(CondGroup::controlId),
                'control' => array(
                    CondCtrl::getTypePrefix('prefix', Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.URL_CONDITION_TITLE')),
                    CondCtrl::getTypeLogic('logic', array(
                        CondCtrl::LOGIC_EQUAL => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.EQUAL'),
                        CondCtrl::LOGIC_NOT => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.NOT_EQUAL'),
                        CondCtrl::LOGIC_START => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.START'),
                        CondCtrl::LOGIC_HAS => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.HAS'),
                    )),
                    CondCtrl::getTypeInput('value')
                )
            );
        }
        
        
        public static function generateConditionEval($cond)
        {
            $value = \Bitrix\Main\Text\BinaryString::changeCaseToLower($cond['value']);
            $value = str_replace('"', '\"', $value);
            
            switch ($cond['logic']) {
                case CondCtrl::LOGIC_EQUAL:
                    {
                        return '($arItem["CURRENT_PAGE_URL"]  === "' . $value . '") or ($arItem["CURRENT_PAGE_URL_FULL"]  === "' . $value . '") ';
                        break;
                    }
                case CondCtrl::LOGIC_NOT:
                    {
                        return '($arItem["CURRENT_PAGE_URL"]  !== "' . $value . '")  or ($arItem["CURRENT_PAGE_URL_FULL"]  !== "' . $value . '") ';
                        break;
                    }
                case CondCtrl::LOGIC_START:
                    {
                        return '(\Bitrix\Main\Text\BinaryString::getPositionIgnoreCase($arItem["CURRENT_PAGE_URL"],"' . $value . '") === 0)  or (\Bitrix\Main\Text\BinaryString::getPositionIgnoreCase($arItem["CURRENT_PAGE_URL_FULL"],"' . $value . '")  === 0)';
                        break;
                    }
                case CondCtrl::LOGIC_HAS:
                    {
                        return '(\Bitrix\Main\Text\BinaryString::getPositionIgnoreCase($arItem["CURRENT_PAGE_URL"],"' . $value . '") !== false)  or (\Bitrix\Main\Text\BinaryString::getPositionIgnoreCase($arItem["CURRENT_PAGE_URL_FULL"],"' . $value . '")  !== false)';
                        break;
                    }
            }
            
            return false;
        }
        
    }
    
    /**
     * Фильтрация по времени
     * Class CondTimeId
     *
     * @package Bxmaker\SmsNotice\Template
     */
    class CondTimeId extends ConditionBase
    {
        const controlId = 'CondTimeId';
        
        public static function getJsControl()
        {
            return array(
                'controlId' => static::controlId,
                'defaultText' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.SELECT_VALUE'),
                'label' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.TIME_CONDITION_TITLE'),
                'group' => false,
                'showIn' => array(CondGroup::controlId),
                'control' => array(
                    CondCtrl::getTypePrefix('prefix', Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.TIME_CONDITION_TITLE')),
                    CondCtrl::getTypeLogic('logic', array(
                        'lt' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.LESS_THAN'),
                        'lte' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.LESS_THAN_EQUAL'),
                        'mt' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.MORE_THAN'),
                        'mte' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.MORE_THAN_EQUAL'),
                    )),
                    CondCtrl::getTypeInput('value')
                )
            );
        }
        
        
        public static function generateConditionEval($cond)
        {
            $value = \Bitrix\Main\Text\BinaryString::changeCaseToLower($cond['value']);
            $value = str_replace(':', '', $value);
            $time = date('Hi');
            
            switch ($cond['logic']) {
                case CondCtrl::LOGIC_LT:
                    {
                        return '(' . $time . '  < ' . $value . ')';
                        break;
                    }
                case CondCtrl::LOGIC_LTE:
                    {
                        return '(' . $time . '  <= ' . $value . ')';
                        break;
                    }
                case CondCtrl::LOGIC_MT:
                    {
                        return '(' . $time . '  > ' . $value . ')';
                        break;
                    }
                case CondCtrl::LOGIC_MTE:
                    {
                        return '(' . $time . '  >= ' . $value . ')';
                        break;
                    }
            }
            
            return false;
        }
        
    }
    
    /**
     * Фильтрация по дню недели
     * Class CondTimeId
     *
     * @package Bxmaker\SmsNotice\Template
     */
    class CondWeekdayId extends ConditionBase
    {
        const controlId = 'CondWeekdayId';
        
        public static function getJsControl()
        {
            return array(
                'controlId' => static::controlId,
                'defaultText' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.SELECT_VALUE'),
                'label' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_TITLE'),
                'group' => false,
                'showIn' => array(CondGroup::controlId),
                'control' => array(
                    CondCtrl::getTypePrefix('prefix', Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_TITLE')),
                    CondCtrl::getTypeLogic('logic', array(
                        CondCtrl::LOGIC_EQUAL => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.EQUAL'),
                        CondCtrl::LOGIC_NOT => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_CTRL.NOT_EQUAL'),
                    )),
                    //                    CondCtrl::getTypeInput('value')
                    CondCtrl::getTypeSelect('value', array(
                        1 => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_1'),
                        2 => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_2'),
                        3 => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_3'),
                        4 => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_4'),
                        5 => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_5'),
                        6 => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_6'),
                        7 => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.WEEKDAY_CONDITION_7'),
                    ))
                )
            );
        }
        
        
        public static function generateConditionEval($cond)
        {
            $value = \Bitrix\Main\Text\BinaryString::changeCaseToLower($cond['value']);
            $value = str_replace(':', '', $value);
            $day = date('w'); //1234560
            if ($day < 1) {
                $day = 7;
            }
            
            switch ($cond['logic']) {
                case CondCtrl::LOGIC_EQUAL:
                    {
                        return '(' . $day . '  === ' . $value . ')';
                        break;
                    }
                case CondCtrl::LOGIC_NOT:
                    {
                        return '(' . $day . '  !== ' . $value . ')';
                        break;
                    }
            }
            
            return false;
        }
        
    }
    
    /**
     * Условия по способу оплаты -------------------
     */
    class CondPaymentId extends ConditionBase
    {
        const controlId = 'CondPaymentId';
        
        public static function getJsControl()
        {
            return array(
                'controlId' => static::controlId,
                'defaultText' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.SELECT_VALUE'),
                'label' => Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.PAYMENT_METHOD'),
                'group' => false,
                'showIn' => array(CondGroup::controlId),
                'control' => array(
                    CondCtrl::getTypePrefix('prefix', Loc::getMessage('bxmaker.smsnotice_tpl_cond.COND_BASE.PAYMENT_METHOD')),
                    CondCtrl::getTypeLogic('logic'),
                    CondCtrl::getTypeSelect('value', self::getPaymentList())
                )
            );
        }
        
        public static function getPaymentList()
        {
            static $arReturn;
            
            if (!isset($arReturn)) {
                $arReturn = array();
                
                if (Loader::includeModule('sale')) {
                    
                    $res = \Bitrix\Sale\PaySystem\Manager::getList(array(
                        'filter' => array('ACTIVE' => 'Y'),
                        'select' => array(
                            'ID',
                            'NAME'
                        )
                    ));
                    while ($item = $res->Fetch()) {
                        $arReturn[$item['ID']] = '[' . $item['ID'] . '] ' . $item['NAME'];
                    }
                }
            }
            
            
            return $arReturn;
        }
        
        public static function generateConditionEval($cond)
        {
            $value = \Bitrix\Main\Text\BinaryString::changeCaseToLower($cond['value']);
            $value = intval($value);
            
            switch ($cond['logic']) {
                case CondCtrl::LOGIC_EQUAL:
                    {
                        return '(intval($arItem["PAY_SYSTEM_ID"])  === ' . $value . ')';
                        break;
                    }
                case CondCtrl::LOGIC_NOT:
                    {
                        return '(intval($arItem["PAY_SYSTEM_ID"])  !== ' . $value . ')';
                        break;
                    }
            }
            
            return false;
        }
        
    }
    
    


