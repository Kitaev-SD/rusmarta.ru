BX.saleAccountPay = (function() {
	var classDescription = function(params) {
		this.messages = params.alertMessages || {};
		this.nameValue = params.nameValue || "buyMoney";
		this.ajaxUrl = params.url;
		this.signedParams = params.signedParams || {};
		this.wrapperId = params.wrapperId || "";
		this.templateFolder = params.templateFolder;
		this.wrapper = document.getElementById('bx-sap'+ this.wrapperId);

		this.changeInputContainer = this.wrapper.getElementsByClassName('sale-acountpay-fixedpay-container')[0];
		this.paySystemsContainer = this.wrapper.getElementsByClassName('sale-acountpay-pp')[0];
		this.inputElement = this.wrapper.getElementsByClassName('sale-acountpay-input')[0];		
		this.submitButton = this.wrapper.getElementsByClassName('sale-acountpay-btn')[0];		

		BX.ready(BX.proxy(this.init, this));
	};
	
	classDescription.prototype.init = function() {
		BX.bindDelegate(this.paySystemsContainer, 'click', {className: 'sale-acountpay-pp-company'}, BX.proxy(
			function() {
				var oldChosenCheckboxList = this.wrapper.querySelectorAll('.sale-acountpay-pp-company-input:checked');
				Array.prototype.forEach.call(oldChosenCheckboxList, function(checkbox) {
					checkbox.checked = false;
					checkbox.parentNode.parentNode.parentNode.parentNode.classList.remove('bx-selected');					
				});
				var target = BX.proxy_context;
				target.classList.add('bx-selected');
				target.querySelector('.sale-acountpay-pp-company-input').checked = 'checked';				
				return this;
			}, this)
		);

		BX.bind(this.inputElement, 'input', BX.delegate(
			function() {
				this.inputElement.value = this.inputElement.value.replace(/[^\d,.]*/g, '').replace(/\,/g, '.').replace(/([,.])[,.]+/g, '$1').replace(/^[^\d]*(\d+([.,]\d{0,5})?).*$/g, '$1');
			}, this)
		);
		
		BX.bindDelegate(this.changeInputContainer, 'click', {className: 'sale-acountpay-fixedpay-item' }, BX.proxy(
			function(event) {
				this.inputElement.value = parseInt(event.target.innerText);
			}, this)
		);

		BX.bind(this.submitButton, 'click', BX.delegate(
			function(event) {
				event.preventDefault();
				if(parseFloat(this.inputElement.value) <= 0 || this.inputElement.value == "") {
					window.alert(BX.util.htmlspecialchars(this.messages.wrongInput));
					return false;
				}
				var wait = BX.showWait(this.wrapper);				
				BX.ajax(
					{
						method: 'POST',
						dataType: 'html',
						url: this.ajaxUrl,
						data:
						{
							sessid: BX.bitrix_sessid(),
							buyMoney: this.inputElement.value,
							paySystemId: this.wrapper.querySelector('.sale-acountpay-pp-company-input:checked').value,
							signedParamsString: this.signedParams
						},
						onsuccess: BX.proxy(function(result) {
							while(this.wrapper.firstChild) {
								this.wrapper.removeChild(this.wrapper.firstChild);
							}
							this.wrapper.innerHTML = result;
							BX.closeWait(this.wrapper, wait);							
						},this),
						onfailure: BX.proxy(function() {
							return this;
						}, this)
					}, this
				);
				this.destroy();
			}, this)
		);
		return this;
	};

	classDescription.prototype.destroy = function() {
		this.messages = null;
		this.nameValue = null;
		this.signedParams = null;
		this.changeInputContainer = null;
		this.paySystemsContainer = null;
		this.inputElement = null;
		this.submitButton = null;
	};
	
	return classDescription;
})();