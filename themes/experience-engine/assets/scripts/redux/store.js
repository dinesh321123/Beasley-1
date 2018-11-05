import { createStore, compose, applyMiddleware, combineReducers } from 'redux';
import thunk from 'redux-thunk';

import playerReducer, { DEFAULT_STATE as PLAYER_DEFAULT_STATE } from './reducers/player';
import modalReducer, { DEFAULT_STATE as MODAL_DEFAULT_STATE } from './reducers/modal';
import screenReducer, { DEFAULT_STATE as SCREEN_DEFAULT_STATE } from './reducers/screen';

export default function() {
	const middleware = [thunk];

	let composeEnhancers = compose;
	if ( 'production' !== process.env.NODE_ENV ) {
		composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || composeEnhancers;
	}

	const rootReducer = combineReducers( {
		player: playerReducer,
		modal: modalReducer,
		screen: screenReducer,
	} );

	const defaultState = {
		player: PLAYER_DEFAULT_STATE,
		modal: MODAL_DEFAULT_STATE,
		screen: SCREEN_DEFAULT_STATE,
	};

	return createStore( rootReducer, defaultState, composeEnhancers( applyMiddleware( ...middleware ) ) );
}
