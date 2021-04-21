<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Localization\Loc;

    Loc::loadMessages(__FILE__);


    /**
     * ������� ����� ��� ������������
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
         * ������ ������ ��� �������� � �������� �������
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
         * ���������� �������������� ���������
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
         * �������� 1�� ��� ���������
         *
         * @param       $phone    - ����� ��������
         * @param       $text     - �����
         * @param array $arParams - �������������� ����������
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        abstract public function send($phone, $text, $arParams = array());

        /**
         * �������� ������ ��� ���������,
         * ������� ������ ���� ��������� � �������, ����� ��������� ������� � �������� ��� �� �����
         * ���� �������� ��������� � ������� ����������� �� ������ �� ���  {messages => [smsId => smsStatusResult, ...]}
         *
         * @param array $arPack - ������ ��������� {'messages' =>  [ {phone: string, clientId : int, text : string, sender : string}, ... ]}
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        abstract public function sendPack($arPack);


        /**
         * ���������� ������ � ������ �������� ��� �������
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        abstract public function getBalance();


        /**
         * ���������� ���-���� ������������ �� ������ ���������,
         * ������������ � ��������� ��� ��������
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        public function notice()
        {
            return new \Bxmaker\SmsNotice\Result();
        }

        /**
         * �����, ���������� � ����������� ������� ���������
         *
         * @param $arSms - ������ ��������� {id => arSms}
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        abstract public function agent($arSms);

        /**
         * ������ ������ ����������� ��������� ����������� ��� ������ � ��� ��������, ����� ��� �����, ������ ����������� � ��
         *
         * @return array
         */
        abstract public function getParams();

        /**
         * �������� ������� ��� ��� ������, ������ ����� � � ����� ���� �������, ��� �������� ���������� �������         *
         *
         * @return string
         */
        abstract public function getDescription();

        /**
         * ������������ ������ � ��������� UTF-8, ���� ��������� ����� ����
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
         * ������������ ������ �� ��������� UTF-8  � ��������� �����
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
         * ������������ ������ � ��������� windows-1251, ���� ��������� ����� ����
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
         * ������������ ������ �� ��������� windows-1251  � ��������� �����
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
