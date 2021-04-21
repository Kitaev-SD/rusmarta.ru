$(document).ready(function(){
	if($('.table-elements .item.slice-item').length)
	{
		$('.item.slice-item .title').sliceHeight();
		$('.item.slice-item .image').sliceHeight({lineheight:-3});
		$('.table-elements .item.slice-item').sliceHeight();
	}
})