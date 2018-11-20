import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { STATUSES } from '../../redux/actions/player';

const STATUS_LABELS = {
	[STATUSES.LIVE_PAUSE]: 'Paused',
	[STATUSES.LIVE_PLAYING]: 'On Air',
	[STATUSES.LIVE_STOP]: 'Disconnected',
	[STATUSES.LIVE_FAILED]: 'Stream unavailable',
	[STATUSES.LIVE_BUFFERING]: 'Buffering...',
	[STATUSES.LIVE_CONNECTING]: 'Live stream connection in progress...',
	[STATUSES.LIVE_RECONNECTING]: 'Reconnecting live stream...',
	[STATUSES.STREAM_GEO_BLOCKED]: 'Sorry, this content is not available in your area',
	[STATUSES.STATION_NOT_FOUND]: 'Station not found',
};

function getCuePointInfo( cuePoint ) {
	if ( !cuePoint ) {
		return false;
	}

	let info = [];
	const { artistName, cueTitle, type } = cuePoint;
	if ( 'ad' === type ) {
		return false;
	}

	if ( cueTitle && cueTitle.length ) {
		info.push( <span key="cue-title" className="cue-point-title">{cueTitle}</span> );
	}

	if ( artistName && artistName.length ) {
		info.push( <span key="cue-artist" className="cue-point-artist">{artistName}</span> );
	}

	return info.length ? info : false;
}

function Info( { station, streams, status, cuePoint } ) {
	let info = STATUS_LABELS[status] || '';
	if ( 'LIVE_PLAYING' === status ) {
		const pointInfo = getCuePointInfo( cuePoint );
		if ( pointInfo ) {
			info = pointInfo;
		}
	}

	const stream = streams.find( item => item.stream_call_letters === station );

	return (
		<div>
			<b>{stream ? stream.title : station}</b>
			<div>{info}</div>
		</div>
	);
}

Info.propTypes = {
	station: PropTypes.string.isRequired,
	streams: PropTypes.arrayOf( PropTypes.object ).isRequired,
	status: PropTypes.string.isRequired,
	cuePoint: PropTypes.oneOfType( [PropTypes.object, PropTypes.bool] ).isRequired,
};

function mapStateToProps( { player } ) {
	return {
		station: player.station,
		streams: player.streams,
		status: player.status,
		cuePoint: player.cuePoint,
	};
}

export default connect( mapStateToProps )( Info );
