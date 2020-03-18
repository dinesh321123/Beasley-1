/* eslint-disable sort-keys */
import playerjs from 'player.js';

export const ACTION_SET_PLAYER = 'SET_PLAYER';
export const ACTION_STATUS_CHANGE = 'PLAYER_STATUS_CHANGE';
export const ACTION_CUEPOINT_CHANGE = 'PLAYER_CUEPOINT_CHANGE';
export const ACTION_SET_VOLUME = 'PLAYER_SET_VOLUME';
export const ACTION_PLAY = 'PLAYER_PLAY';
export const ACTION_PLAY_OMNY = 'PLAYER_PLAY_OMNY';
export const ACTION_PAUSE = 'PLAYER_PAUSE';
export const ACTION_RESUME = 'PLAYER_RESUME';
export const ACTION_DURATION_CHANGE = 'PLAYER_DURATION_CHANGE';
export const ACTION_TIME_CHANGE = 'PLAYER_TIME_CHANGE';
export const ACTION_SEEK_POSITION = 'PLAYER_SEEK_POSITION';
export const ACTION_NOW_PLAYING_LOADED = 'PLAYER_NOW_PLAYING_LOADED';
export const ACTION_AD_PLAYBACK_START = 'PLAYER_AD_PLAYBACK_START';
export const ACTION_AD_PLAYBACK_STOP = 'PLAYER_AD_PLAYBACK_STOP';
export const ACTION_AD_PLAYBACK_COMPLETE = 'PLAYER_AD_PLAYBACK_COMPLETE';
export const ACTION_AD_PLAYBACK_ERROR = 'PLAYER_AD_PLAYBACK_ERROR';
export const ACTION_AD_BREAK_SYNCED = 'PLAYER_AD_BREAK_SYNCED';
export const ACTION_AD_BREAK_SYNCED_HIDE = 'PLAYER_AD_BREAK_SYNCED_HIDE';

export const ACTION_PLAYER_START = 'PLAYER_START';
export const ACTION_PLAYER_STOP = 'PLAYER_STOP';
export const ACTION_PLAYER_END = 'ACTION_PLAYER_END';
export const ACTION_SET_PLAYER_TYPE = 'PLAYER_SET_TYPE';

export const STATUSES = {
	LIVE_BUFFERING: 'LIVE_BUFFERING',
	LIVE_CONNECTING: 'LIVE_CONNECTING',
	LIVE_FAILED: 'LIVE_FAILED',
	LIVE_PAUSE: 'LIVE_PAUSE',
	LIVE_PLAYING: 'LIVE_PLAYING',
	LIVE_RECONNECTING: 'LIVE_RECONNECTING',
	LIVE_STOP: 'LIVE_STOP',
	STATION_NOT_FOUND: 'STATION_NOT_FOUND',
	STREAM_GEO_BLOCKED: 'STREAM_GEO_BLOCKED',
};

/**
 * Holds reference to all players
 */
window.tdPlayer = null;
window.audioPlayer = null;
window.omnyPlayer = null;

/**
 * playbackStop action creator
 * @param {*} actionType
 * @returns {Object} action payload
 * TODO: Originally this was clearing the timeout that was
 * set by the adPlaybackStart action creator. I believe this was
 * explicitly called in order to clear the global timeout if
 * this was called directly.
 */
export function adPlaybackStop(actionType) {
	return {
		type: ACTION_AD_PLAYBACK_STOP,
		payload: {
			actionType,
		},
	};
}

/**
 * playbackStart action creator
 * @returns {Object} action payload
 * TODO: Originally this was setting a timeout that would
 * dispatch the adPlaybackStop action creator after 70 seconds
 */
export function adPlaybackStart() {
	return {
		type: ACTION_AD_PLAYBACK_START,
	};
}

/**
 * adBreakSynced action creator
 */
export function adBreakSynced() {
	return {
		type: ACTION_AD_BREAK_SYNCED,
	};
}

/**
 * adBreakSyncedHide action creator
 */
export function adBreakSyncedHide() {
	return {
		type: ACTION_AD_BREAK_SYNCED_HIDE,
	};
}

/**
 * statusUpdate action creator
 * @param {String} status
 */
export function statusUpdate(status) {
	return {
		type: ACTION_STATUS_CHANGE,
		status,
	};
}

/**
 * cuePoint action creator
 * @param {Object} data - Payload from player event
 */
export function cuePoint(cuePointData = {}) {
	return {
		type: ACTION_CUEPOINT_CHANGE,
		cuePoint: cuePointData,
	};
}

/**
 * nowPlayingLoaded action creator
 * @param {Object} data - Payload from player event
 */
export function nowPlayingLoaded(data) {
	return {
		type: ACTION_NOW_PLAYING_LOADED,
		...data,
	};
}

/**
 * durationChange action creator
 * @param {String} duration - Payload from player
 */
export function durationChange(duration) {
	return {
		type: ACTION_DURATION_CHANGE,
		duration,
	};
}

/**
 * timeChange action creator
 * @param {String} time - Payload from player
 * @param {String|null} duration - Payload from player
 */
