var timerHide = false;

if(!funcDefined('showToggles'))
{
	showToggles = function()
	{
		new DG.OnOffSwitchAuto({
	        cls:'.block-item.active .custom-switch',
	        textOn:"",
	        height:33,
	        heightTrack:16,
	        textOff:"",
	        trackColorOff:"f5f5f5",
	        listener:function(name, checked){
	        	var bNested = $('input[name='+name+']').closest('.values').length;
	        	if(checked)
					$('input[name='+name+']').val('Y');

				else
					$('input[name='+name+']').val('N');

				if(bNested)
				{
					var ajax_btn = $('<div class="btn-ajax-block animation-opacity"></div>'),
						option_wrapper = $('input[name='+name+']').closest('.option-wrapper'),
						pos = BX.pos(option_wrapper[0], true),
						top = 0;
					ajax_btn.html($('.values > .apply-block').html());
					option_wrapper.toggleClass('disabled');
					top = pos.top+$('.style-switcher .header').actual('outerHeight');
					ajax_btn.css('top',top);
					if($('.btn-ajax-block').length)
						$('.btn-ajax-block').remove();
					ajax_btn.appendTo($('.style-switcher'));
					ajax_btn.addClass('opacity1');
				}

				setTimeout(function(){
					if(!bNested)
						$('form[name=style-switcher]').submit();
				},200);
	        }
	    });
	}
}

