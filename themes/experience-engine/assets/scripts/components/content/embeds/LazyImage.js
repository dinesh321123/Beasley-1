import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import IntersectionObserverContext from '../../../context/intersection-observer';
import { pageview } from '../../../library/google-analytics';

class LazyImage extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.loading = false;
		self.boxRef = React.createRef();
		self.state = { image: '' };

		self.onIntersectionChange = self.handleIntersectionChange.bind( self );
	}

	componentDidMount() {
		const self = this;
		const { placeholder } = self.props;

		self.container = document.getElementById( placeholder );
		self.context.observe( self.container, self.onIntersectionChange );
	}

	componentWillUnmount() {
		this.context.unobserve( this.container );
	}

	handleIntersectionChange() {
		const self = this;
		const { tracking } = self.props;

		// disable intersection observing
		self.context.unobserve( self.container );

		// track virtual page view if it's needed
		if ( tracking ) {
			pageview( document.title, tracking );
		}

		// load image
		if ( !self.loading ) {
			self.loading = true;
			self.loadImage();
		}
	}

	getDimensions() {
		const self = this;
		const { container } = self;

		let parent = container;
		while ( parent && 1 > parent.offsetHeight ) {
			parent = parent.parentNode;
		}

		const { offsetWidth } = container;
		const { offsetHeight } = parent;

		return {
			containerWidth: offsetWidth,
			containerHeight: offsetHeight,
		};
	}

	getImageUrl( quality = null ) {
		if ( ! quality ) {
			quality = 95;
		}

		const self = this;
		const { src, width, height } = self.props;
		const { containerWidth, containerHeight } = self.getDimensions();

		const imageWidth = +width;
		const imageHeight = +height;
		const anchor = imageWidth > imageHeight ? 'middlecenter' : 'leftop';

		let maxheight = 'maxheight';
		let maxwidth = 'maxwidth';
		let mode = '';

		if ( 400 > +containerWidth || 400 > +containerHeight ) {
			if ( imageWidth > imageHeight ) {
				if ( 2 < ( imageWidth / imageHeight ) ) {
					maxheight = 'height';
					mode = '&mode=crop';
				}
			} else {
				if ( 2 < ( imageHeight / imageWidth ) ) {
					maxwidth = 'width';
					mode = '&mode=crop';
				}
			}
		}

		let multiplier = window.devicePixelRatio;
		if ( 1 > multiplier ) {
			multiplier = 1;
		} else if ( 2 < multiplier ) {
			multiplier = 2;
		}

		let imageSrc = `${src.split( '?' )[0]}?${maxwidth}=${Math.round( containerWidth * multiplier )}&${maxheight}=${Math.round( containerHeight * multiplier )}&anchor=${anchor}${mode}`;
		if ( quality && 0 < quality ) {
			imageSrc += `&quality=${quality}`;
		}

		return imageSrc;
	}

	loadImage() {
		const self = this;
		const { autoheight } = self.props;

		// load image and update state
		const imageSrc = self.getImageUrl();
		const imageLoader = new Image();

		imageLoader.src = imageSrc;
		imageLoader.onload = () => {
			// adjust height of container if it is needed
			if ( autoheight ) {
				const { width, height } = imageLoader;
				// only for landscape images
				if ( width > height ) {
					const { containerWidth, containerHeight } = self.getDimensions();
					const containerAspect = containerHeight / containerWidth;
					const imageAspect = height / width;
					if ( containerAspect > imageAspect ) {
						const { container } = self;
						container.style.maxHeight = `${containerHeight * imageAspect / containerAspect}px`;
					}
				}
			}

			// check if component is still mounted
			if ( self.boxRef.current ) {
				self.setState( { image: imageSrc } );
			}
		};
	}

	render() {
		const self = this;
		const { image } = self.state;
		const { alt, attribution } = self.props;

		const styles = {};

		let child = false;
		if ( image ) {
			styles.backgroundImage = `url(${image})`;
			if ( attribution ) {
				child = <div className="lazy-image-attribution">{attribution}</div>;
			}
		} else {
			child = <div className="loading" />;
		}

		return (
			<div className="lazy-image" ref={self.boxRef} style={styles} role="img" aria-label={alt}>
				{child}
			</div>
		);
	}

}

LazyImage.propTypes = {
	placeholder: PropTypes.string.isRequired,
	src: PropTypes.string.isRequired,
	width: PropTypes.string.isRequired,
	height: PropTypes.string.isRequired,
	alt: PropTypes.string.isRequired,
	tracking: PropTypes.string,
	attribution: PropTypes.string,
	autoheight: PropTypes.string,
};

LazyImage.defaultProps = {
	tracking: '',
	attribution: '',
	autoheight: '',
};

LazyImage.contextType = IntersectionObserverContext;

export default LazyImage;
