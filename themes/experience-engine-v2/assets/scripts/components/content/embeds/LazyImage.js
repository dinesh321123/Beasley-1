import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { IntersectionObserverContext } from '../../../context/intersection-observer';
import { pageview } from '../../../library/google-analytics';

class LazyImage extends PureComponent {
	constructor(props) {
		super(props);

		this.loading = false;
		this.boxRef = React.createRef();
		this.state = { image: '' };

		this.onIntersectionChange = this.handleIntersectionChange.bind(this);
	}

	componentDidMount() {
		const { placeholder } = this.props;

		this.container = document.getElementById(placeholder);
		this.context.observe(this.container, this.onIntersectionChange);
	}

	componentWillUnmount() {
		this.context.unobserve(this.container);
	}

	handleIntersectionChange() {
		const { tracking, referrer } = this.props;

		// disable intersection observing
		this.context.unobserve(this.container);

		// track virtual page view if it's needed
		if (tracking) {
			pageview(document.title, tracking);

			if (window.PARSELY) {
				// eslint-disable-next-line no-undef
				PARSELY.beacon.trackPageView({
					url: tracking,
					urlref: referrer,
				});
			}
		}

		// load image
		if (!this.loading) {
			this.loading = true;
			this.loadImage();
		}
	}

	getDimensions() {
		const { container } = this;

		let parent = container;
		while (parent && parent.offsetHeight < 1) {
			parent = parent.parentNode;
		}

		const { offsetWidth } = container;
		const { offsetHeight } = parent;

		return {
			containerWidth: offsetWidth,
			containerHeight: offsetHeight,
		};
	}

	getImageUrl(quality = null) {
		if (!quality) {
			quality = 95;
		}

		let { src } = this.props;
		const { width, height } = this.props;

		const { containerWidth, containerHeight } = this.getDimensions();

		// Kludge: Temporary fix for incorrectly cached image URLs in EE API
		src = src.replace(
			'b987fm.bbgistage.com/wp-content/uploads/sites/82/',
			'987theshark.bbgistage.com/wp-content/uploads/sites/90/',
		);

		const imageWidth = +width;
		const imageHeight = +height;
		const anchor = imageWidth > imageHeight ? 'middlecenter' : 'leftop';

		let maxheight = 'maxheight';
		let maxwidth = 'maxwidth';
		let mode = '';

		if (+containerWidth < 400 || +containerHeight < 400) {
			if (imageWidth > imageHeight) {
				if (imageWidth / imageHeight > 2) {
					maxheight = 'height';
					if (this.props.crop) {
						mode = '&mode=crop';
					}
				}
			} else if (imageHeight / imageWidth > 2) {
				maxwidth = 'width';
				if (this.props.crop) {
					mode = '&mode=crop';
				}
			}
		}

		let multiplier = window.devicePixelRatio;
		if (multiplier < 1) {
			multiplier = 1;
		} else if (multiplier > 2) {
			multiplier = 2;
		}

		let imageSrc = `${src.split('?')[0]}?${maxwidth}=${Math.round(
			containerWidth * multiplier,
		)}&${maxheight}=${Math.round(
			containerHeight * multiplier,
		)}&anchor=${anchor}${mode}`;
		if (quality && quality > 0) {
			imageSrc += `&quality=${quality}`;
		}

		imageSrc += '&zoom=1.5';

		return imageSrc;
	}

	changeContainer(width, height) {
		const { containerWidth, containerHeight } = this.getDimensions();
		const containerAspect = containerHeight / containerWidth;
		const imageAspect = height / width;
		if (containerAspect > imageAspect) {
			const { container } = this;
			container.style.maxHeight = `${(containerHeight * imageAspect) /
				containerAspect}px`;
		}
	}

	loadImage() {
		const { autoheight } = this.props;
		const { width, height } = this.props;

		// load image and update state
		const imageSrc = this.getImageUrl();

		if (width && height) {
			// adjust height of container if it is needed
			// only for landscape images
			if (autoheight && width > height) {
				this.changeContainer(width, height);
			}

			// check if component is still mounted
			if (this.boxRef.current) {
				this.setState({ image: imageSrc });
			}
			return;
		}

		const imageLoader = new Image();

		imageLoader.src = imageSrc;
		imageLoader.onload = () => {
			// adjust height of container if it is needed
			if (autoheight) {
				const { width, height } = imageLoader;
				// only for landscape images
				if (width > height) {
					this.changeContainer(width, height);
				}
			}

			// check if component is still mounted
			if (this.boxRef.current) {
				this.setState({ image: imageSrc });
			}
		};
	}

	render() {
		const { image } = this.state;
		const { alt, attribution } = this.props;

		const styles = {};

		let child = false;
		if (image) {
			styles.backgroundImage = `url(${image})`;
			if (attribution) {
				child = <div className="lazy-image-attribution">{attribution}</div>;
			}
		} else {
			child = <div className="loading" />;
		}

		return (
			<div
				className="lazy-image"
				ref={this.boxRef}
				style={styles}
				role="img"
				aria-label={alt}
			>
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
	referrer: PropTypes.string,
	attribution: PropTypes.string,
	autoheight: PropTypes.string,
	crop: PropTypes.bool,
};

LazyImage.defaultProps = {
	tracking: '',
	referrer: '',
	attribution: '',
	autoheight: '',
	crop: true,
};

LazyImage.contextType = IntersectionObserverContext;

export default LazyImage;
