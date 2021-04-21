$(document).ready(function() {
	$.ajax({
		url: arNextOptions['SITE_DIR']+'include/mainpage/comp_instagramm.php',
		data: {'AJAX_REQUEST_INSTAGRAM': 'Y', 'SHOW_INSTAGRAM': arNextOptions['THEME']['INSTAGRAMM_INDEX']},
		type: 'POST',
		success: function(html){
			$('.instagram_ajax').html(html).addClass('loaded');
			InitFlexSlider();
			var eventdata = {action:'instagrammLoaded'};
			BX.onCustomEvent('onCompleteAction', [eventdata]);
		}
	});
});