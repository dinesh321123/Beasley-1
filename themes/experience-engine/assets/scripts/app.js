import '../styles/main.css';

import React, { PureComponent, Fragment } from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import createStore from './redux/store';

import IntersectionObserverContext, { Observable } from './context/intersection-observer';

import ContentDispatcher from './modules/ContentDispatcher';
import ModalDispatcher from './modules/ModalDispatcher';
import LivePlayer from './modules/LivePlayer';
import PrimaryNav from './modules/PrimaryNav';
import UserNav from './modules/UserNav';
import SearchForm from './modules/SearchForm';
import BackToTop from './components/BackToTop';

import ErrorBoundary from './components/ErrorBoundary';
import { isSafari } from './library/browser';

class Application extends PureComponent {

	constructor( props ) {
		super( props );

		this.observer = new Observable();
	}

	render() {

		if( isSafari() ) {
			document.body.classList.add( 'is-safari' );
		}

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
						<UserNav />
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
