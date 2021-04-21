(function (window) {

	if(!!window.JCCatalogElementProps) {
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

	window.JCCatalogElementProps = function (arParams) {
		this.productType = 0;

		this.config = {
			useCatalog: true,
			refPriceCoef: false
		};

		this.checkQuantity = false;
		this.maxQuantity = 0;
		this.minQuantity = 0;
		this.stepQuantity = 1;
		this.isDblQuantity = false;

		this.currentPriceMode = "";
		this.currentPrices = [];
		this.currentPriceSelected = 0;
		this.currentQuantityRanges = [];
		this.currentQuantityRangeSelected = 0;
		this.currentPriceMatrix = [];
		this.currentPriceMatrixOffer = [];
		
		this.precision = 6;
		this.precisionFactor = Math.pow(10, this.precision);
			
		this.visual = {
			ID: "",
			PICT_ID: "",
			PRICE_ID: "",
			BUY_ID: ""
		};

		this.product = {
			name: "",
			id: 0,
			pict: {}
		};
		
		this.offersView = null;
		this.offers = [];
		this.offerNum = 0;
		this.treeProps = [];
		this.obTreeRows = [];
		this.selectedValues = {};
		this.selectProps = [];
		this.obSelectRows = [];
		
		this.obProduct = null;
		this.obPict = null;
		this.obPrice = null;
		this.obBuy = null;		
		this.obTree = null;
		this.obSelect = null;
		this.obBuyBtn = null;
		
		this.obPopupWin = null;
		this.obPopupWinMatrix = null;
		this.basketParams = {};

		this.errorCode = 0;

		if(typeof arParams === "object") {
			this.params = arParams;
			this.initConfig();

			switch(this.productType) {				
				case 1://product
				case 2://set
					this.initProductData();
					break;
				case 3://sku
					this.initOffersData();
					break;
				default:
					this.errorCode = -1;
			}
		}
		if(0 === this.errorCode) {
			BX.ready(BX.delegate(this.Init,this));
		}
		this.params = {};
	};

	window.JCCatalogElementProps.prototype.Init = function() {
		var i = 0,
		strPrefix = "",
		selPrefix = "",
		TreeItems = null;
		SelectItems = null;
		
		this.obProduct = BX(this.visual.ID);
		if(!this.obProduct) {
			this.errorCode = -1;
		}

		if(3 === this.productType) {
			if("LIST" !== this.offersView) {
				this.obPict = BX(this.visual.PICT_ID);
				if(!this.obPict && this.config.useCatalog) {
					this.errorCode = -16;
				}

				this.obPrice = BX(this.visual.PRICE_ID);
				if(!this.obPrice && this.config.useCatalog) {
					this.errorCode = -16;
				}

				this.obBuy = BX(this.visual.BUY_ID);
				if(!this.obBuy && this.config.useCatalog) {
					this.errorCode = -16;
				}
				
				if(!!this.visual.TREE_ID) {
					this.obTree = BX(this.visual.TREE_ID);
					if(!this.obTree) {
						this.errorCode = -256;
					}
					strPrefix = this.visual.TREE_ITEM_ID;
					for(i = 0; i < this.treeProps.length; i++) {
						this.obTreeRows[i] = {
							LIST: BX(strPrefix+this.treeProps[i].ID+"_list"),
							CONT: BX(strPrefix+this.treeProps[i].ID+"_cont")
						};
						if(!this.obTreeRows[i].LIST || !this.obTreeRows[i].CONT) {
							this.errorCode = -512;
							break;
						}
					}
				}
			}
		}

		if(!!this.visual.SELECT_PROP_ID) {
			this.obSelect = BX(this.visual.SELECT_PROP_ID);
			if(!this.obSelect && this.config.useCatalog) {
				this.errorCode = -256;
			}
			selPrefix = this.visual.SELECT_PROP_ITEM_ID;
			for(i = 0; i < this.selectProps.length; i++) {
				this.obSelectRows[i] = BX(selPrefix+this.selectProps[i].ID);
				if(!this.obSelectRows[i]) {
					this.errorCode = -512;
					break;
				}
			}
		}
		
		if(!!this.visual.PRICE_MATRIX_BTN_ID) {
			for(var key in this.visual.PRICE_MATRIX_BTN_ID) {
				this.obPriceMatrix = BX(this.visual.PRICE_MATRIX_BTN_ID[key]);
				BX.bind(this.obPriceMatrix, "click", BX.delegate(this.OpenPriceMatrixPopup, this));
			}
						
		}

		if(!!this.visual.BTN_BUY_ID) {			
			this.obBuyBtn = BX(this.visual.BTN_BUY_ID);			
		}
		
		if(0 === this.errorCode) {
			switch(this.productType) {				
				case 1://product
				case 2://set
					if(!!this.obSelect) {
						SelectItems = BX.findChildren(this.obSelect, {tagName: "li"}, true);
						if(!!SelectItems && 0 < SelectItems.length) {
							for(i = 0; i < SelectItems.length; i++) {
								BX.bind(SelectItems[i], "click", BX.delegate(this.SelectProp, this));
							}
							this.SetSelectCurrent();
						}						
					}
					break;
				case 3://sku
					if("LIST" !== this.offersView) {
						TreeItems = BX.findChildren(this.obTree, {tagName: "li"}, true);
						if(!!TreeItems && 0 < TreeItems.length) {
							for(i = 0; i < TreeItems.length; i++) {
								BX.bind(TreeItems[i], "click", BX.delegate(this.SelectOfferProp, this));
							}
						}
						this.SetCurrent();
					}
					
					if(!!this.obSelect) {
						SelectItems = BX.findChildren(this.obSelect, {tagName: "li"}, true);
						if(!!SelectItems && 0 < SelectItems.length) {
							for(i = 0; i < SelectItems.length; i++) {
								BX.bind(SelectItems[i], "click", BX.delegate(this.SelectProp, this));
							}
							this.SetSelectCurrent();
						}						
					}
					break;
			}
		}

		switch(this.productType) {			
			case 1://product
			case 2://set
				this.obQuantityUp = BX("quantity_select_plus_" + this.visual.ID);
				if(!!this.obQuantityUp)
					BX.bind(this.obQuantityUp, "click", BX.delegate(this.QuantityUp, this));
				
				this.obQuantityDown = BX("quantity_select_minus_" + this.visual.ID);
				if(!!this.obQuantityDown)
					BX.bind(this.obQuantityDown, "click", BX.delegate(this.QuantityDown, this));

				this.obQuantity = BX("quantity_select_" + this.visual.ID);
				if(!!this.obQuantity)
					BX.bind(this.obQuantity, "change", BX.delegate(this.QuantityChange, this));
				
				if(!!this.obBuyBtn)
					BX.bind(this.obBuyBtn, "click", BX.delegate(this.Add2Basket, this));
				break;
			case 3://sku
				if("LIST" !== this.offersView) {
					if(!!this.obBuy) {
						quantityUpItems = BX.findChildren(this.obBuy, {tagName: "a", className: "plus"}, true);
						quantityDownItems = BX.findChildren(this.obBuy, {tagName: "a", className: "minus"}, true);
						quantityItems = BX.findChildren(this.obBuy, {tagName: "input", className: "quantity"}, true);
						if(!!this.visual.POPUP_BTN_ID)
							popupBtnItems = BX.findChildren(this.obBuy, {attribute: {id: this.visual.POPUP_BTN_ID}}, true);
						buyBtnItems = BX.findChildren(this.obBuy, {tagName: "button", attribute: {name: "add2basket"}}, true);
						zoomItems = null;
					}
				} else {
					if(!!this.obProduct) {
						priceRangesItems = BX.findChildren(this.obProduct, {className: "catalog-item-price-ranges"}, true);
						if(!!priceRangesItems && 0 < priceRangesItems.length) {
							for(i = 0; i < priceRangesItems.length; i++) {
								BX.bind(priceRangesItems[i], "click", BX.delegate(this.OpenPriceRangesPopup, this));
							}
						}
						quantityUpItems = BX.findChildren(this.obProduct, {tagName: "a", className: "plus"}, true);
						quantityDownItems = BX.findChildren(this.obProduct, {tagName: "a", className: "minus"}, true);
						quantityItems = BX.findChildren(this.obProduct, {tagName: "input", className: "quantity"}, true);
						if(!!this.visual.POPUP_BTN_ID)
							popupBtnItems = BX.findChildren(this.obProduct, {attribute: {id: this.visual.POPUP_BTN_ID}}, true);
						buyBtnItems = BX.findChildren(this.obProduct, {tagName: "button", attribute: {name: "add2basket"}}, true);
						zoomItems = BX.findChildren(this.obProduct, {className: "catalog-item"}, true);
					}
				}
				
				if(!!quantityUpItems && 0 < quantityUpItems.length) {
					for(i = 0; i < quantityUpItems.length; i++) {
						BX.bind(quantityUpItems[i], "click", BX.delegate(this.QuantityUp, this));
					}
				}
				
				if(!!quantityDownItems && 0 < quantityDownItems.length) {
					for(i = 0; i < quantityDownItems.length; i++) {
						BX.bind(quantityDownItems[i], "click", BX.delegate(this.QuantityDown, this));
					}
				}

				if(!!quantityItems && 0 < quantityItems.length) {
					for(i = 0; i < quantityItems.length; i++) {
						BX.bind(quantityItems[i], "change", BX.delegate(this.QuantityChange, this));
					}
				}
				
				if(!!popupBtnItems && 0 < popupBtnItems.length) {
					for(i = 0; i < popupBtnItems.length; i++) {
						BX.bind(popupBtnItems[i], "click", BX.delegate(this.OpenFormPopup, this));
					}
				}
				
				if(!!zoomItems && 0 < zoomItems.length) {
					for(i = 0; i < zoomItems.length; i++) {
						var zoom = BX.findChildren(BX(zoomItems[i]),{className:"zoom"},true);
						BX.bind(BX(zoom[0]), "click", BX.delegate(this.pictZoom, this));
					}
				}
				
				if(!!this.visual.PRICE_MATRIX_BTN_ID) {
					for(var key in this.visual.PRICE_MATRIX_BTN_ID) {
						for(var j in this.visual.PRICE_MATRIX_BTN_ID[key]) {
							this.obPriceMatrix = BX(this.visual.PRICE_MATRIX_BTN_ID[key][j]);
							BX.bind(this.obPriceMatrix, "click", BX.delegate(this.OpenPriceMatrixPopup, this));
						}
					}		
				}	
				
				if(!!buyBtnItems && 0 < buyBtnItems.length) {
					for(i = 0; i < buyBtnItems.length; i++) {
						BX.bind(buyBtnItems[i], "click", BX.delegate(this.Add2Basket, this));
					}
				}
				break;
		}
	};

	window.JCCatalogElementProps.prototype.initConfig = function() {
		this.productType = parseInt(this.params.PRODUCT_TYPE, 10);
		if(!!this.params.CONFIG && typeof(this.params.CONFIG) === "object") {
			if(this.params.CONFIG.USE_CATALOG !== "undefined" && BX.type.isBoolean(this.params.CONFIG.USE_CATALOG)) {
				this.config.useCatalog = this.params.CONFIG.USE_CATALOG;
			}
			if(!!this.params.CONFIG.REFERENCE_PRICE_COEF)
				this.config.refPriceCoef = this.params.CONFIG.REFERENCE_PRICE_COEF
		} else {
			// old version
			if(this.params.USE_CATALOG !== "undefined" && BX.type.isBoolean(this.params.USE_CATALOG)) {
				this.config.useCatalog = this.params.USE_CATALOG;
			}
			if(!!this.params.REFERENCE_PRICE_COEF)
				this.config.refPriceCoef = this.params.REFERENCE_PRICE_COEF
		}
		if(!this.params.VISUAL || typeof(this.params.VISUAL) !== "object" || !this.params.VISUAL.ID) {
			this.errorCode = -1;
			return;
		}
		this.visual = this.params.VISUAL;
	};

	window.JCCatalogElementProps.prototype.initProductData = function() {
		if(!!this.params.PRODUCT && "object" === typeof(this.params.PRODUCT)) {
			this.product.id = this.params.PRODUCT.ID;
			this.product.name = this.params.PRODUCT.NAME;
			this.product.pict = this.params.PRODUCT.PICT;

			this.currentPriceMode = this.params.PRODUCT.ITEM_PRICE_MODE;
			this.currentPrices = this.params.PRODUCT.ITEM_PRICES;
			this.currentPriceSelected = this.params.PRODUCT.ITEM_PRICE_SELECTED;
			this.currentQuantityRanges = this.params.PRODUCT.ITEM_QUANTITY_RANGES;
			this.currentQuantityRangeSelected = this.params.PRODUCT.ITEM_QUANTITY_RANGE_SELECTED;
			this.currentPriceMatrix = this.params.PRODUCT.PRICE_MATRIX;		
			
			this.checkQuantity = this.params.PRODUCT.CHECK_QUANTITY;
			this.isDblQuantity = this.params.PRODUCT.QUANTITY_FLOAT;
			if(this.checkQuantity)
				this.maxQuantity = (this.isDblQuantity ? parseFloat(this.params.PRODUCT.MAX_QUANTITY) : parseInt(this.params.PRODUCT.MAX_QUANTITY, 10));
			this.stepQuantity = (this.isDblQuantity ? parseFloat(this.params.PRODUCT.STEP_QUANTITY) : parseInt(this.params.PRODUCT.STEP_QUANTITY, 10));
			if(this.isDblQuantity)
				this.stepQuantity = Math.round(this.stepQuantity * this.precisionFactor) / this.precisionFactor;
			this.minQuantity = this.currentPriceMode === "Q" ? parseFloat(this.currentPrices[this.currentPriceSelected].MIN_QUANTITY) : this.stepQuantity;

			if(!!this.params.SELECT_PROPS) {
				this.selectProps = this.params.SELECT_PROPS;
			}
		} else {
			this.errorCode = -1;
		}
	};

	window.JCCatalogElementProps.prototype.initOffersData = function() {
		if(!!this.params.OFFERS && BX.type.isArray(this.params.OFFERS)) {
			if(!!this.params.OFFERS_VIEW) {
				this.offersView = this.params.OFFERS_VIEW;
			}
			this.offers = this.params.OFFERS;
			this.offerNum = 0;
			if(!!this.params.OFFER_SELECTED) {
				this.offerNum = parseInt(this.params.OFFER_SELECTED, 10);
			}
			if(isNaN(this.offerNum)) {
				this.offerNum = 0;
			}			
			if(!!this.params.TREE_PROPS) {
				this.treeProps = this.params.TREE_PROPS;
			}
			if(!!this.params.PRODUCT && typeof(this.params.PRODUCT) === "object") {
				this.product.id = parseInt(this.params.PRODUCT.ID, 10);
				this.product.name = this.params.PRODUCT.NAME;
				this.product.pict = this.params.PRODUCT.PICT;
				if(!!this.params.SELECT_PROPS) {
					this.selectProps = this.params.SELECT_PROPS;
				}
			}
			
			for(var k in this.offers) {
				this.currentPriceMatrixOffer[k] = this.offers[k].PRICE_MATRIX;
			}
			
			this.currency = this.params.PRODUCT.PRINT_CURRENCY;
			
			if("LIST" !== this.offersView) {
				this.initOffersQuantityData(this.offerNum);
			}
		} else {
			this.errorCode = -1;
		}
	};

	window.JCCatalogElementProps.prototype.initOffersQuantityData = function(offerNum) {
		this.currentPriceMode = this.offers[offerNum].ITEM_PRICE_MODE;
		this.currentPrices = this.offers[offerNum].ITEM_PRICES;
		this.currentPriceSelected = this.offers[offerNum].ITEM_PRICE_SELECTED;
		this.currentQuantityRanges = this.offers[offerNum].ITEM_QUANTITY_RANGES;
		this.currentQuantityRangeSelected = this.offers[offerNum].ITEM_QUANTITY_RANGE_SELECTED;

		this.checkQuantity = this.offers[offerNum].CHECK_QUANTITY;
		this.isDblQuantity = this.offers[offerNum].QUANTITY_FLOAT;
		if(this.checkQuantity)
			this.maxQuantity = (this.isDblQuantity ? parseFloat(this.offers[offerNum].MAX_QUANTITY) : parseInt(this.offers[offerNum].MAX_QUANTITY, 10));
		this.stepQuantity = (this.isDblQuantity ? parseFloat(this.offers[offerNum].STEP_QUANTITY) : parseInt(this.offers[offerNum].STEP_QUANTITY, 10));
		if(this.isDblQuantity)
			this.stepQuantity = Math.round(this.stepQuantity * this.precisionFactor) / this.precisionFactor;
		this.minQuantity = this.currentPriceMode === "Q" ? parseFloat(this.currentPrices[this.currentPriceSelected].MIN_QUANTITY) : this.stepQuantity;
		this.obQuantity = BX("quantity_" + this.visual.ID + "_" + this.offers[offerNum].ID);					
	};

	window.JCCatalogElementProps.prototype.QuantityUp = function() {
		var curValue = 0,
			boolSet = true;
		
		switch(this.productType) {			
			case 1://product
			case 2://set				
				break;
			case 3://sku
				if("LIST" == this.offersView) {					
					var target = BX.proxy_context,
						offerItem = BX.findParent(target, {className: "catalog-item"});
					if(!!offerItem)
						this.offerNum = offerItem.getAttribute("data-offer-num");
					this.initOffersQuantityData(this.offerNum);
				}
				break;
		}
		
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

				this.SetPrice();
			}
		}
	};

	window.JCCatalogElementProps.prototype.QuantityDown = function() {
		var curValue = 0,
			boolSet = true;

		switch(this.productType) {			
			case 1://product
			case 2://set				
				break;
			case 3://sku
				if("LIST" == this.offersView) {					
					var target = BX.proxy_context,
						offerItem = BX.findParent(target, {className: "catalog-item"});
					if(!!offerItem)
						this.offerNum = offerItem.getAttribute("data-offer-num");
					this.initOffersQuantityData(this.offerNum);
				}
				break;
		}
		
		curValue = this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10);
		if(!isNaN(curValue)) {
			curValue -= this.stepQuantity;
			this.CheckPriceRange(curValue);
			if(curValue < this.minQuantity) {
				boolSet = false;
			}
			if(boolSet) {
				if(this.isDblQuantity) {
					curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
				}
				this.obQuantity.value = curValue;

				this.SetPrice();
			}
		}
	};

	window.JCCatalogElementProps.prototype.QuantityChange = function() {
		var curValue = 0,
			intCount,
			count;

		switch(this.productType) {			
			case 1://product
			case 2://set				
				break;
			case 3://sku
				if("LIST" == this.offersView) {					
					var target = BX.proxy_context,
						offerItem = BX.findParent(target, {className: "catalog-item"});
					if(!!offerItem)
						this.offerNum = offerItem.getAttribute("data-offer-num");
					this.initOffersQuantityData(this.offerNum);
				}
				break;
		}
		
		curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
		if(!isNaN(curValue)) {
			if(this.checkQuantity) {
				if(curValue > this.maxQuantity) {
					curValue = this.maxQuantity;
				}
			}
			this.CheckPriceRange(curValue);
			if(curValue < this.minQuantity) {
				curValue = this.minQuantity;
			} else {
				intCount = Math.round(Math.round(curValue * this.precisionFactor / this.stepQuantity) / this.precisionFactor) || 1;
				curValue = (intCount <= 1 ? this.stepQuantity : intCount * this.stepQuantity);
				curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
			}
			this.obQuantity.value = curValue;
		} else {
			this.obQuantity.value = this.minQuantity;
		}

		this.SetPrice();
	};

	window.JCCatalogElementProps.prototype.CheckPriceRange = function(quantity) {
		if(typeof quantity === "undefined" || this.currentPriceMode != "Q")
			return;

		var range, found = false;

		for(var i in this.currentQuantityRanges) {
			if(this.currentQuantityRanges.hasOwnProperty(i)) {
				range = this.currentQuantityRanges[i];

				if(parseInt(quantity) >= parseInt(range.SORT_FROM) && (range.SORT_TO == "INF" || parseInt(quantity) <= parseInt(range.SORT_TO))) {
					found = true;
					this.currentQuantityRangeSelected = range.HASH;
					break;
				}
			}
		}

		if(!found && (range = this.GetMinPriceRange())) {
			this.currentQuantityRangeSelected = range.HASH;
		}

		for(var k in this.currentPrices) {
			if(this.currentPrices.hasOwnProperty(k)) {
				if(this.currentPrices[k].QUANTITY_HASH == this.currentQuantityRangeSelected) {
					this.currentPriceSelected = k;
					break;
				}
			}
		}
	};

	window.JCCatalogElementProps.prototype.GetMinPriceRange = function() {
		var range;

		for(var i in this.currentQuantityRanges) {
			if(this.currentQuantityRanges.hasOwnProperty(i)) {
				if(!range || parseInt(this.currentQuantityRanges[i].SORT_FROM) < parseInt(range.SORT_FROM)) {
					range = this.currentQuantityRanges[i];
				}
			}
		}

		return range;
	};

	window.JCCatalogElementProps.prototype.SetPrice = function() {
		var price, priceItem, oldPriceItem;
		
		if(this.obQuantity) {
			this.CheckPriceRange(this.obQuantity.value);
		}
		
		price = this.currentPrices[this.currentPriceSelected];
		
		switch(this.productType) {			
			case 1://product
			case 2://set				
				priceItem = BX.findChild(this.obProduct, {className: "price-current"}, true, false);		
				if(!!priceItem)
					BX.adjust(priceItem, {html: price.PRINT_RATIO_PRICE});
				
				oldPriceItem = BX.findChild(this.obProduct, {className: "price-old"}, true, false);
				if(!!oldPriceItem)
					BX.adjust(oldPriceItem, {html: price.PRINT_RATIO_BASE_PRICE});

				percentPriceItem = BX.findChild(this.obProduct, {className: "price-percent"}, true, false);
				if(!!percentPriceItem)
					BX.adjust(percentPriceItem, {html: BX.message("PROPS_ELEMENT_SKIDKA") + " " + price.PRINT_RATIO_DISCOUNT});		
				
				referencePriceItem = BX.findChild(this.obProduct, {className: "price-reference"}, true, false);
				if(!!referencePriceItem && !!this.config.refPriceCoef)
					BX.adjust(referencePriceItem, {html: BX.Currency.currencyFormat(price.RATIO_PRICE * this.config.refPriceCoef, price.CURRENCY, true)});
				break;
			case 3://sku
				if("LIST" !== this.offersView) {
					var priceItemCont = BX("price_" + this.visual.ID + "_" + this.offers[this.offerNum].ID);
					if(!!priceItemCont) {
						priceItem = BX.findChild(priceItemCont, {className: "price-current"}, true, false);
						if(!!priceItem)
							BX.adjust(priceItem, {html: price.PRINT_RATIO_PRICE});

						oldPriceItem = BX.findChild(priceItemCont, {className: "price-old"}, true, false);
						if(!!oldPriceItem)
							BX.adjust(oldPriceItem, {html: price.PRINT_RATIO_BASE_PRICE});

						percentPriceItem = BX.findChild(priceItemCont, {className: "price-percent"}, true, false);
						if(!!percentPriceItem)
							BX.adjust(percentPriceItem, {html: BX.message("PROPS_ELEMENT_SKIDKA") + " " + price.PRINT_RATIO_DISCOUNT});
						
						referencePriceItem = BX.findChild(priceItemCont, {className: "price-reference"}, true, false);
						if(!!referencePriceItem && !!this.config.refPriceCoef)
							BX.adjust(referencePriceItem, {html: BX.Currency.currencyFormat(price.RATIO_PRICE * this.config.refPriceCoef, price.CURRENCY, true)});
					}
				}
				break;
		}
	};

	window.JCCatalogElementProps.prototype.SelectProp = function() {
		var i = 0,
		RowItems = null,
		ActiveItems = null,
		selPropValueArr = [],		
		selPropValue = null,
		selDelayOnclick = null,
		selDelayOnclickArr = [],
		selDelayOnclickNew = null,
		target = BX.proxy_context;
		
		if(!!target && target.hasAttribute("data-select-onevalue")) {
			RowItems = BX.findChildren(target.parentNode, {tagName: "li"}, false);
			if(!!RowItems && 0 < RowItems.length) {
				for(i = 0; i < RowItems.length; i++) {
					BX.removeClass(RowItems[i], "active");
				}
			}
			BX.addClass(target, "active");
		}
		
		ActiveItems = BX.findChildren(this.obSelect, {tagName: "li", className: "active"}, true);
		if(!!ActiveItems && 0 < ActiveItems.length) {
			for(i = 0; i < ActiveItems.length; i++) {
				selPropValueArr[i] = ActiveItems[i].getAttribute("data-select-onevalue");
			}
		}
		selPropValue = selPropValueArr.join("||");
		
		if(!!this.offers && 0 < this.offers.length) {
			for(i = 0; i < this.offers.length; i++) {
				/*CART*/
				if(!!BX("select_props_"+this.visual.ID+"_"+this.offers[i].ID))
					BX("select_props_"+this.visual.ID+"_"+this.offers[i].ID).value = selPropValue;				
				/*DELAY*/
				if(!!BX("catalog-item-delay-"+this.visual.ID+"-"+this.offers[i].ID)) {
					selDelayOnclick = BX("catalog-item-delay-"+this.visual.ID+"-"+this.offers[i].ID).getAttribute("onclick");
					selDelayOnclickArr = selDelayOnclick.split("',");
					selDelayOnclickArr[3] = " '"+selPropValue;
					selDelayOnclickNew = selDelayOnclickArr.join("',");
					BX("catalog-item-delay-"+this.visual.ID+"-"+this.offers[i].ID).setAttribute("onclick", selDelayOnclickNew);
				}
			}
		} else {
			/*CART*/
			if(!!BX("select_props_"+this.visual.ID))
				BX("select_props_"+this.visual.ID).value = selPropValue;
		}
	};

	window.JCCatalogElementProps.prototype.SelectOfferProp = function() {
		var i = 0,
		strTreeValue = "",
		arTreeItem = [],
		RowItems = null,
		target = BX.proxy_context;

		if(!!target && target.hasAttribute("data-treevalue")) {
			strTreeValue = target.getAttribute("data-treevalue");
			arTreeItem = strTreeValue.split("_");
			this.SearchOfferPropIndex(arTreeItem[0], arTreeItem[1]);
			RowItems = BX.findChildren(target.parentNode, {tagName: "li"}, false);
			if(!!RowItems && 0 < RowItems.length) {
				for(i = 0; i < RowItems.length; i++) {
					BX.removeClass(RowItems[i], "active");
				}
			}
			BX.addClass(target, "active");
		}
	};

	window.JCCatalogElementProps.prototype.SearchOfferPropIndex = function(strPropID, strPropValue) {
		var strName = "",
		arShowValues = false,
		arCanBuyValues = [],
		allValues = [],
		index = -1,
		i, j,
		arFilter = {},
		tmpFilter = [];

		for(i = 0; i < this.treeProps.length; i++) {
			if(this.treeProps[i].ID === strPropID) {
				index = i;
				break;
			}
		}

		if(-1 < index) {
			for(i = 0; i < index; i++) {
				strName = "PROP_"+this.treeProps[i].ID;
				arFilter[strName] = this.selectedValues[strName];
			}
			strName = "PROP_"+this.treeProps[index].ID;
			arFilter[strName] = strPropValue;
			for(i = index+1; i < this.treeProps.length; i++) {
				strName = "PROP_"+this.treeProps[i].ID;
				arShowValues = this.GetRowValues(arFilter, strName);
				if(!arShowValues) {
					break;
				}
				allValues = [];
				arCanBuyValues = [];
				tmpFilter = [];
				tmpFilter = BX.clone(arFilter, true);
				for(j = 0; j < arShowValues.length; j++) {
					tmpFilter[strName] = arShowValues[j];
					allValues[allValues.length] = arShowValues[j];
					if(this.GetCanBuy(tmpFilter))
						arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
				}
				if(!!this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues)) {
					arFilter[strName] = this.selectedValues[strName];
				} else {
					arFilter[strName] = (arCanBuyValues.length > 0 ? arCanBuyValues[0] : allValues[0]);
				}
				this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
			}
			this.selectedValues = arFilter;
			this.ChangeInfo();
		}
	};

	window.JCCatalogElementProps.prototype.UpdateRow = function(intNumber, activeID, showID, canBuyID) {
		var i = 0,
		showI = 0,
		value = "",
		obData = {},
		RowItems = null,
		isCurrent = false,
		selectIndex = 0;
		
		if(-1 < intNumber && intNumber < this.obTreeRows.length) {
			RowItems = BX.findChildren(this.obTreeRows[intNumber].LIST, {tagName: "li"}, false);
			if(!!RowItems && 0 < RowItems.length) {
				obData = {
					props: { className: "" },
					style: {}
				};
				for(i = 0; i < RowItems.length; i++) {
					value = RowItems[i].getAttribute("data-onevalue");
					isCurrent = (value === activeID);
					if(BX.util.in_array(value, canBuyID)) {
						obData.props.className = (isCurrent ? "active" : "");
					} else {
						obData.props.className = (isCurrent ? "active disabled" : "disabled");
					}
					obData.style.display = "none";
					if(BX.util.in_array(value, showID)) {
						obData.style.display = "";
						if(isCurrent) {
							selectIndex = showI;
						}
						showI++;
					}
					BX.adjust(RowItems[i], obData);
				}

				obData = {
					style: {}
				};

				BX.adjust(this.obTreeRows[intNumber].LIST, obData);
			}
		}
	};

	window.JCCatalogElementProps.prototype.GetRowValues = function(arFilter, index) {
		var arValues = [],
		i = 0,
		j = 0,
		boolSearch = false,
		boolOneSearch = true;

		if(0 === arFilter.length) {
			for(i = 0; i < this.offers.length; i++) {
				if(!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
					arValues[arValues.length] = this.offers[i].TREE[index];
				}
			}
			boolSearch = true;
		} else {
			for(i = 0; i < this.offers.length; i++) {
				boolOneSearch = true;
				for(j in arFilter) {
					if(arFilter[j] !== this.offers[i].TREE[j]) {
						boolOneSearch = false;
						break;
					}
				}
				if(boolOneSearch) {
					if(!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
						arValues[arValues.length] = this.offers[i].TREE[index];
					}
					boolSearch = true;
				}
			}
		}
		return (boolSearch ? arValues : false);
	};

	window.JCCatalogElementProps.prototype.GetCanBuy = function(arFilter) {
		var i = 0,
			j = 0,
			boolOneSearch = true,
			boolSearch = false;
		
		for(i = 0; i < this.offers.length; i++) {			
			boolOneSearch = true;
			for(j in arFilter) {
				if(arFilter[j] !== this.offers[i].TREE[j]) {
					boolOneSearch = false;
					break;
				}
			}
			if(boolOneSearch) {
				if(this.offers[i].CAN_BUY) {
					boolSearch = true;
					break;
				}
			}
		}
		return boolSearch;
	};

	window.JCCatalogElementProps.prototype.SetSelectCurrent = function() {
		var i = 0,
		SelectItems = null,
		selPropValueArr = [],
		selPropValue = null,
		selDelayOnclick = null,
		selDelayOnclickArr = [],
		selDelayOnclickNew = null;		

		for(i = 0; i < this.obSelectRows.length; i++) {
			SelectItems = BX.findChildren(this.obSelectRows[i], {tagName: "li"}, true);
			if(!!SelectItems && 0 < SelectItems.length) {
				BX.addClass(SelectItems[0], "active");
				selPropValueArr[i] = SelectItems[0].getAttribute("data-select-onevalue");
			}
		}
		selPropValue = selPropValueArr.join("||");
		
		if(!!this.offers && 0 < this.offers.length) {
			for(i = 0; i < this.offers.length; i++) {
				/*CART*/
				if(!!BX("select_props_"+this.visual.ID+"_"+this.offers[i].ID))
					BX("select_props_"+this.visual.ID+"_"+this.offers[i].ID).value = selPropValue;				
				/*DELAY*/
				if(!!BX("catalog-item-delay-"+this.visual.ID+"-"+this.offers[i].ID)) {
					selDelayOnclick = BX("catalog-item-delay-"+this.visual.ID+"-"+this.offers[i].ID).getAttribute("onclick");
					selDelayOnclickArr = selDelayOnclick.split("',");
					selDelayOnclickArr[3] = " '"+selPropValue;
					selDelayOnclickNew = selDelayOnclickArr.join("',");
					BX("catalog-item-delay-"+this.visual.ID+"-"+this.offers[i].ID).setAttribute("onclick", selDelayOnclickNew);
				}				
			}
		} else {
			/*CART*/
			if(!!BX("select_props_"+this.visual.ID))
				BX("select_props_"+this.visual.ID).value = selPropValue;
		}
	}

	window.JCCatalogElementProps.prototype.SetCurrent = function() {
		var i = 0,
		j = 0,
		strName = "",
		arShowValues = false,
		arCanBuyValues = [],
		arFilter = {},
		tmpFilter = [],
		current = this.offers[this.offerNum].TREE;

		for(i = 0; i < this.treeProps.length; i++) {
			strName = "PROP_"+this.treeProps[i].ID;
			arShowValues = this.GetRowValues(arFilter, strName);
			if(!arShowValues) {
				break;
			}
			if(BX.util.in_array(current[strName], arShowValues)) {
				arFilter[strName] = current[strName];
			} else {
				arFilter[strName] = arShowValues[0];
				this.offerNum = 0;
			}
			arCanBuyValues = [];
			tmpFilter = [];
			tmpFilter = BX.clone(arFilter, true);
			for(j = 0; j < arShowValues.length; j++) {
				tmpFilter[strName] = arShowValues[j];
				if(this.GetCanBuy(tmpFilter)) {
					arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
				}
			}
			this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
		}
		this.selectedValues = arFilter;		
	};

	window.JCCatalogElementProps.prototype.ChangeInfo = function() {
		var index = -1,
		i = 0,
		j = 0,
		boolOneSearch = true;

		for(i = 0; i < this.offers.length; i++) {
			boolOneSearch = true;
			for(j in this.selectedValues) {
				if(this.selectedValues[j] !== this.offers[i].TREE[j]) {
					boolOneSearch = false;
					break;
				}
			}
			if(boolOneSearch) {
				index = i;
				break;
			}
		}
		if(-1 < index) {
			this.initOffersQuantityData(index);
			this.setPict(this.offers[index].ID);
			this.setPrice(this.offers[index].ID);
			this.setBuy(this.offers[index].ID);			
			this.offerNum = index;
		}
	};

	window.JCCatalogElementProps.prototype.setPict = function(offerId) {
		var pictItems = BX.findChildren(this.obPict, {className: "img"}, true);
		if(!!pictItems && 0 < pictItems.length) {
			for(i = 0; i < pictItems.length; i++) {
				BX.addClass(pictItems[i], "hidden");
			}
		}
		var curPictItem = BX("img_" + this.visual.ID + "_" + offerId);
		if(!!curPictItem)
			BX.removeClass(curPictItem, "hidden");
	};

	window.JCCatalogElementProps.prototype.setPrice = function(offerId) {
		var priceItems = BX.findChildren(this.obPrice, {className: "price"}, true);
		if(!!priceItems && 0 < priceItems.length) {
			for(i = 0; i < priceItems.length; i++) {
				BX.addClass(priceItems[i], "hidden");
			}
		}
		var curPriceItem = BX("price_" + this.visual.ID + "_" + offerId);
		if(!!curPriceItem)
			BX.removeClass(curPriceItem, "hidden");
	};

	window.JCCatalogElementProps.prototype.setBuy = function(offerId) {
		var buyItems = BX.findChildren(this.obBuy, {className: "buy_more"}, true);
		if(!!buyItems && 0 < buyItems.length) {
			for(i = 0; i < buyItems.length; i++) {
				BX.addClass(buyItems[i], "hidden");
			}
		}
		var curBuyItem = BX("buy_more_" + this.visual.ID + "_" + offerId);
		if(!!curBuyItem)
			BX.removeClass(curBuyItem, "hidden");
	};

	window.JCCatalogElementProps.prototype.OpenPriceRangesPopup = function() {
		var target = BX.proxy_context,
			offerItem = BX.findParent(target, {className: "catalog-item"}),
			minPrice,
			idPrice = Array(),
			colPrice = 0;
			
		if(!!offerItem)
			this.offerNum = offerItem.getAttribute("data-offer-num");
		this.initOffersQuantityData(this.offerNum);

		var parentVisualId = this.visual.ID,
			visualId = "price_ranges_" + this.visual.ID + "_" + this.offers[this.offerNum].ID;

		if(!!this.obPopupWin)
			this.obPopupWin.close();
		
		this.obPopupWin = BX.PopupWindowManager.create(visualId, null, {		
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,						
			draggable: false,
			closeByEsc: false,
			className: "pop-up price-ranges",
			closeIcon: { right : "-10px", top : "-10px"},			
			titleBar: false,
			events: {
				onAfterPopupShow: function()
				{
					BX(parentVisualId).style.display = "block";
					BX("popup-window-overlay-" + parentVisualId).style.display = "block";
				}
			}
		});
		
		var content = BX.create("div", {
			props: {					
				className: "price-ranges__block"
			}
		});
		for(var k in this.currentQuantityRanges) {
			if(this.currentQuantityRanges[k].HASH !== "ZERO-INF") {				
				for(var j in this.currentPrices) {
					if(this.currentPrices[j].QUANTITY_HASH === this.currentQuantityRanges[k].HASH) {
						break;
					}
				}
				if(!!this.currentPrices[j]) {
					content.appendChild(BX.create("div", {
						props: {					
							className: "price-ranges__row"
						},
						children: [
							BX.create("div", {
								props: {
									className: "price-ranges__sort"
								},
								html: !isFinite(this.currentQuantityRanges[k].SORT_TO) ? BX.message("PROPS_ELEMENT_FROM") + " " + this.currentQuantityRanges[k].SORT_FROM : this.currentQuantityRanges[k].SORT_FROM + " - " + this.currentQuantityRanges[k].SORT_TO
							}),
							BX.create("div", {
								props: {
									className: "price-ranges__dots"
								}
							}),
							BX.create("div", {
								props: {
									className: "price-ranges__price"
								},
								html: this.currentPrices[j].RATIO_PRICE
							}),
							BX.create("span", {
								props: {
									className: "unit"
								},
								html: this.currency
							})
						]
					}));
				}
			}
		}
		
		for(var k in this.currentPriceMatrixOffer[this.offerNum].COLS) {
			colPrice++;
			idPrice[colPrice-1] = k;
		}
		
		if(colPrice > 1) {
			content.appendChild(BX.create("div", {
				props: {					
					className: "price-ranges__block__matrix"
				}
			}));
			
			var colRange = BX.findChildren(BX(content),{className:"price-ranges__row"});
			
			var blockMatrix = BX.findChild(BX(content),{className: "price-ranges__block__matrix"});
			
			if(colRange.length == 0) {
				BX.adjust(BX(blockMatrix),{style:{"margin":"0"}});
			}
			
			for(var k in this.currentPriceMatrixOffer[this.offerNum].ROWS) {
				minPrice = k;
			}

			for(var k in this.currentPriceMatrixOffer[this.offerNum].COLS) {
				blockMatrix.appendChild(
					BX.create("div", {
						props: {					
							className: "price-ranges__row"
						},
						children: [
							BX.create("div", {
								props: {
									className: "price-ranges__sort"
								},
								html: this.currentPriceMatrixOffer[this.offerNum].COLS[k].NAME_LANG
							}),
							BX.create("div", {
								props: {
									className: "price-ranges__dots"
								}
							}),
							BX.create("span", {
								props: {
									className: "from"
								},
								html: (!isFinite(this.currentPriceMatrixOffer[this.offerNum][k][minPrice]) && this.currentPriceMatrixOffer[this.offerNum][k].length > 1) ? BX.message("PROPS_ELEMENT_FROM") : ""
							}),
							BX.create("div", {
								props: {
									className: "price-ranges__price"
								},
								html: this.currentPriceMatrixOffer[this.offerNum][k][minPrice].DISCOUNT_PRICE
							}),
							BX.create("span", {
								props: {
									className: "unit"
								},
								html: this.currentPriceMatrixOffer[this.offerNum][k][minPrice].PRINT_CURRENCY
							})
						]
					})
				);
			}
		}
		
		if(colPrice) {
			var matrixRange = BX.findChild(BX(blockMatrix),{className:"price-ranges__row"},true,true);
			for(var k in matrixRange){
				if(this.currentPriceMatrixOffer[this.offerNum][idPrice[k]].length > 1) {
					matrixRange[k].appendChild(
						BX.create("span", {
							props: {					
								className: "catalog-item-price-ranges-wrap"
							},
							children: [
								BX.create("a", {
									props: {
										className: "catalog-item-price-ranges",
										id: this.visual.ID + "_" + this.offers[this.offerNum].ID + "_price_ranges_btn_" + idPrice[k]
									},
									attrs :{
										"data-key" : idPrice[k]
									},
									children: [
										BX.create("i", {
											props: {
												className: "fa fa-question-circle-o"
											}
										})
									]
								})
							]
						})
					);
				}
			}
		}
		
		this.obPopupWin.setContent(content);
		
		var btnRange = BX.findChildren(BX(blockMatrix),{className:"catalog-item-price-ranges"},true);

		if(btnRange.length > 0) {
			for(var k in btnRange) {
				BX.bind(BX(btnRange[k]), "click", BX.delegate(this.OpenPriceMatrixPopup, this));
			}
		}
		
		var close = BX.findChild(BX(visualId), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";
		
		target.parentNode.appendChild(BX(visualId));
		
		this.obPopupWin.show();
	};
	
	window.JCCatalogElementProps.prototype.OpenPriceMatrixPopup = function() {
		var target = BX.proxy_context,
			key = target.getAttribute("data-key"),
			visualId;
			
		if(this.productType == "3") {
			visualId = "price_matrix_" + this.visual.ID + "_" + this.offers[this.offerNum].ID + "_" + key;
		} else {
			visualId = "price_matrix_" + this.visual.ID + "_" + key;
		}	
		
		if(!!this.obPopupWinMatrix)
			this.obPopupWinMatrix.close();

		this.obPopupWinMatrix = BX.PopupWindowManager.create(visualId, null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,			
			draggable: false,
			closeByEsc: false,
			className: "pop-up price-ranges",
			closeIcon: { right : "-10px", top : "-10px"},			
			titleBar: false
		});
				
		var content = BX.create("div", {
			props: {					
				className: "price-ranges__block"
			}
		});
		
		if(this.productType == "3") {
			for(var j in this.currentPriceMatrixOffer[this.offerNum][key]) {
				content.appendChild(BX.create("div", {
					props: {					
						className: "price-ranges__row"
					},
					children: [
						BX.create("div", {
							props: {
								className: "price-ranges__sort"
							},
							html: !isFinite(this.currentPriceMatrixOffer[this.offerNum][key][j].QUANTITY_TO) ? BX.message("PROPS_ELEMENT_FROM") + " " + this.currentPriceMatrixOffer[this.offerNum][key][j].QUANTITY_FROM : this.currentPriceMatrixOffer[this.offerNum][key][j].QUANTITY_FROM + " - " + this.currentPriceMatrixOffer[this.offerNum][key][j].QUANTITY_TO
						}),
						BX.create("div", {
							props: {
								className: "price-ranges__dots"
							}
						}),
						BX.create("div", {
							props: {
								className: "price-ranges__price"
							},
							html: this.currentPriceMatrixOffer[this.offerNum][key][j].DISCOUNT_PRICE
						}),
						BX.create("span", {
							props: {
								className: "unit"
							},
							html: this.currentPriceMatrixOffer[this.offerNum][key][j].PRINT_CURRENCY
						})
					]
				}));
			}
		} else {
			for(var k in this.currentPriceMatrix[key]) {
				content.appendChild(BX.create("div", {
					props: {					
						className: "price-ranges__row"
					},
					children: [
						BX.create("div", {
							props: {
								className: "price-ranges__sort"
							},
							html: !isFinite(this.currentPriceMatrix[key][k].QUANTITY_TO) ? BX.message("PROPS_ELEMENT_FROM") + " " + this.currentPriceMatrix[key][k].QUANTITY_FROM : this.currentPriceMatrix[key][k].QUANTITY_FROM + " - " + this.currentPriceMatrix[key][k].QUANTITY_TO
						}),
						BX.create("div", {
							props: {
								className: "price-ranges__dots"
							}
						}),
						BX.create("div", {
							props: {
								className: "price-ranges__price"
							},
							html: this.currentPriceMatrix[key][k].DISCOUNT_PRICE
						}),
						BX.create("span", {
							props: {
								className: "unit"
							},
							html: this.currentPriceMatrix[key][k].PRINT_CURRENCY
						})
					]
				}));
			}
		}
		
		this.obPopupWinMatrix.setContent(content);
		
		var close = BX.findChild(BX(visualId), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";
		
		BX.adjust(BX(target.parentNode),{children:[BX(this.obPopupWinMatrix.popupContainer)]});
		
		BX.adjust(BX(visualId),{style : {"display" : "block"}});
	};		

	window.JCCatalogElementProps.prototype.OpenFormPopup = function() {
		var target = BX.proxy_context,
			form = BX.findParent(target, {tagName: "form"}),
			action = !!form ? BX.findChild(form, {tagName: "input", attribute: {name: "ACTION"}}, true, false).value : target.getAttribute("data-action");

		if("LIST" == this.offersView) {		
			var offerItem = BX.findParent(target, {className: "catalog-item"});
			if(!!offerItem)
				this.offerNum = offerItem.getAttribute("data-offer-num");
		}
		var elementId = this.offers[this.offerNum].ID,
			elementNameInput = BX.findChild(form, {tagName: "input", attribute: {name: "NAME"}}, true, false),
			elementName = !!elementNameInput ? elementNameInput.value : "",			
			parentVisualId = this.visual.ID,
			visualId = this.visual.ID + "_" + elementId;
		
		if(!!this.obPopupWin)
			this.obPopupWin.close();
		
		this.obPopupWin = BX.PopupWindowManager.create(action + "_" + visualId, null, {
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
					if(!BX(action + "_" + visualId + "_form")) {
						BX.ajax.post(
							BX.message("PROPS_COMPONENT_TEMPLATE") + "/popup.php",
							{							
								sessid: BX.bitrix_sessid(),
								action: action,
								arParams: BX.message("PROPS_COMPONENT_PARAMS"),
								ELEMENT_ID: elementId,
								ELEMENT_AREA_ID: visualId,									
								ELEMENT_NAME: elementName
							},
							BX.delegate(function(result)
							{
								this.setContent(result);
								var windowSize =  BX.GetWindowInnerSize(),
								windowScroll = BX.GetWindowScrollPos(),
								popupHeight = BX(action + "_" + visualId).offsetHeight;
								BX(action + "_" + visualId).style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
							},
							this)
						);
					} else if(action == "boc") {
						//PROPS//
						var parentPropsInput = BX("props_" + visualId),
							bocPropsInput = BX.findChild(BX("boc_" + visualId + "_form"), {attribute: {name: "PROPS"}}, true, false);
						if(!!parentPropsInput && !!bocPropsInput)
							bocPropsInput.value = parentPropsInput.value;

						//SELECT_PROPS//
						var parentSelPropsInput = BX("select_props_" + visualId),
							bocSelPropsInput = BX.findChild(BX("boc_" + visualId + "_form"), {attribute: {name: "SELECT_PROPS"}}, true, false);
						if(!!parentSelPropsInput && !!bocSelPropsInput)
							bocSelPropsInput.value = parentSelPropsInput.value;

						//QUANTITY//
						var parentQntInput = BX("quantity_" + visualId),
							bocQntInput = BX.findChild(BX("boc_" + visualId + "_form"), {attribute: {name: "QUANTITY"}}, true, false);
						if(!!parentQntInput && !!bocQntInput)
							bocQntInput.value = parentQntInput.value;
					}
					BX(parentVisualId).style.display = "none";
					BX("popup-window-overlay-" + parentVisualId).style.display = "none";
				}
			}			
		});
		
		var close = BX.findChild(BX(action + "_" + visualId), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";

		this.obPopupWin.show();
	};
	
	window.JCCatalogElementProps.prototype.Add2Basket = function() {
		var target = BX.proxy_context,
			form = BX.findParent(target, {"tag" : "form"}),
			formInputs = BX.findChildren(form, {"tag" : "input"}, true);
		
		if(!!formInputs && 0 < formInputs.length) {
			for(i = 0; i < formInputs.length; i++) {
				this.basketParams[formInputs[i].getAttribute("name")] = formInputs[i].value;
			}
		}

		if("LIST" == this.offersView) {
			var offerItem = BX.findParent(target, {className: "catalog-item"});
			if(!!offerItem)
				this.offerNum = offerItem.getAttribute("data-offer-num");
		}
		
		BX.ajax.post(
			form.getAttribute("action"),			
			this.basketParams,			
			BX.delegate(function(result) {
				if(location.pathname != BX.message("PROPS_SITE_DIR") + "personal/cart/") {
					BX.ajax.post(
						BX.message("PROPS_SITE_DIR") + "ajax/basket_line.php",
						"",
						BX.delegate(function(data) {
							refreshCartLine(data);
						}, this)
					);
					BX.ajax.post(
						BX.message("PROPS_SITE_DIR") + "ajax/delay_line.php",
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
					html: "<i class='fa fa-check'></i><span>" + BX.message("PROPS_ADDITEMINCART_ADDED") + "</span>"
				});
				if(location.pathname != BX.message("PROPS_SITE_DIR") + "personal/cart/") {
					this.BasketResult();
				} else {
					this.BasketRedirect();
				}
			}, this)			
		);		
	};

	window.JCCatalogElementProps.prototype.BasketResult = function() {
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
			closeByEsc: true,
			className: "pop-up modal",
			closeIcon: {top: "-10px", right: "-10px"},
			titleBar: {content: BX.create("span", {html: BX.message("PROPS_POPUP_WINDOW_TITLE")})}			
		});
		
		close = BX.findChild(BX("addItemInCart"), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";

		switch(this.productType) {			
			case 1://product
			case 2://set			
				strPictSrc = this.product.pict.SRC;
				strPictWidth = this.product.pict.WIDTH;
				strPictHeight = this.product.pict.HEIGHT;
				break;
			case 3://sku
				strPictSrc = (!!this.offers[this.offerNum].PREVIEW_IMG ? this.offers[this.offerNum].PREVIEW_IMG.SRC : this.product.pict.SRC);
				strPictWidth = (!!this.offers[this.offerNum].PREVIEW_IMG ? this.offers[this.offerNum].PREVIEW_IMG.WIDTH : this.product.pict.WIDTH);
				strPictHeight = (!!this.offers[this.offerNum].PREVIEW_IMG ? this.offers[this.offerNum].PREVIEW_IMG.HEIGHT : this.product.pict.HEIGHT);
				break;
		}
		
		strContent = "<div class='cont'><div class='item_image_cont'><div class='item_image_full'><img src='" + strPictSrc + "' width='" + strPictWidth + "' height='" + strPictHeight + "' alt='"+ this.product.name +"' /></div></div><div class='item_title'>" + this.product.name + "</div></div>";

		buttons = [			
			new BasketButton({				
				text: BX.message("PROPS_POPUP_WINDOW_BTN_CLOSE"),
				name: "close",
				className: "btn_buy ppp close",
				events: {
					click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
				}
			}),
			new BasketButton({				
				text: BX.message("PROPS_POPUP_WINDOW_BTN_ORDER"),
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
	
	window.JCCatalogElementProps.prototype.pictZoom = function() {
		var target = BX.proxy_context,
			items = BX.findChildren(this.obProduct, {className: "catalog-item"}, true),
			parent = BX.findParent(BX(target),{className:"catalog-item"}),
			lightBox, lightBoxDetail, morePhoto;
			
		if(this.offers[parent.getAttribute("data-offer-num")].MORE_PHOTO.length > 0) {
			morePhoto = BX.create("div",{
				props:{
					className:"more_photo hidden"
				}
			});
		}
		
		for(var i in items) {
			if(items[i].getAttribute("data-offer-num") !== parent.getAttribute("data-offer-num")) {
				lightBox = BX.findChildren(BX(items[i]),{className:"fancybox"},true);
				for(var k in lightBox) {
					BX.adjust(BX(lightBox[k]),{props:{rel:""}});
				}
			} else {
				for(var k in this.offers[parent.getAttribute("data-offer-num")].MORE_PHOTO) {
					BX.adjust(BX(morePhoto),{
						children:[
							BX.create("a",{
								props:{
									className:"fancybox",
									rel:"lightbox",
									href:this.offers[parent.getAttribute("data-offer-num")].MORE_PHOTO[k].DETAIL.SRC
								}
							})
						]
					});
				};
				var itemBlock = BX.findChildren(BX(items[i]),{className:"catalog-item-image"},true)
				BX.adjust(BX(itemBlock[0]),{
					children:[
						BX(morePhoto)
					]
				});
			}
		}
		
		var detailPicture = BX.findChildren(BX(this.obProduct),{className:"catalog-detail-pictures"},true);
		lightBoxDetail = BX.findChildren(BX(detailPicture[0]),{className:"fancybox"},true);
		for(var k in lightBoxDetail) {
			BX.adjust(BX(lightBoxDetail[k]),{props:{rel:""}});
		}
		
		BX.bind(BX("fancybox-close"),"click",function(){
			for(var i in items) {
				if(items[i].getAttribute("data-offer-num") !== parent.getAttribute("data-offer-num")) {
					lightBox = BX.findChildren(BX(items[i]),{className:"fancybox"},true);
					for(var k in lightBox) {
						BX.adjust(BX(lightBox[k]),{props:{rel:"lightbox"}});
					}

				}
			}
			
			lightBoxDetail = BX.findChildren(BX(detailPicture[0]),{className:"fancybox"},true);
			for(var k in lightBoxDetail) {
				BX.adjust(BX(lightBoxDetail[k]),{props:{rel:"lightbox"}});
			}
			
			BX.remove(BX(morePhoto));
		})
		
		//FANCYBOX//
		$(".fancybox").fancybox({
			"transitionIn": "elastic",
			"transitionOut": "elastic",
			"speedIn": 600,
			"speedOut": 200,
			"overlayShow": false,
			"cyclic" : true,
			"padding": 20,
			"titlePosition": "over",
			"onComplete": function() {
				$("#fancybox-title").css({"top":"100%", "bottom":"auto"});
			} 
		});
	};

	window.JCCatalogElementProps.prototype.BasketRedirect = function() {
		location.href = BX.message("PROPS_SITE_DIR") + "personal/cart/";
	};
})(window);