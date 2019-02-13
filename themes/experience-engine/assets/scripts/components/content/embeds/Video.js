import { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { loadAssets } from '../../../library/dom';

class Video extends PureComponent {

	static detectIE() {
		const ua = window.navigator.userAgent;
	
		const msie = ua.indexOf( 'MSIE ' );
		if ( 0 < msie ) {
			// IE 10 or older => return version number
			return parseInt( ua.substring( msie + 5, ua.indexOf( '.', msie ) ), 10 );
		}
	
		const trident = ua.indexOf( 'Trident/' );
		if ( 0 < trident ) {
			// IE 11 => return version number
			const rv = ua.indexOf( 'rv:' );
			return parseInt( ua.substring( rv + 3, ua.indexOf( '.', rv ) ), 10 );
		}
	
		const edge = ua.indexOf( 'Edge/' );
		if ( 0 < edge ) {
			// Edge (IE 12+) => return version number
			return parseInt( ua.substring( edge + 5, ua.indexOf( '.', edge ) ), 10 );
		}
	
		// other browser
		return false;
	}

	componentDidMount() {
		const self = this;

		const masterScripts = [
			'//imasdk.googleapis.com/js/sdkloader/ima3.js',
			'/wp-content/themes/experience-engine/bundle/video.min.js',
		];

		const slaveScripts = [
			'/wp-content/themes/experience-engine/bundle/videojs-flash.min.js',
			'/wp-content/themes/experience-engine/bundle/videojs-contrib-hls.min.js',
			'/wp-content/themes/experience-engine/bundle/videojs-contrib-ads.min.js',
			'/wp-content/themes/experience-engine/bundle/videojs.ima.min.js',
			'/wp-content/themes/experience-engine/bundle/videojs-contrib-quality-levels.min.js',
			'/wp-content/themes/experience-engine/bundle/videojs-hls-quality-selector.min.js',
		];

		const styles = [
			'/wp-content/themes/experience-engine/bundle/video-js.min.css',
			'/wp-content/themes/experience-engine/bundle/videojs-contrib-ads.css',
			'/wp-content/themes/experience-engine/bundle/videojs.ima.css',
		];

		loadAssets( masterScripts, styles )
			.then( () => loadAssets( slaveScripts ) )
			.then( self.loadVideoJs.bind( self ) )
			.catch( error => console.error( error ) ); // eslint-disable-line no-console
	}

	componentWillUnmount() {
		const self = this;
		if ( self.player ) {
			self.player.dispose();
		}
	}

	loadVideoJs() {
		const self = this;
		const { placeholder, id, src, poster, adTagUrl } = self.props;

		const container = document.getElementById( placeholder );
		if ( !container ) {
			return;
		}

		const videoRef = document.createElement( 'video' );

		videoRef.id = id;
		videoRef.className = 'video-js vjs-default-skin';
		videoRef.controls = true;
		videoRef.poster = poster;

		container.appendChild( videoRef );

		const videojsOptions = Video.detectIE()
			? { techOrder: ['flash', 'html5'] }
			: {};

		const player = window.videojs( videoRef, videojsOptions );
		const videoArgs = {
			src,
			type: 'application/x-mpegURL',
			withCredentials: true
		};

		player.src( videoArgs );
		player.hlsQualitySelector();

		if ( adTagUrl ) {
			player.ima( { id, adTagUrl } );

			let startEvent = 'click';
			if ( navigator.userAgent.match( /(iPhone|iPad|Android)/i ) ) {
				startEvent = 'touchend';
			}

			// Initialize the ad container when the video player is clicked, but only the
			// first time it's clicked.
			var initAdDisplayContainer = function() {
				player.ima.initializeAdDisplayContainer();
				videoRef.removeEventListener( startEvent, initAdDisplayContainer );
			};

			videoRef.addEventListener( startEvent, initAdDisplayContainer );
		}

		self.player = player;
	}

	render() {
		return false;
	}

}

Video.propTypes = {
	placeholder: PropTypes.string.isRequired,
	id: PropTypes.string.isRequired,
	adTagUrl: PropTypes.string.isRequired,
	poster: PropTypes.string.isRequired,
	src: PropTypes.string.isRequired,
};

export default Video;
