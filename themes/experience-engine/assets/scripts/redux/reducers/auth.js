import {
	ACTION_SET_USER,
	ACTION_RESET_USER,
	ACTION_SUPPRESS_USER_CHECK,
} from '../actions/auth';

export const DEFAULT_STATE = {
	user: null,
	suppressUserCheck: false,
};

function reducer( state = {}, action = {} ) {
	switch ( action.type ) {
		case ACTION_SET_USER:
			return {
				...state,
				user: action.user,
			};

		case ACTION_RESET_USER:
			return {
				...DEFAULT_STATE,
			};

		case ACTION_SUPPRESS_USER_CHECK:
			return {
				...state,
				suppressUserCheck: true,
			};

		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
