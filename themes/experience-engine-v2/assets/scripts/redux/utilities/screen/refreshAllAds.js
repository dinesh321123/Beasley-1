import {
	doPubadsRefreshForAllRegisteredAds,
	hidePlaceholder,
	topScrollingDivID,
	logPrebidTargeting,
} from '../../../library/ad-utils';

export default function refreshAllAds() {
	const { prebid_enabled } = window.bbgiconfig;

	// Trying to keep top ad visible - no longer hide
	window.topAdsShown = 0; // Reset Header Ad Counter
	hidePlaceholder(topScrollingDivID);

	if (!prebid_enabled) {
		const { googletag } = window;
		googletag.cmd.push(() => {
			doPubadsRefreshForAllRegisteredAds(googletag);
		});
		return; // EXIT FUNCTION
	}

	const pbjs = window.pbjs || {};
	pbjs.que = pbjs.que || [];

	pbjs.que.push(() => {
		const PREBID_TIMEOUT = 1500;
		// pbjs.addAdUnits(adUnits);
		pbjs.requestBids({
			bidsBackHandler: initAdserver,
			timeout: PREBID_TIMEOUT,
		});
	});

	function initAdserver() {
		const { googletag } = window;
		googletag.cmd.push(() => {
			pbjs.que.push(() => {
				pbjs.setTargetingForGPTAsync();
				logPrebidTargeting();
				doPubadsRefreshForAllRegisteredAds(googletag);
			});
		});
	}
}
