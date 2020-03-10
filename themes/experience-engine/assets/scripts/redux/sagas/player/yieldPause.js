import { call, takeLatest, select } from 'redux-saga/effects';
import { lyticsTrack } from '../../utilities';
import {
	ACTION_PAUSE,
} from '../../actions/player';

/**
 * @function yieldPause
 * Generator runs whenever ACTION_PAUSE is dispatched
 */
function* yieldPause() {
	// Get player from state
	const playerStore = yield select( ( { player } ) => player );

	// Destructure from playerStore in state
	const {
		trackType,
		cuePoint,
		player,
	} = playerStore;

	// Simplifying, by calling the state player and
	// sniffing for its function type, we can call
	// what is available (tdplayer has stop, omny mp3 have pause)
	if ( player ) {
		if ( 'function' === typeof player.pause ) {
			yield call( [ player, 'pause' ] );
		} else if ( 'function' === typeof player.stop ) {
			yield call( [ player, 'stop' ] );
		}
	}

	// Call lyticsTrack
	if (
		cuePoint &&
		'podcast' === trackType &&
		'function' === typeof lyticsTrack
	) {
		yield call( lyticsTrack, 'pause', cuePoint );
	}
}

/**
 * @function watchPause
 * Generator used to bind action and callback
 */
export default function* watchPause() {
	yield takeLatest( [ACTION_PAUSE], yieldPause );
}

