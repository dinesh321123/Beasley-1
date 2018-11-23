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
	// remove scripts loaded on the previous page
	unloadScripts( Object.keys( unload ) );

	// a workaround to make sure Facebook embeds work properly
	delete window.FB;
	window.FB = null;

	// load scripts for the new page
	loadAssets( Object.keys( load ) );
}

function manageBbgiConfig() {
	let newconfig = {};

	try {
		newconfig = JSON.parse( document.body.dataset.bbgiconfig );

		const { googletag } = window;
		const { dfp } = newconfig;

		if ( dfp && Array.isArray( dfp.global ) ) {
			googletag.pubads().clearTargeting();
			for ( let i = 0, pairs = dfp.global; i < pairs.length; i++ ) {
				googletag.pubads().setTargeting( pairs[i][0], pairs[i][1] );
			}
		}
	} catch ( err ) {
		// do nothing
	}

	window.bbgiconfig = newconfig;
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
			manageBbgiConfig();

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
			manageBbgiConfig();

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
