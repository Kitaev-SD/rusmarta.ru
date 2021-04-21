(function(BX, window) {

	const Reference = BX.namespace('YandexMarket.Field.Reference');
	const Fieldset = BX.namespace('YandexMarket.Field.Fieldset');

	const constructor = Fieldset.Collection = Reference.Collection.extend({

		defaults: {
			itemElement: '.js-fieldset-collection__item',
			itemAddElement: '.js-fieldset-collection__item-add',
			itemDeleteElement: '.js-fieldset-collection__item-delete',

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_FIELDSET_'
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
			this.handleItemAddClick(true);
			this.handleItemDeleteClick(true);
		},

		unbind: function() {
			this.handleItemAddClick(false);
			this.handleItemDeleteClick(false);
		},

		handleItemAddClick: function(dir) {
			const addButton = this.getItemAddButton();

			addButton[dir ? 'on' : 'off']('click', $.proxy(this.onItemAddClick, this));
		},

		handleItemDeleteClick: function(dir) {
			const deleteSelector = this.getElementSelector('itemDelete');

			this.$el[dir ? 'on' : 'off']('click', deleteSelector, $.proxy(this.onItemDeleteClick, this));
		},

		handleModalSave: function(childInstance, dir) {
			childInstance.$el[dir ? 'on' : 'off']('uiModalSave', $.proxy(this.onModalSave, this));
		},

		handleModalClose: function(childInstance, dir) {
			childInstance.$el[dir ? 'on' : 'off']('uiModalClose', $.proxy(this.onModalClose, this));
		},

		onItemAddClick: function(evt) {
			const instance = this.addItem();

			instance.initEdit();

			evt.preventDefault();
		},

		onItemDeleteClick: function(evt) {
			const deleteButton = $(evt.target);
			const item = this.getElement('item', deleteButton, 'closest');

			this.deleteItem(item);

			evt.preventDefault();
		},

		getItemAddButton: function() {
			return this.getElement('itemAdd', this.$el, 'next');
		},

		getItemPlugin: function() {
			return Fieldset.Row;
		}

	}, {
		dataName: 'FieldFieldsetCollection',
		pluginName: 'YandexMarket.Field.Fieldset.Collection'
	});

})(BX, window);