(function($, playerjs) {
	var $document = $(document);

	var connectToEmbeds = function() {
		$('iframe[src^="https://omny.fm/"]').each(function () {
			var player = new playerjs.Player(this);

			player.on(playerjs.EVENTS.READY, function() {
				var pauseOnCustomAudioPlay = function() {
					player.pause();
				};

				var customAudio = window.customAudio || false;
				if (customAudio) {
					if (customAudio.addEventListener) {
						customAudio.addEventListener('playing', pauseOnCustomAudioPlay);
					} else if (customAudio.attachEvent) {
						customAudio.attachEvent('playing', pauseOnCustomAudioPlay);
					}
				}

				var streamAudio = window.player || false;
				if (streamAudio) {
					if (streamAudio.addEventListener) {
						streamAudio.addEventListener('stream-start', pauseOnCustomAudioPlay);
					} else if (player.attachEvent) {
						streamAudio.attachEvent('stream-start', pauseOnCustomAudioPlay);
					}
				}
			});

			player.on(playerjs.EVENTS.PLAY, function() {
				var customAudio = window.customAudio || false;
				var streamAudio = window.player || false;

				if (customAudio) {
					customAudio.pause();
				}

				if (streamAudio) {
					streamAudio.pause();
				}
			});
		});
	};

	$document.bind('pjax:end', connectToEmbeds).ready(connectToEmbeds);
})(jQuery, playerjs);
