<?php

namespace Yandex\Market\Trading\Service\Common\Concerns\Action;

use Yandex\Market;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

/**
 * trait HasItemIdMatch
 * @property TradingService\Common\Provider $provider
 * @property TradingEntity\Reference\Environment $environment
 * @property TradingService\Common\Action\SendRequest $request
 * @method TradingEntity\Reference\Order getOrder()
 * @method Market\Api\Model\Order getExternalOrder()
 */
trait HasItemIdMatch
{
	protected function getItemsBasketCodes(array $items)
	{
		$result = [];

		foreach ($items as $item)
		{
			$basketCode = $this->getItemBasketCode($item);

			if ($basketCode !== null)
			{
				$result[] = $basketCode;
			}
		}

		return $result;
	}

	protected function getItemBasketCode(array $item)
	{
		$methods = [
			'xmlId',
			'id',
			'productId',
		];

		$order = $this->getOrder();
		$result = null;

		foreach ($methods as $method)
		{
			if (!isset($item[$method])) { continue; }

			$value = $item[$method];

			if ($method === 'xmlId')
			{
				$result = $order->getBasketItemCode($value, 'XML_ID');
			}
			else if ($method === 'id')
			{
				$itemModel = new Market\Api\Model\Order\Item([ 'id' => $value ]);
				$xmlId = $this->provider->getDictionary()->getOrderItemXmlId($itemModel);

				$result = $order->getBasketItemCode($xmlId, 'XML_ID');
			}
			else if ($method === 'productId')
			{
				$result = $order->getBasketItemCode($value);
			}

			if ($result !== null) { break; }
		}

		return $result;
	}

	protected function getItemId(array $item)
	{
		$externalOrder = null;
		$offerMap = null;
		$result = null;
		$methods = [
			'id',
			'xmlId',
			'productId',
		];

		foreach ($methods as $method)
		{
			if (!isset($item[$method])) { continue; }

			$value = $item[$method];

			if ($method === 'id')
			{
				$result = $value;
			}
			else if ($method === 'xmlId')
			{
				$matches = $this->provider->getDictionary()->parseOrderItemXmlId($value);
				$result = isset($matches['ID']) ? $matches['ID'] : null;
			}
			else if ($method === 'productId')
			{
				if ($externalOrder === null)
				{
					$externalOrder = $this->getExternalOrder();
					$offerMap = $this->getOfferMap($externalOrder->getItems());
				}

				/** @var Market\Api\Model\Order\Item $externalItem */
				foreach ($externalOrder->getItems() as $externalItem)
				{
					if ((string)$externalItem->mapProductId($offerMap) === (string)$value)
					{
						$result = $externalItem->getId();
						break;
					}
				}
			}

			if ($result !== null) { break; }
		}

		return $result;
	}

	protected function getOfferMap(Market\Api\Model\Order\ItemCollection $items)
	{
		$offerIds = $items->getOfferIds();
		$command = new TradingService\Common\Command\OfferMap(
			$this->provider,
			$this->environment
		);

		return $command->make($offerIds);
	}
}
