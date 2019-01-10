/**
 * We use this approach to minify action names in the production bundle and have
 * human friendly actions in the dev bundle. Use "u{x}" format to create new actions.
 */

export const ACTION_SET_USER = 'production' === process.env.NODE_ENV ? 'u0' : 'SET_USER';
export const ACTION_RESET_USER = 'production' === process.env.NODE_ENV ? 'u1' : 'RESET_USER';
export const ACTION_SUPPRESS_USER_CHECK = 'production' === process.env.NODE_ENV ? 'u2' : 'SUPPRESS_USER_CHECK';

export function setUser( user ) {
	return {
		type: ACTION_SET_USER,
		user,
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
	resetUser,
	suppressUserCheck,
};
