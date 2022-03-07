import {
	ACTION_SET_USER,
	ACTION_RESET_USER,
	ACTION_SUPPRESS_USER_CHECK,
	ACTION_SET_USER_FEEDS,
	ACTION_MODIFY_USER_FEEDS,
	ACTION_DELETE_USER_FEED,
	ACTION_SET_DISPLAY_NAME,
} from '../actions/auth';

export const DEFAULT_STATE = {
	displayName: '',
	feeds: [],
	suppressUserCheck: false,
	user: null,
};

function reducer(state = {}, action = {}) {
	switch (action.type) {
		case ACTION_SET_USER:
			return { ...state, user: action.user };
		case ACTION_RESET_USER:
			return { ...DEFAULT_STATE };
		case ACTION_SUPPRESS_USER_CHECK:
			return { ...state, suppressUserCheck: true };
		case ACTION_MODIFY_USER_FEEDS:
		case ACTION_SET_USER_FEEDS:
			return {
				...state,
				feeds: action.feeds
					.filter(item => !!item.sortorder)
					.map(item => ({
						id: item.id,
						sortorder: item.sortorder,
					})),
			};
		case ACTION_DELETE_USER_FEED:
			return {
				...state,
				feeds: state.feeds.filter(item => item.id !== action.feed),
			};
		case ACTION_SET_DISPLAY_NAME:
			return { ...state, displayName: action.name };
		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
