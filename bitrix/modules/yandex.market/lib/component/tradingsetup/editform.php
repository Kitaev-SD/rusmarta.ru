<?php

namespace Yandex\Market\Component\TradingSetup;

use Yandex\Market;
use Yandex\Market\Trading\Service as TradingService;
use Yandex\Market\Trading\Setup as TradingSetup;
use Bitrix\Main;

class EditForm extends Market\Component\Model\EditForm
{
	use Market\Component\Concerns\HasUiService;
	use Market\Reference\Concerns\HasMessage;

	protected $repository;

	public function getFields(array $select = [], $item = null)
	{
		$result = parent::getFields($select, $item);
		$result = $this->getRepository()->extendCommonFields($result);

		if (!empty($item['ID']))
		{
			$result = $this->getRepository()->markNotEditableFields($result);
		}

		return $result;
	}

	public function modifyRequest($request, $fields)
	{
		if (isset($fields['CODE'], $request['SITE_ID']) && trim($request['CODE']) === '')
		{
			$request['CODE'] = $request['SITE_ID'];
		}

		if (isset($request['NAME']) && trim($request['NAME']) === '')
		{
			unset($request['NAME']);
		}

		if (!empty($request['ID']))
		{
			$request = $this->getRepository()->unsetNotEditableValues($request);
		}

		return parent::modifyRequest($request, $fields);
	}

	public function validate($data, array $fields = null)
	{
		if ($fields !== null && !isset($data['NAME']))
		{
			$fields = array_filter($fields, static function(array $field) {
				return ($field['FIELD_NAME'] !== 'NAME');
			});
		}

		return parent::validate($data, $fields);
	}

	public function load($primary, array $select = [], $isCopy = false)
	{
		$result = parent::load($primary, $select, $isCopy);

		if ($isCopy)
		{
			$copyNameMarker = (string)static::getMessage('COPY_NAME_MARKER', null, '');

			if (
				$copyNameMarker !== ''
				&& isset($result['NAME'])
				&& Market\Data\TextString::getPositionCaseInsensitive($result['NAME'], $copyNameMarker) === false
			)
			{
				$result['NAME'] .= ' ' . $copyNameMarker;
			}

			if (isset($result['CODE']))
			{
				$suffix = randString(3);
				$suffix = Market\Data\TextString::toLower($suffix);

				$result['CODE'] .= '-' . $suffix;
			}
		}

		return $result;
	}

	public function add($fields)
	{
		$result = new Main\Entity\AddResult();

		try
		{
			if ($this->getComponentParam('COPY'))
			{
				$origin = $this->loadOrigin();
				$model = $this->installModel($fields);

				$this->copySettings($origin, $model);
			}
			else
			{
				$model = $this->installModel($fields);
			}

			$result->setId($model->getId());
		}
		catch (Main\SystemException $exception)
		{
			$result->addError(new Main\Error(
				$exception->getMessage()
			));
		}

		return $result;
	}

	/** @return TradingSetup\Model */
	protected function loadOrigin()
	{
		$copyId = $this->getComponentParam('PRIMARY');
		$modelClass = $this->getModelClass();
		$model = $modelClass::loadById($copyId);

		if (!($model instanceof TradingSetup\Model))
		{
			throw new Main\InvalidOperationException();
		}

		return $model;
	}

	/** @return TradingSetup\Model */
	protected function installModel($fields)
	{
		$modelClass = $this->getModelClass();
		$model = new $modelClass($fields);

		if (!($model instanceof TradingSetup\Model))
		{
			throw new Main\InvalidOperationException();
		}

		if (!$model->isInstalled() && TradingService\Migration::isDeprecated($model->getServiceCode()))
		{
			throw new Main\SystemException('cant install deprecated service');
		}

		$model->install();
		$model->activate();

		return $model;
	}

