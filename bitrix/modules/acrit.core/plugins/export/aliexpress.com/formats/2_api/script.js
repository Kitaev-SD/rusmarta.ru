
/**
 * Отображение группы селекторов разделов
 */
function AcritCoreAliexpressApiGetCategoryBlock(selected_categs) {
	BX.showWait('acrit_core_aliexpress_category')
	$('.acrit-core-aliexpress-sections .acrit-core-aliexpress-section').attr('disabled', true);
	AcritCoreAliexpressApiGetCategoryLists(selected_categs, function (categ_lists) {
		let categ_list, i, j, item;
		$('.acrit-core-aliexpress-sections').html('');
		for (i in categ_lists) {
			$('.acrit-core-aliexpress-sections').append('<select name="PROFILE[PARAMS][SECTION][]" class="acrit-core-aliexpress-section" data-index="' + i + '"></select>')
			$('.acrit-core-aliexpress-sections .acrit-core-aliexpress-section:last-child').append('<option value="">Выбрать вариант</option>')
			categ_list = categ_lists[i]
			for (j in categ_list) {
				item = categ_list[j]
				$('.acrit-core-aliexpress-sections .acrit-core-aliexpress-section:last-child').append('<option value="' + item.id + '"' + (item.selected ? ' selected' : '') + '>' + item.name + '</option>')
			}
		}
		BX.closeWait('acrit_core_aliexpress_category')
	})
}

/**
 * Загрузка группы списков разделов
 */
function AcritCoreAliexpressApiGetCategoryLists(selected_categs, callback) {
	acritExpAjax(['plugin_ajax_action', 'get_sections'], {
		'selected_categs': selected_categs
	}, function (JsonResult, textStatus, jqXHR) {
		if (JsonResult.result == 'ok') {
			// Callback
			if (typeof callback === 'function') {
				callback(JsonResult.lists);
			}
		}
	}, function (jqXHR) {
		console.log(jqXHR);
	}, true);
}


/**
 * Проверка подключения
 */

$(document).delegate('[data-role="acrit_exp_aliapi_cred_check"]', 'click', function(e) {
	var btn = $(this);
	var token = $('[data-role="acrit_exp_aliapi_cred_token"]').val();
	if (!btn.hasClass('adm-btn-disabled')) {
		btn.addClass('adm-btn-disabled');
		acritExpAjax(['plugin_ajax_action', 'check_connection'], {
			'token': token,
		}, function (JsonResult, textStatus, jqXHR) {
			if (JsonResult.result == 'ok') {
				btn.removeClass('adm-btn-disabled');
				if (JsonResult.check == 'success') {
					$('#acrit_core_aliexpress_cred_check_msg').html(JsonResult.message);
				}
				else {
					$('#acrit_core_aliexpress_cred_check_msg').html('<span class="required">' + JsonResult.message + '</span>');
				}
			}
		}, function (jqXHR) {
			console.log(jqXHR);
		}, true);
	}
	return false;
});

/**
 * Выбор раздела
 */
$(document).delegate('.acrit-core-aliexpress-section', 'change', function(e) {
	let index = $(this).data('index')
	acrit_core_aliexpress_sections.splice(index + 1)
	acrit_core_aliexpress_sections[index] = $(this).val()
	AcritCoreAliexpressApiGetCategoryBlock(acrit_core_aliexpress_sections)
});


$(document).ready(function(){
	// Загрузка блока разделов
	AcritCoreAliexpressApiGetCategoryBlock(acrit_core_aliexpress_sections)
});
