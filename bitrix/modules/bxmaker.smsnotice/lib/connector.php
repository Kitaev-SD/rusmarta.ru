<?php
    
    namespace Bxmaker\SmsNotice;
    
    use Bitrix\Main\Error;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\ModuleManager;
    use Bitrix\Main\Result;
    use Bitrix\Main\Web\HttpClient;
    use Bitrix\Main\Web\Json;
    
    use Bitrix\MessageService\Sender;
    use Bitrix\MessageService\Sender\Result\MessageStatus;
    use Bitrix\MessageService\Sender\Result\SendMessage;
    
    use Bitrix\MessageService;
    
    Loc::loadMessages(__FILE__);
    
    
    class Connector extends \Bitrix\MessageService\Sender\BaseConfigurable
    {
        public static function onGetSmsSenders()
        {
            return [new self()];
        }
        
        public function getId()
        {
            return 'bxmaker.smsnotice';
        }
        
        public function getName()
        {
            return Loc::getMessage('bxmaker.smsnotice.connector.name');
        }
        
        public function getShortName()
        {
            return $this->getName();
        }
        
        public function getFromList()
        {
            $list = array();
            $list[] = array(
                'id' => 'bxmaker_default',
                'name' => Loc::getMessage('bxmaker.smsnotice.connector.sender'),
                'description' => Loc::getMessage('bxmaker.smsnotice.connector.sender'),
            );
            return $list;
        }
        
        public function getDefaultFrom()
        {
            return null;
        }
        
        public function setDefaultFrom($from)
        {
            return $this;
        }
        
        public function isRegistered()
        {
            return true;
        }
        
        public function register(array $fields)
        {
            return new \Bitrix\Main\Result();
        }
        
        /**
         * @return array [
         *    'login' => ''
         * ]
         */
        public function getOwnerInfo()
        {
            return array();
        }
        
        public function getExternalManageUrl()
        {
            return 'https://bxmaker.ru/doc/smsnotice/';
        }
        
        public function sendMessage(array $messageFields)
        {
            if (!$this->canUse()) {
                $result = new SendMessage();
                $result->addError(new Error('Service is unavailable'));
                return $result;
            }
            
            $result = new SendMessage();
            
            $resultRequest = \Bxmaker\SmsNotice\Manager::getInstance()->send(
                str_replace('+', '', $messageFields['MESSAGE_TO']),
                $messageFields['MESSAGE_BODY']
            );
            if ($resultRequest->isSuccess()) {
                $result->setExternalId($resultRequest->getMore('smsId'));
                $result->setAccepted();
                
            } else {
                $arErrors = array();
                $errors = $resultRequest->getErrors();
                foreach ($errors as $error) {
                    /**
                     * @var \Bxmaker\SmsNotice\Error $error
                     */
                    $arErrors[] = new \Bitrix\Main\Error($error->getMessage(), $error->getCode(), $error->getMore());
                }
                $result->addErrors($arErrors);
            }
            
            return $result;
        }
        
        public function getMessageStatus(array $messageFields)
        {
            $result = new MessageStatus();
            $result->setId($messageFields['ID']);
            $result->setExternalId($messageFields['EXTERNAL_ID']);
            
            if (!$this->canUse()) {
                $result->addError(new Error('Service is unavailable'));
                return $result;
            }
            
            $arSms = \Bxmaker\SmsNotice\Manager::getInstance()->getTable()->getById($result->getExternalId())->fetch();
            if ($arSms) {
                switch ($arSms['STATUS']) {
                    case \Bxmaker\SmsNotice\SMS_STATUS_SENT:
                        {
                            $result->setStatusText(Loc::getMessage('bxmaker.smsnotice.connector.status.sent'));
                            $result->setStatusCode(\Bitrix\MessageService\MessageStatus::SENT);
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED:
                        {
                            $result->setStatusText(Loc::getMessage('bxmaker.smsnotice.connector.status.delivered'));
                            $result->setStatusCode(\Bitrix\MessageService\MessageStatus::DELIVERED);
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_ERROR:
                        {
                            $result->setStatusText(Loc::getMessage('bxmaker.smsnotice.connector.status.error'));
                            $result->setStatusCode(\Bitrix\MessageService\MessageStatus::ERROR);
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_WAIT:
                        {
                            $result->setStatusText(Loc::getMessage('bxmaker.smsnotice.connector.status.wait'));
                            $result->setStatusCode(\Bitrix\MessageService\MessageStatus::QUEUED);
                            break;
                        }
                }
            } else {
                $result->addErrors(new \Bitrix\Main\Error(Loc::getMessage('bxmaker.smsnotice.connector.status.no_found_sms')));
            }
            
            return $result;
        }
        
        public static function resolveStatus($serviceStatus)
        {
            $status = parent::resolveStatus($serviceStatus);
            
            switch ($serviceStatus) {
                case 'seen':
                    return MessageService\MessageStatus::READ;
                    break;
                case 'delivered':
                    return MessageService\MessageStatus::DELIVERED;
                    break;
                case 'accepted':
                    return MessageService\MessageStatus::ACCEPTED;
                    break;
                case 'enrouted':
                    return MessageService\MessageStatus::SENT;
                    break;
                case 'undeliverable':
                case 'expired':
                case 'deleted':
                    return MessageService\MessageStatus::UNDELIVERED;
                    break;
                case 'reject':
                case 'notsent':
                case 'textblacklist':
                case 'noviber':
                case 'blocked':
                case 'unknown':
                case 'nostatus':
                    return MessageService\MessageStatus::FAILED;
                    break;
            }
            
            return $status;
        }
        
        public function sync()
        {
            if ($this->isRegistered()) {
                $this->getFromList();
            }
            return $this;
        }
        
    }