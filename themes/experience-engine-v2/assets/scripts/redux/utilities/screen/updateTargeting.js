import updateInterstitialAdDiv from './updateInterstitialAdDiv';
import refreshAllAds from './refreshAllAds';

export default function updateTargeting() {
	const { googletag } = window;
	if (googletag) {
		googletag.cmd.push(() => {
			const { dfp } = window.bbgiconfig;
			if (dfp && Array.isArray(dfp.global)) {
				updateInterstitialAdDiv();

				for (let i = 0, pairs = dfp.global; i < pairs.length; i++) {
					googletag.pubads().setTargeting(pairs[i][0], pairs[i][1]);
				}
				refreshAllAds();
			}
		});
	}
}
