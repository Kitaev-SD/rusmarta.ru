<?
namespace Bxmaker\SmsNotice;

use Bitrix\Main\Application;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Loader;



Class TemplateTable extends Entity\DataManager
{

    public static
    function getFilePath()
    {
        return __FILE__;
    }

    public static
    function getTableName()
    {
        return 'bxmaker_smsnotice_template';
    }

    public static
    function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary'      => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('TYPE_ID', array(
                'required' => true
            )),
            new Entity\StringField('NAME', array(
                'required' => true
            )),
            new Entity\BooleanField('ACTIVE'),
            new Entity\BooleanField('TRANSLIT'),

            new Entity\StringField('PHONE', array(
                'required' => true
            )),
			new Entity\StringField('PHONE_COPY', array(
                'default' => ''
            )),
            new Entity\StringField('TEXT', array(
                'required' => true
            )),
            new Entity\StringField('EVENT', array(
                'default' => null
            )),
            new Entity\TextField('PARAMS', array(
                'default' => array(),
                'serialized' => true
            )),

            new Entity\ReferenceField('TYPE', '\Bxmaker\SmsNotice\Template\TypeTable', array('=this.TYPE_ID' => 'ref.ID'), array('type_join' => 'left')),
            new Entity\ReferenceField('SITE', '\Bxmaker\SmsNotice\Template\SiteTable', array('=this.ID' => 'ref.TID'), array('type_join' => 'left'))
        );
    }
}