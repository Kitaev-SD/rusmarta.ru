
/**
 * Manual import start (with progress bar)
 */

function AcritMansyncStartExportResetVars() {
	run_enabled = true;
	$('#man_sync_result .mansync-result-all span').text(0);
	$('#man_sync_result .mansync-result-done span').text(0);
	AcritMansyncStartExportProgress(1, 0);
	$('#start_mansync_errors').val('');
}
function AcritMansyncStartExportProgress(count, current) {
	let percent, width, max_width;
	percent = 0;
	if (current > 0) {
		percent = current / count * 100;
		percent = Number((percent).toFixed(2));
	}
	width = 50;
	max_width = $('.adm-progress-bar-outer').width();
	width = max_width / 100 * percent;
	$('#start_export_progress .adm-progress-bar-inner').width(width + 'px');
	$('#start_export_progress .adm-progress-bar-inner-text').text(percent + '%');
	$('#start_export_progress .adm-progress-bar-outer-text').text(percent + '%');
}
function AcritMansyncStartExport(next_item, count) {
	if (!run_enabled) {
		$('#man_sync_start').removeClass("adm-btn-disabled");
		$('#man_sync_noprogress_start').removeClass("adm-btn-disabled");
		return false;
	}
	acritExpAjax('man_sync_run', {
		"next_item": next_item,
		"count": count,
	}, function (JsonResult, textStatus, jqXHR) {
		if (JsonResult.result == 'ok') {
			if (JsonResult.errors.length > 0) {
				JsonResult.errors.forEach(function(item, i, arr) {
					AcritMansyncMessageAdd(item);
				});
			}
			AcritMansyncStartExportProgress(count, JsonResult.next_item);
			$('#man_sync_result .mansync-result-done span').text(JsonResult.report.done);
			if (JsonResult.next_item && JsonResult.next_item > next_item && JsonResult.next_item < count) {
				AcritMansyncStartExport(JsonResult.next_item, count);
			}
			else {
				AcritMansyncStartExportProgress(1, 1);
				$('#man_sync_stop').addClass("adm-btn-disabled");
				$('#man_sync_start').removeClass("adm-btn-disabled");
			}
		}
		else {
			console.log(JsonResult);
		}
	}, function (jqXHR) {
		console.log(jqXHR);
	}, true);
}

function AcritMansyncMessageAdd(message) {
	var text = $('#start_mansync_errors').val();
	text += message + "\n";
	$('#start_mansync_errors').val(text);
}


/**
 * Manual import start (without progress bar)
 */

function AcritMansyncNoprogressStartExport(next_item) {
	if (!run_enabled) {
		return false;
	}
	acritExpAjax('man_sync_run', {
		"next_item": next_item,
		"count": 0,
	}, function (JsonResult, textStatus, jqXHR) {
		if (JsonResult.result == 'ok') {
			if (JsonResult.errors.length > 0) {
				JsonResult.errors.forEach(function(item, i, arr) {
					AcritMansyncMessageAdd(item);
				});
			}
			$('#man_sync_result_count span').text(JsonResult.report.done);
			if (JsonResult.next_item && JsonResult.next_item > next_item) {
				AcritMansyncNoprogressStartExport(JsonResult.next_item);
			}
			else {
				$('#man_sync_stop').addClass("adm-btn-disabled");
				$('#man_sync_noprogress_start').removeClass("adm-btn-disabled");
			}
		}
		else {
			console.log(JsonResult);
		}
	}, function (jqXHR) {
		console.log(jqXHR);
	}, true);
}


/**
 * JS actions
 */

$(function() {

	/**
	 * Manual export
	 */

	AcritMansyncStartExportResetVars();

	$("#man_sync_start").click(function() {
		if (!$(this).hasClass("adm-btn-disabled")) {
			AcritMansyncStartExportResetVars();
			$('#man_sync_start').addClass("adm-btn-disabled");
			$('#man_sync_stop').removeClass("adm-btn-disabled");
			// $('#man_sync_result').show();
			acritExpAjax('man_sync_count', {}, function (JsonResult, textStatus, jqXHR) {
				if (JsonResult.result == 'ok') {
					let mansync_count = JsonResult.count;
					if (JsonResult.errors.length > 0) {
						JsonResult.errors.forEach(function(item, i, arr) {
							AcritMansyncMessageAdd(item);
						});
						$('#man_sync_stop').addClass("adm-btn-disabled");
						$('#man_sync_start').removeClass("adm-btn-disabled");
					}
					else {
						AcritMansyncStartExportProgress(1, 0);
						$('#man_sync_result .mansync-result-all span').text(mansync_count);
						AcritMansyncStartExport(0, mansync_count);
					}
				}
				else {
					console.log(JsonResult);
				}
			}, function (jqXHR) {
				console.log(jqXHR);
			}, true);
		}
		return false;
	});

	$("#man_sync_noprogress_start").click(function() {
		if (!$(this).hasClass("adm-btn-disabled")) {
			AcritMansyncStartExportResetVars();
			$('#man_sync_noprogress_start').addClass("adm-btn-disabled");
			$('#man_sync_stop').removeClass("adm-btn-disabled");
			// $('#man_sync_result').show();
			AcritMansyncNoprogressStartExport(0);
		}
		return false;
	});

	$("#man_sync_stop").click(function() {
		if (!$(this).hasClass("adm-btn-disabled")) {
			$('#man_sync_stop').addClass("adm-btn-disabled");
			run_enabled = false;
		}
		return false;
	});

	$('.acrit-mansync-store-fields').select2({
		width: '100%',
		language: {
			'noResults': function(){
				return loc_messages.ACRIT_MANSYNC_STORE_FIELDS_NOTFOUND;
			}
		}
	});

});
