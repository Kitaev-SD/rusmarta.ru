BasketPoolQuantity = function() {
	this.processing = false;
	this.poolQuantity = {};
	this.updateTimer = null;
	this.currentQuantity = {};
	this.lastStableQuantities = {};

	this.updateQuantity();
};

BasketPoolQuantity.prototype.updateQuantity = function() {
	var items = BX.findChildren(BX('cart_equipment'), {className: 'tr'}, true);
	if(!!items && items.length > 0) {
		for(var i = 0; items.length > i; i++) {
			var itemId = items[i].id;
			this.currentQuantity[itemId] = BX('QUANTITY_' + itemId).value;
		}
	}

    if(parseInt(BX("min_price_vlue").value)> parseInt(BX("total_price_basket").value)){       
          BX.removeClass(BX("min_price_message"), 'disN');
          BX.addClass(BX("min_price_message"), 'disB');          
          BX("boc_anch_cart").disabled = true;
          BX("btn_basket_order").disabled = true;      

    }else{
          BX("boc_anch_cart").disabled = false;
          BX("btn_basket_order").disabled = false;
          BX.removeClass(BX("min_price_message"), 'disB');
          BX.addClass(BX("min_price_message"), 'disN');
    }
    
	this.lastStableQuantities = BX.clone(this.currentQuantity, true);
};

BasketPoolQuantity.prototype.changeQuantity = function(itemId) {
	var quantity = BX('QUANTITY_' + itemId).value;
	var isPoolEmpty = this.isPoolEmpty();

	if(this.currentQuantity[itemId] && this.currentQuantity[itemId] != quantity) {
		this.poolQuantity[itemId] = this.currentQuantity[itemId] = quantity;
	}

	if(!isPoolEmpty) {
		this.enableTimer(true);
	} else {
		this.trySendPool();
	}
};

BasketPoolQuantity.prototype.trySendPool = function() {
	if(!this.isPoolEmpty() && !this.isProcessing()) {
		this.enableTimer(false);
		recalcBasketAjax({});
	}
};

BasketPoolQuantity.prototype.isPoolEmpty = function() {
	return(Object.keys(this.poolQuantity).length == 0);
};

BasketPoolQuantity.prototype.clearPool = function() {
	this.poolQuantity = {};
};

BasketPoolQuantity.prototype.isProcessing = function() {
	return(this.processing === true);
};

BasketPoolQuantity.prototype.setProcessing = function(value) {
	this.processing = (value === true);
};

BasketPoolQuantity.prototype.enableTimer = function(value) {
	clearTimeout(this.updateTimer);
	if(value === false)
		return;
	this.updateTimer = setTimeout(function(){ basketPoolQuantity.trySendPool(); }, 1500);
};

