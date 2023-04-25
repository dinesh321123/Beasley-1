// polyfills
import 'core-js/stable'; // used by babel-preset-env
import 'regenerator-runtime/runtime'; // used by babel-preset-env
import 'isomorphic-unfetch';
import 'intersection-observer';

import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import './library/geotargetly';
import '../styles/main.css';
import mParticle from '@mparticle/web-sdk';
import { closestPolyfill } from './library';

import createStore from './redux/store';
import App from './app';

closestPolyfill();

if (window.bbgiAnalyticsConfig?.mparticle_key) {
	console.log('Configuring mparticle in bundle');
	// Configures the SDK. Note the settings below for isDevelopmentMode
	// and logLevel.
	mParticle.init(
		window.bbgiAnalyticsConfig.mparticle_key,
		window.bbgiAnalyticsConfig.mParticleConfig,
	);
	window.mParticle = mParticle;
	console.log(
		'Done configuring mparticle in bundle, now initializing Beasley Analytics',
	);
	window.beasleyanalytics.initializeMParticle();
}

const root = document.createElement('div');
document.body.appendChild(root);

ReactDOM.render(
	<Provider store={createStore()}>
		<App />
	</Provider>,
	root,
);
