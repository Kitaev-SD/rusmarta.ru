(function(BX, $, window) {

	const FieldReference = BX.namespace('YandexMarket.Field.Reference');
	const OrderView = BX.namespace('YandexMarket.OrderView');

	const constructor = OrderView.BasketItem = FieldReference.Complex.extend({

		defaults: {
			id: null,

			childElement: '.js-yamarket-basket-item__field',
			inputElement: '.js-yamarket-basket-item__data',
		},

		initialize: function() {
			this.callParent('initialize', constructor);
			this.bind();
		},

		destroy: function() {
			this.unbind();
			this.callParent('destroy', constructor);
		},

		bind: function() {
			this.handleCountChange(true);
		},

		unbind: function() {
			this.handleCountChange(false);
		},

		handleCountChange: function(dir) {
			const input = this.getInput('COUNT');

			if (!input) { return; }

			input[dir ? 'on' : 'off']('change',  $.proxy(this.onCountChange, this));
		},

		onCountChange: function() {
			this.updateCisCount();
		},

		updateCisCount: function() {
			const cis = this.getCis();
			const count = this.getCount();

			if (cis && count > 0) {
				cis.setBasketCount(count);
				cis.refreshSummary();
			}
		},

		getTitle: function() {
			const name = this.getInput('NAME');

			return name && name.text();
		},

		getCountDiff: function() {
			let result;

			if (this.needDelete()) {
				result = this.getInitialCount();
			} else {
				result = this.getInitialCount() - this.getCount();
			}

			return result;
		},

		getCount: function() {
			const values = this.getValue();

			return values['COUNT'] !== null ? parseInt(values['COUNT']) : null;
		},

		getInitialCount: function() {
			const values = this.getValue();

			return values['INITIAL_COUNT'] !== null ? parseInt(values['INITIAL_COUNT']) : null;
		},

		setInitialCount: function(count) {
			const input = this.getInput('INITIAL_COUNT');

			if (!input) { return; }

			input.val(count);
		},

		needDelete: function() {
			const input = this.getInput('DELETE');

			return input && input.prop('checked');
		},

		validate: function() {
			const cis = this.getCis();

			if (cis && !this.needDelete()) {
				cis.validate();
			}
		},

		getCis: function() {
			return this.getChildField('CIS');
		},

	}, {
		dataName: 'orderViewBasketItem',
		pluginName: 'YandexMarket.OrderView.BasketItem',
	});

})(BX, jQuery, window);