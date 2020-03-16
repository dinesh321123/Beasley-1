/**
 * @function getNewsStreamsFromFeeds
 * Helper method to return News Streams
 *
 * @param {Array} feeds An array of feeds
 * @returns {Array} An array of items that match stream type
 */
export default function getNewsStreamsFromFeeds(feeds = []) {
	return feeds
		.filter(item => item.type === 'stream' && (item.content || []).length > 0)
		.map(item => item.content[0]);
}
