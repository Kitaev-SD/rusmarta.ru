$(document).ready(function(){
	$("form.sform_footer").validate({
		rules:{ "sf_EMAIL": {email: true} }
	});
})