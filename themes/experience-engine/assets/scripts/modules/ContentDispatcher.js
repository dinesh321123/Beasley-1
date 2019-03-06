import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import firebase from 'firebase';
import md5 from 'md5';

import ErrorBoundary from '../components/ErrorBoundary';
import ContentBlock from '../components/content/ContentBlock';

import { initPage, loadPage, updatePage } from '../redux/actions/screen';
import { loadAssets, unloadScripts } from '../library/dom';
import { untrailingslashit } from '../library/strings';

const specialPages = [
	'/wp-admin/',
	'/wp-signup.php',
	'/wp-login.php',
];

class ContentDispatcher extends Component {

	constructor( props ) {
		super( props );

		const self = this;
		self.onClick = self.handleClick.bind( self );
		self.onPageChange = self.handlePageChange.bind( self );
		self.handleSliders = self.handleSliders.bind( self );
		self.handleSliderLoad = self.handleSliderLoad.bind( self );
	}

	componentDidMount() {
		const self = this;

		window.addEventListener( 'click', self.onClick );
		window.addEventListener( 'popstate', self.onPageChange );

		// replace current state with proper markup
		const { history, location, pageXOffset, pageYOffset } = window;
		const state = { data: document.documentElement.outerHTML, pageXOffset, pageYOffset };
		history.replaceState( state, document.title, location.href );

		// load current page into the state
		self.props.init();
		self.handleSliderLoad();
	}

	componentDidUpdate() {
		const self = this;
		const element = document.querySelector( '.scroll-to' );
		if ( element ) {
			let top = element.offsetTop;

			const wpadminbar = document.querySelector( '#wpadminbar' );
			if ( wpadminbar ) {
				top -= wpadminbar.offsetHeight;
			}

			setTimeout( () => window.scrollTo( 0, top ), 500 );
		}
		self.handleSliderLoad();
	}

	componentWillUnmount() {
		window.removeEventListener( 'click', this.onClick );
		window.removeEventListener( 'popstate', this.onPageChange );
	}

	handleSliderLoad() {
		const self = this;
		const carousels = document.querySelectorAll( '.carousel' );

		const scripts = [
			'https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js',
		];

		const styles = [
			'https://unpkg.com/flickity@2/dist/flickity.min.css',
		];

		if ( carousels.length ) {
			loadAssets( scripts, styles )
				.then( self.handleSliders.bind( self ) )
				.catch( error => console.error( error ) ); // eslint-disable-line no-console
		} else {
			unloadScripts( scripts );
			unloadScripts( styles );
		}
	}

	handleSliders() {
		const carousels = document.querySelectorAll( '.carousel' );
		const breakpoint = window.matchMedia( '(min-width: 768px)' ).matches;
		
		if( carousels.length > 0 ) { // eslint-disable-line
			carousels.forEach( carousel => {
				const isLarge = carousel.classList.contains( 'large' );
				let config;

				if( breakpoint ) {
					config = {
						draggable: false,
						cellAlign: 'left',
						contain: true,
						pageDots: false,
						groupCells: isLarge ? 2 : 4,
						selectedAttraction: 0.01,
						friction: 0.15
					};
				} else {
					config = {
						draggable: true,
						cellAlign: 'left',
						contain: true,
						pageDots: false,
						freeScroll: true,
						freeScrollFriction: 0.02,
						prevNextButtons: false,
					};
				}

				const flkty = new Flickity( carousel, config ); // eslint-disable-line

			} );
		}
	}

	handleClick( e ) {
		const self = this;
		const { load } = self.props;

		const { target } = e;
		let linkNode = target;

		// find if a click has been made by an anchor or an element that is a child of an anchor
		while ( linkNode && 'A' !== linkNode.nodeName.toUpperCase() ) {
			linkNode = linkNode.parentElement;
		}

		// do nothing if anchor is not found
		if ( !linkNode ) {
			return;
		}

		// do nothing if this link has to be opened in a new window
		if ( '_blank' === linkNode.getAttribute( 'target' ) ) {
			return;
		}

		const { location } = window;
		const { origin } = location;

		const link = linkNode.getAttribute( 'href' );
		const linkOrigin = link.substring( 0, origin.length );

		// return if different origin or a relative link that doesn't start from forward slash
		if ( ( origin !== linkOrigin && !link.match( /^\/\w+/ ) ) ) {
			return;
		}

		// return if it is an admin link or a link to a special page
		if ( specialPages.find( url => -1 < link.indexOf( url ) ) ) {
			return;
		}

		// target link is internal page, thus stop propagation and prevent default actions
		e.preventDefault();
		e.stopPropagation();

		// load user homepage if token is not empty and the next page is a homepage
		// otherwise just load the next page
		const auth = firebase.auth();
		if ( untrailingslashit( origin ) === untrailingslashit( link.split( /[?#]/ )[0] ) && auth.currentUser ) {
			auth.currentUser
				.getIdToken()
				.then( token => {
					load( link, {
						fetchUrlOverride: `${window.bbgiconfig.wpapi}feeds-content`,
						fetchParams: {
							method: 'POST',
							headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
							body: `format=raw&authorization=${encodeURIComponent( token )}`,
						},
					} );
				} )
				.catch( () => {
					load( link );
				} );
		} else {
			load( link );
		}
	}

	handlePageChange( event ) {
		if ( event && event.state ) {
			const { data, pageXOffset, pageYOffset } = event.state;
			// update content state
			this.props.update( data );
			// scroll to the top of the page
			setTimeout( () => window.scrollTo( pageXOffset, pageYOffset ), 100 );
		}
	}

	render() {
		const { content, embeds, partials } = this.props;
		const blocks = [];

		if ( !content || !content.length ) {
			return false;
		}

		blocks.push(
			// the composed ke is needed to make sure we use a new ContentBlock component when we replace the content of the current page
			<ErrorBoundary key={`${window.location.href}-${md5( content )}`}>
				<ContentBlock content={content} embeds={embeds} />,
			</ErrorBoundary>
		);

		Object.keys( partials ).forEach( ( key ) => {
			blocks.push(
				<ErrorBoundary key={key}>
					<ContentBlock {...partials[key]} partial />
				</ErrorBoundary>
			);
		} );

		return blocks;
	}

}

ContentDispatcher.propTypes = {
	content: PropTypes.string.isRequired,
	embeds: PropTypes.arrayOf( PropTypes.object ).isRequired,
	partials: PropTypes.shape( {} ).isRequired,
	init: PropTypes.func.isRequired,
	load: PropTypes.func.isRequired,
	update: PropTypes.func.isRequired,
};

function mapStateToProps( { screen } ) {
	return {
		content: screen.content,
		embeds: screen.embeds,
		partials: screen.partials,
	};
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		init: initPage,
		load: loadPage,
		update: updatePage,
	}, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( ContentDispatcher );
