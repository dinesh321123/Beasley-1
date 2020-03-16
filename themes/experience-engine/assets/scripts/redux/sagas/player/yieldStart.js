import { call, takeLatest, select } from 'redux-saga/effects';
import {
	sendInlineAudioPlaying,
	sendLiveStreamPlaying,
} from '../../../library/google-analytics';
import { ACTION_PLAYER_START } from '../../actions/player';

/**
 * @function yieldStart
 * Generator runs whenever ACTION_AUDIO_START is dispatched
 */
function* yieldStart() {
	const playerStore = yield select(({ player }) => player);

	// Get interval from global
	const interval = window.bbgiconfig.intervals.live_streaming;

	if (playerStore.playerType === 'mp3player') {
		// Get inlineAudioInterval from window, default null
		let { inlineAudioInterval = null } = window;

		// If interval
		if (interval && interval > 0) {
			// Clear if set
			if (inlineAudioInterval) {
				yield call([window, 'clearInterval'], inlineAudioInterval);
			}

			// Set inlineAudioInterval
			inlineAudioInterval = yield call(
				[window, 'setInterval'],
				() => {
					sendInlineAudioPlaying();
				},
				interval * 60 * 1000,
			);
		}
	} else if (playerStore.playerType === 'tdplayer') {
		// Get liveStreamInterval from window, default null
		let { liveStreamInterval = null } = window;

		// If interval
		if (interval && interval > 0) {
			// Clear if set
			if (liveStreamInterval) {
				yield call([window, 'clearInterval'], liveStreamInterval);
			}

			// Set liveStreamInterval
			liveStreamInterval = yield call(
				[window, 'setInterval'],
				() => {
					sendLiveStreamPlaying();
				},
				interval * 60 * 1000,
			);
		}
	}
}

/**
 * @function watchAudioStart
 * Generator used to bind action and callback
 */
export default function* watchStart() {
	yield takeLatest([ACTION_PLAYER_START], yieldStart);
}
