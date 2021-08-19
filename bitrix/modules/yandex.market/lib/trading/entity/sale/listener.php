<?php

namespace Yandex\Market\Trading\Entity\Sale;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Sale;

class Listener extends Market\Trading\Entity\Reference\Listener
{
	const STATE_PROCESSING = 1;
	const STATE_SAVING = 2;

	protected static $statusEventQueue = [];
	protected static $eventOrderList = [];
	protected static $eventOrderState = [];
	protected static $tradingSetupCache = [];
	protected static $watches = [];
	protected static $internalChanges = [];
	protected static $handledSaleOrderBeforeSaved = false;

	public static function onBeforeSaleOrderSetField(Main\Event $event)
	{
		$order = $event->getParameter('ENTITY');
		$name = $event->getParameter('NAME');
		$value = $event->getParameter('VALUE');
		$result = null;

		if ($order instanceof Sale\Order && $order->getField($name) !== $value && static::isAdminRequest())
		{
			$submitStatus = null;

			if ($name === 'STATUS_ID')
			{
				$submitStatus = $value;
			}
			else if ($name === 'CANCELED' && $value === 'Y')
			{
				static::addWatch($order->getId(), [
					'FIELD' => 'ORDER.CANCELED',
					'TARGET' => 'Y',
					'PATH' => 'send/status',
					'PAYLOAD' => [
						'status' => Status::STATUS_CANCELED,
					],
				]);
			}

			if ($submitStatus !== null)
			{
				$result = static::sendStatusForEventBefore($order, $submitStatus);
			}
		}

		return $result;
	}

	public static function onBeforeSaleShipmentSetField(Main\Event $event)
	{
		$shipment = $event->getParameter('ENTITY');
		$name = $event->getParameter('NAME');
		$value = $event->getParameter('VALUE');
		$result = null;

		if ($shipment instanceof Sale\Shipment && $shipment->getField($name) !== $value && static::isAdminRequest())
		{
			$order = static::getShipmentOrder($shipment);

			if ($order === null) { return null; }

			if (
				($name === Status::STATUS_ALLOW_DELIVERY || $name === Status::STATUS_DEDUCTED)
				&& $value === 'Y'
				&& static::isShipmentSiblingsFilled($shipment, $name, $value)
			)
			{
				$result = static::sendStatusForEventBefore($order, $name);
			}
			else if ($name === 'TRACKING_NUMBER' && !Market\Utils\Value::isEmpty($value))
			{
				static::addWatch($order->getId(), [
					'FIELD' => 'SHIPMENT.TRACKING_NUMBER',
					'TARGET' => $value,
					'PATH' => 'send/track',
					'PAYLOAD' => [
						'trackCode' => $value,
						'deliveryId' => $shipment->getDeliveryId(),
					],
				]);
			}
		}

		return $result;
	}

	public static function onBeforeSalePaymentSetField(Main\Event $event)
	{
		$payment = $event->getParameter('ENTITY');
		$name = $event->getParameter('NAME');
		$value = $event->getParameter('VALUE');
		$result = null;

		if ($payment instanceof Sale\Payment && $payment->getField($name) !== $value && static::isAdminRequest())
		{
			$submitStatus = null;

			if (
				$name === 'PAID' && $value === 'Y'
				&& static::isPaymentOrderPaid($payment)
			)
			{
				$submitStatus = Status::STATUS_PAYED;
			}

			if ($submitStatus !== null && ($order = static::getPaymentOrder($payment)))
			{
				$result = static::sendStatusForEventBefore($order, $submitStatus);
			}
		}

		return $result;
	}

	public static function onSaleStatusOrderChange(Main\Event $event)
	{
		$order = $event->getParameter('ENTITY');
		$newStatus = $event->getParameter('VALUE');
		$oldStatus = $event->getParameter('OLD_VALUE');

		if ($newStatus !== $oldStatus && $order instanceof Sale\Order)
		{
			static::sendStatusForEventAfter($order, $newStatus);
		}
	}

