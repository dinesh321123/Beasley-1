(function() {
	var ggComObj;

	// Nielsen SDK event codes:
	//  5 - play
	//  6 - pause
	//  7 - stop
	//  8 - position change
	//  9 - mute
	// 10 - fullscreen
	// 11 - volume change
	// 15 - load Metadata
	// 49 - set Playhead Position
	// 55 - timed Metadata

	window.bindNielsenSDKEvents = function(beacon, player) {
		var hasAddEventListener = player.addEventListener ? true : false,
			events = {
				'track-cue-point': onTrackCuePoint,
				'ad-break-cue-point': onAdBreakCuePoint,
				'stream-stop': onStreamStop
			};

		ggComObj = new NielsenSDKggCom(beacon, player);

		for (var event in events) {
			if (hasAddEventListener) {
				player.addEventListener(event, events[event]);
			} else {
				player.attachEvent(event, events[event]);
			}
		}
	};

	function NielsenSDKggCom(beacon, player) {
		var that = this;

		that.gg = beacon;
		that.player = player;
		that.is_playing = false;
	}

	var onAdBreakCuePoint = function(e) {
		var data = e.data.adBreakData;

		if (ggComObj.is_playing) {
			onStreamStop();
		}

		debug('Send ad block cue metadata event to Nielsen SDK.');
		ggComObj.gg.ggPM(15, {
			assetid: data.cueID,
			title: data.cueTitle,
			length: data.duration / 1000, // convert to seconds
			type: 'midroll'
		});

		debug('Send playhead position event to Nielsen SDK.');
		ggComObj.gg.ggPM(49, Date.now() / 1000);

		ggComObj.is_playing = true;
	};

	var onTrackCuePoint = function(e) {
		var data = e.data.cuePoint;

		if (ggComObj.is_playing) {
			onStreamStop();
		}

		debug('Send track cue metadata event to Nielsen SDK.');
		ggComObj.gg.ggPM(15, {
			assetid: data.cueID,
			title: data.cueTitle,
			length: data.cueTimeDuration,
			type: 'content'
		});

		debug('Send playhead position event to Nielsen SDK.');
		ggComObj.gg.ggPM(49, Date.now() / 1000);

		ggComObj.is_playing = true;
	};

	var onStreamStop = function() {
		if (ggComObj.is_playing) {
			debug('Send stop event to Nielsen SDK.');
			
			ggComObj.gg.ggPM(7, Date.now() / 1000);
			ggComObj.is_playing = false;
		}
	};

	var debug = function(info) {
		if (gmr.debug && console) {
			console.log(info);
		}
	};
})();