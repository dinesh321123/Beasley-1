import {
	removeChildren,
	getStateFromContent,
	parseHtml,
	pageview,
} from '../../library';
import { playStation } from './player';

export const ACTION_SET_SCREEN_STATE = 'SET_SCREEN_STATE';
export const ACTION_INIT_PAGE = 'PAGE_INIT';
export const ACTION_LOADING_PAGE = 'PAGE_LOADING';
export const ACTION_LOADED_PAGE = 'PAGE_LOADED';
export const ACTION_LOADING_PARTIAL = 'PARTIAL_LOADING';
export const ACTION_LOADED_PARTIAL = 'PARTIAL_LOADED';
export const ACTION_LOAD_ERROR = 'LOADING_ERROR';
export const ACTION_HIDE_SPLASH_SCREEN = 'HIDE_SPLASH_SCREEN';
export const ACTION_UPDATE_NOTICE = 'UPDATE_NOTICE ';
export const ACTION_HISTORY_HTML_SNAPSHOT = 'HISTORY_HTML_SNAPSHOT';
export const ACTION_HIDE_LISTEN_LIVE = 'HIDE_LISTEN_LIVE';
export const ACTION_SHOW_LISTEN_LIVE = 'SHOW_LISTEN_LIVE';

/**
 * Parses the current content blocks for redux.
 */
export function initPage() {
	const content = document.getElementById('content');
	const parsed = getStateFromContent(content, window.location.href);

	// clean up content block for now, it will be poplated in the render function
	removeChildren(content);

	return {
		type: ACTION_INIT_PAGE,
		payload: {
			content: parsed.content,
			embeds: parsed.embeds,
			scripts: parsed.scripts,
		},
	};
}

/**
 * Sets the html snapshot of the given page.
 *
 * @param {string} uuid A slugifyed representation of the url
 * @param {string} html The html of the page.
 */
export function initPageLoaded(uuid, html) {
	return { type: ACTION_HISTORY_HTML_SNAPSHOT, uuid, html };
}

/**
 * Fetches the feed content for a user.
 *
 * @param {string} token Firease ID token
 * @param {string} url   Optional URL to associate the feeds content to.
 * @param {object} Options
 */
export const fetchFeedsContent = (
	token,
	url = '',
	options = { suppressHistory: true },
) => async dispatch => {
	dispatch({ type: ACTION_LOADING_PAGE, url });

	try {
		const response = await fetch(
			`${window.bbgiconfig.wpapi}feeds-content?device=other`,
			{
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: `authorization=${encodeURIComponent(token)}`,
			},
		).then(res => res.json());

		const parsedHtml = parseHtml(url, response.html);
		dispatch({
			type: ACTION_LOADED_PAGE,
			url,
			response,
			options,
			isHome: true,
			parsedHtml,
		});
	} catch (error) {
		dispatch({ type: ACTION_LOAD_ERROR, error });
	}
};

/**
 * Fetches a page by calling the page endpoint.
 *
 * @param {string} url
 * @param {object} Options
 */
export const fetchPage = (url, options = {}) => async dispatch => {
	const pageEndpoint = `${
		window.bbgiconfig.wpapi
	}\page?url=${encodeURIComponent(url)}`; // eslint-disable-line no-useless-escape

	try {
		const station_play_link_matched = url.match(/str-station-([A-Za-z0-9]+)/);

		if (station_play_link_matched) {
			console.log(`${station_play_link_matched[1].toUpperCase()}`);
			// TODO: Dispatch play event with station in payload
			dispatch(playStation(station_play_link_matched[1].toUpperCase()));
			return;
		}

		dispatch({ type: ACTION_LOADING_PAGE, url });

		const response = await fetch(pageEndpoint).then(response =>
			response.json(),
		);
		const { redirect } = response;

		// redirects.
		if ([301, 302, 303, 307, 308].includes(response.status)) {
			if (redirect.url && !redirect.internal) {
				window.location.href = response.redirect;
			} else {
				// internal redirect
				dispatch(fetchPage(redirect.url, options));
			}

			return;
		}

		// unsuccessful status code.
		if (response.status !== 200 && response.status !== 201) {
			dispatch({ type: ACTION_LOAD_ERROR });
			return;
		}
		const parsedHtml = parseHtml(url, response.html);

		dispatch({
			type: ACTION_LOADED_PAGE,
			url,
			response,
			options,
			isHome: parsedHtml.document.body.classList.contains('home'),
			parsedHtml,
		});
	} catch (error) {
		dispatch({ type: ACTION_LOAD_ERROR, error });
	}
};

/**
 * Loads a partial page (e.g for LoadMode)
 *
 * @param {string} url
 * @param {*} placeholder
 */
export function loadPartialPage(url, placeholder) {
	return dispatch => {
		dispatch({ type: ACTION_LOADING_PARTIAL, url });

		function onError(error) {
			// eslint-disable-next-line no-console
			console.error(error);
			dispatch({ type: ACTION_LOAD_ERROR, error });
		}

		function onSuccess(data) {
			const parsed = parseHtml(url, data, '#inner-content');
			dispatch({ type: ACTION_LOADED_PARTIAL, url, ...parsed, placeholder });
			pageview(parsed.document.title, url);
		}

		fetch(url)
			.then(response => response.text())
			.then(onSuccess)
			.catch(onError);
	};
}

/**
 * Hides the splash screen
 */
export function hideSplashScreen() {
	return { type: ACTION_HIDE_SPLASH_SCREEN };
}

export function hideListenLive() {
	return { type: ACTION_HIDE_LISTEN_LIVE };
}

export function showListenLive({ isTriggeredByStream }) {
	return {
		type: ACTION_SHOW_LISTEN_LIVE,
		isTriggeredByStream,
	};
}

/**
 * Updates the Notice component message.
 */
export function updateNotice({ isOpen, message }) {
	return {
		type: ACTION_UPDATE_NOTICE,
		force: true,
		isOpen,
		message,
	};
}

export default {
	hideSplashScreen,
	initPage,
	initPageLoaded,
	loadPartialPage,
	updateNotice,
};
