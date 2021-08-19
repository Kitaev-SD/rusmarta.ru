<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Model\Order\Delivery;

use Yandex\Market;

class Track extends Market\Api\Reference\Model
{
	/** @return string */
	public function getTrackCode()
	{
		return (string)$this->getField('trackCode');
	}

	/** @return string  */
	public function getDeliveryServiceId()
	{
		return (string)$this->getField('deliveryServiceId');
	}
}