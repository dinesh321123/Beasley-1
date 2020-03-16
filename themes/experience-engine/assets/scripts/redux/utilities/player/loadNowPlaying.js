/**
 * @function loadNowPlaying
 * Used to load a configuration to the NowPlaying API
 * TODO: Is this asynchronous??? Need docs
 *
 * @param {String} station Station identifier
 * @param {Object} player Player instance
 */
export default function loadNowPlaying({
	station = null,
	player = null,
	playerType = null,
}) {
	// If not tdplayer type, abandon
	// Only tdplayer contains the NowPlayingApi
	if (!playerType || playerType !== 'tdplayer') {
		return;
	}

	// If station and player
	// If player.NowPlayingApi
	// If load function exists
	if (
		station &&
		player &&
		player.NowPlayingApi &&
		typeof player.NowPlayingApi.load === 'function'
	) {
		player.NowPlayingApi.load({ numberToFetch: 10, mount: station });
	}
}
