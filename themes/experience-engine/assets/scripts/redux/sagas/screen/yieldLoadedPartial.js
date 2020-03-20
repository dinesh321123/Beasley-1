import { call, put, takeLatest } from 'redux-saga/effects';
import NProgress from 'nprogress';
import { manageBbgiConfig } from '../../utilities';
import {
	ACTION_LOADED_PARTIAL,
	ACTION_HIDE_SPLASH_SCREEN,
} from '../../actions/screen';

/**
 * Generator runs whenever [ ACTION_LOADED_PARTIAL ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 */
function* yieldLoadedPartial(action) {
	// Destructure from action payload
	const { document: pageDocument } = action;

	// Start the loading progress bar.
	yield call([NProgress, 'done']);

	// Update BBGI Config
	yield call(manageBbgiConfig, pageDocument);

	// make sure to hide splash screen.
	yield put({ type: ACTION_HIDE_SPLASH_SCREEN });
}

/**
 * @function watchLoadedPartial
 * Generator used to bind action and callback
 */
export default function* watchLoadedPartial() {
	yield takeLatest([ACTION_LOADED_PARTIAL], yieldLoadedPartial);
}
