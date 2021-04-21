$(document).ready(function(){
	if($('.item.slice-item').length)
	{
		$('.item.slice-item .title').sliceHeight();
	}
})
BX.addCustomEvent('onCompleteAction', function(eventdata){
	if(eventdata.action === 'ajaxContentLoaded')
	{
		$('.item.slice-item .title').sliceHeight();
	}
});