export const removeChildren = ( element ) => {
	while ( element && element.firstChild ) {
		element.removeChild( element.firstChild );
	}
};

export const dispatchEvent = ( type ) => {
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
};

const loadScript = ( src ) => new Promise( ( resolve, reject ) => {
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

const loadStyle = ( src ) => new Promise( ( resolve, reject ) => {
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

export const loadAssets = ( scripts = [], styles = [] ) => {
	return Promise.all( [
		...scripts.map( loadScript ),
		...styles.map( loadStyle ),
	] );
};

export default {
	removeChildren,
	dispatchEvent,
	loadAssets,
};
