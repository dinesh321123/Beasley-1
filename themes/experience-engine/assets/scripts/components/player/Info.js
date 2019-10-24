import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { STATUSES } from '../../redux/actions/player';

import Progress from './Progress';

const STATUS_LABELS = {
	[STATUSES.LIVE_PAUSE]: 'Paused',
	[STATUSES.LIVE_PLAYING]: 'On Air',
	[STATUSES.LIVE_STOP]: 'Listen Live',
	[STATUSES.LIVE_FAILED]: 'Stream unavailable',
	[STATUSES.LIVE_BUFFERING]: 'Buffering...',
	[STATUSES.LIVE_CONNECTING]: 'Live stream connection in progress...',
	[STATUSES.LIVE_RECONNECTING]: 'Reconnecting live stream...',
	[STATUSES.STREAM_GEO_BLOCKED]: 'Sorry, this content is not available in your area',
	[STATUSES.STATION_NOT_FOUND]: 'Station not found',
};

class Info extends Component {

	static getCuePointInfo( cuePoint ) {
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

	renderAudio() {
		const self = this;
		const { cuePoint, time, duration, colors  } = self.props;
		const info = Info.getCuePointInfo( cuePoint );

		const textStyle = {
			color: colors['--brand-text-color'] || colors['--global-theme-secondary'],
		};

		return (
			<div className="controls-info" style={colors}>
				<p>
					<strong>{info[0] || ''}</strong>
					<span className="time -mobile -current">{Progress.format( time )}</span>
					<span className="time -mobile -total">{Progress.format( duration )}</span>
				</p>
				<p>{info[1] || ''}</p>
			</div>
		);
	}

	renderStation() {
		const self = this;
		const { station, streams, status, cuePoint, colors } = self.props;

		let info = STATUS_LABELS[status] || '';
		if ( 'LIVE_PLAYING' === status ) {
			const pointInfo = Info.getCuePointInfo( cuePoint );
			if ( pointInfo ) {
				info = pointInfo;
			}
		}

		const stream = streams.find( item => item.stream_mount_key === station );

		return (
			<div className="controls-info" style={colors}>
				<p>
					<strong>{stream ? stream.title : station}</strong>
					{'LIVE_PLAYING' === status && (
						<span className="live">Live</span>
					)}
				</p>
				<p>{info}</p>
			</div>
		);
	}

	render() {
		const self = this;
		const { station } = self.props;
		return station ? self.renderStation() : self.renderAudio();
	}

}

Info.propTypes = {
	station: PropTypes.string.isRequired,
	streams: PropTypes.arrayOf( PropTypes.object ).isRequired,
	status: PropTypes.string.isRequired,
	cuePoint: PropTypes.oneOfType( [PropTypes.object, PropTypes.bool] ).isRequired,
	time: PropTypes.number,
	duration: PropTypes.number,
};

function mapStateToProps( { player } ) {
	return {
		station: player.station,
		streams: player.streams,
		status: player.status,
		cuePoint: player.cuePoint,
		time: player.time,
		duration: player.duration,
	};
}

export default connect( mapStateToProps )( Info );
