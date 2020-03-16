import { call, takeLatest, select } from 'redux-saga/effects';
import { lyticsTrack } from '../../utilities';
import { ACTION_PAUSE } from '../../actions/player';

/**
 * @function yieldPause
 * Generator runs whenever ACTION_PAUSE is dispatched
 */
function* yieldPause() {
	// Get player from state
	const playerStore = yield select(({ player }) => player);

	// Destructure from playerStore in state
	const { trackType, cuePoint, player } = playerStore;

	// Simplifying, by calling the state player and
	// sniffing for its function type, we can call
	// what is available (tdplayer has stop, omny mp3 have pause)
	if (player) {
		if (typeof player.pause === 'function') {
			yield call([player, 'pause']);
		} else if (typeof player.stop === 'function') {
			yield call([player, 'stop']);
		}
	}

	// Call lyticsTrack
	if (
		cuePoint &&
		trackType === 'podcast' &&
		typeof lyticsTrack === 'function'
	) {
		yield call(lyticsTrack, 'pause', cuePoint);
	}
}

/**
 * @function watchPause
 * Generator used to bind action and callback
 */
export default function* watchPause() {
	yield takeLatest([ACTION_PAUSE], yieldPause);
}
