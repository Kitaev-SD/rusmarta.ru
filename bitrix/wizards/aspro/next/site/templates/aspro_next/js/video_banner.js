var players = {};

function pauseMainBanner(){
	$('.top_slider_wrapp .flexslider').flexslider('pause');
}

function playMainBanner(){
	$('.top_slider_wrapp .flexslider').flexslider('play');
}

function startMainBannerSlideVideo($slide){
	var slideActiveIndex = $slide.attr('data-slide_index')
	var $slides = $slide.closest('.slides').find('.box[data-slide_index="'+ slideActiveIndex +'"]') // current slide & cloned
	var videoSource = $slide.attr('data-video_source')
	if(videoSource){
		$slides.addClass('loading')
		pauseMainBanner()

		var $slider = $slide.closest('.flexslider')
		$slider.addClass('video_visible')
		var slidesIndexesWithVideo = $slider.data('slidesIndexesWithVideo')
		if(typeof slidesIndexesWithVideo === 'undefined'){
			slidesIndexesWithVideo = [];
		}
		slidesIndexesWithVideo.push(slideActiveIndex)
		$slider.data('slidesIndexesWithVideo', slidesIndexesWithVideo)

		var videoPlayerSrc = $slide.attr('data-video_src')
		var videoSoundDisabled = $slide.attr('data-video_disable_sound')
		var bVideoSoundDisabled = videoSoundDisabled == 1
		var videoLoop = $slide.attr('data-video_loop')
		var bVideoLoop = videoLoop == 1
		var videoCover = $slide.attr('data-video_cover')
		var bVideoCover = videoCover == 1 && !isMobile
		var videoUnderText = $slide.attr('data-video_under_text')
		var bVideoUnderText = videoUnderText == 1
		var videoPlayer = $slide.attr('data-video_player')
		var bVideoPlayerYoutube = videoPlayer === 'YOUTUBE'
		var bVideoPlayerVimeo = videoPlayer === 'VIMEO'
		var bVideoPlayerRutube = videoPlayer === 'RUTUBE'
		var bVideoPlayerHtml5 = videoPlayer === 'HTML5'

		if(videoPlayerSrc && !$slide.find('.video').length){
			var InitPlayer = function(){
				$slides.each(function(i, node){
					var $_slide = $(node);
					var videoID = getRandomInt(100, 1000);
					var bClone = $_slide.hasClass('clone'),
						tmp_class = $_slide.attr('id');
					if(!$_slide.find('.video.'+tmp_class).length)
					{
						if(bVideoPlayerYoutube){
							$_slide.prepend('<div class="wrapper_video"><iframe id="player_' + videoID + '" class="video ' + tmp_class + (bVideoCover ? ' cover' : '') + '" src="'+ videoPlayerSrc +'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="autoplay"></iframe></div>');
						}
						else if(bVideoPlayerVimeo){
							$_slide.prepend('<div class="wrapper_video"><iframe id="player_' + videoID + '" class="video ' + tmp_class + (bVideoCover ? ' cover' : '') + '" src="'+ videoPlayerSrc +'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="autoplay"></iframe></div>');
						}
						else if(bVideoPlayerRutube){
							videoPlayerSrc = videoPlayerSrc + '&playerid=' + videoID;
							$_slide.prepend('<div class="wrapper_video"><iframe id="player_' + videoID + '" class="video ' + tmp_class + (bVideoCover ? ' cover' : '') + '" src="'+ videoPlayerSrc +'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="autoplay"></iframe></div>');
						}
						else if(bVideoPlayerHtml5){
							$_slide.prepend('<div class="wrapper_video"><video autobuffer playsinline webkit-playsinline autoplay id="player_' + videoID + '" class="video ' + tmp_class + (bVideoCover ? ' cover' : '') + '"' + (bVideoLoop ? ' loop ' : '') + (bVideoSoundDisabled || bClone ? ' muted ' : '') + '><source src="'+ videoPlayerSrc +'" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\' /><p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video</p></iframe></div>');
						}
					}

					if(typeof(players) !== 'undefined' && players){
						players[videoID] = {
							id: 'player_' + videoID,
							mute: bVideoSoundDisabled || bClone,
							loop: bVideoLoop,
							cover: bVideoCover,
							videoPlayer: videoPlayer,
							slideIndex: slideActiveIndex,
							clone: bClone,
							playing: false
						};

						if(bVideoPlayerYoutube){
							window[players[videoID].id] = new YT.Player(
								players[videoID].id, {
									events: {
										'onReady': onYoutubePlayerReady,
										'onStateChange': onYoutubePlayerStateChange
									}
								}
							);
						}
						else if(bVideoPlayerVimeo){
						    window[players[videoID].id] = new Vimeo.Player(document.getElementById(players[videoID].id), {autopause: false, byline: false, loop: false, title: false});
						    window[players[videoID].id].on('loaded', onVimeoPlayerReady)
						    window[players[videoID].id].on('play', onVimeoPlayerStateChange)
						    window[players[videoID].id].on('pause', onVimeoPlayerStateChange)
						    window[players[videoID].id].on('ended', onVimeoPlayerStateChange)
						}
						else if(bVideoPlayerRutube){
							document.getElementById(players[videoID].id).onload = function(e){
								var videoID = this.id.replace('player_', '')
								players[videoID].contentWindow = this.contentWindow
								onRutubePlayerReady(videoID)
							}
						}
						else if(bVideoPlayerHtml5){
							document.getElementById(players[videoID].id).addEventListener('loadeddata', onHtml5PlayerReady)
							document.getElementById(players[videoID].id).addEventListener('play', onHtml5PlayerStateChange)
							document.getElementById(players[videoID].id).addEventListener('pause', onHtml5PlayerStateChange)
							document.getElementById(players[videoID].id).addEventListener('ended', onHtml5PlayerStateChange)
						}
					}
				});
			}

			if(!bVideoPlayerHtml5){
				var obPlayerVariable = ''
				var fnPlayerVariable = ''
				if(typeof window['YoutubePlayerScriptLoaded'] === 'undefined'){
					window['YoutubePlayerScriptLoaded'] = false
				}
				if(typeof window['VimeoPlayerScriptLoaded'] === 'undefined'){
					window['VimeoPlayerScriptLoaded'] = false
				}
				if(typeof window['RutubePlayerListnersAdded'] === 'undefined'){
					window['RutubePlayerListnersAdded'] = false
				}

				// load script
				if(bVideoPlayerYoutube){
					obPlayerVariable = 'YT'
					fnPlayerVariable = 'Player'
					if(!window['YoutubePlayerScriptLoaded']){
						var script = document.createElement('script');
						script.src = "https://www.youtube.com/iframe_api";
						var firstScriptTag = document.getElementsByTagName('script')[0];
						firstScriptTag.parentNode.insertBefore(script, firstScriptTag);
						window['YoutubePlayerScriptLoaded'] = true;
					}
				}
				else if(bVideoPlayerVimeo){
					obPlayerVariable = 'Vimeo'
					if(!window['VimeoPlayerScriptLoaded']){
						var script = document.createElement('script');
						script.src = 'https://player.vimeo.com/api/player.js';
						(document.head || document.documentElement).appendChild(script);
						window['VimeoPlayerScriptLoaded'] = true
					}
				}
				else if(bVideoPlayerRutube){
					if(!window['RutubePlayerListnersAdded']){
						window.addEventListener('message', function(e){
							if(e.origin.indexOf('rutube.ru') !== -1){
							    var message = JSON.parse(e.data)
							    if(typeof message === 'object' && message){
							    	if(typeof message.type !== 'undefined' && message.type){
							    		var videoID = false

							    		for(var j in players){
									    	if(typeof players[j].contentWindow !== 'undefined'){
									    		if(players[j].contentWindow == e.source){
									    			videoID = j
									    			break
									    		}
									    	}
									    }

									    if(videoID){
										    switch (message.type) {
										        case 'player:changeState':
										            onRutubePlayerStateChange(videoID, message.data.state)
										            break
										        case 'player:currentTime':
										            onRutubePlayerCurrentTime(videoID, message.data.time)
										            break
										    }
										}
									}
							    }
							}
						});
					}
				}

				if(!obPlayerVariable.length){
					InitPlayer()
				}
				else{
					// wait player class
					if(typeof window[obPlayerVariable] === 'object'){
						if(!fnPlayerVariable.length || (fnPlayerVariable.length && typeof window[obPlayerVariable][fnPlayerVariable] === 'function')){

							InitPlayer()
						}
					}
					else{
						var waitPlayerInterval = setInterval(function(){
							if(typeof window[obPlayerVariable] === 'object'){
								if(!fnPlayerVariable.length || (fnPlayerVariable.length && typeof window[obPlayerVariable][fnPlayerVariable] === 'function')){

									clearInterval(waitPlayerInterval)

									InitPlayer()
								}
							}
						}, 50)
					}
				}

			}
			else{
				InitPlayer()
			}
		}
		else
		{
			// pause play video
			if(typeof(players) !== 'undefined' && players){
				for(var j in players){
					if(/*players[j].playing &&*/ !players[j].clone/* && (players[j].slideIndex != curSlideIndex)*/){
						if((typeof window[players[j].id] == 'object')){
							if(players[j].playing)
							{
								if(players[j].videoPlayer === 'YOUTUBE'){
									window[players[j].id].pauseVideo()
								}
								else if(players[j].videoPlayer === 'VIMEO'){
									window[players[j].id].pause()
								}
								else if(players[j].videoPlayer === 'RUTUBE'){
									document.getElementById(players[j].id).contentWindow.postMessage(JSON.stringify({
									    type: 'player:pause',
									    data: {}
									}), '*')
								}
								else if(players[j].videoPlayer === 'HTML5'){
									document.getElementById(players[j].id).pause()
								}
							}
							else if(players[j].slideIndex == slideActiveIndex)
							{
								if(players[j].videoPlayer === 'YOUTUBE'){
									window[players[j].id].playVideo()
								}
								else if(players[j].videoPlayer === 'VIMEO'){
									window[players[j].id].play()
								}
								else if(players[j].videoPlayer === 'RUTUBE'){
									document.getElementById(players[j].id).contentWindow.postMessage(JSON.stringify({
									    type: 'player:play',
									    data: {}
									}), '*')
								}
								else if(players[j].videoPlayer === 'HTML5'){
									document.getElementById(players[j].id).play()
								}
							}
						}
					}
				}
			}
		}
	}
}

