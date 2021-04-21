<?

	namespace Bxmaker\GeoIP\Message;

	use Bitrix\Main\Application;
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
			return 'bxmaker_geoip_message_type';
		}

		public static
		function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\StringField('SITE_ID', array(
					'required'  => true,
					'validator' => function () {
						return array(
							new Entity\Validator\Range(2, 2)
						);
					}
				)),
				new Entity\StringField('TYPE', array(
					'required'  => true,
					'validator' => function () {
						return array(
							new Entity\Validator\Length(1, 30)
						);
					}
				)),
				new Entity\ExpressionField('CNT', 'COUNT(ID)')
			);
		}


		public static function onBeforeAdd(Entity\Event $event)
		{
			$result = new Entity\EventResult;
			$data = $event->getParameter("fields");

			$arFields = array();

			if (!isset($data['SITE_ID'])) {
				$arFields['SITE_ID'] = \Bxmaker\GeoIP\Manager::getInstance()->getCurrentSiteId();
			}
			$result->modifyFields($arFields);
			return $result;
		}

		public static function OnBeforeDelete(Entity\Event $event)
		{

			$result  = new Entity\EventResult;
			$primary = $event->getParameter("primary");

			if (intval($primary['ID'])) {
				//удаляем сообщения этого типа
				\Bxmaker\GeoIP\MessageTable::deleteAllByType($primary['ID']);
			}

			return $result;
		}

	}

