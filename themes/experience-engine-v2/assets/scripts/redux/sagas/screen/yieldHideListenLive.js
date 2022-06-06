import { put, select, takeLatest } from 'redux-saga/effects';
import { ACTION_HIDE_LISTEN_LIVE } from '../../actions/screen';
import { hideDropdownAd } from '../../actions/dropdownad';
import { STATUSES } from '../../actions/player';
import { showSignInModal } from '../../actions/modal';

/**
 * Generator runs whenever [ ACTION_HIDE_LISTEN_LIVE ]
 * is dispatched
 */
function* yieldHideListenLive() {
	yield put(hideDropdownAd());
	const listenlivecontainer = document.getElementById('my-listen-dropdown2');
	listenlivecontainer.style.display = 'none';

	const playerStore = yield select(store => store.player);
	if (playerStore.status === STATUSES.LIVE_PLAYING) {
		yield put(showSignInModal());
	}
}

/**
 * Generator used to bind action and callback
 */
export default function* watchHideListenLive() {
	yield takeLatest([ACTION_HIDE_LISTEN_LIVE], yieldHideListenLive);
}
