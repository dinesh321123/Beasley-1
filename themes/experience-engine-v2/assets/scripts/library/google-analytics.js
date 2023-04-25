export function pageview(title, location, targeting = null) {
	const { beasleyanalytics } = window;
	if (!beasleyanalytics) {
		return;
	}

	if (targeting) {
		beasleyanalytics.setAnalytics(
			'contentGroup1',
			targeting.contentgroup1 || '',
		);
		beasleyanalytics.setAnalytics(
			'contentGroup2',
			targeting.contentgroup2 || '',
		);

		if (targeting.dimensionkey) {
			beasleyanalytics.setAnalytics(
				targeting.dimensionkey,
				targeting.dimensionvalue,
			);
		}
	}

	beasleyanalytics.sendEvent({ hitType: 'pageview', title, location });
}

/**
 * A ga('send') wrapped behind a check in case GA is blocked or absent.
 *
 * @param opts The ga event opts
 */
export function sendToGA(opts) {
	const { beasleyanalytics } = window;

	if (beasleyanalytics) {
		beasleyanalytics.sendEvent(opts);
	}
}

/**
 * Sends a Inline audio playing event to GA
 */
export function sendInlineAudioPlaying() {
	sendToGA({
		hitType: 'event',
		eventCategory: 'audio',
		eventAction: 'Inline audio playing',
	});
}

export default {
	pageview,
};
