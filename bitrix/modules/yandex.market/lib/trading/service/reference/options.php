<?php

namespace Yandex\Market\Trading\Service\Reference;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Trading\Entity as TradingEntity;

abstract class Options
{
	protected $provider;
	protected $values;
	protected $fieldset = [];
	protected $fieldsetCollection = [];

	public function __construct(Provider $provider)
	{
		$this->provider = $provider;
	}

	abstract public function getTitle($version = '');

	abstract public function getTabs();

	abstract public function getFields(TradingEntity\Reference\Environment $environment, $siteId);

	public function getSetupId()
	{
		return $this->getRequiredValue('SETUP_ID');
	}

	public function getSiteId()
	{
		return $this->getRequiredValue('SITE_ID');
	}

	public function getPlatformId()
	{
		return $this->getRequiredValue('PLATFORM_ID');
	}

	public function setValues(array $values)
	{
		$leftValues = $this->setFieldsetValues($values);
		$leftValues = $this->setFieldsetCollectionValues($leftValues);

		$this->values = $leftValues;
		$this->applyValues();
	}

	protected function setFieldsetValues(array $values)
	{
		$map = $this->getFieldsetMap();

		if (empty($map))
		{
			$leftValues = $values;
		}
		else
		{
			foreach ($map as $key => $dummy)
			{
				$fieldsetValues = isset($values[$key]) && is_array($values[$key])
					? $values[$key]
					: [];

				$this->getFieldset($key)->setValues($fieldsetValues);
			}

			$leftValues = array_diff_key($values, $map);
		}

		return $leftValues;
	}

	protected function setFieldsetCollectionValues(array $values)
	{
		$map = $this->getFieldsetCollectionMap();

		if (empty($map))
		{
			$leftValues = $values;
		}
		else
		{
			foreach ($map as $key => $dummy)
			{
				$fieldsetValues = isset($values[$key]) && is_array($values[$key])
					? $values[$key]
					: [];

				$this->getFieldsetCollection($key)->setValues($fieldsetValues);
			}

			$leftValues = array_diff_key($values, $map);
		}

		return $leftValues;
	}

	protected function applyValues()
	{
		// nothing by default
	}

	public function getValue($key)
	{
		return isset($this->values[$key]) ? $this->values[$key] : null;
	}

	public function getRequiredValue($key)
	{
		$result = $this->getValue($key);

		if (Market\Utils\Value::isEmpty($result))
		{
			throw new Main\SystemException('Required option ' . $key . ' not set');
		}

		return $result;
	}

	public function getValues()
	{
		$result = $this->values;
		$result += $this->getFieldsetValues();
		$result += $this->getFieldsetCollectionValues();

		return $result;
	}

	protected function getFieldsetValues()
	{
		$result = [];

		foreach ($this->getFieldsetMap() as $key => $dummy)
		{
			$result[$key] = $this->getFieldset($key)->getValues();
		}

		return $result;
	}

	protected function getFieldsetCollectionValues()
	{
		$result = [];

		foreach ($this->getFieldsetCollectionMap() as $key => $dummy)
		{
			$result[$key] = $this->getFieldsetCollection($key)->getValues();
		}

		return $result;
	}

	/**
	 * @return array<string, Options\Fieldset>
	 */
	protected function getFieldsetMap()
	{
		return [];
	}

	/**
	 * @param $key
	 *
	 * @return Options\Fieldset
	 * @throws Main\ArgumentException
	 */
	protected function getFieldset($key)
	{
		if (!isset($this->fieldset[$key]))
		{
			$this->fieldset[$key] = $this->createFieldset($key);
		}

		return $this->fieldset[$key];
	}

	protected function createFieldset($key)
	{
		$classMap = $this->getFieldsetMap();

		if (!isset($classMap[$key]))
		{
			throw new Main\ArgumentException(sprintf('Fieldset %s not defined', $key));
		}

		$className = $classMap[$key];

		return new $className($this->provider);
	}

	/**
	 * @return array<string, Options\FieldsetCollection>
	 */
	protected function getFieldsetCollectionMap()
	{
		return [];
	}

	/**
	 * @param $key
	 *
	 * @return Options\FieldsetCollection
	 * @throws Main\ArgumentException
	 */
	protected function getFieldsetCollection($key)
	{
		if (!isset($this->fieldsetCollection[$key]))
		{
			$this->fieldsetCollection[$key] = $this->createFieldsetCollection($key);
		}

		return $this->fieldsetCollection[$key];
	}

	protected function createFieldsetCollection($key)
	{
		$classMap = $this->getFieldsetCollectionMap();

		if (!isset($classMap[$key]))
		{
			throw new Main\ArgumentException(sprintf('Fieldset collection %s not defined', $key));
		}

		$className = $classMap[$key];

		return new $className($this->provider);
	}
}