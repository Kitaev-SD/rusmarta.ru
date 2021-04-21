<?php

	namespace Bxmaker\GeoIP\Location;

	use Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;

	Loc::loadMessages(__FILE__);

	class CountryTable extends Entity\DataManager
	{
		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'bxmaker_geoip_loc_country';
		}

		public static function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\StringField('NAME', array(
					'required'   => true,
					'validation' => function () {
						return array(
							new \Bitrix\Main\Entity\Validator\Length(null, 60)
						);
					}
				)),
				new Entity\StringField('NAME_EN', array(
					'required'   => true,
					'validation' => function () {
						return array(
							new \Bitrix\Main\Entity\Validator\Length(null, 80)
						);
					}
				)),
				new Entity\StringField('CODE', array(
					'default'    => NULL,
					'validation' => function () {
						return array(
							new \Bitrix\Main\Entity\Validator\Length(0, 2)
						);
					}
				)),
				new Entity\ExpressionField('CNT', 'COUNT(*)')

			);
		}


		public static function onBeforeAdd(Entity\Event $event)
		{
			$result   = new Entity\EventResult;
			$data     = $event->getParameter("fields");
			$oManager = \BXmaker\GeoIP\Manager::getInstance();

			$arFields = array();

			if (!isset($data['NAME_EN']) && isset($data['NAME'])) {
				$arFields['NAME_EN'] = $oManager->translit($data['NAME']);
			}


			$result->modifyFields($arFields);
			return $result;
		}


		public static function OnBeforeDelete(Entity\Event $event)
		{
			$result  = new Entity\EventResult;
			$primary = $event->getParameter("primary");

			if (intval($primary['ID'])) {
				CityTable::onCountryDelete($primary['ID']);
				RegionTable::onCountryDelete($primary['ID']);
			}

			return $result;
		}

		/**
		 * Очистка таблицы
		 * @return bool
		 */
		public function clearTable()
		{
			$connection = \Bitrix\Main\Application::getConnection();
			$connection->queryExecute("DELETE FROM `" . self::getTableName() . "`");
			return true;
		}


	}