export function timeChange(time, duration = null) {
	return {
		type: ACTION_TIME_CHANGE,
		time,
		duration,
	};
}

/**
 * Start action creator
 */
export function start() {
	return {
		type: ACTION_PLAYER_START,
	};
}

/**
 * Stop action creator
 */
export function stop() {
	return {
		type: ACTION_PLAYER_STOP,
	};
}

/**
 * Stop action creator
 */
export function end() {
	return {
		type: ACTION_PLAYER_END,
	};
}

/**
 * pause action creator
 */
export function pause() {
	return {
		type: ACTION_PAUSE,
	};
}

/**
 * resume action creator
 */
export function resume() {
	return {
		type: ACTION_RESUME,
	};
}

/**
 * initializeTdPlayer action creator
 *
 * @param {Object} player - player object reference
 */
export function setPlayer(player, playerType) {
	return {
		type: ACTION_SET_PLAYER,
		payload: { player, playerType },
	};
}

/**
 * setVolume action creator
 * @param {String} volume
 */
export function setVolume(volume) {
	return {
		type: ACTION_SET_VOLUME,
		volume,
	};
}

/**
 * seekPosition action creator
 * @param {String} position
 */
export function seekPosition(position) {
	return {
		type: ACTION_SEEK_POSITION,
		position,
	};
}

/**
 * errorCatcher outputs helper console messages
 * @param {String} prefix
 */
function errorCatcher(prefix = '') {
	return e => {
		const { data } = e;
		const { errors = [] } = data;

		errors.forEach(error =>
			// eslint-disable-next-line no-console
			console.error(`${prefix}: [${error.code}] ${error.message}`),
		);
	};
}

/**
 * Initializes the TdPlayer
 *
 * @param {*} modules
 */
export function initTdPlayer(modules) {
	return dispatch => {
		let adSyncedTimeout = false;

		function dispatchSyncedStart() {
			// hide after 35 seconds if it hasn't been hidden yet
			clearTimeout(adSyncedTimeout);
			adSyncedTimeout = setTimeout(() => dispatch(adBreakSyncedHide()), 35000);
			dispatch(adBreakSynced());
		}

		window.tdPlayer = new window.TDSdk({
			configurationError: errorCatcher('Configuration Error'),
			coreModules: modules,
			moduleError: errorCatcher('Module Error'),
		});

		window.tdPlayer.addEventListener('stream-status', ({ data }) =>
			dispatch(statusUpdate(data.code)),
		);
		window.tdPlayer.addEventListener('list-loaded', ({ data }) =>
			dispatch(nowPlayingLoaded(data)),
		);
		window.tdPlayer.addEventListener('track-cue-point', ({ data }) =>
			dispatch(cuePoint(data.cuePoint || {})),
		);
		window.tdPlayer.addEventListener('speech-cue-point', ({ data }) =>
			dispatch(cuePoint(data.cuePoint || {})),
		);
		window.tdPlayer.addEventListener('custom-cue-point', ({ data }) =>
			dispatch(cuePoint(data.cuePoint || {})),
		);
		window.tdPlayer.addEventListener('ad-break-cue-point', ({ data }) =>
			dispatch(cuePoint(data.cuePoint || {})),
		);
		window.tdPlayer.addEventListener('ad-break-cue-point-complete', () =>
			dispatch(cuePoint()),
		);
		window.tdPlayer.addEventListener(
			'ad-break-synced-element',
			dispatchSyncedStart,
		);
		window.tdPlayer.addEventListener('ad-playback-start', () =>
			dispatch(adPlaybackStart()),
		); // used to dispatchPlaybackStart
		window.tdPlayer.addEventListener('ad-playback-complete', () =>
			dispatch(adPlaybackStop(ACTION_AD_PLAYBACK_COMPLETE)),
		); // used to dispatchPlaybackStop( ACTION_AD_PLAYBACK_COMPLETE )
		window.tdPlayer.addEventListener('stream-start', () => dispatch(start()));
		window.tdPlayer.addEventListener('stream-stop', () => dispatch(end()));
		window.tdPlayer.addEventListener('ad-playback-error', () => {
			/*
			 * the beforeStreamStart function may be injected onto the window
			 * object from google tag manager. This function provides a callback
			 * when it is completed. Currently we are using it to play a preroll
			 * from kubient when there is no preroll provided by triton. To ensure
			 * that we do not introduce unforeseen issues we return the original
			 * ACTION_AD_PLAYBACK_ERROR type.
			 * */
			if (window.beforeStreamStart) {
				window.beforeStreamStart(() =>
					dispatch(adPlaybackStop(ACTION_AD_PLAYBACK_ERROR)),
				); // used to dispatchPlaybackStop( ACTION_AD_PLAYBACK_ERROR )( );
			} else {
				dispatch(adPlaybackStop(ACTION_AD_PLAYBACK_ERROR)); // used to dispatch( adPlaybackStop( ACTION_AD_PLAYBACK_ERROR ) );
			}
		});
	};
}

/**
 * Sets up the audio player
 *
 * @param {*} dispatch
 * @param {*} src The audio source
 */
