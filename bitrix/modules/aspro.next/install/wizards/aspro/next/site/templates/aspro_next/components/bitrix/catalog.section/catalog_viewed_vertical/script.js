BX.addCustomEvent('onSlideInit', function(eventdata) {
	try{
		ignoreResize.push(true);
		if(eventdata){
			var slider = eventdata.slider;
			if(slider){
				$('.viewed_block .rows_block .item .item-title').sliceHeight({'autoslicecount': false, 'slice': 8});
				$('.viewed_block .rows_block .item').sliceHeight({'autoslicecount': false, 'slice': 8});
			}
		}
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});