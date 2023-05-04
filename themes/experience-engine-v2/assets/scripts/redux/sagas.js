import { all } from 'redux-saga/effects';
import {
	watchSetPlayer,
	watchStart,
	watchStop,
	watchPause,
	watchResume,
	watchSetVolume,
	watchCuePointChange,
	watchSeekPosition,
	watchAdPlaybackStart,
	watchGamAdPlaybackStart,
	watchAdPlaybackComplete,
	watchAdPlaybackStop,
	watchInitPage,
	watchLoadingPage,
	watchLoadingPage2,
	watchLoadedPage,
	watchLoadedPartial,
	watchHideSplashScreen,
	watchPlay,
	watchEnd,
	watchAutoHideListenLive,
	watchHideListenLive,
	watchShowListenLive,
	watchLoadVimeo,
} from './sagas/';

/**
 * Root saga that watches for side effects.
 */
export default function* rootSaga() {
	yield all([
		watchSetPlayer(),
		watchStart(),
		watchPlay(),
		watchEnd(),
		watchStop(),
		watchPause(),
		watchResume(),
		watchSetVolume(),
		watchCuePointChange(),
		watchSeekPosition(),
		watchAdPlaybackStart(),
		watchGamAdPlaybackStart(),
		watchAdPlaybackComplete(),
		watchAdPlaybackStop(),
		watchInitPage(),
		watchLoadingPage(),
		watchLoadingPage2(),
		watchLoadedPage(),
		watchLoadedPartial(),
		watchHideSplashScreen(),
		watchAutoHideListenLive(),
		watchHideListenLive(),
		watchShowListenLive(),
		watchLoadVimeo(),
	]);
}
