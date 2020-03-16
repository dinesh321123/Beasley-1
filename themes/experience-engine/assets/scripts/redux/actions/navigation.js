export const ACTION_NAVIGATION_SET_CURRENT = 'SET_NAVIGATION_CURRENT';
export const ACTION_NAVIGATION_SET_REVERT = 'SET_NAVIGATION_REVERT';

export function setNavigationCurrent(menu) {
	return dispatch => {
		dispatch({ type: ACTION_NAVIGATION_SET_CURRENT, menu });
	};
}

export function setNavigationRevert() {
	return dispatch => {
		dispatch({ type: ACTION_NAVIGATION_SET_REVERT });
	};
}

export default {
	setNavigationCurrent,
	setNavigationRevert,
};
