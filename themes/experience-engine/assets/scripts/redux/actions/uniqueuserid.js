import {
	readcookies_method,
	bid_cookie_key,
	bid_created_cookie_key,
	current_session_cookie_key,
	prior_session_cookie_key,
} from '../../library/uniqueuserid';

export const ACTION_READ_COOKIES = 'READ_COOKIES';

export function readBID(data) {
	// console.log('console test from readBID uniqueuserid.js action: ', data);
	const bidValue = readcookies_method(bid_cookie_key);
	const bidCreateValue = readcookies_method(bid_created_cookie_key);
	const bidCurrentSessionValue = readcookies_method(current_session_cookie_key);
	const bidPriorSessionValue = readcookies_method(prior_session_cookie_key);
	// console.log('Console in Action function', bidValue);
	return {
		type: ACTION_READ_COOKIES,
		bidValueKey: bidValue,
		bidCreateValueKey: bidCreateValue,
		bidCurrentSessionValueKey: bidCurrentSessionValue,
		bidPriorSessionValueKey: bidPriorSessionValue,
	};
}
