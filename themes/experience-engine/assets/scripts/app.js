import React, { useEffect } from 'react';

import { IntersectionObserverProvider } from './context';
import {
	ContentDispatcher,
	ModalDispatcher,
	LivePlayer,
	BottomAdhesion,
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
	const { '--is_V20': isV20 } = window.bbgiconfig.cssvars.variables;
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
				<LivePlayer />
				{isV20 ? <BottomAdhesion /> : null}
				<PrimaryNav />
				<UserNav suppressUserCheck={false} />
				<SearchForm />
				<BackToTop />
			</ErrorBoundary>
		</IntersectionObserverProvider>
	);
};

export default App;
