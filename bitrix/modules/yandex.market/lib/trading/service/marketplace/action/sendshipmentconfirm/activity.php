<?php

namespace Yandex\Market\Trading\Service\Marketplace\Action\SendShipmentConfirm;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Trading\Service as TradingService;

class Activity extends TradingService\Reference\Action\FormActivity
{
	use Market\Reference\Concerns\HasMessage;

	public function getTitle()
	{
		return self::getMessage('TITLE');
	}

	public function getSourceType()
	{
		return Market\Trading\Entity\Registry::ENTITY_TYPE_LOGISTIC_SHIPMENT;
	}

	public function getFilter()
	{
		return [
			'PROCESSING' => true,
		];
	}

	public function getFields()
	{
		return [
			'externalShipmentId' => [
				'TYPE' => 'string',
				'NAME' => self::getMessage('EXTERNAL_SHIPMENT_ID'),
				'MANDATORY' => 'Y',
			],
			'orderIds' => [
				'TYPE' => 'enumeration',
				'NAME' => self::getMessage('ORDER_IDS'),
				'MULTIPLE' => 'Y',
				'MANDATORY' => 'Y',
				'SETTINGS' => [
					'DISPLAY' => 'CHECKBOX',
				],
			],
		];
	}

	public function getEntityValues($entity)
	{
		/** @var TradingService\Marketplace\Model\ShipmentDetails $entity */
		Market\Reference\Assert::typeOf($entity, TradingService\Marketplace\Model\ShipmentDetails::class, 'entity');

		$orderIds = $entity->getOrderIds();

		if (empty($orderIds))
		{
			throw new Main\SystemException(self::getMessage('SHIPMENT_WITHOUT_ORDERS'));
		}

		return [
			'externalShipmentId' => $entity->getExternalId() ?: $entity->getId(),
			'orderIds' => $orderIds,
		];
	}

	public function extendFields(array $fields, array $values = null)
	{
		if (isset($fields['orderIds'], $values['orderIds']))
		{
			$fields['orderIds']['VALUES'] = array_map(
				static function($orderId) { return [ 'ID' => $orderId, 'VALUE' => $orderId ]; },
				$values['orderIds']
			);
		}

		return $fields;
	}

	public function getPayload(array $values)
	{
		return $values;
	}
}