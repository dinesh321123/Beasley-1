import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

const STATUSES = {
	LIVE_PAUSE: 'Paused',
	LIVE_PLAYING: 'On Air',
	LIVE_STOP: 'Disconnected',
	LIVE_FAILED: 'Stream unavailable',
	LIVE_BUFFERING: 'Buffering...',
	LIVE_CONNECTING: 'Live stream connection in progress...',
	LIVE_RECONNECTING: 'Reconnecting live stream...',
	STREAM_GEO_BLOCKED: 'Sorry, this content is not available in your area',
	STATION_NOT_FOUND: 'Station not found',
};

const getCuePointInfo = ( cuePoint ) => {
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
};

const Info = ( { station, status, cuePoint } ) => {
	let info = STATUSES[status] || '';
	if ( 'LIVE_PLAYING' === status ) {
		const pointInfo = getCuePointInfo( cuePoint );
		if ( pointInfo ) {
			info = pointInfo;
		}
	}

	return (
		<div>
			<b>{station}</b>
			<div>{info}</div>
		</div>
	);
};

Info.propTypes = {
	station: PropTypes.string.isRequired,
	status: PropTypes.string.isRequired,
	cuePoint: PropTypes.oneOfType( [PropTypes.object, PropTypes.bool] ).isRequired,
};

const mapStateToProps = ( { player } ) => ( {
	station: player.station,
	status: player.status,
	cuePoint: player.cuePoint,
} );

export default connect( mapStateToProps )( Info );
