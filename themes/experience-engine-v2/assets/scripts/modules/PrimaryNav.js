import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { removeChildren } from '../library/dom';
import { hideModal } from '../redux/actions/modal';
import { setNavigationCurrent } from '../redux/actions/navigation';
import { refreshDropdownAd, hideDropdownAd } from '../redux/actions/dropdownad';

import {
	fetchPublisherInformation,
	fixMegaSubMenuWidth,
	isSafari,
} from '../library';

const $ = window.jQuery;
const config = window.bbgiconfig;

const navRoot = document.getElementById('js-primary-mega-nav');
const sidebarContainer = document.querySelector(
	'.primary-sidebar-navigation-new',
);
const listenliveContainer = document.getElementById('listen-live-button');
const sButtonContainer = document.getElementById('wp-search-submit');

class PrimaryNav extends PureComponent {
	constructor(props) {
		super(props);

		this.primaryNavRef = React.createRef();
		this.state = {
			navHtml: navRoot.innerHTML,
		};

		this.onResize = this.onResize.bind(this);
		this.handleSubMenu = this.handleSubMenu.bind(this);
		this.onPageChange = this.handlePageChange.bind(this);
		this.handleOnLoadFix = this.handleOnLoadFix.bind(this);
		this.handleClickOutSide = this.handleClickOutSide.bind(this);
		this.handleListenliveClick = this.handleListenliveClick.bind(this);
		this.handleSearchClick = this.handleSearchClick.bind(this);
		this.handleScrollNavigation = this.handleScrollNavigation.bind(this);
		this.closeMenus = this.closeMenus.bind(this);
		this.handleSubMenuSize = this.handleSubMenuSize.bind(this);

		removeChildren(navRoot);
	}

	componentDidMount() {
		window.addEventListener('resize', this.onResize);
		window.addEventListener('scroll', this.handleScrollNavigation);
		window.addEventListener('popstate', this.onPageChange);
		window.addEventListener('pushstate', this.onPageChange);

		// Add close button in the active mega menu and set current active menu
		this.handleOnLoadFix();

		const container = this.primaryNavRef.current;
		container.addEventListener('click', this.handleSubMenu);

		document.addEventListener('click', this.handleClickOutSide);

		if (window.matchMedia('(min-width: 1301px)').matches) {
			navRoot.parentNode.setAttribute('aria-hidden', false);
		}

		// Fix for login link in Safari
		if (isSafari()) {
			sidebarContainer.classList.add('is-safari');
		}

		listenliveContainer.addEventListener('click', this.handleListenliveClick);

		// Defend against null sButtonContainer which is occurring on some local environments including Mike's Local.
		if (sButtonContainer) {
			sButtonContainer.addEventListener('click', this.handleSearchClick);
		}

		document.body.classList.remove('-lock');
	}

	componentWillUnmount() {
		window.removeEventListener('resize', this.onResize);
		window.removeEventListener('scroll', this.handleScrollNavigation);
		window.removeEventListener('popstate', this.onPageChange);
		window.removeEventListener('pushstate', this.onPageChange);

		const container = this.primaryNavRef.current;
		container.removeEventListener('click', this.handleSubMenu);

		document.removeEventListener('click', this.handleClickOutSide);

		listenliveContainer.removeEventListener(
			'click',
			this.handleListenliveClick,
		);
		sButtonContainer.removeEventListener('click', this.handleSearchClick);
	}

