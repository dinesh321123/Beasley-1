import { put, select, takeLatest } from 'redux-saga/effects';
import {
	ACTION_AD_PLAYBACK_ERROR,
	ACTION_GAM_AD_PLAYBACK_START,
	adPlaybackStop,
} from '../../actions/player';
import getWhetherPlayGAMPreroll from '../../utilities/player/getWhetherPlayGAMPreroll';

/**
 * @function yieldGamAdPlaybackStart
 * Generator runs whenever ACTION_AD_PLAYBACK_START is dispatched
 */
function* yieldGamAdPlaybackStart({ nowTime }) {
	// Get player from state
	const playerStore = yield select(({ player }) => player);
	const { lastAdPlaybackTime } = playerStore;

	if (!getWhetherPlayGAMPreroll(nowTime, lastAdPlaybackTime)) {
		yield put(adPlaybackStop(ACTION_AD_PLAYBACK_ERROR));
	}
}

/**
 * @function watchAdPlaybackStart
 * Generator used to bind action and callback
 */
export default function* watchGamAdPlaybackStart() {
	yield takeLatest([ACTION_GAM_AD_PLAYBACK_START], yieldGamAdPlaybackStart);
}
