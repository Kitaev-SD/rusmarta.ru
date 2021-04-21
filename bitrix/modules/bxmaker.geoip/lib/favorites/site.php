<?

	namespace Bxmaker\GeoIP\Favorites;

	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;

	Loc::loadMessages(__FILE__);

	/**
	 * Привязка тизбранных местоположений к сайтам
	 * Class SiteTable
	 * @package Bxmaker\GeoIP\Favorites
	 */
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
			return 'bxmaker_geoip_favorites_site';
		}

		public static
		function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\IntegerField('FID', array( // ID избранного местоположения
					'required' => true
				)),
				new Entity\StringField('SID', array( // Идентификатор сайта
					'required'  => true,
					'validator' => function () {
						return new Entity\Validator\Length(2, 2);
					}
				))
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