	componentDidUpdate(prevProps) {
		if (prevProps.songs !== this.props.songs) {
			const { songs } = this.props;
			let callsign = '';
			let viewMoreLink = '';
			let recentHtml = ``;

			if (!Array.isArray(songs) || !songs.length) {
				return;
			}

			if (config.streams && config.streams.length > 0) {
				callsign = config.streams[0].stream_call_letters;
				viewMoreLink = `/stream/${callsign}/`;
			}

			const items = songs.map(song => {
				let time = ``;
				const songTitle = song?.cueTitle ? song.cueTitle : ``;
				const artistName = song?.artistName ? `- ${song.artistName}` : ``;
				if (song?.cueTimeStart) {
					time = new Date(+song.cueTimeStart);
					time = time.toLocaleString('en-US', {
						hour: 'numeric',
						minute: 'numeric',
						hour12: true,
					});
				}

				return `<li><span>${time} ${songTitle} ${artistName}</span></li>`;
			});

			const recentlyPlayed = document.getElementById(
				'live-player-recently-played',
			);
			if (items.length) {
				const filterItems = items.slice(0, 4);

				const previousRecentlyPlayed = document.querySelectorAll(
					'.recently-played-section-ul',
				);

				if (previousRecentlyPlayed.length) {
					previousRecentlyPlayed.forEach(el => {
						el.remove();
					});
				}

				recentHtml = `<ul class="recently-played-section-ul">`;
				recentHtml += `
						<li><strong>Recently Played</strong></li>
						${filterItems.join('')}`;
				if (items.length > 4) {
					recentHtml += `<li><a href="${viewMoreLink}">VIEW MORE</strong></li>`;
				}
				recentHtml += `</ul>`;
				recentlyPlayed.innerHTML += recentHtml;
			}
		}
	}

