export function untrailingslashit(url) {
	let newurl = url;
	while (newurl.length && newurl[newurl.length - 1] === '/') {
		newurl = newurl.substring(0, newurl.length - 1);
	}

	return newurl;
}

export function trailingslashit(url) {
	return `${untrailingslashit(url)}/`;
}
/**
 * Checks if a URL is absolute or not.
 *
 * @param {string} url
 */
export function isAbsoluteUrl(url) {
	if (typeof url !== 'string') {
		throw new TypeError(`Expected a \`string\`, got \`${typeof url}\``);
	}

	// Don't match Windows paths `c:\`
	if (/^[a-zA-Z]:\\/.test(url)) {
		return false;
	}

	// Scheme: https://tools.ietf.org/html/rfc3986#section-3.1
	// Absolute URL: https://tools.ietf.org/html/rfc3986#section-4.3
	return /^[a-zA-Z][a-zA-Z\d+\-.]*:/.test(url);
}

/**
 * @function isAudioOnly
 * Detects if ad is an audio only advert
 *
 * @param {Object} playerStore
 * @param {Object} playerStore.player - Full player reference
 * @param {string} playerStore.playerType - Player Type
 * @returns {Boolean}
 */
export function isAudioAdOnly({ player, playerType }) {
	let currentAdModule = null;

	// If not tdplayer, abandon
	if (playerType !== 'tdplayer') {
		return false;
	}

	if (player && player.MediaPlayer && player.MediaPlayer.adManager) {
		currentAdModule = player.MediaPlayer.adManager.currentAdModule;
	}

	// Look for ad, return whether NOT an MP4.
	// eslint-disable-next-line no-prototype-builtins
	if (currentAdModule && currentAdModule.hasOwnProperty('html5Node')) {
		const regEx = new RegExp(/\.mp4$/);
		const adUrl = currentAdModule.html5Node
			? currentAdModule.html5Node.currentSrc || false
			: false;

		console.log(`TRITON PREROLL URL: ${adUrl}`);
		const retval = adUrl ? !regEx.test(adUrl) : true; // ONLY RETURN FALSE WHEN WE KNOW IT IS VIDEO
		console.log(`DID ${retval ? '' : 'NOT '}DETECT AUDIO ONLY PREROLL`);
		return retval;
	}

	return true;
}

export default {
	untrailingslashit,
	trailingslashit,
	isAudioAdOnly,
};
