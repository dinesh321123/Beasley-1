import { put, takeLatest } from 'redux-saga/effects';
import { ACTION_HIDE_LISTEN_LIVE } from '../../actions/screen';
import { hideDropdownAd } from '../../actions/dropdownad';

/**
 * Generator runs whenever [ ACTION_HIDE_LISTEN_LIVE ]
 * is dispatched
 */
function* yieldHideListenLive() {
	yield put(hideDropdownAd());
	const listenlivecontainer = document.getElementById('my-listen-dropdown2');
	listenlivecontainer.style.display = 'none';
}

/**
 * Generator used to bind action and callback
 */
export default function* watchHideListenLive() {
	yield takeLatest([ACTION_HIDE_LISTEN_LIVE], yieldHideListenLive);
}
