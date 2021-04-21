var basketTimeoutSlide;
var resizeEventTimer;

var funcDefined = function(func){
	try
	{
		if(typeof func == 'function')
			return true;
		else
			return typeof window[func] === "function";
	}
	catch (e)
	{
		return false;
	}
}

if(!funcDefined('setLocationSKU')){
	function setLocationSKU(ID){
		var objUrl = parseUrlQuery(),
			j = 0,
			prefix = '',
			query_string = '',
			url = '';
		if('oid' in objUrl)
		{
			if(parseInt(objUrl.oid)>0)
			{
				objUrl.oid = ID;
				for(var i in objUrl)
				{
					if(parseInt(j)>0)
						prefix = '&';
					query_string = query_string + prefix + i + '='+ objUrl[i];
					j++;
				}
				if(query_string)
				{
					url = location.pathname+'?'+query_string;
				}
				try
				{
					history.replaceState(null, null, url);
					return;
				}
				catch(e){}
				location.hash = '#' + url.substr(1)
			}
		}
	}
}

if(!funcDefined('ShowOverlay')){
	ShowOverlay = function(){
		$('<div class="jqmOverlay waiting"></div>').appendTo('body');
	}
}
if(!funcDefined('HideOverlay')){
	HideOverlay = function(){
		$('.jqmOverlay').remove();
	}
}
if(!funcDefined('trimPrice')){
	var trimPrice = function trimPrice(s){
		s=s.split(" ").join("");
		s=s.split("&nbsp;").join("");
		return s;
	}
}

if(!funcDefined('markProductRemoveBasket')){
	var markProductRemoveBasket = function markProductRemoveBasket(id){
		$('.in-cart[data-item='+id+']').hide();
		$('.to-cart[data-item='+id+']').show();
		$('.to-cart[data-item='+id+']').closest('.button_block').removeClass('wide');
		$('.to-cart[data-item='+id+']').closest('.counter_wrapp').find('.counter_block').show();
		$('.counter_block[data-item='+id+']').show();
		$('.in-subscribe[data-item='+id+']').hide();
		$('.to-subscribe[data-item='+id+']').show();
		$('.wish_item[data-item='+id+']').removeClass("added");
		$('.wish_item[data-item='+id+'] .value:not(.added)').show();
		$('.wish_item[data-item='+id+'] .value.added').hide();
	}
}

if(!funcDefined('markProductAddBasket')){
	var markProductAddBasket = function markProductAddBasket(id){
		$('.to-cart[data-item='+id+']').hide();
		$('.to-cart[data-item='+id+']').closest('.counter_wrapp').find('.counter_block').hide();
		$('.to-cart[data-item='+id+']').closest('.button_block').addClass('wide');
		$('.in-cart[data-item='+id+']').show();
		$('.wish_item[data-item='+id+']').removeClass("added");
		$('.wish_item[data-item='+id+'] .value:not(.added)').show();
		$('.wish_item[data-item='+id+'] .value.added').hide();
	}
}

if(!funcDefined('markProductDelay')){
	var markProductDelay = function markProductDelay(id){
		$('.in-cart[data-item='+id+']').hide();
		$('.to-cart[data-item='+id+']').show();
		$('.to-cart[data-item='+id+']').closest('.counter_wrapp').find('.counter_block').show();
		$('.to-cart[data-item='+id+']').closest('.button_block').removeClass('wide');
		$('.wish_item[data-item='+id+']').addClass("added");
		$('.wish_item[data-item='+id+'] .value:not(.added)').hide();
		$('.wish_item[data-item='+id+'] .value.added').css('display','block');
	}
}

if(!funcDefined('markProductSubscribe')){
	var markProductSubscribe = function markProductSubscribe(id){
		$('.to-subscribe[data-item='+id+']').hide();
		$('.in-subscribe[data-item='+id+']').css('display','block');
	}
}

if(!funcDefined('basketFly')){
	var basketFly = function basketFly(action,opener){
		if(typeof obNextPredictions === 'object'){
			obNextPredictions.updateAll();
		}
		/*if(arNextOptions['PAGES']['BASKET_PAGE'])
			return;*/
		$.post( arNextOptions['SITE_DIR']+"ajax/basket_fly.php", "PARAMS="+$("#basket_form").find("input#fly_basket_params").val(), $.proxy(function( data ){
			var small=$('.opener .basket_count').hasClass('small'),
				basket_count=$(data).find('.basket_count').find('.items div').text();
			$('#basket_line .basket_fly').addClass('loaded').html(data);

			if(action=="refresh")
				$('li[data-type=AnDelCanBuy]').trigger('click');

			if(typeof(opener) == 'undefined')
			{
				if(window.matchMedia('(min-width: 769px)').matches)
				{
					if(action=='open')
					{
						if(small)
						{
							if(arNextOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N')
								$('.opener .basket_count').click();
						}
						else
						{
							$('.opener .basket_count').removeClass('small')
							$('.tabs_content.basket li[item-section="AnDelCanBuy"]').addClass('cur');
							$('#basket_line ul.tabs li[item-section="AnDelCanBuy"]').addClass('cur');
						}
					}
					else if(action=='wish')
					{
						if(small)
						{
							if(arNextOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N')
								$('.opener .wish_count').click();
						}
						else
						{
							$('.opener .wish_count').removeClass('small')
							$('.tabs_content.basket li[item-section="DelDelCanBuy"]').addClass('cur');
							$('#basket_line ul.tabs li[item-section="DelDelCanBuy"]').addClass('cur');
						}
					}
					else
					{
						if(arNextOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N')
							$('.opener .basket_count').click();
					}
				}
			}
		}));
	}
}

//work with hash start
var lastHash = location.hash;
if("onhashchange" in window)
{
	$(window).bind('hashchange', function() {
		var newHash = location.hash;
		if(newHash == '#delayed')
		{
			if($('#basket_toolbar_button_delayed').length)
				$('#basket_toolbar_button_delayed').trigger('click');
		}
		else
		{
			if($('#basket_toolbar_button').length)
				$('#basket_toolbar_button').trigger('click');
		}
		// Do something
		var diff = compareHash(newHash, lastHash);
		// alert("Difference between old and new hash:\n"+diff[0]+"\n\n"+diff[1]);

		//At the end of the func:
		lastHash = newHash;
	});

	function compareHash(current, previous){
		for(var i=0, len=Math.min(current.length, previous.length); i<len; i++){
			if(current.charAt(0) != previous.charAt(0)) break;
		}
		current = current.substr(i);
		previous = previous.substr(i);
		for(var i=0, len=Math.min(current.length, previous.length); i<len; i++){
			if(current.substr(-1) != previous.substr(-1)) break;
		}

		//Array: Current = New hash, previous = old hash
		return [current, previous];
	}
}
$(document).on('click', ".back-mobile-arrow .arrow-back", function(){

	if(document.referrer) {
		window.history.back();
	} else {
		location.href = '/';
	}

})
$(document).on('click', "#basket_toolbar_button", function(){
	if(lastHash)
		location.hash = 'cart';
})
$(document).on('click', "#basket_toolbar_button_delayed", function(){
	if(lastHash)
		location.hash = 'delayed';
})
//work with hash end

$(document).on('click', "#basket_line .basket_fly .opener > div.clicked", function(){
	if(arNextOptions['PAGES']['BASKET_PAGE'])
		return;
	function onOpenFlyBasket(_this){
		$("#basket_line .basket_fly .tabs li").removeClass("cur");
		$("#basket_line .basket_fly .tabs_content li").removeClass("cur");
		$("#basket_line .basket_fly .remove_all_basket").removeClass("cur");
		if(!$(_this).is(".wish_count.empty"))
		{
			$("#basket_line .basket_fly .tabs_content li[item-section="+$(_this).data("type")+"]").addClass("cur");
			$("#basket_line .basket_fly .tabs li:eq("+$(_this).index()+")").addClass("cur");
			$("#basket_line .basket_fly .remove_all_basket."+$(_this).data("type")).addClass("cur");
		}
		else
		{
			$("#basket_line .basket_fly .tabs li").first().addClass("cur").siblings().removeClass("cur");
			$("#basket_line .basket_fly .tabs_content li").first().addClass("cur").siblings().removeClass("cur");
			$("#basket_line .basket_fly .remove_all_basket").first().addClass("cur");
		}
		$("#basket_line .basket_fly .opener > div.clicked").removeClass('small');
	}

	if(window.matchMedia('(min-width: 769px)').matches)
	{
		var _this = this;
		if(parseInt($("#basket_line .basket_fly").css("right")) < 0)
		{
			$("#basket_line .basket_fly").stop().animate({"right": "0"}, 333, function(){
				if($(_this).closest('.basket_fly.loaded').length)
				{
					onOpenFlyBasket(_this);
				}
				else
				{
					$.ajax({
						url: arNextOptions['SITE_DIR'] + 'ajax/basket_fly.php',
						type: 'post',
						success: function(html){
							$('#basket_line .basket_fly').addClass('loaded').html(html);
							onOpenFlyBasket(_this);
						}
					});
				}
			});
		}
		else if($(this).is(".wish_count:not(.empty)") && !$("#basket_line .basket_fly .basket_sort ul.tabs li.cur").is("[item-section=DelDelCanBuy]"))
		{
			$("#basket_line .basket_fly .tabs li").removeClass("cur");
			$("#basket_line .basket_fly .tabs_content li").removeClass("cur");
			$("#basket_line .basket_fly .remove_all_basket").removeClass("cur");
			$("#basket_line .basket_fly .tabs_content li[item-section="+$(this).data("type")+"]").addClass("cur");
			$("#basket_line  .basket_fly .tabs li:eq("+$(this).index()+")").first().addClass("cur");
			$("#basket_line .basket_fly .remove_all_basket."+$(this).data("type")).first().addClass("cur");
		}
		else if($(this).is(".basket_count") && $("#basket_line .basket_fly .basket_sort ul.tabs li.cur").length && !$("#basket_line .basket_fly .basket_sort ul.tabs li.cur").is("[item-section=AnDelCanBuy]"))
		{
			$("#basket_line .basket_fly .tabs li").removeClass("cur");
			$("#basket_line .basket_fly .tabs_content li").removeClass("cur");
			$("#basket_line .basket_fly .remove_all_basket").removeClass("cur");
			$("#basket_line  .basket_fly .tabs_content li:eq("+$(this).index()+")").addClass("cur");
			$("#basket_line  .basket_fly .tabs li:eq("+$(this).index()+")").first().addClass("cur");
			$("#basket_line .basket_fly .remove_all_basket."+$(this).data("type")).first().addClass("cur");
		}
		else
		{
			$("#basket_line .basket_fly").stop().animate({"right": -$("#basket_line .basket_fly").outerWidth()}, 150);
			$("#basket_line .basket_fly .opener > div.clicked").addClass('small');
		}
	}
});

if(!funcDefined("clearViewedProduct")){
	function clearViewedProduct(){
		try{
			var siteID = arNextOptions.SITE_ID;
			var localKey = 'NEXT_VIEWED_ITEMS_' + siteID;
			var cookieParams = {path: '/', expires: 30};
			if(typeof BX.localStorage !== 'undefined')
			{
				// remove local storage
				BX.localStorage.set(localKey, {}, 0);
			}
			// remove cookie
			$.removeCookie(localKey, cookieParams);
		}
		catch(e){
			console.error(e);
		}
	}
}

if(!funcDefined("setViewedProduct")){
	function setViewedProduct(id, arData){
		try{

			// save $.cookie option
			var bCookieJson = $.cookie.json;
			$.cookie.json = true;

			var siteID = arNextOptions.SITE_ID;
			var localKey = 'NEXT_VIEWED_ITEMS_' + siteID;
			var cookieParams = {path: '/', expires: 30};

			if((typeof BX.localStorage !== 'undefined') && (typeof id !== 'undefined') && (typeof arData !== 'undefined')){
				var PRODUCT_ID = (typeof arData.PRODUCT_ID !== 'undefined') ? arData.PRODUCT_ID : id;
				var arViewedLocal = BX.localStorage.get(localKey) ? BX.localStorage.get(localKey) : {};
				var arViewedCookie = $.cookie(localKey) ? $.cookie(localKey) : {};
				var count = 0;

				// delete some items (sync cookie & local storage)
				for(var _id in arViewedLocal){
					arViewedLocal[_id].IS_LAST = false;
					if(typeof arViewedCookie[_id] === 'undefined'){
						delete arViewedLocal[_id];
					}
				}
				for(var _id in arViewedCookie){
					if(typeof arViewedLocal[_id] === 'undefined'){
						delete arViewedCookie[_id];
					}
				}

				for(var _id in arViewedCookie){
					count++;
				}

				// delete item if other item (offer) of that PRODUCT_ID is exists
				if(typeof arViewedLocal[PRODUCT_ID] !== 'undefined'){
					if(arViewedLocal[PRODUCT_ID].ID != id){
						delete arViewedLocal[PRODUCT_ID];
						delete arViewedCookie[PRODUCT_ID];
					}
				}

				delete arViewedLocal[2243];
				delete arViewedCookie[2243];

				var time = new Date().getTime();
				arData.ID = id;
				arData.ACTIVE_FROM = time;
				arData.IS_LAST = true;
				arViewedLocal[PRODUCT_ID] = arData;
				arViewedCookie[PRODUCT_ID] = [time.toString(), arData.PICTURE_ID];

				$.cookie(localKey, arViewedCookie, cookieParams);
				BX.localStorage.set(localKey, arViewedLocal, 2592000);  // 30 days
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



if(!funcDefined('initSelects')){
	function initSelects(target){
		var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
		if ( iOS ) return;
		if($("#bx-soa-order").length)
			return;
		// SELECT STYLING
		$(target).find('.wrapper1 select:visible').ikSelect({
			syntax: '<div class="ik_select_link"> \
						<span class="ik_select_link_text"></span> \
						<div class="trigger"></div> \
					</div> \
					<div class="ik_select_dropdown"> \
						<div class="ik_select_list"> \
						</div> \
					</div>',
			dynamicWidth: true,
			ddMaxHeight: 112,
			customClass: 'common_select',
			//equalWidths: true,
			onShow: function(inst){
				inst.$dropdown.css('top', (parseFloat(inst.$dropdown.css('top'))-5)+'px');
				if ( inst.$dropdown.outerWidth() < inst.$link.outerWidth() ){
					inst.$dropdown.css('width', inst.$link.outerWidth());
				}
				if ( inst.$dropdown.outerWidth() > inst.$link.outerWidth() ){
					inst.$dropdown.css('width', inst.$link.outerWidth());
				}
				var count=0,
					client_height=0;
				inst.$dropdown.css('left', inst.$link.offset().left);
				$(inst.$listInnerUl).find('li').each(function(){
					if(!$(this).hasClass('ik_select_option_disabled')){
						++count;
						client_height+=$(this).outerHeight();
					}
				})
				if(client_height<112){
					inst.$listInner.css('height', 'auto');
				}else{
					inst.$listInner.css('height', '112px');
				}
				inst.$link.addClass('opened');
				inst.$listInner.addClass('scroller');
				if($('.confirm_region').length)
					$('.confirm_region').remove();
			},
			onHide: function(inst){
				inst.$link.removeClass('opened');
			}
		});
		// END OF SELECT STYLING

		var timeout;
		$(window).on('resize', function(){
			ignoreResize.push(true);
				clearTimeout(timeout);
				timeout = setTimeout(function(){
					//$('select:visible').ikSelect('redraw');
					var inst='';
					if(inst=$('.common_select-link.opened + select').ikSelect().data('plugin_ikSelect')){
						inst.$dropdown.css('left', inst.$link.offset().left+'px');
					}
				}, 20);
			ignoreResize.pop();
		});
	}
}

if(!funcDefined('CheckTopMenuFullCatalogSubmenu')){
	CheckTopMenuFullCatalogSubmenu = function(){
		if(arNextOptions['THEME']['MENU_TYPE_VIEW'] != 'HOVER')
			return;

		var $menu = $('.menu_top_block');
		if($menu.length)
		{
			var $wrapmenu = $menu.parents('.wrap_menu');

			var wrapMenuWidth = $menu.closest('.wrapper_inner').actual('width');
			if(!wrapMenuWidth)
				wrapMenuWidth = $menu.closest('.wraps').actual('width');

			var bCatalogFirst = $menu.hasClass('catalogfirst');
			var findMenuLi = $('.menu_top_block:visible li.full');
			var parentSubmenuWidth = $menu.actual('outerWidth');
			var wrapMenuLeft = 0;
			var wrapMenuRight = 0;

			if($wrapmenu.length)
			{
				wrapMenuWidth = $wrapmenu.actual('outerWidth');
				wrapMenuLeft = $wrapmenu.offset().left;
				wrapMenuRight = wrapMenuLeft + wrapMenuWidth;
			}

			if($('.catalog_block.menu_top_block').length){
				if($('.catalog_block.menu_top_block').is(':visible'))
					findMenuLi=$('.menu_top_block.catalog_block li.full');
			}
			findMenuLi.each(function(){
				var $this = $(this);
				var $submenu = $this.find('>.dropdown');

				if($submenu.length){
					//if(bCatalogFirst){
						$submenu.css({left: parentSubmenuWidth + 'px', width: (wrapMenuWidth - parentSubmenuWidth) + 'px', 'padding-left': '0px', 'padding-right': '0px', 'opacity': 1});
					/*}
					else{
						$submenu.css({left: ($this.offset().left * -1) + 'px', width: ($(window).width() - 1) + 'px', 'padding-left': wrapMenuLeft + 'px', 'padding-right': ($(window).width() - wrapMenuRight) + 'px'});
					}*/
					// if(!isOnceInited && bCatalogFirst && arNextOptions["THEME"]["MENU_POSITION"] == "TOP"){
					if(!isOnceInited && arNextOptions["THEME"]["MENU_POSITION"] == "TOP"){
						$this.on('mouseenter', function(){
							$submenu.css('min-height', $this.closest('.dropdown').actual('outerHeight') + 'px');
						});
					}
				}
			});
		}
	}
}

$.fn.getMaxHeights = function( outer, classNull, minHeight ){
	var maxHeight = this.map( function( i, e ){
		var calc_height=0;
		$(e).css('height', '');

		if( outer == true )
			calc_height=$(e).actual('outerHeight');
		else
			calc_height=$(e).actual('height');

		return calc_height;
	}).get();
	for(var i = 0, c = maxHeight.length; i < c; ++i)
	{
		if(maxHeight[i] % 2)
			--maxHeight[i];
	}
	return Math.max.apply( this, maxHeight );
}

$.fn.equalizeHeights = function( outer, classNull, minHeight ){
	var maxHeight = this.map( function( i, e ){
		var minus_height=0,
			calc_height=0;


		if(classNull!==false)
		{
			if(!isMobile){
				minus_height=parseInt($(e).find(classNull).actual('outerHeight'));
			}
		}

		if(minus_height){
			minus_height+=12;
		}

		$(e).css('height', '');

		if( outer == true )
			calc_height=$(e).actual('outerHeight')-minus_height;
		else
			calc_height=$(e).actual('height')-minus_height;

		if(minHeight!==false)
		{
			if(calc_height<minHeight)
				calc_height+=(minHeight-calc_height);

			if(window.matchMedia('(max-width: 520px)').matches)
				calc_height=300;

			if(window.matchMedia('(max-width: 400px)').matches)
				calc_height=200;
		}
		return calc_height;
	}).get();

	for(var i = 0, c = maxHeight.length; i < c; ++i){
		if(maxHeight[i] % 2){
			--maxHeight[i];
		}
	}
	return this.height( Math.max.apply( this, maxHeight ) );
}

$.fn.getFloatWidth = function(){
	var width = 0
	if($(this).length){
		var rect = $(this)[0].getBoundingClientRect();
		if(!(width = rect.width))
			width = rect.right - rect.left;
	}
	return width
}

$.fn.sliceHeight = function( options ){
	function _slice(el){
		el.each(function() {
			$(this).css('line-height', '');
			$(this).css('height', '');
		});

		if(options.mobile == true){
			if(window.matchMedia('(max-width: 550px)').matches){
				return;
			}
		}

		if(typeof options.autoslicecount === 'undefined' || options.autoslicecount !== false){
			var elsw = (typeof options.row !== 'undefined' && options.row.length) ?  el.first().parents(options.row).getFloatWidth() : el.first().parents('.items').getFloatWidth();
			var elw = (typeof options.item !== 'undefined' && options.item.length) ? $(options.item).first().getFloatWidth() : el.first().closest('.item').getFloatWidth();

			if(!elsw){
				elsw = el.first().parents('.row').getFloatWidth();
			}

			if(elw && options.fixWidth){
				elw -= options.fixWidth;
			}

			if(elsw && elw){
				options.slice = Math.floor(elsw / elw);
			}
		}

		if(options.customSlice){
			//manual slice count
			var arBreakpoints = Object.keys(options.breakpoint),
				bSliceNext = false;
			if(arBreakpoints.length){
				var elw = (typeof options.item !== 'undefined' && options.item.length) ? $(options.item).last().getFloatWidth() : el.last().closest('.item').getFloatWidth();

				if(elw){
					options.sliceNext = Math.floor(elsw / elw);
				}

				for(var key in arBreakpoints){
					if(window.matchMedia(arBreakpoints[key].toString()).matches){
						bSliceNext = true;
						options.slice = options.breakpoint[arBreakpoints[key]];
					}
				}
			}
		}

		if(typeof options.typeResize === 'undefined' || options.typeResize == false){
			if(options.slice){
				for(var i = 0; i < el.length; i += options.slice){
					if(options.customSlice && options.sliceNext && bSliceNext && i) //manual slice count
						options.slice = options.sliceNext;
					$(el.slice(i, i + options.slice)).equalizeHeights(options.outer, options.classNull, options.minHeight, options.typeResize, options.typeValue);
				}
			}

			if(options.lineheight){
				var lineheightAdd = parseInt(options.lineheight);
				if(isNaN(lineheightAdd)){
					lineheightAdd = 0;
				}

				el.each(function() {
					$(this).css('line-height', ($(this).actual('height') + lineheightAdd) + 'px');
				});
			}
		}
	}

	var options = $.extend({
		slice: null,
		sliceNext: null,
		outer: false,
		lineheight: false,
		autoslicecount: true,
		classNull: false,
		minHeight: false,
		row:false,
		item:false,
		typeResize:false,
		typeValue:false,
		fixWidth:0,
		resize:true,
		mobile:false,
		customSlice:false,
		breakpoint:{},
	}, options);

	var el = $(this);

	if(el.length){
		if(options.mobile == true){
			if(typeof arNextOptions === 'object' && arNextOptions.THEME.MOBILE_CATALOG_LIST_ELEMENTS_COMPACT === 'Y' && el.first().parents('.catalog_block.items').length){
				options.mobile = false;
			}
		}

		_slice(el);

		if(options.resize){
			BX.addCustomEvent('onWindowResize', function(eventdata) {
				try{
					_slice(el);
				}
				catch(e){}
			});
		}

		if($(this).find('img.lazyload').length){
			BX.addCustomEvent('onLazyLoaded', function(eventdata) {
				var bSlice = false;
				if(eventdata.length){
					for(var i in eventdata){
						if($(eventdata[i]).closest(el).length){
							bSlice = true;
							break;
						}
					}
				}

				if(bSlice){
					try{
						_slice(el);
					}
					catch(e){}
				}
			});
		}
	}
}

$.fn.sliceHeightNoResize = function( options ){
	function _slice(el){
		el.each(function() {
			$(this).css('line-height', '');
			$(this).css('height', '');
		});

		if(typeof(options.autoslicecount) === 'undefined' || options.autoslicecount !== false){
			var elw = el.first().closest('.item').getFloatWidth();
			var elsw = el.first().parents('.items').getFloatWidth();

			if(!elsw){
				elsw = el.first().parents('.row').getFloatWidth();
			}

			if(elsw && elw){
				options.slice = Math.floor(elsw / elw);
			}
		}

		if(options.slice){
			for(var i = 0; i < el.length; i += options.slice){

				$(el.slice(i, i + options.slice)).equalizeHeights(options.outer, options.classNull, options.minHeight);
			}
		}
		if(options.lineheight){
			var lineheightAdd = parseInt(options.lineheight);
			if(isNaN(lineheightAdd)){
				lineheightAdd = 0;
			}
			el.each(function() {
				$(this).css('line-height', ($(this).actual('height') + lineheightAdd) + 'px');
			});
		}
	}

	var options = $.extend({
		slice: null,
		outer: false,
		lineheight: false,
		autoslicecount: true,
		classNull: false,
		minHeight: false,
		options: false,
		resize: true,
	}, options);

	var el = $(this);

	if(el.length){
		_slice(el);

		if($(this).find('img.lazyload').length){
			BX.addCustomEvent('onLazyLoaded', function(eventdata) {
				var bSlice = false;
				if(eventdata.length){
					for(var i in eventdata){
						if($(eventdata[i]).closest(el).length){
							bSlice = true;
							break;
						}
					}
				}

				if(bSlice){
					try{
						_slice(el);
					}
					catch(e){}
				}
			});
		}
	}
}

if(!funcDefined('initHoverBlock')){
	function initHoverBlock(target){
		/*$(target).find('.catalog_item.item_wrap').on('mouseenter', function(){
			$(this).addClass('hover');
		})
		$(target).find('.catalog_item.item_wrap').on('mouseleave', function(){
			$(this).removeClass('hover');
		})*/
	}
}
if(!funcDefined('setStatusButton')){
	function setStatusButton(){
		if(!funcDefined('setItemButtonStatus')){
			setItemButtonStatus = function(data){
				if(data.BASKET){
					for( var i in data.BASKET ){
						var id = data.BASKET[i];
						if(typeof id === 'number' || typeof id === 'string'){
							$('.to-cart[data-item='+id+']').hide();
							$('.counter_block[data-item='+id+']').hide();
							$('.in-cart[data-item='+id+']').show();
							$('.in-cart[data-item='+id+']').closest('.button_block').addClass('wide');
						}
					}
				}
				if(data.DELAY){
					for( var i in data.DELAY ){
						var id = data.DELAY[i];
						if(typeof id === 'number' || typeof id === 'string'){
							$('.wish_item.to[data-item='+id+']').hide();
							$('.wish_item.in[data-item='+id+']').show();
							if ($('.wish_item[data-item='+id+']').find(".value.added").length) {
								$('.wish_item[data-item='+id+']').addClass("added");
								$('.wish_item[data-item='+id+']').find(".value").hide();
								$('.wish_item[data-item='+id+']').find(".value.added").show();
							}
						}
					}
				}
				if(data.SUBSCRIBE){
					for( var i in data.SUBSCRIBE ){
						var id = data.SUBSCRIBE;
						if(typeof id === 'number' || typeof id === 'string'){
							$('.to-subscribe[data-item='+id+']').hide();
							$('.in-subscribe[data-item='+id+']').show();
						}
					}
				}
				if(data.COMPARE){
					for( var i in data.COMPARE ){
						var id = data.COMPARE;
						if(typeof id === 'number' || typeof id === 'string'){
							$('.compare_item.to[data-item='+id+']').hide();
							$('.compare_item.in[data-item='+id+']').show();
							if ($('.compare_item[data-item='+id+']').find(".value.added").length){
								$('.compare_item[data-item='+id+']').find(".value").hide();
								$('.compare_item[data-item='+id+']').find(".value.added").show();
							}
						}
					}
				}
			}
		}
		if(!Object.keys(arStatusBasketAspro).length)
		{
			if(typeof arNextOptions === 'undefined')
			{
				var arNextOptions = ({
					"SITE_DIR" : '/'
				});
			}
			$.ajax({
				url: arNextOptions["SITE_DIR"]+'ajax/getAjaxBasket.php',
				type: 'POST',
				success: function(data){
					arStatusBasketAspro = data;
					setItemButtonStatus(arStatusBasketAspro);
				}
			});
		}
		else
			setItemButtonStatus(arStatusBasketAspro);
	}
}


if(!funcDefined('onLoadjqm')){
	var onLoadjqm = function(name, hash, requestData, selector, requestTitle, isButton, thButton){

		$.each($(hash.t).get(0).attributes, function(index, attr){
			if(/^data\-autoload\-(.+)$/.test(attr.nodeName)){
				var key = attr.nodeName.match(/^data\-autoload\-(.+)$/)[1];
				var el = $('input[data-sid="'+key.toUpperCase()+'"]');
				// el.val( $(hash.t).data('autoload-'+key) ).attr('readonly', 'readonly');
				el.val(BX.util.htmlspecialcharsback($(hash.t).data('autoload-'+key))).attr('readonly', 'readonly');
				el.closest('.form-group').addClass('input-filed');
				el.attr('title', el.val());
			}
		});

		//show gift block
		if(hash.w.hasClass('send_gift_frame'))
		{
			var imgHtml = priceHtml = propsHtml = '';
			if($('.offers_img a').length)
				imgHtml = $('.offers_img a').html();
			else if($('.item_main_info .item_slider:not(.flex) .slides li').length)
				imgHtml = $('.item_main_info .item_slider .slides li:first a').html();

			if($('.item_main_info *[itemprop="offers"]').length) //show price
			{
				if($('.offers_img.wof').length || $('.prices_tab').length)
				{
					if($('.prices_block .price').length)
						priceHtml = $('.prices_block .cost.prices').html().replace('id', 'data-id');
				}
				else
				{
					if($('.prices_block .with_matrix').length)
						priceHtml = '<div class="with_matrix">'+$('.prices_block .with_matrix').html()+'</div>';
					else if($('.prices_block .price_group.min').length)
						priceHtml = $('.prices_block .price_group.min').html();
					else if($('.prices_block .price_matrix_wrapper').length)
						priceHtml = $('.prices_block .price_matrix_wrapper').html();
				}
			}

			if($('.buy_block .sku_props').length)
			{
				propsHtml = '<div class="props_item">';
				$('.buy_block .sku_props .bx_catalog_item_scu > div').each(function(){
					var title = $(this).find('.bx_item_section_name > span').html();
					propsHtml += '<div class="prop_item">'+
									'<span>'+
										title + (title.indexOf(':') > 0 ? '' : ': ')+
										(title.indexOf(':') > 0 ? '' : '<span class="val">'+$(this).find('ul li.active > span').text()+'</span>')+
									'</span>'+
								'</div>';

				})
				propsHtml += '</div>';
			}
			$('<div class="custom_block">'+
				'<div class="title">'+BX.message('POPUP_GIFT_TEXT')+'</div>'+
				'<div class="item_block">'+
					'<table class="item_list"><tr>'+
						'<td class="image">'+
							'<div>'+imgHtml+'</div>'+
						'</td>'+
						'<td class="text">'+
							'<div class="name">'+$('h1').text()+'</div>'+
							priceHtml+
							propsHtml+
						'</td>'+
					'</tr></table>'+
				'</div>'+
				'</div>').prependTo(hash.w.find('.form_body'))
		}

		if(arNextOptions['THEME']['REGIONALITY_SEARCH_ROW'] == 'Y' && (hash.w.hasClass('city_chooser_frame ') || hash.w.hasClass('city_chooser_small_frame')))
			hash.w.addClass('small_popup_regions')

		hash.w.addClass('show').css({
			'margin-left': ($(window).width() > hash.w.outerWidth() ? '-' + hash.w.outerWidth() / 2 + 'px' : '-' + $(window).width() / 2 + 'px'),
			'opacity': 1,
		});

		hash.w.addClass('show').css({
			// 'top': $(document).scrollTop() + (($(window).height() > hash.w.outerHeight() ? ($(window).height() - hash.w.outerHeight()) / 2 : 10))   + 'px',
			'top': (($(window).height() > hash.w.height()) ? Math.floor(($(window).height() - hash.w.height()) / 2) : 0) + 'px',
		});

		var eventdata = {action:'loadForm'};
		BX.onCustomEvent('onCompleteAction', [eventdata, $(hash.t)[0]]);

		if(typeof(requestData) == 'undefined'){
			requestData = '';
		}
		if(typeof(selector) == 'undefined'){
			selector = false;
		}
		var width = $('.'+name+'_frame').width();
		$('.'+name+'_frame').css('margin-left', '-'+width/2+'px');

		if(name=='order-popup-call') {
		}
		else if(name=='order-button') {
			$(".order-button_frame").find("div[product_name]").find("input").val(hash.t.title).attr("readonly", "readonly").css({"overflow": "hidden", "text-overflow": "ellipsis"});
		}
		else if(name=='basket_error')
		{
			$(".basket_error_frame .pop-up-title").text(requestTitle);
			$(".basket_error_frame .ajax_text").html(requestData);

			if(window.matchMedia('(max-width: 991px)').matches)
			{
				$("body").addClass("all_viewed");
			}

			initSelects(document);
			if(isButton=="Y" && thButton)
				$("<div class='popup_button_basket_wr'><span class='popup_button_basket big_btn button' data-item="+thButton.data("item")+"><span class='btn btn-default'>"+BX.message("ERROR_BASKET_BUTTON")+"</span></span></div>").insertAfter($(".basket_error_frame .ajax_text"));
		}

		$('.'+name+'_frame').show();
	}
}

$.fn.jqmEx = function(){
	// $(this).each(function(){
		var _this = $(this);
		var name = _this.data('name');
		var sFastViewClass = '';
		if(name.length){
			if(_this.closest('#fast_view_item').length)
				sFastViewClass = 'fast_view_popup';
			var script = arNextOptions['SITE_DIR'] + 'ajax/form.php';
			var paramsStr = ''; var trigger = ''; var arTriggerAttrs = {};
			$.each(_this.get(0).attributes, function(index, attr){
				var attrName = attr.nodeName;
				var attrValue = _this.attr(attrName);
				trigger += '[' + attrName + '=\"' + attrValue + '\"]';
				arTriggerAttrs[attrName] = attrValue;
				if(/^data\-param\-(.+)$/.test(attrName)){
					var key = attrName.match(/^data\-param\-(.+)$/)[1];
					paramsStr += key + '=' + attrValue + '&';
				}
			});
			var triggerAttrs = JSON.stringify(arTriggerAttrs);
			var encTriggerAttrs = encodeURIComponent(triggerAttrs);
			if(name == 'auth')
				script += '?' + paramsStr + 'auth=Y';
			else
				script += '?' + paramsStr + 'data-trigger=' + encTriggerAttrs;
			if(!$('.' + name + '_frame[data-trigger="' + encTriggerAttrs + '"]').length){
				if(_this.attr('disabled') != 'disabled'){
					$('body').find('.' + name + '_frame[data-trigger="' + encTriggerAttrs + '"]').remove();
					$('body').append('<div class="' + name + '_frame '+sFastViewClass+' jqmWindow popup" data-trigger="' + encTriggerAttrs + '"></div>');
					$('.' + name + '_frame[data-trigger="' + encTriggerAttrs + '"]').jqm({trigger: trigger, onLoad: function(hash){onLoadjqm(name, hash);}, onHide: function(hash){onHidejqm(name, hash);}, ajax:script});
				}
			}
		}
	// });
}

if(!funcDefined('onHidejqm')){
	var onHidejqm = function(name, hash){
		if (hash.w.find('.one_click_buy_result_success').is(':visible') && name=="one_click_buy_basket") {
			window.location.href = window.location.href;
		}

		if($('.xzoom-source').length)
			$('.xzoom-source').remove();
		if($('.xzoom-preview').length)
			$('.xzoom-preview').remove();

		// hash.w.css('opacity', 0).hide();
		hash.w.animate({'opacity': 0}, 200, function(){
			hash.w.hide();
			hash.w.empty();
			hash.o.remove();
			hash.w.removeClass('show');

			if(window.matchMedia('(max-width: 991px)').matches)
			{
				$("body").removeClass("all_viewed");
			}

			if(name == 'fast_view')
				$('.fast_view_popup').remove();
		});
	}
}

if(!funcDefined("scroll_block")) {
	function scroll_block(block){
		var topPos = block.offset().top,
			headerH = $('header').outerHeight(true,true);
		if($(".stores_tab").length){
			$(".stores_tab").addClass("active").siblings().removeClass("active");
		}else{
			$(".prices_tab").addClass("active").siblings().removeClass("active");
			if($(".prices_tab .opener").length && !$(".prices_tab .opener .opened").length){
				var item = $(".prices_tab .opener").first();
				item.find(".opener_icon").addClass("opened");
				item.parents("tr").addClass("nb")
				item.parents("tr").next(".offer_stores").find(".stores_block_wrap").slideDown(200);
			}
		}
		$('html,body').animate({'scrollTop':topPos-80},150);
	}
}

if(!funcDefined("jqmEd")) {
	var jqmEd = function (name, form_id, open_trigger, requestData, selector, requestTitle, isButton, thButton){
		if(typeof(requestData) == "undefined"){
			requestData = '';
		}
		if(typeof(selector) == "undefined"){
			selector = false;
		}
		$('body').find('.'+name+'_frame').remove();
		$('body').append('<div class="'+name+'_frame jqmWindow popup"></div>');
		if(typeof open_trigger == "undefined" ){
			$('.'+name+'_frame').jqm({trigger: '.'+name+'_frame.popup',onHide: function(hash) { onHidejqm(name,hash); }, onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector); }, ajax: arNextOptions["SITE_DIR"]+'ajax/form.php?form_id='+form_id+(requestData.length ? '&' + requestData : '')});
		}else{
			if(name == 'enter'){
				$('.'+name+'_frame').jqm({trigger: open_trigger,onHide: function(hash) { onHidejqm(name,hash); },  onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector); }, ajax: arNextOptions["SITE_DIR"]+'ajax/auth.php'});
			}else if(name=='basket_error'){
				$('.'+name+'_frame').jqm({trigger: open_trigger, onHide: function(hash) { onHidejqm(name,hash); }, onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector, requestTitle, isButton, thButton); }, ajax: arNextOptions["SITE_DIR"]+'ajax/basket_error.php'});

			}else{
				$('.'+name+'_frame').jqm({trigger: open_trigger, onHide: function(hash) { onHidejqm(name,hash); }, onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector); }, ajax: arNextOptions["SITE_DIR"]+'ajax/form.php?form_id='+form_id+(requestData.length ? '&' + requestData : '')});
			}
			$(open_trigger).dblclick(function(){return false;})
		}
		return true;
	}
}

