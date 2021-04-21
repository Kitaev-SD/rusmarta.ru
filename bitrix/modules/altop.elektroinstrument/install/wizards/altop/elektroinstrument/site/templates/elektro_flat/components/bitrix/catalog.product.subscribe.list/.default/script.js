(function (window) {

	if(!!window.JCCatalogProductSubscribeList) {
		return;
	}
	
	window.JCCatalogProductSubscribeList = function (arParams) {		
		this.visual = {
			ID: ""
		};
		
		this.product = {			
			id: 0,
			listSubscribeId: {}
		};
		
		this.unSubscribeBtn = null;		

		this.obPopupWin = null;				

		this.ajaxUrl = "/bitrix/components/bitrix/catalog.product.subscribe.list/ajax.php";

		if("object" === typeof arParams) {
			this.notifyUser = Boolean(arParams.NOTIFY_USER);
			this.notifyPopupTitle = arParams.NOTIFY_POPUP_TITLE;
			this.notifySuccess = Boolean(arParams.NOTIFY_SUCCESS);
			this.notifyMessage = arParams.NOTIFY_MESSAGE;
			if(this.notifyUser) {
				BX.ready(BX.delegate(this.showPopupNotifyingUser,this));
				return;
			}

			this.visual = arParams.VISUAL;

			if(!!this.visual.UNSUBSCRIBE_BTN_ID) {
				this.unSubscribeBtn = BX(this.visual.UNSUBSCRIBE_BTN_ID);
				if(!!this.unSubscribeBtn) {
					BX.bind(this.unSubscribeBtn, "click", BX.delegate(this.unSubscribe, this));
				}
				this.product.listSubscribeId = arParams.PRODUCT.LIST_SUBSCRIBE_ID;
			}

			this.product.id = arParams.PRODUCT.ID;
		}
	};
	
	window.JCCatalogProductSubscribeList.prototype.showPopupNotifyingUser = function() {
		if(!!this.obPopupWin)
			return;

		this.obPopupWin = BX.PopupWindowManager.create("bx-catalog-subscribe-notifying-user", null, {
			autoHide: false,
			offsetLeft: 0,
			offsetTop: 0,
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: true,
			className: "pop-up forms short",
			closeIcon: { right : "-10px", top : "-10px"},	
			titleBar: {content: BX.create("span", {html: this.notifyPopupTitle})}
		});
		
		var content = BX.create("div", {
			props: {
				className: "popup-window-message"
			},
			children: [
                BX.create("span", {
                    props: {
                        className: "alertMsg " + (this.notifySuccess ? "good" : "bad")
                    },
					children: [
						BX.create("i", {
							props: {
								className: "fa fa-" + (this.notifySuccess ? "check" : "exclamation-triangle"),
								"aria-hidden": "true"
							}
						}),
						BX.create("span", {
							props: {
								className: "text"
							},
							text: this.notifyMessage
						})
					]
				})
			]
		});
		this.obPopupWin.setContent(content);
		
		var close = BX.findChild(BX("bx-catalog-subscribe-notifying-user"), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";

		this.obPopupWin.show();
	};
	
	window.JCCatalogProductSubscribeList.prototype.unSubscribe = function() {
		var itemId = this.product.id;
		
		if(!itemId || !this.product.listSubscribeId.hasOwnProperty(itemId))
			return;

		BX.ajax({
			method: "POST",
			dataType: "json",
			url: this.ajaxUrl,
			data: {
				sessid: BX.bitrix_sessid(),
				deleteSubscribe: "Y",
				itemId: itemId,
				listSubscribeId: this.product.listSubscribeId[itemId]
			},
			onsuccess: BX.delegate(function(result) {
				if(result.success) {
					this.showWindowWithAnswer({status: "success"});
					location.reload();
				} else {
					this.showWindowWithAnswer({status: "error", message: result.message});
				}
			}, this)
		});
	};

	window.JCCatalogProductSubscribeList.prototype.showWindowWithAnswer = function(answer) {
		answer = answer || {};
		if(!answer.message) {
			if(answer.status == "success") {
				answer.message = BX.message("CPSL_STATUS_SUCCESS");
			} else {
				answer.message = BX.message("CPSL_STATUS_ERROR");
			}
		}
		
		var messageBox = BX.create("div", {
			props: {
				className: "popup-window-message"
			},
			children: [
                BX.create("span", {
                    props: {
                        className: "alertMsg " + (answer.status == "success" ? "good" : "bad")
                    },
					children: [
						BX.create("i", {
							props: {
								className: "fa fa-" + (answer.status == "success" ? "check" : "exclamation-triangle"),
								"aria-hidden": "true"
							}
						}),
						BX.create("span", {
							props: {
								className: "text"
							},
							text: answer.message
						})
					]
				})
			]
		});
		
		var currentPopup = BX.PopupWindowManager.getCurrentPopup();
		if(currentPopup) {
			currentPopup.destroy();
		}
		
		var idTimeout = setTimeout(function () {
			var w = BX.PopupWindowManager.getCurrentPopup();
			if (!w || w.uniquePopupId != "bx-catalog-subscribe-status-action") {
				return;
			}
			w.close();
			w.destroy();
		}, 3500);
		
		var popupConfirm = BX.PopupWindowManager.create("bx-catalog-subscribe-status-action", null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			className: "pop-up forms short",
			closeIcon: { right : "-10px", top : "-10px"},
			onPopupClose: function() {
				this.destroy();
				clearTimeout(idTimeout);
			},
			titleBar: {content: BX.create("span", {html: BX.message("CPSL_TITLE_UNSUBSCRIBE")})},
			content: messageBox
		});
		
		var close = BX.findChild(BX("bx-catalog-subscribe-status-action"), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";
		
		popupConfirm.show();
		
		BX("bx-catalog-subscribe-status-action").onmouseover = function (e) {
			clearTimeout(idTimeout);
		};
		BX("bx-catalog-subscribe-status-action").onmouseout = function (e) {
			idTimeout = setTimeout(function () {
				var w = BX.PopupWindowManager.getCurrentPopup();
				if (!w || w.uniquePopupId != "bx-catalog-subscribe-status-action") {
					return;
				}
				w.close();
				w.destroy();
			}, 3500);
		};
	};
})(window);