import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Swiper from 'swiper';
import md5 from 'md5';

import { firebaseAuth } from '../library/firebase';
import ContentBlock from '../components/content/ContentBlock';
import {
	initPage,
	initPageLoaded,
	fetchPage,
	fetchFeedsContent,
} from '../redux/actions/screen';
import { untrailingslashit } from '../library/strings';
import slugify from '../library/slugify';

const specialPages = ['/wp-admin/', '/wp-signup.php', '/wp-login.php'];

/**
 * The ContentDispatcher component is responsible for catching click on intenral links
 * and trigger the page loading logic.
 */
class ContentDispatcher extends Component {
	constructor( props ) {
		super( props );

		this.onClick = this.handleClick.bind( this );
		this.handleSliders = this.handleSliders.bind( this );
		this.handleSliderLoad = this.handleSliderLoad.bind( this );
	}

	componentDidMount() {
		const { initPage, initPageLoaded } = this.props;

		window.addEventListener( 'click', this.onClick );
		window.addEventListener( 'popstate', this.onPageChange );

		// replace current state with proper markup
		const { history, location, pageXOffset, pageYOffset } = window;
		const uuid = slugify( location.href );
		const html = document.documentElement.outerHTML;

		history.replaceState(
			{
				uuid,
				pageXOffset,
				pageYOffset,
			},
			document.title,
			location.href,
		);

		// load current page into the state
		initPage();
		initPageLoaded( uuid, html );

		this.handleSliderLoad();
	}

	componentDidUpdate() {
		const element = document.querySelector( '.scroll-to' );
		if ( element ) {
			let top = element.offsetTop;

			const wpadminbar = document.querySelector( '#wpadminbar' );
			if ( wpadminbar ) {
				top -= wpadminbar.offsetHeight;
			}

			setTimeout( () => window.scrollTo( 0, top ), 500 );
		}
		this.handleSliderLoad();
	}

	componentWillUnmount() {
		window.removeEventListener( 'click', this.onClick );
		window.removeEventListener( 'popstate', this.onPageChange );
	}

	/**
	 * Handles setting up the sliders.
	 */
	handleSliderLoad() {
		const carousels = document.querySelectorAll( '.swiper-container' );

		if ( carousels.length ) {
			this.handleSliders();
		}
	}

	/**
	 * Setup the sliders with Swiper.js
	 */
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

	/**
	 * Handle the click links and if it's an internal links trigger the page loading process.
	 *
	 * @param {event} e
	 */
	handleClick( e ) {
		const { fetchPage, fetchFeedsContent } = this.props;

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
					fetchFeedsContent( token, link );
				} )
				.catch( () => {
					fetchPage( link );
				} );
		} else {
			fetchPage( link );
		}
	}

	render() {
		const { content, embeds, partials, isHome } = this.props;
		const blocks = [];

		if ( !content || !content.length ) {
			return null;
		}

		blocks.push(
			// the composed key is needed to make sure we use a new ContentBlock component when we replace the content of the current page
			<ContentBlock key={`${window.location.href}-${md5( content )}`} content={content} embeds={embeds} isHome={isHome} />,
		);

		Object.keys( partials ).forEach( key => {
			blocks.push(
				<ContentBlock key={key} {...partials[key]} partial />,
			);
		} );

		return blocks;
	}
}

ContentDispatcher.propTypes = {
	content: PropTypes.string.isRequired,
	embeds: PropTypes.arrayOf( PropTypes.object ).isRequired,
	partials: PropTypes.shape( {} ).isRequired,
	initPage: PropTypes.func.isRequired,
	isHome: PropTypes.bool.isRequired,
	initPageLoaded: PropTypes.func.isRequired,
	fetchPage: PropTypes.func.isRequired,
	fetchFeedsContent: PropTypes.func.isRequired,
};

export default connect(
	( { screen } ) => ( {
		content: screen.content,
		embeds: screen.embeds,
		isHome: screen.isHome,
		partials: screen.partials,
	} ),
	{
		initPage,
		initPageLoaded,
		fetchPage,
		fetchFeedsContent,
	},
)( ContentDispatcher );