	handleSubMenuSize() {
		if (window.matchMedia('(min-width: 1301px)').matches) {
			const adEle = document.getElementById('main-custom-logo');
			let adEleStyleHeight = '';
			if (adEle) {
				const adEleStyle = window.getComputedStyle(adEle);
				adEleStyleHeight = adEleStyle.height
					? Math.ceil(parseFloat(adEleStyle.height))
					: 0;
			}
			if (adEleStyleHeight > 40) {
				const menuLinkEl = document.querySelectorAll('.mega-menu-link');
				adEleStyleHeight -= 10;
				for (let i = 0; i < menuLinkEl.length; i++) {
					const mainel = menuLinkEl[i];
					mainel.style.height = `${adEleStyleHeight}px`;
				}
			}

			fixMegaSubMenuWidth();
		} else {
			const facebookURL = fetchPublisherInformation('facebook');
			const twitterURL = fetchPublisherInformation('twitter');
			const instagramURL = fetchPublisherInformation('instagram');
			const menuUL = document.getElementById('mega-menu-primary-nav');

			const newSocialEl = document.createElement('li');
			newSocialEl.classList.add('mega-menu-item');
			const ifSocialExist = document.getElementsByClassName(
				'mobile-head-social',
			);
			if (ifSocialExist.length === 0) {
				let mobileSocialHtml = `<div class="mobile-head-social">`;
				if (facebookURL) {
					mobileSocialHtml += `
						<a href="${facebookURL}" aria-label="Go to station's Facebook page" target="_blank" rel="noopener">
							<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 64 64" width="25px" height="25px">
								<path d="M32,6C17.642,6,6,17.642,6,32c0,13.035,9.603,23.799,22.113,25.679V38.89H21.68v-6.834h6.433v-4.548 c0-7.529,3.668-10.833,9.926-10.833c2.996,0,4.583,0.223,5.332,0.323v5.965h-4.268c-2.656,0-3.584,2.52-3.584,5.358v3.735h7.785 l-1.055,6.834h-6.73v18.843C48.209,56.013,58,45.163,58,32C58,17.642,46.359,6,32,6z"/>
							</svg>
						</a>
					`;
				}
				if (twitterURL) {
					mobileSocialHtml += `
						<a href="${twitterURL}" aria-label="Go to station's Twitter page" target="_blank" rel="noopener">
							<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 30 30" width="25px" height="25px">
								<path d="M28,6.937c-0.957,0.425-1.985,0.711-3.064,0.84c1.102-0.66,1.947-1.705,2.345-2.951c-1.03,0.611-2.172,1.055-3.388,1.295 c-0.973-1.037-2.359-1.685-3.893-1.685c-2.946,0-5.334,2.389-5.334,5.334c0,0.418,0.048,0.826,0.138,1.215 c-4.433-0.222-8.363-2.346-10.995-5.574C3.351,6.199,3.088,7.115,3.088,8.094c0,1.85,0.941,3.483,2.372,4.439 c-0.874-0.028-1.697-0.268-2.416-0.667c0,0.023,0,0.044,0,0.067c0,2.585,1.838,4.741,4.279,5.23 c-0.447,0.122-0.919,0.187-1.406,0.187c-0.343,0-0.678-0.034-1.003-0.095c0.679,2.119,2.649,3.662,4.983,3.705 c-1.825,1.431-4.125,2.284-6.625,2.284c-0.43,0-0.855-0.025-1.273-0.075c2.361,1.513,5.164,2.396,8.177,2.396 c9.812,0,15.176-8.128,15.176-15.177c0-0.231-0.005-0.461-0.015-0.69C26.38,8.945,27.285,8.006,28,6.937z"/>
							</svg>
						</a>
					`;
				}
				if (instagramURL) {
					mobileSocialHtml += `
						<a href="${instagramURL}" aria-label="Go to station's Instagram page" target="_blank" rel="noopener">
							<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 24 24" width="25px" height="25px">
								<path d="M 8 3 C 5.243 3 3 5.243 3 8 L 3 16 C 3 18.757 5.243 21 8 21 L 16 21 C 18.757 21 21 18.757 21 16 L 21 8 C 21 5.243 18.757 3 16 3 L 8 3 z M 8 5 L 16 5 C 17.654 5 19 6.346 19 8 L 19 16 C 19 17.654 17.654 19 16 19 L 8 19 C 6.346 19 5 17.654 5 16 L 5 8 C 5 6.346 6.346 5 8 5 z M 17 6 A 1 1 0 0 0 16 7 A 1 1 0 0 0 17 8 A 1 1 0 0 0 18 7 A 1 1 0 0 0 17 6 z M 12 7 C 9.243 7 7 9.243 7 12 C 7 14.757 9.243 17 12 17 C 14.757 17 17 14.757 17 12 C 17 9.243 14.757 7 12 7 z M 12 9 C 13.654 9 15 10.346 15 12 C 15 13.654 13.654 15 12 15 C 10.346 15 9 13.654 9 12 C 9 10.346 10.346 9 12 9 z"/>
							</svg>
						</a>
					`;
				}
				mobileSocialHtml += `</div>`;
				newSocialEl.innerHTML = mobileSocialHtml;
				menuUL.insertBefore(newSocialEl, menuUL.firstChild);
			}

			const newFormEl = document.createElement('li');
			newFormEl.classList.add('mega-menu-item');
			const ifExist = document.getElementsByClassName('mobile-search-form');
			if (ifExist.length === 0) {
				newFormEl.innerHTML = `
					<form role="search" method="get" class="mobile-search-form" action="${window.location.origin}/">
						<label for="mobile-search-q" id="mobile-q" class="screen-reader-text">Search for:</label>
						<input id="mobile-search-q" type="search" class="search-field" name="s" value="" placeholder="Search">
						<button type="submit" class="search-submit" aria-label="Submit search">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="15">
								<path d="M10.266 9.034h-.65l-.23-.222a5.338 5.338 0 0 0 1.216-4.385C10.216 2.144 8.312.32 6.012.042a5.342 5.342 0 0 0-5.97 5.97c.279 2.3 2.102 4.204 4.385 4.59a5.338 5.338 0 0 0 4.385-1.215l.222.23v.649l3.49 3.49c.337.336.887.336 1.224 0s.336-.887 0-1.224l-3.482-3.498zm-4.928 0c-2.044 0-3.695-1.65-3.695-3.696s1.65-3.695 3.695-3.695 3.696 1.65 3.696 3.695-1.65 3.696-3.696 3.696z" fill="currentcolor"/>
							</svg>
						</button>
					</form>`;
				menuUL.insertBefore(newFormEl, menuUL.firstChild);
			}
		}
	}

