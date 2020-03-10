import { call, select, takeLatest } from 'redux-saga/effects';
import { ACTION_PLAYER_END } from '../../actions/player';
import { lyticsTrack } from '../../utilities';

function* yieldEnd() {
	const playerStore = yield select( ( { player } ) => player );

	if ( 'tdplayer' === playerStore.playerType ) {
		const { liveStreamInterval = null } = window;

		if ( liveStreamInterval ) {
			yield call( [ window, 'clearInterval' ], liveStreamInterval );
		}
	} else if ( 'mp3player' === playerStore.playerType ) {
		const {
			trackType,
			duration,
			time,
			cuePoint,
			userInteraction,
		} = playerStore;

		let { inlineAudioInterval = null } = window;

		// Clear interval
		if( inlineAudioInterval ) {
			yield call( [ window, 'clearInterval' ], inlineAudioInterval );
		}

		// Checks then call lyticsTrack
		if (
			trackType &&
			'podcast' === trackType &&
			1 >= Math.abs( duration - time ) &&
			! userInteraction
		) {
			yield call( lyticsTrack, 'end', cuePoint );
		}
	}

}

export default function* watchEnd() {
	yield takeLatest( [ACTION_PLAYER_END], yieldEnd );
}
