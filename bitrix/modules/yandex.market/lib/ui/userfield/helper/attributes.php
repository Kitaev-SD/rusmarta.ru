<?php

namespace Yandex\Market\Ui\UserField\Helper;

use Yandex\Market;

class Attributes
{
	public static function convertNameToId($name)
	{
		$result = str_replace(['[', ']', '-', '__'], '_', $name);
		$result = trim($result, '_');

		return $result;
	}

	public static function extractFromSettings($userFieldSettings, $settingNames = null)
	{
		$result = isset($userFieldSettings['ATTRIBUTES']) ? (array)$userFieldSettings['ATTRIBUTES'] : [];

		if ($settingNames === null)
		{
			$settingNames = [
				'READONLY',
				'STYLE',
				'PLACEHOLDER',
			];
		}

		foreach ($settingNames as $settingName)
		{
			if (
				isset($userFieldSettings[$settingName])
				&& $userFieldSettings[$settingName] !== ''
				&& $userFieldSettings[$settingName] !== false
			)
			{
				$setting = $userFieldSettings[$settingName];
				$attributeName = Market\Data\TextString::toLower($settingName);

				$result[$attributeName] = $setting;
			}
		}

		return $result;
	}

	public static function insert($html, $attributes)
	{
		if (!empty($attributes))
		{
			$attributesString = static::stringify($attributes);
			$result = preg_replace_callback('/(<input|<textarea|<select)(.*?)(\/?>)/si', static function($matches) use ($attributesString) {
				return preg_match('/type=["\']button["\']/i', $matches[2])
					? $matches[0]
					: $matches[1] . $matches[2] . ' ' . $attributesString . $matches[3];
			}, $html);
		}
		else
		{
			$result = $html;
		}

		return $result;
	}

	public static function insertDataName($html, $name, $baseName, $attributeName = 'data-name')
	{
		return preg_replace_callback('/(<input|<textarea|<select)(.*?)(\/?>)/si', static function($matches) use ($name, $baseName, $attributeName) {
			list(, $tagStart, $attributes, $tagEnding) = $matches;
			$dataName = $name;

			if (preg_match('/type=["\']button["\']/i', $attributes)) { return $matches[0]; }

			if (preg_match('/(^|\s)name=["\'](.*?)["\']/', $attributes, $nameMatch))
			{
				$inputName = $nameMatch[2];

				if ($inputName !== $baseName && Market\Data\TextString::getPosition($inputName, $baseName) === 0)
				{
					$leftName = Market\Data\TextString::getSubstring($inputName, strlen($baseName));
					$leftName = preg_replace('/\[\d*]$/', '', $leftName);

					if ($leftName !== '')
					{
						$dataName = '[' . $dataName . ']' . $leftName;
					}
				}
			}

			return
				$tagStart
				. $attributes
				. ($attributeName . '="' . htmlspecialcharsbx($dataName) . '"')
				. $tagEnding;
		}, $html);
	}

	public static function delayPluginInitialization($html)
	{
		return preg_replace('/([\s"\'])js-plugin([\s"\'])/', '$1js-plugin-delayed$2', $html);
	}

	public static function sliceInputName($html)
	{
		return preg_replace('/(<input|<textarea|<select)(.*?) name=".*?"(.*?\/?>)/si', '$1$2$3', $html);
	}

	public static function stringify($attributes)
	{
		if (is_array($attributes))
		{
			$htmlAttributes = [];

			foreach ($attributes as $key => $value)
			{
				if (is_numeric($key))
				{
					$htmlAttributes[] = $value;
				}
				else if ($value === false || $value === null)
				{
					// skip
				}
				else if (is_array($value))
				{
					$valueEncoded = Market\Utils::jsonEncode($value, JSON_UNESCAPED_UNICODE);

					$htmlAttributes[] = htmlspecialcharsbx($key) . '="' . htmlspecialcharsbx($valueEncoded) . '"';
				}
				else if ($value === true || (string)$value === '')
				{
					$htmlAttributes[] = htmlspecialcharsbx($key);
				}
				else
				{
					$htmlAttributes[] = htmlspecialcharsbx($key) . '="' . htmlspecialcharsbx($value) . '"';
				}
			}

			$result = implode(' ', $htmlAttributes);
		}
		else
		{
			$result = (string)$attributes;
		}

		return $result;
	}
}