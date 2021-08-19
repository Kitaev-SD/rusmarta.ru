<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Command;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Trading\Service as TradingService;
use Yandex\Market\Reference\Assert;

class DeliveryShipmentDate
{
	protected $shipmentSchedule;
	protected $deliveryDate;
	protected $shipmentTimetable;
	protected $deliveryOption;
	protected $deliveryTimetable;
	protected $now;
	protected $calculateDirection;
	protected $calculateOffset;

	public function __construct(
		TradingService\MarketplaceDbs\Options\ShipmentSchedule $shipmentSchedule,
		TradingService\MarketplaceDbs\Options\DeliveryOption $deliveryOption = null,
		Main\Type\Date $deliveryDate = null,
		Main\Type\Date $now = null
	)
	{
		$this->shipmentSchedule = $shipmentSchedule;
		$this->deliveryOption = $deliveryOption !== null ? $deliveryOption : $shipmentSchedule->makeCommonDeliveryOption();
		$this->deliveryDate = $deliveryDate;
		$this->now = $now !== null ? $now : new Main\Type\DateTime();

		$this->shipmentTimetable = new TradingService\MarketplaceDbs\Options\Timetable(
			$this->shipmentSchedule->getSchedule(),
			$this->shipmentSchedule->getHoliday()
		);
		$this->deliveryTimetable = new TradingService\MarketplaceDbs\Options\Timetable(
			$this->deliveryOption->getSchedule(),
			$this->deliveryOption->getHoliday()
		);
	}

	public function setCalculateDirection($direction)
	{
		$this->calculateDirection = (bool)$direction;
	}

	public function getCalculateDirection()
	{
		return $this->calculateDirection !== null
			? $this->calculateDirection
			: $this->deliveryOption->getShipmentDateDirection();
	}

	public function setCalculateOffset($offset)
	{
		$this->calculateOffset = (int)$offset;
	}

	public function getCalculateOffset()
	{
		$result = $this->calculateOffset !== null
			? $this->calculateOffset
			: $this->deliveryOption->getShipmentDateOffset();

		Assert::notNull($result, 'shipmentDateOffset');

		return $result;
	}

	/** @return Main\Type\Date */
	public function execute()
	{
		$direction = $this->getCalculateDirection();
		$offset = $this->getCalculateOffset();
		/** @var Main\Type\Date $startDate */
		$startDate = $this->getStartDate($direction);
		$hasIntersection = $this->shipmentTimetable->hasIntersection($this->deliveryTimetable);
		$result = $startDate;
		$iterationLimit = 100;
		$iterationCount = 0;

		do
		{
			if (++$iterationCount > $iterationLimit)
			{
				throw new Main\SystemException('infinite loop on search intersection of shipment schedule and delivery option');
			}

			$shipmentDate = $this->getShipmentReadyDate($result, $offset, $direction);
			$deliveryDate = $this->getDeliveryWorkingDate($shipmentDate, $hasIntersection ? $direction : true);
			$offset = 0;

			$result = $deliveryDate;

			if (!$hasIntersection) { break; }
		}
		while (Market\Data\Date::compare($shipmentDate, $deliveryDate) !== 0);

		$result = $this->applyLimit($result, $direction);

		return $result;
	}

	protected function getShipmentReadyDate(Main\Type\Date $from, $offset, $direction)
	{
		$result = clone $from;
		$matchRule = $direction
			? TradingService\MarketplaceDbs\Options\ScheduleOption::MATCH_UNTIL_END
			: TradingService\MarketplaceDbs\Options\ScheduleOption::MATCH_AFTER_START;

		while (
			$offset > 0
			|| !$this->shipmentTimetable->isMatch($result, $matchRule)
		)
		{
			$result = $this->shipmentTimetable->getNextWorkingDay($result, !$direction);
			--$offset;
		}

		return $result;
	}

	protected function getDeliveryWorkingDate(Main\Type\Date $from, $direction)
	{
		$matchRule = $direction
			? TradingService\MarketplaceDbs\Options\ScheduleOption::MATCH_UNTIL_END
			: TradingService\MarketplaceDbs\Options\ScheduleOption::MATCH_AFTER_START;

		return $this->deliveryTimetable->isMatch($from, $matchRule)
			? clone $from
			: $this->deliveryTimetable->getNextWorkingDay($from, !$direction);
	}

	protected function getStartDate($direction)
	{
		if ($direction)
		{
			$result = clone $this->now;
			$result = $this->applyDelay($result, true);
		}
		else
		{
			Assert::notNull($this->deliveryDate, 'deliveryDate');
			$result = clone $this->deliveryDate;
		}

		return $result;
	}

	protected function getDelay()
	{
		$delay = Market\Data\Time::makeIntervalString($this->deliveryOption->getShipmentDelay());

		if ($delay === null)
		{
			$delay = Market\Data\Time::makeIntervalString($this->shipmentSchedule->getShipmentDelay());
		}

		return $delay;
	}

	protected function applyDelay(Main\Type\Date $date, $direction)
	{
		$delay = $this->getDelay();

		if ($delay === null || !$direction) { return $date; }

		$date->add($delay);

		return $date;
	}

	protected function applyLimit(Main\Type\Date $date, $direction)
	{
		if (!$direction)
		{
			$result = Market\Data\Date::max($this->now, $date);
		}
		else if ($this->deliveryDate !== null)
		{
			$result = Market\Data\Date::min($this->deliveryDate, $date);
		}
		else
		{
			$result = $date;
		}

		return $result;
	}
}