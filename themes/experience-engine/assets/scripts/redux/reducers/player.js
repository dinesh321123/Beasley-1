import { getStorage } from '../../library/local-storage';
import { ACTION_LOADED_PAGE, ACTION_LOADED_PARTIAL } from '../actions/screen';
import {
	ACTION_INIT_TDPLAYER,
	ACTION_STATUS_CHANGE,
	ACTION_CUEPOINT_CHANGE,
	ACTION_SET_VOLUME,
	ACTION_PLAY_AUDIO,
	ACTION_PLAY_STATION,
	ACTION_PLAY_OMNY,
	ACTION_PAUSE,
	ACTION_RESUME,
	ACTION_DURATION_CHANGE,
	ACTION_TIME_CHANGE,
	ACTION_SEEK_POSITION,
	ACTION_NOW_PLAYING_LOADED,
	ACTION_AD_PLAYBACK_START,
	ACTION_AD_PLAYBACK_COMPLETE,
	ACTION_AD_PLAYBACK_ERROR,
	ACTION_AD_BREAK_SYNCED,
	ACTION_AD_BREAK_SYNCED_HIDE,
	STATUSES,
} from '../actions/player';

const localStorage = getStorage( 'liveplayer' );
const { streams } = window.bbgiconfig || {};

let tdplayer = null;
let mp3player = null;
let omnyplayer = null;

function parseVolume( value ) {
	let volume = parseInt( value, 10 );
	if ( Number.isNaN( volume ) || 100 < volume ) {
		volume = 100;
	} else if ( 0 > volume ) {
		volume = 0;
	}

	return volume;
}

function loadNowPlaying( station ) {
	if ( station && tdplayer && !omnyplayer && !mp3player ) {
		tdplayer.NowPlayingApi.load( { numberToFetch: 10, mount: station } );
	}
}

function fullStop() {
	if ( mp3player ) {
		mp3player.pause();
		mp3player = null;
	}

	if ( omnyplayer ) {
		omnyplayer.off( 'ready' );
		omnyplayer.off( 'play' );
		omnyplayer.off( 'pause' );
		omnyplayer.off( 'ended' );
		omnyplayer.off( 'timeupdate' );

		omnyplayer.pause();
		omnyplayer.elem.parentNode.removeChild( omnyplayer.elem );
		omnyplayer = null;
	}

	if ( tdplayer ) {
		tdplayer.stop();
		tdplayer.skipAd();
	}
}

function getInitialStation( streamsList ) {
	const station = localStorage.getItem( 'station' );
	return streamsList.find( stream => stream.stream_call_letters === station );
}

const adReset = {
	adPlayback: false,
	adSynced: false,
};

const stateReset = {
	audio: '',
	station: '',
	cuePoint: false,
	time: 0,
	duration: 0,
	songs: [],
	...adReset,
};

let initialStation = getInitialStation( streams );

export const DEFAULT_STATE = {
	...stateReset,
	status: STATUSES.LIVE_STOP,
	station: ( initialStation || streams[0] || {} ).stream_call_letters,
	volume: parseVolume( localStorage.getItem( 'volume' ) || 100 ),
	streams,
};

function reducer( state = {}, action = {} ) {
	switch ( action.type ) {
		case ACTION_INIT_TDPLAYER:
			tdplayer = action.player;
			tdplayer.setVolume( state.volume / 100 );
			break;

		case ACTION_PLAY_AUDIO:
			fullStop();

			mp3player = action.player;
			mp3player.volume = state.volume / 100;
			mp3player.play();

			return { ...state, ...stateReset, audio: action.audio };

		case ACTION_PLAY_STATION: {
			const { station } = action;
			const stream = state.streams.find( item => item.stream_call_letters === station );

			fullStop();

			tdplayer.playAd( 'tap', {
				host: stream.stream_cmod_domain,
				type: 'preroll',
				format: 'vast',
				stationId: stream.stream_tap_id,
			} );

			localStorage.setItem( 'station', station );

			return { ...state, ...stateReset, station };
		}

		case ACTION_PLAY_OMNY:
			fullStop();

			omnyplayer = action.player;
			omnyplayer.play();
			// Omny doesn't support sound provider, thus we can't change/control volume :(
			// omnyplayer.setVolume( state.volume );

			return { ...state, ...stateReset, audio: action.audio };

		case ACTION_PAUSE:
			if ( mp3player ) {
				mp3player.pause();
			} else if ( omnyplayer ) {
				omnyplayer.pause();
			} else if ( tdplayer ) {
				tdplayer.stop();
			}
			return { ...state, ...adReset };

		case ACTION_RESUME:
			if ( mp3player ) {
				mp3player.play();
			} else if ( omnyplayer ) {
				omnyplayer.play();
			} else if ( tdplayer ) {
				tdplayer.play();
			}
			return { ...state, ...adReset };

		case ACTION_STATUS_CHANGE:
			return { ...state, status: action.status };

		case ACTION_SET_VOLUME: {
			const volume = parseVolume( action.volume );
			localStorage.setItem( 'volume', volume );

			const value = volume / 100;
			if ( mp3player ) {
				mp3player.volume = value;
			} else if ( omnyplayer ) {
				// omnyplayer.setVolume( volume );
			} else if ( tdplayer ) {
				tdplayer.setVolume( value );
			}

			return { ...state, volume };
		}

		case ACTION_CUEPOINT_CHANGE:
			loadNowPlaying( state.station );
			return { ...state, ...adReset, cuePoint: action.cuePoint };

		case ACTION_DURATION_CHANGE:
			return { ...state, duration: +action.duration };

		case ACTION_TIME_CHANGE: {
			const override = { time: +action.time };
			if ( action.duration ) {
				override.duration = +action.duration;
			}

			return { ...state, ...override };
		}

		case ACTION_SEEK_POSITION: {
			const { position } = action;

			if ( mp3player ) {
				mp3player.currentTime = position;
				return Object.assign( {}, state, { time: +position } );
			} else if ( omnyplayer ) {
				omnyplayer.setCurrentTime( position );
				return Object.assign( {}, state, { time: +position } );
			}
			break;
		}

		case ACTION_NOW_PLAYING_LOADED:
			return { ...state, songs: action.list };

		case ACTION_AD_PLAYBACK_START:
			document.body.classList.add( 'locked' );
			return { ...state, adPlayback: true };

		case ACTION_AD_PLAYBACK_ERROR:
		case ACTION_AD_PLAYBACK_COMPLETE: {
			const { station, adPlayback } = state;
			document.body.classList.remove( 'locked' );

			// start station only if the ad playback is playing now
			if ( adPlayback ) {
				tdplayer.skipAd();
			}

			tdplayer.play( { station } );
			loadNowPlaying( station );

			return { ...state, adPlayback: false };
		}

		case ACTION_AD_BREAK_SYNCED:
			return { ...state, ...adReset, adSynced: true };

		case ACTION_AD_BREAK_SYNCED_HIDE:
			return { ...state, ...adReset };

		case ACTION_LOADED_PAGE:
		case ACTION_LOADED_PARTIAL: {
			const newstate = { ...state, streams: window.bbgiconfig.streams || [] };

			if ( !initialStation ) {
				initialStation = getInitialStation( newstate.streams );
				if ( initialStation ) {
					newstate.station = initialStation.stream_call_letters;
				}
			}

			return newstate;
		}

		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
