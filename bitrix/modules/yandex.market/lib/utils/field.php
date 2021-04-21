<?php

namespace Yandex\Market\Utils;

class Field
{
	public static function getChainValue($values, $key)
	{
		$keyParts = explode('.', $key);
		$lastLevel = $values;

		foreach ($keyParts as $keyPart)
		{
			if (isset($lastLevel[$keyPart]))
			{
				$lastLevel = $lastLevel[$keyPart];
			}
			else
			{
				$lastLevel = null;
				break;
			}
		}

		return $lastLevel;
	}

	public static function setChainValue(&$values, $key, $value)
	{
		$keyParts = explode('.', $key);
		$keyPartIndex = 0;
		$keyPartCount = count($keyParts);
		$lastLevel = &$values;

		foreach ($keyParts as $keyPart)
		{
			if ($keyPartCount === $keyPartIndex + 1)
			{
				$lastLevel[$keyPart] = $value;
			}
			else
			{
				if (!isset($lastLevel[$keyPart]) || !is_array($lastLevel[$keyPart]))
				{
					$lastLevel[$keyPart] = [];
				}

				$lastLevel = &$lastLevel[$keyPart];
			}

			$keyPartIndex++;
		}
	}

	public static function pushChainValue(&$values, $key, $value)
	{
		$keyParts = explode('.', $key);
		$keyPartIndex = 0;
		$keyPartCount = count($keyParts);
		$lastLevel = &$values;

		foreach ($keyParts as $keyPart)
		{
			if ($keyPartCount === $keyPartIndex + 1)
			{
				if (!isset($lastLevel[$keyPart]))
				{
					$lastLevel[$keyPart] = [];
				}

				$lastLevel[$keyPart][] = $value;
			}
			else
			{
				if (!isset($lastLevel[$keyPart]) || !is_array($lastLevel[$keyPart]))
				{
					$lastLevel[$keyPart] = [];
				}

				$lastLevel = &$lastLevel[$keyPart];
			}

			$keyPartIndex++;
		}
	}

	public static function unsetChainValue(&$values, $key)
	{
		$keyParts = explode('.', $key);
		$keyPartIndex = 0;
		$keyPartCount = count($keyParts);
		$lastLevel = &$values;

		foreach ($keyParts as $keyPart)
		{
			if (!isset($lastLevel[$keyPart]))
			{
				break;
			}
			else if ($keyPartCount === $keyPartIndex + 1)
			{
				unset($lastLevel[$keyPart]);
			}
			else
			{
				$lastLevel = &$lastLevel[$keyPart];
			}

			$keyPartIndex++;
		}
	}
}