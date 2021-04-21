console.log('script')
getYMReviewsModel = function(pageNumber, bScroll)
{
	if(!bScroll)
		bScroll = false;
	if(!pageNumber)
		pageNumber = 1;
	
	var obParams = BX.message("PARAMS_YM"),
		count = obParams.REVIEWS_COUNT,
		container = $('.model_reviews_wrapper');
	obParams.PAGE = pageNumber;

	container.find('.pager_info .load').show();
	container.find('.pager_info .num').hide();
	container.find('.pager_block > a').addClass("disabled");
	
	BX.ajax({
		url: BX.message("AJAX_PATH_YM")+"/ajax.php",
		method: 'POST',
		data: BX.ajax.prepareData(obParams),
		dataType: 'html',
		processData: false,
		start: true,
		headers: [{'name': 'X-Requested-With', 'value': 'XMLHttpRequest'}],
		onfailure: function(data) {
			alert('Error connecting server');
		},
		onsuccess: function(html){
			// container.find('.reviews').html(data)

			var ob = BX.processHTML(html);
			// inject
			BX('ya_reviews').innerHTML = ob.HTML;

			container.find('.pager_info .load').hide()
			container.find('.pager_info .num').show()
				
			if(container.find('.js_reviews .total').length > 0)
			{
				var page = parseInt(container.find('.js_reviews .pager_info').text());
				var total = parseInt(container.find('.js_reviews .total').text());
				var max = Math.floor(total/count);
				if(total % count != 0)
					max = max + 1;
					
				container.find('.pager_info .num span').html(page.toString() + " / " + max.toString());

				if(page > 1)
					container.find(".prev.disabled").removeClass("disabled");
					
					
				if(page < max)
					container.find(".next.disabled").removeClass("disabled");
					
				container.find(".prev").off('click');
				container.find(".prev").click(function(){
					if(page > 1){
						page = page - 1;
						updateYRMS(page, true);
					}
				});
				container.find(".next").off('click');
				container.find(".next").click(function(){
					if(page < max){
						page = page + 1;
						updateYRMS(page, true);
					}
				});
				if (1 > total) {
					container.find('.pager_info .num span').html(" - ");
				}
			}else{
				container.find('.pager_info .num span').html(" - ");
			}
			if(bScroll)
			{
				$('html, body').animate({
					scrollTop: container.offset().top
				}, 1000);
			}
		}
	})

	/*$.ajax({
		url: BX.message("AJAX_PATH_YM")+"/ajax.php",
		type: "POST",
		dataType: "html",
		data: "PAGE="+pageNumber,
		success: function(data){
			container.find('.reviews').html(data)
			container.find('.page .load').hide()
			container.find('.page .num').show()
				
			if(container.find('.reviews .total').length > 0)
			{
				var page = parseInt(container.find('.reviews .page').text());
				var total = parseInt(container.find('.reviews .total').text());
				var max = Math.floor(total/count);
				if(total % count != 0)
					max = max + 1;
					
				container.find('.page .num span').html(page.toString() + " / " + max.toString());

				if(page > 1)
					container.find(".prev.disabled").removeClass("disabled");
					
					
				if(page < max)
					container.find(".next.disabled").removeClass("disabled");
					
				container.find(".prev").off('click');
				container.find(".prev").click(function(){
					if(page > 1){
						page = page - 1;
						updateYRMS(page, true);
					}
				});
				container.find(".next").off('click');
				container.find(".next").click(function(){
					if(page < max){
						page = page + 1;
						updateYRMS(page, true);
					}
				});
				if (1 > total) {
					container.find('.page .num span').html(" - ");
				}
			}else{
				container.find('.page .num span').html(" - ");
			}
			if(bScroll)
			{
				$('html, body').animate({
					scrollTop: container.offset().top
				}, 1000);
			}
		}
	});*/
}