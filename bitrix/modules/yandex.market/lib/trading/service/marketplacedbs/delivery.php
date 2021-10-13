<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs;

use Yandex\Market;
use Bitrix\Main;

class Delivery extends Market\Trading\Service\Marketplace\Delivery
{
	const LIFT_NOT_NEEDED = 'NOT_NEEDED';
	const LIFT_MANUAL = 'MANUAL';
	const LIFT_ELEVATOR  = 'ELEVATOR';
	const LIFT_CARGO_ELEVATOR  = 'CARGO_ELEVATOR';
	const LIFT_FREE  = 'FREE';

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
		parent::includeMessages();
	}

	public function getLiftTypes()
	{
		return [
			static::LIFT_NOT_NEEDED,
			static::LIFT_MANUAL,
			static::LIFT_ELEVATOR,
			static::LIFT_CARGO_ELEVATOR,
			static::LIFT_FREE,
		];
	}

	public function getLiftTitle($type, $version = '')
	{
		$suffix = ($version !== '' ? '_' . Market\Data\TextString::toUpper($version) : '');

		return static::getLang('TRADING_SERVICE_MARKETPLACE_DELIVERY_LIFT_' . Market\Data\TextString::toUpper($type) . $suffix, null, (string)$type);
	}

	public function getLiftEnum()
	{
		$result = [];

		foreach ($this->getLiftTypes() as $type)
		{
			$result[] = [
				'ID' => $type,
				'VALUE' => $this->getLiftTitle($type),
			];
		}

		return $result;
	}
}