var CoverPlayerHtml = function(curSlideIndex){
	var $videoCover = $('.video.cover');

	if($videoCover.length){
		//setTimeout(function(){
			var currenSlide = $('.box[data-slide_index="'+curSlideIndex+'"]');

			var bannersHeight = $('.top_slider_wrapp').height();
			var bannersWidth = $('.top_slider_wrapp').width();
			var videoHeight = $('.top_slider_wrapp video').outerHeight();
			var videoWidth = $('.top_slider_wrapp video').outerWidth();

			if(bannersHeight >= videoHeight && videoWidth >= bannersWidth){
				currenSlide.find('video.cover').height(bannersHeight+(jQuery.browser.mobile ? 30 : 0)).width('auto');

				if(currenSlide.hasClass('wvideo'))
					currenSlide.css('background-position-x', 'auto');

			}
			else if(bannersHeight < videoHeight && videoWidth < bannersWidth){
				currenSlide.find('video.cover').width(bannersWidth).height('auto');

				if(currenSlide.hasClass('wvideo'))
					currenSlide.css('background-position-y', 'auto');
			}

			//if(window.matchMedia('(min-width:992px)').matches){
				//setTimeout(function(){
					if(currenSlide.find('video.cover').length)
					{
						currenSlide.find('video.cover').css('margin-top', -videoHeight/2);
						var bannerWidth = $('.top_slider_wrapp video').width();
						// currenSlide.find('video.cover').css('margin-left', -bannerWidth/2);
					}
				//}, 300);
			/*}
			else{
				$('.top_slider_wrapp video').css('margin-top', 0);
			}*/
			setTimeout(function(){
				$('.video.cover').css('visibility', 'visible');
			}, 1300);
		//}, 10);
	}
}

