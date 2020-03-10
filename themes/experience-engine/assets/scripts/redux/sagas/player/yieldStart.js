import { call, takeLatest, select } from 'redux-saga/effects';
import {
	sendInlineAudioPlaying, sendLiveStreamPlaying,
} from '../../../library/google-analytics';
import {
	ACTION_AUDIO_START,
} from '../../actions/player';

/**
 * @function yieldStart
 * Generator runs whenever ACTION_AUDIO_START is dispatched
 */
function* yieldStart() {

	console.log( 'yieldStart' );

	const playerStore = yield select( ( {player} ) => player );

	// Get interval from global
	const interval = window.bbgiconfig.intervals.live_streaming;

	if ( 'mp3player' === playerStore.playerType ) {
		// Get inlineAudioInterval from window, default null
		let { inlineAudioInterval = null } = window;

		// If interval
		if (
			interval &&
		0 < interval
		) {

			// Clear if set
			if( inlineAudioInterval ) {
				yield call( [ window, 'clearInterval' ], inlineAudioInterval );
			}

			// Set inlineAudioInterval
			inlineAudioInterval = yield call(
				[ window, 'setInterval' ],
				function() {
					sendInlineAudioPlaying();
				},
				interval * 60 * 1000,
			);
		}
	} else if ( 'tdplayer' === playerStore.playerType ) {
		// Get liveStreamInterval from window, default null
		let { liveStreamInterval = null } = window;

		// If interval
		if (
			interval &&
			0 < interval
		) {

			// Clear if set
			if( liveStreamInterval ) {
				yield call( [ window, 'clearInterval' ], liveStreamInterval );
			}

			// Set liveStreamInterval
			liveStreamInterval = yield call(
				[ window, 'setInterval' ],
				function() {
					sendLiveStreamPlaying();
				},
				interval * 60 * 1000,
			);
		}
	}

}

/**
 * @function watchAudioStart
 * Generator used to bind action and callback
 */
export default function* watchStart() {
	yield takeLatest( [ACTION_AUDIO_START], yieldStart );
}
