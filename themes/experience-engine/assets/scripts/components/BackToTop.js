import React, { PureComponent } from 'react';

// browser window scroll (in pixels) after which the "back to top" link is shown
const OFFSET = 300;
// browser window scroll (in pixels) after which the "back to top" link opacity is reduced
const OFFSET_OPACITY = 1200;
// duration of the top scrolling animation (in ms)
const SCROLL_DURATION = 700;

class BackToTop extends PureComponent {
	constructor(props) {
		super(props);

		this.scrolling = false;

		this.state = {
			fadeOut: false,
			show: false,
		};

		this.onBackToTop = this.handleBackToTop.bind(this);
		this.onScroll = this.handleScroll.bind(this);
	}

	componentDidMount() {
		this.handleScroll();
		window.addEventListener('scroll', this.onScroll);
	}

	componentWillUnmount() {
		window.removeEventListener('scroll', this.onScroll);
	}

	handleScroll() {
		if (this.scrolling) {
			return;
		}

		this.scrolling = true;

		window.requestAnimationFrame(() => {
			const params = {};
			const windowTop = window.scrollY || document.documentElement.scrollTop;

			if (windowTop > OFFSET) {
				params.show = true;
			} else {
				params.show = false;
				params.fadeOut = false;
			}

			if (windowTop > OFFSET_OPACITY) {
				params.fadeOut = true;
			}

			if (Object.keys(params).length > 0) {
				this.setState(params);
			}

			this.scrolling = false;
		});
	}

	easeInOutQuad(t, b, c, d) {
		t /= d / 2;
		if (t < 1) {
			return (c / 2) * t * t + b;
		}

		t--;

		return (-c / 2) * (t * (t - 2) - 1) + b;
	}

	handleBackToTop() {
		const start = window.scrollY || document.documentElement.scrollTop;
		let currentTime = null;

		const animateScroll = timestamp => {
			if (!currentTime) {
				currentTime = timestamp;
			}

			const progress = timestamp - currentTime;
			const val = Math.max(
				this.easeInOutQuad(progress, start, -start, SCROLL_DURATION),
				0,
			);

			window.scrollTo(0, val);
			if (progress < SCROLL_DURATION) {
				window.requestAnimationFrame(animateScroll);
			}
		};

		window.requestAnimationFrame(animateScroll);
	}

	render() {
		const { show, fadeOut } = this.state;
		let classes = 'back-to-top';

		if (show) {
			classes += ' -show';
		}

		if (fadeOut) {
			classes += ' -fadeout';
		}

		return (
			<button
				className={classes}
				aria-label="Back to top"
				onClick={this.onBackToTop}
				type="button"
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="18">
					<path fill="#FFF" d="M8 2.8l8 7.9-2.4 2.4-5.5-5.5-5.6 5.6L0 10.7z" />
				</svg>
			</button>
		);
	}
}

export default BackToTop;
