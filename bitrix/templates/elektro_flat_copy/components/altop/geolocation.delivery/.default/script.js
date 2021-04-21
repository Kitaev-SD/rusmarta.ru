//SHOW_GEOLOCATION_DELIVERY//
BX.ShowGeolocationDelivery = function(arParams) {	
	BX("geolocationDelivery-" + arParams.PARAMS.ELEMENT_ID).appendChild(
		BX.create("div", {
			attrs: {
				className: "geolocation-delivery__wait"
			},
			children: [
				BX.create("i", {
					attrs: {
						className: "fa fa-spinner fa-pulse"
					}
				})
			]
		})
	);	
	BX.ajax.post(
		arParams.COMPONENT_PATH + "/ajax.php",
		{
			sessid: BX.bitrix_sessid(),
			action: "geolocationDelivery",
			arParams: arParams.PARAMS
		},
		BX.delegate(function(result)
		{
			BX("geolocationDelivery-" + arParams.PARAMS.ELEMENT_ID).parentNode.innerHTML = result;
		},
		this)
	);
};

//GEOLOCATION_DELIVERY_DETAIL//
BX.GeolocationDeliveryDetail = function(arParams) {	
	var close;	
	
	BX.GeolocationDeliveryDetail.popup = BX.PopupWindowManager.create("geolocationDeliveryDetail-" + arParams.PARAMS.ELEMENT_ID, null, {
		autoHide: true,
		offsetLeft: 0,
		offsetTop: 0,			
		overlay: {
			opacity: 100
		},
		draggable: false,
		closeByEsc: false,
		closeIcon: { right : "-10px", top : "-10px"},			
		titleBar: {content: BX.create("span", {html: arParams.TITLE + " " + BX("geolocationDeliveryLink-" + arParams.PARAMS.ELEMENT_ID).innerHTML})},
		content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",
		events: {
			onAfterPopupShow: function()
			{
				if(!BX("geolocationDeliveryDetailDeliveryList-" + arParams.PARAMS.ELEMENT_ID)) {
					BX.ajax.post(
						arParams.COMPONENT_TEMPLATE + "/popup.php",
						{							
							sessid: BX.bitrix_sessid(),
							action: "geolocationDelivery",
							arParams: arParams.PARAMS
						},
						BX.delegate(function(result)
						{
							this.setContent(result);
							var windowSize =  BX.GetWindowInnerSize(),
								windowScroll = BX.GetWindowScrollPos(),
								popupHeight = BX("geolocationDeliveryDetail-" + arParams.PARAMS.ELEMENT_ID).offsetHeight;
							BX("geolocationDeliveryDetail-" + arParams.PARAMS.ELEMENT_ID).style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
						},
						this)
					);
				}
			}
		}
	});
	
	BX.addClass(BX("geolocationDeliveryDetail-" + arParams.PARAMS.ELEMENT_ID), "pop-up geolocation-delivery-detail");
	close = BX.findChildren(BX("geolocationDeliveryDetail-" + arParams.PARAMS.ELEMENT_ID), {className: "popup-window-close-icon"}, true);
	if(!!close && 0 < close.length) {
		for(i = 0; i < close.length; i++) {					
			close[i].innerHTML = "<i class='fa fa-times'></i>";
		}
	}		
	
	BX.GeolocationDeliveryDetail.popup.show();
};