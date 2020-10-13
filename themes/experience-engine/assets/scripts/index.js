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
import { closestPolyfill } from './library';

import createStore from './redux/store';
import App from './app';

closestPolyfill();

const root = document.createElement('div');
document.body.appendChild(root);

ReactDOM.render(
	<Provider store={createStore()}>
		<App />
	</Provider>,
	root,
);
