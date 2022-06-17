import Bowser from 'bowser';

export const isSafari = () => {
	const { userAgent } = window.navigator;
	return /^((?!chrome|android).)*safari/i.test(userAgent);
};

export const isChrome = () => {
	return !!window.chrome;
};

export const isFireFox = () => {
	const { userAgent } = window.navigator;
	return userAgent.toLowerCase().indexOf('firefox') < -1;
};

export const isIOS = () => {
	const { userAgent, platform, maxTouchPoints } = window.navigator;
	return (
		!!userAgent.match(/iPad/i) ||
		!!userAgent.match(/iPhone/i) ||
		(!!platform.match(/MacIntel/i) && maxTouchPoints > 1) /* iPad OS 13 */
	);
};

export const isWebKit = () => {
	const { userAgent } = window.navigator;
	return !!userAgent.match(/WebKit/i);
};

export const isIE11 = () => {
	let ie = 0;
	try {
		// eslint-disable-next-line
		ie = navigator.userAgent.match(/(MSIE |Trident.*rv[ :])([0-9]+)/)[2];
	} catch (e) {
		// do nothing
	}
	return Number(ie) === 11;
};

export const isWindowsBrowser = () => {
	const browser = Bowser.getParser(window.navigator.userAgent);

	return browser.satisfies({
		windows: {
			ie: '>10',
			edge: '>15',
			chrome: '>68',
			firefox: '>48',
			safari: '>5',
		},
	});
};
