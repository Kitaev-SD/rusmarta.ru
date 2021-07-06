(function(BX, $, window) {

	const Input = BX.namespace('YandexMarket.Ui.Input');

	const constructor = Input.Autocomplete = Input.TagInput.extend({

		defaults: {
			paging: false,
		},

		createPluginOptions: function() {
			return $.extend(
				this.callParent('createPluginOptions', constructor),
				this.getAjaxOptions()
			);
		},

		getAjaxOptions: function() {
			return {
				ajax: {
					delay: 300,
					url: this.options.url,
					type: 'post',
					data: $.proxy(this.makeAjaxData, this),
					dataType: 'json',
					processResults: $.proxy(this.processResults, this),
				}
			};
		},

		makeAjaxData: function(params) {
			return {
				q: params.term,
				page: this.options.paging && params.page || 1,
			};
		},

		processResults: function(data, params) {
			const response = {
				results: [],
				pagination: {
					more: false,
				},
			};

			if (data.status === 'ok') {
				for (const option of data.enum) {
					response.results.push({
						id: option['ID'],
						text: option['VALUE'],
					});
				}

				response.pagination.more = !!data.hasNext;
			}

			return response;
		},

	}, {
		dataName: 'uiAutocompleteInput'
	});

})(BX, jQuery, window);