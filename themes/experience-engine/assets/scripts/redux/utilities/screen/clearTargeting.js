/**
 * @function clearTargeting
 */
export default function clearTargeting() {
	let googletag = window.googletag;

	if ( googletag && googletag.apiReady ) {
		googletag.pubads().clearTargeting();
	}
}
