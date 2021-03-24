import { bake_cookie, read_cookie } from 'sfcookies';
import { v4 as uuidv4 } from 'uuid';
import { sendBidToGA } from './google-analytics';

export const readcookies_method = (cookie_key) => {
    return read_cookie(cookie_key)
}

export const backcookie_method = (cookie_key,cookie_value)  => {
    return bake_cookie(cookie_key, cookie_value);
}
export const bid_cookie_key = 'BID';
export const bid_created_cookie_key = 'BID_CREATED';
export const current_session_cookie_key = 'CURRENT_SESSION';
export const prior_session_cookie_key = 'PRIOR_SESSION';

export  const uniqueUserId = () => {
		const bid_cookie_val = readcookies_method(bid_cookie_key);
		const current_session_cookie_val = readcookies_method(current_session_cookie_key);
		if(bid_cookie_val && bid_cookie_val.length === 0) {
			/*
			 * IF cookie BID does not exist create UUID using UUID Generator and set it too a cookie called BID
			 * If cookie BID does not exist create create cookie called BID_CREATED and set it to the current epoch time
			 */
			const uuid_value = uuidv4();
			backcookie_method(bid_cookie_key, uuid_value);
			backcookie_method(bid_created_cookie_key, Date.now());
			/*
			 * Send BID
			 */
			sendBidToGA(uuid_value);
		}
		if (current_session_cookie_val && current_session_cookie_val.length === 0) {
			/*
			 * Set cookie equal to CURRENT_SESSION time equal to current epoch time
			 */
			backcookie_method(current_session_cookie_key, Date.now());
		} else {
			/*
			 * IF CURRENT_SESSION time cookie exists already set cookie PRIOR_SESSION equal to the value of CURRENT_SESSION cookie
			 */
			backcookie_method(prior_session_cookie_key, current_session_cookie_val);
			backcookie_method(current_session_cookie_key, Date.now());
        }	
}
