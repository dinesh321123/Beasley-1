import { call, put, takeLatest, select } from 'redux-saga/effects';
import NProgress from 'nprogress';
import {
	manageScripts,
	manageInlineScripts,
	manageBbgiConfig,
	updateTargeting,
	renderSendToNews,
	handleInjectos,
} from '../../utilities';
import {
	ACTION_LOADED_PAGE,
	ACTION_HISTORY_HTML_SNAPSHOT,
	hideSplashScreen,
} from '../../actions/screen';
import {
	slugify,
	dispatchEvent,
	updateCanonicalUrl,
	getBeasleyCanonicalUrl,
} from '../../../library';
// import resetScrollToTop from '../../utilities/player/resetScrollToTop';
import { doPageStackScroll } from '../../../library/page-utils';

/**
 * Updates window.history with new url and title
 *
 * @param {string} url The URL to update history with
 * @param {object} pageDocument
 */
function updateHistory(url, title) {
	const { history, location, pageXOffset, pageYOffset } = window;
	const uuid = slugify(url);

	history.replaceState(
		{ ...history.state, pageXOffset, pageYOffset },
		document.title,
		location.href,
	);
	history.pushState({ uuid, pageXOffset: 0, pageYOffset: 0 }, title, url);

	dispatchEvent('pushstate');
}

/**
 * Generator runs whenever [ ACTION_LOADED_PAGE ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 */
function* yieldLoadedPage(action) {
	console.log('LOADED FULL PAGE');
	const { url, response, options, parsedHtml } = action;
	const { ad_reset_digital_enabled } = window.bbgiconfig;
	const urlSlugified = slugify(url);
	const pageDocument = parsedHtml.document;

	// Test var to determine if isAdmin
	// Useful for adding body classes for example
	let isAdmin = false;

	// Screen store from state
	const screenStore = yield select(({ screen }) => screen);

	// Update BBGI Config
	yield call(manageBbgiConfig, pageDocument);

	const leavingPageUrl = getBeasleyCanonicalUrl();
	updateCanonicalUrl(url);

	if (ad_reset_digital_enabled === 'on' && window.fireResetPixel) {
		window.fireResetPixel(url);
	}

	// Update Ad Targeting
	yield call(updateTargeting);

	// Fix WP Admin Bar
	if (pageDocument) {
		const barId = 'wpadminbar';
		const wpadminbar = document.getElementById(barId);
		if (wpadminbar) {
			// True, we are loading an admin page
			isAdmin = true;
			const newbar = pageDocument.getElementById(barId);
			if (newbar) {
				wpadminbar.parentNode.replaceChild(newbar, wpadminbar);
			}
		}
	}

	// dispatch history html snapshot
	yield put({
		type: ACTION_HISTORY_HTML_SNAPSHOT,
		uuid: urlSlugified,
		data: response.html,
	});

	// Start the loading progress bar.
	yield call([NProgress, 'done']);

	// Update Scripts.
	yield call(manageScripts, parsedHtml.scripts, screenStore.scripts);

	// console.log('***Yield Loading Page Adjusting Scroll');
	// make sure the user scroll bar is into view.
	// yield call(scrollIntoView);
	console.log('**** CALLING NEW PAGE PROCESSING ****');
	yield call(doPageStackScroll, leavingPageUrl, url);

	// make sure to hide splash screen.
	yield put(hideSplashScreen());

	document.title = pageDocument.title;
	document.body.className = pageDocument.body.className;

	// If admin, need to add admin-bar class
	if (isAdmin) {
		document.body.classList.add('admin-bar');
	}

	try {
		window.dispatchEvent(new Event('resize'));
	} catch (e) {
		// no-op
	}

	// last step is update history, return early if it's not needed.
	if (options.suppressHistory) {
		console.log('***Yield Load Not Updating History');
		yield call(manageInlineScripts, parsedHtml.inlineScripts);
		return;
	}

	yield call(updateHistory, url, pageDocument.title);

	yield call(renderSendToNews);

	yield call(handleInjectos);

	// yield call(handleNationalContest);

	yield call(manageInlineScripts, parsedHtml.inlineScripts);

	// debug code
	console.log('yieldLoadedPage() - history updated');
}

/**
 * Generator used to bind action and callback
 */
export default function* watchLoadedPage() {
	yield takeLatest([ACTION_LOADED_PAGE], yieldLoadedPage);
}
