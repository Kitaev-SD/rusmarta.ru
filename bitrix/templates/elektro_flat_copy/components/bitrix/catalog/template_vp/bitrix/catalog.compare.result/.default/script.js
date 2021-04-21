(function (window) {

	if(!!window.JCCatalogSection) {
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

	window.JCCatalogSection = function (arParams) {
		this.productType = 0;
		this.visual = {
			ID: '',
			PICT_ID: '',
			PRICE_ID: '',
			BUY_ID: '',
		};
		this.product = {
			name: '',
			id: 0,
			pict: {},
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
		this.basketParams = {};
			
		this.errorCode = 0;

		if('object' === typeof arParams) {
			this.productType = parseInt(arParams.PRODUCT_TYPE, 10);
			this.visual = arParams.VISUAL;

			switch (this.productType) {
				case 1://product
				case 2://set
					if(!!arParams.PRODUCT && 'object' === typeof(arParams.PRODUCT)) {
						this.product.name = arParams.PRODUCT.NAME;
						this.product.pict = arParams.PRODUCT.PICT;
						this.product.id = arParams.PRODUCT.ID;
						if(!!arParams.SELECT_PROPS) {
							this.selectProps = arParams.SELECT_PROPS;
						}
					} else {
						this.errorCode = -1;
					}
					break;
				case 3://sku
					if(!!arParams.OFFERS && BX.type.isArray(arParams.OFFERS)) {
						if(!!arParams.PRODUCT && 'object' === typeof(arParams.PRODUCT)) {
							this.product.name = arParams.PRODUCT.NAME;
							this.product.pict = arParams.PRODUCT.PICT;
							this.product.id = arParams.PRODUCT.ID;
							if(!!arParams.SELECT_PROPS) {
								this.selectProps = arParams.SELECT_PROPS;
							}
						}
						if(!!arParams.OFFERS_VIEW) {
							this.offersView = arParams.OFFERS_VIEW;
						}
						this.offers = arParams.OFFERS;
						this.offerNum = 0;
						if(!!arParams.OFFER_SELECTED) {
							this.offerNum = parseInt(arParams.OFFER_SELECTED, 10);
						}
						if(isNaN(this.offerNum)) {
							this.offerNum = 0;
						}
						if(!!arParams.TREE_PROPS) {
							this.treeProps = arParams.TREE_PROPS;
						}						
					} else {
						this.errorCode = -1;
					}
					break;
				default:
					this.errorCode = -1;
			}
		}
		if(0 === this.errorCode) {
			BX.ready(BX.delegate(this.Init,this));
		}
	};

	window.JCCatalogSection.prototype.Init = function() {
		var i = 0,
			strPrefix = '',
			selPrefix = '',
			TreeItems = null,
			SelectItems = null,
			buyBtnItems = null;

		this.obProduct = BX(this.visual.ID);
		if(!this.obProduct) {
			this.errorCode = -1;
		}		
		
		if(3 === this.productType) {
			if("LIST" !== this.offersView) {
				this.obPict = BX(this.visual.PICT_ID);
				if(!this.obPict) {
					this.errorCode = -16;
				}

				this.obPrice = BX(this.visual.PRICE_ID);
				if(!this.obPrice) {
					this.errorCode = -16;
				}

				this.obBuy = BX(this.visual.BUY_ID);
				if(!this.obBuy) {
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
							LIST: BX(strPrefix+this.treeProps[i].ID+'_list'),
							CONT: BX(strPrefix+this.treeProps[i].ID+'_cont')
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
			if(!this.obSelect) {
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
		
		if(!!this.visual.BTN_BUY_ID) {			
			this.obBuyBtn = BX(this.visual.BTN_BUY_ID);			
		}
		
		if(0 === this.errorCode) {
			switch (this.productType) {
				case 1://product
				case 2://set
					if(!!this.obSelect) {
						SelectItems = BX.findChildren(this.obSelect, {tagName: 'li'}, true);
						if(!!SelectItems && 0 < SelectItems.length) {
							for(i = 0; i < SelectItems.length; i++) {
								BX.bind(SelectItems[i], 'click', BX.delegate(this.SelectProp, this));
							}
							this.SetSelectCurrent();
						}
					}
					break;
				case 3://sku
					if("LIST" !== this.offersView) {
						TreeItems = BX.findChildren(this.obTree, {tagName: 'li'}, true);
						if(!!TreeItems && 0 < TreeItems.length) {
							for(i = 0; i < TreeItems.length; i++) {
								BX.bind(TreeItems[i], 'click', BX.delegate(this.SelectOfferProp, this));
							}
						}
						this.SetCurrent();
					}

					if(!!this.obSelect) {
						SelectItems = BX.findChildren(this.obSelect, {tagName: 'li'}, true);
						if(!!SelectItems && 0 < SelectItems.length) {
							for(i = 0; i < SelectItems.length; i++) {
								BX.bind(SelectItems[i], 'click', BX.delegate(this.SelectProp, this));
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
				if(!!this.obBuyBtn)
					BX.bind(this.obBuyBtn, "click", BX.delegate(this.Add2Basket, this));
				break;
			case 3://sku
				if("LIST" !== this.offersView) {
					if(!!this.obBuy) {
						buyBtnItems = BX.findChildren(this.obBuy, {tagName: "button"}, true);
						if(!!buyBtnItems && 0 < buyBtnItems.length) {
							for(i = 0; i < buyBtnItems.length; i++) {
								BX.bind(buyBtnItems[i], "click", BX.delegate(this.Add2Basket, this));
							}
						}					
					}
				} else {
					if(!!this.obProduct) {
						buyBtnItems = BX.findChildren(this.obProduct, {tagName: "button", attribute: {name: "add2basket"}}, true);
						if(!!buyBtnItems && 0 < buyBtnItems.length) {
							for(i = 0; i < buyBtnItems.length; i++) {
								BX.bind(buyBtnItems[i], "click", BX.delegate(this.Add2Basket, this));
							}
						}
					}
				}
				break;
		}		
	};

	window.JCCatalogSection.prototype.SelectProp = function() {
		var i = 0,
		RowItems = null,
		ActiveItems = null,
		selPropValueArr = [],		
		selPropValue = null,		
		MinselDelayOnclick = null,
		MinselDelayOnclickArr = [],
		MinselDelayOnclickNew = null,
		selDelayOnclick = null,
		selDelayOnclickArr = [],
		selDelayOnclickNew = null,
		target = BX.proxy_context;

		if(!!target && target.hasAttribute('data-select-onevalue')) {
			RowItems = BX.findChildren(target.parentNode, {tagName: 'li'}, false);
			if(!!RowItems && 0 < RowItems.length) {
				for(i = 0; i < RowItems.length; i++) {
					BX.removeClass(RowItems[i], 'active');
				}
			}
			BX.addClass(target, 'active');
		}

		ActiveItems = BX.findChildren(this.obSelect, {tagName: 'li', className: 'active'}, true);
		if(!!ActiveItems && 0 < ActiveItems.length) {
			for(i = 0; i < ActiveItems.length; i++) {
				selPropValueArr[i] = ActiveItems[i].getAttribute('data-select-onevalue');
			}
		}
		selPropValue = selPropValueArr.join('||');
		
		if(!!this.offers && 0 < this.offers.length) {
			for(i = 0; i < this.offers.length; i++) {
				/*CART*/
				if(!!BX('select_props_'+this.visual.ID+'_'+this.offers[i].ID))
					BX('select_props_'+this.visual.ID+'_'+this.offers[i].ID).value = selPropValue;				
				/*MIN_DELAY*/
				if(!!BX('catalog-item-delay-min-'+this.visual.ID+'-'+this.offers[i].ID)) {
					MinselDelayOnclick = BX('catalog-item-delay-min-'+this.visual.ID+'-'+this.offers[i].ID).getAttribute('onclick');
					MinselDelayOnclickArr = MinselDelayOnclick.split("',");
					MinselDelayOnclickArr[3] = " '"+selPropValue;
					MinselDelayOnclickNew = MinselDelayOnclickArr.join("',");
					BX('catalog-item-delay-min-'+this.visual.ID+'-'+this.offers[i].ID).setAttribute('onclick', MinselDelayOnclickNew);					
				}
				/*DELAY*/
				if(!!BX('catalog-item-delay-'+this.visual.ID+'-'+this.offers[i].ID)) {
					selDelayOnclick = BX('catalog-item-delay-'+this.visual.ID+'-'+this.offers[i].ID).getAttribute('onclick');
					selDelayOnclickArr = selDelayOnclick.split("',");
					selDelayOnclickArr[3] = " '"+selPropValue;
					selDelayOnclickNew = selDelayOnclickArr.join("',");
					BX('catalog-item-delay-'+this.visual.ID+'-'+this.offers[i].ID).setAttribute('onclick', selDelayOnclickNew);
				}				
			}
		} else {
			/*CART*/
			if(!!BX('select_props_'+this.visual.ID))
				BX('select_props_'+this.visual.ID).value = selPropValue;
			/*DELAY*/
			if(!!BX('catalog-item-delay-'+this.visual.ID)) {
				selDelayOnclick = BX('catalog-item-delay-'+this.visual.ID).getAttribute('onclick');
				selDelayOnclickArr = selDelayOnclick.split("',");
				selDelayOnclickArr[3] = " '"+selPropValue;
				selDelayOnclickNew = selDelayOnclickArr.join("',");
				BX('catalog-item-delay-'+this.visual.ID).setAttribute('onclick', selDelayOnclickNew);
			}
		}
	}

	window.JCCatalogSection.prototype.SelectOfferProp = function() {
		var i = 0,
		value = '',
		strTreeValue = '',
		arTreeItem = [],
		RowItems = null,
		target = BX.proxy_context;

		if(!!target && target.hasAttribute('data-treevalue')) {
			strTreeValue = target.getAttribute('data-treevalue');
			arTreeItem = strTreeValue.split('_');
			if(this.SearchOfferPropIndex(arTreeItem[0], arTreeItem[1])) {
				RowItems = BX.findChildren(target.parentNode, {tagName: 'li'}, false);
				if(!!RowItems && 0 < RowItems.length) {
					for(i = 0; i < RowItems.length; i++) {
						value = RowItems[i].getAttribute('data-onevalue');
						if(value === arTreeItem[1]) {
							BX.addClass(RowItems[i], 'active');
						} else {
							BX.removeClass(RowItems[i], 'active');
						}
					}
				}
			}
		}
	};
	
	window.JCCatalogSection.prototype.SearchOfferPropIndex = function(strPropID, strPropValue) {
		var strName = '',
		arShowValues = false,
		i, j,
		arCanBuyValues = [],
		allValues = [],
		index = -1,
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
				strName = 'PROP_'+this.treeProps[i].ID;
				arFilter[strName] = this.selectedValues[strName];
			}
			strName = 'PROP_'+this.treeProps[index].ID;
			arShowValues = this.GetRowValues(arFilter, strName);
			if(!arShowValues) {
				return false;
			}
			if(!BX.util.in_array(strPropValue, arShowValues)) {
				return false;
			}
			arFilter[strName] = strPropValue;
			for(i = index+1; i < this.treeProps.length; i++) {
				strName = 'PROP_'+this.treeProps[i].ID;
				arShowValues = this.GetRowValues(arFilter, strName);
				if(!arShowValues) {
					return false;
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
		return true;
	};
	
	window.JCCatalogSection.prototype.UpdateRow = function(intNumber, activeID, showID, canBuyID) {
		var i = 0,
		showI = 0,
		value = '',
		obData = {},
		isCurrent = false,
		selectIndex = 0,
		RowItems = null;
		
		if(-1 < intNumber && intNumber < this.obTreeRows.length) {
			RowItems = BX.findChildren(this.obTreeRows[intNumber].LIST, {tagName: 'li'}, false);
			if(!!RowItems && 0 < RowItems.length) {
				obData = {
					props: { className: '' },
					style: {}
				};
				for(i = 0; i < RowItems.length; i++) {
					value = RowItems[i].getAttribute('data-onevalue');
					isCurrent = (value === activeID);
					if(BX.util.in_array(value, canBuyID)) {
						obData.props.className = (isCurrent ? 'active' : '');
					} else {
						obData.props.className = (isCurrent ? 'active disabled' : 'disabled');
					}
					obData.style.display = 'none';
					if(BX.util.in_array(value, showID)) {
						obData.style.display = '';
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

	window.JCCatalogSection.prototype.GetRowValues = function(arFilter, index) {
		var i = 0,
		j,
		arValues = [],
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

	window.JCCatalogSection.prototype.GetCanBuy = function(arFilter) {
		var i = 0,
			j,
			boolSearch = false,
			boolOneSearch = true;
		
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

	window.JCCatalogSection.prototype.SetSelectCurrent = function() {
		var i = 0,
		SelectItems = null,
		selPropValueArr = [],		
		selPropValue = null,		
		MinselDelayOnclick = null,
		MinselDelayOnclickArr = [],
		MinselDelayOnclickNew = null,
		selDelayOnclick = null,
		selDelayOnclickArr = [],
		selDelayOnclickNew = null;		
		
		for(i = 0; i < this.obSelectRows.length; i++) {
			SelectItems = BX.findChildren(this.obSelectRows[i], {tagName: 'li'}, true);
			if(!!SelectItems && 0 < SelectItems.length) {
				BX.addClass(SelectItems[0], 'active');
				selPropValueArr[i] = SelectItems[0].getAttribute('data-select-onevalue');
			}
		}
		selPropValue = selPropValueArr.join('||');
		
		if(!!this.offers && 0 < this.offers.length) {
			for(i = 0; i < this.offers.length; i++) {
				/*CART*/
				if(!!BX('select_props_'+this.visual.ID+'_'+this.offers[i].ID))
					BX('select_props_'+this.visual.ID+'_'+this.offers[i].ID).value = selPropValue;
				/*MIN_DELAY*/
				if(!!BX('catalog-item-delay-min-'+this.visual.ID+'-'+this.offers[i].ID)) {
					MinselDelayOnclick = BX('catalog-item-delay-min-'+this.visual.ID+'-'+this.offers[i].ID).getAttribute('onclick');
					MinselDelayOnclickArr = MinselDelayOnclick.split("',");
					MinselDelayOnclickArr[3] = " '"+selPropValue;
					MinselDelayOnclickNew = MinselDelayOnclickArr.join("',");
					BX('catalog-item-delay-min-'+this.visual.ID+'-'+this.offers[i].ID).setAttribute('onclick', MinselDelayOnclickNew);					
				}
				/*DELAY*/
				if(!!BX('catalog-item-delay-'+this.visual.ID+'-'+this.offers[i].ID)) {
					selDelayOnclick = BX('catalog-item-delay-'+this.visual.ID+'-'+this.offers[i].ID).getAttribute('onclick');
					selDelayOnclickArr = selDelayOnclick.split("',");
					selDelayOnclickArr[3] = " '"+selPropValue;
					selDelayOnclickNew = selDelayOnclickArr.join("',");
					BX('catalog-item-delay-'+this.visual.ID+'-'+this.offers[i].ID).setAttribute('onclick', selDelayOnclickNew);
				}
			}
		} else {
			/*CART*/
			if(!!BX('select_props_'+this.visual.ID))
				BX('select_props_'+this.visual.ID).value = selPropValue;
			/*DELAY*/
			if(!!BX('catalog-item-delay-'+this.visual.ID)) {
				selDelayOnclick = BX('catalog-item-delay-'+this.visual.ID).getAttribute('onclick');
				selDelayOnclickArr = selDelayOnclick.split("',");
				selDelayOnclickArr[3] = " '"+selPropValue;
				selDelayOnclickNew = selDelayOnclickArr.join("',");
				BX('catalog-item-delay-'+this.visual.ID).setAttribute('onclick', selDelayOnclickNew);
			}
		}
	}

	window.JCCatalogSection.prototype.SetCurrent = function() {
		var i = 0,
		j = 0,
		arCanBuyValues = [],
		strName = '',
		arShowValues = false,
		arFilter = {},
		tmpFilter = [],
		current = this.offers[this.offerNum].TREE;

		for(i = 0; i < this.treeProps.length; i++) {
			strName = 'PROP_'+this.treeProps[i].ID;
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
		this.ChangeInfo();
	};

	window.JCCatalogSection.prototype.ChangeInfo = function() {
		var i = 0,
		j,
		index = -1,
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
			this.setPict(this.visual.ID, this.offers[index].ID);
			this.setPrice(this.visual.ID, this.offers[index].ID);
			this.setBuy(this.visual.ID, this.offers[index].ID);
			this.offerNum = index;
		}
	};

	window.JCCatalogSection.prototype.setPict = function(visual_id, offer_id) {
		PictItems = BX.findChildren(this.obPict, {className: 'img'}, true);
		if(!!PictItems && 0 < PictItems.length) {
			for(i = 0; i < PictItems.length; i++) {
				BX.addClass(PictItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('img_'+visual_id+'_'+offer_id), 'hidden');
	};

	window.JCCatalogSection.prototype.setPrice = function(visual_id, offer_id) {
		PriceItems = BX.findChildren(this.obPrice, {className: 'price'}, true);
		if(!!PriceItems && 0 < PriceItems.length) {
			for(i = 0; i < PriceItems.length; i++) {
				BX.addClass(PriceItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('price_'+visual_id+'_'+offer_id), 'hidden');
	};

	window.JCCatalogSection.prototype.setBuy = function(visual_id, offer_id) {
		BuyItems = BX.findChildren(this.obBuy, {className: 'buy_more'}, true);
		if(!!BuyItems && 0 < BuyItems.length) {
			for(i = 0; i < BuyItems.length; i++) {
				BX.addClass(BuyItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('buy_more_'+visual_id+'_'+offer_id), 'hidden');
	};

	window.JCCatalogSection.prototype.Add2Basket = function() {
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
				BX.ajax.post(
					BX.message("COMPARE_SITE_DIR") + "ajax/basket_line.php",
					"",
					BX.delegate(function(data) {
						refreshCartLine(data);
					}, this)
				);
				BX.ajax.post(
					BX.message("COMPARE_SITE_DIR") + "ajax/delay_line.php",
					"",
					BX.delegate(function(data) {
						var delayLine = BX.findChildren(document.body, {className: "delay_line"}, true);
						if(!!delayLine && 0 < delayLine.length) {
							for(i = 0; i < delayLine.length; i++) {
								delayLine[i].innerHTML = data;
							}
						}						
					}, this)
				);
				BX.adjust(target, {
					props: {disabled: true},
					html: "<i class='fa fa-check'></i><span>" + BX.message("COMPARE_ADDITEMINCART_ADDED") + "</span>"
				});
				this.BasketResult();
			}, this)			
		);		
	};

	window.JCCatalogSection.prototype.BasketResult = function() {
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
			closeIcon: {top: "-10px", right: "-10px"},
			titleBar: {content: BX.create("span", {html: BX.message("COMPARE_POPUP_WINDOW_TITLE")})}			
		});
		
		BX.addClass(BX("addItemInCart"), "pop-up modal");
		close = BX.findChildren(BX("addItemInCart"), {className: "popup-window-close-icon"}, true);
		if(!!close && 0 < close.length) {
			for(i = 0; i < close.length; i++) {					
				close[i].innerHTML = "<i class='fa fa-times'></i>";
			}
		}

		switch(this.productType) {
			case 1://product
			case 2://set
				strPictSrc = this.product.pict.SRC;
				strPictWidth = this.product.pict.WIDTH;
				strPictHeight = this.product.pict.HEIGHT;
				break;
			case 3://sku
				strPictSrc = (!!this.offers[this.offerNum].PREVIEW_PICTURE ? this.offers[this.offerNum].PREVIEW_PICTURE.SRC : this.product.pict.SRC);
				strPictWidth = (!!this.offers[this.offerNum].PREVIEW_PICTURE ? this.offers[this.offerNum].PREVIEW_PICTURE.WIDTH : this.product.pict.WIDTH);
				strPictHeight = (!!this.offers[this.offerNum].PREVIEW_PICTURE ? this.offers[this.offerNum].PREVIEW_PICTURE.HEIGHT : this.product.pict.HEIGHT);
				break;
		}
		
		strContent = "<div class='cont'><div class='item_image_cont'><div class='item_image_full'><img src='" + strPictSrc + "' width='" + strPictWidth + "' height='" + strPictHeight + "' alt='"+ this.product.name +"' /></div></div><div class='item_title'>" + this.product.name + "</div></div>";

		buttons = [			
			new BasketButton({				
				text: BX.message("COMPARE_POPUP_WINDOW_BTN_CLOSE"),
				name: "close",
				className: "btn_buy ppp close",
				events: {
					click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
				}
			}),
			new BasketButton({				
				text: BX.message("COMPARE_POPUP_WINDOW_BTN_ORDER"),
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

	window.JCCatalogSection.prototype.BasketRedirect = function() {
		location.href = BX.message("COMPARE_SITE_DIR") + "personal/cart/";
	};
})(window);