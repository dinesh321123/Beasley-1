import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import debounce from 'lodash.debounce';

class LazyImage extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.boxRef = React.createRef();
		self.state = {
			containerWidth: 0,
			containerHeight: 0,
			image: '',
		};

		self.updateDimensions = self.updateDimensions.bind( self );
		self.loadImage = self.loadImage.bind( self );
		self.loadImageDebounced = debounce( self.loadImage, 500 );

		self.onResize = self.handleResize.bind( self );
		self.onScroll = self.handleScroll.bind( self );
	}

	componentDidMount() {
		const self = this;
		const { placeholder } = self.props;

		self.container = document.getElementById( placeholder );
		self.updateDimensions();

		window.addEventListener( 'scroll', self.onScroll );
		window.addEventListener( 'resize', self.onResize );
	}

	componentWillUnmount() {
		this.removeListeners();
	}

	componentDidUpdate( prevProps, prevState ) {
		const self = this;
		const { containerWidth, containerHeight } = self.state;

		if ( containerWidth !== prevState.containerWidth || containerHeight !== prevState.containerHeight ) {
			// load image when container size has changed
			this.loadImageDebounced();
		}
	}

	removeListeners() {
		const self = this;
		window.removeEventListener( 'scroll', self.onScroll );
		window.removeEventListener( 'resize', self.onResize );
	}

	handleResize() {
		window.requestAnimationFrame( this.updateDimensions );
	}

	handleScroll() {
		window.requestAnimationFrame( this.loadImage );
	}

	updateDimensions() {
		const self = this;
		const { container } = self;
		const { aspect } = self.props;

		let parent = container;
		while ( parent && 1 > parent.offsetHeight ) {
			parent = parent.parentNode;
		}

		const { offsetWidth } = container;
		const containerWidth = offsetWidth;

		const { offsetHeight } = parent;
		let containerHeight = offsetWidth / aspect;
		if ( containerHeight > offsetHeight ) {
			containerHeight = offsetHeight;
		}

		self.setState( { containerWidth, containerHeight } );
	}

	loadImage() {
		const self = this;

		// do nothing if the image is not in the viewport or close to it
		const { containerWidth, containerHeight } = self.state;
		if ( !self.isInViewport( containerWidth, containerHeight ) ) {
			return;
		}

		// we don't need event listeners because image will be loaded
		self.removeListeners();

		// build image URL
		const { src, width, height } = self.props;
		const anchor = width > height ? 'middlecenter' : 'leftop';
		const imageSrc = `${src.split( '?' )[0]}?maxwidth=${containerWidth}&maxheight=${containerHeight}&anchor=${anchor}`;

		// load image and update state
		const imageLoader = new Image();
		imageLoader.src = imageSrc;
		imageLoader.onload = () => {
			// check if component is still mounted
			if ( self.boxRef.current ) {
				self.setState( { image: imageSrc } );
			}
		};
	}

	isInViewport( containerWidth, containerHeight ) {
		const bounding = this.container.getBoundingClientRect();
		const { top, left, bottom, right } = bounding;

		const { documentElement } = document;
		const innerHeight = ( window.innerHeight || documentElement.clientHeight ) + containerHeight;
		const innerWidth = ( window.innerWidth || documentElement.clientWidth ) + containerWidth;

		return -containerHeight <= top && -containerWidth <= left && bottom <= innerHeight && right <= innerWidth;
	}

	render() {
		const self = this;
		const { container } = self;
		const { containerWidth, containerHeight, image } = self.state;

		if ( !container ) {
			return false;
		}

		const styles = {
			width: `${containerWidth}px`,
			height: `${containerHeight}px`,
		};

		let loader = false;
		if ( image ) {
			styles.backgroundImage = `url(${image})`;
		} else {
			loader = <div className="loading" />;
		}

		return (
			<div className="lazy-image" ref={self.boxRef} style={styles}>
				{loader}
			</div>
		);
	}

}

LazyImage.propTypes = {
	placeholder: PropTypes.string.isRequired,
	src: PropTypes.string.isRequired,
	width: PropTypes.string.isRequired,
	height: PropTypes.string.isRequired,
	aspect: PropTypes.string.isRequired,
};

export default LazyImage;
