<?php

namespace Yandex\Market\Trading\Service\MarketplaceDbs\Api\Orders;

use Yandex\Market;

class Request extends Market\Api\Partner\Orders\Request
{
	protected $onlyWaitingForCancellationApprove;

	/** @return bool|null */
	public function getOnlyWaitingForCancellationApprove()
	{
		return $this->onlyWaitingForCancellationApprove;
	}

	/** @param bool $onlyWaitingForCancellationApprove */
	public function setOnlyWaitingForCancellationApprove($onlyWaitingForCancellationApprove)
	{
		$this->onlyWaitingForCancellationApprove = (bool)$onlyWaitingForCancellationApprove;
	}

	public function processParameters(array $parameters)
	{
		foreach ($parameters as $key => $value)
		{
			if ($key === 'onlyWaitingForCancellationApprove')
			{
				$this->setOnlyWaitingForCancellationApprove($value);
				unset($parameters[$key]);
			}
		}

		parent::processParameters($parameters);
	}

	public function getQuery()
	{
		$result = parent::getQuery();
		$onlyWaitingForCancellationApprove = $this->getOnlyWaitingForCancellationApprove();

		if ($onlyWaitingForCancellationApprove !== null)
		{
			$result['onlyWaitingForCancellationApprove'] = $onlyWaitingForCancellationApprove ? 'TRUE' : 'FALSE';
		}

		return $result;
	}

	public function buildResponse($data)
	{
		return new Response($data);
	}
}
