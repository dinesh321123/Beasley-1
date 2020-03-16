import { call, select, takeLatest } from 'redux-saga/effects';
import { ACTION_PLAYER_END } from '../../actions/player';
import { lyticsTrack } from '../../utilities';

function* yieldEnd() {
	const playerStore = yield select(({ player }) => player);

	if (playerStore.playerType === 'tdplayer') {
		const { liveStreamInterval = null } = window;

		if (liveStreamInterval) {
			yield call([window, 'clearInterval'], liveStreamInterval);
		}
	} else if (playerStore.playerType === 'mp3player') {
		const {
			trackType,
			duration,
			time,
			cuePoint,
			userInteraction,
		} = playerStore;

		const { inlineAudioInterval = null } = window;

		// Clear interval
		if (inlineAudioInterval) {
			yield call([window, 'clearInterval'], inlineAudioInterval);
		}

		// Checks then call lyticsTrack
		if (
			trackType &&
			trackType === 'podcast' &&
			Math.abs(duration - time) <= 1 &&
			!userInteraction
		) {
			yield call(lyticsTrack, 'end', cuePoint);
		}
	}
}

export default function* watchEnd() {
	yield takeLatest([ACTION_PLAYER_END], yieldEnd);
}
