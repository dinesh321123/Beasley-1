import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { STATUSES } from '../redux/actions/player';
import ErrorBoundary from '../components/ErrorBoundary';
import { Stations } from '../components/player';

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
	getCuePointInfo(cuePoint) {
		const { station } = this.props;

		if (!cuePoint) {
			return false;
		}

		const info = [];
		const { artistName, cueTitle, type } = cuePoint;
		if (type === 'ad') {
			return false;
		}

		if (!station && cueTitle && cueTitle.length) {
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

	constructor(props) {
		super(props);

		this.container = document.getElementById('playing-now-info');
	}

	getAudioInfo() {
		const { cuePoint } = this.props;
		const info = this.getCuePointInfo(cuePoint);

		return this.getMockup(info[0] || '', info[1] || '');
	}

	getStationInfo() {
		const { station, streams, status, cuePoint } = this.props;

		let info = STATUS_LABELS[status] || '';
		if (status === 'LIVE_PLAYING') {
			const pointInfo = this.getCuePointInfo(cuePoint);
			if (pointInfo) {
				info = pointInfo;
			}
		}

		const stream = streams.find(item => item.stream_call_letters === station);

		return this.getMockup(stream ? stream.title : station, info);
	}

	getMockup(title, description) {
		return (
			<ErrorBoundary>
				<div className="on-air-list">
					<ul>
						<li>
							<strong>{title}</strong>
						</li>
						<li>{description}</li>
					</ul>
				</div>
				<Stations />
			</ErrorBoundary>
		);
	}

	render() {
		const { station } = this.props;
		const children = station ? this.getStationInfo() : this.getAudioInfo();
		/*
		<div className="add-links">
			<h3>Now Playing</h3>
			<p><a href="#">Mama Kin</a> &nbsp; | &nbsp; <a href="#">Aerosmith</a></p>
		</div>
		*/

		return ReactDOM.createPortal(children, this.container);
	}
}

Info.defaultProps = {
	station: '',
};

Info.propTypes = {
	colors: PropTypes.shape({
		'--global-theme-secondary': PropTypes.string,
		'--brand-button-color': PropTypes.string,
		'--brand-background-color': PropTypes.string,
		'--brand-text-color': PropTypes.string,
	}),
	station: PropTypes.string,
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