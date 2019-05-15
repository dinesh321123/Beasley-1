import {
	ACTION_NAVIGATION_SET_CURRENT,
	ACTION_NAVIGATION_SET_REVERT
} from '../actions/navigation';

export const DEFAULT_STATE = {
	current: 'menu-item-home',
	previous: null
};

function reducer( state = {}, action = {} ) {
	switch ( action.type ) {
		case ACTION_NAVIGATION_SET_CURRENT:
			return { ...state, current: action.menu, previous: state.current };
		case ACTION_NAVIGATION_SET_REVERT:
			return { ...state, current: state.previous, previous: null };
		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