var CoverPlayer = function(){
	var $videoCover = $('.video.cover')
	if($videoCover.length){
		var bannersHeight = $('.top_slider_wrapp .wrapper_video').height()
		var bannersWidth = $('.top_slider_wrapp .wrapper_video').width()
		var windowWidth = $(window).width()
		var height = windowWidth * 9 / 16
		$videoCover.css({'height': height + 'px', 'margin-top': (height > bannersHeight ? (bannersHeight - height) / 2 : 0) + 'px'})
	}
}

function onYoutubePlayerReady(e) {
	var videoID = e.target.a.id.replace('player_', '')
	if(videoID){
		var mute = players[videoID].mute
		var cover = players[videoID].cover
    	var clone = players[videoID].clone

    	// mute sound
		if(mute || clone){
			window[players[videoID].id].mute()
		}

    	// cover video
		if(cover){
	    	CoverPlayer()
	    }

    	// not start clone video playing
    	if(clone){
    		setTimeout(function(){
				e.target.pauseVideo()
    		}, 100)
    	}
    	else{
		    // stop slider
			pauseMainBanner()
			e.target.playVideo();

		    // e.target.playVideo();
		    // e.target.playVideo();
    	}

    	// update slide class
		var $slide = $('#player_' + videoID).closest('.box')
		$slide.addClass('started')
		// $slide.removeClass('loading')
    }
}

