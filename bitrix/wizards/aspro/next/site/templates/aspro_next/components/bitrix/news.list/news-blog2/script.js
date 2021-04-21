equalWideBlockHeight = function(){
	if($('.wide-block').length) //sliceheight for wide block
	{
		if(window.matchMedia('(min-width: 768px)').matches)
		{
			$('.wide-block').each(function(){
				$(this).css('height', '');
				var _this = $(this),
					parent_block = _this.closest('.items'),
					block_height = _this.actual('outerHeight', { includeMargin : true })-1,
					margin = parseInt($('.wide-block').css('margin-bottom')),
					equal_height = 0;

				if(parent_block.find('.col-item').length)
				{
					parent_block.find('.col-item .item').css('height', '');
					parent_block.find('.col-item').each(function(){
						equal_height += $(this).find('.item').actual('outerHeight', { includeMargin : true });
					})

					if(equal_height)
					{
						equal_height -= margin;
						if(equal_height >= block_height)
							_this.css('height', equal_height);
						else
						{
							equal_height += margin;
							var last_item = parent_block.find('.col-item:last-child .item');
							last_item.css('height', (last_item.actual('outerHeight') + (block_height - equal_height)));
						}
					}
				}
			})
		}
		else
		{
			$('.wide-block').css('height', '');
			$('.col-item .item').css('height', '');
		}
	}	
}
$(document).ready(function(){
	BX.onCustomEvent('onWindowResize', false);
	equalWideBlockHeight();
	$('.banners-small.blog img').on('load', function(){
		equalWideBlockHeight();
		$('.banners-small .item.normal-block').sliceHeight();
	});
})


BX.addCustomEvent('onWindowResize', function(eventdata) {
	try{
		ignoreResize.push(true);
		equalWideBlockHeight();
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});
BX.addCustomEvent('onCompleteAction', function(eventdata){
	if(eventdata.action === 'ajaxContentLoaded')
	{
		$(eventdata.content).find('.item').each(function(){
			$('.banners-small.blog img').on('load', function(){
				equalWideBlockHeight();
				$('.banners-small .item.normal-block').sliceHeight();
			});
		})
		setTimeout(function(){
			equalWideBlockHeight();
		}, 350);
	}
});
