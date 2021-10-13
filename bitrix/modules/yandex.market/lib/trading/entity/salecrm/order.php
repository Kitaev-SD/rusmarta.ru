<?php

namespace Yandex\Market\Trading\Entity\SaleCrm;

use Yandex\Market\Trading\Entity as TradingEntity;
use Bitrix\Main;

class Order extends TradingEntity\Sale\Order
{
	public function getAdminEditUrl()
	{
		if (Main\Context::getCurrent()->getRequest()->isAdminSection())
		{
			return parent::getAdminEditUrl();
		}

		return sprintf(
			'/shop/orders/details/%s/',
			(int)$this->getId()
		);
	}
}