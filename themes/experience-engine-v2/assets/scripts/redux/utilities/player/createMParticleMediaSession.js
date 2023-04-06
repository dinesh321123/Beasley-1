import MediaSession from '@mparticle/web-media-sdk';
import mParticle from '@mparticle/web-sdk';

export default function createMParticleMediaSession(playerType, stream) {
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
		streamParams.mediaTitle = stream?.title || '';
		streamParams.content_asset_id = stream?.stream_tap_id || '';
		streamParams.content_network = stream?.stream_cmod_domain || '';
		streamParams.call_sign = stream?.stream_call_letters || '';
		streamParams.call_sign_id = stream?.stream_mount_key || '';
		streamParams.primary_category =
			window.bbgiconfig?.publisher?.genre?.toString() || '';
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
