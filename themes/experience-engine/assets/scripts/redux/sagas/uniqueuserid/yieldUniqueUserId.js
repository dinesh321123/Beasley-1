import { takeEvery, select } from 'redux-saga/effects';
// import { ACTION_PLAYER_STOP } from '../../actions/player';
import {
	ACTION_READ_COOKIES,
	// ACTION_BID_SESSION_COOKIES,
} from '../../actions/uniqueuserid';

function* yieldBIDSessionCookies() {
	// yield put({ type: ACTION_BID_SESSION_COOKIES });
	yield select(({ uniqueuserid }) => uniqueuserid);
	// const uniqueUserIDStore = yield select(({ uniqueuserid }) => uniqueuserid);
	// console.log('Store from Worker Saga: ', uniqueUserIDStore);
}

export default function* watchUniqueUserId() {
	yield takeEvery([ACTION_READ_COOKIES], yieldBIDSessionCookies);
}
