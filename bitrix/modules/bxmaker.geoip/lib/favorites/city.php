<?

	namespace Bxmaker\GeoIP\Favorites;

	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;

	Loc::loadMessages(__FILE__);


	/**
	 * Избранные местоположения
	 * Class CityTable
	 * @package Bxmaker\GeoIP\Favorites
	 */
	Class CityTable extends Entity\DataManager
	{

		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'bxmaker_geoip_favorites_city';
		}

		public static
		function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\IntegerField('FID', array( // группа избранных местоположений
					'required'  => true
				)),
				new Entity\IntegerField('LOCATION_ID', array( // ID местоположения
					'required'  => true
				)),
				new Entity\IntegerField('SORT', array(
					'required'  => true
				)),
				new Entity\BooleanField('MARK', array(
					'default' => false
				)),
				new Entity\ReferenceField('SITE', '\Bxmaker\GeoIP\Favorites\Site', array('=ref.FID' => 'this.FID'), array('type_join' => 'left')),
				new Entity\ReferenceField('LOCATION', '\Bitrix\Sale\Location\LocationTable', array('=ref.ID' => 'this.LOCATION_ID'), array('type_join' => 'left')),
				new Entity\ExpressionField('CNT', 'COUNT(ID)')
			);
		}


		/**
		 * Массовое удалене
		 *
		 * @param $id
		 *
		 * @return bool
		 */
		public static function deleteAllByFID($id)
		{
			$connection = \Bitrix\Main\Application::getConnection();
			$connection->queryExecute("DELETE FROM `" . self::getTableName() . "` WHERE FID=" . intval($id) . " ");
			return true;
		}

	}

