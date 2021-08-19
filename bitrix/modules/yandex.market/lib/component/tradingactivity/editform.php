<?php

namespace Yandex\Market\Component\TradingActivity;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Reference\Assert;
use Yandex\Market\Trading\Setup as TradingSetup;
use Yandex\Market\Trading\Service as TradingService;

class EditForm extends Market\Component\Plain\EditForm
{
	public function load($primary, array $select = [], $isCopy = false)
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

		return $this->getActivity()->getValues($order);
	}

	public function add($fields)
	{
		throw new Main\NotSupportedException();
	}

	public function update($primary, $fields)
	{
		$result = new Main\Entity\UpdateResult();
		$tradingInfo = $this->getTradingInfo($primary);

		$procedure = new Market\Trading\Procedure\Runner(
			Market\Trading\Entity\Registry::ENTITY_TYPE_ORDER,
			$tradingInfo['ACCOUNT_NUMBER']
		);

		try
		{
			$procedure->run(
				$this->getSetup(),
				$this->getActionPath(),
				$this->getActivity()->getPayload($fields) + $this->getTradingPayload($tradingInfo)
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

	protected function getTradingInfo($primary)
	{
		$platform = $this->getSetup()->getPlatform();
		$orderRegistry = $this->getSetup()->getEnvironment()->getOrderRegistry();

		return [
			'INTERNAL_ORDER_ID' => $orderRegistry->search($primary, $platform, false),
			'EXTERNAL_ORDER_ID' => $primary,
			'ACCOUNT_NUMBER' => $orderRegistry->search($primary, $platform),
		];
	}

	protected function getTradingPayload(array $tradingInfo)
	{
		return [
			'internalId' => $tradingInfo['INTERNAL_ORDER_ID'],
			'orderId' => $tradingInfo['EXTERNAL_ORDER_ID'],
			'orderNum' => $tradingInfo['ACCOUNT_NUMBER'],
			'immediate' => true,
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