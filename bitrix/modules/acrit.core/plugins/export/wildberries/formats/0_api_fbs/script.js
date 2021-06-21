var AcritWbPopupCategory;

function acritExpWildberriesInitialize(){
	if(!window.wildberriesV2Initialized){
		
		// UUID mask
		window.wildberriesUuidMask = '********-****-****-****-************';
		$('input[data-role="acrit_exp_wildberries_supplier_id"]').inputmask({'mask': window.wildberriesUuidMask});

		// Check token main
		$(document).on('click', 'input[data-role="acrit_exp_wildberries_token_check"]', function(e) {
			let
				supplierId = $('input[data-role="acrit_exp_wildberries_supplier_id"]').val(),
				authToken = $('input[data-role="acrit_exp_wildberries_auth_token"]').val(),
				spanStatus = $('span[data-role="acrit_exp_wildberries_auth_token_status"]'),
				btn = $(this),
				classY = 'wildberries_token_status_y',
				classN = 'wildberries_token_status_n',
				classL = 'wildberries_token_status_loading',
				data = {
					supplier_id: supplierId,
					auth_token: authToken
				}
			spanStatus.removeClass(classY).removeClass(classN);
			if(!supplierId.length || supplierId.match(/_/g)){
				alert($('input[data-role="acrit_exp_wildberries_token_error_supplier_id"]').val());
				return;
			}
			if(!authToken.length){
				return;
			}
			spanStatus.addClass(classL);
			btn.attr('disabled', 'disabled');
			acritExpAjax(['plugin_ajax_action', 'token_check'], data, function(JsonResult, textStatus, jqXHR){
				btn.removeAttr('disabled');
				spanStatus.removeClass(classL).removeClass(classY).removeClass(classN);
				if(JsonResult.Success){
					spanStatus.addClass(classY);
				}
				else{
					spanStatus.addClass(classN);
				}
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
				btn.removeAttr('disabled');
				spanStatus.removeClass(classL).removeClass(classY).addClass(classN);
			}, false);
		});

		// // Check token stock
		// $(document).on('click', 'input[data-role="acrit_exp_wildberries_stock_token_check"]', function(e) {
		// 	let
		// 		token = $('input[data-role="acrit_exp_wildberries_export_stock_token"]').val(),
		// 		spanStatus = $('span[data-role="acrit_exp_wildberries_stock_token_status"]'),
		// 		btn = $(this),
		// 		classY = 'wildberries_token_status_y',
		// 		classN = 'wildberries_token_status_n',
		// 		classL = 'wildberries_token_status_loading',
		// 		data = {
		// 			stock_token: token
		// 		}
		// 	spanStatus.removeClass(classY).removeClass(classN);
		// 	if(!token.length){
		// 		return;
		// 	}
		// 	spanStatus.addClass(classL);
		// 	btn.attr('disabled', 'disabled');
		// 	acritExpAjax(['plugin_ajax_action', 'stock_token_check'], data, function(JsonResult, textStatus, jqXHR){
		// 		btn.removeAttr('disabled');
		// 		spanStatus.removeClass(classL).removeClass(classY).removeClass(classN);
		// 		if(JsonResult.Success){
		// 			spanStatus.addClass(classY);
		// 		}
		// 		else{
		// 			spanStatus.addClass(classN);
		// 		}
		// 		acritExpHandleAjaxError(jqXHR, false);
		// 	}, function(jqXHR){
		// 		acritExpHandleAjaxError(jqXHR, true);
		// 		btn.removeAttr('disabled');
		// 		spanStatus.removeClass(classL).removeClass(classY).addClass(classN);
		// 	}, false);
		// });

		// Token get main
		$(document).on('input', 'input[data-role="acrit_exp_wildberries_auth_token"]', function(e) {
			$('input[data-role="acrit_exp_wildberries_token_check"]').toggle(!!$(this).val().trim().length);
			$('div[data-role="acrit_exp_wildberries_token_get"]').toggle(!$(this).val().trim().length);
			$('span[data-role="acrit_exp_wildberries_auth_token_status"]').removeClass('wildberries_token_status_y')
				.removeClass('wildberries_token_status_n');
		});
		$('input[data-role="acrit_exp_wildberries_auth_token"]').trigger('input');

		// // Token get stock
		// $(document).on('input', 'input[data-role="acrit_exp_wildberries_export_stock_token"]', function(e) {
		// 	$('input[data-role="acrit_exp_wildberries_stock_token_check"]').toggle(!!$(this).val().trim().length);
		// 	$('div[data-role="acrit_exp_wildberries_stock_token_get"]').toggle(!$(this).val().trim().length);
		// });
		// $('input[data-role="acrit_exp_wildberries_export_stock_token"]').trigger('input');

		// Categories: add button to add (show popup)
		$(document).delegate('input[data-role="acrit_wb_categories_select"]', 'click', function(e){
			AcritWbPopupCategory.Open($(this).attr('data-popup-title'));
		});
		
		// Categories: remove selected category
		$(document).delegate('div[data-role="acrit_wb_categories_item_delete"] a', 'click', function(e){
			e.preventDefault();
			$(this).closest('[data-role="acrit_wb_categories_item"]').remove();
		});
		
		// Export stocks
		$(document).delegate('input[data-role="acrit_exp_wildberries_export_stocks"]', 'change', function(e){
			$('div[data-role="acrit_exp_wildberries_stores_wrapper"]').toggle($(this).prop('checked'));
		});
		$('input[data-role="acrit_exp_wildberries_export_stocks"]').trigger('change');

		function acritExpWildberriesCatAttrUpdateEnableControls(enabled){
			let
				btnStart = $('input[data-role="categories-update-attributes-start"]'),
				btnStop = $('input[data-role="categories-update-attributes-stop"]'),
				loader = $('div[data-role="categories-update-attributes-loader"]');
			if(enabled){
				btnStart.removeClass('hidden');
				btnStop.addClass('hidden');
				loader.addClass('hidden');
			}
			else{
				btnStart.addClass('hidden');
				btnStop.removeClass('hidden');
				loader.removeClass('hidden');
			}
		}
		
		function acritExpWildberriesCatAttrUpdateExecute(start, force, justAttr){
			let
				action = ['plugin_ajax_action', 'category_attributes_update'],
				data = {
					iblock_id: $('#field_IBLOCK').val(),
					start: start ? 'Y' : 'N'
				};
			if(force){
				data.force = 'Y';
			}
			if(justAttr){
				data.just_attr = 'Y';
			}
			acritExpWildberriesCatAttrUpdateEnableControls(false);
			window.acritExpWildberriesAjaxUpdateAttr = acritExpAjax(action, data, function (arJsonResult, textStatus, jqXHR) {
				if(arJsonResult.Continue){
					acritExpWildberriesCatAttrUpdateExecute(false);
				}
				else{
					acritExpWildberriesCatAttrUpdateEnableControls(true);
				}
				if(arJsonResult.Html){
					$('div[data-role="categories-update-attributes-result"]').html(arJsonResult.Html).closest('tr').show();
				}
			}, function (jqXHR) {
				console.log(jqXHR);
				acritExpWildberriesCatAttrUpdateEnableControls(true);
			}, true);
		}
		
		function acritExpWildberriesCatAttrUpdateStop(){
			acritExpWildberriesCatAttrUpdateEnableControls(true);
			if(window.acritExpWildberriesAjaxUpdateAttr){
				window.acritExpWildberriesAjaxUpdateAttr.abort();
			}
		}

		// Attributes update: start
		$(document).delegate('input[data-role="categories-update-attributes-start"]', 'click', function(e) {
			acritExpWildberriesCatAttrUpdateExecute(true, e.ctrlKey, e.shiftKey);
		});
		
		// Attributes update: stop
		$(document).delegate('input[data-role="categories-update-attributes-stop"]', 'click', function(e) {
			acritExpWildberriesCatAttrUpdateStop();
		});

		/**
		 * Popup for select category
		 */
		AcritWbPopupCategory = new BX.CDialog({
			ID: 'AcritWbPopupCategory',
			title: '',
			content: '',
			resizable: true,
			draggable: true,
			height: 310,
			width: 600
		});
		AcritWbPopupCategory.Open = function(title){
			this.SetTitle(title);
			this.LoadContent();
			this.Show();
		}
		AcritWbPopupCategory.LoadContent = function(){
			var thisPopup = this;
			//
			thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
			// Set popup buttons
			thisPopup.SetNavButtons();
			//
			acritExpAjax(['plugin_ajax_action', 'popup_categories_add'], {}, function(JsonResult, textStatus, jqXHR){
				thisPopup.SetHtml(JsonResult.HTML);
				thisPopup.SetAutoSize();
				//
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
			}, true);
		}
		AcritWbPopupCategory.SetAutoSize = function(){
			$('.bx-core-adm-dialog-content-wrap-inner', this.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
						'-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
		}
		AcritWbPopupCategory.SetHtml = function(html){
			$('.bx-core-adm-dialog-content-wrap-inner', this.PARTS.CONTENT_DATA).first().html(html);
		}
		AcritWbPopupCategory.SetNavButtons = function(){
			$(this.PARTS.BUTTONS_CONTAINER).html('');
			this.SetButtons(
				[{
					'name': BX.message('ACRIT_EXP_POPUP_SAVE'),
					'className': 'adm-btn-green',
					'id': this.PARAMS.ID + '_btnSave',
					'action': function(){
						let 
							list = $('div[data-role="acrit_wb_categories_list"]'),
							sample = $('div[data-role="acrit_wb_categories_list"] > div[data-role="acrit_wb_categories_item"]:first-child'),
							categoryName = $('select[data-role="acrit_wb_popup_category_select"]').val(),
							newItem = sample.clone();
						if(categoryName.length){
							newItem.find('input[type="hidden"]').val(categoryName);
							newItem.find('[data-role="acrit_wb_categories_item_name"]').text(categoryName);
							newItem.appendTo(list);
							this.parentWindow.Close();
						}
					}
				}, {
					'name': BX.message('ACRIT_EXP_POPUP_CANCEL'),
					'id': this.PARAMS.ID + '_btnCancel',
					'action': function(){
						this.parentWindow.Close();
					}
				}]
			)
		}

		/* Log */
		$(document).delegate('input[data-role="log-tasks-refresh"]', 'click', function(e, params) {
			acritExpAjax(['plugin_ajax_action', 'refresh_tasks_list'], params, function(JsonResult, textStatus, jqXHR){
				$('#tr_LOG_CUSTOM > td').html(JsonResult.HTML);
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
			}, false);
		});
	
		$(document).delegate('a[data-role="log-tasks-status-toggle"]', 'click', function(e) {
			let
				div = $(this).next();
			e.preventDefault();
			if(!div.is(':animated')){
				div.slideToggle(150);
			}
		});
	
		$(document).delegate('a[data-role="log-tasks-status-preview"]', 'click', function(e){
			let data = {
				task_id: $(this).closest('tr[data-task-id]').attr('data-task-id'),
				history_item_id: $(this).attr('data-id'),
			};
			e.preventDefault();
			AcritPopupHint.SetSize({width:1000, height:400});
			AcritPopupHint.Open();
			AcritPopupHint.SetTitle('Json preview');
			AcritPopupHint.SetHtml(BX.message('ACRIT_EXP_POPUP_LOADING'));
			acritExpAjax(['plugin_ajax_action', 'history_item_json_preview'], data, function(arJsonResult){
				AcritPopupHint.SetHtml(arJsonResult.HTML);
				$('pre > code', AcritPopupHint.PARTS.CONTENT_DATA).each(function(){
					highlighElement(this);
				});
			});
		});
	
		$(document).delegate('a[data-role="log-tasks-status-stocks-preview"]', 'click', function(e){
			let data = {
				task_id: $(this).closest('tr[data-task-id]').attr('data-task-id'),
				history_item_id: $(this).attr('data-id'),
			};
			e.preventDefault();
			AcritPopupHint.SetSize({width:1000, height:400});
			AcritPopupHint.Open();
			AcritPopupHint.SetTitle('Json preview');
			AcritPopupHint.SetHtml(BX.message('ACRIT_EXP_POPUP_LOADING'));
			acritExpAjax(['plugin_ajax_action', 'task_stocks_json_preview'], data, function(arJsonResult){
				AcritPopupHint.SetHtml(arJsonResult.HTML);
				$('pre > code', AcritPopupHint.PARTS.CONTENT_DATA).each(function(){
					highlighElement(this);
				});
			});
		});
		
	}
	window.wildberriesV2Initialized = true;
}

// On load
setTimeout(function(){
	acritExpWildberriesInitialize();
}, 500);
$(document).ready(function(){
	acritExpWildberriesInitialize();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpWildberriesInitialize();
});