if (!funcDefined("replaceBasketPopup")){
	function replaceBasketPopup (hash){
		if(typeof hash != "undefined"){
			hash.w.hide();
			hash.o.hide();
		}
	}
}

if(!funcDefined("waitLayer")){
	function waitLayer(delay, callback){
		if((typeof dataLayer !== 'undefined') && (typeof callback === 'function')){
			callback();
		}
		else{
			setTimeout(function() {
				waitLayer(delay, callback);
			}, delay);
		}
	}
}

if(!funcDefined("InitTopestMenuGummi")){
	InitTopestMenuGummi = function(){
		if(!isOnceInited){
			function _init(){
				var arItems = $menuTopest.find('>li:not(.more)');
				var cntItems = arItems.length;
				if(cntItems){
					var itemsWidth = 0;
					for(var i = 0; i < cntItems; ++i){
						var item = arItems.eq(i);
						var itemWidth = item.actual('outerWidth',{includeMargin: true});
						arItemsHideWidth[i] = (itemsWidth += itemWidth) + (i !== (cntItems - 1) ? moreWidth : 0);
					}
				}
			}

			function _gummi(){
				var rowWidth = $menuTopest.actual('innerWidth');
				var arItems = $menuTopest.find('>li:not(.more),li.more>.dropdown>li');
				var cntItems = arItems.length;
				if(cntItems){
					var bMore = false;
					for(var i = cntItems - 1; i >= 0; --i){
						var item = arItems.eq(i);
						var bInMore = item.parents('.more').length > 0;
						if(!bInMore){
							if(arItemsHideWidth[i] > rowWidth){
								if(!bMore){
									bMore = true;
									more.removeClass('hidden');
								}
								var clone = item.clone();
								clone.find('>a').addClass('dark_font');
								clone.prependTo(moreDropdown);
								item.addClass('cloned');
							}
						}
					}
					for(var i = 0; i < cntItems; ++i){
						var item = arItems.eq(i);
						var bInMore = item.parents('.more').length > 0;
						if(bInMore){
							if(arItemsHideWidth[i] <= rowWidth){
								if(i === (cntItems - 1)){
									bMore = false;
									more.addClass('hidden');
								}
								var clone = item.clone();
								clone.find('>a').removeClass('dark_font');
								clone.insertBefore(more);
								item.addClass('cloned');
							}
						}
					}
					$menuTopest.find('li.cloned').remove();
				}
			}

			var $menuTopest = $('.menu.topest');
			if($menuTopest.length)
			{
				var more = $menuTopest.find('>.more');
				var moreDropdown = more.find('>.dropdown');
				var moreWidth = more.actual('outerWidth',{includeMargin: true});
				var arItemsHideWidth = [];

				ignoreResize.push(true);
				_init();
				_gummi();
				ignoreResize.pop();

				BX.addCustomEvent('onWindowResize', function(eventdata) {
					try{
						ignoreResize.push(true);
						_gummi();
					}
					catch(e){}
					finally{
						ignoreResize.pop();
					}
				});
			}
		}
	}
}

if(!funcDefined("InitTopMenuGummi")){
	InitTopMenuGummi = function(){
		function _init(){
			var arItems = $topMenu.closest('.wrap_menu').find('.inc_menu .menu_top_block >li:not(.more)');
			var cntItems = arItems.length;
			if(cntItems){
				var itemsWidth = 0;
				for(var i = 0; i < cntItems; ++i){
					var item = arItems.eq(i);
						var itemWidth = item.actual('outerWidth');
						arItemsHideWidth[i] = (itemsWidth += itemWidth) + (i !== (cntItems - 1) ? moreWidth : 0);
				}
			}

		}

		function _gummi(){
			var rowWidth = $wrapMenu.actual('innerWidth') - $wrapMenuLeft.actual('innerWidth');
			var arItems = $topMenu.find('>li:not(.more):not(.catalog),li.more>.dropdown>li');
			var cntItems = arItems.length;

			if(cntItems){
				var bMore = false;
				for(var i = cntItems - 1; i >= 0; --i){
					var item = arItems.eq(i);
					var bInMore = item.parents('.more').length > 0;
					if(!bInMore){
						if(arItemsHideWidth[i] > rowWidth){
							if(!bMore){
								bMore = true;
								more.removeClass('hidden');
							}
							var clone = item.clone();
							clone.find('>.dropdown').removeAttr('style').removeClass('toleft');
							clone.find('>a').addClass('dark_font').removeAttr('style');
							clone.prependTo(moreDropdown);
							item.addClass('cloned');
						}
					}
				}
				for(var i = 0; i < cntItems; ++i){
					var item = arItems.eq(i);
					var bInMore = item.parents('.more').length > 0;
					if(bInMore){
						if(arItemsHideWidth[i] <= rowWidth){
							if(i === (cntItems - 1)){
								bMore = false;
								more.addClass('hidden');
							}
							var clone = item.clone();
							clone.find('>a').removeClass('dark_font');
							clone.insertBefore(more);
							item.addClass('cloned');
						}
					}
				}
				$topMenu.find('li.cloned').remove();

				var cntItemsVisible = $topMenu.find('>li:not(.more):not(.catalog)').length;
				var o = rowWidth - arItemsHideWidth[cntItemsVisible - 1];
				var itemsPaddingAdd = Math.floor(o / (cntItemsVisible + (more.hasClass('hidden') ? 0 : 1)));
				var itemsPadding_new = itemsPadding_min + itemsPaddingAdd;
				var itemsPadding_new_l = Math.floor(itemsPadding_new / 2);
				var itemsPadding_new_r = itemsPadding_new - itemsPadding_new_l;

				$topMenu.find('>li:not(.catalog):visible>a').each(function(){
					$(this).css({'padding-left': itemsPadding_new_l + 'px'});
					$(this).css({'padding-right': itemsPadding_new_r + 'px'});
				});

				var lastItemPadding_new = itemsPadding_new + o - (cntItemsVisible + (more.is(':visible') ? 1 : 0)) * itemsPaddingAdd;
				var lastItemPadding_new_l = Math.floor(lastItemPadding_new / 2);
				var lastItemPadding_new_r = lastItemPadding_new - lastItemPadding_new_l;
				$topMenu.find('>li:visible').last().find('>a').css({'padding-left': lastItemPadding_new_l + 'px'});
				$topMenu.find('>li:visible').last().find('>a').css({'padding-right': lastItemPadding_new_r + 'px'});
			}
			CheckTopMenuFullCatalogSubmenu();
		}

		var $topMenu = $('.menu_top_block');
		if($menuTopest.length)
		{
			var $wrapMenu = $topMenu.parents('.wrap_menu');
			var $wrapMenuLeft = $wrapMenu.find('.catalog_menu_ext');
			var more = $topMenu.find('>.more');
			var moreWidth = more.actual('outerWidth',{includeMargin: true});
			more.addClass('hidden');
			var arItemsHideWidth = [];
			var moreDropdown = more.find('>.dropdown');
			var itemsPadding = parseInt(more.find('>a').css('padding-left')) * 2;
			var itemsPadding_min = itemsPadding;

			// setTimeout(function(){
				ignoreResize.push(true);
				_init();
				_gummi();
				ignoreResize.pop();
			// }, 100)

			BX.addCustomEvent('onWindowResize', function(eventdata) {
				try{
					ignoreResize.push(true);
					_gummi();
				}
				catch(e){}
				finally{
					ignoreResize.pop();
				}
			});

			/*BX.addCustomEvent('onTopPanelFixUnfix', function(eventdata) {
				ignoreResize.push(true);
				_gummi();
				ignoreResize.pop();
			});*/
		}
	}
}

if(!funcDefined("checkCounters")){
	function checkCounters(name){
		if(typeof name !== "undefined"){
			if(name == "google" && (arNextOptions["COUNTERS"]["GOOGLE_ECOMERCE"] == "Y" && arNextOptions["COUNTERS"]["GOOGLE_COUNTER"] > 0)){
				return true;
			}
			else if(name == "yandex" && (arNextOptions["COUNTERS"]["YANDEX_ECOMERCE"] == "Y" && arNextOptions["COUNTERS"]["YANDEX_COUNTER"] > 0)){
				return true;
			}
			else{
				return false;
			}
		}
		else if((arNextOptions["COUNTERS"]["YANDEX_ECOMERCE"] == "Y" && arNextOptions["COUNTERS"]["YANDEX_COUNTER"] > 0) || (arNextOptions["COUNTERS"]["GOOGLE_ECOMERCE"] == "Y" && arNextOptions["COUNTERS"]["GOOGLE_COUNTER"] > 0)) {
			return true;
		}
		else{
			return false;
		}
	}
}

if(!funcDefined("addBasketCounter")){
	function addBasketCounter(id){
		if(arNextOptions['COUNTERS']['USE_BASKET_GOALS'] !== 'N'){
			var eventdata = {goal: 'goal_basket_add', params: {id: id}};
			BX.onCustomEvent('onCounterGoals', [eventdata]);
		}
		if(checkCounters()){
			$.ajax({
				url:arNextOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"ID": id},
				success: function(item){
					if(!!item && !!item.ID){
						waitLayer(100, function() {
							dataLayer.push({
								"event": arNextOptions["COUNTERS"]['GOOGLE_EVENTS']['ADD2BASKET'],
							    "ecommerce": {
							    	"currencyCode": item.CURRENCY,
							        "add": {
							            "products": [{
						                    "id": item.ID,
						                    "name": item.NAME,
						                    "price": item.PRICE,
						                    "brand": item.BRAND,
						                    "category": item.CATEGORY,
						                    "quantity": item.QUANTITY
						                }]
							        }
							    }
							});
						});
					}
				}
			});
		}
	}
}

if(!funcDefined("purchaseCounter")){
	function purchaseCounter(order_id, type, callback){
		if(checkCounters()){
			$.ajax({
				url:arNextOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"ORDER_ID": order_id, "TYPE": type},
				success: function(order){
					var products = [];
					if(order.ITEMS){
						for(var i in order.ITEMS){
							products.push({
								"id": order.ITEMS[i].ID,
								"sku": order.ITEMS[i].ID,
			                    "name": order.ITEMS[i].NAME,
			                    "price": order.ITEMS[i].PRICE,
			                    "brand": order.ITEMS[i].BRAND,
			                    "category": order.ITEMS[i].CATEGORY,
			                    "quantity": order.ITEMS[i].QUANTITY
							});
						}
					}
					if(order.ID){
						waitLayer(100, function() {
							var d = {
						    	"purchase": {
							    	"actionField":{
							    		"id": order.ACCOUNT_NUMBER,
							    		"shipping": order.PRICE_DELIVERY,
							    		"tax": order.TAX_VALUE,
							    		"list": type,
							    		"revenue": order.PRICE
							    	},
						            "products": products
						        }
						    };
							dataLayer.push({
								"event": arNextOptions["COUNTERS"]['GOOGLE_EVENTS']['PURCHASE'],
							    "ecommerce": d
							});

							if(typeof callback !== 'undefined'){
								callback(d);
							}
						});
					}
					else{
						if(typeof callback !== 'undefined'){
							callback();
						}
					}
				},
				error: function(){
					if(typeof callback !== 'undefined'){
						callback();
					}
				}
			});
		}
	}
}

if(!funcDefined("viewItemCounter")){
	function viewItemCounter(id, price_id){
		if(checkCounters()){
			$.ajax({
				url:arNextOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"PRODUCT_ID": id, "PRICE_ID": price_id},
				success: function(item){
					if(item.ID){
						waitLayer(100, function() {
							dataLayer.push({
								//"event": "",
								"ecommerce": {
									"detail": {
										"products": [{
											"id": item.ID,
											"name": item.NAME,
											"price": item.PRICE,
											"brand": item.BRAND,
											"category": item.CATEGORY
										}]
									}
								}
							});
						});
					}
				}
			});
		}
	}
}

if(!funcDefined("checkoutCounter")){
	function checkoutCounter(step, option, callback){
		if(checkCounters('google')){
			$.ajax({
				url:arNextOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"BASKET": "Y"},
				success: function(basket){
					var products = [];
					if(basket.ITEMS){
						for(var i in basket.ITEMS){
							products.push({
								"id": basket.ITEMS[i].ID,
			                    "name": basket.ITEMS[i].NAME,
			                    "price": basket.ITEMS[i].PRICE,
			                    "brand": basket.ITEMS[i].BRAND,
			                    "category": basket.ITEMS[i].CATEGORY,
			                    "quantity": basket.ITEMS[i].QUANTITY
							});
						}
					}
					if(products){
						waitLayer(100, function() {
							dataLayer.push({
								"event": arNextOptions["COUNTERS"]['GOOGLE_EVENTS']['CHECKOUT_ORDER'],
							    "ecommerce": {
							    	'checkout': {
								    	"actionField":{
								    		"step": step,
								    		"option": option
								    	},
								        "products": products
								    }
							    },
							    /*"eventCallback": function() {
							    	if((typeof callback !== 'undefined') && (typeof callback === 'function')){
							    		callback();
							    	}
							   }*/
							});
						});
					}
				}
			});
		}
	}
}

if(!funcDefined("delFromBasketCounter")){
	function delFromBasketCounter(id, callback){
		if(checkCounters()){
			$.ajax({
				url:arNextOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"ID": id},
				success: function(item){
					if(item.ID){
						waitLayer(100, function() {
							dataLayer.push({
								"event": arNextOptions["COUNTERS"]['GOOGLE_EVENTS']['REMOVE_BASKET'],
							    "ecommerce": {
							        "remove": {
							            "products": [{
						                    "id": item.ID,
						                    "name": item.NAME,
						                    "category": item.CATEGORY
						                }]
							        }
							    }
							});
							if(typeof callback == 'function'){
								callback();
							}
						});
					}
				}
			});
		}
	}
}

if(!funcDefined("setHeightCompany")){
	function setHeightCompany(){
		$('.md-50.img').height($('.md-50.big').outerHeight()-35);
	}
}

if(!funcDefined("initSly")){
	function initSly(){
		var $frame  = $(document).find('.frame');
		var $slidee = $frame.children('ul').eq(0);
		var $wrap   = $frame.parent();

		if(arNextOptions["PAGES"]["CATALOG_PAGE"] && $frame.length){
			$frame.sly({
				horizontal: 1,
				itemNav: 'basic',
				smart: 1,
				mouseDragging: 0,
				touchDragging: 0,
				releaseSwing: 0,
				startAt: 0,
				scrollBar: $wrap.find('.scrollbar'),
				scrollBy: 1,
				speed: 300,
				elasticBounds: 0,
				easing: 'swing',
				dragHandle: 1,
				dynamicHandle: 1,
				clickBar: 1,

				// Buttons
				forward: $wrap.find('.forward'),
				backward: $wrap.find('.backward'),
			});
			$frame.sly('reload');
		}
	}
}

if(!funcDefined("createTableCompare")){
	function createTableCompare(originalTable, appendDiv, cloneTable){

		try{
			var clone = originalTable.clone().removeAttr('id').addClass('clone');
			if(cloneTable.length){
				cloneTable.remove();
				appendDiv.html('');
				appendDiv.html(clone);
			}else{
				appendDiv.append(clone);
			}
		}
		catch(e){}
		finally{

		}
	}
}

if(!funcDefined('fillBasketPropsExt')){
	fillBasketPropsExt = function(that, prop_code, basket_prop_div){
		var
			i = 0,
			propCollection = null,
			foundValues = false,
			basketParams = {},
			obBasketProps = null;

		// obBasketProps = that.closest('.catalog_detail').find('.basket_props_block');
		obBasketProps = BX(basket_prop_div);

		if (!!obBasketProps)
		{
			propCollection = obBasketProps.getElementsByTagName('select');
			if (!!propCollection && !!propCollection.length)
			{
				for (i = 0; i < propCollection.length; i++)
				{
					if (!propCollection[i].disabled)
					{
						switch(propCollection[i].type.toLowerCase())
						{
							case 'select-one':
								basketParams[propCollection[i].name] = propCollection[i].value;
								foundValues = true;
								break;
							default:
								break;
						}
					}
				}
			}
			propCollection = obBasketProps.getElementsByTagName('input');
			if (!!propCollection && !!propCollection.length)
			{
				for (i = 0; i < propCollection.length; i++)
				{
					if (!propCollection[i].disabled)
					{
						switch(propCollection[i].type.toLowerCase())
						{
							case 'hidden':
								basketParams[propCollection[i].name] = propCollection[i].value;
								foundValues = true;
								break;
							case 'radio':
								if (propCollection[i].checked)
								{
									basketParams[propCollection[i].name] = propCollection[i].value;
									foundValues = true;
								}
								break;
							default:
								break;
						}
					}
				}
			}
		}
		if (!foundValues)
		{
			basketParams[prop_code] = [];
			basketParams[prop_code][0] = 0;
		}
		return basketParams;
	}
}
if(!funcDefined('showBasketError')){
	showBasketError = function(mess, title, addButton, th){
		var title_set=(title ? title : BX.message("ERROR_BASKET_TITLE")),
			isButton="N",
			thButton="";
		if(typeof addButton!==undefined){
			isButton="Y";
		}
		if(typeof th!==undefined){
			thButton=th;
		}
		$("body").append("<span class='add-error-bakset' style='display:none;'></span>");
		jqmEd('basket_error', 'error-bakset', '.add-error-bakset', mess, this, title_set, isButton, thButton);
		$("body .add-error-bakset").click();
		$("body .add-error-bakset").remove();
	}
}

CheckTopMenuDotted = function(){
	var menu = $('nav.mega-menu.sliced');

	/*if(isMobile)
		return;*/
	if(window.matchMedia('(max-width:991px)').matches)
		return;

	if(menu.length)
	{
		menu.each(function(){
			if($(this).hasClass('initied'))
				return false;

			var menuMoreItem = $(this).find('td.js-dropdown');
			if($(this).parents('.collapse').css('display') == 'none'){
				return false;
			}

			var block_w = $(this).closest('div').actual('width');
			var	menu_w = $(this).find('table').actual('outerWidth');
			var afterHide = false;

			while(menu_w > block_w) {
				menuItemOldSave = $(this).find('td').not('.nosave').last();
				if(menuItemOldSave.length){
					menuMoreItem.show();
					var oldClass = menuItemOldSave.attr('class');
					menuItemNewSave = '<li class="menu-item ' + (menuItemOldSave.hasClass('dropdown') ? 'dropdown-submenu ' : '') + (menuItemOldSave.hasClass('active') ? 'active ' : '') + '" data-hidewidth="' + menu_w + '" ' + (oldClass ? 'data-class="' + oldClass + '"' : '') + '>' + menuItemOldSave.find('.wrap').html() + '</li>';
					menuItemOldSave.remove();
					menuMoreItem.find('> .wrap > .dropdown-menu').prepend(menuItemNewSave);
					menu_w = $(this).find('table').actual('outerWidth');
					afterHide = true;
				}
				//menu.find('.nosave').css('display', 'table-cell');
				else{
					break;
				}
			}

			if(!afterHide) {
				do {
					var menuItemOldSaveCnt = menuMoreItem.find('.dropdown-menu').find('li').length;
					menuItemOldSave = menuMoreItem.find('.dropdown-menu').find('li').first();
					if(!menuItemOldSave.length) {
						menuMoreItem.hide();
						break;
					}
					else {
						var hideWidth = menuItemOldSave.attr('data-hidewidth');
						if(hideWidth > block_w) {
							break
						}
						else {
							var oldClass = menuItemOldSave.attr('data-class');
							menuItemNewSave = '<td class="' + (oldClass ? oldClass + ' ' : '') + '" data-hidewidth="' + block_w + '"><div class="wrap">' + menuItemOldSave.html() + '</div></td>';
							menuItemOldSave.remove();
							$(menuItemNewSave).insertBefore($(this).find('td.js-dropdown'));
							if(!menuItemOldSaveCnt) {
								menuMoreItem.hide();
								break;
							}
						}
					}
					menu_w = $(this).find('table').actual('outerWidth');
				}
				while(menu_w <= block_w);
			}
			$(this).find('td').css('visibility', 'visible');
			$(this).find('td').removeClass('unvisible');
			$(this).addClass('ovisible');
			$(this).addClass('initied');
		})
	}
	return false;
}

CheckTopVisibleMenu = function(that) {
	var dropdownMenu = $('.dropdown-menu:visible').last();

	if(dropdownMenu.length){
		dropdownMenu.find('a').css('white-space', '');
		dropdownMenu.css('left', '');
		dropdownMenu.css('right', '');
		dropdownMenu.removeClass('toright');

		var dropdownMenu_left = dropdownMenu.offset().left;
		if(typeof(dropdownMenu_left) != 'undefined'){
			var menu = dropdownMenu.parents('.mega-menu');
			if(!menu.length)
				menu = dropdownMenu.closest('.logo-row');
			var menu_width = menu.outerWidth();
			var menu_left = menu.offset().left;
			var menu_right = menu_left + menu_width;
			var isToRight = dropdownMenu.parents('.toright').length > 0;
			var parentsDropdownMenus = dropdownMenu.parents('.dropdown-menu');
			var isHasParentDropdownMenu = parentsDropdownMenus.length > 0;
			if(isHasParentDropdownMenu){
				var parentDropdownMenu_width = parentsDropdownMenus.first().outerWidth();
				var parentDropdownMenu_left = parentsDropdownMenus.first().offset().left;
				var parentDropdownMenu_right = parentDropdownMenu_width + parentDropdownMenu_left;
			}

			if(parentDropdownMenu_right + dropdownMenu.outerWidth() > menu_right){
				dropdownMenu.find('a').css('white-space', 'normal');
			}

			var dropdownMenu_width = dropdownMenu.outerWidth();
			var dropdownMenu_right = dropdownMenu_left + dropdownMenu_width;

			if(dropdownMenu_right > menu_right || isToRight){
				var addleft = 0;
				addleft = menu_right - dropdownMenu_right;
				if(isHasParentDropdownMenu || isToRight){
					dropdownMenu.css('left', 'auto');
					dropdownMenu.css('right', '100%');
					dropdownMenu.addClass('toright');
				}
				else{
					var dropdownMenu_curLeft = parseInt(dropdownMenu.css('left'));
					dropdownMenu.css('left', (dropdownMenu_curLeft + addleft) + 'px');
				}
			}
		}
	}
}

if(!funcDefined("isRealValue")){
	function isRealValue(obj){
		return obj && obj !== "null" && obj!== "undefined";
	}
}

if(!funcDefined("rightScroll")){
	function rightScroll(prop, id){
		var el = BX('prop_' + prop + '_' + id);
		if (el) {
			var curVal = parseInt(el.style.marginLeft);
			if (curVal >= 0) el.style.marginLeft = curVal - 20 + '%';
		}
	}
}

if(!funcDefined("leftScroll")){
	function leftScroll(prop, id){
		var el = BX('prop_' + prop + '_' + id);
		if (el) {
			var curVal = parseInt(el.style.marginLeft);
			if (curVal < 0) el.style.marginLeft = curVal + 20 + '%';
		}
	}
}

if(!funcDefined("InitOrderCustom")){
	InitOrderCustom = function () {
		$('.ps_logo img').wrap('<div class="image"></div>');

		$('#bx-soa-order .radio-inline').each(function() {
			if ($(this).find('input').attr('checked') == 'checked') {
				$(this).addClass('checked');
			}
		});

		$('#bx-soa-order .checkbox input[type=checkbox]').each(function() {
			if ($(this).attr('checked') == 'checked')
				$(this).parent().addClass('checked');
		});

		$('#bx-soa-order .bx-authform-starrequired').each(function() {
			var html = $(this).html();
			var $label = $(this).closest('label').length ? $(this).closest('label') : $(this).closest('.bx-authform-label-container');
			var $captcha = $label.find('.bx-captcha');
			if($captcha.length){
				$('<span class="bx-authform-starrequired"> '+ html + '</span>').insertBefore($captcha);
			}
			else{
				$label.append('<span class="bx-authform-starrequired"> '+ html + '</span>');
			}
			$(this).remove();
		});

		$('.bx_ordercart_coupon').each(function() {
			if ($(this).find('.bad').length)
				$(this).addClass('bad');
			else if ($(this).find('.good').length)
				$(this).addClass('good');
		});

		/*if (typeof(propsMap) !== 'undefined') {
			$(propsMap).on('click', function () {
				var value = $('#orderDescription').val();
				if ($('#orderDescription')) {
					if (value != '') {
						$('#orderDescription').closest('.form-group').addClass('value_y');
					}
				}
			});
		}*/
	}
}

