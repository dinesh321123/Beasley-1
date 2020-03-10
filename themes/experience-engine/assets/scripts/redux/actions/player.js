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
const PLAYERS_REGISTRY = {
	tdPlayer: null,
	audioPlayer: null,
	omnyPlayer: null,
};

/**
 * playbackStop action creator
 * @param {*} actionType
 * @returns {Object} action payload
 * TODO: Originally this was clearing the timeout that was
 * set by the adPlaybackStart action creator. I believe this was
 * explicitly called in order to clear the global timeout if
 * this was called directly.
 */
export function adPlaybackStop( actionType ) {
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
export function statusUpdate( status ) {
	return {
		type: ACTION_STATUS_CHANGE,
		status,
	};
}

/**
 * cuePoint action creator
 * @param {Object} data - Payload from player event
 */
export function cuePoint( cuePoint = {} ) {
	return {
		type: ACTION_CUEPOINT_CHANGE,
		cuePoint,
	};
}

/**
 * nowPlayingLoaded action creator
 * @param {Object} data - Payload from player event
 */
export function nowPlayingLoaded( data ) {
	return {
		type: ACTION_NOW_PLAYING_LOADED,
		...data,
	};
}


/**
 * durationChange action creator
 * @param {String} duration - Payload from player
 */
export function durationChange( duration ) {
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
export function timeChange( time, duration = null ) {
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
export function setPlayer( player, playerType ) {
	return {
		type: ACTION_SET_PLAYER,
		payload: { player, playerType },
	};
}

/**
 * setVolume action creator
 * @param {String} volume
 */
export function setVolume( volume ) {
	return {
		type: ACTION_SET_VOLUME,
		volume,
	};
}

/**
 * seekPosition action creator
 * @param {String} position
 */
export function seekPosition( position ) {
	return {
		type: ACTION_SEEK_POSITION,
		position,
	};
}

/**
 * Initializes the TdPlayer
 *
 * @param {*} modules
 */
export function initTdPlayer( modules ) {
	return ( dispatch ) => {
		let adSyncedTimeout = false;

		function dispatchSyncedStart() {
			// hide after 35 seconds if it hasn't been hidden yet
			clearTimeout( adSyncedTimeout );
			adSyncedTimeout = setTimeout(
				() => dispatch( adBreakSyncedHide() ),
				35000,
			);
			dispatch( adBreakSynced() );
		}

		PLAYERS_REGISTRY.tdPlayer = new window.TDSdk( {
			configurationError: errorCatcher( 'Configuration Error' ),
			coreModules: modules,
			moduleError: errorCatcher( 'Module Error' ),
		} );


		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'stream-status', ( { data } ) => dispatch( statusUpdate( data.code ) ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'list-loaded', ( { data } ) => dispatch( nowPlayingLoaded( data ) ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'track-cue-point', ( { data } ) => dispatch( cuePoint( data.cuePoint || {} ) ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'speech-cue-point', ( { data } ) => dispatch( cuePoint( data.cuePoint || {} ) ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'custom-cue-point', ( { data } ) => dispatch( cuePoint( data.cuePoint || {} ) ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'ad-break-cue-point', ( { data } ) => dispatch( cuePoint( data.cuePoint || {} ) ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'ad-break-cue-point-complete', () => dispatch( cuePoint() ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'ad-break-synced-element', dispatchSyncedStart );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'ad-playback-start', () => dispatch( adPlaybackStart() ) ); // used to dispatchPlaybackStart
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'ad-playback-complete', () => dispatch( adPlaybackStop( ACTION_AD_PLAYBACK_COMPLETE ) ) ); // used to dispatchPlaybackStop( ACTION_AD_PLAYBACK_COMPLETE )
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'stream-start', () => dispatch( start() ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener( 'stream-stop', () => dispatch( end() ) );
		PLAYERS_REGISTRY.tdPlayer.addEventListener(
			'ad-playback-error',
			() => {
				/*
				 * the beforeStreamStart function may be injected onto the window
				 * object from google tag manager. This function provides a callback
				 * when it is completed. Currently we are using it to play a preroll
				 * from kubient when there is no preroll provided by triton. To ensure
				 * that we do not introduce unforeseen issues we return the original
				 * ACTION_AD_PLAYBACK_ERROR type.
				 * */
				if ( window.beforeStreamStart ) {
					window.beforeStreamStart( () => dispatch( adPlaybackStop( ACTION_AD_PLAYBACK_ERROR ) ) ); // used to dispatchPlaybackStop( ACTION_AD_PLAYBACK_ERROR )( );
				} else {
					dispatch( adPlaybackStop( ACTION_AD_PLAYBACK_ERROR ) ); // used to dispatch( adPlaybackStop( ACTION_AD_PLAYBACK_ERROR ) );
				}
			},
		);
	};
}


/**
 * doPlayOmny action creator
 * @param {Object} player
 * @param {*} audio
 * @param {*} trackType
 */
function doPlayOmny( player, audio, trackType ) {
	return {
		type: ACTION_PLAY_OMNY,
		player,
		audio,
		trackType,
	};
}

/**
 * errorCatcher outputs helper console messages
 * @param {String} prefix
 */
function errorCatcher( prefix = '' ) {
	return e => {
		const { data } = e;
		const { errors = [] } = data;

		errors.forEach( error =>
			// eslint-disable-next-line no-console
			console.error( `${prefix}: [${error.code}] ${error.message}` ),
		);
	};
}

/**
 * Sets up the audio player
 *
 * @param {*} dispatch
 * @param {*} src The audio source
 */
function setUpAudioPlayer( dispatch, src ) {
	PLAYERS_REGISTRY.audioPlayer = new Audio( src );

	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'loadstart', () => dispatch( statusUpdate( STATUSES.LIVE_BUFFERING ) ) );
	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'pause', () => dispatch( statusUpdate( STATUSES.LIVE_PAUSE ) ) );
	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'playing', () => dispatch( statusUpdate( STATUSES.LIVE_PLAYING ) ) );
	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'ended', () => {
		dispatch( statusUpdate( STATUSES.LIVE_STOP ) );
	} );
	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'play', () => dispatch( start() ) );
	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'pause', () => dispatch( end() ) );
	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'abort', () => dispatch( end() ) );
	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'loadedmetadata', () => dispatch( durationChange( PLAYERS_REGISTRY.audioPlayer.duration ) ) );
	PLAYERS_REGISTRY.audioPlayer.addEventListener( 'timeupdate', () => dispatch( timeChange( PLAYERS_REGISTRY.audioPlayer.currentTime ) ) );
}

