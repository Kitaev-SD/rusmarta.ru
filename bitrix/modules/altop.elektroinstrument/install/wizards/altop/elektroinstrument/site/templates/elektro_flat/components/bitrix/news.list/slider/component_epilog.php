<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$coeff = ($arParams['SLIDER_ASPECT_RATIO'] == '16_9'? 0.56: ($arParams['SLIDER_ASPECT_RATIO'] == '16_7'? 0.43: 0.30));
$delay = (isset($arParams['SLIDER_DELAY']) && !empty($arParams['SLIDER_DELAY'])? $arParams['SLIDER_DELAY']: 3000);
$autoplay = ($arParams['SLIDER_AUTOPLAY'] !== 'N'? 'true': 'false');

if($arResult['IN_VIDEO']) {
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.mb.YTPlayer.min.js");
}

$APPLICATION->AddHeadString('
	<script type="text/javascript">		
		$(function() {
			$(".anythingSlider").anythingSlider({
				"expand": true,
				"delay": '.$delay.',
				"easing": "easeInOutExpo",			
				"buildStartStop": false,
				"forwardText": "<i class=\'fa fa-chevron-right\'></i>",
				"backText": "<i class=\'fa fa-chevron-left\'></i>",
				"hashTags": false,
				"autoPlay": '.$autoplay.',
				"autoPlayLocked": '.$autoplay.',
				"resizeContents": true,
				"onInitialized": function(e, slider) {
					if(!!slider.$currentPage.data("prop") || (!!slider.$el.find("li").data("prop") && slider.$items.length == 1)) {
						var prop = slider.$currentPage.data("prop")? eval("("+slider.$currentPage.data("prop")+")"): eval("("+slider.$el.find("li").data("prop")+")");
						
						if(prop.autoPlaySlide) slider.startStop();
						
						creatYTVideo(prop.idVideo, slider, prop.autoPlay, prop.autoPlaySlide);
					} else 
						toggleControlsSlider(slider, true);
				},
				"onSlideInit": function(e, slider) {
					toggleControlsSlider(slider);
					
					if(!!slider.$currentPage.data("prop")) {
						var prop = eval("("+slider.$currentPage.data("prop")+")");
						
						if($("#"+prop.idVideo).hasClass("mb_YTPlayer") && !!$("#"+prop.idVideo+" > div.mbYTP_wrapper > iframe").length)
							$("#"+prop.idVideo).YTPPause();
					}
				},
				"onSlideComplete": function(slider) {
					if(!!slider.$currentPage.data("prop")) {
						var prop = eval("("+slider.$currentPage.data("prop")+")");
						
						if(prop.autoPlaySlide) slider.startStop();
						
						if(!$("#"+prop.idVideo).hasClass("mb_YTPlayer")) 
							creatYTVideo(prop.idVideo, slider, prop.autoPlay, prop.autoPlaySlide);
						else 
							toggleControlsSlider(slider, true);
					} else {
						toggleControlsSlider(slider, true);
					}
				}
			});
			
			$(window).resize(function () {
				currentWidth = $(".content-wrapper").children(".center").width();
				if(currentWidth < "768") {
					$(".anythingContainer").css({
						"height": currentWidth * '.$coeff.' + "px"
					});
				} else {
					$(".anythingContainer").removeAttr("style");
				}
			});
			$(window).resize();
		});
		
		function toggleControlsSlider(slider, flag) {
			var flag = flag || false;
			var pointerEvents;
			
			if(flag)
				pointerEvents = "auto";
			else
				pointerEvents = "none";
			
			$(slider.$back[0]).css("pointer-events", pointerEvents);
			$(slider.$controls[0]).css("pointer-events", pointerEvents);
			$(slider.$forward[0]).css("pointer-events", pointerEvents);
		}
		
		function hideShowControlsVideo(contr1, contr2) {
			contr1.hide();
			contr2.show();
		}
		
		function creatYTVideo(idVideo, slider, autoPlay, autoPlaySlide) {
			var slideVideo = $("#"+idVideo).YTPlayer({
				onReady: function(e) {
					$(slideVideo).children(".mbYTP_wrapper").css({opacity: 1});
					toggleControlsSlider(slider, true);
				
					if(!autoPlay) hideShowControlsVideo($("#"+idVideo+"_load"), $("#"+idVideo+"_play"));
				
					slideVideo.on("YTPPlay", function(e) {
						hideShowControlsVideo($("#"+idVideo+"_play"), $("#"+idVideo+"_vob"));
					
						if(autoPlaySlide) slider.startStop();
					});
				
					slideVideo.on("YTPPause", function(e) {
						hideShowControlsVideo($("#"+idVideo+"_vob"), $("#"+idVideo+"_play"));
					
						if(autoPlaySlide && document.hasFocus()) slider.startStop(true);
					});
				
					slideVideo.on("YTPStart", function(e) {
						hideShowControlsVideo($("#"+idVideo+"_play"), $("#"+idVideo+"_vob"));
					
						if(autoPlaySlide) slider.startStop();
					});
				
					slideVideo.on("YTPMuted", function(e) {
						$("#"+idVideo+"_mute").hide();
						$("#"+idVideo+"_unmute").show();
					});
				
					slideVideo.on("YTPUnmuted", function(e) {
						$("#"+idVideo+"_mute").show();
						$("#"+idVideo+"_unmute").hide();
					});
					
					slideVideo.on("YTPEnd", function(e) {
						hideShowControlsVideo($("#"+idVideo+"_vob"), $("#"+idVideo+"_play"));
						
						if(autoPlaySlide) slider.startStop(true);
					});
				}
			});
			if($.mbBrowser.mobile && !($.mbBrowser.mobile && ( "playsInline" in document.createElement( "video" )))) {
				$("#"+idVideo+"_load").hide();
				toggleControlsSlider(slider, true);
			}
		}
	</script>
', true);?>