if(!funcDefined("InitLabelAnimation")){
	InitLabelAnimation = function(className) {
		// Fix order labels
		if (!$(className).length) {
			return;
		}
		$(className).find('.form-group').each(function() {
			if ($(this).find('input[type=text], textarea').length && !$(this).find('.dropdown-block').length && $(this).find('input[type=text], textarea').val() != '') {
				$(this).addClass('value_y');
			}
		});

		$(document).on('click', className+' .form-group:not(.bx-soa-pp-field) label', function() {
			$(this).parent().find('input, textarea').focus();
		});

		$(document).on('focusout', className+' .form-group:not(.bx-soa-pp-field) input, '+className+' .form-group:not(.bx-soa-pp-field) textarea', function() {
			var value = $(this).val();
			if (value != '' && !$(this).closest('.form-group').find('.dropdown-block').length && !$(this).closest('.form-group').find('#profile_change').length) {
				$(this).closest('.form-group').addClass('value_y');
			}else{
				$(this).closest('.form-group').removeClass('value_y');
			}
		});

		$(document).on('focus', className+' .form-group:not(.bx-soa-pp-field) input, '+className+' .form-group:not(.bx-soa-pp-field) textarea', function() {
			if (!$(this).closest('.form-group').find('.dropdown-block').length && !$(this).closest('.form-group').find('#profile_change').length && !$(this).closest('.form-group').find('[name=PERSON_TYPE_OLD]').length ) {
				$(this).closest('.form-group').addClass('value_y');
			}
		});
	};
}

checkPopupWidth = function(){
	$('.popup.show').each(function() {
		var width_form = $(this).actual('width');
		$(this).css({
			'margin-left': ($(window).width() > width_form ? '-' + width_form / 2 + 'px' : '-' + $(window).width() / 2 + 'px'),
		});
	});
}

checkCaptchaWidth = function(){
	$('.captcha-row').each(function() {
		var width = $(this).actual('width');
		if($(this).hasClass('b')){
			if(width > 320){
				$(this).removeClass('b');
			}
		}
		else{
			if(width <= 320){
				$(this).addClass('b');
			}
		}
	});
}

checkFormWidth = function(){
	$('.form .form_left').each(function() {
		var form = $(this).parents('.form');
		var width = form.actual('width');
		if(form.hasClass('b')){
			if(width > 417){
				form.removeClass('b');
			}
		}
		else{
			if(width <= 417){
				form.addClass('b');
			}
		}
	});
}

checkFormControlWidth = function(){
	$('.form-control').each(function() {
		var width = $(this).actual('width');
		var labelWidth = $(this).find('label:not(.error) > span').actual('width');
		var errorWidth = $(this).find('label.error').actual('width');
		if(errorWidth > 0){
			if($(this).hasClass('h')){
				if(width > (labelWidth + errorWidth + 5)){
					$(this).removeClass('h');
				}
			}
			else{
				if(width <= (labelWidth + errorWidth + 5)){
					$(this).addClass('h');
				}
			}
		}
		else{
			$(this).removeClass('h');
		}
	});
}

scrollToTop = function(){
	if(arNextOptions['THEME']['SCROLLTOTOP_TYPE'] !== 'NONE'){
		var _isScrolling = false;
		// Append Button
		$('body').append($('<a />').addClass('scroll-to-top ' + arNextOptions['THEME']['SCROLLTOTOP_TYPE'] + ' ' + arNextOptions['THEME']['SCROLLTOTOP_POSITION']).attr({'href': '#', 'id': 'scrollToTop'}));
		$('#scrollToTop').click(function(e){
			e.preventDefault();
			$('body, html').animate({scrollTop : 0}, 500);
			return false;
		});
		// Show/Hide Button on Window Scroll event.
		$(window).scroll(function(){
			if(!_isScrolling) {
				_isScrolling = true;
				if($(window).scrollTop() > 150){
					$('#scrollToTop').stop(true, true).addClass('visible');
					_isScrolling = false;
				}
				else{
					$('#scrollToTop').stop(true, true).removeClass('visible');
					_isScrolling = false;
				}
				checkScrollToTop();
			}
		});
	}
}

checkScrollToTop = function(){
	var bottom = 55,
		scrollVal = $(window).scrollTop(),
		windowHeight = $(window).height(),
		footerOffset = 0;
	if($('footer').length)
		footerOffset = $('footer').offset().top +70;

	if(arNextOptions['THEME']['SCROLLTOTOP_POSITION'] == 'CONTENT'){
		warpperWidth = $('body > .wrapper > .wrapper_inner').width();
		$('#scrollToTop').css('margin-left', Math.ceil(warpperWidth / 2) + 23);
	}

	if(scrollVal + windowHeight > footerOffset){
		$('#scrollToTop').css('bottom', bottom  + scrollVal + windowHeight - footerOffset + 40);
	}
	else if(parseInt($('#scrollToTop').css('bottom')) > bottom){
		$('#scrollToTop').css('bottom', bottom);
	}
}

CheckObjectsSizes = function() {
	$('.container iframe,.container object,.container video').each(function() {
		var height_attr = $(this).attr('height');
		var width_attr = $(this).attr('width');
		if (height_attr && width_attr) {
			$(this).css('height', $(this).outerWidth() * height_attr / width_attr);
		}
	});
}

if(!funcDefined('reloadTopBasket')){
	var reloadTopBasket = function reloadTopBasket(action, basketWindow, speed, delay, slideDown, item){
		var obj={
				"PARAMS": $('#top_basket_params').val(),
				"ACTION": action
			};
		if(typeof item !== "undefined" ){
			obj.delete_top_item='Y';
			obj.delete_top_item_id=item.data('id');
		}
		// $.post( arNextOptions['SITE_DIR']+"ajax/show_basket_popup.php", obj, $.proxy(function( data ){
		$.post( arNextOptions['SITE_DIR']+"ajax/show_basket_actual.php", obj, $.proxy(function( data ){
			$(basketWindow).html(data);

			getActualBasket();

			var eventdata = {action:'loadBasket'};
			BX.onCustomEvent('onCompleteAction', [eventdata]);

			/*if(arNextOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
				if($(window).outerWidth() > 520){
					if(slideDown=="Y")
						$(basketWindow).find('.basket_popup_wrapp').stop(true,true).slideDown(speed);
					clearTimeout(basketTimeoutSlide);
					basketTimeoutSlide = setTimeout(function() {
						var _this = $('#basket_line').find('.basket_popup_wrapp');
						if (_this.is(':hover')) {
							_this.show();
						}else{
							$('#basket_line').find('.basket_popup_wrapp').slideUp(speed);
						}
					},delay);
				}
			}*/
		}))
	}
}

CheckTabActive = function(){
	if(typeof(clicked_tab) && clicked_tab)
	{
		if(window.matchMedia('(min-width: 768px)').matches)
		{
			clicked_tab--;
			$('.nav.nav-tabs li').each(function(){
				if($(this).index() == clicked_tab)
					$(this).addClass('active');
			})
			// $('.nav.nav-tabs li:eq('+clicked_tab+')').addClass('active');
			$('.catalog_detail .tab-content .tab-pane:eq('+clicked_tab+')').addClass('active');
			$('.catalog_detail .tab-content .tab-pane .title-tab-heading').next().removeAttr('style');
			clicked_tab = 0;
		}
	}
}

/*countdown start*/
if(!funcDefined('initCountdown')){
	var initCountdown = function initCountdown(){
		if( $('.view_sale_block').size() ){
			$('.view_sale_block').each(function(){
				var activeTo=$(this).find('.active_to').text(),
					dateTo= new Date(activeTo.replace(/(\d+)\.(\d+)\.(\d+)/, '$3/$2/$1'));
				$(this).find('.countdown').countdown({until: dateTo, format: 'dHMS', padZeroes: true, layout: '{d<}<span class="days item">{dnn}<div class="text">{dl}</div></span>{d>} <span class="hours item">{hnn}<div class="text">{hl}</div></span> <span class="minutes item">{mnn}<div class="text">{ml}</div></span> <span class="sec item">{snn}<div class="text">{sl}</div></span>'}, $.countdown.regionalOptions['ru']);
			})
		}
	}
}

if(!funcDefined('initCountdownTime')){
	var initCountdownTime = function initCountdownTime(block, time){
		if(time)
		{
			var dateTo= new Date(time.replace(/(\d+)\.(\d+)\.(\d+)/, '$3/$2/$1'));
			block.find('.countdown').countdown('destroy');
			block.find('.countdown').countdown({until: dateTo, format: 'dHMS', padZeroes: true, layout: '{d<}<span class="days item">{dnn}<div class="text">{dl}</div></span>{d>} <span class="hours item">{hnn}<div class="text">{hl}</div></span> <span class="minutes item">{mnn}<div class="text">{ml}</div></span> <span class="sec item">{snn}<div class="text">{sl}</div></span>'}, $.countdown.regionalOptions['ru']);
			block.find('.view_sale_block').show();
		}
		else
		{
			block.find('.view_sale_block').hide();
		}
	}
}
/*countdown end*/

waitCounter = function(idCounter, delay, callback){
	var obCounter = window['yaCounter' + idCounter];
	if(typeof obCounter == 'object')
	{
		if(typeof callback == 'function')
			callback();

	}
	else
	{
		setTimeout(function(){
			waitCounter(idCounter, delay, callback);
		}, delay);
	}
}

var isOnceInited = insertFilter = false;
var animationTime = 200;
var delayTime = 200;
var topMenuEnterTimer = false;
var isMobile = jQuery.browser.mobile;

if(isMobile){
	document.documentElement.className += ' mobile';
}

if(navigator.userAgent.indexOf("Edge") != -1){
	document.documentElement.className += ' bx-ie-edge';
}

// ONE CLICK
if(!funcDefined("oneClickBuy")) {
	var oneClickBuy = function (elementID, iblockID, that) {
		var name = 'one_click_buy';
		var elementQuantity = 1;
		var offerProps = false;
		var buy_btn=$(that).closest('.buy_block').find('.to-cart');
		var buy_btn2=$(that).closest('tr').find('.to-cart');

		if(typeof(that) !== 'undefined'){
			elementQuantity = $(that).attr('data-quantity');
			offerProps = $(that).attr('data-props');
		}

		if(elementQuantity < 0){
			elementQuantity = 1;
		}

		var tmp_props=buy_btn.data("props"),
			tmp_props2=buy_btn2.data("props"),
			props='',
			part_props='',
			add_props='N',
			fill_prop={},
			iblockid = buy_btn.data('iblockid'),
			item = buy_btn.attr('data-item');

		if(tmp_props){
			props=tmp_props.split(";");
		}else if(tmp_props2){
			props=tmp_props2.split(";");
		}
		if(buy_btn.data("part_props")){
			part_props=buy_btn.data("part_props");
		}
		if(buy_btn.data("add_props")){
			add_props=buy_btn.data("add_props");
		}

		fill_prop=fillBasketPropsExt(buy_btn, 'prop', buy_btn.data('bakset_div'));
		fill_prop.iblockID=iblockid;
		fill_prop.part_props=part_props;
		fill_prop.add_props=add_props;
		fill_prop.props=JSON.stringify(props);
		fill_prop.item=item;
		fill_prop.ocb_item="Y";

		if(window.matchMedia('(min-width:992px)').matches)
		{
			if(!$(that).hasClass('clicked'))
			{
				$(that).addClass('clicked');
				$('body').find('.'+name+'_frame').remove();
				$('body').find('.'+name+'_trigger').remove();
				$('body').append('<div class="'+name+'_frame popup"></div>');
				$('body').append('<div class="'+name+'_trigger"></div>');
				$('.'+name+'_frame').jqm({trigger: '.'+name+'_trigger', onHide: function(hash) { onHidejqm(name,hash); }, toTop: false, onLoad: function( hash ){ onLoadjqm(name, hash ); }, ajax: arNextOptions["SITE_DIR"]+'ajax/one_click_buy.php?ELEMENT_ID='+elementID+'&IBLOCK_ID='+iblockID+'&ELEMENT_QUANTITY='+elementQuantity+'&OFFER_PROPS='+fill_prop.props});
				$('.'+name+'_trigger').click();
			}
		}
		else
		{
			var script = arNextOptions['SITE_DIR'] + 'form/';
			script += '?name='+name+'&form_id=ocb&path='+window.location.pathname+'&ELEMENT_ID='+elementID+'&IBLOCK_ID='+iblockID+'&ELEMENT_QUANTITY='+elementQuantity+'&OFFER_PROPS='+fill_prop.props
			location.href = script;
		}
	}
}

if(!funcDefined("oneClickBuyBasket")) {
	var oneClickBuyBasket = function () {
		name = 'one_click_buy_basket';
		if(window.matchMedia('(min-width:992px)').matches)
		{
			if(!$('.fast_order').hasClass('clicked'))
			{
				$('.fast_order').addClass('clicked');
				$('body').find('.'+name+'_frame').remove();
				$('body').find('.'+name+'_trigger').remove();
				$('body').append('<div class="'+name+'_frame popup"></div>');
				$('body').append('<div class="'+name+'_trigger"></div>');
				$('.'+name+'_frame').jqm({trigger: '.'+name+'_trigger', onHide: function(hash) { onHidejqm(name,hash) }, onLoad: function( hash ){ onLoadjqm( name, hash ); }, ajax: arNextOptions["SITE_DIR"]+'ajax/one_click_buy_basket.php'});
				$('.'+name+'_trigger').click();
			}
		}
		else
		{
			var script = arNextOptions['SITE_DIR'] + 'form/';
			script += '?name='+name+'&form_id=ocb&path='+window.location.pathname+'&buy_basket=y';
			location.href = script;
		}
	}
}

// TOP MENU ANIMATION
$(document).on('click', '.menu_top_block>li .more a', function(){
	$this = $(this);
	$this.parents('.dropdown').first().find('>.hidden').removeClass('hidden');
	$this.parent().addClass('hidden');
	setTimeout(function(){
		$this.parent().remove();
	}, 500);
});

$(document).on('mouseenter', '.menu_top_block.catalogfirst>li>.dropdown>li.full', function(){
	var $submenu = $(this).find('>.dropdown');

	if($submenu.length){
		if(topMenuEnterTimer){
			clearTimeout(topMenuEnterTimer);
			topMenuEnterTimer = false;
		}
	}
});

$(document).on('mouseenter', '.menu_top_block>li:not(.full)', function(){
	var $submenu = $(this).find('>.dropdown');

	if($submenu.length && !$submenu.hasClass('visible')){
		var $menu = $(this).parents('.menu');
		var $wrapmenu = $menu.parents('.wrap_menu');
		var wrapMenuWidth = $wrapmenu.actual('outerWidth');
		var wrapMenuLeft = $wrapmenu.offset().left;
		var wrapMenuRight = wrapMenuLeft + wrapMenuWidth;
		var left = wrapMenuRight - ($(this).offset().left + $submenu.actual('outerWidth'));
		if(window.matchMedia('(min-width: 951px)').matches && $(this).hasClass('catalog') && ( $('.banner_auto').hasClass('catalog_page') || $('.banner_auto').hasClass('front_page'))){
			return;
		}
		if(left < 0){
			$submenu.css({left: left + 'px'});
		}
		$submenu.stop().slideDown(animationTime, function(){
			$submenu.css({height: '', 'overflow':'visible'});
		});


		$(this).on('mouseleave', function(){
			var leaveTimer = setTimeout(function(){
				$submenu.stop().slideUp(animationTime, function(){
					$submenu.css({left: ''});
				});
			}, delayTime);

			$(this).on('mouseenter', function(){
				if(leaveTimer){
					clearTimeout(leaveTimer);
					leaveTimer = false;
				}
			});
		});
	}
});

$(document).on('mouseenter', '.menu_top_block>li .dropdown>li', function(){
	var $this = $(this);
	var $submenu = $this.find('>.dropdown');

	if($submenu.length && ((!$this.parents('.full').length && !$this.hasClass('full')) || $this.parents('.more').length)){
		var $menu = $this.parents('.menu');
		var $wrapmenu = $menu.parents('.wrap_menu');
		var arParentSubmenuForOpacity = [];
		topMenuEnterTimer = setTimeout(function(){

			var wrapMenuWidth = $wrapmenu.actual('outerWidth');
			var wrapMenuLeft = $wrapmenu.offset().left;
			var wrapMenuRight = wrapMenuLeft + wrapMenuWidth;
			var $parentSubmenu = $this.parent();
			var bToLeft = $parentSubmenu.hasClass('toleft') ? true : false;
			if(!bToLeft){
				bToLeft = $this.offset().left + $this.actual('outerWidth') + $submenu.actual('outerWidth') > wrapMenuRight;
			}
			else{
				bToLeft = $this.offset().left + $this.actual('outerWidth') - $submenu.actual('outerWidth') < wrapMenuLeft;
			}

			if(bToLeft){
				$this.find('>.dropdown').addClass('toleft').show();
			}
			else{
				$this.find('>.dropdown').removeClass('toleft').show();
			}
			var submenuLeft = $submenu.offset().left;
			var submenuRight = submenuLeft + $submenu.actual('outerWidth');

			$this.parents('.dropdown').each(function(){
				var $this = $(this);
				var leftOffset = $this.offset().left;
				var rightOffset = leftOffset + $this.actual('outerWidth');
				if(leftOffset >= submenuLeft  && leftOffset < (submenuRight - 1) || (rightOffset > (submenuLeft + 1) && rightOffset <= submenuRight)){
					arParentSubmenuForOpacity.push($this);
					$this.find('>li>a').css({opacity: '0.1'});
				}
			});
		}, delayTime);

		$this.unbind('mouseleave');
		$this.on('mouseleave', function(){
			var leaveTimer = setTimeout(function(){
				$this.find('.dropdown').removeClass('toleft').hide();
				if(arParentSubmenuForOpacity.length){
					for(i in arParentSubmenuForOpacity){
						arParentSubmenuForOpacity[i].find('>li>a').css({opacity: ''});
					}
				}
			}, delayTime);

			$this.unbind('mouseenter');
			$this.on('mouseenter', function(){
				if(leaveTimer){
					clearTimeout(leaveTimer);
					leaveTimer = false;
				}
			});
		});
	}
});

getGridSize = function(counts,slider) {
	var counts_item=1;
		//wide
		if(window.matchMedia('(min-width: 1200px)').matches){
			counts_item=counts[0];
			if(typeof(slider.data('lg_count')) !== 'undefined' && slider.data('lg_count') && $('.front.wide_page').length)
				counts_item=slider.data('lg_count');
		}

		//large
		if(window.matchMedia('(max-width: 1200px)').matches){
			counts_item=counts[1];
		}

		//middle
		if(window.matchMedia('(max-width: 992px)').matches){
			counts_item=counts[2];
		}

		//small
		if(counts[3]){
			if(window.matchMedia('(max-width: 600px)').matches){
				counts_item=counts[3];
			}
		}

		//exsmall
		if(counts[4]){
			if(window.matchMedia('(max-width: 400px)').matches){
				counts_item=counts[4];
			}
		}
	return counts_item;
}

CheckFlexSlider = function(){
	$('.flexslider:not(.thmb)').each(function(){
		var slider = $(this);
		if(typeof(slider.data('flexslider')) != 'undefined')
		{
			if('vars' in slider.data('flexslider'))
			{
				slider.resize();

				var counts = slider.data('flexslider').vars.counts;
				if(typeof(counts) != 'undefined' && slider.is(':visible')){
					var cnt = getGridSize(counts,slider);
					var to0 = (cnt != slider.data('flexslider').vars.minItems || cnt != slider.data('flexslider').vars.maxItems || cnt != slider.data('flexslider').vars.move);
					if(to0){
						slider.data('flexslider').vars.minItems = cnt;
						slider.data('flexslider').vars.maxItems = cnt;
						slider.data('flexslider').vars.move = cnt;
						slider.flexslider(0);
						slider.resize();
						slider.resize(); // twise!
					}
				}
			}
		}
	});
}

InitFlexSlider = function() {
	$('.flexslider:not(.thmb):not(.flexslider-init)').each(function(){
		var slider = $(this);
		var options;
		var defaults = {
			animationLoop: false,
			controlNav: false,
			keyboard: false,
			pauseOnAction: false,
			pauseInvisible: false,
			directionNav: true,
			useCSS: false,
			animation: "slide"
		}
		var config = $.extend({}, defaults, options, slider.data('plugin-options'));
		if(!slider.parent().hasClass('top_slider_wrapp') && slider.is(':visible')){
			if(typeof(config.counts) != 'undefined' && config.direction !== 'vertical'){
				config.maxItems =  getGridSize(config.counts,slider);
				config.minItems = getGridSize(config.counts,slider);
				config.itemWidth = 200;
			}
			if(typeof(config.move) == 'undefined')
				config.move = 1;

			config.start = function(slider){
				var eventdata = {slider: slider};
				BX.onCustomEvent('onSlideInit', [eventdata]);
			}

			config.after = function(slider){
				var eventdata = {slider: slider};
				BX.onCustomEvent('onSlideComplete', [eventdata]);
			}

			config.end = function(slider){
				var eventdata = {slider: slider};
				BX.onCustomEvent('onSlideEnd', [eventdata]);
			}

			slider.flexslider(config).addClass('flexslider-init');
			if(config.controlNav)
				slider.addClass('flexslider-control-nav');
			if(config.directionNav)
				slider.addClass('flexslider-direction-nav');
		}
	});
}

InitZoomPict = function(el) {
	var block = $('.zoom_picture');
	if(typeof el !== 'undefined')
		block = el;
	if(block.length){
		var slide=block.closest('.slides');
		var zoomer = block,
			options,
			defaults = {
				zoomWidth: 200,
				zoomHeight: 200,
				adaptive: false,
				title: true,
				Xoffset: 15,

			};
		var config = $.extend({}, defaults, options, zoomer.data('plugin-options'));
			zoomer.xzoom(config);

		/*block.on('mouseleave', function(){
			if($('.xzoom-lens').length)
				block.data('xzoom').closezoom();
		})*/
		block.on('mouseleave', function(){
			block.data('xzoom').movezoom(event);
		})
	}
}

var arBasketAsproCounters = arStatusBasketAspro = arBasketPrices = {};
SetActualBasketFlyCounters = function(){
	if(arBasketAsproCounters.DEFAULT == true){
		$.ajax({
			url: arNextOptions['SITE_DIR'] + 'ajax/basket_fly.php',
			type: 'post',
			success: function(html){
				$('#basket_line .basket_fly').removeClass('loaded').html(html);
			}
		});
	}
	else{
		$('.basket_fly .opener .basket_count .count').attr('class', 'count' + (arBasketAsproCounters.READY.COUNT > 0 ? '' : ' empty_items')).find('.items span').text(arBasketAsproCounters.READY.COUNT)
		$('.basket_fly .opener .basket_count + a').attr('href', arBasketAsproCounters['READY']['HREF'])
		$('.basket_fly .opener .basket_count').attr('title', arBasketAsproCounters.READY.TITLE).attr('class', 'basket_count small clicked' + (arBasketAsproCounters.READY.COUNT > 0 ? '' : ' empty'))

		$('.basket_fly .opener .wish_count .count').attr('class', 'count' + (arBasketAsproCounters.DELAY.COUNT > 0 ? '' : ' empty_items')).find('.items span').text(arBasketAsproCounters.DELAY.COUNT)
		$('.basket_fly .opener .wish_count + a').attr('href', arBasketAsproCounters.DELAY.HREF)
		$('.basket_fly .opener .wish_count').attr('title', arBasketAsproCounters.DELAY.TITLE).attr('class', 'wish_count small clicked' + (arBasketAsproCounters.DELAY.COUNT > 0 ? '' : ' empty'))

		$('.basket_fly .opener .compare_count .wraps_icon_block').attr('class', 'wraps_icon_block compare' + (arBasketAsproCounters.COMPARE.COUNT > 0 ? '' : ' empty_block'));
		$('.basket_fly .opener .compare_count .count').attr('class', 'count' + (arBasketAsproCounters.COMPARE.COUNT > 0 ? '' : ' empty_items')).find('.items span').text(arBasketAsproCounters.COMPARE.COUNT)
		$('.basket_fly .opener .compare_count + a').attr('href', arBasketAsproCounters.COMPARE.HREF)

	}
}

CheckHeaderFixed = function(){
	var header_fixed = $('#headerfixed, #headerSimple');
		header = $('header, #headerSimple').first();
	if(header_fixed.length){
		if(header.length)
		{
			var isHeaderFixed = false,
				isTabsFixed = false,
				headerCanFix = true,
				headerFixedHeight = header_fixed.actual('outerHeight'),
				headerNormalHeight = header.actual('outerHeight'),
				headerDiffHeight = headerNormalHeight - headerFixedHeight,
				mobileBtnMenu = $('.btn.btn-responsive-nav'),
				headerTop = $('#panel:visible').actual('outerHeight');
				topBlock = $('.TOP_HEADER').first();

			if(headerDiffHeight <= 0)
				headerDiffHeight = 0;
			if(topBlock.length)
				headerTop += topBlock.actual('outerHeight');

			$(window).scroll(function(){
				var tabs_fixed = $('.product-item-detail-tabs-container-fixed');
				if(window.matchMedia('(min-width:768px)').matches)
				{
					var scrollTop = $(window).scrollTop();
					headerCanFix = !mobileBtnMenu.is(':visible')/* && !$('.dropdown-menu:visible').length*/;

					var tabs_offset = $('.tabs_section .nav.nav-tabs').offset();

					var current_is = $('.search-wrapper .search-input:visible'),
						title_search_result = $('.title-search-result.'+current_is.attr('id')),
						pos, pos_input;

					if(!isHeaderFixed)
					{
						if((scrollTop > headerNormalHeight + headerTop) && headerCanFix)
						{
							isHeaderFixed = true;
							// header_fixed.css('top', '-' + headerNormalHeight + 'px');
							header_fixed.addClass('fixed');
							headerSimpleHeight = $('#headerSimple').actual('outerHeight');
							$('#headerSimple').closest('.header-wrapper').css({'margin-top': headerSimpleHeight});
							// header_fixed.stop(0).animate({top: '0'}, 300);

							/*header_fixed.animate({top: '0'}, {duration:300, complete:
								function(){}
							});*/

							if($('nav.mega-menu.sliced').length)
								$('nav.mega-menu.sliced').removeClass('initied');
							CheckTopMenuDotted();
						}
					}
					else if(isHeaderFixed || !headerCanFix)
					{
						if((scrollTop <= headerDiffHeight + headerTop) || !headerCanFix)
						{
							isHeaderFixed = false;
							header_fixed.removeClass('fixed');
							$('#headerSimple').closest('.header-wrapper').css({'margin-top': 0});
						}
					}
					//fixed tabs
					if(tabs_fixed.length)
					{
						if (scrollTop + headerFixedHeight > tabs_offset.top)
						{
							tabs_fixed.css({'top': header_fixed.actual('outerHeight')});
							tabs_fixed.addClass('fixed');
						}
						else if (tabs_fixed.hasClass('fixed'))
						{
							tabs_fixed.removeAttr('style');
							tabs_fixed.removeClass('fixed');
						}
					}
				}
			});
		}
	}

	//mobile fixed
	var mfixed = $('.wrapper1.mfixed_Y #mobileheader, .wrapper1.mfixed_Y #mobileheadersimple');
	if(mfixed.length && isMobile)
	{
		var isMHeaderFixed = false,
			mheaderCanFix = true,
			//mheaderFixedHeight = mfixed.actual('outerHeight'),
			mheaderFixedHeight = 0,
			mheaderTop = $('#panel:visible').actual('outerHeight');
		$(window).scroll(function(){
			var scrollTop = $(window).scrollTop();
			if(window.matchMedia('(max-width:991px)').matches)
			{
				if($('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheader').length || $('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheadersimple').length)
				{
					if(scrollTop > startScroll)
					{
						$('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheader').removeClass('fixed');
						$('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheadersimple').removeClass('fixed');
					}
					else if(scrollTop > mheaderFixedHeight + mheaderTop){
						$('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheader').addClass('fixed');
						$('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheadersimple').addClass('fixed');
					}
					else if(scrollTop <= mheaderFixedHeight + mheaderTop){
						$('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheader').removeClass('fixed');
						$('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheadersimple').removeClass('fixed');
					}
					startScroll = scrollTop;
				}
				else
				{
					if(!isMHeaderFixed)
					{
						if((scrollTop > mheaderFixedHeight + mheaderTop))
						{
							isMHeaderFixed = true;
							mfixed.addClass('fixed');
						}
					}
					else if(isMHeaderFixed)
					{
						if((scrollTop <= mheaderFixedHeight + mheaderTop))
						{
							isMHeaderFixed = false;
							mfixed.removeClass('fixed');
						}
					}
				}
			}
			else
				mfixed.removeClass('fixed');
		})
	}
}

CheckHeaderFixedMenu = function(){
	if(arNextOptions['THEME']['HEADER_FIXED'] == 2 && $('#headerfixed .js-nav').length && window.matchMedia('(min-width: 992px)').matches)
	{
		$('#headerfixed .js-nav').css('width','0');
		var all_width = 0,
			cont_width = $('#headerfixed .maxwidth-theme').actual('width'),
			padding_menu = $('#headerfixed .logo-row.v2 .menu-block').actual('outerWidth')-$('#headerfixed .logo-row.v2 .menu-block').actual('width');
		$('#headerfixed .logo-row.v2 > .inner-table-block').each(function(){
			if(!$(this).hasClass('menu-block'))
				all_width += $(this).actual('outerWidth');
		})
		$('#headerfixed .js-nav').width(cont_width-all_width-padding_menu);
	}
}

CheckTopMenuPadding = function(){
	if($('.logo_and_menu-row .right-icons .wrap_icon').length && $('.logo_and_menu-row .menu-row').length){
		var menuPosition = $('.menu-row .menu-only').position().left,
			leftPadding = 0,
			rightPadding = 0;
		$('.logo_and_menu-row .menu-row>div').each(function(indx){
			if(!$(this).hasClass('menu-only')){
				var elementPosition = $(this).position().left,
					elementWidth = $(this).outerWidth()+1;

				if(elementPosition > menuPosition){
					rightPadding += elementWidth;
				}else{
					leftPadding += elementWidth;
				}
			}
		}).promise().done(function(){
			$('.logo_and_menu-row .menu-only').css({'padding-left': leftPadding, 'padding-right': rightPadding});
		});
	}
}

CheckTopMenuOncePadding = function(){
	if($('.menu-row.sliced .right-icons .wrap_icon').length){
		var menuPosition = $('.menu-row .menu-only').position().left,
			leftPadding = 0,
			rightPadding = 0;
		$('.menu-row.sliced .maxwidth-theme>div>div>div').each(function(indx){
			if(!$(this).hasClass('menu-only')){
				var elementPosition = $(this).position().left,
					elementWidth = $(this).outerWidth()+1;

				if(elementPosition > menuPosition){
					rightPadding += elementWidth;
				}else{
					leftPadding += elementWidth;
				}
			}
		}).promise().done(function(){
			$('.menu-row.sliced .menu-only').css({'padding-left': leftPadding, 'padding-right': rightPadding});
		});
	}
}

