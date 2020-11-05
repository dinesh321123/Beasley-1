import { call, takeLatest } from 'redux-saga/effects';
import { ACTION_HIDE_SPLASH_SCREEN } from '../../actions/screen';

/**
 * Generator runs whenever [ ACTION_HIDE_SPLASH_SCREEN ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 */
function* yieldHideSplashScreen(action) {
	// Call Google Ads Refresh If dfp_needs_refresh Flag Is Set
	// NOTE: We Only Want To Refresh Here ON Initial Load When Page Originates From WordPress, Otherwise React Handles The Google Ads Refresh
	yield call(() => {
		const splashScreen = document.getElementById('splash-screen');
		const { googletag, dfp_needs_refresh } = window;
		if (dfp_needs_refresh) {
			window.dfp_needs_refresh = false;
			googletag.cmd.push(() => {
				const interstitialAdDiv = window.top.document.getElementById(
					'div-gpt-ad-1484200509775-3',
				);

				if (interstitialAdDiv) {
					// Open Div To Full Screen So That Lazy Ad Can Show
					interstitialAdDiv.style.cssText =
						'bottom: 0; height: 100%; left: 0; position: fixed; right: 0; top: 0; width: 100%; z-index: 9000003;';
				}

				// Refresh All Ads
				googletag.pubads().refresh(); // Refresh ALL Slots
			});
		}

		if (splashScreen) {
			splashScreen.parentNode.removeChild(splashScreen);
		}
	});
}

/**
 * Generator used to bind action and callback
 */
export default function* watchHideSplashScreen() {
	yield takeLatest([ACTION_HIDE_SPLASH_SCREEN], yieldHideSplashScreen);
}
