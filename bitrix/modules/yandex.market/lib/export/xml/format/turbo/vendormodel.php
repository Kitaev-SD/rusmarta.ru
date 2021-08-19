<?php

namespace Yandex\Market\Export\Xml\Format\Turbo;

use Bitrix\Main;
use Yandex\Market\Export\Xml;

class VendorModel extends Xml\Format\YandexMarket\VendorModel
{
	public function getPublishNote()
	{
		return Data\Info::getPublishNote();
	}

	public function getSupportedFields()
	{
		return [];
	}

	public function getRoot()
	{
		$result = parent::getRoot();
		$shop = $result->getChild('shop');

		if ($shop !== null)
		{
			$this->removeChildTags($shop, [
				'cpa',
				'enable_auto_discounts',
			]);
		}

		return $result;
	}

	public function getOffer()
	{
		$result = parent::getOffer();

		$this->overrideTags($result->getChildren(), [
			'description' => [ 'required' => true ],
		]);

		$this->removeChildTags($result, [
			'cpa',
			'enable_auto_discounts',
			'count',
			'cargo-types',
		]);

		return $result;
	}
}