(function (window) {

	if(!!window.JCCatalogBigdataProducts) {
		return;
	}	

	var BasketButton = function(params) {
		BasketButton.superclass.constructor.apply(this, arguments);		
		this.buttonNode = BX.create("button", {
			text: params.text,
			attrs: { 
				name: params.name,
				className: params.className
			},
			events : this.contextEvents
		});
	};
	BX.extend(BasketButton, BX.PopupWindowButton);

	window.JCCatalogBigdataProducts = function (arParams) {
		this.productType = 0;

		this.checkQuantity = false;
		this.maxQuantity = 0;
		this.stepQuantity = 1;
		this.isDblQuantity = false;

		this.precision = 6;
		this.precisionFactor = Math.pow(10, this.precision);

		this.visual = {
			ID: ""
		};
		
		this.product = {			
			id: 0,
			name: "",
			pict: {},
			checkQuantity: false,
			maxQuantity: 0,
			stepQuantity: 1,			
			isDblQuantity: false
		};
			
		this.offer = {
			id: 0,
			iblockId: 0
		};

		this.obPopupBtn = null;
		this.obPropsBtn = null;
		this.obBtnBuy = null;
		
		this.obPopupWin = null;
		this.basketParams = {};
			
		this.errorCode = 0;

		if("object" === typeof arParams) {
			this.productType = parseInt(arParams.PRODUCT_TYPE, 10);
			this.visual = arParams.VISUAL;
			
			if(!!arParams.PRODUCT && "object" === typeof(arParams.PRODUCT)) {
				this.product.id = arParams.PRODUCT.ID;
				this.product.name = arParams.PRODUCT.NAME;
				this.product.pict = arParams.PRODUCT.PICT;

				this.checkQuantity = arParams.PRODUCT.CHECK_QUANTITY;
				this.isDblQuantity = arParams.PRODUCT.QUANTITY_FLOAT;
				if(this.checkQuantity)
					this.maxQuantity = (this.isDblQuantity ? parseFloat(arParams.PRODUCT.MAX_QUANTITY) : parseInt(arParams.PRODUCT.MAX_QUANTITY, 10));
				this.stepQuantity = (this.isDblQuantity ? parseFloat(arParams.PRODUCT.STEP_QUANTITY) : parseInt(arParams.PRODUCT.STEP_QUANTITY, 10));
				if(this.isDblQuantity)
					this.stepQuantity = Math.round(this.stepQuantity * this.precisionFactor) / this.precisionFactor;
				
				if(!!arParams.OFFER) {
					this.offer.id = arParams.OFFER.ID;
					this.offer.iblockId = arParams.OFFER.IBLOCK_ID;
				}
			} else {
				this.errorCode = -1;
			}
		}
		if(0 === this.errorCode) {
			BX.ready(BX.delegate(this.Init,this));
		}
	};

	window.JCCatalogBigdataProducts.prototype.Init = function() {
		this.obQuantityUp = BX("quantity_plus_" + this.visual.ID);
		if(!!this.obQuantityUp)
			BX.bind(this.obQuantityUp, "click", BX.delegate(this.QuantityUp, this));
				
		this.obQuantityDown = BX("quantity_minus_" + this.visual.ID);
		if(!!this.obQuantityDown)
			BX.bind(this.obQuantityDown, "click", BX.delegate(this.QuantityDown, this));

		this.obQuantity = BX("quantity_" + this.visual.ID);
		if(!!this.obQuantity)
			BX.bind(this.obQuantity, "change", BX.delegate(this.QuantityChange, this));

		if(!!this.visual.POPUP_BTN_ID) {
			this.obPopupBtn = BX(this.visual.POPUP_BTN_ID);
			BX.bind(this.obPopupBtn, "click", BX.delegate(this.OpenFormPopup, this));
		}
		
		if(!!this.visual.PROPS_BTN_ID) {
			this.obPropsBtn = BX(this.visual.PROPS_BTN_ID);
			BX.bind(this.obPropsBtn, "click", BX.delegate(this.OpenPropsPopup, this));
		}
		
		if(!!this.visual.BTN_BUY_ID) {
			this.obBtnBuy = BX(this.visual.BTN_BUY_ID);
			BX.bind(this.obBtnBuy, "click", BX.delegate(this.Add2Basket, this));
		}
	};

	window.JCCatalogBigdataProducts.prototype.QuantityUp = function() {
		var curValue = 0,
			boolSet = true;
		
		curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
		if(!isNaN(curValue)) {
			curValue += this.stepQuantity;
			if(this.checkQuantity) {
				if(curValue > this.maxQuantity) {
					boolSet = false;
				}
			}
			if(boolSet) {
				if(this.isDblQuantity) {
					curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
				}
				this.obQuantity.value = curValue;
			}
		}
	};

	window.JCCatalogBigdataProducts.prototype.QuantityDown = function() {
		var curValue = 0,
			boolSet = true;
		
		curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
		if(!isNaN(curValue)) {
			curValue -= this.stepQuantity;
			if(curValue < this.stepQuantity) {
				boolSet = false;
			}
			if(boolSet) {
				if(this.isDblQuantity) {
					curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
				}
				this.obQuantity.value = curValue;
			}
		}
	};

	window.JCCatalogBigdataProducts.prototype.QuantityChange = function() {
		var curValue = 0,
			intCount,
			count;
		
		curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
		if(!isNaN(curValue)) {
			if(this.checkQuantity) {
				if(curValue > this.maxQuantity) {
					curValue = this.maxQuantity;
				}
			}
			if(curValue < this.stepQuantity) {
				curValue = this.stepQuantity;
			} else {
				count = Math.round((curValue * this.precisionFactor) / this.stepQuantity) / this.precisionFactor;
				intCount = parseInt(count, 10);
				if(isNaN(intCount)) {
					intCount = 1;
					count = 1.1;
				}
				if(count > intCount) {
					curValue = (intCount <= 1 ? this.stepQuantity : intCount * this.stepQuantity);
					curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
				}
			}
			this.obQuantity.value = curValue;
		} else {
			this.obQuantity.value = this.stepQuantity;
		}
	};

	window.JCCatalogBigdataProducts.prototype.OpenPropsPopup = function() {
		var visualId = this.visual.ID,
			elementId = this.product.id,
			offerId = this.offer.id,
			offersIblockId = this.offer.iblockId;
		BX.PropsSet =
		{			
			popup: null
		};
		BX.PropsSet.popup = BX.PopupWindowManager.create(visualId, null, {
			autoHide: BX.message("BIGDATA_OFFERS_VIEW") == "LIST" ? false : true,
			offsetLeft: 0,
			offsetTop: 0,
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			className: "pop-up more_options" + (offerId > 0 && BX.message("BIGDATA_OFFERS_VIEW") == "LIST" ? " offers-list" : ""),
			closeIcon: { right : "-10px", top : "-10px"},
			titleBar: {content: BX.create("span", {html: BX.message("BIGDATA_POPUP_WINDOW_MORE_OPTIONS")})},
			content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",
			events: {
				onAfterPopupShow: function()
				{
					if(!BX(visualId + "_info")) {
						BX.ajax.post(
							BX.message("BIGDATA_COMPONENT_TEMPLATE") + "/popup.php",
							{
								sessid: BX.bitrix_sessid(),
								action: "props",
								arParams: BX.message("BIGDATA_COMPONENT_PARAMS"),
								ELEMENT_ID: elementId,
								STR_MAIN_ID: visualId,
								OFFERS_IBLOCK_ID: offersIblockId > 0 ? offersIblockId : ""
							},
							BX.delegate(function(result)
							{
								this.setContent(result);
								var windowSize =  BX.GetWindowInnerSize(),
								windowScroll = BX.GetWindowScrollPos(),
								popupHeight = BX(visualId).offsetHeight;
								BX(visualId).style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
							},
							this)
						);
					} else {
						if(offerId > 0) {
							var parentQntInput = BX("quantity_" + visualId),
								qntInput = BX("quantity_" + visualId + "_" + offerId);
							if(!!parentQntInput && !!qntInput)
								qntInput.value = parentQntInput.value;
						}
						var parentQntSelectInput = BX("quantity_" + visualId),
							qntSelectInput = BX("quantity_select_" + visualId);
						if(!!parentQntSelectInput && !!qntSelectInput)
							qntSelectInput.value = parentQntSelectInput.value;
					}
				}
			}
		});

		var close = BX.findChild(BX(visualId), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";

		BX.PropsSet.popup.show();
	};

	window.JCCatalogBigdataProducts.prototype.OpenFormPopup = function() {
		var target = BX.proxy_context,
			action = target.getAttribute("data-action"),
			visualId = action + "_" + this.visual.ID,
			elementId = this.product.id,
			elementName = this.product.name;
		BX.PopupForm =
		{						
			arParams: {}
		};
		BX.PopupForm.popup = BX.PopupWindowManager.create(visualId, null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,			
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			className: "pop-up forms full",
			closeIcon: { right : "-10px", top : "-10px"},			
			titleBar: true,
			content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",			
			events: {
				onAfterPopupShow: function()
				{
					if(!BX(visualId + "_form")) {
						BX.ajax.post(
							BX.message("BIGDATA_COMPONENT_TEMPLATE") + "/popup.php",
							{							
								sessid: BX.bitrix_sessid(),
								action: action,
								arParams: {
									ELEMENT_ID: elementId,
									ELEMENT_AREA_ID: visualId,
									ELEMENT_NAME: elementName
								}
							},
							BX.delegate(function(result)
							{
								this.setContent(result);
								var windowSize =  BX.GetWindowInnerSize(),
								windowScroll = BX.GetWindowScrollPos(),
								popupHeight = BX(visualId).offsetHeight;
								BX(visualId).style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
							},
							this)
						);
					}					
				}
			}			
		});
		
		var close = BX.findChild(BX(visualId), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";

		BX.PopupForm.popup.show();		
	};

	window.JCCatalogBigdataProducts.prototype.Add2Basket = function() {
		var target = BX.proxy_context,
			form = BX.findParent(target, {"tag" : "form"}),
			formInputs = BX.findChildren(form, {"tag" : "input"}, true);
		
		if(!!formInputs && 0 < formInputs.length) {
			for(i = 0; i < formInputs.length; i++) {
				this.basketParams[formInputs[i].getAttribute("name")] = formInputs[i].value;
			}
		}
		
		BX.ajax.post(
			form.getAttribute("action"),			
			this.basketParams,			
			BX.delegate(function(result) {
				if(location.pathname != BX.message("BIGDATA_SITE_DIR") + "personal/cart/") {
					BX.ajax.post(
						BX.message("BIGDATA_SITE_DIR") + "ajax/basket_line.php",
						"",
						BX.delegate(function(data) {
							refreshCartLine(data);
						}, this)
					);
					BX.ajax.post(
						BX.message("BIGDATA_SITE_DIR") + "ajax/delay_line.php",
						"",
						BX.delegate(function(data) {
							var delayLine = BX.findChild(document.body, {className: "delay_line"}, true, false);
							if(!!delayLine)
								delayLine.innerHTML = data;
						}, this)
					);
				}
				BX.adjust(target, {
					props: {disabled: true},
					html: "<i class='fa fa-check'></i><span>" + BX.message("BIGDATA_ADDITEMINCART_ADDED") + "</span>"
				});
				if(location.pathname != BX.message("BIGDATA_SITE_DIR") + "personal/cart/") {
					this.BasketResult();
				} else {
					this.BasketRedirect();
				}
			}, this)			
		);		
	};

	window.JCCatalogBigdataProducts.prototype.BasketResult = function() {
		var close,
			strContent,
			strPictSrc,
			strPictWidth,
			strPictHeight,
			buttons = [];

		if(!!this.obPopupWin) {
			this.obPopupWin.close();
		}
		
		this.obPopupWin = BX.PopupWindowManager.create("addItemInCart", null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			className: "pop-up modal",
			closeIcon: {top: "-10px", right: "-10px"},
			titleBar: {content: BX.create("span", {html: BX.message("BIGDATA_POPUP_WINDOW_TITLE")})}			
		});
		
		close = BX.findChild(BX("addItemInCart"), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";

		strPictSrc = this.product.pict.SRC;
		strPictWidth = this.product.pict.WIDTH;
		strPictHeight = this.product.pict.HEIGHT;
		
		strContent = "<div class='cont'><div class='item_image_cont'><div class='item_image_full'><img src='" + strPictSrc + "' width='" + strPictWidth + "' height='" + strPictHeight + "' alt='"+ this.product.name +"' /></div></div><div class='item_title'>" + this.product.name + "</div></div>";

		buttons = [			
			new BasketButton({				
				text: BX.message("BIGDATA_POPUP_WINDOW_BTN_CLOSE"),
				name: "close",
				className: "btn_buy ppp close",
				events: {
					click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
				}
			}),
			new BasketButton({				
				text: BX.message("BIGDATA_POPUP_WINDOW_BTN_ORDER"),
				name: "order",
				className: "btn_buy popdef order",
				events: {
					click: BX.delegate(this.BasketRedirect, this)
				}
			})
		];
		
		this.obPopupWin.setContent(strContent);
		this.obPopupWin.setButtons(buttons);
		this.obPopupWin.show();	
	};

	window.JCCatalogBigdataProducts.prototype.BasketRedirect = function() {
		location.href = BX.message("BIGDATA_SITE_DIR") + "personal/cart/";
	};

	window.JCCatalogBigdataProducts.prototype.RememberRecommendation = function(obj, productId) {
		var rcmContainer = BX.findParent(obj, {className: "bigdata_recommended_products_items"});
		var rcmId = BX.findChild(rcmContainer, {attr: {name: "bigdata_recommendation_id"}}, true).value;

		this.RememberProductRecommendation(rcmId, productId);
	};

	window.JCCatalogBigdataProducts.prototype.RememberProductRecommendation = function(recommendationId, productId) {
		//save to RCM_PRODUCT_LOG
		var plCookieName = BX.cookie_prefix+"_RCM_PRODUCT_LOG";
		var plCookie = getCookie(plCookieName);
		var itemFound = false;

		var cItems = [],
			cItem;

		if(plCookie) {
			cItems = plCookie.split(".");
		}

		var i = cItems.length;

		while(i--) {
			cItem = cItems[i].split("-");

			if(cItem[0] == productId) {
				//it's already in recommendations, update the date
				cItem = cItems[i].split("-");

				//update rcmId and date
				cItem[1] = rcmId;
				cItem[2] = BX.current_server_time;

				cItems[i] = cItem.join("-");
				itemFound = true;
			} else {
				if((BX.current_server_time - cItem[2]) > 3600*24*30) {
					cItems.splice(i, 1);
				}
			}
		}

		if(!itemFound) {
			//add recommendation
			cItems.push([productId, rcmId, BX.current_server_time].join("-"));
		}

		//serialize
		var plNewCookie = cItems.join(".");

		var cookieDate = new Date(new Date().getTime() + 1000*3600*24*365*10);
		document.cookie=plCookieName+"="+plNewCookie+"; path=/; expires="+cookieDate.toUTCString()+"; domain="+BX.cookie_domain;
	};
})(window);

function getCookie(name) {
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

function bx_rcm_recommendation_event_attaching(rcm_items_cont) {
	return null;
}

function bx_rcm_adaptive_recommendation_event_attaching(items, uniqId) {
	//onclick handler
	var callback = function(e) {
		var link = BX(this), j;
		for(j in items) {
			if(items[j].productUrl == link.getAttribute("href")) {
				window.JCCatalogBigdataProducts.prototype.RememberProductRecommendation(
					items[j].recommendationId, items[j].productId
				);
				break;
			}
		}
	};

	//check if a container was defined is the template
	var itemsContainer = BX(uniqId);

	if(!itemsContainer) {
		// then get all the links
		itemsContainer = document.body;
	}

	var links = BX.findChildren(itemsContainer, {tag:"a"}, true);

	//bind
	if(links) {
		var i;
		for(i in links) {
			BX.bind(links[i], "click", callback);
		}
	}
}

function bx_rcm_get_from_cloud(injectId, rcmParameters, localAjaxData) {
	var url = "https://analytics.bitrix.info/crecoms/v1_0/recoms.php";
	var data = BX.ajax.prepareData(rcmParameters);

	if(data) {
		url += (url.indexOf("?") !== -1 ? "&" : "?") + data;
	}

	var onready = function(response) {
		if(!response.items) {
			response.items = [];
		}
		BX.ajax({
			url: "/bitrix/components/bitrix/catalog.bigdata.products/ajax.php?"+BX.ajax.prepareData({"AJAX_ITEMS": response.items, "RID": response.id}),
			method: "POST",
			data: localAjaxData,
			dataType: "html",
			processData: false,
			start: true,
			onsuccess: function(html) {
				var ob = BX.processHTML(html);

				// inject
				BX(injectId).innerHTML = ob.HTML;
				BX.ajax.processScripts(ob.SCRIPT);
			}
		});
	};

	BX.ajax({
		"method": "GET",
		"dataType": "json",
		"url": url,
		"timeout": 3,
		"onsuccess": onready,
		"onfailure": onready
	});
}