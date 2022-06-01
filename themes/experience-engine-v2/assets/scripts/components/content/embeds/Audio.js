import React, { Component } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import PropTypes from 'prop-types';

import * as actions from '../../../redux/actions/player';
import Controls from '../../player/Controls';
import { sendOpenLiveStreamDD } from '../../../library';
import {
	hideDropdownAd,
	refreshDropdownAd,
} from '../../../redux/actions/dropdownad';

class AudioEmbed extends Component {
	constructor(props) {
		super(props);

		this.onPlayClick = this.handlePlayClick.bind(this);
	}

	getTitle() {
		const { src, title, sources } = this.props;
		const extractTitle = url =>
			url
				.split('/')
				.pop() // take file name from URL
				.split('?')
				.shift() // drop query parameters
				.split('.')
				.shift() // drop file extension
				.split('_')
				.join(' '); // replace underscores with white spaces

		if (title) {
			return title;
		}

		if (src) {
			return extractTitle(src);
		}

		if (sources) {
			const keys = Object.keys(sources);
			if (keys && keys.length) {
				return extractTitle(keys.shift());
			}
		}

		return false;
	}

	getPlayableSource() {
		const { src, sources } = this.props;
		const urls = Object.keys(sources);
		const audio = document.createElement('audio');

		let maybe = false;
		for (let i = 0, len = urls.length; i < len; i++) {
			const url = urls[i];
			const playable = audio.canPlayType(sources[url]);
			if (playable === 'probably') {
				return url;
			}

			if (playable === 'maybe' && maybe === false) {
				maybe = url;
			}
		}

		if (maybe) {
			return maybe;
		}

		return src;
	}

	handlePlayClick() {
		const {
			omny,
			title,
			author,
			playAudio,
			playOmny,
			tracktype,
			refreshDropdownAd,
			hideDropdownAd,
		} = this.props;
		const src = this.getPlayableSource();

		if (omny) {
			playOmny(src, title, author, tracktype);
		} else {
			playAudio(src, this.getTitle(), author, tracktype);
		}

		const listenlivecontainer = document.getElementById('my-listen-dropdown2');
		const listenliveStyle = window.getComputedStyle(listenlivecontainer);

		if (listenliveStyle.display !== 'block') {
			refreshDropdownAd();
			listenlivecontainer.style.display = 'block';
			sendOpenLiveStreamDD(true);

			setTimeout(() => {
				hideDropdownAd();
				listenlivecontainer.style.display = 'none';
			}, 3500);
		}
	}

	getStatus() {
		const { audio, status } = this.props;
		const src = this.getPlayableSource();

		return audio === src ? status : actions.STATUSES.LIVE_STOP;
	}

	render() {
		const { pause, resume, title } = this.props;

		return (
			<>
				<Controls
					status={this.getStatus()}
					title={title}
					play={this.onPlayClick}
					pause={pause}
					resume={resume}
				/>
			</>
		);
	}
}

AudioEmbed.propTypes = {
	audio: PropTypes.string.isRequired,
	status: PropTypes.string.isRequired,
	src: PropTypes.string.isRequired,
	omny: PropTypes.bool,
	title: PropTypes.string,
	author: PropTypes.string,
	sources: PropTypes.shape({}),
	playAudio: PropTypes.func.isRequired,
	playOmny: PropTypes.func.isRequired,
	pause: PropTypes.func.isRequired,
	resume: PropTypes.func.isRequired,
	tracktype: PropTypes.string.isRequired,
	refreshDropdownAd: PropTypes.func.isRequired,
	hideDropdownAd: PropTypes.func.isRequired,
};

AudioEmbed.defaultProps = {
	omny: false,
	title: '',
	author: '',
	sources: {},
};

function mapStateToProps({ player }) {
	return {
		audio: player.audio,
		status: player.status,
	};
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			playAudio: actions.playAudio,
			playOmny: actions.playOmny,
			pause: actions.pause,
			resume: actions.resume,
			refreshDropdownAd,
			hideDropdownAd,
		},
		dispatch,
	);
}

export default connect(mapStateToProps, mapDispatchToProps)(AudioEmbed);
