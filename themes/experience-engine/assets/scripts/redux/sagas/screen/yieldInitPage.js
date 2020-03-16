import { call, put, takeLatest, select } from 'redux-saga/effects';
import cssVars from 'css-vars-ponyfill';

import { manageScripts } from '../../utilities';
import {
	ACTION_INIT_PAGE,
	ACTION_SET_SCREEN_STATE,
} from '../../actions/screen';

/**
 * @function yieldInitPage
 * Generator runs whenever [ ACTION_INIT_PAGE ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 * @param { Object } action.scripts Scripts from action
 */
function* yieldInitPage(action) {
	const { scripts } = action.payload;

	// Screen store from state
	const screenStore = yield select(({ screen }) => screen);

	// Call manageScripts
	yield call(manageScripts, scripts, screenStore.scripts);

	if (window.bbgiconfig && window.bbgiconfig.cssvars) {
		cssVars(window.bbgiconfig.cssvars);
	}

	yield put({ type: ACTION_SET_SCREEN_STATE, payload: action.payload });
}

/**
 * @function watchInitPage
 * Generator used to bind action and callback
 */
export default function* watchInitPage() {
	yield takeLatest([ACTION_INIT_PAGE], yieldInitPage);
}