/**
 * Sets up the omny player.
 *
 * @param {string} source The audio source file.
 */
function setUpOmnyPlayer( source ) {
	const id = source.replace( /\W+/g, '' );
	if ( document.getElementById( id ) ) {
		return;
	}

	const iframe = document.createElement( 'iframe' );
	iframe.id = id;
	iframe.src = source;
	document.body.appendChild( iframe );

	PLAYERS_REGISTRY.omnyPlayer = new playerjs.Player( iframe );
}

/**
 * Action Creator for playing an audio file.
 *
 * @param {*} src The audio source.
 * @param {*} cueTitle
 * @param {*} artistName
 * @param {*} trackType
 */
export const playAudio = ( src, cueTitle = '', artistName = '', trackType = 'live' ) => dispatch =>
	play( 'mp3player', src, cueTitle, artistName, trackType )( dispatch );

/**
 * playStation action creator
 * @param {String} station
 */
export const playStation = ( station ) => dispatch =>
	play( 'tdplayer', station )( dispatch );


/**
 * Action Creator for playing an audio file using the omnyplayer.
 *
 * @param {*} src The audio source.
 * @param {*} cueTitle
 * @param {*} artistName
 * @param {*} trackType
 */
export const playOmny = ( src, cueTitle = '', artistName = '', trackType = 'live' ) => dispatch =>
	play( 'omnyplayer', src, cueTitle, artistName, trackType )( dispatch );

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
const play = ( playerType, source, cueTitle = '', artistName = '', trackType = '' ) => dispatch => {
	// make sure to stop any running player.
	dispatch( stop() );

	if ( 'tdplayer' === playerType ) {
		// reset time and duration.
		dispatch( timeChange( 0 ) );
		dispatch( durationChange( 0 ) );
		// set the appropriate player.
		dispatch( setPlayer( PLAYERS_REGISTRY.tdPlayer, 'tdplayer' ) );
		// play.
		dispatch( {
			type: ACTION_PLAY,
			payload: {
				source,
			},
		} );
	} else if ( 'mp3player' === playerType ) {
		if ( null === PLAYERS_REGISTRY.audioPlayer ){
			setUpAudioPlayer( dispatch, source );
		} else {
			PLAYERS_REGISTRY.audioPlayer.src = source;
		}
		dispatch( setPlayer( PLAYERS_REGISTRY.audioPlayer, 'mp3player' ) );
		dispatch( {
			type: ACTION_PLAY,
			payload: {
				source,
				trackType,
			},
		} );
		dispatch( cuePoint( { type: 'track', cueTitle, artistName } ) );
	} else if( 'omnyplayer' === playerType ) {
		if ( null === PLAYERS_REGISTRY.audioPlayer ) {
			setUpOmnyPlayer();
		}

		dispatch( setPlayer( PLAYERS_REGISTRY.omnyPlayer, 'omnyplayer' ) );

		// all events are removed when stopping the omny player so we need to recreate them.
		PLAYERS_REGISTRY.omnyPlayer.on( 'ready', () => {
			dispatch( {
				type: ACTION_PLAY,
				payload: {
					source,
					trackType,
				},
			} );
			dispatch( cuePoint( { type: 'track', cueTitle, artistName } ) );
			dispatch( statusUpdate( STATUSES.LIVE_BUFFERING ) );
		} );

		PLAYERS_REGISTRY.omnyPlayer.on( 'play', () => dispatch( statusUpdate( STATUSES.LIVE_PLAYING ) ) );
		PLAYERS_REGISTRY.omnyPlayer.on( 'pause', () => dispatch( statusUpdate( STATUSES.LIVE_PAUSE ) ) );
		PLAYERS_REGISTRY.omnyPlayer.on( 'ended', () => dispatch( statusUpdate( STATUSES.LIVE_STOP ) ) );
		PLAYERS_REGISTRY.omnyPlayer.on( 'error', ()=> errorCatcher( 'Omny Error' ) );
		PLAYERS_REGISTRY.omnyPlayer.on( 'timeupdate', ( { seconds: time, duration } ) => dispatch( timeChange( time, duration ) ) );
	}
};

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
