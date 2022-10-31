import { call, put, takeLatest } from 'redux-saga/effects';
import NProgress from 'nprogress';
import { manageBbgiConfig, updateTargeting } from '../../utilities';
import { ACTION_LOADED_PARTIAL, hideSplashScreen } from '../../actions/screen';

/**
 * Generator runs whenever [ ACTION_LOADED_PARTIAL ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 */
function* yieldLoadedPartial(action) {
	console.log('LOADED PARTIAL');

	// Destructure from action payload
	const { document: pageDocument } = action;

	// Start the loading progress bar.
	yield call([NProgress, 'done']);

	// Update BBGI Config
	yield call(manageBbgiConfig, pageDocument);

	// Update Ad Targeting
	yield call(updateTargeting);

	// make sure to hide splash screen.
	yield put(hideSplashScreen);
}

/**
 * @function watchLoadedPartial
 * Generator used to bind action and callback
 */
export default function* watchLoadedPartial() {
	yield takeLatest([ACTION_LOADED_PARTIAL], yieldLoadedPartial);
}
