import { put, select, takeLatest } from 'redux-saga/effects';
import { hideListenLive } from '../../actions/screen';
import { ACTION_STATUS_CHANGE, STATUSES } from '../../actions/player';

/**
 * Generator runs whenever [ ACTION_STATUS_CHANGE ]
 * is dispatched.
 * Fires hideListenLive() if Live Playing and NOT disallowing Listen Live Auto Close
 */
function* yieldAutoHideHideListenLive() {
	const playerStore = yield select(store => store.player);
	if (playerStore.status === STATUSES.LIVE_PLAYING) {
		const screenStore = yield select(store => store.screen);
		if (screenStore.isAllowingListenLiveAutoClose) {
			const delay = ms => new Promise(res => setTimeout(res, ms));
			console.log('hiding in 5 sec');
			yield delay(5000);
			yield put(hideListenLive());
		}
	}
}

/**
 * Generator used to bind action and callback
 */
export default function* watchAutoHideListenLive() {
	yield takeLatest([ACTION_STATUS_CHANGE], yieldAutoHideHideListenLive);
}
