<?php

	namespace Bxmaker\GeoIP\Location;

	use Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;

	Loc::loadMessages(__FILE__);

	class RegionTable extends Entity\DataManager
	{
		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'bxmaker_geoip_loc_region';
		}

		public static function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\IntegerField('COUNTRY_ID', array(
					'required'      => true,
				)),
				new Entity\StringField('NAME', array(
					'required' => true,
					'validation' => function () {
						return array(
							new \Bitrix\Main\Entity\Validator\Length(1, 100)
						);
					}
				)),
				new Entity\StringField('NAME_EN'),
				new Entity\ExpressionField('CNT', 'COUNT(*)'),

				new \Bitrix\Main\Entity\ReferenceField(
					'COUNTRY',
					'\Bxmaker\GeoIP\Location\CountryTable',
					array('=this.COUNTRY_ID' => 'ref.ID'),
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

			$result->modifyFields($arFields);
			return $result;
		}


		public static function OnBeforeDelete(Entity\Event $event)
		{
			$result = new Entity\EventResult;
			$primary     = $event->getParameter("primary");

			if (intval($primary['ID'])) {
				CityTable::onRegionDelete($primary['ID']);
			}

			return $result;
		}

		/**
		 * Очистка отрегионов удаляемой страны
		 * @param $id
		 *
		 * @return bool
		 */
		public static function onCountryDelete($id)
		{
			$connection = \Bitrix\Main\Application::getConnection();
			$connection->queryExecute("DELETE FROM `" . self::getTableName() ."` WHERE COUNTRY_ID=".intval($id)." ");
			return true;
		}


		/**
		 * Очистка таблицы
		 * @return bool
		 */
		public function clearTable()
		{
			$connection = \Bitrix\Main\Application::getConnection();
			$connection->queryExecute("DELETE FROM `" . self::getTableName() ."`");
			return true;
		}



	}