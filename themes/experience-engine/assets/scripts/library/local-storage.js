const { localStorage } = window;

export function getStorage( namespace ) {
	return {
		getItem( key ) {
			return localStorage.getItem( `${namespace}:${key}` );
		},
		setItem( key, value ) {
			localStorage.setItem( `${namespace}:${key}`, value );
		}
	};
}

export default {
	getStorage,
};