function onYoutubePlayerStateChange(e){
	var videoID = e.target.a.id.replace('player_', '')
    if(videoID){
    	var clone = players[videoID].clone
		var loop = players[videoID].loop
    	var slideIndex = players[videoID].slideIndex
    	if(!clone){
			if(e.data === YT.PlayerState.PLAYING){
				players[videoID].playing = true

				$('#player_'+videoID).closest('.box').find('.wrapper_inner').addClass('loading');
				$('#player_'+videoID).closest('.box').find('.wrapper_inner .btn-video').addClass('loading');

				// stop slider
				pauseMainBanner()

				e.target.playVideo()
			}
			else if(e.data === YT.PlayerState.PAUSED){
		    	players[videoID].playing = false

		    	// sync time in cloned players & pause
	    		var time = Math.floor(window[players[videoID].id].getCurrentTime() * 10) / 10

				$('#player_'+videoID).closest('.box').find('.wrapper_inner').removeClass('loading');
				$('#player_'+videoID).closest('.box').find('.wrapper_inner .btn-video').removeClass('loading');

				window[players[videoID].id].seekTo(time, true)
				for(var j in players){
					if(players[j].slideIndex == slideIndex && players[j].clone){

						if('getCurrentTime' in window[players[j].id])
						{
							window[players[j].id].pauseVideo()
							window[players[j].id].seekTo(time, true)
						}
					}
				}
			}
			else if(e.data === YT.PlayerState.ENDED){
				players[videoID].playing = false
		    	if(loop){
		    		e.target.playVideo()
		    	}
		    	else{
		    		// play slider
					playMainBanner()
		    	}
			}
		}
	}
}

function onVimeoPlayerReady(e){
	var videoID = this.element.id.replace('player_', '')
	if(videoID){
		var mute = players[videoID].mute
		var cover = players[videoID].cover
    	var clone = players[videoID].clone

    	// mute sound
		if(mute || clone){
			window[players[videoID].id].setVolume(0)
		}

    	// cover video
		if(cover){
	    	CoverPlayer()
	    }

    	// not start clone video playing
    	if(clone){
    		setTimeout(function(){
				window[players[videoID].id].pause()
    		}, 100)
    	}
    	else{
		    // stop slider
			pauseMainBanner()

		    window[players[videoID].id].play()
    	}

    	// update slide class
		var $slide = $('#player_' + videoID).closest('.box')
		$slide.addClass('started')
		// $slide.removeClass('loading')
    }
}

