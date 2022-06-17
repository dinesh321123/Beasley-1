import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { playStation } from '../../redux/actions/player';

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

	renderStations(stream, streams) {
		const stations = [];

		/* eslint-disable camelcase */
		streams
			.filter(s => s !== stream)
			.forEach(({ title, stream_call_letters, picture }) => {
				const { large, original } = picture || {};
				const { url } = large || original || {};

				let logo = false;
				if (url) {
					logo = <img src={url} alt={title} />;
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
