export default function refreshAllAds() {
	const { prebid_enabled } = window.bbgiconfig;

	if (!prebid_enabled) {
		const { googletag } = window;
		googletag.cmd.push(() => {
			googletag.pubads().refresh();
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
				logPrebidTargeting(pbjs);
				googletag.pubads().refresh();
			});
		});
	}
}

export function logPrebidTargeting(pbjsInstance, unitId) {
	const targeting = pbjsInstance.getAdserverTargeting();
	let retval;

	if (targeting) {
		Object.keys(targeting).map(tkey => {
			if (targeting[tkey].hb_bidder && (!unitId || unitId === tkey)) {
				console.log(
					`High Prebid Ad ID: ${tkey} Bidder: ${targeting[tkey].hb_bidder} Price: ${targeting[tkey].hb_pb}`,
				);

				try {
					window.ga('send', {
						hitType: 'event',
						eventCategory: 'PrebidTarget',
						eventAction: `${targeting[tkey].hb_bidder}`,
						eventLabel: `${tkey}`,
						eventValue: `${parseInt(
							parseFloat(targeting[tkey].hb_pb) * 100,
							10,
						)}`,
					});
				} catch (ex) {
					console.log(`ERROR Sending to Google Analytics: `, ex);
				}

				// Set retval when UnitID was specified and we have a high bidder
				if (unitId && unitId === tkey) {
					retval = targeting[tkey];
				}
			}
			return tkey;
		});
	}

	return retval;
}
