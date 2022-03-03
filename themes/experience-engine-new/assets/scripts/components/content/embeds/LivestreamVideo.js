import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

class LivestreamVideo extends PureComponent {
	componentDidMount() {
		const script = document.createElement('script');

		script.setAttribute('data-embed_id', this.props.embedid);
		script.setAttribute(
			'src',
			`//livestream.com/assets/plugins/referrer_tracking.js?t=${+new Date()}`,
		);

		document.head.appendChild(script);
	}

	render() {
		const self = this;
		const { src, embedid } = self.props;

		return (
			<div className="lazy-video">
				<iframe
					id={embedid}
					src={src}
					title="Watch Video"
					frameBorder="0"
					scrolling="no"
					allowFullScreen
				/>
			</div>
		);
	}
}

LivestreamVideo.propTypes = {
	embedid: PropTypes.string.isRequired,
};

export default LivestreamVideo;
