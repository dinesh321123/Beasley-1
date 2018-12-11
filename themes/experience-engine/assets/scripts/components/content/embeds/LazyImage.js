import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import IntersectionObserverContext from '../../../context/intersection-observer';

class LazyImage extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.boxRef = React.createRef();
		self.state = { image: '' };
	}

	componentDidMount() {
		const self = this;
		const { placeholder } = self.props;

		self.container = document.getElementById( placeholder );
		self.context.observe( self.container, self.loadImage.bind( self ) );
	}

	componentWillUnmount() {
		this.context.unobserve( this.container );
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

	loadImage() {
		const self = this;

		self.context.unobserve( self.container );

		// build image URL
		const { src, width, height } = self.props;
		const { containerWidth, containerHeight } = self.getDimensions();
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

	render() {
		const self = this;
		const { image } = self.state;
		const { alt } = self.props;

		const styles = {};

		let loader = false;
		if ( image ) {
			styles.backgroundImage = `url(${image})`;
		} else {
			loader = <div className="loading" />;
		}

		return (
			<div className="lazy-image" ref={self.boxRef} style={styles} role="img" aria-label={alt}>
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
	alt: PropTypes.string.isRequired,
};

LazyImage.contextType = IntersectionObserverContext;

export default LazyImage;
