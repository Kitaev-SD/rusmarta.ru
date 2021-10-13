<?php

namespace Yandex\Market\Trading\State;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Trading\Service as TradingService;

class OrderStatusSync extends Market\Reference\Agent\Base
{
	protected static $startTime;
	protected static $timeLimit;

	public static function getDefaultParams()
	{
		return [
			'interval' => static::getPeriod('restart', 86400),
		];
	}

	public static function start($setupId)
	{
		static::register([
			'method' => 'sync',
			'arguments' => [ $setupId ],
			'interval' => static::getPeriod('step', 60),
		]);
	}

	public static function sync($setupId, $offset = 0, $errorCount = 0)
	{
		global $pPERIOD;

		$service = null;

		try
		{
			if (static::isTimeExpired()) { return true; }

			Market\Environment::restore();
			Market\Environment::makeUserPlaceholder(); // required for Sale\Payment::onFieldModify()

			$setup = static::getSetup($setupId);
			$service = $setup->wakeupService();
			$orderCollection = static::loadOrderCollection($service, $offset);
			$pager = $orderCollection->getPager();
			$hasNext = ($pager !== null && $pager->hasNext());
			$orders = static::mapOrderCollection($orderCollection);
			$orders = static::applyOffset($orders, $offset);
			$accountNumberMap = static::getAccountNumberMap($orders, $setup);

			foreach ($orders as $orderId => $order)
			{
				++$offset;

				if (!isset($accountNumberMap[$orderId])) { continue; }
				if (!static::isChanged($service, $order)) { continue; }

				if (static::isTimeExpired())
				{
					$hasNext = true;
					break;
				}

				static::emulateStatus($setup, $order, $accountNumberMap[$orderId]);
			}

			Market\Environment::reset();

			if ($hasNext)
			{
				$pPERIOD = static::getPeriod('step', 60);

				return [ $setupId, $offset ];
			}
		}
		catch (Market\Exceptions\Api\Request $exception)
		{
			Market\Environment::reset();

			if ($errorCount < static::getErrorRepeatLimit())
			{
				$pPERIOD = static::getPeriod('timeout', 600);

				return [ $setupId, $offset, $errorCount + 1 ]; // wait for service up
			}

			if ($service !== null)
			{
				$service->getLogger()->error($exception);
			}
		}
		catch (Main\SystemException $exception)
		{
			Market\Environment::reset();

			if ($service !== null)
			{
				$service->getLogger()->error($exception);
			}
		}
		catch (\Exception $exception)
		{
			Market\Environment::reset();
			throw $exception;
		}
		catch (\Throwable $exception)
		{
			Market\Environment::reset();
			throw $exception;
		}

		return false; // stop
	}

	protected static function getPeriod($type, $default)
	{
		$option = (int)Market\Config::getOption('trading_status_sync_period_' . $type, 0);

		return $option > 0 ? $option : $default;
	}

	protected static function getPageSize()
	{
		$option = (int)Market\Config::getOption('trading_status_sync_page_size', 50);

		return max(1, min(50, $option));
	}

	protected static function getErrorRepeatLimit()
	{
		return (int)Market\Config::getOption('trading_status_sync_repeat_limit', 10);
	}

	protected static function isTimeExpired()
	{
		$limit = static::getTimeLimit();
		$startTime = static::getStartTime();
		$passedTime = microtime(true) - $startTime;

		return ($passedTime >= $limit);
	}

	protected static function getStartTime()
	{
		if (static::$startTime === null)
		{
			static::$startTime = defined('START_EXEC_TIME') ? START_EXEC_TIME : microtime(true);
		}

		return static::$startTime;
	}

