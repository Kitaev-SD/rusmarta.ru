<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Options;

use Yandex\Market;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

class ShipmentSchedule extends TradingService\Reference\Options\Fieldset
{
	use Market\Reference\Concerns\HasMessage;

	public function makeCommonDeliveryOption()
	{
		$option = new DeliveryOption($this->provider);
		$option->setValues([
			'SHIPMENT_DATE_BEHAVIOR' => $this->getSchedule()->hasValid()
				? DeliveryOption::SHIPMENT_DATE_BEHAVIOR_ORDER_DAY
				: DeliveryOption::SHIPMENT_DATE_BEHAVIOR_DELIVERY_DAY,
		]);

		return $option;
	}

	/** @return ScheduleOptions */
	public function getSchedule()
	{
		return $this->getFieldsetCollection('SCHEDULE');
	}

	/** @return string|null */
	public function getShipmentDelay()
	{
		return $this->getValue('SHIPMENT_DELAY');
	}

	/** @return HolidayOption */
	public function getHoliday()
	{
		return $this->getFieldset('HOLIDAY');
	}

	public function getFieldDescription(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return parent::getFieldDescription($environment, $siteId) + [
			'SETTINGS' => [
				'SUMMARY' => '#SCHEDULE#',
				'PLACEHOLDER' => self::getMessage('PLACEHOLDER'),
				'LAYOUT' => 'summary',
				'MODAL_WIDTH' => 600,
				'MODAL_HEIGHT' => 450,
			],
		];
	}

	public function getFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return
			$this->getSelfFields($environment, $siteId)
			+ $this->getHolidayFields($environment, $siteId);
	}

	protected function getSelfFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return [
			'SCHEDULE' => $this->getSchedule()->getFieldDescription($environment, $siteId) + [
				'TYPE' => 'fieldset',
				'NAME' => self::getMessage('SCHEDULE'),
				'GROUP' => self::getMessage('SCHEDULE_GROUP'),
				'HELP_MESSAGE' => self::getMessage('SCHEDULE_HELP'),
			],
			'SHIPMENT_DELAY' => [
				'TYPE' => 'time',
				'NAME' => self::getMessage('SHIPMENT_DELAY'),
				'HELP_MESSAGE' => self::getMessage('SHIPMENT_DELAY_HELP'),
			],
		];
	}

	protected function getHolidayFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = [];
		$defaults = [
			'GROUP' => self::getMessage('HOLIDAY_GROUP'),
		];

		foreach ($this->getHoliday()->getFields($environment, $siteId) as $name => $field)
		{
			$key = sprintf('HOLIDAY[%s]', $name);
			$overrides = $this->getHolidayFieldOverrides($name);

			$result[$key] = $overrides + $field + $defaults;
		}

		return $result;
	}

	protected function getHolidayFieldOverrides($name)
	{
		$langKeys = [
			'NAME' => '',
			'HELP_MESSAGE' => 'HELP',
		];
		$result = [];

		foreach ($langKeys as $resultKey => $type)
		{
			$suffix = ($type !== '' ? '_' . $type : '');
			$message = (string)static::getMessage('HOLIDAY_' . $name . $suffix, null, '');

			if ($message === '') { continue; }

			$result[$resultKey] = $message;
		}

		return $result;
	}

	protected function getFieldsetCollectionMap()
	{
		return [
			'SCHEDULE' => ScheduleOptions::class,
		];
	}

	protected function getFieldsetMap()
	{
		return [
			'HOLIDAY' => HolidayOption::class,
		];
	}
}