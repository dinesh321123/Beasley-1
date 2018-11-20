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

function dispatchStatusUpdate( dispatch, status ) {
	return () => {
		dispatch( { type: ACTION_STATUS_CHANGE, status } );
	};
}

function errorCatcher( prefix ) {
	return ( e ) => {
		const { data } = e;
		const { errors } = data || {};

		( errors || [] ).forEach( ( error ) => {
			// eslint-disable-next-line no-console
			console.error( `${prefix}: [${error.code}] ${error.message}` );
		} );
	};
}

export function initTdPlayer( modules ) {
	return ( dispatch ) => {
		let adPlaybackTimeout = false;
		let adSyncedTimeout = false;

		function dispatchStatusChange( { data } ) {
			dispatch( {
				type: ACTION_STATUS_CHANGE,
				status: data.code,
			} );
		}

		function dispatchCuePoint( { data } ) {
			dispatch( {
				type: ACTION_CUEPOINT_CHANGE,
				cuePoint: ( data || {} ).cuePoint || false,
			} );
		}

		function dispatchListLoaded( { data } ) {
			dispatch( {
				type: ACTION_NOW_PLAYING_LOADED,
				...data,
			} );
		}

		function clearAdTimeout( timeout ) {
			timeout && clearTimeout( timeout );
			return false;
		}

		function dispatchSyncedStart() {
			// hide after 35 seconds if it hasn't been hidden yet
			clearAdTimeout( adSyncedTimeout );
			adSyncedTimeout = setTimeout( () => dispatch( { type: ACTION_AD_BREAK_SYNCED_HIDE } ), 35000 );

			dispatch( { type: ACTION_AD_BREAK_SYNCED } );
		}

		function dispatchPlaybackStart() {
			// hide after 1 min if it hasn't been hidden yet
			clearAdTimeout( adPlaybackTimeout );
			adPlaybackTimeout = setTimeout( dispatchPlaybackStop( ACTION_AD_PLAYBACK_ERROR ), 60000 );

			dispatch( { type: ACTION_AD_PLAYBACK_START } );
		}

		function dispatchPlaybackStop( type ) {
			return () => {
				adPlaybackTimeout = clearAdTimeout();
				dispatch( { type } );
			};
		}

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
}

export function playAudio( audio, cueTitle = '', artistName = '' ) {
	return ( dispatch ) => {
		const player = new Audio( audio );

		player.addEventListener( 'loadstart', dispatchStatusUpdate( dispatch, STATUSES.LIVE_BUFFERING ) );
		player.addEventListener( 'pause', dispatchStatusUpdate( dispatch, STATUSES.LIVE_PAUSE ) );
		player.addEventListener( 'playing', dispatchStatusUpdate( dispatch, STATUSES.LIVE_PLAYING ) );
		player.addEventListener( 'ended', dispatchStatusUpdate( dispatch, STATUSES.LIVE_STOP ) );

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
			cuePoint: { type: 'track', cueTitle, artistName },
		} );
	};
}

export function playOmny( audio, cueTitle = '', artistName = '' ) {
	return ( dispatch ) => {
		const id = audio.replace( /\W+/g, '' );
		if ( document.getElementById( id ) ) {
			return;
		}

		const { playerjs } = window;

		const iframe = document.createElement( 'iframe' );
		iframe.id = id;
		iframe.src = audio;
		document.body.appendChild( iframe );

		const player = new playerjs.Player( iframe );

		player.on( 'ready', () => {
			dispatch( { type: ACTION_PLAY_OMNY, player, audio } );

			dispatch( {
				type: ACTION_CUEPOINT_CHANGE,
				cuePoint: { type: 'track', cueTitle, artistName },
			} );

			dispatchStatusUpdate( dispatch, STATUSES.LIVE_BUFFERING )();
		} );

		player.on( 'play', dispatchStatusUpdate( dispatch, STATUSES.LIVE_PLAYING ) );
		player.on( 'pause', dispatchStatusUpdate( dispatch, STATUSES.LIVE_PAUSE ) );
		player.on( 'ended', dispatchStatusUpdate( dispatch, STATUSES.LIVE_STOP ) );
		player.on( 'error', errorCatcher( 'Omny Error' ) );

		player.on( 'timeupdate', ( { seconds: time, duration } ) => {
			dispatch( { type: ACTION_TIME_CHANGE, time, duration } );
		} );
	};
}

export function playStation( station ) {
	return { type: ACTION_PLAY_STATION, station };
}

export function pause() {
	return { type: ACTION_PAUSE };
}

export function resume() {
	return { type: ACTION_RESUME };
}

export function setVolume( volume ) {
	return { type: ACTION_SET_VOLUME, volume };
}

export function seekPosition( position ) {
	return { type: ACTION_SEEK_POSITION, position };
}

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
