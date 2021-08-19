<?php

namespace Yandex\Market\Trading\Service\Reference\Action;

use Yandex\Market\Api as Api;

abstract class FormActivity extends AbstractActivity
{
	abstract public function getFields();

	abstract public function getValues(Api\Model\Order $order);

	abstract public function getPayload(array $values);
}