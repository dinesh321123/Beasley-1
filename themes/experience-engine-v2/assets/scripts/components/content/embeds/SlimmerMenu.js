import { Component } from 'react';

const moreButtonContent = `<li class="cnavigation-more" id="cnavigation-more"> More </li>`;

class SlimmerMenu extends Component {
	constructor() {
		super();
		this.handleSlimmerMenu = this.handleSlimmerMenu.bind(this);
		this.onSlimmerMenuPageChange = this.onSlimmerMenuPageChange.bind(this);
		this.handleSlimmerMenuCollapse = this.handleSlimmerMenuCollapse.bind(this);
		this.handleSlimmerMenuClick = this.handleSlimmerMenuClick.bind(this);
	}

	componentDidMount() {
		this.onSlimmerMenuPageChange();
		const clickButtonDom = document.querySelector('#cnavigation-more');
		clickButtonDom.addEventListener('click', this.handleSlimmerMenuClick);
		window.addEventListener('click', this.handleSlimmerMenuOutsideClick);
	}

	componentWillUnmount() {
		const clickButtonDom = document.querySelector('#cnavigation-more');
		clickButtonDom.addEventListener('click', this.handleSlimmerMenuClick);
		window.removeEventListener('click', this.handleSlimmerMenuOutsideClick);
	}

	onSlimmerMenuPageChange() {
		const { body } = document;
		const headerContainer = document.querySelector('#show-header-container');

		if (headerContainer) {
			if (!body.classList.contains('slimmer-menu-react')) {
				body.classList.add('slimmer-menu-react');
			}
		}
		this.handleSlimmerMenu();
	}

	handleSlimmerMenu() {
		document.querySelectorAll('[class*="_shows-"]').forEach(function(element) {
			element.classList.add('custom-margin');
		});

		const topHeaderNavigations = document.querySelectorAll(
			'#top_header .cnavigation',
		);
		const topMobileHeaderNavigations = document.querySelectorAll(
			'#top_mobile_header .cnavigation',
		);

		topHeaderNavigations.forEach(function(element) {
			element.setAttribute('data-click-state', 1);
		});

		topMobileHeaderNavigations.forEach(function(element) {
			element.setAttribute('data-click-state', 1);
		});

		if (window.matchMedia('(min-width: 992px)').matches) {
			const topHeader = document.querySelector('#top_header');
			topHeader.classList.add('desktop');

			document.querySelector('#top_mobile_header').innerHTML = '';
			const desktopCNavigation = document.querySelector(
				'.desktop .cnavigation',
			);

			const existingMoreButton = desktopCNavigation.querySelector(
				'.cnavigation-more',
			);

			if (!existingMoreButton) {
				desktopCNavigation.insertAdjacentHTML('beforeend', moreButtonContent);
			}

			const getMenuItemsCounts = this.handleMenuItemsCounts();
			desktopCNavigation.dataset.itemsToCopy = getMenuItemsCounts;
		}

		if (
			window.matchMedia('(min-width: 768px) and (max-width: 992px)').matches
		) {
			this.handleSlimmerMenuInit(6);
		} else if (
			window.matchMedia('(min-width: 480px) and (max-width: 768px)').matches
		) {
			this.handleSlimmerMenuInit(4);
		} else if (
			window.matchMedia('(min-width: 320px) and (max-width: 480px)').matches
		) {
			this.handleSlimmerMenuInit(3);
		} else if (window.matchMedia('(max-width: 320px)').matches) {
			this.handleSlimmerMenuInit(2);
		} else {
			const targetElement = document.querySelector('.cnavigation');
			targetElement.classList.toggle('no-pseudo');
		}
	}

	handleMenuItemsCounts() {
		const desktopCNavigation = document.querySelector('.desktop .cnavigation');
		desktopCNavigation.style.display = 'none';
		const mainContainerWidth = document.querySelector(
			'.desktop #slimmer-mobile-navigation',
		).offsetWidth;
		const navigationLogoWidth = document.querySelector(
			'.desktop #slimmer-mobile-navigation .mobile-navigation-logo',
		).offsetWidth;
		const titleWidth = document.querySelector(
			'.desktop .slimmer-navigation-desktop-container .title_description',
		).offsetWidth;
		const containerWidth = mainContainerWidth - navigationLogoWidth;
		const ulWidth = containerWidth - titleWidth;

		const menu = document.querySelector(
			'.desktop .slimmer-navigation-desktop-container',
		);
		desktopCNavigation.style.display = 'flex';
		desktopCNavigation.style.width = `${ulWidth}px`;
		const itemsCount = menu.querySelectorAll('li:not(.cnavigation-more)')
			.length;
		const items = menu.querySelectorAll('li:not(.cnavigation-more)');
		const moreItem = menu.querySelector('.cnavigation-more');

		let totalWidth = 0;
		let itemsToDisplay = -1;

		items.forEach(function(item) {
			totalWidth += item.offsetWidth;

			if (totalWidth <= ulWidth) {
				itemsToDisplay++;
			}
		});

		if (itemsCount > 3) {
			itemsToDisplay--;
		}

		for (let i = itemsToDisplay; i < items.length; i++) {
			items[i].style.display = 'none';
		}

		if (itemsCount > 3) {
			if (items.length > itemsToDisplay) {
				moreItem.style.display = 'inline';
			}
		}

		return itemsToDisplay;
	}

