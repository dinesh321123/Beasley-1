import livePlayerLocalStorage from './livePlayerLocalStorage';

/**
 * @function getInitialStation
 * Returns a matching stream if local storage
 * station value matches the stream.stream_call_letters
 *
 * @param {Array} streamsList Array of streams
 * @returns {String|Undefined} First match || undefined
 */
export default function getInitialStation(streamsList) {
	const station = livePlayerLocalStorage.getItem('station');
	return streamsList.find(stream => stream.stream_call_letters === station);
}
