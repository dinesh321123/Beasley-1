import {
	DISCOVER_MODAL,
	ACTION_SHOW_MODAL,
	ACTION_HIDE_MODAL,
	COMPLETE_SIGNUP_MODAL,
	SIGNIN_MODAL,
} from '../actions/modal';

export const DEFAULT_STATE = {
	modal: 'CLOSED',
	payload: {},
	isShowSigninModalMode: true,
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
		case ACTION_SHOW_MODAL: {
			const isShowingSignin =
				action.modal === SIGNIN_MODAL && state.isShowSigninModalMode;

			if (
				(action.modal !== DISCOVER_MODAL &&
					action.modal !== COMPLETE_SIGNUP_MODAL) ||
				isShowingSignin
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
				isShowSigninModalMode: state.isShowSigninModalMode && !isShowingSignin,
			};
		}
		case ACTION_HIDE_MODAL:
			document.documentElement.classList.remove('locked');
			document.body.classList.remove('locked');
			document.removeEventListener('ontouchmove', () => {
				return true;
			});

			resizeWindow();
			return {
				...state,
				modal: 'CLOSED',
				payload: {},
			};

		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
