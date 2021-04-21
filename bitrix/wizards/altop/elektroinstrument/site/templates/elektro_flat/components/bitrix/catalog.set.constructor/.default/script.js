BX.namespace("BX.Catalog.SetConstructor");

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

BX.Catalog.SetConstructor = (function() {
	var SetConstructor = function(params) {			
		this.numSetItems = params.numSetItems || 0;
		this.jsId = params.jsId || "";
		this.ajaxPath = params.ajaxPath || "";
		this.popupPath = params.popupPath || "";
		this.currency = params.currency || "";
		this.siteDir = params.siteDir || "";
		this.lid = params.lid || "";		
		this.basketUrl = params.basketUrl || "";
		this.setIds = params.setIds || null;
		this.offersCartProps = params.offersCartProps || null;
		this.itemsRatio = params.itemsRatio || null;
		this.noFotoSrc = params.noFotoSrc || "";
		this.messages = params.messages;
		
		this.mainElementPict = params.mainElementPict || null;
		this.mainElementPrice = params.mainElementPrice || 0;
		this.mainElementRefPrice = params.mainElementRefPrice || 0;
		this.mainElementOldPrice = params.mainElementOldPrice || 0;
		this.mainElementDiffPrice = params.mainElementDiffPrice || 0;
		this.mainElementBasketQuantity = params.mainElementBasketQuantity || 1;

		this.parentCont = BX(params.parentContId) || null;		
		this.setItemsCont = this.parentCont.querySelector("[data-role='added-items']");
		this.otherItemsCont = this.parentCont.querySelector("[data-role='other-items']");

		this.setPriceCont = this.parentCont.querySelector("[data-role='set-price']");		
		this.setRefPriceCont = this.parentCont.querySelector("[data-role='set-ref-price']");
		this.setOldPriceCont = this.parentCont.querySelector("[data-role='set-old-price']");
		this.setDiffPriceCont = this.parentCont.querySelector("[data-role='set-diff-price']");		

		this.emptySetMessage = this.parentCont.querySelector("[data-set-message='empty-set']");
		
		this.buyButton = this.parentCont.querySelector("[data-role='set-buy-btn']");

		BX.bindDelegate(this.setItemsCont, "click", { attribute: "data-role" }, BX.proxy(this.deleteFromSet, this));
		BX.bindDelegate(this.otherItemsCont, "click", { attribute: "data-role" }, BX.proxy(this.addToSet, this));
		BX.bindDelegate(this.otherItemsCont, "click", { attribute: "data-action" }, BX.proxy(this.OpenFormPopup, this));	
		BX.bindDelegate(this.otherItemsCont, "click", { className: "other-items-section-slider-arrow" }, BX.proxy(this.scrollItems, this));		
		BX.bind(this.buyButton, "click", BX.proxy(this.addToBasket, this));		
		
		BX.bind(window, "resize", BX.proxy(this.widthSlider, this));
		this.widthSlider();
	};
	
	SetConstructor.prototype.deleteFromSet = function() {
		var target = BX.proxy_context,
			item,
			itemId,
			itemIblockId,
			itemSectId,
			itemUrl,
			itemImg,
			itemName,
			itemArticle,
			itemPrice,
			itemFormatPrice,
			itemRefPrice,
			itemFormatRefPrice,
			itemOldPrice,
			itemDiffPrice,
			itemCurrency,
			itemMeasure,
			i,
			l,
			keyDel,
			newSliderNode;

		if(!!target && target.hasAttribute("data-role") && target.getAttribute("data-role") == "set-delete-btn") {
			item = target.parentNode.parentNode.parentNode;

			itemId = item.getAttribute("data-id");
			itemIblockId = item.getAttribute("data-iblock-id");
			itemSectId = item.getAttribute("data-section-id");			
			itemUrl = item.getAttribute("data-url");
			itemImg = item.getAttribute("data-img");
			itemName = item.getAttribute("data-name");
			itemArticle = item.getAttribute("data-article");
			itemPrice = item.getAttribute("data-price");
			itemFormatPrice = item.getAttribute("data-format-price");			
			itemRefPrice = item.getAttribute("data-reference-price");
			itemFormatRefPrice = item.getAttribute("data-format-reference-price");
			itemOldPrice = item.getAttribute("data-old-price");
			itemDiffPrice = item.getAttribute("data-diff-price");
			itemCurrency = item.getAttribute("data-currency");
			itemMeasure = item.getAttribute("data-measure");			

			newSliderNode = BX.create("div", {
				attrs: {
					className: "catalog-item-card other-item",
					"data-id": itemId,
					"data-iblock-id": itemIblockId,
					"data-section-id": itemSectId,
					"data-url": itemUrl,
					"data-img": itemImg ? itemImg : "",					
					"data-name": itemName,
					"data-article": itemArticle ? itemArticle : "",
					"data-price": itemPrice,
					"data-format-price": itemFormatPrice,
					"data-reference-price": itemRefPrice,
					"data-format-reference-price": itemFormatRefPrice,
					"data-old-price": itemOldPrice,
					"data-diff-price": itemDiffPrice,
					"data-currency": itemCurrency,
					"data-measure": itemMeasure
				},
				children: [
					BX.create("div", {
						attrs: {
							className: "catalog-item-info"
						},
						children: [
							BX.create("div", {
								attrs: {
									className: "item-image-cont"
								},
								children: [
									BX.create("div", {
										attrs: {
											className: "item-image"
										},
										children: [
											BX.create("a", {
												attrs: {													
													href: itemUrl
												},
												children: [
													BX.create("img", {
														attrs: {													
															className: "item_img",
															width: itemImg ? 160 : 150,
															height: itemImg ? 160 : 150,
															alt: itemName,
															src: itemImg ? itemImg : this.noFotoSrc,
														}
													})
												]
											})
										]
									})
								]
							}),
							BX.create("div", {
								attrs: {
									className: "item-all-title"
								},
								children: [
									BX.create("a", {
										attrs: {
											className: "item-title",
											title: itemName,
											href: itemUrl
										},
										html: itemName
									})
								]
							}),							
							BX.create("div", {
								attrs: {
									className: "item-article"
								},
								style: {
									display: itemArticle ? "" : "none"
								},
								html: itemArticle ? this.messages.ARTICLE + itemArticle : ""
							}),							
							BX.create("div", {
								attrs: {
									className: "item-price-cont" + (this.setOldPriceCont ? "" : " one") + (this.setRefPriceCont ? " reference" : "")
								},
								children: [
									BX.create("div", {
										attrs: {
											className: "item-price"
										},
										children: [											
											BX.create("span", {
												attrs: {
													className: "catalog-item-price-old"
												},
												style: {
													display: this.setOldPriceCont ? (Math.floor(itemDiffPrice * 100) > 0 ? "" : "none") : "none"
												},
												html: this.setOldPriceCont ? (Math.floor(itemDiffPrice * 100) > 0 ? BX.Currency.currencyFormat(itemOldPrice, this.currency, true) : "") : ""
											}),											
											BX.create("span", {
												attrs: {
													className: "catalog-item-price"
												},
												html: itemFormatPrice + "<span class='unit'>" + itemCurrency + " <span>" + itemMeasure + "</span></span>"
											}),											
											BX.create("span", {
												attrs: {
													className: "catalog-item-price-reference"
												},
												style: {
													display: this.setRefPriceCont ? "" : "none"
												},
												html: this.setRefPriceCont ? BX.Currency.currencyFormat(itemRefPrice, this.currency, true) : ""
											})											
										]
									})
								]
							}),
							BX.create("div", {
								attrs: {
									className: "buy_more"
								},
								children: [
									BX.create("div", {
										attrs: {
											className: "add2basket_block"
										},
										children: [
											BX.create("button", {
												attrs: {
													className: "btn_buy",
													value: this.messages.ADD_BUTTON_FULL,
													"data-role": "set-add-btn",
													name: "add2set"
												},
												children: [
													BX.create("i", {
														attrs: {
															className: "fa fa-plus"
														}
													}),
													BX.create("span", {
														attrs: {
															className: "full"
														},
														html: this.messages.ADD_BUTTON_FULL
													}),
													BX.create("span", {
														attrs: {
															className: "short"
														},
														html: this.messages.ADD_BUTTON_SHORT
													})
												]
											})
										]
									})
								]									
							})
						]
					})
				]
			});
														
			var slider = BX.findChild(this.parentCont, {attribute: {id: "other-items-section-slider-" + itemSectId}}, true, false);
			if(!!slider) {
				slider.appendChild(newSliderNode);
				BX.findChild(slider.parentNode.parentNode.parentNode, {className: "qnt"}, true, false).innerHTML = BX.findChildren(slider, {className: "other-item"}, true).length;
			}

			this.numSetItems--;
			
			BX.remove(item);
			
			for(i = 0, l = this.setIds.length; i < l; i++) {		
				if(this.setIds[i].ID == itemId) {
					keyDel = i;			
				}
			}
			this.setIds.splice(keyDel, 1);

			this.recountPrice();
			this.recountSlider(itemSectId);

			if(this.numSetItems <= 0) {
				if(!!this.emptySetMessage) {
					BX.adjust(this.emptySetMessage, {
						style: {
							display: ""
						},
						html: this.messages.EMPTY_SET
					});
				}
				BX.adjust(this.buyButton, {
					props: {disabled: true}
				});
			}
		}
	};
	
	SetConstructor.prototype.addToSet = function() {
		var target = BX.proxy_context,
			item,
			itemId,
			itemIblockId,
			itemSectId,
			itemUrl,
			itemImg,
			itemName,
			itemArticle,
			itemPrice,
			itemFormatPrice,
			itemRefPrice,
			itemFormatRefPrice,
			itemOldPrice,
			itemDiffPrice,
			itemCurrency,
			itemMeasure,
			newSetNode;

		if(!!target && target.hasAttribute("data-role") && target.getAttribute("data-role") == "set-add-btn") {
			item = target.parentNode.parentNode.parentNode.parentNode;			

			itemId = item.getAttribute("data-id");
			itemIblockId = item.getAttribute("data-iblock-id");
			itemSectId = item.getAttribute("data-section-id");			
			itemUrl = item.getAttribute("data-url");
			itemImg = item.getAttribute("data-img");
			itemName = item.getAttribute("data-name");
			itemArticle = item.getAttribute("data-article");
			itemPrice = item.getAttribute("data-price");
			itemFormatPrice = item.getAttribute("data-format-price");			
			itemRefPrice = item.getAttribute("data-reference-price");
			itemFormatRefPrice = item.getAttribute("data-format-reference-price");
			itemOldPrice = item.getAttribute("data-old-price");
			itemDiffPrice = item.getAttribute("data-diff-price");
			itemCurrency = item.getAttribute("data-currency");
			itemMeasure = item.getAttribute("data-measure");
			
			newSetNode = BX.create("div", {
				attrs: {
					className: "catalog-item added-item",
					"data-id": itemId,
					"data-iblock-id": itemIblockId,
					"data-section-id": itemSectId,
					"data-url": itemUrl,
					"data-img": itemImg ? itemImg : "",					
					"data-name": itemName,
					"data-article": itemArticle ? itemArticle : "",
					"data-price": itemPrice,
					"data-format-price": itemFormatPrice,
					"data-reference-price": itemRefPrice,
					"data-format-reference-price": itemFormatRefPrice,
					"data-old-price": itemOldPrice,
					"data-diff-price": itemDiffPrice,
					"data-currency": itemCurrency,
					"data-measure": itemMeasure
				},
				children: [
					BX.create("div", {
						attrs: {
							className: "catalog-item-info"
						},
						children: [
							BX.create("div", {
								attrs: {
									className: "catalog-item-image-cont"
								},
								children: [
									BX.create("div", {
										attrs: {
											className: "catalog-item-image"
										},
										children: [
											BX.create("a", {
												attrs: {
													href: itemUrl
												},
												children: [
													BX.create("img", {
														attrs: {
															className: "item_img",
															width: itemImg ? 160 : 150,
															height: itemImg ? 160 : 150,
															alt: itemName,
															src: itemImg ? itemImg : this.noFotoSrc,
														}
													})
												]
											})
										]
									})
								]
							}),
							BX.create("div", {
								attrs: {
									className: "catalog-item-title"
								},
								children: [
									BX.create("a", {
										attrs: {											
											title: itemName,
											href: itemUrl
										},
										html: itemName
									}),
									BX.create("div", {
										attrs: {
											className: "catalog-item-article"
										},
										style: {
											display: itemArticle ? "" : "none"
										},
										html: itemArticle ? this.messages.ARTICLE + itemArticle : ""
									})
								]
							}),
							BX.create("div", {
								attrs: {
									className: "item-price"
								},
								children: [
									BX.create("span", {
										attrs: {
											className: "catalog-item-price"
										},										
										children: [
											BX.create("span", {
												html: itemFormatPrice
											}),
											BX.create("span", {
												attrs: {
													className: "unit"
												},
												html: itemCurrency + " <span>" + itemMeasure + "</span>"
											}),
											BX.create("span", {
												attrs: {
													className: "catalog-item-price-reference"
												},
												style: {
													display: this.setRefPriceCont ? "" : "none"
												},
												html: this.setRefPriceCont ? itemFormatRefPrice + "<span class='unit'>" + itemCurrency + "</span>" : ""
											})
										]
									}),									
									BX.create("span", {
										attrs: {
											className: "catalog-item-price-old"
										},
										style: {
											display: this.setOldPriceCont ? (Math.floor(itemDiffPrice * 100) > 0 ? "" : "none") : "none"
										},
										html: this.setOldPriceCont ? (Math.floor(itemDiffPrice * 100) > 0 ? BX.Currency.currencyFormat(itemOldPrice, this.currency, true) : "") : ""
									})
								]
							}),
							BX.create("div", {
								attrs: {
									className: "catalog-item-delete"
								},
								children: [
									BX.create("a", {
										attrs: {											
											"data-role": "set-delete-btn",
											href: "javascript:void(0)"
										},
										html: "<i class='fa fa-times'></i>"
									})
								]
							})
						]
					})
				]
			});
			
			this.setItemsCont.appendChild(newSetNode);
			
			this.numSetItems++;
			
			BX.remove(item);
			
			var slider = BX.findChild(this.parentCont, {attribute: {id: "other-items-section-slider-" + itemSectId}}, true, false);
			if(!!slider) {
				BX.findChild(slider.parentNode.parentNode.parentNode, {className: "qnt"}, true, false).innerHTML = BX.findChildren(slider, {className: "other-item"}, true).length;
			}
			
			this.setIds.push({
				ID: itemId,
				IBLOCK_ID: itemIblockId
			});
			this.recountPrice();
			this.recountSlider(itemSectId);

			if(this.numSetItems > 0) {
				if(!!this.emptySetMessage) {
					this.emptySetMessage.innerHTML = "";
					this.emptySetMessage.style.display = "none";
				}
				BX.adjust(this.buyButton, {
					props: {disabled: false}
				});
			}
		}
	};

	SetConstructor.prototype.recountPrice = function() {
		var setPriceCont = this.setPriceCont,
			sumPrice = this.mainElementPrice * this.mainElementBasketQuantity,
			sumRefPrice = this.mainElementRefPrice * this.mainElementBasketQuantity,
			sumOldPrice = this.mainElementOldPrice * this.mainElementBasketQuantity,
			sumDiffDiscountPrice = this.mainElementDiffPrice * this.mainElementBasketQuantity,
			setItems = BX.findChildren(this.setItemsCont, {className: "added-item"}, true),
			i,
			l;
		
		if(!!setItems) {
			for(i = 0, l = setItems.length; i < l; i++) {				
				sumPrice += Number(setItems[i].getAttribute("data-price"));
				sumRefPrice += Number(setItems[i].getAttribute("data-reference-price"));
				sumOldPrice += Number(setItems[i].getAttribute("data-old-price"));
				sumDiffDiscountPrice += Number(setItems[i].getAttribute("data-diff-price"));
			}
		}
		
		BX.ajax.post(
			this.ajaxPath,
			{
				sessid : BX.bitrix_sessid(),
				action : "ajax_recount_prices",
				sumPrice : sumPrice,
				currency : this.currency
			},
			function(result) {
				var json = JSON.parse(result);
				if(json.sumValue) {					
					setPriceCont.innerHTML = json.sumValue;
					setPriceCont.parentNode.style.display = "";
				} else {
					setPriceCont.innerHTML = "";
					setPriceCont.parentNode.style.display = "none";
				}
			}
		);
		
		if(!!this.setRefPriceCont) {
			if(sumRefPrice > 0) {
				this.setRefPriceCont.innerHTML = BX.Currency.currencyFormat(sumRefPrice, this.currency, true);
				this.setRefPriceCont.style.display = "";
			} else {
				this.setRefPriceCont.innerHTML = "";
				this.setRefPriceCont.style.display = "none";
			}
		}
		
		if(Math.floor(sumDiffDiscountPrice * 100) > 0) {
			if(!!this.setOldPriceCont) {
				BX.adjust(this.setOldPriceCont, {
					style: {
						display: ""
					},
					html: BX.Currency.currencyFormat(sumOldPrice, this.currency, true)
				});
			}
			if(!!this.setDiffPriceCont) {
				this.setDiffPriceCont.innerHTML = BX.Currency.currencyFormat(sumDiffDiscountPrice, this.currency, true);
				this.setDiffPriceCont.parentNode.style.display = "";
			}
		} else {
			if(!!this.setOldPriceCont) {
				this.setOldPriceCont.innerHTML = "";
				this.setOldPriceCont.style.display = "none";
			}
			if(!!this.setDiffPriceCont) {
				this.setDiffPriceCont.innerHTML = "";
				this.setDiffPriceCont.parentNode.style.display = "none";
			}
		}
	};

	SetConstructor.prototype.widthSlide = function() {
		var divClassCenter,
			divClassCenterWidth = 0,
			slideWidth = 0;
						
		divClassCenter = BX.findChild(document.body, {className: "center"}, true, false);
		if(!!divClassCenter)
			divClassCenterWidth = BX(divClassCenter).offsetWidth;
		if(divClassCenterWidth >= 1234) {
			slideWidth = 192;			
		} else if(divClassCenterWidth < 1234 && divClassCenterWidth >= 768) {
			slideWidth = 144;			
		}
		
		return slideWidth;
	};
	
	SetConstructor.prototype.widthSlider = function() {
		var slider,
			sliderItemsCount = 0,
			sliderArrowLeft,
			sliderArrowRight,
			slideWidth,
			i;

		slideWidth = this.widthSlide();
		
		slider = BX.findChildren(this.parentCont, {className: "other-items-section-slider"}, true);
		if(!!slider && 0 < slider.length) {
			for(i = 0; i < slider.length; i++) {
				sliderItemsCount = BX.findChildren(slider[i], {className: "other-item"}, true).length;
				slider[i].style.width = sliderItemsCount <= 5 ? "100%" : (slideWidth > 0 ? slideWidth * sliderItemsCount - 2 + "px" : "100%");
				slider[i].setAttribute("data-style-left", 0);
				slider[i].style.left = 0;

				sliderArrowLeft = BX.findChild(slider[i].parentNode.parentNode, {attribute: {"data-role": "arrow-left"}}, true, false);				
				if(!!sliderArrowLeft) {
					sliderArrowLeft.style.display = sliderItemsCount > 5 && slideWidth > 0 ? "block" : "none";
				}

				sliderArrowRight = BX.findChild(slider[i].parentNode.parentNode, {attribute: {"data-role": "arrow-right"}}, true, false);				
				if(!!sliderArrowRight) {
					sliderArrowRight.style.display = sliderItemsCount > 5 && slideWidth > 0 ? "block" : "none";
				}
			}
		}
	};
	
	SetConstructor.prototype.recountSlider = function(section_id) {		
		var slider = BX.findChild(this.parentCont, {attribute: {id: "other-items-section-slider-" + section_id}}, true, false),
			sliderItemsCount = BX.findChildren(slider, {className: "other-item"}, true).length || 0,
			sliderArrowLeft = BX.findChild(this.parentCont, {attribute: {id: "other-items-section-slider-left-" + section_id}}, true, false),
			sliderArrowRight = BX.findChild(this.parentCont, {attribute: {id: "other-items-section-slider-right-" + section_id}}, true, false),
			slideWidth;
			
		slideWidth = this.widthSlide();

		if(!!slider)
			slider.style.width = sliderItemsCount <= 5 ? "100%" : (slideWidth > 0 ? slideWidth * sliderItemsCount - 2 + "px" : "100%");
		
		if(sliderItemsCount > 5 && slideWidth > 0) {
			if(!!sliderArrowLeft)
				sliderArrowLeft.style.display = "block";
			if(!!sliderArrowRight)
				sliderArrowRight.style.display = "block";		
		} else {
			if(!!sliderArrowLeft)
				sliderArrowLeft.style.display = "none";
			if(!!sliderArrowRight)
				sliderArrowRight.style.display = "none";
			if(!!slider) {				
				slider.setAttribute("data-style-left", 0);
				slider.style.left = 0;
			}
		}
	};

	SetConstructor.prototype.scrollItems = function() {		
		var target = BX.proxy_context,			
			slider = BX.findChild(target.parentNode, {className: "other-items-section-slider"}, true, false),
			sliderItemsCount = BX.findChildren(slider, {className: "other-item"}, true).length || 0,
			curLeftPercent,
			leftPercent,
			slideWidth;
		
		slideWidth = this.widthSlide();
		
		if(slideWidth <= 0)
			return;
		
		if(!!target && target.hasAttribute("data-role")) {
			if(target.getAttribute("data-role") == "arrow-left") {
				curLeftPercent = slider.getAttribute("data-style-left");
				if(curLeftPercent >= 0)
					return;
				leftPercent = +(curLeftPercent) + slideWidth;
			} else if(target.getAttribute("data-role") == "arrow-right") {
				curLeftPercent = slider.getAttribute("data-style-left");				
				if(-curLeftPercent >= (sliderItemsCount - 5) * slideWidth)
					return;
				leftPercent = +(curLeftPercent) - slideWidth;
			}
			if(!!slider) {
				slider.setAttribute("data-style-left", leftPercent);
				slider.style.left = leftPercent + "px";
			}
		}
	};

	SetConstructor.prototype.OpenFormPopup = function() {
		var target = BX.proxy_context,			
			action = target.getAttribute("data-action"),
			item = target.parentNode.parentNode.parentNode.parentNode,			
			elementId = item.getAttribute("data-id"),
			elementName = item.getAttribute("data-name"),
			visualId = action + "_" + this.jsId + "_" + elementId,
			popupPath = this.popupPath;
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
							popupPath,
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

	SetConstructor.prototype.addToBasket = function() {
		var target = BX.proxy_context;
		
		BX.ajax.post(
			this.ajaxPath,
			{
				sessid: BX.bitrix_sessid(),
				action: "catalogSetAdd2Basket",
				set_ids: this.setIds,
				lid: this.lid,
				setOffersCartProps: this.offersCartProps,				
				setSelectProps: !!BX("select_props_" + this.jsId) ? BX("select_props_" + this.jsId).getAttribute("value") : "",
				itemsRatio: this.itemsRatio
			},
			BX.proxy(function(result) {				
				BX.ajax.post(
					this.siteDir + "ajax/basket_line.php",
					"",
					BX.proxy(function(data) {					
						refreshCartLine(data);
					}, this)
				);
				BX.ajax.post(
					this.siteDir + "ajax/delay_line.php",
					"",
					BX.proxy(function(data) {
						BX.findChild(document.body, {className: "delay_line"}, true, false).innerHTML = data;
					}, this)
				);
				BX.adjust(target, {
					props: {disabled: true},
					html: "<i class='fa fa-check'></i><span>" + this.messages.ADDITEMINCART_ADDED + "</span>"
				});
				this.BasketResult();
			}, this)
		);
	};

	SetConstructor.prototype.BasketResult = function() {
		var close,
			strContent,
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
			titleBar: {content: BX.create("span", {html: this.messages.POPUP_TITLE})}			
		});
		
		close = BX.findChild(BX("addItemInCart"), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";
		
		strContent = BX.create("div", {
			attrs: {
				className: "cont"
			},
			children: [
				BX.create("div", {
					attrs: {
						className: "item_image_cont"
					},
					children: [
						BX.create("div", {
							attrs: {
								className: "item_image_full"
							},
							children: [
								BX.create("img", {
									attrs: {
										width: this.mainElementPict.WIDTH,
										height: this.mainElementPict.HEIGHT,
										alt: this.messages.POPUP_TITLE,
										src: this.mainElementPict.SRC
									}
								})
							]
						})
					]
				})
			]
		});

		buttons = [			
			new BasketButton({				
				text: this.messages.POPUP_BTN_CLOSE,
				name: "close",
				className: "btn_buy ppp close",
				events: {
					click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
				}
			}),
			new BasketButton({				
				text: this.messages.POPUP_BTN_ORDER,
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

	SetConstructor.prototype.BasketRedirect = function() {
		document.location.href = this.basketUrl;
	};

	return SetConstructor;
})();