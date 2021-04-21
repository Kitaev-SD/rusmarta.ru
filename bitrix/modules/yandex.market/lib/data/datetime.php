<?php

namespace Yandex\Market\Data;

use Bitrix\Main;

class DateTime extends Date
{
	public static function format(Main\Type\Date $date)
	{
		$timestamp = $date->getTimestamp();

		return ConvertTimeStamp($timestamp, 'FULL');
	}

	public static function compare(Main\Type\Date $first, Main\Type\Date $second)
	{
		$firstValue = $first->getTimestamp();
		$secondValue = $second->getTimestamp();

		if ($firstValue === $secondValue)
		{
			return 0;
		}

		return $firstValue < $secondValue ? -1 : 1;
	}

	public static function convertFromService($dateString, $format = DateTime::FORMAT_DEFAULT_FULL)
	{
		return new Main\Type\DateTime($dateString, $format);
	}

	public static function makeDummy()
	{
		return Main\Type\DateTime::createFromTimestamp(0);
	}
}