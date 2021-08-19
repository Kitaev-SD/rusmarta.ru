<?php

namespace Yandex\Market\Trading\Service\Common\Command;

use Yandex\Market;
use Yandex\Market\Trading\Service as TradingService;
use Yandex\Market\Trading\Entity as TradingEntity;

class OfferMap
{
	protected $provider;
	protected $environment;

	public function __construct(
		TradingService\Common\Provider $provider,
		TradingEntity\Reference\Environment $environment
	)
	{
		$this->provider = $provider;
		$this->environment = $environment;
	}

	public function make(array $offerIds)
	{
		$options = $this->provider->getOptions();
		$skuMap = $options->getProductSkuMap();
		$skuPrefix = $options->getProductSkuPrefix();
		$maps = [];

		if ($skuPrefix !== '')
		{
			$prefixMap = $this->mapOfferWithPrefix($offerIds, $skuPrefix);
			$offerIds = array_values($prefixMap);

			$maps[] = $prefixMap;
		}

		if (!empty($skuMap))
		{
			$maps[] = $this->environment->getProduct()->getOfferMap($offerIds, $skuMap);
		}

		return $this->combineOfferMaps($maps);
	}

	protected function mapOfferWithPrefix($offerIds, $prefix)
	{
		$prefixLength = Market\Data\TextString::getLength($prefix);
		$result = [];

		foreach ($offerIds as $offerId)
		{
			$productId = $offerId;

			if (Market\Data\TextString::getPosition($offerId, $prefix) === 0)
			{
				$productId = Market\Data\TextString::getSubstring($offerId, $prefixLength);
			}

			$result[$offerId] = $productId;
		}

		return $result;
	}

	protected function combineOfferMaps(array $maps)
	{
		if (empty($maps)) { return null; }

		$first = array_shift($maps);
		$second = $this->combineOfferMaps($maps);

		if ($second === null) { return $first; }

		$result = [];

		foreach ($first as $originId => $offerId)
		{
			if (!isset($second[$offerId])) { continue; }

			$result[$originId] = $second[$offerId];
		}

		return $result;
	}
}