/* eslint-disable sort-keys */
import { getStorage } from '../../library/local-storage';
import {
	sendLiveStreamPlaying,
	sendInlineAudioPlaying,
} from '../../library/google-analytics';
import { isAudioAdOnly } from '../../library/strings';
import {
	ACTION_SET_USER_FEEDS,
	ACTION_UPDATE_USER_FEEDS,
	ACTION_RESET_USER,
} from '../actions/auth';
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
	ACTION_STREAM_START,
	ACTION_STREAM_STOP,
	ACTION_AUDIO_START,
	ACTION_AUDIO_STOP,
	STATUSES,
} from '../actions/player';

const localStorage = getStorage( 'liveplayer' );
const { streams } = window.bbgiconfig || {};

let tdplayer = null;
let mp3player = null;
let omnyplayer = null;

let liveStreamInterval = 0;
let inlineAudioInterval = 0;

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

function lyticsTrack( action, params ) {
	if ( window.googletag && window.googletag.cmd ) {
		window.googletag.cmd.push( () => {

			if ( 'undefined' === typeof window.LyticsTrackAudio ) {
				return;
			}

			if ( 'play' === action && window.LyticsTrackAudio.set_podcastPayload ) {
				window.LyticsTrackAudio.set_podcastPayload( {
					type: 'podcast',
					name: params.artistName,
					episode: params.cueTitle,
				}, () => {
					window.LyticsTrackAudio.playPodcast();
				} );
			}

			if ( 'pause' === action && window.LyticsTrackAudio.pausePodcast ) {
				window.LyticsTrackAudio.pausePodcast();
			}

			if ( 'end' === action && window.LyticsTrackAudio.endOfPodcast ) {
				window.LyticsTrackAudio.endOfPodcast();
			}
		} );
	}
}

const adReset = {
	adPlayback: false,
	adSynced: false,
};

const stateReset = {
	audio: '',
	station: '',
	trackType: '',
	cuePoint: false,
	time: 0,
	duration: 0,
	...adReset,
};

let initialStation = getInitialStation( streams );
let userInteraction = false;

export const DEFAULT_STATE = {
	...stateReset,
	status: STATUSES.LIVE_STOP,
	station: ( initialStation || streams[0] || {} ).stream_call_letters,
	volume: parseVolume( localStorage.getItem( 'volume' ) || 100 ),
	streams,
};

