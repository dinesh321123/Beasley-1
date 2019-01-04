function __api( strings, ...params ) {
	let url = window.bbgiconfig.eeapi;
	strings.forEach( ( string, i ) => {
		url += string + encodeURIComponent( params[i] || '' );
	} );

	return url;
}

export function saveUser( email, zipcode, gender, dateofbirth, token ) {
	const { publisher } = window.bbgiconfig;
	const { id: channel } = publisher || {};

	const params = {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify( {
			zipcode,
			gender: 'male' === gender ? 'M' : 'F',
			dateofbirth,
			email,
		} ),
	};

	return fetch( __api`user?authorization=${token}`, params )
		.then( () => fetch( __api`experience/channels/${channel}?authorization=${token}`, { method: 'PUT' } ) )
		.catch( ( error ) => {
			console.error( error ); // eslint-disable-line no-console
		} );
}

export function getUser( token ) {
	return fetch( __api`user?authorization=${token}` ).then( response => response.json() );
}

export default {
	saveUser,
	getUser,
};
