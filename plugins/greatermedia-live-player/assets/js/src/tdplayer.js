/**
 * This is a forked version of the tdplayer.js file from http://player.listenlive.co/api/2.5/js/tdplayer.js
 *
 * This file was modified to fit the unique functionality of the GMR sites including the integration of inline audio
 * and podcasts playing in the live player area. This file also adds support for older browsers that do not support
 * the `addEventListener` method. The core functions for the Triton API are using `addEventListener`. To add support,
 * conditionals were added that would use `attachEvent` if `addEventListener` is not supported. A custom function --
 * `addEventHandler` -- that will handle the switch is also being used throughout.
 */
(function ($, window, document, undefined) {
	"use strict";

	var $window = $(window);
	var $document = $(document);

	var tech = getUrlVars()['tech'] || 'html5_flash';
	var aSyncCuePointFallback = getUrlVars()['aSyncCuePointFallback'] == 'false' ? false : true;

	var player;
	/* TD player instance */

	var playingCustomAudio = false;
	/* This will be true if we're playing a custom audio file vs. live stream */
	var customAudio = false;
	/* Will be an HTML5 Audio object, if we support it */
	var customArtist, customTrack, customHash; // So we can re-add these when resuming via live-player
	var playingLiveAudio = false; // This will be true if we're playing live audio from the live stream

	var adPlaying;
	/* boolean - Ad break currently playing */
	var livePlaying;
	/* boolean - Live stream currently playing */
	var song;
	/* Song object that wraps NPE data */
	var companions;
	/* VAST companion banner object */
	var currentStation = '';
	/* String - Current station played */

	var body = document.querySelector('body');
	var tdContainer = document.getElementById('td_container');
	var livePlayer = document.getElementById('live-player');
	var liveStreamPlayer = document.querySelector('.live-stream__player');
	var playBtn = document.getElementById('playButton');
	var pauseBtn = document.getElementById('pauseButton');
	var resumeBtn = document.getElementById('resumeButton');
	var loadingBtn = document.getElementById('loadButton');
	var podcastPlayBtn = document.querySelector('.podcast__btn--play');
	var podcastPauseBtn = document.querySelector('.podcast__btn--pause');
	var podcastPlayer = document.querySelector('.podcast-player');
	var listenNow = document.getElementById('live-stream__listen-now');
	var nowPlaying = document.getElementById('live-stream__now-playing');
	var listenLogin = document.getElementById('live-stream__login');
	var $trackInfo = $(document.getElementById('trackInfo'));
	var clearDebug = document.getElementById('clearDebug');
	var onAir = document.getElementById('on-air');
	var streamStatus = document.getElementById('live-stream__status');
	var nowPlayingInfo = document.getElementById('nowPlaying');
	var trackInfo = document.getElementById('trackInfo');
	var liveStreamSelector = document.querySelector('.live-player__stream');
	var inlineAudioInterval = null;
	var liveStreamInterval = null;
	var trackTimeout = null;
	var footer = document.querySelector('.footer');
	var lpInit = false;
	var volume_slider = $(document.getElementById('live-player--volume'));
	var global_volume = 1;

	var $audioControls = $(document.getElementById('js-audio-controls'));
	var $audioVolume = $(document.getElementById('js-audio-volume'));
	var $audioVolumeBtn = $(document.getElementById('js-audio-volume-button'));
	var $audioStatus = $(document.getElementById('js-audio-status'));
	var $audioTrackInfo = $(document.getElementById('js-track-info'));
	var $audioAuthorInfo = $(document.getElementById('js-artist-info'));
	var $audioExpandBtn = $(document.getElementById('js-audio-expand'));
	var $audioPodcast = $(document.getElementById('js-audio-podcast'));
	var $audioAdBreakContainerAbovePlayer = $(document.getElementById('js-audio-ad-aboveplayer'));
	var $audioAdBreakContainerInPlayer = $(document.getElementById('js-audio-ad-inplayer'));
	var $audioMore = $(document.getElementById('js-audio-more')).find('a');
	var $audioStatusListen = $(document.getElementById('js-audio-status-listen'));

	/**
	 * Reads comments of an element.
	 */
	$.fn.getComments = function() {
		return this.contents().map(function () {
			if (this.nodeType === 8) {
				return this.nodeValue;
			}
		}).get();
	};

	/**
	 * Stars playing a stream and triggers appropriate event.
	 *
	 * @param {string} station
	 */
	function playStream(station) {
		debug('tdplayer::play: ' + station);
		player.play({station: station, timeShift: true});
		$document.trigger('player:starts');
	}

	/**
	 * function to detect if the current browser can use `addEventListener`, if not, use `attachEvent`
	 * this is a specific fix for IE8
	 *
	 * @param elem
	 * @param eventType
	 * @param handler
	 */
	function addEventHandler(elem, eventType, handler) {
		if (elem != null) {
			if (elem.addEventListener) {
				elem.addEventListener(eventType, handler, false);
			} else if (elem.attachEvent) {
				elem.attachEvent('on' + eventType, handler);
			}
		}
	}

	/**
	 * Starts an interval timer for when the live stream is playing
	 * Broadcasts an event every `audioIntervalDuration`
	 */
	function startLiveStreamInterval() {
		var interval = gmr.intervals.live_streaming;

		if (interval > 0) {
			debug('Live stream interval set');

			liveStreamInterval = setInterval(function () {
				$(body).trigger('liveStreamPlaying.gmr');
				debug('Live stream interval reached');
			}, interval * 60 * 1000);
		}
	}

	/**
	 * Starts an interval timer for when inline audio is playing
	 * Broadcasts an event every `audioIntervalDuration`
	 */
	function startInlineAudioInterval() {
		var interval = gmr.intervals.inline_audio;

		if (interval > 0) {
			debug('Inline audio interval set');

			inlineAudioInterval = setInterval(function () {
				$(body).trigger('inlineAudioPlaying.gmr');
				debug('Inline audio interval reached');
			}, interval * 60 * 1000);
		}
	}

	/**
	 * Stops the live stream interval timer
	 * Should be called whenever live stream goes from playing to not playing
	 */
	function stopLiveStreamInterval() {
		clearInterval(liveStreamInterval);
		debug('Live stream interval off');
	}

	/**
	 * Stops the inline audio interval timer
	 * Should be called whenever inline audio goes from playing to not playing (including paused)
	 */
	function stopInlineAudioInterval() {
		clearInterval(inlineAudioInterval);
		debug('Inline audio interval off');
	}

	window.tdPlayerApiReady = function() {
		debug("--- TD Player API Loaded ---");
		initPlayer();
	};

	function calcTechPriority() {
		if (bowser.firefox) {
			return ['Flash', 'Html5'];
		} else if (bowser.safari) {
			return ['Html5', 'Flash'];
		} else if (bowser.chrome) {
			return ['Html5', 'Flash'];
		} else {
			return ['Html5', 'Flash'];
		}
	}

	function initPlayer() {
		var container = document.getElementById('td_container'),
			techPriority;

		if (!container) {
			return;
		}

		techPriority = calcTechPriority();
		debug('+++ initPlayer - techPriority = ' + techPriority.join(', '));

		window.player = player = new TDSdk({
			coreModules: [
				{
					id: 'MediaPlayer',
					playerId: 'td_container',
					isDebug: false,
					techPriority: techPriority,
					timeShift: { // timeShifting is currently available on Flash only. Leaving for HTML5 future
						active: 0, /* 1 = active, 0 = inactive */
						max_listening_time: 35 /* If max_listening_time is undefined, the default value will be 30 minutes */
					},
					// set geoTargeting to false on devices in order to remove the daily geoTargeting in browser
					geoTargeting: {desktop: {isActive: false}, iOS: {isActive: false}, android: {isActive: false}},
					plugins: [{id: "vastAd"}]
				},
				{id: 'NowPlayingApi'},
				{id: 'Npe'},
				{id: 'PlayerWebAdmin'},
				{
					id: 'SyncBanners',
					elements: [
						{
							id: window.innerWidth >= 768 ? 'js-audio-ad-inplayer' : 'js-audio-ad-aboveplayer',
							width: 320,
							height: 50
						}
					]
				},
				{id: 'TargetSpot'}
			],
			playerReady: onPlayerReady,
			configurationError: onConfigurationError,
			moduleError: onModuleError
		});
	}

	/**
	 * DO NOT REMOVE THIS FUNCTION --- REQUIRED FOR TRITON API
	 *
	 * load TD Player API asynchronously
	 */
	function loadIdSync(station) {
		var scriptTag = document.createElement('script');
		scriptTag.setAttribute("type", "text/javascript");
		scriptTag.setAttribute("src", "//playerservices.live.streamtheworld.com/api/idsync.js?station=" + station);
		document.getElementsByTagName('head')[0].appendChild(scriptTag);
	}

	function initControlsUi() {
		pauseBtn != null && addEventHandler(pauseBtn, 'click', pauseStream);
		resumeBtn != null && addEventHandler(resumeBtn, 'click', resumeLiveStream);
		$audioStatusListen.click(resumeLiveStream);
	}

	function setPlayingStyles() {
		if (null === tdContainer) {
			return;
		}

		tdContainer.classList.add('stream__active');
		if (true === playingCustomAudio && window.innerWidth <= 767) {
			playBtn.classList.add('live-player__login');
		} else {
			playBtn.classList.add('live-player__muted');
		}

		if (!resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.add('live-player__muted');
		}
		if (resumeBtn.classList.contains('resume__live')) {
			resumeBtn.classList.remove('resume__live');
		}

		$audioStatus.removeClass('-show');
		$audioControls.removeClass('-playing -paused -loading');

		$(nowPlaying).addClass('-show');
		$(listenNow).text('On Air');

		if (true === playingCustomAudio) {
			$audioPodcast.addClass('-show');
			$audioControls.addClass('-playing');
			$('.audio-stream .audio-stream__title').text('SWITCH TO LIVE STREAM');
		} else {
			$audioPodcast.removeClass('-show');
			//$audioControls.addClass('-loading');

			$('.audio-stream .audio-stream__title').each(function() {
				var $this = $(this),
					callSign = $.trim($this.attr('data-callsign')),
					description = $.trim($('.audio-stream__link[data-callsign="' + callSign +'"] .audio-stream__desc').text());

				$this.text(description && description.length > 0 ? description : callSign);
			});
		}

		if (false === playingCustomAudio && loadingBtn != null) {
			loadingBtn.classList.add('loading');
		}
		if (true === playingCustomAudio && pauseBtn != null) {
			if (pauseBtn.classList.contains('live-player__muted')) {
				pauseBtn.classList.remove('live-player__muted');
			}
		} else {
			pauseBtn.classList.add('live-player__muted');
		}

	}

	function setStoppedStyles() {
		if (null === tdContainer) {
			return;
		}

		$audioControls.removeClass('-playing -loading -paused');
		$audioStatus.removeClass('-show');

		clearTrackInfo();

		if (resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.remove('live-player__muted');
			resumeBtn.classList.add('resume__live');
		}

		$(listenNow).addClass('-show').text('Listen Live');
		$(nowPlaying).removeClass('-show');

		pauseBtn.classList.add('live-player__muted');

		hideAdBreakBanner();
	}

	function setPausedStyles() {
		if (null === tdContainer) {
			return;
		}

		//$audioControls.removeClass('-playing -loading');
		//$audioControls.addClass('-paused');
		if (!playingCustomAudio) {
			$audioStatus.addClass('-show');
		}

		if (true === playingCustomAudio && window.innerWidth <= 767) {
			playBtn.classList.add('live-player__login');
		} else {
			playBtn.classList.add('live-player__muted');
		}

		if (body.classList.contains('live-player--active')) {
			body.classList.remove('live-player--active');
		}

		pauseBtn.classList.add('live-player__muted');

		if (resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.remove('live-player__muted');
			resumeBtn.classList.add('resume__live');
		}

		resumeBtn.classList.add('resume__audio');

		hideAdBreakBanner();
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

		$(listenNow).removeClass('-show');
		$(nowPlaying).addClass('-show');
		$audioPodcast.addClass('-show');

		//$audioControls.removeClass('-loading -paused');
		//$audioControls.addClass('-playing');
	}

	function nearestPodcastPlaying(event) {
		var eventTarget = event.target;
		var $podcastPlayer = $(eventTarget).parents('.podcast-player');
		var podcastCover = eventTarget.parentNode;
		var audioCurrent = podcastCover.nextElementSibling;
		var runtimeCurrent = audioCurrent.nextElementSibling;
		var audioTime = $podcastPlayer.find('.podcast__play .audio__time'), i;
		var runtime = document.querySelector('.podcast__runtime');
		var inlineCurrent = podcastCover.parentNode;
		var inlineMeta = inlineCurrent.nextElementSibling;
		var inlineTime = inlineMeta.querySelector('.audio__time');

		$('.playing__current').removeClass('playing__current');

		if (podcastPlayer != null && ( body.classList.contains('single-show') || body.classList.contains('post-type-archive-podcast') || body.classList.contains('single-podcast') || body.classList.contains('home'))) {
			audioCurrent.classList.add('playing__current');
			runtimeCurrent.classList.add('playing');
		} else if (podcastPlayer != null && ! (body.classList.contains('single-show') || body.classList.contains('post-type-archive-podcast') || body.classList.contains('single-podcast'))) {
			audioCurrent.classList.add('playing__current');
			inlineTime.classList.add('playing__current');
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

	var listenLiveStopCustomInlineAudio = function () {
		var listenNowText = listenNow.textContent;

		if (true === playingCustomAudio) {
			customAudio.pause();
			resetInlineAudioStates();
			resetInlineAudioUX();
			playingCustomAudio = false;
			stopInlineAudioInterval();
		}

		if (listenNowText !== 'Listen Live') {
			listenNow.innerHTML = 'Listen Live';
		}

		playLiveStreamDevice();
	};

	function setInitialPlay() {
		lpInit = 1;
		debug('-- Player Initialized By Click ---');
	}

	function setPlayerReady() {
		lpInit = true;
		debug('-- Player Ready to Go ---');
	}

	function playLiveStreamDevice() {
		playingCustomAudio = false;
		if (lpInit === true) {
			setStoppedStyles();
			if (window.innerWidth >= 768) {
				playLiveStream();
			} else {
				playLiveStreamMobile();
			}
		}
	}

	function changePlayerState() {
		if (playBtn != null) {
			addEventHandler(playBtn, 'click', function() {

				if (lpInit === true) {
					setStoppedStyles();
					playLiveStreamDevice();
				} else {
					setInitialPlay();
				}
			});
		}

		if (listenNow != null) {
			addEventHandler(listenNow, 'click', function() {

				if (!livePlaying && !playingCustomAudio) {
					listenLiveStopCustomInlineAudio();
				}
			});
		}
	}

	$document.ready(function() {
		initPlayer();
		changePlayerState();
	});

	function preVastAd() {
		var preRoll = document.getElementById('live-stream__container');

		body.classList.add('vast-ad--playing');

		if (preRoll != null) {
			preRoll.classList.add('vast__pre-roll');
		}
	}

	function postVastAd() {
		var preRoll = document.getElementById('live-stream__container');

		if (body.classList.contains('vast-ad--playing')) {
			body.classList.remove('vast-ad--playing');
		}

		if (preRoll != null) {
			preRoll.classList.remove('vast__pre-roll');
		}
		Cookies.set('gmr_play_live_audio', undefined);
		Cookies.set('gmr_play_live_audio', 1, {expires: 86400});
	}

	function streamVastAd() {
		var stationId = parseInt($('.audio-stream .audio-stream__title').attr('data-station-id'));

		if (isNaN(stationId) || !stationId) {
			onAdPlaybackComplete();
			return;
		}

		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();

		player.playAd('tap', {
			host: 'cmod.live.streamtheworld.com',
			type: 'preroll',
			format: 'vast',
			stationId: stationId,
			trackingParameters: {dist: "debug"}
		});

		setTimeout($.proxy(player.skipAd, player), 35000);
	}

	$window.on('click', function() {
		$('.audio-stream.-open').removeClass('-open');
	});

	$document.on('click', '.audio-stream .audio-stream__title', function(e) {
		e.stopPropagation();
		var audioStream = $(this).parents('.audio-stream');

		if (playingCustomAudio) {
			stopCustomInlineAudio();
			playLiveStreamDevice();
		} else {
			if (audioStream.is('.-multiple')) {
				audioStream.toggleClass('-open');
			}
		}
	});

	$document.on('click', '.audio-stream__item .audio-stream__link', function(e) {
		var $this = $(this),
			callSign = $.trim($this.find('.audio-stream__name').text()),
			description = $.trim($this.find('.audio-stream__desc').text()),
			stationId = $this.attr('data-station-id'),
			$audioStream = $this.parents('.audio-stream');

		e.stopPropagation();

		$audioStream
			.removeClass('-open')
			.find('.audio-stream__title')
			.text(description && description.length > 0 ? description : callSign)
			.attr('data-callsign', callSign)
			.attr('data-station-id', stationId);

		$audioExpandBtn.removeClass('-open');

		$audioMore.attr('href', $audioMore.attr('data-tmpl').split('%s').join(callSign));

		if (livePlaying) {
			player.stop();
			setStoppedStyles();
		}

		if (true === playingCustomAudio) {
			listenLiveStopCustomInlineAudio();
		} else {
			playLiveStreamDevice();
		}
	});

	$audioExpandBtn.click(function(e) {
		e.stopPropagation();

		$('.audio-stream').toggleClass('-open');
		$(this).toggleClass('-open');
	});

	var currentStream = $('.live-player__stream--current-name');

	currentStream.bind('DOMSubtreeModified', function () {
		debug('--- new stream select ---');
		var station = currentStream.text();

		if (livePlaying) {
			player.stop();
		}

		if (true === playingCustomAudio) {
			listenLiveStopCustomInlineAudio();
		}

		playStream(station);

		livePlayer.classList.add('live-player--active');
		setPlayingStyles();
	});

	function getCurrentStation() {
		var station = $.trim($('.audio-stream .audio-stream__title').attr('data-callsign'));

		if (station.length < 1) {
			station = gmr.callsign;
		}

		return station;
	}

	function playLiveStreamMobile() {
		var station = getCurrentStation();

		if (!station) {
			return;
		}

		pjaxInit();
		if (true === playingCustomAudio) {
			listenLiveStopCustomInlineAudio();
		}
		debug('playLiveStream - station=' + station);

//		preVastAd();
		streamVastAd();
		if (player.addEventListener) {
			player.addEventListener('ad-playback-complete', onAdPlaybackComplete);
		} else if (player.attachEvent) {
			player.attachEvent('ad-playback-complete', onAdPlaybackComplete);
		}

	}

	/**
	 * Temp to remove vast ad while issues are resolves
	 */
	function playLiveStreamMobileNoAd() {
		var station = getCurrentStation();

		if (!station) {
			return;
		}

		if (true === playingCustomAudio) {
			listenLiveStopCustomInlineAudio();
		}
		debug('playLiveStream - station=' + station);

		if (livePlaying) {
			player.stop();
		}

		body.classList.add('live-player--active');
		livePlayer.classList.add('live-player--active');
		playStream(station);
		setPlayingStyles();

	}

	function playLiveStream() {
		var station = getCurrentStation();

		if (!station) {
			return;
		}

		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();
			setPlayingStyles();
		} else {
			debug('playLiveStream - station=' + station);

//			preVastAd();
			streamVastAd();
			if (player.addEventListener) {
				player.addEventListener('ad-playback-complete', onAdPlaybackComplete);
			} else if (player.attachEvent) {
				player.attachEvent('ad-playback-complete', onAdPlaybackComplete);
			}
		}
	}

	/**
	 * Temp to remove vast ad while issues are resolves
	 */
	function playLiveStreamNoAd() {
		var station = getCurrentStation();

		if (!station) {
			return;
		}

		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();
			setPlayingStyles();
		} else {
			debug('playLiveStream - station=' + station);

			if (livePlaying) {
				player.stop();
			}

			body.classList.add('live-player--active');
			livePlayer.classList.add('live-player--active');
			playStream(station);
			setPlayingStyles();
		}
	}

	function resumeLiveStream() {
		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else {
			var station = getCurrentStation();

			if (station === '') {
				return;
			}

			debug('playLiveStream - station=' + station);

			if (livePlaying) {
				player.stop();
			}

			livePlayer.classList.add('live-player--active');
			playStream(station);
			setPlayingStyles();
		}
	}

	function pauseStream() {

		if (true === playingCustomAudio) {
			pauseCustomInlineAudio();
			stopInlineAudioInterval();
		} else {
			playingLiveAudio = false;
			player.pause();
			stopLiveStreamInterval();
		}

		if (livePlayer.classList.contains('live-player--active')) {
			livePlayer.classList.remove('live-player--active');
		}
		setPausedStyles();
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

	function onPlayerReady() {
		//Return if MediaPlayer is not loaded properly...
		if (player.MediaPlayer === undefined) {
			return;
		}

		//Listen on companion-load-error event
		//companions.addEventListener("companion-load-error", onCompanionLoadError);
		initControlsUi();

		if (player.addEventListener) {
			player.addEventListener('track-cue-point', onTrackCuePoint);
			player.addEventListener('ad-break-cue-point', onAdBreak);
//			player.addEventListener('ad-break-cue-point-complete', onAdBreakComplete);
			player.addEventListener('ad-break-synced-element', onAdBreakSyncedElement);
			player.addEventListener('stream-track-change', onTrackChange);
			player.addEventListener('hls-cue-point', onHlsCuePoint);

			player.addEventListener('stream-status', onStatus);
			player.addEventListener('stream-geo-blocked', onGeoBlocked);
			player.addEventListener('timeout-alert', onTimeOutAlert);
			player.addEventListener('timeout-reach', onTimeOutReach);
//			player.addEventListener('npe-song', onNPESong);

			player.addEventListener('stream-select', onStreamSelect);

			player.addEventListener('stream-start', onStreamStarted);
			player.addEventListener('stream-stop', onStreamStopped);
		} else if (player.attachEvent) {
			player.attachEvent('track-cue-point', onTrackCuePoint);
			player.attachEvent('ad-break-cue-point', onAdBreak);
//			player.attachEvent('ad-break-cue-point-complete', onAdBreakComplete);
			player.attachEvent('ad-break-synced-element', onAdBreakSyncedElement);
			player.attachEvent('stream-track-change', onTrackChange);
			player.attachEvent('hls-cue-point', onHlsCuePoint);

			player.attachEvent('stream-status', onStatus);
			player.attachEvent('stream-geo-blocked', onGeoBlocked);
			player.attachEvent('timeout-alert', onTimeOutAlert);
			player.attachEvent('timeout-reach', onTimeOutReach);
//			player.attachEvent('npe-song', onNPESong);

			player.attachEvent('stream-select', onStreamSelect);

			player.attachEvent('stream-start', onStreamStarted);
			player.attachEvent('stream-stop', onStreamStopped);
		}

		player.setVolume(1);

		setStatus('Api Ready');
		if (lpInit === 1) {
			setPlayerReady();
			playLiveStreamDevice();
		} else if (Cookies.get('gmlp_play_button_pushed') === 1) {
			setPlayerReady();
			playLiveStreamDevice();
		} else {
			setPlayerReady();
		}
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

		if (bowser.ios) {
			livePlayer.classList.add('no-volume-control');
			$audioVolume.hide();
		} else {
			$audioVolume.find('input[type="range"]').val(getVolume()).change(function() {
				global_volume = parseFloat($(this).val());
				if (isNaN(global_volume)) {
					global_volume = 1;
				}

				if (livePlaying) {
					player.setVolume(global_volume);
				}

				if (customAudio) {
					customAudio.volume = global_volume;
				}

				if (typeof(localStorage) !== "undefined") {
					localStorage.setItem("gmr-live-player-volume", global_volume);
				}

				setVolumeIcon( global_volume );
			});

			$audioVolumeBtn.click(function() {
				$audioVolume.toggleClass('-open');
			});
		}
	}

	/**
	 * Event fired in case the loading of the companion ad returned an error.
	 * @param e
	 */
	function onCompanionLoadError(e) {
		debug('tdplayer::onCompanionLoadError - containerId=' + e.containerId + ', adSpotUrl=' + e.adSpotUrl, true);
	}

	function onAdPlaybackStart(e) {
		debug('******** ad playback start **********');
		debug(e);
		preVastAd();
		adPlaying = true;
		setStatus('Advertising... Type=' + e.data.type);
	}

	function onAdPlaybackComplete() {
		var station = getCurrentStation();

		adPlaying = false;
		$("#td_adserver_bigbox").empty();
		$("#td_adserver_leaderboard").empty();
		setStatus('Ready');

		if (!station) {
			return;
		}

		postVastAd();
		debug("--- ad complete ---");

		if (livePlaying) {
			player.stop();
		}

		body.classList.add('live-player--active');
		livePlayer.classList.add('live-player--active');
		playStream(station);
		setPlayingStyles();
	}

	/**
	 * Custom function to handle when a vast ad fails. This runs when there is an `ad-playback-error` event.
	 *
	 * @param e
	 */
	function adError(e) {
		setStatus('Ready');

		postVastAd();
		var station = getCurrentStation();
		if (livePlaying) {
			player.stop();
		}

		livePlayer.classList.add('live-player--active');
		playStream(station);
		setPlayingStyles();
	}

	function onAdCountdown(e) {
		debug('Ad countdown : ' + e.data.countDown + ' second(s)');
	}

	function onVastProcessComplete(e) {
		debug('Vast Process complete');
//		displayVastCompanionAds(e.data.companions);
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

	function getVolume() {
		var volume = global_volume;

		if (typeof(localStorage) !== "undefined") {
			volume = localStorage.getItem("gmr-live-player-volume");
			if (volume === null) {
				volume = 1;
			} else {
				volume = parseFloat(volume);
				if (isNaN(volume)) {
					volume = 1;
				}
			}
		}

		setVolumeIcon( volume );

		return volume;
	}

	function setVolumeIcon( volume ) {
		volume = typeof volume !== 'undefined' ? volume : 1;

		if ( 0 === volume ) {
			$audioVolumeBtn.attr( 'class', 'audio-volume__btn -off' );
		} else if ( volume > 0 && volume < 0.33 ) {
			$audioVolumeBtn.attr( 'class', 'audio-volume__btn -low' );
		} else if ( volume > 0.33 && volume < 0.66 ) {
			$audioVolumeBtn.attr( 'class', 'audio-volume__btn -medium' );
		} else if ( volume > 0.66 ) {
			$audioVolumeBtn.attr( 'class', 'audio-volume__btn' );
		}
	}

	function onStreamStarted() {
		livePlaying = true;
		playingLiveAudio = true;

		//$audioControls.removeClass('-loading -paused');
		//$audioControls.addClass('-playing');

		if (loadingBtn.classList.contains('loading')) {
			loadingBtn.classList.remove('loading');
		}

		if (pauseBtn.classList.contains('live-player__muted')) {
			pauseBtn.classList.remove('live-player__muted');
		}

		startLiveStreamInterval();

		player.setVolume(getVolume());
	}

	function onStreamSelect() {
		$('#hasHQ').html(player.MediaPlayer.hasHQ().toString());
		$('#isHQ').html(player.MediaPlayer.isHQ().toString());

		$('#hasLow').html(player.MediaPlayer.hasLow().toString());
		$('#isLow').html(player.MediaPlayer.isLow().toString());
	}

	function onStreamStopped() {
		livePlaying = false;
		playingLiveAudio = false;

		$("#trackInfo").html('');
		$("#asyncData").html('');

		$('#hasHQ').html('N/A');
		$('#isHQ').html('N/A');

		$('#hasLow').html('N/A');
		$('#isLow').html('N/A');

		stopLiveStreamInterval();
	}

	function clearTrackInfo() {
		$audioTrackInfo.text('');
		$audioAuthorInfo.text('');
	}

	function setOnAir() {
		clearTrackInfo();
		$(listenNow).addClass('-show').text('On Air');
	}

	function onTrackCuePoint(e) {
		var data = e.data && e.data.cuePoint ? e.data.cuePoint : {},
			duration = parseInt(data.cueTimeDuration);

		debug('New Track cuepoint received');
		debug('Title: ' + data.cueTitle + ' - Artist: ' + data.artistName);

		hideAdBreakBanner();

		if (data.cueTitle || data.artistName) {
			data.cueTitle && $audioTrackInfo.text(data.cueTitle);
			data.artistName && $audioAuthorInfo.text(data.artistName);
			$(listenNow).removeClass('-show');
		}

		if (data.nowplayingURL) {
			player.Npe.loadNpeMetadata(data.nowplayingURL, data.artistName, data.cueTitle);
		}

		if (!isNaN(duration)) {
			trackTimeout && clearTimeout(trackTimeout);

			// set a timeout if duration is longer than a minute
			if (duration > 60000) {
				trackTimeout = setTimeout(setOnAir, duration);
			}
		}

		$(body).trigger("liveAudioTrack.gmr");
	}

	function onTrackChange(e) {
		debug('Stream Track has changed');
		debug('Codec:' + e.data.cuePoint.audioTrack.codec() + ' - Bitrate:' + e.data.cuePoint.audioTrack.bitRate());
	}

	function onHlsCuePoint(e) {
		debug('New HLS cuepoint received');
		debug('Track Id:' + e.data.cuePoint.hlsTrackId + ' SegmentId:' + e.data.cuePoint.hlsSegmentId);
	}

	function hideAdBreakBanner() {
		$audioAdBreakContainerAbovePlayer.hide();
		$audioAdBreakContainerInPlayer.hide();
	}

	function onAdBreak(e) {
		var data = e.data && e.data.adBreakData ? e.data.adBreakData : {};

		debug('New Ad Break cuepoint was received');
		debug('Title: ' + data.cueTitle + ' - URL: ' + data.url + ' - Duration: ' + data.duration);

		setOnAir();
	}

	function onAdBreakComplete() {
		debug('Ad Break complete');
		hideAdBreakBanner();
	}

	function onAdBreakSyncedElement(e) {
		debug('Ad Break Synced Element');

		if (window.innerWidth >= 768) {
			$audioAdBreakContainerInPlayer.show();
		} else {
			$audioAdBreakContainerAbovePlayer.show();
		}

		setTimeout(hideAdBreakBanner, 60000);
	}

	//Song History
	function onListLoaded(e) {
		debug('Song History loaded');

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
	}

	function onModuleError(object) {
		var message = '';

		$.each(object.data.errors, function (i, val) {
			message += 'ERROR : ' + val.data.error.message + '<br/>';
		});

		$("#status").html('<p><span class="label label-important">' + message + '</span><p></p>');
	}

	function onStatus(e) {
		debug('tdplayer::onStatus');
		debug('code: ' + e.data.code);

		// Handle different status events
		switch (e.data.code) {
			case 'LIVE_PAUSE':
				$audioControls.removeClass('-loading -playing');
				$audioControls.addClass('-paused');
			break;

			case 'LIVE_PLAYING':
				$audioControls.removeClass('-loading -paused');
				$audioControls.addClass('-playing');
			break;

			case 'LIVE_STOP':
				$audioControls.removeClass('-loading -playing');
				$audioControls.addClass('-paused');
			break;

			case 'LIVE_FAILED':
				$audioControls.removeClass('-loading -playing');
				$audioControls.addClass('-paused');
			break;

			case 'LIVE_BUFFERING':
				$audioControls.removeClass('-paused -playing');
				$audioControls.addClass('-loading');
			break;

			case 'LIVE_CONNECTING':
				$audioControls.removeClass('-paused -playing');
				$audioControls.addClass('-loading');
			break;

			case 'LIVE_RECONNECTING':
				$audioControls.removeClass('-paused -playing');
				$audioControls.addClass('-loading');
			break;

			case 'STREAM_GEO_BLOCKED':
				$audioControls.removeClass('-loading -playing');
				$audioControls.addClass('-paused');
			break;

			case 'STATION_NOT_FOUND':
				$audioControls.removeClass('-loading -playing');
				$audioControls.addClass('-paused');
			break;
		}

		setStatus(e.data.status);
	}

	function onGeoBlocked(e) {
		debug('tdplayer::onGeoBlocked');

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

		$("#asyncData").html('<br><p><span class="label label-warning">PlayerWebAdmin:</span>');

		var tableContent = '<table class="table table-striped"><thead><tr><th>Key</th><th>Value</th></tr></thead>';

		for (var item in e.data.config) {
			tableContent += "<tr><td>" + item + "</td><td>" + e.data.config[item] + "</td></tr>";
		}

		tableContent += "</table></p>";

		$("#asyncData").html("<div>" + tableContent + "</div>");
	}

	function attachAdListeners() {
		if (player.addEventListener) {
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
		if (player.removeEventListener) {
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
		debug('tdplayer::onNPESong');

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
		debug('tdplayer::onArtistPictureComplete');

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
		if (gmr.debug && window.console) {
			if (error) {
				console.error(info);
			} else {
				console.info('[' + (new Date()).toLocaleString() + ']: ' + info);
			}
		}
	}

	function clearDebugInfo() {
		$('#debugInformation').html('');
	}

	/* Inline Audio Support */
	var stopLiveStreamIfPlaying = function () {
		if ("undefined" !== typeof player && "undefined" !== typeof player.stop) {
			player.stop();
		}
	};

	var resetInlineAudioStates = function () {
		$('.podcast__btn--play.playing').removeClass('playing');
		$('.podcast__btn--pause.playing').removeClass('playing');
	};

	/*
	 * Finds any inline audio players with a matching hash of the current custom audio file, and sets the playing state appropriately
	 */
	var setInlineAudioStates = function () {
		var className = '.mp3-' + customHash;

		$(className + ' .podcast__btn--play').addClass('playing');
		$(className + ' .podcast__btn--pause').addClass('playing');
	};

	var setInlineAudioSrc = function (src) {
		customAudio.src = src;
	};

	var resumeCustomInlineAudio = function () {
		playingCustomAudio = true;
		stopLiveStreamIfPlaying();

		// restart audio if it eneded
		if (customAudio && customAudio.duration == customAudio.currentTime) {
			customAudio.currentTime = 0;
		}

		customAudio.play();
		customAudio.volume = getVolume();

		setPlayerTrackName();
		setPlayerArtist();
		resetInlineAudioStates();
		setPlayingStyles();
		setInlineAudioStates();
		setInlineAudioUX();
		startInlineAudioInterval();
	};

	var playCustomInlineAudio = function (src) {
		pjaxInit();

		playingCustomAudio = true;

		// Only set the src if its different than what is already there, so we can resume the audio with the inline buttons
		if (src !== customAudio.src) {
			setInlineAudioSrc(src);
		}
		resumeCustomInlineAudio();
	};

	var pauseCustomInlineAudio = function () {
		customAudio.pause();
	};

	/*
	 Same as pausing, but sets the "Playing" state to false, to allow resuming live player audio
	 */
	var stopCustomInlineAudio = function () {
		customAudio.pause();
		resetInlineAudioStates();
		playingCustomAudio = false;
		setStoppedStyles();
		resetInlineAudioUX();
		stopInlineAudioInterval();
	};

	var setPlayerTrackName = function () {
		var template = _.template('<div class="now-playing__title"><%- title %></div>'),
			$trackTitleDiv = $('.now-playing__title'),
			$trackTitleWrap = '<div class="audio__title">',
			$time = '</div><div class="audio__time"><span class="audio__time--inline">(</span><div class="audio__time--elapsed"></div><span class="audio__time--inline"> / </span><div class="audio__time--remaining"></div><span class="audio__time--inline">)</span></div>';

		if ($trackTitleDiv.length > 0) {
			$trackTitleDiv.html($trackTitleWrap + customTrack + $time);
		} else {
			$trackInfo.prepend(template({title: customTrack}));
		}

		$audioTrackInfo.text(customTrack);
		$audioAuthorInfo.text('');
	};

	var setPlayerArtist = function () {
		var template = _.template('<div class="now-playing__artist"><%- artist %></div>'),
			$trackArtistDiv = $('.now-playing__artist');

		if ($trackArtistDiv.length > 0) {
			$trackArtistDiv.text(customArtist);
		} else {
			$trackInfo.append(template({artist: customArtist}));
		}
	};

	var setCustomAudioMetadata = function (track, artist, hash) {
		customTrack = track;
		customArtist = artist;
		customHash = hash;

		setPlayerTrackName();
		setPlayerArtist();
		setInlineAudioStates();
	};

	var initCustomAudioPlayer = function () {
		if ("undefined" === typeof Modernizr || !Modernizr.audio) {
			return;
		}

		window.customAudio = customAudio = new Audio();

		var customAudioPaused = function() {
			resetInlineAudioStates();
			setPausedStyles();
			stopInlineAudioInterval();
		};

		var customAudioEnded = function() {
			playingCustomAudio = false;
			resetInlineAudioStates();
			setStoppedStyles();
			stopInlineAudioInterval();
		};

		// Revert the button states back to play once the file is done playing
		if (customAudio.addEventListener) {
			customAudio.addEventListener('pause', customAudioPaused);
			customAudio.addEventListener('ended', customAudioEnded);
		} else if (customAudio.attachEvent) {
			customAudio.attachEvent('pause', customAudioPaused);
			customAudio.attachEvent('ended', customAudioEnded);
		}
	};

	function initInlineAudioUI() {
		if ("undefined" !== typeof Modernizr && Modernizr.audio) {
			var content = document.querySelectorAll('.content'),
				$content = $(content); // Because getElementsByClassName is not supported in IE8 ಠ_ಠ

			$content.on('click', '.podcast__btn--play', function (e) {
				var $play = $(e.currentTarget);

				nearestPodcastPlaying(e);
				playCustomInlineAudio($play.attr('data-mp3-src'));
				resetInlineAudioStates();
				setCustomAudioMetadata($play.attr('data-mp3-title'), $play.attr('data-mp3-artist'), $play.attr('data-mp3-hash'));
			});

			$content.on('click', '.podcast__btn--pause', pauseCustomInlineAudio);

			$audioPodcast.find('input[type="range"]').change(function() {
				if (customAudio) {
					var duration = parseInt(customAudio.duration);
					customAudio.currentTime = Math.floor(duration * parseFloat($(this).val()));
				}
			});
		} else {
			var $meFallbacks = $('.gmr-mediaelement-fallback audio'),
				$customInterfaces = $('.podcast__play');

			$meFallbacks.mediaelementplayer();
			$customInterfaces.hide();
		}
	}

	function pjaxInit() {
		if ($.support.pjax) {
			$(document).pjax('a:not(.ab-item)', '.main', {
				'fragment': '.main',
				'maxCacheLength': 500,
				'timeout': 10000
			});
		}
	}

	/**
	 * Stops pjax if the live player or inline audio has stopped
	 *
	 * @param event
	 */
	function pjaxStop(event) {
		if (!playingLiveAudio && !playingCustomAudio) {
			event.preventDefault();
		}
	}

	$document.bind('pjax:click', pjaxStop);

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

		$audioPodcast.find('input[type="range"]').val(value / 100);
	}

	/**
	 * Enables scrubbing of current audio file
	 */
	$('.audio__progress-bar').click(function(e) {
		var $this = $(this);

		var thisWidth = $this.width();
		var thisOffset = $this.offset();
		var relX = e.pageX - thisOffset.left;
		var seekLocation = Math.floor(( relX / thisWidth ) * customAudio.duration);
		customAudio.currentTime = seekLocation;
	});

	/**
	 * calculates the time of an inline audio element and outputs the time remaining
	 */
	function audioTimeRemaining() {
		var duration = parseInt(customAudio.duration),
			currentTime = parseInt(customAudio.currentTime),
			timeleft = new Date(2000,1,1,0,0,0),
			hours, mins, secs;

		if (isNaN(duration)) {
			duration = currentTime = 0;
		} else if (isNaN(currentTime)) {
			currentTime = 0;
		}

//		timeleft.setSeconds(duration - currentTime);
		timeleft.setSeconds(duration);

		hours = timeleft.getHours();
		mins = ('0' + timeleft.getMinutes()).slice(-2);
		secs = ('0' + timeleft.getSeconds()).slice(-2);
		if (hours > 0) {
			timeleft = hours + ':' + mins + ':' + secs;
		} else {
			timeleft = mins + ':' + secs;
		}

//		$('.podcast__btn--play.playing').parents('.podcast-player').find('.audio__time--remaining').text(timeleft);
		$audioPodcast.find('span:last').text(timeleft);
	}

	/**
	 * calculates the time of an inline audio element and outputs the time that has elapsed
	 */
	function audioTimeElapsed() {
		var passedSeconds = parseInt(customAudio.currentTime),
			currentTime = new Date(2000,1,1,0,0,0),
			hours, mins, secs, i;

		currentTime.setSeconds(isNaN(passedSeconds) ? 0 : passedSeconds);

		hours = currentTime.getHours();
		mins = ('0' + currentTime.getMinutes()).slice(-2);
		secs = ('0' + currentTime.getSeconds()).slice(-2);
		if (hours > 0) {
			currentTime = hours + ':' + mins + ':' + secs;
		} else {
			currentTime = mins + ':' + secs;
		}

//		$('.podcast__btn--play.playing').parents('.podcast-player').find('.audio__time--elapsed').text(currentTime);
		$audioPodcast.find('span:first').text(currentTime);
	}

	initCustomAudioPlayer();
	initInlineAudioUI();

	/**
	 * event listeners for customAudio time
	 */
	customAudio.addEventListener('timeupdate', function () {
		audioUpdateProgress();
		audioTimeElapsed();
		audioTimeRemaining();
	}, false);

	addEventHandler(podcastPlayBtn, 'click', setInlineAudioUX);

	addEventHandler(podcastPauseBtn, 'click', pauseCustomInlineAudio);

	// Ensures our listeners work even after a PJAX load
	$document.on('pjax:end', function () {
		initInlineAudioUI();
		setInlineAudioStates();
		addEventHandler(podcastPlayBtn, 'click', setInlineAudioUX);
		addEventHandler(podcastPauseBtn, 'click', pauseCustomInlineAudio);
	});
})(jQuery, window, document);
