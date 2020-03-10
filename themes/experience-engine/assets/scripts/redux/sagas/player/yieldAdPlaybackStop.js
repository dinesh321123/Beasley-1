// Import saga effects
import { put, takeLatest, select, call } from 'redux-saga/effects';

// Import action constant(s)
import {
	ACTION_AD_PLAYBACK_STOP,
} from '../../actions/player';

/**
 * @function yieldAdPlaybackStop
 * Runs whenever ACTION_AD_PLAYBACK_STOP is dispatched
 *
 * @param {Object} action dispatched action
 * @param {Object} action.payload payload from dispatch
 */
function* yieldAdPlaybackStop( { payload } ) {
	// Destructure from payload
	const { actionType } = payload;

	// Player store from state
	const playerStore = yield select( ( { player } ) => player );

	// Destructure from playerStore
	const {
		adPlayback,
		station,
		player,
		playerType,
	} = playerStore;

	// Update DOM
	yield call( [ document.body.classList, 'remove' ], 'locked' );

	// If the current player is a tdplayer
	if( 'tdplayer' === playerType ) {

		// If adPlayback and player.skipAd
		if(
			adPlayback &&
			'function' === typeof player.skipAd
		) {
			yield call( [ player, 'skipAd' ] );
		}

		// If station and player.skipAd
		if(
			station &&
			'function' === typeof player.play
		) {
			yield call( [ player, 'play' ], { station } );
		}

	}

	// finalize dispatch
	yield put( { type: actionType } );
}

/**
 * @function watchAdPlaybackStop
 * Watches for playback stop.
 */
export default function* watchAdPlaybackStop() {
	yield takeLatest( [ACTION_AD_PLAYBACK_STOP], yieldAdPlaybackStop );
}
