if (!window.YandexMarketplaceInitialized) {

	// Export stocks
	$(document).delegate('input[data-role="acrit_exp_yandex_marketplace_export_stocks"]', 'change', function(e){
		$('div[data-role="acrit_exp_yandex_marketplace_stores_wrapper"]').toggle($(this).prop('checked'));
	});

	// Add store
	$(document).delegate('input[data-role="acrit_exp_yandex_marketplace_store_add"]', 'click', function(e){
		let
			items = $('div[data-role="acrit_exp_yandex_marketplace_stores_list"]'),
			item = items.children().first(),
			newItem = item.clone();
		newItem.appendTo(items);
		newItem.find('input[type="text"]').val('');
	});

	// Delete store
	$(document).delegate('input[data-role="acrit_exp_yandex_marketplace_store_delete"]', 'click', function(e){
		if(confirm($(this).attr('data-confirm'))){
			$(this).closest('[data-role="acrit_exp_yandex_marketplace_store"]').remove();
		}
	});

	// External request
	$(document).delegate('input[data-role="acrit_exp_yandex_marketplace_external_request"]', 'change', function(e){
		$('div[data-role="acrit_exp_yandex_marketplace_external_request_wrapper"]').toggle($(this).prop('checked'));
	});

	// Stocks log: reload
	$(document).delegate('input[data-role="acrit_exp_yandex_marketplace_stocks_reload"]', 'click', function(e, params) {
		acritExpAjax(['plugin_ajax_action', 'refresh_stocks_log'], params, function(JsonResult, textStatus, jqXHR){
			$('#tr_LOG_CUSTOM > td').html(JsonResult.HTML);
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, false);
	});
	
	// Trigger events
	function acritExpYandexMarketplaceTriggers(){
		$('input[data-role="acrit_exp_yandex_marketplace_export_stocks"]').trigger('change');
		$('input[data-role="acrit_exp_yandex_marketplace_external_request"]').trigger('change');
	}

	window.YandexMarketplaceInitialized = true;
}

// On load
setTimeout(function(){
	acritExpYandexMarketplaceTriggers();
}, 500);
$(document).ready(function(){
	acritExpYandexMarketplaceTriggers();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpYandexMarketplaceTriggers();
});
