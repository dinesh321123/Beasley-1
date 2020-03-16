import { call, takeLatest, select } from 'redux-saga/effects';
import { lyticsTrack, loadNowPlaying } from '../../utilities';
import { ACTION_CUEPOINT_CHANGE } from '../../actions/player';

/**
 * @function yieldCuePointChange
 * Generator runs whenever ACTION_CUEPOINT_CHANGE is dispatched
 * NOTE: Omny doesn't support sound provider, thus we can't change/control volume :(
 *
 * @param {Object} action dispatched action
 * @param {String} action.cuePoint cuePoint from action
 */
function* yieldCuePointChange({ cuePoint }) {
	// Get player from state
	const playerStore = yield select(({ player }) => player);

	// Call loadNowPlaying
	yield call(loadNowPlaying, playerStore);

	// Destructure
	const { trackType } = playerStore;

	// If action passes cuePoint
	// If trackType in state is podcast
	// If lyticsTrack has a play method
	if (
		cuePoint &&
		trackType === 'podcast' &&
		typeof lyticsTrack.play === 'function'
	) {
		// Call lyticsTrack
		yield call(lyticsTrack, 'play', cuePoint);
	}
}

/**
 * @function watchCuePointChange
 * Generator used to bind action and callback
 */
export default function* watchCuePointChange() {
	yield takeLatest([ACTION_CUEPOINT_CHANGE], yieldCuePointChange);
}