CheckSearchWidth = function(){
	if($('.logo_and_menu-row .search_wrap').length){
		var searchPosition = $('.logo_and_menu-row .search_wrap').position().left,
			maxWidth = $('.logo_and_menu-row .maxwidth-theme').width() - 2;
			width = 0;

		$('.logo_and_menu-row .maxwidth-theme > .row >div').each(function(){
			if(!$(this).hasClass('search_wrap')){
				var elementWidth = $(this).outerWidth();
				width = (width ? width - elementWidth : maxWidth - elementWidth);
			}
		}).promise().done(function(){
			if($('.logo_and_menu-row .search_wrap.wide_search').length)
				$('.logo_and_menu-row .search_wrap .search-block').outerWidth(width);
			else
				$('.logo_and_menu-row .search_wrap').outerWidth(width);
			$('.logo_and_menu-row .search_wrap').css({'opacity': 1, 'visibility': 'visible'});
		});
	}
}

CheckPhoneDropdown = function(){
        var $footer = $('#footer');
        var $phoneDropdown = $('#footer .phone .dropdown');
        if($footer.length && $phoneDropdown.length){
                if($footer.offset().top+$footer.height() < $phoneDropdown.offset().top+$phoneDropdown.height()){
                        $phoneDropdown.css({'top':'-'+$phoneDropdown.height()+'px'});
                }
        }
};

scrollPreviewBlock = function(){
	if(typeof($.cookie('scroll_block')) != 'undefined' && $.cookie('scroll_block'))
	{
		var scroll_block = $($.cookie('scroll_block'));
		if(scroll_block.length)
		{
			$('body, html').animate({scrollTop: scroll_block.offset().top}, 500);
		}
		$.cookie('scroll_block', null);
	}
}

scrollToBlock = function(block){
	if($(block).length)
	{
		var offset = $(block).offset().top;
		if(typeof($(block).data('toggle')) != 'undefined')
			$(block).click();

		if(typeof($(block).data('offset')) != 'undefined')
			offset += $(block).data('offset');
		$('body, html').animate({scrollTop: offset}, 500);
	}
}

SetFixedAskBlock = function(){
	if($('.ask_a_question_wrapper').length)
	{
		var offset = $('.ask_a_question_wrapper').offset(),
			footer_offset = 0,
			block = $('.ask_a_question_wrapper').find('.ask_a_question'),
			block_offset = BX.pos(block[0]),
			block_height = block_offset.bottom-block_offset.top,
			diff_top_scroll = $('#headerfixed').height() + 20;

		if($('footer').length)
			footer_offset = $('footer').offset().top;

		if($('.banner.CONTENT_BOTTOM').length)
			footer_offset = $('.banner.CONTENT_BOTTOM').offset().top;

		/* removed for ISSUE NEXT-414
		if(block_height+130 > block.closest('.fixed_wrapper').height())
			block.addClass('nonfixed');
		else
			block.removeClass('nonfixed');
		*/

		if(block_height+diff_top_scroll+documentScrollTopLast + 130 > footer_offset)
		{
			block.removeClass('fixed').css({'top': 'auto', 'width': 'auto', 'bottom': 0});
			block.parent().css('position', 'static');
			block.parent().parent().css('position', 'static');
		}
		else
		{
			block.parent().removeAttr('style');
			block.parent().parent().removeAttr('style');

			if(documentScrollTopLast + diff_top_scroll > offset.top)
			{
				var fixed_width = $('.fixed_block_fix').width();
				block.addClass('fixed').css({'top': diff_top_scroll, 'bottom': 'auto'});
				if(fixed_width)
					block.css({'width': $('.fixed_block_fix').width()})
			}
			else
				block.removeClass('fixed').css({'top': 0, 'width': 'auto'});
		}
	}
}

MegaMenuFixed = function(){
	var animationTime = 150;

	$('.logo_and_menu-row .burger').on('click', function(){
		$('.mega_fixed_menu').fadeIn(animationTime);
		$('.header_wrap').toggleClass('zindexed');
	});

	$('.mega_fixed_menu .svg.svg-inline-close').on('click', function(){
		$(this).closest('.mega_fixed_menu').fadeOut(animationTime);
		$('.header_wrap').toggleClass('zindexed');
	});

	$('.mega_fixed_menu .dropdown-menu .arrow').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).closest('.dropdown-submenu').find('.dropdown-menu').slideToggle(animationTime);
		$(this).closest('.dropdown-submenu').addClass('opened');
	});
}

CheckPopupTop = function(){
	var popup = $('.jqmWindow.show:last');
	if(!popup.length)
		popup = $('.jqmWindow.show');
	if(!popup.length)
		popup = $('.jqm-init.show');
	if(popup.length){
		var documentScollTop = $(document).scrollTop();
		var windowHeight = $(window).height();
		var popupTop = parseInt(popup.css('top'));
		var popupHeight = popup.height();

		if(windowHeight >= popupHeight){
			// center
			popupTop = (windowHeight - popupHeight) / 2;
		}
		else{
			if(documentScollTop > documentScrollTopLast){
				// up
				popupTop -= documentScollTop - documentScrollTopLast;
			}
			else if(documentScollTop < documentScrollTopLast){
				// down
				popupTop += documentScrollTopLast - documentScollTop;
			}

			if(popupTop + popupHeight < windowHeight){
				// bottom
				popupTop = windowHeight - popupHeight;
			}
			else if(popupTop > 0){
				// top
				popupTop = 0;
			}
		}
		popup.css('top', popupTop + 'px');
	}
}

initCalculatePreview = function(){
	$('.calculate-delivery.with_preview:not(.inited)').each(function(){
		var $this = $(this);
		var $calculateSpan = $this.find('span[data-event=jqm]');
		var $preview = $this.find('.calculate-delivery-preview');

		$this.addClass('inited');
		$this.appear(function(){
			if($calculateSpan.length){
				if(typeof window['calculate-delivery-preview-index'] === 'undefined'){
					window['calculate-delivery-preview-index'] = 1001;
				}
				else{
					++window['calculate-delivery-preview-index'];
				}

				var productId = $calculateSpan.attr('data-param-product_id') * 1;
				var quantity = $calculateSpan.attr('data-param-quantity') * 1;

				if(productId > 0){
					var areaIndexSended = window['calculate-delivery-preview-index'];
					$calculateSpan.data({areaIndex: areaIndexSended});

					$.ajax({
						url: arNextOptions['SITE_DIR'] + 'ajax/delivery.php',
						type: 'POST',
						data: {
							is_preview: 'Y',
							index: areaIndexSended,
							product_id: productId,
							quantity: quantity
						},
						beforeSend: function(){
							$this.addClass('loadings');
						},
						success: function(response){
							var areaIndex = $calculateSpan.data('areaIndex');
							if(typeof areaIndex !== 'undefined' && areaIndex == areaIndexSended){
								$calculateSpan.hide();
								$preview.html(response);
								if(!$preview.find('.catalog-delivery-preview').length){
									$preview.empty();
									$calculateSpan.show();
								}
							}
						},
						error: function(xhr, ajaxOptions, thrownError){
						},
						complete: function(){
							var areaIndex = $calculateSpan.data('areaIndex');
							if(typeof areaIndex !== 'undefined' && areaIndex == areaIndexSended){
								$this.removeClass('loadings');
							}
						}
					});
				}
			}
		}, {accX: 0, accY: 0});
	});
}

/*set price item*/
if(!funcDefined('setPriceItem')){
	var setPriceItem = function setPriceItem(main_block, quantity, rewrite_price, check_quantity, is_sku, show_percent, percent){
		var old_quantity = main_block.find('.to-cart').attr('data-ratio'),
			value = (typeof rewrite_price !== 'undefined' && rewrite_price ? rewrite_price : main_block.find('.to-cart').attr('data-value')),
			currency = main_block.find('.to-cart').attr('data-currency'),
			total_block = '<div class="total_summ" style="display:none;"><div>'+BX.message('TOTAL_SUMM_ITEM')+'<span></span></div></div>',
			price_block = main_block.find('.cost.prices'),
			use_percent = (typeof show_percent !== 'undefined' && show_percent == 'Y'),
			percent_number = (typeof percent !== 'undefined' && percent),
			sku_checked = (main_block.find('.has_offer_prop').length ? 'Y' : 'N'),
			check = (typeof check_quantity !== 'undefined' && check_quantity);

		if(main_block.find('.buy_block').length)
		{
			if(!main_block.find('.buy_block .total_summ').length && !is_sku)
				$(total_block).appendTo(main_block.find('.buy_block'))
		}
		else if(main_block.find('.counter_wrapp').length)
		{
			if(!main_block.find('.counter_wrapp .total_summ').length && !is_sku)
				$(total_block).appendTo(main_block.find('.counter_wrapp:first'))
		}
		if(main_block.find('.total_summ').length)
		{
			if(value && currency)
			{
				if((1 == quantity && old_quantity == quantity) || (typeof is_sku !== 'undefined' && is_sku && !check))
				{
					main_block.find('.total_summ').slideUp(50);
				}
				else
				{
					main_block.find('.total_summ span').html(BX.Currency.currencyFormat((value*quantity), currency, true));
					if(main_block.find('.total_summ').is(':hidden')/* || sku_checked == 'Y'*/)
						main_block.find('.total_summ').slideDown(100);
				}
			}
			else
			{
				main_block.find('.total_summ').slideUp(100);
			}
		}
	}
}

if(!funcDefined('getCurrentPrice')){
	var getCurrentPrice = function getCurrentPrice(price, currency, print_price){
		var val = '';
		var format_value = BX.Currency.currencyFormat(price, currency);
		if(print_price.indexOf(format_value) >= 0)
		{
			val = print_price.replace(format_value, '<span class="price_value">'+format_value+'</span><span class="price_currency">');
			val += '</span>';
		}
		else
		{
			val = print_price;
		}

		return val;
	}
}

