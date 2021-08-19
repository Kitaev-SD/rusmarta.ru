<?php

namespace Yandex\Market\Ui\Trading;

use Yandex\Market;
use Bitrix\Main;

class OrderList extends Market\Ui\Reference\Page
{
	use Market\Reference\Concerns\HasLang;
	use Market\Ui\Trading\Concerns\HasHandleMigration;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
	}

	protected function getReadRights()
	{
		return Market\Ui\Access::RIGHTS_PROCESS_TRADING;
	}

	public function show()
	{
		$setupCollection = $this->getSetupCollection();
		$setupId = $this->getRequestSetupId() ?: $this->getCookieSetupId();

		try
		{
			$setup = $this->resolveSetup($setupCollection, $setupId);

			$this->showSetupSelector($setupCollection, $setup->getId());
			$this->showOrderList($setup);

			$this->setCookieSetupId($setup->getId());
		}
		catch (Main\ObjectException $exception)
		{
			$this->showSetupSelector($setupCollection, $setupId, true);
			$this->showError($exception->getMessage());
		}
		catch (Main\ObjectNotFoundException $exception)
		{
			if ($this->getRequestSetupId() === null && $this->getCookieSetupId() === $setupId)
			{
				$this->resetCookieSetupId();
			}

			$this->showSetupSelector($setupCollection, $setupId, true);
			$this->showError($exception->getMessage());
		}
	}

	public function handleException(\Exception $exception)
	{
		$isHandled = (
			$this->handleMigration($exception)
			|| $this->handleDeprecated($exception)
		);

		if (!$isHandled)
		{
			$this->showError($exception->getMessage());
		}
	}

	protected function showError($message)
	{
		\CAdminMessage::ShowMessage([
			'TYPE' => 'ERROR',
			'MESSAGE' => $message,
		]);
	}

	protected function showSetupSelector(Market\Trading\Setup\Collection $setupCollection, $selectedId = null, $force = false)
	{
		global $APPLICATION;

		$options = $this->buildRoleOptions($setupCollection);
		$showLimit = $force ? 0 : 1;

		if (count($options) <= $showLimit) { return; }

		$usedBehaviors = array_column($options, 'BEHAVIOR');
		$usedSites = array_unique(array_column($options, 'SITE_ID'));
		$useOnlyGroup = true;

		if (count($usedBehaviors) !== count(array_unique($usedBehaviors)))
		{
			$useOnlyGroup = false;
		}
		else if (count($usedSites) > 1)
		{
			$useOnlyGroup = false;
		}

		echo '<div style="margin-bottom: 10px;">';

		foreach ($options as $option)
		{
			$title = $useOnlyGroup ? $option['GROUP'] : $option['VALUE'];

			if ($option['ID'] === (int)$selectedId)
			{
				echo sprintf(
					' <span class="adm-btn adm-btn-active">%s</span>',
					htmlspecialcharsbx($title)
				);
			}
			else
			{
				$url = $APPLICATION->GetCurPageParam(http_build_query([ 'setup' => $option['ID'] ]), [ 'setup' ]);

				echo sprintf(
					' <a class="adm-btn" href="%s">%s</a>',
					htmlspecialcharsbx($url),
					htmlspecialcharsbx($title)
				);
			}
		}

		echo '</div>';
	}

	protected function buildRoleOptions(Market\Trading\Setup\Collection $setupCollection)
	{
		$result = [];
		$usedBehaviors = [];

		/** @var Market\Trading\Setup\Model $setup */
		foreach ($setupCollection as $setup)
		{
			if (!$setup->isActive()) { continue; }

			$siteId = $setup->getSiteId();
			$service = $setup->getService();
			$behaviorCode = $service->getBehaviorCode();
			$behaviorTitle = $setup->getService()->getInfo()->getTitle('BEHAVIOR');
			$title = $setup->getField('NAME');

			$usedBehaviors[$behaviorCode] = true;

			if ($title === $setup->getDefaultName())
			{
				$siteEntity = $setup->getEnvironment()->getSite();
				$title = sprintf('[%s] %s (%s)', $siteId, $siteEntity->getTitle($siteId), $behaviorTitle);
			}

			$result[] = [
				'ID' => (int)$setup->getId(),
				'VALUE' => $title,
				'BEHAVIOR' => $behaviorCode,
				'SITE_ID' => $siteId,
				'GROUP' => $behaviorTitle,
			];
		}

		if (count($usedBehaviors) > 1)
		{
			$result = $this->sortRoleOptionsByBehavior($result);
		}

		return $result;
	}

	protected function sortRoleOptionsByBehavior($options)
	{
		$serviceCode = $this->getServiceCode();
		$behaviors = Market\Trading\Service\Manager::getBehaviors($serviceCode);
		$behaviorsSort = array_flip($behaviors);

		uasort($options, static function($optionA, $optionB) use ($behaviorsSort) {
			$sortA = isset($behaviorsSort[$optionA['BEHAVIOR']]) ? $behaviorsSort[$optionA['BEHAVIOR']] : 500;
			$sortB = isset($behaviorsSort[$optionB['BEHAVIOR']]) ? $behaviorsSort[$optionB['BEHAVIOR']] : 500;

			if ($sortA === $sortB) { return 0; }

			return $sortA < $sortB ? -1 : 1;
		});

		return $options;
	}

	protected function showOrderList(Market\Trading\Setup\Model $setup)
	{
		global $APPLICATION;

		$documents = $this->getPrintDocuments($setup);
		$activities = $this->getServiceActivities($setup);

		$this->initializePrintActions($setup, $documents);
		$this->initializeActivityActions($setup, $activities);

		$APPLICATION->IncludeComponent('yandex.market:admin.grid.list', '', [
			'GRID_ID' => 'YANDEX_MARKET_ADMIN_TRADING_ORDER_LIST',
			'PROVIDER_TYPE' => 'TradingOrder',
			'CONTEXT_MENU_EXCEL' => 'Y',
			'SETUP_ID' => $setup->getId(),
			'BASE_URL' => $this->getComponentBaseUrl($setup),
			'PAGER_LIMIT' => 50,
			'DEFAULT_FILTER_FIELDS' => [
				'STATUS',
				'DATE_CREATE',
				'DATE_SHIPMENT',
				'FAKE',
			],
			'DEFAULT_LIST_FIELDS' => [
				'ID',
				'ACCOUNT_NUMBER',
				'DATE_CREATE',
				'DATE_SHIPMENT',
				'BASKET',
				'BOX_COUNT',
				'TOTAL',
				'SUBSIDY',
				'STATUS_LANG',
			],
			'ROW_ACTIONS' => $this->getOrderListRowActions($setup, $documents, $activities),
			'ROW_ACTIONS_PERSISTENT' => 'Y',
			'GROUP_ACTIONS' => $this->getOrderListGroupActions($setup, $documents),
			'GROUP_ACTIONS_PARAMS' => $this->getOrderListGroupActionsParams(),
			'UI_GROUP_ACTIONS' => $this->getOrderListUiGroupActions($setup, $documents),
			'UI_GROUP_ACTIONS_PARAMS' => [
				'disable_action_target' => true,
			],
			'CANCEL_STATUS' => $this->getCancelStatus($setup),
			'CHECK_ACCESS' => !Market\Ui\Access::isWriteAllowed(),
			'RELOAD_EVENTS' => [
				'yamarketShipmentSubmitEnd',
				'yamarketFormSave',
			],
		]);
	}

	protected function initializePrintActions(Market\Trading\Setup\Model $setup, $documents)
	{
		if (empty($documents)) { return; }

		static::loadMessages();

		Market\Ui\Library::load('jquery');

		Market\Ui\Assets::loadPluginCore();
		Market\Ui\Assets::loadPlugins([
			'lib.dialog',
			'lib.printdialog',
			'OrderList.DialogAction',
			'OrderList.Print',
		]);

		Market\Ui\Assets::loadMessages([
			'PRINT_DIALOG_SUBMIT',
			'UI_TRADING_ORDER_LIST_PRINT_REQUIRE_SELECT_ORDERS'
		]);

		$this->addDialogActionsScript('Print', [
			'url' => Market\Ui\Admin\Path::getModuleUrl('trading_order_print', [
				'view' => 'dialog',
				'setup' => $setup->getId(),
				'alone' => 'Y',
			]),
			'items' => $this->getPrintItems($documents),
		]);
	}

	protected function initializeActivityActions(Market\Trading\Setup\Model $setup, $activities)
	{
		if (empty($activities)) { return; }

		Market\Ui\Library::load('jquery');

		Market\Ui\Assets::loadPluginCore();
		Market\Ui\Assets::loadPlugins([
			'lib.dialog',
			'Ui.ModalForm',
			'OrderList.DialogAction',
			'OrderList.Activity',
		]);

		$this->addDialogActionsScript('Activity', [
			'url' => Market\Ui\Admin\Path::getModuleUrl('trading_order_activity', [
				'view' => 'dialog',
				'setup' => $setup->getId(),
				'alone' => 'Y',
			]),
			'items' => $this->getActivityItems($activities),
		]);
	}

	protected function addDialogActionsScript($type, array $parameters)
	{
		$pageAssets = Main\Page\Asset::getInstance();
		$contents = sprintf(
			'<script>
				BX.YandexMarket.OrderList["%s"] = new BX.YandexMarket.OrderList.%s(null, ' . \CUtil::PhpToJSObject($parameters) . ');
			</script>',
			Market\Data\TextString::toLower($type),
			$type
		);

		$pageAssets->addString($contents, false, Main\Page\AssetLocation::AFTER_JS);
	}

	/**
	 * @param Market\Trading\Service\Reference\Document\AbstractDocument[] $documents
	 *
	 * @return array
	 */
	protected function getPrintItems($documents)
	{
		$result = [];

		foreach ($documents as $type => $document)
		{
			$result[] = [
				'TYPE' => $type,
				'TITLE' => $document->getTitle(),
			];
		}

		return $result;
	}

	protected function getActivityItems($activities)
	{
		$result = [];

		foreach ($activities as $path => $activity)
		{
			$items = $this->makeActivityItems($path, $activity);

			if (empty($items)) { continue; }

			array_push($result, ...$items);
		}

		return $result;
	}

	protected function makeActivityItems($path, Market\Trading\Service\Reference\Action\AbstractActivity $activity, $chain = '')
	{
		$result = [];

		if ($activity instanceof Market\Trading\Service\Reference\Action\ComplexActivity)
		{
			foreach ($activity->getActivities() as $key => $child)
			{
				$childChain = ($chain !== '' ? $chain . '.' . $key : $key);
				$childItems = $this->makeActivityItems($path, $child, $childChain);

				if (empty($childItems)) { continue; }

				array_push($result, ...$childItems);
			}
		}
		else
		{
			$result[] = [
				'TYPE' => $path . ($chain !== '' ? '|' . $chain : ''),
				'TITLE' => $activity->getTitle(),
			];
		}

		return $result;
	}

	/**
	 * @param Market\Trading\Setup\Model $setup
	 * @param Market\Trading\Service\Reference\Document\AbstractDocument[] $documents
	 * @param Market\Trading\Service\Reference\Action\AbstractActivity[] $activities
	 *
	 * @return array
	 */
	protected function getOrderListRowActions(Market\Trading\Setup\Model $setup, $documents, $activities)
	{
		return
			$this->getOrderListRowCommonActions($setup)
			+ $this->getOrderListRowStatusActions($setup)
			+ $this->getOrderListRowActivityActions($setup, $activities)
			+ $this->getOrderListRowCancelActions($setup)
			+ $this->getOrderListRowPrintActions($setup, $documents);
	}

	protected function getOrderListRowCommonActions(Market\Trading\Setup\Model $setup)
	{
		return [
			'EDIT' => [
				'ICON' => 'view',
				'TEXT' =>
					$setup->getService()->getInfo()->getMessage('ORDER_VIEW_TAB')
					?: static::getLang('UI_TRADING_ORDER_LIST_ACTION_ORDER_VIEW'),
				'MODAL' => 'Y',
				'MODAL_TITLE' => static::getLang('UI_TRADING_ORDER_LIST_ACTION_ORDER_VIEW_MODAL_TITLE'),
				'MODAL_PARAMETERS' => [
					'width' => 1024,
					'height' => 750,
				],
				'URL' => Market\Ui\Admin\Path::getModuleUrl('trading_order_view', [
					'lang' => LANGUAGE_ID,
					'view' => 'popup',
					'setup' => $setup->getId(),
					'site' => $setup->getSiteId(),
				]) . '&id=#ID#',
			],
		];
	}

	protected function getOrderListRowStatusActions(Market\Trading\Setup\Model $setup)
	{
		$variants = $this->getOutgoingStatuses($setup);

		if (empty($variants)) { return []; }

		return [
			'STATUS' => [
				'TEXT' => self::getLang('UI_TRADING_ORDER_LIST_ACTION_STATUS'),
				'MENU' => $this->makeOrderListRowStatusAction($variants),
			],
		];
	}

	protected function getOrderListRowCancelActions(Market\Trading\Setup\Model $setup)
	{
		$variants = $this->getCancelReasons($setup);
		$cancelStatus = $this->getCancelStatus($setup);

		if ($cancelStatus === null) { return []; }

		if (!empty($variants))
		{
			$menu = $this->makeOrderListRowCancelAction($variants);
		}
		else
		{
			$statusVariants = [];
			$statusVariants[] = [
				'NAME' => $this->getStatusTitle($setup, $cancelStatus),
				'VALUE' => $cancelStatus,
			];

			$menu = $this->makeOrderListRowStatusAction($statusVariants, true);
		}

		return [
			'CANCEL' => [
				'TEXT' => self::getLang('UI_TRADING_ORDER_LIST_ACTION_CANCEL'),
				'MENU' => $menu,
			],
		];
	}

	protected function makeOrderListRowStatusAction($variants, $useConfirm = false)
	{
		$menu = [];

		foreach ($variants as $outgoingVariant)
		{
			$key = 'STATUS_' . Market\Data\TextString::toUpper($outgoingVariant['VALUE']);
			$item = [
				'TEXT' => $outgoingVariant['NAME'],
				'ACTION' => 'status:' . $outgoingVariant['VALUE'],
			];

			if ($useConfirm)
			{
				$item['CONFIRM'] = true;
				$item['CONFIRM_MESSAGE'] = static::getLang('UI_TRADING_ORDER_LIST_ACTION_STATUS_CONFIRM', [
					'#TITLE#' => $outgoingVariant['NAME'],
				]);
			}

			$menu[$key] = $item;
		}

		return $menu;
	}

	protected function makeOrderListRowCancelAction($variants)
	{
		$menu = [];

		foreach ($variants as $outgoingVariant)
		{
			$key = 'CANCEL_' . Market\Data\TextString::toUpper($outgoingVariant['VALUE']);

			$menu[$key] = [
				'TEXT' => $outgoingVariant['NAME'],
				'ACTION' => 'cancel:' . $outgoingVariant['VALUE'],
				'CONFIRM' => true,
				'CONFIRM_MESSAGE' => static::getLang('UI_TRADING_ORDER_LIST_ACTION_CANCEL_CONFIRM', [
					'#REASON#' => $outgoingVariant['NAME'],
				]),
			];
		}

		return $menu;
	}

	/**
	 * @param Market\Trading\Setup\Model $setup
	 * @param Market\Trading\Service\Reference\Action\AbstractActivity[] $activities
	 *
	 * @return array
	 */
	protected function getOrderListRowActivityActions(Market\Trading\Setup\Model $setup, $activities)
	{
		$result = [];

		foreach ($activities as $path => $activity)
		{
			$code = 'ACTIVITY_' . Market\Data\TextString::toUpper(str_replace('/', '_', $path));

			$result[$code] = $this->makeOrderListRowActivityAction($path, $activity);
		}

		return $result;
	}

	protected function makeOrderListRowActivityAction($path, Market\Trading\Service\Reference\Action\AbstractActivity $activity, $chain = '')
	{
		$result = [
			'TEXT' => $activity->getTitle(),
			'FILTER' => $activity->getFilter(),
		];

		if ($activity instanceof Market\Trading\Service\Reference\Action\ComplexActivity)
		{
			$result['MENU'] = [];

			foreach ($activity->getActivities() as $key => $child)
			{
				$childChain = ($chain !== '' ? $chain . '.' . $key : $key);

				$result['MENU'][] = $this->makeOrderListRowActivityAction($path, $child, $childChain);
			}
		}
		else if ($activity instanceof Market\Trading\Service\Reference\Action\CommandActivity)
		{
			$type = $path . ($chain !== '' ? '|' . $chain : '');

			$result['METHOD'] = sprintf(
				'BX.YandexMarket.OrderList.activity.executeCommand("%s", "#ID#", YANDEX_MARKET_ADMIN_TRADING_ORDER_LIST)',
				$type
			);
			$result += $activity->getParameters(); // confirm and etc
		}
		else if ($activity instanceof Market\Trading\Service\Reference\Action\FormActivity)
		{
			$type = $path . ($chain !== '' ? '|' . $chain : '');

			$result['METHOD'] = sprintf(
				'BX.YandexMarket.OrderList.activity.openDialog("%s", "#ID#")',
				$type
			);
		}

		return $result;
	}

	/**
	 * @param Market\Trading\Setup\Model $setup
	 * @param Market\Trading\Service\Reference\Document\AbstractDocument[] $documents
	 *
	 * @return array
	 */
	protected function getOrderListRowPrintActions(Market\Trading\Setup\Model $setup, $documents)
	{
		$menu = [];

		foreach ($documents as $type => $document)
		{
			$key = 'PRINT_' . Market\Data\TextString::toUpper($type);

			$menu[$key] = [
				'TEXT' => $document->getTitle('PRINT'),
				'METHOD' => 'BX.YandexMarket.OrderList.print.openDialog("' .  $type .  '", "#ID#")',
			];
		}

		return [
			'PRINT' => [
				'TEXT' => self::getLang('UI_TRADING_ORDER_LIST_ACTION_PRINT'),
				'MENU' => $menu,
			],
		];
	}

	/**
	 * @param Market\Trading\Setup\Model $setup
	 * @param Market\Trading\Service\Reference\Document\AbstractDocument[] $documents
	 *
	 * @return array
	 */
	protected function getOrderListGroupActions(Market\Trading\Setup\Model $setup, $documents)
	{
		return
			$this->getOrderListGroupPrintActions($documents)
			+ $this->getOrderListGroupStatusActions($setup)
			+ $this->getOrderListGroupBoxActions($setup);
	}

	protected function getOrderListGroupActionsParams()
	{
		$onSelectChange = '';
		$chooses = [
			'status',
			'boxes',
		];

		foreach ($chooses as $choose)
		{
			$onSelectChange .= sprintf(
				'BX(\'%1$s_chooser\') && (BX(\'%1$s_chooser\').style.display = (this.value == \'%1$s\' ? \'block\' : \'none\'));',
				$choose
			);
		}

		return [
			'select_onchange' => $onSelectChange,
			'disable_action_target' => true,
		];
	}

	/**
	 * @param Market\Trading\Setup\Model $setup
	 * @param Market\Trading\Service\Reference\Document\AbstractDocument[] $documents
	 *
	 * @return array
	 */
	protected function getOrderListUiGroupActions(Market\Trading\Setup\Model $setup, $documents)
	{
		return
			$this->getOrderListGroupPrintActions($documents)
			+ $this->getOrderListUiGroupStatusActions($setup)
			+ $this->getOrderListUiGroupBoxActions($setup);
	}

	protected function getOrderListGroupStatusActions(Market\Trading\Setup\Model $setup)
	{
		$variants = $this->getOutgoingStatuses($setup);

		if (empty($variants)) { return []; }

		return [
			'status' => self::getLang('UI_TRADING_ORDER_LIST_ACTION_STATUS'),
			'status_chooser' => [
				'type' => 'html',
				'value' => $this->makeGroupActionSelectHtml('status', $variants),
			],
		];
	}

	protected function getOrderListUiGroupStatusActions(Market\Trading\Setup\Model $setup)
	{
		$variants = $this->getOutgoingStatuses($setup);

		if (empty($variants)) { return []; }

		return [
			'status' => [
				'type' => 'select',
				'name' => 'status',
				'label' => self::getLang('UI_TRADING_ORDER_LIST_ACTION_STATUS'),
				'items' => $variants,
			],
		];
	}

	protected function getOrderListGroupBoxActions(Market\Trading\Setup\Model $setup)
	{
		if (!$this->isSupportBoxes($setup)) { return []; }

		$variants = $this->getBoxesVariants();

		return [
			'boxes' => self::getLang('UI_TRADING_ORDER_LIST_ACTION_SEND_BOXES'),
			'boxes_chooser' => [
				'type' => 'html',
				'value' => $this->makeGroupActionSelectHtml('boxes', $variants),
			],
		];
	}

	protected function getOrderListUiGroupBoxActions(Market\Trading\Setup\Model $setup)
	{
		if (!$this->isSupportBoxes($setup)) { return []; }

		return [
			'boxes' => [
				'type' => 'select',
				'name' => 'boxes',
				'label' => self::getLang('UI_TRADING_ORDER_LIST_ACTION_SEND_BOXES'),
				'items' => $this->getBoxesVariants(),
			],
		];
	}

	protected function isSupportBoxes(Market\Trading\Setup\Model $setup)
	{
		return $setup->getService()->getRouter()->hasAction('send/boxes');
	}

	protected function getBoxesVariants()
	{
		$variants = [];
		$plural = [
			static::getLang('UI_TRADING_ORDER_LIST_ACTION_SEND_BOXES_COUNT_1'),
			static::getLang('UI_TRADING_ORDER_LIST_ACTION_SEND_BOXES_COUNT_2'),
			static::getLang('UI_TRADING_ORDER_LIST_ACTION_SEND_BOXES_COUNT_5'),
		];

		for ($count = 1; $count <= 10; ++$count)
		{
			$variants[] = [
				'VALUE' => $count,
				'NAME' => $count . ' ' . Market\Utils::sklon($count, $plural),
			];
		}

		return $variants;
	}

	protected function makeGroupActionSelectHtml($name, $variants)
	{
		$html = sprintf('<div id="%s_chooser" style="display: none;">', $name);
		$html .= sprintf('<select name="%s">', $name);

		foreach ($variants as $outgoingVariant)
		{
			$html .= sprintf(
				'<option value="%s">%s</option>',
				$outgoingVariant['VALUE'],
				$outgoingVariant['NAME']
			);
		}

		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * @param Market\Trading\Service\Reference\Document\AbstractDocument[] $documents
	 *
	 * @return array
	 */
	protected function getOrderListGroupPrintActions($documents)
	{
		$result = [];

		foreach ($documents as $type => $document)
		{
			$key = 'PRINT_' . Market\Data\TextString::toUpper($type);
			$needSelectOrders = $document->getEntityType() !== Market\Trading\Entity\Registry::ENTITY_TYPE_NONE;

			if ($needSelectOrders)
			{
				$action = sprintf('BX.YandexMarket.OrderList.print.openGroupDialog("%s", YANDEX_MARKET_ADMIN_TRADING_ORDER_LIST)', $type);
			}
			else
			{
				$action = sprintf('BX.YandexMarket.OrderList.print.openDialog("%s")', $type);
			}

			$result[$key] = [
				'type' => 'button',
				'value' => $key,
				'name' => $document->getTitle('PRINT'),
				'action' => $action,
			];
		}

		return $result;
	}

	protected function getOutgoingStatuses(Market\Trading\Setup\Model $setup)
	{
		$service = $setup->getService();
		$status = $service->getStatus();

		if (!($status instanceof Market\Trading\Service\Common\Status)) { return []; }

		$cancelStatus = $this->getCancelStatus($setup);
		$result = [];

		foreach ($status->getOutgoingVariants() as $outgoingVariant)
		{
			if ($outgoingVariant === $cancelStatus) { continue; }

			$result[] = [
				'NAME' => $status->getTitle($outgoingVariant, 'SHORT'),
				'VALUE' => $outgoingVariant,
			];
		}

		return $result;
	}

	protected function getStatusTitle(Market\Trading\Setup\Model $setup, $status, $version = '')
	{
		return $setup->getService()->getStatus()->getTitle($status, $version);
	}

	protected function getCancelStatus(Market\Trading\Setup\Model $setup)
	{
		$service = $setup->getService();
		$status = $service->getStatus();

		if (!($status instanceof Market\Trading\Service\Common\Status)) { return null; }

		$meaningfulMap = $status->getOutgoingMeaningfulMap();

		return isset($meaningfulMap[Market\Data\Trading\MeaningfulStatus::CANCELED])
			? $meaningfulMap[Market\Data\Trading\MeaningfulStatus::CANCELED]
			: null;
	}

	protected function getCancelReasons(Market\Trading\Setup\Model $setup)
	{
		$service = $setup->getService();

		if (!($service instanceof Market\Trading\Service\Reference\HasCancelReason)) { return []; }

		$cancelReasonProvider = $service->getCancelReason();
		$result = [];

		foreach ($cancelReasonProvider->getVariants() as $cancelReasonVariant)
		{
			$result[] = [
				'NAME' => $cancelReasonProvider->getTitle($cancelReasonVariant),
				'VALUE' => $cancelReasonVariant,
			];
		}

		return $result;
	}

	protected function getServiceActivities(Market\Trading\Setup\Model $setup)
	{
		$router = $setup->getService()->getRouter();
		$environment = $setup->getEnvironment();
		$result = [];

		foreach ($router->getMap() as $path => $actionClass)
		{
			if (!$router->hasDataAction($path)) { continue; }

			$action = $router->getDataAction($path, $environment);

			if (!($action instanceof Market\Trading\Service\Reference\Action\HasActivity)) { continue; }

			$result[$path] = $action->getActivity();
		}

		return $result;
	}

	protected function getPrintDocuments(Market\Trading\Setup\Model $setup)
	{
		$printer = $setup->getService()->getPrinter();
		$result = [];

		foreach ($printer->getTypes() as $type)
		{
			$result[$type] = $printer->getDocument($type);
		}

		return $result;
	}

	protected function getComponentBaseUrl(Market\Trading\Setup\Model $setup)
	{
		global $APPLICATION;

		$queryParameters = array_filter([
			'lang' => LANGUAGE_ID,
			'service' => $setup->getServiceCode(),
			'id' => $this->getRequestSetupId(),
		]);

		return $APPLICATION->GetCurPage() . '?' . http_build_query($queryParameters);
	}

	protected function getSetupCollection()
	{
		$serviceCode = $this->getServiceCode();

		return Market\Trading\Setup\Collection::loadByFilter([
			'filter' => [
				'=TRADING_SERVICE' => $serviceCode,
			],
		]);
	}

	protected function getServiceCode()
	{
		$result = (string)$this->request->get('service');

		if ($result === '')
		{
			$message = static::getLang('UI_TRADING_ORDER_LIST_SERVICE_CODE_NOT_SET');
			throw new Main\ArgumentException($message, 'service');
		}

		if (!Market\Trading\Service\Manager::isExists($result))
		{
			$message = static::getLang('UI_TRADING_ORDER_LIST_SERVICE_CODE_INVALID', [ '#SERVICE#' => $result ]);
			throw new Main\SystemException($message);
		}

		return $result;
	}

	protected function getRequestSetupId()
	{
		return $this->request->get('setup');
	}

	protected function getCookieSetupId()
	{
		$cookieName = $this->getCookieSetupIdName();
		$value = (string)$this->request->getCookie($cookieName);

		return $value !== '' ? $value : null;
	}

	protected function resetCookieSetupId()
	{
		$response = Main\Context::getCurrent()->getResponse();

		$response->addCookie(new Main\Web\Cookie(
			$this->getCookieSetupIdName(),
			'',
			0 // remove
		));
	}

	protected function setCookieSetupId($setupId)
	{
		$response = Main\Context::getCurrent()->getResponse();

		if ((string)$this->getCookieSetupId() !== (string)$setupId)
		{
			$response->addCookie(new Main\Web\Cookie(
				$this->getCookieSetupIdName(),
				$setupId
			));
		}
	}

	protected function getCookieSetupIdName()
	{
		$serviceCode = $this->getServiceCode();

		return 'YAMARKET_TRADING_DOCUMENTS_SETUP_' . Market\Data\TextString::toUpper($serviceCode);
	}

	/**
	 * @param Market\Trading\Setup\Collection $setupCollection
	 * @param int|null $setupId
	 *
	 * @return Market\Trading\Setup\Model
	 * @throws Main\SystemException
	 */
	protected function resolveSetup(Market\Trading\Setup\Collection $setupCollection, $setupId = null)
	{
		if ($setupId !== null)
		{
			$setup = $setupCollection->getItemById($setupId);

			if ($setup === null)
			{
				$message = static::getLang('UI_TRADING_ORDER_LIST_SETUP_NOT_FOUND', [ '#ID#' => $setupId ]);
				throw new Main\ObjectNotFoundException($message);
			}

			if (!$setup->isActive())
			{
				$message = static::getLang('UI_TRADING_ORDER_LIST_SETUP_INACTIVE', [ '#ID#' => $setupId ]);
				throw new Main\ObjectException($message);
			}
		}
		else
		{
			$setup = $setupCollection->getActive();

			if ($setup === null)
			{
				$message = static::getLang('UI_TRADING_ORDER_LIST_SETUP_NOT_EXISTS');
				throw new Main\ObjectNotFoundException($message);
			}
		}

		return $setup;
	}
}