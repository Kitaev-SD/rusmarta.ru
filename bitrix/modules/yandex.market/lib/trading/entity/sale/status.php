<?php

namespace Yandex\Market\Trading\Entity\Sale;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Sale;

class Status extends Market\Trading\Entity\Reference\Status
{
	use Market\Reference\Concerns\HasLang;

	const STATUS_CANCELED = 'CANCELED';
	const STATUS_ALLOW_DELIVERY = 'ALLOW_DELIVERY';
	const STATUS_PAYED = 'PAYED';
	const STATUS_DEDUCTED = 'DEDUCTED';
	
	protected $orderEnum;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
	}

	public function getTitle($status, $version = '')
	{
		$orderEnum = $this->getOrderEnum();

		if (isset($orderEnum[$status]))
		{
			$result = $orderEnum[$status];
		}
		else
		{
			$result = $this->getSpecialTitle($status, $version);
		}

		return $result;
	}

	public function getEnum($variants)
	{
		$result = [];
		$orderVariants = $this->getOrderVariants();

		foreach ($variants as $variant)
		{
			if (in_array($variant, $orderVariants, true))
			{
				$statusName = '[' . $variant . '] ' . $this->getTitle($variant);
			}
			else
			{
				$statusName = $this->getTitle($variant);
			}

			$result[] = [
				'ID' => $variant,
				'VALUE' => $statusName,
			];
		}

		return $result;
	}

	public function getVariants()
	{
		return array_merge(
			$this->getSpecialVariants(),
			$this->getOrderVariants()
		);
	}

	public function getMeaningfulMap()
	{
		return [
			Market\Data\Trading\MeaningfulStatus::CREATED => 'N',
			Market\Data\Trading\MeaningfulStatus::ALLOW_DELIVERY => static::STATUS_ALLOW_DELIVERY,
			Market\Data\Trading\MeaningfulStatus::DEDUCTED => static::STATUS_DEDUCTED,
			Market\Data\Trading\MeaningfulStatus::PAYED => static::STATUS_PAYED,
			Market\Data\Trading\MeaningfulStatus::CANCELED => static::STATUS_CANCELED,
			Market\Data\Trading\MeaningfulStatus::FINISHED => 'F',
		];
	}

	protected function getSpecialVariants()
	{
		return [
			static::STATUS_ALLOW_DELIVERY,
			static::STATUS_DEDUCTED,
			static::STATUS_PAYED,
			static::STATUS_CANCELED,
		];
	}

	protected function getSpecialTitle($status, $version = '')
	{
		$statusKey = Market\Data\TextString::toUpper($status);
		$versionSuffix = ($version !== '' ? '_' . $version : '');

		return static::getLang('TRADING_ENTITY_SALE_STATUS_' . $statusKey . $versionSuffix);
	}
	
	protected function getOrderVariants()
	{
		$enum = $this->getOrderEnum();

		return array_keys($enum);
	}
	
	protected function getOrderEnum()
	{
		if ($this->orderEnum === null)
		{
			$this->orderEnum = $this->loadOrderEnum();
		}
		
		return $this->orderEnum;
	}
	
	protected function loadOrderEnum()
	{
		$result = [];
		$query = Sale\Internals\StatusTable::getList([
			'order' => [ 'SORT' => 'asc' ],
			'filter' => [ '=TYPE' => 'O', '=YAMARKET_STATUS_LANG.LID' => LANGUAGE_ID ],
			'select' => [ 'ID', 'YAMARKET_NAME' => 'YAMARKET_STATUS_LANG.NAME' ],
			'runtime' => [
				new Main\Entity\ReferenceField(
					'YAMARKET_STATUS_LANG',
					Sale\Internals\StatusLangTable::class,
					[ '=this.ID' => 'ref.STATUS_ID' ]
				)
			]
		]);

		while ($row = $query->Fetch())
		{
			$result[$row['ID']] = $row['YAMARKET_NAME'];
		}

		return $result;
	}
}