<?php

namespace Yandex\Market\Trading\Service\Marketplace;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

class ModelFactory extends TradingService\Reference\ModelFactory
{
	/** @return Model\Cart */
	public function getCartClassName()
	{
		return Model\Cart::class;
	}

	/** @return Model\OrderFacade */
	public function getOrderFacadeClassName()
	{
		return Model\OrderFacade::class;
	}

	/** @return Model\Order */
	public function getOrderClassName()
	{
		return Model\Order::class;
	}
}