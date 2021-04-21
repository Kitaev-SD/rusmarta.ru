<?php

namespace Yandex\Market\Ui\UserField;

use Yandex\Market;
use Bitrix\Main;

class DateTimeType extends \CUserTypeDateTime
{
	function getEditFormHtml($userField, $additionalParameters)
	{
		if (empty($userField['ENTITY_VALUE_ID']))
		{
			$userField['ENTITY_VALUE_ID'] = 1;
		}

		return parent::getEditFormHtml($userField, $additionalParameters);
	}
}