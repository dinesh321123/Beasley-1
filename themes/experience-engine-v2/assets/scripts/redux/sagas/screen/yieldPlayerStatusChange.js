import { put, select, takeLatest } from 'redux-saga/effects';
import { hideListenLive } from '../../actions/screen';
import { ACTION_STATUS_CHANGE, STATUSES } from '../../actions/player';
import { showSignInModal } from '../../actions/modal';

/**
 * Generator runs whenever [ ACTION_STATUS_CHANGE ]
 * is dispatched.
 * Fires hideListenLive() if Live Playing and NOT disallowing Listen Live Auto Close
 */
function* yieldPlayerStatusChange() {
	const playerStore = yield select(store => store.player);
	if (playerStore.status === STATUSES.LIVE_PLAYING) {
		const screenStore = yield select(store => store.screen);
		const modalStore = yield select(store => store.modal);
		if (screenStore.isAutoClosingListenLiveMode) {
			const delay = ms => new Promise(res => setTimeout(res, ms));
			console.log('hiding in 3.5 sec');
			yield delay(3500);
			yield put(hideListenLive());
			if (modalStore.isShowSigninModalMode) {
				yield put(showSignInModal());
			}
		} else if (modalStore.isShowSigninModalMode) {
			yield put(showSignInModal());
		}
	}
}

/**
 * Generator used to bind action and callback
 */
export default function* watchAutoHideListenLive() {
	yield takeLatest([ACTION_STATUS_CHANGE], yieldPlayerStatusChange);
}
