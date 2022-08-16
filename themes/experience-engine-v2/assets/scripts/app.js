import React, { useEffect } from 'react';
import { IntersectionObserverProvider } from './context';
import {
	TopScrollingAd,
	DropDownAd,
	ContentDispatcher,
	ModalDispatcher,
	BottomAdhesionAd,
	PlayerButton,
	PlayingNowInfo,
	PrimaryNav,
	UserNav,
	SearchForm,
} from './modules';
import BackToTop from './components/BackToTop';
import ErrorBoundary from './components/ErrorBoundary';
import { isIOS, isSafari, isWindowsBrowser } from './library';
import ScreenInactive from './components/ScreenInactive';

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
				<ScreenInactive />
				<TopScrollingAd />
				<PlayerButton />
				<PlayingNowInfo />
				<DropDownAd />
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
