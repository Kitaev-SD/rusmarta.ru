<?php

namespace Yandex\Market\Ui\Trading;

use Yandex\Market;
use Bitrix\Main;

class OrderList extends Reference\EntityList
{
	use Market\Reference\Concerns\HasMessage;

	protected function getTargetEntity()
	{
		return Market\Trading\Entity\Registry::ENTITY_TYPE_ORDER;
	}

	protected function getUserOptionCategory()
	{
		return 'yamarket_order_grid';
	}

	protected function showGrid(Market\Trading\Setup\Model $setup)
	{
		global $APPLICATION;

		$documents = $this->getPrintDocuments($setup);
		$activities = $this->getServiceActivities($setup);

		$this->initializePrintActions($setup, $documents);
		$this->initializeActivityActions($setup, $activities);

		$APPLICATION->IncludeComponent('yandex.market:admin.grid.list', '', [
			'GRID_ID' => $this->getGridId(),
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

	protected function getGridId()
	{
		return 'YANDEX_MARKET_ADMIN_TRADING_ORDER_LIST';
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
					?: self::getMessage('ACTION_ORDER_VIEW'),
				'MODAL' => 'Y',
				'MODAL_TITLE' => self::getMessage('ACTION_ORDER_VIEW_MODAL_TITLE'),
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
				'DEFAULT' => true,
			],
		];
	}

	protected function getOrderListRowStatusActions(Market\Trading\Setup\Model $setup)
	{
		$variants = $this->getOutgoingStatuses($setup);

		if (empty($variants)) { return []; }

		return [
			'STATUS' => [
				'TEXT' => self::getMessage('ACTION_STATUS'),
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
				'TEXT' => self::getMessage('ACTION_CANCEL'),
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
				$item['CONFIRM_MESSAGE'] = self::getMessage('ACTION_STATUS_CONFIRM', [
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
				'CONFIRM_MESSAGE' => self::getMessage('ACTION_CANCEL_CONFIRM', [
					'#REASON#' => $outgoingVariant['NAME'],
				]),
			];
		}

		return $menu;
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
			'status' => self::getMessage('ACTION_STATUS'),
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
				'label' => self::getMessage('ACTION_STATUS'),
				'items' => $variants,
			],
		];
	}

	protected function getOrderListGroupBoxActions(Market\Trading\Setup\Model $setup)
	{
		if (!$this->isSupportBoxes($setup)) { return []; }

		$variants = $this->getBoxesVariants();

		return [
			'boxes' => self::getMessage('ACTION_SEND_BOXES'),
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
				'label' => self::getMessage('ACTION_SEND_BOXES'),
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
			self::getMessage('ACTION_SEND_BOXES_COUNT_1'),
			self::getMessage('ACTION_SEND_BOXES_COUNT_2'),
			self::getMessage('ACTION_SEND_BOXES_COUNT_5'),
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
}