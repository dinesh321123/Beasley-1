// All Sagas watches as Exports
// Sagas are used to avoid side effects in Redux

// Player
export { default as watchSetPlayer } from './player/yieldSetPlayer';
export { default as watchAdPlaybackStop } from './player/yieldAdPlaybackStop';
export { default as watchStop } from './player/yieldStop';
export { default as watchEnd } from './player/yieldEnd';
export { default as watchPlay } from './player/yieldPlay';
export { default as watchPause } from './player/yieldPause';
export { default as watchStart } from './player/yieldStart';
export { default as watchResume } from './player/yieldResume';
export { default as watchSetVolume } from './player/yieldSetVolume';
export { default as watchCuePointChange } from './player/yieldCuePointChange';
export { default as watchSeekPosition } from './player/yieldSeekPosition';
export { default as watchAdPlaybackStart } from './player/yieldAdPlaybackStart';
export { default as watchAdPlaybackComplete } from './player/yieldAdPlaybackComplete';
export { default as watchGamAdPlaybackStart } from './player/yieldGamAdPlaybackStart';

// Screen
export { default as watchInitPage } from './screen/yieldInitPage';
export { default as watchLoadingPage } from './screen/yieldLoadingPage';
export { default as watchLoadedPage } from './screen/yieldLoadedPage';
export { default as watchLoadedPartial } from './screen/yieldLoadedPartial';
export { default as watchHideSplashScreen } from './screen/yieldHideSplashScreen';
