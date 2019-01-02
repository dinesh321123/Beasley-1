/**
 * We use this approach to minify action names in the production bundle and have
 * human friendly actions in the dev bundle. Use "u{x}" format to create new actions.
 */

export const ACTION_SET_USER = 'production' === process.env.NODE_ENV ? 'u0' : 'SET_USER';
export const ACTION_SET_TOKEN = 'production' === process.env.NODE_ENV ? 'u1' : 'SET_TOKEN';
export const ACTION_RESET_USER = 'production' === process.env.NODE_ENV ? 'u2' : 'RESET_USER';
export const ACTION_SUPPRESS_USER_CHECK = 'production' === process.env.NODE_ENV ? 'u3' : 'SUPPRESS_USER_CHECK';

export function setUser( user ) {
	return {
		type: ACTION_SET_USER,
		user,
	};
}

export function setToken( token ) {
	return {
		type: ACTION_SET_TOKEN,
		token,
	};
}

export function resetUser() {
	return { type: ACTION_RESET_USER };
}

export function suppressUserCheck() {
	return { type: ACTION_SUPPRESS_USER_CHECK };
}

export default {
	setUser,
	setToken,
	resetUser,
	suppressUserCheck,
};
