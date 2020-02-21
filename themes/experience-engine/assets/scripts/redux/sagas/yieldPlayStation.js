
import { call, takeLatest, select } from 'redux-saga/effects';
import {
	fullStop,
	livePlayerLocalStorage,
} from '../utilities/';
import {
	ACTION_PLAY_STATION,
} from '../actions/player';

/**
 * @function getStreamByStation
 * Used to return a helper function that will receive an
 * item with a stream_call_letters property that is then
 * compared against the station parameter
 *
 * @param {String} station station id value
 * @returns {Function} method that tests matching station in an object
 */
const getStreamByStation = station =>
	( { stream_call_letters } ) =>
		stream_call_letters === station;

/**
 * @function yieldPlayStation
 * Generator runs whenever ACTION_PLAY_STATION is dispatched
 *
 * @param {Object} action dispatched action
 * @param {String} action.station station from action
 */
function* yieldPlayStation( { station } ) {

	console.log( 'yieldPlayAudio' );

	// Get player from state
	const playerStore = yield select( ( { player } ) => player );

	// Get streams from state
	const { streams = [] } = playerStore;

	// Find matching stream
	const stream = yield call( [ streams, 'find' ], getStreamByStation( station ) );

	// Destructure from window
	const {
		tdplayer, // Global player
		authwatcher, // Triton
	} = window;

	// Setup adConfig used by player and triton call
	const adConfig = {
		host: stream.stream_cmod_domain,
		type: 'preroll',
		format: 'vast',
		stationId: stream.stream_tap_id,
	};

	// Call triton, must live here since it modifies the adConfig object
	// before being sent to the player API
	if ( authwatcher && authwatcher.lastLoggedInUser ) {
		if (  'undefined' !== typeof authwatcher.lastLoggedInUser.demographicsset ) {
			if ( authwatcher.lastLoggedInUser.demographicsset ) {
				console.log( 'triton','params sent' );
				adConfig['trackingParameters'] = {
					postalcode: authwatcher.lastLoggedInUser.zipcode,
					gender: authwatcher.lastLoggedInUser.gender,
					dob: authwatcher.lastLoggedInUser.dateofbirth,
				};
			}
		}
	}

	// Call fullStop
	yield call( fullStop );

	// Call tdplayer.playAd
	if (
		tdplayer &&
		stream &&
		'function' === typeof tdplayer.playAd
	) {
		yield call(
			[ tdplayer, 'playAd' ],
			'tap',
			adConfig,
		);
	}

	// Call livePlayerLocalStorage
	if(
		livePlayerLocalStorage &&
		'function' === typeof livePlayerLocalStorage.setItem
	) {
		yield call( [ livePlayerLocalStorage, 'setItem' ], 'station', station );
	}


}

/**
 * @function watchPlayStation
 * Generator used to bind action and callback
 */
export default function* watchPlayStation() {
	yield takeLatest( [ACTION_PLAY_STATION], yieldPlayStation );
}
