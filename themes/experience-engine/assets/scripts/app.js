import React, { Fragment } from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import createStore from './redux/store';

import ContentDispatcher from './modules/ContentDispatcher';
import ModalDispatcher from './modules/ModalDispatcher';
import LivePlayer from './modules/LivePlayer';
import PrimaryNav from './modules/PrimaryNav';
import UserNav from './modules/UserNav';
import SearchForm from './modules/SearchForm';

import '../styles/main.css';

const root = document.createElement( 'div' );
document.body.appendChild( root );

const app = (
	<Provider store={createStore()}>
		<Fragment>
			<ContentDispatcher />
			<ModalDispatcher />
			<LivePlayer />
			<PrimaryNav />
			<UserNav />
			<SearchForm />
		</Fragment>
	</Provider>
);

ReactDOM.render( app, root );
