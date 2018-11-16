const { localStorage } = window;

export const getStorage = ( namespace ) => ( {
	getItem( key ) {
		return localStorage.getItem( `${namespace}:${key}` );
	},
	setItem( key, value ) {
		localStorage.setItem( `${namespace}:${key}`, value );
	}
} );

export default {
	getStorage,
};
