import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import cssVars from 'css-vars-ponyfill';

import createStore from './redux/store';

import IntersectionObserverContext, { Observable } from './context/intersection-observer';

import ContentDispatcher from './modules/ContentDispatcher';
import ModalDispatcher from './modules/ModalDispatcher';
import LivePlayer from './modules/LivePlayer';
import PrimaryNav from './modules/PrimaryNav';
import UserNav from './modules/UserNav';
import SearchForm from './modules/SearchForm';
import LiveCta from './modules/LiveCta';
import FeedCta from './modules/FeedCta';

import '../styles/main.css';

let theme = {};
const { bbgiconfig } = window;
const { themeData } = bbgiconfig;

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

class Application extends PureComponent {

	constructor( props ) {
		super( props );

		this.observer = new Observable();
	}

	render() {
		return (
			<IntersectionObserverContext.Provider value={this.observer}>
				<ContentDispatcher />
				<ModalDispatcher />
				<LivePlayer />
				<PrimaryNav />
				<UserNav />
				<LiveCta />
				<FeedCta />
				<SearchForm />
			</IntersectionObserverContext.Provider>
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
