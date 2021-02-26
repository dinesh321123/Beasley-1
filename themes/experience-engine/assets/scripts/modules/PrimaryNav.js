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

const navRoot = document.getElementById('js-primary-nav');
const siteMenuToggle = document.getElementById('js-menu-toggle');
const sidebarContainer = document.querySelector('.primary-sidebar-navigation');

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

		removeChildren(navRoot);
	}

	componentDidMount() {
		window.addEventListener('resize', this.onResize);
		window.addEventListener('popstate', this.onPageChange);
		window.addEventListener('pushstate', this.onPageChange);

		const container = this.primaryNavRef.current;
		container.addEventListener('click', this.handleSubMenu);

		siteMenuToggle.addEventListener('click', this.handleMobileNav);
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

		console.log('componentDidMount()');
		document.body.classList.remove('-lock');
		console.log('removed lock');
	}

	componentWillUnmount() {
		window.removeEventListener('resize', this.onResize);
		window.removeEventListener('popstate', this.onPageChange);
		window.removeEventListener('pushstate', this.onPageChange);

		const container = this.primaryNavRef.current;
		container.removeEventListener('click', this.handleSubMenu);

		siteMenuToggle.removeEventListener('click', this.handleMobileNav);
		document.removeEventListener('click', this.handleClickOutSide);
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
			console.log(
				'Remove lock because parent has submenu, but dont close menus',
			);
			document.body.classList.remove('-lock');
			return;
		}

		this.closeMenus();
	}

	closeMenus() {
		console.log('Closing Menus');
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

	render() {
		const { navHtml } = this.state;

		// render back into #primary-nav container
		return ReactDOM.createPortal(
			<div
				ref={this.primaryNavRef}
				dangerouslySetInnerHTML={{ __html: navHtml }}
			/>,
			document.getElementById('js-primary-nav'),
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
};

function mapStateToProps({ auth, navigation }) {
	return {
		signedIn: !!auth.user,
		currentMenu: navigation.current,
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
