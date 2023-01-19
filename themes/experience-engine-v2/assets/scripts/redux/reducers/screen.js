import {
	ACTION_LOADING_PARTIAL,
	ACTION_LOADING_PAGE,
	ACTION_LOADED_PAGE,
	ACTION_LOADED_PARTIAL,
	ACTION_LOAD_ERROR,
	ACTION_HIDE_SPLASH_SCREEN,
	ACTION_UPDATE_NOTICE,
	ACTION_HISTORY_HTML_SNAPSHOT,
	ACTION_SET_SCREEN_STATE,
	ACTION_HIDE_LISTEN_LIVE,
	ACTION_SHOW_LISTEN_LIVE,
} from '../actions/screen';

export const DEFAULT_STATE = {
	content: '',
	embeds: [],
	error: '',
	history: {},
	isHome: false,
	partials: {},
	scripts: {},
	splashScreen: true,
	url: false,
	notice: {
		isOpen: false,
		message: '',
	},
	isListenLiveShowing: false,
	isAllowingListenLiveAutoClose: false,
};

/**
 * Screen Reducer
 *
 * @param {Object} state State object
 * @param {Object} action Dispatched action
 */
function reducer(state = {}, action = {}) {
	switch (action.type) {
		// Catch in Sagas
		case ACTION_SET_SCREEN_STATE:
			return {
				...state,
				content: action.payload.content,
				embeds: action.payload.embeds,
				scripts: action.payload.scripts,
			};

		case ACTION_LOADING_PARTIAL:
		case ACTION_LOADING_PAGE:
			return {
				...state,
				url: action.url,
			};

		case ACTION_LOADED_PAGE: {
			// do not accept action if user goes to another page before current page is loaded
			if (state.url !== action.url && !action.force) {
				return state;
			}

			return {
				...state,
				content: action.parsedHtml.content,
				isHome: action.isHome,
				embeds: action.parsedHtml.embeds,
				error: '',
				partials: {},
				scripts: action.parsedHtml.scripts,
			};
		}

		case ACTION_LOADED_PARTIAL: {
			// do not accept action if user goes to another page before current page is loaded
			if (state.url !== action.url) {
				return state;
			}

			return {
				...state,
				error: '',
				partials: {
					...state.partials,
					[action.placeholder]: {
						content: action.content,
						embeds: action.embeds,
					},
				},
			};
		}

		case ACTION_LOAD_ERROR:
			return {
				...state,
				error: action.error,
			};

		case ACTION_HIDE_SPLASH_SCREEN:
			return {
				...state,
				splashScreen: false,
			};

		case ACTION_UPDATE_NOTICE: {
			return {
				...state,
				notice: {
					isOpen: action.isOpen,
					message: action.message,
				},
			};
		}

		case ACTION_HISTORY_HTML_SNAPSHOT:
			return {
				...state,
				history: {
					...state.history,
					[action.uuid]: {
						id: action.uuid,
						data: action.data,
					},
				},
			};

		case ACTION_HIDE_LISTEN_LIVE: {
			return {
				...state,
				isListenLiveShowing: false,
				isAllowingListenLiveAutoClose: false,
			};
		}

		case ACTION_SHOW_LISTEN_LIVE: {
			const isAllowingListenLiveAutoClose =
				!state.isListenLiveShowing && action.isTriggeredByStream;
			console.log(
				`isListenLiveShowing: ${state.isListenLiveShowing} isTriggeredByStream: ${action.isTriggeredByStream} isAllowingListenLiveAutoClose: ${isAllowingListenLiveAutoClose}`,
			);
			return {
				...state,
				isListenLiveShowing: true,
				isAllowingListenLiveAutoClose,
			};
		}

		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
