BX.addCustomEvent('onSlideInit', function(eventdata) {
	try{
		ignoreResize.push(true);
		if(eventdata){
			var slider = eventdata.slider;
			if(slider){
				$('.news_akc_block .items .item').sliceHeight();
			}
		}
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});