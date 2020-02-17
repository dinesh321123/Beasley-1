import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import Swiper from 'swiper';
import md5 from 'md5';

import { firebaseAuth } from '../library/firebase';
import ErrorBoundary from '../components/ErrorBoundary';
import ContentBlock from '../components/content/ContentBlock';

import { hideModal } from '../redux/actions/modal';
import {
	initPage,
	initPageLoaded,
	loadPage,
	updatePage,
} from '../redux/actions/screen';

import { untrailingslashit } from '../library/strings';
import slugify from '../library/slugify';

const specialPages = ['/wp-admin/', '/wp-signup.php', '/wp-login.php'];

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
		const uuid = slugify( location.href );
		const data = document.documentElement.outerHTML;
		const state = {
			uuid,
			pageXOffset,
			pageYOffset,
		};
		history.replaceState( state, document.title, location.href );

		// load current page into the state
		self.props.initPage();
		self.props.initPageLoaded( uuid, data );
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
		const carousels = document.querySelectorAll( '.swiper-container' );

		if ( carousels.length ) {
			this.handleSliders();
		}
	}

	handleSliders() {
		const carousels = document.querySelectorAll( '.swiper-container' );

		if ( carousels ) {
			for ( let i = 0, len = carousels.length; i < len; i++ ) {
				const count = carousels[i].classList.contains( '-large' ) ? 2.2 : 4.2;
				const group = carousels[i].classList.contains( '-large' ) ? 2 : 4;

				new Swiper( carousels[i], {
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
				} );
			}
		}
	}

	handleClick( e ) {
		const self = this;
		const { loadPage } = self.props;

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

		if ( link.match( /\.(pdf|doc|docx)$/ ) ) {
			return;
		}

		// return if different origin or a relative link that doesn't start from forward slash
		if ( origin !== linkOrigin && !link.match( /^\/\w+/ ) ) {
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
		if (
			untrailingslashit( origin ) === untrailingslashit( link.split( /[?#]/ )[0] ) &&
			firebaseAuth.currentUser
		) {
			firebaseAuth.currentUser
				.getIdToken()
				.then( token => {
					loadPage( link, {
						fetchUrlOverride: `${
							window.bbgiconfig.wpapi
						}feeds-content?device=other`,
						fetchParams: {
							method: 'POST',
							headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
							body: `format=raw&authorization=${encodeURIComponent( token )}`,
						},
					} );
				} )
				.catch( () => {
					loadPage( link );
				} );
		} else {
			loadPage( link );
		}
	}

	handlePageChange( event ) {
		if ( event && event.state ) {
			const { uuid, pageXOffset, pageYOffset } = event.state;
			// @jerome may not be needed and above can get const
			// const { location } = window;
			// const uuidOverride = slugify( location.href );
			const { data } = this.props.history[uuid];
			this.props.updatePage( data );
			// scroll to the top of the page and remove modal (one way or other)
			setTimeout( () => window.scrollTo( pageXOffset, pageYOffset ), 100 );
			this.props.hideModal();
		}
	}

	render() {
		const { content, embeds, partials, isHome } = this.props;
		const blocks = [];

		if ( !content || !content.length ) {
			return false;
		}

		blocks.push(
			// the composed ke is needed to make sure we use a new ContentBlock component when we replace the content of the current page
			<ErrorBoundary key={`${window.location.href}-${md5( content )}`}>
				<ContentBlock content={content} embeds={embeds} isHome={isHome} />,
			</ErrorBoundary>,
		);

		Object.keys( partials ).forEach( key => {
			blocks.push(
				<ErrorBoundary key={key}>
					<ContentBlock {...partials[key]} partial />
				</ErrorBoundary>,
			);
		} );

		return blocks;
	}
}

ContentDispatcher.propTypes = {
	content: PropTypes.string.isRequired,
	embeds: PropTypes.arrayOf( PropTypes.object ).isRequired,
	history: PropTypes.shape( {
		uuid: PropTypes.shape( { data: PropTypes.shape( {} ) } ),
		replaceState: PropTypes.func,
	} ),
	partials: PropTypes.shape( {} ).isRequired,
	hideModal: PropTypes.func.isRequired,
	initPage: PropTypes.func.isRequired,
	isHome: PropTypes.bool.isRequired,
	loadPage: PropTypes.func.isRequired,
	updatePage: PropTypes.func.isRequired,
};

function mapStateToProps( { screen } ) {
	return {
		history: screen.history,
		content: screen.content,
		embeds: screen.embeds,
		isHome: screen.isHome,
		partials: screen.partials,
	};
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators(
		{
			hideModal,
			initPage,
			initPageLoaded,
			loadPage,
			updatePage,
		},
		dispatch,
	);
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)( ContentDispatcher );
