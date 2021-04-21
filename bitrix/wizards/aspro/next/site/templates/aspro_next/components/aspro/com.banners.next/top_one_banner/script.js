function checkNavColor(slider){
	var nav_color_flex = slider.find('.flex-active-slide').data('nav_color');
	if(nav_color_flex == 'dark')
		slider.find('.flex-control-nav').addClass('flex-dark');
	else
		slider.find('.flex-control-nav').removeClass('flex-dark');

	var eventdata = {slider: slider};
	BX.onCustomEvent('onSlide', [eventdata]);
}
function checkHeight(){
	/*if($('.col-m-pull-60').length)
	{
		if(window.matchMedia('(min-width:992px)').matches)
		{
			var height = $('.col-m-pull-60').actual('outerHeight'),
				padding = parseInt($('.col-m-pull-60 .item').css('padding-bottom'));

			$('.top_slider_wrapp .flexslider .slides > li, .top_slider_wrapp .flexslider, .top_slider_wrapp .flexslider .slides > li td').height(height-padding);
		}
		else
		{
			$('.top_slider_wrapp .flexslider .slides > li, .top_slider_wrapp .flexslider, .top_slider_wrapp .flexslider .slides > li td').height('');
		}
	}*/
}
$(document).ready(function(){
	if($('.top_slider_wrapp .flexslider').length){
		var config = {"controlNav": true, "animationLoop": true, "pauseOnHover" : true};
		if(typeof(arNextOptions['THEME']) != 'undefined'){
			var slideshowSpeed = Math.abs(parseInt(arNextOptions['THEME']['BIGBANNER_SLIDESSHOWSPEED']));
			var animationSpeed = Math.abs(parseInt(arNextOptions['THEME']['BIGBANNER_ANIMATIONSPEED']));
			config["slideshow"] = (slideshowSpeed && arNextOptions['THEME']['BIGBANNER_ANIMATIONTYPE'].length ? true : false);
			config["animation"] = (arNextOptions['THEME']['BIGBANNER_ANIMATIONTYPE'] === 'FADE' ? 'fade' : 'slide');
			if(animationSpeed >= 0){
				config["animationSpeed"] = animationSpeed;
			}
			if(slideshowSpeed >= 0){
				config["slideshowSpeed"] = slideshowSpeed;
			}
			if(arNextOptions['THEME']['BIGBANNER_ANIMATIONTYPE'] !== 'FADE'){
				config["direction"] = (arNextOptions['THEME']['BIGBANNER_ANIMATIONTYPE'] === 'SLIDE_VERTICAL' ? 'vertical' : 'horizontal');
			}
			config.start = function(slider){
				checkNavColor(slider);
				
				if(slider.count <= 1){
					slider.find('.flex-direction-nav li').addClass('flex-disabled');
				}
				$(slider).find('.flex-control-nav').css('opacity',1);
			}
			config.after = function(slider){
				checkNavColor(slider);
			}
		}

		$(".top_slider_wrapp .flexslider").flexslider(config);
	}
	
	checkHeight();

	BX.addCustomEvent('onWindowResize', function(eventdata){
		try{
			ignoreResize.push(true);
			checkHeight();
			CoverPlayerHtml()
		}
		catch(e){}
		finally{
			ignoreResize.pop();
		}
	})
});