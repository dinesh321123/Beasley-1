import React, { useEffect } from 'react';

import IntersectionObserverProvider from './context/intersection-observer';
import ContentDispatcher from './modules/ContentDispatcher';
import ModalDispatcher from './modules/ModalDispatcher';
import LivePlayer from './modules/LivePlayer';
import PrimaryNav from './modules/PrimaryNav';
import UserNav from './modules/UserNav';
import SearchForm from './modules/SearchForm';
import BackToTop from './components/BackToTop';
import ErrorBoundary from './components/ErrorBoundary';
import { isSafari, isWindowsBrowser } from './library/browser';

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
