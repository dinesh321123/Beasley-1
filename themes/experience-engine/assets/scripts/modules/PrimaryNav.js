import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { removeChildren } from '../library/dom';
import { showSignInModal, showDiscoverModal } from '../redux/actions/modal';
import { setNavigationCurrent } from '../redux/actions/navigation';

import { isWindowsBrowser } from '../library/browser';

const navRoot = document.getElementById( 'js-primary-nav' );
const siteMenuToggle = document.getElementById( 'js-menu-toggle' );
const sidebarContainer = document.querySelector( '.primary-sidebar-navigation' );

class PrimaryNav extends PureComponent {
	constructor( props ) {
		super( props );

		const self = this;

		self.primaryNavRef = React.createRef();
		self.state = {
			navHtml: navRoot.innerHTML,
			initialWw: window.innerWidth
		};

		self.handleSubMenu = self.handleSubMenu.bind( self );
		self.handleMobileNav = self.handleMobileNav.bind( self );
		self.onPageChange = self.handlePageChange.bind( self );
		self.onResize = self.onResize.bind( self );
		self.detectScrollbar = self.detectScrollbar.bind( self );
		self.handleEscapeKey = self.handleEscapeKey.bind( self );
		self.handleClickOutSide = self.handleClickOutSide.bind( self );

		removeChildren( navRoot );
	}

	componentDidMount() {
		const self = this;

		window.addEventListener( 'resize', self.onResize );
		window.addEventListener( 'popstate', self.onPageChange );
		window.addEventListener( 'pushstate', self.onPageChange );

		const container = self.primaryNavRef.current;
		container.addEventListener( 'click', self.handleSubMenu );

		siteMenuToggle.addEventListener( 'click', self.handleMobileNav );
		document.addEventListener( 'keydown', self.handleEscapeKey, false );
		document.addEventListener( 'click', self.handleClickOutSide, false );

		if ( window.matchMedia( '(min-width: 900px)' ).matches ) {
			navRoot.parentNode.setAttribute( 'aria-hidden', false );
		}

		if ( isWindowsBrowser() ) {
			self.detectScrollbar();
		}
	}

	componentWillUnmount() {
		const self = this;

		window.removeEventListener( 'resize', self.onResize );
		window.removeEventListener( 'popstate', self.onPageChange );
		window.removeEventListener( 'pushstate', self.onPageChange );

		const container = self.primaryNavRef.current;
		container.removeEventListener( 'click', self.handleSubMenu );

		siteMenuToggle.removeEventListener( 'click', self.handleMobileNav );
	}

	handlePageChange() {
		const self = this;
		const { primaryNavRef } = self;
		const { setNavigationCurrent } = self.props;
		const container = primaryNavRef.current;

		const { href, pathname } = window.location;

		const previouslySelected = container.querySelectorAll( '.current-menu-item' );
		for ( let i = 0; i < previouslySelected.length; i++ ) {
			previouslySelected[i].classList.remove( 'current-menu-item' );
		}

		const links = container.querySelectorAll( '.menu-item > a' );
		for ( let i = 0; i < links.length; i++ ) {
			const element = links[i];
			const link = element.getAttribute( 'href' );
			if ( href === link || pathname === link ) {
				element.parentNode.classList.add( 'current-menu-item' );
				setNavigationCurrent( element.parentNode.id );
			}
		}

		if (
			!window.matchMedia( '(min-width: 900px)' ).matches &&
			navRoot.parentNode.classList.contains( 'is-active' )
		) {
			self.handleMobileNav();
		}
	}

	handleSubMenu( e ) {
		const { target } = e;
		const menuItem = target.parentNode;

		const self = this;
		const { primaryNavRef } = self;
		const container = primaryNavRef.current;

		if ( 'BUTTON' === target.nodeName.toUpperCase() &&
			target.parentNode.classList.contains( 'menu-item-discovery' ) ) {
			const {
				setNavigationCurrent,
				showDiscover,
				showSignin,
				signedIn
			} = self.props;

			// Remove "current-menu-item" from any / all.
			const links = container.querySelectorAll( '.menu-item > a' );
			for ( let i = 0; i < links.length; i++ ) {
				const element = links[i];
				element.parentNode.classList.remove( 'current-menu-item' );
			}
			// Set this as the Current Menu Item (despite being Modal and !onPageChange)
			setNavigationCurrent( menuItem.id );
			menuItem.classList.add( 'current-menu-item' );
			if ( signedIn ) {
				showDiscover();
			} else {
				showSignin();
			}

			return;
		}

		const toggler = menuItem.querySelector( '.sub-menu-activator' );
		if ( toggler ) {
			toggler.classList.toggle( 'is-active' );
		}

		const subMenu = menuItem.querySelector( '.sub-menu' );
		if ( subMenu ) {
			subMenu.setAttribute(
				'aria-hidden',
				subMenu.classList.contains( 'is-active' )
			);
			subMenu.classList.toggle( 'is-active' );
		}

		const actives = container.querySelectorAll(
			'.menu-item-has-children .is-active'
		);
		for ( let i = 0; i < actives.length; i++ ) {
			const element = actives[i];
			if ( element !== toggler && element !== subMenu ) {
				element.classList.remove( 'is-active' );
				element.setAttribute( 'aria-hidden', true );
			}
		}
	}

