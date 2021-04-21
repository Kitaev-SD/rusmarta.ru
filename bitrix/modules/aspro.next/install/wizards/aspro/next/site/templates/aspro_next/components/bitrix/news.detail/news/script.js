$(document).ready(function(){
	if($('.docs-block .blocks').length)
		$('.docs-block .blocks .inner-wrapper').sliceHeight({'row': '.blocks', 'item': '.inner-wrapper'});
	if($('.projects.item-views').length)
	{
		$('.projects.item-views .item .image').sliceHeight({lineheight: -3});
		$('.projects.item-views .item').sliceHeight();
	}
	$('.items-services .item').sliceHeight();
	SetFixedAskBlock();
});
BX.addCustomEvent('onSlideInit', function(eventdata){
	try{
		ignoreResize.push(true);
		if(eventdata)
		{
			var slider = eventdata.slider;
			if(slider.hasClass('small_slider'))
				$('.detail .small-gallery-block .item').sliceHeight({lineheight: -3});
			if(slider.hasClass('big_slider'))
				$('.detail .big_slider .item').sliceHeight({lineheight: -3});
		}
		$(window).resize();
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
})