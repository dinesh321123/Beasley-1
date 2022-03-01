import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import VimeoReplaceLiveStream from './VimeoReplaceLiveStream';

class LivestreamVideo extends PureComponent {
	constructor(props) {
		super(props);
		this.setAdjustedSrc = this.setAdjustedSrc.bind(this);
		this.setAdjustedSrc();
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
				// const newSrc = mappingData.embed.replace(
				//	`height='720'`,
				//	`height='360'`,
				// );
				if (newSrc) {
					console.log(`Changing Livestream SRC to Vimeo: ${newSrc}`);
					this.setState({ adjustedSrc: newSrc });
				} else {
					throw new Error(`Parse error: ${mappingData.embed}`);
				}
			})
			.catch(err => {
				console.log(err.message);
				this.setState({ adjustedSrc: src });
			});
	}

	componentDidMount() {
		console.log('LIVESTREAM COMPONENT DID MOUNT');
		if (!this.state) {
			return;
		}

		const script = document.createElement('script');

		script.setAttribute('data-embed_id', this.props.embedid);
		script.setAttribute(
			'src',
			`//livestream.com/assets/plugins/referrer_tracking.js?t=${+new Date()}`,
		);

		document.head.appendChild(script);
		// this.setAdjustedSrc();
	}

	render() {
		console.log('LIVESTREAM RENDER');
		const self = this;
		const { embedid, src } = self.props;

		if (!this.state) {
			return null;
		}
		const { adjustedSrc } = this.state;

		if (adjustedSrc) {
			return <VimeoReplaceLiveStream adjustedSrc={adjustedSrc} />;
		}

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
