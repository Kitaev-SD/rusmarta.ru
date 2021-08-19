<?php

namespace Yandex\Market\Trading\Service\Common\Concerns\Action;

use Yandex\Market;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

/**
 * trait HasOrder
 * @property TradingService\Common\Provider $provider
 * @property TradingEntity\Reference\Environment $environment
 * @property TradingService\Common\Action\SendRequest $request
 */
trait HasOrder
{
	/** @var TradingEntity\Reference\Order */
	protected $order;

	protected function getOrder()
	{
		if ($this->order === null)
		{
			$this->order = $this->loadOrder();
		}

		return $this->order;
	}

	protected function loadOrder()
	{
		$orderId = $this->request->getInternalId();
		$orderRegistry = $this->environment->getOrderRegistry();

		return $orderRegistry->loadOrder($orderId);
	}

	protected function updateOrder(TradingEntity\Reference\Order $order)
	{
		$updateResult = $order->update();

		Market\Result\Facade::handleException($updateResult);
	}
}