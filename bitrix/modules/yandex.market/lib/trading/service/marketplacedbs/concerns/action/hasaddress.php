<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Concerns\Action;

use Yandex\Market;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

/**
 * trait HasMeaningfulProperties
 * @property TradingService\Common\Provider $provider
 * @property TradingEntity\Reference\Environment $environment
 * @property TradingService\MarketplaceDbs\Action\Cart\Request|TradingService\MarketplaceDbs\Action\OrderAccept\Request|TradingService\MarketplaceDbs\Action\OrderStatus\Request $request
 * @property TradingEntity\Reference\Order $order
 * @method setMeaningfulPropertyValues($propertyValues)
 * @method getConfiguredMeaningfulProperties($meaningfulNames)
 */
trait HasAddress
{
	protected function fillAddressProperties()
	{
		$delivery = $this->getRequestDelivery();
		$address = $this->getRequestAddress();

		if ($address === null) { return; }

		$propertyValues = $this->getAddressProperties($address);
		$propertyValues = $this->extendDeliveryProperties($delivery, $propertyValues);

		$this->setMeaningfulPropertyValues($propertyValues);
	}

	public function getAddressProperties(TradingService\MarketplaceDbs\Model\Cart\Delivery\Address $address)
	{
		$useDetails = $this->provider->getOptions()->useAddressDetails();
		$configuredAddressParts = $useDetails ? $this->getConfiguredAddressParts() : [];
		$result = [
			'ZIP' => $address->getMeaningfulZip(),
			'CITY' => $address->getMeaningfulCity(),
			'ADDRESS' => $address->getMeaningfulAddress($configuredAddressParts),
			'LAT' => $address->getLat(),
			'LON' => $address->getLon(),
		];

		if ($useDetails)
		{
			$result += $address->getAddressValues();
		}

		return $result;
	}

	protected function extendDeliveryProperties(Market\Api\Model\Cart\Delivery $delivery, $propertyValues)
	{
		if (!($delivery instanceof TradingService\MarketplaceDbs\Model\Order\Delivery)) { return $propertyValues; }
		if ($delivery->getLiftType() === null) { return $propertyValues; }

		/** @var Market\Api\Model\Cart $cart */
		$cart = $delivery->getParent();
		$deliveryService = $this->provider->getDelivery();
		$values = [
			'LIFT_TYPE' => new Market\Data\Type\EnumValue(
				$delivery->getLiftType(),
				$deliveryService->getLiftTitle($delivery->getLiftType(), 'ALONE')
			),
			'LIFT_PRICE' => Market\Data\Currency::format(
				$delivery->getLiftPrice(),
				$cart->getCurrency()
			),
		];
		$ignore = [
			'LIFT_TYPE' => new Market\Data\Type\EnumValue(
				TradingService\MarketplaceDbs\Delivery::LIFT_NOT_NEEDED,
				$deliveryService->getLiftTitle(TradingService\MarketplaceDbs\Delivery::LIFT_NOT_NEEDED, 'ALONE')
			),
			'LIFT_PRICE' => Market\Data\Currency::format(
				0,
				$cart->getCurrency()
			),
		];
		$configuredMap = $this->provider->getOptions()->useAddressDetails()
			? $this->getConfiguredMeaningfulProperties(array_keys($values))
			: [];

		if (!isset($configuredMap['LIFT_PRICE']) && $this->provider->getOptions()->includeLiftPrice())
		{
			unset($values['LIFT_PRICE']);
		}

		foreach ($values as $key => $value)
		{
			if (isset($configuredMap[$key]))
			{
				$propertyValues[$key] = $value;
			}
			else if ((string)$value !== '')
			{
				if (isset($ignore[$key]) && (string)$ignore[$key] === (string)$value) { continue; }

				$propertyValues['ADDRESS'] = $this->insertAddressValueAdditional($propertyValues['ADDRESS'], $value);
			}
		}

		return $propertyValues;
	}

	protected function insertAddressValueAdditional($address, $additional)
	{
		if (preg_match('/^(.*?) \((.*)\)$/', $address, $matches))
		{
			$result = sprintf('%s (%s, %s)', $matches[1], $matches[2], $additional);
		}
		else
		{
			$result = $address . ' (' . $additional . ')';
		}

		return $result;
	}

	protected function getRequestDelivery()
	{
		$order = \method_exists($this->request, 'getOrder')
			? $this->request->getOrder()
			: $this->request->getCart();

		return $order->getDelivery();
	}

	protected function getRequestAddress()
	{
		return $this->getRequestDelivery()->getAddress();
	}

	protected function getConfiguredAddressParts()
	{
		$addressFields = TradingService\MarketplaceDbs\Model\Cart\Delivery\Address::getAddressFields();
		$configuredMap = $this->getConfiguredMeaningfulProperties($addressFields);

		return array_keys($configuredMap);
	}
}