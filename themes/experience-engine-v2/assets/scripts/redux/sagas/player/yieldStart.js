import { call, put, takeLatest, select } from 'redux-saga/effects';
import mParticle from '@mparticle/web-sdk';
import MediaSession from '@mparticle/web-media-sdk';
import { sendInlineAudioPlaying } from '../../../library/google-analytics';
import { ACTION_PLAYER_START } from '../../actions/player';
import { showSignInModal } from '../../actions/modal';

function sendMParticleMediaEvents(playerStore) {
	const { playerType, streams } = playerStore;

	const streamParams = {
		mediaID: window.createUUID(),
		contentType: 'Audio',
		pageEventToggle: '',
		mediaEventToggle: '',
	};

	const isLiveStream = playerType === 'tdplayer';
	if (isLiveStream) {
		streamParams.streamType = 'Live'; // OnDemand, Live, Linear, Podcast, Audiobook
		streamParams.duration = 1000 * 60 * 60 * 24; // Default to 1 day
		streamParams.mediaTitle =
			streams && streams.length > 0 ? streams[0].title : '';
		streamParams.content_asset_id =
			streams && streams.length > 0 ? streams[0].stream_tap_id : '';
		streamParams.content_network =
			streams && streams.length > 0 ? streams[0].stream_cmod_domain : '';
		streamParams.call_sign =
			streams && streams.length > 0 ? streams[0].stream_call_letters : '';
		streamParams.call_sign_id =
			streams && streams.length > 0 ? streams[0].stream_mount_key : '';
		streamParams.primary_category = 'LiveStreamCategory?';
		streamParams.primary_category_id = 'LiveStreamCategoryID?';
		streamParams.show_name = 'LiveStreamShowName?';
		streamParams.show_id = 'LiveStreamShowID?';
		streamParams.content_daypart = 'LiveStreamContentDayPart?';
	} else {
		streamParams.streamType = 'Podcast';
		streamParams.duration = 1000 * 60 * 60 * 24; // Default to 1 day
		streamParams.mediaTitle = 'PodcastTitle?';
		streamParams.content_asset_id = 'PodcastTitleAssetID?';
		streamParams.content_network = 'PodcastNetwork?';
		streamParams.call_sign = 'PodcastCallSign?';
		streamParams.call_sign_id = 'PodcastCallSignID?';
		streamParams.primary_category = 'PodcastCategory?';
		streamParams.primary_category_id = 'PodcastCategoryID?';
		streamParams.show_name = 'PodcastShowName?';
		streamParams.show_id = 'PodcastShowID?';
		streamParams.content_daypart = 'PodcastContentDayPart?';
	}

	// Load Up Beasley Analytics With All Media Params
	Object.keys(streamParams).forEach(key => {
		window.beasleyanalytics.setMediaAnalyticsForMParticle(
			key,
			streamParams[key],
		);
	});

	window.mediaSession = new MediaSession(
		mParticle, // mParticle SDK Instance
		streamParams.mediaID, // Custom media ID, added as content_id for media events
		streamParams.mediaTitle, // Custom media Title, added as content_title for media events
		streamParams.duration, // Duration in milliseconds, added as content_duration for media events
		streamParams.contentType, // Content Type (Video or Audio), added as content_type for media events
		streamParams.streamType, // Stream Type (OnDemand or LiveStream), added as stream_type for media events
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

	sendMParticleMediaEvents(playerStore);
}

/**
 * @function watchAudioStart
 * Generator used to bind action and callback
 */
export default function* watchStart() {
	yield takeLatest([ACTION_PLAYER_START], yieldStart);
}
