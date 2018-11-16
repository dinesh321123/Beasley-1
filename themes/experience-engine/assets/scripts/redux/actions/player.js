/**
 * We use this approach to minify action names in the production bundle and have
 * human friendly actions in the dev bundle. Use "p{x}" format to create new actions.
 */

export const ACTION_INIT_TDPLAYER        = 'production' === process.env.NODE_ENV ? 'p0' : 'PLAYER_INIT_TDPLAYER';
export const ACTION_STATUS_CHANGE        = 'production' === process.env.NODE_ENV ? 'p1' : 'PLAYER_STATUS_CHANGE';
export const ACTION_CUEPOINT_CHANGE      = 'production' === process.env.NODE_ENV ? 'p2' : 'PLAYER_CUEPOINT_CHANGE';
export const ACTION_SET_VOLUME           = 'production' === process.env.NODE_ENV ? 'p3' : 'PLAYER_SET_VOLUME';
export const ACTION_PLAY_AUDIO           = 'production' === process.env.NODE_ENV ? 'p4' : 'PLAYER_PLAY_AUDIO';
export const ACTION_PLAY_STATION         = 'production' === process.env.NODE_ENV ? 'p5' : 'PLAYER_PLAY_STATION';
export const ACTION_PLAY_OMNY            = 'production' === process.env.NODE_ENV ? 'p6' : 'PLAYER_PLAY_OMNY';
export const ACTION_PAUSE                = 'production' === process.env.NODE_ENV ? 'p7' : 'PLAYER_PAUSE';
export const ACTION_RESUME               = 'production' === process.env.NODE_ENV ? 'p8' : 'PLAYER_RESUME';
export const ACTION_DURATION_CHANGE      = 'production' === process.env.NODE_ENV ? 'p9' : 'PLAYER_DURATION_CHANGE';
export const ACTION_TIME_CHANGE          = 'production' === process.env.NODE_ENV ? 'pa' : 'PLAYER_TIME_CHANGE';
export const ACTION_SEEK_POSITION        = 'production' === process.env.NODE_ENV ? 'pb' : 'PLAYER_SEEK_POSITION';
export const ACTION_NOW_PLAYING_LOADED   = 'production' === process.env.NODE_ENV ? 'pc' : 'PLAYER_NOW_PLAYING_LOADED';
export const ACTION_AD_PLAYBACK_START    = 'production' === process.env.NODE_ENV ? 'pd' : 'PLAYER_AD_PLAYBACK_START';
export const ACTION_AD_PLAYBACK_COMPLETE = 'production' === process.env.NODE_ENV ? 'pe' : 'PLAYER_AD_PLAYBACK_COMPLETE';
export const ACTION_AD_PLAYBACK_ERROR    = 'production' === process.env.NODE_ENV ? 'pf' : 'PLAYER_AD_PLAYBACK_ERROR';
export const ACTION_AD_BREAK_SYNCED      = 'production' === process.env.NODE_ENV ? 'pg' : 'PLAYER_AD_BREAK_SYNCED';
export const ACTION_AD_BREAK_SYNCED_HIDE = 'production' === process.env.NODE_ENV ? 'ph' : 'PLAYER_AD_BREAK_SYNCED_HIDE';

export const STATUSES = {
	LIVE_PAUSE: 'LIVE_PAUSE',
	LIVE_PLAYING: 'LIVE_PLAYING',
	LIVE_STOP: 'LIVE_STOP',
	LIVE_FAILED: 'LIVE_FAILED',
	LIVE_BUFFERING: 'LIVE_BUFFERING',
	LIVE_CONNECTING: 'LIVE_CONNECTING',
	LIVE_RECONNECTING: 'LIVE_RECONNECTING',
	STREAM_GEO_BLOCKED: 'STREAM_GEO_BLOCKED',
	STATION_NOT_FOUND: 'STATION_NOT_FOUND',
};

const errorCatcher = prefix => ( e ) => {
	const { data } = e;
	const { errors } = data || {};

	( errors || [] ).forEach( ( error ) => {
		// eslint-disable-next-line no-console
		console.error( `${prefix}: [${error.code}] ${error.message}` );
	} );
};

