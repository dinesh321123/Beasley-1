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

	return getToken().then( token => {
		return fetch( __api`user?authorization=${token}`, params ).then( () =>
			fetch( __api`experience/channels/${channel}?authorization=${token}`, {
				method: 'PUT',
			} ),
		);
	} );
}

/**
 * Checks if User has previously saved Profile information. Returns a
 * promise that results to a boolean.
 *
 * @return Promise
 */
export function userHasProfile() {
	return getUser().then( result => !result.Error );
}

/**
 * Checks if the current user has registered the specified channel. Uses
 * the EE API GET /channels/{channel}. Resolves a promise chain to a
 * boolean.
 *
 * @param string channel The channel to check
 * @return Promise
 */
export function userHasChannel( channel ) {
	return getToken().then( token => {
		return fetch( __api`experience/channels/${channel}?authorization=${token}`, {
			method: 'GET',
		} )
			.then( response => response.json() )
			.then( result => !result.Error );
	} );
}

/**
 * Checks if the current publisher channel has been registered with the
 * current user.
 *
 * @return Promise
 */
export function userHasCurrentChannel() {
	const channel = getChannel();
	return userHasChannel( channel );
}

/**
 * Checks if the current user is registered with the current channel. If
 * not, adds the channel to the user.
 *
 * @return Promise
 */
export function ensureUserHasCurrentChannel() {
	return userHasCurrentChannel()
		.then( result => {
			if ( result ) {
				return true;
			} else {
				return addCurrentChannelToUser();
			}
		} )
		.catch( () => {
			return false;
		} );
}

/**
 * Adds the specified channel to the current user
 *
 * @param string channel The channel to add
 * @return Promise
 */
export function addChannelToUser( channel ) {
	return getToken().then( token => {
		return fetch( __api`experience/channels/${channel}?authorization=${token}`, {
			method: 'PUT',
		} );
	} );
}

/**
 * Adds the current publisher channel to the current user
 *
 * @return Promise
 */
export function addCurrentChannelToUser() {
	const channel = getChannel();
	return addChannelToUser( channel );
}

export function getUser() {
	return getToken()
		.then( token => fetch( __api`user?authorization=${token}` ) )
		.then( response => response.json() );
}

export function discovery( filters ) {
	const channel = getChannel();
	const { keyword, type, location, genre, brand } = filters;

	return getToken().then( token =>
		fetch(
			__api`discovery/?media_type=${type}&genre=${genre}&location=${location}&brand=${brand}&keyword=${keyword}&channel=${channel}&authorization=${token}`,
		),
	);
}

export function getFeeds( jwt = null ) {
	const channel = getChannel();

	return getToken( jwt )
		.then( token =>
			fetch(
				__api`experience/channels/${channel}/feeds/content/?authorization=${token}`,
			),
		)
		.then( response => response.json() );
}

export function modifyFeeds( feeds ) {
	const channel = getChannel();
	const params = {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify( feeds ),
	};

	return getToken().then( token =>
		fetch(
			__api`experience/channels/${channel}/feeds/?authorization=${token}`,
			params,
		),
	);
}

export function deleteFeed( feedId ) {
	const channel = getChannel();
	const params = { method: 'DELETE' };

	return getToken().then( token =>
		fetch(
			__api`experience/channels/${channel}/feeds/${feedId}/?authorization=${token}`,
			params,
		),
	);
}

export function searchKeywords( keyword ) {
	return fetch(
		__api`experience/channels/${getChannel()}/keywords/${keyword}/`,
	).then( response => response.json() );
}

export function validateDate( dateString ) {
	// @note: Leaving this is without disabling it.
	console.log( 'validateDate', dateString );
	// First check for the pattern
	if ( !/^\d{1,2}\/|-\d{1,2}\/|-\d{4}$/.test( dateString ) ) {
		return false;
	}

	// Parse the date parts to integers
	let parts;

	if ( dateString.includes( '-' ) ) {
		parts = dateString.split( '-' );
	} else {
		parts = dateString.split( '/' );
	}

	const year = parseInt( parts[2], 10 );
	const month = parseInt( parts[0], 10 );
	const day = parseInt( parts[1], 10 );

	// Check the ranges of month and year
	if ( 1000 > year || 3000 < year || 0 == month || 12 < month ) {
		return false;
	}

	const monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

	// Adjust for leap years
	if ( 0 == year % 400 || ( 0 != year % 100 && 0 == year % 4 ) )
		monthLength[1] = 29;

	// Check the range of the day
	return 0 < day && day <= monthLength[month - 1];
}

/**
 * Returns a boolean depending on whether the email is valid.
 *
 * Props: https://stackoverflow.com/a/46181
 *
 * @param email The input string
 * @return bool
 */
export function validateEmail( email ) {
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test( String( email ).toLowerCase() );
}

/**
 * Returns a boolean depending on whether the specified zipcode is a
 * valid US Zipcode.
 *
 * @param zipcode The input string
 * @return bool
 */
export function validateZipcode( zipcode ) {
	if ( zipcode ) {
		return /(^\d{5}$)|(^\d{5}-\d{4}$)/.test( zipcode );
	} else {
		return false;
	}
}

/**
 * Checks if gender field is valid.
 *
 * @param string The input string
 * @return bool
 */
export function validateGender( gender ) {
	return !!gender;
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
