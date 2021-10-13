<?php

namespace Yandex\Market\Trading\Service\Marketplace\Action\SendCis;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

/**
 * @property TradingService\Marketplace\Provider $provider
 * @property Request $request
 */
class Action extends TradingService\Reference\Action\DataAction
{
	use Market\Reference\Concerns\HasLang;
	use TradingService\Common\Concerns\Action\HasOrder;
	use TradingService\Common\Concerns\Action\HasOrderMarker;
	use TradingService\Common\Concerns\Action\HasItemIdMatch;

	protected $sentItems;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
	}

	protected function createRequest(array $data)
	{
		return new Request($data);
	}

	public function getAudit()
	{
		return Market\Logger\Trading\Audit::SEND_CIS;
	}

	public function process()
	{
		try
		{
			if ($this->isAutoSubmit() && $this->hasManualFlag()) { return; }

			$this->sendCis();
			$this->logCis();

			$this->resolveManualFlag();
			$this->resolveOrderMarker(true);
		}
		catch (Market\Exceptions\Api\Request $exception)
		{
			$sendResult = new Main\Result();
			$sendResult->addError(new Main\Error(
				$exception->getMessage(),
				$exception->getCode()
			));

			$this->resolveOrderMarker(false, $sendResult);
			throw $exception;
		}
	}

	protected function isAutoSubmit()
	{
		return $this->request->isAutoSubmit();
	}

	protected function hasManualFlag()
	{
		$uniqueKey = $this->provider->getUniqueKey();
		$orderId = $this->request->getOrderId();
		$stored = Market\Trading\State\OrderData::getValue($uniqueKey, $orderId, 'CIS_MANUAL');

		return ($stored === 'Y');
	}

	protected function resolveManualFlag()
	{
		if ($this->isAutoSubmit()) { return; }

		$uniqueKey = $this->provider->getUniqueKey();
		$orderId = $this->request->getOrderId();

		Market\Trading\State\OrderData::setValue($uniqueKey, $orderId, 'CIS_MANUAL', 'Y');
	}

	protected function sendCis()
	{
		$request = $this->buildRequest();
		$sendResult = $request->send();

		if (!$sendResult->isSuccess())
		{
			$message = static::getLang('TRADING_ACTION_SEND_CIS_RESPONSE_FAIL', [
				'#MESSAGE#' => implode(PHP_EOL, $sendResult->getErrorMessages())
			]);
			throw new Market\Exceptions\Api\Request($message);
		}
	}

	protected function buildRequest()
	{
		$result = new TradingService\Marketplace\Api\SendCis\Request();
		$options = $this->provider->getOptions();
		$logger = $this->provider->getLogger();
		$items = $this->makeItems();

		$result->setLogger($logger);
		$result->setOauthClientId($options->getOauthClientId());
		$result->setOauthToken($options->getOauthToken()->getAccessToken());
		$result->setCampaignId($options->getCampaignId());
		$result->setOrderId($this->request->getOrderId());
		$result->setItems($items);

		$this->sentItems = $items;

		return $result;
	}

	protected function makeItems()
	{
		$items = $this->request->getItems();
		$result = [];

		foreach ($items as $item)
		{
			$id = $this->getItemId($item);

			if ($id === null) { continue; }

			$result[] = [
				'id' => $id,
				'instances' => $item['instances'],
			];
		}

		return $result;
	}

	protected function logCis()
	{
		$logger = $this->provider->getLogger();
		$message = static::getLang('TRADING_ACTION_SEND_CIS_SEND_LOG', [
			'#CIS_COUNT#' => $this->getCisCount(),
		]);

		$logger->info($message, [
			'AUDIT' => $this->getAudit(),
			'ENTITY_TYPE' => TradingEntity\Registry::ENTITY_TYPE_ORDER,
			'ENTITY_ID' => $this->request->getOrderNumber(),
		]);
	}

	protected function getCisCount()
	{
		$result = 0;
		$items = $this->sentItems !== null ? $this->sentItems : $this->request->getItems();

		foreach ($items as $item)
		{
			if (!isset($item['instances'])) { continue; }

			$result += count($item['instances']);
		}

		return $result;
	}

	protected function getMarkerCode()
	{
		return $this->provider->getDictionary()->getErrorCode('SEND_CIS_ERROR');
	}
}