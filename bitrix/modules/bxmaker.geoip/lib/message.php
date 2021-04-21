<?

	namespace Bxmaker\GeoIP;

	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;

	Loc::loadMessages(__FILE__);


	Class MessageTable extends Entity\DataManager
	{

		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'bxmaker_geoip_message';
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
					'required'  => true,
				)),
				new Entity\TextField('MESSAGE', array(
				)),
				new Entity\BooleanField('DEF', array(
					'required'  => true
				)),
				new Entity\StringField('CITY', array(
					'validator' => function () {
						return array(
							new Entity\Validator\Length(1, 30)
						);
					}
				)),
				new Entity\StringField('START', array(
					'default' => '00:00'
				)),
				new Entity\StringField('STOP', array(
					'default' => '23:59'
				)),
				new Entity\IntegerField('GROUP', array()),
				new Entity\ReferenceField('TYPE', '\Bxmaker\GeoIP\Message\Type', array('=ref.ID' => 'this.TYPE_ID'), array('type_join' => 'left')),
				new Entity\ExpressionField('CNT', 'COUNT(ID)')
			);
		}



		public static function onBeforeAdd(Entity\Event $event)
		{
			$result = new Entity\EventResult;
			$data = $event->getParameter("fields");

			$arFields = array();

			if (!isset($data['DEF'])) {
				$arFields['DEF'] = false;
			}
			$result->modifyFields($arFields);
			return $result;
		}

		public static function onBeforeUpdate(Entity\Event $event)
		{
			$result = new Entity\EventResult;
			$data = $event->getParameter("fields");
			$arID = $event->getParameter("id");

			return $result;
		}



		/**
		 * Массовое удалене
		 *
		 * @param $id
		 *
		 * @return bool
		 */
		public static function deleteAllByType($id)
		{
			$connection = \Bitrix\Main\Application::getConnection();
			$connection->queryExecute("DELETE FROM `" . self::getTableName() . "` WHERE TYPE_ID=" . intval($id) . " ");
			return true;
		}

	}

