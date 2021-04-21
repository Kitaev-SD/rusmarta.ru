<?

	namespace Bxmaker\GeoIP\Favorites;

	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;

	Loc::loadMessages(__FILE__);

	/**
	 * �������� ���������� �������������� � ������
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
				new Entity\IntegerField('FID', array( // ID ���������� ��������������
					'required' => true
				)),
				new Entity\StringField('SID', array( // ������������� �����
					'required'  => true,
					'validator' => function () {
						return new Entity\Validator\Length(2, 2);
					}
				))
			);
		}

		/**
		 * �������� �������
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