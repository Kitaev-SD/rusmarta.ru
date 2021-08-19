<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Action\SendStatus;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

class Action extends TradingService\Marketplace\Action\SendStatus\Action
{
	/** @var TradingService\MarketplaceDbs\Provider */
	protected $provider;
	/** @var Request */
	protected $request;

	public function __construct(TradingService\MarketplaceDbs\Provider $provider, TradingEntity\Reference\Environment $environment, array $data)
	{
		parent::__construct($provider, $environment, $data);
	}

	protected function createRequest(array $data)
	{
		return new Request($data);
	}

	protected function checkHasStatus($orderId, $state)
	{
		try
		{
			/** @var Market\Trading\Service\MarketplaceDbs\Status $serviceStatuses */
			$serviceStatuses = $this->provider->getStatus();
			$externalOrder = $this->loadExternalOrder($orderId);
			$currentStatus = $externalOrder->getStatus();

			if ($state === TradingService\MarketplaceDbs\Status::STATUS_CANCELLED)
			{
				$result = $externalOrder->isCancelRequested() || $serviceStatuses->isCanceled($currentStatus);
			}
			else
			{
				$outgoingOrder = $serviceStatuses->getStatusOrder($state);
				$currentOrder = $serviceStatuses->getStatusOrder($currentStatus);

				$result = (
					$outgoingOrder !== null
					&& $outgoingOrder <= $currentOrder
				);
			}
		}
		catch (Market\Exceptions\Api\Request $exception)
		{
			$result = false;
		}

		return $result;
	}

	protected function getExternalStatus($state)
	{
		$status = $state;
		$subStatus = null;

		if ($status === TradingService\MarketplaceDbs\Status::STATUS_CANCELLED)
		{
			$subStatus = $this->getCancelReason();
		}

		return [ $status, $subStatus ];
	}

	protected function getCancelReason()
	{
		return
			$this->getCancelReasonFromRequest()
			?: $this->getCancelReasonFromStatusOption()
			?: $this->getCancelReasonFromProperty()
			?: $this->getCancelReasonFromOrder()
			?: $this->getCancelReasonDefault();
	}

	protected function getCancelReasonFromRequest()
	{
		return $this->request->getCancelReason();
	}

	protected function getCancelReasonFromStatusOption()
	{
		$requestStatus = $this->request->getStatus();
		$orderStatuses = $this->getOrder()->getStatuses();
		$result = null;

		if ($requestStatus !== null && !in_array($requestStatus, $orderStatuses, true))
		{
			$orderStatuses[] = $requestStatus;
		}

		foreach ($this->provider->getOptions()->getCancelStatusOptions() as $cancelStatusOption)
		{
			$optionStatus = $cancelStatusOption->getStatus();

			if (in_array($optionStatus, $orderStatuses, true))
			{
				$result = $cancelStatusOption->getCancelReason();
				break;
			}
		}

		return $result;
	}

	protected function getCancelReasonFromProperty()
	{
		$propertyId = (string)$this->provider->getOptions()->getProperty('REASON_CANCELED');
		$result = null;

		if ($propertyId === '') { return $result; }

		$propertyValue = $this->getOrder()->getPropertyValue($propertyId);

		return $this->provider->getCancelReason()->resolveVariant($propertyValue);
	}

	protected function getCancelReasonFromOrder()
	{
		$reason = $this->getOrder()->getReasonCanceled();

		return $this->provider->getCancelReason()->resolveVariant($reason);
	}

	protected function getCancelReasonDefault()
	{
		return $this->provider->getCancelReason()->getDefault();
	}

	protected function extractSendResultSkipErrorCurrentStatus(Main\Result $sendResult, $state)
	{
		list($status, $subStatus) = $this->getExternalStatus($state);
		$result = null;

		foreach ($sendResult->getErrors() as $error)
		{
			$message = $error->getMessage();
			$regexp =
				'#Order \d+ with status'
				. ' (?<status>\w+)( and substatus (?<substatus>\w+))?'
				. ' is not allowed for status'
				. ' (?<requestStatus>\w+)( and substatus (?<requestSubstatus>\w+))?#';

			if (!preg_match($regexp, $message, $matches)) { continue; }
			if ($matches['requestStatus'] !== $status) { continue; }
			if ($subStatus !== null && $matches['requestSubstatus'] !== $subStatus) { continue; }

			$result = [
				$matches['status'],
				isset($matches['substatus']) ? $matches['substatus'] : null,
			];
			break;
		}

		return $result;
	}

	protected function getSubmitStack($fromStatus, $toStatus)
	{
		$disabled = [
			TradingService\MarketplaceDbs\Status::STATUS_CANCELLED => true,
		];

		if ($fromStatus[0] === null || $toStatus[0] === null) { return null; }
		if (isset($disabled[$toStatus[0]])) { return null; }

		$statusProvider = $this->provider->getStatus();
		$statusOrder = $statusProvider->getProcessOrder();

		if (!isset($statusOrder[$fromStatus[0]], $statusOrder[$toStatus[0]])) { return null; }

		$result = [];
		$fromFound = false;
		$skip = $disabled + [
			TradingService\MarketplaceDbs\Status::STATUS_PICKUP => true,
		];
		$skip = array_diff_key($skip, [ $toStatus[0] => true ]);

		foreach ($statusOrder as $processStatus => $processOrder)
		{
			if ($processStatus === $fromStatus[0])
			{
				$fromFound = true;
			}
			else if ($fromFound && !isset($skip[$processStatus]))
			{
				$result[$processOrder] = $processStatus;

				if ($processStatus === $toStatus[0]) { break; }
			}
		}

		return $result;
	}
}