<?php

namespace Yandex\Market\Trading\State;

use Bitrix\Main;
use Yandex\Market;

class OrderData
{
	protected static $cache = [];

	public static function getValue($serviceUniqueKey, $orderId, $name)
	{
		$values = static::getValues($serviceUniqueKey, $orderId);

		return isset($values[$name]) ? $values[$name] : null;
	}

	public static function setValue($serviceUniqueKey, $orderId, $name, $value)
	{
		static::setValues($serviceUniqueKey, $orderId, [
			$name => $value,
		]);
	}

	public static function getValues($serviceUniqueKey, $orderId)
	{
		$key = static::makeCachedKey($serviceUniqueKey, $orderId);

		if (!isset(static::$cache[$key]))
		{
			static::$cache[$key] = static::fetchValues($serviceUniqueKey, $orderId);
		}

		return static::$cache[$key];
	}

	protected static function fetchValues($serviceUniqueKey, $orderId)
	{
		$result = [];

		$query = Internals\DataTable::getList([
			'filter' => [
				'=SERVICE' => $serviceUniqueKey,
				'=ENTITY_ID' => $orderId,
			],
			'select' => [
				'NAME',
				'VALUE',
			],
		]);

		while ($row = $query->fetch())
		{
			$result[$row['NAME']] = $row['VALUE'];
		}

		return $result;
	}

	public static function setValues($serviceUniqueKey, $orderId, $values)
	{
		if (empty($values)) { return; }

		$stored = static::getValues($serviceUniqueKey, $orderId);
		$exists = array_intersect_key($values, $stored);
		$delete = array_filter($values, static function($value) {
			return $value === null || (is_scalar($value) && (string)$value === '');
		});
		$update = array_diff_key($exists, $delete);
		$new = array_diff_key($values, $stored);
		$new = array_diff_key($new, $delete);
		$delete = array_intersect_key($delete, $stored);

		static::applyAdd($serviceUniqueKey, $orderId, $new);
		static::applyUpdate($serviceUniqueKey, $orderId, $update);
		static::applyDelete($serviceUniqueKey, $orderId, array_keys($delete));
		static::modifyCached($serviceUniqueKey, $orderId, $values);
	}

	protected static function applyAdd($serviceUniqueKey, $orderId, $values)
	{
		foreach ($values as $name => $value)
		{
			$addResult = Internals\DataTable::add([
				'SERVICE' => $serviceUniqueKey,
				'ENTITY_ID' => $orderId,
				'NAME' => $name,
				'VALUE' => $value,
				'TIMESTAMP_X' => new Main\Type\DateTime(),
			]);

			Market\Result\Facade::handleException($addResult);
		}
	}

	protected static function applyUpdate($serviceUniqueKey, $orderId, $values)
	{
		foreach ($values as $name => $value)
		{
			$updateResult = Internals\DataTable::update(
				[
					'SERVICE' => $serviceUniqueKey,
					'ENTITY_ID' => $orderId,
					'NAME' => $name,
				],
				[
					'VALUE' => $value,
					'TIMESTAMP_X' => new Main\Type\DateTime(),
				]
			);

			Market\Result\Facade::handleException($updateResult);
		}
	}

	protected static function applyDelete($serviceUniqueKey, $orderId, $names)
	{
		foreach ($names as $name)
		{
			$deleteResult = Internals\DataTable::delete([
				'SERVICE' => $serviceUniqueKey,
				'ENTITY_ID' => $orderId,
				'NAME' => $name,
			]);

			Market\Result\Facade::handleException($deleteResult);
		}
	}

	protected static function modifyCached($serviceUniqueKey, $orderId, $values)
	{
		$key = static::makeCachedKey($serviceUniqueKey, $orderId);

		if (!isset(static::$cache[$key])) { return; }

		static::$cache[$key] = $values + static::$cache[$key];
	}

	protected static function makeCachedKey($serviceUniqueKey, $orderId)
	{
		return $serviceUniqueKey . ':' . $orderId;
	}
}