	public static function onSaleOrderCanceled(Main\Event $event)
	{
		$order = $event->getParameter('ENTITY');

		if ($order instanceof Sale\Order && $order->isCanceled())
		{
			static::sendStatusForEventAfter($order, Status::STATUS_CANCELED);
		}
	}

	public static function onShipmentAllowDelivery(Main\Event $event)
	{
		$shipment = $event->getParameter('ENTITY');

		if ($shipment instanceof Sale\Shipment)
		{
			/** @var Sale\ShipmentCollection $shipmentCollection */
			$shipmentCollection = $shipment->getCollection();
			$order = $shipmentCollection->getOrder();

			if ($shipmentCollection->isAllowDelivery())
			{
				static::sendStatusForEventAfter($order, Status::STATUS_ALLOW_DELIVERY);
			}
		}
	}

	public static function onShipmentTrackingNumberChange(Main\Event $event)
	{
		$shipment = $event->getParameter('ENTITY');

		if (!($shipment instanceof Sale\Shipment)) { return; }

		$trackNumber = trim($shipment->getField('TRACKING_NUMBER'));
		$deliveryId = $shipment->getDeliveryId();
		$order = static::getShipmentOrder($shipment);

		if ($order === null || $trackNumber === '' || $deliveryId <= 0) { return; }

		static::sendTrackingNumber($order, $trackNumber, $deliveryId);
	}

	public static function onSaleOrderPaid(Main\Event $event)
	{
		$order = $event->getParameter('ENTITY');

		if ($order instanceof Sale\Order && $order->isPaid())
		{
			static::sendStatusForEventAfter($order, Status::STATUS_PAYED);
		}
	}

	public static function onShipmentDeducted(Main\Event $event)
	{
		$shipment = $event->getParameter('ENTITY');

		if ($shipment instanceof Sale\Shipment)
		{
			/** @var Sale\ShipmentCollection $shipmentCollection */
			$shipmentCollection = $shipment->getCollection();
			$order = $shipmentCollection->getOrder();

			if ($shipmentCollection->isShipped())
			{
				static::sendStatusForEventAfter($order, Status::STATUS_DEDUCTED);
			}
		}
	}

	/** @deprecated */
	protected static function handleSaleOrderBeforeSaved()
	{
		trigger_error(self::class . '::handleSaleOrderBeforeSaved is deprecated', E_USER_DEPRECATED);
	}

	public static function onSaleOrderBeforeSaved(Main\Event $event)
	{
		if (!static::isAdminRequest()) { return null; }

		$order = $event->getParameter('ENTITY');

		return static::processSaleOrderChangeEvent($order, static::STATE_PROCESSING);
	}

	public static function onSaleOrderSaved(Main\Event $event)
	{
		if (static::isAdminRequest()) { return null; }

		$order = $event->getParameter('ENTITY');

		static::processSaleOrderChangeEvent($order, static::STATE_SAVING);
	}

	protected static function processSaleOrderChangeEvent(Sale\OrderBase $internalOrder, $state)
	{
		$result = new Main\EventResult(Main\EventResult::SUCCESS);
		$tradingInfo = static::getTradingInfo($internalOrder);

		if ($tradingInfo === null) { return $result; }

		$actions = static::makeOrderActions($internalOrder, $tradingInfo);

		foreach ($actions as $action)
		{
			$payload = static::makeProcedurePayload($action);

			if ($payload === null) { continue; }

			$procedureData = new Listener\ProcedureData($action['PATH'], $payload);
			$actionResult = static::processOrderAction($internalOrder, $tradingInfo, $procedureData, $state);

			if ($actionResult->getType() === Main\EventResult::ERROR)
			{
				$result = $actionResult;
				break;
			}
		}

		static::releaseWatches($tradingInfo->getInternalId());
		static::releaseInternalChanges($tradingInfo->getInternalId());

		return $result;
	}

