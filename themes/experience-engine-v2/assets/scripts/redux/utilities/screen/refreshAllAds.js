import {
	doPubadsRefreshForAllRegisteredAds,
	hidePlaceholder,
	topScrollingDivID,
} from '../../../library/ad-utils';

export default function refreshAllAds() {
	console.log('NOT POLLING PREBID ON PAGE LOAD');
	window.topAdsShown = 0; // Reset Header Ad Counter
	hidePlaceholder(topScrollingDivID);

	const { googletag } = window;
	googletag.cmd.push(() => {
		// googletag.pubads().refresh();
		doPubadsRefreshForAllRegisteredAds(googletag);
	});
}
