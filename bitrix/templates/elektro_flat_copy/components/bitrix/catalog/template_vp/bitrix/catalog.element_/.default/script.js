(function (window) {

	if(!!window.JCCatalogElement) {
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

	window.JCCatalogElement = function (arParams) {
		this.productType = 0;

		this.config = {
			useCatalog: true,
		};

		this.currentIsSet = false;
		this.updateViewedCount = false;

		this.visual = {
			ID: '',
			PICT_ID: '',
			PRICE_ID: '',
			BUY_ID: '',
			DELAY_ID: '',
			DELIVERY_ID: '',
			ARTICLE_ID: '',
			PROPERTIES_ID: '',
			CONSTRUCTOR_ID: '',
			STORE_ID: '',
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
		this.obDelay = null;
		this.obDelivery = null;
		this.obArticle = null;
		this.obProperties = null;
		this.obTree = null;
		this.obSelect = null;
		this.obBuyBtn = null;
		this.obConstructor = null;
		this.obStore = null;
		
		this.viewedCounter = {
			path: '/bitrix/components/bitrix/catalog.element/ajax.php',
			params: {
				AJAX: 'Y',
				SITE_ID: '',
				PRODUCT_ID: 0,
				PARENT_ID: 0
			}
		};

		this.obPopupWin = null;
		this.basketParams = {};

		this.errorCode = 0;

		if(typeof arParams === 'object') {
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

	window.JCCatalogElement.prototype.Init = function() {
		var i = 0,
		strPrefix = '',
		selPrefix = '',
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

				this.obDelay = BX(this.visual.DELAY_ID);
				if(!this.obDelay && this.config.useCatalog) {
					this.errorCode = -16;
				}

				this.obDelivery = BX(this.visual.DELIVERY_ID);
				if(!this.obDelivery && this.config.useCatalog) {
					this.errorCode = -16;
				}

				this.obArticle = BX(this.visual.ARTICLE_ID);
				if(!this.obArticle && this.config.useCatalog) {
					this.errorCode = -16;
				}

				this.obProperties = BX(this.visual.PROPERTIES_ID);
				if(!this.obProperties && this.config.useCatalog) {
					this.errorCode = -16;
				}

				this.obConstructor = BX(this.visual.CONSTRUCTOR_ID);
				if(!this.obConstructor && this.config.useCatalog) {
					this.errorCode = -16;
				}

				this.obStore = BX(this.visual.STORE_ID);
				if(!this.obStore && this.config.useCatalog) {
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

		if(!!this.visual.BTN_BUY_ID) {			
			this.obBuyBtn = BX(this.visual.BTN_BUY_ID);			
		}
		
		if(0 === this.errorCode) {
			switch(this.productType) {				
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
						buyBtnItems = BX.findChildren(this.obBuy, {tagName: "button", attribute: {name: "add2basket"}}, true);
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

	window.JCCatalogElement.prototype.initConfig = function() {
		this.productType = parseInt(this.params.PRODUCT_TYPE, 10);
		if(!!this.params.CONFIG && typeof(this.params.CONFIG) === 'object') {
			if(this.params.CONFIG.USE_CATALOG !== 'undefined' && BX.type.isBoolean(this.params.CONFIG.USE_CATALOG)) {
				this.config.useCatalog = this.params.CONFIG.USE_CATALOG;
			}
		} else {
			// old version
			if(this.params.USE_CATALOG !== 'undefined' && BX.type.isBoolean(this.params.USE_CATALOG)) {
				this.config.useCatalog = this.params.USE_CATALOG;
			}
		}
		if(!this.params.VISUAL || typeof(this.params.VISUAL) !== 'object' || !this.params.VISUAL.ID) {
			this.errorCode = -1;
			return;
		}
		this.visual = this.params.VISUAL;
	};

	window.JCCatalogElement.prototype.initProductData = function() {
		if(!!this.params.PRODUCT && 'object' === typeof(this.params.PRODUCT)) {
			this.product.id = this.params.PRODUCT.ID;
			this.product.name = this.params.PRODUCT.NAME;
			this.product.pict = this.params.PRODUCT.PICT;
			if(!!this.params.SELECT_PROPS) {
				this.selectProps = this.params.SELECT_PROPS;
			}
		} else {
			this.errorCode = -1;
		}
	};

	window.JCCatalogElement.prototype.initOffersData = function() {
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
			if(!!this.params.PRODUCT && typeof(this.params.PRODUCT) === 'object') {
				this.product.id = parseInt(this.params.PRODUCT.ID, 10);
				this.product.name = this.params.PRODUCT.NAME;
				this.product.pict = this.params.PRODUCT.PICT;
				if(!!this.params.SELECT_PROPS) {
					this.selectProps = this.params.SELECT_PROPS;
				}
			}	
		} else {
			this.errorCode = -1;
		}
	};

	window.JCCatalogElement.prototype.SelectProp = function() {
		var i = 0,
		RowItems = null,
		ActiveItems = null,
		selPropValueArr = [],		
		selPropValue = null,
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
	};

	window.JCCatalogElement.prototype.SelectOfferProp = function() {
		var i = 0,
		strTreeValue = '',
		arTreeItem = [],
		RowItems = null,
		target = BX.proxy_context;

		if(!!target && target.hasAttribute('data-treevalue')) {
			strTreeValue = target.getAttribute('data-treevalue');
			arTreeItem = strTreeValue.split('_');
			this.SearchOfferPropIndex(arTreeItem[0], arTreeItem[1]);
			RowItems = BX.findChildren(target.parentNode, {tagName: 'li'}, false);
			if(!!RowItems && 0 < RowItems.length) {
				for(i = 0; i < RowItems.length; i++) {
					BX.removeClass(RowItems[i], 'active');
				}
			}
			BX.addClass(target, 'active');
		}
	};

	window.JCCatalogElement.prototype.SearchOfferPropIndex = function(strPropID, strPropValue) {
		var strName = '',
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
				strName = 'PROP_'+this.treeProps[i].ID;
				arFilter[strName] = this.selectedValues[strName];
			}
			strName = 'PROP_'+this.treeProps[index].ID;
			arFilter[strName] = strPropValue;
			for(i = index+1; i < this.treeProps.length; i++) {
				strName = 'PROP_'+this.treeProps[i].ID;
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

	window.JCCatalogElement.prototype.UpdateRow = function(intNumber, activeID, showID, canBuyID) {
		var i = 0,
		showI = 0,
		value = '',
		obData = {},
		RowItems = null,
		isCurrent = false,
		selectIndex = 0;
		
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

	window.JCCatalogElement.prototype.GetRowValues = function(arFilter, index) {
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

	window.JCCatalogElement.prototype.GetCanBuy = function(arFilter) {
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

	window.JCCatalogElement.prototype.SetSelectCurrent = function() {
		var i = 0,
		SelectItems = null,
		selPropValueArr = [],
		selPropValue = null,
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
				if(!!BX('select_props_'+this.offers[i].ID))
					BX('select_props_'+this.offers[i].ID).value = selPropValue;				
				/*DELAY*/
				if(!!BX('catalog-item-delay-'+this.offers[i].ID)) {
					selDelayOnclick = BX('catalog-item-delay-'+this.offers[i].ID).getAttribute('onclick');
					selDelayOnclickArr = selDelayOnclick.split("',");
					selDelayOnclickArr[3] = " '"+selPropValue;
					selDelayOnclickNew = selDelayOnclickArr.join("',");
					BX('catalog-item-delay-'+this.offers[i].ID).setAttribute('onclick', selDelayOnclickNew);
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

	window.JCCatalogElement.prototype.SetCurrent = function() {
		var i = 0,
		j = 0,
		strName = '',
		arShowValues = false,
		arCanBuyValues = [],
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

	window.JCCatalogElement.prototype.ChangeInfo = function() {
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
			this.setPict(this.visual.ID, this.offers[index].ID);
			this.setPrice(this.visual.ID, this.offers[index].ID);
			this.setBuy(this.visual.ID, this.offers[index].ID);
			this.setDelay(this.visual.ID, this.offers[index].ID);
			this.setDelivery(this.visual.ID, this.offers[index].ID);
			this.setArticle(this.visual.ID, this.offers[index].ID);
			this.setProperties(this.visual.ID, this.offers[index].ID);
			this.setConstructor(this.visual.ID, this.offers[index].ID);
			this.setStore(this.visual.ID, this.offers[index].ID);
			this.offerNum = index;
			this.incViewedCounter();
			BX.onCustomEvent('onCatalogStoreProductChange', [this.offers[this.offerNum].ID]);
		}
	};

	window.JCCatalogElement.prototype.setPict = function(visual_id, offer_id) {
		PictItems = BX.findChildren(this.obPict, {className: 'detail_picture'}, true);
		if(!!PictItems && 0 < PictItems.length) {
			for(i = 0; i < PictItems.length; i++) {
				BX.addClass(PictItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('detail_picture_'+visual_id+'_'+offer_id), 'hidden');
		
		PictItemsA = BX.findChildren(this.obPict, {className: 'catalog-detail-images'}, true);
		if(!!PictItemsA && 0 < PictItemsA.length) {
			for(i = 0; i < PictItemsA.length; i++) {
				BX.adjust(PictItemsA[i], {props: {rel: ''}});
			}
		}
		BX.adjust(BX('catalog-detail-images-'+visual_id+'-'+offer_id), {props: {rel: 'lightbox'}});
	};

	window.JCCatalogElement.prototype.setPrice = function(visual_id, offer_id) {
		PriceItems = BX.findChildren(this.obPrice, {className: 'detail_price'}, true);
		if(!!PriceItems && 0 < PriceItems.length) {
			for(i = 0; i < PriceItems.length; i++) {
				BX.addClass(PriceItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('detail_price_'+visual_id+'_'+offer_id), 'hidden');
	};

	window.JCCatalogElement.prototype.setBuy = function(visual_id, offer_id) {
		BuyItems = BX.findChildren(this.obBuy, {className: 'buy_more_detail'}, true);
		if(!!BuyItems && 0 < BuyItems.length) {
			for(i = 0; i < BuyItems.length; i++) {
				BX.addClass(BuyItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('buy_more_detail_'+visual_id+'_'+offer_id), 'hidden');
	};

	window.JCCatalogElement.prototype.setDelay = function(visual_id, offer_id) {
		DelayItems = BX.findChildren(this.obDelay, {className: 'delay'}, true);
		if(!!DelayItems && 0 < DelayItems.length) {
			for(i = 0; i < DelayItems.length; i++) {
				BX.addClass(DelayItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('delay_'+visual_id+'_'+offer_id), 'hidden');
	};

	window.JCCatalogElement.prototype.setDelivery = function(visual_id, offer_id) {
		DeliveryItems = BX.findChildren(this.obDelivery, {className: 'geolocation-delivery-from'}, true);
		if(!!DeliveryItems && 0 < DeliveryItems.length) {
			for(i = 0; i < DeliveryItems.length; i++) {
				BX.addClass(DeliveryItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('geolocation-delivery-from-'+visual_id+'-'+offer_id), 'hidden');
	};

	window.JCCatalogElement.prototype.setArticle = function(visual_id, offer_id) {
		ArticleItems = BX.findChildren(this.obArticle, {className: 'article'}, true);
		if(!!ArticleItems && 0 < ArticleItems.length) {
			for(i = 0; i < ArticleItems.length; i++) {
				BX.addClass(ArticleItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('article_'+visual_id+'_'+offer_id), 'hidden');
	};

	window.JCCatalogElement.prototype.setProperties = function(visual_id, offer_id) {
		PropertiesItems = BX.findChildren(this.obProperties, {className: 'offer-property'}, true);
		if(!!PropertiesItems && 0 < PropertiesItems.length) {
			for(i = 0; i < PropertiesItems.length; i++) {
				BX.addClass(PropertiesItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('offer-property_'+visual_id+'_'+offer_id), 'hidden');
	};

	window.JCCatalogElement.prototype.setConstructor = function(visual_id, offer_id) {
		ConstructorItems = BX.findChildren(this.obConstructor, {className: 'set-constructor-from'}, true);
		if(!!ConstructorItems && 0 < ConstructorItems.length) {
			for(i = 0; i < ConstructorItems.length; i++) {
				BX.addClass(ConstructorItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('set-constructor-from-'+visual_id+'-'+offer_id), 'hidden');
	};

	window.JCCatalogElement.prototype.setStore = function(visual_id, offer_id) {
		StoreItems = BX.findChildren(this.obStore, {className: 'catalog-detail-stores-from'}, true);
		if(!!StoreItems && 0 < StoreItems.length) {
			for(i = 0; i < StoreItems.length; i++) {
				BX.addClass(StoreItems[i], 'hidden');
			}
		}
		BX.removeClass(BX('catalog-detail-stores-from-'+visual_id+'-'+offer_id), 'hidden');
	};

	window.JCCatalogElement.prototype.Add2Basket = function() {
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
					BX.message("DETAIL_SITE_DIR") + "ajax/basket_line.php",
					"",
					BX.delegate(function(data) {
						refreshCartLine(data);
					}, this)
				);
				BX.ajax.post(
					BX.message("DETAIL_SITE_DIR") + "ajax/delay_line.php",
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
					html: "<i class='fa fa-check'></i><span>" + BX.message("DETAIL_ADDITEMINCART_ADDED") + "</span>"
				});
				this.BasketResult();
			}, this)			
		);		
	};

	window.JCCatalogElement.prototype.BasketResult = function() {
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
			titleBar: {content: BX.create("span", {html: BX.message("DETAIL_POPUP_WINDOW_TITLE")})}			
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
				strPictSrc = (!!this.offers[this.offerNum].PREVIEW_IMG ? this.offers[this.offerNum].PREVIEW_IMG.SRC : this.product.pict.SRC);
				strPictWidth = (!!this.offers[this.offerNum].PREVIEW_IMG ? this.offers[this.offerNum].PREVIEW_IMG.WIDTH : this.product.pict.WIDTH);
				strPictHeight = (!!this.offers[this.offerNum].PREVIEW_IMG ? this.offers[this.offerNum].PREVIEW_IMG.HEIGHT : this.product.pict.HEIGHT);
				break;
		}
		
		strContent = "<div class='cont'><div class='item_image_cont'><div class='item_image_full'><img src='" + strPictSrc + "' width='" + strPictWidth + "' height='" + strPictHeight + "' alt='"+ this.product.name +"' /></div></div><div class='item_title'>" + this.product.name + "</div></div>";

		buttons = [			
			new BasketButton({				
				text: BX.message("DETAIL_POPUP_WINDOW_BTN_CLOSE"),
				name: "close",
				className: "btn_buy ppp close",
				events: {
					click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
				}
			}),
			new BasketButton({				
				text: BX.message("DETAIL_POPUP_WINDOW_BTN_ORDER"),
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

	window.JCCatalogElement.prototype.BasketRedirect = function() {
		location.href = BX.message("DETAIL_SITE_DIR") + "personal/cart/";
	};

	window.JCCatalogElement.prototype.incViewedCounter = function() {
		if(this.currentIsSet && !this.updateViewedCount) {
			switch(this.productType) {
				case 1://product
				case 2://set	
					this.viewedCounter.params.PRODUCT_ID = this.product.id;
					this.viewedCounter.params.PARENT_ID = this.product.id;
					break;
				case 3://sku
					this.viewedCounter.params.PARENT_ID = this.product.id;
					this.viewedCounter.params.PRODUCT_ID = this.offers[this.offerNum].ID;
					break;
				default:
					return;
			}
			this.viewedCounter.params.SITE_ID = BX.message("DETAIL_SITE_ID");
			this.updateViewedCount = true;
			BX.ajax.post(
				this.viewedCounter.path,
				this.viewedCounter.params,
				BX.delegate(function(){ this.updateViewedCount = false; }, this)
			);
		}
	};

	window.JCCatalogElement.prototype.allowViewedCount = function(update) {
		update = !!update;
		this.currentIsSet = true;
		if(update) {
			this.incViewedCounter();
		}
	};
})(window);