<?

	namespace Bxmaker\GeoIP;

	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;

	Loc::loadMessages(__FILE__);


	/**
	 * Избранные местоположения
	 * Class TypeTable
	 * @package Bxmaker\GeoIP\Favorites
	 */
	Class FavoritesTable extends Entity\DataManager
	{

		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'bxmaker_geoip_favorites';
		}

		public static
		function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\StringField('NAME', array(
					'required'  => true,
					'validator' => function () {
						return array(
							new Entity\Validator\Length(1, 30)
						);
					}
				)),
				new Entity\ReferenceField('SITE', '\Bxmaker\GeoIP\Favorites\Site', array('=ref.FID' => 'this.ID'), array('type_join' => 'left')),
				new Entity\ReferenceField('CITY', '\Bxmaker\GeoIP\Favorites\City', array('=ref.FID' => 'this.ID'), array('type_join' => 'left')),
				new Entity\ExpressionField('CNT', 'COUNT(ID)')
			);
		}


		public static function OnBeforeDelete(Entity\Event $event)
		{

			$result  = new Entity\EventResult;
			$primary = $event->getParameter("primary");

			if (intval($primary['ID'])) {
				//удаление привязок к сайту
				\Bxmaker\GeoIP\Favorites\SiteTable::deleteAllByFID($primary['ID']);

				//удаление привязки к местоположениям
				\Bxmaker\GeoIP\Favorites\CityTable::deleteAllByFID($primary['ID']);
			}

			return $result;
		}

	}

