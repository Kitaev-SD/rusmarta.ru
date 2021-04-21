<?
namespace Bxmaker\SmsNotice\Template;

use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


Class TypeTable extends Entity\DataManager
{

    public static
    function getFilePath()
    {
        return __FILE__;
    }

    public static
    function getTableName()
    {
        return 'bxmaker_smsnotice_template_type';
    }

    public static
    function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\StringField('CODE', array(
                'required' => true,
                'validator' => function(){
                    return new Entity\Validator\Length(null, 255);
                }
            )),
            new Entity\StringField('NAME', array(
                'required' => true,
                'validator' => function(){
                    return new Entity\Validator\Length(null, 255);
                }
            )),
            new Entity\TextField('DESCR')
        );
    }
}