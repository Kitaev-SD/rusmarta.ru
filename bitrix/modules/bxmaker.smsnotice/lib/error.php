<?

namespace Bxmaker\SmsNotice;

/**
 * Класс описания ошибки
 *
 * @package Bxmaker\SmsNotice
 */
class Error {

    /**
     * @var string Текст ошибки
     */
    private $message = '';
    /**
     * @var string Код ошибки
     */
    private $code = '';
    /**
     * @var array Дополнительные даныне по ошибке
     */
    private $arMore = array();

    /**
     * Конструктор объекта ошибки
     *
     * @param        $message
     * @param string $code
     * @param array  $arMore
     */
    public function __construct($message, $code = '', $arMore = array()){
        $this->message = $message;
        $this->code = $code;
        $this->arMore = $arMore;
    }

    /**
     * Вернет текст ошибки
     * @return string
     */
    public function getMessage(){
        return $this->message;
    }

    /**
     * Вернет код ошибки
     * @return string
     */
    public function getCode(){
        return $this->code;
    }

    /**
     * Вернет дополнительные данные по ошибке,
     * по умолчанию возвращается весь массив данных,
     * но если указать ключ, то верентся только данные по нему
     *
     * @param null $name -  ключ по которому необходимо вренуть данных из дополнительного описания
     *
     * @return array|mixed|null
     */
    public function getMore($name = null){
        if(!is_null($name))
        {
            return (isset($this->arMore[$name]) ? $this->arMore[$name] : null);
        }
        return $this->arMore;
    }
}