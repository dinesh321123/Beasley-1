export function saveUser( email, zipcode, gender, dateofbirth, token ) {
	const url = `${window.bbgiconfig.eeapi}user?authorization=${encodeURIComponent( token )}`;
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

	return fetch( url, params ).catch( ( error ) => {
		console.error( error ); // eslint-disable-line no-console
	} );
}

export default {
	saveUser,
};
