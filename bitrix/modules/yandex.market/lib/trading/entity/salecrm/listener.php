<?php

namespace Yandex\Market\Trading\Entity\SaleCrm;

use Yandex\Market\Trading\Entity as TradingEntity;

class Listener extends TradingEntity\Sale\Listener
{
	protected static function isAdminPage($path)
	{
		if (
			preg_match('#/crm\.order\..+?/#', $path) // is components namespace crm.order
			&& preg_match('#ajax\.php$#', $path) // ajax page
		)
		{
			$result = true;
		}
		else
		{
			$result = parent::isAdminPage($path);
		}

		return $result;
	}

	public function bind()
	{
		$this->unbindParent();
		parent::bind();
	}

	protected function unbindParent()
	{
		$parent = new TradingEntity\Sale\Listener($this->environment);
		$parent->unbind();
	}
}