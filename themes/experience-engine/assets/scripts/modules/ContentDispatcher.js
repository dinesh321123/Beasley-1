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
import { firebaseAuth, untrailingslashit } from '../library';

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
		this.onPageChange = this.onPageChange.bind(this);
	}

	/**
	 * Inits the current page and handle a few other things on first load.
	 */
	componentDidMount() {
		const { initPage } = this.props;

		window.addEventListener('click', this.onClick);
		window.addEventListener('popstate', this.onPageChange);

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
		window.removeEventListener('popstate', this.onPageChange);
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
				new Swiper(carousels[i], {
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
		const { fetchPage, fetchFeedsContent } = this.props;

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

		// load user homepage if token is not empty and the next page is the homepage
		// otherwise just load the next page
		if (
			untrailingslashit(origin) === untrailingslashit(link.split(/[?#]/)[0]) &&
			firebaseAuth.currentUser
		) {
			firebaseAuth.currentUser
				.getIdToken()
				.then(token => {
					// we don't want to supressHistory here as we want to update the URL to the homepage.
					fetchFeedsContent(token, link, { supressHistory: false });
				})
				.catch(() => {
					// fallback to loading regular homepage if fetchFeedsContent fails.
					fetchPage(link);
				});
		} else {
			// if it's a regular internal page (not homepage) just fetch the page as usual.
			fetchPage(link);
		}
	}

	onPageChange(e) {
		const { fetchPage } = this.props;
		fetchPage(document.location.href, { suppressHistory: true });
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
