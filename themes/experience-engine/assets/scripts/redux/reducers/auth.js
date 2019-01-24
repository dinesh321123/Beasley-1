import {
	ACTION_SET_USER,
	ACTION_RESET_USER,
	ACTION_SUPPRESS_USER_CHECK,
	ACTION_SET_USER_FEEDS,
	ACTION_DELETE_USER_FEED,
} from '../actions/auth';

export const DEFAULT_STATE = {
	user: null,
	suppressUserCheck: false,
	feeds: [],
};

function reducer( state = {}, action = {} ) {
	switch ( action.type ) {
		case ACTION_SET_USER:
			return { ...state, user: action.user };
		case ACTION_RESET_USER:
			return { ...DEFAULT_STATE };
		case ACTION_SUPPRESS_USER_CHECK:
			return { ...state, suppressUserCheck: true };
		case ACTION_SET_USER_FEEDS:
			return { ...state, feeds: action.feeds };
		case ACTION_DELETE_USER_FEED:
			return { ...state, feeds: state.feeds.filter( item => item.id !== action.feed ) };
		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
