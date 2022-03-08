import React, { useEffect } from 'react';

import { IntersectionObserverProvider } from './context';
import {
	ContentDispatcher,
	ModalDispatcher,
	BottomAdhesionAd,
	PrimaryNav,
	UserNav,
	SearchForm,
} from './modules';
import BackToTop from './components/BackToTop';
import ErrorBoundary from './components/ErrorBoundary';
import { isIOS, isSafari, isWindowsBrowser } from './library';

/**
 * The App's entry point.
 */
const App = () => {
	useEffect(() => {
		if (isSafari()) {
			document.body.classList.add('is-safari');
			if (isIOS()) {
				document.body.classList.add('is-IOS');
			}
		} else if (isWindowsBrowser()) {
			document.body.classList.add('is-windows');
		}
	}, []);

	return (
		<IntersectionObserverProvider>
			<ErrorBoundary>
				<ContentDispatcher />
				<ModalDispatcher />
				<BottomAdhesionAd />
				<PrimaryNav />
				<UserNav suppressUserCheck={false} />
				<SearchForm />
				<BackToTop />
			</ErrorBoundary>
		</IntersectionObserverProvider>
	);
};

export default App;
