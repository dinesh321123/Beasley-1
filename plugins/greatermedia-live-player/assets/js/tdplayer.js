(function ($, window, undefined) {
	"use strict";

	var tech = getUrlVars()['tech'] || 'html5_flash';
	var aSyncCuePointFallback = getUrlVars()['aSyncCuePointFallback'] == 'false' ? false : true;

	var player; /* TD player instance */

	var playingCustomAudio = false; /* This will be true if we're playing a custom audio file vs. live stream */
	var customAudio = false; /* Will be an HTML5 Audio object, if we support it */
	var customArtist, customTrack, customHash; // So we can re-add these when resuming via live-player

	var adPlaying; /* boolean - Ad break currently playing */
	var currentTrackCuePoint; /* Current Track */
	var livePlaying; /* boolean - Live stream currently playing */
	var song; /* Song object that wraps NPE data */
	var companions; /* VAST companion banner object */
	var currentStation = ''; /* String - Current station played */

	var body = document.querySelector( 'body' );
	var tdContainer = document.getElementById('td_container');
	var livePlayer = document.getElementById('live-player');
	var liveStreamPlayer = document.querySelector('.live-stream__player');
	var playBtn = document.getElementById('playButton');
	var pauseBtn = document.getElementById('pauseButton');
	var resumeBtn= document.getElementById('resumeButton');
	var podcastPlayBtn = document.querySelector('.podcast__btn--play');
	var podcastPauseBtn = document.querySelector('.podcast__btn--pause');
	var podcastPlayer = document.querySelector('.podcast-player');
	var podcastPlaying = document.querySelector('.podcast__btn--play.playing');
	var listenNow = document.getElementById('live-stream__listen-now');
	var nowPlaying = document.getElementById('live-stream__now-playing');
	var listenLogin = document.getElementById('live-stream__login');
	var $trackInfo = $(document.getElementById('trackInfo'));
	var gigyaLogin = gmr.homeUrl + "members/login";
	var clearDebug = document.getElementById('clearDebug');
	var adBlockCheck = document.getElementById('ad-check');
	var adBlockClose = document.getElementById('close-adblock');
	var onAir = document.getElementById('on-air');
	var onAirTitle = document.querySelector('.on-air__title');
	var onAirShow = document.querySelector('.on-air__show');
	var streamStatus = document.getElementById('live-stream__status');
	var nowPlayingInfo = document.getElementById('nowPlaying');
	var trackInfo = document.getElementById('trackInfo');
	var liveStreamSelector = document.querySelector('.live-player__stream');

	/**
	 * global variables for event types to use in conjunction with `addEventHandler` function
	 * @type {string}
	 */
	var elemClick = 'click',
		elemLoad = 'load',
		elemScroll = 'scroll',
		elemResize = 'resize';

	/**
	 * function to detect if the current browser can use `addEventListener`, if not, use `attachEvent`
	 * this is a specific fix for IE8
	 *
	 * @param elem
	 * @param eventType
	 * @param handler
	 */
	function addEventHandler(elem,eventType,handler) {
		if (elem != null) {
			if (elem.addEventListener) {
				elem.addEventListener(eventType, handler, false);
			} else if (elem.attachEvent) {
				elem.attachEvent('on' + eventType, handler);
			}
		}
	}

	/**
	 * @todo remove the console log before beta
	 */
	window.tdPlayerApiReady = function () {
		console.log("--- TD Player API Loaded ---");
		initPlayer();
	};

	function calcTechPriority() {
		if (bowser.firefox) {
			return ['Flash'];
		} else if (bowser.safari) {
			return ['Html5'];
		} else if (bowser.chrome) {
			return ['Flash'];
		} else {
			return ['Html5', 'Flash'];
		}
	}

	function initPlayer() {
		var techPriority = calcTechPriority();
		console.log('+++ initPlayer - techPriority = ', techPriority);
		/*
		switch ( tech ) {
			case 'html5_flash' :
				techPriority = ['Html5', 'Flash'];
				break;
			case 'flash' :
				techPriority = ['Flash'];
				break;
			case 'html5' :
				techPriority = ['Html5'];
				break;
			case 'flash_html5' :
			default :
				techPriority = ['Flash', 'Html5'];
				break;

		}
		*/

		/* TD player configuration object used to create player instance */
		var tdPlayerConfig = {
			coreModules: [
				{
					id: 'MediaPlayer',
					playerId: 'td_container',
					isDebug: true,
					techPriority: techPriority,
					timeShift: { // timeShifting is currently available on Flash only. Leaving for HTML5 future
						active: 0, /* 1 = active, 0 = inactive */
						max_listening_time: 35 /* If max_listening_time is undefined, the default value will be 30 minutes */
					},
					// set geoTargeting to false on devices in order to remove the daily geoTargeting in browser
					geoTargeting: {desktop: {isActive: false}, iOS: {isActive: false}, android: {isActive: false}},
					plugins: [ {id:"vastAd"} ]
				},
				{id: 'NowPlayingApi'},
				{id: 'Npe'},
				{id: 'PlayerWebAdmin'},
				{id: 'SyncBanners', elements:[{id:'td_synced_bigbox', width:300, height:250}] },
				{id: 'TargetSpot'}
			]
		};

		require(['tdapi/base/util/Companions'], function (Companions) {
				companions = new Companions();
			}
		);

		player = new TdPlayerApi(tdPlayerConfig);
		if (player.addEventListener) {
			player.addEventListener('player-ready', onPlayerReady);
			player.addEventListener('configuration-error', onConfigurationError);
			player.addEventListener('module-error', onModuleError);
		} else if (player.attachEvent) {
			player.attachEvent('player-ready', onPlayerReady);
			player.attachEvent('configuration-error', onConfigurationError);
			player.attachEvent('module-error', onModuleError);
		}
		player.loadModules();
	}

	/**
	 * load TD Player API asynchronously
	 */
	function loadIdSync(station) {
		var scriptTag = document.createElement('script');
		scriptTag.setAttribute("type", "text/javascript");
		scriptTag.setAttribute("src", "//playerservices.live.streamtheworld.com/api/idsync.js?station=" + station);
		document.getElementsByTagName('head')[0].appendChild(scriptTag);
	}


	function initControlsUi() {

		if (pauseBtn != null) {
			addEventHandler(pauseBtn,elemClick,pauseStream);
		}

		if (resumeBtn != null) {
			addEventHandler(resumeBtn,elemClick,resumeLiveStream);
		}

		if (clearDebug != null) {
			addEventHandler(clearDebug,elemClick,clearDebugInfo);
		}

		if (nowPlaying != null) {
			addEventHandler(nowPlaying,elemClick,stopStream);
		}

	}

	function setPlayingStyles() {
		if ( null === tdContainer ) {
			// gigya user is logged out, so everything is different ಠ_ಠ - Should we force login for inline audio as well??
			return;
		}

		tdContainer.classList.add('stream__active');
		if (true === playingCustomAudio && window.innerWidth <= 767) {
			playBtn.classList.add('live-player__login');
		} else {
			playBtn.classList.add('live-player__muted');
		}
		if (! resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.add('live-player__muted');
		}
		if (resumeBtn.classList.contains('resume__live')) {
			resumeBtn.classList.remove('resume__live');
		}
		if (true === playingCustomAudio) {
			nowPlaying.style.display = 'none';
			listenNow.style.display = 'inline-block';
		} else {
			nowPlaying.style.display = 'inline-block';
			listenNow.style.display = 'none';
		}
		if (pauseBtn.classList.contains('live-player__muted')) {
			pauseBtn.classList.remove('live-player__muted');
		}


	}

	function setStoppedStyles() {
		if ( null === tdContainer ) {
			// gigya user is logged out, so everything is different ಠ_ಠ - Should we force login for inline audio as well??
			return;
		}

		if (resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.remove('live-player__muted');
			resumeBtn.classList.add('resume__live');
		}
		listenNow.style.display = 'inline-block';
		nowPlaying.style.display = 'none';
		pauseBtn.classList.add('live-player__muted');
	}

	function setPausedStyles() {
		if ( null === tdContainer ) {
			// gigya user is logged out, so everything is different ಠ_ಠ - Should we force login for inline audio as well??
			return;
		}

		if (true === playingCustomAudio && window.innerWidth <= 767) {
			playBtn.classList.add('live-player__login');
		} else {
			playBtn.classList.add('live-player__muted');
		}
		listenNow.style.display = 'inline-block';
		nowPlaying.style.display = 'none';
		pauseBtn.classList.add('live-player__muted');
		if (resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.remove('live-player__muted');
			resumeBtn.classList.add('resume__live');
		}
		resumeBtn.classList.add('resume__audio');
	}

	function setInlineAudioUX() {
		var audioTime = document.querySelectorAll('.audio__time'), i;

		for (i = 0; i < audioTime.length; ++i) {
			audioTime[i].classList.add('playing');
		}

		if (liveStreamPlayer != null) {
			liveStreamPlayer.classList.add('audio__playing');
		}

		if (streamStatus != null) {
			streamStatus.classList.add('audio__playing');
		}

		if (livePlayer != null) {
			livePlayer.classList.add('playing');
		}

		if (nowPlayingInfo != null) {
			nowPlayingInfo.classList.add('playing');
		}

		if (listenNow != null) {
			setTimeout(function() {
				listenNow.innerHTML = 'Switch to Live Stream';
			}, 1000);
		}
	}

	function nearestPodcastPlaying(event) {
		var eventTarget = event.target;
		var podcastCover = eventTarget.parentNode;
		var audioCurrent = podcastCover.nextElementSibling;
		var runtimeCurrent = audioCurrent.nextElementSibling;
		var audioTime = document.querySelectorAll('.audio__time'), i;
		var runtime = document.querySelector('.podcast__runtime');
		$('.playing__current').removeClass('playing__current');
		if (podcastPlayer != null && ( body.classList.contains('single-show') || body.classList.contains('post-type-archive-podcast') || body.classList.contains('single-podcast') ) ) {
			audioCurrent.classList.add('playing__current');
			runtimeCurrent.classList.add('playing');
		} else {
			for (i = 0; i < audioTime.length; ++i) {
				if (audioTime[i] != null) {
					audioTime[i].classList.add('playing');
					audioTime[i].classList.add('playing__current');
				}
			}
			runtime.classList.add('playing');
		}
	}

	function resetInlineAudioUX() {
		var audioTime = document.querySelectorAll('.audio__time'), i;
		var runtime = document.querySelectorAll('.podcast__runtime');

		for (i = 0; i < audioTime.length; ++i) {
			if (audioTime[i] != null && audioTime[i].classList.contains('playing')) {
				audioTime[i].classList.remove('playing');
			}
			if (audioTime[i] != null && audioTime[i].classList.contains('playing__current')) {
				audioTime[i].classList.remove('playing__current');
			}
		}

		if (liveStreamPlayer != null) {
			liveStreamPlayer.classList.remove('audio__playing');
		}

		if (streamStatus != null) {
			streamStatus.classList.remove('audio__playing');
		}

		if (livePlayer != null) {
			livePlayer.classList.remove('playing');
		}

		for (i = 0; i < runtime.length; ++i) {
			if (runtime[i] != null && runtime[i].classList.contains('playing')) {
				runtime[i].classList.remove('playing');
			}
		}

		if (nowPlayingInfo != null) {
			nowPlayingInfo.classList.remove('playing');
		}
	}

	function replaceNPInfo() {
		if (window.innerWidth <= 767) {
			if (trackInfo.innerHTML === '') {
				onAir.classList.add('on-air__npe');
				liveStreamSelector.classList.add('full__width');
			} else if (onAir.classList.contains('on-air__npe')){
				onAir.classList.remove('on-air__npe');
				liveStreamSelector.classList.remove('full__width');
			}
		}
	}

	function addPlayBtnHeartbeat() {
		if (playBtn != null) {
			playBtn.classList.add('play-btn--heartbeat');
		}
		if (livePlayer != null) {
			livePlayer.classList.add('live-player--heartbeat');
		}
	}

	function removePlayBtnHeartbeat() {
		if (playBtn != null && playBtn.classList.contains('play-btn--heartbeat')) {
			playBtn.classList.remove('play-btn--heartbeat');
		}
		if (livePlayer != null && livePlayer.classList.contains('live-player--heartbeat')) {
			livePlayer.classList.remove('live-player--heartbeat');
		}
	}

	var listenLiveStopCustomInlineAudio = function() {
		var listenNowText = listenNow.textContent;
		var nowPlayingTitle = document.getElementById('trackInfo');
		var nowPlayingInfo = document.getElementById('npeInfo');

		if (true === playingCustomAudio) {
			customAudio.pause();
			nowPlayingTitle.innerHTML = '';
			nowPlayingInfo.innerHTML = '';
			resetInlineAudioStates();
			resetInlineAudioUX();
			playingCustomAudio = false;
		}
		if (listenNowText === 'Switch to Live Stream') {
			listenNow.innerHTML = 'Listen Live';
		}
		if (window.innerWidth >= 768) {
			playLiveStream();
		}
	};

	function changePlayerState() {
		if (is_gigya_user_logged_in()) {
			if (window.innerWidth >= 768) {
				if (playBtn != null) {
					addEventHandler(playBtn, elemClick, playLiveStream);
				}
			} else {
				if (playBtn != null) {
					addEventHandler(playBtn, elemClick, playLiveStreamMobile);
				}
			}
			if (listenNow != null) {
				addEventHandler(listenNow, elemClick, listenLiveStopCustomInlineAudio);
			}
		} else {
			if (playBtn != null) {
				addEventHandler(playBtn, 'click', function () {
					window.location.href = gigyaLogin;
				});
			}
			if (listenNow != null) {
				addEventHandler(listenNow, 'click', function () {
					window.location.href = gigyaLogin;
				});
			}
			if (listenLogin != null && window.innerWidth <= 767) {
				addEventHandler(listenLogin, 'click', function() {
					window.location.href = gigyaLogin;
				});
			}
		}
	}

	changePlayerState();

	function loggedInGigyaUser() {
		if (is_gigya_user_logged_in() ) {
			setStoppedStyles();
			if( Cookies.get( "gmlp_play_button_pushed" ) == 1 ) {
				if (window.innerWidth >= 768) {
					playLiveStream();
				} else {
					playLiveStreamMobile();
				}
				Cookies.set( "gmlp_play_button_pushed", 0 );
			} else {
				console.log("--- Log In with Gigya ---");
			}
		}
	}

	function preVastAd() {
		var preRoll = document.getElementById('live-stream__container');

		if(preRoll != null) {
			preRoll.classList.add('vast__pre-roll');
		}
	}

	function postVastAd() {
		var preRoll = document.getElementById('live-stream__container');

		if (preRoll != null) {
			preRoll.classList.remove('vast__pre-roll');
		}
		Cookies.set('gmr_play_live_audio', undefined);
		Cookies.set('gmr_play_live_audio', 1, {expires: 86400});
	}

	function streamVastAd() {
		var vastUrl = gmr.streamUrl;

		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('vastAd', {url: vastUrl});
	}

	/**
	 * @todo add a close option for the ad block detection modal
	 */
	function showAdBlockDetect() {
		var preRoll = document.querySelector('.vast__pre-roll');

		if(preRoll != null) {
			preRoll.innerHTML = '<div class="adblock--detected"><div class="adblock--detected__notice">We\'ve detected that you\'re using <strong>AdBlock Plus</strong> or some other adblocking software. In order to have the best viewing experience, please diasable AdBlock.</div></div>';
		}
	}

	function closeAdBlockDetect() {
		var preRoll = document.getElementById('live-stream__container');

		while (preRoll.hasChildNodes()) {
			preRoll.removeChild(preRoll.firstChild);
		}
		preRoll.classList.remove('vast__pre-roll');
	}

	var currentStream = $('.live-player__stream--current-name');

	currentStream.bind("DOMSubtreeModified",function(){
		console.log("--- new stream select ---");
		var station = currentStream.text();

		if (livePlaying) {
			player.stop();
		}

		if ( true === playingCustomAudio ) {
			listenLiveStopCustomInlineAudio();
		}

		player.play({station: station, timeShift: true});

		livePlayer.classList.add('live-player--active');
		setPlayingStyles();
		setTimeout(replaceNPInfo, 2000);
	});

	function playLiveStreamMobile() {
		var station = gmr.callsign;

		if (Cookies.get('gmr_play_live_audio') == 1) {
			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			debug('playLiveStream - station=' + station);

			if (livePlaying) {
				player.stop();
			}

			if ( true === playingCustomAudio ) {
				listenLiveStopCustomInlineAudio();
			}

			livePlayer.classList.add('live-player--active');
			player.play({station: station, timeShift: true});
			setPlayingStyles();
			setTimeout(replaceNPInfo, 2000);
		} else if (Cookies.get('gmr_play_live_audio') === 0) {
			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			if ( true === playingCustomAudio ) {
				listenLiveStopCustomInlineAudio();
			}

			debug('playLiveStream - station=' + station);

			preVastAd();
			if (adBlockCheck === undefined) {
				showAdBlockDetect();
				setTimeout(postVastAd, 15000);
			} else {
				streamVastAd();
			}
			if (player.addEventListener) {
				player.addEventListener('ad-playback-complete', function () {
					postVastAd();
					console.log("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					livePlayer.classList.add('live-player--active');
					player.play({station: station, timeShift: true});
					setPlayingStyles();
					setTimeout(replaceNPInfo, 2000);
				});
			} else if (player.attachEvent) {
				player.attachEvent('ad-playback-complete', function () {
					postVastAd();
					console.log("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					livePlayer.classList.add('live-player--active');
					player.play({station: station, timeShift: true});
					setPlayingStyles();
					setTimeout(replaceNPInfo, 2000);
				});
			}
		}

	}

	function playLiveStream() {
		var station = gmr.callsign;

		pjaxInit();
		if ( true === playingCustomAudio ) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else if (adBlockCheck === undefined) {
			preVastAd();
			showAdBlockDetect();
			setTimeout(postVastAd, 15000);
		} else if (Cookies.get('gmr_play_live_audio') != 1) {

			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			debug('playLiveStream - station=' + station);

			preVastAd();
			streamVastAd();
			if (player.addEventListener) {
				player.addEventListener('ad-playback-complete', function () {
					postVastAd();
					console.log("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					livePlayer.classList.add('live-player--active');
					player.play({station: station, timeShift: true});
					setPlayingStyles();
					setTimeout(replaceNPInfo, 2000);
				});
			} else if (player.attachEvent) {
				player.attachEvent('ad-playback-complete', function () {
					postVastAd();
					console.log("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					livePlayer.classList.add('live-player--active');
					player.play({station: station, timeShift: true});
					setPlayingStyles();
					setTimeout(replaceNPInfo, 2000);
				});
			}
		} else {
			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			debug('playLiveStream - station=' + station);

			if (livePlaying) {
				player.stop();
			}

			if ( true === playingCustomAudio ) {
				listenLiveStopCustomInlineAudio();
			}

			livePlayer.classList.add('live-player--active');
			player.play({station: station, timeShift: true});
			setPlayingStyles();
			setTimeout(replaceNPInfo, 2000);
		}
	}

	function resumeLiveStream() {
		pjaxInit();
		if ( true === playingCustomAudio ) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else if (adBlockCheck === undefined) {
			preVastAd();
			showAdBlockDetect();
			setTimeout(postVastAd, 15000);
		} else {
			var station = gmr.callsign;
			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			debug('playLiveStream - station=' + station);

			if (livePlaying) {
				player.stop();
			}

			livePlayer.classList.add('live-player--active');
			player.play({station: station, timeShift: true});
			setPlayingStyles();
			setTimeout(replaceNPInfo, 2000);
		}
	}

	function playLiveStreamWithPreRoll() {
		if ( true === playingCustomAudio ) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else {
			var station = gmr.callsign;
			var vastUrl = gmr.streamUrl;
			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			debug('playLiveStream - station=' + station);

			preVastAd();
			if (adBlockCheck === undefined) {
				showAdBlockDetect();
				setTimeout(postVastAd, 15000);
			} else {
				streamVastAd();
			}
			if (player.addEventListener) {
				player.addEventListener('ad-playback-complete', function () {
					postVastAd();
					console.log("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					livePlayer.classList.add('live-player--active');
					player.play({station: station, timeShift: true});
					setPlayingStyles();
					setTimeout(replaceNPInfo, 2000);
				});
			} else if (player.attachEvent) {
				player.attachEvent('ad-playback-complete', function () {
					postVastAd();
					console.log("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					livePlayer.classList.add('live-player--active');
					player.play({station: station, timeShift: true});
					setPlayingStyles();
					setTimeout(replaceNPInfo, 2000);
				});
			}
		}
	}

	function stopStream() {
		if ( true === playingCustomAudio ) {
			stopCustomInlineAudio();
		} else {
			player.stop();
		}

		if (livePlayer.classList.contains('live-player--active')) {
			livePlayer.classList.remove('live-player--active');
		}
		setStoppedStyles();
	}

	function pauseStream() {
		if ( true === playingCustomAudio ) {
			pauseCustomInlineAudio();
		} else {
			player.pause();
		}

		if (livePlayer.classList.contains('live-player--active')) {
			livePlayer.classList.remove('live-player--active');
		}
		setPausedStyles();
	}

	function resumeStream() {
		if ( true === playingCustomAudio ) {
			resumeCustomInlineAudio();
		} else {
			if (livePlaying) {
				player.resume();
			} else {
				player.play();
			}
		}

		setPlayingStyles();
	}

	function seekLive() {
		player.seekLive();
		setPlayingStyles();
	}

	function skipAd()
	{
		player.skipAd();
	}

	function loadNpApi() {
		if ($("#songHistoryCallsignUser").val() === '') {
			alert('Please enter a Callsign');
			return;
		}

		var isHd = ( $("#songHistoryConnectionTypeSelect").val() == 'hdConnection' );

		//Set the hd parameter to true if the station has AAC. Set it to false if the station has no AAC.
		player.NowPlayingApi.load({mount: $("#songHistoryCallsignUser").val(), hd: isHd, numberToFetch: 15});
	}

	function setVolume50() {
		player.setVolume(0.5);
	}

	function mute() {
		player.mute();
	}

	function unMute() {
		player.unMute();
	}

	function getArtistData() {
		if (song && song.artist() != null) {
			song.artist().fetchData();
		}
	}

	function onPlayerReady() {
		//Return if MediaPlayer is not loaded properly...
		if (player.MediaPlayer === undefined) {
			return;
		}

		//Listen on companion-load-error event
		//companions.addEventListener("companion-load-error", onCompanionLoadError);

		loggedInGigyaUser();
		initControlsUi();

		if (player.addEventListener) {
			player.addEventListener('track-cue-point', onTrackCuePoint);
			player.addEventListener('ad-break-cue-point', onAdBreak);
			player.addEventListener( 'stream-track-change', onTrackChange );
			player.addEventListener( 'hls-cue-point', onHlsCuePoint );

			player.addEventListener('stream-status', onStatus);
			player.addEventListener('stream-geo-blocked', onGeoBlocked);
			player.addEventListener('timeout-alert', onTimeOutAlert);
			player.addEventListener('timeout-reach', onTimeOutReach);
			player.addEventListener('npe-song', onNPESong);

			player.addEventListener('stream-select', onStreamSelect);

			player.addEventListener('stream-start', onStreamStarted);
			player.addEventListener('stream-stop', onStreamStopped);
		} else if (player.attachEvent) {
			player.attachEvent('track-cue-point', onTrackCuePoint);
			player.attachEvent('ad-break-cue-point', onAdBreak);
			player.attachEvent( 'stream-track-change', onTrackChange );
			player.attachEvent( 'hls-cue-point', onHlsCuePoint );

			player.attachEvent('stream-status', onStatus);
			player.attachEvent('stream-geo-blocked', onGeoBlocked);
			player.attachEvent('timeout-alert', onTimeOutAlert);
			player.attachEvent('timeout-reach', onTimeOutReach);
			player.attachEvent('npe-song', onNPESong);

			player.attachEvent('stream-select', onStreamSelect);

			player.attachEvent('stream-start', onStreamStarted);
			player.attachEvent('stream-stop', onStreamStopped);
		}

		player.setVolume(1); //Set volume to 100%

		setStatus('Api Ready');
		if (window.innerWidth >= 768) {
			addPlayBtnHeartbeat();
			setTimeout(removePlayBtnHeartbeat, 2000);
		}
		setTech(player.MediaPlayer.tech.type);

		if (player.addEventListener) {
			player.addEventListener('list-loaded', onListLoaded);
			player.addEventListener('list-empty', onListEmpty);
			player.addEventListener('nowplaying-api-error', onNowPlayingApiError);
		} else if (player.attachEvent) {
			player.attachEvent('list-loaded', onListLoaded);
			player.attachEvent('list-empty', onListEmpty);
			player.attachEvent('nowplaying-api-error', onNowPlayingApiError);
		}

		$("#fetchSongHistoryByUserCallsignButton").click(function () {
			loadNpApi();
		});

		if (player.addEventListener) {
			player.addEventListener('pwa-data-loaded', onPwaDataLoaded);
		} else if (player.attachEvent) {
			player.attachEvent('pwa-data-loaded', onPwaDataLoaded);
		}

		$("#pwaButton").click(function () {
			loadPwaData();
		});
	}

	/**
	 * Event fired in case the loading of the companion ad returned an error.
	 * @param e
	 */
	function onCompanionLoadError(e)
	{
		debug( 'tdplayer::onCompanionLoadError - containerId=' + e.containerId + ', adSpotUrl=' + e.adSpotUrl, true );
	}

	function onAdPlaybackStart(e) {
		adPlaying = true;
		setStatus('Advertising... Type=' + e.data.type);
	}

	function onAdPlaybackComplete(e) {
		adPlaying = false;
		$("#td_adserver_bigbox").empty();
		$("#td_adserver_leaderboard").empty();
		setStatus('Ready');
	}

	/**
	 * Custom function to handle when a vast ad fails. This runs when there is an `ad-playback-error` event.
	 *
	 * @param e
	 */
	function adError(e) {
		setStatus('Ready');

		postVastAd();
		var station = gmr.callsign;
		if (livePlaying) {
			player.stop();
		}

		livePlayer.classList.add('live-player--active');
		player.play({station: station, timeShift: true});
		setPlayingStyles();
		setTimeout(replaceNPInfo, 2000);
	}

	function onAdCountdown(e) {
		debug('Ad countdown : ' + e.data.countDown + ' second(s)');
	}

	function onVastProcessComplete(e) {
		debug('Vast Process complete');

		var vastCompanions = e.data.companions;

		//Load Vast Ad companion (bigbox & leaderbaord ads)
		displayVastCompanionAds(vastCompanions);
	}

	function onVpaidAdCompanions(e) {
		debug('Vpaid Ad Companions');

		//Load Vast Ad companion (bigbox & leaderbaord ads)
		displayVastCompanionAds(e.companions);
	}

	function displayVastCompanionAds(vastCompanions) {
		if (vastCompanions && vastCompanions.length > 0) {
			var bigboxIndex = -1;
			var leaderboardIndex = -1;

			$.each(vastCompanions, function (i, val) {
				if (parseInt(val.width) == 300 && parseInt(val.height) == 250) {
					bigboxIndex = i;
				} else if (parseInt(val.width) == 728 && parseInt(val.height) == 90) {
					leaderboardIndex = i;
				}
			});

			if (bigboxIndex > -1) {
				companions.loadVASTCompanionAd('td_adserver_bigbox', vastCompanions[bigboxIndex]);
			}

			if (leaderboardIndex > -1) {
				companions.loadVASTCompanionAd('td_adserver_leaderboard', vastCompanions[leaderboardIndex]);
			}
		}
	}

	function onStreamStarted() {
		livePlaying = true;
	}

	function onStreamSelect() {
		$('#hasHQ').html(player.MediaPlayer.hasHQ().toString());
		$('#isHQ').html(player.MediaPlayer.isHQ().toString());

		$('#hasLow').html(player.MediaPlayer.hasLow().toString());
		$('#isLow').html(player.MediaPlayer.isLow().toString());
	}

	function onStreamStopped() {
		livePlaying = false;

		clearNpe();
		$("#trackInfo").html('');
		$("#asyncData").html('');

		$('#hasHQ').html('N/A');
		$('#isHQ').html('N/A');

		$('#hasLow').html('N/A');
		$('#isLow').html('N/A');
	}

	function onTrackCuePoint(e) {
		debug('New Track cuepoint received');
		debug('Title:' + e.data.cuePoint.cueTitle + ' - Artist:' + e.data.cuePoint.artistName);
		console.log(e);

		if (currentTrackCuePoint && currentTrackCuePoint != e.data.cuePoint) {
			clearNpe();
		}

		if (e.data.cuePoint.nowplayingURL) {
			player.Npe.loadNpeMetadata(e.data.cuePoint.nowplayingURL, e.data.cuePoint.artistName, e.data.cuePoint.cueTitle);
		}

		currentTrackCuePoint = e.data.cuePoint;

		$("#trackInfo").html('<div class="now-playing__title">' + currentTrackCuePoint.cueTitle + '</div><div class="now-playing__artist">' + currentTrackCuePoint.artistName + '</div>');

	}

	function onTrackChange( e )
	{
		debug( 'Stream Track has changed' );
		debug( 'Codec:'+ e.data.cuePoint.audioTrack.codec() + ' - Bitrate:'+ e.data.cuePoint.audioTrack.bitRate());
	}

	function onHlsCuePoint( e )
	{
		debug( 'New HLS cuepoint received' );
		debug( 'Track Id:'+e.data.cuePoint.hlsTrackId+' SegmentId:'+e.data.cuePoint.hlsSegmentId );
		console.log( e );
	}


	function onAdBreak(e) {
		setStatus('Commercial break...');
		console.log(e);
	}

	function clearNpe() {
		$("#npeInfo").html('');
		$("#asyncData").html('');
	}

	//Song History
	function onListLoaded(e) {
		debug('Song History loaded');
		console.log(e.data);

		$("#asyncData").html('<br><p><span class="label label-warning">Song History:</span>');

		var tableContent = '<table class="table table-striped"><thead><tr><th>Song title</th><th>Artist name</th><th>Time</th></tr></thead>';

		var time;
		$.each(e.data.list, function (index, item) {
			time = new Date(Number(item.cueTimeStart));
			tableContent += "<tr><td>" + item.cueTitle + "</td><td>" + item.artistName + "</td><td>" + time.toLocaleTimeString() + "</td></tr>";
		});

		tableContent += "</table></p>";

		$("#asyncData").html("<div>" + tableContent + "</div>");
	}

	//Song History empty
	function onListEmpty(e) {
		$("#asyncData").html('<br><p><span class="label label-important">Song History is empty</span>');
	}

	function onNowPlayingApiError(e) {
		debug('Song History loading error', true);
		console.error(e);

		$("#asyncData").html('<br><p><span class="label label-important">Song History error</span>');
	}

	function onTimeOutAlert(e) {
		debug('Time Out Alert');
	}

	function onTimeOutReach(e) {
		debug('Time Out Reached');
	}

	function onConfigurationError(e) {
		debug('Configuration error', true);
		console.log(e);
	}

	function onModuleError(object) {
		var message = '';

		$.each(object.data.errors, function (i, val) {
			message += 'ERROR : ' + val.data.error.message + '<br/>';
		});

		$("#status").html('<p><span class="label label-important">' + message + '</span><p></p>');
	}

	function onStatus(e) {
		console.log('tdplayer::onStatus');

		setStatus(e.data.status);
	}

	function onGeoBlocked(e) {
		console.log('tdplayer::onGeoBlocked');

		setStatus(e.data.text);
	}

	function setStatus(status) {
		debug(status);

		$("#status").html('<p><span class="label label-success">Status: ' + status + '</span></p>');
	}

	function setTech(techType) {
		var apiVersion = player.version.major + '.' + player.version.minor + '.' + player.version.patch + '.' + player.version.flag;

		var techInfo = '<p><span class="label label-info">Api version: ' + apiVersion + ' - Technology: ' + techType;

		if (player.flash.available) {
			techInfo += ' - Your current version of flash plugin is: ' + player.flash.version.major + '.' + player.flash.version.minor + '.' + player.flash.version.rev;
		}

		techInfo += '</span></p>';

		$("#techInfo").html(techInfo);
	}

	function loadPwaData() {
		if ($("#pwaCallsign").val() === '' || $("#pwaStreamId").val() === '') {
			alert('Please enter a Callsign and a streamid');
			return;
		}

		player.PlayerWebAdmin.load($("#pwaCallsign").val(), $("#pwaStreamId").val());
	}

	function onPwaDataLoaded(e) {
		debug('PlayerWebAdmin data loaded successfully');
		console.log(e);

		$("#asyncData").html('<br><p><span class="label label-warning">PlayerWebAdmin:</span>');

		var tableContent = '<table class="table table-striped"><thead><tr><th>Key</th><th>Value</th></tr></thead>';

		for (var item in e.data.config) {
			console.log(item);
			tableContent += "<tr><td>" + item + "</td><td>" + e.data.config[item] + "</td></tr>";
		}

		tableContent += "</table></p>";

		$("#asyncData").html("<div>" + tableContent + "</div>");
	}

	function playRunSpotAd() {
		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('vastAd', {sid: 8441});
	}

	function playRunSpotAdById() {
		if ($("#runSpotId").val() === '') {
			return;
		}

		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('vastAd', {sid: $("#runSpotId").val()});
	}

	function playVastAd() {
		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('vastAd', {url: 'http://runspot4.tritondigital.com/RunSpotV4.svc/GetVASTAd?&StationID=8441&MediaFormat=21&RecordImpressionOnCall=false&AdMinimumDuration=0&AdMaximumDuration=900&AdLevelPlacement=1&AdCategory=1'});
	}

	function playVastAdByUrl() {
		if ($("#vastAdUrl").val() === '') {
			return;
		}

		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('vastAd', {url: $("#vastAdUrl").val()});
	}

	function playBloomAd() {
		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('bloom', {id: 4974});
	}

	function playMediaAd() {
		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		//player.playAd( 'mediaAd', { mediaUrl: 'http://cdnp.tremormedia.com/video/acudeo/Carrot_400x300_500kb.flv', linkUrl:'http://www.google.fr/' } );
		player.playAd('mediaAd', {mediaUrl: 'http://vjs.zencdn.net/v/oceans.mp4', linkUrl: 'http://www.google.fr/'});
	}

	function attachAdListeners() {
		if (player.addEventListener){
			player.addEventListener('ad-playback-start', onAdPlaybackStart);
			player.addEventListener('ad-playback-error', adError);
			player.addEventListener('ad-playback-complete', onAdPlaybackComplete);
			player.addEventListener('ad-countdown', onAdCountdown);
			player.addEventListener('vast-process-complete', onVastProcessComplete);
			player.addEventListener('vpaid-ad-companions', onVpaidAdCompanions);
		} else if (player.attachEvent) {
			player.attachEvent('ad-playback-start', onAdPlaybackStart);
			player.attachEvent('ad-playback-error', adError);
			player.attachEvent('ad-playback-complete', onAdPlaybackComplete);
			player.attachEvent('ad-countdown', onAdCountdown);
			player.attachEvent('vast-process-complete', onVastProcessComplete);
			player.attachEvent('vpaid-ad-companions', onVpaidAdCompanions);
		}
	}

	function detachAdListeners() {
		if (player.removeEventListener){
			player.removeEventListener('ad-playback-start', onAdPlaybackStart);
			player.removeEventListener('ad-playback-error', adError);
			player.removeEventListener('ad-playback-complete', onAdPlaybackComplete);
			player.removeEventListener('ad-countdown', onAdCountdown);
			player.removeEventListener('vast-process-complete', onVastProcessComplete);
			player.removeEventListener('vpaid-ad-companions', onVpaidAdCompanions);
		} else if (player.detachEvent) {
			player.detachEvent('ad-playback-start', onAdPlaybackStart);
			player.detachEvent('ad-playback-error', adError);
			player.detachEvent('ad-playback-complete', onAdPlaybackComplete);
			player.detachEvent('ad-countdown', onAdCountdown);
			player.detachEvent('vast-process-complete', onVastProcessComplete);
			player.detachEvent('vpaid-ad-companions', onVpaidAdCompanions);
		}
	}

	var artist;

	function onNPESong(e) {
		console.log('tdplayer::onNPESong');
		console.log(e);

		song = e.data.song;

		artist = song.artist();
		if (artist.addEventListener) {
			artist.addEventListener('artist-complete', onArtistComplete);
		} else if (artist.attachEvent) {
			artist.attachEvent('artist-complete', onArtistComplete);
		}

		var songData = getNPEData();

		displayNpeInfo(songData, false);
	}

	function displayNpeInfo(songData, asyncData) {
		$("#asyncData").empty();

		var id = asyncData ? 'asyncData' : 'npeInfo';
		var list = $("#" + id);

		if (asyncData === false) {
			list.html('<span class="label label-inverse">Npe Info:</span>');
		}

		list.append(songData);
	}

	function onArtistComplete(e) {
		if (artist.addEventListener) {
			artist.addEventListener('picture-complete', onArtistPictureComplete);
		} else if (artist.attachEvent) {
			artist.attachEvent('picture-complete', onArtistPictureComplete);
		}

		var pictures = artist.getPictures();
		var picturesIds = [];
		for (var i = 0; i < pictures.length; i++) {
			picturesIds.push(pictures[i].id);
		}
		if (picturesIds.length > 0) {
			artist.fetchPictureByIds(picturesIds);
		}

		var songData = getArtist();

		displayNpeInfo(songData, true);
	}

	function onArtistPictureComplete(pictures) {
		console.log('tdplayer::onArtistPictureComplete');
		console.log(pictures);

		var songData = '<span class="label label-inverse">Photos:</span><br>';

		for (var i = 0; i < pictures.length; i++) {
			if (pictures[i].getFiles()) {
				songData += '<a href="' + pictures[i].getFiles()[0].url + '" rel="lightbox[npe]" title="Click on the right side of the image to move forward."><img src="' + pictures[i].getFiles()[0].url + '" width="125" /></a>&nbsp;';
			}
		}

		$("#asyncData").append(songData);
	}

	function getArtist() {
		if (song !== undefined) {
			var songData = '<span class="label label-inverse">Artist:</span>';

			songData += '<ul><li>Artist id: ' + song.artist().id + '</li>';
			songData += '<li>Artist birth date: ' + song.artist().getBirthDate() + '</li>';
			songData += '<li>Artist end date: ' + song.artist().getEndDate() + '</li>';
			songData += '<li>Artist begin place: ' + song.artist().getBeginPlace() + '</li>';
			songData += '<li>Artist end place: ' + song.artist().getEndPlace() + '</li>';
			songData += '<li>Artist is group ?: ' + song.artist().getIsGroup() + '</li>';
			songData += '<li>Artist country: ' + song.artist().getCountry() + '</li>';

			var albums = song.artist().getAlbums();
			for (var i = 0; i < albums.length; i++) {
				songData += '<li>Album ' + ( i + 1 ) + ': ' + albums[i].getTitle() + '</li>';
			}
			var similars = song.artist().getSimilar();
			for (i < similars.length; i++;) {
				songData += '<li>Similar artist ' + ( i + 1 ) + ': ' + similars[i].name + '</li>';
			}
			var members = song.artist().getMembers();
			for (i < members.length; i++;) {
				songData += '<li>Member ' + ( i + 1 ) + ': ' + members[i].name + '</li>';
			}

			songData += '<li>Artist website: ' + song.artist().getWebsite() + '</li>';
			songData += '<li>Artist twitter: ' + song.artist().getTwitterUsername() + '</li>';
			songData += '<li>Artist facebook: ' + song.artist().getFacebookUrl() + '</li>';
			songData += '<li>Artist biography: ' + song.artist().getBiography().substring(0, 2000) + '...</small>';

			var genres = song.artist().getGenres();
			for (i < genres.length; i++;) {
				songData += '<li>Genre ' + ( i + 1 ) + ': ' + genres[i] + '</li>';
			}
			songData += '</ul>';

			return songData;
		} else {
			return '<span class="label label-important">The artist information is undefined</span>';
		}
	}

	function getNPEData() {
		var innerContent = 'NPE Data undefined';

		if (song !== undefined && song.album()) {
			var _iTunesLink = '';
			if (song.album().getBuyUrl() != null) {
				_iTunesLink = '<a target="_blank" title="' + song.album().getBuyUrl() + '" href="' + song.album().getBuyUrl() + '">Buy on iTunes</a><br/>';
			}

			innerContent = '<p><b>Album:</b> ' + song.album().getTitle() + '<br/>' +
			_iTunesLink +
			'<img src="' + song.album().getCoverArtOriginal().url + '" style="height:100px" /></p>';
		}

		return innerContent;
	}

	function getUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for (var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	function debug(info, error) {

		if (error) {
			console.error(info);
		} else {
			console.log(info);
		}

		$('#debugInformation').append(info);
		$('#debugInformation').append('\n');
	}

	function clearDebugInfo() {
		$('#debugInformation').html('');
	}

	/* Inline Audio Support */
	var stopLiveStreamIfPlaying = function() {
		if ( "undefined" !== typeof player && "undefined" !== typeof player.stop ) {
			player.stop();
		}
	};

	var resetInlineAudioStates = function() {
		$('.podcast__btn--play.playing').removeClass('playing');
		$('.podcast__btn--pause.playing').removeClass('playing');
	};

	/*
	 * Finds any inline audio players with a matching hash of the current custom audio file, and sets the playing state appropriately
	 */
	var setInlineAudioStates = function() {
		var className = '.mp3-' + customHash;

		$( className + ' .podcast__btn--play').addClass('playing');
		$( className + ' .podcast__btn--pause').addClass('playing');
	};

	var setInlineAudioSrc = function( src ) {
		customAudio.src = src;
	};

	var resumeCustomInlineAudio = function() {
		playingCustomAudio = true;
		stopLiveStreamIfPlaying();
		customAudio.play();
		setPlayerTrackName();
		setPlayerArtist();
		resetInlineAudioStates();
		setPlayingStyles();
		setInlineAudioStates();
		setInlineAudioUX();
	};

	var playCustomInlineAudio = function( src ) {
		pjaxInit();

		// Only set the src if its different than what is already there, so we can resume the audio with the inline buttons
		if ( src !== customAudio.src ) {
			setInlineAudioSrc( src );
		}
		resumeCustomInlineAudio();
	};

	var pauseCustomInlineAudio = function() {
		pjaxStop();
		customAudio.pause();
		resetInlineAudioStates();
		setPausedStyles();
	};

	/*
	Same as pausing, but sets the "Playing" state to false, to allow resuming live player audio
	 */
	var stopCustomInlineAudio = function() {
		pjaxStop();
		customAudio.pause();
		resetInlineAudioStates();
		playingCustomAudio = false;
		setStoppedStyles();
		resetInlineAudioUX();
	};

	var setPlayerTrackName = function() {
		var template = _.template('<div class="now-playing__title"><%- title %></div>'),
			$trackTitleDiv = $('.now-playing__title'),
			$trackTitleWrap = '<div class="audio__title">',
			$time = '</div><div class="audio__time"><span class="audio__time--inline">(</span><div class="audio__time--elapsed"></div><span class="audio__time--inline"> / </span><div class="audio__time--remaining"></div><span class="audio__time--inline">)</span></div>';

		if ( $trackTitleDiv.length > 0 ) {
			$trackTitleDiv.html( $trackTitleWrap + customTrack + $time );
		} else {
			$trackInfo.prepend( template({ title: customTrack }) );
		}
	};

	var setPlayerArtist = function() {
		var template = _.template('<div class="now-playing__artist"><%- artist %></div>'),
			$trackArtistDiv = $('.now-playing__artist');

		if ( $trackArtistDiv.length > 0 ) {
			$trackArtistDiv.text( customArtist );
		} else {
			$trackInfo.append( template({ artist: customArtist }) );
		}
	};

	var setCustomAudioMetadata = function( track, artist, hash ) {
		customTrack = track;
		customArtist = artist;
		customHash = hash;

		setPlayerTrackName();
		setPlayerArtist();
		setInlineAudioStates();
	};

	var initCustomAudioPlayer = function() {
		if ( "undefined" !== typeof Modernizr && Modernizr.audio ) {
			customAudio = new Audio();

			// Revert the button states back to play once the file is done playing
			if (customAudio.addEventListener) {
				customAudio.addEventListener( 'ended', function() {
					resetInlineAudioStates();
					setPausedStyles();
				} );
			} else if (customAudio.attachEvent) {
				customAudio.attachEvent( 'ended', function() {
					resetInlineAudioStates();
					setPausedStyles();
				} );
			}

		}
	};

	function initInlineAudioUI() {
		if ( "undefined" !== typeof Modernizr && Modernizr.audio ) {
			var content = document.querySelectorAll( '.content'),
				$content = $(content); // Because getElementsByClassName is not supported in IE8 ಠ_ಠ

			$content.on('click', '.podcast__btn--play', function(e) {
				var $play = $(e.currentTarget);

				nearestPodcastPlaying(e);

				playCustomInlineAudio( $play.attr( 'data-mp3-src' ) );

				resetInlineAudioStates();

				setCustomAudioMetadata( $play.attr( 'data-mp3-title' ), $play.attr( 'data-mp3-artist' ), $play.attr('data-mp3-hash') );
			});

			$content.on('click', '.podcast__btn--pause', pauseCustomInlineAudio );
		} else {
			var $meFallbacks = $('.gmr-mediaelement-fallback audio'),
				$customInterfaces = $('.podcast__play');

			$meFallbacks.mediaelementplayer();
			$customInterfaces.hide();
		}
	}

	function pjaxInit() {
		if (is_gigya_user_logged_in()) {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.main', {
					'fragment': '.main',
					'maxCacheLength': 500,
					'timeout': 5000
				});
			}
		} else if (gmlp.logged_in) {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.page-wrap', {
					'fragment': '.page-wrap',
					'maxCacheLength': 500,
					'timeout': 5000
				});
			}
		}
	}

	function pjaxStop() {
		$(document).on('pjax:click', function(event) {
			event.preventDefault();
		});
	}

	/**
	 * calculates the time of an inline audio element and outputs the duration as a % displayed in the progress bar
	 */
	function audioUpdateProgress() {
		var progress = document.querySelectorAll('.audio__progress'), i,
			value = 0;
		for (i = 0; i < progress.length; ++i) {
			if (customAudio.currentTime > 0) {
				value = Math.floor((100 / customAudio.duration) * customAudio.currentTime);
			}
			progress[i].style.width = value + "%";
		}
	}

	/**
	 * calculates the time of an inline audio element and outputs the time remaining
	 */
	function audioTimeRemaining() {
		var timeleft = document.querySelectorAll('.audio__time--remaining'), i,
			duration = parseInt(customAudio.duration),
			currentTime = parseInt(customAudio.currentTime),
			timeLeft = duration - currentTime,
			s, m;

		for (i = 0; i < timeleft.length; ++i) {
			s = timeLeft % 60;
			if (isNaN(s)) {
				s = '00';
			} else {
				s = s < 10 ? "0" + s : s;
			}

			m = Math.floor( timeLeft / 60 ) % 60;
			if (isNaN(m)) {
				m = '0';
			}

			timeleft[i].innerHTML = m + ":" + s;
		}
	}

	/**
	 * calculates the time of an inline audio element and outputs the time that has elapsed
	 */
	function audioTimeElapsed() {
		var timeline = document.querySelectorAll('.audio__time--elapsed'), i,
			s = parseInt(customAudio.currentTime % 60),
			m = parseInt((customAudio.currentTime / 60) % 60);

		for (i = 0; i < timeline.length; ++i) {
			if (s < 10) {
				timeline[i].innerHTML = m + ':0' + s;
			}
			else {
				timeline[i].innerHTML = m + ':' + s;
			}
		}
	}

	function fadeOutInlineAudio() {
		if (true === playingCustomAudio) {
			customAudio.animate({volume: 0}, 2000);
			customAudio.pause();
		}
	}

	function fadeInInlineAudio() {
		if (false === playingCustomAudio) {
			customAudio.play();
			customAudio.animate({volume: 1}, 2000);
		}
	}

	initCustomAudioPlayer();
	initInlineAudioUI();

	/**
	 * event listeners for customAudio time
	 */
	customAudio.addEventListener('timeupdate', function(){
		audioUpdateProgress();
		audioTimeElapsed();
		audioTimeRemaining();
	}, false);

	addEventHandler(podcastPlayBtn,elemClick,setInlineAudioUX);

	addEventHandler(podcastPauseBtn,elemClick,pauseCustomInlineAudio);

	// Ensures our listeners work even after a PJAX load
	$(document).on( 'pjax:end', function() {
		initInlineAudioUI();
		setInlineAudioStates();
		addEventHandler(podcastPlayBtn,elemClick,setInlineAudioUX);
		addEventHandler(podcastPauseBtn,elemClick,pauseCustomInlineAudio);
	});

})(jQuery, window);
