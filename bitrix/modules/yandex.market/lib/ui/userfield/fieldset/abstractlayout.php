<?php

namespace Yandex\Market\Ui\UserField\Fieldset;

use Bitrix\Main;
use Yandex\Market\Ui\UserField\Helper;

abstract class AbstractLayout
{
	protected $userField;
	protected $name;
	protected $fields;

	public function __construct($userField, $name, array $fields)
	{
		$this->userField = $userField;
		$this->name = $name;
		$this->fields = $fields;
	}

	abstract public function edit($value);

	abstract public function editMultiple($values);

	protected function extendFields($name, $fields)
	{
		foreach ($fields as $fieldKey => &$field)
		{
			$fieldName = $name . '[' . $fieldKey . ']';

			$field = Helper\Field::extend($field, $fieldName);
		}
		unset($field);

		return $fields;
	}

	protected function resolveRowValues($values)
	{
		if (!is_array($values))
		{
			$values = [];
		}

		if (isset($this->userField['ROW']))
		{
			$values['PARENT_ROW'] = $this->userField['ROW'];
		}

		return $values;
	}
}