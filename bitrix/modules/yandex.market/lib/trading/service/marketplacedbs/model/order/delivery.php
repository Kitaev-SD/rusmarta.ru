<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Model\Order;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Service as TradingService;

class Delivery extends Market\Api\Model\Order\Delivery
{
	public function getPartnerType()
	{
		return $this->getField('deliveryPartnerType');
	}

	public function getType()
	{
		return $this->getField('type');
	}

	public function getShopDeliveryId()
	{
		return $this->getRequiredField('shopDeliveryId');
	}

	/** @return Delivery\Address|null */
	public function getAddress()
	{
		return $this->getChildModel('address');
	}

	/** @return Delivery\Outlet|null */
	public function getOutlet()
	{
		return $this->getChildModel('outlet');
	}

	/** @return Delivery\TrackCollection|null */
	public function getTracks()
	{
		return $this->getChildCollection('tracks');
	}

	protected function getChildModelReference()
	{
		$result = [
			'address' => Delivery\Address::class,
			'outlet' => Delivery\Outlet::class,
		];

		return $result + parent::getChildModelReference();
	}

	protected function getChildCollectionReference()
	{
		$result = [
			'tracks' => Delivery\TrackCollection::class,
		];

		return $result + parent::getChildCollectionReference();
	}
}