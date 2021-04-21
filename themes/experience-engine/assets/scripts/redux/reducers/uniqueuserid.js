import { ACTION_READ_COOKIES } from '../actions/uniqueuserid';

export const DEFAULT_STATE = {
	BID: '',
	BID_CREATED: '',
	CURRENT_SESSION: '',
	PRIOR_SESSION: '',
};

function reducer(state = {}, action = {}) {
	switch (action.type) {
		case ACTION_READ_COOKIES:
			// console.log('Console in reducer function', action.bidValueKey);
			return {
				...state,
				BID: action.bidValueKey,
				BID_CREATED: action.bidCreateValueKey,
				CURRENT_SESSION: action.bidCurrentSessionValueKey,
				PRIOR_SESSION: action.bidPriorSessionValueKey,
			};
		default:
			// do anything
			break;
	}
	return state;
}

export default reducer;
