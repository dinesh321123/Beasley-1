import {
	DISCOVER_MODAL,
	ACTION_SHOW_MODAL,
	ACTION_HIDE_MODAL,
	COMPLETE_SIGNUP_MODAL,
} from '../actions/modal';

export const DEFAULT_STATE = {
	modal: 'CLOSED',
	payload: {},
};

function resizeWindow() {
	try {
		window.dispatchEvent(new Event('resize'));
	} catch (e) {
		// no-op
	}
}

function reducer(state = {}, action = {}) {
	switch (action.type) {
		case ACTION_SHOW_MODAL:
			if (
				action.modal !== DISCOVER_MODAL &&
				action.modal !== COMPLETE_SIGNUP_MODAL
			) {
				document.documentElement.classList.add('locked');
				document.body.classList.add('locked');
				document.addEventListener('ontouchmove', e => {
					e.preventDefault();
				});

				resizeWindow();
			}

			return {
				...state,
				modal: action.modal,
				payload: action.payload,
			};
		case ACTION_HIDE_MODAL:
			document.documentElement.classList.remove('locked');
			document.body.classList.remove('locked');
			document.removeEventListener('ontouchmove', () => {
				return true;
			});

			resizeWindow();
			return { ...DEFAULT_STATE };
		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
