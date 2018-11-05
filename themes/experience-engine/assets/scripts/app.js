import React, { PureComponent, Fragment } from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import createStore from './redux/store';

import ContentDispatcher from './modules/ContentDispatcher';
import ModalDispatcher from './modules/ModalDispatcher';
import LivePlayer from './modules/LivePlayer';
import PrimaryNav from './modules/PrimaryNav';
import UserNav from './modules/UserNav';

import '../styles/main.css';

class Application extends PureComponent {

	componentDidMount() {
		const splashScreen = document.getElementById( 'splash-screen' );
		if ( splashScreen ) {
			splashScreen.parentNode.removeChild( splashScreen );
		}
	}

	render() {
		return (
			<Fragment>
				<ContentDispatcher />
				<ModalDispatcher />
				<LivePlayer />
				<PrimaryNav />
				<UserNav />
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
