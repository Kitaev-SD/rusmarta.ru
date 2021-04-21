$(document).ready(function(){
	if($('.detail .galery-block .flexslider .item').length)
	{
		$('.detail .galery-block .flexslider .item').sliceHeight({lineheight: -3});
		if($('.detail .galery #carousel').length)
		{
			$('.detail .galery #carousel').flexslider({
				animation: 'slide',
				controlNav: false,
				animationLoop: true,
				slideshow: false,
				itemWidth: 77,
				itemMargin: 7.5,
				minItems: 2,
				maxItems: 4,
				asNavFor: '.detail .galery #slider'
			});
		}
	}
	if($('.docs-block .blocks').length)
		$('.docs-block .blocks .inner-wrapper').sliceHeight({'row': '.blocks', 'item': '.inner-wrapper'});
	if($('.projects.item-views').length)
		$('.projects.item-views .item').sliceHeight();
	$('.items-services .item').sliceHeight();
	SetFixedAskBlock();
});
BX.addCustomEvent('onSlideInit', function(eventdata){
	try{
		ignoreResize.push(true);
		if(eventdata)
		{
			var slider = eventdata.slider;
			if(slider.hasClass('top_slider'))
			{
				slider.find('.item').css('opacity',1);
			}
			if(slider.hasClass('small_slider'))
			{
				$('.detail .small-gallery-block .item').sliceHeight({lineheight: -3});
			}
			if(slider.hasClass('big_slider'))
			{
				$('.detail .big_slider .item').sliceHeight({lineheight: -3});
				$(window).resize();
			}
		}
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
})