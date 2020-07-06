import { call, takeLatest, select } from 'redux-saga/effects';
import { parseVolume, livePlayerLocalStorage } from '../../utilities';
import { ACTION_SET_VOLUME } from '../../actions/player';

/**
 * @function yieldSetVolume
 * Generator runs whenever ACTION_SET_VOLUME is dispatched
 * NOTE: Omny doesn't support sound provider, thus we can't change/control volume :(
 *
 * @param {Object} action dispatched action
 * @param {String} action.volume volume from action
 */
function* yieldSetVolume({ volume }) {
	// Get player from state
	const playerStore = yield select(({ player }) => player);

	// Destructure
	const { player, playerType } = playerStore;

	// Parse the volume from action payload
	const getVolume = parseVolume(volume);

	// Set volume percentage
	let setVolume = volume / 100;

	// If livePlayerLocalStorage
	if (
		livePlayerLocalStorage &&
		typeof livePlayerLocalStorage.setItem === 'function'
	) {
		yield call([livePlayerLocalStorage, 'setItem'], 'volume', getVolume);
	}

	// If mp3player
	if (playerType === 'mp3player') {
		player.volume = setVolume;

		// If tdplayer
	} else if (
		playerType === 'tdplayer' &&
		typeof player.setVolume === 'function'
	) {
		if (setVolume <= 0.1) {
			setVolume = 0.1;
		}
		yield call([player, 'setVolume'], setVolume);
	}
}

/**
 * @function watchSetVolume
 * Generator used to bind action and callback
 */
export default function* watchSetVolume() {
	yield takeLatest([ACTION_SET_VOLUME], yieldSetVolume);
}
