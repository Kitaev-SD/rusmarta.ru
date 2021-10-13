<?php

namespace Yandex\Market\Component\TradingActivity;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Reference\Assert;
use Yandex\Market\Trading\Setup as TradingSetup;
use Yandex\Market\Trading\Service as TradingService;

class EditForm extends Market\Component\Plain\EditForm
{
	protected $entity;

	public function load($primary, array $select = [], $isCopy = false)
	{
		$entity = $this->loadEntity($primary);

		return $this->getActivity()->getEntityValues($entity);
	}

	protected function loadEntity($primary)
	{
		$sourceType = $this->getActivity()->getSourceType();

		if ($sourceType === Market\Trading\Entity\Registry::ENTITY_TYPE_ORDER)
		{
			return $this->loadOrder($primary);
		}

		return $this->loadEntityByFacade($sourceType, $primary);
	}

	protected function loadOrder($primary)
	{
		$service = $this->getSetup()->wakeupService();
		$options = $service->getOptions();

		if (Market\Trading\State\SessionCache::has('order', $primary))
		{
			$orderClassName = $service->getModelFactory()->getOrderClassName();
			$fields = Market\Trading\State\SessionCache::get('order', $primary);

			$order = $orderClassName::initialize($fields);
		}
		else
		{
			$orderFacade = $service->getModelFactory()->getOrderFacadeClassName();

			$order = $orderFacade::load($options, $primary);
		}

		return $order;
	}

	protected function loadEntityByFacade($entityType, $primary)
	{
		$service = $this->getSetup()->wakeupService();
		$options = $service->getOptions();
		$facade = $service->getModelFactory()->getEntityFacadeClassName($entityType);

		return $facade::load($options, $primary);
	}

	public function getFields(array $select = [], $item = null)
	{
		$result = parent::getFields($select, $item);

		return $this->getActivity()->extendFields($result, $item);
	}

	public function add($fields)
	{
		throw new Main\NotSupportedException();
	}

	public function update($primary, $fields)
	{
		$result = new Main\Entity\UpdateResult();
		$activity = $this->getActivity();
		$entityType = $activity->getSourceType();
		$tradingInfo = $this->getTradingInfo($entityType, $primary);

		$procedure = new Market\Trading\Procedure\Runner(
			$entityType,
			$tradingInfo['ACCOUNT_NUMBER']
		);

		try
		{
			$procedure->run(
				$this->getSetup(),
				$this->getActionPath(),
				$this->getActivity()->getPayload($fields) + $this->getTradingPayload($entityType, $tradingInfo)
			);
		}
		catch (Market\Exceptions\Trading\NotImplementedAction $exception)
		{
			$result->addError(new Main\Error($exception->getMessage()));
		}
		catch (Market\Exceptions\Api\Request $exception)
		{
			$result->addError(new Main\Error($exception->getMessage()));
		}
		catch (\Exception $exception)
		{
			$procedure->logException($exception);

			$result->addError(new Main\Error($exception->getMessage()));
		}

		return $result;
	}

	protected function getTradingInfo($entityType, $primary)
	{
		if ($entityType === Market\Trading\Entity\Registry::ENTITY_TYPE_ORDER)
		{
			$result = $this->getOrderTradingInfo($primary);
		}
		else
		{
			$result = [
				'ID' => $primary,
				'ACCOUNT_NUMBER' => $primary,
			];
		}

		return $result;
	}

	protected function getOrderTradingInfo($primary)
	{
		$platform = $this->getSetup()->getPlatform();
		$orderRegistry = $this->getSetup()->getEnvironment()->getOrderRegistry();

		return [
			'INTERNAL_ORDER_ID' => $orderRegistry->search($primary, $platform, false),
			'EXTERNAL_ORDER_ID' => $primary,
			'ACCOUNT_NUMBER' => $orderRegistry->search($primary, $platform),
		];
	}

	protected function getTradingPayload($entityType, array $tradingInfo)
	{
		if ($entityType === Market\Trading\Entity\Registry::ENTITY_TYPE_ORDER)
		{
			$result = $this->getOrderTradingPayload($tradingInfo);
		}
		else if ($entityType === Market\Trading\Entity\Registry::ENTITY_TYPE_LOGISTIC_SHIPMENT)
		{
			$result = $this->getShipmentTradingPayload($tradingInfo);
		}
		else
		{
			$result = [];
		}

		return $result + [
			'immediate' => true,
		];
	}

	protected function getOrderTradingPayload(array $tradingInfo)
	{
		return [
			'internalId' => $tradingInfo['INTERNAL_ORDER_ID'],
			'orderId' => $tradingInfo['EXTERNAL_ORDER_ID'],
			'orderNum' => $tradingInfo['ACCOUNT_NUMBER'],
		];
	}

	protected function getShipmentTradingPayload(array $tradingInfo)
	{
		return [
			'shipmentId' => $tradingInfo['ID'],
		];
	}

	/** @return TradingSetup\Model */
	protected function getSetup()
	{
		$action = $this->getComponentParam('TRADING_SETUP');

		Assert::notNull($action, 'TRADING_SETUP');
		Assert::typeOf($action, TradingSetup\Model::class, 'TRADING_SETUP');

		return $action;
	}

	/** @return string */
	protected function getActionPath()
	{
		$path = $this->getComponentParam('TRADING_PATH');

		Assert::notNull($path, 'TRADING_PATH');

		return (string)$path;
	}

	/** @return TradingService\Reference\Action\DataAction */
	protected function getAction()
	{
		$action = $this->getComponentParam('TRADING_ACTION');

		Assert::notNull($action, 'TRADING_ACTION');
		Assert::typeOf($action, TradingService\Reference\Action\DataAction::class, 'TRADING_ACTION');

		return $action;
	}

	/** @return TradingService\Reference\Action\FormActivity */
	protected function getActivity()
	{
		$action = $this->getComponentParam('TRADING_ACTIVITY');

		Assert::notNull($action, 'TRADING_ACTIVITY');
		Assert::typeOf($action, TradingService\Reference\Action\FormActivity::class, 'TRADING_ACTIVITY');

		return $action;
	}
}