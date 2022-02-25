import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

class LivestreamVideo extends PureComponent {
	constructor(props) {
		super(props);
		this.setAdjustedSrc = this.setAdjustedSrc.bind(this);
		this.state = { adjustedSrc: '' };
	}

	parseSrcTagFromEmbedTag(embedTag) {
		// DOMParser expects a value for the allowfullscreen attribute which was not in Vimeo Embed
		if (embedTag.indexOf(' allowfullscreen ') > -1) {
			embedTag = embedTag.replace(
				' allowfullscreen ',
				` allowfullscreen='true' `,
			);
		}

		const parser = new DOMParser();
		const iframe = parser.parseFromString(embedTag, 'text/xml');
		return iframe.documentElement.attributes.src.nodeValue;
	}

	setAdjustedSrc() {
		const self = this;
		const { src } = self.props;
		const lookupDomain = '//experiencefeeds.bbgi.com/LivestreamMappingFiles';
		// const lookupDomain =
		//	'//experiencefeeds.bbgi.com.s3-website-us-east-1.amazonaws.com/LivestreamMappingFiles';
		const lookupURL = src
			.replace('//livestream.com', lookupDomain)
			.replace('/accounts/', '/account')
			.replace('/events/', '/event')
			.replace('/videos/', '/video')
			.replace('/player', '.json');
		console.log(`LiveStream LookupURL: ${lookupURL}`);

		fetch(`https:${lookupURL}`)
			.then(response => {
				if (!response.ok) {
					throw new Error(`HTTP error: ${response.status}`);
				}
				return response.json();
			})
			.then(mappingData => {
				const newSrc = this.parseSrcTagFromEmbedTag(mappingData.embed);
				console.log(`Changing Livestream SRC to Vimeo: ${newSrc}`);
				this.setState({ adjustedSrc: newSrc });
			})
			.catch(err => {
				console.log(err.message);
				this.setState({ adjustedSrc: src });
			});
	}

	componentDidMount() {
		// Exit if we have already populated state.adjustedSrc
		const { adjustedSrc } = this.state;
		if (adjustedSrc) {
			return;
		}

		const script = document.createElement('script');

		script.setAttribute('data-embed_id', this.props.embedid);
		script.setAttribute(
			'src',
			`//livestream.com/assets/plugins/referrer_tracking.js?t=${+new Date()}`,
		);

		document.head.appendChild(script);
		this.setAdjustedSrc();
	}

	render() {
		const self = this;
		const { embedid } = self.props;
		const { adjustedSrc } = this.state;

		if (!adjustedSrc) {
			return null;
		}

		return (
			<div className="lazy-video">
				<iframe
					id={embedid}
					src={adjustedSrc}
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
