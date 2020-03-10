/**
 * @function getNewsStreamsFromFeeds
 * Helper method to return News Streams
 *
 * @param {Array} feeds An array of feeds
 * @returns {Array} An array of items that match stream type
 */
function getNewsStreamsFromFeeds( feeds = [] ) {
	return feeds.filter( item => 'stream' === item.type && 0 < ( item.content || [] ).length ).map( item => item.content[0] );
}
