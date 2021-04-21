$(document).ready(function(){

	$("div.custom__select-current").click(function(){
		$(this).closest("div.custom__select").toggleClass("opening");
	});
	$("div.custom__select-content-option").click(function(){
		$(this).closest("div.custom__select").find("div.custom__select-current").text($(this).text()).attr("data-val", $(this).attr("data-val"));
		$(this).closest("div.custom__select").removeClass("opening");
	});

	$("ul.custom__radio li").click(function(){
		$(this).closest("ul").find("li").each(function(){
			$(this).removeClass("current__item");
		});
		$(this).addClass("current__item");
	});


	$("button.hdd___calc-order").click(function(){


		$("div.hdd___calc-content-item-error").empty();
		$("input").removeClass("error");

		if(($("input[name='kamera']").val() < 1) || ($("input[name='kamera']").val() > 32)){
			$("input[name='kamera']").closest("div.hdd___calc-content-item-row-item").find("div.hdd___calc-content-item-error").text("Зачение должно быть от 1 до 32");
			$("input[name='kamera']").addClass("error");
		}
		if(($("input[name='arhiv']").val() < 1) || ($("input[name='arhiv']").val() > 365)){
			$("input[name='arhiv']").closest("div.hdd___calc-content-item-row-item").find("div.hdd___calc-content-item-error").text("Зачение должно быть от 1 до 365");
			$("input[name='arhiv']").addClass("error");
		}

		var chekParametrics = 0;

		$("div.hdd___calc input").each(function(){
			if($(this).hasClass("error")){
				chekParametrics = chekParametrics + 1;
			}
		});

		if(chekParametrics == 0){
			hddCalc();
		}

	});



	function hddCalc(){

		var resolution,
			h264,
			daymode,
			fps,
			quantity,
			arhiv,
			result;

		resolution = parseFloat($("div.custom__select-current").attr("data-val"));
		h264 = parseFloat($("ul.h264 li.current__item").attr("data-val"));
		daymode = parseFloat($("ul.daymode li.current__item").attr("data-val"));
		fps = parseFloat($("ul.fps li.current__item").attr("data-val"));
		quantity = parseFloat($("input[name='kamera']").val());
		arhiv = parseFloat($("input[name='arhiv']").val());

		result = resolution*fps*60*60/1024/1024/1024*daymode*quantity*arhiv/h264;

		$("div.hdd___calc-order-result").text("Требуемый объем жесткого диска:= " + result.toFixed(2) + " Тб").show();


	};

});