function updateYRMS(page, flag, path, count)
{
	if(!flag) flag = false;
	
	var ymrs = $('.yandex_market_reviews_store');
	ymrs.find('.page .load').show()
	ymrs.find('.page .num').hide()
	ymrs.find('.prev').addClass("disabled");
	ymrs.find('.next').addClass("disabled");
	
	$.ajax({
		url: path+"/ajax.handler.php",
		type: "POST",
		dataType: "html",
		data: "PAGE="+page,
		success: function(data){
			ymrs.find('.reviews').html(data)
			ymrs.find('.page .load').hide()
			ymrs.find('.page .num').show()
				
			if(ymrs.find('.reviews .total').length > 0)
			{
				var page = parseInt(ymrs.find('.reviews .page').text());
				var total = parseInt(ymrs.find('.reviews .total').text());
				var max = Math.floor(total/count);
				if(total % count != 0)
					max = max + 1;
					
				ymrs.find('.page .num span').html(page.toString() + " / " + max.toString());

				if(page > 1)
					ymrs.find(".prev.disabled").removeClass("disabled");
					
					
				if(page < max)
					ymrs.find(".next.disabled").removeClass("disabled");
					
				ymrs.find(".prev").unbind('click');
				ymrs.find(".prev").click(function(){
					if(page > 1){
						page = page - 1;
						updateYRMS(page, false, path, count);
					}
				});
				ymrs.find(".next").unbind('click');
				ymrs.find(".next").click(function(){
					if(page < max){
						page = page + 1;
						updateYRMS(page, false, path, count);
					}
				});
			}else{
				ymrs.find('.page .num span').html(" - ");
			}
			if(!flag)
			{
				$('html, body').animate({
					scrollTop: ymrs.offset().top
				}, 1000);
			}
		}
	});
}