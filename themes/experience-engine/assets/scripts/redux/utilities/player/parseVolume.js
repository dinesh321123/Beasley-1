/**
 * Returns a parsed number from 0 to 100
 *
 * @param {Number} value - default 50
 * @returns {Number} volume
 */
export default function parseVolume(value = 50) {
	let volume = parseInt(value, 10);
	if (Number.isNaN(volume) || volume > 100) {
		volume = 100;
	} else if (volume < 0) {
		volume = 0;
	}
	return volume;
}
