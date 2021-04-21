<?php

namespace Yandex\Market\Trading\State\Internals;

use Yandex\Market;
use Bitrix\Main;

class StatusProxy
{
	protected static $fetched = [];

	public static function get($service, $orderId)
	{
		$key = $service . ':' . $orderId;

		if (!array_key_exists($key, static::$fetched))
		{
			static::$fetched[$key] = static::fetch($service, $orderId);
		}

		return static::$fetched[$key];
	}

	public static function set($service, $orderId, $value)
	{
		$stored = static::get($service, $orderId);
		$value = (string)$value;

		if ($stored === $value) { return; }

		$primary = [
			'SERVICE' => $service,
			'ENTITY_ID' => $orderId,
		];
		$fields = [
			'VALUE' => $value,
		];

		$writeResult = ($stored === null)
			? StatusTable::add($primary + $fields)
			: StatusTable::update($primary, $fields);

		Market\Result\Facade::handleException($writeResult);

		static::$fetched[$service . ':' . $orderId] = $value;
	}

	protected static function fetch($service, $orderId)
	{
		$result = null;

		$query = StatusTable::getList([
			'filter' => [
				'=SERVICE' => $service,
				'=ENTITY_ID' => $orderId,
			],
			'select' => [ 'VALUE' ],
		]);

		if ($row = $query->fetch())
		{
			$result = (string)$row['VALUE'];
		}

		return $result;
	}
}