	handleSlimmerMenuCollapse() {
		const navigation = document.querySelector('.cnavigation');
		const clickState = navigation.getAttribute('data-click-state');
		if (clickState === '1') {
			navigation.setAttribute('data-click-state', 0);
			navigation.classList.add('dropdown-active');

			const subMenu = document.querySelector('#slimmer-submenu');
			if (!subMenu) {
				navigation.insertAdjacentHTML(
					'beforeend',
					"<span class='bg_overlay'></span><ul id='slimmer-submenu' class='sub_menu' style='display:block;'></ul>",
				);
				const sourceItems = document.querySelectorAll('.cnavigation li');
				const itemsToCopy = Array.prototype.slice.call(
					sourceItems,
					navigation.dataset.itemsToCopy,
				);
				const subMenuList = document.querySelector('#slimmer-submenu');
				subMenuList.innerHTML = '';
				itemsToCopy.forEach(function(item) {
					subMenuList.appendChild(item);
				});

				subMenuList.querySelector('.cnavigation-more').remove();
				navigation.insertAdjacentHTML('beforeend', moreButtonContent);
				const clickButtonDom = document.querySelector('#cnavigation-more');
				clickButtonDom.addEventListener('click', this.handleSlimmerMenuClick);

				let sumWidth = 0;
				const mobileUlWidth = document.querySelectorAll(
					'.top_mobile_header .cnavigation li:not(#slimmer-submenu li)',
				);

				if (mobileUlWidth.length > 0) {
					mobileUlWidth.forEach(function(li) {
						sumWidth += li.offsetWidth + 10;
					});

					const closestNavigation = mobileUlWidth[0].closest('.cnavigation');

					if (closestNavigation) {
						const subMenu = closestNavigation.querySelector('#slimmer-submenu');
						if (subMenu) {
							subMenu.style.width = `${sumWidth + 50}px`;
						}
					}
				}

				let desktopSumWidth = 0;
				const ulWidth = document.querySelectorAll(
					'.top_header.desktop .cnavigation li:not(#slimmer-submenu li)',
				);

				if (ulWidth.length > 0) {
					ulWidth.forEach(function(li) {
						desktopSumWidth += li.offsetWidth + 10;
					});

					const closestNavigation = ulWidth[0].closest('.cnavigation');

					if (closestNavigation) {
						const subMenu = closestNavigation.querySelector('#slimmer-submenu');
						if (subMenu) {
							subMenu.style.width = `${desktopSumWidth - 10}px`;
						}
					}
				}

				const desktopLis = document.querySelectorAll(
					'.desktop .cnavigation li',
				);
				desktopLis.forEach(function(li) {
					li.style.display = 'block';
				});
				const overlayElement = document.querySelector(
					'.desktop .cnavigation .bg_overlay',
				);
				if (overlayElement) {
					overlayElement.style.display = 'none';
				}
			} else {
				subMenu.style.display = 'block';
				document.querySelector('.bg_overlay').style.display = 'block';
				const overlayElement = document.querySelector(
					'.desktop .cnavigation .bg_overlay',
				);
				if (overlayElement) {
					overlayElement.style.display = 'none';
				}
			}
		} else {
			navigation.setAttribute('data-click-state', 1);
			navigation.classList.remove('dropdown-active');
			document
				.querySelectorAll('#slimmer-submenu, .bg_overlay')
				.forEach(function(el) {
					el.style.display = 'none';
				});
		}
	}

	handleSlimmerMenuOutsideClick = event => {
		const slimmerSubmenu = document.getElementById('slimmer-submenu');
		const navigationUl = document.querySelector('.cnavigation');
		const clickState = navigationUl.getAttribute('data-click-state');

		if (
			clickState === '0' &&
			slimmerSubmenu &&
			!slimmerSubmenu.contains(event.target) &&
			event.target.id !== 'cnavigation-more'
		) {
			if (slimmerSubmenu.style.display === 'block') {
				slimmerSubmenu.style.display = 'none';
				document.querySelector('.bg_overlay').style.display = 'none';
				navigationUl.setAttribute('data-click-state', '1');
				navigationUl.classList.remove('dropdown-active');
			}
		}
	};

	handleSlimmerMenuInit(itemsCountToCopy) {
		document.querySelector('.article-inner-container .cnavigation').innerHTML =
			'';
		const navigation = document.querySelector(
			'#top_mobile_header .cnavigation',
		);
		navigation.setAttribute('data-items-to-copy', itemsCountToCopy);
		const existingMoreButton = navigation.querySelector('.cnavigation-more');
		if (!existingMoreButton) {
			navigation.insertAdjacentHTML('beforeend', moreButtonContent);
		}
	}

	handleSlimmerMenuClick = event => {
		if (event.target.classList.contains('cnavigation-more')) {
			this.handleSlimmerMenuCollapse();
		}
	};

	render() {
		return null;
	}
}

export default SlimmerMenu;
