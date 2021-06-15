import { call, select, takeLatest } from 'redux-saga/effects';
import { ACTION_PLAYER_END } from '../../actions/player';

function* yieldEnd() {
	const playerStore = yield select(({ player }) => player);

	if (playerStore.playerType === 'tdplayer') {
		const { liveStreamInterval = null } = window;

		if (liveStreamInterval) {
			yield call([window, 'clearInterval'], liveStreamInterval);
		}
	} else if (playerStore.playerType === 'mp3player') {
		const { inlineAudioInterval = null } = window;

		// Clear interval
		if (inlineAudioInterval) {
			yield call([window, 'clearInterval'], inlineAudioInterval);
		}
	}
}

export default function* watchEnd() {
	yield takeLatest([ACTION_PLAYER_END], yieldEnd);
}
