/* eslint-disable sort-keys */
import { removeChildren, dispatchEvent } from '../../library/dom';
import { getStateFromContent, parseHtml } from '../../library/html-parser';
import { pageview } from '../../library/google-analytics';
import slugify from '../../library/slugify';
import { isIE11 } from '../../library/browser';
import { trailingslashit } from '../../library/strings';
/**
 * We use this approach to minify action names in the production bundle and have
 * human friendly actions in the dev bundle. Use "s{x}" format to create new actions.
 */
export const ACTION_INIT_PAGE =
	'production' === process.env.NODE_ENV ? 's0' : 'PAGE_INIT';
export const ACTION_LOADING_PAGE =
	'production' === process.env.NODE_ENV ? 's1' : 'PAGE_LOADING';
export const ACTION_LOADED_PAGE =
	'production' === process.env.NODE_ENV ? 's2' : 'PAGE_LOADED';
export const ACTION_LOADING_PARTIAL =
	'production' === process.env.NODE_ENV ? 's3' : 'PARTIAL_LOADING';
export const ACTION_LOADED_PARTIAL =
	'production' === process.env.NODE_ENV ? 's4' : 'PARTIAL_LOADED';
export const ACTION_LOAD_ERROR =
	'production' === process.env.NODE_ENV ? 's5' : 'LOADING_ERROR';
export const ACTION_HIDE_SPLASH_SCREEN =
	'production' === process.env.NODE_ENV ? 's6' : 'HIDE_SPLASH_SCREEN';
export const ACTION_UPDATE_NOTICE =
	'production' === process.env.NODE_ENV ? 's7' : 'UPDATE_NOTICE ';
export const ACTION_HISTORY_HTML_SNAPSHOT =
	'production' === process.env.NODE_ENV ? 's10' : 'HISTORY_HTML_SNAPSHOT';

export function initPage() {
	const content = document.getElementById( 'content' );
	const parsed = getStateFromContent( content );
	// clean up content block for now, it will be poplated in the render function
	removeChildren( content );

	return { type: ACTION_INIT_PAGE, ...parsed };
}

export function initPageLoaded( uuid, data ) {
	return { type: ACTION_HISTORY_HTML_SNAPSHOT, uuid, data };
}

export function loadPage( url, options = {} ) {
	const urlSlugified = slugify( url );
	return dispatch => {
		const { history, location, pageXOffset, pageYOffset } = window;
		let redirecting = false;

		dispatch( { type: ACTION_LOADING_PAGE, url } );

		function onError( error ) {
			// eslint-disable-next-line no-console
			dispatch( { type: ACTION_LOAD_ERROR, error } );
		}

		function onSuccess( data ) {
			if ( ! redirecting ) {
				const parsed = parseHtml( data );
				const pageDocument = parsed.document;

				dispatch( {
					type: ACTION_LOADED_PAGE,
					url,
					...parsed,
					isHome: pageDocument.body.classList.contains( 'home' ),
				} );

				if ( !options.suppressHistory ) {
					history.replaceState(
						{ ...history.state, pageXOffset, pageYOffset },
						document.title,
						location.href,
					);
					history.pushState(
						{ uuid: urlSlugified, pageXOffset: 0, pageYOffset: 0 },
						pageDocument.title,
						url,
					);
					dispatch( {
						type: ACTION_HISTORY_HTML_SNAPSHOT,
						uuid: urlSlugified,
						data,
					} );

					dispatchEvent( 'pushstate' );
					pageview( pageDocument.title, window.location.href );

					document.title = pageDocument.title;
					document.body.className = pageDocument.body.className;
				}

				// Get content container
				const content = document.getElementById( 'content' );

				// Scroll to top of content
				if( content ) {
					content.scrollIntoView( true );
				}
			}

		}

		/**
		 * If the fetch response is anything different than basic (very likely a opaqueredirect)
		 * we force a full page refresh. 'basic' response type is the only request we can safely use to proceed
		 * with our load page logic.
		 *
		 * @param {*} response
		 */
		const maybeRedirect = ( response ) => {
			if ( isIE11() ) {
				return response;
			}

			if ( 'basic' !== response.type ) {
				window.location.href = response.url;
				redirecting = true;
			}
			return response;
		};

		/**
		 * Given external redirects were not properly implemented within the hybrid theme approach. (see https://tenup.teamwork.com/#/tasks/18645110).
		 * A little hack was implemented to get them working. We do a fetch request with 'redirect: "manual"' which
		 * means fetch will not follow the redirect, if present. Then we check if the response was indeed a redirect
		 * (opaqueredirect, or to be more generic anything different than basic).
		 * In that case we simply force a full page refresh to let the server properly handle redirects.
		 */
		fetch( trailingslashit( options.fetchUrlOverride || url ), options.fetchParams || {
			redirect: isIE11() ? 'follow' : 'manual', // IE11 does not support this work around.
		} )
			.then( maybeRedirect )
			.then( response => response.text() )
			.then( onSuccess )
			.catch( onError );
	};
}

export function updatePage( data ) {
	const parsed = parseHtml( data );
	const pageDocument = parsed.document;

	document.body.className = pageDocument.body.className;

	return {
		type: ACTION_LOADED_PAGE,
		force: true,
		...parsed,
	};
}

export function loadPartialPage( url, placeholder ) {
	return dispatch => {
		dispatch( { type: ACTION_LOADING_PARTIAL, url } );

		function onError( error ) {
			// eslint-disable-next-line no-console
			console.error( error );
			dispatch( { type: ACTION_LOAD_ERROR, error } );
		}

		function onSuccess( data ) {
			const parsed = parseHtml( data, '#inner-content' );
			dispatch( { type: ACTION_LOADED_PARTIAL, url, ...parsed, placeholder } );
			pageview( parsed.document.title, url );
		}

		fetch( url )
			.then( response => response.text() )
			.then( onSuccess )
			.catch( onError );
	};
}

export function hideSplashScreen() {
	return { type: ACTION_HIDE_SPLASH_SCREEN };
}

export function updateNotice( { isOpen, message } ) {
	return {
		type: ACTION_UPDATE_NOTICE,
		force: true,
		isOpen,
		message,
	};
}

export default {
	hideSplashScreen,
	initPage,
	initPageLoaded,
	loadPage,
	loadPartialPage,
	updateNotice,
};
