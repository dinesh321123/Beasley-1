import { call, takeLatest, select } from 'redux-saga/effects';
import { lyticsTrack } from '../../utilities';
import {
	ACTION_RESUME,
} from '../../actions/player';

/**
 * @function yieldResume
 * Generator runs whenever ACTION_RESUME is dispatched
 */
function* yieldResume() {
	// Get player from state
	const playerStore = yield select( ( { player } ) => player );

	// Destructure from playerStore in state
	const {
		trackType,
		cuePoint,
		player,
	} = playerStore;

	// If player
	if ( player ) {

		// If has play (omny mp3)
		if ( 'function' === typeof player.play ) {
			yield call( [ player, 'play' ] );

		// If has resume (tdplayer)
		} else if ( 'function' === typeof player.resume ) {
			yield call( [ player, 'resume' ] );
		}
	}

	if (
		cuePoint &&
		'podcast' === trackType &&
		'function' === typeof lyticsTrack
	) {
		yield call( lyticsTrack, 'play', cuePoint );
	}
}

/**
 * @function watchResume
 * Generator used to bind action and callback
 */
export default function* watchResume() {
	yield takeLatest( [ACTION_RESUME], yieldResume );
}