function setUpAudioPlayer(dispatch, src) {
	window.audioPlayer = new Audio(src);

	window.audioPlayer.addEventListener('loadstart', () =>
		dispatch(statusUpdate(STATUSES.LIVE_BUFFERING)),
	);
	window.audioPlayer.addEventListener('pause', () =>
		dispatch(statusUpdate(STATUSES.LIVE_PAUSE)),
	);
	window.audioPlayer.addEventListener('playing', () =>
		dispatch(statusUpdate(STATUSES.LIVE_PLAYING)),
	);
	window.audioPlayer.addEventListener('ended', () => {
		dispatch(statusUpdate(STATUSES.LIVE_STOP));
	});
	window.audioPlayer.addEventListener('play', () => dispatch(start()));
	window.audioPlayer.addEventListener('pause', () => dispatch(end()));
	window.audioPlayer.addEventListener('abort', () => dispatch(end()));
	window.audioPlayer.addEventListener('loadedmetadata', () =>
		dispatch(durationChange(window.audioPlayer.duration)),
	);
	window.audioPlayer.addEventListener('timeupdate', () =>
		dispatch(timeChange(window.audioPlayer.currentTime)),
	);
}

/**
 * Sets up the omny player.
 *
 * @param {string} source The audio source file.
 */
function setUpOmnyPlayer(source) {
	const id = source.replace(/\W+/g, '');
	if (document.getElementById(id)) {
		return;
	}

	const iframe = document.createElement('iframe');
	iframe.id = id;
	iframe.src = source;
	document.body.appendChild(iframe);

	window.omnyPlayer = new playerjs.Player(iframe);
}

/**
 * Low-level play action creator
 *
 * @param {string} playerType Which player to use.
 * @param {*} source Audior source or station name.
 * @param {*} type
 * @param {*} cueTitle
 * @param {*} artistName
 * @param {*} trackType
 */
const play = (
	playerType,
	source,
	cueTitle = '',
	artistName = '',
	trackType = '',
) => dispatch => {
	// make sure to stop any running player.
	dispatch(stop());

	if (playerType === 'tdplayer') {
		// reset time and duration.
		dispatch(timeChange(0));
		dispatch(durationChange(0));
		// set the appropriate player.
		dispatch(setPlayer(window.tdPlayer, 'tdplayer'));
		// play.
		dispatch({
			type: ACTION_PLAY,
			payload: {
				source,
			},
		});
	} else if (playerType === 'mp3player') {
		if (window.audioPlayer === null) {
			setUpAudioPlayer(dispatch, source);
		} else {
			window.audioPlayer.src = source;
		}
		dispatch(setPlayer(window.audioPlayer, 'mp3player'));
		dispatch({
			type: ACTION_PLAY,
			payload: {
				source,
				trackType,
			},
		});
		dispatch(cuePoint({ type: 'track', cueTitle, artistName }));
	} else if (playerType === 'omnyplayer') {
		if (window.audioPlayer === null) {
			setUpOmnyPlayer();
		}

		dispatch(setPlayer(window.omnyPlayer, 'omnyplayer'));

		// all events are removed when stopping the omny player so we need to recreate them.
		window.omnyPlayer.on('ready', () => {
			dispatch({
				type: ACTION_PLAY,
				payload: {
					source,
					trackType,
				},
			});
			dispatch(cuePoint({ type: 'track', cueTitle, artistName }));
			dispatch(statusUpdate(STATUSES.LIVE_BUFFERING));
		});

		window.omnyPlayer.on('play', () =>
			dispatch(statusUpdate(STATUSES.LIVE_PLAYING)),
		);
		window.omnyPlayer.on('pause', () =>
			dispatch(statusUpdate(STATUSES.LIVE_PAUSE)),
		);
		window.omnyPlayer.on('ended', () =>
			dispatch(statusUpdate(STATUSES.LIVE_STOP)),
		);
		window.omnyPlayer.on('error', () => errorCatcher('Omny Error'));
		window.omnyPlayer.on('timeupdate', ({ seconds: time, duration }) =>
			dispatch(timeChange(time, duration)),
		);
	}
};

/**
 * Action Creator for playing an audio file.
 *
 * @param {*} src The audio source.
 * @param {*} cueTitle
 * @param {*} artistName
 * @param {*} trackType
 */
export const playAudio = (
	src,
	cueTitle = '',
	artistName = '',
	trackType = 'live',
) => dispatch =>
	play('mp3player', src, cueTitle, artistName, trackType)(dispatch);

/**
 * playStation action creator
 * @param {String} station
 */
export const playStation = station => dispatch =>
	play('tdplayer', station)(dispatch);

/**
 * Action Creator for playing an audio file using the omnyplayer.
 *
 * @param {*} src The audio source.
 * @param {*} cueTitle
 * @param {*} artistName
 * @param {*} trackType
 */
export const playOmny = (
	src,
	cueTitle = '',
	artistName = '',
	trackType = 'live',
) => dispatch =>
	play('omnyplayer', src, cueTitle, artistName, trackType)(dispatch);

export default {
	setPlayer,
	pause,
	playAudio,
	playOmny,
	playStation,
	resume,
	seekPosition,
	setVolume,
};
