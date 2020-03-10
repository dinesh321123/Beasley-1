import { call, takeLatest, select } from 'redux-saga/effects';
import {
	ACTION_SEEK_POSITION,
} from '../../actions/player';

/**
 * @function yieldSeekPosition
 * Generator runs whenever ACTION_SEEK_POSITION is dispatched
 *
 * @param {Object} action Dispathed action
 * @param {Object} action.position Position from dispatched action
 */
function* yieldSeekPosition( { position } ) {
	// Get player from state
	const playerStore = yield select( ( { player } ) => player );

	// Destructure player and type
	const {
		player,
		playerType,
	} = playerStore;

	// If mp3player
	if ( 'mp3player' === playerType ) {
		player.currentTime = position;

	// If omnyplayer
	} else if ( 'omnyplayer' === playerType ) {
		yield call( [ player, 'setCurrentTime' ], position );
	}

}

/**
 * @function watchSeekPosition
 * Generator used to bind action and callback
 */
export default function* watchSetVolume() {
	yield takeLatest( [ACTION_SEEK_POSITION], yieldSeekPosition );
}
