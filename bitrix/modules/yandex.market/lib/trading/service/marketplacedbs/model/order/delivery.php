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

	public function hasShopDeliveryId()
	{
		return $this->hasField('shopDeliveryId');
	}

	public function getShopDeliveryId()
	{
		return $this->getRequiredField('shopDeliveryId');
	}

	public function getLiftType()
	{
		return $this->getField('liftType');
	}

	public function getLiftPrice()
	{
		return Market\Data\Number::normalize($this->getField('liftPrice'));
	}

	/** @return Delivery\Address|null */
	public function getAddress()
	{
		return $this->getChildModel('address');
	}

	/** @return Delivery\Outlet|null */
	public function getOutlet()
	{
		$result = $this->getChildModel('outlet');

		if ($result !== null) { return $result; }

		if ($this->hasField('outletCode'))
		{
			$reference = $this->getChildModelReference();

			if (!isset($reference['outlet'])) { return null; }

			$modelClassName = $reference['outlet'];
			$relativePath = $this->relativePath . 'outlet.';
			$data = [
				'id' => $this->getField('outletId'),
				'code' => $this->getField('outletCode'),
			];

			$result = $modelClassName::initialize($data, $relativePath);
			$result->setParent($this);

			$this->childModel['outlet'] = $result;
		}

		return $result;
	}

	protected function getChildModelReference()
	{
		$result = [
			'address' => Delivery\Address::class,
			'outlet' => Delivery\Outlet::class,
		];

		return $result + parent::getChildModelReference();
	}
}