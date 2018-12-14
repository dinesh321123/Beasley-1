import React, { Component, Fragment } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import firebase from 'firebase';
import md5 from 'md5';

import { showSignInModal, showSignUpModal } from '../redux/actions/modal';

class UserNav extends Component {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			loading: true,
			user: null,
		};

		self.onAuthStateChanged = self.handleAuthStateChanged.bind( self );
		self.onSignIn = self.handleSignIn.bind( self );
		self.onSignUp = self.handleSignUp.bind( self );
		self.onSignOut = self.handleSignOut.bind( self );
	}

	componentDidMount() {
		const { firebase: config } = window.bbgiconfig;

		if ( config.projectId ) {
			firebase.initializeApp( config );

			const auth = firebase.auth();
			auth.onAuthStateChanged( this.onAuthStateChanged );
		}
	}

	handleAuthStateChanged( user ) {
		this.setState( { loading: false, user } );
	}

	handleSignIn() {
		this.props.showSignIn();
	}

	handleSignUp() {
		this.props.showSignUp();
	}

	handleSignOut() {
		firebase.auth().signOut();
	}

	renderLoadingState() {
		return <div className="loading" />;
	}

	renderSignedInState() {
		const { currentUser } = firebase.auth();
		if ( !currentUser ) {
			return false;
		}

		const displayName = currentUser.displayName || currentUser.email;
		let photo = currentUser.photoURL;
		if ( !photo || !photo.length ) {
			photo = `//www.gravatar.com/avatar/${md5( currentUser.email )}.jpg?s=100`;
		}

		return (
			<Fragment>
				<div className="user-nav-info">
					<span className="user-nav-name">{displayName}</span>
					<button className="user-nav-button" type="button" onClick={this.onSignOut}>Log Out</button>
				</div>
				<div className="user-nav-image">
					<img src={photo} width="30" height="30" alt={displayName} />
				</div>
			</Fragment>
		);
	}

	renderSignedOutState() {
		const self = this;

		return (
			<div className="user-nav-logged-out">
				<button className="user-nav-button -with-icon" type="button" onClick={self.onSignIn}>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 563.43 563.43" aria-labelledby="sign-in-button-title sign-in-button-desc">
						<title id="sign-in-button-title"></title>
						<desc id="sign-in-button-desc"></desc>
						<path d="M331.685 425.378c-7.478 7.479-7.478 19.584 0 27.043 7.479 7.478 19.584 7.478 27.043 0l131.943-131.962c3.979-3.979 5.681-9.276 5.412-14.479.269-5.221-1.434-10.499-5.412-14.477L358.728 159.56c-7.459-7.478-19.584-7.478-27.043 0-7.478 7.478-7.478 19.584 0 27.042l100.272 100.272H19.125C8.568 286.875 0 295.443 0 306s8.568 19.125 19.125 19.125h412.832L331.685 425.378zM535.5 38.25H153c-42.247 0-76.5 34.253-76.5 76.5v76.5h38.25v-76.5c0-21.114 17.117-38.25 38.25-38.25h382.5c21.133 0 38.25 17.136 38.25 38.25v382.5c0 21.114-17.117 38.25-38.25 38.25H153c-21.133 0-38.25-17.117-38.25-38.25v-76.5H76.5v76.5c0 42.247 34.253 76.5 76.5 76.5h382.5c42.247 0 76.5-34.253 76.5-76.5v-382.5c0-42.247-34.253-76.5-76.5-76.5z"/>
					</svg>
					Sign In
				</button>
				<button className="user-nav-button -with-icon" type="button" onClick={self.onSignUp} aria-labelledby="sign-up-button-title sign-up-button-desc">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 563.43 563.43">
						<title id="sign-up-button-title"></title>
						<desc id="sign-up-button-desc"></desc>
						<path d="M280.79 314.559c83.266 0 150.803-67.538 150.803-150.803S364.055 13.415 280.79 13.415 129.987 80.953 129.987 163.756s67.537 150.803 150.803 150.803zm0-261.824c61.061 0 111.021 49.959 111.021 111.021s-49.96 111.02-111.021 111.02-111.021-49.959-111.021-111.021 49.959-111.02 111.021-111.02zM19.891 550.015h523.648c11.102 0 19.891-8.789 19.891-19.891 0-104.082-84.653-189.198-189.198-189.198H189.198C85.116 340.926 0 425.579 0 530.124c0 11.102 8.789 19.891 19.891 19.891zm169.307-169.307h185.034c75.864 0 138.313 56.436 148.028 129.524H41.17c9.714-72.625 72.164-129.524 148.028-129.524z"/>
						
					</svg>
					Sign Up
				</button>
			</div>
		);
	}

	render() {
		const { firebase: config } = window.bbgiconfig;

		if ( !config.projectId ) {
			return false;
		}

		const self = this;
		const { loading, user } = self.state;
		const container = document.getElementById( 'user-nav' );

		let component = false;
		if ( loading ) {
			component = self.renderLoadingState();
		} else if ( user ) {
			component = self.renderSignedInState();
		} else {
			component = self.renderSignedOutState();
		}

		return ReactDOM.createPortal( component, container );
	}

}

UserNav.propTypes = {
	showSignIn: PropTypes.func.isRequired,
	showSignUp: PropTypes.func.isRequired,
};

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		showSignIn: showSignInModal,
		showSignUp: showSignUpModal,
	}, dispatch );
}

export default connect( null, mapDispatchToProps )( UserNav );
