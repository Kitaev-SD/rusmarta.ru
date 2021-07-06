<?php

namespace Yandex\Market\Ui\UserField\Helper;

use Bitrix\Main;
use Yandex\Market;

class Field
{
	public static function extend($field, $name = null)
	{
		$field += [
			'MULTIPLE' => 'N',
			'EDIT_IN_LIST' => 'Y',
			'EDIT_FORM_LABEL' => $field['NAME'],
			'FIELD_NAME' => $name,
			'SETTINGS' => [],
		];

		if (!isset($field['USER_TYPE']) && isset($field['TYPE']))
		{
			$field['USER_TYPE'] = Market\Ui\UserField\Manager::getUserType($field['TYPE']);
		}

		return $field;
	}

	public static function extendValue($userField, $value, $row)
	{
		$defaults = [];

		if ($value !== null)
		{
			$defaults['VALUE'] = $value;
		}

		if ($row !== null)
		{
			$defaults['ENTITY_VALUE_ID'] = isset($row['ID']) ? $row['ID'] : null;
			$defaults['ROW'] = $row;
		}

		return $userField + $defaults;
	}
}