function onVimeoPlayerStateChange(e){
	var videoID = this.element.id.replace('player_', '')
	if(videoID){
		var cover = players[videoID].cover
    	var clone = players[videoID].clone
		var loop = players[videoID].loop
    	var slideIndex = players[videoID].slideIndex

    	if(!clone){
    		window[players[videoID].id].getPaused().then(function(paused){
    			if(paused){
			    	players[videoID].playing = false

			    	$('#player_'+videoID).closest('.box').find('.wrapper_inner').removeClass('loading');
					$('#player_'+videoID).closest('.box').find('.wrapper_inner .btn-video').removeClass('loading');

			    	// sync time in cloned players & pause
			    	window[players[videoID].id].getCurrentTime().then(function(seconds){
			    		var time = Math.floor(seconds * 10) / 10
			    		window[players[videoID].id].setCurrentTime(time).then(function(seconds){
							for(var j in players){
								if(players[j].slideIndex == slideIndex && players[j].clone){
									window[players[j].id].pause()
									window[players[j].id].setCurrentTime(time).then(function(seconds){})
								}
							}
			    		})
			    	})
    			}
    			else{
    				$('#player_'+videoID).closest('.box').find('.wrapper_inner').addClass('loading');
					$('#player_'+videoID).closest('.box').find('.wrapper_inner .btn-video').addClass('loading');
		    		window[players[videoID].id].getEnded().then(function(ended){
		    			if(ended){
							players[videoID].playing = false
					    	if(loop){
					    		window[players[videoID].id].play()
					    	}
					    	else{
					    		// play slider
								playMainBanner()
					    	}
		    			}
		    			else{
		    				players[videoID].playing = true


		    				// stop slider
							pauseMainBanner()
		    			}
		    		})
    			}
    		})
		}
	}
}

function onRutubePlayerReady(videoID){
	if(videoID){
		var mute = players[videoID].mute
		var cover = players[videoID].cover
    	var clone = players[videoID].clone
    	var player = document.getElementById(players[videoID].id)

    	// mute sound
		if(mute || clone){
			player.contentWindow.postMessage(JSON.stringify({
			    type: 'player:mute',
			    data: {}
			}), '*')
		}

    	// cover video
		if(cover){
	    	CoverPlayer()
	    }

    	// not start clone video playing
    	if(clone){
    		setTimeout(function(){
				player.contentWindow.postMessage(JSON.stringify({
				    type: 'player:pause',
				    data: {}
				}), '*')
    		}, 100)
    	}
    	else{
		    // stop slider
			pauseMainBanner()

		    player.contentWindow.postMessage(JSON.stringify({
			    type: 'player:play',
			    data: {}
			}), '*')
    	}

    	// update slide class
		var $slide = $('#player_' + videoID).closest('.box')
		$slide.addClass('started')
		// $slide.removeClass('loading')
    }
}

function onRutubePlayerCurrentTime(videoID, time){
	if(videoID){
		players[videoID].time = time
	}
}

function onRutubePlayerStateChange(videoID, state){
	if(videoID){
    	var clone = players[videoID].clone
		var loop = players[videoID].loop
    	var slideIndex = players[videoID].slideIndex
    	var player = document.getElementById(players[videoID].id)

    	if(!clone){
			if(state === 'playing'){
				$('#'+videoID).closest('.box').find('.wrapper_inner').addClass('loading');
				$('#'+videoID).closest('.box').find('.wrapper_inner .btn-video').addClass('loading');

				players[videoID].playing = true

				// stop slider
				pauseMainBanner()
			}
			else if(state === 'paused'){
				$('#'+videoID).closest('.box').find('.wrapper_inner').removeClass('loading');
				$('#'+videoID).closest('.box').find('.wrapper_inner .btn-video').removeClass('loading');

		    	players[videoID].playing = false

		    	// sync time in cloned players & pause
	    		var time = Math.floor(players[videoID].time * 10) / 10
				player.contentWindow.postMessage(JSON.stringify({
				    type: 'player:setCurrentTime',
				    data: {time: time}
				}), '*')
				for(var j in players){
					if(players[j].slideIndex == slideIndex && players[j].clone){
						document.getElementById(players[j].id).contentWindow.postMessage(JSON.stringify({
						    type: 'player:pause',
						    data: {}
						}), '*')
						document.getElementById(players[j].id).contentWindow.postMessage(JSON.stringify({
						    type: 'player:setCurrentTime',
						    data: {time: time}
						}), '*')
					}
				}
			}
			else if(state === 'stopped'){
				$('#'+videoID).closest('.box').find('.wrapper_inner').removeClass('loading');
				$('#'+videoID).closest('.box').find('.wrapper_inner .btn-video').removeClass('loading');

				players[videoID].playing = false
		    	if(loop){
		    		player.contentWindow.postMessage(JSON.stringify({
					    type: 'player:play',
					    data: {}
					}), '*')
		    	}
		    	else{
		    		// play slider
					playMainBanner()
		    	}
			}
		}
	}
}

