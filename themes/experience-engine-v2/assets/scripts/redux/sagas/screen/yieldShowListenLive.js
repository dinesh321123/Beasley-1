import { put, takeLatest } from 'redux-saga/effects';
import { ACTION_SHOW_LISTEN_LIVE } from '../../actions/screen';
import { refreshDropdownAd } from '../../actions/dropdownad';
import { sendOpenLLDropDown } from '../../../library';

/**
 * Generator runs whenever [ ACTION_SHOW_LISTEN_LIVE ]
 * is dispatched
 */
function* yieldShowListenLive({ isTriggeredByStream }) {
	console.log(`Calling yield show ll - ${isTriggeredByStream}`);
	const listenlivecontainer = document.getElementById('my-listen-dropdown2');
	const listenliveStyle = window.getComputedStyle(listenlivecontainer);
	if (listenliveStyle.display !== 'block') {
		yield put(refreshDropdownAd());
		listenlivecontainer.style.display = 'block';
	}
	sendOpenLLDropDown(isTriggeredByStream);
}

/**
 * Generator used to bind action and callback
 */
export default function* watchShowListenLive() {
	yield takeLatest([ACTION_SHOW_LISTEN_LIVE], yieldShowListenLive);
}
