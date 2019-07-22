import NProgress from 'nprogress';

import { loadAssets, unloadScripts } from '../../library/dom';
import {
	ACTION_INIT_PAGE,
	ACTION_LOADING_PARTIAL,
	ACTION_LOADING_PAGE,
	ACTION_LOADED_PAGE,
	ACTION_LOADED_PARTIAL,
	ACTION_LOAD_ERROR,
	ACTION_HIDE_SPLASH_SCREEN,
	ACTION_UPDATE_NOTICE,
	ACTION_HISTORY_HTML_SNAPSHOT,
} from '../actions/screen';

export const DEFAULT_STATE = {
	content: '',
	embeds: [],
	error: '',
	history: [],
	isHome: false,
	partials: {},
	scripts: {},
	splashScreen: true,
	url: false,
	notice: {
		isOpen: false,
		message: '',
	},
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

function manageBbgiConfig( pageDocument ) {
	let newconfig = {};

	try {
		newconfig = JSON.parse( pageDocument.getElementById( 'bbgiconfig' ).innerHTML );
	} catch ( err ) {
		// do nothing
	}

	window.bbgiconfig = newconfig;
}

function hideSplashScreen() {
	setTimeout( () => {
		const splashScreen = document.getElementById( 'splash-screen' );
		if ( splashScreen ) {
			splashScreen.parentNode.removeChild( splashScreen );
		}

		if ( 'function' === typeof window['cssVars'] ) {
			window['cssVars']( window.bbgiconfig.cssvars );
		}
	}, 2000 );
}

export function updateTargeting() {
	let { googletag } = window;

	const { dfp } = window.bbgiconfig;

	if ( dfp && Array.isArray( dfp.global ) ) {
		for ( let i = 0, pairs = dfp.global; i < pairs.length; i++ ) {
			googletag.pubads().setTargeting( pairs[i][0], pairs[i][1] );
		}
	}
}

export function clearTargeting() {
	let googletag = window.googletag;

	if ( googletag && googletag.apiReady ) {
		googletag.pubads().clearTargeting();
	}
}

export function updateCorrelator() {
	let { googletag } = window;

	/* Extra safety as updateCorrelator is a deprecated function in DFP */
	try {
		if ( googletag && googletag.apiReady && googletag.pubads().updateCorrelator ) {
			googletag.pubads().updateCorrelator();
		}
	} catch ( e ) {
		// no-op
	}
}

function reducer( state = {}, action = {} ) {
	switch ( action.type ) {
		case ACTION_INIT_PAGE:
			manageScripts( action.scripts, state.scripts );

			return {
				...state,
				content: action.content,
				embeds: action.embeds,
				scripts: action.scripts,
			};

		case ACTION_LOADING_PARTIAL:
		case ACTION_LOADING_PAGE:
			if ( window.location.href !== action.url ) {
				updateCorrelator();
				clearTargeting();
			}

			NProgress.start();
			return { ...state, url: action.url };

		case ACTION_LOADED_PAGE: {
			// do not accept action if user goes to another page before current page is loaded
			if ( state.url !== action.url && !action.force ) {
				return state;
			}

			const { document: pageDocument } = action;

			manageBbgiConfig( pageDocument );
			updateTargeting();

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
			hideSplashScreen();

			return {
				...state,
				content: action.content,
				isHome: action.isHome,
				embeds: action.embeds,
				error: '',
				partials: {},
				scripts: action.scripts,
			};
		}

		case ACTION_LOADED_PARTIAL: {
			// do not accept action if user goes to another page before current page is loaded
			if ( state.url !== action.url ) {
				return state;
			}

			const { document: pageDocument } = action;

			NProgress.done();
			manageBbgiConfig( pageDocument );
			hideSplashScreen();

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
			return { ...state, error: action.error };

		case ACTION_HIDE_SPLASH_SCREEN:
			hideSplashScreen();
			return { ...state, splashScreen: false };

		case ACTION_UPDATE_NOTICE: {
			const notice = {
				isOpen: action.isOpen,
				message: action.message,
			};

			return { ...state, notice: notice };
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

		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
