<?

namespace Bxmaker\SmsNotice;

class Result {

    private $arErrors = array();
    private $result = null;
    private $arMore = array();

    public function __construct($result = null, $arMore = array()){
        if($result instanceof Error )
        {
            $this->setError($result);
            return;
        }
        $this->result = $result;
        $this->arMore = $arMore;

        return $this;
    }

    /**
     * Проверка успешности операции
     * @return bool
     */
    public function isSuccess(){
        return empty($this->arErrors);
    }

    /**
     * Получение массива ошибок
     * @return array
     */
    public function getErrors(){
        return $this->arErrors;
    }

    public function getErrorMessages(){
        $ar = array();
        /**
         * @var Error $error
         */
        foreach($this->arErrors as $error)
        {
            $ar[] = /*(!!$error->getCode() ? $error->getCode() . ' ' : '') .*/ $error->getMessage();
        }
        return $ar;
    }

    /**
     * Фиксирвоание ошибки в процессе выполнения операции
     *
     * @param \Bxmaker\SmsNotice\Error $error
     *
     * @return $this
     */
    public function setError(Error $error)
    {
        Manager::getInstance()->log($error);
        $this->arErrors[] = $error;
        return $this;
    }

    /**
     * Добавление ошибки
     * 
     * @param        $message
     * @param string $code
     * @param array  $arMore
     *
     * @return \Bxmaker\SmsNotice\Result
     */
    public function addError($message, $code = '', $arMore = array())
    {
        return $this->setError(new \Bxmaker\SmsNotice\Error($message, $code, $arMore));
    }

    /**
     * Установка результата выполнения операции
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult($result){
        $this->result = $result;
        return $this;
    }

    /**
     * Возвращает результат выполнения операции
     * @return mixed
     */
    public function getResult(){
        return $this->result;
    }

    /**
     * Передача дополнительных данных
     * @param $name
     * @param $value
     */
    public function setMore($name, $value){
        $this->arMore[$name] = $value;
    }

    /**
     * Возвращает массив дополнительных данных или конкретного именованного элемента если он существует иначе null
     * @param null $name
     *
     * @return array|mixed|null
     */
    public function getMore($name = null)
    {
        if($name === null) return $this->arMore;

        return (isset($this->arMore[$name]) ? $this->arMore[$name] : null);
    }
}