export const initTdPlayer = ( modules ) => ( dispatch ) => {
	let adPlaybackTimeout = false;
	let adSyncedTimeout = false;

	const dispatchStatusChange = ( { data } ) => {
		dispatch( {
			type: ACTION_STATUS_CHANGE,
			status: data.code,
		} );
	};

	const dispatchCuePoint = ( { data } ) => {
		dispatch( {
			type: ACTION_CUEPOINT_CHANGE,
			cuePoint: ( data || {} ).cuePoint || false,
		} );
	};

	const dispatchListLoaded = ( { data } ) => {
		dispatch( {
			type: ACTION_NOW_PLAYING_LOADED,
			...data,
		} );
	};

	const clearAdTimeout = ( timeout ) => {
		timeout && clearTimeout( timeout );
		return false;
	};

	const dispatchSyncedStart = () => {
		// hide after 35 seconds if it hasn't been hidden yet
		clearAdTimeout( adSyncedTimeout );
		adSyncedTimeout = setTimeout( () => dispatch( { type: ACTION_AD_BREAK_SYNCED_HIDE } ), 35000 );

		dispatch( { type: ACTION_AD_BREAK_SYNCED } );
	};

	const dispatchPlaybackStart = () => {
		// hide after 1 min if it hasn't been hidden yet
		clearAdTimeout( adPlaybackTimeout );
		adPlaybackTimeout = setTimeout( dispatchPlaybackStop( ACTION_AD_PLAYBACK_ERROR ), 60000 );

		dispatch( { type: ACTION_AD_PLAYBACK_START } );
	};

	const dispatchPlaybackStop = ( type ) => () => {
		adPlaybackTimeout = clearAdTimeout();
		dispatch( { type } );
	};

	const player = new window.TDSdk( {
		coreModules: modules,
		configurationError: errorCatcher( 'Configuration Error' ),
		moduleError: errorCatcher( 'Module Error' ),
		playerReady() {
			player.addEventListener( 'stream-status', dispatchStatusChange );
			player.addEventListener( 'list-loaded', dispatchListLoaded );

			player.addEventListener( 'track-cue-point', dispatchCuePoint );
			player.addEventListener( 'speech-cue-point', dispatchCuePoint );
			player.addEventListener( 'custom-cue-point', dispatchCuePoint );

			player.addEventListener( 'ad-break-cue-point', dispatchCuePoint );
			player.addEventListener( 'ad-break-cue-point-complete', dispatchCuePoint );
			player.addEventListener( 'ad-break-synced-element', dispatchSyncedStart );

			player.addEventListener( 'ad-playback-start', dispatchPlaybackStart );
			player.addEventListener( 'ad-playback-complete', dispatchPlaybackStop( ACTION_AD_PLAYBACK_COMPLETE ) );
			player.addEventListener( 'ad-playback-error', dispatchPlaybackStop( ACTION_AD_PLAYBACK_ERROR ) );

			dispatch( { type: ACTION_INIT_TDPLAYER, player } );
		},
	} );
};

export const playAudio = ( audio, title = '', artist = '' ) => ( dispatch ) => {
	const dispatchStatusUpdate = ( status ) => () => {
		dispatch( {
			type: ACTION_STATUS_CHANGE,
			status,
		} );
	};

	const player = new Audio( audio );

	player.addEventListener( 'loadstart', dispatchStatusUpdate( STATUSES.LIVE_BUFFERING ) );
	player.addEventListener( 'pause', dispatchStatusUpdate( STATUSES.LIVE_PAUSE ) );
	player.addEventListener( 'playing', dispatchStatusUpdate( STATUSES.LIVE_PLAYING ) );
	player.addEventListener( 'ended', dispatchStatusUpdate( STATUSES.LIVE_STOP ) );

	player.addEventListener( 'loadedmetadata', () => {
		dispatch( {
			type: ACTION_DURATION_CHANGE,
			duration: player.duration,
		} );
	} );

	player.addEventListener( 'timeupdate', () => {
		dispatch( {
			type: ACTION_TIME_CHANGE,
			time: player.currentTime,
		} );
	} );

	dispatch( {
		type: ACTION_PLAY_AUDIO,
		player,
		audio,
	} );

	dispatch( {
		type: ACTION_CUEPOINT_CHANGE,
		cuePoint: {
			type: 'track',
			cueTitle: title,
			artistName: artist,
		},
	} );
};

export const playOmny = ( audio, title = '', artist = '' ) => ( dispatch ) => {
	const dispatchStatusUpdate = ( status ) => () => {
		dispatch( {
			type: ACTION_STATUS_CHANGE,
			status,
		} );
	};

	const { playerjs } = window;

	const iframe = document.createElement( 'iframe' );
	iframe.src = audio;
	document.body.appendChild( iframe );

	const player = new playerjs.Player( iframe );

	player.on( 'ready', dispatchStatusUpdate( STATUSES.LIVE_BUFFERING ) );
	player.on( 'play', dispatchStatusUpdate( STATUSES.LIVE_PLAYING ) );
	player.on( 'pause', dispatchStatusUpdate( STATUSES.LIVE_PAUSE ) );
	player.on( 'ended', dispatchStatusUpdate( STATUSES.LIVE_STOP ) );

	player.on( 'timeupdate', ( e ) => {
		dispatch( {
			type: ACTION_TIME_CHANGE,
			time: e.seconds,
			duration: e.duration,
		} );
	} );

	dispatch( {
		type: ACTION_PLAY_OMNY,
		player,
		audio,
	} );

	dispatch( {
		type: ACTION_CUEPOINT_CHANGE,
		cuePoint: {
			type: 'track',
			cueTitle: title,
			artistName: artist,
		},
	} );
};

export const playStation = ( station ) => ( {
	type: ACTION_PLAY_STATION,
	station,
} );

export const pause = () => ( {
	type: ACTION_PAUSE,
} );

export const resume = () => ( {
	type: ACTION_RESUME,
} );

export const setVolume = ( volume ) => ( {
	type: ACTION_SET_VOLUME,
	volume,
} );

export const seekPosition = ( position ) => ( {
	type: ACTION_SEEK_POSITION,
	position,
} );

export default {
	initTdPlayer,
	playAudio,
	playStation,
	playOmny,
	pause,
	resume,
	setVolume,
	seekPosition,
};
