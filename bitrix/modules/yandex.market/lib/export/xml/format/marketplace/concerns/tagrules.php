<?php

namespace Yandex\Market\Export\Xml\Format\Marketplace\Concerns;

use Yandex\Market\Export\Xml;
use Yandex\Market\Type;

/**
 * @method removeChildTags(Xml\Tag\Base $tag, string[] $names)
 * @method overrideTags(Xml\Tag\Base[] $tags, array $rules)
 */
trait TagRules
{
	public function isSupportDeliveryOptions()
	{
		return false;
	}

	public function getCurrency()
	{
		return null;
	}

	public function getPromo($type = null)
	{
		return null;
	}

	public function getPromoProduct($type = null)
	{
		return null;
	}

	public function getPromoGift($type = null)
	{
		return null;
	}

	public function getGift()
	{
		return null;
	}

	protected function sanitizeRoot(Xml\Tag\Base $root)
	{
		$shop = $root->getChild('shop');

		if ($shop === null) { return; }

		$this->removeChildTags($shop, [ 'cpa', 'enable_auto_discounts', 'currencies', 'gifts', 'promos' ]);
	}

	protected function extendOffer(Xml\Tag\Base $offer)
	{
		$this->removeChildTags($offer, ['cargo-types']);
		$offer->addChild(new Xml\Tag\Base(['name' => 'manufacturer', 'visible' => true]), 'manufacturer_warranty');

		$offer->addChildren([
			new Xml\Tag\Expiry(['name' => 'period-of-validity-days']),
			new Xml\Tag\Base(['name' => 'comment-validity-days']),
			new Xml\Tag\Expiry(['name' => 'service-life-days']),
			new Xml\Tag\Base(['name' => 'comment-life-days']),
			new Xml\Tag\Expiry(['name' => 'warranty-days']),
			new Xml\Tag\Base(['name' => 'comment-warranty']),
			new Xml\Tag\Base(['name' => 'certificate']),
		], 'dimensions');

		$this->removeChildTags($offer, ['count']); // add below for sorting
		$offer->addChildren([
			new Xml\Tag\Base(['name' => 'tn-ved-code', 'wrapper_name' => 'tn-ved-codes', 'multiple' => true, 'value_type' => Type\Manager::TYPE_TN_VED_CODE]),
			new Xml\Tag\ShopSku(['required' => true]),
			new Xml\Tag\Base(['name' => 'market-sku']),
			new Xml\Tag\Base([
				'name' => 'availability',
				'value_type' => Type\Manager::TYPE_BOOLEAN,
				'overrides' => [
					'true' => 'ACTIVE',
					'false' => 'INACTIVE',
					'archive' => 'DELISTED',
				],
			]),
			new Xml\Tag\Disabled(),
			new Xml\Tag\Count(),
			new Xml\Tag\Base(['name' => 'transport-unit', 'value_type' => Type\Manager::TYPE_NUMBER]),
			new Xml\Tag\Base(['name' => 'min-delivery-pieces', 'value_type' => Type\Manager::TYPE_NUMBER]),
			new Xml\Tag\Base(['name' => 'quantum', 'value_type' => Type\Manager::TYPE_NUMBER]),
			new Xml\Tag\Base(['name' => 'leadtime', 'value_type' => Type\Manager::TYPE_NUMBER]),
			new Xml\Tag\Base(['name' => 'box-count', 'value_type' => Type\Manager::TYPE_NUMBER]),
			new Xml\Tag\Base(['name' => 'delivery-weekday', 'wrapper_name' => 'delivery-weekdays', 'multiple' => true, 'value_type' => Type\Manager::TYPE_WEEKDAY]),
		]);
	}

	protected function sanitizeOffer(Xml\Tag\Base $offer)
	{
		$available = $offer->getAttribute('available');

		if ($available !== null)
		{
			$available->extendParameters([ 'visible' => false ]);
		}

		$this->overrideTags($offer->getChildren(), [
			'picture' => [ 'required' => false ],
			'country_of_origin' => [ 'visible' => true ],
			'dimensions' => [ 'visible' => true ],
			'weight' => [ 'visible' => true ],
			'param' => [ 'visible' => false ],
		]);

		$this->removeChildTags($offer, ['condition', 'credit-template', 'purchase_price']);
	}
}
