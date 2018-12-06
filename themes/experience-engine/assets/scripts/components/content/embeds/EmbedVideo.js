import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

class EmbedVideo extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		const fragment = document.createElement( 'div' );
		fragment.innerHTML = props.html;

		let html = '';
		const iframe = fragment.querySelector( 'iframe' );
		if ( iframe ) {
			const parts = iframe.src.split( '?' );
			iframe.src = parts[0] + '?' + ( parts[1] || '' ) + '&rel=0&showinfo=0&autoplay=1';
			html = iframe.outerHTML;
		}

		self.state = {
			show: false,
			html,
		};

		self.onPlayClick = self.handlePlayClick.bind( self );
	}

	handlePlayClick() {
		this.setState( { show: true } );
	}

	render() {
		const self = this;
		const { show, html } = self.state;
		const { thumbnail } = self.props;
		const style = { backgroundImage: `url(${thumbnail})` };

		if ( show ) {
			return <div className="lazy-video" style={style} dangerouslySetInnerHTML={{ __html: html }} />;
		}

		return (
			<div className="lazy-video" style={style}>
				<button onClick={self.onPlayClick} title="Play" aria-label="Play video" />
			</div>
		);
	}

}

EmbedVideo.propTypes = {
	html: PropTypes.string.isRequired,
	thumbnail: PropTypes.string.isRequired,
};

export default EmbedVideo;
