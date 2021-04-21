<?php

namespace Yandex\Market\Trading\Setup;

use Bitrix\Main;
use Yandex\Market;

class Table extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_trading_setup';
	}

	public static function getUfId()
	{
		return 'YAMARKET_TRADING_SETUP';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('ID', [
				'autocomplete' => true,
				'primary' => true
			]),
			new Main\Entity\StringField('NAME', [
				'required' => true,
				'validation' => [__CLASS__, 'validateName'],
			]),
			new Main\Entity\BooleanField('ACTIVE', [
				'values' => [static::BOOLEAN_N, static::BOOLEAN_Y],
				'default_value' => static::BOOLEAN_Y,
			]),
			new Main\Entity\StringField('TRADING_SERVICE', [
				'required' => true,
				'validation' => [__CLASS__, 'validateTradingService'],
			]),
			new Main\Entity\StringField('TRADING_BEHAVIOR', [
				'required' => true,
				'validation' => [__CLASS__, 'validateTradingBehavior'],
				'default_value' => Market\Trading\Service\Manager::BEHAVIOR_DEFAULT,
			]),
			new Main\Entity\StringField('SITE_ID', [
				'required' => true,
				'validation' => [__CLASS__, 'validateSiteId'],
			]),
			new Main\Entity\StringField('EXTERNAL_ID', [
				'required' => true,
				'validation' => [__CLASS__, 'validateExternalId'],
			]),
		];
	}

	public static function onAfterUpdate(Main\Entity\Event $event)
	{
		$cache = Main\Application::getInstance()->getManagedCache();
		$tableName = static::getTableName();

		$cache->cleanDir($tableName);
	}

	public static function migrate(Main\DB\Connection $connection)
	{
		parent::migrate($connection);
		static::migrateIncreaseServiceLength($connection);
		static::migrateFillDefaultBehavior($connection);
	}

	protected static function migrateIncreaseServiceLength(Main\DB\Connection $connection)
	{
		$entity = static::getEntity();

		Market\Migration\StorageFacade::updateFieldsLength($connection, $entity, [ 'TRADING_SERVICE' ]);
	}

	protected static function migrateFillDefaultBehavior(Main\DB\Connection $connection)
	{
		$sqlHelper = $connection->getSqlHelper();
		$tableName = static::getTableName();

		$connection->queryExecute(sprintf(
			'UPDATE %1$s SET %2$s=\'%3$s\' WHERE %2$s is null or %2$s=\'\'',
			$sqlHelper->quote($tableName),
			$sqlHelper->quote('TRADING_BEHAVIOR'),
			$sqlHelper->forSql(Market\Trading\Service\Manager::BEHAVIOR_DEFAULT)
		));
	}

	public static function getReference($primary = null)
	{
		return [
			'SETTINGS' => [
				'TABLE' => Market\Trading\Settings\Table::getClassName(),
				'LINK_FIELD' => 'SETUP_ID',
				'LINK' => [
					'SETUP_ID' => $primary,
				],
			],
		];
	}

	public static function validateName()
	{
		return [
			new Main\Entity\Validator\Length(null, 65),
		];
	}

	public static function validateTradingService()
	{
		return [
			new Main\Entity\Validator\Length(null, 20),
		];
	}

	public static function validateTradingBehavior()
	{
		return [
			new Main\Entity\Validator\Length(null, 20),
		];
	}

	public static function validateSiteId()
	{
		return [
			new Main\Entity\Validator\Length(null, 10),
		];
	}

	public static function validateExternalId()
	{
		return [
			new Main\Entity\Validator\Length(null, 20),
		];
	}
}
