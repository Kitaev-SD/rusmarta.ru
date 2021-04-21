$(document).ready(function(){
	if(typeof obDataSubscribe !== "undefined")
	{
		$(".s_"+obDataSubscribe+" form.sform").validate({
			rules:{ "sf_EMAIL": {email: true} }
		});
	}
})