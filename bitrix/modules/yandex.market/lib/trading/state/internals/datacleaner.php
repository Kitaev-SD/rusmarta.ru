<?php

namespace Yandex\Market\Trading\State\Internals;

use Yandex\Market;
use Bitrix\Main;

class DataCleaner extends Market\Reference\Agent\Regular
{
	public static function getDefaultParams()
	{
		return [
			'interval' => 86400,
		];
	}

	public static function run()
	{
		$days = static::getExpireDays();

		if ($days <= 0) { return; }

		$date = static::buildExpireDate($days);

		DataTable::deleteBatch([
			'filter' => [ '<=TIMESTAMP_X' => $date ],
		]);
	}

	protected static function getExpireDays()
	{
		return (int)Market\Config::getOption('trading_data_expire_days', 30);
	}

	protected static function buildExpireDate($days)
	{
		$result = new Main\Type\DateTime();
		$result->add('-P' . (int)$days . 'D');

		return $result;
	}
}