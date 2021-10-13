<?php

namespace Yandex\Market\Ui\Trading\ShipmentRequest;

use Bitrix\Main;
use Yandex\Market;

class BasketItem extends Market\Api\Reference\Model
{
	/** @return string */
	public function getId()
	{
		return (string)$this->getRequiredField('ID');
	}

	/** @return string[] */
	public function getCis()
	{
		$values = (array)$this->getField('CIS');
		$values = array_map('trim', $values);

		return array_filter($values, static function($value) { return $value !== ''; });
	}

	/** @return float|null */
	public function getCount()
	{
		return Market\Data\Number::normalize($this->getField('COUNT'));
	}

	/** @return float|null */
	public function getInitialCount()
	{
		return Market\Data\Number::normalize($this->getField('INITIAL_COUNT'));
	}

	/** @return bool */
	public function needDelete()
	{
		return $this->getField('DELETE') === 'Y';
	}
}