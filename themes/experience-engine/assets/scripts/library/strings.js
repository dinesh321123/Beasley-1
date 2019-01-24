export function untrailingslashit( url ) {
	let newurl = url;
	while ( newurl.length && '/' === newurl[newurl.length - 1] ) {
		newurl = newurl.substring( 0, newurl.length - 1 );
	}

	return newurl;
}

export default {
	untrailingslashit,
};