	protected static function getTimeLimit()
	{
		if (static::$timeLimit === null)
		{
			$maxExecutionTime = (int)ini_get('max_execution_time') * 0.75;
			$optionName = 'trading_status_sync_time_limit';
			$optionDefault = 5;

			if (Market\Utils::isCli())
			{
				$optionName .= '_cli';
				$optionDefault = 30;
			}

			static::$timeLimit = (int)Market\Config::getOption($optionName, $optionDefault);

			if ($maxExecutionTime > 0 && static::$timeLimit > $maxExecutionTime)
			{
				static::$timeLimit = $maxExecutionTime;
			}
		}

		return static::$timeLimit;
	}

	protected static function getSetup($setupId)
	{
		$setup = Market\Trading\Setup\Model::loadById($setupId);

		if (!$setup->isActive())
		{
			throw new Main\SystemException(sprintf('setup %s is inactive', $setupId));
		}

		return $setup;
	}

	/**
	 * @param TradingService\Reference\Provider $service
	 * @param int $offset
	 *
	 * @return Market\Api\Model\OrderCollection
	 * @throws Main\SystemException
	 */
	protected static function loadOrderCollection(TradingService\Reference\Provider $service, $offset = 0)
	{
		/** @var Market\Api\Reference\HasOauthConfiguration $options */
		$options = $service->getOptions();
		$pageSize = static::getPageSize();
		$parameters = [
			'page' => floor($offset / $pageSize) + 1,
			'pageSize' => $pageSize,
			'fake' => true,
		];

		$orderFacade = $service->getModelFactory()->getOrderFacadeClassName();

		return $orderFacade::loadList($options, $parameters);
	}

	protected static function mapOrderCollection(Market\Api\Model\OrderCollection $orderCollection)
	{
		$result = [];

		foreach ($orderCollection as $order)
		{
			$result[$order->getId()] = $order;
		}

		return $result;
	}

	protected static function applyOffset(array $orders, $offset = 0)
	{
		$pageOffset = $offset % static::getPageSize();

		if ($pageOffset === 0) { return $orders; }

		return array_slice($orders, $pageOffset, null, true);
	}

	protected static function getAccountNumberMap(array $orders, Market\Trading\Setup\Model $setup)
	{
		return $setup->getEnvironment()->getOrderRegistry()->searchList(
			array_keys($orders),
			$setup->getPlatform(),
			false
		);
	}

	protected static function isChanged(TradingService\Reference\Provider $service, Market\Api\Model\Order $order)
	{
		$current = $order->getStatus();
		$storedFull = OrderStatus::getValue($service->getUniqueKey(), $order->getId());
		list($stored) = explode(':', (string)$storedFull, 2);

		if ($stored === $current) { return false; }

		$storedOrder = $service->getStatus()->getStatusOrder($stored);
		$currentOrder = $service->getStatus()->getStatusOrder($current);

		return ($currentOrder !== null && $currentOrder > $storedOrder);
	}

	protected static function emulateStatus(Market\Trading\Setup\Model $setup, Market\Api\Model\Order $order, $accountNumber)
	{
		$logger = null;
		$audit = null;

		try
		{
			$environment = $setup->getEnvironment();
			$service = $setup->wakeupService();
			$logger = $service->getLogger();
			$server = Main\Context::getCurrent()->getServer();
			$request = static::makeRequestFromOrder($server, $order);

			$action = $service->getRouter()->getHttpAction('order/status', $environment, $request, $server);
			$audit = $action->getAudit();

			$action->process();
		}
		catch (Main\SystemException $exception)
		{
			if ($logger === null) { throw $exception; }

			$logger->error($exception, array_filter([
				'AUDIT' => $audit,
				'ENTITY_TYPE' => Market\Trading\Entity\Registry::ENTITY_TYPE_ORDER,
				'ENTITY_ID' => $accountNumber,
			]));
		}
	}

	protected static function makeRequestFromOrder(Main\Server $server, Market\Api\Model\Order $order)
	{
		return new Main\HttpRequest(
			$server,
			[], // query string
			[ 'order' => $order->getFields() ], // post
			[], // files
			[] // cookies
		);
	}
}