export default function updateInterstitialAdDiv() {
	const { ad_lazy_loading_enabled } = window;

	if (ad_lazy_loading_enabled) {
		const interstitialAdDiv = window.top.document.getElementById(
			'div-gpt-ad-1484200509775-3',
		);

		if (interstitialAdDiv) {
			const nonScriptInterstitialChildren = interstitialAdDiv.querySelectorAll(
				':not(script)',
			);

			// If Interstitial Div Is Empty, Open Interstial Div Full Screen So That Lazy Load Can Occur
			if (nonScriptInterstitialChildren.length === 0) {
				interstitialAdDiv.style.cssText =
					'bottom: 0; height: 100%; left: 0; position: fixed; right: 0; top: 0; width: 100%; z-index: 9000003;';
			}
		}
	}
}