	protected static function makeOrderActions(Sale\OrderBase $internalOrder, Listener\TradingInfo $tradingInfo)
	{
		$options = $tradingInfo->getSetup()->wakeupService()->getOptions();
		$fieldActions = array_merge(
			$options->getEnvironmentFieldActions(),
			static::getWatches($tradingInfo->getInternalId())
		);
		$usedFields = array_unique(array_column($fieldActions, 'FIELD'));
		$changedValues = static::collectOrderChanges($internalOrder, $usedFields);
		$changedValues = static::filterInternalChanges($tradingInfo->getInternalId(), $changedValues);

		return static::collectChangesActions($fieldActions, $changedValues);
	}

	protected static function collectOrderChanges(Sale\OrderBase $order, $usedFields)
	{
		$result = [];

		foreach ($usedFields as $usedField)
		{
			list($type, $name) = explode('.', $usedField);

			if ($type === 'ORDER')
			{
				if (in_array($name, $order->getFields()->getChangedKeys(), true))
				{
					$result[$usedField] = $order->getField($name);
				}
			}
			else if (!($order instanceof Sale\Order))
			{
				continue;
			}
			else if (Market\Data\TextString::getPosition($type, 'PROPERTY_') === 0)
			{
				$propertyId = (int)Market\Data\TextString::getSubstring(
					$type,
					Market\Data\TextString::getLength('PROPERTY_')
				);

				if ($propertyId <= 0) { continue; }

				$propertyCollection = $order->getPropertyCollection();
				$property = $propertyCollection->getItemByOrderPropertyId($propertyId);

				if ($property === null) { continue; }

				if (in_array($name, $property->getFields()->getChangedKeys(), true))
				{
					$result[$usedField] = $property->getField($name);
				}
			}
			else if ($type === 'SHIPMENT')
			{
				/** @var Sale\Shipment $shipment */
				foreach ($order->getShipmentCollection() as $shipment)
				{
					if ($shipment->isSystem()) { continue; }

					if (in_array($name, $shipment->getFields()->getChangedKeys(), true))
					{
						$result[$usedField] = $shipment->getField($name);
					}
				}
			}
		}

		return $result;
	}

	protected static function collectChangesActions($fieldActions, $changes)
	{
		$result = [];

		foreach ($fieldActions as $fieldAction)
		{
			if (!isset($changes[$fieldAction['FIELD']])) { continue; }

			$value = $changes[$fieldAction['FIELD']];

			if (isset($fieldAction['TARGET']) && $fieldAction['TARGET'] !== $value) { continue; }

			$result[] = $fieldAction + [
				'VALUE' => $value,
			];
		}

		return $result;
	}

	protected static function makeProcedurePayload(array $action)
	{
		$result = null;

		if (isset($action['PAYLOAD']))
		{
			$result = $action['PAYLOAD'];
		}
		else if (isset($action['VALUE'], $action['PAYLOAD_MAP'][$action['VALUE']]))
		{
			$result = $action['PAYLOAD_MAP'][$action['VALUE']];
		}

		if ($result === null) { return null; }

		if (is_callable($result))
		{
			$result = $result($action);
		}

		return (array)$result;
	}

	protected static function sendStatusForEventBefore(Sale\Order $order, $status)
	{
		return static::sendStatus($order, $status, true);
	}

	protected static function sendStatusForEventAfter(Sale\Order $order, $status)
	{
		static::sendStatus($order, $status);
	}

	protected static function sendStatus(Sale\OrderBase $order, $status, $isImmediate = false)
	{
		$tradingInfo = static::getTradingInfo($order);

		if ($tradingInfo === null) { return new Main\EventResult(Main\EventResult::SUCCESS); }

		$state = ($isImmediate ? static::STATE_PROCESSING : static::STATE_SAVING);
		$procedureData = new Listener\ProcedureData(
			'send/status',
			[ 'status' => $status ]
		);

		return static::processOrderAction($order, $tradingInfo, $procedureData, $state);
	}

