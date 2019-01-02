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

import '../styles/main.css';

class Application extends PureComponent {

	constructor( props ) {
		super( props );

		this.observer = new Observable();
	}

	render() {
		return (
			<Fragment>
				<IntersectionObserverContext.Provider value={this.observer}>
					<ContentDispatcher />
					<ModalDispatcher />
					<LivePlayer />
					<PrimaryNav />
					<UserNav />
					<SearchForm />
				</IntersectionObserverContext.Provider>

				<BackToTop />
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
