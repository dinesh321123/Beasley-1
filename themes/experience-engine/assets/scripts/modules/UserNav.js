import React, { Component, Fragment } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import md5 from 'md5';

import { firebaseAuth } from '../library/firebase';
import {
	ensureUserHasCurrentChannel,
	userHasProfile,
} from '../library/experience-engine';
import ErrorBoundary from '../components/ErrorBoundary';
import {
	showSignInModal,
	showCompleteSignupModal,
} from '../redux/actions/modal';
import { setUser, resetUser } from '../redux/actions/auth';
import { fetchFeedsContent, hideSplashScreen } from '../redux/actions/screen';

class UserNav extends Component {
	static isHomepage() {
		return document.body.classList.contains( 'home' );
	}

	constructor( props ) {
		super( props );

		this.state = {
			didLogin: false,
			didRedirect: false,
			loading: true,
		};

		this.onSignIn = this.handleSignIn.bind( this );
		this.onSignOut = this.handleSignOut.bind( this );

		this.didAuthStateChange = this.didAuthStateChange.bind( this );
		this.finishLoading = this.finishLoading.bind( this );
	}

	componentDidMount() {
		const { firebase: config } = window.bbgiconfig;
		if ( config.projectId ) {
			firebaseAuth.onAuthStateChanged( this.didAuthStateChange );
			firebaseAuth
				.getRedirectResult()
				.then( result => {
					if ( result.user ) {
						this.setState( { didRedirect: true } );
					}
				} )
				.catch( err => {
					// eslint-disable-next-line no-console
					console.error( 'Authentication Error', err );
				} );
		} else {
			// eslint-disable-next-line no-console
			console.error( 'Firebase Project ID not found in bbgiconfig.' );
		}
	}

	/**
	 * If user logged in, load page using logged-in lifecycle
	 * If user logged out after login, reset user
	 * Else load as if not logged in - load page using logged-out lifecycle
	 */
	didAuthStateChange( user ) {
		if ( user ) {
			this.setState( { didLogin: true } );
			this.loadAsLoggedIn( user );
		} else if ( !this.state.didLogin ) {
			this.loadAsNotLoggedIn();
		} else {
			this.props.resetUser();
			this.finishLoading();
		}
	}

	/**
	 * The Logged In page lifecyles has 2 extra checks.
	 *
	 * 1. Check if User has a valid Channel, if not initialize it.
	 * 2. Check if User has Profile data, if not show Profile Modal.
	 */
	loadAsLoggedIn( user ) {
		this.props.setUser( user );
		this.setState( { loading: false } );

		if ( this.state.didRedirect ) {
			userHasProfile()
				.then( result => {
					if ( !result ) {
						this.finishLoading();
						this.props.showCompleteSignup();
					} else {
						ensureUserHasCurrentChannel()
							.then( ( result ) => {
								this.loadHomepage( user );
							} );
					}
				} )
				.catch( () => {
					this.finishLoading();
				} );
		} else if ( UserNav.isHomepage() ) {
			this.loadHomepage( user );
		} else {
			this.finishLoading();
		}
	}

	/**
	 * If Not logged in the page content is already loaded, just hide the
	 * splash screen.
	 */
	loadAsNotLoggedIn() {
		this.setState( { loading: false } );
		this.props.hideSplashScreen();
	}

	/**
	 * Helper to complete loading stage.
	 */
	finishLoading() {
		this.props.hideSplashScreen();
		this.setState( { loading: false } );
	}

	/**
	 * Loads the Homepage feeds from the EE API proxy.
	 */
	loadHomepage( user ) {
		return user.getIdToken().then( this.props.fetchFeedsContent );
	}

	handleSignIn() {
		this.props.showSignIn();
	}

	handleSignOut() {
		firebaseAuth.signOut();
		if ( UserNav.isHomepage() ) {
			window.location.reload();
		}
	}

	renderLoadingState() {
		return <div className="loading" />;
	}

	renderSignedInState( user ) {
		const { userDisplayName } = this.props;

		const displayName = user.displayName || userDisplayName || user.email;
		let photo = user.photoURL;
		if ( ( !photo || !photo.length ) && user.email ) {
			photo = `//www.gravatar.com/avatar/${md5( user.email )}.jpg?s=100`;
		}

		if ( photo && -1 !== photo.indexOf( 'gravatar.com' ) ) {
			photo += '&d=mp';
		}

		return (
			<Fragment>
				<div className="user-nav-info">
					<span className="user-nav-name" data-uid={user.uid}>
						{displayName}
					</span>
					<button
						className="user-nav-button"
						type="button"
						onClick={this.onSignOut}
					>
						Log Out
					</button>
				</div>
				<div className="user-nav-image">
					<img src={photo} alt={displayName} />
				</div>
			</Fragment>
		);
	}

	renderSignedOutState() {
		return (
			<div className="user-nav-logged-out">
				<button
					className="user-nav-button -with-icon"
					aria-label="Sign In to Your Account"
					type="button"
					onClick={this.onSignIn}
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 563.43 563.43">
						<title id="sign-in-button-title">Sign In</title>
						<desc id="sign-in-button-desc">User icon indicating entrance</desc>
						<path d="M280.79 314.559c83.266 0 150.803-67.538 150.803-150.803S364.055 13.415 280.79 13.415 129.987 80.953 129.987 163.756s67.537 150.803 150.803 150.803zm0-261.824c61.061 0 111.021 49.959 111.021 111.021s-49.96 111.02-111.021 111.02-111.021-49.959-111.021-111.021 49.959-111.02 111.021-111.02zM19.891 550.015h523.648c11.102 0 19.891-8.789 19.891-19.891 0-104.082-84.653-189.198-189.198-189.198H189.198C85.116 340.926 0 425.579 0 530.124c0 11.102 8.789 19.891 19.891 19.891zm169.307-169.307h185.034c75.864 0 138.313 56.436 148.028 129.524H41.17c9.714-72.625 72.164-129.524 148.028-129.524z" />
					</svg>
					Sign In
				</button>
			</div>
		);
	}

	render() {
		const { firebase: config } = window.bbgiconfig;
		if ( !config.projectId ) {
			return false;
		}

		const { loading } = this.state;
		const { user } = this.props;
		const container = document.getElementById( 'user-nav' );

		let component = false;
		if ( loading ) {
			component = this.renderLoadingState();
		} else if ( user ) {
			component = this.renderSignedInState( user );
		} else {
			component = this.renderSignedOutState();
		}

		return ReactDOM.createPortal(
			React.createElement( ErrorBoundary, {}, component ),
			container,
		);
	}
}

UserNav.propTypes = {
	hideSplashScreen: PropTypes.func.isRequired,
	fetchFeedsContent: PropTypes.func.isRequired,
	resetUser: PropTypes.func.isRequired,
	setUser: PropTypes.func.isRequired,
	showCompleteSignup: PropTypes.func.isRequired,
	showSignIn: PropTypes.func.isRequired,
	suppressUserCheck: PropTypes.bool.isRequired,
	user: PropTypes.oneOfType( [PropTypes.object, PropTypes.bool] ).isRequired,
	userDisplayName: PropTypes.string.isRequired,
};

export default connect(
	( { auth } ) => ( {
		suppressUserCheck: auth.suppressUserCheck,
		user: auth.user || false,
		userDisplayName: auth.displayName,
	} ),
	{
		hideSplashScreen,
		fetchFeedsContent,
		resetUser,
		setUser,
		showCompleteSignup: showCompleteSignupModal,
		showSignIn: showSignInModal,
	},
)( UserNav );
