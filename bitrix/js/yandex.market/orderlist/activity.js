(function(BX, window) {

	const Ui = BX.namespace('YandexMarket.Ui');
	const OrderList = BX.namespace('YandexMarket.OrderList');

	const constructor = OrderList.Activity = OrderList.DialogAction.extend({

		openDialog: function(type, orderIds) {
			const item = this.getItem(type);
			const url = this.buildUrl(item, orderIds);
			const form = this.createForm(url, item);

			form.activate();
		},

		createForm: function(url, item) {
			return new Ui.ModalForm(this.$el, {
				title: item.DIALOG_TITLE || item.TITLE,
				saveTitle: this.getLang('ACTIVITY_SUBMIT'),
				url: url,
			});
		},

		executeCommand: function(type, orderIds, adminList) {
			const item = this.getItem(type);
			const url = this.buildUrl(item, orderIds);

			this.showLoading(adminList);

			this.sendCommand(url)
				.then((response) => this.parseCommandResponse(response))
				.then(() => this.reloadGrid(adminList))
				.catch((error) => {
					this.hideLoading(adminList);
					this.showError(adminList, error);
				});
		},

		sendCommand: function(url) {
			return new Promise(function(resolve, reject) {
				BX.ajax({
					url: url,
					method: 'POST',
					data: {
						command: 'Y',
						sessid: BX.bitrix_sessid(),
					},
					dataType: 'json',
					onsuccess: resolve,
					onfailure: reject,
				});
			});
		},

		parseCommandResponse: function(response) {
			if (response == null || typeof response !== 'object') {
				throw new Error('ajax response missing');
			}

			if (response.status == null) {
				throw new Error('ajax response status missing');
			}

			if (response.status === 'error') {
				throw new Error(response.message);
			}
		},

		showLoading: function(grid) {
			this.isUiGrid(grid)
				? this.getUiGrid(grid).tableFade()
				: BX.showWait(grid.gridId);
		},

		hideLoading: function(grid) {
			this.isUiGrid(grid)
				? this.getUiGrid(grid).tableUnfade()
				: BX.closeWait(grid.gridId);
		},

		showError: function(grid, error) {
			const message = error instanceof Error ? error.message : error;

			if (this.isUiGrid(grid)) {
				this.showUiGridError(grid, message);
			} else {
				alert(message);
			}
		},

		showUiGridError: function(grid, message) {
			const uiGrid = this.getUiGrid(grid);

			uiGrid.arParams.MESSAGES = [
				{ TYPE: 'ERROR', TEXT: message },
			];

			BX.onCustomEvent(window, 'BX.Main.grid:paramsUpdated', []);
		},

		reloadGrid: function(grid) {
			if (this.isUiGrid(grid)) {
				this.getUiGrid(grid).reloadTable();
			} else {
				grid.GetAdminList(window.location.href);
			}
		}

	}, {
		dataName: 'orderListActivity',
		pluginName: 'YandexMarket.OrderList.Activity',
	});

})(BX, window);