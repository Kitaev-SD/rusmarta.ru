<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Action\SendItems;

use Yandex\Market\Trading\Service as TradingService;

class Request extends TradingService\Common\Action\SendRequest
{
	public function getItems()
	{
		return (array)$this->getRequiredField('items');
	}

	public function getReason()
	{
		return (string)$this->getRequiredField('reason');
	}
}