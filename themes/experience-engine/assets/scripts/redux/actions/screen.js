import { removeChildren, dispatchEvent } from '../../library/dom';
import { getStateFromContent, parseHtml } from '../../library/html-parser';

/**
 * We use this approach to minify action names in the production bundle and have
 * human friendly actions in the dev bundle. Use "s{x}" format to create new actions.
 */

export const ACTION_INIT_PAGE       = 'production' === process.env.NODE_ENV ? 's0' : 'PAGE_INIT';
export const ACTION_LOADING_PAGE    = 'production' === process.env.NODE_ENV ? 's1' : 'PAGE_LOADING';
export const ACTION_LOADED_PAGE     = 'production' === process.env.NODE_ENV ? 's2' : 'PAGE_LOADED';
export const ACTION_LOADING_PARTIAL = 'production' === process.env.NODE_ENV ? 's3' : 'PARTIAL_LOADING';
export const ACTION_LOADED_PARTIAL  = 'production' === process.env.NODE_ENV ? 's4' : 'PARTIAL_LOADED';

export const initPage = () => {
	const content = document.getElementById( 'content' );
	const results = getStateFromContent( content );

	// clean up content block for now, it will be poplated in the render function
	removeChildren( content );

	return {
		type: ACTION_INIT_PAGE,
		content: results.content,
		embeds: results.embeds,
	};
};

export const loadPage = ( url ) => ( dispatch ) => {
	const { history, location, pageXOffset, pageYOffset } = window;

	dispatch( {
		type: ACTION_LOADING_PAGE,
		url,
	} );

	const onError =  ( error ) => {
		console.error( error ); // eslint-disable-line no-console

		dispatch( {
			type: ACTION_LOADED_PAGE,
			content: '',
			embeds: [],
			error,
		} );
	};

	const onSuccess = ( data ) => {
		const results = parseHtml( data );
		const pageDocument = results.document;

		const currentState = Object.assign( {}, history.state, {
			pageXOffset,
			pageYOffset,
		} );

		const newState = {
			data,
			pageXOffset: 0,
			pageYOffset: 0,
		};

		history.replaceState( currentState, document.title, location.href );
		history.pushState( newState, pageDocument.title, url );

		dispatchEvent( 'pushstate' );

		document.title = pageDocument.title;
		document.body.className = pageDocument.body.className;

		dispatch( {
			type: ACTION_LOADED_PAGE,
			content: results.content,
			embeds: results.embeds,
			error: '',
			document: pageDocument,
		} );

		window.scrollTo( 0, 0 );
	};

	fetch( url )
		.then( response => response.text() )
		.then( onSuccess )
		.catch( onError );
};

export const updatePage = ( data ) => {
	const results = parseHtml( data );

	const pageDocument = results.document;
	document.body.className = pageDocument.body.className;

	return {
		type: ACTION_LOADED_PAGE,
		content: results.content,
		embeds: results.embeds,
		error: '',
		document: pageDocument,
	};
};

export const loadPartialPage = ( url, placeholder ) => ( dispatch ) => {
	dispatch( {
		type: ACTION_LOADING_PARTIAL,
		url,
	} );

	const onError =  ( error ) => {
		console.error( error ); // eslint-disable-line no-console

		dispatch( {
			type: ACTION_LOADED_PARTIAL,
			content: '',
			embeds: [],
			error,
		} );
	};

	const onSuccess = ( data ) => {
		const results = parseHtml( data );

		dispatch( {
			type: ACTION_LOADED_PARTIAL,
			content: results.content,
			embeds: results.embeds,
			error: '',
			placeholder,
		} );
	};

	fetch( url )
		.then( response => response.text() )
		.then( onSuccess )
		.catch( onError );
};

export default {
	initPage,
	loadPage,
	updatePage,
	loadPartialPage,
};
