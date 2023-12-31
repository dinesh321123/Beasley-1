import { firebaseAuth } from './firebase';

function __api(strings, ...params) {
	let url = window.bbgiconfig.eeapi;
	strings.forEach((string, i) => {
		url += string + encodeURIComponent(params[i] || '');
	});

	return url;
}

export function getChannel() {
	const { publisher } = window.bbgiconfig;
	const { id: channel } = publisher || {};

	return channel || '';
}

export function getToken(token = null) {
	if (token) {
		return Promise.resolve(token);
	}

	if (!firebaseAuth.currentUser) {
		return Promise.reject();
	}

	return firebaseAuth.currentUser
		.getIdToken()
		.catch(error => console.error(error)); // eslint-disable-line no-console
}

export function getUser() {
	return getToken()
		.then(token => fetch(__api`user?authorization=${token}`))
		.then(response => response.json());
}

export function saveUser(email, zipcode, gender, dateofbirth) {
	const channel = getChannel();
	const params = {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			zipcode,
			gender: gender === 'male' ? 'M' : 'F',
			dateofbirth,
			email,
		}),
	};

	return getToken().then(token => {
		return fetch(__api`user?authorization=${token}`, params).then(() =>
			fetch(__api`experience/channels/${channel}?authorization=${token}`, {
				method: 'PUT',
			}),
		);
	});
}

/**
 * Checks if User has previously saved Profile information. Returns a
 * promise that results to a boolean.
 *
 * @return Promise
 */
export function userHasProfile() {
	return getUser().then(result => !result.Error);
}

/**
 * Checks if the current user has registered the specified channel. Uses
 * the EE API GET /channels/{channel}. Resolves a promise chain to a
 * boolean.
 *
 * @param string channel The channel to check
 * @return Promise
 */
export function userHasChannel(channel) {
	return getToken().then(token => {
		return fetch(__api`experience/channels/${channel}?authorization=${token}`, {
			method: 'GET',
		})
			.then(response => response.json())
			.then(result => !result.Error);
	});
}

/**
 * Checks if the current publisher channel has been registered with the
 * current user.
 *
 * @return Promise
 */
export function userHasCurrentChannel() {
	const channel = getChannel();
	return userHasChannel(channel);
}

/**
 * Adds the current publisher channel to the current user
 *
 * @return Promise
 */
export function addCurrentChannelToUser() {
	const channel = getChannel();
	return addChannelToUser(channel);
}

/**
 * Checks if the current user is registered with the current channel. If
 * not, adds the channel to the user.
 *
 * @return Promise
 */
export function ensureUserHasCurrentChannel() {
	return userHasCurrentChannel()
		.then(result => {
			if (result) {
				return true;
			}
			return addCurrentChannelToUser();
		})
		.catch(() => {
			return false;
		});
}

/**
 * Adds the specified channel to the current user
 *
 * @param string channel The channel to add
 * @return Promise
 */
export function addChannelToUser(channel) {
	return getToken().then(token => {
		return fetch(__api`experience/channels/${channel}?authorization=${token}`, {
			method: 'PUT',
		});
	});
}

export function discovery(filters) {
	const channel = getChannel();
	const { keyword, type, location, genre, brand } = filters;

	return getToken().then(token =>
		fetch(
			__api`discovery/?media_type=${type}&genre=${genre}&location=${location}&brand=${brand}&keyword=${keyword}&channel=${channel}&authorization=${token}`,
		),
	);
}

export function getFeeds(jwt = null) {
	const channel = getChannel();

	return getToken(jwt)
		.then(token =>
			fetch(
				__api`experience/channels/${channel}/feeds/content/?authorization=${token}`,
			),
		)
		.then(response => response.json());
}

export function modifyFeeds(feeds) {
	const channel = getChannel();
	const params = {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(feeds),
	};

	return getToken().then(token =>
		fetch(
			__api`experience/channels/${channel}/feeds/?authorization=${token}`,
			params,
		),
	);
}

export function deleteFeed(feedId) {
	const channel = getChannel();
	const params = { method: 'DELETE' };

	return getToken().then(token =>
		fetch(
			__api`experience/channels/${channel}/feeds/${feedId}/?authorization=${token}`,
			params,
		),
	);
}

export function searchKeywords(keyword) {
	return fetch(
		__api`experience/channels/${getChannel()}/keywords/${keyword}/`,
	).then(response => response.json());
}

export function validateFutureDate(dateString) {
	const today = new Date();
	const parsedDate = new Date(dateString);

	if (parsedDate >= today) {
		return false;
	}
}

