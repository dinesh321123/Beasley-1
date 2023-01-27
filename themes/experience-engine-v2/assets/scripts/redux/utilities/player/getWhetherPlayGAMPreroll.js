/**
 * Returns whether it has been Greater than 10 minutes since lastAdPlaybackTime(ie Preroll)
 *
 * @param {Number} nowTime - current Epoch
 * @param {Number} lastAdPlaybackTime - Epoch of last Preroll start
 * @returns {boolean} shouldPlayGAMPreroll - whether we should play a GAM Preroll
 */
export default function getWhetherPlayGAMPreroll(
	nowTime = 0,
	lastAdPlaybackTime = 0,
) {
	const timeSinceLastPreroll = nowTime - lastAdPlaybackTime;
	const shouldPlayGAMPreroll = timeSinceLastPreroll > 10 * 60 * 1000; // Greater than 10 minutes
	console.log(
		`It has been ${timeSinceLastPreroll} milliseconds since last Preroll. ${
			shouldPlayGAMPreroll ? '' : 'NOT '
		}Playing Preroll.`,
	);
	return shouldPlayGAMPreroll;
}
