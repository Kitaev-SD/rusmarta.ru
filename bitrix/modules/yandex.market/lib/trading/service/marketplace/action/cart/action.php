<?php

namespace Yandex\Market\Trading\Service\Marketplace\Action\Cart;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

class Action extends TradingService\Common\Action\Cart\Action
{
	protected function createRequest(Main\HttpRequest $request, Main\Server $server)
	{
		return new Request($request, $server);
	}

	protected function collectResponse()
	{
		$this->collectTaxSystem();
		$this->collectItems();
	}

	protected function collectItems()
	{
		$items = $this->request->getCart()->getItems();
		$hasValidItems = false;
		$hasTaxSystem = ($this->getTaxSystem() !== '');
		$disabledKeys = [];

		if (!$hasTaxSystem)
		{
			$disabledKeys['vat'] = true;
		}

		/** @var TradingService\Marketplace\Model\Cart\Item $item */
		foreach ($items as $itemIndex => $item)
		{
			$feedId = $item->getFeedId();
			$offerId = $item->getOfferId();
			$responseItem = [
				'feedId' => $feedId,
				'offerId' => $offerId,
				'count' => 0,
				'vat' => 'NO_VAT',
			];

			if (isset($this->basketMap[$itemIndex]))
			{
				$basketCode = $this->basketMap[$itemIndex];
				$basketResult = $this->order->getBasketItemData($basketCode);

				if ($basketResult->isSuccess())
				{
					$hasValidItems = true;
					$basketData = $basketResult->getData();
					$responseItem['count'] = (int)$basketData['QUANTITY'];
					$responseItem['vat'] = Market\Data\Vat::convertForService($basketData['VAT_RATE']);
				}
			}

			$responseItem = array_diff_key($responseItem, $disabledKeys);

			$this->response->pushField('cart.items', $responseItem);
		}

		if (!$hasValidItems)
		{
			$this->response->setField('cart.items', []);
		}
	}
}