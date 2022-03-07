import {
	getFeeds,
	modifyFeeds,
	deleteFeed,
} from '../../library/experience-engine';

export const ACTION_SET_USER = 'SET_USER';
export const ACTION_RESET_USER = 'RESET_USER';
export const ACTION_SUPPRESS_USER_CHECK = 'SUPPRESS_USER_CHECK';
export const ACTION_SET_USER_FEEDS = 'SET_USER_FEEDS';
export const ACTION_MODIFY_USER_FEEDS = 'MODIFY_USER_FEEDS';
export const ACTION_UPDATE_USER_FEEDS = 'UPDATE_USER_FEEDS';
export const ACTION_DELETE_USER_FEED = 'DELETE_USER_FEED';
export const ACTION_SET_DISPLAY_NAME = 'SET_USER_DISPLAY_NAME';

function suppressCatch() {}

export function setUser(user) {
	return dispatch => {
		dispatch({ type: ACTION_SET_USER, user });

		user
			.getIdToken()
			.then(token => getFeeds(token))
			.then(feeds => dispatch({ type: ACTION_SET_USER_FEEDS, feeds }))
			.catch(suppressCatch);
	};
}

export function resetUser() {
	return { type: ACTION_RESET_USER };
}

export function suppressUserCheck() {
	return { type: ACTION_SUPPRESS_USER_CHECK };
}

export function modifyUserFeeds(feeds) {
	return dispatch => {
		modifyFeeds(feeds)
			.then(() => dispatch({ type: ACTION_MODIFY_USER_FEEDS, feeds }))
			.then(() => getFeeds())
			.then(userFeeds =>
				dispatch({ type: ACTION_UPDATE_USER_FEEDS, feeds: userFeeds }),
			)
			.catch(suppressCatch);
	};
}

export function deleteUserFeed(feed) {
	return dispatch => {
		deleteFeed(feed)
			.then(() => dispatch({ type: ACTION_DELETE_USER_FEED, feed }))
			.then(() => getFeeds())
			.then(userFeeds =>
				dispatch({ type: ACTION_UPDATE_USER_FEEDS, feeds: userFeeds }),
			)
			.catch(suppressCatch);
	};
}

export function setDisplayName(name) {
	return { type: ACTION_SET_DISPLAY_NAME, name };
}

export default {
	setUser,
	resetUser,
	suppressUserCheck,
	modifyUserFeeds,
	deleteUserFeed,
	setDisplayName,
};