$(document).ready(function() {
	$('.style-switcher .presets').mCustomScrollbar({
		mouseWheel: {
			scrollAmount: 150,
			preventDefault: true
		},
		callbacks:{
			onScroll: function(){
				var topPositionPresets = $('.style-switcher .presets .mCSB_container').css('top');

				$.cookie('STYLE_SWITCHER_SCROLL_PRESET_POSITION', topPositionPresets, {path: arNextOptions['SITE_DIR']});
			}
		},
		setTop: (typeof($.cookie('STYLE_SWITCHER_SCROLL_PRESET_POSITION')) !== 'undefined' ? $.cookie('STYLE_SWITCHER_SCROLL_PRESET_POSITION') : 0),
	});

	$('.style-switcher .left-block').mCustomScrollbar({
		mouseWheel: {
			scrollAmount: 150,
			preventDefault: true
		},
		callbacks:{
			onScroll: function(){
				var topPositionLeftBlock = $('.style-switcher .left-block .mCSB_container').css('top');

				$.cookie('STYLE_SWITCHER_SCROLL_LEFT_POSITION', topPositionLeftBlock, {path: arNextOptions['SITE_DIR']});
			}
		},
		setTop: (typeof($.cookie('STYLE_SWITCHER_SCROLL_LEFT_POSITION')) !== 'undefined' ? $.cookie('STYLE_SWITCHER_SCROLL_LEFT_POSITION') : 0),
	});

	$('.style-switcher .right-block').mCustomScrollbar({
		mouseWheel: {
			scrollAmount: 150,
			preventDefault: true,
		},
		callbacks:{
			onScroll: function(){
				var topPositionRightBlock = $('.style-switcher .right-block .mCSB_container').css('top');

				$.cookie('STYLE_SWITCHER_SCROLL_RIGHT_POSITION', topPositionRightBlock, {path: arNextOptions['SITE_DIR']});
			}
		},
		setTop: (typeof($.cookie('STYLE_SWITCHER_SCROLL_RIGHT_POSITION')) !== 'undefined' ? $.cookie('STYLE_SWITCHER_SCROLL_RIGHT_POSITION') : 0),
	});

	$('.style-switcher .item input[type=checkbox]').on('change', function(){
		var _this =  $(this);
		if(_this.is(':checked'))
			_this.val('Y');
		else
			_this.val('N');
		$('form[name=style-switcher]').submit();
	})

	$('.style-switcher .can_save .save_btn').on('click', function(){
		var _this = $(this);

		if(timerHide){
			clearTimeout(timerHide);
			timerHide = false;
		}

		$.ajax({
			type:"POST",
			url:arNextOptions['SITE_DIR']+"ajax/options_save.php",
			data:{'SAVE_OPTIONS':'Y'},
			dataType:"json",
			success:function(response){
				if("STATUS" in response)
				{
					if(!$('.save_config_status').length)
						$('<div class="save_config_status"><span></span></div>').appendTo(_this.parent());
					if(response.STATUS === 'OK')
						$('.save_config_status').addClass('success');
					else
						$('.save_config_status').addClass('error');

					$('.save_config_status span').text(BX.message(response.MESSAGE));

					$('.save_config_status').slideDown(200);
					timerHide = setTimeout(function(){
						// here delayed functions in event
						$('.save_config_status').slideUp(200, function(){
							$(this).remove();
						})
					}, 1000);
				}
			}
		})
	})

	showToggles(); //replace checkbox in custom toggle

	if($.cookie('styleSwitcherType') === 'presets'){
		$('.style-switcher .presets').addClass('active');
	}

	if($.cookie('styleSwitcher') === 'open'){
		$('.style-switcher').addClass('active');

		if($.cookie('styleSwitcherType') === 'presets'){
			$('.style-switcher .switch_presets').addClass('active');
		}
		else{
			$('.style-switcher .switch').addClass('active');
		}
	}

	if($('.base_color_custom input[type=hidden]').length)
	{
		$('.base_color_custom input[type=hidden]').each(function(){
			var _this = $(this),
				parent = $(this).closest('.base_color_custom');
			_this.spectrum({
				preferredFormat: 'hex',
				showButtons: true,
				showInput: true,
				showPalette: false,
				appendTo: parent,
				chooseText: BX.message('CUSTOM_COLOR_CHOOSE'),
				cancelText: BX.message('CUSTOM_COLOR_CANCEL'),
				containerClassName: 'custom_picker_container',
				replacerClassName: 'custom_picker_replacer',
				clickoutFiresChange: false,
				move: function(color) {
					var colorCode = color.toHexString();
					parent.find('span span').attr('style', 'background:' + colorCode);
				},
				hide: function(color) {
					var colorCode = color.toHexString();
					parent.find('span span').attr('style', 'background:' + colorCode);
				},
				change: function(color) {
					parent.addClass('current').siblings().removeClass('current');

					$('form[name=style-switcher] input[name=' + parent.find('.click_block').data('option-id') + ']').val(parent.find('.click_block').data('option-value'));
					$('form[name=style-switcher]').submit();
				}
			});
		})
	}

	$('.base_color_custom').click(function(e) {
		e.preventDefault();
		$('input[name='+$(this).data('name')+']').spectrum('toggle');
		return false;
	});

	if($('.base_color.current').length)
	{
		$('.base_color.current').each(function(){
			var color_block = $(this).closest('.options').find('.base_color_custom'),
				curcolor = $(this).data('color');
			if(curcolor != undefined && curcolor.length)
			{
				$('input[name='+color_block.data('name')+']').spectrum('set', curcolor);
				color_block.find('span span').attr('style', 'background:' + curcolor);
			}
		})
	}

	$('.style-switcher .switch,.style-switcher .switch_presets').click(function(e){
		e.preventDefault();
		var styleswitcher = $(this).closest('.style-switcher');
		var presets = styleswitcher.find('.presets');
		var parametrs = styleswitcher.find('.parametrs');
		var bSwitchPresets = $(this).hasClass('switch_presets');

		if(styleswitcher.hasClass('active')){

			// current switch type
			var typeSwitcher = $.cookie('styleSwitcherType');

			// change switcher bgcolor
			styleswitcher.find('.switch').removeClass('active');
			styleswitcher.find('.switch_presets').removeClass('active');

			if((bSwitchPresets && typeSwitcher === 'presets') || (!bSwitchPresets && typeSwitcher === 'parametrs')){
				setTimeout(function(){
					HideHintBlock(true);
				}, 300);

				// remove switcher type
				$.removeCookie('styleSwitcherType', {path: '/'});

				// save switcher as hidden
				$.removeCookie('styleSwitcher', {path: '/'});

				// hide switcher with transition
				styleswitcher.addClass('closes');
				setTimeout(function(){
					styleswitcher.removeClass('active');
				}, 500)
			}
			else{
				HideHintBlock(false);

				// save switcher type
				$.cookie('styleSwitcherType', (bSwitchPresets ? 'presets' : 'parametrs'), {path: '/'});

				// hide switcher title
				styleswitcher.find('.header .title').hide();

				// set presets visible or hidden with transition and change switcher bgcolor
				if(bSwitchPresets){
					styleswitcher.find('.header .title.title-presets').show();
					styleswitcher.find('.switch_presets').addClass('active');
					presets.addClass('active');
				}
				else{
					styleswitcher.find('.header .title.title-parametrs').show();
					styleswitcher.find('.switch').addClass('active');
					presets.removeClass('active');
				}
			}
		}
		else{
			HideHintBlock(true);

			// change switcher bgcolor
			$(this).addClass('active');

			// save switcher type
			$.cookie('styleSwitcherType', (bSwitchPresets ? 'presets' : 'parametrs'), {path: '/'});

			// hide switcher title
			styleswitcher.find('.header .title').hide();

			// set presets visible or hidden immediately before adding .active to .style-switcher
			if(bSwitchPresets){
				styleswitcher.find('.header .title.title-presets').show();
				presets.addClass('active');
			}
			else{
				styleswitcher.find('.header .title.title-parametrs').show();
				presets.removeClass('active');
			}

			// show overlay
			ShowOverlay();

			// show switcher with transition
			styleswitcher.removeClass('closes').addClass('active');

			// save switcher as open
			$.cookie('styleSwitcher', 'open', {path: '/'});
		}
	});

	HideHintBlock = function(bHideOverlay)
	{
		if(typeof bHideOverlay === 'undefined' || bHideOverlay){
			HideOverlay();
		}
		$.cookie('clickedSwitcher', 'Y', {path: '/'});
		if($('.hint-theme').length)
		{
			$('.hint-theme').fadeIn(300, function(){
				$('.hint-theme').remove();
			});
		}
	}

	$(document).on('click', '.close-overlay', function(){
		HideHintBlock()
	})

	$(document).on('click', '.jqmOverlay', function(){
		var styleswitcher = $('.style-switcher');

		if(styleswitcher.length && styleswitcher.hasClass('active')){
			if(!$('.hint-theme').length){
				setTimeout(function(){
					HideOverlay();
				}, 300);
			}

			styleswitcher.each(function(){
				var _this = $(this);
				_this.addClass('closes');

				setTimeout(function(){
					_this.removeClass('active');
				},500);

				$('.form_demo-switcher').animate({
					left: '-' + $('.form_demo-switcher').outerWidth() + 'px'
				}, 100).removeClass('active abs');
			})

			$('.style-switcher .switch,.style-switcher .switch_presets').removeClass('active');

			$.removeCookie('styleSwitcherType', {path: '/'});
			$.removeCookie('styleSwitcher', {path: '/'});
		}
	})

	$('.style-switcher .section-block').on('click', function(){
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
		$('.style-switcher .right-block .block-item').removeClass('active');
		$('.style-switcher .right-block .block-item:eq('+$(this).index()+')').addClass('active');
		$.cookie('styleSwitcherTabIndex', $(this).index(), {path: '/'});

		//replace checkbox in custom toggle
		if(!$(this).hasClass('toggle_initied'))
			showToggles();
		$(this).addClass('toggle_initied');
	})

	$('.style-switcher .reset').click(function(e){
		$('form[name=style-switcher]').append('<input type="hidden" name="THEME" value="default" />');
		$('form[name=style-switcher]').submit();
	});

	$(document).on('click', '.style-switcher .apply', function(){
		$('form[name=style-switcher]').submit();
	})
	$('.style-switcher .sup-params.options .block-title').click(function(){
		$(this).next().slideToggle();
	})

	$(document).on('click', '.style-switcher .presets .preset-block', function(){
		var $this = $(this);

		if($this.hasClass('current') || $this.hasClass('editing')){
			return;
		}

		var i = $this.closest('.item').index();
		if(typeof arNextOptions.PRESETS === 'object' && typeof arNextOptions.PRESETS[i] === 'object'){
			var options = {
				'backurl': arNextOptions['SITE_DIR']
			};
			var order = [];
			var serialize = $('form[name=style-switcher]').serializeArray();
			for (j in serialize){
				options[serialize[j].name] = serialize[j].value;
			}

			if(arNextOptions.PRESETS[i]['OPTIONS'] && typeof arNextOptions.PRESETS[i]['OPTIONS'] === 'object'){
				for(j in arNextOptions.PRESETS[i]['OPTIONS']){
					var val = arNextOptions.PRESETS[i]['OPTIONS'][j];
					if(typeof val !== 'object'){
						options[j] = val;
					}
					else{
						if(typeof val.VALUE !== 'undefined'){
							options[j] = val.VALUE;

							if(typeof val.SUB_PARAMS === 'object'){
								for(z in val.SUB_PARAMS){
									var subval = val.SUB_PARAMS[z];
									if(typeof subval !== 'object'){
										options[val.VALUE + '_' + z] = subval;
									}
									else{
										if(typeof subval.VALUE !== 'undefined'){
											options[val.VALUE + '_' + z] = subval.VALUE;

											if(typeof subval.TEMPLATE !== 'undefined'){
												options[val.VALUE + '_' + z + '_TEMPLATE'] = subval.TEMPLATE;
											}
										}
									}
								}
							}

							if(typeof val.ORDER === 'object'){
								order.push({
									NAME: 'SORT_ORDER_' + j + '_' + val.VALUE,
									VALUE: val.ORDER.join(',')
								});
							}
							else if(typeof val.ORDER === 'string'){
								order.push({
									NAME: 'SORT_ORDER_' + j + '_' + val.VALUE,
									VALUE: val.ORDER
								});
							}
						}
					}
				}
			}

			function _sendOptions(){
				$.ajax({
					type: 'POST',
					data: options,
					success: function(){
						$('.style-switcher .switch_presets').trigger('click');
						location.href = arNextOptions['SITE_DIR'];
					}
				});
			}

			function _sendOrder(){
				if(order.length){
					var sort = order.pop();
					$.ajax({
						url: arNextOptions['SITE_DIR'] + 'ajax/options_save_mainpage.php',
						type: 'POST',
						data: sort,
						success: function(){
							_sendOrder();
						}
					});
				}
				else{
					_sendOptions();
				}
			}

			_sendOrder();
		}
	})

	$('.style-switcher .options > .link-item,.style-switcher .options > div:not(.base_color_custom) span.link-item,.style-switcher .options > div:not(.base_color_custom) .click_block').click(function(e){
		var _this = $(this);
		if(_this.hasClass('current'))
			return;

		_this.addClass('current').siblings().removeClass('current');
		$('form[name=style-switcher] input[name=' + _this.data('option-id') + ']').val(_this.data('option-value'));

		if(typeof($(this).data('option-type')) != 'undefined') // set cookie for scroll block
			$.cookie('scroll_block', $(this).data('option-type'));

		if(typeof($(this).data('option-url')) != 'undefined') // set action form for redirect
			$('form[name=style-switcher]').prepend('<input type="hidden" name="backurl" value='+$(this).data('option-url')+' />');

		if(_this.closest('.options').hasClass('refresh-block'))
		{
			if(!_this.closest('.options').hasClass('sup-params'))
				var index = _this.index()-1;
			_this.closest('.item').find('.sup-params.options').removeClass('active');
			_this.closest('.item').find('.sup-params.options.s_'+_this.data('option-value')+'').addClass('active');
			$('form[name=style-switcher]').submit();
		}
		else
			$('form[name=style-switcher]').submit();
	});

	$('.tooltip-link').on('shown.bs.tooltip', function (e) {
		var tooltip_block = $(this).next(),
			wihdow_height = $(window).height(),
			scroll = $(this).closest('form').scrollTop(),
			pos = BX.pos($(this)[0], true),
			pos_tooltip = BX.pos(tooltip_block[0], true),
			pos_item_wrapper = BX.pos($(this).closest('.item')[0], true);

		if(!$(this).closest('.item').next().length && pos_tooltip.bottom > pos_item_wrapper.bottom)
		{
			tooltip_block.removeClass('bottom').addClass('top');
			tooltip_block.css({'top':(pos.top-tooltip_block.actual('outerHeight'))});
		}
	})
});