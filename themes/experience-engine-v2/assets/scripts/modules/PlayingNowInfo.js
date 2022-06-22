import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { STATUSES } from '../redux/actions/player';
import ErrorBoundary from '../components/ErrorBoundary';
import {
	PodcastScrubber,
	Stations,
	Volume,
	DropDownAd,
} from '../components/player';
import { isIOS } from '../library';
import PlayerButton from './PlayerButton';

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
	// Use Holder To Work Around Bug Where cueTitle can hold Podcast Title when stream plays.
	lastPodcastArtist = '';

	lastPodcastTitle = '';

	getCuePointInfo(cuePoint) {
		const { station } = this.props;

		if (!cuePoint) {
			return false;
		}

		const retval = [];
		const { artistName, cueTitle, type } = cuePoint;
		if (type === 'ad') {
			return false;
		}

		// Set Holders If A Podcast
		if (!station) {
			this.lastPodcastArtist = artistName;
			this.lastPodcastTitle = cueTitle;
		}

		const isStationCuePointHoldingPodcastInfo =
			!!station &&
			this.lastPodcastArtist === artistName &&
			this.lastPodcastTitle === cueTitle;

		if (!isStationCuePointHoldingPodcastInfo) {
			if (cueTitle && cueTitle.length) {
				retval.push(
					<span
						key="cue-title"
						className="cue-point-title"
						dangerouslySetInnerHTML={{
							__html: cueTitle.trim(),
						}}
					/>,
				);
			}
			if (artistName && artistName.length) {
				retval.push(
					<span
						key="cue-artist"
						className="cue-point-artist"
						dangerouslySetInnerHTML={{
							__html: artistName.trim(),
						}}
					/>,
				);
			}
		}

		return retval.length ? retval : false;
	}

	constructor(props) {
		super(props);

		this.container = document.getElementById('playing-now-info');
	}

	getAudioInfo() {
		const { cuePoint } = this.props;
		const info = this.getCuePointInfo(cuePoint);

		return info
			? this.getControlMarkup(info[0] || null, info[1] || null)
			: false;
	}

	getStationInfo() {
		const { station, streams, status, cuePoint } = this.props;

		let info = STATUS_LABELS[status] || null;
		if (status === 'LIVE_PLAYING') {
			const pointInfo = this.getCuePointInfo(cuePoint);
			if (pointInfo) {
				info = pointInfo;
			}
		}

		const stream = streams.find(item => item.stream_call_letters === station);

		return this.getControlMarkup(stream ? stream.title : station, info);
	}

	getControlMarkup(title, description) {
		const container = document.getElementById('player-button-div');
		const buttonsFillStyle = {};

		if (container) {
			let { customColors } = container.dataset;
			customColors = JSON.parse(customColors);
			buttonsFillStyle.fill =
				customColors['--brand-music-control-color'] ||
				customColors['--global-theme-secondary'];
			buttonsFillStyle.stroke =
				customColors['--brand-music-control-color'] ||
				customColors['--global-theme-secondary'];
		}

		const isIos = isIOS();
		const volumeControl = isIos ? null : <Volume colors={buttonsFillStyle} />;

		return (
			<ErrorBoundary>
				<div className="on-air-list ll-top-container">
					<ul>
						<li className="ll-title-with-play-btn">
							<PlayerButton inDropDown customTitle={title} />
						</li>
						<li>{description}</li>
					</ul>
					<div className="ll-volume-control">
						<div className="button-holder">{volumeControl}</div>
					</div>
				</div>
				<div className="top-progress-holder">
					<PodcastScrubber />
				</div>
				<Stations />
				<hr />
				<div
					className="on-air-list ll-menu-section full-width-menu"
					id="live-player-recently-played"
				/>
				<DropDownAd />
			</ErrorBoundary>
		);
	}

	render() {
		const { station } = this.props;
		const children = station ? this.getStationInfo() : this.getAudioInfo();
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
