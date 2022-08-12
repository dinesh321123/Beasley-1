import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Swiper from 'swiper';
import md5 from 'md5';

import ContentBlock from '../components/content/ContentBlock';
import {
	initPage,
	fetchPage,
	fetchFeedsContent,
} from '../redux/actions/screen';
import { firebaseAuth, getCanonicalUrl, untrailingslashit } from '../library';

const specialPages = ['/wp-admin/', '/wp-signup.php', '/wp-login.php'];

/**
 * The ContentDispatcher component is responsible for catching click on
 * internal links and trigger the page loading logic.
 */
class ContentDispatcher extends Component {
	constructor(props) {
		super(props);

		this.onClick = this.handleClick.bind(this);
		this.handleSliders = this.handleSliders.bind(this);
		this.handleSliderLoad = this.handleSliderLoad.bind(this);
		this.onPageHistoryPop = this.onPageHistoryPop.bind(this);
	}

	/**
	 * Inits the current page and handle a few other things on first load.
	 */
	componentDidMount() {
		const { initPage } = this.props;

		window.addEventListener('click', this.onClick);
		// a zero timeout ensures that the callback runs when the new history state is in place.
		// https://developer.mozilla.org/en-US/docs/Web/API/Window/popstate_event
		window.addEventListener('popstate', () =>
			setTimeout(this.onPageHistoryPop, 0),
		);

		// load current page into the state
		initPage();
		this.handleSliderLoad();
	}

	componentDidUpdate() {
		const element = document.querySelector('.scroll-to');
		if (element) {
			let top = element.offsetTop;

			const wpadminbar = document.querySelector('#wpadminbar');
			if (wpadminbar) {
				top -= wpadminbar.offsetHeight;
			}

			setTimeout(() => window.scrollTo(0, top), 500);
		}
		this.handleSliderLoad();
	}

	componentWillUnmount() {
		window.removeEventListener('click', this.onClick);
		window.removeEventListener('popstate', this.onPageHistoryPop);
	}

	/**
	 * Handles setting up the sliders.
	 */
	handleSliderLoad() {
		const carousels = document.querySelectorAll('.swiper-container');

		if (carousels.length) {
			this.handleSliders();
		}
	}

	/**
	 * Setup the sliders with Swiper.js for the homepage feeds.
	 */
	handleSliders() {
		const carousels = document.querySelectorAll('.swiper-container');

		if (carousels) {
			for (let i = 0, len = carousels.length; i < len; i++) {
				const count = carousels[i].classList.contains('-large') ? 2.2 : 4.2;
				const group = carousels[i].classList.contains('-large') ? 2 : 4;

				// eslint-disable-next-line no-new
				const swiper = new Swiper(carousels[i], {
					init: false,
					slidesPerView: count + 2,
					slidesPerGroup: group + 2,
					spaceBetween: 36,
					freeMode: true,
					breakpoints: {
						1680: {
							slidesPerView: count + 1,
							slidesPerGroup: count + 1,
						},
						1280: {
							slidesPerView: count,
							slidesPerGroup: group,
							spaceBetween: 27,
						},
						767: {
							slidesPerView: 2.7,
							slidesPerGroup: 2,
							spaceBetween: 4,
						},
						480: {
							slidesPerView: 2.7,
							slidesPerGroup: 2,
							spaceBetween: 4,
						},
					},
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
				});

				swiper.on('init', () => {
					const fakenextButton = swiper.el.querySelectorAll(
						'.swiper-button-fake-next',
					);
					if (fakenextButton && fakenextButton.length > 0) {
						fakenextButton[0].onclick = () => {
							console.log('alternate click');
							swiper.slideTo(0, 200, false);
						};
					}
				});

				swiper.init();
			}
		}
	}

