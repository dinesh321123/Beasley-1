import { getFeeds, modifyFeeds, deleteFeed } from '../../library/experience-engine';

/**
 * We use this approach to minify action names in the production bundle and have
 * human friendly actions in the dev bundle. Use "u{x}" format to create new actions.
 */

export const ACTION_SET_USER = 'production' === process.env.NODE_ENV ? 'u0' : 'SET_USER';
export const ACTION_RESET_USER = 'production' === process.env.NODE_ENV ? 'u1' : 'RESET_USER';
export const ACTION_SUPPRESS_USER_CHECK = 'production' === process.env.NODE_ENV ? 'u2' : 'SUPPRESS_USER_CHECK';
export const ACTION_SET_USER_FEEDS = 'production' === process.env.NODE_ENV ? 'u3' : 'SET_USER_FEEDS';
export const ACTION_DELETE_USER_FEED = 'production' === process.env.NODE_ENV ? 'u4' : 'DELETE_USER_FEED';

function suppressCatch() {}

export function setUser( user ) {
	return ( dispatch ) => {
		dispatch( { type: ACTION_SET_USER, user } );

		user.getIdToken()
			.then( token => getFeeds( token ) )
			.then( feeds => dispatch( { type: ACTION_SET_USER_FEEDS, feeds } ) )
			.catch( suppressCatch );
	};
}

export function resetUser() {
	return { type: ACTION_RESET_USER };
}

export function suppressUserCheck() {
	return { type: ACTION_SUPPRESS_USER_CHECK };
}

export function modifyUserFeeds( feeds ) {
	return ( dispatch ) => {
		modifyFeeds( feeds )
			.then( () => dispatch( { type: ACTION_SET_USER_FEEDS, feeds } ) )
			.catch( suppressCatch );
	};
}

export function deleteUserFeed( feed ) {
	return ( dispatch ) => {
		deleteFeed( feed )
			.then( () => dispatch( { type: ACTION_DELETE_USER_FEED, feed } ) )
			.catch( suppressCatch );
	};
}

export default {
	setUser,
	resetUser,
	suppressUserCheck,
};
