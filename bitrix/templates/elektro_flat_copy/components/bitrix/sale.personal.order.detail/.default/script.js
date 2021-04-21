BX.namespace('BX.Sale.PersonalOrderComponent');

(function() {
	BX.Sale.PersonalOrderComponent.PersonalOrderDetail = {
		init : function(params) {
			var linkOrderInformation = BX('sod-user-info-link'),
				linkOrderInformationMoreI = BX.findChild(linkOrderInformation, {'class' : 'read-more-i'}),
				linkOrderInformationLessI = BX.findChild(linkOrderInformation, {'class' : 'read-less-i'}),
				clientInformation = BX('sod-order-info-block'),
				listShipmentWrapper = document.getElementsByClassName('sale-order-detail-payment-options-shipment-container'),
				listPaymentWrapper = document.getElementsByClassName('sale-order-detail-payment-options-methods'),
				shipmentTrackingId = document.getElementsByClassName('sale-order-detail-shipment-id');

			if (shipmentTrackingId[0]) {
				Array.prototype.forEach.call(shipmentTrackingId, function(blockId) {
					var clipboard = blockId.parentNode.getElementsByClassName('sale-order-detail-shipment-id-icon')[0];
					if (clipboard)
					{
						BX.clipboard.bindCopyClick(clipboard, {text : blockId.innerHTML});
					}
				});
			}


			BX.bind(linkOrderInformation, 'click', function() {
				if(clientInformation.style.display != 'block') {
					BX.style(clientInformation, 'display', 'block');
					BX.style(linkOrderInformationMoreI, 'display', 'none');
					BX.style(linkOrderInformationLessI, 'display', 'block');
				} else {
					BX.style(clientInformation, 'display', 'none');
					BX.style(linkOrderInformationMoreI, 'display', 'block');
					BX.style(linkOrderInformationLessI, 'display', 'none');
				}
			},this);

			Array.prototype.forEach.call(listShipmentWrapper, function(shipmentWrapper) {
				var detailShipmentBlock = shipmentWrapper.getElementsByClassName('sale-order-detail-payment-options-shipment-composition-map')[0],
					showInformation = shipmentWrapper.getElementsByClassName('sale-order-detail-show-link')[0],
					hideInformation = shipmentWrapper.getElementsByClassName('sale-order-detail-hide-link')[0];
				
				BX.bindDelegate(shipmentWrapper, 'click', { 'class': 'sale-order-detail-show-link' }, BX.proxy(function() {
					BX.style(showInformation, 'display', 'none');
					BX.style(hideInformation, 'display', 'inline-block');
					BX.style(detailShipmentBlock, 'display', 'block');
				}, this));
				
				BX.bindDelegate(shipmentWrapper, 'click', { 'class': 'sale-order-detail-hide-link' }, BX.proxy(function() {
					BX.style(showInformation, 'display', 'inline-block');
					BX.style(hideInformation, 'display', 'none');
					BX.style(detailShipmentBlock, 'display', 'none');
				}, this));
			});

			Array.prototype.forEach.call(listPaymentWrapper, function(paymentWrapper) {
				var rowPayment = paymentWrapper.getElementsByClassName('sale-order-detail-payment-options-methods-info')[0];

				BX.bindDelegate(paymentWrapper, 'click', { 'class': 'active-button' }, BX.proxy(function() {
					BX.toggleClass(paymentWrapper, 'sale-order-detail-active-event');
				}, this));

				BX.bindDelegate(rowPayment, 'click', { 'class': 'sale-order-detail-payment-options-methods-info-change-link' }, BX.proxy(function(event) {
					event.preventDefault();

					var btn = rowPayment.parentNode.getElementsByClassName('sale-order-detail-payment-options-methods-button-container')[0];
					var linkReturn = rowPayment.parentNode.getElementsByClassName('sale-order-detail-payment-inner-row-template')[0];
					BX.ajax(
						{
							method: 'POST',
							dataType: 'html',
							url: params.url,
							data: 
							{
								sessid: BX.bitrix_sessid(),
								orderData: params.paymentList[event.target.id]
							},
							onsuccess: BX.proxy(function(result) {
								rowPayment.innerHTML = result;
								if (btn) {
									btn.parentNode.removeChild(btn);
								}
								linkReturn.style.display = "block";
								BX.bind(linkReturn, 'click', function() {
									window.location.reload();
								},this);
							},this),
							onfailure: BX.proxy(function() {
								return this;
							}, this)
						}, this
					);

				}, this));
			});
		}
	};
})();