	/**
	 * Handle the click links and if it's an internal links trigger the
	 * page loading process.
	 *
	 * If the user is logged in and the click is for the homepage, the feed will be fetched
	 * from Experience Engine by calling fetchFeeedsContent.
	 *
	 * @see assets/js/redux/actions/screen.js
	 *
	 * @param {event} e The event object.
	 */
	handleClick(e) {
		const { target } = e;
		let linkNode = target;

		// find if a click has been made by an anchor or an element that is a child of an anchor
		while (linkNode && linkNode.nodeName.toUpperCase() !== 'A') {
			linkNode = linkNode.parentElement;
		}

		// do nothing if anchor is not found
		if (!linkNode) {
			return;
		}

		// do nothing if this link has to be opened in a new window
		if (linkNode.getAttribute('target') === '_blank') {
			return;
		}

		const { location } = window;
		const { origin } = location;

		const link = linkNode.getAttribute('href');
		const linkOrigin = link.substring(0, origin.length);

		if (link.match(/\.(pdf|doc|docx)$/)) {
			return;
		}

		// return if different origin or a relative link that doesn't start from forward slash
		if (origin !== linkOrigin && !link.match(/^\/\w+/)) {
			return;
		}

		// return if it is an admin link or a link to a special page
		if (specialPages.find(url => link.indexOf(url) > -1)) {
			return;
		}

		// target link is internal page, thus stop propagation and prevent default actions
		e.preventDefault();
		e.stopPropagation();

		this.loadPage(link);
	}

	onPageHistoryPop(e) {
		const lastCanonicalUrl = getCanonicalUrl();
		console.log(
			`BACK - Canonical: ${lastCanonicalUrl} Current: ${window.location.href}`,
		);

		if (window.location.href.replace('#//', '') === lastCanonicalUrl) {
			console.log(`Current Matched Canonical - doubling back`);
			window.history.back();
		} else if (window.location.href.indexOf('#') > -1) {
			console.log('Found # - doubling back');
			window.history.back();
		} else {
			console.log(`Back caused load of ${window.location.href}`);
			this.loadPage(window.location.href, { suppressHistory: true });
		}
	}

	/**
	 * Uses the appropriate action creator to fetch the page based on the URL.
	 *
	 * @param {String} url
	 */
	loadPage(url, options = {}) {
		const { fetchPage, fetchFeedsContent } = this.props;
		const { origin } = window.location;

		window.lastLoadedUrl = url;

		// load user homepage if token is not empty and the next page is the homepage
		// otherwise just load the next page
		if (
			untrailingslashit(origin) === untrailingslashit(url.split(/[?#]/)[0]) &&
			firebaseAuth.currentUser
		) {
			firebaseAuth.currentUser
				.getIdToken()
				.then(token => {
					fetchFeedsContent(token, url, options);
				})
				.catch(() => {
					// fallback to loading regular homepage if fetchFeedsContent fails.
					fetchPage(url, options);
				});
		} else {
			// if it's a regular internal page (not homepage) just fetch the page as usual.
			fetchPage(url, options);
		}
	}

	render() {
		const { content, embeds, partials, isHome } = this.props;
		const blocks = [];

		if (!content || !content.length) {
			return null;
		}

		blocks.push(
			// the composed key is needed to make sure we use a new ContentBlock component when we replace the content of the current page
			<ContentBlock
				key={`${window.location.href}-${md5(content)}`}
				content={content}
				embeds={embeds}
				isHome={isHome}
			/>,
		);

		Object.keys(partials).forEach(key => {
			// eslint-disable-next-line react/jsx-props-no-spreading
			blocks.push(<ContentBlock key={key} {...partials[key]} partial />);
		});

		return blocks;
	}
}

ContentDispatcher.propTypes = {
	content: PropTypes.string.isRequired,
	embeds: PropTypes.arrayOf(PropTypes.object).isRequired,
	partials: PropTypes.shape({}).isRequired,
	initPage: PropTypes.func.isRequired,
	isHome: PropTypes.bool,
	fetchPage: PropTypes.func.isRequired,
	fetchFeedsContent: PropTypes.func.isRequired,
};

ContentDispatcher.defaultProps = {
	isHome: false,
};

export default connect(
	({ screen }) => ({
		content: screen.content,
		embeds: screen.embeds,
		isHome: screen.isHome,
		partials: screen.partials,
	}),
	{
		initPage,
		fetchPage,
		fetchFeedsContent,
	},
)(ContentDispatcher);