function updateBasketTable(basketItemId, res) {
	//update product params after recalculation
	if(!!res.BASKET_DATA) {
		for(id in res.BASKET_DATA.GRID.ROWS) {
			if(res.BASKET_DATA.GRID.ROWS.hasOwnProperty(id)) {
				var item = res.BASKET_DATA.GRID.ROWS[id];				

				//ITEM_PRICE//
				if(BX("price_" + id))
					BX("price_" + id).innerHTML = item.PRICE_FORMATED;

				//ITEM_REFERENCE_PRICE//
				if(BX("reference-price_" + id)) {
					var itemReferenceCont = $(BX("reference-price_" + id)),
						itemReferenceCoef = itemReferenceCont.data("reference-coef"),
						dec_point = itemReferenceCont.data("dec-point"),
						thousands_sep = itemReferenceCont.data("separator"),
						decimals = itemReferenceCont.data("reference-decimal");
					if(itemReferenceCont.data("hide-zero") == "Y") {			
						if(Math.abs(parseFloat(item.PRICE * itemReferenceCoef).toFixed(decimals)) == Math.abs(parseFloat(item.PRICE * itemReferenceCoef).toFixed(0))) {						
							decimals = 0;								
						}
					}
					BX("itemReferenceVal_" + id).innerHTML = number_format(item.PRICE * itemReferenceCoef, decimals, dec_point, thousands_sep);
				}
				
				//ITEM_OLD_PRICE//
				if(BX("old-price_" + id))
					BX("old-price_" + id).innerHTML = (item.FULL_PRICE_FORMATED != item.PRICE_FORMATED) ? item.FULL_PRICE_FORMATED : '';				

				//ITEM_UNIT//
				if(BX("unit_" + id))
					BX("unit_" + id).innerHTML = item.MEASURE_TEXT;
				
				//ITEM_SUM//
				if(BX("cart-item-summa_" + id)) {
					var itemSumCont,
						itemSumOld,
						itemSumCurr,
						decimals;

					itemSumCont = $(BX("cart-item-summa_" + id));

					itemSumOld = itemSumCont.data("itemsum");					
					itemSumCont.data("itemsum", (item.PRICE * item.QUANTITY));
					itemSumCurr = itemSumCont.data("itemsum");

					if(itemSumCurr != itemSumOld) {
						decimals = itemSumCont.data("decimal");
						if(itemSumCont.data("hide-zero") == "Y") {			
							if(Math.abs(parseFloat(itemSumCurr).toFixed(decimals)) == Math.abs(parseFloat(itemSumCurr).toFixed(0))) {						
								decimals = 0;								
							}
						}
						var options = {
							useEasing: false,
							useGrouping: true,
							separator: itemSumCont.data("separator"),
							decimal: itemSumCont.data("dec-point")
						}
						var counter = new countUp("itemSumVal_" + id, itemSumOld, itemSumCurr, decimals, 0.5, options);
						counter.start();
					}

					//ITEM_REFERENCE_SUM//
					if(BX("itemReferenceSumVal_" + id)) {
						var itemReferenceSumCoef,
							itemReferenceSumOld,
							itemReferenceSumCurr;					
					
						itemReferenceSumCoef = itemSumCont.data("itemreferencesumcoef");
						itemReferenceSumOld = itemSumCont.data("itemreferencesum");					
						itemSumCont.data("itemreferencesum", (item.PRICE * item.QUANTITY * itemReferenceSumCoef));
						itemReferenceSumCurr = itemSumCont.data("itemreferencesum");

						if(itemReferenceSumCurr != itemReferenceSumOld) {
							decimals = itemSumCont.data("reference-decimal");
							if(itemSumCont.data("hide-zero") == "Y") {			
								if(Math.abs(parseFloat(itemReferenceSumCurr).toFixed(decimals)) == Math.abs(parseFloat(itemReferenceSumCurr).toFixed(0))) {						
									decimals = 0;								
								}
							}
							var options = {
								useEasing: false,
								useGrouping: true,
								separator: itemSumCont.data("separator"),
								decimal: itemSumCont.data("dec-point")
							}
							var counter = new countUp("itemReferenceSumVal_" + id, itemReferenceSumOld, itemReferenceSumCurr, decimals, 0.5, options);
							counter.start();
						}
					}					
				}

				//if the quantity was set by user to 0 or was too much, we need to show corrected quantity value from ajax response
				if(BX("QUANTITY_" + id)) {
					BX('QUANTITY_INPUT_' + id).value = item.QUANTITY;
					BX('QUANTITY_INPUT_' + id).defaultValue = item.QUANTITY;

					BX('QUANTITY_' + id).value = item.QUANTITY;
				}
			}
		}
	}

	//update coupon info
	if(!!res.BASKET_DATA)
		couponListUpdate(res.BASKET_DATA);

	//update warnings if any
	if(res.hasOwnProperty('WARNING_MESSAGE')) {
		var warningText = '';
		for(i = res['WARNING_MESSAGE'].length - 1; i >= 0; i--)
			warningText += res['WARNING_MESSAGE'][i] + '<br/>';		
		BX('warning_message').innerHTML = warningText;
	}

	//update total basket values
	if(!!res.BASKET_DATA) {
		//ALL_SUM//
		if(BX("cart-allsum")) {
			var allSumCont,
				allSumOld,
				allSumCurr,
				decimals;

			allSumCont = $(BX("cart-allsum"));
			
			allSumOld = allSumCont.data("allsum");		
			allSumCont.data("allsum", res["BASKET_DATA"]["allSum"]);
			allSumCurr = allSumCont.data("allsum");

			if(allSumCurr != allSumOld) {
				decimals = allSumCont.data("decimal");
				if(allSumCont.data("hide-zero") == "Y") {			
					if(Math.abs(parseFloat(allSumCurr).toFixed(decimals)) == Math.abs(parseFloat(allSumCurr).toFixed(0))) {						
						decimals = 0;										
					}
				}
				var options = {
					useEasing: false,
					useGrouping: true,
					separator: allSumCont.data("separator"),
					decimal: allSumCont.data("dec-point")
				}
				var counter = new countUp("allSumVal", allSumOld, allSumCurr, decimals, 0.5, options);
				counter.start();
                
                BX("total_price_basket").value=allSumCurr;
			}

			//ALL_REFERENCE_SUM//
			if(BX("allReferenceSumVal")) {
				var allReferenceSumCoef,
					allReferenceSumOld,
					allReferenceSumCurr;

				allReferenceSumCoef = allSumCont.data("allreferencesumcoef");
				allReferenceSumOld = allSumCont.data("allreferencesum");		
				allSumCont.data("allreferencesum", (res["BASKET_DATA"]["allSum"] * allReferenceSumCoef));
				allReferenceSumCurr = allSumCont.data("allreferencesum");

				if(allReferenceSumCurr != allReferenceSumOld) {
					decimals = allSumCont.data("reference-decimal");
					if(allSumCont.data("hide-zero") == "Y") {			
						if(Math.abs(parseFloat(allReferenceSumCurr).toFixed(decimals)) == Math.abs(parseFloat(allReferenceSumCurr).toFixed(0))) {						
							decimals = 0;										
						}
					}
					var options = {
						useEasing: false,
						useGrouping: true,
						separator: allSumCont.data("separator"),
						decimal: allSumCont.data("dec-point")
					}
					var counter = new countUp("allReferenceSumVal", allReferenceSumOld, allReferenceSumCurr, decimals, 0.5, options);
					counter.start();
				}
			}
			
			BX.onCustomEvent("OnBasketChange");
		}
	}
}

