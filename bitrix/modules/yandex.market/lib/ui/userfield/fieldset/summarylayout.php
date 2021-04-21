<?php

namespace Yandex\Market\Ui\UserField\Fieldset;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Ui\UserField\Helper;

class SummaryLayout extends AbstractLayout
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

		return $this->editRow($this->name, $value, [
			'class' => 'js-plugin',
			'data-base-name' => $this->name,
		]);
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

		$result = '<div class="js-plugin" data-plugin="Field.Fieldset.SummaryCollection" data-base-name="' . $inputName . '">';

		foreach ($values as $value)
		{
			$valueName = $inputName . '[' . $valueIndex . ']';
			$rowHtml = $this->editRow($valueName, $value, [
				'class' => 'js-fieldset-collection__item' . ($onlyPlaceholder ? ' is--hidden' : ''),
			]);

			if ($onlyPlaceholder)
			{
				$rowHtml = Helper\Attributes::sliceInputName($rowHtml);
			}

			$result .= $rowHtml;

			++$valueIndex;
		}

		$result .= '<div class="b-field-add">';
		$result .= '<input ' . Helper\Attributes::stringify([
			'class' => 'adm-btn js-fieldset-collection__item-add',
			'type' => 'button',
			'value' => static::getLang('USER_FIELD_FIELDSET_ADD'),
		]) . ' />';
		$result .= '</div>';
		$result .= '</div>';

		return $result;
	}

	protected static function loadCollectionAssets()
	{
		Market\Ui\Assets::loadPluginCore();
		Market\Ui\Assets::loadFieldsCore();
		Market\Ui\Assets::loadPlugins([
			'Field.Fieldset.Collection',
			'Field.Fieldset.SummaryCollection',
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

	protected function editRow($name, $value, array $attributes = [])
	{
		$value = $this->resolveRowValues($value);
		$fields = $this->extendFields($name, $this->fields);
		$summaryTemplate = isset($this->userField['SETTINGS']['SUMMARY']) ? $this->userField['SETTINGS']['SUMMARY'] : null;
		$summary = !empty($value)
			? (string)Helper\Summary::make($fields, $value, $summaryTemplate)
			: '';

		$rootAttributes =
			$attributes
			+ array_filter([
				'data-plugin' => 'Field.Fieldset.Summary',
				'data-lang' => array_filter([
					'MODAL_TITLE' => $this->userField['NAME'],
				]),
				'data-summary' => $summaryTemplate,
			])
			+ $this->collectFieldsSummaryAttributes($fields);

		$rootAttributes['class'] = 'b-form-pill' . (isset($rootAttributes['class']) ? ' ' . $rootAttributes['class'] : '');

		$result = '<div ' . Helper\Attributes::stringify($rootAttributes) . '>';
		$result .= '<a class="b-link action--heading target--inside js-fieldset-summary__text" href="#">';
		$result .= $summary ?: static::getLang('USER_FIELD_FIELDSET_SUMMARY_HOLDER');
		$result .= '</a>';
		$result .= '<button class="b-close js-fieldset-collection__item-delete" type="button" title=""></button>';
		$result .= '<div class="is--hidden js-fieldset-summary__edit-modal">';
		$result .= $this->renderEditForm($fields, $value);
		$result .= '</div>';
		$result .= '</div>';

		return $result;
	}

	protected function collectFieldsSummaryAttributes($fields)
	{
		$result = [];

		foreach ($fields as $code => $field)
		{
			if (isset($field['SETTINGS']['SUMMARY']) && is_string($field['SETTINGS']['SUMMARY']))
			{
				$attributeName = 'data-field-' . Market\Data\TextString::toLower($code) . '-summary';

				$result[$attributeName] = $field['SETTINGS']['SUMMARY'];
			}

			if (!empty($field['SETTINGS']['UNIT']))
			{
				$attributeName = 'data-field-' . Market\Data\TextString::toLower($code) . '-unit';

				$result[$attributeName] = is_array($field['SETTINGS']['UNIT'])
					? implode('|', $field['SETTINGS']['UNIT'])
					: $field['SETTINGS']['UNIT'];
			}
		}

		return $result;
	}

	protected function renderEditForm($fields, $values)
	{
		$result = '<table class="edit-table js-fieldset-summary__field" width="100%" data-plugin="Field.Fieldset.Row">';

		foreach ($fields as $fieldKey => $field)
		{
			$value = isset($values[$fieldKey]) ? $values[$fieldKey] : null;

			$row = Helper\Renderer::getEditRow($field, $value, $values);

			// row attributes

			$rowAttributes = [];

			if ($row['ROW_CLASS'] !== '')
			{
				$rowAttributes['class'] = $row['ROW_CLASS'];
			}

			if (isset($field['DEPEND']))
			{
				Market\Ui\Assets::loadPlugin('Ui.Input.DependField');

				$rowAttributes['class'] =
					(isset($rowAttributes['class']) ? $rowAttributes['class'] . ' ' : '')
					. 'js-plugin-delayed';
				$rowAttributes['data-plugin'] = 'Ui.Input.DependField';
				$rowAttributes['data-depend'] = Market\Utils::jsonEncode($field['DEPEND'], JSON_UNESCAPED_UNICODE);
				$rowAttributes['data-form-element'] = '.js-fieldset-summary__field';

				if (!Market\Utils\UserField\DependField::test($field['DEPEND'], $values))
				{
					$rowAttributes['class'] .= ' is--hidden';
				}
			}

			// title cell

			$titleAttributes = [];

			if ($row['VALIGN'] !== '')
			{
				$titleAttributes['valign'] = $row['VALIGN'];
			}

			// control

			$control = $row['CONTROL'];
			$control = Helper\Attributes::insert($control, [
				'class' => 'js-fieldset-row__input',
			]);
			$control = Helper\Attributes::insertDataName($control, $fieldKey, $field['FIELD_NAME']);
			$control = Helper\Attributes::delayPluginInitialization($control);

			// write result

			$result .= sprintf(
				'<tr %s>'
				. '<td class="adm-detail-content-cell-l" width="40%%" %s>%s</td>'
				. '<td class="adm-detail-content-cell-r" width="60%%">%s</td>'
				. '</tr>',
				Helper\Attributes::stringify($rowAttributes),
				Helper\Attributes::stringify($titleAttributes),
				$field['NAME'],
				$control
			);
		}

		$result .= '</table>';

		return $result;
	}
}