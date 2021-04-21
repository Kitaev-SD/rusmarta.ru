//CITY_CHANGE//
BX.CityChange = function() {	
	var close;
	
	BX.CityChange.popup = BX.PopupWindowManager.create("cityChange", null, {
		autoHide: true,
		offsetLeft: 0,
		offsetTop: 0,			
		overlay: {
			opacity: 100
		},
		draggable: false,
		closeByEsc: false,
		closeIcon: { right : "-10px", top : "-10px"},			
		titleBar: {content: BX.create("span", {html: BX.message("GEOLOCATION_POPUP_WINDOW_TITLE")})},
		content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",
		events: {
			onAfterPopupShow: function()
			{
				if(!BX.findChild(BX("cityChange"), {className: "bx-sls"}, true, false)) {
					BX.ajax.post(
						BX.message("GEOLOCATION_COMPONENT_TEMPLATE") + "/popup.php",
						{							
							arParams: BX.message("GEOLOCATION_PARAMS")
						},
						BX.delegate(function(result)
						{
							this.setContent(result);
							var windowSize =  BX.GetWindowInnerSize(),
								windowScroll = BX.GetWindowScrollPos(),
								popupHeight = BX("cityChange").offsetHeight;
							BX("cityChange").style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
						},
						this)
					);
				}
			}
		}
	});
	
	BX.addClass(BX("cityChange"), "pop-up city-change");
	close = BX.findChildren(BX("cityChange"), {className: "popup-window-close-icon"}, true);
	if(!!close && 0 < close.length) {
		for(i = 0; i < close.length; i++) {					
			close[i].innerHTML = "<i class='fa fa-times'></i>";
		}
	}		
	
	BX.CityChange.popup.show();
};

//CITY_CONFIRM//
BX.CityConfirm = function(not_defined) {
	var not_defined = not_defined || false,
		close,
		strContent,
		buttons = [];
	
	BX.CityConfirm.popup = BX.PopupWindowManager.create("cityConfirm", null, {
		autoHide: true,
		offsetLeft: 0,
		offsetTop: 0,			
		overlay: {
			opacity: 100
		},
		draggable: false,
		closeByEsc: false,
		closeIcon: { right : "-10px", top : "-10px"},			
		titleBar: false
	});		
	
	BX.addClass(BX("cityConfirm"), "pop-up city-confirm");
	close = BX.findChildren(BX("cityConfirm"), {className: "popup-window-close-icon"}, true);
	if(!!close && 0 < close.length) {
		for(i = 0; i < close.length; i++) {					
			close[i].innerHTML = "<i class='fa fa-times'></i>";
		}
	}		
	
	strContent = "<div class='your-city'><div class='your-city__label'>" + BX.message("GEOLOCATION_YOUR_CITY") + "</div><div class='your-city__val'>" + BX.message("GEOLOCATION_POSITIONING") + "</div></div>";
	
	var CityConfirmButton = function(params) {
		CityConfirmButton.superclass.constructor.apply(this, arguments);		
		this.buttonNode = BX.create("button", {
			text: params.text,
			attrs: { 
				name: params.name,
				className: params.className
			},
			events : this.contextEvents
		});
	};
	BX.extend(CityConfirmButton, BX.PopupWindowButton);
	
	if(!not_defined) {
		buttons = [
			new CityConfirmButton({				
				text: BX.message("GEOLOCATION_YES"),
				name: "cityConfirmYes",
				className: "btn_buy popdef",
				events: {
					click: BX.delegate(BX.CityConfirm.popup.close, BX.CityConfirm.popup)
				}
			}),
			new CityConfirmButton({				
				text: BX.message("GEOLOCATION_CHANGE_CITY"),
				name: "cityConfirmChange",
				className: "btn_buy apuo",
				events: {
					click: BX.delegate(BX.CityChange, BX)
				}
			})
		];
	} else {
		buttons = [
			new CityConfirmButton({				
				text: BX.message("GEOLOCATION_CHANGE_CITY"),
				name: "cityConfirmChange",
				className: "btn_buy apuo",
				events: {
					click: BX.delegate(BX.CityChange, BX)
				}
			})
		];
	}	
	
	BX.CityConfirm.popup.setContent(strContent);
	BX.CityConfirm.popup.setButtons(buttons);		
	
	BX("geolocation").appendChild(BX("popup-window-overlay-cityConfirm"));
	BX("geolocation").appendChild(BX("cityConfirm"));
	
	BX.CityConfirm.popup.show();
};

//GEOLOCATION_DELIVERY//
BX.GeolocationDelivery = function(elementId) {
	BX("geolocationDelivery-" + elementId).appendChild(
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
		"/bitrix/components/altop/geolocation.delivery/ajax.php",
		{
			sessid: BX.bitrix_sessid(),
			action: "geolocationDelivery",
			arParams: {
				"ELEMENT_ID": elementId,
				"ELEMENT_COUNT": BX("geolocationDelivery-" + elementId).getAttribute("data-element-count"),
				"CART_PRODUCTS": BX("geolocationDelivery-" + elementId).getAttribute("data-cart-products"),
				"CACHE_TYPE": BX("geolocationDelivery-" + elementId).getAttribute("data-cache-type"),
				"CACHE_TIME": BX("geolocationDelivery-" + elementId).getAttribute("data-cache-time")
			}
		},
		function(result) {
			BX("geolocationDelivery-" + elementId).parentNode.innerHTML = result;
		}
	);
};

//GEOLOCATION//
BX.Geolocation = function(geolocation) {
	if(geolocation.city) {
		BX.ajax.post(
			BX.message("GEOLOCATION_COMPONENT_PATH") + "/ajax.php",
			{
				arParams: BX.message("GEOLOCATION_PARAMS"),
				sessid: BX.bitrix_sessid(),
				action: "searchLocation",
				country: geolocation.country,
				region: geolocation.region,
				city: geolocation.city
			},
			function(result) {
				var json = JSON.parse(result);
				$(".geolocation__value").html(json.city);				
				if(BX.message("GEOLOCATION_SHOW_CONFIRM") == "Y") {
					BX.CityConfirm();
					$(".your-city__val").html(json.city + "?");
				}
				if(!!json.contacts && json.contacts.length > 0)
					$(".telephone").html(json.contacts);
				geolocationDeliveryItems = BX.findChildren(document.body, {className: "geolocation-delivery"}, true);
				if(!!geolocationDeliveryItems && 0 < geolocationDeliveryItems.length) {
					for(i = 0; i < geolocationDeliveryItems.length; i++) {
						elementId = geolocationDeliveryItems[i].getAttribute("data-element-id");
						if(!!elementId)
							BX.GeolocationDelivery(elementId);
					}
				}
			}
		);		
	} else {
		$(".geolocation__value").html(BX.message("GEOLOCATION_NOT_DEFINED"));
		if(BX.message("GEOLOCATION_SHOW_CONFIRM") == "Y") {
			BX.CityConfirm(true);
			$(".your-city__val").html(BX.message("GEOLOCATION_NOT_DEFINED"));
		}
		geolocationDeliveryItems = BX.findChildren(document.body, {className: "geolocation-delivery"}, true);
		if(!!geolocationDeliveryItems && 0 < geolocationDeliveryItems.length) {
			for(i = 0; i < geolocationDeliveryItems.length; i++) {
				elementId = geolocationDeliveryItems[i].getAttribute("data-element-id");
				if(!!elementId)
					BX.GeolocationDelivery(elementId);
			}
		}
	}
};

//GEOLOCATION_YANDEX//
BX.GeolocationYandex = function() {
	BX.Geolocation(ymaps.geolocation);
};