	protected function copySettings(TradingSetup\Model $from, TradingSetup\Model $to)
	{
		$reservedKeys = $from->getReservedSettingsKeys();
		$settings = $from->getSettings()->getValues();
		$settings = array_diff_key($settings, array_flip($reservedKeys));
		$dataClass = $this->getDataClass();

		if ($from->getServiceCode() !== $to->getServiceCode() || $from->getBehaviorCode() !== $to->getBehaviorCode())
		{
			$options = $to->wakeupService()->getOptions();
			$fields = $options->getFields($to->getEnvironment(), $to->getSiteId());

			$settings = array_intersect_key($settings, $fields);
			$settings = $this->fillSettingsDefaults($fields, $settings);
			$settings = $this->sanitizeSettingsEnum($fields, $settings);
		}

		$dataClass::update($to->getId(), [
			'SETTINGS' => $this->convertSettingsToRows($settings),
		]);
	}

	protected function fillSettingsDefaults($fields, $values)
	{
		$result = $values;

		foreach ($fields as $fieldName => $field)
		{
			if (!isset($field['SETTINGS']['DEFAULT_VALUE'])) { continue; }
			if (!empty($field['SETTINGS']['READONLY'])) { continue; }

			$isHidden = (!empty($field['HIDDEN']) && $field['HIDDEN'] === 'Y');
			$defaultValue = $field['SETTINGS']['DEFAULT_VALUE'];
			$value = Market\Utils\Field::getChainValue($result, $fieldName, Market\Utils\Field::GLUE_BRACKET);

			if ($value === null || $isHidden || $fieldName === 'PERSON_TYPE')
			{
				Market\Utils\Field::setChainValue($result, $fieldName, $defaultValue, Market\Utils\Field::GLUE_BRACKET);
			}
		}

		return $result;
	}

	protected function sanitizeSettingsEnum($fields, $values)
	{
		$result = $values;

		foreach ($fields as $fieldName => $field)
		{
			$value = Market\Utils\Field::getChainValue($result, $fieldName, Market\Utils\Field::GLUE_BRACKET);
			$userField = Market\Ui\UserField\Helper\Field::extend($field);
			$userField = Market\Ui\UserField\Helper\Field::extendValue($userField, $value, $values);
			$isMultiple = ($userField['MULTIPLE'] !== 'N');

			if (empty($userField['USER_TYPE']['CLASS_NAME'])) { continue; }
			if (!is_callable([$userField['USER_TYPE']['CLASS_NAME'], 'GetList'])) { continue; }

			$query = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'GetList'], $userField);
			$enum = Market\Ui\UserField\Helper\Enum::toArray($query);
			$enumIds = array_column($enum, 'ID');
			$valueIds = $isMultiple && is_array($value) ? $value : [ $value ];
			$existIds = array_intersect($valueIds, $enumIds);

			if (!empty($existIds)) { continue; }

			if (!empty($userField['SETTINGS']['DEFAULT_VALUE']))
			{
				$defaultValue = $userField['SETTINGS']['DEFAULT_VALUE'];
				$enumDefaultIds = $isMultiple && is_array($defaultValue) ? $defaultValue : [ $defaultValue ];
			}
			else
			{
				$enumDefaults = array_filter($enum, static function($option) {
					return isset($option['DEF']) && $option['DEF'] === 'Y';
				});
				$enumDefaultIds = array_column($enumDefaults, 'ID');
			}

			if (empty($enumDefaultIds))
			{
				Market\Utils\Field::unsetChainValue($result, $fieldName, Market\Utils\Field::GLUE_BRACKET);
			}
			else if ($isMultiple)
			{
				Market\Utils\Field::setChainValue($result, $fieldName, $enumDefaultIds, Market\Utils\Field::GLUE_BRACKET);
			}
			else
			{
				Market\Utils\Field::setChainValue($result, $fieldName, reset($enumDefaultIds), Market\Utils\Field::GLUE_BRACKET);
			}
		}

		return $result;
	}

	protected function convertSettingsToRows(array $settings)
	{
		$result = [];

		foreach ($settings as $key => $value)
		{
			$result[] = [
				'NAME' => $key,
				'VALUE' => $value,
			];
		}

		return $result;
	}

	protected function getRepository()
	{
		if ($this->repository === null)
		{
			$this->repository = $this->makeRepository();
		}

		return $this->repository;
	}

	protected function makeRepository()
	{
		$uiService = $this->getUiService();
		$modelClass = $this->getModelClass();

		return new Repository($uiService, $modelClass);
	}
}