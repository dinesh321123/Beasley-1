export default function updateInterstitialAdDiv() {
	const { ad_lazy_loading_enabled } = window;

	if (ad_lazy_loading_enabled) {
		const interstitialAdDiv = window.top.document.getElementById(
			'div-gpt-ad-1484200509775-3',
		);

		if (interstitialAdDiv) {
			const copyOfInterstitialStyle = `${interstitialAdDiv.style.cssText}`;

			// Open Div To Full Screen So That Lazy Ad Can Show
			interstitialAdDiv.style.cssText =
				'bottom: 0; height: 100%; left: 0; position: fixed; right: 0; top: 0; width: 100%; z-index: 9000003;';

			// Put in watchdog to revert style should Google not create children in 3.5 seconds
			setTimeout(() => {
				console.log('Checking whether Interstital has content.');
				if (interstitialAdDiv.children.length === 0) {
					console.log(
						`Reverting Interstital style to ${copyOfInterstitialStyle}`,
					);
					interstitialAdDiv.style.cssText = copyOfInterstitialStyle;
				}
			}, 3500);
		}
	}
}
