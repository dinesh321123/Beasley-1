export default function createMParticleMediaFields(
	playerType,
	stream,
	payload, // Podcast will have: src, cueTitle, artistName, trackType
) {
	const streamParams = {
		content_id: window.createUUID(),
		content_type: 'Audio',
	};

	const isLiveStream = playerType === 'tdplayer';
	if (isLiveStream) {
		streamParams.stream_type = 'LiveStream'; // OnDemand, Live, Linear, Podcast, Audiobook
		streamParams.content_duration = 1000 * 60 * 60 * 24; // Default to 1 day
		streamParams.content_title = stream?.title || '';
		streamParams.content_asset_id = stream?.stream_tap_id || '';
		streamParams.content_network = stream?.stream_cmod_domain || '';
		streamParams.stream_call_letters = stream?.stream_call_letters || '';
		streamParams.primary_category =
			window.bbgiconfig?.publisher?.genre?.toString() || '';
		streamParams.primary_category_id = '?LiveStreamCategoryID?';
		streamParams.show_name = '?LiveStreamShowName?';
		streamParams.show_id = '?LiveStreamShowID?';
		streamParams.content_daypart = '?LiveStreamContentDayPart?';
		streamParams.is_primary_stream = 'True';
	} else {
		streamParams.stream_type = 'OnDemand';
		streamParams.content_duration = 1000 * 60 * 60 * 24; // Default to 1 day
		streamParams.content_title = payload?.cueTitle || '';
		streamParams.content_asset_id = payload?.src || '';
		streamParams.content_network = '?PodcastNetwork?';
		streamParams.stream_call_letters = '?PodcastCallLetters?';
		streamParams.primary_category = '?PodcastCategory?';
		streamParams.primary_category_id = '?PodcastCategoryID?';
		streamParams.show_name = payload?.artistName || '';
		streamParams.show_id = '?PodcastShowID?';
		streamParams.content_daypart = '?PodcastContentDayPart?';
		streamParams.is_primary_stream = '?PodcastIsPrimaryStream?';
	}

	// Load Up Beasley Analytics With All Media Params
	Object.keys(streamParams).forEach(key => {
		window.beasleyanalytics.setMediaAnalyticsForMParticle(
			key,
			streamParams[key],
		);
	});
}
