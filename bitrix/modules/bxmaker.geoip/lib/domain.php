<?

	namespace Bxmaker\GeoIP;

	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;

	Loc::loadMessages(__FILE__);


	Class DomainTable extends Entity\DataManager
	{

		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'bxmaker_geoip_domain';
		}

		public static
		function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\StringField('SID', array(
					'validator' => function () {
						return array(
							new Entity\Validator\Length(2, 2)
						);
					}
				)),
				new Entity\IntegerField('LOCATION_ID', array(

				)),
				new Entity\StringField('VALUE', array(
					'required' => true
				)),
				new Entity\IntegerField('GROUP', array(

				)),
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
		public static function deleteAllBySiteID($sid)
		{
			if(\Bitrix\Main\Text\BinaryString::getLength($sid) <= 0) return false;
			$connection = \Bitrix\Main\Application::getConnection();
			$helper = $connection->getSqlHelper();
			$connection->queryExecute("DELETE FROM `" . self::getTableName() . "` WHERE SID=" . $helper->forSql(trim($sid)) . " ");
			return true;
		}

	}

