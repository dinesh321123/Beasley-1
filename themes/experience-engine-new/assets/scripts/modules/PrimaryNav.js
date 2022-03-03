import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { removeChildren } from '../library/dom';
import {
	showSignInModal,
	showDiscoverModal,
	hideModal,
} from '../redux/actions/modal';
import { setNavigationCurrent } from '../redux/actions/navigation';

import { isWindowsBrowser, isSafari } from '../library';

const $ = window.jQuery;
const config = window.bbgiconfig;

const navRoot = document.getElementById('js-primary-mega-nav');
// const siteMenuToggle = document.getElementById('js-menu-toggle');
const sidebarContainer = document.querySelector(
	'.primary-sidebar-navigation-new',
);
const listenliveContainer = document.getElementById('listen-live-button');

class PrimaryNav extends PureComponent {
	constructor(props) {
		super(props);

		this.primaryNavRef = React.createRef();
		this.state = {
			navHtml: navRoot.innerHTML,
			initialWw: window.innerWidth,
		};

		this.handleSubMenu = this.handleSubMenu.bind(this);
		this.handleMobileNav = this.handleMobileNav.bind(this);
		this.onPageChange = this.handlePageChange.bind(this);
		this.onResize = this.onResize.bind(this);
		this.detectScrollbar = this.detectScrollbar.bind(this);
		this.handleEscapeKey = this.handleEscapeKey.bind(this);
		this.handleClickOutSide = this.handleClickOutSide.bind(this);
		this.closeMenus = this.closeMenus.bind(this);
		this.handleListenliveClick = this.handleListenliveClick.bind(this);
		this.handleScrollNavigation = this.handleScrollNavigation.bind(this);

		removeChildren(navRoot);
	}

	componentDidMount() {
		window.addEventListener('scroll', this.handleScrollNavigation);
		window.addEventListener('resize', this.onResize);
		window.addEventListener('popstate', this.onPageChange);
		window.addEventListener('pushstate', this.onPageChange);

		if (window.matchMedia('(min-width: 900px)').matches) {
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

		const container = this.primaryNavRef.current;
		container.addEventListener('click', this.handleSubMenu);

		// siteMenuToggle.addEventListener('click', this.handleMobileNav);
		document.addEventListener('keydown', this.handleEscapeKey);
		document.addEventListener('click', this.handleClickOutSide);

		if (window.matchMedia('(min-width: 900px)').matches) {
			navRoot.parentNode.setAttribute('aria-hidden', false);
		}

		if (isWindowsBrowser()) {
			this.detectScrollbar();
		}

		// Fix for login link in Safari
		if (isSafari()) {
			sidebarContainer.classList.add('is-safari');
		}

		listenliveContainer.addEventListener('click', this.handleListenliveClick);

		document.body.classList.remove('-lock');
	}

	componentWillUnmount() {
		window.removeEventListener('scroll', this.handleScrollNavigation);
		window.removeEventListener('resize', this.onResize);
		window.removeEventListener('popstate', this.onPageChange);
		window.removeEventListener('pushstate', this.onPageChange);

		const container = this.primaryNavRef.current;
		container.removeEventListener('click', this.handleSubMenu);

		// siteMenuToggle.removeEventListener('click', this.handleMobileNav);
		document.removeEventListener('click', this.handleClickOutSide);

		listenliveContainer.removeEventListener(
			'click',
			this.handleListenliveClick,
		);
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
				return `<li><span>${song.artistName.toLowerCase()}</span></li>`;
			});

