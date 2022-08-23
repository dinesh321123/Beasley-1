import { call, takeLatest } from 'redux-saga/effects';

import { ACTION_LOADING_PAGE } from '../../actions/screen';
import { doUpdatePageStack } from '../../../library/page-utils';

/**
 * Generator runs whenever [ ACTION_LOADING_PAGE ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 * @param { Object } action.url url from action
 */
function* yieldUpdatePageStats({ url }) {
	console.log('**** CALLING NEW PAGE PROCESSING ****');
	yield call(doUpdatePageStack, window.location.href, url);
}

/**
 * Generator used to bind action and callback
 */
export default function* watchLoadingPage2() {
	yield takeLatest([ACTION_LOADING_PAGE], yieldUpdatePageStats);
}
