import { call, takeLatest, select } from 'redux-saga/effects';
import { sendInlineAudioPlaying } from '../../../library/google-analytics';
import { ACTION_PLAYER_START } from '../../actions/player';
// import { showSignInModal } from '../../actions/modal';

/**
 * @function yieldStart
 * Generator runs whenever ACTION_AUDIO_START is dispatched
 */
function* yieldStart() {
	console.log('yieldStart()');
	const playerStore = yield select(({ player }) => player);
	const authStore = yield select(({ auth }) => auth);
	const modalStore = yield select(({ modal }) => modal);

	if (!authStore.user && !modalStore.signInWasShown) {
		// disable signin
		// yield put(showSignInModal());
	}

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
	}
}

/**
 * @function watchAudioStart
 * Generator used to bind action and callback
 */
export default function* watchStart() {
	yield takeLatest([ACTION_PLAYER_START], yieldStart);
}
