if(typeof window.JCCatalogDelivery === 'undefined'){
	window.JCCatalogDelivery = function(rand, arParams, arResult){
		this.rand = rand;

		this.timerPlusMinus = false;

		this.params = {};
		this.result = {};

		this.$popup = false;
		this.$block = false;
		this.$form = false;
		this.$error = false;
		this.$baseFields = false;

		if(typeof arResult === 'object'){
			this.result = arResult;
		}

		if(typeof arParams === 'object'){
			this.params = arParams;

			this.init();
		}
	};

	window.JCCatalogDelivery.prototype = {
		init: function(){
			this.jsSolutionOptions = typeof window['arMaxOptions'] === 'object' ? window['arMaxOptions'] : false;

			this.ratio = this.result.PRODUCT.RATIO;
			this.ratio_is_float = this.result.PRODUCT.RATIO_IS_FLOAT;
			this.ratio = this.ratio_is_float ? parseFloat(this.ratio) : parseInt(this.ratio, 10);

			if(this.ratio_is_float && typeof this.jsSolutionOptions === 'object'){
				this.ratio = Math.round(this.ratio * arMaxOptions.JS_ITEM_CLICK.precisionFactor) / arMaxOptions.JS_ITEM_CLICK.precisionFactor;
			}

			this.$block = $('#catalog-delivery-' + this.rand);

			if(this.$block.length){
				this.$popup = this.$block.closest('.popup');
				this.bPopup = this.$popup.length > 0;

				this.$form = this.$block.find('form[name=catalog-delivery]');
				this.$error = this.$block.find('.catalog-delivery-error-text');
				this.$baseFields = this.$block.find('.catalog-delivery-fields-base');

				if(this.$baseFields.length){
					var $firstField = this.$baseFields.find('.catalog-delivery-field').first();
					this.maxBaseFieldsHeight = $firstField.actual('height') + parseInt($firstField.css('margin-bottom'));
				}

				var that = this;

				var waitInterval = setInterval(function(){
					if(that.$block.height()){
						clearInterval(waitInterval);

						if(typeof CheckPopupTop === 'function'){
							CheckPopupTop();
						}
						that.onResizeHandler();
					}
				}, 100);
			}

			this.bindEvents();
		},

		sendForm: function(bCalculate){
			var url = this.result.AJAX_URL;
			if(url){
				if(bCalculate){
					this.$form.append('<input type="hidden" name="CALCULATE" value="Y" />');
				}

				var data = this.$form.serialize();
				var that = this;

				if(that.timerPlusMinus){
					clearTimeout(that.timerPlusMinus);
					that.timerPlusMinus = false;
				}

				var bLocationChanged = this.$form.find('input[name=LOCATION_CHANGED]').val() === 'Y';
				var locationCode = this.$form.find('input[name=LOCATION]').val();

				if(bCalculate){
					$.ajax({
						url: url,
						type: 'POST',
						data: data,
						beforeSend: function(){
							that.$block.addClass('sending');
						},
						success: function(response){
							that.$block.wrap('<div></div>');
							var $wrap = that.$block.parent();
							$wrap.html(response);
							that.$block = $wrap.find('.catalog-delivery');
							$wrap.find('>div').unwrap('<div></div>');

							if(bLocationChanged){
								BX.onCustomEvent('onCatalogDeliveryChangeLocation', [{code: locationCode}]);
							}
						},
						error: function(xhr, ajaxOptions, thrownError){
							that.$block.addClass('haserror');
							that.$error.text(BX.message.CD_T_ERROR_REQUEST + ' ' + thrownError + ' (' + xhr.status + ')');
						},
						complete: function(){
							that.$form.find('input[name=CALCULATE]').remove();
							that.$block.removeClass('sending');
						}
					});
				}
				else{
					$.ajax({
						url: url,
						type: 'POST',
						data: data
					});
				}
			}
		},

		bindEvents: function(){
			var that = this;

			if(this.$block.length){
				if(this.$form.length){
					this.$form.submit(function(e){
						e.preventDefault();
					});

					this.$form.find('input, select').change(function(){
						var $this = $(this);

						if($(this).data('send') === 'Y'){
							that.sendForm(true);
						}
					});
				}

				if($.inArray('LOCATION', this.params.CHANGEABLE_FIELDS) !== -1){
					this.$block.find('.catalog-delivery-title-city').click(function(e){
						that.$block.addClass('search');
					});

					this.$block.find('input[name=LOCATION_SEARCH]').change(function(e){
						var $this = $(this);
						var value = $this.val();
						if(value.length){
							var city = $this.closest('.bx-ui-sls-input-block').find('.bx-ui-sls-fake').val();
							var oldValue = that.$form.find('input[name=LOCATION]').val();

							that.$block.find('.catalog-delivery-title-city>span').eq(0).html(city);
							that.$block.removeClass('search');

							if(oldValue != value){
								that.$form.find('input[name=LOCATION_CHANGED]').val('Y');
								that.$form.find('input[name=LOCATION]').val(value).change();
							}
						}
					});
				}

				this.$block.find('.hasdropdown .catalog-delivery-field-box-value').click(function(){
					var bOpen = $(this).closest('.catalog-delivery-field-box.open').length;
					$('.catalog-delivery-field-box.open').removeClass('open');
					if(!bOpen){
						$(this).closest('.catalog-delivery-field-box').addClass('open');
					}
				});

				this.$block.find('.catalog-delivery-field-box-dropdown-item').click(function(){
					var value = $(this).attr('data-value');
					var $box = $(this).closest('.catalog-delivery-field-box');
					if($box.length){
						var $select = $box.find('select');
						if($select.length){
							$box.find('.catalog-delivery-field-box-value span').text($(this).text());
							$box.removeClass('open');

							var oldValue = $select.val();
							if(oldValue != value){
								$select.val(value).change();
							}
						}
					}
				});

				this.$block.find('.catalog-delivery-item-head').click(function(e){
					var $item = $(this).closest('.catalog-delivery-item');
					if($item.length){
						$item.toggleClass('open');
						$item.find('.catalog-delivery-item-more').slideToggle();
						e.stopPropagation();
					}

					that.$form.find('input[name^=DELIVERY]').remove();

					var cnt = that.$block.find('.catalog-delivery-item.open').length;
					if(cnt){
						for(var i = 0; i < cnt; ++i){
							that.$form.append('<input type="hidden" name="DELIVERY[' + i + ']" value="' + that.$block.find('.catalog-delivery-item.open').eq(i).data('id') + '" />');
						}
					}
					else{
						that.$form.append('<input type="hidden" name="DELIVERY[]" value="" />');
					}

					if(that.params.SAVE_IN_SESSION === 'Y'){
						that.sendForm(false);
					}
				});

				if($.inArray('QUANTITY', this.params.CHANGEABLE_FIELDS) !== -1){
					var $input = this.$block.find('.catalog-delivery-field_quantity .catalog-delivery-field-box-value input[type=text]');
					if($input.length){
						this.$block.find('.catalog-delivery-field_quantity .catalog-delivery-field-box-value .plus').click(function(e){
							if(that.timerPlusMinus){
								clearTimeout(that.timerPlusMinus);
								that.timerPlusMinus = false;
							}

							var value = $input.val();
							value = that.ratio_is_float ? parseFloat(value) : parseInt(value, 10);
							value += that.ratio;
							if(value < that.ratio){
								value = that.ratio;
							}
							if(that.ratio_is_float && typeof that.jsSolutionOptions === 'object'){
								value = Math.round(value * that.jsSolutionOptions.JS_ITEM_CLICK.precisionFactor) / that.jsSolutionOptions.JS_ITEM_CLICK.precisionFactor;
							}

							$input.val(value);

							if(that.result.PRODUCT_QUANTITY != value){
								that.timerPlusMinus = setTimeout(function(){
									$input.change();
								}, 1000);
							}
						});

						this.$block.find('.catalog-delivery-field_quantity .catalog-delivery-field-box-value .minus').click(function(e){
							if(that.timerPlusMinus){
								clearTimeout(that.timerPlusMinus);
								that.timerPlusMinus = false;
							}

							var value = $input.val();
							value = that.ratio_is_float ? parseFloat(value) : parseInt(value, 10);
							value -= that.ratio;
							if(value < that.ratio){
								value = that.ratio;
							}
							if(that.ratio_is_float && typeof that.jsSolutionOptions === 'object'){
								value = Math.round(value * that.jsSolutionOptions.JS_ITEM_CLICK.precisionFactor) / that.jsSolutionOptions.JS_ITEM_CLICK.precisionFactor;
							}

							$input.val(value);

							if(that.result.PRODUCT_QUANTITY != value){
								that.timerPlusMinus = setTimeout(function(){
									$input.change();
								}, 1000);
							}
						});

						$input.change(function(e){
							var value = $(this).val();
							value = that.ratio_is_float ? parseFloat(value) : parseInt(value, 10);
							if(value > that.ratio){
								diff = value % that.ratio;
								if(diff > 0){
									value -= diff;
								}
							}
							else{
								value = that.ratio;
							}
							if(that.ratio_is_float && typeof that.jsSolutionOptions === 'object'){
								value = Math.round(value * that.jsSolutionOptions.JS_ITEM_CLICK.precisionFactor) / that.jsSolutionOptions.JS_ITEM_CLICK.precisionFactor;
							}

							$(this).val(value);
						});

						if(typeof $.fn.numeric === 'function'){
							$input.numeric(this.ratio_is_float ? {allow: '.'} : {});
						}
					}
				}

				if(this.$baseFields.length){
					this.$block.find('.catalog-delivery-fields-opener').click(function(e){
						$(this).closest('.catalog-delivery-fields').toggleClass('open');
						that.onResizeHandler();
					});
				}

				if(this.$baseFields.length || this.bPopup){
					$(window).resize(function(){
						that.onResizeHandler();
					});
				}
			}
		},

		onResizeHandler:function (){
			if(this.$baseFields.length){
				var height = this.$baseFields.actual('height');
				if(this.$block.hasClass('shortfields')){
					if(height <= this.maxBaseFieldsHeight){
						this.$block.removeClass('shortfields');
						if(typeof CheckPopupTop === 'function'){
							CheckPopupTop();
						}
					}
				}
				else{
					if(height > this.maxBaseFieldsHeight){
						this.$block.addClass('shortfields');
						if(typeof CheckPopupTop === 'function'){
							CheckPopupTop();
						}
					}
				}
			}

			if(this.bPopup){
				if(window.matchMedia('(max-width:430px)').matches){
					$('html').css('overflow', 'hidden');
				}
				else{
					$('html').css('overflow', '');
				}
			}
		}
	};

	$(document).on('click', function(e){
		if(!$(e.target).closest('.catalog-delivery-field-box.open').length){
			$('.catalog-delivery-field-box.open').removeClass('open');
		}
	});
}