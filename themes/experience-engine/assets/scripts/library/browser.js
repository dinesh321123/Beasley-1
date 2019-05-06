import Bowser from 'bowser';

export const isSafari = () => {
	const { userAgent } = window.navigator;
	return /^((?!chrome|android).)*safari/i.test( userAgent );
};

export const isChrome = () => {
	return !!window.chrome;
};

export const isFireFox = () => {
	const { userAgent } = window.navigator;
	return -1 > userAgent.toLowerCase().indexOf( 'firefox' );
};

export const isIOS = () => {
	const { userAgent } = window.navigator;
	return !!userAgent.match( /iPad/i ) || !!userAgent.match( /iPhone/i );
};

export const isWebKit = () => {
	const { userAgent } = window.navigator;
	return !!userAgent.match( /WebKit/i );
};

export const isWindowsBrowser = () => {
	const browser = Bowser.getParser( window.navigator.userAgent );

	return browser.satisfies( {
		windows: {
			'ie': '>10',
			'edge': '>15'
		}
	} );
};
