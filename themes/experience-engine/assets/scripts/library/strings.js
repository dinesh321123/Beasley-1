export function untrailingslashit( url ) {
	let newurl = url;
	while ( newurl.length && '/' === newurl[newurl.length - 1] ) {
		newurl = newurl.substring( 0, newurl.length - 1 );
	}

	return newurl;
}

export function trailingslashit( url ) {
	return untrailingslashit( url ) + '/';
}

export function isAudioAdOnly() {
	const { currentAdModule } = window.tdplayer.MediaPlayer.adManager || false;

	// Look for ad, if MP3, don't display it.
	if ( currentAdModule && typeof currentAdModule.html5Node !== 'undefined' ) {
		const regEx = new RegExp( /\.mp3$/ );
		let adUrl = currentAdModule.html5Node.currentSrc || false;

		return regEx.test( adUrl );
	}

	return false;
}

export default {
	untrailingslashit,
	trailingslashit,
	isAudioAdOnly,
};
