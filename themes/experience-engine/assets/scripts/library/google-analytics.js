export function pageview(title, location, targeting = null) {
	const { ga } = window;
	if (!ga) {
		return;
	}

	if (targeting) {
		ga('set', 'contentGroup1', targeting.contentgroup1 || '');
		ga('set', 'contentGroup2', targeting.contentgroup2 || '');

		if (targeting.dimensionkey) {
			ga('set', targeting.dimensionkey, targeting.dimensionvalue);
		}
	}

	ga('send', { hitType: 'pageview', title, location });
}

/**
 * A ga('send') wrapped behind a check in case GA is blocked or absent.
 *
 * @param opts The ga event opts
 */
export function sendToGA(opts) {
	if (location.search.indexOf('gadebug=1') !== -1) {
		window.console.log('sendToGA', opts);
	}

	const { ga } = window;

	if (ga) {
		ga('send', opts);
	}
}

/**
 * Sends a Live stream playing event to GA
 */
export function sendLiveStreamPlaying() {
	sendToGA({
		hitType: 'event',
		eventCategory: 'audio',
		eventAction: 'Live stream playing',
	});
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
