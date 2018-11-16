import NProgress from 'nprogress';

import { loadAssets, unloadScripts } from '../../library/dom';
import {
	ACTION_INIT_PAGE,
	ACTION_LOADING_PARTIAL,
	ACTION_LOADING_PAGE,
	ACTION_LOADED_PAGE,
	ACTION_LOADED_PARTIAL,
	ACTION_LOAD_ERROR,
} from '../actions/screen';

export const DEFAULT_STATE = {
	scripts: {},
	embeds: [],
	content: '',
	partials: {},
	error: '',
};

function manageScripts( load, unload ) {
	unloadScripts( Object.keys( unload ) );
	loadAssets( Object.keys( load ) );
}

export default function reducer( state = {}, action = {} ) {
	switch ( action.type ) {
		case ACTION_INIT_PAGE:
			manageScripts( action.scripts, state.scripts );

			return {
				...state,
				embeds: action.embeds,
				content: action.content,
				scripts: action.scripts,
			};

		case ACTION_LOADING_PARTIAL:
		case ACTION_LOADING_PAGE:
			NProgress.start();
			break;

		case ACTION_LOADED_PAGE: {
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
			manageScripts( action.scripts, state.scripts );

			return {
				...state,
				scripts: action.scripts,
				embeds: action.embeds,
				content: action.content,
				error: '',
				partials: {},
			};
		}

		case ACTION_LOADED_PARTIAL:
			NProgress.done();
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

		case ACTION_LOAD_ERROR:
			return {
				...state,
				error: action.error,
			};

		default:
			// do nothing
			break;
	}

	return state;
}
