$(document).ready(function(){
	if($('.item.slice-item').length)
	{
		$('.item.slice-item .title').sliceHeight();
		$('.item.slice-item').sliceHeight();
	}
})