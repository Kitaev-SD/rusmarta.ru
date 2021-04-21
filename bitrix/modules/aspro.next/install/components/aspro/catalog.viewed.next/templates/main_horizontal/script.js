BX.addCustomEvent('onSlideInit', function(eventdata) {
	try{
		ignoreResize.push(true);
		if(eventdata){
			var slider = eventdata.slider;
			if(slider){
				$('.viewed_block .rows_block .item .item-title').sliceHeight({'autoslicecount': false, 'slice': 5});
				$('.viewed_block .rows_block .item').sliceHeight({'autoslicecount': false, 'slice': 5});
			}
		}
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});


showViewedItems = function(lastViewedTime, bShowMeasure, $viewedSlider){

	if($viewedSlider.length){
	
		// save $.cookie option
		var bCookieJson = $.cookie.json;
		$.cookie.json = true;

		var arViewedLocal = {};
		var arViewedCookie = {};

		try{
			if(typeof BX.localStorage !== 'undefined'){
				var localKey = 'NEXT_VIEWED_ITEMS_'+BX.message('SITE_ID');
				var cookieParams = {path: '/', expires: 30};
				arViewedLocal = BX.localStorage.get(localKey) ? BX.localStorage.get(localKey) : {};

				arViewedCookie = $.cookie(localKey) ? $.cookie(localKey) : {};
				for(var PRODUCT_ID in arViewedLocal){
					var $item = $viewedSlider.find('div[data-id=' + PRODUCT_ID + ']');

					if($item.length){
						var item = arViewedLocal[PRODUCT_ID];
						var picture = (typeof $item.attr('data-picture') !== 'undefined') ? JSON.parse($item.attr('data-picture')) : {ID: false, SRC: BX.message('SITE_TEMPLATE_PATH')+'/images/no_photo_medium.png', ALT: item.NAME, TITLE: item.NAME};
						var bIsOffer = (typeof item.IS_OFFER !== 'undefined') ? (item.IS_OFFER === 'Y') : false;
						var bWithOffers = (typeof item.WITH_OFFERS !== 'undefined') ? (item.WITH_OFFERS === 'Y') : false;
						$item.html(
							'<div class="inner_wrap">'+
								'<div class="image_wrapper_block">'+
									'<a href="' + item.DETAIL_PAGE_URL + '" class="thumb">'+
										'<img border="0" src="' + picture.SRC + '" alt="' + (picture.ALT.length ? picture.ALT : item.NAME) + '" title="' + (picture.TITLE.length ? picture.TITLE : item.NAME) + '" />'+
									'</a>'+
								'</div>'+
								'<div class="item_info">'+
									'<div class="item-title">'+
										'<a class="dark-color" href="' + item.DETAIL_PAGE_URL + '"><span>' + item.NAME + '</span></a>'+
									'</div>'+
									'<div class="cost prices clearfix">'+
										(item.MIN_PRICE ?
											((((item.MIN_PRICE.VALUE * 1) > (item.MIN_PRICE.DISCOUNT_VALUE * 1))) ?
												'<div class="price only_price">' + (bWithOffers ? BX.message('CATALOG_FROM_VIEWED')+' ' : '') + item.MIN_PRICE.PRINT_DISCOUNT_VALUE + (bShowMeasure && item.CATALOG_MEASURE_NAME.length ? '/' + item.CATALOG_MEASURE_NAME : '') + '</div>'
											: '<div class="price only_price">' + (bWithOffers ? BX.message('CATALOG_FROM_VIEWED')+' ' : '') + item.MIN_PRICE.PRINT_DISCOUNT_VALUE + (bShowMeasure && item.CATALOG_MEASURE_NAME.length ? '/' + item.CATALOG_MEASURE_NAME : '') + '</div>')
										: '')+
									'</div>'+
								'</div>'+
							'</div>'
						);
					}
					else{
						// item not finded
						// may be if it`s new item (it`s detail page), than ACTIVE_FROM > last viewed exists item
						// or it`s old died item and quantity limit
						var ACTIVE_FROM = (typeof arViewedLocal[PRODUCT_ID].ACTIVE_FROM !== 'undefined') ? arViewedLocal[PRODUCT_ID].ACTIVE_FROM : ((typeof arViewedCookie[PRODUCT_ID] !== 'undefined') ? arViewedCookie[PRODUCT_ID][0] : false);
						if(!ACTIVE_FROM || ACTIVE_FROM < lastViewedTime){
							// get actual for save
							var _arViewedLocal = BX.localStorage.get(localKey) ? BX.localStorage.get(localKey) : {};
							var _arViewedCookie = $.cookie(localKey) ? $.cookie(localKey) : {};
							delete _arViewedLocal[PRODUCT_ID];
							delete _arViewedCookie[PRODUCT_ID];
							BX.localStorage.set(localKey, _arViewedLocal, 2592000);  // 30 days
							$.cookie(localKey, _arViewedCookie, cookieParams);
						}
					}
				}
			}

			//remove some items
			$viewedSlider.find('>.item').each(function() {
				var PRODUCT_ID = (typeof $(this).attr('data-id') !== 'undefined') ? $(this).attr('data-id') : false;
				if(PRODUCT_ID && (typeof arViewedLocal[PRODUCT_ID] === 'undefined')){
					$(this).removeClass('has-item').find('>.inner_wrap').remove();
				}
			});

			// if no items than remove block
			if(!$viewedSlider.find('>.item.has-item').length){
				$viewedSlider.closest('.viewed_block').remove();
			}
		}
		catch(e){
			console.error(e);
		}
		finally{
			// restore $.cookie option
			$.cookie.json = bCookieJson;
		}
	}
}

if (typeof window.frameCacheVars !== "undefined"){
	BX.addCustomEvent("onFrameDataReceived", function (json){
		InitFlexSlider();
	})
}