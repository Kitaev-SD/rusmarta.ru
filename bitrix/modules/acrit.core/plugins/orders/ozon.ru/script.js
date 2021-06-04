
$(document).delegate('[data-role="connection-check"]', 'click', function(e) {
	var btn = $(this);
	var client_id = $('[data-role="connect-cred-client_id"]').val();
	var api_key = $('[data-role="connect-cred-api_key"]').val();
	if (!btn.hasClass('adm-btn-disabled')) {
		data = {
			'client_id': client_id,
			'api_key': api_key,
		};
		btn.addClass('adm-btn-disabled');
		$('#check_msg').html('');
		acritExpAjax(['plugin_ajax_action', 'connection_check'], data, function (JsonResult, textStatus, jqXHR) {
			if (JsonResult.result == 'ok') {
				btn.removeClass('adm-btn-disabled');
				if (JsonResult.check == 'success') {
					$('#check_msg').html(JsonResult.message);
				}
				else {
					$('#check_msg').html('<span class="required">' + JsonResult.message + '</span>');
				}
			}
		}, function (jqXHR) {
			console.log(jqXHR);
		}, true);
	}
	return false;
});


$(document).ready(function(){

});
