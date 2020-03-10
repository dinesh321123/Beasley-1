/**
 * @function manageBbgiConfig
 *
 * @param {*} pageDocument
 */
export default function manageBbgiConfig( pageDocument ) {
	let newconfig = {};

	try {
		newconfig = JSON.parse( pageDocument.getElementById( 'bbgiconfig' ).innerHTML );
	} catch ( err ) {
		// do nothing
	}

	window.bbgiconfig = newconfig;
}
