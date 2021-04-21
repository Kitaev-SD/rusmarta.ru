(function(BX, window) {

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Fieldset = BX.namespace('YandexMarket.Field.Fieldset');

	var constructor = Fieldset.Row = Reference.Base.extend({

		defaults: {
			inputElement: '.js-fieldset-row__input',

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_FIELDSET_'
		}

	}, {
		dataName: 'FieldFieldsetRow',
		pluginName: 'YandexMarket.Field.Fieldset.Row'
	});

})(BX, window);