function onHtml5PlayerReady(e){
	var videoID = e.target.id.replace('player_', '')
	if(videoID){
		var mute = players[videoID].mute
		var cover = players[videoID].cover
    	var clone = players[videoID].clone

    	// mute sound
		if(mute || clone){
			$('#' + players[videoID].id).prop('muted', true);
		}

    	// cover video
		if(cover){
	    	CoverPlayer()
	    }

    	// not start clone video playing
    	if(clone){
    		e.target.pause()
    	}
    	else{
		    // stop slider
			pauseMainBanner()

		    e.target.play()
    	}

    	// update slide class
		var $slide = $('#player_' + videoID).closest('.box')
		$slide.addClass('started')
		// $slide.removeClass('loading')
    }
}

function onHtml5PlayerStateChange(e){
	var videoID = e.target.id.replace('player_', '')
	if(videoID){
    	var cover = players[videoID].cover
    	var clone = players[videoID].clone
		var loop = players[videoID].loop
    	var slideIndex = players[videoID].slideIndex

    	if(!clone){
			if(e.target.paused){
		    	players[videoID].playing = false

		    	$('#player_'+videoID).closest('.box').find('.wrapper_inner').removeClass('loading');
				$('#player_'+videoID).closest('.box').find('.wrapper_inner .btn-video').removeClass('loading');

		    	// sync time in cloned players & pause
	    		var time = Math.floor(e.target.currentTime * 10) / 10
				e.target.currentTime = time
				for(var j in players){
					if(players[j].slideIndex == slideIndex && players[j].clone){
						document.getElementById(players[j].id).pause()
						document.getElementById(players[j].id).currentTime = time
					}
				}
			}
			else if(e.target.ended){
				players[videoID].playing = false
		    	if(loop){
		    		$('#player_'+videoID).closest('.box').find('.wrapper_inner').addClass('loading');
					$('#player_'+videoID).closest('.box').find('.wrapper_inner .btn-video').addClass('loading');

		    		e.target.play()
		    	}
		    	else{
		    		// play slider
					playMainBanner()
		    	}
			}
			else{
				players[videoID].playing = true

				$('#player_'+videoID).closest('.box').find('.wrapper_inner').addClass('loading');
				$('#player_'+videoID).closest('.box').find('.wrapper_inner .btn-video').addClass('loading');
				// stop slider
				pauseMainBanner()
			}
		}
	}
}

waitYTPlayer = function(delay, callback){
	if((typeof YT !== "undefined") && YT && YT.Player)
	{
		if(typeof callback == 'function')
			callback();
	}
	else
	{
		setTimeout(function(){
			waitYTPlayer(delay, callback);
		}, delay);
	}
}

// click on HTML5 video
$(document).on('click', 'video.video', function(e){
	var videoID = e.target.id.replace('player_', '')
    if(videoID){
    	if(players[videoID].playing){
			e.target.pause()
    	}
    	else{
    		//e.target.play()
    	}
    }
})

// START VIDEO BUTTON
$(document).on('click', '.top_slider_wrapp .box .btn-video', function(e){
	e.stopPropagation();
	if(!$(this).hasClass('loading'))
	{
		$(this).addClass('loading');
		$(this).closest('.wrapper_inner').addClass('loading');
	}
	else
	{
		$(this).removeClass('loading');
		$(this).closest('.wrapper_inner').removeClass('loading');
	}

	startMainBannerSlideVideo($(this).closest('.box'));
});

// START VIDEO BY BANNER OR GOTO LINK
$(document).on('click', '.top_slider_wrapp .box .wrapper_inner', function(e){
	var wvideo = $(this).closest('.box').hasClass('wvideo');
	var wurl = $(this).closest('.box').hasClass('wurl');

	if($(e.target).hasClass('btn')){
		if(wvideo){
			if($(e.target).hasClass('btn-video')){
				e.stopPropagation();
				$(this).find('.btn-video').trigger('click');
			}
		}
	}
	else{
		if(wurl){
			var href = $(this).closest('.box').find('.target').attr('href');
			if(href.length){
				var target = $(this).closest('.box').find('.target').attr('target');
				if(typeof target === 'undefined' || target === '_self'){
					location.href = href;
				}
				else{
					window.open(href);
				}
			}
		}
		else{
			if(wvideo){
				e.stopPropagation();
				$(this).find('.btn-video').trigger('click');
			}
		}
	}
})


