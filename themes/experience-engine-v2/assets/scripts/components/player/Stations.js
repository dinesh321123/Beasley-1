import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import dayjs from 'dayjs';
import { playStation } from '../../redux/actions/player';

const utc = require('dayjs/plugin/utc');
const timezone = require('dayjs/plugin/timezone');

const config = window.bbgiconfig;

class Stations extends Component {
	constructor(props) {
		super(props);
		this.stationModalRef = React.createRef();
	}

	handlePlayClick(station) {
		this.props.play(station);
	}

	shouldRender(stream, streams) {
		if (!stream) {
			return true;
		}

		if (streams && streams.length > 1) {
			return true;
		}

		return false;
	}

	checkTimeZone() {
		const tz = config.timezone_string;
		if (!Intl || !Intl.DateTimeFormat().resolvedOptions().timeZone) {
			return tz;
		}

		try {
			Intl.DateTimeFormat(undefined, { timeZone: tz });
			return tz;
		} catch (ex) {
			return false;
		}
	}

	renderStations(stream, streams) {
		const stations = [];
		const days = [
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
		];
		dayjs.extend(utc);
		dayjs.extend(timezone);
		const timezoneString = this.checkTimeZone();
		if (timezoneString) {
			dayjs.tz.setDefault(timezoneString);
		}

		const dayName = days[dayjs.tz().day()].toLowerCase();
		const dayTime = dayjs.tz();

		/* eslint-disable camelcase */
		streams
			.filter(s => s !== stream)
			.forEach(({ title, stream_call_letters, picture, secondStreamTime }) => {
				const { large, original } = picture || {};
				const { url } = large || original || {};
				let { startTime, endTime } = '';
				let logo = false;
				if (url) {
					logo = <img src={url} alt={title} />;
				}
				if (!(dayName in secondStreamTime)) {
					return;
				}
				if (secondStreamTime[dayName].startTime) {
					const temp = secondStreamTime[dayName].startTime.split(':');
					startTime = dayjs
						.tz()
						.set('hour', temp[0])
						.set('minute', temp[1]);
				}

				if (secondStreamTime[dayName].endTime) {
					const temp = secondStreamTime[dayName].endTime.split(':');
					endTime = dayjs
						.tz()
						.set('hour', temp[0])
						.set('minute', temp[1]);
				}
				if (dayTime < startTime || dayTime > endTime) {
					return;
				}

				stations.push(
					<div key={stream_call_letters}>
						<button
							type="button"
							className="control-station-button"
							onClick={this.handlePlayClick.bind(this, stream_call_letters)}
						>
							{logo}
							<span>{title}</span>
						</button>
					</div>,
				);
			});
		/* eslint-enable */

		return stations;
	}

	render() {
		const { stream, streams } = this.props;

		if (!this.shouldRender(stream, streams)) {
			return false;
		}

		return this.renderStations(stream, streams);
	}
}

Stations.propTypes = {
	play: PropTypes.func.isRequired,
	stream: PropTypes.oneOfType([PropTypes.bool, PropTypes.object]),
	streams: PropTypes.arrayOf(PropTypes.object).isRequired,
};

Stations.defaultProps = {
	stream: false,
};

function mapStateToProps({ player }) {
	const { streams, station } = player;

	return {
		stream: streams.find(item => item.stream_call_letters === station),
		streams,
	};
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators({ play: playStation }, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(Stations);
