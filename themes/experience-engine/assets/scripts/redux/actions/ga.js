export const ACTION_GA_SET_PAGEVIEW_DATA = 'SET_GA_PAGEVIEW';

export function setGAPageviewData(data) {
	return dispatch => {
		dispatch({ type: ACTION_GA_SET_PAGEVIEW_DATA, data });
	};
}

export default {
	setGAPageviewData,
};
