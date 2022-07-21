export const ACTION_DROPDOWN_AD_REFRESH = 'ACTION_DROPDOWN_AD_REFRESH';
export const ACTION_DROPDOWN_AD_REFRESHED = 'ACTION_DROPDOWN_AD_REFRESHED';
export const ACTION_DROPDOWN_AD_HIDE = 'ACTION_DROPDOWN_AD_HIDE';
export const ACTION_DROPDOWN_AD_HIDDEN = 'ACTION_DROPDOWN_AD_HIDDEN';

export function refreshDropdownAd() {
	return { type: ACTION_DROPDOWN_AD_REFRESH };
}

export function dropdownAdRefreshed() {
	return { type: ACTION_DROPDOWN_AD_REFRESHED };
}

export function hideDropdownAd() {
	return { type: ACTION_DROPDOWN_AD_HIDE };
}

export function dropdownAdHidden() {
	return { type: ACTION_DROPDOWN_AD_HIDDEN };
}

export default {
	refreshDropdownAd,
	dropdownAdRefreshed,
	hideDropdownAd,
	dropdownAdHidden,
};
