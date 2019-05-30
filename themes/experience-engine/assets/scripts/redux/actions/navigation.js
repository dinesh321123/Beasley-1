export const ACTION_NAVIGATION_SET_CURRENT =
	'production' === process.env.NODE_ENV ? 'n0' : 'SET_NAVIGATION_CURRENT';
export const ACTION_NAVIGATION_SET_REVERT =
	'production' === process.env.NODE_ENV ? 'n1' : 'SET_NAVIGATION_REVERT';

export function setNavigationCurrent( menu ) {
	return dispatch => {
		dispatch( { type: ACTION_NAVIGATION_SET_CURRENT, menu } );
	};
}

export function setNavigationRevert() {
	return dispatch => {
		dispatch( { type: ACTION_NAVIGATION_SET_REVERT } );
	};
}

export default {
	setNavigationCurrent,
	setNavigationRevert,
};