	setRightRailTopMargin() {
		const rightRailAdAside = document.querySelector('aside.ad.-sticky');
		const rightRailAdContainer = rightRailAdAside.querySelector(
			'aside.ad.-sticky > .wrapper',
		);
		if (rightRailAdAside && rightRailAdContainer) {
			const rightRailAsideTop = rightRailAdAside.getBoundingClientRect().top;
			const rightRailAsideHeight = rightRailAdAside.getBoundingClientRect()
				.height;
			const rightRailAdContainerHeight = rightRailAdContainer.getBoundingClientRect()
				.height;
			const topAdContainer = document.getElementById('top-scrolling-container');
			if (topAdContainer) {
				const topAdContainerBoundingRect = topAdContainer.getBoundingClientRect();
				const topAdTop = topAdContainerBoundingRect.top;
				const pixelsScrolledPastRightRailTop = topAdTop - rightRailAsideTop;
				if (pixelsScrolledPastRightRailTop < 0) {
					rightRailAdContainer.style.marginTop = `0px`;
				} else if (
					pixelsScrolledPastRightRailTop + rightRailAdContainerHeight <
					rightRailAsideHeight - 2
				) {
					rightRailAdContainer.style.marginTop = `${pixelsScrolledPastRightRailTop}px`;
				}
			}
		}
	}

	onResize() {
		this.handleSubMenuSize();
		this.setRightRailTopMargin();
	}

	handleOnLoadFix() {
		this.handleSubMenuSize();
		if (window.matchMedia('(min-width: 1301px)').matches) {
			const container = this.primaryNavRef.current;
			const { href, pathname } = window.location;

			// eslint-disable-next-line no-restricted-syntax
			for (const item of container.querySelectorAll('.menu-item')) {
				item.classList.remove('current-menu-item');
			}
			const links = container.querySelectorAll('.menu-item > a');
			for (let i = 0; i < links.length; i++) {
				const element = links[i];
				const link = element.getAttribute('href');
				if (href === link || pathname === link) {
					element.parentNode.classList.add('current-mega-menu-item');
					element.parentNode
						.closest('li.mega-menu-item-has-children')
						.classList.add('current-mega-main-menu-item');
					setNavigationCurrent(element.parentNode.id);
				}
			}
		}

		const megaMenuContainer = $('#mega-menu-primary-nav');
		if (megaMenuContainer.length) {
			const clostButtonhtml = `
				<div class="close-main-mega-menu">
					<button onclick="jQuery(this).parents('li').removeClass('mega-toggle-on');">
						<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="40" height="40" viewBox="0 0 30 30" style=" fill:#ffffff;">    <path d="M 7 4 C 6.744125 4 6.4879687 4.0974687 6.2929688 4.2929688 L 4.2929688 6.2929688 C 3.9019687 6.6839688 3.9019687 7.3170313 4.2929688 7.7070312 L 11.585938 15 L 4.2929688 22.292969 C 3.9019687 22.683969 3.9019687 23.317031 4.2929688 23.707031 L 6.2929688 25.707031 C 6.6839688 26.098031 7.3170313 26.098031 7.7070312 25.707031 L 15 18.414062 L 22.292969 25.707031 C 22.682969 26.098031 23.317031 26.098031 23.707031 25.707031 L 25.707031 23.707031 C 26.098031 23.316031 26.098031 22.682969 25.707031 22.292969 L 18.414062 15 L 25.707031 7.7070312 C 26.098031 7.3170312 26.098031 6.6829688 25.707031 6.2929688 L 23.707031 4.2929688 C 23.316031 3.9019687 22.682969 3.9019687 22.292969 4.2929688 L 15 11.585938 L 7.7070312 4.2929688 C 7.5115312 4.0974687 7.255875 4 7 4 z"></path></svg>
					</button>
				</div>
			`;
			megaMenuContainer.children().each(function() {
				const ulElement = $(this).children('ul');
				if (ulElement.length) {
					// ulElement.addClass('got-selected');
					ulElement.prepend(clostButtonhtml);
				}
			});
		}
	}

