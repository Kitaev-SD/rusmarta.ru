/*pagination*/
$(document).on('click', '.js_reviews .nums .flex-direction-nav > li a', function(){
	var _this = $(this);
	getYMReviewsModel(_this.data('page'), true)
})

$(document).on('click', '.js_reviews .nums .dark_link', function(){
	var _this = $(this);
	getYMReviewsModel(_this.data('page'), true)
})
/**/

getYMReviewsModel = function(pageNumber, bScroll)
{
	if(!bScroll)
		bScroll = false;

	if(!pageNumber)
		pageNumber = 1;
	
	var obParams = BX.message("PARAMS_YM"),
		count = obParams.REVIEWS_COUNT,
		container = $('.model_reviews_wrapper'),
		objUrl = parseUrlQuery(),
		add_url = "";

	if("clear_cache" in objUrl)
	{
		if(objUrl.clear_cache == "Y")
			add_url += "?clear_cache=Y";
	}
	obParams.PAGE = pageNumber;

	container.find('.js_reviews').removeClass('initied');

	BX.ajax({
		url: BX.message("AJAX_PATH_YM")+"/ajax.php"+add_url,
		method: 'POST',
		data: BX.ajax.prepareData(obParams),
		dataType: 'html',
		processData: false,
		start: true,
		headers: [{'name': 'X-Requested-With', 'value': 'XMLHttpRequest'}],
		onfailure: function(data) {
			console.log(data);
			alert('Error connecting server');
		},
		onsuccess: function(html){
			var ob = BX.processHTML(html),
				offset = container.offset().top;

			// inject
			BX('ya_reviews').innerHTML = ob.HTML;

			container.find('.js_reviews').addClass('initied');
			if(container.find('.js_reviews .items').data('counts'))
			{
				if($('.product_reviews_tab').length)
					$('.product_reviews_tab a .count').removeClass('empty').text('('+container.find('.js_reviews .items').data('counts')+')');

				if($('.reviews_item_tab').length)
					$('.reviews_item_tab a .count').removeClass('empty').text('('+container.find('.js_reviews .items').data('counts')+')');
				
				if($('.info_item .count_reviews .text>span').length){
					var countReviews = $('#reviews_content .items').data('counts'),
						countMessage = declOfNum(countReviews, [BX.message('PEOPLE'), BX.message('PEOPLE2'), BX.message('PEOPLE')]);

					$('.info_item .count_reviews .text>span').text(countReviews + ' ' + countMessage);
					$('.info_item .count_reviews').addClass('initied');
				}
			}

			if(bScroll)
			{
				if(arNextOptions["THEME"]["TOP_MENU_FIXED"] == "Y")
				{
					if($('#headerfixed').length)
						offset -= $('#headerfixed').height()+10;
					if($('.product-item-detail-tabs-container-fixed').length)
						offset -= $('.product-item-detail-tabs-container-fixed').height();
				}

				$('html, body').animate({
					scrollTop: offset
				}, 1000);
			}
		}
	})
}