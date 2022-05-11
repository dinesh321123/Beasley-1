// Import saga effects
import { put, takeLatest, select, call } from 'redux-saga/effects';

// Import action constant(s)
import { ACTION_AD_PLAYBACK_STOP } from '../../actions/player';
import { refreshDropdownAd, hideDropdownAd } from '../../actions/dropdownad';

function* hidePrerollShade() {
	const prerollWrapper = document.querySelector('div.preroll-wrapper.-active');
	if (prerollWrapper) {
		yield call([prerollWrapper.classList, 'remove'], '-active');
	}
}

function* breiflyShowPlayerDropdown() {
	const listenlivecontainer = document.getElementById('my-listen-dropdown2');
	const listenliveStyle = window.getComputedStyle(listenlivecontainer);
	if (listenliveStyle.display !== 'block') {
		yield put(refreshDropdownAd());
		yield call(hidePrerollShade);
		listenlivecontainer.style.display = 'block';
		const delay = ms => new Promise(res => setTimeout(res, ms));
		yield delay(3500);
		yield put(hideDropdownAd());
		listenlivecontainer.style.display = 'none';
	}
}

/**
 * @function yieldAdPlaybackStop
 * Runs whenever ACTION_AD_PLAYBACK_STOP is dispatched
 *
 * @param {Object} action dispatched action
 * @param {Object} action.payload payload from dispatch
 */
function* yieldAdPlaybackStop({ payload }) {
	// Destructure from payload
	const { actionType } = payload;

	// Player store from state
	const playerStore = yield select(({ player }) => player);

	// Destructure from playerStore
	const { adPlayback, station, player, playerType } = playerStore;

	// Update DOM
	yield call([document.body.classList, 'remove'], 'locked');

	// If the current player is a tdplayer
	if (playerType === 'tdplayer') {
		// If adPlayback and player.skipAd
		if (adPlayback && typeof player.skipAd === 'function') {
			yield call([player, 'skipAd']);
		}

		// If station and player.skipAd
		if (station && typeof player.play === 'function') {
			yield call([player, 'play'], {
				station,
				trackingParameters: { dist: 'beasleyweb' },
			});
		}
	}

	yield call(breiflyShowPlayerDropdown);

	// finalize dispatch
	yield put({ type: actionType });
}

/**
 * @function watchAdPlaybackStop
 * Watches for playback stop.
 */
export default function* watchAdPlaybackStop() {
	yield takeLatest([ACTION_AD_PLAYBACK_STOP], yieldAdPlaybackStop);
}
