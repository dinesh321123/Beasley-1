import { call, takeLatest, select } from 'redux-saga/effects';
import { isAudioAdOnly } from '../../../library/strings';
import { ACTION_AD_PLAYBACK_START } from '../../actions/player';

/**
 * @function yieldAdPlaybackStart
 * Generator runs whenever ACTION_AD_PLAYBACK_START is dispatched
 */
function* yieldAdPlaybackStart() {
	// Get player from state
	const playerStore = yield select(({ player }) => player);

	// Check for falsey isAudioAdOnly
	if (!isAudioAdOnly(playerStore)) {
		// Add class to body
		yield call([document.body.classList, 'add'], 'locked');
	}
}

/**
 * @function watchAdPlaybackStart
 * Generator used to bind action and callback
 */
export default function* watchAdPlaybackStart() {
	yield takeLatest([ACTION_AD_PLAYBACK_START], yieldAdPlaybackStart);
}
