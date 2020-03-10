import { select, takeLatest } from 'redux-saga/effects';
import { ACTION_PLAYER_STOP } from '../../actions/player';

function* yieldStop( ) {
	const { player, playerType } = yield select( ( { player } ) => player );

	// If no player or type, abandon
	if (
		!player ||
		!playerType
	) {
		return;
	}

	// If mp3player
	if ( 'mp3player' === playerType ) {
		player.pause();
	}

	// If omnyplayer
	if ( 'omnyplayer' === playerType ) {
		player.off( 'ready' );
		player.off( 'play' );
		player.off( 'pause' );
		player.off( 'ended' );
		player.off( 'timeupdate' );
		player.pause();

		// TODO: needs testing here
		player.elem.parentNode.removeChild( player.elem );
	}

	// If tdplayer
	if ( 'tdplayer' === playerType ) {
		player.stop();
		player.skipAd();
	}
}

export default function* watchStop() {
	yield takeLatest( [ACTION_PLAYER_STOP], yieldStop );
}
