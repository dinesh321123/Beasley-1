import {
	ACTION_DROPDOWN_AD_REFRESH,
	ACTION_DROPDOWN_AD_REFRESHED,
	ACTION_DROPDOWN_AD_HIDE,
	ACTION_DROPDOWN_AD_HIDDEN,
} from '../actions/dropdownad';

export const DEFAULT_STATE = {
	shouldRefreshDropdownAd: false,
	shouldHideDropdownAd: false,
};

function reducer(state = {}, action = {}) {
	switch (action.type) {
		case ACTION_DROPDOWN_AD_REFRESH:
			return { ...state, shouldRefreshDropdownAd: true };
		case ACTION_DROPDOWN_AD_REFRESHED:
			return { ...state, shouldRefreshDropdownAd: false };
		case ACTION_DROPDOWN_AD_HIDE:
			return { ...state, shouldHideDropdownAd: true };
		case ACTION_DROPDOWN_AD_HIDDEN:
			return {
				...state,
				shouldHideDropdownAd: false,
			};
		default:
			// do nothing
			break;
	}

	return state;
}

export default reducer;
