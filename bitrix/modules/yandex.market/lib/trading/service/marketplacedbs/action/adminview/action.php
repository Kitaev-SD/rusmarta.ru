<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Action\AdminView;

use Yandex\Market;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

/** @property TradingService\MarketplaceDbs\Model\Order $externalOrder */
class Action extends TradingService\Marketplace\Action\AdminView\Action
{
	use Market\Reference\Concerns\HasMessage;

	public function process()
	{
		parent::process();
		$this->collectTracks();
	}

	protected function collectShipments()
	{
		// nothing
	}

	protected function collectShipmentEdit()
	{
		$allowEdit = (
			$this->isOrderProcessing()
			&& (
				$this->hasRights(TradingEntity\Operation\Order::BOX)
				|| $this->hasRights(TradingEntity\Operation\Order::CIS)
			)
		);

		$this->response->setField('shipmentEdit', $allowEdit);
	}

	protected function collectTracks()
	{
		$delivery = $this->externalOrder->getDelivery();
		$tracks = $delivery->getTracks();

		if ($tracks === null) { return; }

		$codes = [];

		foreach ($tracks as $track)
		{
			$codes[] = (string)$track->getTrackCode();
		}

		if (empty($codes)) { return; }

		$this->response->pushField('properties', [
			'ID' => 'TRACKS',
			'NAME' => self::getMessage('PROPERTY_TRACKING_NUMBER'),
			'VALUE' => implode(', ', $codes),
		]);
	}
}