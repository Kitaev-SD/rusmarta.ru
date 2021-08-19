<?php

namespace Yandex\Market\Trading\Service\Reference\Action;

use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

abstract class AbstractActivity
{
	protected $provider;
	protected $environment;

	public function __construct(TradingService\Reference\Provider $provider, TradingEntity\Reference\Environment $environment)
	{
		$this->provider = $provider;
		$this->environment = $environment;
	}

	abstract public function getTitle();

	/** @return array|null */
	public function getFilter()
	{
		return null;
	}
}