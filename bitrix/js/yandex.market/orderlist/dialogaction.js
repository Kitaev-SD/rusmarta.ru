(function(BX, window) {

	const Plugin = BX.namespace('YandexMarket.Plugin');
	const OrderList = BX.namespace('YandexMarket.OrderList');

	const constructor = OrderList.DialogAction = Plugin.Base.extend({

		defaults: {
			url: null,
			width: null,
			height: null,
			minWidth: 400,
			minHeight: 300,
			maxWidth: 600,
			maxHeight: 600,
			items: [],
			lang: {},
			langPrefix: 'YANDEX_MARKET_UI_TRADING_ORDER_LIST_DIALOG_ACTION_'
		},

		openGroupDialog: function(type, adminList) {
			const orderIds = this.getAdminListOrderIds(adminList);

			if (orderIds.length > 0) {
				this.openDialog(type, orderIds);
			} else {
				alert(this.getLang('REQUIRE_SELECT_ORDERS'));
			}
		},

		getAdminListOrderIds: function(adminList) {
			let result;

			if (this.isUiGrid(adminList)) {
				result = this.getAdminGridSelectedRows(adminList);
			} else {
				result = this.getAdminListSelectedCheckboxes(adminList);
			}

			return result;
		},

		getAdminGridSelectedRows: function(adminList) {
			return this.getUiGrid(adminList).getRows().getSelectedIds();
		},

		getAdminListSelectedCheckboxes: function(adminList) {
			const checkboxes = adminList.CHECKBOX;
			const result = [];
			let checkbox;
			let checkboxIndex;

			for (checkboxIndex = 0; checkboxIndex < checkboxes.length; checkboxIndex++) {
				checkbox = checkboxes[checkboxIndex];

				if (checkbox.checked && !checkbox.disabled) {
					result.push(checkbox.value);
				}
			}

			return result;
		},

		openDialog: function(type, orderIds) {
			throw new Error('not implemented');
		},

		buildUrl: function(item, orderIds) {
			let result = this.options.url;
			let orderId;
			let orderIndex;

			result +=
				(result.indexOf('?') === -1 ? '?' : '&')
				+ 'type=' + item.TYPE;

			if (orderIds == null) {
				// nothing
			} else if (Array.isArray(orderIds)) {
				for (orderIndex = 0; orderIndex < orderIds.length; orderIndex++) {
					orderId = orderIds[orderIndex];

					result += '&id[]=' + encodeURIComponent(orderId);
				}
			} else {
				result += '&id=' + encodeURIComponent(orderIds);
			}

			return result;
		},

		getItem: function(type) {
			const items = this.options.items;
			let result;
			let itemIndex;
			let item;

			for (itemIndex = 0; itemIndex < items.length; itemIndex++) {
				item = items[itemIndex];

				if (item.TYPE === type) {
					result = item;
					break;
				}
			}

			return result;
		},

		isUiGrid: function(adminList) {
			return (
				(BX.adminUiList != null && adminList instanceof BX.adminUiList)
				|| (BX.publicUiList != null && adminList instanceof BX.publicUiList)
			);
		},

		getUiGrid: function(adminList) {
			return BX.Main.gridManager.getById(adminList.gridId).instance;
		}

	});

})(BX, window);