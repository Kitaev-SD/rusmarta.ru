$(document).ready(function(){
	$('.landings_list3 .more').on('click', function(){
		var $this = $(this),
                    dataOpened = $this.data('opened'),
                    dataText = $this.data('text'),
                    thisText = $this.text(),
                    item = $this.closest('.landings_list3').find('.hidden_items'),
                    animationTime = 400;
		
		if(dataOpened == 'N'){
			item.fadeIn(animationTime);
			$this.addClass('opened').data('opened', 'Y');
		}
		else{
			item.fadeOut(animationTime);
			$this.removeClass('opened').data('opened', 'N');
		}
		
		$this.data('text', thisText).text(dataText);
	});
});