export function validateDate(dateString) {
	// @note: Leaving this is without disabling it.
	// First check for the pattern
	if (!/^\d{1,2}\/|-\d{1,2}\/|-\d{4}$/.test(dateString)) {
		return false;
	}
	// Parse the date parts to integers
	let parts;

	if (dateString.includes('-')) {
		parts = dateString.split('-');
	} else {
		parts = dateString.split('/');
	}

	const year = parseInt(parts[2], 10);
	const month = parseInt(parts[0], 10);
	const day = parseInt(parts[1], 10);

	// Check the ranges of month and year
	if (year < 1000 || year > 3000 || month === 0 || month > 12) {
		return false;
	}

	const monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

	// Adjust for leap years
	if (year % 400 === 0 || (year % 100 !== 0 && year % 4 === 0))
		monthLength[1] = 29;

	// Check the range of the day
	return day > 0 && day <= monthLength[month - 1];
}

/**
 * Returns a boolean depending on whether the email is valid.
 *
 * Props: https://stackoverflow.com/a/46181
 *
 * @param email The input string
 * @return bool
 */
export function validateEmail(email) {
	const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(String(email).toLowerCase());
}

/**
 * Returns a boolean depending on whether the specified zipcode is a
 * valid US Zipcode.
 *
 * @param zipcode The input string
 * @return bool
 */
export function validateZipcode(zipcode) {
	if (zipcode) {
		return /(^\d{5}$)|(^\d{5}-\d{4}$)/.test(zipcode);
	}
	return false;
}

/**
 * Checks if gender field is valid.
 *
 * @param string The input string
 * @return bool
 */
export function validateGender(gender) {
	return !!gender;
}

/**
 * Returns a boolean depending on whether the specified value is a
 * valid URL or not.
 *
 * @param url The input string
 * @return bool
 */
export function validateUrl(url) {
	if (url) {
		return /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(
			url,
		);
	}
	return false;
}

export function fetchPublisherInformation(metaVal) {
	const { publisher } = window.bbgiconfig;

	if (publisher && Object.keys(publisher).length === 0) {
		return null;
	}

	let response = publisher[metaVal];
	switch (metaVal) {
		case 'facebook':
			if (!validateUrl(response)) {
				response = `https://www.facebook.com/${encodeURIComponent(response)}`;
			}
			break;
		case 'twitter':
			if (!validateUrl(response)) {
				response = `https://twitter.com/${encodeURIComponent(
					response.replace(new RegExp('^@+'), ''),
				)}`;
			}
			break;
		case 'instagram':
			if (!validateUrl(response)) {
				response = `https://www.instagram.com/${encodeURIComponent(
					response.replace(new RegExp('^@+'), ''),
				)}`;
			}
			break;
		case 'youtube':
			if (!validateUrl(response)) {
				response = `https://www.youtube.com/user/${encodeURIComponent(
					response,
				)}`;
			}
			break;
		default:
			break;
	}

	return response;
}

export function getOffsetEl(el) {
	const rect = el.getBoundingClientRect();
	const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
	const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
	return { top: rect.top + scrollTop, left: rect.left + scrollLeft };
}

export function fixMegaSubMenuWidth() {
	if (window.matchMedia('(min-width: 1301px)').matches) {
		const mainUl = document.getElementById('mega-menu-primary-nav');
		const mainUlLeft = mainUl ? getOffsetEl(mainUl).left : 0;
		const container = document.getElementById('js-primary-mega-nav');

		const mainlinks = container.querySelectorAll(
			'.mega-menu-item-has-children > a',
		);
		for (let i = 0; i < mainlinks.length; i++) {
			const mainel = mainlinks[i];
			const nextEl = mainel.nextElementSibling;
			if (
				nextEl.nodeName.toUpperCase() === 'UL' &&
				nextEl.classList.contains('mega-sub-menu')
			) {
				const leftoffset = `calc(-${mainUlLeft}px + 1vw)`;
				nextEl.style.left = leftoffset;
				nextEl.style.width = '98vw';
			}
		}
	}
}
export function deleteUser() {
	const params = {
		method: 'delete',
		headers: {
			Accept: 'application/json',
			'Content-Type': 'application/json',
		},
		body: null,
	};
	return getToken().then(token => {
		fetch(__api`user?authorization=${token}`, params).then(response => {
			// Log user out
			firebaseAuth.signOut();
			response.json();
		});
	});
}

export default {
	saveUser,
	getToken,
	getChannel,
	getUser,
	discovery,
	getFeeds,
	modifyFeeds,
	deleteFeed,
	searchKeywords,
	validateDate,
	validateFutureDate,
	validateUrl,
	fetchPublisherInformation,
	getOffsetEl,
	fixMegaSubMenuWidth,
	deleteUser,
};
