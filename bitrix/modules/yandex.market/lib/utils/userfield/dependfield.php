<?php

namespace Yandex\Market\Utils\UserField;

use Bitrix\Main;
use Yandex\Market;

class DependField
{
	const RULE_ANY = 'ANY';
	const RULE_EMPTY = 'EMPTY';

	public static function test($rules, $values)
	{
		$result = true;

		foreach ($rules as $fieldName => $rule)
		{
			$value = isset($values[$fieldName]) ? $values[$fieldName] : null;

			switch ($rule['RULE'])
			{
				case static::RULE_EMPTY:
					$isDependValueEmpty = Market\Utils\Value::isEmpty($value) || (is_scalar($value) && (string)$value === '0');
					$isMatch = ($isDependValueEmpty === $rule['VALUE']);
				break;

				case static::RULE_ANY:
					$isMatch = in_array($rule['VALUE'], (array)$value);
				break;

				default:
					$isMatch = true;
				break;
			}

			if (!$isMatch)
			{
				$result = false;
				break;
			}
		}

		return $result;
	}
}