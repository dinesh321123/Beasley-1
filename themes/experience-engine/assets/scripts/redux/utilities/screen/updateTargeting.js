/**
 * @function updateTargeting
 */
export default function updateTargeting() {
	let { googletag } = window;

	const { dfp } = window.bbgiconfig;

	if ( dfp && Array.isArray( dfp.global ) ) {
		for ( let i = 0, pairs = dfp.global; i < pairs.length; i++ ) {
			googletag.pubads().setTargeting( pairs[i][0], pairs[i][1] );
		}
	}
}