	handleMobileNav( e ) {
		e.preventDefault();

		const container = navRoot.parentNode;

		if( !container.classList.contains( 'is-active' ) ) {
			container.classList.add( 'is-active' );
			container.parentNode.parentNode.classList.add( 'menu-is-active' );
			document.body.classList.add( '-lock' );
			if( window.matchMedia( '(min-width: 900px)' ).matches ) {
				container.setAttribute( 'aria-hidden', false );
			} else {
				container.setAttribute( 'aria-hidden', true );
			}
		} else {
			container.classList.remove( 'is-active' );
			container.parentNode.parentNode.classList.remove( 'menu-is-active' );
			document.body.classList.remove( '-lock' );
			if( window.matchMedia( '(min-width: 900px)' ).matches ) {
				container.setAttribute( 'aria-hidden', false );
			} else {
				container.setAttribute( 'aria-hidden', true );
			}
		}
	}

	handleEscapeKey( e ) {
		const { keyCode } = e;
		const container = navRoot.parentNode;

		if( 27 === keyCode ) {
			container.classList.remove( 'is-active' );
			container.parentNode.parentNode.classList.remove( 'menu-is-active' );
			document.body.classList.remove( '-lock' );
			if( window.matchMedia( '(min-width: 900px)' ).matches ) {
				container.setAttribute( 'aria-hidden', false );
			} else {
				container.setAttribute( 'aria-hidden', true );
			}
		}
	}

	handleClickOutSide( e ) {
		const { target } = e;
		const container = navRoot.parentNode;

		if( target !== siteMenuToggle && !container.contains( target ) ) {
			container.classList.remove( 'is-active' );
			container.parentNode.parentNode.classList.remove( 'menu-is-active' );
			document.body.classList.remove( '-lock' );
			if( window.matchMedia( '(min-width: 900px)' ).matches ) {
				container.setAttribute( 'aria-hidden', false );
			} else {
				container.setAttribute( 'aria-hidden', true );
			}

		}
	}

	detectScrollbar() {
		const hasScrollbar = sidebarContainer.scrollHeight > sidebarContainer.clientHeight;

		if ( hasScrollbar ) {
			sidebarContainer.classList.add( 'has-scrollbar' );
		} else {
			sidebarContainer.classList.remove( 'has-scrollbar' );
		}
	}

	onResize() {
		const container = navRoot.parentNode;
		const ww = window.innerWidth;
		const { initialWw } = this.state;
		window.requestAnimationFrame( () => {
			if ( window.matchMedia( '(min-width: 900px)' ).matches ) {
				container.setAttribute( 'aria-hidden', false );
				if ( container.classList.contains( 'is-active' ) ) {
					container.classList.remove( 'is-active' );
				}

				if (
					container.parentNode.parentNode.classList.contains( 'menu-is-active' )
				) {
					container.parentNode.parentNode.classList.remove( 'menu-is-active' );
				}

				if ( document.body.classList.contains( '-lock' ) ) {
					document.body.classList.remove( '-lock' );
				}

				if ( isWindowsBrowser() ) {
					this.detectScrollbar();
				}

			} else {
				if ( !container.classList.contains( 'is-active' ) ) {
					container.setAttribute( 'aria-hidden', true );
				}
				if ( container.classList.contains( 'is-active' ) && ww !== initialWw ) {
					container.classList.toggle( 'is-active' );
					container.parentNode.parentNode.classList.toggle( 'menu-is-active' );
					document.body.classList.toggle( '-lock' );
					container.setAttribute(
						'aria-hidden',
						'false' === container.getAttribute( 'aria-hidden' )
					);
				}
			}
		} );
	}

	render() {
		const self = this;
		const { navHtml } = self.state;

		// render back into #primary-nav container
		return ReactDOM.createPortal(
			<div
				ref={self.primaryNavRef}
				dangerouslySetInnerHTML={{ __html: navHtml }}
			/>,
			document.getElementById( 'js-primary-nav' )
		);
	}
}

PrimaryNav.propTypes = {
	signedIn: PropTypes.bool.isRequired,
	showDiscover: PropTypes.func.isRequired,
	showSignin: PropTypes.func.isRequired,
	setNavigationCurrent: PropTypes.func.isRequired
};

function mapStateToProps( { auth } ) {
	return {
		signedIn: !!auth.user
	};
}

function mapDispatchToProps( dispatch ) {
	const actions = {
		showSignin: showSignInModal,
		showDiscover: showDiscoverModal,
		setNavigationCurrent: setNavigationCurrent
	};

	return bindActionCreators( actions, dispatch );
}

export default connect(
	mapStateToProps,
	mapDispatchToProps
)( PrimaryNav );
