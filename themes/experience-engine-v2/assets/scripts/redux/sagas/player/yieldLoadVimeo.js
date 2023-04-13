// Import saga effects
import { call, takeLatest } from 'redux-saga/effects';
import { initializeVimeo } from '../../utilities';
import { ACTION_LOAD_VIMEO } from '../../actions/player';

/**
 * @function yieldSetPlayer
 *
 * Generator runs whenever ACTION_SET_PLAYER is dispatched
 */
function* yieldLoadVimeo() {
	// Call loadNowPlaying
	yield call(initializeVimeo);
}

/**
 * @function watchSetPlayer
 *
 * Generator used to bind action and callback
 */
export default function* watchLoadVimeo() {
	yield takeLatest([ACTION_LOAD_VIMEO], yieldLoadVimeo);
}