			const recentlyPlayed = document.getElementById(
				'live-player-recently-played',
			);
			if (items.length) {
				const filterItems = items.slice(0, 4);
				recentHtml = `
						<li><strong>Recently Played</strong></li>
						${filterItems.join('')}`;
				if (items.length > 4) {
					recentHtml += `<li><a href="${viewMoreLink}">VIEW MORE</strong></li>`;
				}
				recentlyPlayed.innerHTML = recentHtml;
			}
		}
	}

	handleScrollNavigation() {
		const { y } = this.state;
		const yOffset = window.scrollY;
		const primaryTopbar = document.querySelector('.primary-mega-topbar');
		if (!window.matchMedia('(min-width: 900px)').matches) {
			if (y > yOffset) {
				console.log('Scrolling UP with scrollY: ', yOffset);
				primaryTopbar.classList.remove('sticky-header-listenlive');

				if (yOffset < 10) {
					primaryTopbar.classList.remove('sticky-header');
				}
			} else if (y < yOffset) {
				console.log('Scrolling DOWN with scrollY: ', yOffset);
				if (yOffset > 100) {
					primaryTopbar.classList.add('sticky-header');
				}
				if (yOffset > 600) {
					primaryTopbar.classList.add('sticky-header-listenlive');
				}
			}
			this.setState({ y: yOffset });
		}
	}

	onResize() {
		const container = navRoot.parentNode;
		const ww = window.innerWidth;
		const { initialWw } = this.state;
		window.requestAnimationFrame(() => {
			if (window.matchMedia('(min-width: 900px)').matches) {
				container.setAttribute('aria-hidden', false);
				if (container.classList.contains('is-active')) {
					container.classList.remove('is-active');
				}

				if (
					container.parentNode.parentNode.classList.contains('menu-is-active')
				) {
					container.parentNode.parentNode.classList.remove('menu-is-active');
				}

				if (document.body.classList.contains('-lock')) {
					document.body.classList.remove('-lock');
				}

				if (isWindowsBrowser()) {
					this.detectScrollbar();
				}
			} else {
				if (!container.classList.contains('is-active')) {
					container.setAttribute('aria-hidden', true);
				}
				if (container.classList.contains('is-active') && ww !== initialWw) {
					container.classList.toggle('is-active');
					container.parentNode.parentNode.classList.toggle('menu-is-active');
					document.body.classList.toggle('-lock');
					container.setAttribute(
						'aria-hidden',
						container.getAttribute('aria-hidden') === 'false',
					);
				}
			}
		});
	}

	detectScrollbar() {
		const hasScrollbar =
			sidebarContainer.scrollHeight > sidebarContainer.clientHeight;

		if (hasScrollbar) {
			sidebarContainer.classList.add('has-scrollbar');
		} else {
			sidebarContainer.classList.remove('has-scrollbar');
		}
	}

	handleMobileNav(e) {
		if (e) {
			e.preventDefault();
			e.stopPropagation();
		}

		const container = navRoot.parentNode;
		container.classList.toggle('is-active');
		container.parentNode.parentNode.classList.toggle('menu-is-active');
		document.body.classList.toggle('-lock');
		container.setAttribute(
			'aria-hidden',
			container.getAttribute('aria-hidden') === 'false',
		);
	}

	handleEscapeKey(e) {
		if (!e || e.keyCode !== 27) {
			return false;
		}

		e.preventDefault();
		e.stopPropagation();

		if (!window.matchMedia('(min-width: 900px)').matches) {
			const container = navRoot.parentNode;
			container.classList.remove('is-active');
			container.parentNode.parentNode.classList.remove('menu-is-active');
			document.body.classList.remove('-lock');
			container.setAttribute('aria-hidden', 'true');
		}

		return true;
	}

	handleClickOutSide(event) {
		const container = navRoot.parentNode;

		if (event && !container.contains(event.target)) {
			// @TODO move this repeated code into a function.
			if (!window.matchMedia('(min-width: 900px)').matches) {
				container.classList.remove('is-active');
				container.parentNode.parentNode.classList.remove('menu-is-active');
				document.body.classList.remove('-lock');
				container.setAttribute('aria-hidden', 'true');
			} else {
				return false;
			}
		}

		return true;
	}

	handleSubMenu(e) {
		const { target } = e;
		const menuItem = target.parentNode;

		const { primaryNavRef } = this;
		const container = primaryNavRef.current;

		if (
			target.nodeName.toUpperCase() === 'BUTTON' &&
			target.parentNode.classList.contains('menu-item-discovery')
		) {
			const {
				setNavigationCurrent,
				showDiscover,
				showSignin,
				signedIn,
			} = this.props;

			// Remove "current-menu-item" from any / all.
			const links = container.querySelectorAll('.menu-item > a');
			for (let i = 0; i < links.length; i++) {
				const element = links[i];
				element.parentNode.classList.remove('current-menu-item');
			}
			// Set this as the Current Menu Item (despite being Modal and !onPageChange)
			setNavigationCurrent(menuItem.id);
			menuItem.classList.add('current-menu-item');

			// Deselect the mobile menu (if open)
			const mobileMenuToggle = document.getElementById('js-menu-toggle');
			const mobileMenuToggleStyle = window.getComputedStyle(mobileMenuToggle);

			if (mobileMenuToggleStyle.display !== 'none') {
				mobileMenuToggle.click();
			}

			if (signedIn) {
				showDiscover();
			} else {
				showSignin();
			}

			return;
		}

		const toggler = menuItem.querySelector('.sub-menu-activator');
		if (toggler) {
			toggler.classList.toggle('is-active');
		}

		const subMenu = menuItem.querySelector('.sub-menu');
		if (subMenu) {
			subMenu.setAttribute(
				'aria-hidden',
				subMenu.classList.contains('is-active'),
			);
			subMenu.classList.toggle('is-active');
		}
	}

	handlePageChange() {
		const { primaryNavRef } = this;
		const { setNavigationCurrent, hideModal } = this.props;
		const container = primaryNavRef.current;
		let linkToActivate;
		let parent;

		const { href, pathname } = window.location;

		const previouslySelected = container.querySelectorAll('.current-menu-item');

		hideModal();

		for (let i = 0; i < previouslySelected.length; i++) {
			previouslySelected[i].classList.remove('current-menu-item');
			parent = previouslySelected[i].parentNode;
		}

		const links = container.querySelectorAll('.menu-item > a');
		for (let i = 0; i < links.length; i++) {
			const element = links[i];
			const link = element.getAttribute('href');
			if (href === link || pathname === link) {
				element.parentNode.classList.add('current-menu-item');
				setNavigationCurrent(element.parentNode.id);
				linkToActivate = element;
			}
		}

		if (
			!window.matchMedia('(min-width: 900px)').matches &&
			navRoot.parentNode.classList.contains('is-active')
		) {
			this.handleMobileNav();
		}

		if (
			parent &&
			parent.classList.contains('sub-menu') &&
			parent.contains(linkToActivate)
		) {
			document.body.classList.remove('-lock');
			return;
		}

		this.closeMenus();
	}

	closeMenus() {
		document.body.classList.remove('-lock');
		const container = this.primaryNavRef.current;
		const actives = container.querySelectorAll(
			'.menu-item-has-children .is-active',
		);

		const currentMenu = document.querySelector(`.${this.props.currentMenu}`);

		for (let i = 0; i < actives.length; i++) {
			const element = actives[i];

			if (element.contains(currentMenu)) {
				continue;
			}

			element.classList.remove('is-active');
			element.setAttribute('aria-hidden', true);
		}
	}

	handleListenliveClick() {
		const dropdownToggle = document.getElementById('my-listen-dropdown2');
		const dropdownStyle = window.getComputedStyle(dropdownToggle);
		if (dropdownStyle.display !== 'none') {
			dropdownToggle.style.display = 'none';
		} else {
			dropdownToggle.style.display = 'block';
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
	signedIn: PropTypes.bool.isRequired,
	showDiscover: PropTypes.func.isRequired,
	showSignin: PropTypes.func.isRequired,
	setNavigationCurrent: PropTypes.func.isRequired,
	currentMenu: PropTypes.string.isRequired,
	hideModal: PropTypes.func.isRequired,
	songs: PropTypes.arrayOf(PropTypes.shape({})),
};

PrimaryNav.defaultProps = {
	songs: [],
};

function mapStateToProps({ auth, navigation, player }) {
	return {
		signedIn: !!auth.user,
		currentMenu: navigation.current,
		songs: player.songs,
	};
}

function mapDispatchToProps(dispatch) {
	const actions = {
		showSignin: showSignInModal,
		showDiscover: showDiscoverModal,
		setNavigationCurrent,
		hideModal,
	};

	return bindActionCreators(actions, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(PrimaryNav);
