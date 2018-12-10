import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

class EmbedVideo extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		const fragment = document.createElement( 'div' );
		fragment.innerHTML = props.html;

		let html = '';
		let src = '';
		const iframe = fragment.querySelector( 'iframe' );
		if ( iframe ) {
			const parts = iframe.src.split( '?' );
			iframe.src = src = `${parts[0]}?${( parts[1] || '' )}&rel=0&showinfo=0&autoplay=1`;
			html = iframe.outerHTML;
		}

		self.state = {
			show: false,
			html,
			src,
		};

		self.onPlayClick = self.handlePlayClick.bind( self );
	}

	handlePlayClick( e ) {
		e.preventDefault();
		e.stopPropagation();

		this.setState( { show: true } );
	}

	render() {
		const self = this;
		const { show, html, src } = self.state;
		const { thumbnail, title } = self.props;

		if ( show ) {
			return <div className="lazy-video" dangerouslySetInnerHTML={{ __html: html }} />;
		}

		let webp = false;
		if ( false !== thumbnail.indexOf( 'i.ytimg.com' ) ) {
			webp = <source srcSet={thumbnail.replace( '/vi/', '/vi_webp/' ).replace( '.jpg', '.webp' )} type="image/webp" />;
		}

		return (
			<div className="lazy-video">
				<a href={src} title={title} aria-label={`Play ${title}`} onClick={self.onPlayClick}>
					<picture>
						{webp}
						<img src={thumbnail} alt={title} />
					</picture>
				</a>

				<button onClick={self.onPlayClick} title="Play" aria-label={`Play ${title}`}>
					<svg width="68" height="48" viewBox="0 0 68 48">
						<path className="shape" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" />
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
