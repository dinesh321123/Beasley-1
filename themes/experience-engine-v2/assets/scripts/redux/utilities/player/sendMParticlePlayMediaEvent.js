import MediaSession from '@mparticle/web-media-sdk';
import mParticle from '@mparticle/web-sdk';

export default function sendMParticlePlayMediaEvent() {
	const streamParams = window.beasleyanalytics.getMParticleAllMediaFields();
	if (!streamParams) {
		return;
	}

	window.mediaSession = new MediaSession(
		mParticle, // mParticle SDK Instance
		streamParams.content_id, // Custom media ID, added as content_id for media events
		streamParams.content_title, // Custom media Title, added as content_title for media events
		streamParams.content_duration, // Duration in milliseconds, added as content_duration for media events
		streamParams.content_type, // Content Type (Video or Audio), added as content_type for media events
		streamParams.stream_type, // Stream Type (OnDemand or LiveStream), added as stream_type for media events
		true, // Log Page Event Toggle (true/false)
		true, // Log Media Event Toggle (true/false)
	);
	window.mediaSession.isStopped = false;

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
