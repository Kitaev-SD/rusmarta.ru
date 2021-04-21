$(document).ready(function(){
	$(document).on('click', '.confirm_region .aprove', function(){
		var _this = $(this);
		$.removeCookie('current_region');
		$.cookie('current_region', _this.data('id'), {path: '/',domain: arNextOptions['SITE_ADDRESS']});
		$('.confirm_region').remove();
		if(typeof _this.data('href') !== 'undefined')
			location.href = _this.data('href');
	});

	$(document).on('click', '.js_city_change', function(){
		var _this = $(this);
		_this.closest('.region_wrapper').find('.js_city_chooser').trigger('click');
		if(_this.closest('.top_mobile_region').length)
		{
			$('.burger').click();

			$('.mobile_regions > ul > li > a').click()
		}
		$('.confirm_region').remove();
	});

	$(document).on('click', '.js_city_chooser', function(){
		var _this = $(this);
		$('.confirm_region').remove();
	});
});