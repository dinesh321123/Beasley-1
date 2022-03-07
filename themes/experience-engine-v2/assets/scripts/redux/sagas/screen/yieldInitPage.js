import { call, put, takeLatest, select } from 'redux-saga/effects';
import cssVars from 'css-vars-ponyfill';

import { manageScripts } from '../../utilities';
import {
	ACTION_INIT_PAGE,
	ACTION_SET_SCREEN_STATE,
	initPageLoaded,
} from '../../actions/screen';
import { slugify } from '../../../library';

/**
 * Generator runs whenever [ ACTION_INIT_PAGE ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 * @param { Object } action.scripts Scripts from action
 */
function* yieldInitPage(action) {
	const { scripts } = action.payload;

	// Screen store from state.
	const screenStore = yield select(({ screen }) => screen);

	// Call manageScripts.
	yield call(manageScripts, scripts, screenStore.scripts);

	// set up cssVars polyfill.
	if (window.bbgiconfig && window.bbgiconfig.cssvars) {
		cssVars(window.bbgiconfig.cssvars);
	}

	yield put({ type: ACTION_SET_SCREEN_STATE, payload: action.payload });

	// replace current state with proper markup
	const { history, location, pageXOffset, pageYOffset } = window;
	const { ad_reset_digital_enabled } = window.bbgiconfig;
	const uuid = slugify(location.href);
	const html = document.documentElement.outerHTML;

	history.replaceState(
		{
			uuid,
			pageXOffset,
			pageYOffset,
		},
		document.title,
		location.href,
	);

	if (ad_reset_digital_enabled === 'on' && window.fireResetPixel) {
		window.fireResetPixel(location.href);
	}

	yield put(initPageLoaded(uuid, html));
}

/**
 * Generator used to bind action and callback
 */
export default function* watchInitPage() {
	yield takeLatest([ACTION_INIT_PAGE], yieldInitPage);
}
