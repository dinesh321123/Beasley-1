import { ACTION_GA_SET_PAGEVIEW_DATA } from '../actions/ga';

export const DEFAULT_STATE = {
	pageview_data: {},
};

function reducer(state = {}, action = {}) {
	switch (action.type) {
		case ACTION_GA_SET_PAGEVIEW_DATA:
			return { ...state, pageview_data: action.data };
		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
