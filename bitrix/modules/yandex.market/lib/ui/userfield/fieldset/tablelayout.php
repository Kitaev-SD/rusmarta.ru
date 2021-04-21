<?php

namespace Yandex\Market\Ui\UserField\Fieldset;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Ui\UserField\Helper;

class TableLayout extends AbstractLayout
{
	use Market\Reference\Concerns\HasLang;
	use Market\Reference\Concerns\HasOnceStatic;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
	}

	public function edit($value)
	{
		static::onceStatic('loadRowAssets');

		$result = '<table>';
		$result .= $this->editRow($this->name, $value, [
			'class' => 'js-plugin',
			'data-base-name' => $this->name,
		]);
		$result .= '</table>';

		return $result;
	}

	public function editMultiple($values)
	{
		static::onceStatic('loadCollectionAssets');
		static::onceStatic('loadRowAssets');

		$valueIndex = 0;
		$inputName = preg_replace('/\[]$/', '', $this->name);
		$onlyPlaceholder = false;

		if (empty($values))
		{
			$onlyPlaceholder = true;
			$values[] = [];
		}

		$collectionAttributes = [
			'class' => 'js-plugin',
			'data-plugin' => 'Field.Fieldset.Collection',
			'data-base-name' => $inputName,
		];

		if ($this->userField['MANDATORY'] === 'Y')
		{
			$collectionAttributes['data-persistent'] = 'true';
		}

		$result = sprintf('<table %s>', Helper\Attributes::stringify($collectionAttributes));

		foreach ($values as $value)
		{
			$valueName = $inputName . '[' . $valueIndex . ']';
			$rowAttributes = [
				'class' => 'js-fieldset-collection__item' . ($onlyPlaceholder ? ' is--hidden' : ''),
			];
			$rowHtml = $this->editRow($valueName, $value, $rowAttributes, true);

			if ($onlyPlaceholder)
			{
				$rowHtml = Helper\Attributes::sliceInputName($rowHtml);
			}

			$result .= $rowHtml;

			++$valueIndex;
		}

		$result .= '</table>';
		$result .= '<input ' . Helper\Attributes::stringify([
			'class' => 'adm-btn js-fieldset-collection__item-add',
			'type' => 'button',
			'value' => static::getLang('USER_FIELD_FIELDSET_ADD'),
		]) . ' />';

		return $result;
	}

	protected static function loadCollectionAssets()
	{
		Market\Ui\Assets::loadPluginCore();
		Market\Ui\Assets::loadFieldsCore();
		Market\Ui\Assets::loadPlugins([
			'Field.Fieldset.Collection',
		]);
	}

	protected static function loadRowAssets()
	{
		Market\Ui\Assets::loadPluginCore();
		Market\Ui\Assets::loadFieldsCore();
		Market\Ui\Assets::loadPlugins([
			'Field.Fieldset.Summary',
			'Field.Fieldset.Row',
		]);
	}

	protected function editRow($name, $values, array $attributes = [], $allowDelete = false)
	{
		$fields = $this->extendFields($name, $this->fields);
		$result = sprintf('<tr %s>', Helper\Attributes::stringify($attributes + [
			'data-plugin' => 'Field.Fieldset.Row',
		]));

		foreach ($fields as $fieldKey => $field)
		{
			$value = isset($values[$fieldKey]) ? $values[$fieldKey] : null;

			$row = Helper\Renderer::getEditRow($field, $value, $values);
			$control = $row['CONTROL'];
			$control = Helper\Attributes::insert($control, [
				'class' => 'js-fieldset-row__input',
			]);
			$control = Helper\Attributes::insertDataName($control, $fieldKey, $field['FIELD_NAME']);

			// write result

			$result .= sprintf('<td>%s</td>', $control);
		}

		if ($allowDelete)
		{
			$result .= sprintf(
				'<td><button class="adm-btn js-fieldset-collection__item-delete" type="button" title="%s">-</button></td>',
				static::getLang('USER_FIELD_FIELDSET_DELETE')
			);
		}

		$result .= '</tr>';

		return $result;
	}
}