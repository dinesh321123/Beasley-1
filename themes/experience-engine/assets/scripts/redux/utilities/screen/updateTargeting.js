export default function updateTargeting() {
	const { googletag } = window;
	if (googletag) {
		googletag.cmd.push(() => {
			const interstitialAdDiv = window.top.document.getElementById(
				'div-gpt-ad-1484200509775-3',
			);

			if (interstitialAdDiv) {
				// Hide Div To So That Lazy Ad Will Not Show
				interstitialAdDiv.style.cssText = 'height:0;overflow:hidden;width:0;';
			}

			const { dfp } = window.bbgiconfig;
			if (dfp && Array.isArray(dfp.global)) {
				for (let i = 0, pairs = dfp.global; i < pairs.length; i++) {
					googletag.pubads().setTargeting(pairs[i][0], pairs[i][1]);
				}
				googletag.pubads().refresh(); // Refresh ALL Slots
			}
		});
	}
}