	// Set whether Breaking News Is Visible And Return New Scroll Position
	setBreakingNewsVisibility(shouldShow) {
		const breakingNewsElement = document.getElementById('breaking-news-banner');
		if (breakingNewsElement) {
			if (shouldShow) {
				breakingNewsElement.style.display = 'block';
			} else {
				breakingNewsElement.style.display = 'none';
			}
		}
		return window.scrollY;
	}

	handleScrollNavigation() {
		const { y } = this.state;
		let yOffset = window.scrollY;
		const primaryTopbar = document.querySelector('.primary-mega-topbar');
		if (!window.matchMedia('(min-width: 1301px)').matches) {
			if (y > yOffset) {
				primaryTopbar.classList.remove('sticky-header-listenlive');
				if (yOffset === 0) {
					primaryTopbar.classList.remove('sticky-header');
				}
				yOffset = this.setBreakingNewsVisibility(true);
			} else if (y < yOffset) {
				if (yOffset > 100) {
					primaryTopbar.classList.add('sticky-header');
				}
				if (yOffset > 600) {
					primaryTopbar.classList.add('sticky-header-listenlive');
					yOffset = this.setBreakingNewsVisibility(false);
				}
			}
		}
		this.setState({ y: yOffset });
		this.setRightRailTopMargin();
	}

	isPlayerButtonEvent(event) {
		const isListenLiveButtonEvent = () => {
			const playerButtonDiv = document.getElementById('player-button-div');
			return (
				event &&
				event.srcElement &&
				playerButtonDiv &&
				playerButtonDiv.contains(event.srcElement)
			);
		};
		const isListenLiveAlternativeStreamEvent = () => {
			return (
				event &&
				event.srcElement &&
				event.srcElement.parentElement &&
				(event.srcElement.className === 'control-station-button' ||
					event.srcElement.parentElement.className === 'control-station-button')
			);
		};
		const isPodcastPlayButtonEvent = () => {
			return (
				event &&
				event.srcElement &&
				event.srcElement.parentElement &&
				event.srcElement.parentElement.parentElement &&
				(event.srcElement.className === 'play-btn' ||
					event.srcElement.className === 'pause-btn' ||
					event.srcElement.className === 'resume-btn' ||
					event.srcElement.className === 'loading-btn' ||
					event.srcElement.parentElement.className === 'play-btn' ||
					event.srcElement.parentElement.className === 'pause-btn' ||
					event.srcElement.parentElement.className === 'resume-btn' ||
					event.srcElement.parentElement.className === 'loading-btn' ||
					event.srcElement.parentElement.parentElement.className ===
						'play-btn' ||
					event.srcElement.parentElement.parentElement.className ===
						'pause-btn' ||
					event.srcElement.parentElement.parentElement.className ===
						'resume-btn' ||
					event.srcElement.parentElement.parentElement.className ===
						'loading-btn')
			);
		};
		return (
			isListenLiveButtonEvent() ||
			isListenLiveAlternativeStreamEvent() ||
			isPodcastPlayButtonEvent()
		);
	}

	handleClickOutSide(event) {
		if (this.isPlayerButtonEvent(event)) {
			return;
		}

		if (event.target.classList.contains('mega-menu-link')) {
			event.target.parentNode.classList.toggle('mega-toggle-on');
		}

		const llbutton = document.getElementById('listen-live-button');
		const listenlivecontainer = document.getElementById('my-listen-dropdown2');
		if (
			event &&
			!listenlivecontainer.contains(event.target) &&
			!llbutton.contains(event.target)
		) {
			if (window.matchMedia('(min-width: 1301px)').matches) {
				const listenliveStyle = window.getComputedStyle(listenlivecontainer);
				if (listenliveStyle.display !== 'none') {
					listenlivecontainer.style.display = 'none';
				}
			}
		}
		return true;
	}

	handleSubMenu(e) {
		const { target } = e;
		if (target.nodeName.toUpperCase() === 'A') {
			this.closeMenus();
		}
	}

