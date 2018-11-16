import NProgress from 'nprogress';

import * as actions from '../actions/screen';

export const DEFAULT_STATE = {
	embeds: [],
	content: '',
	partials: {},
	error: '',
};

const reducer = ( state = {}, action = {} ) => {
	switch ( action.type ) {
		case actions.ACTION_INIT_PAGE:
			return {
				...state,
				embeds: action.embeds,
				content: action.content,
			};

		case actions.ACTION_LOADING_PARTIAL:
		case actions.ACTION_LOADING_PAGE:
			NProgress.start();
			break;

		case actions.ACTION_LOADED_PAGE: {
			const { document: pageDocument } = action;
			if ( pageDocument ) {
				const barId = 'wpadminbar';
				const wpadminbar = document.getElementById( barId );
				if ( wpadminbar ) {
					const newbar = pageDocument.getElementById( barId );
					if ( newbar ) {
						wpadminbar.parentNode.replaceChild( newbar, wpadminbar );
					}
				}
			}

			NProgress.done();

			return {
				...state,
				embeds: action.embeds,
				content: action.content,
				error: action.error,
				partials: {},
			};
		}

		case actions.ACTION_LOADED_PARTIAL:
			NProgress.done();
			return {
				...state,
				error: action.error,
				partials: {
					...state.partials,
					[action.placeholder]: {
						content: action.content,
						embeds: action.embeds,
					},
				},
			};

		default:
			// do nothing
			break;
	}

	return state;
};

export default reducer;
