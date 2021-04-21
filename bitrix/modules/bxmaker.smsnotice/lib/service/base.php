<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Localization\Loc;

    Loc::loadMessages(__FILE__);


    /**
     * Базовый класс для наследования
     * Class Base
     *
     * @package Bxmaker\SmsNotice\Service
     */
    abstract Class Base
    {

        /**
         * @var \Bitrix\Main\Web\HttpClient
         */
        protected $oHttpClient = null;

        public function __construct($arParams)
        {

        }

        /**
         * Вренет объект для запросов к внешнему сервису
         *
         * @return \Bitrix\Main\Web\HttpClient
         */
        public function getHttpClient()
        {
            if (is_null($this->oHttpClient)) {
                $this->oHttpClient = new \Bitrix\Main\Web\HttpClient();
            }
            return $this->oHttpClient;
        }

        /**
         * Возвращает языкозависимое сообщение
         *
         * @param       $name
         * @param array $arReplace
         *
         * @return string
         */
        public function getMessage($name, $arReplace = array())
        {
            return Loc::getMessage('bxmaker.smsnotice.service.' . $name, $arReplace);
        }


        /**
         * Отправка 1го смс сообщения
         *
         * @param       $phone    - номер телефона
         * @param       $text     - текст
         * @param array $arParams - дополнительные параметеры
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        abstract public function send($phone, $text, $arParams = array());

        /**
         * Отправка пакета смс сообщений,
         * вренуть должен либо результат с ошибкой, тогда изменений никаких в статусах смс не будет
         * либо успешный результат с масивом результатов по каждой из смс  {messages => [smsId => smsStatusResult, ...]}
         *
         * @param array $arPack - массив сообщений {'messages' =>  [ {phone: string, clientId : int, text : string, sender : string}, ... ]}
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        abstract public function sendPack($arPack);


        /**
         * Возвращает баланс в личном кабинете смс сервиса
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        abstract public function getBalance();


        /**
         * Обработчик веб-хука оповещающего ос татусе сообщения,
         * исопльзуется в некоторых смс сервисах
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        public function notice()
        {
            return new \Bxmaker\SmsNotice\Result();
        }

        /**
         * Агент, проверящий и обновляющий статусы сообщений
         *
         * @param $arSms - массив сообщений {id => arSms}
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        abstract public function agent($arSms);

        /**
         * Вернет массив описывающий парамтеры необходимые дял работы с смс сервисом, такие как логин, пароль отправитель и тп
         *
         * @return array
         */
        abstract public function getParams();

        /**
         * Описание сервиса что гда нажать, откуда взять и в какое поле занести, для страницы параметров сервиса         *
         *
         * @return string
         */
        abstract public function getDescription();

        /**
         * Конвертирует данные в кодировку UTF-8, если кодировка сайта иная
         *
         * @param $data
         *
         * @return mixed
         */
        protected function toUtf($data)
        {
            if (\Bxmaker\SmsNotice\Manager::getInstance()->getBase()->isUtf()) {
                return $data;
            } else {
                return \Bitrix\Main\Text\Encoding::convertEncoding($data, LANG_CHARSET, "UTF-8");
            }
        }

        /**
         * Конвертирует данные из кодировки UTF-8  в кодировку сайта
         *
         * @param $data
         *
         * @return mixed
         */
        protected function fromUtf($data)
        {
            if (\Bxmaker\SmsNotice\Manager::getInstance()->getBase()->isUtf()) {
                return $data;
            } else {
               return \Bitrix\Main\Text\Encoding::convertEncoding($data, "UTF-8", LANG_CHARSET);
            }
        }

        /**
         * Конвертирует данные в кодировку windows-1251, если кодировка сайта иная
         *
         * @param $data
         *
         * @return mixed
         */
        protected function toWindows1251($data)
        {
            if (!\Bxmaker\SmsNotice\Manager::getInstance()->getBase()->isUtf()) {
                return $data;
            } else {
               return \Bitrix\Main\Text\Encoding::convertEncoding($data, LANG_CHARSET, 'windows-1251');
            }
        }

        /**
         * Конвертирует данные из кодировки windows-1251  в кодировку сайта
         *
         * @param $data
         *
         * @return mixed
         */
        protected function fromWindows1251($data)
        {
            if (!\Bxmaker\SmsNotice\Manager::getInstance()->getBase()->isUtf()) {
                return $data;
            } else {
               return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'windows-1251', LANG_CHARSET);
            }
        }


    }
