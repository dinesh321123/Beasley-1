import firebase from 'firebase';

function getChannel() {
	const { publisher } = window.bbgiconfig;
	const { id: channel } = publisher || {};

	return channel || '';
}

function getToken() {
	return firebase
		.auth()
		.currentUser
		.getIdToken()
		.catch( error => console.error( error ) ); // eslint-disable-line no-console
}

function __api( strings, ...params ) {
	let url = window.bbgiconfig.eeapi;
	strings.forEach( ( string, i ) => {
		url += string + encodeURIComponent( params[i] || '' );
	} );

	return url;
}

export function saveUser( email, zipcode, gender, dateofbirth ) {
	const channel = getChannel();
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

	return getToken().then( ( token ) => {
		return fetch( __api`user?authorization=${token}`, params )
			.then( () => fetch( __api`experience/channels/${channel}?authorization=${token}`, { method: 'PUT' } ) );
	} );
}

export function getUser() {
	return getToken()
		.then( token => fetch( __api`user?authorization=${token}` ) )
		.then( response => response.json() );
}

export function discovery( filters ) {
	const channel = getChannel();
	const { keyword, type, location, genre, brand } = filters;

	return getToken()
		.then( token => fetch( __api`discovery/?media_type=${type}&genre=${genre}&location=${location}&brand=${brand}&keyword=${keyword}&channel=${channel}&authorization=${token}` ) );
}

export function getFeeds() {
	const channel = getChannel();

	return getToken()
		.then( token => fetch( __api`experience/channels/${channel}/feeds/?authorization=${token}` ) )
		.then( response => response.json() );
}

export function modifyFeeds( feeds ) {
	const channel = getChannel();
	const params = {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify( feeds ),
	};

	return getToken()
		.then( token => fetch( __api`experience/channels/${channel}/feeds/?authorization=${token}`, params ) );
}

export function deleteFeed( feedId ) {
	const channel = getChannel();
	const params = { method: 'DELETE' };

	return getToken()
		.then( token => fetch( __api`experience/channels/${channel}/feeds/${feedId}/?authorization=${token}`, params ) );
}

export function searchKeywords( keyword ) {
	return fetch( __api`experience/channels/${getChannel()}/keywords/${keyword}` )
		.then( response => response.json() );
}

export default {
	saveUser,
	getUser,
	discovery,
	getFeeds,
	modifyFeeds,
	deleteFeed,
	searchKeywords,
};
