/**
 * We use this approach to minify action names in the production bundle and have
 * human friendly actions in the dev bundle. Use "m{x}" format to create new actions.
 */

export const ACTION_SHOW_MODAL = 'production' === process.env.NODE_ENV ? 'm0' : 'MODAL_SHOW';
export const ACTION_HIDE_MODAL = 'production' === process.env.NODE_ENV ? 'm1' : 'MODAL_HIDE';

export const SIGNIN_MODAL = 'SIGNIN-MODAL';
export const SIGNUP_MODAL = 'SIGNUP-MODAL';
export const RESTORE_MODAL = 'RESTORE-MODAL';
export const COMPLETE_SIGNUP_MODAL = 'COMPLETE-SIGNUP-MODAL';
export const DISCOVER_MODAL = 'DISCOVER-MODAL';
export const EDIT_FEED_MODAL = 'EDIT-FEED-MODAL';

function showModal( modal, payload ) {
	return {
		type: ACTION_SHOW_MODAL,
		modal,
		payload,
	};
}

export function hideModal() {
	return { type: ACTION_HIDE_MODAL };
}

export function showSignInModal( payload = {} ) {
	return showModal( SIGNIN_MODAL, payload );
}

export function showSignUpModal( payload = {} ) {
	return showModal( SIGNUP_MODAL, payload );
}

export function showRestoreModal( payload = {} ) {
	return showModal( RESTORE_MODAL, payload );
}

export function showCompleteSignupModal( payload = {} ) {
	return showModal( COMPLETE_SIGNUP_MODAL, payload );
}

export function showDiscoverModal( payload = {} ) {
	return showModal( DISCOVER_MODAL, payload );
}

export function showEditFeedModal( payload = {} ) {
	return showModal( EDIT_FEED_MODAL, payload );
}

export default {
	hideModal,
	showSignInModal,
	showSignUpModal,
	showRestoreModal,
	showCompleteSignupModal,
	showDiscoverModal,
	showEditFeedModal,
};
