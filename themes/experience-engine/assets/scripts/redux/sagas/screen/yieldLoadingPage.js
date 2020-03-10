import { call, takeLatest } from 'redux-saga/effects';
import NProgress from 'nprogress';
import {
	updateCorrelator,
	clearTargeting,
} from '../../utilities';
import {
	ACTION_LOADING_PAGE,
	ACTION_LOADING_PARTIAL,
} from '../../actions/screen';

/**
 * @function yieldLoadingPage
 * Generator runs whenever [ ACTION_INIT_PAGE ]
 * is dispatched
 *
 * @param { Object } action Dispatched action
 * @param { Object } action.url url from action
 */
function* yieldLoadingPage( { url } ) {
	if ( window.location.href !== url ) {

		// Call updateCorrelator
		yield call( updateCorrelator );

		// Call clearTargeting
		yield call( clearTargeting );
	}

	// Call NProgress start
	yield call( [ NProgress, 'start' ] );
}


/**
 * @function watchLoadingPage
 * Generator used to bind action and callback
 */
export default function* watchLoadingPage() {
	yield takeLatest( [ ACTION_LOADING_PARTIAL, ACTION_LOADING_PAGE ], yieldLoadingPage );
}
