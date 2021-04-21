<?
namespace Bxmaker\GeoIP;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;

class Agent {

    private static $module_id = 'bxmaker.geoip';



    public static function updateBase()
    {
        //$oManager = Manager::getInstance();

		$oIPGeoBase = new \BXmaker\GeoIP\Manager\IPGeoBase();
		$oIPGeoBase->updateBase();


        return __METHOD__ . '();';
    }

}