$(document).ready(function(){
	//check width for menu and search
	CheckSearchWidth();
	MegaMenuFixed();
    CheckPhoneDropdown();

	//ecommerce order
	if(arNextOptions["PAGES"]["ORDER_PAGE"])
	{
		var arUrl = parseUrlQuery();
		if("ORDER_ID" in arUrl)
		{
			var _id = arUrl["ORDER_ID"];
			if(arNextOptions['COUNTERS']['USE_FULLORDER_GOALS'] !== 'N')
			{
				var eventdata = {goal: 'goal_order_success', result: _id};
				BX.onCustomEvent('onCounterGoals', [eventdata])
			}
			if(checkCounters())
			{
				if(typeof localStorage !== 'undefined'){
					// var d = BX.localStorage.get('gtm_ee_' + _id);
					var val = localStorage.getItem('gtm_e_' + _id),
						d = '';
					try{
						d = JSON.parse(val);
					}
					catch(e){
						d = val
					}
					if(typeof d === 'object'){
						window.dataLayer = window.dataLayer || [];
						dataLayer.push({"event": arNextOptions["COUNTERS"]['GOOGLE_EVENTS']['PURCHASE'], "ecommerce": d});
					}

					if(typeof localStorage !== 'undefined'){
						localStorage.removeItem('gtm_e_' + _id);
					}
				}
			}
		}
	}

	var bSafary = false;
	if(typeof jQuery.browser == 'object')
		bSafary = jQuery.browser.safari;
	else if(typeof browser == 'object')
		bSafary = browser.safari;
	if(!bSafary){
		CheckTopMenuPadding();
		CheckTopMenuOncePadding();
		CheckTopMenuDotted();
		CheckHeaderFixed();
		setTimeout(function() {$(window).resize();}, 150); // need to check resize flexslider & menu
		setTimeout(function() {$(window).scroll();}, 250); // need to check position fixed ask block
	}
	else{
		setTimeout(function(){
			$(window).resize(); // need to check resize flexslider & menu
			setTimeout(function(){
				CheckTopMenuPadding();
				CheckTopMenuOncePadding();
				CheckTopMenuDotted();
				CheckHeaderFixed();

				setTimeout(function(){
					$(window).scroll();
				}, 50);
			}, 50);
		}, 350);
	}

	if(arNextOptions['THEME']['USE_DEBUG_GOALS'] === 'Y')
		$.cookie('_ym_debug', 1, {path: '/',});
	else
		$.cookie('_ym_debug', null, {path: '/',});

    /*  --- Bind mobile menu  --- */
	var $mobileMenu = $("#mobilemenu, #mobileheadersimple");
	$mobileMenu.isOpen = false;
	if($mobileMenu.length){
		$mobileMenu.isOpen = $mobileMenu.hasClass('show')
		$mobileMenu.isLeftSide = $mobileMenu.hasClass('leftside')
		$mobileMenu.isDowndrop = $mobileMenu.find('>.scroller').hasClass('downdrop')

		$(document).on('click', '#mobileheader .burger', function(){
			SwipeMobileMenu()
		})

		if($mobileMenu.isLeftSide){
			$mobileMenu.parent().append('<div id="mobilemenu-overlay"></div>')
			var $mobileMenuOverlay = $('#mobilemenu-overlay')

			$mobileMenuOverlay.click(function(){
				if($mobileMenu.isOpen){
					CloseMobileMenu()
				}
			});

			$(document).swiperight(function(e) {
				if(!$(e.target).closest('.flexslider').length && !$(e.target).closest('.swipeignore').length && !$(e.target).closest('ymaps').length){
					OpenMobileMenu()
				}
			});

			$(document).swipeleft(function(e) {
				if(!$(e.target).closest('.flexslider').length && !$(e.target).closest('.swipeignore').length && !$(e.target).closest('ymaps').length){
					CloseMobileMenu()
				}
			});
		}
		else{
			$(document).on('click', '#mobileheader', function(e){
				if(!$(e.target).closest('#mobilemenu').length && !$(e.target).closest('.burger').length && $mobileMenu.isOpen){
					CloseMobileMenu()
				}
			});
		}

		$(document).on('click', '#mobilemenu .menu a,#mobilemenu .social-icons a', function(e){
			var $this = $(this)
			if($this.hasClass('parent')){
				e.preventDefault()

				if(!$mobileMenu.isDowndrop){
					$this.closest('li').addClass('expanded')
					MoveMobileMenuWrapNext()
				}
				else{
					if(!$this.closest('li').hasClass('expanded')){
						$this.closest('li').addClass('expanded')
					}
					else{
						$this.closest('li').removeClass('expanded')
					}
				}
			}
			else{
				if(!$this.hasClass('city_item')){
					var href = $this.attr('href')
					if(typeof href !== 'undefined' && href.length){
						e.preventDefault()
						window.location.href = href
						//window.location.reload()
					}
				}

				if(!$this.closest('.menu_back').length){
					CloseMobileMenu()
				}
			}
		})

		$(document).on('click', '#mobilemenu .dropdown .menu_back', function(e){
			e.preventDefault()
			var $this = $(this)
			MoveMobileMenuWrapPrev()
			setTimeout(function(){
				$this.closest('.expanded').removeClass('expanded')
			}, 400)
		})

		OpenMobileMenu = function(){
			CloseMobilePhone();

			if(!$mobileMenu.isOpen){
				// hide styleswitcher
				if($('.style-switcher').hasClass('active')){
					$('.style-switcher .switch').trigger('click')
				}
				$('.style-switcher .switch').hide()

				if($mobileMenu.isLeftSide){
					// show overlay
					setTimeout(function(){
						$mobileMenuOverlay.fadeIn('fast')
					}, 100)

					// fix body
					$('body').css({'overflow-y':'hidden'});
				}
				else{
					// scroll body to top & set fixed
					$('body').scrollTop(0).css({position: 'fixed'})

					// set menu top = bottom of header
					$mobileMenu.css({top: + ($('#mobileheader').height() + $('#mobileheader').offset().top) + 'px'})

					// change burger icon
					$('#mobileheader .burger').addClass('c')
				}

				// show menu
				$mobileMenu.addClass('show')
				$mobileMenu.isOpen = true;

				if(!$mobileMenu.isDowndrop){
					var $wrap = $mobileMenu.find('.wrap').first()
					var params =  $wrap.data('params')
					if(typeof params === 'undefined'){
						params = {
							depth: 0,
							scroll: {},
							height: {}
						}
					}
					$wrap.data('params', params)
				}
			}
		}

		CloseMobileMenu = function(){
			if($mobileMenu.isOpen){
				// hide menu
				$mobileMenu.removeClass('show')
				$mobileMenu.isOpen = false

				// show styleswitcher
				$('.style-switcher .switch').show()

				if($mobileMenu.isLeftSide){
					// unfix body
                    $('body').css({'overflow-y':'auto'});

					// hide overlay
					setTimeout(function(){
						$mobileMenuOverlay.fadeOut('fast')
					}, 100)
				}
				else{
					// change burger icon
					$('#mobileheader .burger').removeClass('c')

					// body unset fixed
					$('body').css({position: ''})
				}

				if(!$mobileMenu.isDowndrop){
					setTimeout(function(){
						var $scroller = $mobileMenu.find('.scroller').first()
						var $wrap = $mobileMenu.find('.wrap').first()
						var params =  $wrap.data('params')
						params.depth = 0
						$wrap.data('params', params).attr('style', '')
						$mobileMenu.scrollTop(0)
						$scroller.css('height', '')
					}, 400)
				}
			}
		}

		SwipeMobileMenu = function(){
			if($mobileMenu.isOpen){
				CloseMobileMenu()
			}
			else{
				OpenMobileMenu()
			}
		}

		MoveMobileMenuWrapNext = function(){
			if(!$mobileMenu.isDowndrop){
				var $scroller = $mobileMenu.find('.scroller').first()
				var $wrap = $mobileMenu.find('.wrap').first()
				if($wrap.length){
					var params =  $wrap.data('params')
					var $dropdownNext = $mobileMenu.find('.expanded>.dropdown').eq(params.depth)
					if($dropdownNext.length){
						// save scroll position
						params.scroll[params.depth] = parseInt($mobileMenu.scrollTop())

						// height while move animating
						params.height[params.depth + 1] = Math.max($dropdownNext.height(), (!params.depth ? $wrap.height() : $mobileMenu.find('.expanded>.dropdown').eq(params.depth - 1).height()))
						$scroller.css('height', params.height[params.depth + 1] + 'px')

						// inc depth
						++params.depth

						// translateX for move
						$wrap.css('transform', 'translateX(' + -100 * params.depth + '%)')

						// scroll to top
						setTimeout(function() {
							$mobileMenu.animate({scrollTop : 0}, 200);
						}, 100)

						// height on enimating end
						var h = $dropdownNext.height()
						setTimeout(function() {
							if(h){
								$scroller.css('height', h + 'px')
							}
							else{
								$scroller.css('height', '')
							}
						}, 200)
					}

					$wrap.data('params', params)
				}
			}
		}

		MoveMobileMenuWrapPrev = function(){
			if(!$mobileMenu.isDowndrop){
				var $scroller = $mobileMenu.find('.scroller').first()
				var $wrap = $mobileMenu.find('.wrap').first()
				if($wrap.length){
					var params =  $wrap.data('params')
					if(params.depth > 0){
						var $dropdown = $mobileMenu.find('.expanded>.dropdown').eq(params.depth - 1)
						if($dropdown.length){
							// height while move animating
							$scroller.css('height', params.height[params.depth] + 'px')

							// dec depth
							--params.depth

							// translateX for move
							$wrap.css('transform', 'translateX(' + -100 * params.depth + '%)')

							// restore scroll position
							setTimeout(function() {
								$mobileMenu.animate({scrollTop : params.scroll[params.depth]}, 200);
							}, 100)

							// height on enimating end
							var h = (!params.depth ? false : $mobileMenu.find('.expanded>.dropdown').eq(params.depth - 1).height())
							setTimeout(function() {
								if(h){
									$scroller.css('height', h + 'px')
								}
								else{
									$scroller.css('height', '')
								}
							}, 200)
						}
					}

					$wrap.data('params', params)
				}
			}
		}
	}
	/*  --- END Bind mobile menu  --- */

    /*  --- Bind mobile phone  --- */
    var $mobileHeader = $("#mobileheader, #mobileheadersimple");
    var $mobilePhone = $("#mobilePhone");
    $mobilePhone.isOpen = false;
    if($mobilePhone.length){
        $mobilePhone.isOpen = $mobilePhone.hasClass('show');

        $(document).on('click', '.wrap_phones .svg-inline-phone', function(e){
            SwipeMobilePhone();
            e.stopPropagation();
        });

        $(document).on('click', '.wrap_phones .svg-inline-close', function(e){
            CloseMobilePhone();
            e.stopPropagation();
        });
    }

    SwipeMobilePhone = function(){
        if($mobilePhone.isOpen){
            CloseMobilePhone();
        }
        else{
            OpenMobilePhone();
        }
    }

    OpenMobilePhone = function(){
        if(!$mobilePhone.isOpen){
            CloseMobileMenu();

            // show overlay
            $('<div class="jqmOverlay mobp" style="top:'+($mobileHeader.position().top+$mobileHeader.height())+'px;"></div>').appendTo('body');
            // toggle phones
            setTimeout(function(){
                $mobilePhone.slideDown('fast', function(){
                    $mobilePhone.addClass('show');
                    $mobilePhone.isOpen = true;
                });
            }, 100);
        }
    }

    CloseMobilePhone = function(){
        if($mobilePhone.isOpen){
        	// toggle phones
            setTimeout(function(){
                $mobilePhone.slideUp('fast', function(){
                    $mobilePhone.removeClass('show');
                    $mobilePhone.isOpen = false;

		        	// hide overlay
		            $('.jqmOverlay.mobp').remove();
                });
            }, 100);
        }
    }

    checkMobilePhone = function(){
        if((!window.matchMedia('(max-width: 991px)').matches)){
            CloseMobilePhone();
		}
    }
    /*  --- END Bind mobile phone  --- */

    /*  --- Bind mobile filter  --- */
    var $mobilefilter = $("#mobilefilter");
    if($mobilefilter.length){
        $mobilefilter.isOpen = $mobileMenu.hasClass('show');
        $mobilefilter.isAppendLeft = false;
        $mobilefilter.isWrapFilter = false;
        $mobilefilter.isHorizontalOrCompact = $('.filter_horizontal').length || $('.bx_filter_vertical.compact').length;
        $mobilefilter.close = '<i class="svg svg-close close-icons"></i>';

        $(document).on('click', '.filter_opener', function(){
            OpenMobileFilter();
        });

        $(document).on('click', '#mobilefilter .svg-close.close-icons', function(){
            CloseMobileFilter();
        });

        $(document).on('click', '.bx_filter_select_block', function(e){
            var bx_filter_select_container = $(e.target).parents('.bx_filter_select_container');
            if(bx_filter_select_container.length) {
                var prop_id = bx_filter_select_container.closest('.bx_filter_parameters_box').attr('data-property_id');
                if($('#smartFilterDropDown'+prop_id).length){
					$('#smartFilterDropDown'+prop_id).css({"max-width":bx_filter_select_container.width(), "z-index":"3020"});
                }
            }
        });

        $(document).on('mouseup', '.bx_filter_section', function(e){
            if($(e.target).hasClass('bx_filter_search_button')) {
                CloseMobileFilter();
            }
        });


        $(document).on('mouseup', '.bx_filter_parameters_box_title', function(e){
            $("[id^='smartFilterDropDown']").hide();
            if($(e.target).hasClass('close-icons')) {
                CloseMobileFilter();
            }
        });

        /*$(document).on('DOMSubtreeModified', "#mobilefilter #modef_num_mobile", function() {
            mobileFilterNum($(this));
        });

        $(document).on('DOMSubtreeModified', "#mobilefilter .bx_filter_container_modef", function() {
            mobileFilterNum($(this));
        });*/

        $mobilefilter.parent().append('<div id="mobilefilter-overlay"></div>');
        var $mobilefilterOverlay = $('#mobilefilter-overlay');

        $mobilefilterOverlay.click(function(){
            if($mobilefilter.isOpen){
                CloseMobileFilter();
                //e.stopPropagation();
            }
        });

        mobileFilterNum = function(num, def){
            if(def){
                $('.bx_filter_search_button').val(num.data("f"));;
            }
            else {
                var str = "";
                var $prosLeng = $('.bx_filter_parameters_box > span');

                str += $prosLeng.data("f") + " " + num + " " + declOfNumFilter(num, [$prosLeng.data("fi"), $prosLeng.data("fr"), $prosLeng.data("frm"),]);
                $('.bx_filter_search_button').val(str);
            }
        }

        declOfNumFilter = function(number, titles){
            cases = [2, 0, 1, 1, 1, 2];
            return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
        }

        OpenMobileFilter = function(){
            if(!$mobilefilter.isOpen){
                if(!$mobilefilter.isAppendLeft){
                    if(!$mobilefilter.isWrapFilter){
                        $(".bx_filter").wrap("<div id='wrapInlineFilter'></div>");
                        $mobilefilter.isWrapFilter = true;
                    }
                    $(".bx_filter").appendTo($("#mobilefilter")).find('.title .bx_filter_parameters_box_title').append($mobilefilter.close);
                    $mobilefilter.isAppendLeft = true;
                }

                // show overlay
                setTimeout(function(){
					$mobilefilterOverlay.fadeIn('fast');
                }, 100);

                // fix body
                $('body').css({'overflow-y':'hidden'});

                // show mobile filter
                $mobilefilter.addClass('show');
                $mobilefilter.find('.bx_filter').css({'display':'block'});
                $mobilefilter.isOpen = true;

                var init =  $mobilefilter.data('init');
                if(typeof init === 'undefined'){
                    $mobilefilter.scroll(function() {
                        $(".bx_filter_section .bx_filter_select_container").each(function(){
                            var prop_id=$(this).closest('.bx_filter_parameters_box').attr('data-property_id');
                            if($('#smartFilterDropDown'+prop_id).length){
                                $('#smartFilterDropDown'+prop_id).hide();
                            }
                        });
                    });

                    $mobilefilter.data('init', "Y");
                };
            };
        }

        CloseMobileFilter = function(append){
            if($mobilefilter.isOpen){
            	// scroll to top
                $mobilefilter.find('.bx_filter_parameters').scrollTop(0);

                // unfix body
                $('body').css({'overflow-y':'auto'});

                // hide overlay
                setTimeout(function(){
					$mobilefilterOverlay.fadeOut('fast');
                }, 100);

                // hide mobile filter
                $mobilefilter.removeClass('show');
                $mobilefilter.isOpen = false;
            }

            if(append && $mobilefilter.isAppendLeft){
                $(".bx_filter").appendTo($("#wrapInlineFilter")).show().find('.svg-close').remove();
                $mobilefilter.isAppendLeft = false;
                $mobilefilter.removeData('init');
                mobileFilterNum($("#modef_num_mobile"),true);
	        }
        }

        checkMobileFilter = function(){
            if((!window.matchMedia('(max-width: 991px)').matches && !$mobilefilter.isHorizontalOrCompact) || (!window.matchMedia('(max-width: 767px)').matches && $mobilefilter.isHorizontalOrCompact)){
                CloseMobileFilter(true);
			}
        }
    }
    else {
        checkTopFilter();
        $(document).on('click', '.filter_opener', function(){
			$(this).toggleClass("opened");
			if($('.visible_mobile_filter').length) {
				$('.visible_mobile_filter').show();
				$('.bx_filter_vertical, .bx_filter').slideToggle(333);
			}
			else{
				$('.bx_filter_vertical').closest('div[id^=bx_incl]').show();
			    $('.bx_filter_vertical, .bx_filter').slideToggle(333);
			}
        });
    }
    /*  --- END Bind mobile filter  --- */

	/* change type2 menu for fixed */
	if($('#headerfixed .js-nav').length)
	{
		if(arNextOptions['THEME']['HEADER_FIXED'] == 2)
			CheckHeaderFixedMenu();

		setTimeout(function(){
			$('#headerfixed .js-nav').addClass('opacity1');
		},350);
	}

	// -- scroll after apply option
	if($('.instagram_ajax').length)
	{
		BX.addCustomEvent('onCompleteAction', function(eventdata){
			if(eventdata.action === 'instagrammLoaded')
				scrollPreviewBlock();
		});
	}
	else
		scrollPreviewBlock();

	scrollToTop();

	$.extend( $.validator.messages, {
		required: BX.message('JS_REQUIRED'),
		email: BX.message('JS_FORMAT'),
		equalTo: BX.message('JS_PASSWORD_COPY'),
		minlength: BX.message('JS_PASSWORD_LENGTH'),
		remote: BX.message('JS_ERROR')
	});

	$.validator.addMethod(
		'regexp', function( value, element, regexp ){
			var re = new RegExp( regexp );
			return this.optional( element ) || re.test( value );
		},
		BX.message('JS_FORMAT')
	);

	$.validator.addMethod(
		'filesize', function( value, element, param ){
			return this.optional( element ) || ( element.files[0].size <= param )
		},
		BX.message('JS_FILE_SIZE')
	);

	$.validator.addMethod(
		'date', function( value, element, param ) {
			var status = false;
			if(!value || value.length <= 0){
				status = false;
			}
			else{
				// html5 date allways yyyy-mm-dd
				var re = new RegExp('^([0-9]{4})(.)([0-9]{2})(.)([0-9]{2})$');
				var matches = re.exec(value);
				if(matches){
					var composedDate = new Date(matches[1], (matches[3] - 1), matches[5]);
					status = ((composedDate.getMonth() == (matches[3] - 1)) && (composedDate.getDate() == matches[5]) && (composedDate.getFullYear() == matches[1]));
				}
				else{
					// firefox
					var re = new RegExp('^([0-9]{2})(.)([0-9]{2})(.)([0-9]{4})$');
					var matches = re.exec(value);
					if(matches){
						var composedDate = new Date(matches[5], (matches[3] - 1), matches[1]);
						status = ((composedDate.getMonth() == (matches[3] - 1)) && (composedDate.getDate() == matches[1]) && (composedDate.getFullYear() == matches[5]));
					}
				}
			}
			return status;
		}, BX.message('JS_DATE')
	);

	$.validator.addMethod(
		'extension', function(value, element, param){
			param = typeof param === 'string' ? param.replace(/,/g, '|') : 'png|jpe?g|gif';
			return this.optional(element) || value.match(new RegExp('.(' + param + ')$', 'i'));
		}, BX.message('JS_FILE_EXT')
	);

	$.validator.addMethod(
		'captcha', function( value, element, params ){
			return $.validator.methods.remote.call(this, value, element,{
				url: arNextOptions['SITE_DIR'] + 'ajax/check-captcha.php',
				type: 'post',
				data:{
					captcha_word: value,
					captcha_sid: function(){
						return $(element).closest('form').find('input[name="captcha_sid"]').val();
					}
				}
			});
		},
		BX.message('JS_ERROR')
	);

	$.validator.addMethod(
		'recaptcha', function(value, element, param){
			var id = $(element).closest('form').find('.g-recaptcha').attr('data-widgetid');
			if(typeof id !== 'undefined'){
				return grecaptcha.getResponse(id) != '';
			}
			else{
				return true;
			}
		}, BX.message('JS_RECAPTCHA_ERROR')
	);

	$.validator.addClassRules({
		'phone':{
			regexp: arNextOptions['THEME']['VALIDATE_PHONE_MASK']
		},
		'confirm_password':{
			equalTo: 'input[name="REGISTER\[PASSWORD\]"]',
			minlength: 6
		},
		'password':{
			minlength: 6
		},
		'inputfile':{
			extension: arNextOptions['THEME']['VALIDATE_FILE_EXT'],
			filesize: 5000000
		},
		'captcha':{
			captcha: ''
		},
		'recaptcha':{
			recaptcha: ''
		}
	});

	if(arNextOptions['THEME']['PHONE_MASK']){
		$('input.phone').inputmask('mask', {'mask': arNextOptions['THEME']['PHONE_MASK']});
	}

	// init calculate delivery with preview
	initCalculatePreview();

	/*city*/
	$('select.region').on('change', function(){
		var val = parseInt($(this).val());
		if($('select.city').length)
		{
			if(val)
			{
				$('select.city option').hide();
				$('select.city option').prop('disabled', 'disabled');
				$('select.city option[data-parent_section='+val+']').prop('disabled', '');
				$('select.city option:eq(0)').prop('disabled', '');
				$('select.city').ikSelect('reset');
				$('select.city option[data-parent_section='+val+']').show();
			}
			else
				$('select.city option').prop('disabled', 'disabled');
				$('select.city option:eq(0)').prop('disabled', '');
				$('select.city').ikSelect('reset');
		}
	})

	$('select.city, select.region').on('change', function(){
		var _this = $(this),
			val = parseInt(_this.val());
		if(_this.hasClass('region'))
		{
			$('select.city option:eq(0)').show();
			$('select.city').val(0);
		}

		if((_this.hasClass('region') && !val) || _this.hasClass('city'))
		{
			$.ajax({
				type: 'POST',
				data: {ID: val},
			}).success(function(html){
				var ob = BX.processHTML(html);
				$('.ajax_items')[0].innerHTML = ob.HTML;
				BX.ajax.processScripts(ob.SCRIPT);
			})
		}
	})

	$(document).on('click', '.mobile_regions .city_item', function(e){
    	e.preventDefault();
    	var _this = $(this);
    	$.removeCookie('current_region');
		$.cookie('current_region', _this.data('id'), {path: '/',domain: arNextOptions['SITE_ADDRESS']});
		location.href = _this.attr('href');
    })

	/* toggle */
	var $this = this,
		previewParClosedHeight = 25;

	$('section.toggle > label').prepend($('<i />').addClass('fa fa-plus'));
	$('section.toggle > label').prepend($('<i />').addClass('fa fa-minus'));
	$('section.toggle.active > p').addClass('preview-active');
	$('section.toggle.active > div.toggle-content').slideDown(350, function() {});

	$('section.toggle > label').click(function(e){
		var parentSection = $(this).parent(),
			parentWrapper = $(this).parents('div.toogle'),
			previewPar = false,
			isAccordion = parentWrapper.hasClass('toogle-accordion');

		if(isAccordion && typeof(e.originalEvent) != 'undefined') {
			parentWrapper.find('section.toggle.active > label').trigger('click');
		}

		parentSection.toggleClass('active');

		// Preview Paragraph
		if( parentSection.find('> p').get(0) ){
			previewPar = parentSection.find('> p');
			var previewParCurrentHeight = previewPar.css('height');
			previewPar.css('height', 'auto');
			var previewParAnimateHeight = previewPar.css('height');
			previewPar.css('height', previewParCurrentHeight);
		}

		// Content
		var toggleContent = parentSection.find('> div.toggle-content');

		if( parentSection.hasClass('active') ){
			$(previewPar).animate({
				height: previewParAnimateHeight
			}, 350, function() {
				$(this).addClass('preview-active');
			});
			toggleContent.slideDown(350, function() {});
		}
		else{
			$(previewPar).animate({
				height: previewParClosedHeight
			}, 350, function() {
				$(this).removeClass('preview-active');
			});
			toggleContent.slideUp(350, function() {});
		}
	});

	$('.tables-responsive .responsive').footable(); //responsive table

	$('a[rel=tooltip]').tooltip();
	$('span[data-toggle=tooltip]').tooltip();

	$('.toggle .more_items').on('click', function(){
		$(this).closest('.toggle').find('.collapsed').fadeToggle();
		$(this).remove();
		if(typeof $(this).data('resize') !== 'undefined' && $(this).data('resize'))
			$(window).resize();
	})
	$('.toggle_menu .more_items').on('click', function(){
		$(this).closest('.toggle_menu').find('.collapsed').addClass('clicked_exp');
		$(this).remove();
	})

	/* search sync */
	$(document).on('keyup', '.search-input-div input', function(e){
		var inputValue = $(this).val();
		$('.search-input-div input:not(:focus').val(inputValue);

		if($(this).closest('#headerfixed').length)
		{
			if(e.keyCode == 13)
				$('.search form').submit();
		}
	});

    $(document).on('keyup', function(e){
        if (e.keyCode == 27){
            if($('.jqmWindow').length){
                $('.jqmWindow').jqmHide();
            }
            else if($('.inline-search-block.fixed').hasClass('show')){
                $('.inline-search-block .close-block').click();
                setTimeout(function(){
                    $('.title-search-result').hide();
                }, 0);
            }
            else if($('.mega_fixed_menu').is(':visible')){
                $('.mega_fixed_menu .svg-inline-close').click();
            }
        }
    });

	$(document).on('click', '.search-button-div button', function(e){
		if($(this).closest('#headerfixed').length)
			$('.search form').submit();
	});

	$('.inline-search-show, .inline-search-hide').on('click', function(e){
		CloseMobilePhone();

		if(typeof($(this).data('type_search')) != 'undefined' && $(this).data('type_search') == 'fixed'){
			$('.inline-search-block').addClass('fixed');
		}

		if(arNextOptions['THEME']['TYPE_SEARCH'] == 'fixed'){
			$('.inline-search-block.fixed.big .search-input').focus();
		}

	    if(arNextOptions['THEME']['TYPE_SEARCH'] != 'fixed'){
	        var height_block = 0;
	        /*if(!$('header > .top-block').length || $('header.long').length)
	        {*/
	            if($('header.long').length){
	                height_block = $('header.long').closest('#header').actual('outerHeight');
	            }
	            else{
	                height_block = $(this).closest('.maxwidth-theme').actual('outerHeight');
	                if($(this).closest('.top-block').length){
	                    height_block = $(this).closest('.top-block').actual('outerHeight');
	                }
	                else if($(this).closest('header.header-v8').length){
	                    height_block = $(this).closest('header.header-v8').actual('outerHeight');
	                }
	            }

	            if($('#bx-panel').length){
	                height_block += $('#bx-panel').actual('outerHeight');
	            }

	            $('.inline-search-block').css({
	                'height': height_block,
	                'line-height': height_block-4+'px',
	                'top': -height_block
	            })
	        //}
	    }

	    $('.inline-search-block').toggleClass('show');

	    if($('.top-block').length){
	        if($('.inline-search-block').hasClass('show')){
	            $('.inline-search-block').css('background', $('.top-block').css('background-color'));
	        }
	        else{
	            $('.inline-search-block').css('background', '#fff');
	        }
	    }

	    if(arNextOptions['THEME']['TYPE_SEARCH'] === 'fixed'){
	        if($('.inline-search-block').hasClass('show')){
	            $('<div class="jqmOverlay search"></div>').appendTo('body');
	        }
	        else{
	            $('.jqmOverlay').remove();
	        }
	    }
	})

	/* close search block */
	$("html, body").on('mousedown', function(e){
		if(typeof e.target.className == 'string' && e.target.className.indexOf('adm') < 0){
			e.stopPropagation();
			var search_target = $(e.target).closest('.bx_searche');
			if(!$(e.target).hasClass('inline-search-block') && !$(e.target).hasClass('svg') && !search_target.length){
				$('.inline-search-block').removeClass('show');
				$('.title-search-result').hide();
				if(arNextOptions['THEME']['TYPE_SEARCH'] === 'fixed'){
					$('.jqmOverlay.search').remove();
				}
			}

            if($mobilePhone.length){
                CloseMobilePhone();
            }

			if($('#basket_line .basket_fly').length && parseInt($("#basket_line .basket_fly").css("right")) >= 0){
				if(!$(e.target).closest('.basket_wrapp').length){
					$("#basket_line .basket_fly").stop().animate({"right": -$("#basket_line .basket_fly").outerWidth()}, 150);
				}
			}

			if(isMobile){
				if(search_target.length){
					location.href = search_target.attr('href');
				}
			}

			var class_name = $(e.target).attr('class');
			if(typeof(class_name) == 'undefined' || class_name.indexOf('tooltip') < 0){
				//tooltip link
				$('.tooltip-link').tooltip('hide');
			}
		}
	});
	$('.inline-search-block').find('*').on('mousedown', function(e){
		e.stopPropagation();
	});

	/*check mobile device*/
	if(jQuery.browser.mobile){
		$(document).on('click', '*[data-event="jqm"]', function(e){
			e.preventDefault();
			e.stopPropagation();
			var _this = $(this);
			var name = _this.data('name');

			if(window.matchMedia('(min-width:992px)').matches)
			{
				if(!$(this).hasClass('clicked'))
				{
					$(this).addClass('clicked');
					$(this).jqmEx();
					$(this).trigger('click');
				}
				return false;
			}
			else if(name.length){
				var script = arNextOptions['SITE_DIR'] + 'form/';
				var paramsStr = ''; var arTriggerAttrs = {};
				$.each(_this.get(0).attributes, function(index, attr){
					var attrName = attr.nodeName;
					var attrValue = _this.attr(attrName);
					arTriggerAttrs[attrName] = attrValue;
					if(/^data\-param\-(.+)$/.test(attrName)){
						var key = attrName.match(/^data\-param\-(.+)$/)[1];
						paramsStr += key + '=' + attrValue + '&';
					}
				});

				var triggerAttrs = JSON.stringify(arTriggerAttrs);
				var encTriggerAttrs = encodeURIComponent(triggerAttrs);
				script += '?name=' + name + '&' + paramsStr + 'data-trigger=' + encTriggerAttrs;

				location.href = script;
			}
		});

		$('.fancybox').removeClass('fancybox');
	}
	else
	{
		$(document).on('click', '*[data-event="jqm"]', function(e){
			e.preventDefault();
			e.stopPropagation();
			if(!$(this).hasClass('clicked'))
			{
				$(this).addClass('clicked');
				$(this).jqmEx();
				$(this).trigger('click');
			}
			return false;
		});
	}

	$('.animate-load').on('click', function(){
		if(!jQuery.browser.mobile)
			$(this).parent().addClass('loadings');
	})

	$(document).on('click', 'html.bx-touch .breadcrumbs .bx-breadcrumb-item.drop>a', function(e){
		e.preventDefault();
	});

	BX.addCustomEvent('onCompleteAction', function(eventdata, _this){
		try{
			if(eventdata.action === 'loadForm')
			{
				$(_this).parent().removeClass('loadings');
				$(_this).removeClass('clicked');

				if($(_this).hasClass('one_click_buy_trigger'))
					$('.one_click').removeClass('clicked')
				else if($(_this).hasClass('one_click_buy_basket_trigger'))
					$('.fast_order').removeClass('clicked')
			}
			else if(eventdata.action === 'loadBasket')
			{
				$('.basket-link.basket').attr('title', arBasketPrices.BASKET_SUMM_TITLE);
				$('.basket-link.delay').attr('title', arBasketPrices.DELAY_SUMM_TITLE);

				if(arBasketPrices.BASKET_COUNT>0)
				{
					$('.basket-link.basket').addClass('basket-count');
					$('.basket-link.basket .count').removeClass('empted');
					if($('.basket-link.basket .prices').length)
						$('.basket-link.basket .prices').html(arBasketPrices.BASKET_SUMM);
				}
				else
				{
					$('.basket-link.basket').removeClass('basket-count');
					$('.basket-link.basket .count').addClass('empted');
					if($('.basket-link.basket .prices').length)
						$('.basket-link.basket .prices').html(arBasketPrices.BASKET_SUMM_TITLE_SMALL);
				}
				$('.basket-link.basket .count').text(arBasketPrices.BASKET_COUNT);
				if(arBasketPrices.DELAY_COUNT>0)
				{
					$('.basket-link.delay').addClass('basket-count');
					$('.basket-link.delay .count').removeClass('empted');
				}
				else
				{
					$('.basket-link.delay').removeClass('basket-count');
					$('.basket-link.delay .count').addClass('empted');
				}
				$('.basket-link.delay .count').text(arBasketPrices.DELAY_COUNT);
			}
			else if(eventdata.action === 'loadActualBasketCompare')
			{
				var compare_count = Object.keys(arBasketAspro.COMPARE).length;
				if(compare_count>0)
				{
					$('.basket-link.compare').addClass('basket-count');
					$('.basket-link.compare .count').removeClass('empted');
					if($('#compare_fly').length)
						$('#compare_fly').removeClass('empty_block');
				}
				else
				{
					$('.basket-link.compare').removeClass('basket-count');
					$('.basket-link.compare .count').addClass('empted');
					if($('#compare_fly').length)
						$('#compare_fly').addClass('empty_block');
				}
				$('.basket-link.compare .count').text(compare_count);
			}
			else if(eventdata.action === 'instagrammLoaded')
			{
				$('.instagram .scroll-title').mCustomScrollbar({
					mouseWheel: {
						scrollAmount: 150,
						preventDefault: true,
					}
				});

			}
			else if(eventdata.action === 'loadRSS')
			{
			}
		}
		catch(e){
			console.error(e)
		}
	})

	/*slices*/
	if($('.banners-small .item.normal-block').length)
		$('.banners-small .item.normal-block').sliceHeight();
	if($('.teasers .item').length)
		$('.teasers .item').sliceHeight();
	if($('.wrap-portfolio-front .row.items > div').length)
		$('.wrap-portfolio-front .row.items > div').sliceHeight({'row': '.row.items', 'item': '.item1'});

	/* bug fix in ff*/
	$('img').removeAttr('draggable');

	clicked_tab = 0;

	$('.title-tab-heading').on('click', function(){
		var container = $(this).parent(),
			slide_block = $(this).next(),
			bReviewTab = container.hasClass('media_review');

		clicked_tab = container.index()+1;

		container.siblings().removeClass('active');

		$('.nav.nav-tabs li').removeClass('active');

		if(container.hasClass('active'))
		{
			if(bReviewTab)
			{
				$('#reviews_content').slideUp(200, function(){
					container.removeClass('active');
				});
			}
			else
			{
				slide_block.slideUp(200, function(){
					container.removeClass('active');
				});
			}
		}
		else
		{
			container.addClass('active');
			if(bReviewTab)
			{
				$('#reviews_content').slideDown();
			}
			else
			{
				if($('.tabs_section + #reviews_content').length)
					$('.tabs_section + #reviews_content').slideUp();
				slide_block.slideDown();
				if(typeof container.attr('id') !== 'undefined' && container.attr('id') == 'descr'){
					var $gallery = $('.galerys-block');
					if($gallery.length){
						var bTypeBig = $gallery.find('.big_slider').length;
						var $slider = bTypeBig ? $gallery.find('.big_slider') : $gallery.find('.small_slider');
						InitFlexSlider();
						var interval = setInterval(function(){
							if($slider.find('.slides .item').attr('style').indexOf('height') === -1){
								$(window).resize();
							}
							else{
								clearInterval(interval);
							}
						}, 100);
					}
				}
			}
		}
	})

	InitFlexSlider();

	setTimeout(function(){
		InitTopestMenuGummi();
		isOnceInited = true;
	},50);

	InitZoomPict();

	// $('body').on( 'click', '.captcha_reload', function(e){
	$(document).on('click', '.captcha_reload', function(e){
		var captcha = $(this).parents('.captcha-row');
		e.preventDefault();
		$.ajax({
			url: arNextOptions['SITE_DIR'] + 'ajax/captcha.php'
		}).done(function(text){
			captcha.find('input[name=captcha_sid]').val(text);
			captcha.find('img').attr('src', '/bitrix/tools/captcha.php?captcha_sid=' + text);
			captcha.find('input[name=captcha_word]').val('').removeClass('error');
			captcha.find('.captcha_input').removeClass('error').find('.error').remove();
		});
	});

	/* show print */
	if(arNextOptions["PAGES"]["BASKET_PAGE"])
	{
		if(arNextOptions["THEME"]["SHOW_BASKET_PRINT"]=="Y")
		{
			if($('.page-top h1').length)
				$('<div class="print-link" title="'+arNextOptions["THEME"]["EXPRESSION_FOR_PRINT_PAGE"]+'"><i class="svg svg-print"></i></div>').insertBefore($('.page-top h1'));
		}
	}
	else
	{
		if(arNextOptions['THEME']['PRINT_BUTTON'] == 'Y')
		{
			setTimeout(function(){
				if($('.page-top .rss-block.top').length)
				{
					$('<div class="print-link" title="'+arNextOptions["THEME"]["EXPRESSION_FOR_PRINT_PAGE"]+'"><i class="svg svg-print"></i></div>').insertBefore($('.page-top .rss-block.top .share_wrapp'));
				}
				else if($('.page-top .rss').length)
				{
					$('<div class="print-link" title="'+arNextOptions["THEME"]["EXPRESSION_FOR_PRINT_PAGE"]+'"><i class="svg svg-print"></i></div>').insertAfter($('.page-top .rss'));
				}
				else if($('.page-top h1').length)
					$('<div class="print-link" title="'+arNextOptions["THEME"]["EXPRESSION_FOR_PRINT_PAGE"]+'"><i class="svg svg-print"></i></div>').insertBefore($('.page-top h1'));
				// else
					// $('footer .print-block').html('<div class="print-link"><i class="svg svg-print"><svg id="Print.svg" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path class="cls-1" d="M1553,287h-2v3h-8v-3h-2a2,2,0,0,1-2-2v-5a2,2,0,0,1,2-2h2v-4h8v4h2a2,2,0,0,1,2,2v5A2,2,0,0,1,1553,287Zm-8,1h4v-4h-4v4Zm4-12h-4v2h4v-2Zm4,4h-12v5h2v-3h8v3h2v-5Z" transform="translate(-1539 -274)"/></svg></i></div>');
			},150);
		}
	}

	$(document).on('click', '.print-link', function(){
		window.print();
	})

	$('.head-block .item-link').on('click', function(){
		var _this = $(this);
		_this.siblings().removeClass('active');
		_this.addClass('active');
	})

	$('table.table').each(function(){
		var _this = $(this),
			first_td = _this.find('thead tr th');
		if(!first_td.length)
			first_td = _this.find('thead tr td');
		if(first_td.length)
		{
			var j = 0;
			_this.find('tbody tr td').each(function(i){
				if(j > first_td.length-1)
					j = 0;
				$('<div class="th-mobile">'+first_td[j].textContent+'</div>').appendTo($(this));
				j++;
			})
		}
	})

	if(window.matchMedia('(min-width: 768px)').matches)
		$('.wrapper_middle_menu.wrap_menu').removeClass('mobile');


	if(window.matchMedia('(max-width: 767px)').matches)
		$('.wrapper_middle_menu.wrap_menu').addClass('mobile');

	$('.menu_top_block .v_bottom > a').on('click', function(e){
		if($(e.target).hasClass('toggle_block'))
			e.preventDefault();
	});

	$('.menu_top_block .v_bottom > a .toggle_block').on('click', function(e){
		$(this).closest('.v_bottom').toggleClass('opened');
		$(this).closest('.v_bottom').find('>.dropdown').slideToggle()
	});

	$(document).on('click', '.show_props', function(){
		$(this).prev('.props_list_wrapp').stop().slideToggle(333);
		$(this).find('.char_title').toggleClass('opened');
	});

	$('.see_more').on('click', function(e) {
		e.preventDefault();
		var see_more = ($(this).is('.see_more') ? $(this) : $(this).parents('.see_more').first());
		var see_moreText = (see_more.find('> a').length ? see_more.find('> a') : see_more);
		var see_moreHiden = see_more.parent().find('> .d');
		if(see_more.hasClass('open')){
			see_moreText.text(BX.message('CATALOG_VIEW_MORE'));
			see_more.removeClass('open');
			see_moreHiden.hide();
		}
		else{
			see_moreText.text(BX.message('CATALOG_VIEW_LESS'));
			see_more.addClass('open');
			see_moreHiden.show();
		}
		return false;
	});

	$('.button.faq_button').click(function(e){
		e.preventDefault();
		$(this).toggleClass('opened');
		$('.faq_ask .form').slideToggle();
	});

	$('.staff.list .staff_section .staff_section_title a').click(function(e) {
		e.preventDefault();
		$(this).parents('.staff_section').toggleClass('opened');
		$(this).parents('.staff_section').find('.staff_section_items').stop().slideToggle(600);
		$(this).parents('.staff_section_title').find('.opener_icon').toggleClass('opened');
	});

	$('.jobs_wrapp .item .name').click(function(e) {
		$(this).closest('.item').toggleClass('opened');
		$(this).closest('.item').find('.description_wrapp').stop().slideToggle(600);
		$(this).closest('.item').find('.opener_icon').toggleClass('opened');
	});

	$('.faq.list .item .q a').on('click', function(e){
		e.preventDefault();
		$(this).parents('.item').toggleClass('opened');
		$(this).parents('.item').find('.a').stop().slideToggle();
		$(this).parents('.item').find('.q .opener_icon').toggleClass('opened');
	});

	$('.opener_icon').click(function(e) {
		e.preventDefault();
		$(this).parent().find('a').trigger('click');
	});

	$('.dotdot').dotdotdot();

	$('.more_block span').on('click', function() {
		var content_offset=$('.catalog_detail .tabs_section').offset();
		$('html, body').animate({scrollTop: content_offset.top-43}, 400);
	});

	$(document).on("click", ".counter_block:not(.basket) .plus", function(){
		if(!$(this).parents('.basket_wrapp').length){
			if($(this).parent().data("offers")!="Y"){
				var isDetailSKU2 = $(this).parents('.counter_block_wr').length;
					input = $(this).parents(".counter_block").find("input[type=text]")
					tmp_ratio = !isDetailSKU2 ? $(this).parents(".counter_wrapp").find(".to-cart").data('ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('ratio'),
					isDblQuantity = !isDetailSKU2 ? $(this).parents(".counter_wrapp").find(".to-cart").data('float_ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('float_ratio'),
					ratio=( isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10)),
					max_value='';
					currentValue = input.val();


				if(isDblQuantity)
					ratio = Math.round(ratio*arNextOptions.JS_ITEM_CLICK.precisionFactor)/arNextOptions.JS_ITEM_CLICK.precisionFactor;

				curValue = (isDblQuantity ? parseFloat(currentValue) : parseInt(currentValue, 10));

				curValue += ratio;
				if (isDblQuantity){
					curValue = Math.round(curValue*arNextOptions.JS_ITEM_CLICK.precisionFactor)/arNextOptions.JS_ITEM_CLICK.precisionFactor;
				}
				if(parseFloat($(this).data('max'))>0){
					if(input.val() < $(this).data('max')){
						if(curValue<=$(this).data('max'))
							input.val(curValue);

						input.change();
					}
				}else{
					input.val(curValue);
					input.change();
				}
			}
		}
	});

	$(document).on("click", ".counter_block:not(.basket) .minus", function(){
		if(!$(this).parents('.basket_wrapp').length){
			if($(this).parent().data("offers")!="Y"){
				var isDetailSKU2 = $(this).parents('.counter_block_wr').length;
					input = $(this).parents(".counter_block").find("input[type=text]")
					tmp_ratio = !isDetailSKU2 ? $(this).parents(".counter_wrapp").find(".to-cart").data('ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('ratio'),
					isDblQuantity = !isDetailSKU2 ? $(this).parents(".counter_wrapp").find(".to-cart").data('float_ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('float_ratio'),
					ratio=( isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10)),
					max_value='';
					currentValue = input.val();

				if(isDblQuantity)
					ratio = Math.round(ratio*arNextOptions.JS_ITEM_CLICK.precisionFactor)/arNextOptions.JS_ITEM_CLICK.precisionFactor;

				curValue = (isDblQuantity ? parseFloat(currentValue) : parseInt(currentValue, 10));

				curValue -= ratio;
				if (isDblQuantity){
					curValue = Math.round(curValue*arNextOptions.JS_ITEM_CLICK.precisionFactor)/arNextOptions.JS_ITEM_CLICK.precisionFactor;
				}

				if(parseFloat($(this).parents(".counter_block").find(".plus").data('max'))>0){
					if(currentValue > ratio){
						if(curValue<ratio){
							input.val(ratio);
						}else{
							input.val(curValue);
						}
						input.change();
					}
				}else{
					if(curValue > ratio){
						input.val(curValue);
					}else{
						if(ratio){
							input.val(ratio);
						}else if(currentValue > 1){
							input.val(curValue);
						}
					}
					input.change();
				}
			}
		}
	});

	$('.counter_block input[type=text]').numeric({allow:"."});
	$('.counter_block input[type=text]').on('focus', function(){
		$(this).addClass('focus');
	});
	$('.counter_block input[type=text]').on('blur', function(){
		$(this).removeClass('focus');
	});

	var timerInitCalculateDelivery = false;
	$(document).on("change", ".counter_block input[type=text]", function(e){
		if(!$(this).parents('.basket_wrapp').length){
			var val = $(this).val(),
				tmp_ratio = $(this).parents(".counter_wrapp").find(".to-cart").data('ratio') ? $(this).parents(".counter_wrapp").find(".to-cart").data('ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('ratio'),
				isDblQuantity = $(this).parents(".counter_wrapp").find(".to-cart").data('float_ratio') ? $(this).parents(".counter_wrapp").find(".to-cart").data('float_ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('float_ratio'),
				ratio=( isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10)),
				diff = val % ratio;

			if(isDblQuantity)
			{
				ratio = Math.round(ratio*arNextOptions.JS_ITEM_CLICK.precisionFactor)/arNextOptions.JS_ITEM_CLICK.precisionFactor;
				if(Math.round(diff*arNextOptions.JS_ITEM_CLICK.precisionFactor)/arNextOptions.JS_ITEM_CLICK.precisionFactor == ratio)
					diff = 0;
			}

			if($(this).hasClass('focus'))
			{
				intCount = Math.round(
					Math.round(val * arNextOptions.JS_ITEM_CLICK.precisionFactor / ratio) / arNextOptions.JS_ITEM_CLICK.precisionFactor
				) || 1;
				val = (intCount <= 1 ? ratio : intCount * ratio);
				// val -= diff;
				val = Math.round(val*arNextOptions.JS_ITEM_CLICK.precisionFactor)/arNextOptions.JS_ITEM_CLICK.precisionFactor;
			}

			if(parseFloat($(this).parents(".counter_block").find(".plus").data('max'))>0){
				if(val>parseFloat($(this).parents(".counter_block").find(".plus").data('max'))){
					val=parseFloat($(this).parents(".counter_block").find(".plus").data('max'));
					// val -= (val % ratio);
				}
			}
			if(val<ratio){
				val=ratio;
			}else if(!parseFloat(val)){
				val=1;
			}

			$(this).parents('.counter_block').parent().parent().find('.to-cart').attr('data-quantity', val);
			$(this).parents('.counter_block').parent().parent().find('.one_click').attr('data-quantity', val);
			$(this).val(val);

			var $calculate = $(this).closest('.item').length ? $(this).closest('.item').find('.calculate-delivery') : $(this).closest('.catalog_detail').find('.calculate-delivery');
			if($calculate.length){
				$calculate.each(function(){
					var $calculateSpan = $(this).find('span[data-event=jqm]').first();

					if($calculateSpan.length){
						var $clone = $calculateSpan.clone();
						$clone.attr('data-param-quantity', val).removeClass('clicked');
						$clone.insertAfter($calculateSpan).on('click', function(){
							if(!jQuery.browser.mobile){
								$(this).parent().addClass('loadings');
							}
						});
						$calculateSpan.remove();
					}

					if($(this).hasClass('with_preview')){
						$(this).removeClass('inited');

						if(timerInitCalculateDelivery){
							clearTimeout(timerInitCalculateDelivery);
						}

						timerInitCalculateDelivery = setTimeout(function(){
							initCalculatePreview();
							timerInitCalculateDelivery = false;
						}, 1000);
					}
				});
			}

			var eventdata = {type: 'change', params: {id: $(this), value: val}};
			BX.onCustomEvent('onCounterProductAction', [eventdata]);
		}
	});

	BX.addCustomEvent('onCounterProductAction', function(eventdata) {
		if(typeof eventdata != 'object'){
			eventdata = {type: 'undefined'};
		}
		try{
			if(typeof eventdata.type != 'undefined'){
				if(!eventdata.params.id.closest('.gifts').length) // no gift
				{
					var obProduct = eventdata.params.id.data('product');

					if(eventdata.params.id.closest('.has_offer_prop').length) // type1 for offers in element list
					{
						if(typeof window['obSkuQuantys'] === 'undefined')
							window["obSkuQuantys"] = {};

						// if(typeof window['obOffers'] === 'undefined')
							window["obSkuQuantys"][eventdata.params.id.closest('.offer_buy_block').find('.to-cart').data('item')] = eventdata.params.value;
					}
					if(typeof window[obProduct] == 'object')
					{
						if(obProduct == "obOffers")
							setPriceAction('', '', 'Y');
						else
							window[obProduct].setPriceAction('Y');
					}
					else if(eventdata.params.id.length)
					{
						if(eventdata.params.id.closest('.main_item_wrapper').length && arNextOptions['THEME']['SHOW_TOTAL_SUMM'] == 'Y')
						{
							setPriceItem(eventdata.params.id.closest('.main_item_wrapper'), eventdata.params.value);
						}
					}
					BX.onCustomEvent('onCounterProductActionResize');
				}
			}
		}
		catch(e){
			console.error(e)
		}
	});

	/*slide cart*/
	$(document).on('mouseenter', '#basket_line .basket_normal:not(.empty_cart):not(.bcart) .basket_block ', function() {
		$(this).closest('.basket_normal').find('.popup').addClass('block');
		$(this).closest('.basket_normal').find('.basket_popup_wrapp').stop(true,true).slideDown(150);
	});
	$(document).on('mouseleave', '#basket_line .basket_normal .basket_block ', function() {
		var th=$(this);
		$(this).closest('.basket_normal').find('.basket_popup_wrapp').stop(true,true).slideUp(150, function(){
			th.closest('.basket_normal').find('.popup').removeClass('block');
		});
	});

	$(document).on('click', '.popup_button_basket', function(){
		var th=$(".to-cart[data-item="+$(this).data("item")+"]");

		var val = th.attr('data-quantity');

		if(!val) $val = 1;

		var tmp_props=th.data("props"),
			props='',
			part_props='',
			add_props='N',
			fill_prop={},
			iblockid = th.data('iblockid'),
			offer = th.data('offers'),
			rid='',
			basket_props='',
			item = th.attr('data-item');

		if(offer!="Y"){
			offer = "N";
		}else{
			basket_props=th.closest('.prices_tab').find('.bx_sku_props input').val();
		}
		if(tmp_props){
			props=tmp_props.split(";");
		}
		if(th.data("part_props")){
			part_props=th.data("part_props");
		}
		if(th.data("add_props")){
			add_props=th.data("add_props");
		}
		if($('.rid_item').length){
			rid=$('.rid_item').data('rid');
		}else if(th.data('rid')){
			rid=th.data('rid');
		}

		fill_prop=fillBasketPropsExt(th, 'prop', 'bx_ajax_text');

		fill_prop.quantity=val;
		fill_prop.add_item='Y';
		fill_prop.rid=rid;
		fill_prop.offers=offer;
		fill_prop.iblockID=iblockid;
		fill_prop.part_props=part_props;
		fill_prop.add_props=add_props;
		fill_prop.props=JSON.stringify(props);
		fill_prop.item=item;
		fill_prop.basket_props=basket_props;

		$.ajax({
			type:"POST",
			url:arNextOptions['SITE_DIR']+"ajax/item.php",
			data:fill_prop,
			dataType:"json",
			success:function(data){
				$('.basket_error_frame').jqmHide();
				if("STATUS" in data){
					getActualBasket(fill_prop.iblockID);
					if(data.STATUS === 'OK'){
						th.hide();
						th.closest('.counter_wrapp').find('.counter_block').hide();
						th.parents('tr').find('.counter_block_wr .counter_block').hide();
						th.closest('.button_block').addClass('wide');
						th.parent().find('.in-cart').show();

						addBasketCounter(item);
						$('.wish_item[data-item='+item+']').removeClass("added");
						$('.wish_item[data-item='+item+']').find(".value").show();
						$('.wish_item[data-item='+item+']').find(".value.added").hide();

						if($("#ajax_basket").length)
							reloadTopBasket('add', $('#ajax_basket'), 200, 5000, 'Y');

						if($("#basket_line .basket_fly").length){
							if(th.closest('.fast_view_frame').length || window.matchMedia('(max-width: 767px)').matches)
								basketFly('open', 'N');
							else
								basketFly('open');
						}

					}else{
						showBasketError(BX.message(data.MESSAGE));
					}
				}else{
					showBasketError(BX.message("CATALOG_PARTIAL_BASKET_PROPERTIES_ERROR"));
				}
			}
		});
	})

	$(document).on( 'click', '.to-cart:not(.read_more)', function(e){
		e.preventDefault();
		var th=$(this);
		if(!th.hasClass('clicked'))
		{
			th.addClass('clicked');
			var val = $(this).attr('data-quantity');


			var tmp_props=$(this).data("props"),
				props='',
				part_props='',
				add_props='N',
				fill_prop={},
				iblockid = $(this).data('iblockid'),
				offer = $(this).data('offers'),
				rid='',
				basket_props='',
				item = $(this).attr('data-item');
			if(th.closest('.but-cell').length)
			{
				if($('.counter_block[data-item="'+item+'"]').length)
					val = $('.counter_block[data-item="'+item+'"] input').val();
			}

			if(!val)
				val = 1;
			if(offer!="Y"){
				offer = "N";
			}else{
				basket_props=$(this).closest('.prices_tab').find('.bx_sku_props input').val();
			}
			if(tmp_props){
				props=tmp_props.split(";");
			}
			if($(this).data("part_props")){
				part_props=$(this).data("part_props");
			}
			if($(this).data("add_props")){
				add_props=$(this).data("add_props");
			}
			if($('.rid_item').length){
				rid=$('.rid_item').data('rid');
			}else if($(this).data('rid')){
				rid=$(this).data('rid');
			}

			fill_prop=fillBasketPropsExt(th, 'prop', th.data('bakset_div'));


			fill_prop.quantity=val;
			fill_prop.add_item='Y';
			fill_prop.rid=rid;
			fill_prop.offers=offer;
			fill_prop.iblockID=iblockid;
			fill_prop.part_props=part_props;
			fill_prop.add_props=add_props;
			fill_prop.props=JSON.stringify(props);
			fill_prop.item=item;
			fill_prop.basket_props=basket_props;

			if(th.data("empty_props")=="N"){
				showBasketError($("#"+th.data("bakset_div")).html(), BX.message("ERROR_BASKET_PROP_TITLE"), "Y", th);

				var eventdata = {action:'loadForm'};
				BX.onCustomEvent('onCompleteAction', [eventdata, th[0]]);
			}else{
				$.ajax({
					type:"POST",
					url:arNextOptions['SITE_DIR']+"ajax/item.php",
					data:fill_prop,
					dataType:"json",
					success:function(data){
						getActualBasket(fill_prop.iblockID);

						var eventdata = {action:'loadForm'};
						BX.onCustomEvent('onCompleteAction', [eventdata, th[0]]);
						arStatusBasketAspro = {};

						if(data !==null){
							if("STATUS" in data){
								if(data.MESSAGE_EXT===null)
									data.MESSAGE_EXT='';
								if(data.STATUS === 'OK'){
									// th.hide();
									$('.to-cart[data-item='+item+']').hide();
									$('.to-cart[data-item='+item+']').closest('.counter_wrapp').find('.counter_block').hide();
									$('.to-cart[data-item='+item+']').parents('tr').find('.counter_block_wr .counter_block').hide();
									$('.to-cart[data-item='+item+']').closest('.button_block').addClass('wide');
									// th.parent().find('.in-cart').show();
									$('.in-cart[data-item='+item+']').show();

									addBasketCounter(item);
									$('.wish_item[data-item='+item+']').removeClass("added");
									$('.wish_item[data-item='+item+']').find(".value").show();
									$('.wish_item[data-item='+item+']').find(".value.added").hide();

									if($("#ajax_basket").length)
										reloadTopBasket('add', $('#ajax_basket'), 200, 5000, 'Y');

									if($("#basket_line .basket_fly").length){
										if(th.closest('.fast_view_frame').length || window.matchMedia('(max-width: 767px)').matches  || $("#basket_line .basket_fly.loaded").length)
											basketFly('open', 'N');
										else
											basketFly('open');
									}

								}else{
									showBasketError(BX.message(data.MESSAGE)+' <br/>'+data.MESSAGE_EXT);
								}
							}else{
								showBasketError(BX.message("CATALOG_PARTIAL_BASKET_PROPERTIES_ERROR"));
							}
						}else{
							// th.hide();
							$('.to-cart[data-item='+item+']').hide();
							$('.to-cart[data-item='+item+']').closest('.counter_wrapp').find('.counter_block').hide();
							$('.to-cart[data-item='+item+']').parents('tr').find('.counter_block_wr .counter_block').hide();
							$('.to-cart[data-item='+item+']').closest('.button_block').addClass('wide');
							// th.parent().find('.in-cart').show();
							$('.in-cart[data-item='+item+']').show();

							addBasketCounter(item);
							$('.wish_item[data-item='+item+']').removeClass("added");
							$('.wish_item[data-item='+item+']').find(".value").show();
							$('.wish_item[data-item='+item+']').find(".value.added").hide();

							if($("#ajax_basket").length)
								reloadTopBasket('add', $('#ajax_basket'), 200, 5000, 'Y');

							if($("#basket_line .basket_fly").length)
							{
								if(th.closest('.fast_view_frame').length || window.matchMedia('(max-width: 767px)').matches  || $("#basket_line .basket_fly.loaded").length)
									basketFly('open', 'N');
								else
									basketFly('open');
							}
						}
					}
				});
			}
		}

	})

	$(document).on('click', '.to-subscribe', function(e){
		e.preventDefault();
		var th=$(this);
		if(!th.hasClass('clicked'))
		{
			th.addClass('clicked');
			if($(this).is('.auth'))
			{
				if($(this).hasClass('nsubsc'))
				{
					$(this).jqmEx();
					$(this).trigger('click');
				}
				else
				{
					location.href=arNextOptions['SITE_DIR']+'auth/?backurl='+location.pathname;
				}
			}
			else
			{
				var item = $(this).attr('data-item'),
					iblockid = $(this).attr('data-iblockid');
				// $(this).hide();
				$('.to-subscribe[data-item='+item+']').hide();
				$('.to-subscribe[data-item='+item+']').parent().find('.in-subscribe').show();
				$.get(arNextOptions['SITE_DIR'] + 'ajax/item.php?item=' + item + '&subscribe_item=Y',
					$.proxy(function(data){
						arStatusBasketAspro = {};
						getActualBasket(iblockid);
					})
				);
				th.removeClass('clicked');
			}
		}
	})

	$(document).on('click', '.in-subscribe', function(e){
		e.preventDefault();
		var item = $(this).attr('data-item'),
			iblockid = $(this).attr('data-iblockid');;
		// $(this).hide();
		$('.in-subscribe[data-item='+item+']').hide();
		$('.in-subscribe[data-item='+item+']').parent().find('.to-subscribe').removeClass('clicked');
		$('.in-subscribe[data-item='+item+']').parent().find('.to-subscribe').show();
		$.get(arNextOptions['SITE_DIR'] + 'ajax/item.php?item=' + item + '&subscribe_item=Y',
			$.proxy(function(data){
				getActualBasket(iblockid);
				arStatusBasketAspro = {};
			})
		);
	})

	$(document).on('click', '.wish_item', function(e){
		e.preventDefault();
		var val = $(this).attr('data-quantity'),
			_this = $(this),
			offer = $(this).data('offers'),
			iblockid = $(this).data('iblock'),
			tmp_props=$(this).data("props"),
			props='',
			item = $(this).attr('data-item');
			item2 = $(this).attr('data-item');

		if(!_this.hasClass('clicked'))
		{
			_this.addClass('clicked');
			if(!val) $val = 1;
			if(offer!="Y") offer = "N";
			if(tmp_props){
				props=tmp_props.split(";");
			}
			if(!$(this).hasClass('text')){
				if($(this).hasClass('added')){
					$(this).hide();
					$(this).closest('.wish_item_button').find('.to').show();

					$('.like_icons').each(function(){
						if($(this).find('.wish_item.text[data-item="'+item+'"]').length){
							$(this).find('.wish_item.text[data-item="'+item+'"]').removeClass('added');
							$(this).find('.wish_item.text[data-item="'+item+'"]').find('.value').show();
							$(this).find('.wish_item.text[data-item="'+item+'"]').find('.value.added').hide();
						}
						if($(this).find('.wish_item_button').length){
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').removeClass('added');
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value').show();
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value.added').hide();
						}
					})
				}
				else{
					$(this).hide();
					$(this).closest('.wish_item_button').find('.in').addClass('added').show();

					$('.like_icons').each(function(){
						if($(this).find('.wish_item.text[data-item="'+item+'"]').length){
							$(this).find('.wish_item.text[data-item="'+item+'"]').addClass('added');
							$(this).find('.wish_item.text[data-item="'+item+'"]').find('.value').hide();
							$(this).find('.wish_item.text[data-item="'+item+'"]').find('.value.added').css({"display":"block"})
						}
						if($(this).find('.wish_item_button').length){
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').addClass('added');
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value').hide();
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value.added').show();
						}
					})
				}
			}else{
				if(!$(this).hasClass('added')){
					$('.wish_item[data-item=' + item + ']').addClass('added');
					$('.wish_item[data-item=' + item + ']').find('.value').hide();
					$('.wish_item[data-item=' + item + ']').find('.value.added').css('display','block');

					$('.like_icons').each(function(){
						if($(this).find('.wish_item_button').length){
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').addClass('added');
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value').hide();
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value.added').show();
						}
					})
				}else{
					$('.wish_item[data-item=' + item + ']').removeClass('added');
					$('.wish_item[data-item=' + item + ']').find('.value').show();
					$('.wish_item[data-item=' + item + ']').find('.value.added').hide();

					$('.like_icons').each(function(){
						if($(this).find('.wish_item_button').length){
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').removeClass('added');
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value').show();
							$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value.added').hide();
						}
					})
				}
			}

			$('.in-cart[data-item=' + item + ']').hide();
			$('.to-cart[data-item=' + item + ']').removeClass('clicked');
			$('.to-cart[data-item=' + item + ']').parent().removeClass('wide');
			if(!$('.counter_block[data-item=' + item + ']').closest('.counter_wrapp').find('.to-order').length)
			{
				$('.to-cart[data-item=' + item + ']').show();
				$('.counter_block[data-item=' + item + ']').show();
			}
			if(!$(this).closest('.module-cart').size()){
				$.ajax({
					type:"GET",
					url:arNextOptions['SITE_DIR']+"ajax/item.php",
					data:"item="+item2+"&quantity="+val+"&wish_item=Y"+"&offers="+offer+"&iblockID="+iblockid+"&props="+JSON.stringify(props),
					dataType:"json",
					success: function(data){
						getActualBasket(iblockid);
						arStatusBasketAspro = {};
						if(data !==null){
							if(data.MESSAGE_EXT===null)
								data.MESSAGE_EXT='';
							if("STATUS" in data){
								if(data.STATUS === 'OK'){
									if(arNextOptions['COUNTERS']['USE_BASKET_GOALS'] !== 'N'){
										var eventdata = {goal: 'goal_wish_add', params: {id: item2}};
										BX.onCustomEvent('onCounterGoals', [eventdata]);
									}
									if($("#ajax_basket").length)
										reloadTopBasket('wish', $('#ajax_basket'), 200, 5000, 'N');

									if($("#basket_line .basket_fly").length){
										if(_this.closest('.fast_view_frame').length || window.matchMedia('(max-width: 767px)').matches || $("#basket_line .basket_fly.loaded").length)
											basketFly('wish', 'N');
										else
											basketFly('wish');
									}
								}else{
									showBasketError(BX.message(data.MESSAGE)+' <br/>'+data.MESSAGE_EXT, BX.message("ERROR_ADD_DELAY_ITEM"));
								}
							}else{
								showBasketError(BX.message(data.MESSAGE)+' <br/>'+data.MESSAGE_EXT, BX.message("ERROR_ADD_DELAY_ITEM"));
							}
						}else{
							if($("#ajax_basket").length)
								reloadTopBasket('wish', $('#ajax_basket'), 200, 5000, 'N');

							if($("#basket_line .basket_fly").length){
								if(_this.closest('.fast_view_frame').length || window.matchMedia('(max-width: 767px)').matches || $("#basket_line .basket_fly.loaded").length)
									basketFly('wish', 'N');
								else
									basketFly('wish');
							}
						}
					_this.removeClass('clicked');
					}
				});
			}
		}

	})

	$(document).on('click', '.item_main_info .item_slider .flex-direction-nav li span', function(e){
		if($('.inner_slider .slides_block').length)
		{
			if($(this).parent().hasClass('flex-nav-next'))
				$('.inner_slider .slides_block li.current').next().click();
			else
				$('.inner_slider .slides_block li.current').prev().click();
		}
	})

	$(document).on('click', '.compare_item', function(e){
		e.preventDefault();
		var item = $(this).attr('data-item');
		var iblockID = $(this).attr('data-iblock');
		var th=$(this);
		if(!th.hasClass('clicked'))
		{
			th.addClass('clicked');
			if(!$(this).hasClass('text')){
				if($(this).hasClass('added')){
					$(this).hide();
					$(this).closest('.compare_item_button').find('.to').show();

					/*sync other button*/
					$('.like_icons').each(function(){
						if($(this).find('.compare_item.text[data-item="'+item+'"]').length){
							$(this).find('.compare_item.text[data-item="'+item+'"]').removeClass('added');
							$(this).find('.compare_item.text[data-item="'+item+'"]').find('.value').show();
							$(this).find('.compare_item.text[data-item="'+item+'"]').find('.value.added').hide();
						}
						if($(this).find('.compare_item_button').length){
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').removeClass('added');
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value').show();
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value.added').hide();
						}
					})
				}
				else{
					$(this).hide();
					$(this).closest('.compare_item_button').find('.in').show();

					/*sync other button*/
					$('.like_icons').each(function(){
						if($(this).find('.compare_item.text[data-item="'+item+'"]').length){
							$(this).find('.compare_item.text[data-item="'+item+'"]').addClass('added');;
							$(this).find('.compare_item.text[data-item="'+item+'"]').find('.value').hide();
							$(this).find('.compare_item.text[data-item="'+item+'"]').find('.value.added').css({"display":"block"});
						}
						if($(this).find('.compare_item_button').length){
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').addClass('added');
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value.added').show();
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value').hide();
						}
					})
				}
			}else{
				if(!$(this).hasClass('added')){
					$('.compare_item[data-item=' + item + ']').addClass('added');
					$('.compare_item[data-item=' + item + ']').find('.value').hide();
					$('.compare_item[data-item=' + item + ']').find('.value.added').css('display','block');

					/*sync other button*/
					$('.like_icons').each(function(){
						if($(this).find('.compare_item_button').length){
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').addClass('added');
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value.added').show();
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value').hide();
						}
					})
				}else{
					$('.compare_item[data-item=' + item + ']').removeClass('added');
					$('.compare_item[data-item=' + item + ']').find('.value').show();
					$('.compare_item[data-item=' + item + ']').find('.value.added').hide();

					/*sync other button*/
					$('.like_icons').each(function(){
						if($(this).find('.compare_item_button').length){
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').removeClass('added');
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value').show();
							$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value.added').hide();
						}
					})
				}
			}

			$.get(arNextOptions['SITE_DIR'] + 'ajax/item.php?item=' + item + '&compare_item=Y&iblock_id=' + iblockID,
				$.proxy(function(data){
					getActualBasket(iblockID, 'Compare');
					arStatusBasketAspro = {};
					if($('#compare_fly').length){
						jsAjaxUtil.InsertDataToNode(arNextOptions['SITE_DIR'] + 'ajax/show_compare_preview_fly.php', 'compare_fly', false);
					}
				th.removeClass('clicked');
				})
			);
		}

	});

	$('.fancy').fancybox({
		openEffect  : 'fade',
		closeEffect : 'fade',
		nextEffect : 'fade',
		prevEffect : 'fade',
		tpl:{
			closeBtn : '<a title="'+BX.message('FANCY_CLOSE')+'" class="fancybox-item fancybox-close" href="javascript:;"></a>',
			next     : '<a title="'+BX.message('FANCY_NEXT')+'" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
			prev     : '<a title="'+BX.message('FANCY_PREV')+'" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
		},
	});
	$('.fancybox').fancybox();

	$(".video_link").fancybox({
		type: "iframe",
        maxWidth    : 800,
        maxHeight   : 600,
        fitToView   : true,
        //width       : '70%',
        //height      : '70%',
        autoSize    : true,
        closeClick  : false,
    });

	$('.tabs>li').on('click', function(){
		var parent = $(this).parent();
		if(!$(this).hasClass('active')){
			var sliderIndex = $(this).index(),
				curLiNav=$(this).closest('.top_blocks').find('.slider_navigation').find('>li:eq(' + sliderIndex + ')'),
				curLi=$(this).closest('.top_blocks').siblings('.tabs_content').find('>li:eq(' + sliderIndex + ')');
			$(this).addClass('active').addClass('cur').siblings().removeClass('active').removeClass('cur');
			curLi.addClass('cur').siblings().removeClass('cur');
			curLiNav.addClass('cur').siblings().removeClass('cur');

			if(parent.hasClass('ajax')) // front tabs
			{
				if(!$(this).hasClass('clicked'))
				{
					$.ajax({
						url: arNextOptions['SITE_DIR'] + 'include/mainpage/comp_catalog_ajax.php',
						type: 'POST',
						data: {AJAX_POST: 'Y', FILTER_HIT_PROP: $(this).data('code'), AJAX_PARAMS: $(this).closest('.tab_slider_wrapp').find('.request-data').data('value'), GLOBAL_FILTER: curLi.data('filter')},
					}).success(function(html){
						curLi.find('.tabs_slider').html(html);
						setTimeout(function(){
							curLi.addClass('opacity1');
						}, 100);

						/*equal height*/
						curLi.find('.catalog_block .catalog_item_wrapp .catalog_item .item_info:visible .item-title').sliceHeight({item:'.catalog_item:visible', mobile: true});
						curLi.find('.catalog_block .catalog_item_wrapp .catalog_item .item_info:visible').sliceHeight({item:'.catalog_item:visible', mobile: true});
						curLi.find('.catalog_block .catalog_item_wrapp .catalog_item:visible').sliceHeight({classNull: '.footer_button', item:'.catalog_item:visible', mobile: true});

						initCountdown();
					})
					$(this).addClass('clicked');
				}
			}
		}
	})

	/*search click*/
	$('.search_block .icon').on('click', function(){
		var th=$(this);
		if($(this).hasClass('open')){
			$(this).closest('.center_block').find('.search_middle_block').removeClass('active');
			$(this).removeClass('open');
			$(this).closest('.center_block').find('.search_middle_block').find('.noborder').hide();
		}else{
			setTimeout(function(){
				th.closest('.center_block').find('.search_middle_block').find('.noborder').show();
			},100);
			$(this).closest('.center_block').find('.search_middle_block').addClass('active');
			$(this).addClass('open');
		}
	})
	$(document).on('mouseenter', '.display_list .item_wrap', function(){
		$(this).prev().addClass('prev');
	});
	$(document).on('mouseleave', '.display_list .item_wrap', function(){
		$(this).prev().removeClass('prev');
	});
	/*$(document).on('mouseenter', '.catalog_block .item_wrap', function(){
		$(this).addClass('shadow_delay');
	});
	$(document).on('mouseleave', '.catalog_block .item_wrap', function(){
		$(this).removeClass('shadow_delay');
	});*/
	$(document).on('click', '.no_goods .button', function(){
		$('.bx_filter .smartfilter .bx_filter_search_reset').trigger('click');
	});

	$(document).on('click', '.ajax_load_btn', function(){
		var url=$(this).closest('.container').find('.module-pagination .flex-direction-nav .flex-next').attr('href'),
			th=$(this).find('.more_text_ajax');

		if(!th.hasClass('loading'))
		{
			th.addClass('loading');
			var objUrl = parseUrlQuery(),
				add_url = ''
				obGetData = {"ajax_get": "Y", "AJAX_REQUEST": "Y"};
			if('clear_cache' in objUrl)
			{
				if(objUrl.clear_cache == 'Y')
					add_url = '&clear_cache=Y';
			}
			if(th.closest('.goods-block.ajax_load'))
				obGetData.bitrix_include_areas = 'N';
			$.ajax({
				url: url+add_url,
				data: obGetData,
				success: function(html){
					if($('.ajax_load').length)
					{
						th.removeClass('loading');
						if($('.display_list').length){
							$('.display_list').append(html);
						}else if($('.block_list').length){
							$('.block_list').append(html);
							touchItemBlock('.catalog_item a');
						}else if($('.module_products_list').length){
							$('.module_products_list > tbody').append(html);
						}
						setStatusButton();
						initCountdown();

						var eventdata = {action:'ajaxContentLoadedTab'};
						BX.onCustomEvent('onAjaxSuccess', [eventdata]);
						$('.bottom_nav').html($(html).find('.bottom_nav').html());
					}
					else
					{
						if($('.banners-small.front').length)
						{
							$('.banners-small .items.row').append(html);
							$('.bottom_nav').html($('.banners-small .items.row .bottom_nav').html());
							$('.banners-small .items.row .bottom_nav').remove();
						}
						else
						{
							if(th.closest('.item-views').find('.items').length)
							{
								th.closest('.item-views').find('.items').append(html);
							}
							else
							{
								$(html).insertBefore($('.blog .bottom_nav'));
							}

							$('.bottom_nav').html($('.bottom_nav:hidden').html());
							$('.bottom_nav:hidden').remove();
						}

						var eventdata = {action:'ajaxContentLoaded', content: html};
						BX.onCustomEvent('onCompleteAction', [eventdata, th[0]]);

						setTimeout(function(){
							$('.banners-small .item.normal-block').sliceHeight({resize: false});
							if($('.item.slice-item').length)
							{
								$('.item.slice-item .title').sliceHeight({resize: false});
								$('.item.slice-item').sliceHeight({resize: false});
							}
							th.removeClass('loading');
						}, 100);
					}
				}
			})
		}
	})

	//set cookie for basket link click
	$(document).on('click', '.bx_ordercart_order_table_container .control > a, .basket-item-actions-remove, a[data-entity=basket-item-remove-delayed]', function(e){
		$.removeCookie('click_basket', {path: '/'});
		$.cookie('click_basket', 'Y', {path: '/'});
	})

	$(document).on('click', '.bx_compare .tabs-head li', function(){
		var url=$(this).find('.sortbutton').data('href');
		BX.showWait(BX("bx_catalog_compare_block"));
		$.ajax({
			url: url,
			data: {'ajax_action': 'Y'},
			success: function(html){
				history.pushState(null, null, url);
				$('#bx_catalog_compare_block').html(html);
				BX.closeWait();
			}
		})
	})
	var hoveredTrs;
	$(document).on({
		mouseover: function(e){
			var _ = $(this);
			var tbodyIndex = _.closest('tbody').index()+1; //+1 for nth-child
			var trIndex = _.index()+1; // +1 for nth-child
			hoveredTrs = $(e.delegateTarget).find('.data_table_props')
				.children(':nth-child('+tbodyIndex+')')
				.children(':nth-child('+trIndex+')').addClass('hovered');
		},
		mouseleave: function(e){
			if(hoveredTrs)
				hoveredTrs.removeClass('hovered');
		}
	}, '.bx_compare .data_table_props tbody>tr');
	$(document).on('click', '.fancy_offer', function(e){
		e.preventDefault();
		var arPict=[], index = 0;
		$(this).closest('.item_slider').find('.sliders .slides_block li').each(function(){
			var obImg = {};
			obImg = {
				'title': $(this).find('img').attr('alt'),
				'href': $(this).data('big')
			}
			if($(this).hasClass('current')){
				index = $(this).index();
			}
			arPict.push(obImg);
		})
		$.fancybox(arPict, {
			index: index,
			openEffect  : 'fade',
			closeEffect : 'fade',
			nextEffect : 'fade',
			prevEffect : 'fade',
			type : 'image',
			tpl:{
				closeBtn : '<a title="'+BX.message('FANCY_CLOSE')+'" class="fancybox-item fancybox-close" href="javascript:;"></a>',
				next     : '<a title="'+BX.message('FANCY_NEXT')+'" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
				prev     : '<a title="'+BX.message('FANCY_PREV')+'" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
			},
		});
	})

	/*tabs*/
	$(".tabs_section .tabs-head li").on("click", function(){
		if(!$(this).is(".current")){
			$(".tabs_section .tabs-head li").removeClass("current");
			$(this).addClass("current");
			$(".tabs_section ul.tabs_content li").removeClass("current");
			if($(this).attr("id") == "product_reviews_tab"){
				$(".shadow.common").hide(); $("#reviews_content").show();
			}
			else{
				$(".shadow.common").show();
				$("#reviews_content").hide();
				$(".tabs_section ul.tabs_content > li:eq("+$(this).index()+")").addClass("current");
			}
		}
	});

	/*open first section slide*/
	setTimeout(function() {
		$('.jobs_wrapp .item:first .name tr').trigger('click');
	}, 300);

	$('.choise').on('click', function(){
		var _this = $(this);
		if(typeof(_this.data('block')) != 'undefined')
		{
			scrollToBlock(_this.data('block'));
		}
	})

	$(document).on('click', '.buy_block .slide_offer', function(){
		scroll_block($('.tabs_section'));
	});
	$('.share  > .share_wrapp .text').on('click', function(){
		var block = $(this).parent().find('.shares');
		if(!block.is(':visible') && !$(this).closest('.share.top').length)
			$('#content').css('z-index',3);
		block.fadeToggle(100,function(){
			if(!block.is(':visible'))
				$('#content').css('z-index',2);
		});
	})
	$('html, body').on('mousedown', function(e) {
		if(typeof e.target.className == 'string' && e.target.className.indexOf('adm') < 0)
		{
			e.stopPropagation();
			$('div.shares').fadeOut(100, function(){
				$('#content').css('z-index',2);
				$('.price_txt .share_wrapp').removeClass('opened');
			});
			$('.search_middle_block').removeClass('active_wide');
		}
	})
	$('.share_wrapp').find('*').on('mousedown', function(e) {
		e.stopPropagation();
	});

	$('.price_txt .share_wrapp .text').click(function(){
		$(this).parent().toggleClass('opened');
		$(this).parent().find('.shares').fadeToggle();
	})

	$(document).on('click', '.reviews-collapse-link', function(){
		$('.reviews-reply-form').slideToggle();
	})

	/* accordion action*/
	$('.panel-collapse').on('hidden.bs.collapse', function(){
		$(this).parent().toggleClass('opened');
	})
	$('.panel-collapse').on('show.bs.collapse', function(){
		$(this).parent().toggleClass('opened');
	})

	/* accordion */
	$('.accordion-head').on('click', function(e){
		e.preventDefault();
		if(!$(this).next().hasClass('collapsing')){
			$(this).toggleClass('accordion-open');
			$(this).toggleClass('accordion-close');
		}
	});

	/* progress bar */
	$('[data-appear-progress-animation]').each(function(){
		var $this = $(this);
		$this.appear(function(){
			var delay = ($this.attr('data-appear-animation-delay') ? $this.attr('data-appear-animation-delay') : 1);
			if( delay > 1 )
				$this.css('animation-delay', delay + 'ms');
			$this.addClass($this.attr('data-appear-animation'));

			setTimeout(function(){
				$this.animate({
					width: $this.attr('data-appear-progress-animation')
				}, 1500, 'easeOutQuad', function() {
					$this.find('.progress-bar-tooltip').animate({
						opacity: 1
					}, 500, 'easeOutQuad');
				});
			}, delay);
		}, {accX: 0, accY: -50});
	});

	initCountdown();

	/* portfolio item */
	if($('.item.animated-block').length)
	{
		$('.item.animated-block').appear(function(){
			var $this = $(this);

			$this.addClass($this.data('animation')).addClass('visible');

		}, {accX: 0, accY: 150})
	}

	/*adaptive menu start*/
	$('.menu.adaptive').on('click', function(){
		$(this).toggleClass('opened');
		if($(this).hasClass('opened')){
			$('.mobile_menu').toggleClass('opened').slideDown();
		}else{
			$('.mobile_menu').toggleClass('opened').slideUp();
		}
	});
	$('.mobile_menu .has-child >a').on('click', function(e){
		var parentLi=$(this).parent();
		e.preventDefault();
		parentLi.toggleClass('opened');
		parentLi.find('.dropdown').slideToggle();
	})

	$('.mobile_menu .search-input-div input').on('keyup', function(e) {
		var inputValue = $(this).val();
		$('.center_block .stitle_form input').val(inputValue);
		if(e.keyCode == 13){
			$('.center_block .stitle_form form').submit();
		}
	});

	$('.center_block .stitle_form input').on('keyup', function(e) {
		var inputValue = $(this).val();
		$('.mobile_menu .search-input-div input').val(inputValue);
		if(e.keyCode == 13){
			$('.center_block .stitle_form form').submit();
		}
	});

	$('.mobile_menu .search-button-div button').on('click', function(e) {
		e.preventDefault();
		var inputValue = $(this).parents().find('input').val();
		$('.center_block .stitle_form input').val(inputValue);
		$('.center_block .stitle_form form').submit();
	});
	/*adaptive menu end*/

	$(document).on('click', '.mega-menu .dropdown-menu', function(e){
		e.stopPropagation()
	});

	$(document).on('click', '.mega-menu .dropdown-toggle.more-items', function(e){
		e.preventDefault();
	});

	$('.table-menu .dropdown,.table-menu .dropdown-submenu,.table-menu .dropdown-toggle').on('mouseenter', function() {
		CheckTopVisibleMenu();
	});

	$(document).on('mouseenter', '#headerfixed .table-menu .dropdown-menu .dropdown-submenu', function() {
		CheckTopVisibleMenu();
	});

	$('.mega-menu .search-item .search-icon, .menu-row #title-search .fa-close').on('click', function(e) {
		e.preventDefault();
		$('.menu-row #title-search').toggleClass('hide');
	});

	$('.mega-menu ul.nav .search input').on('keyup', function(e) {
		var inputValue = $(this).val();
		$('.menu-row > .search input').val(inputValue);
		if(e.keyCode == 13){
			$('.menu-row > .search form').submit();
		}
	});

	$('.menu-row > .search input').on('keyup', function(e) {
		var inputValue = $(this).val();
		$('.mega-menu ul.nav .search input').val(inputValue);
		if(e.keyCode == 13){
			$('.menu-row > .search form').submit();
		}
	});

	$('.mega-menu ul.nav .search button').on('click', function(e) {
		e.preventDefault();
		var inputValue = $(this).parents('.search').find('input').val();
		$('.menu-and-search .search input').val(inputValue);
		$('.menu-row > .search form').submit();
	});

	$('.btn.btn-add').on('click', function(){
		$.ajax({
			type:"GET",
			url:arNextOptions['SITE_DIR']+"ajax/clearBasket.php",
			success: function(data){
			}
		});
	});

	$('.sort_display a').on('click', function(){
		$(this).siblings().removeClass('current');
		$(this).addClass('current');
	});

	/*detail order show payments*/
	$('.sale-order-detail-payment-options-methods-info-change-link').on('click', function(){
		$(this).closest('.sale-order-detail-payment-options-methods-info').addClass('opened').siblings().addClass('opened');
	});

	/*expand/hide filter values*/
	$(document).on('click', '.expand_block', function(){
		togglePropBlock($(this));
	});

	/*touch event*/
	document.addEventListener('touchend', function(event) {
		if(!$(event.target).closest('.menu-item').length && !$(event.target).hasClass('menu-item')){
			$('.menu-row .dropdown-menu').css({'display':'none','opacity':0});
			$('.menu-item').removeClass('hover');
			$('.bx-breadcrumb-item.drop').removeClass('hover');
		}
		if(!$(event.target).closest('.menu.topest').length){
			$('.menu.topest').css({'overflow': 'hidden'});
			$('.menu.topest > li').removeClass('hover');
		}
		if(!$(event.target).closest('.full.has-child').length){
			$('.menu_top_block.catalog_block li').removeClass('hover');
		}
		if(!$(event.target).closest('.basket_block').length){
			$('.basket_block .link').removeClass('hover');
			$('.basket_block .basket_popup_wrapp').slideUp();
		}
		if(!$(event.target).closest('.catalog_item').length){
			var tabsContentUnhoverHover = $('.tab:visible').attr('data-unhover') * 1;
			$('.tab:visible').stop().animate({'height': tabsContentUnhoverHover}, 100);
			$('.tab:visible').find('.catalog_item').removeClass('hover');
			$('.tab:visible').find('.catalog_item .buttons_block').stop().fadeOut(233);
			if($('.catalog_block').length){
				$('.catalog_block').find('.catalog_item_wrapp').removeClass('hover');
				$('.catalog_block').find('.catalog_item').removeClass('hover');
			}
		}
		//togglePropBlock($(event.target));

	}, false);

	touchMenu('.menu-row .menu-item');
	touchTopMenu('.menu.topest li');
	touchLeftMenu('.menu_top_block li.full');
	touchBreadcrumbs('.bx-breadcrumb-item.drop');

	$(document).on('keyup', '.coupon .input_coupon input', function(){
		if($(this).val().length){
			$(this).removeClass('error');
			$(this).closest('.input_coupon').find('.error').remove();
		}else{
			$(this).addClass('error');
			$("<label class='error'>"+BX.message("INPUT_COUPON")+"</label>").insertBefore($(this));
		}
	});
	showPhoneMask('input[autocomplete=tel]');
	BX.addCustomEvent(window, "onAjaxSuccessFilter", function(e){
		setBasketStatusBtn();
	});

	BX.addCustomEvent(window, "onAjaxSuccess", function(e){
		if(typeof(e) === 'undefined' || e != 'OK')
		{
            if(typeof(e) !== 'undefined' && e && typeof(e.FILTER_URL) != 'undefined' && typeof(e.ELEMENT_COUNT) != 'undefined'){
                mobileFilterNum(e.ELEMENT_COUNT);
            }

			initSelects(document);
			InitOrderCustom();
			showPhoneMask('input[autocomplete=tel]');
			if($('.catalog_detail').length && !$('.fast_view_frame').length)
			{
				$('.bx_filter').remove();
				InitFlexSlider();
			}

			if(arNextOptions["PAGES"]["CATALOG_PAGE"])
			{
				//setStatusButton();
				initCountdown();
			}

			if(arNextOptions["PAGES"]["ORDER_PAGE"])
			{
				orderActions(e);
			}
		}
	});

	//event for default basket quantity change
	BX.addCustomEvent(window, "OnBasketChange", function(e){
		if(arNextOptions["PAGES"]["BASKET_PAGE"])
		{
			var summ = 0,
				title = '';

			if(typeof BX.Sale !== "undefined")
			{
				if(typeof BX.Sale.BasketComponent !== "undefined")
				{
					summ = BX.Sale.BasketComponent.result.allSum;
					title = BX.message('JS_BASKET_COUNT_TITLE').replace('SUMM', summ);
				}
			}
			else
			{
				summ = $('#allSum_FORMATED').html().replace(/&nbsp;/g, ' ');
				title = BX.message('JS_BASKET_COUNT_TITLE').replace('SUMM', summ);
			}

			if($('.js-basket-block .wrap .prices').length)
				$('.js-basket-block .wrap .prices').html(summ);
			if($('a.basket-link.basket').length)
				$('a.basket-link.basket').attr('title', title);
			if($('.basket_fly .opener .basket_count').length)
				$('.basket_fly .opener .basket_count').attr('title', title);

		}
	});
	BX.addCustomEvent(window, "onFrameDataRequestFail", function(response){
		console.log(response);
	});
});

if(!funcDefined('setBasketStatusBtn')){
	setBasketStatusBtn = function(){
		if(typeof(arBasketAspro) !== 'undefined')
		{
			if('BASKET' in arBasketAspro) // basket items
			{
				if(arBasketAspro.BASKET)
				{
					for(var i in arBasketAspro.BASKET)
					{
						$('.to-cart[data-item='+i+']').hide();
						$('.counter_block[data-item='+i+']').hide();
						$('.in-cart[data-item='+i+']').show();
						$('.in-cart[data-item='+i+']').closest('.button_block').addClass('wide');
					}
				}
			}

			if('DELAY' in arBasketAspro) // delay items
			{
				if(arBasketAspro.DELAY)
				{
					for(var i in arBasketAspro.DELAY)
					{
						$('.wish_item.to[data-item='+i+']').hide();
						$('.wish_item.in[data-item='+i+']').show();
						if ($('.wish_item[data-item='+i+']').find(".value.added").length)
						{
							$('.wish_item[data-item='+i+']').addClass("added");
							$('.wish_item[data-item='+i+']').find(".value").hide();
							$('.wish_item[data-item='+i+']').find(".value.added").css('display','block');
						}
					}
				}
			}

			if('SUBSCRIBE' in arBasketAspro) // subscribe items
			{
				if(arBasketAspro.SUBSCRIBE)
				{
					for(var i in arBasketAspro.SUBSCRIBE)
					{
						$('.to-subscribe[data-item='+i+']').hide();
						$('.in-subscribe[data-item='+i+']').show();
					}
				}
			}

			if('COMPARE' in arBasketAspro) // compare items
			{
				if(arBasketAspro.COMPARE)
				{
					for(var i in arBasketAspro.COMPARE)
					{
						$('.compare_item.to[data-item='+i+']').hide();
						$('.compare_item.in[data-item='+i+']').show();
						if ($('.compare_item[data-item='+i+']').find(".value.added").length)
						{
							$('.compare_item[data-item='+i+']').addClass("added");
							$('.compare_item[data-item='+i+']').find(".value").hide();
							$('.compare_item[data-item='+i+']').find(".value.added").css('display','block');
						}
					}
				}
			}
		}
	}
}

if(!funcDefined('togglePropBlock')){
	togglePropBlock=function(className){
		var all_props_block = className.closest('.bx_filter_parameters_box_container').find('.hidden_values');
		if(all_props_block.length && (className.hasClass('inner_text') || className.hasClass('expand_block')))
		{
			if(all_props_block.is(':visible'))
			{
				className.text(BX.message('FILTER_EXPAND_VALUES'));
				all_props_block.hide();
			}
			else
			{
				className.text(BX.message('FILTER_HIDE_VALUES'));
				all_props_block.show();
			}
		}
	}
}

if(!funcDefined('showPhoneMask')){
	showPhoneMask=function(className){
		$(className).inputmask('mask', {'mask': arNextOptions['THEME']['PHONE_MASK'], 'showMaskOnHover':false });
	}
}

if(!funcDefined('parseUrlQuery')){
	parseUrlQuery=function() {
	    var data = {};
	    if(location.search) {
	        var pair = (location.search.substr(1)).split('&');
	        for(var i = 0; i < pair.length; i ++) {
	            var param = pair[i].split('=');
	            data[param[0]] = param[1];
	        }
	    }
	    return data;
	}
}

if(!funcDefined('getActualBasket')){
	getActualBasket=function(iblockID, type){
		var data='';
		if(typeof iblockID !=="undefined"){
			data={"iblockID":iblockID}
		}
		$.ajax({
			type:"GET",
			url:arNextOptions['SITE_DIR']+"ajax/actualBasket.php",
			data:data,
			success: function(data){
				if(!$('.js_ajax').length)
					$('body').append('<div class="js_ajax"></div>');
				$('.js_ajax').html(data);

				if(typeof(type) !== undefined)
				{
					var eventdata = {action:'loadActualBasket'+type};
					BX.onCustomEvent('onCompleteAction', [eventdata]);
				}
			}
		});
	}
}

function touchMenu(selector){
	if(isMobile){
		if($(selector).length)
		{
			$(selector).each(function(){
				var th=$(this);
				th.on('touchend', function(e) {
					var _th = $(e.target).closest('.menu-item');

					$('.menu.topest > li').removeClass('hover');
					$('.menu_top_block.catalog_block li').removeClass('hover');
					$('.bx-breadcrumb-item.drop').removeClass('hover');

					if (_th.find('.dropdown-menu').length && !_th.hasClass('hover')) {
						e.preventDefault();
						e.stopPropagation();
						_th.siblings().removeClass('hover');
						_th.addClass('hover');
						$('.menu-row .dropdown-menu').css({'display':'none', 'opacity':0});
						if(_th.hasClass('menu-item'))
						{
							_th.closest('.dropdown-menu').css({'display':'block', 'opacity':1});
						}
						if(_th.find('> .wrap > .dropdown-menu'))
						{
							_th.find('> .wrap > .dropdown-menu').css({'display':'block', 'opacity':1});
						}
						else if(_th.find('> .dropdown-menu'))
						{
							_th.find('> .dropdown-menu').css({'display':'block', 'opacity':1});
						}
						CheckTopVisibleMenu();
					}
					else
					{
						var href = ($(e.target).attr('href') ? $(e.target).attr('href') : $(e.target).closest('a').attr('href'))
						if(href && href !== 'undefined')
							location.href = href;
					}
				})
			})
		}
	}else{
		$(selector).off();
	}
}

function touchTopMenu(selector){
	if(isMobile){
		if($(selector).length)
		{
			$(selector).each(function(){
				var th=$(this);
				th.on('touchend', function(e) {
					var _th = $(e.target).closest('li');

					$('.menu-item').removeClass('hover');
					$('.menu_top_block.catalog_block li').removeClass('hover');
					$('.bx-breadcrumb-item.drop').removeClass('hover');

					if (_th.hasClass('more') && !_th.hasClass('hover')) {
						e.preventDefault();
						e.stopPropagation();
						_th.siblings().removeClass('hover');
						_th.addClass('hover');
						$('.menu.topest').css({'overflow': 'visible'});
					}
					else
					{
						var href = ($(e.target).attr('href') ? $(e.target).attr('href') : $(e.target).closest('a').attr('href'))
						if(href && href !== 'undefined')
							location.href = href;
					}
				})
			})
		}
	}else{
		$(selector).off();
	}
}

function touchLeftMenu(selector){
	if(isMobile){
		if($(selector).length)
		{
			$(selector).each(function(){
				var th=$(this);
				th.on('touchend', function(e) {
					var _th = $(e.target).closest('li');

					$('.menu-item').removeClass('hover');
					$('.bx-breadcrumb-item.drop').removeClass('hover');
					$('.menu.topest > li').removeClass('hover');

					if (_th.hasClass('has-child') && !_th.hasClass('hover')) {
						e.preventDefault();
						e.stopPropagation();
						_th.siblings().removeClass('hover');
						_th.addClass('hover');
					}
					else
					{
						var href = ($(e.target).attr('href') ? $(e.target).attr('href') : $(e.target).closest('a').attr('href'))
						if(href && href !== 'undefined')
							location.href = href;
					}
				})
			})
		}
	}else{
		$(selector).off();
	}
}

function touchBreadcrumbs(selector){
	if(isMobile){
		if($(selector).length)
		{
			$(document).on('touchend', selector, function(e){
				var _th = $(e.target).closest('.bx-breadcrumb-item');

				$('.menu-item').removeClass('hover');
				$('.menu.topest > li').removeClass('hover');
				$('.menu_top_block.catalog_block li').removeClass('hover');

				if($(e.target).closest('.dropdown_wrapp').length){
					var href = ($(e.target).attr('href') ? $(e.target).attr('href') : $(e.target).closest('a').attr('href'));
					if(href && href !== 'undefined')
						location.href = href;
				}
				else{
					if(!_th.hasClass('hover')) {
						e.preventDefault();
						e.stopPropagation();
						_th.siblings().removeClass('hover');
						_th.addClass('hover');
					}
					else
					{
						_th.removeClass('hover');
						var href = ($(e.target).attr('href') ? $(e.target).attr('href') : $(e.target).closest('a').attr('href'));
						if(href && href !== 'undefined')
							location.href = href;
					}
				}
			});
		}
	}else{
		$(selector).off();
	}
}

function touchItemBlock(selector){
	/*if(isMobile){
		if($(selector).length)
		{
			$(selector).each(function(){
				var th=$(this),
					item=th.closest('.catalog_item');
				th.on('touchend', function(e) {
					if (!item.hasClass('hover')) {
						e.preventDefault();
						// e.stopPropagation();
						item.siblings().removeClass('hover');
						item.siblings().blur();
						item.closest('.catalog_block').find('.catalog_item').removeClass('hover');
						item.addClass('hover');
						item.addClass('touch');

						var tabsContentHover = th.closest('.tab').attr('data-hover') * 1,
							tabsContentUnhoverHover = th.closest('.tab').attr('data-unhover') * 1;

						th.closest('.tab').stop().animate({'height': tabsContentUnhoverHover}, 100);
						th.closest('.catalog_item').siblings().find('.buttons_block').stop().fadeOut(233)

						th.closest('.tab').fadeTo(100, 1);
						th.closest('.tab').stop().css({'height': tabsContentHover});
						th.closest('.catalog_item').find('.buttons_block').fadeIn(450, 'easeOutCirc');
					}
				})
			})
		}
	}else{
		$(selector).off();
	}*/
}
function touchBasket(selector){
	if(arNextOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
		if($(window).outerWidth()>600){
			$(document).find(selector).on('touchend', function(e) {
				if ($(this).parent().find('.basket_popup_wrapp').length && !$(this).hasClass('hover')) {
					e.preventDefault();
					e.stopPropagation();
					$(this).addClass('hover');
					$(this).parent().find('.basket_popup_wrapp').slideDown();
				}
			})
		}else{
			$(selector).off();
		}
	}
}

function showTotalSummItem(popup){
	//show total summ
	if(arNextOptions["THEME"]["SHOW_TOTAL_SUMM_TYPE"] == "ALWAYS" && arNextOptions["THEME"]["SHOW_TOTAL_SUMM"] == "Y")
	{
		var parent = 'body ';
		if(typeof popup  === 'string' && popup == 'Y')
			parent = '.popup ';
		$(parent+'.counter_wrapp .counter_block input.text').each(function(){
			var _th = $(this);
			if(_th.data('product'))
			{
				var obProduct = _th.data('product');
				if(typeof window[obProduct] == 'object')
					window[obProduct].setPriceAction('Y');
				else
					setPriceItem(_th.closest('.main_item_wrapper'), _th.val());
			}
			else
				setPriceItem(_th.closest('.main_item_wrapper'), _th.val());
		})
	}
}

function initFull(){
	initSelects(document);
	initHoverBlock(document);
	touchItemBlock('.catalog_item a');
	InitOrderCustom();
	showTotalSummItem();
	basketActions();
	orderActions();

	checkMobileRegion();
}

checkMobileRegion = function(){
	if($('.confirm_region').length)
	{
		if(!$('.top_mobile_region').length)
			$('<div class="top_mobile_region"><div class="confirm_wrapper"><div class="confirm_region"></div><div class="close_popup"></div></div></div>').insertBefore($('#mobileheader'));
		$('.top_mobile_region .confirm_region').html($('.confirm_region').html());

		$('.top_mobile_region .close_popup').click(function(){
			$(this).remove();
			$('.confirm_region').remove();
		})
	}
}
if(!funcDefined('orderActions')){
	orderActions = function(e){
		if(arNextOptions["PAGES"]["ORDER_PAGE"]){
			//phone
			if($('#bx-soa-order input[autocomplete=tel]').length){
				// get property phone
				for(var i = 0;i<BX.Sale.OrderAjaxComponent.result.ORDER_PROP.properties.length;++i){
					if(BX.Sale.OrderAjaxComponent.result.ORDER_PROP.properties[i].IS_PHONE == 'Y'){
						var arPropertyPhone = BX.Sale.OrderAjaxComponent.result.ORDER_PROP.properties[i];
					}
				}

				// validate input type=tel
				if(typeof(BX.Sale.OrderAjaxComponent) !== 'undefined' && typeof(BX.Sale.OrderAjaxComponent) === 'object' && typeof(arPropertyPhone) == 'object' && arPropertyPhone){
					BX.Sale.OrderAjaxComponent.validatePhone = function(input, arProperty, fieldName)
					{
						if (!input || !arProperty)
							return [];

						var value = input.value,
							errors = [],
							name = BX.util.htmlspecialchars(arProperty.NAME),
							field = BX.message('SOA_FIELD') + ' "' + name + '"',
							re;

						if (arProperty.REQUIRED == 'Y' && value.length == 0){
							errors.push(field + ' ' + BX.message('SOA_REQUIRED'));
						}

						if(arProperty.IS_PHONE == 'Y' && value.length > 0){
							function regexpPhone(value, element, regexp){
								var re = new RegExp( regexp );
								return re.test(value);
							}

							var validPhone = regexpPhone($(input).val(), $(input), arNextOptions['THEME']['VALIDATE_PHONE_MASK'].length ? arNextOptions['THEME']['VALIDATE_PHONE_MASK'] : '.*');

							if(!validPhone){
								errors.push(field + ' ' +BX.message('JS_FORMAT_ORDER'));
							}
						}

						return errors;
					}

					BX.Sale.OrderAjaxComponent.getValidationDataPhone = function(arProperty, propContainer){
						var data = {}, inputs;
						switch (arProperty.TYPE)
						{
							case 'STRING':
								data.action = 'blur';
								data.func = BX.delegate(function(input, fieldName){
									return this.validatePhone(input, arProperty, fieldName);
								}, this);

								inputs = propContainer.querySelectorAll('input[type=tel]');
								if ($(inputs).length)
								{
									data.inputs = inputs;
									break;
								}
						}

						return data;
					};

					BX.Sale.OrderAjaxComponent.bindValidationPhone = function(id, propContainer)
					{
						if (!this.validation.properties || !this.validation.properties[id])
							return;

						var arProperty = this.validation.properties[id],
							data = this.getValidationDataPhone(arProperty, propContainer),
							i, k;

						if (data && data.inputs && data.action)
						{
							for (i = 0; i < $(data.inputs).length; i++)
							{
								if (BX.type.isElementNode(data.inputs[i])){
									BX.bind(data.inputs[i], data.action, BX.delegate(function(){
										this.isValidProperty(data);
									}, this));
								}
								else{
									for (k = 0; k < $(data.inputs[i]).length; k++)
										BX.bind(data.inputs[i][k], data.action, BX.delegate(function(){
											this.isValidProperty(data);
										}, this));
								}
							}
						}
					};

					BX.Sale.OrderAjaxComponent.isValidPropertiesBlock = function(excludeLocation)
					{
						if (!this.options.propertyValidation)
							return [];

						var props = this.orderBlockNode.querySelectorAll('.bx-soa-customer-field[data-property-id-row]'),
							propsErrors = [],
							id, propContainer, arProperty, data, i;

						for (i = 0; i < props.length; i++)
						{
							id = props[i].getAttribute('data-property-id-row');

							if (!!excludeLocation && this.locations[id])
								continue;

							propContainer = props[i].querySelector('.soa-property-container');
							if (propContainer)
							{
								arProperty = this.validation.properties[id];
								data = this.getValidationData(arProperty, propContainer);
								dataPhone = this.getValidationDataPhone(arProperty, propContainer);
								data = $.extend({}, data, dataPhone);

								propsErrors = propsErrors.concat(this.isValidProperty(data, true));
							}
						}

						return propsErrors;
					};


					// create input type=tel
					var input = $('input[autocomplete=tel]'),
						inputHTML = input[0].outerHTML,
						value = input.val(),
						newInput = input[0].outerHTML.replace('type="text"', 'type="tel" value="'+value+'"');

					if($(input).length < 2)
					{
						input.hide();
						$(newInput).insertAfter(input);
					}
					showPhoneMask('input[autocomplete=tel][type=tel]');

					// change value input type=text when change input type=tel
					$('input[autocomplete=tel][type=tel]').on('blur', function(){
						var $this = $(this);

						var value = $this.val();
						$this.parent().find('input[autocomplete=tel][type=text]').val(value);
					});

					BX.Sale.OrderAjaxComponent.bindValidationPhone(arPropertyPhone.ID, $('input[autocomplete=tel]').parent()[0]);
				}
			}

			if($('.bx-soa-cart-total').length){
				if(!$('.change_basket').length)
					$('.bx-soa-cart-total').prepend('<div class="change_basket">'+BX.message("BASKET_CHANGE_TITLE")+'<a href="'+arNextOptions["SITE_DIR"]+'basket/" class="change_link">'+BX.message("BASKET_CHANGE_LINK")+'</a></div>');

				if(typeof (BX.Sale.OrderAjaxComponent) == "object"){
					if(arNextOptions['COUNTERS']['USE_FULLORDER_GOALS'] !== 'N'){
						if(typeof BX.Sale.OrderAjaxComponent.reachgoalbegin === 'undefined'){
							BX.Sale.OrderAjaxComponent.reachgoalbegin = true;
							var eventdata = {goal: 'goal_order_begin'};
							BX.onCustomEvent('onCounterGoals', [eventdata])
						}
					}

					if(BX.Sale.OrderAjaxComponent.hasOwnProperty("params")){
						$('.bx-soa-cart-total .change_link').attr('href', BX.Sale.OrderAjaxComponent.params.PATH_TO_BASKET);
						if(arNextOptions["PRICES"]["MIN_PRICE"]){
							if(arNextOptions["PRICES"]["MIN_PRICE"]>Number(BX.Sale.OrderAjaxComponent.result.TOTAL.ORDER_PRICE)){
								$('<div class="fademask_ext"></div>').appendTo($('body'));
								location.href=BX.Sale.OrderAjaxComponent.params.PATH_TO_BASKET;
							}
						}
					}

					if($('.bx-soa-cart-total-line-total').length && arNextOptions["THEME"]["SHOW_LICENCE"] == "Y"){
						if(typeof(e) === 'undefined')
							BX.Sale.OrderAjaxComponent.state_licence = (arNextOptions['THEME']['LICENCE_CHECKED'] == 'Y' ? 'checked' : '');

						if(!$('.licence_block.filter').length){
							$('<div class="form"><div class="licence_block filter label_block"><label data-for="licenses_order" class="hidden error">'+BX.message('JS_REQUIRED_LICENSES')+'</label><input type="checkbox" name="licenses_order" required '+BX.Sale.OrderAjaxComponent.state_licence+' value="Y"><label data-for="licenses_order" class="license">'+BX.message('LICENSES_TEXT')+'</label></div></div>').insertBefore($('#bx-soa-orderSave'));

							$(document).on('click', '.bx-soa .licence_block label.license', function(){
								var id = $(this).data('for');
								$('.bx-soa .licence_block label.error').addClass('hidden');
								if(!$('input[name='+id+']').prop('checked')){
									$('input[name='+id+']').prop('checked', 'checked');
									BX.Sale.OrderAjaxComponent.state_licence = 'checked';
								}
								else{
									$('input[name='+id+']').prop('checked', '');
									BX.Sale.OrderAjaxComponent.state_licence = '';
								}
							})

							$(document).on('click', '.lic_condition a', function(){
								if(BX.hasClass(BX('bx-soa-order'), 'orderform--v1')){
									if(BX.Sale.OrderAjaxComponent.isValidForm())
									{
										BX.Sale.OrderAjaxComponent.animateScrollTo($('.licence_block')[0], 800, 50);
									}
								}
								else{
									var iCountErrors = BX.Sale.OrderAjaxComponent.isValidPropertiesBlock().length;
									if(!BX.Sale.OrderAjaxComponent.activeSectionId || !iCountErrors)
									{
										BX.Sale.OrderAjaxComponent.animateScrollTo($('.licence_block')[0], 800, 50);
									}
								}
							})
						}

						$('#bx-soa-orderSave, .bx-soa-cart-total-button-container').addClass('lic_condition');

						// update oreder auth form
						if($('#bx-soa-auth').length && !$('#bx-soa-auth .redisigned').length){
							// update input USER_LOGIN
							if($('input[name="USER_LOGIN"]').length){
								var $label = $('input[name="USER_LOGIN"]').closest('.bx-authform-formgroup-container').find('.bx-authform-label-container');
								if(!$label.find('.bx-authform-starrequired').length){
									$label.html($label.html() + '<span class="bx-authform-starrequired"> *</span>');
								}
							}

							// update input USER_PASSWORD
							if($('input[name="USER_PASSWORD"]').length){
								var $label = $('input[name="USER_PASSWORD"]').closest('.bx-authform-formgroup-container').find('.bx-authform-label-container');
								if(!$label.find('.bx-authform-starrequired').length){
									$label.html($label.html() + '<span class="bx-authform-starrequired"> *</span>');
								}
							}

							if($('input[name="USER_REMEMBER"]').length){
								var $label = $('input[name="USER_REMEMBER"]').attr('id', 'ORDER_AUTH_USER_REMEMBER').closest('label').attr('for', 'ORDER_AUTH_USER_REMEMBER');
								var html = $('input[name="USER_REMEMBER"]').attr('id', 'ORDER_AUTH_USER_REMEMBER').wrap('<div id="trem_"></div>').parent().html();
								$('#trem_').remove();
								$label.html($label.text());
								$(html).insertBefore($label).closest('.bx-authform-formgroup-container').addClass('filter');
								var html = $('#bx-soa-auth .bx-authform>a').addClass('pull-right').addClass('forgot').wrap('<div id="trem_"></div>').parent().html();
								$('#trem_').remove();
								$(html).insertBefore($label.closest('.checkbox'));
							}

							$('#bx-soa-auth .bx-soa-reg-block .btn').removeClass('btn-default').removeClass('btn-lg').addClass('transparent').text(BX.message('ORDER_REGISTER_BUTTON'));

							$('#bx-soa-auth').append('<div class="redisigned hidden></div>');
						}

						// update oreder register form
						if($('.bx-soa-section-content.reg').length && !$('.bx-soa-section-content.reg .redisigned').length){
							var bRebindRegSubmit = false;

							if(arNextOptions.THEME.LOGIN_EQUAL_EMAIL === 'Y'){
								bRebindRegSubmit = true;

								// update input NEW_LOGIN
								if($('input[name="NEW_LOGIN"]').length){
									$('input[name="NEW_LOGIN"]').closest('.bx-authform-formgroup-container').hide();
								}
							}

							if(arNextOptions.THEME.PERSONAL_ONEFIO === 'Y'){
								bRebindRegSubmit = true;

								// update input NEW_NAME
								if($('input[name="NEW_NAME"]').length){
									$('input[name="NEW_NAME"]').closest('.bx-authform-formgroup-container').find('.bx-authform-label-container').html(BX.message('ORDER_FIO_LABEL') + '<span class="bx-authform-starrequired"> *</span>');
								}

								// update input NEW_LAST_NAME
								if($('input[name="NEW_LAST_NAME"]').length){
									$('input[name="NEW_LAST_NAME"]').closest('.bx-authform-formgroup-container').hide();
									$('input[name="NEW_LAST_NAME"]').val(' ');
								}
							}

							if(bRebindRegSubmit){
								// bind new handler for submit button
								var $regSubmit = $('#do_register~input[type=submit]');
								if($regSubmit.length){
									BX.unbindAll($regSubmit[0]);
									$(document).on('click', '#do_register~input[type=submit]', function(e){
										e.preventDefault();
										e.stopImmediatePropagation();

										if(arNextOptions.THEME.LOGIN_EQUAL_EMAIL === 'Y'){
											var email = BX.findChild(BX('bx-soa-auth'), {attribute: {'name': 'NEW_EMAIL'}}, true, false);
											var login = BX.findChild(BX('bx-soa-auth'), {attribute: {'name': 'NEW_LOGIN'}}, true, false);

											if(login && email){
												login.value = email.value;
											}
										}

										BX('do_register').value = 'Y';
										BX.Sale.OrderAjaxComponent.sendRequest('showAuthForm');
									});
								}
							}

							// update captcha
							var $captcha = $('.bx-soa-section-content.reg').find('.bx-captcha');
							if($captcha.length){
								$captcha.addClass('captcha_image');
								$captcha.append('<div class="captcha_reload"></div>');
								$captcha.closest('.bx-authform-formgroup-container').addClass('captcha-row').find('input[name=captcha_word]').closest('.bx-authform-input-container').addClass('captcha_input');
							}

							// update input NEW_NAME && NEW_LAST_NAME
							if($('input[name=NEW_NAME]').length && $('input[name=NEW_LAST_NAME]').length && arNextOptions.THEME.PERSONAL_ONEFIO !== 'Y'){
								if(!$('input[name=NEW_NAME]').closest('.bx-authform-formgroup-container.col-md-6').length){
									$('input[name=NEW_NAME],input[name=NEW_LAST_NAME]').closest('.bx-authform-formgroup-container').addClass('col-md-6');
									var html = $('input[name=NEW_LAST_NAME]').closest('.bx-authform-formgroup-container').wrap('<div id="trem_"></div>').parent().html();
									$('#trem_').remove();
									$(html).insertAfter($('input[name=NEW_NAME]').closest('.bx-authform-formgroup-container').wrap('<div class="row"></div>'));
								}
							}

							// update input NEW_EMAIL && PHONE_NUMBER
							if($('input[name=NEW_EMAIL]').length && $('input[name=PHONE_NUMBER]').length){
								if(!$('input[name=PHONE_NUMBER]').closest('.bx-authform-formgroup-container.col-md-6').length){
									$('input[name=NEW_EMAIL],input[name=PHONE_NUMBER]').closest('.bx-authform-formgroup-container').addClass('col-md-6');
									var html = $('input[name=PHONE_NUMBER]').closest('.bx-authform-formgroup-container').wrap('<div id="trem_"></div>').parent().html();
									$('#trem_').remove();
									$(html).insertAfter($('input[name=NEW_EMAIL]').closest('.bx-authform-formgroup-container').wrap('<div class="row"></div>'));
								}
							}

							// update input NEW_PASSWORD && NEW_PASSWORD_CONFIRM
							if($('input[name=NEW_PASSWORD]').length && $('input[name=NEW_PASSWORD_CONFIRM]').length){
								if(!$('input[name=NEW_PASSWORD]').closest('.bx-authform-formgroup-container.col-md-6').length){
									$('input[name=NEW_PASSWORD],input[name=NEW_PASSWORD_CONFIRM]').closest('.bx-authform-formgroup-container').addClass('col-md-6');
									var html = $('input[name=NEW_PASSWORD_CONFIRM]').closest('.bx-authform-formgroup-container').wrap('<div id="trem_"></div>').parent().html();
									$('#trem_').remove();
									$(html).insertAfter($('input[name=NEW_PASSWORD]').closest('.bx-authform-formgroup-container').wrap('<div class="row"></div>'));
								}
							}

							// update input PHONE_NUMBER
							if($('input[name=PHONE_NUMBER]').length){
								var input = $('input[name=PHONE_NUMBER]'),
									inputHTML = input[0].outerHTML,
									value = input.val(),
									newInput = input[0].outerHTML.replace('type="text"', 'type="tel" value="'+value+'"');

								if($(input).length < 2){
									input.hide();
									$(newInput).insertAfter(input);

									showPhoneMask('input[name=PHONE_NUMBER][type=tel]');

									$('input[name=PHONE_NUMBER][type=tel]').on('blur', function(){
										var $this = $(this);
										var value = $this.val();
										$this.parent().find('input[name=PHONE_NUMBER][type=text]').val(value);
									});

									var $label = $('input[name=PHONE_NUMBER][type=tel]').closest('.bx-authform-formgroup-container').find('.bx-authform-label-container');
									$label.html(BX.message('ORDER_PHONE_LABEL') + ($label.find('.bx-authform-starrequired').length ? '<span class="bx-authform-starrequired"> *</span>' : ''));
								}
							}

							$('.bx-soa-section-content.reg').append('<div class="redisigned hidden></div>');
						}

						if(typeof (BX.Sale.OrderAjaxComponent.oldClickOrderSaveAction) === "undefined" && typeof (BX.Sale.OrderAjaxComponent.clickOrderSaveAction) !== 'undefined'){
							BX.Sale.OrderAjaxComponent.oldClickOrderSaveAction = BX.Sale.OrderAjaxComponent.clickOrderSaveAction;
							BX.Sale.OrderAjaxComponent.clickOrderSaveAction = function(event){
								if($('input[name="licenses_order"]').prop('checked')){
									$('.bx-soa .licence_block label.error').addClass('hidden');

									if (BX.Sale.OrderAjaxComponent.isValidForm())
									{
										if(typeof BX.Sale.OrderAjaxComponent.allowOrderSave == 'function')
											BX.Sale.OrderAjaxComponent.allowOrderSave();
										if(typeof BX.Sale.OrderAjaxComponent.doSaveAction == 'function')
											BX.Sale.OrderAjaxComponent.doSaveAction();
										else
											BX.Sale.OrderAjaxComponent.oldClickOrderSaveAction(event);
									}
								}
								else{
									$('.bx-soa .licence_block label.error').removeClass('hidden');
								}
							}
							if(BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector('.checkbox'))
							{
								if(typeof browser == 'object')
								{
									if('msie' in browser && browser.msie)
										$(BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector('.checkbox')).remove();
									else
										BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector('.checkbox').remove();
								}
							}
							BX.unbindAll(BX.Sale.OrderAjaxComponent.totalInfoBlockNode.querySelector('a.btn-order-save'));
							BX.unbindAll(BX.Sale.OrderAjaxComponent.mobileTotalBlockNode.querySelector('a.btn-order-save'));
							BX.unbindAll(BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector('a'));
							BX.bind(BX.Sale.OrderAjaxComponent.totalInfoBlockNode.querySelector('a.btn-order-save'), 'click', BX.proxy(BX.Sale.OrderAjaxComponent.clickOrderSaveAction, BX.Sale.OrderAjaxComponent));
							BX.bind(BX.Sale.OrderAjaxComponent.mobileTotalBlockNode.querySelector('a.btn-order-save'), 'click', BX.proxy(BX.Sale.OrderAjaxComponent.clickOrderSaveAction, BX.Sale.OrderAjaxComponent));
							BX.bind(BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector('a'), 'click', BX.proxy(BX.Sale.OrderAjaxComponent.clickOrderSaveAction, BX.Sale.OrderAjaxComponent));
						}
					}

					// fix hide total block
					$(window).scroll();

					if(checkCounters() && typeof (BX.Sale.OrderAjaxComponent.oldSaveOrder) === "undefined"){
						var saveFunc = typeof (BX.Sale.OrderAjaxComponent.saveOrder) !== 'undefined' ? 'saveOrder' : 'saveOrderWithJson';
						if(typeof (BX.Sale.OrderAjaxComponent[saveFunc]) !== 'undefined'){
							BX.Sale.OrderAjaxComponent.oldSaveOrder = BX.Sale.OrderAjaxComponent[saveFunc];
							BX.Sale.OrderAjaxComponent[saveFunc] = function(result){
								var res = BX.parseJSON(result);
								if (res && res.order){
									if (!res.order.SHOW_AUTH){
										if (res.order.REDIRECT_URL && res.order.REDIRECT_URL.length && (!res.order.ERROR || BX.util.object_keys(res.order.ERROR).length < 1)){
											if((arMatch = res.order.REDIRECT_URL.match(/ORDER_ID\=[^&=]*/g)) && arMatch.length && (_id = arMatch[0].replace(/ORDER_ID\=/g, '', arMatch[0]))){
												$.ajax({
													url:arNextOptions['SITE_DIR']+"ajax/check_order.php",
													dataType: "json",
													type: "POST",
													data: { "ID": _id },
													success: function(id){
														if(parseInt(id)){
															purchaseCounter(parseInt(id), BX.message('FULL_ORDER'), function(d){
																if(typeof localStorage !== 'undefined' && typeof d === 'object'){
																	localStorage.setItem('gtm_e_' + _id, JSON.stringify(d));
																}
																BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
															});
														}
														else{
															BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
														}
													},
													error: function(){
														BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
													}
												})
											}
											else{
												BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
											}
										}
										else{
											BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
										}
									}
									else{
										BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
									}
								}
								else{
									BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
								}
							}
						}
					}

					if($('#bx-soa-order-form .captcha-row').length){
						if(window.asproRecaptcha && window.asproRecaptcha.key && window.asproRecaptcha.params.recaptchaSize == 'invisible'){
							$('#bx-soa-order-form .captcha-row').addClass('invisible');
							if(asproRecaptcha.params.recaptchaLogoShow === 'n'){
								$('#bx-soa-order-form .captcha-row').addClass('logo_captcha_n');
							}
						}
					}

					if($('#bx-soa-order-form .captcha-row.invisible').length){
						if(typeof (BX.Sale.OrderAjaxComponent.oldSendRequest) === "undefined" && typeof (BX.Sale.OrderAjaxComponent.sendRequest) !== 'undefined'){
							var tmpAction, tmpActionData;
							BX.Sale.OrderAjaxComponent.oldSendRequest = BX.Sale.OrderAjaxComponent.sendRequest;
							BX.Sale.OrderAjaxComponent.sendRequest = function(action, actionData){
								var bSend = true;

								if($('#bx-soa-order-form .captcha-row.invisible').length){
									if(window.renderRecaptchaById && window.asproRecaptcha && window.asproRecaptcha.key){
										if(window.asproRecaptcha.params.recaptchaSize == 'invisible'){
											var form = BX('bx-soa-order-form');
											if($(form).find('.g-recaptcha').length){
												if($(form).find('.g-recaptcha-response').val()){
													bSend = true;
												}
												else{
													if(typeof grecaptcha != 'undefined'){
														grecaptcha.execute($(form).find('.g-recaptcha').data('widgetid'));
														bSend = false;
													}
													else{
														bSend = false;
													}
												}
											}
										}
									}
								}

								if(bSend){
									BX.Sale.OrderAjaxComponent.oldSendRequest(action, actionData);
								}
								else{
									tmpAction = action;
									tmpActionData = actionData;
								}
							}

							$(document).on('submit', '#bx-soa-order-form', function(e){
								e.preventDefault();

								if(typeof tmpAction !== 'undefined' || typeof tmpActionData !== 'undefined'){
									BX.Sale.OrderAjaxComponent.sendRequest(tmpAction, tmpActionData);
									tmpAction = undefined;
									tmpActionData = undefined;
								}
							});
						}
					}
				}

				$('.bx-ui-sls-quick-locations.quick-locations').on('click', function(){
					$(this).siblings().removeClass('active');
					$(this).addClass('active');
				})
			}
		}
	}
}

if(!funcDefined('basketActions')){
	basketActions = function(){
		if(arNextOptions["PAGES"]["BASKET_PAGE"]){
			checkMinPrice();

			//remove4Cart
			if(typeof(BX.Sale) !== 'undefined' && typeof(BX.Sale) === 'object')
			{
				if(typeof(BX.Sale.BasketComponent) !== 'undefined' && typeof(BX.Sale.BasketComponent) === 'object')
				{
					$(document).on('click', '.basket-item-actions-remove', function(){
						var basketID = $(this).closest('.basket-items-list-item-container').data('id');
						delFromBasketCounter(BX.Sale.BasketComponent.items[basketID].PRODUCT_ID);
					})
				}
			}

			if(location.hash)
			{
				var hash = location.hash.substring(1);
				if($('#basket_toolbar_button_'+hash).length)
					$('#basket_toolbar_button_'+hash).trigger('click');

				if($('.basket-items-list-header-filter a[data-filter="'+hash+'"]').length)
					$('.basket-items-list-header-filter a[data-filter="'+hash+'"]')[0].click();
			}

			$('.bx_sort_container').append('<div class="top_control basket_sort"><span style="opacity:0;" class="delete_all btn btn-default white white-bg grey remove_all_basket">'+BX.message("BASKET_CLEAR_ALL_BUTTON")+'</span></div>');
			if($('.basket-items-list-header-filter').length)
			{
				$('.basket-items-list-header-filter').append('<div class="top_control basket_sort"><span style="opacity:1;" class="delete_all btn btn-default white white-bg grey remove_all_basket">'+BX.message("BASKET_CLEAR_ALL_BUTTON")+'</span></div>');

				var cur_index = $('.basket-items-list-header-filter > a.active').index();
				//fix delayed
				if(cur_index == 3)
					cur_index = 2;

				if($('.basket-items-list-header-filter > a.active').data('filter') == 'all')
					cur_index = 'all';

				$('.basket-items-list-header-filter .top_control .delete_all').data("type",cur_index);

				$('.basket-items-list-header-filter > a').on('click', function() {
					var index = $(this).index();

					//fix delayed
					if(index == 3)
						index = 2;

					if($(this).data('filter') == 'all')
						index = 'all';

					$('.basket-items-list-header-filter .top_control .delete_all').data("type", index);
				});
			}
			else
			{
				var cur_index = $('.bx_sort_container a.current').index();
				$('.bx_sort_container .top_control .delete_all').data("type",cur_index);
				if($('.bx_ordercart > div:eq('+cur_index+') table tbody tr td.item').length)
					$('.bx_sort_container .top_control .delete_all').css('opacity',1);

				$('.bx_ordercart .bx_ordercart_coupon #coupon').wrap('<div class="input"></div>');

				$('.bx_sort_container > a').on('click', function() {
					var index = $(this).index();
					$('.bx_sort_container .top_control .delete_all').data("type", index);

					if($('.bx_ordercart > div:eq('+index+') table tbody tr td.item').length)
						$('.bx_sort_container .top_control .delete_all').css('opacity',1);
					else
						$('.bx_sort_container .top_control .delete_all').css('opacity',0);
				});
			}


			$('.basket_print').on('click', function() {
				 // window.open(location.pathname+"?print=Y",'_blank');
				 window.print();
			});

			$('.delete_all').on('click', function() {
				if(arNextOptions['COUNTERS']['USE_BASKET_GOALS'] !== 'N'){
					var eventdata = {goal: 'goal_basket_clear', params: {type: $(this).data('type')}};
					BX.onCustomEvent('onCounterGoals', [eventdata]);
				}
				$.post( arNextOptions['SITE_DIR']+"ajax/action_basket.php", "TYPE="+$(this).data('type')+"&CLEAR_ALL=Y", $.proxy(function( data ) {
					location.reload();
				}));
			});

			$('.bx_item_list_section .bx_catalog_item').sliceHeight({row:'.bx_item_list_slide', item:'.bx_catalog_item'});

			BX.addCustomEvent('onAjaxSuccess', function() {
				checkMinPrice();

				var errorText = $.trim($('#warning_message').text());
				$('#basket_items_list .error_text').remove();
				if (errorText != '') {
					$('#warning_message').hide().text('');
					$('#basket_items_list').prepend('<div class="error_text">' +errorText+ '</div>');
				}
			});
			$(document).on('click', '.bx_ordercart_order_pay_center .checkout, .basket-checkout-section-inner .basket-btn-checkout', function(){
				if(checkCounters('google')){
					checkoutCounter(1, 'start order');
				}
			})
		}
	}
}

if(!funcDefined('checkMinPrice')){
	checkMinPrice = function(){
		if(arNextOptions["PAGES"]["BASKET_PAGE"]){
			var summ_raw=0,
				summ=0;
			if($('#allSum_FORMATED').length)
			{
				summ_raw=$('#allSum_FORMATED').text().replace(/[^0-9\.,]/g,'');
				summ=parseFloat(summ_raw);
				if($('#basket_items').length)
				{
					var summ = 0;
					$('#basket_items tr').each(function(){
						if(typeof ($(this).data('item-price')) !== 'undefined' && $(this).data('item-price'))
							summ += $(this).data('item-price')*$(this).find('#QUANTITY_INPUT_'+$(this).attr('id')).val();
					})
				}
				if(!$('.catalog_back').length)
					$('.bx_ordercart_order_pay_center').prepend('<a href="'+arNextOptions["PAGES"]["CATALOG_PAGE_URL"]+'" class="catalog_back btn btn-default btn-lg white grey">'+BX.message("BASKET_CONTINUE_BUTTON")+'</a>');
			}

			if(arNextOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y")
				$('.basket-coupon-section').addClass('smallest');

			if(typeof BX.Sale !== "undefined")
			{
				if(typeof BX.Sale.BasketComponent !== "undefined" && typeof BX.Sale.BasketComponent.result !== "undefined")
					summ = BX.Sale.BasketComponent.result.allSum;
			}

			if(arNextOptions["PRICES"]["MIN_PRICE"]){
				if(arNextOptions["PRICES"]["MIN_PRICE"]>summ){

					if($('.oneclickbuy.fast_order').length)
						$('.oneclickbuy.fast_order').remove();

					if($('.basket-checkout-container').length)
					{
						if(!$('.icon_error_wrapper').length){
							$('.basket-checkout-block.basket-checkout-block-btn').html('<div class="icon_error_wrapper"><div class="icon_error_block">'+BX.message("MIN_ORDER_PRICE_TEXT").replace("#PRICE#", jsPriceFormat(arNextOptions["PRICES"]["MIN_PRICE"]))+'</div></div>');
						}
					}
					else
					{
						if(!$('.icon_error_wrapper').length  && typeof jsPriceFormat !== 'undefined'){
							$('.bx_ordercart_order_pay_center').prepend('<div class="icon_error_wrapper"><div class="icon_error_block">'+BX.message("MIN_ORDER_PRICE_TEXT").replace("#PRICE#", jsPriceFormat(arNextOptions["PRICES"]["MIN_PRICE"]))+'</div></div>');
						}
						if($('.bx_ordercart_order_pay .checkout').length)
							$('.bx_ordercart_order_pay .checkout').remove();
					}
				}else{
					if($('.icon_error_wrapper').length)
						$('.icon_error_wrapper').remove();

					if($('.basket-checkout-container').length)
					{
						if(!$('.oneclickbuy.fast_order').length && arNextOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y" && !$('.basket-btn-checkout.disabled').length)
							$('.basket-checkout-section-inner').append('<div class="fastorder"><span class="oneclickbuy btn btn-default btn-lg fast_order" onclick="oneClickBuyBasket()">'+BX.message("BASKET_QUICK_ORDER_BUTTON")+'</span></div>');
					}
					else
					{
						if($('.bx_ordercart_order_pay .checkout').length)
							$('.bx_ordercart .bx_ordercart_order_pay .checkout').css('opacity','1');
						else
							$('.bx_ordercart_order_pay_center').append('<a href="javascript:void(0)" onclick="checkOut();" class="checkout" style="opacity: 1;">'+BX.message("BASKET_ORDER_BUTTON")+'</a>');
						if(!$('.oneclickbuy.fast_order').length && arNextOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y")
							$('.bx_ordercart_order_pay_center').append('<span class="oneclickbuy btn btn-default btn-lg fast_order" onclick="oneClickBuyBasket()">'+BX.message("BASKET_QUICK_ORDER_BUTTON")+'</span>');
					}
				}
			}else{
				if($('.basket-checkout-container').length)
				{
					if(!$('.oneclickbuy.fast_order').length && arNextOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y" && !$('.basket-btn-checkout.disabled').length)
						$('.basket-checkout-section-inner').append('<div class="fastorder"><span class="oneclickbuy btn btn-default btn-lg fast_order" onclick="oneClickBuyBasket()">'+BX.message("BASKET_QUICK_ORDER_BUTTON")+'</span></div>');
				}
				else
				{
					$('.bx_ordercart .bx_ordercart_order_pay .checkout').css('opacity','1');
					if(!$('.oneclickbuy.fast_order').length && arNextOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y")
						$('.bx_ordercart_order_pay_center').append('<span class="oneclickbuy btn btn-default btn-lg fast_order" onclick="oneClickBuyBasket()">'+BX.message("BASKET_QUICK_ORDER_BUTTON")+'</span>');
				}
			}

			$('#basket-root .basket-checkout-container .basket-checkout-section .basket-checkout-block .basket-btn-checkout').addClass('white');
			$('#basket-root .basket-checkout-container').addClass('visible');
		}
	}
}

var isFrameDataReceived = false;
if (typeof window.frameCacheVars !== "undefined"){
	BX.addCustomEvent("onFrameDataReceived", function (json){
		initFull();

		CheckTopMenuPadding();
		CheckTopMenuOncePadding();
		CheckTopMenuDotted();

		CheckSearchWidth();

		if($('.logo-row.v2').length)
		{
			$(window).resize(); // need to check resize flexslider & menu
			setTimeout(function(){
				CheckTopMenuDotted();
			}, 100);
		}

		isFrameDataReceived = true;
	});
}else{
	$( document ).ready(initFull);
}

if(!funcDefined('setHeightBlockSlider')){
	setHeightBlockSlider = function(){
		$(document).find('.specials.tab_slider_wrapp .tabs_content > li.cur').equalize({children: '.item-title'});
		$(document).find('.specials.tab_slider_wrapp .tabs_content > li.cur').equalize({children: '.item_info'});
		$(document).find('.specials.tab_slider_wrapp .tabs_content > li.cur').equalize({children: '.catalog_item'});

		var sliderWidth = $(document).find('.specials.tab_slider_wrapp').outerWidth();

		var iCountTabs = $(document).find('.specials.tab_slider_wrapp .tabs_content > li.cur').length;

		if(iCountTabs <= 1)
		{
			$(document).find('.specials.tab_slider_wrapp .tabs_content > li.cur').css('height', '');

			var itemsButtonsHeight = 0;
			if($(document).find('.specials.tab_slider_wrapp .tabs_content .tab.cur .tabs_slider li .footer_button').length)
			{
				$(document).find('.specials.tab_slider_wrapp .tabs_content .tab.cur .tabs_slider li .footer_button').css('height', 'auto');
				itemsButtonsHeight = $(document).find('.specials.tab_slider_wrapp .tabs_content .tab.cur .tabs_slider li .footer_button').height();
				$(document).find('.specials.tab_slider_wrapp .tabs_content .tab.cur .tabs_slider li .footer_button').css('height', '');
			}
			var tabsContentUnhover = $(document).find('.specials.tab_slider_wrapp .tabs_content .tab.cur').height() * 1;
			var tabsContentHover = tabsContentUnhover + itemsButtonsHeight+50;
			$(document).find('.specials.tab_slider_wrapp .tabs_content .tab.cur').attr('data-unhover', tabsContentUnhover);
			$(document).find('.specials.tab_slider_wrapp .tabs_content .tab.cur').attr('data-hover', tabsContentHover);
			$(document).find('.specials.tab_slider_wrapp .tabs_content').height(tabsContentUnhover);
			$(document).find('.specials.tab_slider_wrapp .tabs_content .tab.cur .flex-viewport').height(tabsContentUnhover);
		}
		else
		{
			$(document).find('.specials.tab_slider_wrapp .tabs_content > li.cur').each(function(){
				var _th = $(this);
				_th.css('height', '');

				var itemsButtonsHeight = 0;
				if(_th.find('.tabs_slider li .footer_button').length)
				{
					_th.find('.tabs_slider li .footer_button').css('height', 'auto');
					itemsButtonsHeight = _th.find('.tabs_slider li .footer_button').height();
					_th.find('.tabs_slider li .footer_button').css('height', '');
				}

				var tabsContentUnhover = _th.height() * 1;
				var tabsContentHover = tabsContentUnhover + itemsButtonsHeight+50;
				_th.attr('data-unhover', tabsContentUnhover);
				_th.attr('data-hover', tabsContentHover);
				_th.parent().height(tabsContentUnhover);
				_th.find('.flex-viewport').height(tabsContentUnhover);

			})
		}
	}
}

if(!funcDefined('checkTopFilter')){
	checkTopFilter = function() {
		if($('.adaptive_filter').length && window.matchMedia('(max-width: 991px)').matches && !$("#mobilefilter").length){
                        var top_pos=$('.adaptive_filter').position().top;
                        $('.bx_filter.bx_filter_vertical').css({'top':top_pos+43});
		}
	}
}

if(!funcDefined('checkStickyFooter')){
	checkStickyFooter = function() {
		try{
			ignoreResize.push(true);
			$('#content').css('min-height', '');
			var contentTop = $('#content').offset().top;
			var contentBottom = contentTop + $('#content').outerHeight();
			var footerTop = 0;
			if($('footer').length)
				footerOffset = $('footer').offset().top;

			$('#content').css('min-height', $(window).height() - contentTop - (footerTop - contentBottom) - $('footer').outerHeight() + 'px');
			ignoreResize.pop();
		}
		catch(e){console.error(e);}
	}
}

/* EVENTS */
var timerResize = false, ignoreResize = [];
$(window).resize(function(){
	CheckPopupTop();
	/*if(!$('html.print').length)
		checkStickyFooter();*/

	// here immediate functions
	if(!ignoreResize.length){
		if(timerResize){
			clearTimeout(timerResize);
			timerResize = false;
		}
		timerResize = setTimeout(function(){
			// here delayed functions in event
			BX.onCustomEvent('onWindowResize', false);
		}, 50);
	}
});

var timerScroll = false, ignoreScroll = [], documentScrollTopLast = $(document).scrollTop(), startScroll = 0;
$(window).scroll(function(){
	CheckPopupTop();
	// here immediate functions
	documentScrollTopLast = $(document).scrollTop();
	SetFixedAskBlock();
	/*if($('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheader').length && window.matchMedia('(max-width: 991px)').matches)
	{
		if(documentScrollTopLast > startScroll)
			$('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheader').removeClass('fixed');
		else
			$('.wrapper1.mfixed_Y.mfixed_view_scroll_top #mobileheader').addClass('fixed');
		startScroll = documentScrollTopLast;
	}*/

	if(!ignoreScroll.length){
		if(timerScroll){
			clearTimeout(timerScroll);
			timerScroll = false;
		}
		timerScroll = setTimeout(function(){
			// here delayed functions in event
			BX.onCustomEvent('onWindowScroll', false);
		}, 50);
	}
});

var timerLazyLoad = false;
var lastLazyLoaded = [];
$(document).on('lazyloaded', function(e){
	if(timerLazyLoad){
		clearTimeout(timerLazyLoad);
		timerLazyLoad = false;
	}

	lastLazyLoaded.push(e.target);
	timerLazyLoad = setTimeout(function(){
		// here delayed functions in event
		BX.onCustomEvent('onLazyLoaded', [lastLazyLoaded]);
		lastLazyLoaded = [];
	}, 100);
});

BX.addCustomEvent('onWindowResize', function(eventdata){
	try{
		ignoreResize.push(true);

		CheckTopMenuPadding();
		CheckTopMenuOncePadding();
		CheckSearchWidth();

		CheckTabActive();
		CheckTopMenuFullCatalogSubmenu();
		CheckHeaderFixedMenu();

		CheckTopMenuDotted();
		if($('nav.mega-menu.sliced').length)
			$('nav.mega-menu.sliced').removeClass('initied');

		CheckTopVisibleMenu();

		checkScrollToTop();
		CheckObjectsSizes();

		CheckFlexSlider();
		initSly();

		checkMobilePhone();
		checkTopFilter();
		checkMobileFilter();

		if(window.matchMedia('(min-width: 767px)').matches)
			$('.wrapper_middle_menu.wrap_menu').removeClass('mobile');

		if(window.matchMedia('(max-width: 767px)').matches)
			$('.wrapper_middle_menu.wrap_menu').addClass('mobile');

		if($("#basket_form").length && $(window).outerWidth()<=600){
			$("#basket_form .tabs_content.basket > li.cur td").each(function() { $(this).css("width","");});
		}

		$(".bx_filter_section .bx_filter_select_container").each(function(){
			var prop_id=$(this).closest('.bx_filter_parameters_box').attr('data-property_id');
			if($('#smartFilterDropDown'+prop_id).length){
				$('#smartFilterDropDown'+prop_id).css("max-width", $(this).width());
			}
		})
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});

BX.addCustomEvent('onWindowScroll', function(eventdata){
	try{
		ignoreScroll.push(true);
	}
	catch(e){}
	finally{
		ignoreScroll.pop();
	}
});

BX.addCustomEvent('onSlideInit', function(eventdata) {
	try{
		ignoreResize.push(true);
		if(eventdata){
			var slider = eventdata.slider;
			if(slider){
				if(slider.hasClass('small-gallery'))
					$(window).resize();
				// add classes .curent & .shown to slide
				slider.find('.item').removeClass('current');
				var curSlide = slider.find('.item.flex-active-slide'),
					curSlideId = curSlide.attr('id'),
					nav = slider.find('.flex-direction-nav');

				curSlide.addClass('current');

				slider.find('.visible').css('opacity', '1');

				if(curSlide.hasClass('shown')){
					slider.find('.item.clone[id=' + curSlideId + '_clone]').addClass('shown');
				}

				curSlide.addClass('shown');
			}
		}
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});

BX.addCustomEvent('onCounterGoals', function(eventdata){
	if(arNextOptions['THEME']['YA_GOALS'] == 'Y' && arNextOptions['THEME']['YA_COUNTER_ID'])
	{
		var idCounter = arNextOptions['THEME']['YA_COUNTER_ID'];
		idCounter = parseInt(idCounter);

		if(typeof eventdata != 'object')
			eventdata = {goal: 'undefined'};

		if(typeof eventdata.goal != 'string')
			eventdata.goal = 'undefined';

		if(idCounter)
		{
			try
			{
				waitCounter(idCounter, 50, function(){
					var obCounter = window['yaCounter' + idCounter];
					if(typeof obCounter == 'object'){
						obCounter.reachGoal(eventdata.goal);
					}
				});
			}
			catch(e)
			{
				console.error(e)
			}
		}
		else
		{
			console.info('Bad counter id!', idCounter);
		}
	}
})

var onCaptchaVerifyinvisible = function(response){
	$('.g-recaptcha:last').each(function(){
		var id = $(this).attr('data-widgetid');
		if(typeof(id) !== 'undefined' && response){
			if(!$(this).closest('form').find('.g-recaptcha-response').val())
				$(this).closest('form').find('.g-recaptcha-response').val(response)
			if($('iframe[src*=recaptcha]').length)
			{
				$('iframe[src*=recaptcha]').each(function(){
					var block = $(this).parent().parent();
					if(!block.hasClass('grecaptcha-badge'))
						block.css('width', '100%');
				})
			}
			$(this).closest('form').submit();
		}
	})
}

var onCaptchaVerifynormal = function(response){
	$('.g-recaptcha').each(function(){
		var id = $(this).attr('data-widgetid');
		if(typeof(id) !== 'undefined'){
			if(grecaptcha.getResponse(id) != ''){
				$(this).closest('form').find('.recaptcha').valid();
			}
		}
	});
}

BX.addCustomEvent('onSubmitForm', function(eventdata){
	try{
		if(!window.renderRecaptchaById || !window.asproRecaptcha || !window.asproRecaptcha.key)
		{
			eventdata.form.submit();
			$(eventdata.form).closest('.form').addClass('sending');
			return true;
		}

		if(window.asproRecaptcha.params.recaptchaSize == 'invisible' && $(eventdata.form).find('.g-recaptcha').length)
		{
			if($(eventdata.form).find('.g-recaptcha-response').val())
			{
				eventdata.form.submit();
				$(eventdata.form).closest('.form').addClass('sending');
				return true;
			}
			else
			{
				if(typeof grecaptcha != 'undefined'){
					grecaptcha.execute($(eventdata.form).find('.g-recaptcha').data('widgetid'));
				}
				else{
					return false;
				}
			}
		}
		else
		{
			eventdata.form.submit();
			$(eventdata.form).closest('.form').addClass('sending');
			return true;
		}
	}
	catch (e){
		console.error(e);
		return true;
	}
})

/*custom event for sku prices*/

/*BX.addCustomEvent('onAsproSkuSetPrice', function(eventdata){
	console.log(eventdata);
})*/

/*BX.addCustomEvent('onAsproSkuSetPriceMatrix', function(eventdata){
	console.log(eventdata);
})*/