function number_format(number, decimals, dec_point, thousands_sep) {
	number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === "undefined") ? "," : thousands_sep,
		dec = (typeof dec_point === "undefined") ? "." : dec_point,
		s = "",
		toFixedFix = function (n, prec) {
			var k = Math.pow(10, prec);
			return "" + Math.round(n * k) / k;
		};
	s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
	if(s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if((s[1] || "").length < prec) {
		s[1] = s[1] || "";
		s[1] += new Array(prec - s[1].length + 1).join("0");
	}
	return s.join(dec);
}

function couponCreate(couponBlock, oneCoupon) {
	var couponClass = 'disabled';

	if(!BX.type.isElementNode(couponBlock))
		return;
	if(oneCoupon.JS_STATUS === 'BAD')
		couponClass = 'bad';
	else if(oneCoupon.JS_STATUS === 'APPLYED')
		couponClass = 'good';

	couponBlock.appendChild(BX.create(
		'div',
		{
			props: {
				className: 'bx_ordercart_coupon'
			},
			children: [				
				BX.create(
					'input',
					{
						props: {							
							type: 'hidden',
							value: oneCoupon.COUPON,
							name: 'OLD_COUPON[]'
						}						
					}
				),
				BX.create(
					'div',
					{
						props: {
							className: 'old_coupon ' + couponClass
						},
						html: oneCoupon.COUPON + ' ' + oneCoupon.JS_CHECK_CODE
					}
				),
				BX.create(
					'span',
					{
						props: {
							className: 'close'
						},
						attrs: {
							'data-coupon': oneCoupon.COUPON
						},
						children: [
							BX.create(
								'i',
								{
									props: {
										className: 'fa fa-times'
									}
								}
							)
						]
					}
				),
				BX.create(
					'div',
					{
						props: {
							className: 'clr'
						}						
					}
				)
			]
		}
	));
}

function couponListUpdate(res) {
	var couponBlock,		
		fieldCoupon,
		couponsCollection,
		couponFound,
		divCoupon,
		i,
		j,
		key;

	if(!!res && typeof res !== 'object') {
		return;
	}

	couponBlock = BX('cart-coupon');
	if(!!couponBlock) {
		if(!!res.COUPON_LIST && BX.type.isArray(res.COUPON_LIST)) {
			fieldCoupon = BX('coupon');
			if(!!fieldCoupon) {
				fieldCoupon.value = '';
			}
			couponsCollection = BX.findChildren(couponBlock, { tagName: 'input', property: { name: 'OLD_COUPON[]' } }, true);			

			if(!!couponsCollection) {
				if(BX.type.isElementNode(couponsCollection)) {
					couponsCollection = [couponsCollection];
				}
				for(i = 0; i < res.COUPON_LIST.length; i++) {
					couponFound = false;
					key = -1;
					for(j = 0; j < couponsCollection.length; j++) {
						if(couponsCollection[j].value === res.COUPON_LIST[i].COUPON) {
							couponFound = true;
							key = j;
							couponsCollection[j].couponUpdate = true;
							break;
						}
					}
					if(couponFound) {
						couponClass = 'disabled';
						if(res.COUPON_LIST[i].JS_STATUS === 'BAD')
							couponClass = 'bad';
						else if(res.COUPON_LIST[i].JS_STATUS === 'APPLYED')
							couponClass = 'good';
						divCoupon = BX.findNextSibling(couponsCollection[key], {className: 'old_coupon'});
						if(!!divCoupon)
							BX.adjust(divCoupon, {
								props: {
									className: 'old_coupon ' + couponClass
								},
								html: res.COUPON_LIST[i].COUPON + ' ' + res.COUPON_LIST[i].JS_CHECK_CODE
							});
					} else {
						couponCreate(couponBlock, res.COUPON_LIST[i]);
					}
				}
				for(j = 0; j < couponsCollection.length; j++) {
					if(typeof (couponsCollection[j].couponUpdate) === 'undefined' || !couponsCollection[j].couponUpdate) {
						BX.remove(couponsCollection[j].parentNode);
						couponsCollection[j] = null;
					} else {
						couponsCollection[j].couponUpdate = null;
					}
				}
			} else {
				for(i = 0; i < res.COUPON_LIST.length; i++) {
					couponCreate(couponBlock, res.COUPON_LIST[i]);
				}
			}
		}
	}
	couponBlock = null;
}

