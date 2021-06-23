import { call, takeLatest, select } from 'redux-saga/effects';
import { ACTION_RESUME } from '../../actions/player';

/**
 * @function yieldResume
 * Generator runs whenever ACTION_RESUME is dispatched
 */
function* yieldResume() {
	// Get player from state
	const playerStore = yield select(({ player }) => player);

	// Destructure from playerStore in state
	const { player } = playerStore;

	// If player
	if (player) {
		// If has play (omny mp3)
		if (typeof player.play === 'function') {
			yield call([player, 'play']);

			// If has resume (tdplayer)
		} else if (typeof player.resume === 'function') {
			yield call([player, 'resume']);
		}
	}
}

/**
 * @function watchResume
 * Generator used to bind action and callback
 */
export default function* watchResume() {
	yield takeLatest([ACTION_RESUME], yieldResume);
}
