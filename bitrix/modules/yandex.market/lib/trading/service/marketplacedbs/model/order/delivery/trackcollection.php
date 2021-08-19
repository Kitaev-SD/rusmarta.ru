<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Model\Order\Delivery;

use Yandex\Market;

/** @method Track current() */
class TrackCollection extends Market\Api\Reference\Collection
{
	public static function getItemReference()
	{
		return Track::class;
	}
}