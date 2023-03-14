import { call, put, takeLatest, select } from 'redux-saga/effects';
import mParticle from '@mparticle/web-sdk';
import MediaSession from '@mparticle/web-media-sdk';
import { sendInlineAudioPlaying } from '../../../library/google-analytics';
import { ACTION_PLAYER_START } from '../../actions/player';
import { showSignInModal } from '../../actions/modal';

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
		yield put(showSignInModal());
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

	window.mediaSession = new MediaSession(
		mParticle, // mParticle SDK Instance
		'1234567', // Custom media ID, added as content_id for media events
		'Funny Internet cat video', // Custom media Title, added as content_title for media events
		120000, // Duration in milliseconds, added as content_duration for media events
		'Audio', // Content Type (Video or Audio), added as content_type for media events
		'LiveStream', // Stream Type (OnDemand or LiveStream), added as stream_type for media events
		true, // Log Page Event Toggle (true/false)
		true, // Log Media Event Toggle (true/false)
	);

	const sessionStartOptions = {};
	sessionStartOptions.customAttributes = window.beasleyanalytics.getMParticleMediaEventObject(
		window.beasleyanalytics.BeasleyAnalyticsMParticleProvider
			.mparticleEventNames.mediaSessionStart,
	);
	window.mediaSession.logMediaSessionStart(sessionStartOptions);

	const playOptions = {};
	playOptions.customAttributes = window.beasleyanalytics.getMParticleMediaEventObject(
		window.beasleyanalytics.BeasleyAnalyticsMParticleProvider
			.mparticleEventNames.play,
	);
	window.mediaSession.logPlay(playOptions);
}

/**
 * @function watchAudioStart
 * Generator used to bind action and callback
 */
export default function* watchStart() {
	yield takeLatest([ACTION_PLAYER_START], yieldStart);
}
