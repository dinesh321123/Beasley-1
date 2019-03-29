import firebase from 'firebase';

function getChannel() {
	const { publisher } = window.bbgiconfig;
	const { id: channel } = publisher || {};

	return channel || '';
}

function getToken( token = null ) {
	if ( token ) {
		return Promise.resolve( token );
	}

	const auth = firebase.auth();
	if ( !auth.currentUser ) {
		return Promise.reject();
	}

	return auth.currentUser.getIdToken().catch( error => console.error( error ) ); // eslint-disable-line no-console
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

export function getFeeds( jwt = null ) {
	const channel = getChannel();

	return getToken( jwt )
		.then( token => fetch( __api`experience/channels/${channel}/feeds/content/?authorization=${token}` ) )
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
	return fetch( __api`experience/channels/${getChannel()}/keywords/${keyword}/` )
		.then( response => response.json() );
}


export function validateDate( dateString ) {
	// First check for the pattern
	if( !/^\d{1,2}\/\d{1,2}\/\d{4}$/.test( dateString ) )
		return false;

	// Parse the date parts to integers
	const parts = dateString.split( '/' );
	const year = parseInt( parts[2], 10 );
	const month = parseInt( parts[0], 10 );
	const day = parseInt( parts[1], 10 );

	// Check the ranges of month and year
	if( 1000 > year || 3000 < year || 0 == month || 12 < month )
		return false;

	const monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

	// Adjust for leap years
	if( 0 == year % 400 || ( 0 != year % 100 && 0 == year % 4 ) )
		monthLength[1] = 29;

	// Check the range of the day
	return 0 < day && day <= monthLength[month - 1];
}

export default {
	saveUser,
	getUser,
	discovery,
	getFeeds,
	modifyFeeds,
	deleteFeed,
	searchKeywords,
	validateDate,
};