function reducer( state = {}, action = {} ) {
	let interval;

	switch ( action.type ) {
		case ACTION_INIT_TDPLAYER:
			tdplayer = action.player;
			tdplayer.setVolume( state.volume / 100 );

			loadNowPlaying( state.station );

			window.tdplayer = tdplayer;
			break;

		case ACTION_PLAY_AUDIO:
			fullStop();

			mp3player = action.player;
			mp3player.volume = state.volume / 100;
			mp3player.play();

			return { ...state, ...stateReset, audio: action.audio, trackType: action.trackType };

		case ACTION_PLAY_STATION: {
			const { station } = action;
			const stream = state.streams.find(
				item => item.stream_call_letters === station,
			);

			console.log( 'streaming info' );
			console.log( stream.stream_cmod_domain );
			console.log( stream.stream_tap_id );

			fullStop();

			console.log( 'triton check for ad availability' );

			let adConfig = {
				host: stream.stream_cmod_domain,
				type: 'preroll',
				format: 'vast',
				stationId: stream.stream_tap_id,
			};

			/***
			 * Sends demographic tracking information to triton.
			 */
			if ( window.authwatcher && window.authwatcher.lastLoggedInUser ) {
				if (  'undefined' !== typeof window.authwatcher.lastLoggedInUser.demographicsset ) {
					if ( window.authwatcher.lastLoggedInUser.demographicsset ) {
						console.log( 'triton','params sent' );
						adConfig['trackingParameters'] = {
							postalcode: window.authwatcher.lastLoggedInUser.zipcode,
							gender: window.authwatcher.lastLoggedInUser.gender,
							dob: window.authwatcher.lastLoggedInUser.dateofbirth,
						};
					}
				}
			}


			tdplayer.playAd( 'tap', adConfig );

			localStorage.setItem( 'station', station );

			return { ...state, ...stateReset, station };
		}

		case ACTION_PLAY_OMNY:
			fullStop();

			omnyplayer = action.player;
			omnyplayer.play();
			// Omny doesn't support sound provider, thus we can't change/control volume :(
			// omnyplayer.setVolume( state.volume );

			return { ...state, ...stateReset, audio: action.audio, trackType: action.trackType };

		case ACTION_PAUSE:
			if ( mp3player ) {
				mp3player.pause();
			} else if ( omnyplayer ) {
				omnyplayer.pause();
			} else if ( tdplayer ) {
				tdplayer.stop();
			}

			if ( 'podcast' === state.trackType ) {
				lyticsTrack( 'pause', state.cuePoint );
			}

			return { ...state, ...adReset };

		case ACTION_RESUME:
			if ( mp3player ) {
				mp3player.play();
			} else if ( omnyplayer ) {
				omnyplayer.play();
			} else if ( tdplayer ) {
				tdplayer.resume();
			}

			if ( 'podcast' === state.trackType ){
				lyticsTrack( 'play', state.cuePoint );
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

			//todo remove me
			console.log( 'cue point', action.cuePoint );

			if ( 'podcast' === state.trackType ) {
				userInteraction = false;
				lyticsTrack( 'play', action.cuePoint );
			}

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
			userInteraction = true;
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

		case ACTION_STREAM_START:
			interval = window.bbgiconfig.intervals.live_streaming;

			if ( 0 < interval ) {
				clearInterval( liveStreamInterval );

				liveStreamInterval = setInterval( function() {
					sendLiveStreamPlaying();
				}, interval * 60 * 1000 );
			}
			break;

		case ACTION_STREAM_STOP:
			clearInterval( liveStreamInterval );
			break;

		case ACTION_AUDIO_START:
			interval = window.bbgiconfig.intervals.inline_audio;

			if ( 0 < interval ) {
				clearInterval( inlineAudioInterval );

				inlineAudioInterval = setInterval( function() {
					sendInlineAudioPlaying();
				}, interval * 60 * 1000 );
			}
			break;

		case ACTION_AUDIO_STOP:
			clearInterval( inlineAudioInterval );
			if ( 'podcast' === state.trackType && 1 >= Math.abs( state.duration - state.time ) && !userInteraction ) {
				lyticsTrack( 'end', state.cuePoint );
			}
			break;

		case ACTION_AD_PLAYBACK_START:
			if ( !isAudioAdOnly() ) {
				document.body.classList.add( 'locked' );
			}
			return { ...state, adPlayback: true };

		case ACTION_AD_PLAYBACK_ERROR:
		case ACTION_AD_PLAYBACK_COMPLETE: {
			const { station } = state;

			loadNowPlaying( station );

			return { ...state, adPlayback: false };
		}

		case ACTION_AD_BREAK_SYNCED:
			return { ...state, ...adReset, adSynced: true };

		case ACTION_AD_BREAK_SYNCED_HIDE:
			return { ...state, ...adReset };

		case ACTION_UPDATE_USER_FEEDS:
		case ACTION_SET_USER_FEEDS: {
			const newstreams = ( action.feeds || [] )
				.filter(
					item => 'stream' === item.type && 0 < ( item.content || [] ).length,
				)
				.map( item => item.content[0] );

			const newstate = {
				...state,
				streams: newstreams.length ? newstreams : DEFAULT_STATE.streams,
			};

			if ( !initialStation ) {
				initialStation = getInitialStation( newstate.streams );
				if ( initialStation ) {
					newstate.station = initialStation.stream_call_letters;
				}
			}

			return newstate;
		}

		case ACTION_RESET_USER:
			return {
				...state,
				station: DEFAULT_STATE.station,
				streams: DEFAULT_STATE.streams,
			};

		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
