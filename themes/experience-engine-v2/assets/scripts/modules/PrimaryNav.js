import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { removeChildren } from '../library/dom';
import { hideModal } from '../redux/actions/modal';
import { setNavigationCurrent } from '../redux/actions/navigation';

import { isSafari } from '../library';

const $ = window.jQuery;
const config = window.bbgiconfig;

const navRoot = document.getElementById('js-primary-mega-nav');
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
		};

		this.handleSubMenu = this.handleSubMenu.bind(this);
		this.onPageChange = this.handlePageChange.bind(this);
		this.handleOnLoadFix = this.handleOnLoadFix.bind(this);
		this.handleClickOutSide = this.handleClickOutSide.bind(this);
		this.handleListenliveClick = this.handleListenliveClick.bind(this);
		this.handleScrollNavigation = this.handleScrollNavigation.bind(this);
		this.closeMenus = this.closeMenus.bind(this);

		removeChildren(navRoot);
	}

	componentDidMount() {
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

		document.body.classList.remove('-lock');
	}

	componentWillUnmount() {
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

	handleOnLoadFix() {
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

	handleScrollNavigation() {
		const { y } = this.state;
		const yOffset = window.scrollY;
		const primaryTopbar = document.querySelector('.primary-mega-topbar');
		if (y > yOffset) {
			if (!window.matchMedia('(min-width: 1301px)').matches) {
				primaryTopbar.classList.remove('sticky-header-listenlive');
			}

			if (yOffset === 0) {
				primaryTopbar.classList.remove('sticky-header');
			}
		} else if (y < yOffset) {
			if (yOffset > 100) {
				primaryTopbar.classList.add('sticky-header');
			}
			if (!window.matchMedia('(min-width: 1301px)').matches && yOffset > 600) {
				primaryTopbar.classList.add('sticky-header-listenlive');
			}
		}
		this.setState({ y: yOffset });
	}

	handleClickOutSide(event) {
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

		hideModal();

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

		this.closeMenus();
	}

	closeMenus() {
		const megaMenuUl = document.getElementById('mega-menu-primary-nav');
		if (megaMenuUl.children.length) {
			for (let i = 0; i < megaMenuUl.children.length; i++) {
				megaMenuUl.children[i].classList.remove('mega-toggle-on');
			}
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
	setNavigationCurrent: PropTypes.func.isRequired,
	hideModal: PropTypes.func.isRequired,
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
	};

	return bindActionCreators(actions, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(PrimaryNav);
