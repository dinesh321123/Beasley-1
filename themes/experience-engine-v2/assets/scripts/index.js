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

console.log('configing mparticle');
if (window.bbgiAnalyticsConfig.mparticle_key) {
	// Configures the SDK. Note the settings below for isDevelopmentMode
	// and logLevel.
	const mParticleConfig = {
		isDevelopmentMode: true,
		logLevel: 'verbose',
		dataPlan: {
			planId: 'beasley_web',
			planVersion: 1,
		},
	};
	mParticle.init(window.bbgiAnalyticsConfig.mparticle_key, mParticleConfig);
	window.mParticle = mParticle;
	window.beasleyanalytics.initializeMParticle();
}
console.log('done configing mparticle');

const root = document.createElement('div');
document.body.appendChild(root);

ReactDOM.render(
	<Provider store={createStore()}>
		<App />
	</Provider>,
	root,
);
