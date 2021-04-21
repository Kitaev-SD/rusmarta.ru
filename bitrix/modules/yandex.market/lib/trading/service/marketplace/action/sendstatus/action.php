<?php

namespace Yandex\Market\Trading\Service\Marketplace\Action\SendStatus;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

class Action extends TradingService\Common\Action\SendStatus\Action
{
	/** @var TradingService\Marketplace\Provider */
	protected $provider;

	public function __construct(TradingService\Marketplace\Provider $provider, TradingEntity\Reference\Environment $environment, array $data)
	{
		parent::__construct($provider, $environment, $data);
	}

	protected function createRequest(array $data)
	{
		return new Request($data);
	}

	protected function isChangedOrderStatus($orderId, $state)
	{
		$serviceKey = $this->provider->getUniqueKey();
		$storedStatusEncoded = Market\Trading\State\OrderStatus::getValue($serviceKey, $orderId);
		$result = false;

		if ($storedStatusEncoded === null)
		{
			$result = true;
		}
		else
		{
			/** @var Market\Trading\Service\Marketplace\Status $serviceStatus */
			list($submitStatus, $submitSubStatus) = $this->getExternalStatus($state);
			list($storedStatus, $storedSubStatus) = explode(':', $storedStatusEncoded);
			$serviceStatus = $this->provider->getStatus();
			$submitStatusOrder = $serviceStatus->getStatusOrder($submitStatus);
			$storedStatusOrder = $serviceStatus->getStatusOrder($storedStatus);

			if ($submitStatusOrder !== null && $submitStatusOrder < $storedStatusOrder)
			{
				$result = false;
			}
			else if ($storedStatus !== $submitStatus)
			{
				$result = true;
			}
			else if (
				$submitStatus === TradingService\Marketplace\Status::STATUS_PROCESSING
				&& $submitSubStatus !== $storedSubStatus
			)
			{
				$submitSubStatusOrder = $serviceStatus->getSubStatusOrder($submitSubStatus);
				$storedSubStatusOrder = $serviceStatus->getSubStatusOrder($storedSubStatus);

				$result = (
					$submitSubStatusOrder === null
					|| $submitSubStatusOrder > $storedSubStatusOrder
				);
			}
		}

		return $result;
	}

	protected function checkHasStatus($orderId, $state)
	{
		$result = false;

		try
		{
			$serviceStatuses = $this->provider->getStatus();
			$externalOrder = $this->loadExternalOrder($orderId);
			$orderStatus = $externalOrder->getStatus();
			$subStatus = $externalOrder->getSubStatus();

			switch ($state)
			{
				case TradingService\Marketplace\Status::STATE_SHOP_FAILED:
					$result = $externalOrder->isCancelRequested() || $serviceStatuses->isCanceled($orderStatus);
				break;

				case TradingService\Marketplace\Status::STATE_READY_TO_SHIP:
					$availableStates = [
						TradingService\Marketplace\Status::STATE_READY_TO_SHIP => true,
						TradingService\Marketplace\Status::STATE_SHIPPED => true,
					];

					$result =
						$serviceStatuses->isLeftProcessing($orderStatus)
						|| ($serviceStatuses->isProcessing($orderStatus) && isset($availableStates[$subStatus]));
				break;

				case TradingService\Marketplace\Status::STATE_SHIPPED:
					$result =
						$serviceStatuses->isLeftProcessing($orderStatus)
						|| ($serviceStatuses->isProcessing($orderStatus) && $subStatus === $state);
				break;
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
		if ($state === TradingService\Marketplace\Status::STATE_SHOP_FAILED)
		{
			$status = TradingService\Marketplace\Status::STATUS_CANCELLED;
		}
		else
		{
			$status = TradingService\Marketplace\Status::STATUS_PROCESSING;
		}

		return [ $status, $state ];
	}
}