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
                new Entity\IntegerField('PID', array()), // pid ��������, ��� ������������ ���� ������ �������� ���� �����
                new Entity\DatetimeField('DATE_INSERT'), // ����� �������
                new Entity\StringField('TEXT', array( // ����� ������
                    'required' => true,

                )),
                new Entity\StringField('SITE_ID', array( // ����
                    'required' => true
                )),
                new Entity\StringField('TYPE', array( //��� ������
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
         * �������� ���� �������
         */
        public static function deleteAll()
        {
            \Bitrix\Main\Application::getConnection()->query('DELETE FROM ' . self::getTableName());
        }

    }