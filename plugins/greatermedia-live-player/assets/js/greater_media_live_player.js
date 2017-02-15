/*!
  * Bowser - a browser detector
  * https://github.com/ded/bowser
  * MIT License | (c) Dustin Diaz 2014
  */

!function (name, definition) {
  if (typeof module != 'undefined' && module.exports) module.exports['browser'] = definition()
  else if (typeof define == 'function' && define.amd) define(definition)
  else this[name] = definition()
}('bowser', function () {
  /**
    * See useragents.js for examples of navigator.userAgent
    */

  var t = true

  function detect(ua) {

    function getFirstMatch(regex) {
      var match = ua.match(regex);
      return (match && match.length > 1 && match[1]) || '';
    }

    var iosdevice = getFirstMatch(/(ipod|iphone|ipad)/i).toLowerCase()
      , likeAndroid = /like android/i.test(ua)
      , android = !likeAndroid && /android/i.test(ua)
      , versionIdentifier = getFirstMatch(/version\/(\d+(\.\d+)?)/i)
      , tablet = /tablet/i.test(ua)
      , mobile = !tablet && /[^-]mobi/i.test(ua)
      , result

    if (/opera|opr/i.test(ua)) {
      result = {
        name: 'Opera'
      , opera: t
      , version: versionIdentifier || getFirstMatch(/(?:opera|opr)[\s\/](\d+(\.\d+)?)/i)
      }
    }
    else if (/windows phone/i.test(ua)) {
      result = {
        name: 'Windows Phone'
      , windowsphone: t
      , msie: t
      , version: getFirstMatch(/iemobile\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/msie|trident/i.test(ua)) {
      result = {
        name: 'Internet Explorer'
      , msie: t
      , version: getFirstMatch(/(?:msie |rv:)(\d+(\.\d+)?)/i)
      }
    }
    else if (/chrome|crios|crmo/i.test(ua)) {
      result = {
        name: 'Chrome'
      , chrome: t
      , version: getFirstMatch(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)
      }
    }
    else if (iosdevice) {
      result = {
        name : iosdevice == 'iphone' ? 'iPhone' : iosdevice == 'ipad' ? 'iPad' : 'iPod'
      }
      // WTF: version is not part of user agent in web apps
      if (versionIdentifier) {
        result.version = versionIdentifier
      }
    }
    else if (/sailfish/i.test(ua)) {
      result = {
        name: 'Sailfish'
      , sailfish: t
      , version: getFirstMatch(/sailfish\s?browser\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/seamonkey\//i.test(ua)) {
      result = {
        name: 'SeaMonkey'
      , seamonkey: t
      , version: getFirstMatch(/seamonkey\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/firefox|iceweasel/i.test(ua)) {
      result = {
        name: 'Firefox'
      , firefox: t
      , version: getFirstMatch(/(?:firefox|iceweasel)[ \/](\d+(\.\d+)?)/i)
      }
      if (/\((mobile|tablet);[^\)]*rv:[\d\.]+\)/i.test(ua)) {
        result.firefoxos = t
      }
    }
    else if (/silk/i.test(ua)) {
      result =  {
        name: 'Amazon Silk'
      , silk: t
      , version : getFirstMatch(/silk\/(\d+(\.\d+)?)/i)
      }
    }
    else if (android) {
      result = {
        name: 'Android'
      , version: versionIdentifier
      }
    }
    else if (/phantom/i.test(ua)) {
      result = {
        name: 'PhantomJS'
      , phantom: t
      , version: getFirstMatch(/phantomjs\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/blackberry|\bbb\d+/i.test(ua) || /rim\stablet/i.test(ua)) {
      result = {
        name: 'BlackBerry'
      , blackberry: t
      , version: versionIdentifier || getFirstMatch(/blackberry[\d]+\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/(web|hpw)os/i.test(ua)) {
      result = {
        name: 'WebOS'
      , webos: t
      , version: versionIdentifier || getFirstMatch(/w(?:eb)?osbrowser\/(\d+(\.\d+)?)/i)
      };
      /touchpad\//i.test(ua) && (result.touchpad = t)
    }
    else if (/bada/i.test(ua)) {
      result = {
        name: 'Bada'
      , bada: t
      , version: getFirstMatch(/dolfin\/(\d+(\.\d+)?)/i)
      };
    }
    else if (/tizen/i.test(ua)) {
      result = {
        name: 'Tizen'
      , tizen: t
      , version: getFirstMatch(/(?:tizen\s?)?browser\/(\d+(\.\d+)?)/i) || versionIdentifier
      };
    }
    else if (/safari/i.test(ua)) {
      result = {
        name: 'Safari'
      , safari: t
      , version: versionIdentifier
      }
    }
    else result = {}

    // set webkit or gecko flag for browsers based on these engines
    if (/(apple)?webkit/i.test(ua)) {
      result.name = result.name || "Webkit"
      result.webkit = t
      if (!result.version && versionIdentifier) {
        result.version = versionIdentifier
      }
    } else if (!result.opera && /gecko\//i.test(ua)) {
      result.name = result.name || "Gecko"
      result.gecko = t
      result.version = result.version || getFirstMatch(/gecko\/(\d+(\.\d+)?)/i)
    }

    // set OS flags for platforms that have multiple browsers
    if (android || result.silk) {
      result.android = t
    } else if (iosdevice) {
      result[iosdevice] = t
      result.ios = t
    }

    // OS version extraction
    var osVersion = '';
    if (iosdevice) {
      osVersion = getFirstMatch(/os (\d+([_\s]\d+)*) like mac os x/i);
      osVersion = osVersion.replace(/[_\s]/g, '.');
    } else if (android) {
      osVersion = getFirstMatch(/android[ \/-](\d+(\.\d+)*)/i);
    } else if (result.windowsphone) {
      osVersion = getFirstMatch(/windows phone (?:os)?\s?(\d+(\.\d+)*)/i);
    } else if (result.webos) {
      osVersion = getFirstMatch(/(?:web|hpw)os\/(\d+(\.\d+)*)/i);
    } else if (result.blackberry) {
      osVersion = getFirstMatch(/rim\stablet\sos\s(\d+(\.\d+)*)/i);
    } else if (result.bada) {
      osVersion = getFirstMatch(/bada\/(\d+(\.\d+)*)/i);
    } else if (result.tizen) {
      osVersion = getFirstMatch(/tizen[\/\s](\d+(\.\d+)*)/i);
    }
    if (osVersion) {
      result.osversion = osVersion;
    }

    // device type extraction
    var osMajorVersion = osVersion.split('.')[0];
    if (tablet || iosdevice == 'ipad' || (android && (osMajorVersion == 3 || (osMajorVersion == 4 && !mobile))) || result.silk) {
      result.tablet = t
    } else if (mobile || iosdevice == 'iphone' || iosdevice == 'ipod' || android || result.blackberry || result.webos || result.bada) {
      result.mobile = t
    }

    // Graded Browser Support
    // http://developer.yahoo.com/yui/articles/gbs
    if ((result.msie && result.version >= 10) ||
        (result.chrome && result.version >= 20) ||
        (result.firefox && result.version >= 20.0) ||
        (result.safari && result.version >= 6) ||
        (result.opera && result.version >= 10.0) ||
        (result.ios && result.osversion && result.osversion.split(".")[0] >= 6) ||
        (result.blackberry && result.version >= 10.1)
        ) {
      result.a = t;
    }
    else if ((result.msie && result.version < 10) ||
        (result.chrome && result.version < 20) ||
        (result.firefox && result.version < 20.0) ||
        (result.safari && result.version < 6) ||
        (result.opera && result.version < 10.0) ||
        (result.ios && result.osversion && result.osversion.split(".")[0] < 6)
        ) {
      result.c = t
    } else result.x = t

    return result
  }

  var bowser = detect(typeof navigator !== 'undefined' ? navigator.userAgent : '')


  /*
   * Set our detect method to the main bowser object so we can
   * reuse it to test other user agents.
   * This is needed to implement future tests.
   */
  bowser._detect = detect;

  return bowser
});

(function($, window, undefined) {
	"use strict";

	// variables
	var document = window.document,
		body = document.querySelectorAll('body'),
		playButton = $('#playButton'),
		pauseButton = $('#pauseButton'),
		resumeButton = $('#resumeButton'),
		listenNow = $('#live-stream__listen-now'),
		listenLogin = $('#live-stream__login'),
		accountLogin = $('.header__account--btn');

	/**
	 *
	 * Pjax is running against the DOM. By default pjax detects a click event, and this case, we are targeting all `a`
	 * links in the `.main` element. This will run pjax against the first link clicked. After the initial link is
	 * clicked, pjax will stop.
	 *
	 * It is important to call pjax against the `.main` element. Initially we used `.page-wrap` but this caused elements
	 * that had click events attached to them to not function.
	 *
	 * To prevent pjax from stopping, we introduce some pjax `options`.
	 * The `fragment` allows for pjax to continue to detect clicks within the same element, in this case `.main`,
	 * that we initially are calling pjax against. This ensures that pjax continues to run.
	 * `maxCacheLength` is the maximum cache size for the previous container contents.
	 * `timeout` is the ajax timeout in milliseconds after which a full refresh is forced.
	 *
	 * If a user is logged into WordPress, pjax will not work. To resolve this, we run a check that is part of the `else
	 * if` statement that runs a localized variable from the PHP Class `GMLP_Player` in the Greater Media Live player
	 * plugin folder>includes>class-gmlp-player.php. This variable is `gmlp.logged_in` and checks if a user is logged
	 * in with WordPress. If a user is logged in with WordPress, we change the element that pjax is targeting to
	 * `.page-wrap`.
	 *
	 * @summary Detects if a user is authenticated, then runs pjax against `a` links in `.page-wrap`
	 *
	 * @event click
	 * @fires pjax
	 *
	 * @see https://github.com/defunkt/jquery-pjax
	 */
	function pjaxInit() {
		if ($.support.pjax) {
			$(document).pjax('a:not(.ab-item)', '.main', {
				'fragment': '.main',
				'maxCacheLength': 500,
				'timeout': 10000
			});
		}
	}

	playButton.on('click', function() {
		pjaxInit();
	});

	resumeButton.on('click', function() {
		pjaxInit();
	});
})(jQuery, window);
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
	var currentTrackCuePoint;
	/* Current Track */
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

	/**
	 * @todo remove the console log before beta
	 */
	window.tdPlayerApiReady = function () {
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
		var techPriority = calcTechPriority();
		debug('+++ initPlayer - techPriority = ' + techPriority.join(', '));

		/* TD player configuration object used to create player instance */
		var tdPlayerConfig = {
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
				{id: 'SyncBanners', elements: [{id: 'td_synced_bigbox', width: 300, height: 250}]},
				{id: 'TargetSpot'}
			]
		};

		require(['tdapi/base/util/Companions'], function (Companions) {
				companions = new Companions();
			}
		);

		window.player = player = new TdPlayerApi(tdPlayerConfig);
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

		if (pauseBtn != null) {
			addEventHandler(pauseBtn, 'click', pauseStream);
		}

		if (resumeBtn != null) {
			addEventHandler(resumeBtn, 'click', resumeLiveStream);
		}

		if (clearDebug != null) {
			addEventHandler(clearDebug, 'click', clearDebugInfo);
		}

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

		$audioControls.removeClass('-playing -paused');
		$audioControls.addClass('-loading');
		$audioStatus.removeClass('-show');

		if (!resumeBtn.classList.contains('live-player__muted')) {
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

		$audioTrackInfo.text('');
		$audioAuthorInfo.text('');

		if (resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.remove('live-player__muted');
			resumeBtn.classList.add('resume__live');
		}
		listenNow.style.display = 'inline-block';
		nowPlaying.style.display = 'none';
		pauseBtn.classList.add('live-player__muted');
	}

	function setPausedStyles() {
		if (null === tdContainer) {
			return;
		}

		$audioControls.removeClass('-playing -loading');
		$audioControls.addClass('-paused');
		$audioStatus.addClass('-show');

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
			setTimeout(function () {
				listenNow.innerHTML = 'Switch to Live Stream';
			}, 1000);
		}
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

	function replaceNPInfo() {
		if (window.innerWidth <= 767) {
			if (trackInfo.innerHTML === '') {
				onAir.classList.add('on-air__npe');
				liveStreamSelector.classList.add('full__width');
			} else if (onAir.classList.contains('on-air__npe')) {
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

	var listenLiveStopCustomInlineAudio = function () {
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
			stopInlineAudioInterval();
		}
		if (listenNowText === 'Switch to Live Stream') {
			listenNow.innerHTML = 'Listen Live';
		}
		if (window.innerWidth >= 768) {
			playLiveStream();
		}
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
			addEventHandler(playBtn, 'click', function(){
				if (lpInit === true) {
					setStoppedStyles();
					if (window.innerWidth >= 768) {
						playLiveStream();
					} else {
						playLiveStreamMobile();
					}
				} else {
					setInitialPlay();
				}
			});
		}
		if (listenNow != null) {
			addEventHandler(listenNow, 'click', listenLiveStopCustomInlineAudio);
		}
	}

	$document.ready(changePlayerState);

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
		var vastUrl = gmr.streamUrl;

		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('vastAd', {url: vastUrl});
		setTimeout(function() {
			this.stop();
		}, 25000);
	}

	$window.on('click', function() {
		$('.audio-stream.-open').removeClass('-open');
	});

	$document.on('click', '.audio-stream.-multiple .audio-stream__title', function(e) {
		e.stopPropagation();
		$(this).parents('.audio-stream').toggleClass('-open');
	});

	$document.on('click', '.audio-stream__item', function(e) {
		var $this = $(this),
			callSign = $this.find('.audio-stream__name').text(),
			$audioStream = $this.parents('.audio-stream');

		e.stopPropagation();

		$audioStream.find('.audio-stream__title').text(callSign);
		$audioStream.removeClass('-open');

		if (livePlaying) {
			player.stop();
			setStoppedStyles();
		}

		if (true === playingCustomAudio) {
			listenLiveStopCustomInlineAudio();
		}

		playStream(callSign);

		livePlayer.classList.add('live-player--active');
		setPlayingStyles();
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

	function playLiveStreamMobile() {
		var station = gmr.callsign;

		pjaxInit();
		if (station === '') {
			alert('Please enter a Station');
			return;
		}
		if (true === playingCustomAudio) {
			listenLiveStopCustomInlineAudio();
		}
		debug('playLiveStream - station=' + station);

		preVastAd();
		streamVastAd();
		if (player.addEventListener) {
			player.addEventListener('ad-playback-complete', function () {
				postVastAd();
				debug("--- ad complete ---");

				if (livePlaying) {
					player.stop();
				}

				body.classList.add('live-player--active');
				livePlayer.classList.add('live-player--active');
				playStream(station);
				setPlayingStyles();
			});
		} else if (player.attachEvent) {
			player.attachEvent('ad-playback-complete', function () {
				postVastAd();
				debug("--- ad complete ---");

				if (livePlaying) {
					player.stop();
				}

				body.classList.add('live-player--active');
				livePlayer.classList.add('live-player--active');
				playStream(station);
				setPlayingStyles();
			});
		}

	}

	/**
	 * Temp to remove vast ad while issues are resolves
	 */
	function playLiveStreamMobileNoAd() {
		var station = gmr.callsign;

		if (station === '') {
			alert('Please enter a Station');
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
		var station = gmr.callsign;

		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else {

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
					debug("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					body.classList.add('live-player--active');
					livePlayer.classList.add('live-player--active');
					playStream(station);
					setPlayingStyles();
				});
			} else if (player.attachEvent) {
				player.attachEvent('ad-playback-complete', function () {
					postVastAd();
					debug("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					body.classList.add('live-player--active');
					livePlayer.classList.add('live-player--active');
					playStream(station);
					setPlayingStyles();
				});
			}
		}
	}

	/**
	 * Temp to remove vast ad while issues are resolves
	 */
	function playLiveStreamNoAd() {
		var station = gmr.callsign;

		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else {

			if (station === '') {
				alert('Please enter a Station');
				return;
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
	}

	function resumeLiveStream() {
		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();

			setPlayingStyles();
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

			player.addEventListener('stream-config-error', streamError);
			player.addEventListener('stream-config-load-error', streamError);
			player.addEventListener('stream-fail', streamError);
			player.addEventListener('stream-error', streamError);
		} else if (player.attachEvent) {
			player.attachEvent('track-cue-point', onTrackCuePoint);
			player.attachEvent('ad-break-cue-point', onAdBreak);
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

			player.attachEvent('stream-config-error', streamError);
			player.attachEvent('stream-config-load-error', streamError);
			player.attachEvent('stream-fail', streamError);
			player.attachEvent('stream-error', streamError);
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
			});

			$audioVolumeBtn.click(function() {
				$audioVolume.toggleClass('-open');
			});
		}
	}

	function streamError(e) {
		debug('Stream error', 1);
		debug(e);
	}

	/**
	 * Event fired in case the loading of the companion ad returned an error.
	 * @param e
	 */
	function onCompanionLoadError(e) {
		debug('tdplayer::onCompanionLoadError - containerId=' + e.containerId + ', adSpotUrl=' + e.adSpotUrl, true);
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
		playStream(station);
		setPlayingStyles();
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

		return volume;
	}

	function onStreamStarted() {
		livePlaying = true;
		playingLiveAudio = true;

		$audioControls.removeClass('-loading -paused');
		$audioControls.addClass('-playing');

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

		clearNpe();
		$("#trackInfo").html('');
		$("#asyncData").html('');

		$('#hasHQ').html('N/A');
		$('#isHQ').html('N/A');

		$('#hasLow').html('N/A');
		$('#isLow').html('N/A');

		stopLiveStreamInterval();
	}

	function onTrackCuePoint(e) {
		debug('New Track cuepoint received');
		debug('Title: ' + e.data.cuePoint.cueTitle + ' - Artist: ' + e.data.cuePoint.artistName);

		$audioTrackInfo.text(e.data.cuePoint.cueTitle);
		$audioAuthorInfo.text(e.data.cuePoint.artistName);

		if (currentTrackCuePoint && currentTrackCuePoint != e.data.cuePoint) {
			clearNpe();
		}

		if (e.data.cuePoint.nowplayingURL) {
			player.Npe.loadNpeMetadata(e.data.cuePoint.nowplayingURL, e.data.cuePoint.artistName, e.data.cuePoint.cueTitle);
		}

		currentTrackCuePoint = e.data.cuePoint;

		$("#trackInfo").html('<div class="now-playing__title">' + currentTrackCuePoint.cueTitle + '</div><div class="now-playing__artist">' + currentTrackCuePoint.artistName + '</div>');

		setTimeout(replaceNPInfo, 10000);
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

	function onAdBreak(e) {
		setStatus('Commercial break...');
	}

	function clearNpe() {
		$("#npeInfo").html('');
		$("#asyncData").html('');
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
		if (!gmr.debug) {
			return;
		}

		if (window.console) {
			if (error) {
				console.error(info);
			} else {
				console.log(info);
			}
		}

		$('#debugInformation').append(info);
		$('#debugInformation').append('\n');
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

		// Only set the src if its different than what is already there, so we can resume the audio with the inline buttons
		if (src !== customAudio.src) {
			setInlineAudioSrc(src);
		}
		resumeCustomInlineAudio();
	};

	var pauseCustomInlineAudio = function () {
		customAudio.pause();
		resetInlineAudioStates();
		playingCustomAudio = false;
		setPausedStyles();
		stopInlineAudioInterval();
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
		if ("undefined" !== typeof Modernizr && Modernizr.audio) {
			customAudio = new Audio();

			// Revert the button states back to play once the file is done playing
			if (customAudio.addEventListener) {
				customAudio.addEventListener('ended', function () {
					resetInlineAudioStates();
					setPausedStyles();
					stopInlineAudioInterval();
				});
			} else if (customAudio.attachEvent) {
				customAudio.attachEvent('ended', function () {
					resetInlineAudioStates();
					setPausedStyles();
					stopInlineAudioInterval();
				});
			}

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
		if (playingLiveAudio === true || true === playingCustomAudio) {
			// do nothing
		} else {
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
		var ramainings = document.querySelectorAll('.audio__time--remaining'), i,
			duration = parseInt(customAudio.duration),
			currentTime = parseInt(customAudio.currentTime),
			timeleft = new Date(2000,1,1,0,0,0),
			hours, mins, secs;

		if (isNaN(duration)) {
			duration = currentTime = 0;
		} else if (isNaN(currentTime)) {
			currentTime = 0;
		}

		timeleft.setSeconds(duration - currentTime);

		hours = timeleft.getHours();
		mins = ('0' + timeleft.getMinutes()).slice(-2);
		secs = ('0' + timeleft.getSeconds()).slice(-2);
		if (hours > 0) {
			timeleft = hours + ':' + mins + ':' + secs;
		} else {
			timeleft = mins + ':' + secs;
		}

		for (i = 0; i < ramainings.length; ++i) {
			ramainings[i].innerHTML = timeleft;
		}
	}

	/**
	 * calculates the time of an inline audio element and outputs the time that has elapsed
	 */
	function audioTimeElapsed() {
		var timeline = document.querySelectorAll('.audio__time--elapsed'),
			passedSeconds = parseInt(customAudio.currentTime),
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

		for (i = 0; i < timeline.length; ++i) {
			timeline[i].innerHTML = currentTime;
		}
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
