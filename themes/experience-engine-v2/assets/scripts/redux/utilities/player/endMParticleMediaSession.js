export default function endMParticleMediaSession() {
	if (!window.mediaSession?.isStopped) {
		window.mediaSession.isStopped = true;
		const contentEndOptions = {};
		contentEndOptions.customAttributes = window.beasleyanalytics.getMParticleMediaEventObject(
			window.beasleyanalytics.BeasleyAnalyticsMParticleProvider
				.mparticleEventNames.mediaContentEnd,
		);
		window.mediaSession.logMediaContentEnd(contentEndOptions);

		const sessionEndOptions = {};
		sessionEndOptions.customAttributes = window.beasleyanalytics.getMParticleMediaEventObject(
			window.beasleyanalytics.BeasleyAnalyticsMParticleProvider
				.mparticleEventNames.mediaSessionEnd,
		);
		window.mediaSession.logMediaSessionEnd(sessionEndOptions);
	}
}
