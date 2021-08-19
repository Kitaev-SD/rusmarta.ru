<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Action\OrderAccept;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

class Action extends TradingService\Marketplace\Action\OrderAccept\Action
{
	use TradingService\MarketplaceDbs\Concerns\Action\HasRegionHandler;
	use TradingService\MarketplaceDbs\Concerns\Action\HasDeliveryDates;
	use TradingService\MarketplaceDbs\Concerns\Action\HasAddress;

	/** @var TradingService\MarketplaceDbs\Provider */
	protected $provider;
	/** @var Request */
	protected $request;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
		parent::includeMessages();
	}

	protected function createRequest(Main\HttpRequest $request, Main\Server $server)
	{
		return new Request($request, $server);
	}

	protected function collectOrder($orderNum, $hasWarnings = false)
	{
		parent::collectOrder($orderNum, $hasWarnings);
		$this->collectShipmentDate();
	}

	protected function collectShipmentDate()
	{
		try
		{
			list($deliveryId) = $this->resolveDelivery();
			$options = $this->provider->getOptions();
			$deliveryOption = $options->getDeliveryOptions()->getItemByServiceId($deliveryId);
			$schedule = $options->getShipmentSchedule();
			$dates = $this->request->getOrder()->getDelivery()->getDates();
			$deliveryDate = $dates !== null ? $dates->getFrom() : null;

			$command = new TradingService\MarketplaceDbs\Command\DeliveryShipmentDate(
				$schedule,
				$deliveryOption,
				$deliveryDate
			);
			$shipmentDate = $command->execute();

			$this->response->setField(
				'order.shipmentDate',
				Market\Data\Date::convertForService($shipmentDate, Market\Data\Date::FORMAT_DEFAULT_SHORT)
			);
		}
		catch (Main\SystemException $exception)
		{
			// nothing
		}
	}

	protected function sanitizeRegionMeaningfulValues($meaningfulValues)
	{
		if ($this->request->getOrder()->getDelivery()->getAddress() !== null)
		{
			$meaningfulValues = array_diff_key($meaningfulValues, [
				'LAT' => true,
				'LON' => true,
			]);
		}

		return $meaningfulValues;
	}

	protected function fillProperties()
	{
		$this->fillAddressProperties();
		$this->fillDeliveryDatesProperties();
		$this->fillUtilProperties();
	}

	protected function fillDelivery()
	{
		list($deliveryId, $price) = $this->resolveDelivery();

		if ((string)$deliveryId !== '')
		{
			$this->order->createShipment($deliveryId, $price);
		}
	}

	protected function resolveDelivery()
	{
		$deliveryRequest = $this->request->getOrder()->getDelivery();
		$partnerType = $deliveryRequest->getPartnerType();
		$price = null;

		if ($this->provider->getDelivery()->isShopDelivery($partnerType))
		{
			$deliveryId = $deliveryRequest->getShopDeliveryId();
			$price = $deliveryRequest->getPrice();
		}
		else
		{
			$deliveryId = $this->environment->getDelivery()->getEmptyDeliveryId();
		}

		return [$deliveryId, $price];
	}

	protected function fillOutlet()
	{
		$deliveryRequest = $this->request->getOrder()->getDelivery();
		$outletRequest = $deliveryRequest->getOutlet();
		$storeField = (string)$this->provider->getOptions()->getOutletStoreField();

		if ($outletRequest !== null && $storeField !== '')
		{
			$deliveryId = $deliveryRequest->getShopDeliveryId();
			$storeId = $this->environment->getStore()->findStore($storeField, $outletRequest->getCode());

			$this->order->setShipmentStore($deliveryId, $storeId);
		}
	}

	protected function resolvePaySystem()
	{
		$method = $this->request->getOrder()->getPaymentMethod();
		$compatibleIds = $this->getCompatiblePaySystems();
		$configuredIds = $this->getConfiguredPaySystemsForMethod($method);
		$matchedIds = array_intersect($compatibleIds, $configuredIds);
		$result = null;

		if (!empty($matchedIds))
		{
			$result = reset($matchedIds);
		}
		else if (!$this->provider->getOptions()->isPaySystemStrict())
		{
			$environmentPaySystem = $this->environment->getPaySystem();
			$servicePaySystem = $this->provider->getPaySystem();
			$meaningfulMap = $servicePaySystem->getMethodMeaningfulMap();

			if (!isset($meaningfulMap[$method])) { return null; }

			$meaningfulMethod = $meaningfulMap[$method];

			foreach ($compatibleIds as $compatibleId)
			{
				$suggestMethods = $environmentPaySystem->suggestPaymentMethod($compatibleId, $meaningfulMap);

				if (!empty($suggestMethods) && in_array($meaningfulMethod, $suggestMethods, true))
				{
					$result = $compatibleId;
					break;
				}
			}
		}

		return (string)$result;
	}

	protected function getCompatiblePaySystems()
	{
		$paySystem = $this->environment->getPaySystem();

		return $paySystem->getCompatible($this->order);
	}

	protected function getConfiguredPaySystemsForMethod($method)
	{
		$result = [];

		/** @var TradingService\MarketplaceDbs\Options\PaySystemOption $paySystemOption */
		foreach ($this->provider->getOptions()->getPaySystemOptions() as $paySystemOption)
		{
			if ($paySystemOption->getMethod() === $method)
			{
				$result[] = $paySystemOption->getPaySystemId();
			}
		}

		return $result;
	}

	protected function check()
	{
		return Market\Result\Facade::merge([
			parent::check(),
			$this->checkDeliveryPrice(),
		]);
	}

	protected function checkDeliveryPrice()
	{
		$validationResult = $this->validateDeliveryPrice();

		if ($validationResult->isSuccess()) { return $validationResult; }

		$allowModifyPrice = $this->provider->getOptions()->isAllowModifyPrice();
		$checkPriceData = $validationResult->getData();

		if ($checkPriceData['SIGN'] > 0) // requested price more then delivery price
		{
			$allowModifyPrice = true;
		}

		if (!$allowModifyPrice) { return $validationResult; }

		$modifyPrice = $this->modifyDeliveryPrice();
		$result = new Market\Result\Base();

		if (!$modifyPrice->isSuccess())
		{
			$result->addErrors($modifyPrice->getErrors());
		}

		return $result;
	}

	protected function validateDeliveryPrice()
	{
		list($deliveryId, $price) = $this->resolveDelivery();
		$result = new Market\Result\Base();

		if ((string)$deliveryId === '' || $price === null) { return $result; }

		$deliveryPrice = $this->order->getShipmentPrice($deliveryId);

		if (Market\Data\Price::round($price) === Market\Data\Price::round($deliveryPrice)) { return $result; }

		$currency = $this->order->getCurrency();

		$message = static::getLang('TRADING_ACTION_ORDER_ACCEPT_ORDER_DELIVERY_PRICE_NOT_MATCH', [
			'#REQUEST_PRICE#' => Market\Data\Currency::format($price, $currency),
			'#DELIVERY_PRICE#' => Market\Data\Currency::format($deliveryPrice, $currency),
		]);
		$result->addError(new Market\Error\Base($message, 'PRICE_NOT_MATCH'));
		$result->setData([
			'SIGN' => $price < $deliveryPrice ? -1 : 1,
		]);

		return $result;
	}

	protected function modifyDeliveryPrice()
	{
		list($deliveryId, $price) = $this->resolveDelivery();
		$result = new Market\Result\Base();

		if ((string)$deliveryId !== '' && $price !== null)
		{
			$setResult = $this->order->setShipmentPrice($deliveryId, $price);

			if (!$setResult->isSuccess())
			{
				$result->addErrors($setResult->getErrors());
			}
		}

		return $result;
	}
}