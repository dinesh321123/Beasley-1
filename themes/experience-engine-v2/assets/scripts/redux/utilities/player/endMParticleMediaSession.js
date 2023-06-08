export default function endMParticleMediaSession() {
	if (window.mediaSession && !window.mediaSession?.isStopped) {
		window.mediaSession.isStopped = true;

		window.beasleyanalytics.setMParticlePerEventKeys();
		const contentEndOptions = {};
		contentEndOptions.customAttributes = window.beasleyanalytics.getMParticleMediaEventObject(
			window.beasleyanalytics.BeasleyAnalyticsMParticleProvider
				.mparticleEventNames.mediaContentEnd,
		);
		window.mediaSession.logMediaContentEnd(contentEndOptions);

		window.beasleyanalytics.setMParticlePerEventKeys();
		const sessionEndOptions = {};
		sessionEndOptions.customAttributes = window.beasleyanalytics.getMParticleMediaEventObject(
			window.beasleyanalytics.BeasleyAnalyticsMParticleProvider
				.mparticleEventNames.mediaSessionEnd,
		);
		window.mediaSession.logMediaSessionEnd(sessionEndOptions);
	}
}
