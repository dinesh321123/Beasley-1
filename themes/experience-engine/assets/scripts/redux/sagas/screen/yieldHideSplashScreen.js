import { call, takeLatest } from 'redux-saga/effects';
import { ACTION_HIDE_SPLASH_SCREEN } from '../../actions/screen';

/**
 * @function yieldHideSplashScreen
 * Generator runs whenever [ ACTION_HIDE_SPLASH_SCREEN ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 */
function* yieldHideSplashScreen(action) {
	yield call(
		[window, 'setTimeout'],
		() => {
			const splashScreen = document.getElementById('splash-screen');
			if (splashScreen) {
				splashScreen.parentNode.removeChild(splashScreen);
			}
		},
		2000,
	);
}

/**
 * @function watchHideSplashScreen
 * Generator used to bind action and callback
 */
export default function* watchHideSplashScreen() {
	yield takeLatest([ACTION_HIDE_SPLASH_SCREEN], yieldHideSplashScreen);
}
