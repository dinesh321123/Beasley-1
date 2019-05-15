import { createStore, compose, applyMiddleware, combineReducers } from 'redux';
import thunk from 'redux-thunk';

import authReducer, {
	DEFAULT_STATE as AUTH_DEFAULT_STATE
} from './reducers/auth';
import modalReducer, {
	DEFAULT_STATE as MODAL_DEFAULT_STATE
} from './reducers/modal';
import navigationReducer, {
	DEFAULT_STATE as NAVIGATION_DEFAULT_STATE
} from './reducers/navigation';
import playerReducer, {
	DEFAULT_STATE as PLAYER_DEFAULT_STATE
} from './reducers/player';
import screenReducer, {
	DEFAULT_STATE as SCREEN_DEFAULT_STATE
} from './reducers/screen';

export default function() {
	const middleware = [thunk];

	let composeEnhancers = compose;
	if ( 'production' !== process.env.NODE_ENV ) {
		composeEnhancers =
			window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || composeEnhancers;
	}

	const rootReducer = combineReducers( {
		auth: authReducer,
		modal: modalReducer,
		navigation: navigationReducer,
		screen: screenReducer,
		player: playerReducer // must go after screen reducer
	} );

	const defaultState = {
		auth: AUTH_DEFAULT_STATE,
		modal: MODAL_DEFAULT_STATE,
		navigation: NAVIGATION_DEFAULT_STATE,
		screen: SCREEN_DEFAULT_STATE,
		player: PLAYER_DEFAULT_STATE // mimic rootReducer order
	};

	return createStore(
		rootReducer,
		defaultState,
		composeEnhancers( applyMiddleware( ...middleware ) )
	);
}
