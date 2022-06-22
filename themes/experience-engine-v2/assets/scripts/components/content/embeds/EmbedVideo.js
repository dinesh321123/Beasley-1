import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

class EmbedVideo extends PureComponent {
	constructor(props) {
		super(props);

		const fragment = document.createElement('div');
		fragment.innerHTML = props.html;

		let html = '';
		let src = '';
		const iframe = fragment.querySelector('iframe');
		if (iframe) {
			src = this.adjustEmbeddedVideoUrlSrc(iframe);
			iframe.src = src;
			html = iframe.outerHTML;
		}

		this.state = {
			isFallback: false,
			show: false,
			html,
			src,
		};

		this.onPlayClick = this.handlePlayClick.bind(this);
	}

	componentDidMount = async () => {
		let { thumbnail } = this.props;
		thumbnail = thumbnail
			.replace('/vi/', '/vi_webp/')
			.replace('hqdefault.jpg', 'mqdefault.jpg')
			.replace('.jpg', '.webp');
		const img = await this.checkImg(thumbnail);
		if (img) {
			if (img.naturalWidth === 120) {
				this.setState({ isFallback: true });
			}
		} else {
			this.setState({ isFallback: true });
		}
	};

	// Exclude autoplay=1 on Vimeo Video links because it causes autoplay
	adjustEmbeddedVideoUrlSrc = iframe => {
		const parts = iframe.src.split('?');
		const autoPlayParam =
			parts[0].toLowerCase().indexOf('vimeo') === -1 ? '&autoplay=1' : '';
		return `${parts[0]}?${parts[1] || ''}${
			parts[1] ? '&' : ''
		}rel=0&showinfo=0${autoPlayParam}`;
	};

	handlePlayClick(e) {
		e.preventDefault();
		e.stopPropagation();

		this.setState({ show: true });
	}

	checkImg = async url => {
		const img = new Image();
		return new Promise((resolve, reject) => {
			img.onload = function() {
				resolve(img);
			};
			img.onerror = function() {
				resolve();
			};
			img.src = url;
		});
	};

	render() {
		const { show, html, src, isFallback } = this.state;
		const { thumbnail, title } = this.props;

		if (show) {
			return (
				<div
					className="lazy-video"
					dangerouslySetInnerHTML={{ __html: html }}
				/>
			);
		}

		let webp = false;
		if (thumbnail.indexOf('i.ytimg.com') !== false) {
			let webpThumbType = 'image/jpg';
			let webpThumbSrc = thumbnail.replace('hqdefault.jpg', 'mqdefault.jpg');
			if (!isFallback) {
				webpThumbType = 'image/webp';
				webpThumbSrc = thumbnail
					.replace('/vi/', '/vi_webp/')
					.replace('hqdefault.jpg', 'mqdefault.jpg')
					.replace('.jpg', '.webp');
			}
			webp = <source srcSet={webpThumbSrc} type={webpThumbType} />;
		}

		return (
			<div className="lazy-video">
				<a href={src} aria-label={`Play ${title}`} onClick={this.onPlayClick}>
					<picture>
						{webp}
						<img
							src={thumbnail.replace('hqdefault.jpg', 'mqdefault.jpg')}
							alt={title}
						/>
					</picture>
				</a>

				<button onClick={this.onPlayClick} aria-hidden="true" type="button">
					<svg width="68" height="48" viewBox="0 0 68 48">
						<path
							className="shape"
							d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z"
						/>
						<path className="icon" d="M 45,24 27,14 27,34" />
					</svg>
				</button>
			</div>
		);
	}
}

EmbedVideo.propTypes = {
	html: PropTypes.string.isRequired,
	title: PropTypes.string.isRequired,
	thumbnail: PropTypes.string.isRequired,
};

export default EmbedVideo;
