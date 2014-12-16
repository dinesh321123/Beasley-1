(function ($, window, undefined) {
	"use strict";

	var tech = getUrlVars()['tech'];
	var aSyncCuePointFallback = getUrlVars()['aSyncCuePointFallback'] == 'false' ? false : true;

	var player; /* TD player instance */

	var adPlaying; /* boolean - Ad break currently playing */
	var currentTrackCuePoint; /* Current Track */
	var livePlaying; /* boolean - Live stream currently playing */
	var song; /* Song object that wraps NPE data */
	var companions; /* VAST companion banner object */
	var currentStation = ''; /* String - Current station played */

	/**
	 * @todo remove the console log before beta
	 */
	window.tdPlayerApiReady = function () {
		console.log("--- TD Player API Loaded ---")
		initPlayer();
	};

	function initPlayer() {
		var techPriority;
		switch (tech) {
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
		player.addEventListener('player-ready', onPlayerReady);
		player.addEventListener('configuration-error', onConfigurationError);
		player.addEventListener('module-error', onModuleError);
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
	};


	function initControlsUi() {
		//$(document).on('click', 'input[data-action="play-live"]', playLiveAudioStream);

		// custom call to use button instead of input for styling purposes
		var playButton, pauseButton, resumeButton, podcastButton;
		playButton = $('#playButton');
		pauseButton = $('#pauseButton');
		resumeButton = $('#resumeButton');
		podcastButton = $('.mejs-play');

		$(document).on('click', '#playButton', playLiveAudioStream);

		playButton.click(function () {
			playButton.hide();
			pauseButton.show();
		});

		$("#clearDebug").click(function () {
			clearDebugInfo();
		});

		$("#playStreamByUserStationButton").click(function () {
			playStreamByUserStation();
		});

		$("#playUrlButton").click(function () {
			if ($("#streamUrlUser").val() == '') {
				alert('Please enter an url');
				return;
			}

			if (adPlaying)
				player.skipAd();

			if (livePlaying)
				player.stop();

			player.MediaPlayer.tech.playStream({url: $("#streamUrlUser").val(), aSyncCuePoint: {active: false}});
		});

		$(".stop-live").click(function () {
			stopStream();
		});

		pauseButton.click(function () {
			pauseButton.hide();
			resumeButton.show();
			pauseStream();
		});

		podcastButton.click(function () {
			pauseButton.hide();
			resumeButton.show();
			pauseStream();
		});

		resumeButton.click(function () {
			resumeButton.hide();
			pauseButton.show();
			seekLive();
		});

		$("#seekLiveButton").click(function () {
			seekLive();
		});

		$("#muteButton").click(function () {
			mute();
		});

		$("#unMuteButton").click(function () {
			unMute();
		});

		$("#setVolume50Button").click(function () {
			setVolume50();
		});

		$("#getArtistButton").click(function () {
			getArtistData();
		});


	}

	function loggedInGigyaUser() {
		if (!gmr.logged_in) {
			console.log("--- Log In with Gigya ---");
		} else {
			console.log("--- You are logged in, so now enjoy some music ---");
			streamVastAd();
			player.addEventListener('ad-playback-complete', function() {
				console.log("--- add complete ---");
				var station = gmr.callsign;
				var tdContainer = document.getElementById('td_container');
				var playButton = document.getElementById('playButton');
				var pauseButton = document.getElementById('pauseButton');
				var listenNow = document.getElementById('live-stream__listen-now');
				var nowPlaying = document.getElementById('live-stream__now-playing');

				if (station == '') {
					alert('Please enter a Station');
					return;
				}

				debug('playLiveAudioStream - station=' + station);

				if (livePlaying)
					player.stop();

				player.play({station: station, timeShift: true});
				tdContainer.classList.add('stream__active');
				playButton.style.display = 'none';
				pauseButton.style.display = 'block';
				listenNow.style.display = 'none';
				nowPlaying.style.display = 'inline-block';
			});
		}
	}

	function streamVastAd() {
		var vastUrl = gmr.streamUrl;

		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('vastAd', {url: vastUrl});
	}

	function playLiveAudioStream(event) {
		event.preventDefault();

		var station = $(event.target).data('station');

		if (station == '') {
			alert('Please enter a Station');
			return;
		}

		debug('playLiveAudioStream - station=' + station);

		$('#stationUser').val('');

		if (livePlaying)
			player.stop();

		player.play({station: station, timeShift: true});
	}

	function playStreamByUserStation() {
		if ($("#stationUser").val() == '') {
			alert('Please enter a Station');
			return;
		}

		if (adPlaying)
			player.skipAd();

		if (livePlaying)
			player.stop();

		player.play({station: $("#stationUser").val(), timeShift: true});

		if (currentStation != $("#stationUser").val()) {
			currentStation = $("#stationUser").val();
			loadIdSync(currentStation);
		}
	}

	function stopStream() {
		player.stop();
	}

	function pauseStream() {
		player.pause();
	}

	function resumeStream() {
		if (livePlaying)
			player.resume();
		else
			player.play();
	}

	function seekLive() {
		player.seekLive();
	}

	function loadNpApi() {
		if ($("#songHistoryCallsignUser").val() == '') {
			alert('Please enter a Callsign');
			return;
		}

		var isHd = ( $("#songHistoryConnectionTypeSelect").val() == 'hdConnection' );

		//Set the hd parameter to true if the station has AAC. Set it to false if the station has no AAC.
		player.NowPlayingApi.load({mount: $("#songHistoryCallsignUser").val(), hd: isHd, numberToFetch: 15});
	}

	function setVolume50() {
		player.setVolume(.5);
	}

	function mute() {
		player.mute();
	}

	function unMute() {
		player.unMute();
	}

	function getArtistData() {
		if (song && song.artist() != null)
			song.artist().fetchData();
	}

	function onPlayerReady() {
		//Return if MediaPlayer is not loaded properly...
		if (player.MediaPlayer == undefined) return;

		//Listen on companion-load-error event
		//companions.addEventListener("companion-load-error", onCompanionLoadError);

		loggedInGigyaUser();
		initControlsUi();

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

		player.setVolume(1); //Set volume to 100%

		setStatus('Api Ready');
		setTech(player.MediaPlayer.tech.type);

		player.addEventListener('list-loaded', onListLoaded);
		player.addEventListener('list-empty', onListEmpty);
		player.addEventListener('nowplaying-api-error', onNowPlayingApiError);

		$("#fetchSongHistoryByUserCallsignButton").click(function () {
			loadNpApi();
		});

		player.addEventListener('pwa-data-loaded', onPwaDataLoaded);

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

			if (bigboxIndex > -1)
				companions.loadVASTCompanionAd('td_adserver_bigbox', vastCompanions[bigboxIndex]);

			if (leaderboardIndex > -1)
				companions.loadVASTCompanionAd('td_adserver_leaderboard', vastCompanions[leaderboardIndex]);
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

		if (currentTrackCuePoint && currentTrackCuePoint != e.data.cuePoint)
			clearNpe();

		if (e.data.cuePoint.nowplayingURL)
			player.Npe.loadNpeMetadata(e.data.cuePoint.nowplayingURL, e.data.cuePoint.artistName, e.data.cuePoint.cueTitle);

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

		if (player.flash.available)
			techInfo += ' - Your current version of flash plugin is: ' + player.flash.version.major + '.' + player.flash.version.minor + '.' + player.flash.version.rev;

		techInfo += '</span></p>';

		$("#techInfo").html(techInfo);
	}

	function loadPwaData() {
		if ($("#pwaCallsign").val() == '' || $("#pwaStreamId").val() == '') {
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
		if ($("#runSpotId").val() == '') return;

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
		if ($("#vastAdUrl").val() == '') return;

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
		player.addEventListener('ad-playback-start', onAdPlaybackStart);
		player.addEventListener('ad-playback-error', onAdPlaybackComplete);
		player.addEventListener('ad-playback-complete', onAdPlaybackComplete);
		player.addEventListener('ad-countdown', onAdCountdown);
		player.addEventListener('vast-process-complete', onVastProcessComplete);
		player.addEventListener('vpaid-ad-companions', onVpaidAdCompanions);
	}

	function detachAdListeners() {
		player.removeEventListener('ad-playback-start', onAdPlaybackStart);
		player.removeEventListener('ad-playback-error', onAdPlaybackComplete);
		player.removeEventListener('ad-playback-complete', onAdPlaybackComplete);
		player.removeEventListener('ad-countdown', onAdCountdown);
		player.removeEventListener('vast-process-complete', onVastProcessComplete);
		player.removeEventListener('vpaid-ad-companions', onVpaidAdCompanions);
	}

	var artist;

	function onNPESong(e) {
		console.log('tdplayer::onNPESong');
		console.log(e);

		song = e.data.song;

		artist = song.artist();
		artist.addEventListener('artist-complete', onArtistComplete);

		var songData = getNPEData();

		displayNpeInfo(songData, false);
	}

	function displayNpeInfo(songData, asyncData) {
		$("#asyncData").empty();

		var id = asyncData ? 'asyncData' : 'npeInfo';
		var list = $("#" + id);

		if (asyncData == false)
			list.html('<span class="label label-inverse">Npe Info:</span>');

		list.append(songData);
	}

	function onArtistComplete(e) {
		artist.addEventListener('picture-complete', onArtistPictureComplete);

		var pictures = artist.getPictures();
		var picturesIds = [];
		for (var i = 0; i < pictures.length; i++) {
			picturesIds.push(pictures[i].id);
		}
		if (picturesIds.length > 0)
			artist.fetchPictureByIds(picturesIds);

		var songData = getArtist();

		displayNpeInfo(songData, true);
	}

	function onArtistPictureComplete(pictures) {
		console.log('tdplayer::onArtistPictureComplete');
		console.log(pictures);

		var songData = '<span class="label label-inverse">Photos:</span><br>';

		for (var i = 0; i < pictures.length; i++) {
			if (pictures[i].getFiles())
				songData += '<a href="' + pictures[i].getFiles()[0].url + '" rel="lightbox[npe]" title="Click on the right side of the image to move forward."><img src="' + pictures[i].getFiles()[0].url + '" width="125" /></a>&nbsp;';
		}

		$("#asyncData").append(songData);
	}

	function getArtist() {
		if (song != undefined) {
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
			for (var i = 0; i < similars.length; i++) {
				songData += '<li>Similar artist ' + ( i + 1 ) + ': ' + similars[i].name + '</li>';
			}
			var members = song.artist().getMembers();
			for (var i = 0; i < members.length; i++) {
				songData += '<li>Member ' + ( i + 1 ) + ': ' + members[i].name + '</li>';
			}

			songData += '<li>Artist website: ' + song.artist().getWebsite() + '</li>';
			songData += '<li>Artist twitter: ' + song.artist().getTwitterUsername() + '</li>';
			songData += '<li>Artist facebook: ' + song.artist().getFacebookUrl() + '</li>';
			songData += '<li>Artist biography: ' + song.artist().getBiography().substring(0, 2000) + '...</small>';

			var genres = song.artist().getGenres();
			for (var i = 0; i < genres.length; i++) {
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

		if (song != undefined && song.album()) {
			var _iTunesLink = '';
			if (song.album().getBuyUrl() != null)
				_iTunesLink = '<a target="_blank" title="' + song.album().getBuyUrl() + '" href="' + song.album().getBuyUrl() + '">Buy on iTunes</a><br/>';

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

		if (error)
			console.error(info);
		else
			console.log(info);

		$('#debugInformation').append(info);
		$('#debugInformation').append('\n');
	}

	function clearDebugInfo() {
		$('#debugInformation').html('');
	}

})(jQuery, window);
