<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Service as TradingService;

class Status extends TradingService\Marketplace\Status
{
	const COMPLEX_PROCESSING_PREPAID = 'PROCESSING_PREPAID';

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
		parent::includeMessages();
	}

	public function __construct(Provider $provider)
	{
		parent::__construct($provider);
	}

	public function getVariants()
	{
		return [
			static::STATUS_CANCELLED,
			static::STATUS_PROCESSING,
			static::STATUS_DELIVERY,
			static::STATUS_PICKUP,
			static::STATUS_DELIVERED,
		];
	}

	public function getIncomingVariants()
	{
		return [
			static::VIRTUAL_CREATED,
			static::STATUS_CANCELLED,
			static::STATUS_PROCESSING,
			static::COMPLEX_PROCESSING_PREPAID,
		];
	}

	public function getIncomingRequired()
	{
		return [
			static::STATUS_CANCELLED,
			static::COMPLEX_PROCESSING_PREPAID,
		];
	}

	public function getIncomingMeaningfulMap()
	{
		return [
			Market\Data\Trading\MeaningfulStatus::CREATED => static::VIRTUAL_CREATED,
			Market\Data\Trading\MeaningfulStatus::PAYED => static::COMPLEX_PROCESSING_PREPAID,
			Market\Data\Trading\MeaningfulStatus::CANCELED => static::STATUS_CANCELLED,
		];
	}

	public function getOutgoingVariants()
	{
		return [
			static::STATUS_DELIVERY,
			static::STATUS_PICKUP,
			static::STATUS_DELIVERED,
			static::STATUS_CANCELLED,
		];
	}

	public function getOutgoingRequired()
	{
		return [
			static::STATUS_DELIVERY,
			static::STATUS_DELIVERED,
			static::STATUS_CANCELLED,
		];
	}

	public function getOutgoingMeaningfulMap()
	{
		return [
			Market\Data\Trading\MeaningfulStatus::CANCELED => static::STATUS_CANCELLED,
			Market\Data\Trading\MeaningfulStatus::DEDUCTED => static::STATUS_DELIVERY,
			Market\Data\Trading\MeaningfulStatus::FINISHED => static::STATUS_DELIVERED,
		];
	}
}