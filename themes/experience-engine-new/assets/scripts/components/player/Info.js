import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { STATUSES } from '../../redux/actions/player';

const STATUS_LABELS = {
	[STATUSES.LIVE_PAUSE]: 'Paused',
	[STATUSES.LIVE_PLAYING]: 'On Air',
	[STATUSES.LIVE_STOP]: 'Listen Live',
	[STATUSES.LIVE_FAILED]: 'Stream unavailable',
	[STATUSES.LIVE_BUFFERING]: 'Buffering...',
	[STATUSES.LIVE_CONNECTING]: 'Live stream connection in progress...',
	[STATUSES.LIVE_RECONNECTING]: 'Reconnecting live stream...',
	[STATUSES.STREAM_GEO_BLOCKED]:
		'Sorry, this content is not available in your area',
	[STATUSES.STATION_NOT_FOUND]: 'Station not found',
};

class Info extends Component {
	static getCuePointInfo(cuePoint) {
		if (!cuePoint) {
			return false;
		}

		const info = [];
		const { artistName, cueTitle, type } = cuePoint;
		if (type === 'ad') {
			return false;
		}

		if (cueTitle && cueTitle.length) {
			info.push(
				<span key="cue-title" className="cue-point-title">
					{cueTitle}
				</span>,
			);
		}

		if (artistName && artistName.length) {
			info.push(
				<span key="cue-artist" className="cue-point-artist">
					{artistName}
				</span>,
			);
		}

		return info.length ? info : false;
	}

	renderAudio() {
		const { cuePoint, colors } = this.props;
		const info = Info.getCuePointInfo(cuePoint);

		return (
			<div className="controls-info" style={colors}>
				<p>
					<strong>{info[0] || ''}</strong>
				</p>
				<p>{info[1] || ''}</p>
			</div>
		);
	}

	renderStation() {
		const { station, streams, status, cuePoint, colors } = this.props;

		let info = STATUS_LABELS[status] || '';
		if (status === 'LIVE_PLAYING') {
			const pointInfo = Info.getCuePointInfo(cuePoint);
			if (pointInfo) {
				info = pointInfo;
			}
		}

		const stream = streams.find(item => item.stream_call_letters === station);

		return (
			<div className="controls-info" style={colors}>
				<p>
					<strong>{stream ? stream.title : station}</strong>
					{status === 'LIVE_PLAYING' && <span className="live">Live</span>}
				</p>
				<p>{info}</p>
			</div>
		);
	}

	render() {
		const { station } = this.props;
		return station ? this.renderStation() : this.renderAudio();
	}
}

Info.propTypes = {
	colors: PropTypes.shape({
		'--global-theme-secondary': PropTypes.string,
		'--brand-button-color': PropTypes.string,
		'--brand-background-color': PropTypes.string,
		'--brand-text-color': PropTypes.string,
	}),
	station: PropTypes.string.isRequired,
	streams: PropTypes.arrayOf(PropTypes.object).isRequired,
	status: PropTypes.string.isRequired,
	cuePoint: PropTypes.oneOfType([PropTypes.object, PropTypes.bool]).isRequired,
};

Info.defaultProps = {
	colors: {},
};

function mapStateToProps({ player }) {
	return {
		station: player.station,
		streams: player.streams,
		status: player.status,
		cuePoint: player.cuePoint,
		time: player.time,
		duration: player.duration,
	};
}

export default connect(mapStateToProps)(Info);