function checkOut() {
	if(!!BX("coupon"))
		BX("coupon").disabled = true;	
	BX("basket_form").submit();	
	return true;
}

function enterCoupon() {
	var newCoupon = BX("coupon");
	if(!!newCoupon && !!newCoupon.value)
		recalcBasketAjax({"coupon" : newCoupon.value});
}

function updateQuantity(controlId, basketId, ratio, bUseFloatQuantity) {
	var oldVal = BX(controlId).defaultValue,
		newVal = parseFloat(BX(controlId).value) || 0,
		bIsCorrectQuantityForRatio = false,
		autoCalculate = ((BX("auto_calculation") && BX("auto_calculation").value == "Y") || !BX("auto_calculation"));

	if(ratio === 0 || ratio == 1) {
		bIsCorrectQuantityForRatio = true;
	} else {
		var newValInt = newVal * 10000,
			ratioInt = ratio * 10000,
			reminder = newValInt % ratioInt,
			newValRound = parseInt(newVal);

		if(reminder === 0) {
			bIsCorrectQuantityForRatio = true;
		}
	}

	var bIsQuantityFloat = false;

	if(parseInt(newVal) != parseFloat(newVal)) {
		bIsQuantityFloat = true;
	}

	newVal = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(newVal) : parseFloat(newVal).toFixed(4);
	newVal = correctQuantity(newVal);

	if(bIsCorrectQuantityForRatio) {
		BX(controlId).defaultValue = newVal;		
		
		BX("QUANTITY_INPUT_" + basketId).value = newVal;

		//set hidden real quantity value (will be used in actual calculation)
		BX("QUANTITY_" + basketId).value = newVal;
		
		if(autoCalculate) {			
			basketPoolQuantity.changeQuantity(basketId);
		}
	} else {
		newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);
		newVal = correctQuantity(newVal);
		
		if(newVal != oldVal) {
			BX("QUANTITY_INPUT_" + basketId).value = newVal;
			BX("QUANTITY_" + basketId).value = newVal;			
			
			if(autoCalculate) {				
				basketPoolQuantity.changeQuantity(basketId);
			}
		} else {
			BX(controlId).value = oldVal;
		}
	}
}

//used when quantity is changed by clicking on arrows
function setQuantity(basketId, ratio, sign, bUseFloatQuantity) {
	var curVal = parseFloat(BX("QUANTITY_INPUT_" + basketId).value),
		newVal;

	newVal = (sign == 'up') ? curVal + ratio : curVal - ratio;

	if(newVal < 0)
		newVal = 0;

	if(bUseFloatQuantity) {
		newVal = newVal.toFixed(4);
	}
	newVal = correctQuantity(newVal);

	if(ratio > 0 && newVal < ratio) {
		newVal = ratio;
	}

	if(!bUseFloatQuantity && newVal != newVal.toFixed(4)) {
		newVal = newVal.toFixed(4);
	}

	newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);
	newVal = correctQuantity(newVal);

	BX("QUANTITY_INPUT_" + basketId).value = newVal;
	BX("QUANTITY_INPUT_" + basketId).defaultValue = newVal;

	updateQuantity('QUANTITY_INPUT_' + basketId, basketId, ratio, bUseFloatQuantity);
}

function getCorrectRatioQuantity(quantity, ratio, bUseFloatQuantity) {
	var newValInt = quantity * 10000,
		ratioInt = ratio * 10000,
		reminder = newValInt % ratioInt,
		result = quantity,
		bIsQuantityFloat = false,
		i;
	ratio = parseFloat(ratio);

	if(reminder === 0) {
		return result;
	}

	if(ratio !== 0 && ratio != 1) {
		for(i = ratio, max = parseFloat(quantity) + parseFloat(ratio); i <= max; i = parseFloat(parseFloat(i) + parseFloat(ratio)).toFixed(4)) {
			result = i;
		}
	} else if(ratio === 1) {
		result = quantity | 0;
	}

	if(parseInt(result, 10) != parseFloat(result)) {
		bIsQuantityFloat = true;
	}

	result = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(result, 10) : parseFloat(result).toFixed(4);
	result = correctQuantity(result);

	return result;
}

