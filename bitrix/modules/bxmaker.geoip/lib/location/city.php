<?php

	namespace Bxmaker\GeoIP\Location;

	use Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;

	Loc::loadMessages(__FILE__);

	class CityTable extends Entity\DataManager
	{
		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'bxmaker_geoip_loc_city';
		}

		public static function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary' => true
				)),
				new Entity\IntegerField('COUNTRY_ID', array(
					'required' => true
				)),
				new Entity\IntegerField('REGION_ID', array(
					'default' => null,
					'required' => true
				)),
				new Entity\StringField('NAME', array(
					'required' => true,
					'validation' => function () {
						return array(
							new \Bitrix\Main\Entity\Validator\Length(1, 120)
						);
					}
				)),
				new Entity\StringField('NAME_EN', array(
					'required' => true,
					'validation' => function () {
						return array(
							new \Bitrix\Main\Entity\Validator\Length(null, 160)
						);
					}
				)),
				new Entity\StringField('AREA', array(
					'default' => NULL,
					'validation' => function () {
						return array(
							new \Bitrix\Main\Entity\Validator\Length(null, 100)
						);
					}
				)),
				new Entity\StringField('AREA_EN', array(
					'default' => NULL,
					'validation' => function () {
						return array(
							new \Bitrix\Main\Entity\Validator\Length(null, 100)
						);
					}
				)),

				new Entity\ExpressionField('CNT', 'COUNT(*)'),

				new \Bitrix\Main\Entity\ReferenceField(
					'COUNTRY',
					'\Bxmaker\GeoIP\Location\CountryTable',
					array('=this.COUNTRY_ID' => 'ref.ID'),
					array('join_type' => 'LEFT')
				),

				new \Bitrix\Main\Entity\ReferenceField(
					'REGION',
					'\Bxmaker\GeoIP\Location\RegionTable',
					array('=this.REGION_ID' => 'ref.ID'),
					array('join_type' => 'LEFT')
				),

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
			if (!isset($data['AREA_EN']) && isset($data['AREA'])) {
				$arFields['AREA_EN'] = $oManager->translit($data['AREA']);
			}


			$result->modifyFields($arFields);
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

		/**
		 * Очистка от городов удаляемой страны
		 *
		 * @param $id
		 *
		 * @return bool
		 */
		public static function onCountryDelete($id)
		{
			$connection = \Bitrix\Main\Application::getConnection();
			$connection->queryExecute("DELETE FROM `" . self::getTableName() . "` WHERE COUNTRY_ID=" . intval($id) . " ");
			return true;
		}

		/**
		 * Очистка от лишних городов после удаления региона
		 *
		 * @param $id
		 *
		 * @return bool
		 */
		public static function onRegionDelete($id)
		{
			$connection = \Bitrix\Main\Application::getConnection();
			$connection->queryExecute("DELETE FROM `" . self::getTableName() . "` WHERE REGION_ID=" . intval($id) . " ");
			return true;
		}

	}