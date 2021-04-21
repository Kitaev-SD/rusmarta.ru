<?php

namespace Yandex\Market\Trading\Service\Reference\Options;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Trading\Service as TradingService;
use Yandex\Market\Trading\Entity as TradingEntity;

abstract class Fieldset
{
	protected $provider;
	protected $values;
	
	public function __construct(TradingService\Reference\Provider $provider)
	{
		$this->provider = $provider;
	}

	public function getFieldDescription(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return [
			'MULTIPLE' => 'N',
			'FIELDS' => $this->getFields($environment, $siteId),
		];
	}

	abstract public function getFields(TradingEntity\Reference\Environment $environment, $siteId);

	public function setValues(array $values)
	{
		$this->values = $values;
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
		return $this->values;
	}
}