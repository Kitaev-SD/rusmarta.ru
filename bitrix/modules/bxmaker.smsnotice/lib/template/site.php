<?
namespace Bxmaker\SmsNotice\Template;

use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


Class SiteTable extends Entity\DataManager
{

    public static
    function getFilePath()
    {
        return __FILE__;
    }

    public static
    function getTableName()
    {
        return 'bxmaker_smsnotice_template_site';
    }

    public static
    function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('TID', array(
                'required' => true
            )),
            new Entity\StringField('SID', array(
                'required' => true,
                'validator' => function(){
                    return new Entity\Validator\Length(2, 2);
                }
            ))
        );
    }
}