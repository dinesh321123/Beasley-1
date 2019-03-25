import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { removeChildren } from '../library/dom';
import { showSignInModal, showDiscoverModal } from '../redux/actions/modal';

const navRoot = document.getElementById( 'js-primary-nav' );
const siteMenuToggle = document.getElementById( 'js-menu-toggle' );

class PrimaryNav extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.primaryNavRef = React.createRef();
		self.state = {
			navHtml: navRoot.innerHTML,
			initialWw: window.innerWidth,
		};

		self.handleSubMenu = self.handleSubMenu.bind( self );
		self.handleMobileNav = self.handleMobileNav.bind( self );
		self.onPageChange = self.handlePageChange.bind( self );
		self.onResize = self.onResize.bind( self );

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

		if ( window.matchMedia( '(min-width: 900px)' ).matches ) {
			navRoot.parentNode.setAttribute( 'aria-hidden', false );
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
			}
		}

		if ( !window.matchMedia( '(min-width: 900px)' ).matches && navRoot.parentNode.classList.contains( 'is-active' ) ) {
			self.handleMobileNav();
		}
	}

	handleSubMenu( e ) {
		const { target } = e;
		const menuItem = target.parentNode;

		const self = this;
		const { primaryNavRef } = self;
		const container = primaryNavRef.current;

		if ( 'BUTTON' === target.nodeName.toUpperCase() ) {
			if ( menuItem.classList.contains( 'menu-item-discovery' ) ) {
				const { signedIn, showDiscover, showSignin } = self.props;

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
				subMenu.setAttribute( 'aria-hidden', subMenu.classList.contains( 'is-active' ) );
				subMenu.classList.toggle( 'is-active' );
			}

			const actives = container.querySelectorAll( '.menu-item-has-children .is-active' );
			for ( let i = 0; i < actives.length; i++ ) {
				const element = actives[i];
				if ( element !== toggler && element !== subMenu ) {
					element.classList.remove( 'is-active' );
					element.setAttribute( 'aria-hidden', true );
				}
			}
		}
	}

	handleMobileNav( e ) {
		if ( e ) {
			e.preventDefault();
			e.stopPropagation();
		}

		const container = navRoot.parentNode;
		container.classList.toggle( 'is-active' );
		container.parentNode.parentNode.classList.toggle( 'menu-is-active' );
		document.body.classList.toggle( '-lock' );
		container.setAttribute( 'aria-hidden', 'false' === container.getAttribute( 'aria-hidden' ) );
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

				if ( container.parentNode.parentNode.classList.contains( 'menu-is-active' ) ) {
					container.parentNode.parentNode.classList.remove( 'menu-is-active' );
				}

				if ( document.body.classList.contains( '-lock' ) ) {
					document.body.classList.remove( '-lock' );
				}

			} else {
				if ( !container.classList.contains( 'is-active' ) ){
					container.setAttribute( 'aria-hidden', true );
				}
				if ( container.classList.contains( 'is-active' ) && ww !== initialWw ) {
					container.classList.toggle( 'is-active' );
					container.parentNode.parentNode.classList.toggle( 'menu-is-active' );
					document.body.classList.toggle( '-lock' );
					container.setAttribute( 'aria-hidden', 'false' === container.getAttribute( 'aria-hidden' ) );
				}
			}
		} );
	}

	render() {
		const self = this;
		const { navHtml } = self.state;

		// render back into #primary-nav container
		return ReactDOM.createPortal(
			<div ref={self.primaryNavRef} dangerouslySetInnerHTML={{ __html: navHtml }} />,
			document.getElementById( 'js-primary-nav' ),
		);
	}

}

PrimaryNav.propTypes = {
	signedIn: PropTypes.bool.isRequired,
	showDiscover: PropTypes.func.isRequired,
	showSignin: PropTypes.func.isRequired,
};

function mapStateToProps( { auth } ) {
	return {
		signedIn: !!auth.user,
	};
}

function mapDispatchToProps( dispatch ) {
	const actions = {
		showSignin: showSignInModal,
		showDiscover: showDiscoverModal,
	};

	return bindActionCreators( actions, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( PrimaryNav );
