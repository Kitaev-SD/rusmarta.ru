<?php

namespace Yandex\Market\Ui\UserField;

use Yandex\Market;
use Bitrix\Main;

class StringType extends \CUserTypeString
{
	use Market\Reference\Concerns\HasLang;
	use Concerns\HasMultipleRow;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
	}

	function getUserTypeDescription()
	{
		$result = parent::getUserTypeDescription();

		if (!empty($result['USE_FIELD_COMPONENT']))
		{
			$result['USE_FIELD_COMPONENT'] = false;
		}

		return $result;
	}

	function GetEditFormHtmlMulty($userField, $htmlControl)
	{
		$values = Helper\Value::asMultiple($userField, $htmlControl);
		$values = static::sanitizeMultipleValues($values);
		$valueIndex = 0;

		if (empty($values)) { $values[] = ''; }

		$result = sprintf('<table id="%s">', static::makeFieldHtmlId($userField, 'table'));

		foreach ($values as $value)
		{
			$result .= '<tr><td>';
			$result .= static::GetEditFormHTML($userField, [
				'NAME' => $userField['FIELD_NAME'] . '[' . $valueIndex . ']',
				'VALUE' => $value,
			]);
			$result .= '</td></tr>';

			++$valueIndex;
		}

		$result .= '<tr><td style="padding-top: 6px;">';
		$result .= static::getMultipleAddButton($userField);
		$result .= '</td></tr>';
		$result .= '</table>';
		$result .= static::getMultipleAutoSaveScript($userField);

		return $result;
	}

	function GetEditFormHTML($userField, $htmlControl)
	{
		$attributes = Helper\Attributes::extractFromSettings($userField['SETTINGS']);

		$result = static::getEditInput($userField, $htmlControl);
		$result = Helper\Attributes::insert($result, $attributes);

		if (isset($userField['SETTINGS']['COPY_BUTTON']))
		{
			$result .= ' ' . static::getCopyButton($userField, $htmlControl);
		}

		return $result;
	}

	function GetAdminListViewHtml($userField, $htmlControl)
	{
		$value = (string)Helper\Value::asSingle($userField, $htmlControl);

		return $value !== '' ? $value : '&nbsp;';
	}

	protected static function sanitizeMultipleValues(array $values)
	{
		$result = [];

		foreach ($values as $value)
		{
			if (is_scalar($value) && (string)$value !== '')
			{
				$result[] = htmlspecialcharsbx($value);
			}
		}

		return $result;
	}

	protected static function getEditInput($userField, $htmlControl)
	{
		if ($userField['ENTITY_VALUE_ID'] < 1 && (string)$userField['SETTINGS']['DEFAULT_VALUE'] !== '')
		{
			$htmlControl['VALUE'] = htmlspecialcharsbx($userField['SETTINGS']['DEFAULT_VALUE']);
		}

		if ($userField['SETTINGS']['ROWS'] < 2)
		{
			$htmlControl['VALIGN'] = 'middle';
			$attributes = [
				'type' => 'text',
				'name' => $htmlControl['NAME'],
			];
			$attributes += array_filter([
				'size' => isset($userField['SETTINGS']['SIZE']) ? (int)$userField['SETTINGS']['SIZE'] : null,
				'maxlength' => isset($userField['SETTINGS']['MAX_LENGTH']) ? (int)$userField['SETTINGS']['MAX_LENGTH'] : null,
				'disabled' => $userField['EDIT_IN_LIST'] !== 'Y',
				'data-multiple' => $userField['MULTIPLE'] !== 'N',
			]);
			
			return sprintf(
				'<input %s value="%s" />',
				Helper\Attributes::stringify($attributes),
				$htmlControl['VALUE']
			);
		}
		else
		{
			$attributes = [
				'name' => $htmlControl['NAME'],
			];
			$attributes += array_filter([
				'cols' => isset($userField['SETTINGS']['SIZE']) ? (int)$userField['SETTINGS']['SIZE'] : null,
				'rows' => isset($userField['SETTINGS']['ROWS']) ? (int)$userField['SETTINGS']['ROWS'] : null,
				'maxlength' => isset($userField['SETTINGS']['MAX_LENGTH']) ? (int)$userField['SETTINGS']['MAX_LENGTH'] : null,
				'disabled' => $userField['EDIT_IN_LIST'] !== 'Y',
				'data-multiple' => $userField['MULTIPLE'] !== 'N',
			]);

			return sprintf(
				'<textarea %s>%s</textarea>',
				Helper\Attributes::stringify($attributes),
				$htmlControl['VALUE']
			);
		}
	}

	protected static function getCopyButton($userField, $htmlControl)
	{
		static::loadMessages();

		Market\Ui\Assets::loadPlugin('Ui.Input.CopyClipboard');
		Market\Ui\Assets::loadMessages([
			'INPUT_COPY_CLIPBOARD_SUCCESS',
			'INPUT_COPY_CLIPBOARD_FAIL',
		]);

		return
			'<button class="adm-btn js-plugin-click" type="button" data-plugin="Ui.Input.CopyClipboard">'
				. static::getLang('UI_USER_FIELD_STRING_COPY')
			. '</button>';
	}
}