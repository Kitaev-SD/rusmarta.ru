<?

    namespace Bxmaker\SmsNotice;

    use Bitrix\Main\Application;
    use \Bitrix\Main\Entity;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Type\DateTime;
    use Bitrix\Main\Loader;


    Class LogTable extends Entity\DataManager
    {

        public static
        function getFilePath()
        {
            return __FILE__;
        }

        public static
        function getTableName()
        {
            return 'bxmaker_smsnotice_log';
        }

        public static
        function getMap()
        {
            return array(
                new Entity\IntegerField('ID', array(
                    'primary' => true,
                    'autocomplete' => true
                )),
                new Entity\IntegerField('PID', array()), // pid процесса, для отслеживания лога одного процесса если нужна
                new Entity\DatetimeField('DATE_INSERT'), // время вставки
                new Entity\StringField('TEXT', array( // текст ошибки
                    'required' => true,

                )),
                new Entity\StringField('SITE_ID', array( // сайт
                    'required' => true
                )),
                new Entity\StringField('TYPE', array( //тип ошибки
                    'default' => 'DEFAULT'
                )),
                new Entity\ExpressionField('CNT', 'COUNT(ID)')
            );
        }

        public static function onBeforeAdd(Entity\Event $event)
        {
            $result = new Entity\EventResult;
            $data = $event->getParameter("fields");

            $result->modifyFields(array('PID' => getmypid(), 'DATE_INSERT' => new \Bitrix\Main\Type\DateTime()));
            return $result;
        }

        /**
         * Удаление всех записей
         */
        public static function deleteAll()
        {
            \Bitrix\Main\Application::getConnection()->query('DELETE FROM ' . self::getTableName());
        }

    }