	protected static function sendTrackingNumber(Sale\OrderBase $order, $trackCode, $deliveryId, $isImmediate = false)
	{
		$tradingInfo = static::getTradingInfo($order);

		if ($tradingInfo === null) { return new Main\EventResult(Main\EventResult::SUCCESS); }

		$state = ($isImmediate ? static::STATE_PROCESSING : static::STATE_SAVING);
		$procedureData = new Listener\ProcedureData('send/track', [
			'trackCode' => $trackCode,
			'deliveryId' => $deliveryId,
		]);

		return static::processOrderAction($order, $tradingInfo, $procedureData, $state);
	}

	protected static function processOrderAction(Sale\OrderBase $internalOrder, Listener\TradingInfo $tradingInfo, Listener\ProcedureData $procedureData, $state)
	{
		$result = new Main\EventResult(Main\EventResult::SUCCESS);
		$immediate = ($state === static::STATE_PROCESSING);

		if ($immediate)
		{
			static::pushStatusQueue($tradingInfo->getInternalId(), $tradingInfo->getSiteId(), $procedureData);
		}
		else if (static::popStatusQueue($tradingInfo->getInternalId(), $tradingInfo->getSiteId(), $procedureData))
		{
			return $result;
		}

		static::holdOrder($internalOrder->getId(), $internalOrder, $state);

		$procedureResult = static::callProcedure($tradingInfo, $procedureData, $immediate);

		static::releaseOrder($internalOrder->getId());

		if (!$procedureResult->isSuccess())
		{
			$errorMessage = implode(PHP_EOL, $procedureResult->getErrorMessages());

			$result = new Main\EventResult(
				Main\EventResult::ERROR,
				new Sale\ResultError($errorMessage),
				Market\Config::getModuleName()
			);
		}

		return $result;
	}

	protected static function callProcedure(Listener\TradingInfo $tradingInfo, Listener\ProcedureData $action, $isImmediate = false)
	{
		$result = new Main\Result();
		$procedure = new Market\Trading\Procedure\Runner(
			Market\Trading\Entity\Registry::ENTITY_TYPE_ORDER,
			$tradingInfo->getAccountNumber()
		);

		try
		{
			$procedure->run(
				$tradingInfo->getSetup(),
				$action->getPath(),
				$action->getPayload() + $tradingInfo->getProcedurePayload($isImmediate)
			);
		}
		catch (Market\Exceptions\Trading\NotImplementedAction $exception)
		{
			// nothing
		}
		catch (Market\Exceptions\Api\Request $exception)
		{
			if (!$isImmediate)
			{
				$procedure->clearRepeat();
				$procedure->createRepeat();

				$procedure->logException($exception);
			}

			$result->addError(new Main\Error($exception->getMessage()));
		}
		catch (\Exception $exception)
		{
			$procedure->logException($exception);

			$result->addError(new Main\Error($exception->getMessage()));
		}

		return $result;
	}

	/**
	 * @param Sale\OrderBase $order
	 *
	 * @return Listener\TradingInfo|null
	 */
	protected static function getTradingInfo(Sale\OrderBase $order)
	{
		if ($order->isNew()) { return null; }

		$platformRow = OrderRegistry::searchPlatform($order->getId());

		if ($platformRow === null) { return null; }

		$orderInfo = $platformRow + [
			'INTERNAL_ORDER_ID' => $order->getId(),
			'ACCOUNT_NUMBER' => OrderRegistry::getOrderAccountNumber($order),
			'SITE_ID' => $order->getSiteId(),
		];
		$setup = static::getTradingSetup($orderInfo);

		if ($setup === null) { return null; }

		return new Listener\TradingInfo(
			$setup,
			$orderInfo
		);
	}

	protected static function getTradingSetup(array $orderInfo)
	{
		$signValues = array_intersect_key($orderInfo, [
			'TRADING_PLATFORM_ID' => true,
			'SITE_ID' => true,
			'SETUP_ID' => true,
		]);
		$sign = implode(':', $signValues);

		if (!array_key_exists($sign, static::$tradingSetupCache))
		{
			static::$tradingSetupCache[$sign] = static::loadTradingSetup($orderInfo);
		}

		return static::$tradingSetupCache[$sign];
	}

