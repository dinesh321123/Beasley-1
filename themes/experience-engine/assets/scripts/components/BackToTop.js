import React, { PureComponent } from 'react';

// browser window scroll (in pixels) after which the "back to top" link is shown
const OFFSET = 300;
// browser window scroll (in pixels) after which the "back to top" link opacity is reduced
const OFFSET_OPACITY = 1200;
// duration of the top scrolling animation (in ms)
const SCROLL_DURATION = 700;

class BackToTop extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.scrolling = false;

		self.state = {
			show: false,
			fadeOut: false,
		};

		self.onBackToTop = self.handleBackToTop.bind( self );
		self.onScroll = self.handleScroll.bind( self );
	}

	componentDidMount() {
		this.handleScroll();
		window.addEventListener( 'scroll', this.onScroll );
	}

	componentWillUnmount() {
		window.removeEventListener( 'scroll', this.onScroll );
	}

	handleScroll() {
		const self = this;
		if ( self.scrolling ) {
			return;
		}

		self.scrolling = true;

		window.requestAnimationFrame( () => {
			const params = {};
			const windowTop = window.scrollY || document.documentElement.scrollTop;

			if ( windowTop > OFFSET ) {
				params.show = true;
			} else {
				params.show = false;
				params.fadeOut = false;
			}

			if ( windowTop > OFFSET_OPACITY ) {
				params.fadeOut = true;
			}

			if ( 0 < Object.keys( params ).length ) {
				self.setState( params );
			}

			self.scrolling = false;
		} );
	}

	easeInOutQuad( t, b, c, d ) {
		t /= d / 2;
		if ( 1 > t ) {
			return c / 2 * t * t + b;
		}

		t--;

		return -c / 2 * ( t * ( t - 2 ) - 1 ) + b;
	}

	handleBackToTop() {
		const self = this;
		const start = window.scrollY || document.documentElement.scrollTop;
		let currentTime = null;

		const animateScroll = function( timestamp ) {
			if ( !currentTime ) {
				currentTime = timestamp;
			}

			const progress = timestamp - currentTime;
			const val = Math.max( self.easeInOutQuad( progress, start, -start, SCROLL_DURATION ), 0 );

			window.scrollTo( 0, val );
			if ( progress < SCROLL_DURATION ) {
				window.requestAnimationFrame( animateScroll );
			}
		};

		window.requestAnimationFrame( animateScroll );
	}

	render() {
		const self = this;
		const { show, fadeOut } = self.state;
		let classes = 'back-to-top';

		if ( show ) {
			classes += ' -show';
		}

		if ( fadeOut ) {
			classes += ' -fadeout';
		}

		return (
			<button className={classes} aria-label="Back to top" onClick={self.onBackToTop}>
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16">
					<path fill="#FFF" d="M8 2.8l8 7.9-2.4 2.4-5.5-5.5-5.6 5.6L0 10.7z" />
				</svg>
			</button>
		);
	}

}

export default BackToTop;
