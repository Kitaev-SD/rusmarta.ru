<?
	namespace Bxmaker\SmsNotice;

	use Bitrix\Main\Type\Date;
	use Bitrix\Main\Type\DateTime;

	class Agent {

		private static $module_id = 'bxmaker.smsnotice';


		/**
		 * Удаление старых сообщений
		 * @return string
		 * @throws \Bitrix\Main\ArgumentException
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		public static function cleanSmsHistory()
		{

			$option =  new \Bitrix\Main\Config\Option();

			$days = $option->get(self::$module_id, 'HANDLER.CLEAN_SMS_HISTORY', 30);

			$oManagerTable = new ManagerTable();
			$dbr = $oManagerTable->getList(array(
				'filter' => array(
					'<=CREATED' => Date::createFromTimestamp(time() - intval($days) * 60*60*24)
				),
				'order' => array(
					'ID' => 'ASC'
				),
				'limit' => '50'
			));
			while($ar = $dbr->fetch())
			{
				$oManagerTable->delete($ar['ID']);
			}

			return __METHOD__ . '();';
		}


		/**
		 * Проверка статуса отправленных сообщений, обновление на доставлено или ошибка с пояснениями
		 * @return string
		 * @throws \Bitrix\Main\ArgumentException
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		public static function checkSmsStatus()
		{
			Manager::getInstance()->agentCheckSmsStatus();

			return __METHOD__ . '();';
		}

		/**
		 * Отправка смс из очереди
		 * @return string
		 */
		public static function agentSendSmsQueue()
		{
			Manager::getInstance()->sendQueue();

			return __METHOD__ . '();';
		}


	}
