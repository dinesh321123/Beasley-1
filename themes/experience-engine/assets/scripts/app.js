import React, { useEffect } from 'react';

import { IntersectionObserverProvider } from './context';
import {
	ContentDispatcher,
	ModalDispatcher,
	LivePlayer,
	PrimaryNav,
	UserNav,
	SearchForm,
} from './modules';
import BackToTop from './components/BackToTop';
import ErrorBoundary from './components/ErrorBoundary';
import { isSafari, isWindowsBrowser } from './library';

/**
 * The App's entry point.
 */
const App = () => {
	useEffect(() => {
		if (isSafari()) {
			document.body.classList.add('is-safari');
		} else if (isWindowsBrowser()) {
			document.body.classList.add('is-windows');
		}
	}, []);

	return (
		<IntersectionObserverProvider>
			<ErrorBoundary>
				<ContentDispatcher />
				<ModalDispatcher />
				<LivePlayer />
				<PrimaryNav />
				<UserNav suppressUserCheck={false} />
				<SearchForm />
				<BackToTop />
			</ErrorBoundary>
		</IntersectionObserverProvider>
	);
};

export default App;