	protected static function loadTradingSetup(array $orderInfo)
	{
		try
		{
			$result = Market\Trading\Setup\Model::loadByTradingInfo($orderInfo);
		}
		catch (Main\ObjectNotFoundException $exception)
		{
			$result = null;
		}

		return $result;
	}

	public static function hasOrder($orderId)
	{
		return isset(static::$eventOrderList[$orderId]);
	}

	public static function getOrder($orderId)
	{
		return static::$eventOrderList[$orderId];
	}

	public static function getOrderState($orderId)
	{
		return isset(static::$eventOrderState[$orderId]) ? static::$eventOrderState[$orderId] : null;
	}

	public static function holdOrder($orderId, $order, $state)
	{
		static::$eventOrderList[$orderId] = $order;
		static::$eventOrderState[$orderId] = $state;
	}

	public static function releaseOrder($orderId)
	{
		unset(static::$eventOrderList[$orderId], static::$eventOrderState[$orderId]);
	}

	protected static function isAdminRequest()
	{
		global $USER;

		$request = Main\Context::getCurrent()->getRequest();
		$requestedPage = $request->getRequestedPage();
		$result = true;

		if (Market\Utils::isCli()) // is background process
		{
			$result = false;
		}
		else if (Market\Data\TextString::getPosition($requestedPage, BX_ROOT . '/admin/sale_order') !== 0) // not is order admin page
		{
			$result = false;
		}
		else if (!empty($_SESSION['BX_CML2_EXPORT'])) // is 1c exchange
		{
			$result = false;
		}
		else if (!($USER instanceof \CUser) || !$USER->IsAuthorized()) // hasn't valid user
		{
			$result = false;
		}

		return $result;
	}

	protected static function isShipmentSiblingsFilled(Sale\Shipment $shipment, $name, $value)
	{
		$shipmentCollection = $shipment->getCollection();
		$result = false;

		if ($shipmentCollection)
		{
			$result = true;

			/** @var $otherShipment Sale\Shipment */
			foreach ($shipmentCollection as $otherShipment)
			{
				if ($otherShipment === $shipment)
				{
					// nothing
				}
				else if ($otherShipment->isSystem())
				{
					if (!$otherShipment->isEmpty())
					{
						$result = false;
						break;
					}
				}
				else if (!$otherShipment->isEmpty() && $otherShipment->getField($name) !== $value)
				{
					$result = false;
					break;
				}
			}
		}

		return $result;
	}

	protected static function getShipmentOrder(Sale\Shipment $shipment)
	{
		/** @var Sale\ShipmentCollection $shipmentCollection */
		$shipmentCollection = $shipment->getCollection();

		return $shipmentCollection ? $shipmentCollection->getOrder() : null;
	}

	protected static function isPaymentOrderPaid(Sale\Payment $payment)
	{
		/** @var Sale\PaymentCollection $paymentCollection */
		$paymentCollection = $payment->getCollection();
		$order = $paymentCollection ? $paymentCollection->getOrder() : null;
		$result = false;

		if ($paymentCollection && $order)
		{
			$paidSum = 0;

			/** @var Sale\Payment $otherPayment */
			foreach ($paymentCollection as $otherPayment)
			{
				if ($otherPayment === $payment || $otherPayment->isPaid())
				{
					$paidSum += $otherPayment->getSum();
				}
			}

			if (
				$paidSum >= 0
				&& static::roundPrice($order->getPrice()) <= static::roundPrice($paidSum)
			)
			{
				$result = true;
			}
		}

		return $result;
	}

	protected static function roundPrice($price)
	{
		if (method_exists('\Bitrix\Sale\PriceMaths', 'roundPrecision'))
		{
			$result = Sale\PriceMaths::roundPrecision($price);
		}
		else
		{
			$result = roundEx($price, 2);
		}

		return $result;
	}

	protected static function getPaymentOrder(Sale\Payment $payment)
	{
		/** @var Sale\PaymentCollection $paymentCollection */
		$paymentCollection = $payment->getCollection();

		return $paymentCollection ? $paymentCollection->getOrder() : null;
	}

