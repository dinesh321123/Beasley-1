export function pageview( title, location ) {
	const { ga } = window;
	if ( ga ) {
		ga( 'send', { hitType: 'pageview', title, location } );
	}
}

export default {
	pageview,
};
