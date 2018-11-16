export function removeChildren( element ) {
	while ( element && element.firstChild ) {
		element.removeChild( element.firstChild );
	}
}

export function removeElement( element ) {
	element.parentNode.removeChild( element );
}

export function dispatchEvent( type ) {
	let event = false;

	if ( 'function' === typeof( Event ) ) {
		event = new Event( type );
	} else {
		// ie11 compatibility
		event = document.createEvent( 'Event' );
		event.initEvent( type, true, true );
	}

	if ( window.dispatchEvent ) {
		window.dispatchEvent( event );
	} else if ( window.fireEvent ) {
		window.fireEvent( event );
	}
}

function loadScript( src ) {
	return new Promise( ( resolve, reject ) => {
		const id = src.replace( /\W+/g, '' );
		if ( document.getElementById( id ) ) {
			return resolve();
		}

		const element = document.createElement( 'script' );

		element.id = id;
		element.src = src;

		element.onload = () => {
			resolve();
		};

		element.onerror = () => {
			reject();
		};

		document.head.appendChild( element );
	} );
}

function loadStyle( src ) {
	return new Promise( ( resolve, reject ) => {
		const id = src.replace( /\W+/g, '' );
		if ( document.getElementById( id ) ) {
			return resolve();
		}

		const element  = document.createElement( 'link' );

		element.id = id;
		element.rel = 'stylesheet';
		element.type = 'text/css';
		element.href = src;
		element.media = 'all';

		element.onload = () => {
			resolve();
		};

		element.onerror = () => {
			reject();
		};

		document.head.appendChild( element );
	} );
}

export function loadAssets( scripts = [], styles = [] ) {
	return Promise.all( [
		...scripts.map( loadScript ),
		...styles.map( loadStyle ),
	] );
}

export function unloadScripts( scripts = [] ) {
	for ( let i = 0, len = scripts.length; i < len; i++ ) {
		const element = document.getElementById( scripts[i].replace( /\W+/g, '' ) );
		if ( element ) {
			removeElement( element );
		}
	}
}

export default {
	removeChildren,
	removeElement,
	dispatchEvent,
	loadAssets,
	unloadScripts,
};
