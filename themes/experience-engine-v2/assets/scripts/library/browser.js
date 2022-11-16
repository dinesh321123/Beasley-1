import Bowser from 'bowser';

export const isAndroid = () => {
	const { userAgent } = window.navigator;
	return !!userAgent && userAgent.toLowerCase().indexOf('android') > -1;
};

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

export const isIPhone = () => {
	const { userAgent } = window.navigator;
	return !!userAgent.match(/iPhone/i);
};

export const isIPad = () => {
	const { userAgent } = window.navigator;

	return (
		!!userAgent.match(/iPad/i) ||
		(!!userAgent.match(/Mac/i) &&
			'ontouchend' in document) /* iPad OS 13+ in default desktop mode */
	);
};

export const isIOS = () => {
	return isIPhone() || isIPad();
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
