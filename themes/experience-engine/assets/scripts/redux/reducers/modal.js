import { ACTION_SHOW_MODAL, ACTION_HIDE_MODAL } from '../actions/modal';

export const DEFAULT_STATE = {
	modal: 'CLOSED',
	payload: {},
};

function reducer( state = {}, action = {} ) {
	switch ( action.type ) {
		case ACTION_SHOW_MODAL:
			document.body.classList.add( 'locked' );
			return {
				...state,
				modal: action.modal,
				payload: action.payload,
			};
		case ACTION_HIDE_MODAL:
			document.body.classList.remove( 'locked' );
			return { ...DEFAULT_STATE };
		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
