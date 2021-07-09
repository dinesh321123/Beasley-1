import { call, takeLatest, select } from 'redux-saga/effects';
import { ACTION_PAUSE } from '../../actions/player';

/**
 * @function yieldPause
 * Generator runs whenever ACTION_PAUSE is dispatched
 */
function* yieldPause() {
	// Get player from state
	const playerStore = yield select(({ player }) => player);

	// Destructure from playerStore in state
	const { player } = playerStore;

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
}

/**
 * @function watchPause
 * Generator used to bind action and callback
 */
export default function* watchPause() {
	yield takeLatest([ACTION_PAUSE], yieldPause);
}