// SLICE HEIGHT OF TABLET TEXT WITH VIEW 2
$(document).ready(function(){
	if($('.top_slider_wrapp.view_2 .flexslider .slides > li .tablet_text .wrap').length){
		$('.top_slider_wrapp.view_2 .flexslider .slides > li .tablet_text .wrap').sliceHeight({
			row: '.top_slider_wrapp.view_2 .flexslider .slides',
			item: '.top_slider_wrapp.view_2 .flexslider .slides > li .tablet_text .wrap',
		});
	}
});

getRandomInt = function(min, max){
	return Math.floor(Math.random() * (max - min)) + min;
}

var ignoreResize = [];

BX.addCustomEvent('onWindowResize', function(eventdata) {
	try{
		CoverPlayer();
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});

BX.addCustomEvent('onSlideEnd', function(eventdata) {
	try{
		ignoreResize.push(true);
		if(eventdata){
			var slider = eventdata.slider;
			if(slider){
				setTimeout(function(){
					$('.banners-big.front .btn-video, .banners-big.front .box').removeClass('loading');
				}, 300);
			}
		}
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});

BX.addCustomEvent('onSlide', function(eventdata) {
	try{
		ignoreResize.push(true);
		if(eventdata){
			var slider = eventdata.slider;
			if(slider){
				var curSlide = slider.find('.box.flex-active-slide');
				var curSlideIndex = curSlide.attr('data-slide_index');

				if($('.top_slider_wrapp.view_2 .flexslider .slides > li .tablet_text .wrap').length){
					$('.top_slider_wrapp.view_2 .flexslider .slides > li .tablet_text .wrap').sliceHeight({
						resize: false,
						row: '.top_slider_wrapp.view_2 .flexslider .slides',
						item: '.top_slider_wrapp.view_2 .flexslider .slides > li .tablet_text .wrap',
					});
				}

				if(typeof(curSlideIndex) !== 'undefined' && curSlideIndex.length){
					var slidesIndexesWithVideo = slider.data('slidesIndexesWithVideo')
					if(typeof slidesIndexesWithVideo === 'undefined'){
						slidesIndexesWithVideo = [];
					}

					var bVideoVisible = slidesIndexesWithVideo.indexOf(curSlideIndex) != -1;
					if(bVideoVisible){
						slider.addClass('video_visible');
					}
					else{
						slider.removeClass('video_visible');
					}

					setTimeout(function(){
						CoverPlayerHtml(curSlideIndex);
					}, 200);

					// pause play video
					if(typeof(players) !== 'undefined' && players){
						for(var j in players){
							if(players[j].playing && !players[j].clone && (players[j].slideIndex != curSlideIndex)){
								if((typeof window[players[j].id] == 'object')){
									if(players[j].videoPlayer === 'YOUTUBE'){
										window[players[j].id].pauseVideo()
									}
									else if(players[j].videoPlayer === 'VIMEO'){
										window[players[j].id].pause()
									}
									else if(players[j].videoPlayer === 'RUTUBE'){
										document.getElementById(players[j].id).contentWindow.postMessage(JSON.stringify({
										    type: 'player:pause',
										    data: {}
										}), '*')
									}
									else if(players[j].videoPlayer === 'HTML5'){
										document.getElementById(players[j].id).pause()
									}
								}
							}
						}
					}
					// autoplay video
					var bVideoAutoPlay = curSlide.attr('data-video_autoplay') == 1
					if(bVideoAutoPlay){
						startMainBannerSlideVideo(curSlide)
					}
				}

				if(curSlide.find('video').length && !curSlide.find('.btn-video').length){
					var videoID = curSlide.find('video').attr('id');
					document.getElementById(videoID).play();
				}
			}
		}
	}
	catch(e){}
	finally{
		ignoreResize.pop();
	}
});