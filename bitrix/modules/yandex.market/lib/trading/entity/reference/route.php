<?php

namespace Yandex\Market\Trading\Entity\Reference;

use Yandex\Market;
use Bitrix\Main;

abstract class Route
{
	protected $environment;

	public function __construct(Environment $environment)
	{
		$this->environment = $environment;
	}

	/**
	 * @param string $serviceCode
	 * @param string $siteId
	 *
	 * @return string
	 */
	public function getPublicPath($serviceCode, $siteId)
	{
		throw new Market\Exceptions\NotImplementedMethod(static::class, 'getPublicPath');
	}

	/**
	 * @param string $siteId
	 *
	 * @throws Main\SystemException
	 */
	public function installPublic($siteId)
	{
		throw new Market\Exceptions\NotImplementedMethod(static::class, 'installPublic');
	}

	/**
	 * @param string $siteId
	 *
	 * @throws Main\SystemException
	 */
	public function uninstallPublic($siteId)
	{
		throw new Market\Exceptions\NotImplementedMethod(static::class, 'installPublic');
	}
}