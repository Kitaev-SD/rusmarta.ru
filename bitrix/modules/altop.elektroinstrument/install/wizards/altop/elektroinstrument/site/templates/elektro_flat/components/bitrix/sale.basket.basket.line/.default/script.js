function BitrixSmallCart(){}

BitrixSmallCart.prototype = {
	activate: function() {
		this.cartElement = BX(this.cartId);
		
		this.setCartBodyClosure = this.closure("setCartBody");
		BX.addCustomEvent(window, "OnBasketChange", this.closure("refreshCart", {}));
	},

	closure: function(fname, data) {
		var obj = this;
		return data
			? function(){obj[fname](data)}
			: function(arg1){obj[fname](arg1)};
	},

	refreshCart: function(data) {		
		data.sessid = BX.bitrix_sessid();
		data.siteId = this.siteId;
		data.templateName = this.templateName;
		data.arParams = this.arParams;
		BX.ajax({
			url: this.ajaxPath,
			method: 'POST',
			dataType: 'html',
			data: data,
			onsuccess: this.setCartBodyClosure			
		});
	},

	setCartBody: function(result) {		
		var basketCont,
			sumOld,
			sumCurr;

		basketCont = $(this.cartElement);
		
		basketCont.find(".qnt").text($(result).find(".qnt").text());
		
		basketCont.find(".sum").data("decimal", $(result).find(".sum").data("decimal"));
		
		sumOld = basketCont.find(".sum").data("sum");
		basketCont.find(".sum").data("sum", $(result).find(".sum").data("sum"));
		sumCurr = basketCont.find(".sum").data("sum");		

		if(sumCurr != sumOld) {
			var options = {
				useEasing: false,
				useGrouping: true,
				separator: basketCont.find(".sum").data("separator"),
				decimal: basketCont.find(".sum").data("dec-point")
			}
			var counter = new countUp("cartCounter", sumOld, sumCurr, basketCont.find(".sum").data("decimal"), 0.5, options);
			counter.start();
		}

		basketCont.find(".oformit_cont").html($(result).find(".oformit_cont").html());
	}
};