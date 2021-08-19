<?php

namespace Yandex\Market\Trading\Service\Marketplace\Action\Cart;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

/** @property TradingService\Marketplace\Provider $provider */
class Action extends TradingService\Common\Action\Cart\Action
{
	use TradingService\Marketplace\Concerns\Action\HasBasketWarehouses;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
		parent::includeMessages();
	}

	protected function createRequest(Main\HttpRequest $request, Main\Server $server)
	{
		return new Request($request, $server);
	}

	protected function getPriceCalculationMode()
	{
		return null;
	}

	protected function collectResponse()
	{
		$this->collectTaxSystem();
		$this->collectItems();

		$this->applySelfTest();
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
				'delivery' => false,
			];

			if (isset($this->basketMap[$itemIndex]))
			{
				$basketCode = $this->basketMap[$itemIndex];
				$basketResult = $this->order->getBasketItemData($basketCode);
				$basketData = $basketResult->getData();
				$basketQuantity = isset($basketData['QUANTITY']) ? (float)$basketData['QUANTITY'] : null;

				if ($basketQuantity > 0 && $basketResult->isSuccess())
				{
					$hasValidItems = true;
					$responseItem['count'] = $basketQuantity;
					$responseItem['vat'] = Market\Data\Vat::convertForService($basketData['VAT_RATE']);
					$responseItem['delivery'] = true;
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

	protected function applySelfTest()
	{
		$this->applySelfTestOutOfStock();
	}

	protected function applySelfTestOutOfStock()
	{
		if (!$this->provider->getOptions()->getSelfTestOption()->isOutOfStock()) { return; }

		$this->response->setField('cart.items', []);

		$this->provider->getLogger()->warning(static::getLang(
			'TRADING_MARKETPLACE_CART_SELF_TEST_OUT_OF_STOCK_ON'
		));
	}
}