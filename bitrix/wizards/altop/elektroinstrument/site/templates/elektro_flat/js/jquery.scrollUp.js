;(function($) {
	$.scrollUp = function (options) {
		var defaults = {
			scrollName: 'to_top', //Element ID
			topDistance: 0, //Distance from top before showing element (px)
			topSpeed: 200, //Speed back to top (ms)
			animation: 'fade', //Fade, slide, none
			animationInSpeed: 200, //Animation in speed (ms)
			animationOutSpeed: 200, //Animation out speed (ms)
			scrollText: '', //Text for element
			scrollImg: true, //Set true to use image
			activeOverlay: false //Set CSS color to display scrollUp active point, e.g '#00FFFF'
		};
		var o = $.extend({}, defaults, options),
			scrollId = '#' + o.scrollName;
		$('<a/>', {
			id: o.scrollName,
			href: '#top',
			title: o.scrollText
		}).appendTo('body');
		if(!o.scrollImg) {
			$(scrollId).text(o.scrollText);
		} else {
			$("<i class='fa fa-chevron-up'></i>").appendTo(scrollId);
		}
		$(scrollId).css({'display':'none','position': 'fixed','z-index': '500'});
		if(o.activeOverlay) {
			$("body").append("<div id='"+ o.scrollName +"-active'></div>");
			$(scrollId+"-active").css({ 'position': 'absolute', 'top': o.topDistance+'px', 'width': '100%', 'border-top': '1px dotted '+o.activeOverlay, 'z-index': '2147483647' });
		}
		$(window).scroll(function(){	
			switch (o.animation) {
				case "fade":
					$( ($(window).scrollTop() > o.topDistance) ? $(scrollId).fadeIn(o.animationInSpeed) : $(scrollId).fadeOut(o.animationOutSpeed) );
					break;
				case "slide":
					$( ($(window).scrollTop() > o.topDistance) ? $(scrollId).slideDown(o.animationInSpeed) : $(scrollId).slideUp(o.animationOutSpeed) );
					break;
				default:
					$( ($(window).scrollTop() > o.topDistance) ? $(scrollId).show(0) : $(scrollId).hide(0) );
			}
		});
		$(scrollId).click( function(event) {
			$('html, body').animate({scrollTop:0}, o.topSpeed);
			event.preventDefault();
		});
	};
})(jQuery);