	handlePageChange() {
		const { primaryNavRef } = this;
		const { setNavigationCurrent, hideModal } = this.props;
		const container = primaryNavRef.current;

		const { href, pathname } = window.location;

		const previouslySelected = container.querySelectorAll(
			'.current-mega-menu-item',
		);
		const previouslySelectedMainMenu = container.querySelectorAll(
			'.current-mega-main-menu-item',
		);

		for (let i = 0; i < previouslySelected.length; i++) {
			previouslySelected[i].classList.remove('current-mega-menu-item');
		}
		for (let i = 0; i < previouslySelectedMainMenu.length; i++) {
			previouslySelectedMainMenu[i].classList.remove(
				'current-mega-main-menu-item',
			);
		}

		const links = container.querySelectorAll('.menu-item > a');
		for (let i = 0; i < links.length; i++) {
			const element = links[i];
			const link = element.getAttribute('href');
			if (href === link || pathname === link) {
				element.parentNode.classList.add('current-mega-menu-item');
				element.parentNode
					.closest('li.mega-menu-item-has-children')
					.classList.add('current-mega-main-menu-item');
				setNavigationCurrent(element.parentNode.id);
			}
		}

		if (!window.matchMedia('(min-width: 1301px)').matches) {
			const mobileMenuContainer = document.getElementsByClassName(
				'mega-menu-toggle',
			);
			if (mobileMenuContainer && mobileMenuContainer.length > 0) {
				if (mobileMenuContainer[0].classList.contains('mega-menu-open')) {
					mobileMenuContainer[0].classList.remove('mega-menu-open');
				}
			}
		}

		this.closeMenus();
		hideModal();
	}

	closeMenus() {
		const megaMenuUl = document.getElementById('mega-menu-primary-nav');
		if (megaMenuUl.children.length) {
			for (let i = 0; i < megaMenuUl.children.length; i++) {
				megaMenuUl.children[i].classList.remove('mega-toggle-on');
			}
		}

		const htmlElement = document.getElementsByTagName('html');
		if (htmlElement && htmlElement[0]) {
			htmlElement[0].classList.remove('mega-menu-primary-nav-off-canvas-open');
		}
	}

	handleListenliveClick(event) {
		if (this.isPlayerButtonEvent(event)) {
			return;
		}

		const dropdownToggle = document.getElementById('my-listen-dropdown2');
		const dropdownStyle = window.getComputedStyle(dropdownToggle);
		if (dropdownStyle.display !== 'none') {
			dropdownToggle.style.display = 'none';
			const { hideDropdownAd } = this.props;
			hideDropdownAd();
		} else {
			dropdownToggle.style.display = 'block';
			const { refreshDropdownAd } = this.props;
			refreshDropdownAd();
		}
	}

	handleSearchClick() {
		const searchToggle = document.getElementsByClassName('header-search-form');
		if (searchToggle[0]) {
			const searchStyle = window.getComputedStyle(searchToggle[0]);
			if (searchStyle.display !== 'none') {
				searchToggle[0].style.display = 'none';
			} else {
				searchToggle[0].style.display = 'block';
			}
		}
	}

	render() {
		const { navHtml } = this.state;

		// render back into #primary-nav container
		return ReactDOM.createPortal(
			<div
				ref={this.primaryNavRef}
				dangerouslySetInnerHTML={{ __html: navHtml }}
			/>,
			document.getElementById('js-primary-mega-nav'),
		);
	}
}

PrimaryNav.propTypes = {
	setNavigationCurrent: PropTypes.func.isRequired,
	hideModal: PropTypes.func.isRequired,
	refreshDropdownAd: PropTypes.func.isRequired,
	hideDropdownAd: PropTypes.func.isRequired,
	songs: PropTypes.arrayOf(PropTypes.shape({})),
};

PrimaryNav.defaultProps = {
	songs: [],
};

function mapStateToProps({ player }) {
	return {
		songs: player.songs,
	};
}

function mapDispatchToProps(dispatch) {
	const actions = {
		setNavigationCurrent,
		hideModal,
		refreshDropdownAd,
		hideDropdownAd,
	};

	return bindActionCreators(actions, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(PrimaryNav);
