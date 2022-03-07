/**
 * Used to interact with the LyticsTrackAudio window object
 * which is provided by the GTM implementation.
 *
 * @param {String} action The action to take (ie. play, pause, end)
 * @param {Object} params Set of parameters
 */
export default function lyticsTrack(action, params) {
	// Check for googletag
	if (window.googletag && window.googletag.cmd) {
		// Push to the CMD queue
		window.googletag.cmd.push(() => {
			// Abandon if no LyticsTrackAudio global
			if (typeof window.LyticsTrackAudio === 'undefined') {
				return;
			}

			// If action play
			if (action === 'play' && window.LyticsTrackAudio.set_podcastPayload) {
				window.LyticsTrackAudio.set_podcastPayload(
					{
						type: 'podcast',
						name: params.artistName,
						episode: params.cueTitle,
					},
					() => {
						window.LyticsTrackAudio.playPodcast();
					},
				);
			}

			// If action pause
			if (action === 'pause' && window.LyticsTrackAudio.pausePodcast) {
				window.LyticsTrackAudio.pausePodcast();
			}

			// If action end
			if (action === 'end' && window.LyticsTrackAudio.endOfPodcast) {
				window.LyticsTrackAudio.endOfPodcast();
			}
		});
	}
}
