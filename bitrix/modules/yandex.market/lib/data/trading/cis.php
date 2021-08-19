<?php

namespace Yandex\Market\Data\Trading;

use Bitrix\Main;
use Yandex\Market;

class Cis
{
	public static function fromMarkingCode($markingCode)
	{
		if (preg_match('/^(01\d{14}21[A-Za-z0-9!"%&\'*+.\/_,:;=<>?\\\-]{13,27}?)91/', $markingCode, $matches))
		{
			$result = $matches[1];
		}
		else
		{
			$result = Market\Data\TextString::getSubstring($markingCode, 0, 31);
		}

		return $result;
	}
}