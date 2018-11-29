import React, { Fragment } from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import cssVars from 'css-vars-ponyfill';

import createStore from './redux/store';

import ContentDispatcher from './modules/ContentDispatcher';
import ModalDispatcher from './modules/ModalDispatcher';
import LivePlayer from './modules/LivePlayer';
import PrimaryNav from './modules/PrimaryNav';
import UserNav from './modules/UserNav';
import SearchForm from './modules/SearchForm';

import '../styles/main.css';

let theme = {};
const { bbgiconfig } = window;
const { themeData } = bbgiconfig;
const root = document.createElement( 'div' );
document.body.appendChild( root );

if ( '-dark' === themeData.theme ) {
	theme = {
		'--global-theme-primary': '#1a1a1a',
		'--global-theme-secondary': '#282828',

		'--global-theme-font-primary': 'var(--global-white)',
		'--global-theme-font-secondary': '#a5a5a5',
		'--global-theme-font-tertiary': 'var(--global-dove-gray)',
	};
}

const brand = {
	'--brand-primary': themeData.brand.primary,
	'--brand-secondary': themeData.brand.secondary,
	'--brand-tertiary': themeData.brand.tertiary,
};

const fullTheme = { ...theme, ...brand };

cssVars( {
	variables: fullTheme,
} );


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
