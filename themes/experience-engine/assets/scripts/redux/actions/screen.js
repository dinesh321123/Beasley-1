import { removeChildren, dispatchEvent } from '../../library/dom';
import { getStateFromContent, parseHtml } from '../../library/html-parser';
import { pageview } from '../../library/google-analytics';

/**
 * We use this approach to minify action names in the production bundle and have
 * human friendly actions in the dev bundle. Use "s{x}" format to create new actions.
 */
export const ACTION_INIT_PAGE       = 'production' === process.env.NODE_ENV ? 's0' : 'PAGE_INIT';
export const ACTION_LOADING_PAGE    = 'production' === process.env.NODE_ENV ? 's1' : 'PAGE_LOADING';
export const ACTION_LOADED_PAGE     = 'production' === process.env.NODE_ENV ? 's2' : 'PAGE_LOADED';
export const ACTION_LOADING_PARTIAL = 'production' === process.env.NODE_ENV ? 's3' : 'PARTIAL_LOADING';
export const ACTION_LOADED_PARTIAL  = 'production' === process.env.NODE_ENV ? 's4' : 'PARTIAL_LOADED';
export const ACTION_LOAD_ERROR      = 'production' === process.env.NODE_ENV ? 's5' : 'LOADING_ERROR';

export function initPage() {
	const content = document.getElementById( 'content' );
	const parsed = getStateFromContent( content );

	// clean up content block for now, it will be poplated in the render function
	removeChildren( content );

	return { type: ACTION_INIT_PAGE, ...parsed };
}

export function loadPage( url, options = {} ) {
	return ( dispatch ) => {
		const { history, location, pageXOffset, pageYOffset } = window;

		dispatch( { type: ACTION_LOADING_PAGE, url } );

		function onError( error ) {
			console.error( error ); // eslint-disable-line no-console
			dispatch( { type: ACTION_LOAD_ERROR, error } );
		}

		function onSuccess( data ) {
			const parsed = parseHtml( data );
			const pageDocument = parsed.document;

			if ( !options.suppressHistory ) {
				history.replaceState( { ...history.state, pageXOffset, pageYOffset }, document.title, location.href );
				history.pushState( { data, pageXOffset: 0, pageYOffset: 0 }, pageDocument.title, url );

				dispatchEvent( 'pushstate' );
				pageview( pageDocument.title, window.location.href );

				document.title = pageDocument.title;
				document.body.className = pageDocument.body.className;
			}

			dispatch( { type: ACTION_LOADED_PAGE, ...parsed } );
			window.scrollTo( 0, 0 );
		}

		fetch( options.fetchUrlOverride || url, options.fetchParams || {} )
			.then( response => response.text() )
			.then( onSuccess )
			.catch( onError );
	};
}

export function updatePage( data ) {
	const parsed = parseHtml( data );
	const pageDocument = parsed.document;

	document.body.className = pageDocument.body.className;

	return { type: ACTION_LOADED_PAGE, ...parsed };
}

export function loadPartialPage( url, placeholder ) {
	return ( dispatch ) => {
		dispatch( { type: ACTION_LOADING_PARTIAL, url } );

		function onError( error ) {
			console.error( error ); // eslint-disable-line no-console
			dispatch( { type: ACTION_LOAD_ERROR, error } );
		}

		function onSuccess( data ) {
			const parsed = parseHtml( data, '#inner-content' );
			dispatch( { type: ACTION_LOADED_PARTIAL, ...parsed, placeholder } );
			pageview( parsed.document.title, url );
		}

		fetch( url )
			.then( response => response.text() )
			.then( onSuccess )
			.catch( onError );
	};
}

export default {
	initPage,
	loadPage,
	updatePage,
	loadPartialPage,
};
