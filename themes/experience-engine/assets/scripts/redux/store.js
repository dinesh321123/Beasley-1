import { createStore, compose, applyMiddleware, combineReducers } from 'redux';
import thunk from 'redux-thunk';
import createSagaMiddleware from 'redux-saga';

import authReducer, {
	DEFAULT_STATE as AUTH_DEFAULT_STATE,
} from './reducers/auth';
import gaReducer, { DEFAULT_STATE as GA_DEFAULT_STATE } from './reducers/ga';
import modalReducer, {
	DEFAULT_STATE as MODAL_DEFAULT_STATE,
} from './reducers/modal';
import navigationReducer, {
	DEFAULT_STATE as NAVIGATION_DEFAULT_STATE,
} from './reducers/navigation';
import playerReducer, {
	DEFAULT_STATE as PLAYER_DEFAULT_STATE,
} from './reducers/player';
import screenReducer, {
	DEFAULT_STATE as SCREEN_DEFAULT_STATE,
} from './reducers/screen';
import rootSaga from './sagas';

export default function() {
	let composeEnhancers = compose;
	if (process.env.NODE_ENV !== 'production') {
		composeEnhancers =
			window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || composeEnhancers;
	}

	const rootReducer = combineReducers({
		auth: authReducer,
		ga: gaReducer,
		modal: modalReducer,
		navigation: navigationReducer,
		screen: screenReducer,
		// eslint-disable-next-line sort-keys
		player: playerReducer, // must go after screen reducer
	});

	const defaultState = {
		auth: AUTH_DEFAULT_STATE,
		ga: GA_DEFAULT_STATE,
		modal: MODAL_DEFAULT_STATE,
		navigation: NAVIGATION_DEFAULT_STATE,
		screen: SCREEN_DEFAULT_STATE,
		// eslint-disable-next-line sort-keys
		player: PLAYER_DEFAULT_STATE, // mimic rootReducer order
	};

	const sagaMiddleware = createSagaMiddleware();

	const store = createStore(
		rootReducer,
		defaultState,
		composeEnhancers(applyMiddleware(thunk, sagaMiddleware)),
	);

	sagaMiddleware.run(rootSaga);

	return store;
}