function correctQuantity(quantity) {
	return parseFloat((quantity * 1).toString());
}

function recalcBasketAjax(params) {
	if(basketPoolQuantity.isProcessing()) {
		return false;
	}

	BX.showWait();

	var property_values = {},
		action_var = BX('action_var').value,		
		items = BX.findChildren(BX('cart_equipment'), {className: 'tr'}, true),
		shelveItems = BX.findChildren(BX('shelve_equipment'), {className: 'tr'}, true),
		postData,
		i;	

	postData = {
		'sessid': BX.bitrix_sessid(),
		'site_id': BX.message('SITE_ID'),
		'props': property_values,
		'action_var': action_var,
		'select_props': BX('column_headers').value,
		'offers_props': BX('offers_props').value,
		'quantity_float': BX('quantity_float').value,
		'count_discount_4_all_quantity': BX('count_discount_4_all_quantity').value,
		'price_vat_show_value': BX('price_vat_show_value').value,
		'hide_coupon': BX('hide_coupon').value,
		'use_prepayment': BX('use_prepayment').value
	};
	postData[action_var] = 'recalculate';
	if(!!params && typeof params === 'object') {
		for(i in params) {
			if(params.hasOwnProperty(i))
				postData[i] = params[i];
		}
	}

	if(!!items && items.length > 0) {
		for(i = 0; items.length > i; i++)
			postData['QUANTITY_' + items[i].id] = BX('QUANTITY_' + items[i].id).value;
	}

	if(!!shelveItems && shelveItems.length > 0) {
		for(i = 0; shelveItems.length > i; i++)
			postData['DELAY_' + shelveItems[i].id] = 'Y';
	}

	basketPoolQuantity.setProcessing(true);
	basketPoolQuantity.clearPool();

	BX.ajax({
		url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
		method: 'POST',
		data: postData,
		dataType: 'json',
		onsuccess: function(result) {
			BX.closeWait();
			basketPoolQuantity.setProcessing(false);
			if(basketPoolQuantity.isPoolEmpty()) {
				updateBasketTable(null, result);
				basketPoolQuantity.updateQuantity();
			} else {
				basketPoolQuantity.enableTimer(true);
			}
		}
	});
}

function showBasketItems(val) {
	if(val == 2) {
		BX("id-cart-list").style.display = 'none';
		BX("id-shelve-list").style.display = 'block';
	} else {
		BX("id-cart-list").style.display = 'block';
		BX("id-shelve-list").style.display = 'none';			
	}
}

function deleteCoupon(e) {
	var target = BX.proxy_context,
		value;

	if(!!target && target.hasAttribute('data-coupon')) {
		value = target.getAttribute('data-coupon');
		if(!!value && value.length > 0) {
			recalcBasketAjax({'delete_coupon' : value});
		}
	}
}

function openFormBoc() {
	var action = "boc",
		visualId = "cart";
	BX.PopupForm =
	{						
		arParams: {}
	};
	BX.PopupForm.popup = BX.PopupWindowManager.create(action + "_" + visualId, null, {
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
		titleBar: true,
		content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",			
		events: {
			onAfterPopupShow: function()
			{
				if(!BX(action + "_" + visualId + "_form")) {
					BX.ajax.post(
						BX.message("SITE_DIR") + "ajax/popup.php",
						{							
							sessid: BX.bitrix_sessid(),
							action: action,
							arParams: BX.message("SBB_COMPONENT_PARAMS"),
							ELEMENT_AREA_ID: visualId
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
				}
			}
		}			
	});
	
	var close = BX.findChild(BX(action + "_" + visualId), {className: "popup-window-close-icon"}, true, false);
	if(!!close)
		close.innerHTML = "<i class='fa fa-times'></i>";

	BX.PopupForm.popup.show();		
}

BX.ready(function() {
	basketPoolQuantity = new BasketPoolQuantity();
	var couponBlock = BX('cart-coupon');
	if(!!couponBlock)
		BX.bindDelegate(couponBlock, 'click', { 'attribute': 'data-coupon' }, BX.delegate(function(e){deleteCoupon(e); }, this));

	var bocBtn = BX("boc_anch_cart");
	if(!!bocBtn)
		BX.bind(bocBtn, "click", BX.delegate(function(){openFormBoc();}, this));
});