	protected static function hasOrderActivePayment(Sale\Order $order)
	{
		$result = false;

		/** @var Sale\Payment $payment */
		foreach ($order->getPaymentCollection() as $payment)
		{
			if ($payment->isPaid())
			{
				$result = true;
				break;
			}
		}

		return $result;
	}

	protected static function hasOrderActiveShipment(Sale\Order $order)
	{
		$result = false;

		/** @var Sale\Shipment $shipment */
		foreach ($order->getShipmentCollection() as $shipment)
		{
			if ($shipment->isShipped())
			{
				$result = true;
				break;
			}
		}

		return $result;
	}

	protected static function addWatch($orderId, array $action)
	{
		if (!isset(static::$watches[$orderId]))
		{
			static::$watches[$orderId] = [];
		}

		static::$watches[$orderId][] = $action;
	}

	protected static function getWatches($orderId)
	{
		return isset(static::$watches[$orderId]) ? static::$watches[$orderId] : [];
	}

	protected static function releaseWatches($orderId)
	{
		if (isset(static::$watches[$orderId]))
		{
			unset(static::$watches[$orderId]);
		}
	}

	public static function filterInternalChanges($orderId, array $changes)
	{
		$internalChanges = static::getInternalChanges($orderId);

		return array_diff_assoc($changes, $internalChanges);
	}

	public static function addInternalChange($orderId, $field, $value)
	{
		if ($orderId <= 0) { return; }

		if (!isset(static::$internalChanges[$orderId]))
		{
			static::$internalChanges[$orderId] = [];
		}

		static::$internalChanges[$orderId][$field] = $value;
	}

	protected static function getInternalChanges($orderId)
	{
		return isset(static::$internalChanges[$orderId]) ? static::$internalChanges[$orderId] : [];
	}

	protected static function releaseInternalChanges($orderId)
	{
		if (isset(static::$internalChanges[$orderId]))
		{
			unset(static::$internalChanges[$orderId]);
		}
	}

	protected static function pushStatusQueue($orderId, $siteId, $statusId)
	{
		$key = static::getStatusQueueKey($orderId, $siteId, $statusId);

		static::$statusEventQueue[$key] = true;
	}

	protected static function popStatusQueue($orderId, $siteId, $statusId)
	{
		$key = static::getStatusQueueKey($orderId, $siteId, $statusId);
		$result = false;

		if (isset(static::$statusEventQueue[$key]))
		{
			$result = true;
			unset(static::$statusEventQueue[$key]);
		}

		return $result;
	}

	protected static function getStatusQueueKey($orderId, $siteId, $statusId)
	{
		return $orderId . '|' . $siteId . '|' . $statusId;
	}

	protected function getEventHandlers()
	{
		return [
			[
				'module' => 'sale',
				'event' => 'OnBeforeSaleOrderSetField',
				'sort' => 200
			],
			[
				'module' => 'sale',
				'event' => 'OnBeforeSaleShipmentSetField',
				'sort' => 200
			],
			[
				'module' => 'sale',
				'event' => 'OnBeforeSalePaymentSetField',
				'sort' => 200
			],
			[
				'module' => 'sale',
				'event' => 'OnSaleOrderBeforeSaved',
				'sort' => 200,
			],
			[
				'module' => 'sale',
				'event' => 'OnSaleOrderSaved',
			],
			[
				'module' => 'sale',
				'event' => 'OnSaleStatusOrderChange'
			],
			[
				'module' => 'sale',
				'event' => 'OnSaleOrderCanceled'
			],
			[
				'module' => 'sale',
				'event' => 'OnShipmentAllowDelivery'
			],
			[
				'module' => 'sale',
				'event' => 'OnShipmentTrackingNumberChange'
			],
			[
				'module' => 'sale',
				'event' => 'OnSaleOrderPaid'
			],
			[
				'module' => 'sale',
				'event' => 'OnShipmentDeducted'
			],
		];
	}
}