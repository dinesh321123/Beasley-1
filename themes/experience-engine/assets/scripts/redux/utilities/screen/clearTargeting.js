/**
 * @function clearTargeting
 */
export default function clearTargeting() {
	const { googletag } = window;

	if (googletag && googletag.apiReady) {
		googletag.pubads().clearTargeting();
	}
}
