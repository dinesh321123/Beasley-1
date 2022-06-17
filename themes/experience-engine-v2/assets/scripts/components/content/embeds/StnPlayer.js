import { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { connect } from 'react-redux';

class StnPlayer extends PureComponent {
	// eslint-disable-next-line no-useless-constructor
	constructor(props) {
		super(props);
	}

	componentDidMount() {
		const { placeholder, fk, cid, videokey, type } = this.props;

		if (!window.stnvideos) {
			window.stnvideos = {};
		}

		const container = document.getElementById(placeholder);
		if (!container) {
			return;
		}

		if (videokey.toLowerCase() === 'none') {
			window.stnvideos.prevent = true;
		} else if (type === 'supplied') {
			window.stnvideos.override = {
				render: () => {
					this.adPlaylistOrSmartlistPlayer(fk, cid, container, false);
				},
				type: 'default',
			};
		} else if (videokey) {
			if (type === 'featured') {
				const hideCaption = () => {
					const postThumbnailFeaturedCaption = document.querySelector(
						'.description .post-thumbnail-caption',
					);

					if (postThumbnailFeaturedCaption) {
						postThumbnailFeaturedCaption.style.display = 'none';
					}
				};

				// prettier-ignore
				const featuredDiv = document.getElementsByClassName('post-thumbnail')[0];
				window.stnvideos.override = {
					render: () => {
						const stndiv = document.createElement('div');
						stndiv.className = `s2nPlayer k-${fk} lazy-image`;
						stndiv.style.backgroundColor = 'transparent';
						stndiv.setAttribute('data-type', 'float');

						const stn_barker_script = document.createElement('script');
						stn_barker_script.setAttribute('type', 'text/javascript');
						stn_barker_script.setAttribute(
							'src',
							`//embed.sendtonews.com/player3/embedcode.js?SC=${videokey}&cid=${cid}&offsetx=0&offsety=75&floatwidth=400&floatposition=bottom-right`,
						);
						stn_barker_script.setAttribute('data-type', 's2nScript');

						if (featuredDiv) {
							featuredDiv.innerHTML = '';
							featuredDiv.classList.add('stn-video-thumbnail');
							featuredDiv.appendChild(stndiv);
							featuredDiv.appendChild(stn_barker_script);

							hideCaption();
						} else {
							// prettier-ignore
							const description = document.getElementsByClassName('description')[0];
							if (description) {
								const thumbnailDiv1 = document.createElement('div');
								thumbnailDiv1.className = `post-thumbnail featured-media stn-video-thumbnail`;
								thumbnailDiv1.appendChild(stndiv);
								thumbnailDiv1.appendChild(stn_barker_script);

								const thumbnailDiv2 = document.createElement('div');
								thumbnailDiv2.className = `post-thumbnail-wrapper`;
								thumbnailDiv2.appendChild(thumbnailDiv1);

								description.insertBefore(thumbnailDiv2, description.firstChild);

								hideCaption();
							}
						}
					},
				};
			} else {
				window.stnvideos.override = {
					render: () => {
						const stndiv = document.createElement('div');
						stndiv.className = `s2nPlayer k-${fk}`;
						stndiv.setAttribute('data-type', 'float');

						const stn_barker_script = document.createElement('script');
						stn_barker_script.setAttribute('type', 'text/javascript');
						stn_barker_script.setAttribute(
							'src',
							`//embed.sendtonews.com/player3/embedcode.js?SC=${videokey}&cid=${cid}&offsetx=0&offsety=75&floatwidth=400&floatposition=bottom-right`,
						);
						stn_barker_script.setAttribute('data-type', 's2nScript');

						container.appendChild(stndiv);
						container.appendChild(stn_barker_script);
					},
				};
			}
		} else {
			window.stnvideos.default = {
				render: () => {
					this.adPlaylistOrSmartlistPlayer(fk, cid, container);
				},
				type: 'default',
			};
		}
	}

	adPlaylistOrSmartlistPlayer(fk, cid, container, displayheader = true) {
		const stndiv = document.createElement('div');
		stndiv.className = `s2nPlayer k-${fk} s2nSmartPlayer${
			displayheader ? ' s2nheadshow' : ''
		}`;
		stndiv.setAttribute('data-type', 'float');

		const stn_player_script = document.createElement('script');
		stn_player_script.setAttribute('type', 'text/javascript');
		stn_player_script.setAttribute(
			'src',
			`//embed.sendtonews.com/player3/embedcode.js?fk=${fk}&cid=${cid}&offsetx=0&offsety=75&floatwidth=400&floatposition=bottom-right`,
		);
		stn_player_script.setAttribute('data-type', 's2nScript');

		container.appendChild(stndiv);
		container.appendChild(stn_player_script);
	}

	render() {
		return false;
	}
}

StnPlayer.propTypes = {
	placeholder: PropTypes.string.isRequired,
	fk: PropTypes.string,
	cid: PropTypes.string.isRequired,
	videokey: PropTypes.string,
	type: PropTypes.string,
};
StnPlayer.defaultProps = {
	videokey: '',
	fk: '',
	type: '',
};

export default connect()(StnPlayer);
