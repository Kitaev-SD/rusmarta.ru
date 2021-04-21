<?

namespace Bxmaker\SmsNotice;

/**
 * ����� �������� ������
 *
 * @package Bxmaker\SmsNotice
 */
class Error {

    /**
     * @var string ����� ������
     */
    private $message = '';
    /**
     * @var string ��� ������
     */
    private $code = '';
    /**
     * @var array �������������� ������ �� ������
     */
    private $arMore = array();

    /**
     * ����������� ������� ������
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
     * ������ ����� ������
     * @return string
     */
    public function getMessage(){
        return $this->message;
    }

    /**
     * ������ ��� ������
     * @return string
     */
    public function getCode(){
        return $this->code;
    }

    /**
     * ������ �������������� ������ �� ������,
     * �� ��������� ������������ ���� ������ ������,
     * �� ���� ������� ����, �� �������� ������ ������ �� ����
     *
     * @param null $name -  ���� �� �������� ���������� ������� ������ �� ��������������� ��������
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