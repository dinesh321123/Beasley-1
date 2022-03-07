import { dispatchEvent } from './dom';

history.pushState = (f =>
	function pushState() {
		// eslint-disable-next-line prefer-rest-params
		const ret = f.apply(this, arguments);
		dispatchEvent('pushState');
		dispatchEvent('locationchange');
		return ret;
	})(history.pushState);

history.replaceState = (f =>
	function replaceState() {
		// eslint-disable-next-line prefer-rest-params
		const ret = f.apply(this, arguments);
		dispatchEvent('replaceState');
		return ret;
	})(history.replaceState);

window.addEventListener('popstate', () => {
	dispatchEvent('locationchange');
});

window.addEventListener('locationchange', () => {
	if (window.bbgiconfig.geotargetly && window.geotargetly) {
		try {
			window.geotargetly(document, 'script', 'style', 'head');
		} catch (e) {
			// no-op
		}
	}
});
