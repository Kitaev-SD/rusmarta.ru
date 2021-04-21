<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Options;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

class DeliveryOption extends TradingService\Reference\Options\Fieldset
{
	use Market\Reference\Concerns\HasLang;

	/** @var TradingService\MarketplaceDbs\Provider */
	protected $provider;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
	}

	/** @return int */
	public function getServiceId()
	{
		return (int)$this->getRequiredValue('ID');
	}

	/** @return string */
	public function getName()
	{
		return trim($this->getValue('NAME'));
	}

	/** @return string */
	public function getType()
	{
		return (string)$this->getRequiredValue('TYPE');
	}

	/** @return int|null */
	public function getDaysFrom()
	{
		return $this->getDaysValue('FROM');
 	}

	/** @return int|null */
	public function getDaysTo()
	{
		return $this->getDaysValue('TO');
 	}

 	protected function getDaysValue($key)
    {
	    $days = $this->getValue('DAYS');

	    return isset($days[$key]) && (string)$days[$key] !== '' ? (int)$days[$key] : null;
    }

	/** @return string[]|null */
	public function getOutlets()
    {
    	$values = $this->getValue('OUTLET');

    	return $values !== null ? (array)$values : null;
    }

	public function getFieldDescription(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return parent::getFieldDescription($environment, $siteId) + [
			'SETTINGS' => [
				'SUMMARY' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_SUMMARY', null, '#TYPE# &laquo;#ID#&raquo;, #DAYS#'),
				'LAYOUT' => 'summary',
			]
		];
	}

	public function getFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		try
		{
			$result = [
				'ID' => [
					'TYPE' => 'enumeration',
					'MANDATORY' => 'Y',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_ID'),
					'VALUES' => $this->getDeliveryEnum($environment, $siteId),
					'SETTINGS' => [
						'STYLE' => 'max-width: 220px;',
					],
				],
				'NAME' => [
					'TYPE' => 'string',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_NAME'),
					'SETTINGS' => [
						'MAX_LENGTH' => 50,
					],
				],
				'TYPE' => [
					'TYPE' => 'enumeration',
					'MANDATORY' => 'Y',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_TYPE'),
					'VALUES' => $this->provider->getDelivery()->getTypeEnum(),
				],
				'DAYS' => [
					'TYPE' => 'numberRange',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_DAYS'),
					'SETTINGS' => [
						'SUMMARY' => '#FROM#-#TO#',
						'UNIT' => array_filter([
							static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_DAYS_UNIT_1', null, ''),
							static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_DAYS_UNIT_2', null, ''),
							static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_DAYS_UNIT_5', null, ''),
						]),
					],
				],
				'OUTLET' => [
					'TYPE' => 'tradingOutlet',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTIONS_DELIVERY_OPTION_OUTLET'),
					'MULTIPLE' => 'Y',
					'DEPEND' => [
						'TYPE' => [
							'RULE' => 'ANY',
							'VALUE' => TradingService\MarketplaceDbs\Delivery::TYPE_PICKUP,
						],
					],
					'SETTINGS' => [
						'SERVICE' => $this->provider->getCode(),
					],
				],
			];
		}
		catch (Market\Exceptions\NotImplemented $exception)
		{
			$result = [];
		}

		return $result;
	}

	protected function getDeliveryEnum(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$delivery = $environment->getDelivery();

		return array_filter($delivery->getEnum($siteId), static function($option) {
			return $option['TYPE'] !== Market\Data\Trading\Delivery::EMPTY_DELIVERY;
		});
	}
}
