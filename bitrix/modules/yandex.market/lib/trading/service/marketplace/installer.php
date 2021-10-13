<?php

namespace Yandex\Market\Trading\Service\Marketplace;

use Yandex\Market;
use Bitrix\Main;
use Yandex\Market\Trading\Entity as TradingEntity;
use Yandex\Market\Trading\Service as TradingService;

class Installer extends TradingService\Common\Installer
{
	use Market\Reference\Concerns\HasLang;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
	}

	public function install(TradingEntity\Reference\Environment $environment, $siteId, array $context = [])
	{
		parent::install($environment, $siteId, $context);
		$this->installListener($environment);
		$this->installAdminExtension($environment);
		$this->installShipmentMenu();
	}

	public function postInstall(TradingEntity\Reference\Environment $environment, $siteId, array $context = [])
	{
		$this->installSyncAgent($context);
	}

	public function uninstall(TradingEntity\Reference\Environment $environment, $siteId, array $context = [])
	{
		parent::uninstall($environment, $siteId, $context);
		$this->uninstallListener($environment, $context);
		$this->uninstallAdminExtension($environment, $context);
		$this->uninstallShipmentMenu($context);
		$this->uninstallSyncAgent($context);
	}

	protected function installListener(TradingEntity\Reference\Environment $environment)
	{
		$environment->getListener()->bind();
	}

	protected function uninstallListener(TradingEntity\Reference\Environment $environment, array $context)
	{
		if (!$context['SERVICE_USED'])
		{
			$environment->getListener()->unbind();
		}
	}

	protected function installAdminExtension(TradingEntity\Reference\Environment $environment)
	{
		$environment->getAdminExtension()->install();
	}

	protected function uninstallAdminExtension(TradingEntity\Reference\Environment $environment, array $context)
	{
		if (!$context['SERVICE_USED'])
		{
			$environment->getAdminExtension()->uninstall();
		}
	}

	protected function installShipmentMenu()
	{
		if (!$this->isShipmentMenuSupported()) { return; }

		Market\Config::setOption('menu_logistic', 'Y');
	}

	protected function uninstallShipmentMenu(array $context)
	{
		if (!empty($context['BEHAVIOR_USED']) || !$this->isShipmentMenuSupported()) { return; }

		Market\Config::removeOption('menu_logistic');
	}

	protected function isShipmentMenuSupported()
	{
		return $this->provider->getRouter()->hasDataAction('admin/shipments');
	}

	protected function installSyncAgent(array $context)
	{
		Market\Reference\Assert::notNull($context['SETUP_ID'], 'context["SETUP_ID"]');

		$setupId = $context['SETUP_ID'];
		$nextExec = $this->getSyncAgentNextExec();

		Market\Trading\State\OrderStatusSync::register([
			'method' => 'start',
			'arguments' => [ $setupId ],
			'next_exec' => ConvertTimeStamp($nextExec->getTimestamp(), 'FULL'),
		]);
	}

	protected function getSyncAgentNextExec()
	{
		$result = new Main\Type\DateTime();
		$result->setTime(mt_rand(0, 10), mt_rand(0, 59));

		if ($result->getTimestamp() <= time())
		{
			$result->add('P1D');
		}

		return $result;
	}

	protected function uninstallSyncAgent(array $context)
	{
		Market\Reference\Assert::notNull($context['SETUP_ID'], 'context["SETUP_ID"]');

		$setupId = $context['SETUP_ID'];

		Market\Trading\State\OrderStatusSync::unregister([
			'method' => 'start',
			'arguments' => [ $setupId ],
		]);
		Market\Trading\State\OrderStatusSync::unregister([
			'method' => 'sync',
			'arguments' => [ $setupId ],
			'search' => Market\Reference\Agent\Controller::SEARCH_RULE_SOFT,
		]);
	}
}