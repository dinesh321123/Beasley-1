import '../styles/main.css';

import es6promise from 'es6-promise';
import 'isomorphic-unfetch';

import React, { PureComponent, Fragment } from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import createStore from './redux/store';

import IntersectionObserverContext, {
	Observable,
} from './context/intersection-observer';

import ContentDispatcher from './modules/ContentDispatcher';
import ModalDispatcher from './modules/ModalDispatcher';
import LivePlayer from './modules/LivePlayer';
import PrimaryNav from './modules/PrimaryNav';
import UserNav from './modules/UserNav';
import SearchForm from './modules/SearchForm';
import BackToTop from './components/BackToTop';

import ErrorBoundary from './components/ErrorBoundary';
import { isSafari, isWindowsBrowser } from './library/browser';
import './library/geotargetly';
import './polyfills/closest';

es6promise.polyfill();


class Application extends PureComponent {
	constructor( props ) {
		super( props );
		this.observer = new Observable();
	}

	componentDidMount() {
		if ( isSafari() ) {
			document.body.classList.add( 'is-safari' );
		} else if ( isWindowsBrowser() ) {
			document.body.classList.add( 'is-windows' );
		}
	}

	render() {
		return (
			<Fragment>
				<IntersectionObserverContext.Provider value={this.observer}>
					<ErrorBoundary>
						<ContentDispatcher />
					</ErrorBoundary>
					<ErrorBoundary>
						<ModalDispatcher />
					</ErrorBoundary>
					<ErrorBoundary>
						<LivePlayer />
					</ErrorBoundary>
					<ErrorBoundary>
						<PrimaryNav />
					</ErrorBoundary>
					<ErrorBoundary>
						<UserNav suppressUserCheck={false} />
					</ErrorBoundary>
					<ErrorBoundary>
						<SearchForm />
					</ErrorBoundary>
				</IntersectionObserverContext.Provider>

				<ErrorBoundary>
					<BackToTop />
				</ErrorBoundary>
			</Fragment>
		);
	}
}

const root = document.createElement( 'div' );
document.body.appendChild( root );

const app = (
	<Provider store={createStore()}>
		<Application />
	</Provider>
);

ReactDOM.render( app, root );
