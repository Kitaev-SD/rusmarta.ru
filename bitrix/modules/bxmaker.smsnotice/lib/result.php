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
     * �������� ���������� ��������
     * @return bool
     */
    public function isSuccess(){
        return empty($this->arErrors);
    }

    /**
     * ��������� ������� ������
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
     * ������������ ������ � �������� ���������� ��������
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
     * ���������� ������
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
     * ��������� ���������� ���������� ��������
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult($result){
        $this->result = $result;
        return $this;
    }

    /**
     * ���������� ��������� ���������� ��������
     * @return mixed
     */
    public function getResult(){
        return $this->result;
    }

    /**
     * �������� �������������� ������
     * @param $name
     * @param $value
     */
    public function setMore($name, $value){
        $this->arMore[$name] = $value;
    }

    /**
     * ���������� ������ �������������� ������ ��� ����������� ������������ �������� ���� �� ���������� ����� null
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