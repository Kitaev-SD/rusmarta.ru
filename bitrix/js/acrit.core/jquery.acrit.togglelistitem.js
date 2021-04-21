/**
 *	Hide/show for <optgroup> and <option> for Safari in MacOS
 */
if($.fn && !$.fn.acritExpToggleListItem){
	$.fn.acritExpToggleListItem = function(flag){
		acritExpDetectSafari();
		this.each(function(){
			if(window.acritExpSafari){
				if(flag){
					if($(this).parent().is('span')){
						$(this).unwrap();
					}
				}
				else{
					if(!$(this).parent().is('span')){
						$(this).removeAttr('selected').wrap('<span style="display:none"/>');
					}
				}
			}
			else{
				if(flag){
					$(this).show();
				}
				else{
					$(this).hide();
				}
			}
		});
		if(window.acritExpSafari){
			this.closest('select').scrollTop(0);
		}
		return this;
	};
}